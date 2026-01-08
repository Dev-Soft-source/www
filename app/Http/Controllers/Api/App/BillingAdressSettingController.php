<?php

namespace App\Http\Controllers\Api\App;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Models\BillingAddressSettingDetail;

class BillingAdressSettingController extends Controller
{
    use StatusResponser;
    public function billingAddressSettingPage(Request $request)
    {
        $billingAddressSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $billingAddressSettingPage = BillingAddressSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $billingAddressSettingPage = BillingAddressSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $validationMessages = [
            'required' => trans('validation.required'),
            'regex' => trans('validation.regex'),
        ];

        $data = ['billingAddressSettingPage' => $billingAddressSettingPage, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Search my billingAddress page get successfully');
    }
}
