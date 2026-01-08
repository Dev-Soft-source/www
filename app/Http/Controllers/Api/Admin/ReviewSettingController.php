<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ReviewSettingResource;
use App\Models\ReviewSetting;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class ReviewSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $reviewSetting = ReviewSetting::query();
        $reviewSetting = $reviewSetting->first();

        return $this->successResponse($reviewSetting ? new ReviewSettingResource($reviewSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request, ReviewSetting $setting)
    {
        $rules = [
            'leave_review_days' => ['required'],
            'respond_review_days' => ['required'],
        ];
        $this->validate($request, $rules);

        $reviewSetting = ReviewSetting::first();
        if (!$reviewSetting) {
            $setting = ReviewSetting::create([
                'id' => '1',
                'leave_review_days' => $request->leave_review_days,
                'respond_review_days' => $request->respond_review_days,
            ]);
        }
        $result = ReviewSetting::whereId($request->id)->update([
            'leave_review_days' => $request->leave_review_days,
            'respond_review_days' => $request->respond_review_days,
        ]);

        if ($result || $setting) {
            return $this->apiSuccessResponse(new ReviewSettingResource($setting), 'Settings have been updated successfully.');
        }
        return $this->errorResponse();
    }
}
