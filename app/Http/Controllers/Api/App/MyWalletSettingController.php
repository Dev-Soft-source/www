<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\MyWalletSettingDetail;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;

class MyWalletSettingController extends Controller
{
    use StatusResponser;
    public function WalletSettingIndex(Request $request)
    {

        $walletSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $walletSettingPage = MyWalletSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['walletSettingPage' => $walletSettingPage];
        return $this->successResponse($data, 'Search my wallet page get successfully');
    }
}
