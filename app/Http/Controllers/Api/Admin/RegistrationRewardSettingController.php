<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RegistrationRewardSettingResource;
use App\Models\RegistrationRewardSetting;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class RegistrationRewardSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $registrationRewardSetting = RegistrationRewardSetting::query();
        $registrationRewardSetting = $registrationRewardSetting->first();

        return $this->successResponse($registrationRewardSetting ? new RegistrationRewardSettingResource($registrationRewardSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request, RegistrationRewardSetting $setting)
    {
        $rules = [
            'passenger_credit_booking' => ['required'],
            'driver_reward_point' => ['required'],
            'student_reward_point' => ['required'],
        ];
        $this->validate($request, $rules);

        $registrationRewardSetting = RegistrationRewardSetting::first();
        if (!$registrationRewardSetting) {
            $setting = RegistrationRewardSetting::create([
                'passenger_credit_booking' => $request->passenger_credit_booking,
                'driver_reward_point' => $request->driver_reward_point,
                'student_reward_point' => $request->student_reward_point,
            ]);
        }
        $result = RegistrationRewardSetting::whereId($request->id)->update([
            'passenger_credit_booking' => $request->passenger_credit_booking,
            'driver_reward_point' => $request->driver_reward_point,
            'student_reward_point' => $request->student_reward_point,
        ]);

        if ($result || $setting) {
            return $this->apiSuccessResponse(new RegistrationRewardSettingResource($setting), 'Settings have been updated successfully.');
        }
        return $this->errorResponse();
    }
}
