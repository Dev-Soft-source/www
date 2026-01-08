<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\StudentCardAddedMail;
use App\Mail\StudentCardUploadMail;
use App\Models\Admin;
use App\Models\Country;
use App\Models\FCMToken;
use App\Models\User;
use App\Models\Language;
use App\Models\MyStudentCardSettingDetail;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerifyStudentController extends Controller
{
    use StatusResponser;

    public function index(Request $request){
        $user = Auth::guard('sanctum')->user();

        $studentCardPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $studentCardPage = MyStudentCardSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $studentCardPage = MyStudentCardSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $validationMessages = [
            'required' => trans('validation.required'),
            'file' => trans('validation.file'),
            'mimes' => trans('validation.mimes'),
            'max' => trans('validation.max.file'),
            'after' => trans('validation.after'),
        ];

        $data = ['user' => $user, 'studentCardPage' => $studentCardPage, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Get user successfully');
    }

    public function update(Request $request){
        $message = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('student_card_upload_message', 'image_size_error_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('student_card_upload_message', 'image_size_error_message')->first();
            }
        }

        $user = Auth::guard('sanctum')->user();
        $customMessages = [
            'max' => $message->image_size_error_message,
        ];
        $request->validate([
            'student_card' => $user->student_card ? 'nullable|file|mimes:pdf,jpeg,png,gif|max:10240' : 'required|file|mimes:pdf,jpeg,png,gif|max:10240',
            'student_card_exp_date' => 'required|after:today',
        ], $customMessages);

        $filenameOriginal = "";
        if ($request->hasFile('student_card')) {
            $file = $request->file('student_card');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/student_cards');
            $file->move($destination_path,$filename);

            $fileOriginal = $request->file('student_card_original_upload');
            $filenameOriginal = $file->getClientOriginalName();
            $destination_path = public_path('/student_cards');
            $fileOriginal->move($destination_path,$filenameOriginal);

        } elseif ($user->student_card) {
            $filename = basename($user->student_card);
        }
        
        $student_card_exp_date = $request->student_card_exp_date ? $request->student_card_exp_date : '';

        $user = User::whereId($request->id)->first();
        if (basename($user->student_card) != $filename || $user->student_card_exp_date != $student_card_exp_date) {
            User::whereId($request->id)->update([
                'student_card' => $filename,
                'student_card_original_upload' => $filenameOriginal,
                'student_card_upload' => Carbon::now(),
                'student_card_exp_date' => $student_card_exp_date,
                'student' => 2,
                'charge_booking' => 2,
            ]);
        }

        $user = User::whereId($request->id)->first();

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
            'expiry_date' => $student_card_exp_date ? Carbon::parse($student_card_exp_date)->format('M d, Y') : 'Not provided'
        ];
        // Send admin notification email
        Mail::to($admin->admin_email)->queue(new StudentCardUploadMail($data));

        $notification = Notification::create([
            'category' => 'system',
            'type' => null,
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

        $data = ['user' => $user];
        return $this->successResponse($data, $message->student_card_upload_message);
    }

    public function remove(){
        $user_id = Auth::guard('sanctum')->user()->id;

        User::whereId($user_id)->update([
            'student_card' => '',
            'student_card_upload' => '',
            'student_card_original_upload' => NULL,
            'student_card_exp_date' => '',
            'student' => 0,
        ]);

        return $this->successResponse('', 'Student card removed successfully');
    }
}
