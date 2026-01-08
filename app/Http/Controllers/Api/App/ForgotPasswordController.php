<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\UserForgotPassword;
use App\Models\ForgotPasswordPageSettingDetail;
use App\Models\Language;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    use StatusResponser;

    public function create(Request $request){
        $forgotPasswordPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $selectedLanguage = Language::whereId($request->lang_id)->first();
            // Retrieve the ForgotPasswordPageSettingDetail associated with the selected language
            $forgotPasswordPage = ForgotPasswordPageSettingDetail::where('language_id', $request->lang_id)->select('main_heading', 'main_label', 'button_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $forgotPasswordPage = ForgotPasswordPageSettingDetail::where('language_id', $selectedLanguage->id)->select('main_heading', 'main_label', 'button_label')->first();
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
            'email' => trans('validation.email'),
        ];

        $data = ['forgotPasswordPage' => $forgotPasswordPage, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Forgot password page get successfully');
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        $message = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $selectedLanguage = Language::whereId($request->lang_id)->first();
            // Retrieve the SuccessMessagesSettingDetail associated with the selected language
            $message = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('no_user_found_message', 'email_not_verify_message', 'account_closed_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('no_user_found_message', 'email_not_verify_message','account_closed_message')->first();
            }
        }

        if(!$user){
            return $this->apiErrorResponse(strip_tags($message->no_user_found_message), 200);
        }

        if($user->email_verified == 0) {
            return $this->apiErrorResponse(strip_tags($message->email_not_verify_message ?? null), 200);
        }

        if($user->closed == '1') {
            return $this->apiErrorResponse(strip_tags($message->account_closed_message ?? 'Account is closed'), 200);
        }

        $token = Str::random(64);

        $existingRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('type', 'user')
            ->first();

        if ($existingRecord) {
            // If a record with the same email and type exists, delete it
            DB::table('password_resets')
                ->where('email', $request->email)
                ->where('type', 'user')
                ->delete();
        }

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'type' => 'user',
            'created_at' => Carbon::now()
        ]);
        
        $data = ['token' => $token, 'first_name' => $user->first_name,'lang' => $selectedLanguage->abbreviation, 'is_app' => true];

        // Send reset password mail
        Mail::to($request->email)->queue(new UserForgotPassword($data));

        return $this->successResponse($data, strip_tags($message->reset_password_message ?? null));
    }
}
