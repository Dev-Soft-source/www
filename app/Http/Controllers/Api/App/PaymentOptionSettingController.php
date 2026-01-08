<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\PaymentSettingDetail;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;

class PaymentOptionSettingController extends Controller
{
    use StatusResponser;
    public function PaymentPageSettingIndex(Request $request)
    {
        $paymentOptionPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $paymentOptionPage = PaymentSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $paymentOptionPage = PaymentSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['paymentOptionPage' => $paymentOptionPage];
        return $this->successResponse($data, 'Search payment option page get successfully');
    }
}
