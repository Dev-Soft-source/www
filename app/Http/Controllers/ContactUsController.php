<?php

namespace App\Http\Controllers;

use App\Mail\AdminReceiveContactMessageMail;
use App\Mail\ContactMessageSentMail;
use App\Models\Admin;
use App\Models\ChatsPageSettingDetail;
use App\Models\ContactMessage;
use App\Models\ContactUsPageSettingDetail;
use App\Models\ContactProximaRideSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function index($lang = null)
    {
        $contactUsPage = ContactUsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $contactProximaPage = ContactProximaRideSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('contact_us', [
            'contactUsPage' => $contactUsPage,
            'contactProximaPage' => $contactProximaPage,
        ]);
    }

    public function store(Request $request)
    {
        $customMessages = [
            'string' => 'The :attribute must be a string',
            'max' => 'The :attribute may not be greater than :max characters',
            'email' => 'This must be a valid email',
        ];

        $request->validate([
            'name' => 'required|string|max:25',
            'email' => 'required|email',
            'phone' => 'nullable|regex:/^[0-9\-\(\)\s]{1,15}$/',
            'message' => 'required|string|max:300',
            'g-recaptcha-response' => 'required|recaptchav3:register,0.5',

        ], $customMessages);

        $message = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
        ]);

        $admin = Admin::first();
        $data = [
            'username' => $admin->username,
            'name' => $message->name,
            'email' => $message->email,
            'phone' => $message->phone,
            'message' => $message->message,
            'transaction_date' => $message->created_at->format('M d, Y H:i:s'),
        ];
        // Send email to admin
        Mail::to($admin->admin_email)->queue(new AdminReceiveContactMessageMail($data));

        $data = ['name' => $message->name, 'email' => $message->email, 'phone' => $message->phone, 'message' => $message->message];
        // Send email to user
        Mail::to($message->email)->queue(new ContactMessageSentMail($data));

        return back()->with(['success' => 'Your message has been sent to the admin successfully']);
    }
}
