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
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('welcome_message', 'email_sent_message')->first();
                $signupPage = SignupPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'first_name' => isset($signupPage->first_name_error) ? $signupPage->first_name_error : '',
                    'last_name' => isset($signupPage->last_name_error) ? $signupPage->last_name_error : '',
                    'email' => isset($signupPage->email_error) ? $signupPage->email_error : '',
                    'password' => isset($signupPage->password_error) ? $signupPage->password_error : '',
                    'password_confirmation' => isset($signupPage->confirm_password_error) ? $signupPage->confirm_password_error : '',
                    'remember-me' => isset($signupPage->agree_terms_error) ? $signupPage->agree_terms_error : '',
                ];
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('welcome_message', 'email_sent_message')->first();
                $signupPage = SignupPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'first_name' => isset($signupPage->first_name_error) ? $signupPage->first_name_error : '',
                    'last_name' => isset($signupPage->last_name_error) ? $signupPage->last_name_error : '',
                    'email' => isset($signupPage->email_error) ? $signupPage->email_error : '',
                    'password' => isset($signupPage->password_error) ? $signupPage->password_error : '',
                    'password_confirmation' => isset($signupPage->confirm_password_error) ? $signupPage->confirm_password_error : '',
                    'remember-me' => isset($signupPage->agree_terms_error) ? $signupPage->agree_terms_error : '',
                ];
            }
        }

        // Validate the form data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-]+$/',
            'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).+$/',
            'remember-me' => 'required',
            'password_confirmation' => 'required|same:password',
            'rideshare_disclaimer' => 'required|accepted',
        ], [
            'remember-me.required' => isset($signupPage->agree_terms_error) ? $signupPage->agree_terms_error : 'You must agree to the terms',
    'password_confirmation.required' => isset($signupPage->confirm_password_error) ? $signupPage->confirm_password_error : 'Confirm password field is required',
    'password_confirmation.same' => isset($signupPage->password_mismatch_error) ? $signupPage->password_mismatch_error : 'The passwords do not match',
    'rideshare_disclaimer.required' => 'You must acknowledge the rideshare disclaimer',
    'rideshare_disclaimer.accepted' => 'You must acknowledge that ride sharing is not a business',   ], $niceNames);

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'type' => 'verify_email',
            'created_at' => Carbon::now()
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => 39,
            'referral_uuid' => bin2hex(random_bytes(16))
        ]);

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

        // Send email verification immediately; fallback to log mailer if SMTP fails
        try {
            Mail::to($request->email)->send(new UserEmailVerification($data));
        } catch (\Throwable $e) {
            try {
                Mail::mailer('log')->to($request->email)->send(new UserEmailVerification($data));
            } catch (\Throwable $e2) {
                // suppress
            }
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

        return redirect()->back()->with(['showModal' => true, 'messages' => $messages, 'user' => $user]);
    }

    public function sendEmailVerify($email)
    {
        $user = User::where('email', $email)->first();
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
        try {
            Mail::to($user->email)->send(new UserEmailVerification($data));
        } catch (\Throwable $e) {
            Log::error('Failed to send email verification', ['error' => $e->getMessage()]);
            try {
                Mail::mailer('log')->to($user->email)->send(new UserEmailVerification($data));
            } catch (\Throwable $e2) {
            }
        }

        return redirect()->back()->with(['success' => 'We will send you a verification email. Check your inbox']);
    }

    public function redirectToProvider($lang, $provider)
    {
        return Socialite::driver($provider)
        ->redirect();
    }

    public function handleProviderCallback($lang, $provider)
    {
        try {
            $user = Socialite::driver($provider)->user();

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
            ]);

            // Send admin notification about new social signup
            $adminData = [
                'user_name' => $firstName . ' ' . $lastName,
                'user_email' => $user->email,
                'registration_date' => Carbon::now()->format('M d, Y H:i:s'),
                'platform' => 'Web - ' . ucfirst($provider) . ' Login'
            ];
            Mail::to('ccaned@gmail.com')->queue(new AdminNewUserSignupMail($adminData));

            auth()->login($newUser);

            return redirect('/'); // Redirect to the home page
        } catch (\Exception $e) {

            $selectedLanguage = session('selectedLanguage');
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
            }

            Session::flash('message', "Unable to login using " . $provider . ". Please try again");

            return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation]);
        }
    }
}
