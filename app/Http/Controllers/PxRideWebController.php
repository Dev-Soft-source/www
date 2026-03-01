<?php

namespace App\Http\Controllers;

use App\Http\Requests\Px\PxStoreRideRequest;
use App\Models\PxOptionGroup;
use App\Models\Vehicle;
use App\Services\PxRideService;

class PxRideWebController extends Controller
{
    public function create($lang = null)
    {
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        $vehicles = Vehicle::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('primary_vehicle')
            ->orderByDesc('id')
            ->get();

        $optionGroups = PxOptionGroup::query()
            ->with(['options' => function ($q) use ($selectedLangId, $defaultLangId) {
                $q->where('is_active', true)
                    ->orderBy('sort_order')
                    ->with(['translations' => function ($tq) use ($selectedLangId, $defaultLangId) {
                        $tq->whereIn('language_id', array_filter([$selectedLangId, $defaultLangId]));
                    }]);
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($group) use ($selectedLangId, $defaultLangId) {
                $group->options = $group->options->map(function ($option) use ($selectedLangId, $defaultLangId) {
                    $selected = $option->translations->firstWhere('language_id', $selectedLangId);
                    $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
                    $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
                    $option->display_description = optional($selected)->description ?: optional($fallback)->description;
                    return $option;
                });
                return $group;
            });

        return view('px.post_ride', [
            'vehicles' => $vehicles,
            'optionGroups' => $optionGroups,
        ]);
    }

    public function store(PxStoreRideRequest $request, PxRideService $service, $lang = null)
    {
        $payload = $request->validated();
        $payload['stops'] = $this->parseStopsText((string) $request->input('stops_text', ''));

        if (!empty($payload['vehicle_id'])) {
            $ownsVehicle = Vehicle::query()
                ->where('id', $payload['vehicle_id'])
                ->where('user_id', auth()->id())
                ->exists();

            if (!$ownsVehicle) {
                return back()
                    ->withInput()
                    ->withErrors(['vehicle_id' => 'Selected vehicle does not belong to your account.']);
            }
        }

        $ride = $service->createRide($payload, $request->user());

        return redirect()
            ->route('px.post_ride.create', ['lang' => optional($this->selectedLanguage)->abbreviation])
            ->with('message', 'PX ride posted successfully. Ride ID: ' . $ride->id);
    }

    protected function parseStopsText(string $stopsText): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $stopsText) ?: [];
        $stops = [];
        foreach ($lines as $line) {
            $label = trim($line);
            if ($label === '') {
                continue;
            }
            $stops[] = [
                'label' => $label,
                'is_pickup' => true,
                'is_dropoff' => true,
            ];
        }
        return $stops;
    }
}
