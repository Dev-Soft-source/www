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
use App\Models\CoffeeWallPageSettingDetail;

use App\Traits\StatusResponser;

class ProfilePageSettingController extends Controller
{
    use StatusResponser;
    public function findProfilePageSettingIndex(Request $request)
    {

        $myProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        
        $logoutPage = LogoutSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $termsAndConditionHeading = TermsAndConditionPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $privacyPolicyHeading = PrivacyPolicyPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $termsofuseHeading  = TermsOfUsePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $refundPolicyHeading = RefundPolicyPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $cancellationPolicyHeading = CancellationPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $disputePolicyHeading = DisputePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $coffeeOnWallHeading = CoffeeWallPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $data = [
            'myProfilePage' => $myProfilePage,
            'logoutPage' => $logoutPage,
            "termsAndConditionHeading" => $termsAndConditionHeading,
            "privacyPolicyHeading" => $privacyPolicyHeading,
            "termsofuseHeading" => $termsofuseHeading,
            "refundPolicyHeading" => $refundPolicyHeading,
            "cancellationPolicyHeading" => $cancellationPolicyHeading,
            "disputePolicyHeading" => $disputePolicyHeading,
            "coffeeOnWallHeading" => $coffeeOnWallHeading
        ];
        return $this->successResponse($data, 'Profile page setting get successfully');
    }
}
