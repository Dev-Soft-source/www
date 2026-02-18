<?php

namespace App\Http\Controllers;

use App\Models\ChatsPageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\TermsAndConditionPageSettingDetail;
use Illuminate\Http\Request;

class TermsAndConditionsController extends Controller
{
    public function index($lang = null){
        
        $termsAndConditionPage = TermsAndConditionPageSettingDetail::where('language_id', $this->selectedLanguage->id)->first();

        return view('terms_and_conditions',['termsAndConditionPage' => $termsAndConditionPage]);
    }
}
