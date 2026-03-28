<?php

namespace App\Http\Livewire\Px;

use App\Models\City;
use Illuminate\Database\Eloquent\Builder;
use App\Support\LocationCache;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class CityAutocomplete extends Component
{
    private const CANADA_ISO_CODE = 'CA';
    private const SUGGESTIONS_CACHE_TTL_SECONDS = 300;

    public string $field;
    public ?string $class = '';
    public string $placeholder;
    public string $query = '';
    public ?int $cityId = null;
    public array $suggestions = [];
    public int $highlightedIndex = -1;
    public ?string $errorMessage = null;
    public string $invalidErrorMessage = 'Please select a valid city from the dropdown.';

    public function mount(
        string $field,
        string $class = '',
        string $placeholder = 'City, station, or address',
        ?string $initialLabel = null,
        $initialCityId = null,
        ?string $invalidErrorMessage = null
    ): void
    {
        $this->field = $field;
        $this->class = $class;
        $this->placeholder = $placeholder;
        $this->cityId = is_numeric($initialCityId) ? (int) $initialCityId : null;
        $this->invalidErrorMessage = trim((string) ($invalidErrorMessage ?? '')) !== ''
            ? (string) $invalidErrorMessage
            : $this->invalidErrorMessage;

        if ($this->cityId) {
            $city = $this->canadianCitiesQuery()
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
        $this->errorMessage = null;
        $this->loadSuggestions(trim($value));
    }

    public function onFocus(): void
    {
        $this->errorMessage = null;
        $this->loadSuggestions(trim($this->query));
    }

    public function selectCity(int $id): void
    {
        $city = $this->canadianCitiesQuery()
            ->with(['state:id,abrv,country_id', 'state.country:id,name'])
            ->where('id', $id)
            ->first();
        if (!$city) {
            return;
        }

        $this->cityId = (int) $city->id;
        $this->query = $this->formatCityLabel($city);
        $this->errorMessage = null;
        $this->suggestions = [];
        $this->highlightedIndex = -1;
    }

    public function closeSuggestions(): void
    {
        if ($this->cityId === null && !$this->commitTypedExactMatch()) {
            $this->errorMessage = trim($this->query) !== ''
                ? $this->invalidErrorMessage
                : null;
            // $this->query = '';
        } else {
            $this->errorMessage = null;
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
        if ($this->cityId !== null) {
            $this->errorMessage = null;
            $this->suggestions = [];
            $this->highlightedIndex = -1;
            return;
        }

        if (!isset($this->suggestions[$this->highlightedIndex])) {
            if (!$this->commitTypedExactMatch()) {
                $this->errorMessage = trim($this->query) !== ''
                    ? $this->invalidErrorMessage
                    : null;
            }
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
        $search = $this->extractCityName($search);

        if (mb_strlen($search) < 2) {
            $this->suggestions = [];
            $this->highlightedIndex = -1;
            return;
        }

        $normalizedSearch = mb_strtolower(trim($search));
        $cacheKey = LocationCache::key('city-autocomplete:suggestions:' . md5($normalizedSearch));

        $this->suggestions = Cache::remember(
            $cacheKey,
            now()->addSeconds(self::SUGGESTIONS_CACHE_TTL_SECONDS),
            function () use ($search) {
                return $this->canadianCitiesQuery()
                    ->with(['state:id,abrv,country_id', 'state.country:id,name'])
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
            }
        );

        $this->highlightedIndex = empty($this->suggestions) ? -1 : 0;
    }

    protected function commitTypedExactMatch(): bool
    {
        $search = trim($this->query);
        if ($search === '') {
            return false;
        }

        $normalizedSearch = mb_strtolower($search);

        foreach ($this->suggestions as $suggestion) {
            $label = mb_strtolower(trim((string) ($suggestion['label'] ?? '')));
            $name = mb_strtolower(trim((string) ($suggestion['name'] ?? '')));

            if ($normalizedSearch === $label || $normalizedSearch === $name) {
                $this->selectCity((int) $suggestion['id']);
                return true;
            }
        }

        $city = $this->canadianCitiesQuery()
            ->with(['state:id,abrv,country_id', 'state.country:id,name'])
            ->where(function (Builder $query) use ($normalizedSearch, $search) {
                $query->whereRaw('LOWER(name) = ?', [$normalizedSearch]);

                $parsed = $this->parseSearchParts($search);
                if (($parsed['city'] ?? '') === '') {
                    return;
                }

                $query->orWhere(function (Builder $cityQuery) use ($parsed) {
                    $cityQuery->whereRaw('LOWER(name) = ?', [$parsed['city']]);

                    if (($parsed['state'] ?? '') !== '') {
                        $cityQuery->whereHas('state', function (Builder $stateQuery) use ($parsed) {
                            $stateQuery->where(function (Builder $stateNameQuery) use ($parsed) {
                                $stateNameQuery
                                    ->whereRaw('LOWER(abrv) = ?', [$parsed['state']])
                                    ->orWhereRaw('LOWER(name) = ?', [$parsed['state']]);
                            });
                        });
                    }
                });
            })
            ->orderBy('name')
            ->first();

        if (!$city) {
            return false;
        }

        $this->cityId = (int) $city->id;
        $this->query = $this->formatCityLabel($city);

        return true;
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

    protected function canadianCitiesQuery(): Builder
    {
        return City::query()
            ->where('status', '1')
            ->whereHas('state.country', function (Builder $query) {
                $query->where('iso_code', self::CANADA_ISO_CODE);
            });
    }

    protected function extractCityName(string $search): string
    {
        $parts = $this->parseSearchParts($search);

        return $parts['city'] ?? trim($search);
    }

    protected function parseSearchParts(string $search): array
    {
        $parts = array_values(array_filter(array_map(
            static fn (string $part): string => mb_strtolower(trim($part)),
            explode(',', $search)
        )));

        return [
            'city' => $parts[0] ?? '',
            'state' => $parts[1] ?? '',
            'country' => $parts[2] ?? '',
        ];
    }
}
