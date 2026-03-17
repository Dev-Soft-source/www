<?php

namespace App\Http\Controllers;

use App\Mail\UserEmailVerification;
use App\Mail\AdminNewUserSignupMail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SignupPageSettingDetail;
use App\Models\User;
use App\Models\ReferralDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class SignupController extends Controller
{
    public function create($lang = null)
    {

        $signupPage = SignupPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('signup', ['signupPage' => $signupPage]);
    }


    public function signupWithReferral($lang = null, $uuid)
    {
                
        $signupPage = SignupPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
       
        return view('signup', ['signupPage' => $signupPage, 'uuid' => $uuid]);
    }

    public function store(Request $request)
    {
        
        $signupPage = SignupPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $messages = $this->successMessage;

        // Check if email exists and account is closed - allow re-registration
        $existingUser = User::active()
            ->where('email', $request->email)
            ->first();
        if ($existingUser && $existingUser->closed === '1') {
            // Allow closed accounts to re-register - bypass unique validation
            $emailRule = 'required|string|email|max:255';
        } else if ($existingUser && $existingUser->closed !== '1') {
            // Email exists and account is not closed - use standard unique validation
            $emailRule = 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL';
        } else {
            // Email doesn't exist - allow registration
            $emailRule = 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL';
        }

        // Validate the form data with AJAX support
        try {
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-]+$/',
                'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-]+$/',
                'email' => $emailRule,
                'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).+$/',
                'password_confirmation' => 'required|same:password',
                'agree_cost_share_terms' => 'required',
                'rideshare_disclaimer' => 'required',
            ], [
                'password.min' => $signupPage->password_placeholder ?? 'The password must be at least 8 characters long',
            ], []);
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'type' => 'verify_email',
            'created_at' => Carbon::now()
        ]);

        // Check if user exists with closed account - update instead of create
        $existingClosedUser = User::active()
            ->where('email', $request->email)
            ->closed()
            ->first();

        $ip = request()->ip();

        if ($existingClosedUser) {
            // Update the closed account to reactivate it
            $existingClosedUser->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'password' => Hash::make($request->password),
                'lang' => $selectedLanguage->abbreviation ?? config('app.locale', 'en'),
                'closed' => '0', // Reactivate the account
                'email_verified' => '0', // Require email verification again
            ]);
            $user = $existingClosedUser;
        } else {
            $location = geoip()->getLocation($ip);
            $country = Country::where('iso_code', $location['iso_code'])->first();
            // Create new user

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'lang' => $selectedLanguage->abbreviation ?? config('app.locale', 'en'),
                'password' => Hash::make($request->password),
                'country' => $country->id ?? 38,
                'referral_uuid' => bin2hex(random_bytes(16))
            ]);
        }

        session(['selectedLanguage' => $user->lang ?? ($selectedLanguage->abbreviation ?? config('app.locale', 'en'))]);

        DB::table('user_details')->insert([
            'ip_address' => $ip,
            'type' => 'web',
            'user_id' => $user->id,
            'created_at' => Carbon::now()
        ]);

        if (isset($request->uuid) && $request->uuid != 0) {
            $getUserId = User::where('referral_uuid', $request->uuid)->value('id');
            if (isset($getUserId) && !is_null($getUserId)) {
                $referralDetail = ReferralDetail::create([
                    'referral_user_id' => $getUserId,
                    'user_id' => $user->id,
                    'status' => "pending",
                ]);
            }
        }

        $data = ['first_name' => $request->first_name, 'email' => $request->email, 'token' => $token];

        // Log mail configuration for debugging
        $mailDriver = config('mail.default');
        $mailHost = config('mail.mailers.smtp.host');
        // Log::info('Attempting to send email verification', [
        //     'email' => $request->email,
        //     'mail_driver' => $mailDriver,
        //     'mail_host' => $mailHost,
        //     'token' => substr($token, 0, 10) . '...'
        // ]);

        // Send email verification immediately; fallback to log mailer if SMTP fails
        $emailSent = false;
        try {
            Mail::to($request->email)->queue(new UserEmailVerification($data));
            $emailSent = true;
            Log::info('Email verification sent successfully via ' . $mailDriver, [
                'email' => $request->email,
                'mail_driver' => $mailDriver
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send email verification on signup', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            // Try fallback to log mailer
            try {
                Mail::mailer('log')->to($request->email)->queue(new UserEmailVerification($data));
                $emailSent = true;
                Log::info('Email verification sent via log mailer (fallback)', ['email' => $request->email]);
            } catch (\Throwable $e2) {
                Log::error('Failed to send email verification via log mailer', [
                    'email' => $request->email,
                    'error' => $e2->getMessage(),
                    'error_class' => get_class($e2)
                ]);
            }
        }

        // Log final status
        if (!$emailSent) {
            Log::critical('Email verification NOT sent - all methods failed', [
                'email' => $request->email,
                'user_id' => $user->id
            ]);
        }
        // Send admin notification about new user signup
        $adminData = [
            'user_name' => $request->first_name . ' ' . $request->last_name,
            'user_email' => $request->email,
            'registration_date' => Carbon::now()->format('M d, Y H:i:s'),
            'platform' => 'Website'
        ];
        try {
            Mail::to('ccaned@gmail.com')->queue(new AdminNewUserSignupMail($adminData));
        } catch (\Throwable $e) {
            try {
                Mail::mailer('log')->to('ccaned@gmail.com')->send(new AdminNewUserSignupMail($adminData));
            } catch (\Throwable $e2) {
            }
        }

        // Handle AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'showModal' => true,
                'emailSent' => $emailSent,
                'messages' => [
                    'welcome_message' => $messages->welcome_message ?? 'Welcome',
                    'email_sent_message' => $emailSent
                        ? ($messages->email_sent_message ?? 'We\'ve sent you a verification email. Please check your inbox and follow the link to verify your email.')
                        : ($messages->email_sent_message ?? 'We\'ve sent you a verification email. Please check your inbox and follow the link to verify your email.') . ' <strong>Note: There was an issue sending the email. Please use the "Request a new verification email" option if you don\'t receive it.</strong>',
                    'registration_successful_title' => $messages->registration_successful_title ?? 'Registration Successful!',
                ],
                'user' => [
                    'first_name' => $user->first_name,
                    'email' => $user->email,
                ]
            ]);
        }

        return redirect()->back()->with([
            'showModal' => true,
            'messages' => $messages,
            'user' => $user,
            'emailSent' => $emailSent
        ]);
    }

    public function sendEmailVerify($email, Request $request)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            return redirect()->back()->with(['error' => 'User not found']);
        }

        $token = Str::random(64);

        $existingRecord = DB::table('password_resets')
            ->where('email', $user->email)
            ->where('type', 'verify_email')
            ->first();

        if ($existingRecord) {
            // If a record with the same email and type exists, delete it
            DB::table('password_resets')
                ->where('email', $user->email)
                ->where('type', 'verify_email')
                ->delete();
        }

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'type' => 'verify_email',
            'created_at' => Carbon::now()
        ]);

        $data = ['first_name' => $user->first_name, 'email' => $user->email, 'token' => $token];

        // Send email verification immediately; fallback to log mailer
        $emailSent = false;
        try {
            Mail::to($user->email)->send(new UserEmailVerification($data));
            $emailSent = true;
            Log::info('Email verification sent successfully (resend)', ['email' => $user->email]);
        } catch (\Throwable $e) {
            Log::error('Failed to send email verification', [
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            try {
                Mail::mailer('log')->to($user->email)->send(new UserEmailVerification($data));
                $emailSent = true;
                Log::info('Email verification sent via log mailer (resend)', ['email' => $user->email]);
            } catch (\Throwable $e2) {
                Log::error('Failed to send email verification via log mailer', [
                    'email' => $user->email,
                    'error' => $e2->getMessage()
                ]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $emailSent,
                'message' => $emailSent
                    ? 'Verification email has been sent! Please check your inbox.'
                    : 'There was an issue sending the email. Please try again later.'
            ]);
        }

        return redirect()->back()->with([
            'success' => $emailSent
                ? 'We\'ve sent you a verification email. Check your inbox'
                : 'There was an issue sending the email. Please try again later.'
        ]);
    }

    public function redirectToProvider($lang, $provider, Request $request)
    {
        session(['selectedLanguage' => $lang]);
        session([
            'social_auth_intent' => $request->query('intent') === 'login' ? 'login' : 'signup',
        ]);

        return Socialite::driver($provider)
            ->redirect();
    }

    public function handleProviderCallback($lang, $provider)
    {
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('abbreviation', $lang)->first();
        }

        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        try {
            // Check for Facebook error parameters in the request
            if ($provider === 'facebook' && request()->has('error')) {
                $error = request()->get('error');
                $errorDescription = request()->get('error_description', '');
                $errorReason = request()->get('error_reason', '');

                // Handle specific Facebook errors
                if ($error === 'access_denied' || $errorReason === 'user_denied') {
                    Session::flash('error', 'Facebook login was cancelled. Please try again or use another login method.');
                } elseif (
                    str_contains(strtolower($errorDescription), 'app not active') ||
                    str_contains(strtolower($errorDescription), 'app is not accessible')
                ) {
                    Session::flash('error', 'This Facebook app is not accessible right now. The app developer is aware of the issue. You will be able to log in when the app is reactivated. Please try using another login method in the meantime.');
                } else {
                    Session::flash('error', 'Unable to login using Facebook. ' . ($errorDescription ?: 'Please try again or use another login method.'));
                }

                return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation]);
            }

            $providerUser = Socialite::driver($provider)->user();
            $authIntent = session()->pull('social_auth_intent', 'signup');

            Log::info("social login attempt", [
                'provider' => $provider,
                'email' => $providerUser->email ?? 'not provided',
                'has_token' => !empty($providerUser->token ?? null),
                'intent' => $authIntent,
            ]);

            // Validate that required fields are present
            if (empty($providerUser->email)) {
                throw new \Exception("Email address is required from {$provider} provider");
            }

            if (empty($providerUser->name)) {
                throw new \Exception("Name is required from {$provider} provider");
            }

            // Check if the user is already registered
            $existingUser = User::where('email', $providerUser->email)->first();

            if ($existingUser) {
                if ($existingUser->closed === '1') {
                    if ($authIntent === 'login') {
                        $closeModalErrorMessage = $this->successMessage->account_closed_message
                            ?? "It looks like this account has been closed. We'd love to have you back! You can sign up for a new account using this email address anytime.";

                        Session::flash('error', $closeModalErrorMessage);

                        return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation]);
                    }

                    $existingUser->forceFill([
                        'closed' => '0',
                        'email_verified' => '1',
                        'lang' => $existingUser->lang ?: $selectedLanguage->abbreviation,
                        'provider' => $provider,
                        'provider_id' => $providerUser->id,
                        'profile_image' => $providerUser->avatar ?: $existingUser->getRawOriginal('profile_image'),
                    ])->save();
                }

                if (!$existingUser->lang) {
                    $existingUser->forceFill([
                        'lang' => $selectedLanguage->abbreviation,
                    ])->save();
                }

                // Log in the existing user
                Auth::login($existingUser);
                $userLang = $existingUser->fresh()->lang ?: $selectedLanguage->abbreviation;
                session(['selectedLanguage' => $userLang]);

                if ($existingUser->step1 == 0) {
                    return redirect()->route('step1to5', ['lang' => $userLang]);
                } elseif ($existingUser->step2 == 0) {
                    return redirect()->route('step2to5', ['lang' => $userLang]);
                } elseif ($existingUser->step3 == 0) {
                    return redirect()->route('step3to5', ['lang' => $userLang]);
                } elseif ($existingUser->step4 == 0) {
                    return redirect()->route('step4to5', ['lang' => $userLang]);
                }

                return redirect()->route('home', ['lang' => $userLang]);
            }

            // Split the full name into first and last names
            $nameParts = explode(' ', $providerUser->name, 2); // Split into two parts
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : ''; // Set to empty string if no last name

            $ip = request()->ip();
            $location = geoip()->getLocation($ip);
            $country = Country::where('iso_code', $location['iso_code'])->first();

            // If the user is not registered, create a new user
            $newUser = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $providerUser->email,
                'lang' => $selectedLanguage->abbreviation ?? $lang ?? config('app.locale', 'en'),
                'email_verified' => '1',
                'password' => '',
                'profile_image' => $providerUser->avatar,
                'provider' => $provider,
                'provider_id' => $providerUser->id,
                'country' => $country->id ?? 38,
                'referral_uuid' => bin2hex(random_bytes(16))
            ]);

            // Send admin notification about new social signup
            $adminData = [
                'user_name' => $firstName . ' ' . $lastName,
                'user_email' => $providerUser->email,
                'registration_date' => Carbon::now()->format('M d, Y H:i:s'),
                'platform' => 'Web - ' . ucfirst($provider) . ' Login'
            ];

            // Send email with error handling similar to regular signup
            try {
                Mail::to('ccaned@gmail.com')->send(new AdminNewUserSignupMail($adminData));
                Log::info('Admin notification sent successfully for social signup', [
                    'email' => $providerUser->email,
                    'provider' => $provider
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to send admin notification for social signup', [
                    'email' => $providerUser->email,
                    'provider' => $provider,
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e)
                ]);
                // Try fallback to log mailer
                try {
                    Mail::mailer('log')->to('ccaned@gmail.com')->send(new AdminNewUserSignupMail($adminData));
                    Log::info('Admin notification sent via log mailer (fallback) for social signup', [
                        'email' => $providerUser->email,
                        'provider' => $provider
                    ]);
                } catch (\Throwable $e2) {
                    Log::error('Failed to send admin notification via log mailer for social signup', [
                        'email' => $providerUser->email,
                        'provider' => $provider,
                        'error' => $e2->getMessage()
                    ]);
                }
            }

            Auth::login($newUser);
            session(['selectedLanguage' => $newUser->lang]);

            return redirect()->route('step1to5', ['lang' => $newUser->lang]);
        } catch (\Exception $e) {
            // Check if it's a Facebook-specific error
            $errorMessage = $e->getMessage();
            Log::info("social login error:" . $errorMessage);
            if ($provider === 'facebook' && (str_contains(strtolower($errorMessage), 'app not active') ||
                str_contains(strtolower($errorMessage), 'app is not accessible'))) {
                Session::flash('error', 'This Facebook app is not accessible right now. The app developer is aware of the issue. You will be able to log in when the app is reactivated. Please try using another login method in the meantime.');
            } else {
                Session::flash('error', "Unable to login using " . $provider . ". Please try again or use another login method.");
            }

            return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation]);
        }
    }
}
