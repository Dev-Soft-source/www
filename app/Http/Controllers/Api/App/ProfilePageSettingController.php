<?php

namespace App\Http\Controllers\Api\App;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProfilePageSettingDetail;
use App\Models\LogoutSettingDetail;
use App\Models\TermsAndConditionPageSettingDetail;
use App\Models\TermsOfUsePageSettingDetail;
use App\Models\RefundPolicyPageSettingDetail;
use App\Models\PrivacyPolicyPageSettingDetail;
use App\Models\CancellationPageSettingDetail;
use App\Models\DisputePageSettingDetail;

use App\Traits\StatusResponser;

class ProfilePageSettingController extends Controller
{
    use StatusResponser;
    public function findProfilePageSettingIndex(Request $request)
    {
        $myProfilePage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $myProfilePage = ProfilePageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $myProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $logoutPage = null;
        $termsAndConditionHeading = $privacyPolicyHeading = $termsofuseHeading  = $refundPolicyHeading = $cancellationPolicyHeading = $disputePolicyHeading = ""; 
        if ($request->lang_id && $request->lang_id != 0) {
            $logoutPage = LogoutSettingDetail::where('language_id', $request->lang_id)->first();
            $termsAndConditionHeading = TermsAndConditionPageSettingDetail::where('language_id', $request->lang_id)->value('main_heading');

            $privacyPolicyHeading = PrivacyPolicyPageSettingDetail::where('language_id', $request->lang_id)->value('main_heading');

            $termsofuseHeading  = TermsOfUsePageSettingDetail::where('language_id', $request->lang_id)->value('main_heading'); 
            $refundPolicyHeading = RefundPolicyPageSettingDetail::where('language_id', $request->lang_id)->value('main_heading');
            $cancellationPolicyHeading = CancellationPageSettingDetail::where('language_id', $request->lang_id)->value('main_heading');
            $disputePolicyHeading = DisputePageSettingDetail::where('language_id', $request->lang_id)->value('main_heading');;
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $logoutPage = LogoutSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $termsAndConditionHeading = TermsAndConditionPageSettingDetail::where('language_id', $selectedLanguage->id)->value('main_heading');
                $privacyPolicyHeading = PrivacyPolicyPageSettingDetail::where('language_id', $selectedLanguage->id)->value('main_heading');
                $termsofuseHeading  = TermsOfUsePageSettingDetail::where('language_id', $selectedLanguage->id)->value('main_heading'); 
                $refundPolicyHeading = RefundPolicyPageSettingDetail::where('language_id', $selectedLanguage->id)->value('main_heading');
                $cancellationPolicyHeading = CancellationPageSettingDetail::where('language_id', $selectedLanguage->id)->value('main_heading');
                $disputePolicyHeading = DisputePageSettingDetail::where('language_id', $selectedLanguage->id)->value('main_heading');
            }
        }


        $data = ['myProfilePage' => $myProfilePage, 'logoutPage' => $logoutPage, "termsAndConditionHeading" => $termsAndConditionHeading, "privacyPolicyHeading" => $privacyPolicyHeading, "termsofuseHeading" => $termsofuseHeading, "refundPolicyHeading" => $refundPolicyHeading, "cancellationPolicyHeading" => $cancellationPolicyHeading, "disputePolicyHeading" => $disputePolicyHeading];
        return $this->successResponse($data, 'Profile page setting get successfully');
    }
}
