<?php

namespace App\Http\Resources\Admin;

use App\Models\PhoneNumber;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'email_verified' => $this->email_verified,
            'phone' => optional(PhoneNumber::where('user_id', $this->id)->first())->phone ?? null,
            'phone_verified' => PhoneNumber::where('user_id', $this->id)->where('verified', 1)->exists() ? 1 : 0,
            'driver' => $this->driver,
            'student' => $this->student,
            'student_card_exp_date' => $this->student_card_exp_date,
            'charge_booking' => $this->charge_booking,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'address' => $this->address,
            'government_issued_id' => $this->government_issued_id,
            'government_id' => $this->government_id,
            'country' => $this->country,
            'city' => $this->city,
            'pink_ride' => $this->pink_ride,
            'folks_ride' => $this->folks_ride,
            'free_rides' => $this->free_rides,
            'referral' => $this->referral,
            'sms_notification' => $this->sms_notification,
            'email_notification' => $this->email_notification,
            'driver_liscense' => $this->driver_liscense,
            'student_card' => $this->student_card,
            'student_card_exp_date' => $this->student_card_exp_date,
            'suspand' => $this->suspand,
            'closed' => $this->closed,
            'admin_deactive_account' => $this->admin_deactive_account,
            'vehicles' => $this->vehicles,
            'created_at' => date('m/d/Y H:i:s', strtotime($this->created_at)),
            'updated_at' => date('m/d/Y H:i:s', strtotime($this->updated_at)),
        ];
    }
}
