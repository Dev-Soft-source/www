<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\UserEmailVerification;
use App\Mail\AdminNewUserSignupMail;
use App\Models\Language;
use App\Models\SignupPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SignupController extends Controller
{
    use StatusResponser;

    public function create(Request $request)
    {
        $signupPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $selectedLanguage = Language::whereId($request->lang_id)->first();
            // Retrieve the SignupPageSettingDetail associated with the selected language
            $signupPage = SignupPageSettingDetail::where('language_id', $request->lang_id)->select('app_main_heading', 'or_label', 'required_label', 'first_name_label', 'last_name_label', 'email_label', 'password_label', 'password_placeholder', 'confirm_password_label', 'app_agree_terms_part1_label', 'app_agree_terms_link1_label', 'app_agree_terms_link2_label', 'app_agree_terms_part2_label', 'app_agree_terms_link3_label', 'app_agree_terms_part3_label', 'button_label', 'no_account_label', 'signin_link_label', 'now_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $signupPage = SignupPageSettingDetail::where('language_id', $selectedLanguage->id)->select('app_main_heading', 'or_label', 'required_label', 'first_name_label', 'last_name_label', 'email_label', 'password_label', 'password_placeholder', 'confirm_password_label', 'app_agree_terms_part1_label', 'app_agree_terms_link1_label', 'app_agree_terms_link2_label', 'app_agree_terms_part2_label', 'app_agree_terms_link3_label', 'app_agree_terms_part3_label', 'button_label', 'no_account_label', 'signin_link_label', 'now_label')->first();
            }
        }

        if ($selectedLanguage) {
            $locale = $selectedLanguage->abbreviation;
        } else {
            $locale = 'en';
        }

        App::setLocale($locale);

        $validationMessages = [
            'required' => trans('validation.required'),
            'string' => trans('validation.string'),
            'max' => trans('validation.max.string'),
            'email' => trans('validation.email'),
            'confirmed' => trans('validation.confirmed'),
            'min' => trans('validation.min.string'),
            'regex' => trans('validation.regex'),
        ];

        $data = ['signupPage' => $signupPage, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Signup page get successfully');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-]+$/',
                'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-]+$/',
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => 'required|confirmed|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).+$/',
                'remember-me' => 'required',
                'rideshare_disclaimer' => 'required|accepted',
            ]);

            $token = Str::random(64);

            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'type' => 'verify_email',
                'created_at' => Carbon::now(),
            ]);

            $defaultLangId = 0;
            if(isset($request->lang_id)){
                $defaultLangId = $request->lang_id;
            }else{
                $defaultLangId = Language::where('is_default', '1')->value('id');
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'lang_id' => $defaultLangId,
                'referral_uuid' => bin2hex(random_bytes(16))
            ]);

            $ipAddress = null;
            foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
                if (array_key_exists($key, $_SERVER) === true) {
                    foreach (explode(',', $_SERVER[$key]) as $ip) {
                        $ip = trim($ip); // just to be safe
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                            $ipAddress = $ip;
                            break 2;
                        }
                    }
                }
            }
            $ipAddress = $ipAddress ?? 'UNKNOWN';

            DB::table('user_details')->insert([
                'ip_address' => $ipAddress,
                'type' => 'app',
                'user_id' => $user->id,
                'created_at' => Carbon::now()
            ]);

            $data = ['first_name' => $request->first_name, 'email' => $request->email, 'token' => $token, 'app' => true];

            // Send email verification
            Mail::to($request->email)->queue(new UserEmailVerification($data));

            // Send admin notification about new user signup
            $adminData = [
                'user_name' => $request->first_name . ' ' . $request->last_name,
                'user_email' => $request->email,
                'registration_date' => Carbon::now()->format('M d, Y H:i:s'),
                'platform' => 'Mobile App',
                'ip_address' => $ipAddress
            ];
            Mail::to('ccaned@gmail.com')->queue(new AdminNewUserSignupMail($adminData));

            $message = null;
            if ($request->lang_id && $request->lang_id != 0) {
                // Retrieve the SuccessMessagesSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('welcome_message', 'email_sent_message')->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('welcome_message', 'email_sent_message')->first();
                }
            }

            $data = ['user' => $user];
            $successMessage = $message->welcome_message . " " . $user->first_name . ", " . $message->email_sent_message;
            return $this->successResponse($data, $successMessage);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();

            // Check if there's a password confirmation error
            if (isset($errors['password'])) {
                foreach ($errors['password'] as $key => $error) {
                    if ($error === 'Password does not match') {
                        $errors['password_confirmation'][] = $error;
                        unset($errors['password'][$key]);
                    }
                }

                // If password array is empty, remove it
                if (empty($errors['password'])) {
                    unset($errors['password']);
                }
            }

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $errors,
            ], 422);
        }
    }
}
