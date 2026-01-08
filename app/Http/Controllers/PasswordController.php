<?php

namespace App\Http\Controllers;

use App\Mail\PasswordChangedMail;
use App\Models\FCMToken;
use App\Models\Language;
use App\Models\MyReviewSettingDetail;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\PasswordSettingDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function index($lang = null)
    {
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $passwordSettingPage = null;
        $ProfilePage = null;
        $ProfileSetting = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $passwordSettingPage = PasswordSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $passwordSettingPage = PasswordSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
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
        return view('password', ['reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 'ProfileSetting' => $ProfileSetting, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'passwordSettingPage' => $passwordSettingPage]);
    }

    public function update($id, Request $request)
    {
        $niceNames = [];
        $messages = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('password_update_message', 'incorrect_password_message')->first();
                $passwordSettingPage = PasswordSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'pass1' => isset($passwordSettingPage->current_password_error) ? $passwordSettingPage->current_password_error : '',
                    'pass2' => isset($passwordSettingPage->new_password_error) ? $passwordSettingPage->new_password_error : '',
                    'pass3' => isset($passwordSettingPage->confirm_new_password_error) ? $passwordSettingPage->confirm_new_password_error : '',
                ];
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('password_update_message', 'incorrect_password_message')->first();
                $passwordSettingPage = PasswordSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'pass1' => isset($passwordSettingPage->current_password_error) ? $passwordSettingPage->current_password_error : '',
                    'pass2' => isset($passwordSettingPage->new_password_error) ? $passwordSettingPage->new_password_error : '',
                    'pass3' => isset($passwordSettingPage->confirm_new_password_error) ? $passwordSettingPage->confirm_new_password_error : '',
                ];
            }
        }

        $request->validate([
            'pass1' => 'required',
            'pass2' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).+$/',
            'pass3' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).+$/',
        ], [], $niceNames);

        // Check if the current password is correct
        if (!Hash::check($request->pass1, auth()->user()->password)) {
            throw ValidationException::withMessages(['pass1' => $messages->incorrect_password_message]);
        }
        if ($request->pass2 != $request->pass3) {
            throw ValidationException::withMessages(['pass3' => 'The confirm password does not match the new password.']);
        }

        // Update the user's password
        User::whereId($id)->update([
            'password' => bcrypt($request->pass2),
        ]);

        $user = User::find($id);

        Mail::to($user->email)->queue(new PasswordChangedMail([
            'first_name' => $user->first_name
        ]));

        $notification = Notification::create([
            'type' => null,
            'category' => 'system',
            'receiver_id' => $user->id,
            'posted_by' => $user->id,
            'message' =>  'Your password has just been changed',
            'status' => 'password',
            'notification_type' => 'password',
        ]);

        $body = $notification->message;
        $fcmService = new FCMService();

        $fcmToken = $user->mobile_fcm_token;
        if ($fcmToken) {
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

        return redirect()->route('password', ['lang' => $selectedLanguage->abbreviation])->with('success', $messages->password_update_message);
    }
}
