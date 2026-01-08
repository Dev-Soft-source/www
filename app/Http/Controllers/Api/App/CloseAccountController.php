<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\AccountClosedUserMail;
use App\Mail\ClosedAccountMail;
use App\Models\Admin;
use App\Models\CloseAccountSubmission;
use App\Models\Language;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;

class CloseAccountController extends Controller
{
    use StatusResponser;

    public function update(Request $request){

        $selectedLanguage = app()->getLocale() ?? 'en';
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('closed_account_success_message','general_error_message')->first();

        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        
        // $request->validate([
        //     'reasons' => 'array|required',
        //     'close_account' => 'required'
        // ]);

        // Join the selected checkboxes with semicolons.
        $reasons = implode(';', $request->input('reasons', []));

        $submission = CloseAccountSubmission::create([
            'name' => $user->first_name . ' ' . $user->last_name,
            'reasons' => $reasons,
            'recommend' => $request->recommend,
            'improve_message' => $request->improve_message,
            'close_account_reason' => $request->close_account_reason,
        ]);

        $admin = Admin::first();
        $data = ['username' => $admin->username,'name' => $submission->name];
        // Send email to admin
        Mail::to($admin->admin_email)->queue(new ClosedAccountMail($data));

        $userData = ['first_name' => $user->first_name];
        Mail::to($user->email)->queue(new AccountClosedUserMail($userData));

        $user = User::whereId($user_id)->first();
        $user->update([
            'closed' => '1',
        ]);

        return $this->successResponse('', strip_tags($message->closed_account_success_message ??'You have successfully closed your account'));
    }
}
