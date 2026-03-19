<?php

namespace App\Http\Livewire\Px;

use Illuminate\Support\Str;
use Livewire\Component;

class StopsRepeater extends Component
{
    public array $stops = [];
    public ?int $pendingRemoveIndex = null;
    public ?string $originLabel = '';
    public ?string $destinationLabel = '';
    public ?string $stopAlongTheWayLabel = '';
    public ?string $addStopBtnLabel = '';
    public ?string $stopsDeleteConfirmText = '';
    public ?string $removeBtnText = '';
    public ?string $cancelBtnText = '';

    public function mount(array $initialStops = [], ?string $originLabel = '', ?string $destinationLabel = '', 
    ?string $addStopBtnLabel = '', ?string $stopAlongTheWayLabel = '', ?string $stopsDeleteConfirmText = '', ?string $removeBtnText = '', ?string $cancelBtnText = ''): void
    {
        $this->stops = collect($initialStops)
            ->map(function ($stop) {
                $departureAt = $stop['departure_at'] ?? null;
                
                // If we have separate date/time, combine them
                if (!$departureAt && isset($stop['departure_date']) && isset($stop['departure_time'])) {
                    $departureDate = $stop['departure_date'];
                    $departureTime = $stop['departure_time'];
                    if ($departureDate && $departureTime) {
                        try {
                            $departureAt = \Illuminate\Support\Carbon::parse($departureDate . ' ' . $departureTime);
                        } catch (\Throwable $e) {
                            // Keep null if parsing fails
                        }
                    }
                }
                
                // Format for display in input field (Y-m-d H:i)
                $departureAtFormatted = '';
                if ($departureAt) {
                    try {
                        $dt = \Illuminate\Support\Carbon::parse($departureAt);
                        $departureAtFormatted = $dt->format('Y-m-d H:i');
                    } catch (\Throwable $e) {
                        // Keep empty if parsing fails
                    }
                }
                
                // Combine pickup and dropoff locations into one field, preferring pickup if both exist
                $pickupDropoffLocation = (string) ($stop['pickup_dropoff_location'] ?? '');
                if (empty($pickupDropoffLocation)) {
                    $pickupLocation = (string) ($stop['pickup_location'] ?? '');
                    $dropoffLocation = (string) ($stop['dropoff_location'] ?? '');
                    // Combine both if they exist, otherwise use whichever is available
                    if (!empty($pickupLocation) && !empty($dropoffLocation)) {
                        $pickupDropoffLocation = $pickupLocation . ' / ' . $dropoffLocation;
                    } else {
                        $pickupDropoffLocation = $pickupLocation ?: $dropoffLocation;
                    }
                }
                
                return [
                    '_key' => (string) ($stop['_key'] ?? Str::uuid()),
                    'label' => (string) ($stop['label'] ?? ''),
                    'city_id' => isset($stop['city_id']) && is_numeric($stop['city_id']) ? (int) $stop['city_id'] : null,
                    'price_delta_minor' => isset($stop['price_delta_minor']) && is_numeric($stop['price_delta_minor']) ? (int) $stop['price_delta_minor'] : 0,
                    'departure_at' => $departureAtFormatted,
                    'pickup_dropoff_location' => $pickupDropoffLocation,
                ];
            })
            ->values()
            ->all();
        
        $this->originLabel = $originLabel;
        $this->destinationLabel = $destinationLabel;
        $this->addStopBtnLabel = $addStopBtnLabel;
        $this->stopAlongTheWayLabel = $stopAlongTheWayLabel;
        $this->stopsDeleteConfirmText = $stopsDeleteConfirmText;
        $this->removeBtnText = $removeBtnText;
        $this->cancelBtnText = $cancelBtnText;
    }

    public function addStop(): void
    {
        $this->stops[] = [
            '_key' => (string) Str::uuid(),
            'label' => '',
            'city_id' => null,
            'price_delta_minor' => 0,
            'departure_at' => '',
            'pickup_dropoff_location' => '',
        ];
    }

    public function requestRemove(int $index): void
    {
        if (!array_key_exists($index, $this->stops)) {
            return;
        }

        $this->pendingRemoveIndex = $index;
    }

    public function confirmRemove(): void
    {
        if ($this->pendingRemoveIndex === null || !array_key_exists($this->pendingRemoveIndex, $this->stops)) {
            $this->pendingRemoveIndex = null;
            return;
        }

        unset($this->stops[$this->pendingRemoveIndex]);
        $this->stops = array_values($this->stops);
        $this->pendingRemoveIndex = null;
    }

    public function cancelRemove(): void
    {
        $this->pendingRemoveIndex = null;
    }

    public function render()
    {
        return view('livewire.px.stops-repeater');
    }
}
