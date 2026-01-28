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
    $channel = $request->channel ?? 'sms'; // 'sms' or 'whatsapp'

    Log::info('Step5to5Controller@sendVerificationCode AJAX called', [
        'user_id' => $user_id,
        'country' => $request->country ?? null,
        'country_code' => $request->country_code ?? null,
        'channel' => $channel,
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

    // Check if phone number already exists
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
            // Same user, use existing phone record (allow resending verification)
            $phone = $existingPhone;
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

    // Only validate uniqueness if phone doesn't exist yet
    if (!isset($phone)) {
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
    }

    // Normalize phone using Laravel-Phone (again to ensure consistency)
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

    // Create phone record if it doesn't exist
    if (!isset($phone)) {
        $phone = PhoneNumber::create([
            'country_id' => $request->country,
            'user_id' => $user_id,
            'phone' => $normalizedPhone,
        ]);
    }

    Log::info('Phone record ready for verification (AJAX)', [
        'user_id' => $user_id,
        'phone_id' => $phone->id,
        'phone' => $phone->phone,
    ]);

    // Rate Limiting: Check if phone number has exceeded max attempts (3) in last 24 hours
    $attemptsInLast24Hours = DB::table('phone_verifications')
        ->where('phone_number_id', $phone->id)
        ->where('created_at', '>=', Carbon::now()->subHours(24))
        ->count();

    if ($attemptsInLast24Hours >= 3) {
        Log::warning('Rate limit exceeded for phone verification', [
            'phone_id' => $phone->id,
            'phone' => $phone->phone,
            'attempts' => $attemptsInLast24Hours,
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Maximum verification attempts (3) reached for this number. Please try again after 24 hours.'
        ], 429);
    }

    // Check if number is North American (+1)
    $isNorthAmerican = isNorthAmericanNumber($normalizedPhone);

    $sid = env('TWILIO_ACCOUNT_SID');
    $token = env('TWILIO_AUTH_TOKEN');
    $from = env('TWILIO_PHONE_NUMBER');
    $verifyServiceSid = env('TWILIO_VERIFY_SERVICE_SID');
    $appEnv = env('APP_ENV');

    if ($isNorthAmerican) {
        // North American numbers: Use standard SMS
        $verificationCode = rand(1000, 9999);

        Log::info('Generated verification code for North American number (AJAX)', [
            'user_id' => $user_id,
            'phone_id' => $phone->id,
            'code' => $verificationCode,
        ]);

        DB::table('phone_verifications')->insert([
            'phone_number_id' => $phone->id,
            'verification_code' => $verificationCode,
            'channel' => 'sms',
            'expires_at' => Carbon::now()->addMinutes(30),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Send SMS via standard Twilio Messages API
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
                    session(['verification_code_' . $phone->id => $verificationCode]);
                }
            } catch (\Exception $e) {
                Log::error('Twilio SMS send failed (AJAX)', [
                    'to' => $phone->phone,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    } else {
        // International numbers: Use Twilio Verify API with WhatsApp support
        if (!$verifyServiceSid) {
            Log::error('Twilio Verify Service SID not configured', [
                'phone_id' => $phone->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Verification service is not properly configured. Please contact support.'
            ], 500);
        }

        try {
            $twilio = new Client($sid, $token);
            
            // Determine channel: 'whatsapp' if requested, otherwise 'sms' (Twilio Verify will try both)
            $verifyChannel = ($channel === 'whatsapp') ? 'whatsapp' : 'sms';
            
            // Create verification using Twilio Verify API
            $verification = $twilio->verify->v2->services($verifyServiceSid)
                ->verifications
                ->create($phone->phone, $verifyChannel);

            $verifySid = $verification->sid;

            Log::info('Twilio Verify API verification created (AJAX)', [
                'phone_id' => $phone->id,
                'phone' => $phone->phone,
                'verify_sid' => $verifySid,
                'channel' => $verifyChannel,
                'status' => $verification->status,
            ]);

            // Store verification record (code is managed by Twilio, not stored in DB)
            DB::table('phone_verifications')->insert([
                'phone_number_id' => $phone->id,
                'verification_code' => '', // Empty - code is managed by Twilio Verify
                'channel' => $verifyChannel,
                'twilio_verify_sid' => $verifySid,
                'expires_at' => Carbon::now()->addMinutes(10), // Twilio Verify codes expire in 10 minutes
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            // Check if WhatsApp channel is disabled and fall back to SMS
            if ($channel === 'whatsapp' && strpos($errorMessage, 'Delivery channel disabled: WHATSAPP') !== false) {
                Log::warning('WhatsApp channel disabled, falling back to SMS (AJAX)', [
                    'phone_id' => $phone->id,
                    'phone' => $phone->phone,
                ]);
                
                try {
                    // Retry with SMS channel
                    $verification = $twilio->verify->v2->services($verifyServiceSid)
                        ->verifications
                        ->create($phone->phone, 'sms');
                    
                    $verifySid = $verification->sid;
                    
                    Log::info('Twilio Verify API verification created via SMS fallback (AJAX)', [
                        'phone_id' => $phone->id,
                        'phone' => $phone->phone,
                        'verify_sid' => $verifySid,
                        'channel' => 'sms',
                        'status' => $verification->status,
                    ]);
                    
                    // Store verification record with SMS channel
                    DB::table('phone_verifications')->insert([
                        'phone_number_id' => $phone->id,
                        'verification_code' => '',
                        'channel' => 'sms',
                        'twilio_verify_sid' => $verifySid,
                        'expires_at' => Carbon::now()->addMinutes(10),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    
                    // Return success but indicate SMS was used instead
                    return response()->json([
                        'success' => true,
                        'message' => 'Verification code sent via SMS (WhatsApp not available).',
                        'is_north_american' => false,
                        'channel' => 'sms',
                        'whatsapp_unavailable' => true,
                        'remaining_attempts' => max(0, 3 - ($attemptsInLast24Hours + 1))
                    ]);
                    
                } catch (\Exception $smsFallbackError) {
                    Log::error('Twilio Verify API SMS fallback also failed (AJAX)', [
                        'phone_id' => $phone->id,
                        'phone' => $phone->phone,
                        'error' => $smsFallbackError->getMessage(),
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to send verification code. WhatsApp is not available and SMS delivery failed. Please contact support.'
                    ], 500);
                }
            }
            
            Log::error('Twilio Verify API failed (AJAX)', [
                'phone_id' => $phone->id,
                'phone' => $phone->phone,
                'error' => $errorMessage,
                'channel' => $verifyChannel,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again or contact support.'
            ], 500);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Verification code sent successfully',
        'is_north_american' => $isNorthAmerican,
        'channel' => $isNorthAmerican ? 'sms' : ($channel === 'whatsapp' ? 'whatsapp' : 'sms'),
        'remaining_attempts' => max(0, 3 - ($attemptsInLast24Hours + 1)),
        'whatsapp_unavailable' => false
    ]);
}

public function sendVerificationCodeWhatsApp(Request $request, $lang = null)
{
    // Same logic as sendVerificationCode but force channel to 'whatsapp'
    $request->merge(['channel' => 'whatsapp']);
    return $this->sendVerificationCode($request, $lang);
}
}
