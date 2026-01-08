<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PinkRideSettingResource;
use App\Models\PinkRideSetting;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class PinkRideSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $pinkRideSetting = PinkRideSetting::query();
        $pinkRideSetting = $pinkRideSetting->first();

        return $this->successResponse($pinkRideSetting ? new PinkRideSettingResource($pinkRideSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request, PinkRideSetting $setting)
    {
        $verfiy_phone_passenger = $request->filled('verfiy_phone_passenger') ? $request->verfiy_phone_passenger : 0;
        $verfiy_phone = $request->filled('verfiy_phone') ? $request->verfiy_phone : 0;
        $verify_email = $request->filled('verify_email') ? $request->verify_email : 0;
        $driver_license = $request->filled('driver_license') ? $request->driver_license : 0;
        $profile_complete = $request->filled('profile_complete') ? $request->profile_complete : 0;

        $pinkRideSetting = PinkRideSetting::first();
        if (!$pinkRideSetting) {
            $setting = PinkRideSetting::create([
                'id' => '1',
                'verfiy_phone_passenger' => $verfiy_phone_passenger,
                'verfiy_phone' => $verfiy_phone,
                'verify_email' => $verify_email,
                'driver_license' => $driver_license,
                'profile_complete' => $profile_complete,
            ]);
        }
        $result = PinkRideSetting::whereId($request->id)->update([
            'verfiy_phone_passenger' => $verfiy_phone_passenger,
            'verfiy_phone' => $verfiy_phone,
            'verify_email' => $verify_email,
            'driver_license' => $driver_license,
            'profile_complete' => $profile_complete,
        ]);

        if ($result || $setting) {
            return $this->apiSuccessResponse(new PinkRideSettingResource($setting), 'Settings have been updated successfully.');
        }
        return $this->errorResponse();
    }
}
