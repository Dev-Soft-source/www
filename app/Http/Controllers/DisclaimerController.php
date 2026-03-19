<?php

namespace App\Http\Controllers;

use App\Models\DisclaimerPageSettingDetail;

class DisclaimerController extends Controller
{
    public function index($lang = null)
    {
        $disclaimerPage = DisclaimerPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('disclaimer', [
            'disclaimerPage' => $disclaimerPage,
        ]);
    }
}
