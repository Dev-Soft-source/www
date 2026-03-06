<div
    class="relative h-full"
    data-city-autocomplete
    data-invalid-message="{{ $invalidErrorMessage }}"
>
    <div class="relative h-full">
        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                aria-hidden="true">
                <path fill="#888888" fill-rule="evenodd"
                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                    clip-rule="evenodd" />
            </svg>
        </div>
        <input type="text" name="{{ $field }}[label]" wire:model.debounce.250ms="query" wire:focus="onFocus"
            wire:keydown.arrow-down.prevent="highlightNext" wire:keydown.arrow-up.prevent="highlightPrevious"
            wire:keydown.enter.prevent="selectHighlighted" wire:keydown.escape="closeSuggestions"
            wire:blur="closeSuggestions" class=" {{ $class ? $class : 'w-full pl-8 rounded border-gray-300 placeholder-gray-400' }}"
            placeholder="{{ $placeholder }}" autocomplete="off">

        <input type="hidden" name="{{ $field }}[city_id]" value="{{ $cityId }}">


        @if (!empty($suggestions))
            <ul
                class="absolute z-20 mt-1 w-full rounded border border-gray-200 bg-white shadow-md max-h-56 overflow-auto">
                @foreach ($suggestions as $index => $city)
                    <li wire:mousedown.prevent="selectCity({{ $city['id'] }})"
                        wire:mouseenter="setHighlightedIndex({{ $index }})"
                        class="px-3 py-2 cursor-pointer text-lg text-gray-800 {{ $highlightedIndex === $index ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        {{ $city['label'] ?? $city['name'] }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    @if ($errorMessage)
    <div class="absolute">
        <div class="tooltip-error shadow-lg mt-1">{{ $errorMessage }}</div>
    </div>
    @endif

    @error($field . '.label')
    <div class="absolute">
        <div class="tooltip-error shadow-lg">{{ $message }}</div>
    </div>
    @enderror

</div>
