<?php

namespace App\Http\Controllers;

use App\Mail\StudentCardAddedMail;
use App\Mail\StudentCardUploadMail;
use App\Models\Admin;
use App\Models\Country;
use App\Models\FCMToken;
use App\Models\Language;
use App\Models\MyReviewSettingDetail;
use App\Models\MyStudentCardSettingDetail;
use App\Models\Notification;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerifyStudentController extends Controller
{
    public function index($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $studentCardPage = null;
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $studentCardPage = MyStudentCardSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $studentCardPage = MyStudentCardSettingDetail::where('language_id', $selectedLanguage->id)->first();
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

            return view('verify_student',['reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'studentCardPage' => $studentCardPage,'user' => $user,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
        
    }

    public function update($id, Request $request){
        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('student_card_upload_message', 'image_size_error_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('student_card_upload_message', 'image_size_error_message')->first();
            }
        }
        
        $customMessages = [
            'file' => 'Please select a valid file',
            'max' => $message->image_size_error_message,
        ];

        // Manual validation for file extensions and magic bytes (works without php_fileinfo extension)
        if ($request->hasFile('student_card')) {
            $file = $request->file('student_card');
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf'];

            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->withErrors(['student_card' => 'The file must be a PDF, JPEG, PNG, JPG, or GIF file.'])->withInput();
            }

            // Check file magic bytes for more robust validation
            $mime = null;
            try {
                $handle = fopen($file->getRealPath(), 'r');
                $bytes = fread($handle, 4); // Read first 4 bytes
                fclose($handle);

                if (str_starts_with($bytes, "\xFF\xD8\xFF")) { // JPEG
                    $mime = 'image/jpeg';
                } elseif (str_starts_with($bytes, "\x89\x50\x4E\x47")) { // PNG
                    $mime = 'image/png';
                } elseif (str_starts_with($bytes, "%PDF")) { // PDF
                    $mime = 'application/pdf';
                } elseif (str_starts_with($bytes, "GIF8")) { // GIF
                    $mime = 'image/gif';
                }
            } catch (\Exception $e) {
                \Log::error("Error reading file magic bytes: " . $e->getMessage());
            }

            // Validate file type based on extension
            if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                // For image files, verify magic bytes
                if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
                    return redirect()->back()->withErrors(['student_card' => 'The file is not a valid JPEG, PNG, or GIF image.'])->withInput();
                }
            } elseif ($extension === 'pdf') {
                // For PDF files, verify magic bytes
                if ($mime !== 'application/pdf') {
                    return redirect()->back()->withErrors(['student_card' => 'The file is not a valid PDF file.'])->withInput();
                }
            }

            // Validate file size (10MB max = 10240 KB)
            if ($file->getSize() > 10240 * 1024) {
                return redirect()->back()->withErrors(['student_card' => $message->image_size_error_message ?? 'File size cannot exceed 10MB.'])->withInput();
            }

            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/student_cards');
            $file->move($destination_path, $filename);
        } elseif ($request->has('existing_image')) {
            $filename = $request->input('existing_image');
        } else {
            // If no file and no existing image, and it's required
            if (!$request->has('existing_image')) {
                return redirect()->back()->withErrors(['student_card' => 'Please select a valid file.'])->withInput();
            }
        }
        
        $user = User::whereId($id)->first();
        if (basename($user->student_card) != $filename || $user->student_card_exp_date != $request->expiry_date) {
            User::whereId($id)->update([
                'student_card' => $filename,
                'student_card_upload' => Carbon::now(),
                'student_card_exp_date' => $request->expiry_date,
                'student' => 2,
                'charge_booking' => 2,
            ]);
        }

        $userEmailData = [
            'first_name' => $user->first_name,
        ];
        if (isset($user->email_notification) && $user->email_notification == 1) {
        Mail::to($user->email)->send(new StudentCardAddedMail($userEmailData));
        }
        $country = Country::whereId($user->country)->first();
        $admin = Admin::first();

        $data = [
            'username' => $admin->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'country' => $country ? $country->name : 'Not specified',
            'upload_date' => Carbon::now()->format('M d, Y H:i:s'),
            'expiry_date' => $request->expiry_date ? Carbon::parse($request->expiry_date)->format('M d, Y') : 'Not provided'
        ];
        // Send admin notification email
        Mail::to($admin->admin_email)->queue(new StudentCardUploadMail($data));

        $notification = Notification::create([
            'type' => null,
            'category' => 'system',
            'receiver_id' => $user->id,
            'posted_by' => $user->id,
            'message' =>  'A new student card added to your profile',
            'status' => 'student_card',
            'notification_type' => 'student_card',
        ]);

        $fcmToken = $user->mobile_fcm_token;
        $body = $notification->message;
        $fcmService = new FCMService();

        if ($fcmToken) {
            // Send the booking notification
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

        return redirect()->route('student.verify', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->student_card_upload_message);
    }
}
