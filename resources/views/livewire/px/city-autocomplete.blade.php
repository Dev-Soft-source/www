<div class="relative" data-city-autocomplete data-invalid-message="{{ $invalidErrorMessage }}">
    <div class="relative ">
        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
            @if ($icon == 'pick')
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path
                            d="M19 12C19 15.866 15.866 19 12 19C8.13401 19 5 15.866 5 12C5 8.13401 8.13401 5 12 5C15.866 5 19 8.13401 19 12Z"
                            stroke="#888888" stroke-width="2"></path>
                        <path d="M19 12H21" stroke="#888888" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path d="M3 12H5" stroke="#888888" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path d="M12 19L12 21" stroke="#888888" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path d="M12 3L12 5" stroke="#888888" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path
                            d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
                            stroke="#888888" stroke-width="2"></path>
                    </g>
                </svg>
            @else
                {{-- default icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                    aria-hidden="true">
                    <path fill="#888888" fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd" />
                </svg>
            @endif
        </div>
        <input type="text" name="{{ $field }}[label]" wire:model.debounce.250ms="query" wire:focus="onFocus"
            wire:keydown.arrow-down.prevent="highlightNext" wire:keydown.arrow-up.prevent="highlightPrevious"
            wire:keydown.enter.prevent="selectHighlighted" wire:keydown.escape="closeSuggestions"
            wire:blur="closeSuggestions"
            class="city-autocomplete-input {{ $class ? $class : 'w-full pl-8 rounded border-gray-300 placeholder-gray-400' }}"
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
