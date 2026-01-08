<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->resource === null) {
            return [];
        }
        return [
            'id' => $this->id,
            'review' => $this->review,
            'vehicle_condition' => $this->vehicle_condition,
            'conscious' => $this->conscious,
            'comfort' => $this->comfort,
            'communication' => $this->communication,
            'attitude' => $this->attitude,
            'hygiene' => $this->hygiene,
            'respect' => $this->respect,
            'safety' => $this->safety,
            'timeliness' => $this->timeliness,
            'feature' => $this->feature,
            'status' => $this->status,
            'ride_id' => $this->ride_id,
            'added_on' => $this->added_on,
            'is_disply' => $this->is_disply,
            'from_id' => $this->from->id ?? null,
            'from_first_name' => $this->from->first_name ?? null,
            'from_last_name' => $this->from->last_name ?? null,
            'ride_date' => $this->ride->date ?? null,
            'ride_time' => $this->ride->time ?? null,
            'departure_city' => $this->ride->defaultRideDetail[0]->departure ?? null,
            'destination_city' => $this->ride->defaultRideDetail[0]->destination ?? null,
            'driver_id' => $this->ride->driver->id ?? null,
            'driver_first_name' => $this->ride->driver->first_name ?? null,
            'reply' => $this->replies->first()->reply ?? null,
            'passenger_first_name' => $this->ride->bookings->first()->passenger->first_name ?? null,
        ];
    }
}
