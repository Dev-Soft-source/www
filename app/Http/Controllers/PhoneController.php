<?php

namespace App\Http\Controllers;

use App\Mail\PhoneNumberAddedMail;
use App\Mail\PhoneNumberDeleted;
use App\Models\Country;
use App\Models\FCMToken;
use App\Models\Language;
use App\Models\MyPhoneSettingDetail;
use App\Models\MyReviewSettingDetail;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\Step4PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use Illuminate\Support\Str;


class PhoneController extends Controller
{
    /**
     * Check verification code using Twilio Verify API
     * 
     * @param string $phoneNumber Normalized phone number in E.164 format
     * @param string $code Verification code to check
     * @param string $verifySid Twilio Verify SID
     * @return array ['success' => bool, 'status' => string|null, 'error' => string|null]
     */
    private function checkVerificationViaTwilioVerify($phoneNumber, $code, $verifySid)
    {
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $verifyServiceSid = env('TWILIO_VERIFY_SERVICE_SID');
        $appEnv = env('APP_ENV');

        // Check if Twilio credentials are configured
        if (!$sid || !$token || !$verifyServiceSid) {
            Log::warning('Twilio Verify credentials missing for verification check', [
                'has_sid' => $sid ? true : false,
                'has_token' => $token ? true : false,
                'has_verify_service_sid' => $verifyServiceSid ? true : false,
            ]);
            return [
                'success' => false,
                'status' => null,
                'error' => 'Twilio Verify Service not configured'
            ];
        }

        // In local/development environment, skip actual check (accept any code for testing)
        if ($appEnv == 'local' || $appEnv == 'development') {
            Log::info('Skip Twilio Verify check in ' . $appEnv . ' environment (accepting code)', [
                'phone' => $phoneNumber,
                'code' => $code,
            ]);
            return [
                'success' => true,
                'status' => 'approved',
                'error' => null
            ];
        }

        try {
            $twilio = new Client($sid, $token);
            
            // Check verification using Twilio Verify API
            $verificationCheck = $twilio->verify->v2->services($verifyServiceSid)
                ->verificationChecks
                ->create($phoneNumber, ['code' => $code]);

            $status = $verificationCheck->status;

            Log::info('Twilio Verify API verification check', [
                'phone' => $phoneNumber,
                'status' => $status,
                'verify_sid' => $verifySid,
            ]);

            return [
                'success' => $status === 'approved',
                'status' => $status,
                'error' => $status === 'approved' ? null : 'Verification code is invalid or expired'
            ];
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('Twilio Verify API check failed', [
                'phone' => $phoneNumber,
                'code' => $code,
                'error' => $errorMessage,
            ]);

            return [
                'success' => false,
                'status' => null,
                'error' => $errorMessage
            ];
        }
    }
    public function index($lang = null)
    {
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        $phoneSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
                $phoneSetting = MyPhoneSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
                $phoneSetting = MyPhoneSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $phone_numbers = PhoneNumber::where('user_id', $user_id)->orderByRaw('`default` DESC, verified DESC')->orderBy('id', 'desc')->get();

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
            $countries = Country::where('status', '1')->orderBy('name', 'asc')->get();
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();
            return view('phone', ['user' => $user, 'countries' => $countries, 'phoneSetting' => $phoneSetting, 'reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 'ProfileSetting' => $ProfileSetting, 'phone_numbers' => $phone_numbers, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    public function store(Request $request)
    {

        // $user_id = auth()->user()->id;
        $user = auth()->user();
        $user_id = $user->id;

        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_add_message', 'suspended_account_phone_number_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_add_message', 'suspended_account_phone_number_message')->first();
            }
        }

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

        $existingPhone = PhoneNumber::where('phone', $normalizedPhone)->first();

        if ($existingPhone) {
            if ($existingPhone->user_id != $user_id) {
                $otherUser = \App\Models\User::find($existingPhone->user_id);
                if ($otherUser && ($otherUser->admin_deactive_account == 1 || $otherUser->suspand == 1)) {
                    // agar wo user deactivate ya suspend hai to block
                    return back()->withErrors(['phone' => $message->suspended_account_phone_number_message ?? 'This phone number belongs to a suspended or deactivated account.'])->withInput();
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

        $request->validate([
            'full_phone' => 'max:20|unique:phone_numbers,phone,NULL,user_id',
        ]);

        $phone = PhoneNumber::create([
            'user_id' => $user_id,
            'phone' => $normalizedPhone,
            'country_id' => $request->country,
        ]);
        $emailData = [
            'first_name' => $user->first_name,
        ];
        if (isset($user->email_notification) && $user->email_notification == 1) {
            Mail::to($user->email)->send(new PhoneNumberAddedMail($emailData));
        }

        $notification = Notification::create([
            'type' => null,
            'category' => 'system',
            'receiver_id' => $user->id,
            'posted_by' => $user->id,
            'message' =>  'A new phone number added to your profile',
            'status' => 'phone',
            'notification_type' => 'phone',
        ]);

        $body = $notification->message;
        $fcmService = new FCMService();

        $fcmToken = $user->mobile_fcm_token;
        if ($fcmToken) {
            $fcmService->sendNotification($fcmToken, $body);
        }

        $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

        foreach ($fcm_tokens as $fcm_token) {
            try {
                $fcmService->sendNotification($fcm_token->token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
            }
        }

        if ($request->action === 'send') {
            $verificationCode = rand(1000, 9999);

            // Save verification code and its expiration time (30 minutes) to the database
            DB::table('phone_verifications')->insert([
                'phone_number_id' => $phone->id,
                'verification_code' => $verificationCode,
                'expires_at' => Carbon::now()->addMinutes(30),
            ]);

            // Send the verification code via Twilio
            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_PHONE_NUMBER');

            $twilio = new Client($sid, $token);
            $to = $phone->phone;
            $message = "ProximaRide: Your verification code is: $verificationCode \n This code will expire in 30 minutes." ;

            try {
                if (env('APP_ENV') != 'local') {
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                }
            } catch (\Exception  $e) {
                Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                $phone->delete();
                return redirect()->back()->with(['error' => 'Can not send text to ' . $phone->phone . ' because unable to create record: Authenticate']);
            }

            // Preserve return URL if it exists in session (from booking/ride detail pages)
            $returnUrl = session('return_url_after_action');
            if (!$returnUrl) {
                // Only set return URL if we have a referrer that's not the phone page itself
                $referrer = request()->headers->get('referer');
                if ($referrer && !str_contains($referrer, 'phone') && !str_contains($referrer, 'step5')) {
                    session(['return_url_after_action' => $referrer]);
                }
            }

            return redirect()->route('phone_code', ['lang' => $selectedLanguage->abbreviation]);
        }

        return redirect()->route('phone', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->phone_add_message);
    }

    public function setDefault($id)
    {
        $phoneNumber = PhoneNumber::where('id', $id)->first();

        $phone_numbers = PhoneNumber::where('user_id', $phoneNumber->user_id)->get();
        foreach ($phone_numbers as $phone_number) {
            $phone_number->update([
                'default' => 0,
            ]);
        }

        $phoneNumber->update([
            'default' => '1',
        ]);

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        return redirect()->route('phone', ['lang' => $selectedLanguage->abbreviation])->with('message', 'Phone number set as default successfully');
    }

    public function destroy($id)
    {
        $phone_number = PhoneNumber::where('id', $id)->first();
        $user = User::find($phone_number->user_id);
        $emailData = [
            'first_name' => $user->first_name,
        ];
        if (isset($user->email_notification) && $user->email_notification == 1) {
            Mail::to($user->email)->send(new PhoneNumberDeleted($emailData));
        }

        $notification = Notification::create([
            'type' => null,
            'category' => 'system',
            'receiver_id' => $user->id,
            'posted_by' => $user->id,
            'message' =>  'Phone number removed from your profile',
            'status' => 'phone',
            'notification_type' => 'phone',
        ]);

        $body = $notification->message;
        $fcmService = new FCMService();

        $fcmToken = $user->mobile_fcm_token;
        if ($fcmToken) {
            $fcmService->sendNotification($fcmToken, $body);
        }

        $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

        foreach ($fcm_tokens as $fcm_token) {
            try {
                $fcmService->sendNotification($fcm_token->token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
            }
        }

        // Check if this was the primary number before deleting
        $wasDefault = $phone_number->default == '1';
        $user_id = $phone_number->user_id;

        $phone_number->delete();

        // If we deleted the primary number, set another verified number as primary
        if ($wasDefault) {
            $nextPrimary = PhoneNumber::where('user_id', $user_id)
                ->where('verified', '1')
                ->orderBy('id', 'asc')
                ->first();

            if ($nextPrimary) {
                $nextPrimary->update(['default' => '1']);
            }
        }

        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_delete_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_delete_message')->first();
            }
        }
        return redirect()->route('phone', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->phone_delete_message);
    }

    public function sendVerificationCode($lang = null, $id = null)
    {
        // Handle case where route is called without language parameter
        // In that case, $lang will contain the ID
        if ($id === null && is_numeric($lang)) {
            $id = $lang;
            $lang = null;
        }

        $phoneNumber = PhoneNumber::find($id);

        if (!$phoneNumber) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number not found'
                ], 404);
            }
            return redirect()->back()->with(['error' => 'Phone number not found']);
        }

        $existingRecord = DB::table('phone_verifications')
            ->where('phone_number_id', $phoneNumber->id)
            ->first();

        if ($existingRecord) {
            DB::table('phone_verifications')
                ->where('phone_number_id', $phoneNumber->id)
                ->delete();
        }

        $verificationCode = rand(1000, 9999);

        $verificationId = DB::table('phone_verifications')->insertGetId([
            'phone_number_id' => $phoneNumber->id,
            'verification_code' => $verificationCode,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $randomStr = strtoupper(Str::random(4));
        $randomId = $randomStr . '-' . $verificationId;

        DB::table('phone_verifications')
            ->where('id', $verificationId)
            ->update(['random_id' => $randomId]);

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        $twilio = new Client($sid, $token);
        $to = $phoneNumber->phone;
        $message = "ProximaRide: Your verification code is: $verificationCode \n This code will expire in 30 minutes.";

        try {
            if (env('APP_ENV') != 'local') {
                $twilio->messages->create($to, ['from' => $from, 'body' => $message]);
            }
        } catch (\Exception  $e) {
            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
            // Continue anyway - don't block the user from entering code manually
        }

        // Check if this is an AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Verification code sent successfully'
            ]);
        }

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        return redirect()->route('phone_code', ['lang' => $selectedLanguage->abbreviation]);
    }

    public function phoneCode($lang = null)
    {
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $phone_numbers = PhoneNumber::where('user_id', $user_id)->orderBy('id', 'desc')->get();

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

            return view('phone_code', ['phone_numbers' => $phone_numbers, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    public function phoneCodeStep($lang = null)
    {
        $languages = Language::all();
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage') ? Language::where('abbreviation', session('selectedLanguage'))->first() : Language::where('is_default', 1)->first();

        $step4Page = $selectedLanguage ? Step4PageSettingDetail::where('language_id', $selectedLanguage->id)->first() : null;

        if (auth()->user()) {
            $user = auth()->user();
            $user_id = $user->id;
            $phone_numbers = PhoneNumber::where('user_id', $user_id)->orderBy('id', 'desc')->get();

            $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                $query->where('type', '1')->whereHas('ride', function ($query) use ($user_id) {
                    $query->where('added_by', $user_id);
                });
            })->orWhere(function ($query) use ($user_id) {
                $query->where('type', '2')->whereHas('booking', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
            })->orWhere(function ($query) use ($user_id) {
                $query->where('type', null)->whereHas('receiver', function ($query) use ($user_id) {
                    $query->where('id', $user_id);
                });
            })->orderBy('id', 'desc')->get();

            $phone = session('phone');
            if ($phone) {
                $phoneNumber = PhoneNumber::where('phone', $phone)->first();
                if ($phoneNumber) {
                    $this->sendVerificationCode($phoneNumber->id);
                }
            }

            return view('phone_code_step', compact('user', 'phone_numbers', 'step4Page', 'notifications', 'languages', 'selectedLanguage'));
        }
        return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
    }

    public function verifyPhoneNumber(Request $request)
    {
        $message = $phoneSetting = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_verified_message', 'incorrect_code_message', 'phone_code_error_message')->first();
                $phoneSetting = MyPhoneSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_verified_message', 'incorrect_code_message', 'phone_code_error_message')->first();
                $phoneSetting = MyPhoneSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        // Combine the code input values into a single string
        $code = implode('', $request->code);

        $request->validate(['code' => 'required|array']);

        // After initial validation, combine the code digits
        if (strlen($code) < 4) {
            return redirect()->back()->with(['error' => $message->phone_code_error_message ?? 'The code must be exactly 4 digits'])->withInput();
        }

        // Find verification record - handle both North American (code in DB) and International (Twilio Verify)
        $existingRecord = DB::table('phone_verifications')
            ->where('verification_code', $code)
            ->first();

        $isValid = false;
        $phone_number = null;

        if ($existingRecord && empty($existingRecord->twilio_verify_sid)) {
            // North American number: Check against database code (existing logic)
            $phone_number = PhoneNumber::whereId($existingRecord->phone_number_id)->first();
            if ($phone_number) {
                // Check if code hasn't expired
                if (Carbon::parse($existingRecord->expires_at)->isFuture()) {
                    $isValid = true;
                    // Delete the verification record
                    DB::table('phone_verifications')->where('id', $existingRecord->id)->delete();
                }
            }
        } elseif ($existingRecord && !empty($existingRecord->twilio_verify_sid)) {
            // International number: Use Twilio Verify API Check
            $phone_number = PhoneNumber::whereId($existingRecord->phone_number_id)->first();
            if ($phone_number) {
                $verifyResult = $this->checkVerificationViaTwilioVerify(
                    $phone_number->phone,
                    $code,
                    $existingRecord->twilio_verify_sid
                );
                
                if ($verifyResult['success']) {
                    $isValid = true;
                    // Delete the verification record
                    DB::table('phone_verifications')->where('id', $existingRecord->id)->delete();
                }
            }
        } else {
            // No matching record by code - try to find by phone number for Twilio Verify (where code is empty in DB)
            $user_id = auth()->user()->id;
            $phone_numbers = PhoneNumber::where('user_id', $user_id)->get();
            
            foreach ($phone_numbers as $phone) {
                $phoneRecord = DB::table('phone_verifications')
                    ->where('phone_number_id', $phone->id)
                    ->whereNotNull('twilio_verify_sid')
                    ->where('expires_at', '>', Carbon::now())
                    ->first();
                
                if ($phoneRecord) {
                    $verifyResult = $this->checkVerificationViaTwilioVerify(
                        $phone->phone,
                        $code,
                        $phoneRecord->twilio_verify_sid
                    );
                    
                    if ($verifyResult['success']) {
                        $isValid = true;
                        $phone_number = $phone;
                        // Delete the verification record
                        DB::table('phone_verifications')->where('id', $phoneRecord->id)->delete();
                        break;
                    }
                }
            }
        }

        if ($isValid && $phone_number) {
            $phone_number->update(['verified' => '1']);

            // Auto-mark as default if this is the first/only verified phone number
            $verifiedPhoneCount = PhoneNumber::where('user_id', auth()->user()->id)
                ->where('verified', '1')
                ->count();

            if ($verifiedPhoneCount === 1) {
                // This is the only verified phone number, make it default
                // First, remove default from any existing numbers
                PhoneNumber::where('user_id', auth()->user()->id)
                    ->update(['default' => '0']);
                // Then set this one as default
                $phone_number->update(['default' => '1']);
            }

            // Check for return URL in session (to redirect back to original page)
            $returnUrl = session('return_url_after_action');
            
            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                $response = [
                    'success' => true,
                    'message' => $message->phone_verified_message ?? 'Phone number verified successfully'
                ];
                
                if ($request->step) {
                    $response['redirect'] = route('profile', ['lang' => $selectedLanguage->abbreviation]);
                    $response['message'] = "Your profile is all set. Welcome to ProximaRide!";
                } elseif ($returnUrl) {
                    $response['redirect'] = $returnUrl;
                } elseif ($request->page && $request->page == "booking") {
                    $response['redirect'] = url()->previous();
                } else {
                    $response['redirect'] = route('phone', ['lang' => $selectedLanguage->abbreviation]);
                }
                
                session()->forget('return_url_after_action');
                return response()->json($response);
            }
            
            // Handle regular form submissions (non-AJAX)
            if ($request->step) {
                session()->forget('return_url_after_action');
                return redirect()->route('profile', ['lang' => $selectedLanguage->abbreviation])->with('message', "Your profile is all set. Welcome to ProximaRide!");
            }
            
            // If return URL exists, redirect there
            if ($returnUrl) {
                session()->forget('return_url_after_action');
                return redirect($returnUrl)->with('message', $message->phone_verified_message ?? 'Phone number verified successfully');
            }
            
            // Legacy support for page parameter
            if ($request->page && $request->page == "booking") {
                session()->forget('return_url_after_action');
                return redirect()->back();
            }
            
            session()->forget('return_url_after_action');
            return redirect()->route('phone', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->phone_verified_message);
        }

        // Handle AJAX error response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => $message->incorrect_code_message ?? 'The verification code is incorrect or has expired'
            ], 422);
        }

        return redirect()->back()->with(['error' => $message->incorrect_code_message ?? 'The verification code is incorrect or has expired']);
    }

    public function resendCode(Request $request)
    {
        $phone = session('phone');

        if (!$phone) {
            return response()->json(['success' => false, 'message' => 'Phone details not found'], 400);
        }

        $phoneNumber = PhoneNumber::where('phone', $phone)->first();
        if (!$phoneNumber) {
            return response()->json(['success' => false, 'message' => 'Phone number not registered'], 404);
        }

        return $this->sendVerificationCode($phoneNumber->id);
    }

    public function storeAndVerify(Request $request, $lang = null)
    {
        $user = auth()->user();
        $user_id = $user->id;

        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_add_message', 'suspended_account_phone_number_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_add_message', 'suspended_account_phone_number_message')->first();
            }
        }

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

        $existingPhone = PhoneNumber::where('phone', $normalizedPhone)->first();

        if ($existingPhone) {
            if ($existingPhone->user_id != $user_id) {
                $otherUser = \App\Models\User::find($existingPhone->user_id);
                if ($otherUser && ($otherUser->admin_deactive_account == 1 || $otherUser->suspand == 1)) {
                    return response()->json([
                        'success' => false,
                        'message' => $message->suspended_account_phone_number_message ?? 'This phone number belongs to a suspended or deactivated account.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already added this phone number.'
                ], 400);
            }
        }

        // Updated validation to use Laravel-Phone
        $country = null;
        if ($request->country) {
            $countryModel = Country::find($request->country);
            $country = $countryModel ? $countryModel->iso_code : 'US';
        }

        if (!validatePhoneNumber($request->phone, $country)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid phone number.'
            ], 422);
        }

        try {
            $request->validate([
                'full_phone' => 'max:20|unique:phone_numbers,phone,NULL,user_id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $phone = PhoneNumber::create([
            'user_id' => $user_id,
            'phone' => $normalizedPhone,
            'country_id' => $request->country,
        ]);

        $verificationCode = rand(1000, 9999);

        DB::table('phone_verifications')->insert([
            'phone_number_id' => $phone->id,
            'verification_code' => $verificationCode,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        if ($sid) {
            $twilio = new Client($sid, $token);
            $to = $phone->phone;
            $smsMessage = "ProximaRide: Your verification code is: $verificationCode \n This code will expire in 30 minutes.";

            try {
                if (env('APP_ENV') != 'local') {
                    $twilio->messages->create($to, ['from' => $from, 'body' => $smsMessage]);
                }
            } catch (\Exception $e) {
                Log::info('Cannot send text to ' . $to . ' because ' . $e->getMessage());
                // Continue anyway - don't block the user from entering code manually
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent successfully'
        ]);
    }
}
