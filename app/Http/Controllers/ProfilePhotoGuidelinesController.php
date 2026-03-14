<?php

namespace App\Http\Controllers;

use App\Models\ProfilePhotoGuidelinesPageSettingDetail;

class ProfilePhotoGuidelinesController extends Controller
{
    public function index($lang = null)
    {
        $profilePhotoGuidelinesPage = ProfilePhotoGuidelinesPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('proximaride_profile_photo_community_guidelines', [
            'profilePhotoGuidelinesPage' => $profilePhotoGuidelinesPage,
        ]);
    }
}
