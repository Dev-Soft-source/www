<?php

namespace App\Http\Controllers;

use App\Models\CommunityGuidelinesPageSettingDetail;

class CommunityGuidelinesController extends Controller
{
    public function index($lang = null)
    {
        $communityGuidelinesPage = CommunityGuidelinesPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('community_guidelines', [
            'communityGuidelinesPage' => $communityGuidelinesPage,
        ]);
    }
}
