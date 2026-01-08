<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\EmailAddressUpdatedEmail;
use App\Mail\UserEmailVerification;
use App\Models\Language;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
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
    use StatusResponser;

    public function update(Request $request)
    {
        try {
            $customMessages = [
                'email.confirmed' => 'Email does not match',
            ];

            $validated = $request->validate([
                'old_email' => 'required|email',
                'email' => 'required|email|string|unique:users,email,NULL,id,deleted_at,NULL|confirmed',
            ], $customMessages);


            $selectedLanguage = app()->getLocale() ?? 'en';
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage)->select('current_email_not_match')->first();

            // Find the user
            $user = User::findOrFail($request->id);
            $notification = Notification::create([
                'type' => null,
                'category' => 'system',
                'receiver_id' => $user->id,
                'posted_by' => $user->id,
                'message' => 'A new email address added to your profile',
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
            if ($request->old_email !== $user->email) {
                return $this->apiErrorResponse(strip_tags($message->current_email_not_match ?? "The current email does not match"), 200);
            }

            // Store old email for notification
            $oldEmail = $user->email;

            // Update the user's email and set email_verified to 0
            $user->email = $request->email;
            $user->email_verified = '0';
            $user->save();

            $emailData = [
                'first_name' => $user->first_name,
            ];

            // Send to old email address
            Mail::to($oldEmail)->queue(new EmailAddressUpdatedEmail($emailData));

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
            Mail::to($user->email)->queue(new UserEmailVerification($verificationData));

            $message = null;
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
                if ($selectedLanguage) {
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('email_update_message')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('email_update_message')->first();
                }
            }

            return $this->successResponse('', strip_tags($message->email_update_message ?? null));
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();

            // Check if there's a password confirmation error
            if (isset($errors['email'])) {
                foreach ($errors['email'] as $key => $error) {
                    if ($error === 'Email does not match') {
                        $errors['email_confirmation'][] = $error;
                        unset($errors['email'][$key]);
                    }
                }

                // If email array is empty, remove it
                if (empty($errors['email'])) {
                    unset($errors['email']);
                }
            }

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $errors,
            ], 422);
        }
    }
}
