<?php

namespace App\Http\Controllers;

use App\Mail\UserEmailVerification;
use App\Mail\AdminNewUserSignupMail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SignupPageSettingDetail;
use App\Models\User;
use App\Models\Admin;
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
    /** Must match SocialiteProviders registered in EventServiceProvider. */
    private const SOCIAL_LOGIN_PROVIDERS = [
        'apple',
        'facebook',
        'google',
        'instagram',
        'linkedin',
    ];

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
        $admin = Admin::first();
        try {
            Mail::to($admin->admin_email)->queue(new AdminNewUserSignupMail($adminData));
        } catch (\Throwable $e) {
            try {
                Mail::mailer('log')->to($admin->admin_email)->send(new AdminNewUserSignupMail($adminData));
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

        $selectedLanguageId = $this->selectedLanguage?->id;
        $defaultLanguageId = $this->defaultLang?->id;
        $languageId = $selectedLanguageId ?? $defaultLanguageId;

        $messages = $languageId
            ? SuccessMessagesSettingDetail::where('language_id', $languageId)
                ->select('email_sent_message')
                ->first()
            : null;

        $emailSentMessage = $messages->email_sent_message ?? 'We\'ve sent you a verification email. Please check your inbox.';
        if (!$emailSent) {
            // Keep the fallback note in English (admin can include their own wording inside `email_sent_message`).
            $emailSentMessage .= ' <strong>Note: There was an issue sending the email. Please use the "Request a new verification email" option if you don\'t receive it.</strong>';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $emailSent,
                'message' => $emailSentMessage,
            ]);
        }

        return redirect()->back()->with([
            'success' => $emailSentMessage,
        ]);
    }

    public function redirectToProvider($lang, $provider, Request $request)
    {
        if (! in_array($provider, self::SOCIAL_LOGIN_PROVIDERS, true)) {
            abort(404);
        }

        session(['selectedLanguage' => $lang]);
        session([
            'social_auth_intent' => $request->query('intent') === 'login' ? 'login' : 'signup',
        ]);

        Log::info('social_oauth.redirect', [
            'provider' => $provider,
            'lang' => $lang,
            'intent' => session('social_auth_intent'),
            'has_apple_private_key_config' => $provider === 'apple'
                ? (bool) (config('services.apple.private_key') ?? config('services.apple.client_secret'))
                : null,
        ]);

        if ($provider === 'linkedin') {
            return Socialite::driver($provider)->scopes(['openid', 'profile', 'email'])->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($lang, $provider)
    {
        if (! in_array($provider, self::SOCIAL_LOGIN_PROVIDERS, true)) {
            abort(404);
        }

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
            Log::info('social_oauth.callback', [
                'provider' => $provider,
                'method' => request()->method(),
                'has_code' => request()->filled('code'),
                'has_error' => request()->has('error'),
                'error' => request()->get('error'),
                'state_present' => request()->filled('state'),
            ]);

            // OAuth providers return ?error= on cancel or misconfiguration (Facebook, some others use query; Apple may POST body).
            if (request()->has('error')) {
                $error = request()->get('error');
                $errorDescription = (string) request()->get('error_description', '');
                $errorReason = request()->get('error_reason', '');

                if ($provider === 'facebook') {
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
                } elseif (in_array($error, ['access_denied', 'user_cancelled_authorize', 'user_denied'], true)) {
                    Session::flash('error', ucfirst($provider) . ' login was cancelled. Please try again or use another login method.');
                } else {
                    Session::flash(
                        'error',
                        'Unable to login using ' . ucfirst($provider) . '. '
                        . ($errorDescription ?: ($error ?: 'Please try again or use another login method.'))
                    );
                }

                return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation]);
            }

            $providerUser = Socialite::driver($provider)->user();
            $authIntent = session()->pull('social_auth_intent', 'signup');

            $normalizedName = trim((string) ($providerUser->name ?? ''));
            if ($normalizedName === '') {
                $normalizedName = $providerUser->email
                    ? (strstr($providerUser->email, '@', true) ?: 'Member')
                    : 'Member';
            }

            Log::info('social_oauth.user_received', [
                'provider' => $provider,
                'email_present' => !empty($providerUser->email),
                'has_name' => !empty(trim((string) ($providerUser->name ?? ''))),
                'has_token' => !empty($providerUser->token ?? null),
                'provider_user_id' => $providerUser->id ?? null,
                'intent' => $authIntent,
            ]);

            $existingUser = $this->findUserForSocialLogin($provider, $providerUser);

            if (!$existingUser && empty($providerUser->email)) {
                throw new \Exception(
                    "No email from {$provider} and no existing account matched this sign-in. "
                    . 'For Apple, email is only sent on first authorization; use the same Apple ID or sign in with email/password.'
                );
            }

            // Check if the user is already registered

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
                Log::info('social_oauth.login_existing', [
                    'provider' => $provider,
                    'user_id' => $existingUser->id,
                ]);
                $userLang = $existingUser->fresh()->lang ?: $selectedLanguage->abbreviation;
                session(['selectedLanguage' => $userLang]);

                if ($existingUser->step == '1') {
                    return redirect()->route('step1to5', ['lang' => $userLang]);
                } elseif ($existingUser->step == '2') {
                    return redirect()->route('step2to5', ['lang' => $userLang]);
                } elseif ($existingUser->step == '3') {
                    return redirect()->route('step3to5', ['lang' => $userLang]);
                } elseif ($existingUser->step == '4') {
                    return redirect()->route('step4to5', ['lang' => $userLang]);
                }

                return redirect()->route('home', ['lang' => $userLang]);
            }

            // Split the full name into first and last names (Apple often omits name after first authorization)
            $nameParts = explode(' ', $normalizedName, 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

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

            $admin = Admin::first();

            // Send email with error handling similar to regular signup
            try {
                Mail::to($admin->admin_email)->send(new AdminNewUserSignupMail($adminData));
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
                    Mail::mailer('log')->to($admin->admin_email)->send(new AdminNewUserSignupMail($adminData));
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

            Log::info('social_oauth.signup_complete', [
                'provider' => $provider,
                'user_id' => $newUser->id,
            ]);

            return redirect()->route('step1to5', ['lang' => $newUser->lang]);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error('social_oauth.failed', [
                'provider' => $provider,
                'exception' => get_class($e),
                'message' => $errorMessage,
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if ($provider === 'facebook' && (str_contains(strtolower($errorMessage), 'app not active') ||
                str_contains(strtolower($errorMessage), 'app is not accessible'))) {
                Session::flash('error', 'This Facebook app is not accessible right now. The app developer is aware of the issue. You will be able to log in when the app is reactivated. Please try using another login method in the meantime.');
            } else {
                Session::flash('error', 'Unable to login using ' . $provider . '. Please try again or use another login method.');
            }

            return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    /**
     * Match by email first, then by provider + provider_id (Apple may omit email on later sign-ins).
     */
    private function findUserForSocialLogin(string $provider, $providerUser): ?User
    {
        $email = $providerUser->email ?? null;
        if (!empty($email)) {
            $byEmail = User::where('email', $email)->first();
            if ($byEmail) {
                return $byEmail;
            }
        }

        $pid = $providerUser->id ?? null;
        if ($pid === null || $pid === '') {
            return null;
        }

        return User::where('provider', $provider)
            ->where('provider_id', (string) $pid)
            ->first();
    }
}
