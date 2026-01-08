<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\FolkRideSettingResource;
use App\Models\FolkRideSetting;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class FolkRideSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $folkRideSetting = FolkRideSetting::query();
        $folkRideSetting = $folkRideSetting->first();

        return $this->successResponse($folkRideSetting ? new FolkRideSettingResource($folkRideSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request, FolkRideSetting $setting)
    {
        $rules = [
            'average_rating' => ['required'],
            'driver_age' => ['required'],
            'extra_rides_trip_limit' => ['required'],
        ];
        $this->validate($request, $rules);

        $verfiy_phone = $request->filled('verfiy_phone') ? $request->verfiy_phone : 0;
        $verify_email = $request->filled('verify_email') ? $request->verify_email : 0;
        $driver_license = $request->filled('driver_license') ? $request->driver_license : 0;
        $profile_complete = $request->filled('profile_complete') ? $request->profile_complete : 0;

        $folkRideSetting = FolkRideSetting::first();
        if (!$folkRideSetting) {
            $setting = FolkRideSetting::create([
                'id' => '1',
                'average_rating' => $request->average_rating,
                'driver_age' => $request->driver_age,
                'extra_rides_trip_limit' => $request->extra_rides_trip_limit,
                'verfiy_phone' => $verfiy_phone,
                'verify_email' => $verify_email,
                'driver_license' => $driver_license,
                'profile_complete' => $profile_complete,
            ]);
        }
        $result = FolkRideSetting::whereId($request->id)->update([
            'average_rating' => $request->average_rating,
            'driver_age' => $request->driver_age,
            'extra_rides_trip_limit' => $request->extra_rides_trip_limit,
            'verfiy_phone' => $verfiy_phone,
            'verify_email' => $verify_email,
            'driver_license' => $driver_license,
            'profile_complete' => $profile_complete,
        ]);

        if ($result || $setting) {
            return $this->apiSuccessResponse(new FolkRideSettingResource($setting), 'Settings have been updated successfully.');
        }
        return $this->errorResponse();
    }
}
