<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\PhoneNumberAddedMail;
use App\Mail\PhoneNumberDeleted;
use App\Models\PhoneNumber;
use App\Models\User;
use App\Models\MyPhoneSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class PhoneController extends Controller
{
    use StatusResponser;

    public function index(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        $phone_numbers = PhoneNumber::where('user_id',$user_id)->orderBy('id', 'desc')->get();

        $phoneSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $phoneSettingPage = MyPhoneSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $phoneSettingPage = MyPhoneSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $validationMessages = [
            'required' => trans('validation.required'),
            'unique' => trans('validation.unique'),
        ];

        $data = ['phone_numbers' => $phone_numbers, 'phoneSettingPage' => $phoneSettingPage, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Get phone numbers successfully');
    }

    public function store(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        $skip = $request->filled('skip') ? $request->skip : '0';

        // Normalize and validate phone number
        $normalizedPhone = null;
        if ($skip === '0' && $request->phone) {
            // For API, we expect the full international number
            $normalizedPhone = normalizePhoneNumber($request->phone);

            if (!validatePhoneNumber($request->phone)) {
                return $this->apiErrorResponse('Please enter a valid phone number.', 422);
            }
        }

        // Validate uniqueness
        if ($normalizedPhone) {
            $request->validate([
                'phone' => 'unique:phone_numbers,phone,NULL,id,user_id,'.$user_id,
            ]);
        }


        $message = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_add_message', 'admin_block_account_message', 'suspended_account_phone_number_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_add_message', 'admin_block_account_message', 'suspended_account_phone_number_message')->first();
            }
        }

        if ($skip === '0') {

            $getBlockPhoneNumberUser = PhoneNumber::where('phone', $request->phone)->whereHas('user', function($q){
                $q->where('admin_deactive_account', '1');
            })->first();


            if (isset($getBlockPhoneNumberUser) && !empty($getBlockPhoneNumberUser)) {
                return $this->apiErrorResponse(strip_tags($message->admin_block_account_message ?? 'Your account is suspended. Please contact us if you feel it should be reinstated'), 200);
            }

            $existingPhone = PhoneNumber::where('phone', $normalizedPhone)->first();

            if ($existingPhone) {
                if ($existingPhone->user_id != $user_id) {
                    $otherUser = \App\Models\User::find($existingPhone->user_id);
                    if ($otherUser && ($otherUser->admin_deactive_account == 1 || $otherUser->suspand == 1)) {
                        return $this->apiErrorResponse($message->suspended_account_phone_number_message ?? 'This phone number belongs to a suspended or deactivated account.', 200);
                    }
                } else {
                    // same user apna number dobara add kar raha hai
                    return $this->apiErrorResponse('You have already added this phone number.', 200);
                }
            }

            $phone = PhoneNumber::create([
                'user_id' => $user_id,
                'phone' => $normalizedPhone,
            ]);

            $emailData = [
                'first_name' => $user->first_name,
            ];
            if (isset($user->email_notification) && $user->email_notification == 1) {
                Mail::to($user->email)->send(new PhoneNumberAddedMail($emailData));
            }
        }

        if ($request->step) {
            User::whereId($user_id)->update([
                'step' => '5',
            ]);

            $user = User::whereId($user_id)->first();

            if ($request->lang_id && $request->lang_id != 0) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
                }
            }

            if ($user->gender) {
                if ($user->gender === 'male') {
                    $user->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($user->gender === 'female') {
                    $user->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($user->gender === 'prefer not to say') {
                    $user->gender_label = $genderLabel->prefer_option_label ?? null;
                }
            }

            $data = ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'gender' => $user->gender, 'gender_label' => $user->gender_label, 'profile_image' => $user->profile_image, 'email' => $user->email, 'step' => $user->step, 'id' => $user->id];
            return $this->successResponse($data, 'Step 4 completed successfully');
        }

        $notification = Notification::create([
            'type' => null,
            'category' => 'system',
            'receiver_id' => $user->id,
            'posted_by' => $user->id,
            'message' =>  'A new phone number added to your profile',
            'status' => 'phone',
            'notification_type' => 'phone',
        ]);

        $fcmToken = $user->mobile_fcm_token;
        $body = $notification->message;
        $fcmService = new FCMService();

        if ($fcmToken) {
            // Send the booking notification
            $fcmService->sendNotification($fcmToken, $body);
        }

        $phone_number = PhoneNumber::whereId($phone->id)->first();
        $data = ['phone_number' => $phone_number];
        return $this->successResponse($data, strip_tags($message->phone_add_message));
    }

    public function sendVerificationCode(Request $request)
    {
        $selectedLanguage = app()->getLocale() ?? 'en';
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

        $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('verfification_code_sent_message','general_error_message','suspended_account_phone_number_message')->first();

        if ($request->id != '' && $request->id != '0') {
            $phoneNumber = PhoneNumber::find($request->id);

            if ($phoneNumber) {
                $existingRecord = DB::table('phone_verifications')
                    ->where('phone_number_id', $phoneNumber->id)
                    ->first();

                if ($existingRecord) {
                    $existingRecord = DB::table('phone_verifications')
                        ->where('phone_number_id', $phoneNumber->id)
                        ->delete();
                }

                $verificationCode = rand(1000, 9999);

                // Save verification code and its expiration time (30 minutes) to the database
                DB::table('phone_verifications')->insert([
                    'phone_number_id' => $phoneNumber->id,
                    'verification_code' => $verificationCode,
                    'expires_at' => Carbon::now()->addMinutes(30),
                ]);

                // Send the verification code via Twilio
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_PHONE_NUMBER');

                $twilio = new Client($sid, $token);
                $to = $phoneNumber->phone;
                $message = "Message from ProximaRide. Your verification code is: $verificationCode \n This code will expire in 30 minutes.";

                try {
                    if(env('APP_ENV') != 'local'){
                        $res = $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $message,
                            ]
                        );
                    }
                } catch (\Exception  $e) {
                    Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                    return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                }

                $data = ['phone_number' => $phoneNumber];
                return $this->successResponse($data, $messages->verfification_code_sent_message ?? 'Verification code sent');
            }

            return $this->apiErrorResponse($messages->general_error_message ?? "Phone number not found", 404);
        }

        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        // Normalize and validate phone number
        $normalizedPhone = normalizePhoneNumber($request->phone);

        if (!validatePhoneNumber($request->phone)) {
            return $this->apiErrorResponse('Please enter a valid phone number.', 422);
        }

        $request->validate([
            'phone' => 'unique:phone_numbers,phone,NULL,id,user_id,'.$user_id,
        ]);

        $existingPhone = PhoneNumber::where('phone', $normalizedPhone)->first();

        if ($existingPhone) {
            if ($existingPhone->user_id != $user_id) {
                $otherUser = \App\Models\User::find($existingPhone->user_id);
                if ($otherUser && ($otherUser->admin_deactive_account == 1 || $otherUser->suspand == 1)) {
                    // agar wo user deactivate ya suspend hai to block
                    return back()->withErrors(['phone' => $messages->suspended_account_phone_number_message ?? 'This phone number belongs to a suspended or deactivated account.'])->withInput();
                }
            } else {
                // same user apna number dobara add kar raha hai
                return back()->withErrors(['phone' => 'You have already added this phone number.'])->withInput();
            }
        }


        $phone_number = PhoneNumber::create([
            'user_id' => $user_id,
            'phone' => $normalizedPhone,
        ]);

        $emailData = [
            'first_name' => $user->first_name,
        ];
        if (isset($user->email_notification) && $user->email_notification == 1) {
            Mail::to($user->email)->send(new PhoneNumberAddedMail($emailData));
        }

        $verificationCode = rand(1000, 9999);

        // Save verification code and its expiration time (30 minutes) to the database
        DB::table('phone_verifications')->insert([
            'phone_number_id' => $phone_number->id,
            'verification_code' => $verificationCode,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        // Send the verification code via Twilio
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        $twilio = new Client($sid, $token);
        $to = $phone_number->phone;
        $message = "Message from ProximaRide. Your verification code is: $verificationCode \n This code will expire in 30 minutes.";

        try {
            if(env('APP_ENV') != 'local'){
                $res = $twilio->messages->create(
                    $to,
                    [
                        'from' => $from,
                        'body' => $message,
                    ]
                );
            }
        } catch (\Exception  $e) {
            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

            return $this->errorResponse('Can not send text to ' . $phone_number->phone . ' because unable to create record: Authenticate');
        }

        $data = ['phone_number' => $phone_number];
        return $this->successResponse($data, $messages->verfification_code_sent_message ?? 'Verification code sent');
    }

    public function verifyPhoneNumber(Request $request)
    {
        // Validate the form data
        $request->validate([
            'code' => 'required|max:4',
        ]);

        $message = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_verified_message', 'incorrect_code_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_verified_message', 'incorrect_code_message')->first();
            }
        }

        $existingRecord = DB::table('phone_verifications')
            ->where('verification_code', $request->code)
            ->first();

        if ($existingRecord) {
            $phone_number = PhoneNumber::whereId($existingRecord->phone_number_id)->first();
            $phone_number->update([
                'verified' => '1',
            ]);

            $user_id = Auth::guard('sanctum')->user()->id;
            User::whereId($user_id)->update([
                'phone_verified' => '1',
                'step' => '5'
            ]);

            DB::table('phone_verifications')->where('verification_code', $request->code)->delete();

            // Auto-mark as default if this is the first/only verified phone number
            $verifiedPhoneCount = PhoneNumber::where('user_id', $user_id)
                ->where('verified', '1')
                ->count();

            if ($verifiedPhoneCount === 1) {
                // This is the only verified phone number, make it default
                // First, remove default from any existing numbers
                PhoneNumber::where('user_id', $user_id)
                    ->update(['default' => '0']);
                // Then set this one as default
                $phone_number->update(['default' => '1']);
            }

            $phone_number = PhoneNumber::whereId($existingRecord->phone_number_id)->first();
            $data = ['phone_number' => $phone_number];

            return $this->successResponse($data, strip_tags($message->phone_verified_message));
        }

        return $this->apiErrorResponse(strip_tags($message->incorrect_code_message), 200);
    }

    public function setDefault(Request $request)
    {
        $phoneNumber = PhoneNumber::find($request->id);

        $selectedLanguage = app()->getLocale() ?? 'en';


        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();


        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_set_default_message', 'general_error_message')->first();

        if ($phoneNumber) {
            $phone_numbers = PhoneNumber::where('user_id', $phoneNumber->user_id)->get();
            foreach ($phone_numbers as $phone_number) {
                $phone_number->update([
                    'default' => 0,
                ]);
            }

            $phoneNumber->update([
                'default' => '1',
            ]);

            $data = ['phone_number' => $phoneNumber];
            return $this->successResponse($data, strip_tags($message->phone_set_default_message ?? 'Phone number set as default successfully'));
        }
        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? "Phone number not found"), 404);
    }

    public function destroy(Request $request)
    {
        $message = null;
        $phone_number = PhoneNumber::whereId($request->id)->first();

        if ($phone_number) {
            // Check if this was the primary number before deleting
            $wasDefault = $phone_number->default == '1';
            $user_id = $phone_number->user_id;

            $phone_number->delete();

            // If we deleted the primary number, set another verified number as primary
            if ($wasDefault) {
                $nextPrimary = PhoneNumber::where('user_id', $user_id)
                    ->where('verified', '1')
                    ->orderBy('id', 'asc')
                    ->first();

                if ($nextPrimary) {
                    $nextPrimary->update(['default' => '1']);
                }
            }

            $user = Auth::guard('sanctum')->user();

            $emailData = [
                'first_name' => $user->first_name,
            ];
            if (isset($user->email_notification) && $user->email_notification == 1) {
                Mail::to($user->email)->send(new PhoneNumberDeleted($emailData));
            }

            $notification = Notification::create([
                'type' => null,
                'category' => 'system',
                'receiver_id' => $user->id,
                'posted_by' => $user->id,
                'message' =>  'Phone number removed from your profile',
                'status' => 'phone',
                'notification_type' => 'phone',
            ]);

            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;
            $fcmService = new FCMService();

            if ($fcmToken) {
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }

            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                if ($selectedLanguage) {
                    // Retrieve the HomePageSettingDetail associated with the selected language
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_delete_message','general_error_message')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('phone_delete_message','general_error_message')->first();
                }
            }

            return $this->successResponse([], strip_tags($message->phone_delete_message));
        }
        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? 'Phone number not found'), 404);
    }
}
