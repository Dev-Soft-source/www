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
    /**
     * Check rate limiting for phone verification (max 3 attempts in last 24 hours)
     * 
     * @param string $phoneNumber Normalized phone number in E.164 format
     * @return array ['allowed' => bool, 'attempts' => int, 'message' => string|null]
     */
    private function checkRateLimit($phoneNumber)
    {
        $phoneNumberRecord = PhoneNumber::where('phone', $phoneNumber)->first();
        
        if (!$phoneNumberRecord) {
            return ['allowed' => true, 'attempts' => 0, 'message' => null];
        }

        // Count verification attempts in the last 24 hours
        $attemptsCount = DB::table('phone_verifications')
            ->where('phone_number_id', $phoneNumberRecord->id)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->count();

        if ($attemptsCount >= 3) {
            return [
                'allowed' => false,
                'attempts' => $attemptsCount,
                'message' => 'Maximum verification attempts (3) reached. Please try again after 24 hours.'
            ];
        }

        return [
            'allowed' => true,
            'attempts' => $attemptsCount,
            'message' => null
        ];
    }

    /**
     * Send verification code using Twilio Verify API
     * 
     * @param string $phoneNumber Normalized phone number in E.164 format
     * @param int $phoneNumberId Phone number database ID
     * @return array ['success' => bool, 'verify_sid' => string|null, 'channel' => string, 'error' => string|null]
     */
    private function sendVerificationViaTwilioVerify($phoneNumber, $phoneNumberId)
    {
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $verifyServiceSid = env('TWILIO_VERIFY_SERVICE_SID');
        $appEnv = env('APP_ENV');

        // Check if Twilio credentials are configured
        if (!$sid || !$token || !$verifyServiceSid) {
            Log::warning('Twilio Verify credentials missing', [
                'has_sid' => $sid ? true : false,
                'has_token' => $token ? true : false,
                'has_verify_service_sid' => $verifyServiceSid ? true : false,
            ]);
            return [
                'success' => false,
                'verify_sid' => null,
                'channel' => 'sms',
                'error' => 'Twilio Verify Service not configured'
            ];
        }

        // Determine channel based on phone number
        $isNorthAmerican = isNorthAmericanNumber($phoneNumber);
        $channel = $isNorthAmerican ? 'sms' : 'whatsapp';

        Log::info('Sending verification via Twilio Verify API', [
            'phone' => $phoneNumber,
            'channel' => $channel,
            'is_north_american' => $isNorthAmerican,
            'env' => $appEnv,
        ]);

        // In local/development environment, skip actual sending
        if ($appEnv == 'local' || $appEnv == 'development') {
            Log::info('Skip sending verification in ' . $appEnv . ' environment', [
                'phone' => $phoneNumber,
                'channel' => $channel,
            ]);
            return [
                'success' => true,
                'verify_sid' => 'test_verify_sid_' . time(),
                'channel' => $channel,
                'error' => null
            ];
        }

        try {
            $twilio = new Client($sid, $token);
            
            // Create verification using Twilio Verify API
            $verification = $twilio->verify->v2->services($verifyServiceSid)
                ->verifications
                ->create($phoneNumber, ['channel' => $channel]);

            $verifySid = $verification->sid;
            $status = $verification->status;

            Log::info('Twilio Verify API verification sent', [
                'phone' => $phoneNumber,
                'channel' => $channel,
                'verify_sid' => $verifySid,
                'status' => $status,
            ]);

            return [
                'success' => true,
                'verify_sid' => $verifySid,
                'channel' => $channel,
                'error' => null
            ];
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('Twilio Verify API send failed', [
                'phone' => $phoneNumber,
                'channel' => $channel,
                'error' => $errorMessage,
            ]);

            return [
                'success' => false,
                'verify_sid' => null,
                'channel' => $channel,
                'error' => $errorMessage
            ];
        }
    }
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
        // Check rate limiting (max 3 attempts in last 24 hours)
        $rateLimitCheck = $this->checkRateLimit($normalizedPhone);
        if (!$rateLimitCheck['allowed']) {
            return back()->withErrors(['phone' => $rateLimitCheck['message']])->withInput();
        }

        // Determine if we should use Twilio Verify API (for international) or Messages API (for +1)
        $isNorthAmerican = isNorthAmericanNumber($normalizedPhone);
        $appEnv = env('APP_ENV');
        $verificationSent = false;
        $error = null;

        if ($isNorthAmerican) {
            // For North American numbers (+1), use traditional SMS via Messages API
            $verificationCode = rand(1000, 9999);
            Log::info('Generated verification code for North American number', [
                'user_id' => $user_id,
                'phone_id' => $phone->id,
                'code' => $verificationCode,
            ]);

            // Save verification code and its expiration time (30 minutes) to the database
            DB::table('phone_verifications')->insert([
                'phone_number_id' => $phone->id,
                'verification_code' => $verificationCode,
                'channel' => 'sms',
                'expires_at' => Carbon::now()->addMinutes(30),
            ]);

            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_PHONE_NUMBER');

            if($sid != null && $token != null && $from != null){
                $twilio = new Client($sid, $token);
                $to = $phone->phone;
                $message = "ProximaRide: Your verification code is: $verificationCode \n This code will expire in 30 minutes.";
                
                try {
                    if($appEnv != 'local'){
                        $res = $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $message,
                            ]
                        );
                        $verificationSent = true;
                        Log::info('Twilio SMS sent (North American)', [
                            'to' => $to,
                            'sid' => method_exists($res, 'getSid') ? $res->getSid() : null,
                        ]);
                    } else {
                        Log::info('Skip sending SMS in local env; verification code generated', [
                            'to' => $to,
                            'code' => $verificationCode,
                        ]);
                        session(['verification_code_' . $phone->id => $verificationCode]);
                        $verificationSent = true;
                    }
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                    Log::error('Twilio SMS send failed', [
                        'to' => $to,
                        'error' => $error,
                    ]);
                }
            } else {
                $error = 'Twilio credentials not configured';
                if($appEnv == 'local' || $appEnv == 'development'){
                    session(['verification_code_' . $phone->id => $verificationCode]);
                    $verificationSent = true;
                }
            }
        } else {
            // For international numbers, use Twilio Verify API with WhatsApp channel
            $verifyResult = $this->sendVerificationViaTwilioVerify($normalizedPhone, $phone->id);
            
            if ($verifyResult['success']) {
                // Store verification record with Twilio Verify SID
                DB::table('phone_verifications')->insert([
                    'phone_number_id' => $phone->id,
                    'verification_code' => '', // Twilio manages the code
                    'channel' => $verifyResult['channel'],
                    'twilio_verify_sid' => $verifyResult['verify_sid'],
                    'expires_at' => Carbon::now()->addMinutes(30),
                ]);
                $verificationSent = true;
                Log::info('Verification sent via Twilio Verify API (International)', [
                    'phone' => $normalizedPhone,
                    'channel' => $verifyResult['channel'],
                    'verify_sid' => $verifyResult['verify_sid'],
                ]);
            } else {
                $error = $verifyResult['error'];
                Log::error('Twilio Verify API send failed', [
                    'phone' => $normalizedPhone,
                    'error' => $error,
                ]);
            }
        }

        if (!$verificationSent && $error) {
            return back()->withErrors(['phone' => 'Failed to send verification code: ' . $error])->withInput();
        }

        // Store phone details in session
        Log::info('Stored phone verification context in session', [
            'user_id' => $user_id,
            'phone' => $phone->phone,
            'is_north_american' => $isNorthAmerican,
        ]);

        // Preserve return URL if it exists in session (from booking/ride detail pages)
        // If not set, it means user came directly to phone verification, so don't set one
        $returnUrl = session('return_url_after_action');
        if (!$returnUrl) {
            // Only set return URL if we have a referrer that's not the phone verification page itself
            $referrer = request()->headers->get('referer');
            if ($referrer && !str_contains($referrer, 'phone') && !str_contains($referrer, 'step5')) {
                session(['return_url_after_action' => $referrer]);
            }
        }

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

    // Check rate limiting (max 3 attempts in last 24 hours)
    $rateLimitCheck = $this->checkRateLimit($normalizedPhone);
    if (!$rateLimitCheck['allowed']) {
        return response()->json([
            'success' => false,
            'message' => $rateLimitCheck['message']
        ], 429); // 429 Too Many Requests
    }

    // Determine if we should use Twilio Verify API (for international) or Messages API (for +1)
    $isNorthAmerican = isNorthAmericanNumber($normalizedPhone);
    $appEnv = env('APP_ENV');
    $verificationSent = false;
    $error = null;
    $channel = 'sms';
    $verificationCode = null;

    if ($isNorthAmerican) {
        // For North American numbers (+1), use traditional SMS via Messages API
        $verificationCode = rand(1000, 9999);
        $channel = 'sms';
        
        Log::info('Generated verification code for North American number (AJAX)', [
            'user_id' => $user_id,
            'phone_id' => $phone->id,
            'code' => $verificationCode,
        ]);

        // Save verification code and its expiration time (30 minutes) to the database
        DB::table('phone_verifications')->insert([
            'phone_number_id' => $phone->id,
            'verification_code' => $verificationCode,
            'channel' => 'sms',
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        if($sid != null && $token != null && $from != null){
            $twilio = new Client($sid, $token);
            $to = $phone->phone;
            $message = "ProximaRide: Your verification code is: $verificationCode \n This code will expire in 30 minutes.";

            try {
                if($appEnv != 'local'){
                    $twilio->messages->create($to, ['from' => $from, 'body' => $message]);
                    $verificationSent = true;
                    Log::info('Twilio SMS sent (AJAX - North American)', ['to' => $to]);
                } else {
                    Log::info('Skip sending SMS in local env (AJAX); verification code generated', [
                        'to' => $to,
                        'code' => $verificationCode,
                    ]);
                    session(['verification_code_' . $phone->id => $verificationCode]);
                    $verificationSent = true;
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
                Log::error('Twilio SMS send failed (AJAX)', [
                    'to' => $to,
                    'error' => $error,
                ]);
            }
        } else {
            $error = 'Twilio credentials not configured';
            if($appEnv == 'local' || $appEnv == 'development'){
                session(['verification_code_' . $phone->id => $verificationCode]);
                $verificationSent = true;
            }
        }
    } else {
        // For international numbers, use Twilio Verify API with WhatsApp channel
        $verifyResult = $this->sendVerificationViaTwilioVerify($normalizedPhone, $phone->id);
        $channel = $verifyResult['channel'];
        
        if ($verifyResult['success']) {
            // Store verification record with Twilio Verify SID
            DB::table('phone_verifications')->insert([
                'phone_number_id' => $phone->id,
                'verification_code' => '', // Twilio manages the code
                'channel' => $verifyResult['channel'],
                'twilio_verify_sid' => $verifyResult['verify_sid'],
                'expires_at' => Carbon::now()->addMinutes(30),
            ]);
            $verificationSent = true;
            Log::info('Verification sent via Twilio Verify API (AJAX - International)', [
                'phone' => $normalizedPhone,
                'channel' => $verifyResult['channel'],
                'verify_sid' => $verifyResult['verify_sid'],
            ]);
        } else {
            $error = $verifyResult['error'];
            Log::error('Twilio Verify API send failed (AJAX)', [
                'phone' => $normalizedPhone,
                'error' => $error,
            ]);
        }
    }

    if (!$verificationSent) {
        return response()->json([
            'success' => false,
            'message' => $error ?: 'Failed to send verification code. Please try again.'
        ], 500);
    }

    $response = [
        'success' => true,
        'message' => 'Verification code sent successfully',
        'channel' => $channel,
        'is_north_american' => $isNorthAmerican,
    ];

    // In local/development, include the code in response for testing (only for SMS)
    if(($appEnv == 'local' || $appEnv == 'development') && $isNorthAmerican && $verificationCode){
        $response['verification_code'] = $verificationCode;
        $response['message'] = 'Verification code generated (SMS not sent in ' . $appEnv . ' environment)';
    }

    return response()->json($response);
}
}
