<?php

namespace App\Http\Livewire\Px;

use App\Models\City;
use Livewire\Component;

class CityAutocomplete extends Component
{
    public string $field;
    public string $placeholder;
    public string $query = '';
    public ?int $cityId = null;
    public array $suggestions = [];
    public int $highlightedIndex = -1;

    public function mount(string $field, string $placeholder = 'City, station, or address', ?string $initialLabel = null, $initialCityId = null): void
    {
        $this->field = $field;
        $this->placeholder = $placeholder;
        $this->cityId = is_numeric($initialCityId) ? (int) $initialCityId : null;

        if ($this->cityId) {
            $city = City::query()
                ->with(['state:id,abrv,country_id', 'state.country:id,name'])
                ->where('id', $this->cityId)
                ->first();
            $this->query = $city ? $this->formatCityLabel($city) : (string) ($initialLabel ?? '');
            return;
        }

        $this->query = (string) ($initialLabel ?? '');
    }

    public function updatedQuery(string $value): void
    {
        $this->cityId = null;
        $this->loadSuggestions(trim($value));
    }

    public function onFocus(): void
    {
        $this->loadSuggestions(trim($this->query));
    }

    public function selectCity(int $id): void
    {
        $city = City::query()
            ->with(['state:id,abrv,country_id', 'state.country:id,name'])
            ->where('id', $id)
            ->first();
        if (!$city) {
            return;
        }

        $this->cityId = (int) $city->id;
        $this->query = $this->formatCityLabel($city);
        $this->suggestions = [];
        $this->highlightedIndex = -1;
    }

    public function closeSuggestions(): void
    {
        // Enforce city list selection:
        // if user typed free text and left the field without selecting a city,
        // reset the input to empty.
        if ($this->cityId === null) {
            $this->query = '';
        }

        $this->suggestions = [];
        $this->highlightedIndex = -1;
    }

    public function highlightNext(): void
    {
        $count = count($this->suggestions);
        if ($count === 0) {
            $this->highlightedIndex = -1;
            return;
        }

        $this->highlightedIndex = $this->highlightedIndex < ($count - 1)
            ? $this->highlightedIndex + 1
            : 0;
    }

    public function highlightPrevious(): void
    {
        $count = count($this->suggestions);
        if ($count === 0) {
            $this->highlightedIndex = -1;
            return;
        }

        $this->highlightedIndex = $this->highlightedIndex > 0
            ? $this->highlightedIndex - 1
            : $count - 1;
    }

    public function selectHighlighted(): void
    {
        if (!isset($this->suggestions[$this->highlightedIndex])) {
            return;
        }

        $this->selectCity((int) $this->suggestions[$this->highlightedIndex]['id']);
    }

    public function setHighlightedIndex(int $index): void
    {
        $this->highlightedIndex = $index;
    }

    protected function loadSuggestions(string $search): void
    {
        if (mb_strlen($search) < 2) {
            $this->suggestions = [];
            $this->highlightedIndex = -1;
            return;
        }

        $this->suggestions = City::query()
            ->with(['state:id,abrv,country_id', 'state.country:id,name'])
            ->where('status', '1')
            ->where('name', 'like', $search . '%')
            ->orderBy('name')
            ->limit(12)
            ->get()
            ->map(fn (City $city) => [
                'id' => (int) $city->id,
                'name' => $city->name,
                'label' => $this->formatCityLabel($city),
            ])
            ->all();

        $this->highlightedIndex = empty($this->suggestions) ? -1 : 0;
    }

    public function render()
    {
        return view('livewire.px.city-autocomplete');
    }

    protected function formatCityLabel(City $city): string
    {
        $parts = [$city->name];
        $stateAbbr = trim((string) optional($city->state)->abrv);
        $countryName = trim((string) optional(optional($city->state)->country)->name);

        if ($stateAbbr !== '') {
            $parts[] = $stateAbbr;
        }
        if ($countryName !== '') {
            $parts[] = $countryName;
        }

        return implode(', ', $parts);
    }
}
