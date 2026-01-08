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

    public function store(Request $request){
        $niceNames = [];
        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {

                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('no_user_match_message', 'verified_email_message', 'admin_block_account_message')->first();
                $loginPage = LoginPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'email' => isset($loginPage->email_error) ? $loginPage->email_error : '',
                    'password' => isset($loginPage->password_error) ? $loginPage->password_error : '',
                ];
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('no_user_match_message', 'verified_email_message', 'admin_block_account_message')->first();
                $loginPage = LoginPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'email' => isset($loginPage->email_error) ? $loginPage->email_error : '',
                    'password' => isset($loginPage->password_error) ? $loginPage->password_error : '',
                ];
            }
        }

        // Validate the form data
        $validatedData = $request->validate([
            'email' => 'required|string|max:255|email',
            'password' => 'required',
        ], [], $niceNames);

        // Your authentication logic can go here, for example:
        $credentials = $request->only('email', 'password');

        // Find the user by email
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            if ($user->closed === '1') {
                $closeModalErrorMessage = $loginPage ? $loginPage->close_modal_error_message : 'Account has been closed';
                return back()->with(['error' => $closeModalErrorMessage])->withInput();
            }

            if ($user->admin_deactive_account === '1') {
                return back()->with(['error' => $message->admin_block_account_message ?? 'Your account is suspended. Please contact us if you feel it should be reinstated'])->withInput();
            }
        }

        if ($user && !$user->trashed() && $user->email_verified != 0 && auth()->attempt($credentials, $request->has('remember'))) {
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

            $record = DB::table('user_details')->where('user_id', $user->id)->where('ip_address', $ipAddress)->first();
            if (!$record) {
                DB::table('user_details')->insert([
                    'ip_address' => $ipAddress,
                    'type' => 'web',
                    'user_id' => $user->id,
                    'created_at' => now()
                ]);
            }


            // Authentication passed
            $user = auth()->user();
            if ($user->step === '1') {
                // Additional profile fields are empty, redirect to login page with modal
                return redirect()->route('step1to5', ['lang' => $selectedLanguage->abbreviation])->with(['showModal' => true, 'user' => $user]);
            } elseif ($user->step === '2') {
                return redirect()->route('step2to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($user->step === '3') {
                return redirect()->route('step3to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($user->step === '4') {
                return redirect()->route('step5to5', ['lang' => $selectedLanguage->abbreviation]);
            }
            return redirect()->intended('/'.$selectedLanguage->abbreviation.''.'/home');
        } else {
            if ($user && $user->trashed()) {
                // User account is soft deleted
                return back()->with(['message' => 'Account is not available anymore'])->withInput();
            } elseif ($user && $user->email_verified == 0) {
                return back()->with(['error' => $message->verified_email_message ?? null.'<a href="'.route('sendEmailVerify', ['email' => $user->email]).'">Request a new verification email</a>', 'verify_email' => true, 'email' => $user->email])->withInput();
            }
            // Authentication failed
            return back()->with(['error' => $message->no_user_match_message])->withInput();
        }
    }

    public function destroy(Request $request)
    {
        if (Auth::guard('web')->check()) {
            session()->forget('uploaded_profile_image');
            Auth::guard('web')->logout();
        }

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
