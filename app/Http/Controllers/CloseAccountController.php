<?php

namespace App\Http\Controllers;

use App\Mail\AccountClosedUserMail;
use App\Mail\ClosedAccountMail;
use App\Models\Admin;
use App\Models\CloseAccountSubmission;
use App\Models\CloseAccountSettingDetail;
use App\Models\Language;
use App\Models\MyReviewSettingDetail;
use App\Models\Notification;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class CloseAccountController extends Controller
{
    public function index($lang = null){
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $closeAccountPage= null;

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $closeAccountPage = CloseAccountSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $closeAccountPage = CloseAccountSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        }

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
        return view('close_account',['reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'user' => $user,'languages' => $languages,'notifications' => $notifications,'selectedLanguage' => $selectedLanguage, 'closeAccountPage' => $closeAccountPage]);
    }

    public function update(Request $request){
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();

        $niceNames = [];
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('closed_account_success_message')->first();
            $closeAccountPage = CloseAccountSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'close_account' => isset($closeAccountPage->close_my_account_checkbox_error) ? $closeAccountPage->close_my_account_checkbox_error : '',
            ];
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('closed_account_success_message')->first();
            $closeAccountPage = CloseAccountSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'close_account' => isset($closeAccountPage->close_my_account_checkbox_error) ? $closeAccountPage->close_my_account_checkbox_error : '',
            ];
        }
        
        $request->validate([
            'reasons' => 'array|required',
            'close_account' => 'required'
        ], [], $niceNames);

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

        $user->update([
            'closed' => '1',
        ]);
        // $user->delete();

        // Log the user out and invalidate the session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $message->closed_account_success_message ?? 'You have successfully closed your account']);
    }
}
