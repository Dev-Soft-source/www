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
    public function index($lang = null)
    {

        $driverPage       = null;
        $videoDetails     = null;

        $driverPage = DriverPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $video = Video::where('page', 'For Drivers')->orderBy('id', 'desc')->first();
        if ($video) {
            $videoDetails = VideoDetail::getByLanguageWithFallback($video->id, $this->selectedLanguage->id, $this->defaultLang->id);
        }
        
        return view('drivers', [
            'driverPage' => $driverPage, 
            'video' => $videoDetails, 
            ]);
    }
}
