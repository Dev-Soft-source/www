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
    public function index($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $contactUsPage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
    
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
                // Retrieve the HomePageSettingDetail associated with the selected language
                $contactUsPage = ContactUsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $contactProximaPage = ContactProximaRideSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
                $contactUsPage = ContactUsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $contactProximaPage = ContactProximaRideSettingDetail::where('language_id', $selectedLanguage->id)->first();

            }
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0');
            $notifications = $notifications->where(function ($query) use ($user_id) {
                $query->where('type', '1')->whereHas('ride', function ($query) use ($user_id) {
                    $query->where('added_by', $user_id);
                })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '2')->whereHas('booking', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });
                })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', null)->whereHas('receiver', function ($query) use ($user_id) {
                        $query->where('id', $user_id);
                    });
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        }
        return view('contact_us',['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'contactUsPage' => $contactUsPage,'contactProximaPage' => $contactProximaPage,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function store(Request $request){
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
        $data = ['username' => $admin->username,'name' => $message->name,'email' => $message->email,'phone' => $message->phone,'message' => $message->message];
        // Send email to admin
        Mail::to($admin->admin_email)->queue(new AdminReceiveContactMessageMail($data));

        $data = ['name' => $message->name,'email' => $message->email,'phone' => $message->phone,'message' => $message->message];
        // Send email to user
        Mail::to($message->email)->queue(new ContactMessageSentMail($data));

        return back()->with(['success' => 'Your message has been sent to the admin successfully']);
    }
}
