<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\CancellationHistory;
use App\Models\FolkRideSetting;
use App\Models\Language;
use App\Models\NoShowHistory;
use App\Models\PhoneNumber;
use App\Models\PinkRideSetting;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\SelectLocationSettingDetail;
use App\Models\SiteSetting;
use App\Models\Step1PageSettingDetail;
use App\Models\User;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfoIconController extends Controller
{
    use StatusResponser;

    public function pinkRideInfo()
    {
        $pinkRideSetting = PinkRideSetting::getCached();

        $loggedInUser = Auth::guard('sanctum')->user();
        $user = User::whereId($loggedInUser->id)->select('id', 'gender', 'email_verified', 'driver', 'dob', 'profile_complete', 'pink_ride', 'folks_ride')->first();

        $data = ['pinkRideSetting' => $pinkRideSetting, 'user' => $user];
        return $this->successResponse($data, 'Get pink ride settings successfully');
    }

    public function extraCareRideInfo()
    {
        $folkRideSetting = FolkRideSetting::getCached();


        $loggedInUser = Auth::guard('sanctum')->user();
        $user = User::whereId($loggedInUser->id)->select('id', 'gender', 'email_verified', 'driver', 'dob', 'profile_complete', 'pink_ride', 'folks_ride')->first();

        $data = ['folkRideSetting' => $folkRideSetting, 'user' => $user];
        return $this->successResponse($data, 'Get extra care ride settings successfully');
    }


    public function selectLocationSetting()
    {
       
        $selectLocationSetting = SelectLocationSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $data = ['selectLocationSetting' => $selectLocationSetting];
        return $this->successResponse($data, 'Get select location settings successfully');
    }
}
