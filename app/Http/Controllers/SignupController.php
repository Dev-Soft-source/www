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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class SignupController extends Controller
{
    public function create($lang = null)
    {
        
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $signupPage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                // Retrieve the SignupPageSettingDetail associated with the selected language
                $signupPage = SignupPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $signupPage = SignupPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $notifications = null;
        if (auth()->user()) {
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
        }
        return view('signup', ['signupPage' => $signupPage, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
    }


    public function signupWithReferral($lang = null, $uuid)
    {
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $signupPage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                // Retrieve the SignupPageSettingDetail associated with the selected language
                $signupPage = SignupPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $signupPage = SignupPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $notifications = null;
        if (auth()->user()) {
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
        }
        return view('signup', ['signupPage' => $signupPage, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'uuid' => $uuid]);
    }

    public function store(Request $request)
    {
        $niceNames = [];
        $signupPage = null;
        $messages = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('welcome_message', 'email_sent_message', 'registration_successful_title')->first();
                $signupPage = SignupPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'first_name' => ($signupPage && isset($signupPage->first_name_error)) ? $signupPage->first_name_error : '',
                    'last_name' => ($signupPage && isset($signupPage->last_name_error)) ? $signupPage->last_name_error : '',
                    'email' => ($signupPage && isset($signupPage->email_error)) ? $signupPage->email_error : '',
                    'password' => ($signupPage && isset($signupPage->password_error)) ? $signupPage->password_error : '',
                    'password_confirmation' => ($signupPage && isset($signupPage->confirm_password_error)) ? $signupPage->confirm_password_error : '',
                    'remember-me' => ($signupPage && isset($signupPage->agree_terms_error)) ? $signupPage->agree_terms_error : '',
                ];
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('welcome_message', 'email_sent_message', 'registration_successful_title')->first();
                $signupPage = SignupPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'first_name' => ($signupPage && isset($signupPage->first_name_error)) ? $signupPage->first_name_error : '',
                    'last_name' => ($signupPage && isset($signupPage->last_name_error)) ? $signupPage->last_name_error : '',
                    'email' => ($signupPage && isset($signupPage->email_error)) ? $signupPage->email_error : '',
                    'password' => ($signupPage && isset($signupPage->password_error)) ? $signupPage->password_error : '',
                    'password_confirmation' => ($signupPage && isset($signupPage->confirm_password_error)) ? $signupPage->confirm_password_error : '',
                    'remember-me' => ($signupPage && isset($signupPage->agree_terms_error)) ? $signupPage->agree_terms_error : '',
                ];
            }
        }

        // Check if email exists and account is closed - allow re-registration
        $existingUser = User::where('email', $request->email)->whereNull('deleted_at')->first();
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
                'remember-me' => 'required',
                'password_confirmation' => 'required|same:password',
                'rideshare_disclaimer' => 'required|accepted',
            ], [
                'remember-me.required' => isset($signupPage->agree_terms_error) ? $signupPage->agree_terms_error : 'You must agree to the terms',
                'password_confirmation.required' => isset($signupPage->confirm_password_error) ? $signupPage->confirm_password_error : 'Confirm password field is required',
                'password_confirmation.same' => isset($signupPage->password_mismatch_error) ? $signupPage->password_mismatch_error : 'The passwords do not match',
                'rideshare_disclaimer.required' => $signupPage->rideshare_require,
                'rideshare_disclaimer.accepted' => $signupPage->rideshare_require,
            ], $niceNames);
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
        $existingClosedUser = User::where('email', $request->email)->where('closed', '1')->whereNull('deleted_at')->first();
        
        if ($existingClosedUser) {
            // Update the closed account to reactivate it
            $existingClosedUser->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'password' => Hash::make($request->password),
                'closed' => '0', // Reactivate the account
                'email_verified' => '0', // Require email verification again
            ]);
            $user = $existingClosedUser;
        } else {
            // Create new user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'country' => 39,
                'referral_uuid' => bin2hex(random_bytes(16))
            ]);
        }

        $ipAddress = null;
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        $ipAddress = $ip;
                        break 2;
                    }
                }
            }
        }
        $ipAddress = $ipAddress ?? 'UNKNOWN';

        DB::table('user_details')->insert([
            'ip_address' => $ipAddress,
            'type' => 'web',
            'user_id' => $user->id,
            'created_at' => Carbon::now()
        ]);

        if(isset($request->uuid) && $request->uuid != 0){
            $getUserId = User::where('referral_uuid', $request->uuid)->value('id');
            if(isset($getUserId) && !is_null($getUserId)){
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
        Log::info('Attempting to send email verification', [
            'email' => $request->email,
            'mail_driver' => $mailDriver,
            'mail_host' => $mailHost,
            'token' => substr($token, 0, 10) . '...'
        ]);

        // Send email verification immediately; fallback to log mailer if SMTP fails
        $emailSent = false;
        try {
            Mail::to($request->email)->send(new UserEmailVerification($data));
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
                Mail::mailer('log')->to($request->email)->send(new UserEmailVerification($data));
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
            Mail::to('ccaned@gmail.com')->send(new AdminNewUserSignupMail($adminData));
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

    public function redirectToProvider($lang, $provider)
    {
        return Socialite::driver($provider)
        ->redirect();
    }

    public function handleProviderCallback($lang, $provider)
    {
        try {
            // Check for Facebook error parameters in the request
            if ($provider === 'facebook' && request()->has('error')) {
                $error = request()->get('error');
                $errorDescription = request()->get('error_description', '');
                $errorReason = request()->get('error_reason', '');
                
                $selectedLanguage = session('selectedLanguage');
                if ($selectedLanguage) {
                    // Find the language by abbreviation
                    $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
                } else {
                    $selectedLanguage = Language::where('abbreviation', $lang)->first();
                    if (!$selectedLanguage) {
                        $selectedLanguage = Language::where('is_default', 1)->first();
                    }
                }
                
                // Handle specific Facebook errors
                if ($error === 'access_denied' || $errorReason === 'user_denied') {
                    Session::flash('error', 'Facebook login was cancelled. Please try again or use another login method.');
                } elseif (str_contains(strtolower($errorDescription), 'app not active') || 
                          str_contains(strtolower($errorDescription), 'app is not accessible')) {
                    Session::flash('error', 'This Facebook app is not accessible right now. The app developer is aware of the issue. You will be able to log in when the app is reactivated. Please try using another login method in the meantime.');
                } else {
                    Session::flash('error', 'Unable to login using Facebook. ' . ($errorDescription ?: 'Please try again or use another login method.'));
                }
                
                return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation]);
            }
            
            $user = Socialite::driver($provider)->user();

            Log::info("social login attempt", [
                'provider' => $provider,
                'email' => $user->email ?? 'not provided',
                'has_token' => !empty($user->token ?? null)
            ]);

            // Validate that required fields are present
            if (empty($user->email)) {
                throw new \Exception("Email address is required from {$provider} provider");
            }

            if (empty($user->name)) {
                throw new \Exception("Name is required from {$provider} provider");
            }

            // Check if the user is already registered
            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                // Log in the existing user
                auth()->login($existingUser);
                return redirect('/'); // Redirect to the home page
            }

            // Split the full name into first and last names
            $nameParts = explode(' ', $user->name, 2); // Split into two parts
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : ''; // Set to empty string if no last name

            // If the user is not registered, create a new user
            $newUser = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'email_verified' => '1',
                'password' => '',
                'profile_image' => $user->avatar,
                'provider' => $provider,
                'provider_id' => $user->id,
                'country' => 39,
                'referral_uuid' => bin2hex(random_bytes(16))
            ]);

            // Send admin notification about new social signup
            $adminData = [
                'user_name' => $firstName . ' ' . $lastName,
                'user_email' => $user->email,
                'registration_date' => Carbon::now()->format('M d, Y H:i:s'),
                'platform' => 'Web - ' . ucfirst($provider) . ' Login'
            ];
            
            // Send email with error handling similar to regular signup
            try {
                Mail::to('ccaned@gmail.com')->send(new AdminNewUserSignupMail($adminData));
                Log::info('Admin notification sent successfully for social signup', [
                    'email' => $user->email,
                    'provider' => $provider
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to send admin notification for social signup', [
                    'email' => $user->email,
                    'provider' => $provider,
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e)
                ]);
                // Try fallback to log mailer
                try {
                    Mail::mailer('log')->to('ccaned@gmail.com')->send(new AdminNewUserSignupMail($adminData));
                    Log::info('Admin notification sent via log mailer (fallback) for social signup', [
                        'email' => $user->email,
                        'provider' => $provider
                    ]);
                } catch (\Throwable $e2) {
                    Log::error('Failed to send admin notification via log mailer for social signup', [
                        'email' => $user->email,
                        'provider' => $provider,
                        'error' => $e2->getMessage()
                    ]);
                }
            }

            auth()->login($newUser);

            return redirect('/'); // Redirect to the home page
        } catch (\Exception $e) {

            $selectedLanguage = session('selectedLanguage');
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            } else {
                $selectedLanguage = Language::where('abbreviation', $lang)->first();
                if (!$selectedLanguage) {
                    $selectedLanguage = Language::where('is_default', 1)->first();
                }
            }

            // Check if it's a Facebook-specific error
            $errorMessage = $e->getMessage();
            Log::info("social login error:". $errorMessage);
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
