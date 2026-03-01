<?php

namespace App\Http\Requests\Px;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PxStoreRideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'origin' => ['required', 'array'],
            'origin.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'origin.label' => ['required', 'string', 'max:160'],
            'origin.pickup_location' => ['nullable', 'string', 'max:255'],
            'origin.lat' => ['nullable', 'numeric', 'between:-90,90'],
            'origin.lng' => ['nullable', 'numeric', 'between:-180,180'],

            'destination' => ['required', 'array'],
            'destination.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'destination.label' => ['required', 'string', 'max:160'],
            'destination.dropoff_location' => ['nullable', 'string', 'max:255'],
            'destination.lat' => ['nullable', 'numeric', 'between:-90,90'],
            'destination.lng' => ['nullable', 'numeric', 'between:-180,180'],

            'timezone' => ['nullable', 'timezone'],
            'distance_meters' => ['nullable', 'integer', 'min:0'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'polyline' => ['nullable', 'string'],

            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'departure_at' => ['required', 'date', 'after:' . now()->addMinutes(5)->toDateTimeString()],
            'arrival_estimated_at' => ['nullable', 'date', 'after_or_equal:departure_at'],
            'boarding_window_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'seats_total' => ['required', 'integer', 'min:1', 'max:8'],
            'price_minor' => ['required', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'visibility' => ['nullable', Rule::in(['public', 'private'])],
            'booking_mode' => ['nullable', 'integer', 'min:0'],
            'booking_method' => ['nullable', 'integer', 'min:0'],
            'luggage_size' => ['nullable', 'integer', 'min:0'],
            'cancelation_policy' => ['nullable', 'integer', 'min:0'],

            'allow_detour' => ['nullable', 'boolean'],
            'women_only' => ['nullable', 'boolean'],
            'extra_care' => ['nullable', 'boolean'],
            'smoking_allowed' => ['nullable', 'integer', 'min:0'],
            'pets_allowed' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'meta' => ['nullable', 'array'],
            // Backward-compatible alias from UI checkboxes (name="preference[]")
            'preference' => ['nullable', 'array'],
            'preference.*' => ['integer', 'exists:px_ride_options,id'],
            'ride_option_ids' => ['nullable', 'array'],
            'ride_option_ids.*' => ['integer', 'exists:px_ride_options,id'],

            'stops' => ['nullable', 'array', 'max:20'],
            'stops.*.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'stops.*.label' => ['required_with:stops', 'string', 'max:160'],
            'stops.*.lat' => ['nullable', 'numeric', 'between:-90,90'],
            'stops.*.lng' => ['nullable', 'numeric', 'between:-180,180'],
            'stops.*.eta_at' => ['nullable', 'date', 'after_or_equal:departure_at'],
            'stops.*.price_delta_minor' => ['nullable', 'integer', 'min:0'],
            'stops.*.seats_available' => ['nullable', 'integer', 'min:0', 'max:8'],
            'stops.*.is_pickup' => ['nullable', 'boolean'],
            'stops.*.is_dropoff' => ['nullable', 'boolean'],
        ];
    }
}
