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

        Log::info($signupPage);

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
        // ... existing code for $messages, $signupPage, $niceNames ...

        // Validate with AJAX error handling
        try {
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
                'rideshare_disclaimer.accepted' => 'You must acknowledge that ride sharing is not a business',
            ], $niceNames);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // ... existing code for creating user, sending emails, etc. ...

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'showModal' => true,
                'messages' => [
                    'welcome_message' => $messages->welcome_message ?? '',
                    'email_sent_message' => $messages->email_sent_message ?? ''
                ],
                'user' => [
                    'first_name' => $user->first_name,
                    'email' => $user->email
                ]
            ]);
        }

        // Return redirect for non-AJAX requests
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
