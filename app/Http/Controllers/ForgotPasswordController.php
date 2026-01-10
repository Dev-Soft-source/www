<?php

namespace App\Http\Controllers;

use App\Mail\UserForgotPassword;
use App\Models\ForgotPasswordPageSettingDetail;
use App\Models\Language;
use App\Models\LoginPageSettingDetail;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function create($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $forgotPasswordPage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                // Retrieve the ForgotPasswordPageSettingDetail associated with the selected language
                $forgotPasswordPage = ForgotPasswordPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $forgotPasswordPage = ForgotPasswordPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
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

        return view('forgot_password',['forgotPasswordPage' => $forgotPasswordPage,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function store(Request $request) {
        $niceNames = [];
        $messages = null;
        $selectedLanguage = session('selectedLanguage');
        
        // Fetch language and messages
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)
                    ->select('no_user_found_message', 'reset_password_message')
                    ->first();

                $forgotPasswordPage = ForgotPasswordPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $loginPage = LoginPageSettingDetail::where('language_id', $selectedLanguage->id)->first(); // Get the correct model for login page
                $niceNames = [
                    'email' => isset($forgotPasswordPage->email_error) ? $forgotPasswordPage->email_error : '',
                ];
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)
                    ->select('no_user_found_message', 'reset_password_message')
                    ->first();

                $forgotPasswordPage = ForgotPasswordPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $loginPage = LoginPageSettingDetail::where('language_id', $selectedLanguage->id)->first(); // Get the correct model for login page
                $niceNames = [
                    'email' => isset($forgotPasswordPage->email_error) ? $forgotPasswordPage->email_error : '',
                ];
            }
        }

        $validatedData = $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => $forgotPasswordPage->field_require,
            'email.email' => $forgotPasswordPage->email_error,
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => $messages->no_user_found_message]);
        }

        // Check if the user's account is closed
        if ($user->closed === '1') {
            // Pass the user to the session so it's available in the view
            session(['user' => $user]);

            // Get the close_modal_error_message from LoginPageSettingDetail
            $closeModalErrorMessage = isset($loginPage->close_modal_error_message) ? $loginPage->close_modal_error_message : 'Account has been closed';
            return back()->with(['error' => $closeModalErrorMessage])->withInput();
        }

        // If the account is not closed and email is verified
        if ($user->email_verified == 0) {
            return back()->with(['showModal' => true, 'user' => $user]);
        }

        $token = Str::random(64);

        $existingRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('type', 'user')
            ->first();

        if ($existingRecord) {
            // If a record with the same email and type exists, delete it
            DB::table('password_resets')
                ->where('email', $request->email)
                ->where('type', 'user')
                ->delete();
        }

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'type' => 'user',
            'created_at' => Carbon::now()
        ]);

        $data = ['token' => $token, 'first_name' => $user->first_name, 'lang' => $selectedLanguage->abbreviation];

        // Send reset password mail
        try {
            Mail::to($request->email)->send(new UserForgotPassword($data));
        } catch (\Exception $e) {
            \Log::error('Forgot password email failed: ' . $e->getMessage());
            return back()->withErrors(['email' => $forgotPasswordPage->fail_send]);
        }

        return redirect()->route('forgot.password', ['lang' => $selectedLanguage->abbreviation])
                        ->with(['message' => $messages->reset_password_message ?? null]);
    }

}
