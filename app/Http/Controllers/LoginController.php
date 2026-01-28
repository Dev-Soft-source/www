<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\FCMToken;
use App\Models\Language;
use App\Models\LoginPageSettingDetail;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
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

    public function create($lang = null){

        if (!session()->has('url.intended')) {
            session()->put('url.intended', url()->previous());
        }
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
            if ($selectedLanguage) {
                // Retrieve the LoginPageSettingDetail associated with the selected language
                $loginPage = LoginPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $loginPage = LoginPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }
        return view('login',['loginPage' => $loginPage,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function store(Request $request)
    {
        $niceNames = [];
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
                    'email'    => isset($loginPage->email_error) ? $loginPage->email_error : '',
                    'password' => isset($loginPage->password_error) ? $loginPage->password_error : '',
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
                    'email'    => isset($loginPage->email_error) ? $loginPage->email_error : '',
                    'password' => isset($loginPage->password_error) ? $loginPage->password_error : '',
                ];
            }
        }
        // Validate the form data with AJAX support
        try {
            $validatedData = $request->validate([
                'email'    => 'required|string|max:255|email',
                'password' => 'required',
            ], [],$niceNames);
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;
        }
        
        // Auth logic
        $credentials = $request->only('email', 'password');
        $user        = User::where('email', $credentials['email'])->first();
        
        if ($user) {
            if ($user->closed === '1') {
                $closeModalErrorMessage = "It looks like this account has been closed. We'd love to have you back! You can sign up for a new account using this email address anytime.";

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error'   => $closeModalErrorMessage,
                        'redirect_to_signup' => true,
                    ], 422);
                }

                return back()->with(['error' => $closeModalErrorMessage])->withInput();
            }

            if ($user->admin_deactive_account === '1') {
                $adminMsg = $message->admin_block_account_message
                    ?? 'Your account is suspended. Please contact us if you feel it should be reinstated';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error'   => $adminMsg,
                    ], 422);
                }

                return back()->with(['error' => $adminMsg])->withInput();
            }
        }

        // Check if remember me is checked
        // When checkbox is checked, it sends '1', when unchecked it sends '0' or is missing
        $rememberValue = $request->input('remember', '0');
        $remember = ($rememberValue == '1' || $rememberValue == 'on' || $rememberValue === true || $rememberValue === 1);
        
        Log::info('Login attempt with remember me', [
            'email' => $request->email,
            'remember_input' => $rememberValue,
            'remember_input_type' => gettype($rememberValue),
            'remember_boolean' => $remember
        ]);
        
        // Attempt authentication with remember me
        if ($user && !$user->trashed() && $user->email_verified != 0) {
            $loginSuccessful = auth()->attempt($credentials, $remember);
        } else {
            $loginSuccessful = false;
        }
        
        if ($loginSuccessful) {
            // Refresh user to get updated remember_token if remember was true
            $authenticatedUser = auth()->user();
            
            // Log successful authentication
            Log::info('Login successful', [
                'user_id' => $authenticatedUser->id,
                'email' => $request->email,
                'remember' => $remember,
                'remember_token_exists' => $authenticatedUser->remember_token ? 'yes' : 'no',
                'remember_token_length' => $authenticatedUser->remember_token ? strlen($authenticatedUser->remember_token) : 0
            ]);
            
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
            if ($authenticatedUser->step === '1') {
                $redirectUrl = route('step1to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($authenticatedUser->step === '2') {
                $redirectUrl = route('step2to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($authenticatedUser->step === '3') {
                $redirectUrl = route('step3to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($authenticatedUser->step === '4') {
                $redirectUrl = route('step5to5', ['lang' => $selectedLanguage->abbreviation]);
            } else {
                // Use route helper instead of manual URL construction
                $redirectUrl = route('home', ['lang' => $selectedLanguage->abbreviation]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                $response = response()->json([
                    'success'      => true,
                    'redirect_url' => $redirectUrl,
                    'remember_set' => $remember
                ]);
                
                // Log cookie information for debugging
                if ($remember) {
                    // Note: Cookies are queued by middleware, so they may not appear in response headers here
                    // The AddQueuedCookiesToResponse middleware will add them after this response
                    $cookieName = Auth::getRecallerName();
                    
                    Log::info('Remember me authentication completed', [
                        'user_id' => $authenticatedUser->id,
                        'remember_token' => $authenticatedUser->remember_token ? 'exists' : 'missing',
                        'remember_token_length' => $authenticatedUser->remember_token ? strlen($authenticatedUser->remember_token) : 0,
                        'expected_cookie_name' => $cookieName,
                        'note' => 'Cookie will be set by AddQueuedCookiesToResponse middleware'
                    ]);
                }
                
                return $response;
            }

            return redirect()->intended($redirectUrl);
        } else {
            // Error branches
            if ($user && $user->trashed()) {
                $errorMsg = 'Account is not available anymore';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error'   => $errorMsg,
                    ], 422);
                }

                return back()->with(['message' => $errorMsg])->withInput();
            } elseif ($user && $user->email_verified == 0) {
                $errorMsg = ($message->verified_email_message ?? null)
                    . '<a href="' . route('sendEmailVerify', ['email' => $user->email]) . '">Request a new verification email</a>';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success'     => false,
                        'error'       => $errorMsg,
                        'verify_email'=> true,
                        'email'       => $user->email,
                    ], 422);
                }

                return back()->with([
                    'error'        => $errorMsg,
                    'verify_email' => true,
                    'email'        => $user->email,
                ])->withInput();
            } elseif ($user) {
                // User exists but password is incorrect
                $errorMsg = $message->no_password_match_message ?? 'The password you entered is incorrect.';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors'  => ['password' => [$errorMsg]],
                    ], 422);
                }

                return back()->withErrors(['password' => $errorMsg])->withInput();
            } else {
                // User doesn't exist - email is incorrect
                $errorEmailMsg = $message->no_user_match_message ?? 'The email you entered is incorrect.';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors'  => ['email' => [$errorEmailMsg]],
                    ], 422);
                }

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
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation]);
    }

    public function welcomeRoute($email){
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        $user = User::where('email', $email)->first();
        if(isset($user) && !empty($user)){

        }else{
            return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "This email is not exist"]);
        }

        $user = auth()->login($user);

        $user = User::where('email', $email)->first();

        if ($user->step === '1') {
            return redirect()->route('step1to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step === '2') {
            return redirect()->route('step2to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step === '3') {
            return redirect()->route('step3to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step === '4') {
            return redirect()->route('step5to5', ['lang' => $selectedLanguage->abbreviation]);
        }

        return redirect()->route('profile', ['lang' => $selectedLanguage->abbreviation]);
    }

    public function emailVerify($token, $email, Request $request){
        $selectedLanguage = session('selectedLanguage');
        $message = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('email_verified_message', 'continue_with_app_btn_label', 'create_my_profile_btn_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('email_verified_message', 'continue_with_app_btn_label', 'create_my_profile_btn_label')->first();
            }
        }

        $isApp = $request->has('app') && $request->get('app') === 'true';
        
        $result = DB::table('password_resets')->where('token', $token)->where('type', 'verify_email')->first();
        $user = User::where('email', $email)->first();
        
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
                'message' =>  'Welcome to ProximaRide',
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
