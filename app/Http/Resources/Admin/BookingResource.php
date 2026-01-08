<?php

namespace App\Http\Resources\Admin;

use App\Models\Booking;
use App\Models\FeaturesSetting;
use App\Models\Rating;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->resource === null) {
            return [];
        }

        $pinkRide = "";
        $extraCareRide = "";
        $getFeatures = $this->ride->features ?? null;
        if(isset($getFeatures)){
            $explodFeatures = explode('=', $getFeatures);
            if(isset($explodeFeatures) && !empty($explodeFeatures)){
                if(in_array(1, $explodeFeatures)){
                    $pinkRide = "Pink ride";
                }
                if(in_array(2, $explodeFeatures)){
                    $extraCareRide = "Extra-Care Ride";
                }
            }
        }

        return [
            'id' => $this->id,
            'ride_id' => $this->ride_id,
            'seats' => $this->seats,
            'status' => $this->status,
            'booked_on' => $this->booked_on,
            'booking_credit' => round($this->booking_credit,2),
            'departure_city' => $this->departure ?? null,
            'destination_city' => $this->destination ?? null,
            'pink_ride' => $pinkRide,
            'extra_care_ride' => $extraCareRide,
            'price' => round($this->fare,2) ?? null,
            'total' => round($this->fare + $this->booking_credit,2) ?? null,
            'payment_method' => isset($this->ride->payment_method) ? FeaturesSetting::whereId($this->ride->payment_method)->value('slug') : null,
            'departure_date' => $this->ride->date ?? null,
            'departure_time' => $this->ride->time ?? null,
            'ride_features' => $this->ride->features ?? null,
            'ride_seats' => $this->ride->seats ?? null,
            'ride_booked_seats' => Booking::where('ride_id', $this->ride_id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) {
                $query->whereNull('deleted_at');
            })
            ->sum('seats') ?? null,
            'driver_first_name' => $this->ride->driver->first_name ?? null,
            'driver_last_name' => $this->ride->driver->last_name ?? null,
            'driver_email' => $this->ride->driver->email ?? null,
            'driver_gender' => $this->ride->driver->gender ?? null,
            'driver_average_rating' => Rating::where('type', 1)->whereHas('ride', fn($query) => $query->where('added_by', $this->ride?->added_by))->where('status', 1)->avg('average_rating') ?? 0,
            'driver_suspand' => $this->ride->driver->suspand ?? null,
            'passenger_first_name' => $this->passenger->first_name ?? null,
            'passenger_last_name' => $this->passenger->last_name ?? null,
            'passenger_email' => $this->passenger->email ?? null,
            'passenger_gender' => $this->passenger->gender ?? null,
            'passenger_average_rating' => Rating::where('status', 1)->where('type', 1)->whereHas('booking', fn($query) => $query->where('user_id', $this->passenger?->id))->avg('average_rating') ?? 0,
            'passenger_suspand' => $this->passenger->suspand ?? null,
            'passenger_student' => $this->passenger->student ?? null,
        ];
    }
}