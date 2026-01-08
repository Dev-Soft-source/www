<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Models\Rating;
use App\Models\ReviewSetting;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyWalletController extends Controller
{
    use StatusResponser;

    public function passengerRide(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $bookings = Booking::where('user_id', $user_id)->select('id', 'ride_id' , 'seats', 'booking_credit','ride_detail_id', 'departure', 'destination', 'price')
            ->where('status', '!=', '4')
            ->whereHas('ride', function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<=', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<=', now()->toTimeString());
                        });
                })
                ->whereHas('driver', function ($query) {
                    $query->whereNull('deleted_at'); // Exclude soft-deleted drivers
                });
            })
            ->with(['ride' => function ($query) {
                $query->select('id', 'date');
            }])
            ->orderBy('ride_id', 'desc')
            ->get();

        $data = ['bookings' => $bookings];
        return $this->successResponse($data, 'Get my completed trips');
    }
}
