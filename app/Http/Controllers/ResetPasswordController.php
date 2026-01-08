<?php

namespace App\Http\Controllers;

use App\Mail\PasswordChangedMail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\ResetPasswordPageSettingDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    public function create(Request $request, $lang = null)
    {
        $languages = Language::all();

        // Check if this is from app and store in session
        if ($request->query('app') === 'true') {
            session(['is_app_request' => true]);
        }

        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $resetPasswordPage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                // Retrieve the ForgotPasswordPageSettingDetail associated with the selected language
                $resetPasswordPage = ResetPasswordPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $resetPasswordPage = ResetPasswordPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $existingRecord = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('type', 'user')
            ->first();

        if (!$existingRecord) {
            // If a record with the same email and type does not exists,
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "This password reset token is invalid."]);
        }

        $user = User::where('email', $existingRecord->email)->first();

        if (!$user) {
            // If a user with the same email does not exists,
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "Account does not exist"]);
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

        return view('reset_password', ['resetPasswordPage' => $resetPasswordPage, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'request' => $request]);
    }

    public function store(Request $request)
    {
        try {
            $selectedLanguage = session('selectedLanguage');
            $resetPasswordPage = null;
            $niceNames = [];

            // Check if this is from app
            $isApp = $request->query('app') === 'true' || session('is_app_request');

            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
                $resetPasswordPage = ResetPasswordPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'password' => isset($resetPasswordPage->password_error) ? $resetPasswordPage->password_error : '',
                    'password_confirmation' => isset($resetPasswordPage->confirm_password_error) ? $resetPasswordPage->confirm_password_error : '',
                ];
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                $resetPasswordPage = ResetPasswordPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'password' => isset($resetPasswordPage->password_error) ? $resetPasswordPage->password_error : '',
                    'password_confirmation' => isset($resetPasswordPage->confirm_password_error) ? $resetPasswordPage->confirm_password_error : '',
                ];
            }

            // Validate the form data
            $validatedData = $request->validate([
                'password' => 'required|confirmed|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).+$/',
            ], [], $niceNames);

            $password_resets = DB::table('password_resets')->where('token', $request->token)->where('type', 'user')->first();
            if (!$password_resets) {
                return back()->with(['message' => "This password reset token is invalid."]);
            }

            $user = User::where('email', $password_resets->email)->first();
            if (!$user) {
                return back()->withErrors(['message' => "We can't find a user with that email address."]);
            }
            if ($user) {
                $userUpdate = $user->update([
                    'password' => Hash::make($request->password),
                ]);
                if ($userUpdate) {
                    Mail::to($user->email)->queue(new PasswordChangedMail([
                        'first_name' => $user->first_name
                    ]));

                    DB::table('password_resets')->where('token', $request->token)->delete();

                    // If request is from app, redirect to app deep link
                    if ($isApp) {
                        return redirect()->away('proximaride://password-reset-success');
                    }

                    return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation])->with('success', 'Your password has been reset successfully.');
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();

            // Check if there's a password confirmation error
            if (isset($errors['password'])) {
                foreach ($errors['password'] as $key => $error) {
                    if ($error === 'Password does not match') {
                        $errors['password_confirmation'][] = $error;
                        unset($errors['password'][$key]);
                    }
                }

                // If password array is empty, remove it
                if (empty($errors['password'])) {
                    unset($errors['password']);
                }
            }

            return back()->withErrors($errors);
        }
    }
}
