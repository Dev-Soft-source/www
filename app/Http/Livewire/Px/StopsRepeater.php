<?php

namespace App\Http\Livewire\Px;

use Livewire\Component;

class StopsRepeater extends Component
{
    public array $stops = [];
    public ?int $pendingRemoveIndex = null;

    public function mount(array $initialStops = []): void
    {
        $this->stops = collect($initialStops)
            ->map(function ($stop) {
                return [
                    'label' => (string) ($stop['label'] ?? ''),
                    'city_id' => isset($stop['city_id']) && is_numeric($stop['city_id']) ? (int) $stop['city_id'] : null,
                ];
            })
            ->values()
            ->all();
    }

    public function addStop(): void
    {
        $this->stops[] = [
            'label' => '',
            'city_id' => null,
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

