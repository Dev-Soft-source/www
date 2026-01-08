<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\PayoutOptionSettingDetail;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;

class PayoutOptionSettingController extends Controller
{
    use StatusResponser;
    public function PayoutPageSettingIndex(Request $request)
    {
        $payoutOptionPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $payoutOptionPage = PayoutOptionSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $payoutOptionPage = PayoutOptionSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['payoutOptionPage' => $payoutOptionPage];
        return $this->successResponse($data, 'Search payout option page get successfully');
    }
}
