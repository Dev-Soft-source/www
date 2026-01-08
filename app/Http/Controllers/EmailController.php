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
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $emailSettingPage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $emailSettingPage = MyEmailSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $emailSettingPage = MyEmailSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        }
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();

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

            return view('email', ['reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 'ProfileSetting' => $ProfileSetting, 'emailSettingPage' => $emailSettingPage, 'user' => $user, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    public function update($userId, Request $request)
    {
        // dd($request->all());
        $customMessages = [
            // 'email_confirmation.required' => 'Confirm email is required',
            // 'confirmed' => 'Confirm email does not match',
            // // 'password' => 'Password is wrong',
            // 'password.required' => 'Current password is required',
            // 'email' => 'Please use a valid email address',
            // 'email.required' => 'Email address is required',
        ];

        $validated = $request->validate([
            'old_email' => 'required|email',
            'email_confirmation' => 'required|email',
            'email' => 'required|email|string|unique:users,email,NULL,id,deleted_at,NULL|confirmed',
            // 'password' => [
            //     'required',
            //     function ($attribute, $value, $fail) {
            //         if (!Hash::check($value, auth()->user()->password)) {
            //             $fail(__('validation.password'));
            //         }
            //     },
            // ],
        ], $customMessages);

        // Find the user
        $user = User::findOrFail($userId);
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
            return redirect()->back()->withErrors(['old_email' => 'The current email does not match.'])->withInput();
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
        
        Mail::to($user->email)->queue(new EmailAddressUpdatedEmail($verificationData));
        // Mail::to($user->email)->queue(new UserEmailVerification($verificationData));

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        return redirect()->route('email', ['lang' => $selectedLanguage->abbreviation]);
    }
}
