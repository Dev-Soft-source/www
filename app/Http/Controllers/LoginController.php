<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\FCMToken;
use App\Models\Language;
use App\Models\LoginPageSettingDetail;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\ThankyouPageSettingDetail;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function appLogin($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $loginPage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        return view('login_with_app',['languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function create(Request $request, $lang = null){
        $redirectTo = $request->query('redirect_to');

        if (is_string($redirectTo) && str_starts_with($redirectTo, url('/'))) {
            session()->put('url.intended', $redirectTo);
        } elseif (!session()->has('url.intended')) {
            session()->put('url.intended', url()->previous());
        }
        
        $loginPage = LoginPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
       
        return view('login',['loginPage' => $loginPage]);
    }

    public function store(Request $request)
    {
        $niceNames = [
            'email' => __('validation.attributes.email'),
            'password' => __('validation.attributes.password'),
        ];
        $message = null;
        $selectedLanguage = session('selectedLanguage');

        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)
                    ->select('no_user_match_message', 'no_password_match_message', 'verified_email_message', 'admin_block_account_message')
                    ->first();
                $loginPage = LoginPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'email'    => $loginPage->email_label ?? __('validation.attributes.email'),
                    'password' => $loginPage->password_label ?? __('validation.attributes.password'),
                ];
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)
                    ->select('no_user_match_message', 'no_password_match_message', 'verified_email_message', 'admin_block_account_message')
                    ->first();

                $loginPage = LoginPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'email'    => $loginPage->email_label ?? __('validation.attributes.email'),
                    'password' => $loginPage->password_label ?? __('validation.attributes.password'),
                ];
            }
        }

        // Validate the form data
        $validatedData = $request->validate([
            'email'    => 'required|string|max:255|email',
            'password' => 'required',
        ], [], $niceNames);

        // Auth logic
        $credentials = $request->only('email', 'password');
        $user        = User::where('email', $credentials['email'])->first();
        
        if ($user) {
            if ($user->closed === '1') {
                $closeModalErrorMessage = $message->account_closed_message
                    ?? $loginPage->close_modal_error_message
                    ?? "It looks like this account has been closed. We'd love to have you back! You can sign up for a new account using this email address anytime.";

                return back()->with(['error' => $closeModalErrorMessage])->withInput();
            }

            if ($user->admin_deactive_account === '1') {
                $adminMsg = $message->admin_block_account_message
                    ?? 'Your account is suspended. Please contact us if you feel it should be reinstated';

                return back()->with(['error' => $adminMsg])->withInput();
            }
        }

        // Check if remember me is checked
        // When checkbox is checked, it sends '1', when unchecked it sends '0' or is missing
        $rememberValue = $request->input('remember', '0');
        $remember = ($rememberValue == '1' || $rememberValue == 'on' || $rememberValue === true || $rememberValue === 1);
        
        // Log::info('Login attempt with remember me', [
        //     'email' => $request->email,
        //     'remember_input' => $rememberValue,
        //     'remember_input_type' => gettype($rememberValue),
        //     'remember_boolean' => $remember
        // ]);
        
        // Attempt authentication with remember me
        if ($user && !$user->trashed() && $user->email_verified != 0) {
            $loginSuccessful = auth()->attempt($credentials, $remember);
        } else {
            $loginSuccessful = false;
        }
        
        if ($loginSuccessful) {
            // Refresh user to get updated remember_token if remember was true
            $authenticatedUser = auth()->user();
            $selectedLanguage = $selectedLanguage
                ?: Language::where('abbreviation', $authenticatedUser->lang)->first()
                ?: Language::where('is_default', 1)->first();

            if ($selectedLanguage && $authenticatedUser->lang !== $selectedLanguage->abbreviation) {
                $authenticatedUser->forceFill([
                    'lang' => $selectedLanguage->abbreviation,
                ])->save();
            }

            if ($selectedLanguage) {
                session(['selectedLanguage' => $selectedLanguage->abbreviation]);
            }
            
            // Log successful authentication
            // Log::info('Login successful', [
            //     'user_id' => $authenticatedUser->id,
            //     'email' => $request->email,
            //     'remember' => $remember,
            //     'remember_token_exists' => $authenticatedUser->remember_token ? 'yes' : 'no',
            //     'remember_token_length' => $authenticatedUser->remember_token ? strlen($authenticatedUser->remember_token) : 0
            // ]);
            
            // IP & user_details tracking (unchanged)
            $ipAddress = null;
            foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP',
                    'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
                if (array_key_exists($key, $_SERVER) === true) {
                    foreach (explode(',', $_SERVER[$key]) as $ip) {
                        $ip = trim($ip);
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                            $ipAddress = $ip;
                            break 2;
                        }
                    }
                }
            }
            $ipAddress = $ipAddress ?? 'UNKNOWN';

            $record = DB::table('user_details')->where('user_id', $authenticatedUser->id)->where('ip_address', $ipAddress)->first();
            if (!$record) {
                DB::table('user_details')->insert([
                    'ip_address' => $ipAddress,
                    'type'       => 'web',
                    'user_id'    => $authenticatedUser->id,
                    'created_at' => now(),
                ]);
            }

            // Redirect logic (user is already authenticated from attempt above)
            // Use authenticated user for redirect logic
            if ($authenticatedUser->step1 == 0) {
                $redirectUrl = route('step1to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($authenticatedUser->step2 == 0) {
                $redirectUrl = route('step2to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($authenticatedUser->step3 == 0) {
                $redirectUrl = route('step3to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($authenticatedUser->step4 == 0) {
                $redirectUrl = route('step4to5', ['lang' => $selectedLanguage->abbreviation]);
            } else {
                // Use route helper instead of manual URL construction
                $redirectUrl = route('home', ['lang' => $selectedLanguage->abbreviation]);
            }

            return redirect()->intended($redirectUrl);
        } else {
            // Error branches
            if ($user && $user->trashed()) {
                $errorMsg = $message->account_closed_message
                    ?? $loginPage->close_modal_error_message
                    ?? 'Account is not available anymore.';

                return back()->with(['error' => $errorMsg])->withInput();
            } elseif ($user && $user->email_verified == 0) {
                $errorMsg = ($message->verified_email_message ?? null);

                return back()->with([
                    'error'        => $errorMsg,
                    'verify_email' => true,
                    'email'        => $user->email,
                ])->withInput();
            } elseif ($user) {
                // User exists but password is incorrect
                $errorMsg = $message->no_password_match_message ?? 'The password you entered is incorrect.';

                return back()->withErrors(['password' => $errorMsg])->withInput();
            } else {
                // User doesn't exist - email is incorrect
                $errorEmailMsg =  'We couldn’t find an account with this email address. Please check the spelling and try again.';

                $errorEmailMsg = $message->no_user_match_message
                    ?? 'We couldn\'t find an account with this email address. Please check the spelling and try again.';

                return back()->withErrors(['email' => $errorEmailMsg])->withInput();
            }
        }
    }

    public function destroy(Request $request)
    {
        if (Auth::guard('web')->check()) {
            session()->forget('uploaded_profile_image');
            Auth::guard('web')->logout();
        }

        // Http::asForm()->post('https://oauth2.googleapis.com/revoke', [
        //     'token' => "ya29.a0AUMWg_LqmpPRrUVyRFIip8K1dRoEeZNzNSNcrGd54vWh6BwSiXhacQ0SfMBhQO39eUw3O54QxLtt6TIzvQEeIH7-VcmzzkXaNo8B3pcDxQOPL4uxKf5Z7-syzmHK6_cvmG32VNZZH8Lx6qBDgtTiMYVY8zOgCZ7oqbrewl-srlB3cpygqK4G6DbwKN3K2GgBVxSxiskaCgYKAesSARcSFQHGX2Mi-2SVUa7Kk4o2tcqIAR3XZA0206",
        // ]);

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Auth::guard('web')->check()
                ? Language::where('abbreviation', Auth::guard('web')->user()->lang)->first()
                : null;

            if (!$selectedLanguage) {
                $selectedLanguage = Language::where('is_default', 1)->first();
            }
        }
        return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation]);
    }

    public function welcomeRoute($email){
        $user = User::where('email', $email)->first();
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        }

        if (!$selectedLanguage && $user?->lang) {
            $selectedLanguage = Language::where('abbreviation', $user->lang)->first();
        }

        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        if(isset($user) && !empty($user)){

        }else{
            return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "This email is not exist"]);
        }

        $user = auth()->login($user);
        session(['selectedLanguage' => $selectedLanguage->abbreviation]);

        $user = User::where('email', $email)->first();

        if ($user->step1 == 0) {
            return redirect()->route('step1to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step2 == 0) {
            return redirect()->route('step2to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step3 == 0) {
            return redirect()->route('step3to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step4 == 0) {
            return redirect()->route('step4to5', ['lang' => $selectedLanguage->abbreviation]);
        }

        return redirect()->route('profile', ['lang' => $selectedLanguage->abbreviation]);
    }

    /**
     * Show the welcome message page (e.g. when user clicks "Welcome to ProximaRide" notification).
     */
    public function showWelcomeMessage($lang = null)
    {
        $selectedLanguage = $lang
            ? Language::where('abbreviation', $lang)->first()
            : (session('selectedLanguage') ? Language::where('abbreviation', session('selectedLanguage'))->first() : null);
        if (! $selectedLanguage) {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        if ($selectedLanguage && $lang !== $selectedLanguage->abbreviation) {
            session(['selectedLanguage' => $selectedLanguage->abbreviation]);
        }

        $user = auth()->user();
        $data = [
            'first_name' => $user->first_name ?? '',
            'email' => $user->email ?? '',
        ];
        $defaultLang = Language::where('is_default', 1)->first();
        $thankyouPage = ThankyouPageSettingDetail::getByLanguageWithFallback(
            $selectedLanguage->id ?? $defaultLang?->id,
            $defaultLang->id ?? $selectedLanguage->id
        );
        $greeting_message = optional($thankyouPage)->welcome_greeting ?? 'Hi';
        $languages = Language::all();

        return view('welcome_message', compact('data', 'greeting_message', 'selectedLanguage', 'languages', 'thankyouPage'));
    }

    public function emailVerify($token, $email, Request $request){
        $isApp = $request->has('app') && $request->get('app') === 'true';
        
        $result = DB::table('password_resets')->where('token', $token)->where('type', 'verify_email')->first();
        $user = User::where('email', $email)->first();
        $selectedLanguage = session('selectedLanguage');
        $message = null;

        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        }

        if (!$selectedLanguage && $user?->lang) {
            $selectedLanguage = Language::where('abbreviation', $user->lang)->first();
        }

        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        if ($selectedLanguage) {
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('email_verified_message', 'continue_with_app_btn_label', 'create_my_profile_btn_label')->first();
        }
        
        if(isset($user) && !empty($user)){
            if (!$result && $user->email_verified === '1') {
                // User already verified - redirect appropriately
                if ($isApp) {
                    return redirect()->route('emailVerified', ['app' => 'true']);
                }
                return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "This email address has already been verified"]);
            } elseif (!$result) {
                // Invalid token
                if ($isApp) {
                    return redirect()->route('emailVerified', ['app' => 'true', 'error' => 'invalid_token']);
                }
                return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "This email verification token is invalid"]);
            }
        }else{
            // Email doesn't exist
            if ($isApp) {
                return redirect()->route('emailVerified', ['app' => 'true', 'error' => 'email_not_found']);
            }
            return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "This email is not exist"]);
        }


        $userUpdate = User::where('email', $result->email)->update([
            'email_verified' => '1'
        ]);

        if ($userUpdate) {
            DB::table('password_resets')->where('token', $token)->delete();

            $data = ['first_name' => $user->first_name, 'lang' => $selectedLanguage->abbreviation, 'email' => $user->email];
            // Send email verification
            Mail::to($user->email)->queue(new WelcomeMail($data));

            $notification = Notification::create([
                'type' => null,
                'category' => 'system',
                'receiver_id' => $user->id,
                'posted_by' => $user->id,
                'message' => getNotificationMessageText(
                    'welcome_to_proximaride',
                    $user,
                    [],
                    'Welcome to ProximaRide'
                ),
                'status' => 'welcome',
                'notification_type' => 'welcome',
            ]);

            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;
            $fcmService = new FCMService();

            if ($fcmToken) {
                // Send the booking notification
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

            $user = auth()->login($user);
            session(['selectedLanguage' => $selectedLanguage->abbreviation]);
            $token = auth()->user()->createToken('auth_token')->plainTextToken;
            
            // Redirect based on app parameter
            if ($isApp) {
                return redirect()->route('emailVerified', ['app' => 'true', 'success' => 'verified', 'token' => $token]);
            }
            
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['success1' => $message->email_verified_message,'continue_with_app_btn' => $message->continue_with_app_btn_label ?? "Continue with app", 'create_my_profile_btn' => $message->create_my_profile_btn_label ?? "Create my profile"]);
        }
    }

    /**
     * Handle email verified page for app deep links
     */
    public function emailVerified(Request $request)
    {
        $isApp = $request->has('app') && $request->get('app') === 'true';
        $token = $request->get('token'); // Get the auth token if passed
        
        if ($isApp) {
            // For app users, show a simple page that the app can detect
            $status = 'unknown';
            $message = 'Email verification status unknown';
            
            if ($request->has('success')) {
                $status = 'success';
                $message = 'Email verified successfully';
            } elseif ($request->has('error')) {
                $status = 'error';
                $error = $request->get('error');
                switch ($error) {
                    case 'invalid_token':
                        $message = 'Invalid verification token';
                        break;
                    case 'email_not_found':
                        $message = 'Email address not found';
                        break;
                    default:
                        $message = 'Email verification failed';
                }
            } else {
                $status = 'already_verified';
                $message = 'Email already verified';
            }
            
            return response()->view('email-verified', compact('status', 'message', 'isApp', 'token'))
                ->header('Content-Type', 'text/html; charset=utf-8');
        }
        
        // If not from app, redirect to login
        return redirect()->route('login')->with(['message' => 'Email verification complete']);
    }
}
