<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ReferralSystemSettingResource;
use App\Models\ReferralSystemSetting;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class ReferralSystemSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $referralSystemSetting = ReferralSystemSetting::query();
        $referralSystemSetting = $referralSystemSetting->first();

        return $this->successResponse($referralSystemSetting ? new ReferralSystemSettingResource($referralSystemSetting) : [], 'Data Get Successfully!');
    }
 
    public function update(Request $request, ReferralSystemSetting $setting)
    {
        $rules = [
            'p_2_p_booking_credit' => ['required'],
            'p_2_s_booking_credit' => ['required'],
            'p_2_d_booking_credit' => ['required'],
            'd_2_p_reward_point' => ['required'],
            'd_2_s_reward_point' => ['required'],
            'd_2_d_rewad_point' => ['required'],
            's_2_p_reward_point' => ['required'],
            's_2_s_reward_point' => ['required'],
            's_2_d_reward_point' => ['required'],
        ];
        $this->validate($request, $rules);

        $referralSystemSetting = ReferralSystemSetting::first();
        if (!$referralSystemSetting) {
            $setting = ReferralSystemSetting::create([
                'id' => '1',
                'p_2_p_booking_credit' => $request->p_2_p_booking_credit,
                'p_2_s_booking_credit' => $request->p_2_s_booking_credit,
                'p_2_d_booking_credit' => $request->p_2_d_booking_credit,
                'd_2_p_reward_point' => $request->d_2_p_reward_point,
                'd_2_s_reward_point' => $request->d_2_s_reward_point,
                'd_2_d_rewad_point' => $request->d_2_d_rewad_point,
                's_2_p_reward_point' => $request->s_2_p_reward_point,
                's_2_s_reward_point' => $request->s_2_s_reward_point,
                's_2_d_reward_point' => $request->s_2_d_reward_point,
            ]);
        }
        $result = ReferralSystemSetting::whereId($request->id)->update([
            'p_2_p_booking_credit' => $request->p_2_p_booking_credit,
            'p_2_s_booking_credit' => $request->p_2_s_booking_credit,
            'p_2_d_booking_credit' => $request->p_2_d_booking_credit,
            'd_2_p_reward_point' => $request->d_2_p_reward_point,
            'd_2_s_reward_point' => $request->d_2_s_reward_point,
            'd_2_d_rewad_point' => $request->d_2_d_rewad_point,
            's_2_p_reward_point' => $request->s_2_p_reward_point,
            's_2_s_reward_point' => $request->s_2_s_reward_point,
            's_2_d_reward_point' => $request->s_2_d_reward_point,
        ]);

        if ($result || $setting) {
            return $this->apiSuccessResponse(new ReferralSystemSettingResource($setting), 'Settings have been updated successfully.');
        }
        return $this->errorResponse();
    }
}
