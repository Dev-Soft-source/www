<?php

namespace App\Http\Controllers;

use App\Models\ChatsPageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\StudentPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Video;
use App\Models\VideoDetail;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index($lang = null){

        $studentPage = StudentPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        
        $video = Video::where('page','For Students')->orderBy('id', 'desc')->first();
        if ($video) {
            $videoDetails = VideoDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id, ['video_id' => $video->id]);
        }

        return view('students',[
            'studentPage' => $studentPage,
            'video' => $videoDetails]);
    }
}
