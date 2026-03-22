<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Language;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\Step5PageSettingDetail;
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
     * Step 5 page
     */
    public function create($lang = null)
    {
        $user = auth()->user();
        $countries = Country::where('status', '1')->orderBy('name')->get();
        
        $step5Page = Step5PageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        // Update step
        if (request()->has('skip')) {
            User::whereId($user->id)->update([
                'step4' => 2
            ]);
        }


        return view('step5to5', [
            'step5Page' => $step5Page,
            'user' => $user,
            'countries' => $countries,
        ]);
    }

    /**
     * Update user phone & send OTP
     */
    public function update($id, Request $request)
    {
        $user_id = auth()->user()->id;

        $sessionLang = session('selectedLanguage');
        $selectedLanguage = $sessionLang
            ? Language::where('abbreviation', $sessionLang)->first() ?? Language::where('is_default', 1)->first()
            : Language::where('is_default', 1)->first();
        $step5Page = $selectedLanguage ? Step5PageSettingDetail::where('language_id', $selectedLanguage->id)->first() : null;
        $message = $selectedLanguage
            ? SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('admin_block_account_message')->first()
            : null;

        $niceNames = ['phone' => $step5Page->phone_error ?? ''];

        // Normalize phone
        $countryDialCode = optional(Country::find($request->country))->dial_code ?: $request->country_code;
        $normalizedPhone = normalizePhoneNumber($request->phone, $countryDialCode);
        $request->merge(['full_phone' => str_replace('+', '', $normalizedPhone)]);

        // Check if phone exists
        $existingPhone = PhoneNumber::where('phone', $normalizedPhone)->first();
        if ($existingPhone && $existingPhone->user_id != $user_id) {
            $otherUser = User::find($existingPhone->user_id);
            if ($otherUser && ($otherUser->admin_deactive_account || $otherUser->suspand)) {
                return back()->withErrors(['phone' => 'This phone number belongs to a suspended or deactivated account.'])->withInput();
            }
        } elseif ($existingPhone && $existingPhone->user_id == $user_id) {
            return back()->withErrors(['phone' => 'You have already added this phone number.'])->withInput();
        }

        // Validate phone
        $country = optional(Country::find($request->country))->iso_code ?: 'US';
        if (!validatePhoneNumber($request->phone, $country)) {
            return back()->withErrors(['phone' => $step5Page->phone_error_label])->withInput();
        }

        $request->validate([
            'full_phone' => 'max:20|unique:phone_numbers,phone,NULL,id,user_id,' . $user_id,
        ], [
            'full_phone.max' => 'The phone number must be less than 20 characters',
            'full_phone.unique' => 'The phone number has already been taken',
        ], $niceNames);

        // Create phone record
        $phone = PhoneNumber::create([
            'country_id' => $request->country,
            'user_id' => $user_id,
            'phone' => $normalizedPhone,
        ]);

        Log::info('Phone record created', ['phone_id' => $phone->id, 'phone' => $phone->phone]);

        // Send OTP if requested
        if ($request->action === 'send') {
            $response = $this->sendOtp($phone, $request->channel ?? 'sms');
            if ($response) {
                return $response;
            }

            // Redirect to verification step; if a return URL is present in session,
            // Phone verification flow should ultimately redirect there after success.
            return redirect()->route('phone_code_step', ['lang' => $this->selectedLanguage->abbreviation]);
        }

        User::whereId($user_id)->update(['step5' => 1]);

        // If a post-action return URL is stored in the session, prefer that.
        // This is used to send the user back to the original page after completing step 5.
        $returnUrl = session('return_url_after_action');
        if ($returnUrl) {
            return redirect($returnUrl);
        }

        return redirect()->route('profile', ['lang' => $this->selectedLanguage->abbreviation]);
    }

    /**
     * Whether the normalized phone is North American (+1).
     */
    private function isNorthAmericanNumber(string $normalizedPhone): bool
    {
        return str_starts_with($normalizedPhone, '+1');
    }

    /**
     * Send OTP via SMS or WhatsApp with fallback.
     * North American (+1): Messages API. International: Twilio Verify API (WhatsApp preferred).
     */
    private function sendOtp(PhoneNumber $phone, string $channel = 'sms')
    {
        $user_id = $phone->user_id;

        // Rate limit: max 3 attempts per number per 24h (SMS pumping protection)
        $attemptsIn24h = DB::table('phone_verifications')
            ->where('phone_number_id', $phone->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        if ($attemptsIn24h >= 3) {
            Log::warning('Max OTP attempts reached', ['phone_id' => $phone->id]);
            return back()->withErrors(['phone' => 'Maximum verification attempts (3) reached. Try again after 24 hours.']);
        }

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $verifyServiceSid = env('TWILIO_VERIFY_SERVICE_SID');

        // International: use Twilio Verify API with WhatsApp only (SMS often blocked/expensive)
        if (!$this->isNorthAmericanNumber($phone->phone)) {
            if (!$sid || !$token || !$verifyServiceSid) {
                Log::warning('Twilio Verify not configured for international send');
                return back()->withErrors(['phone' => 'Verification for international numbers is not available. Please use a North American number or try again later.'])->withInput();
            }
            $verifyChannel = 'whatsapp';
            try {
                $twilio = new Client($sid, $token);
                $verification = $twilio->verify->v2->services($verifyServiceSid)
                    ->verifications->create($phone->phone, $verifyChannel);
                DB::table('phone_verifications')->insert([
                    'phone_number_id' => $phone->id,
                    'verification_code' => '',
                    'channel' => $verifyChannel,
                    'twilio_verify_sid' => $verification->sid,
                    'expires_at' => now()->addMinutes(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info('International OTP sent via Twilio Verify', ['phone' => $phone->phone, 'channel' => $verifyChannel]);
            } catch (\Exception $e) {
                Log::error('Twilio Verify send failed', ['phone_id' => $phone->id, 'phone' => $phone->phone, 'error' => $e->getMessage()]);
                return back()->withErrors(['phone' => $this->twilioErrorMessageForUser($e->getMessage(), 'sms')])->withInput();
            }
            return redirect()->route('phone_code_step', ['lang' => $this->selectedLanguage->abbreviation ?? 'en']);
        }

        // North American (+1): Messages API with our own OTP (SMS only)
        $otp = random_int(1000, 9999);
        DB::table('phone_verifications')->insert([
            'phone_number_id' => $phone->id,
            'verification_code' => (string) $otp,
            'channel' => 'sms',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $messagingServiceSid = env('TWILIO_MESSAGING_SERVICE_SID');
        $smsFrom = env('TWILIO_SMS_FROM') ?: env('TWILIO_PHONE_NUMBER');
        $twilio = new Client($sid, $token);

        $smsParams = ['body' => "ProximaRide code: {$otp}. Expires in 10 minutes."];
        if ($messagingServiceSid) {
            $smsParams['messagingServiceSid'] = $messagingServiceSid;
        } else {
            $smsParams['from'] = $smsFrom;
        }

        $message = $twilio->messages->create($phone->phone, $smsParams);
        Log::info('OTP sent via SMS', ['phone' => $phone->phone, 'otp' => $otp, 'sid' => $message->sid, 'status' => $message->status]);
    }

    /**
     * AJAX: Send verification code (SMS or WhatsApp). Used by step5to5 blade for "Send code" / resend.
     */
    public function sendVerificationCode(Request $request, $lang = null)
    {
        $request->merge(['channel' => $request->input('channel', 'sms')]);
        return $this->sendVerificationCodeJson($request);
    }

    /**
     * AJAX: Send verification code via WhatsApp only.
     */
    public function sendVerificationCodeWhatsApp(Request $request, $lang = null)
    {
        $request->merge(['channel' => 'whatsapp']);
        return $this->sendVerificationCodeJson($request);
    }

    /**
     * Shared AJAX logic: validate, find/create phone, rate limit, send OTP, return JSON.
     */
    private function sendVerificationCodeJson(Request $request)
    {
        $user_id = auth()->user()->id;
        $channel = $request->input('channel', 'sms');

        $countryDialCode = $request->country
            ? optional(Country::find($request->country))->dial_code
            : $request->country_code;
        $normalizedPhone = normalizePhoneNumber($request->phone, $countryDialCode);

        $step5Page = Step5PageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $country = $request->country
            ? optional(Country::find($request->country))->iso_code
            : 'US';
        if (!validatePhoneNumber($request->phone, $country)) {
            return response()->json(['success' => false, 'message' => $step5Page->phone_error_label], 422);
        }

        $existingPhone = PhoneNumber::where('phone', $normalizedPhone)->first();
        if ($existingPhone && $existingPhone->user_id != $user_id) {
            $otherUser = User::find($existingPhone->user_id);
            if ($otherUser && ($otherUser->admin_deactive_account || $otherUser->suspand)) {
                return response()->json(['success' => false, 'message' => 'This phone number belongs to a suspended or deactivated account.'], 400);
            }
        }
        if ($existingPhone && $existingPhone->user_id == $user_id) {
            $phone = $existingPhone;
        } else {
            $request->merge(['full_phone' => str_replace('+', '', $normalizedPhone)]);
            $request->validate([
                'full_phone' => 'max:20|unique:phone_numbers,phone,NULL,id,user_id,' . $user_id,
            ]);
            $phone = PhoneNumber::create([
                'country_id' => $request->country,
                'user_id' => $user_id,
                'phone' => $normalizedPhone,
            ]);
        }

        $attemptsIn24h = DB::table('phone_verifications')
            ->where('phone_number_id', $phone->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
        if ($attemptsIn24h >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum verification attempts (3) reached for this number. Try again after 24 hours.',
            ], 429);
        }

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $verifyServiceSid = env('TWILIO_VERIFY_SERVICE_SID');
        $messagingServiceSid = env('TWILIO_MESSAGING_SERVICE_SID');
        $smsFrom = env('TWILIO_SMS_FROM') ?: env('TWILIO_PHONE_NUMBER');
        $whatsappFrom = env('TWILIO_WHATSAPP_FROM');
        $whatsappUnavailable = false;
        $isNorthAmerican = $this->isNorthAmericanNumber($normalizedPhone);

        if (!$sid || !$token) {
            return response()->json([
                'success' => false,
                'message' => 'Verification by text or WhatsApp is temporarily unavailable. Please try again later.',
            ], 500);
        }

        // International: Twilio Verify API (WhatsApp or SMS) — avoids expensive international SMS
        if (!$isNorthAmerican) {
            if (!$verifyServiceSid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification for international numbers is not configured. Please use a North American number or contact support.',
                ], 500);
            }
            // International: use WhatsApp only (SMS often blocked/expensive for many prefixes)
            $verifyChannel = 'whatsapp';
            try {
                $twilio = new Client($sid, $token);
                $verification = $twilio->verify->v2->services($verifyServiceSid)
                    ->verifications->create($phone->phone, $verifyChannel);
                DB::table('phone_verifications')->insert([
                    'phone_number_id' => $phone->id,
                    'verification_code' => '',
                    'channel' => $verifyChannel,
                    'twilio_verify_sid' => $verification->sid,
                    'expires_at' => now()->addMinutes(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info('International verification sent via Twilio Verify', ['phone' => $phone->phone, 'channel' => $verifyChannel]);
            } catch (\Exception $e) {
                Log::error('Twilio Verify send failed', ['phone_id' => $phone->id, 'phone' => $phone->phone, 'error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => $this->twilioErrorMessageForUser($e->getMessage(), $verifyChannel),
                ], 500);
            }
            return response()->json([
                'success' => true,
                'message' => 'Verification code sent successfully',
                'is_north_american' => false,
                'channel' => $verifyChannel,
                'remaining_attempts' => max(0, 3 - ($attemptsIn24h + 1)),
                'whatsapp_unavailable' => false,
            ]);
        }

        // North American (+1): Messages API with our own 4-digit OTP
        $otp = random_int(1000, 9999);
        DB::table('phone_verifications')->insert([
            'phone_number_id' => $phone->id,
            'verification_code' => (string) $otp,
            'channel' => $channel,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $canSendWhatsApp = $channel === 'whatsapp' && ($messagingServiceSid || $whatsappFrom);
        $canSendSms = $smsFrom || $messagingServiceSid;
        if ($channel === 'whatsapp' && !$canSendWhatsApp) {
            $whatsappUnavailable = true;
            if (!$canSendSms) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification by text or WhatsApp is temporarily unavailable. Please try again later.',
                ], 500);
            }
        } elseif (!$canSendSms) {
            return response()->json([
                'success' => false,
                'message' => 'Verification by text or WhatsApp is temporarily unavailable. Please try again later.',
            ], 500);
        }

        $twilio = new Client($sid, $token);
        try {
            if ($channel === 'whatsapp' && $canSendWhatsApp) {
                Log::info('Sending WhatsApp message', ['phone' => $phone->phone, 'otp' => $otp, 'via' => $messagingServiceSid ? 'messaging_service' : 'from']);
                $whatsappParams = ['body' => "ProximaRide code: {$otp}. Expires in 10 minutes."];
                if ($messagingServiceSid) {
                    $whatsappParams['messagingServiceSid'] = $messagingServiceSid;
                } else {
                    $whatsappParams['from'] = 'whatsapp:' . $whatsappFrom;
                }
                $message = $twilio->messages->create("whatsapp:{$phone->phone}", $whatsappParams);
                Log::info('WhatsApp message result', ['phone' => $phone->phone, 'sid' => $message->sid, 'status' => $message->status]);
            } else {
                Log::info('Sending SMS message', ['phone' => $phone->phone, 'otp' => $otp]);
                if ($channel === 'whatsapp' && !$canSendWhatsApp) {
                    $whatsappUnavailable = true;
                }
                $smsParams = $this->buildSmsParams($otp, $smsFrom, $messagingServiceSid);
                try {
                    $message = $twilio->messages->create($phone->phone, $smsParams);
                    Log::info('SMS message result', ['phone' => $phone->phone, 'sid' => $message->sid, 'status' => $message->status]);
                } catch (\Exception $smsEx) {
                    if ($smsFrom && (str_contains($smsEx->getMessage(), '21704') || str_contains($smsEx->getMessage(), 'no phone numbers'))) {
                        Log::warning('Messaging Service has no numbers, retrying with From number', ['phone' => $phone->phone]);
                        $smsParams = ['body' => "ProximaRide code: {$otp}. Expires in 10 minutes.", 'from' => $smsFrom];
                        $message = $twilio->messages->create($phone->phone, $smsParams);
                        Log::info('SMS message result', ['phone' => $phone->phone, 'sid' => $message->sid, 'status' => $message->status]);
                    } else {
                        throw $smsEx;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Send verification code failed', ['phone_id' => $phone->id, 'error' => $e->getMessage()]);
            $userMessage = $this->twilioErrorMessageForUser($e->getMessage(), $channel);
            if ($channel === 'whatsapp' && $canSendSms) {
                try {
                    $smsFallbackParams = $this->buildSmsParams($otp, $smsFrom, $messagingServiceSid);
                    $message = $twilio->messages->create($phone->phone, $smsFallbackParams);
                    Log::info('SMS fallback message result', ['phone' => $phone->phone, 'sid' => $message->sid, 'status' => $message->status]);
                    $whatsappUnavailable = true;
                } catch (\Exception $e2) {
                    if ($smsFrom && (str_contains($e2->getMessage(), '21704') || str_contains($e2->getMessage(), 'no phone numbers'))) {
                        Log::warning('Messaging Service has no numbers, retrying with From number', ['phone' => $phone->phone]);
                        $message = $twilio->messages->create($phone->phone, ['body' => "ProximaRide code: {$otp}. Expires in 10 minutes.", 'from' => $smsFrom]);
                        Log::info('SMS fallback message result', ['phone' => $phone->phone, 'sid' => $message->sid, 'status' => $message->status]);
                        $whatsappUnavailable = true;
                    } else {
                        return response()->json(['success' => false, 'message' => $this->twilioErrorMessageForUser($e2->getMessage(), 'whatsapp_sms_fallback')], 500);
                    }
                }
            } else {
                return response()->json(['success' => false, 'message' => $userMessage], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent successfully',
            'is_north_american' => true,
            'channel' => $whatsappUnavailable ? 'sms' : $channel,
            'remaining_attempts' => max(0, 3 - ($attemptsIn24h + 1)),
            'whatsapp_unavailable' => $whatsappUnavailable,
        ]);
    }

    /**
     * Build SMS params. Prefer 'from' number when set to avoid error 21704 (Messaging Service contains no phone numbers).
     */
    private function buildSmsParams(int $otp, ?string $smsFrom, ?string $messagingServiceSid): array
    {
        $params = ['body' => "ProximaRide code: {$otp}. Expires in 10 minutes."];
        if ($smsFrom) {
            $params['from'] = $smsFrom;
        } elseif ($messagingServiceSid) {
            $params['messagingServiceSid'] = $messagingServiceSid;
        }
        return $params;
    }

    /**
     * Return a user-friendly message for known Twilio errors (e.g. geo/region restrictions).
     */
    private function twilioErrorMessageForUser(string $twilioError, string $context = 'sms'): string
    {
        $lower = strtolower($twilioError);
        if (str_contains($lower, 'permission to send') && (str_contains($lower, 'region') || str_contains($lower, 'country'))) {
            return 'Verification by SMS or WhatsApp is not available for your country/region. Please try WhatsApp if you chose SMS, or use a phone number from a supported region.';
        }
        if (str_contains($lower, 'geographic') || str_contains($lower, 'geo permissions')) {
            return 'Verification is not available for your region. Please try WhatsApp or use a number from a supported country.';
        }
        if (str_contains($lower, 'whatsapp') && str_contains($lower, 'disabled')) {
            return 'WhatsApp is not available for this number. Please try sending the code via SMS instead.';
        }
        if ($context === 'whatsapp_sms_fallback') {
            return 'We couldn\'t send the code via WhatsApp or SMS. Please check your phone number and try again in a few minutes.';
        }
        return 'We couldn\'t send the SMS. Please check your phone number and try again.';
    }
}
