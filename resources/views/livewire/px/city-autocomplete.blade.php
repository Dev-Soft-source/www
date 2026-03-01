<div class="relative">
    <input
        type="text"
        name="{{ $field }}[label]"
        wire:model.debounce.250ms="query"
        wire:focus="onFocus"
        wire:keydown.arrow-down.prevent="highlightNext"
        wire:keydown.arrow-up.prevent="highlightPrevious"
        wire:keydown.enter.prevent="selectHighlighted"
        wire:keydown.escape="closeSuggestions"
        wire:blur="closeSuggestions"
        class="w-full rounded border-gray-300"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        required
    >

    <input type="hidden" name="{{ $field }}[city_id]" value="{{ $cityId }}">

    @if(!empty($suggestions))
        <ul class="absolute z-20 mt-1 w-full rounded border border-gray-200 bg-white shadow-md max-h-56 overflow-auto">
            @foreach($suggestions as $index => $city)
                <li
                    wire:mousedown.prevent="selectCity({{ $city['id'] }})"
                    wire:mouseenter="setHighlightedIndex({{ $index }})"
                    class="px-3 py-2 cursor-pointer text-sm text-gray-800 {{ $highlightedIndex === $index ? 'bg-gray-100' : 'hover:bg-gray-100' }}"
                >
                    {{ $city['label'] ?? $city['name'] }}
                </li>
            @endforeach
        </ul>
    @endif
</div>
