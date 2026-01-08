<?php

namespace App\Http\Controllers;

use App\Models\ChatsPageSettingDetail;
use App\Models\DriverPageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Video;
use App\Models\VideoDetail;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index($lang = null){
        $video = Video::where('page','For Drivers')->orderBy('id', 'desc')->first();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $videoDetails = null;
        $driverPage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
    
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();

                // Retrieve the HomePageSettingDetail associated with the selected language
                $driverPage = DriverPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                // Fetch video_details for the Introduction Video using the video's ID.
                if($video){
                    $videoDetails = VideoDetail::where('video_id', $video->id)
                        ->where('language_id', $selectedLanguage->id)
                        ->first();
                }
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
                $driverPage = DriverPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if($video){
                    $videoDetails = VideoDetail::where('video_id', $video->id)
                        ->where('language_id', $selectedLanguage->id)->first();
                }
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
        return view('drivers',['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'driverPage' => $driverPage,'video' => $videoDetails,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }
}
