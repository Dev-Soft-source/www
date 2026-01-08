<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CancelRideSettingResource;
use App\Models\CancelRideSetting;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class CancelRideSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $cancelRideSetting = CancelRideSetting::query();
        $cancelRideSetting = $cancelRideSetting->first();

        return $this->successResponse($cancelRideSetting ? new CancelRideSettingResource($cancelRideSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request, CancelRideSetting $setting)
    {
        $rules = [
            'driver_cancel_hours' => ['required'],
            'passenger_cancel_hours' => ['required'],
        ];
        $this->validate($request, $rules);

        $cancelRideSetting = CancelRideSetting::first();
        if (!$cancelRideSetting) {
            $setting = CancelRideSetting::create([
                'id' => '1',
                'driver_cancel_hours' => $request->driver_cancel_hours,
                'passenger_cancel_hours' => $request->passenger_cancel_hours,
            ]);
        }
        $result = CancelRideSetting::whereId($request->id)->update([
            'driver_cancel_hours' => $request->driver_cancel_hours,
            'passenger_cancel_hours' => $request->passenger_cancel_hours,
        ]);

        if ($result || $setting) {
            return $this->apiSuccessResponse(new CancelRideSettingResource($setting), 'Settings have been updated successfully.');
        }
        return $this->errorResponse();
    }
}
