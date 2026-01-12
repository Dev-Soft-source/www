<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\AdminReceiveContactMessageMail;
use App\Mail\ContactMessageSentMail;
use App\Models\Admin;
use App\Models\ContactMessage;
use App\Models\Language;
use App\Models\SuccessMessagesSettingDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    use StatusResponser;

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:25',
            'email' => 'required|email',
            'phone' => 'nullable',
            'message' => 'required|string|max:300',
        ]);

        $message = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->message, // App sends phone in message field
            'message' => $request->phone, // App sends message in phone field
        ]);

        $admin = Admin::first();
        $data = ['username' => $admin->username,'name' => $message->name,'email' => $message->email,'phone' => $message->phone,'message' => $message->message];
        // Send email to admin
        Mail::to($admin->admin_email)->queue(new AdminReceiveContactMessageMail($data));

        $data = ['name' => $message->name,'email' => $message->email,'phone' => $message->phone,'message' => $message->message];
        // Send email to user
        Mail::to($message->email)->queue(new ContactMessageSentMail($data));

        $selectedLanguage = app()->getLocale();
        $messages = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('contact_form_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('contact_form_message')->first();
            }
        }

        $data = ['form_submission' => $message];
        return $this->successResponse($data, $messages->contact_form_message ?? null);
    }
}
