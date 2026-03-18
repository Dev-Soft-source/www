<?php

namespace App\Http\Controllers;

use App\Mail\EmailAddressUpdatedEmail;
use App\Mail\UserEmailVerification;
use App\Models\Language;
use App\Models\MyEmailSettingDetail;
use App\Models\MyReviewSettingDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\User;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;
use App\Services\FCMService;
use App\Models\FCMToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailController extends Controller
{
    public function index($lang = null)
    {
        
        $emailSettingPage = MyEmailSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();

            return view('email', ['reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 'ProfileSetting' => $ProfileSetting, 
            'emailSettingPage' => $emailSettingPage, 'user' => $user]);
        } else {
            return redirect()->route('home', ['lang' => $this->selectedLanguage->abbreviation]);
        }
    }

    public function update($userId, Request $request)
    {
        // Get language for redirects
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        Log::info("User with ID {$userId} is attempting to update their email address.");
        try {
            $validated = $request->validate([
            //    'old_email' => 'required|email',
                'email_confirmation' => 'required|email',
                'email' => 'required|email|string|unique:users,email,NULL,id,deleted_at,NULL|confirmed',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('email', ['lang' => $selectedLanguage->abbreviation])
                ->withErrors($e->errors())
                ->withInput();
        }
        Log::info("User with ID {$userId} is attempting to update their email address.");
        // Find the user
        $user = User::findOrFail($userId);
        Log::info("User {$user->id} is updating their email from {$user->email} to {$request->email}");
        $notification = Notification::create([
            'type' => null,
            'category' => 'system',
            'receiver_id' => $user->id,
            'posted_by' => $user->id,
            'message' => getNotificationMessageText(
                'email_added_to_profile',
                $user,
                [],
                'A new email address added to your profile'
            ),
            'status' => 'completed',
            'notification_type' => 'upcoming'
        ]);
        // Send push notification
        $fcmService = new FCMService();
        $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
        $body = $notification->message;
        $fcmToken = $user->mobile_fcm_token;
        if ($fcmToken) {
            $fcmService->sendNotification($fcmToken, $body);
        }
        foreach ($fcm_tokens as $fcm_token) {
            try {
                $fcmService->sendNotification($fcm_token->token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: $fcm_token->token, Error: " . $e->getMessage());
            }
        }
        // Check if the old email matches
        // if ($request->old_email !== $user->email) {
        //     return redirect()->route('email', ['lang' => $selectedLanguage->abbreviation])
        //         ->withErrors(['old_email' => 'The current email does not match.'])
        //         ->withInput();
        // }

        // Store old email for notification
        //$oldEmail = $user->email;

        // Update the user's email and set email_verified to 0
        $user->email = $request->email;
        $user->email_verified = '0';
        $user->save();

        $emailData = [
            'first_name' => $user->first_name,
        ];

        // Send to old email address
        // Mail::to($oldEmail)->queue(new EmailAddressUpdatedEmail($emailData));

        // Generate verification token for new email
        $token = Str::random(64);
        
        // Remove any existing verification token for this email
        DB::table('password_resets')
            ->where('email', $user->email)
            ->where('type', 'verify_email')
            ->delete();

        // Insert new verification token
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'type' => 'verify_email',
            'created_at' => Carbon::now()
        ]);

        // Send verification email to new email address
        $verificationData = [
            'first_name' => $user->first_name,
            'email' => $user->email,
            'token' => $token
        ];
        
        // Queue email for sending
        Mail::to($user->email)->queue(new EmailAddressUpdatedEmail($verificationData));
        // Mail::to($user->email)->queue(new UserEmailVerification($verificationData));

        $successMessages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)
            ->select('email_update_verify_message', 'email_update_message')
            ->first();

        if (!$successMessages) {
            $defaultLang = Language::where('is_default', 1)->first();
            $successMessages = $defaultLang
                ? SuccessMessagesSettingDetail::where('language_id', $defaultLang->id)
                    ->select('email_update_verify_message', 'email_update_message')
                    ->first()
                : null;
        }

        $successMessage = ($successMessages?->email_update_verify_message ?? $successMessages?->email_update_message)
            ?? 'Email updated successfully. Please verify your new email address.';

        return redirect()->route('email', ['lang' => $selectedLanguage->abbreviation])
            ->with('success', $successMessage);
    }
}
