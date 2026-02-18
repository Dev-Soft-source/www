<?php

namespace App\Http\Controllers;

use App\Models\ChatsPageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\TermsOfUsePageSettingDetail;
use Illuminate\Http\Request;

class TermsOfUseController extends Controller
{
    public function index($lang = null){
        
        $termsOfUsePage = TermsOfUsePageSettingDetail::where('language_id', $this->selectedLanguage->id)->first();
                
        return view('terms_of_use',['termsOfUsePage' => $termsOfUsePage]);
    }
}
