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
        
        $passwordSettingPage = PasswordSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

       
        return view('password', ['reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 
        'ProfileSetting' => $ProfileSetting, 
        'passwordSettingPage' => $passwordSettingPage]);
    }

    public function update($id, Request $request)
    {
        

        $messages = $this->successMessage;

        $request->validate([
            'pass1' => 'required',
            'pass2' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).+$/',
            'pass3' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).+$/|same:pass2',
        ]);

        // Check if the current password is correct
        if (!Hash::check($request->pass1, auth()->user()->password)) {
            throw ValidationException::withMessages(['pass1' => $messages->incorrect_password_message]);
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
            'message' => getNotificationMessageText(
                'password_changed',
                $user,
                [],
                'Your password has just been changed'
            ),
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

        return redirect()->route('password', ['lang' => $this->selectedLanguage->abbreviation])->with('success', $messages->password_update_message);
    }
}
