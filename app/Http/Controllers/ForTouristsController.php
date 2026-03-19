<?php

namespace App\Http\Controllers;

use App\Models\ForTouristsPageSettingDetail;

class ForTouristsController extends Controller
{
    public function index($lang = null)
    {
        $forTouristsPage = ForTouristsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('for_tourists', [
            'forTouristsPage' => $forTouristsPage,
        ]);
    }
}
