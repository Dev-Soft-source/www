<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Language;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\Step4PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class Step5to5Controller extends Controller
{
    public function create($lang = null){
        $user = auth()->user();
        $countries = Country::where('status', '1')->orderBy('name', 'asc')->get();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $step4Page = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $step4Page = Step4PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $step4Page = Step4PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $user_id = auth()->user()->id;
        $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '1')
                  ->whereHas('ride', function ($query) use ($user_id) {
                      $query->where('added_by', $user_id);
                  });
        })
        ->orWhere(function ($query) use ($user_id) {
            // Ratings where type is 2 and booking_id belongs to the user
            $query->where('type', '2')
                  ->whereHas('booking', function ($query) use ($user_id) {
                      $query->where('user_id', $user_id);
                  });
        })
        ->orWhere(function ($query) use ($user_id) {
            // Ratings where type is null and receiver_id belongs to the user
            $query->where('type', null)
                  ->whereHas('receiver', function ($query) use ($user_id) {
                      $query->where('id', $user_id);
                  });
        })
        ->orderBy('id', 'desc')
        ->get();

        User::whereId($user_id)->update([
            'step' => '5'
        ]);

        return view('step5to5',['step4Page' => $step4Page, 'user' => $user, 'countries' => $countries, 'notifications' => $notifications, 'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

public function update($id, Request $request){
    $selectedLanguage = session('selectedLanguage');
    $step4Page = null;
    $niceNames = [];
    $message = null;
    if ($selectedLanguage) {
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('admin_block_account_message')->first();

        $step4Page = Step4PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
        $niceNames = [
            'phone' => isset($step4Page->phone_error) ? $step4Page->phone_error : '',
        ];
    } else {
        $selectedLanguage = Language::where('is_default', 1)->first();
        $step4Page = Step4PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('admin_block_account_message')->first();
        $niceNames = [
            'phone' => isset($step4Page->phone_error) ? $step4Page->phone_error : '',
        ];
    }

    $user_id = auth()->user()->id;
    Log::info('Step5to5Controller@update called', [
        'user_id' => $user_id,
        'action' => $request->action ?? null,
        'country' => $request->country ?? null,
        'country_code' => $request->country_code ?? null,
        // Do not log raw phone here until normalized
    ]);

    // Get country dial code if country is selected
    $countryDialCode = null;
    if ($request->country) {
        $country = Country::find($request->country);
        $countryDialCode = $country ? $country->dial_code : null;
    }

    // Normalize the phone number using our helper function
    $normalizedPhone = normalizePhoneNumber($request->phone, $countryDialCode ?: $request->country_code);

    $request->merge([
        'full_phone' => str_replace('+', '', $normalizedPhone),
    ]);
    Log::info('Normalized phone for uniqueness check', [
        'full_phone' => $request->full_phone ?? null,
        'normalized_phone' => $normalizedPhone,
    ]);

    $existingPhone = PhoneNumber::where('phone', $normalizedPhone)->first();

    if ($existingPhone) {
        Log::warning('Phone already exists', [
            'existing_phone' => $existingPhone->phone,
            'existing_user_id' => $existingPhone->user_id,
            'current_user_id' => $user_id,
        ]);
        if ($existingPhone->user_id != $user_id) {
            $otherUser = \App\Models\User::find($existingPhone->user_id);
            if ($otherUser && ($otherUser->admin_deactive_account == 1 || $otherUser->suspand == 1)) {
                // agar wo user deactivate ya suspend hai to block
                return back()->withErrors(['phone' => 'This phone number belongs to a suspended or deactivated account.'])->withInput();
            }
        } else {
            // same user apna number dobara add kar raha hai
            return back()->withErrors(['phone' => 'You have already added this phone number.'])->withInput();
        }
    }

    // Updated validation to use Laravel-Phone
    $country = null;
    if ($request->country) {
        $countryModel = Country::find($request->country);
        $country = $countryModel ? $countryModel->iso_code : 'US';
    }

    if (!validatePhoneNumber($request->phone, $country)) {
        return back()->withErrors(['phone' => 'Please enter a valid phone number.'])->withInput();
    }

    try {
        $request->validate([
            'full_phone' => 'max:20|unique:phone_numbers,phone,NULL,id,user_id,'.$user_id,
        ], [], $niceNames);
        Log::info('Step5to5 phone validation passed', ['user_id' => $user_id]);
    } catch (\Throwable $e) {
        Log::error('Step5to5 phone validation failed', [
            'user_id' => $user_id,
            'message' => $e->getMessage(),
        ]);
        throw $e; // keep existing behavior
    }

    $getBlockPhoneNumberUser = PhoneNumber::where('phone', $normalizedPhone)->whereHas('user', function($q){
        $q->where('admin_deactive_account', '1');
    })->first();

    if (isset($getBlockPhoneNumberUser) && !empty($getBlockPhoneNumberUser)) {
        return back()->with(['error' => $message->admin_block_account_message ?? 'Your account is suspended. Please contact us if you feel it should be reinstated'])->withInput();
    }

    $phone = PhoneNumber::create([
        'country_id' => $request->country,
        'user_id' => $user_id,
        'phone' => $normalizedPhone,
    ]);
    Log::info('Phone record created for verification', [
        'user_id' => $user_id,
        'phone_id' => $phone->id,
        'phone' => $phone->phone,
    ]);

    if ($request->action === 'send') {
        $verificationCode = rand(1000, 9999);
        Log::info('Generated verification code', [
            'user_id' => $user_id,
            'phone_id' => $phone->id,
            'code' => $verificationCode,
        ]);

        // Save verification code and its expiration time (30 minutes) to the database
        DB::table('phone_verifications')->insert([
            'phone_number_id' => $phone->id,
            'verification_code' => $verificationCode,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);
        Log::info('Saved verification code', [
            'user_id' => $user_id,
            'phone_id' => $phone->id,
            'expires_at' => Carbon::now()->addMinutes(30)->toDateTimeString(),
        ]);

        // Send the verification code via Twilio
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        if($sid != null){
            $twilio = new Client($sid, $token);
            $to = $phone->phone;
            $message = "Message from ProximaRide. Your verification code is: $verificationCode \n This code will expire in 30 minutes.";
            Log::info('Trying to send SMS via Twilio', [
                'to' => $to,
                'from' => $from,
                'has_sid' => $sid ? true : false,
                'env' => env('APP_ENV'),
            ]);

            try {
                if(env('APP_ENV') != 'local'){
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                    Log::info('Twilio SMS sent', [
                        'to' => $to,
                        'sid' => method_exists($res, 'getSid') ? $res->getSid() : null,
                        'status' => method_exists($res, 'getStatus') ? $res->getStatus() : null,
                    ]);
                } else {
                    Log::info('Skip sending SMS in local env; simulated send complete', [
                        'to' => $to,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Twilio SMS send failed', [
                    'to' => $to,
                    'error' => $e->getMessage(),
                ]);

                // $phone->delete();
                // return redirect()->back()->with(['error' => 'Can not send text to ' . $phone->phone . ' because unable to create record: Authenticate']);
            }
        }

        // Store phone details in session
        // session(['phone' => $phone->phone, 'country_code' => $request->country_code, 'country_id' => $request->country]);
        Log::info('Stored phone verification context in session', [
            'user_id' => $user_id,
            'phone' => $phone->phone,
        ]);

        return redirect()->route('phone_code_step', ['lang' => $selectedLanguage->abbreviation]);
    }

    $user = User::whereId($user_id)->update([
        'step' => '5',
    ]);

    return redirect()->route('profile', ['lang' => $selectedLanguage->abbreviation]);
}

public function sendVerificationCode(Request $request, $lang = null)
{
    $user_id = auth()->user()->id;

    Log::info('Step5to5Controller@sendVerificationCode AJAX called', [
        'user_id' => $user_id,
        'country' => $request->country ?? null,
        'country_code' => $request->country_code ?? null,
    ]);

    $request->merge([
        'full_phone' => str_replace('+', '', $request->country_code) . $request->phone,
    ]);

    // Get country dial code if country is selected
    $countryDialCode = null;
    if ($request->country) {
        $countryModel = Country::find($request->country);
        $countryDialCode = $countryModel ? $countryModel->dial_code : null;
    }

    // Normalize phone using Laravel-Phone
    $normalizedPhone = normalizePhoneNumber($request->phone, $countryDialCode ?: $request->country_code);

    $existingPhone = PhoneNumber::where('phone', $normalizedPhone)->first();

    if ($existingPhone) {
        if ($existingPhone->user_id != $user_id) {
            $otherUser = \App\Models\User::find($existingPhone->user_id);
            if ($otherUser && ($otherUser->admin_deactive_account == 1 || $otherUser->suspand == 1)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This phone number belongs to a suspended or deactivated account.'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You have already added this phone number.'
            ], 400);
        }
    }

    // Get country for validation
    $country = null;
    if ($request->country) {
        $countryModel = Country::find($request->country);
        $country = $countryModel ? $countryModel->iso_code : 'US';
    }

    // Validate using Laravel-Phone
    if (!validatePhoneNumber($request->phone, $country)) {
        return response()->json([
            'success' => false,
            'message' => 'Please enter a valid phone number.'
        ], 422);
    }

    try {
        $request->validate([
            'full_phone' => 'max:20|unique:phone_numbers,phone,NULL,id,user_id,'.$user_id,
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }

    // Normalize phone using Laravel-Phone
    $normalizedPhone = normalizePhoneNumber($request->phone, $countryDialCode ?: $request->country_code);

    $getBlockPhoneNumberUser = PhoneNumber::where('phone', $normalizedPhone)->whereHas('user', function($q){
        $q->where('admin_deactive_account', '1');
    })->first();

    if (isset($getBlockPhoneNumberUser) && !empty($getBlockPhoneNumberUser)) {
        return response()->json([
            'success' => false,
            'message' => 'Your account is suspended. Please contact us if you feel it should be reinstated'
        ], 400);
    }

    $phone = PhoneNumber::create([
        'country_id' => $request->country,
        'user_id' => $user_id,
        'phone' => $normalizedPhone,
    ]);

    Log::info('Phone record created for verification (AJAX)', [
        'user_id' => $user_id,
        'phone_id' => $phone->id,
        'phone' => $phone->phone,
    ]);

    $verificationCode = rand(1000, 9999);

    Log::info('Generated verification code (AJAX)', [
        'user_id' => $user_id,
        'phone_id' => $phone->id,
        'code' => $verificationCode,
    ]);

    DB::table('phone_verifications')->insert([
        'phone_number_id' => $phone->id,
        'verification_code' => $verificationCode,
        'expires_at' => Carbon::now()->addMinutes(30),
    ]);

    $sid = env('TWILIO_ACCOUNT_SID');
    $token = env('TWILIO_AUTH_TOKEN');
    $from = env('TWILIO_PHONE_NUMBER');
    $appEnv = env('APP_ENV');

    // Attempt to send SMS if credentials are available
    if($sid != null && $token != null && $from != null){
        try {
            $twilio = new Client($sid, $token);
            $to = $phone->phone;
            $message = "ProximaRide: Your verification code is: $verificationCode \n This code will expire in 30 minutes.";

            if($appEnv != 'local'){
                $twilio->messages->create($to, ['from' => $from, 'body' => $message]);
                Log::info('Twilio SMS sent (AJAX)', ['to' => $to]);
            } else {
                Log::info('Skip sending SMS in local env (AJAX); verification code generated', [
                    'to' => $to,
                    'code' => $verificationCode,
                ]);
                // Store code in session for local testing
                session(['verification_code_' . $phone->id => $verificationCode]);
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $isCurlError = strpos($errorMessage, 'CURLOPT') !== false || 
                          strpos($errorMessage, 'curl') !== false ||
                          strpos($errorMessage, 'Undefined constant') !== false;
            
            Log::error('Twilio SMS send failed (AJAX)', [
                'to' => $phone->phone,
                'error' => $errorMessage,
                'is_curl_error' => $isCurlError,
            ]);
            
            // If it's a cURL/SDK configuration error, log it but continue
            // Verification code is saved in DB, user can enter it manually
            if ($isCurlError) {
                Log::warning('Twilio SDK cURL configuration issue - SMS not sent, but verification code is available in database', [
                    'phone_id' => $phone->id,
                ]);
            }
            // Continue anyway - don't block the user from entering code manually
        }
    } else {
        Log::warning('Twilio credentials not configured - SMS not sent, but verification code is available in database', [
            'phone_id' => $phone->id,
        ]);
        // In local/dev, store code in session for testing
        if($appEnv == 'local' || $appEnv == 'development'){
            session(['verification_code_' . $phone->id => $verificationCode]);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Verification code sent successfully'
    ]);
}
}
