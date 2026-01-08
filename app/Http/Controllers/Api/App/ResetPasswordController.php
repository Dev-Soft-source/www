<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangedMail;
use App\Models\Language;
use App\Models\MobileResetPasswordSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    use StatusResponser;

    public function create(Request $request){
        $resetPasswordPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            // Retrieve the ResetPasswordPageSettingDetail associated with the selected language
            $resetPasswordPage = MobileResetPasswordSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $resetPasswordPage = MobileResetPasswordSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['resetPasswordPage' => $resetPasswordPage];
        return $this->successResponse($data, 'Reset password page get successfully');
    }

    public function store(Request $request){
        // Validate the form data
        $validatedData = $request->validate([
            'password' => 'required|confirmed|string|min:8',
        ]);

        $selectedLanguage = app()->getLocale() ?? 'en';

        
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('password_token_invalid_message', 'general_error_message','email_not_found_message', 'password_reset_success_message')->first();

        $password_resets = DB::table('password_resets')->where('token', $request->token)->where('type', 'user')->first();
        if (!$password_resets) {
            return $this->apiErrorResponse(strip_tags($message->password_token_invalid_message ?? 'This password reset token is invalid'), 200);
        }

        $user = User::where('email', $password_resets->email)->first();
        if (!$user) {
            return $this->apiErrorResponse(strip_tags($message->email_not_found_message ?? "We can't find a user with that email address"), 200);
        }
        if ($user) {
            $userUpdate = $user->update([
                'password' => Hash::make($request->password)
            ]);
            if ($userUpdate) {
                Mail::to($user->email)->queue(new PasswordChangedMail([
                    'first_name' => $user->first_name
                ]));

                DB::table('password_resets')->where('token', $request->token)->delete();
                return $this->successResponse('', strip_tags($message->password_reset_success_message ?? 'Your password has been reset successfully'));
            }
        }
    }
}
