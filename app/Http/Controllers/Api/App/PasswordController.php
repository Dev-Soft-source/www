<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangedMail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    use StatusResponser;

    public function update(Request $request){
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).+$/',
            'confirm_password' => 'required',
        ]);

        $messages = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('password_update_message', 'incorrect_password_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('password_update_message', 'incorrect_password_message')->first();
            }
        }

        $user = Auth::guard('sanctum')->user();
        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            $message = $messages->incorrect_password_message;
            return response()->json([
                'message' => $message,
                'errors' => [
                    'current_password' => [$message],
                ],
            ], 422);
        }

        if ($request->new_password != $request->confirm_password) {
            $message = "Password does not match";
            return response()->json([
                'message' => $message,
                'errors' => [
                    'confirm_password' => [$message],
                ],
            ], 422);
        }

        // Update the user's password
        User::whereId($request->id)->update([
            'password' => bcrypt($request->new_password),
        ]);

        Mail::to($user->email)->queue(new PasswordChangedMail([
            'first_name' => $user->first_name
        ]));

        $notification = Notification::create([
            'category' => 'system',
            'type' => null,
            'receiver_id' => $user->id,
            'posted_by' => $user->id,
            'message' =>  'Your password has just been changed',
            'status' => 'password',
            'notification_type' => 'password',
        ]);

        $fcmToken = $user->mobile_fcm_token;
        $body = $notification->message;
        $fcmService = new FCMService();

        if ($fcmToken) {
            // Send the booking notification
            $fcmService->sendNotification($fcmToken, $body);
        }

        return $this->successResponse('', $messages->password_update_message);
    }
}
