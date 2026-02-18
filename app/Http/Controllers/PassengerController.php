<?php

namespace App\Http\Controllers;

use App\Models\ChatsPageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\PassengerPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Video;
use App\Models\VideoDetail;
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    public function index($lang = null)
    {

        $passengerPage = PassengerPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $video = Video::where('page', 'For Passengers')->orderBy('id', 'desc')->first();
        if ($video) {
            $videoDetails = VideoDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id, ['video_id' => $video->id]);
        }

        return view('passengers', [
            'passengerPage' => $passengerPage,
            'video' => $videoDetails,
        ]);
    }
}
