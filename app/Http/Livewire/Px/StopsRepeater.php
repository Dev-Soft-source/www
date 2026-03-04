<?php

namespace App\Http\Livewire\Px;

use Livewire\Component;

class StopsRepeater extends Component
{
    public array $stops = [];
    public ?int $pendingRemoveIndex = null;
    public string $originLabel = '';
    public string $destinationLabel = '';

    public function mount(array $initialStops = [], string $originLabel = '', string $destinationLabel = ''): void
    {
        $this->stops = collect($initialStops)
            ->map(function ($stop) {
                return [
                    'label' => (string) ($stop['label'] ?? ''),
                    'city_id' => isset($stop['city_id']) && is_numeric($stop['city_id']) ? (int) $stop['city_id'] : null,
                    'price_delta_minor' => isset($stop['price_delta_minor']) && is_numeric($stop['price_delta_minor']) ? (int) $stop['price_delta_minor'] : 0,
                ];
            })
            ->values()
            ->all();
        
        $this->originLabel = $originLabel;
        $this->destinationLabel = $destinationLabel;
    }

    public function addStop(): void
    {
        $this->stops[] = [
            'label' => '',
            'city_id' => null,
            'price_delta_minor' => 0,
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
