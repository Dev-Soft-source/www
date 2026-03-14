<?php

namespace App\Http\Controllers;

use App\Models\ChatsPageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\PrivacyPolicyPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    public function index($lang = null)
    {
        $privacyPolicyPage = PrivacyPolicyPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('privacy_policy', [
            'privacyPolicyPage' => $privacyPolicyPage
        ]);
    }
}
