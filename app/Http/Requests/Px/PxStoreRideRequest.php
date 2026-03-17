<?php

namespace App\Http\Requests\Px;

use App\Models\PxOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PxStoreRideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeRideFeatureFlags();

        // Handle departure_at - use directly if provided, otherwise combine from date/time (backward compatibility)
        $departureAt = $this->input('departure_at');
        if (!$departureAt) {
            $departureDate = $this->input('departure_date');
            $departureTime = $this->input('departure_time');
            if ($departureDate && $departureTime) {
                $departureAt = trim($departureDate . ' ' . $departureTime);
                $this->merge(['departure_at' => $departureAt]);
            }
        }

        // Ensure departure_at is in Y-m-d H:i format
        if ($departureAt) {
            try {
                $dt = \Illuminate\Support\Carbon::parse($departureAt);
                $this->merge(['departure_at' => $dt->format('Y-m-d H:i')]);
            } catch (\Throwable $e) {
                // Keep original value if parsing fails
            }
        }

        // Process stops departure_at fields - convert from Y-m-d H:i format to datetime
        $stops = $this->input('stops', []);
        if (!empty($stops)) {
            $processedStops = [];
            foreach ($stops as $index => $stop) {
                $processedStop = $stop;
                if (isset($stop['departure_at']) && !empty($stop['departure_at'])) {
                    try {
                        // Validate and ensure proper format
                        $dt = \Illuminate\Support\Carbon::parse($stop['departure_at']);
                        $processedStop['departure_at'] = $dt->format('Y-m-d H:i');
                    } catch (\Throwable $e) {
                        // Keep original value if parsing fails
                    }
                }
                $processedStops[$index] = $processedStop;
            }
            $this->merge(['stops' => $processedStops]);
        }
    }

    protected function normalizeRideFeatureFlags(): void
    {
        $rideOptionIds = $this->input('ride_option_ids', $this->input('preference', []));

        if (!is_array($rideOptionIds)) {
            $rideOptionIds = is_string($rideOptionIds) ? explode(',', $rideOptionIds) : [$rideOptionIds];
        }

        $rideOptionIds = array_values(array_filter(array_map('intval', $rideOptionIds)));
        $featureCodes = PxOption::query()
            ->whereIn('id', $rideOptionIds)
            ->pluck('code')
            ->map(fn($code) => (string) $code)
            ->all();

        $this->merge([
            'ride_option_ids' => $rideOptionIds,
            'women_only' => in_array('pink_rides', $featureCodes, true),
            'extra_care' => in_array('extra_plus_rides', $featureCodes, true),
        ]);
    }

    public function rules(): array
    {
        return [
            'origin' => ['required', 'array'],
            'origin.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'origin.label' => ['required', 'string', 'max:160'],
            'origin.pickup_location' => ['required', 'string', 'max:255'],
            'origin.lat' => ['nullable', 'numeric', 'between:-90,90'],
            'origin.lng' => ['nullable', 'numeric', 'between:-180,180'],

            'destination' => ['required', 'array'],
            'destination.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'destination.label' => ['required', 'string', 'max:160'],
            'destination.dropoff_location' => ['required', 'string', 'max:255'],
            'destination.price_delta_minor' => ['nullable', 'integer', 'min:0'],
            'destination.lat' => ['nullable', 'numeric', 'between:-90,90'],
            'destination.lng' => ['nullable', 'numeric', 'between:-180,180'],

            'timezone' => ['nullable', 'timezone'],
            'distance_meters' => ['nullable', 'integer', 'min:0'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'polyline' => ['nullable', 'string'],

            'vehicle_mode' => ['nullable', Rule::in(['skip', 'add_new', 'existing'])],
            'vehicle_id' => ['required_if:vehicle_mode,existing', 'nullable', 'integer', 'exists:vehicles,id'],
            'new_vehicle' => ['nullable', 'array'],
            'new_vehicle.make' => ['required_if:vehicle_mode,add_new', 'string', 'max:255'],
            'new_vehicle.model' => ['required_if:vehicle_mode,add_new', 'string', 'max:255'],
            'new_vehicle.type' => ['required_if:vehicle_mode,add_new', Rule::in(['Convertable', 'Coupe', 'Hatchback', 'Minivan', 'Sedan', 'Station wagon', 'SUV', 'Truck', 'Van'])],
            'new_vehicle.license_no' => ['required_if:vehicle_mode,add_new', 'string', 'max:8'],
            'new_vehicle.color' => ['required_if:vehicle_mode,add_new', 'string', 'max:15'],
            'new_vehicle.year' => ['required_if:vehicle_mode,add_new', 'digits:4'],
            'new_vehicle.car_type' => ['required_if:vehicle_mode,add_new', Rule::in(['Electric', 'Hybrid', 'Gas'])],
            'new_vehicle.primary_vehicle' => ['required_if:vehicle_mode,add_new', Rule::in(['0', '1', 0, 1])],
            'new_vehicle_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'],
            'departure_at' => ['required', 'date_format:Y-m-d H:i', 'after:' . now()->addMinutes(5)->toDateTimeString()],
            // Backward compatibility - these are optional if departure_at is provided
            'departure_date' => ['nullable', 'date_format:Y-m-d'],
            'departure_time' => ['nullable', 'date_format:H:i'],
            'arrival_estimated_at' => ['nullable', 'date', 'after_or_equal:departure_at'],
            'boarding_window_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'middle_seats' => ['required', Rule::in([2, 3, '2', '3'])],
            'back_seats' => ['required', Rule::in([2, 3, '2', '3'])],
            'seats_total' => ['required', 'integer', 'min:1', 'max:7'],
            'price_minor' => ['required', 'integer', 'gt:0', 'required_if:stops,null,empty,[]'],
            'currency' => ['nullable', 'string', 'size:3'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'visibility' => ['nullable', Rule::in(['public', 'private'])],
            'booking_mode' => ['nullable', 'integer', 'min:0'],
            'booking_method' => ['nullable', 'integer', 'min:0'],
            'luggage_size' => ['nullable', 'integer', 'min:0'],
            'accept_more_luggage' => ['nullable', 'boolean'],
            'cancelation_policy' => ['nullable', 'integer', 'min:0'],

            'allow_detour' => ['nullable', 'boolean'],
            'women_only' => ['nullable', 'boolean'],
            'extra_care' => ['nullable', 'boolean'],
            'is_recurring' => ['nullable', 'boolean'],
            'recurring_frequency' => ['required_if:is_recurring,1', Rule::in(['daily', 'weekly'])],
            'recurring_trips' => ['required_if:is_recurring,1', 'integer', 'min:1', 'max:365'],

            'pick_drop_off_description' => ['required', 'string', 'max:5000'],
            'smoking_allowed' => ['nullable', 'integer', 'min:0'],
            'pets_allowed' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'agree_terms' => ['accepted'],
            'bypass_price_validation' => ['nullable', 'boolean'],
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
            'stops.*.departure_at' => ['required_with:stops', 'date_format:Y-m-d H:i'],
            'stops.*.eta_at' => ['nullable', 'date'],
            'stops.*.pickup_dropoff_location' => ['required_with:stops', 'string', 'max:500'],
            'stops.*.price_delta_minor' => ['nullable', 'integer', 'min:0'],
            'stops.*.seats_available' => ['nullable', 'integer', 'min:0', 'max:8'],
            'stops.*.is_pickup' => ['nullable', 'boolean'],
            'stops.*.is_dropoff' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Get main ride departure_at - try both direct input and combined from date/time
            $mainDepartureAt = $this->input('departure_at');
            if (!$mainDepartureAt) {
                $departureDate = $this->input('departure_date');
                $departureTime = $this->input('departure_time');
                if ($departureDate && $departureTime) {
                    $mainDepartureAt = trim($departureDate . ' ' . $departureTime);
                }
            }

            $stops = $this->input('stops', []);

            if (!$mainDepartureAt || empty($stops)) {
                return;
            }

            try {
                // Parse main departure - try Y-m-d H:i format first, then fallback to parse
                $mainDeparture = \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i', $mainDepartureAt);
                if ($mainDeparture === false) {
                    $mainDeparture = \Illuminate\Support\Carbon::parse($mainDepartureAt);
                }
            } catch (\Throwable $e) {
                // Main departure_at validation will handle this
                return;
            }

            foreach ($stops as $index => $stop) {
                if (!isset($stop['departure_at']) || empty($stop['departure_at'])) {
                    continue;
                }

                try {
                    // Parse stop departure - try Y-m-d H:i format first, then fallback to parse
                    $stopDeparture = \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i', $stop['departure_at']);
                    if ($stopDeparture === false) {
                        $stopDeparture = \Illuminate\Support\Carbon::parse($stop['departure_at']);
                    }

                    // Compare: stop must be >= main departure
                    if ($stopDeparture->lt($mainDeparture)) {
                        $validator->errors()->add(
                            "stops.$index.departure_at",
                            "The stop departure date & time must be after or equal to the main ride departure time."
                        );
                    }
                } catch (\Throwable $e) {
                    // Date format validation will handle this - skip this stop
                    continue;
                }
            }
        });
    }

    public function messages(): array
    {
        $requiredMessage = $this->translatedRequiredMessage();

        return [
            'required' => $requiredMessage,
            'required_if' => $requiredMessage,
            'accepted' => $requiredMessage,
        ];
    }

    protected function translatedRequiredMessage(): string
    {
        // Try to reuse siteText from Controller if already loaded
        $siteText = View::shared('siteText', null);
        if ($siteText === null) {
            $siteText = getCurrentSiteText();
        }
        return (string) ($siteText['required_field_error_text'] ?? __('validation.required'));
    }
}
