<?php

namespace App\Http\Requests\Px;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PxSearchRidesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin_city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'destination_city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'origin_label' => ['nullable', 'string', 'max:160'],
            'destination_label' => ['nullable', 'string', 'max:160'],

            'origin_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'origin_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'destination_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'destination_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'radius_km' => ['nullable', 'numeric', 'min:1', 'max:300'],

            'departure_date' => ['nullable', 'date'],
            'departure_from' => ['nullable', 'date'],
            'departure_to' => ['nullable', 'date', 'after_or_equal:departure_from'],

            'seats_required' => ['nullable', 'integer', 'min:1', 'max:8'],
            'price_minor_min' => ['nullable', 'integer', 'min:0'],
            'price_minor_max' => ['nullable', 'integer', 'min:0'],
            'booking_mode' => ['nullable', 'integer', 'min:0'],
            'women_only' => ['nullable', 'boolean'],
            'extra_care' => ['nullable', 'boolean'],
            'smoking_allowed' => ['nullable', 'integer', 'min:0'],
            'pets_allowed' => ['nullable', 'integer', 'min:0'],
            'luggage_size' => ['nullable', 'integer', 'min:0'],

            'sort' => ['nullable', Rule::in(['soonest', 'price_asc', 'price_desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'log_search' => ['nullable', 'boolean'],
        ];
    }
}
