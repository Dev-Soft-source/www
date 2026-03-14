<?php

namespace App\Http\Controllers;

use App\Models\CostSharingPageSettingDetail;

class CostSharingPolicyController extends Controller
{
    public function index($lang = null)
    {
        $costSharingPage = CostSharingPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('cost_sharing_policy', [
            'costSharingPage' => $costSharingPage,
        ]);
    }
}
