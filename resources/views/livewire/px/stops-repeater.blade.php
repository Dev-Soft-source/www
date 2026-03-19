<div class="space-y-2">
    <label id="px-stops-origin-city" class="block mb-2 origin_city">{{ $originLabel }}</label>
    <label class="block mb-4 hidden">{{ $stopAlongTheWayLabel }}</label>
    @foreach ($stops as $index => $stop)
        <div wire:key="stop-row-{{ $stop['_key'] ?? $index }}" class="flex flex-col md:flex-row md:items-center gap-3 stop-row ml-8">
            <div class="flex flex-col md:flex-row gap-2 items-stretch  min-w-0 w-full">
                <div class="flex-1 min-w-0 w-full md:w-auto">
                    @livewire(
                        'px.city-autocomplete',
                        [
                            'field' => "stops[$index]",
                            'placeholder' => 'Stop city (in route order)',
                            'initialLabel' => $stop['label'] ?? '',
                            'initialCityId' => $stop['city_id'] ?? null,
                        ],
                        key('px-stop-city-' . ($stop['_key'] ?? $index))
                    )
                    <input type="hidden" name="stops[{{ $index }}][is_pickup]" value="1">
                    <input type="hidden" name="stops[{{ $index }}][is_dropoff]" value="1">
                    <input type="hidden" name="stops[{{ $index }}][price_delta_minor]"
                        value="{{ (int) ($stop['price_delta_minor'] ?? 0) }}">
                    @error("stops.$index.label")
                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>
                <div class="flex-1 min-w-0 w-full md:w-auto">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill="#888888" fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div wire:ignore>
                            <input type="text"
                                value="{{ $stop['departure_at'] ?? old("stops.$index.departure_at") }}"
                                class="w-full pl-8 rounded border-gray-300 placeholder-gray-400"
                                data-stop-departure-picker="{{ $index }}"
                                data-stop-departure-display
                                placeholder="Select departure date and time" autocomplete="off">
                        </div>
                        <input type="hidden" name="stops[{{ $index }}][departure_at]" wire:model.defer="stops.{{ $index }}.departure_at"
                            value="{{ $stop['departure_at'] ?? old("stops.$index.departure_at") }}"
                            data-stop-departure-value="{{ $index }}">
                    </div>
                    @error("stops.$index.departure_at")
                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                    
                </div>
                <div class="flex-1 min-w-0 w-full md:w-auto flex flex-col">
                    <textarea name="stops[{{ $index }}][pickup_dropoff_location]" rows="1"
                        class="w-full rounded border-gray-300 md:h-full md:flex-1 resize-none" placeholder="e.g. Tim Hortons parking lot, near the entrance" autocomplete="off">{{ $stop['pickup_dropoff_location'] ?? old("stops.$index.pickup_dropoff_location") }}</textarea>
                    @error("stops.$index.pickup_dropoff_location")
                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="button" wire:click="requestRemove({{ $index }})"
                    class="flex-shrink-0 p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded focus:outline-none focus:ring-2 focus:ring-red-400 self-start md:self-auto"
                    aria-label="Remove stop" title="Remove stop">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
            </button>

        </div>
    @endforeach
    <button type="button" wire:click="addStop" data-add-stop-button
        class="button-exp-fill flex-shrink-0 whitespace-nowrap mb-4 disabled:opacity-60 disabled:cursor-not-allowed">+ {{ $addStopBtnLabel }}</button>
    <label id="px-stops-destination-city"
        class="block mb-4 destination_city">{{ $destinationLabel }}</label>

    @if (!is_null($pendingRemoveIndex))
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl text-center modal-border1">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="text-2xl text-gray-900"></h3>
                    <button type="button" wire:click="cancelRemove"
                        class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg></button>
                </div>
                <p class="mt-3 text-xl text-center text-gray-600">{{ $stopsDeleteConfirmText }}</p>
                <div class="mt-5 flex items-center justify-end gap-2">
                    <button type="button" wire:click="cancelRemove"
                        class="button-exp-no-fill !px-3 !py-2">{{ $cancelBtnText }}</button>
                    <button type="button" wire:click="confirmRemove"
                        class="button-exp-red-fill !px-3 !py-2">{{ $removeBtnText }}</button>
                </div>
            </div>
        </div>
    @endif
    @once
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                function syncStopsEdgeCities() {
                    const originInput = document.querySelector('input[name="origin[label]"]');
                    const destinationInput = document.querySelector('input[name="destination[label]"]');
                    const originLabel = document.getElementById('px-stops-origin-city');
                    const destinationLabel = document.getElementById('px-stops-destination-city');

                    if (originLabel) {
                        originLabel.textContent = originInput?.value || '';
                    }
                    if (destinationLabel) {
                        destinationLabel.textContent = destinationInput?.value || '';
                    }
                }

                // Function to find input by checking all inputs and matching the name pattern
                function findInputByName(namePattern) {
                    const inputs = document.querySelectorAll('input[type="text"]');
                    for (const input of inputs) {
                        if (input.name && input.name.includes(namePattern)) {
                            return input;
                        }
                    }
                    return null;
                }

                // Enhanced sync function that tries multiple methods
                function syncStopsEdgeCitiesEnhanced() {
                    const originLabel = document.getElementById('px-stops-origin-city');
                    const destinationLabel = document.getElementById('px-stops-destination-city');

                    // Try to find origin input
                    let originInput = document.querySelector('input[name="origin[label]"]');
                    if (!originInput) {
                        originInput = findInputByName('origin[label]');
                    }
                    // Also check for Livewire wire:model bound inputs
                    if (!originInput) {
                        const livewireOrigin = document.querySelector('[wire\\:model*="query"][name*="origin"]');
                        if (livewireOrigin) {
                            originInput = livewireOrigin;
                        }
                    }

                    // Try to find destination input
                    let destinationInput = document.querySelector('input[name="destination[label]"]');
                    if (!destinationInput) {
                        destinationInput = findInputByName('destination[label]');
                    }
                    // Also check for Livewire wire:model bound inputs
                    if (!destinationInput) {
                        const livewireDestination = document.querySelector(
                            '[wire\\:model*="query"][name*="destination"]');
                        if (livewireDestination) {
                            destinationInput = livewireDestination;
                        }
                    }

                    if (originLabel && originInput) {
                        originLabel.textContent = originInput.value || '';
                    }
                    if (destinationLabel && destinationInput) {
                        destinationLabel.textContent = destinationInput.value || '';
                    }
                }

                function syncAddStopButtonState() {
                    const addStopButton = document.querySelector('[data-add-stop-button]');
                    if (!addStopButton) {
                        return;
                    }

                    const stopRows = document.querySelectorAll('.stop-row');
                    if (stopRows.length === 0) {
                        addStopButton.disabled = false;
                        return;
                    }

                    const hasIncompleteRow = Array.from(stopRows).some(function(row, index) {
                        const cityIdInput = row.querySelector('input[name="stops[' + index + '][city_id]"]');
                        const departureInput = row.querySelector('[data-stop-departure-value="' + index + '"]');
                        const pickupDropoffInput = row.querySelector(
                            'textarea[name="stops[' + index + '][pickup_dropoff_location]"]'
                        );

                        const hasCity = !!(cityIdInput && cityIdInput.value && cityIdInput.value.trim() !== '');
                        const hasDeparture = !!(departureInput && departureInput.value && departureInput.value.trim() !== '');
                        const hasPickupInfo = !!(pickupDropoffInput && pickupDropoffInput.value && pickupDropoffInput.value.trim() !== '');

                        return !(hasCity && hasDeparture && hasPickupInfo);
                    });

                    addStopButton.disabled = hasIncompleteRow;
                }

                // Listen for input changes
                document.addEventListener('input', function(event) {
                    const target = event.target;
                    if (!(target instanceof HTMLInputElement) && !(target instanceof HTMLTextAreaElement)) {
                        return;
                    }
                    if (target.name && (target.name.includes('origin[label]') || target.name.includes(
                            'destination[label]'))) {
                        syncStopsEdgeCitiesEnhanced();
                    }
                    syncAddStopButtonState();
                });

                document.addEventListener('change', function(event) {
                    const target = event.target;
                    if (!(target instanceof HTMLInputElement) && !(target instanceof HTMLTextAreaElement)) {
                        return;
                    }

                    syncAddStopButtonState();
                });

                // Use Livewire hooks if available
                if (window.Livewire) {
                    // Hook into Livewire initialization
                    if (typeof window.Livewire.hook === 'function') {
                        window.Livewire.hook('message.processed', function() {
                            setTimeout(syncStopsEdgeCitiesEnhanced, 100);
                        });
                    }

                    // Also listen for component initialization
                    document.addEventListener('livewire:load', function() {
                        setTimeout(syncStopsEdgeCitiesEnhanced, 200);
                    });
                }

                // Initial sync with multiple attempts to catch Livewire initialization
                syncStopsEdgeCitiesEnhanced();
                syncAddStopButtonState();

                // Retry after a short delay to catch Livewire components that load later
                setTimeout(syncStopsEdgeCitiesEnhanced, 300);
                setTimeout(syncStopsEdgeCitiesEnhanced, 600);
                setTimeout(syncStopsEdgeCitiesEnhanced, 1000);
                setTimeout(syncAddStopButtonState, 300);
                setTimeout(syncAddStopButtonState, 600);
                setTimeout(syncAddStopButtonState, 1000);

                // Initialize flatpickr for stop departure date/time fields
                function initializeStopDatePickers() {
                    if (typeof flatpickr === 'undefined') {
                        return;
                    }

                    function findStopValueInput(index) {
                        return document.querySelector('[data-stop-departure-value="' + index + '"]');
                    }

                    function syncStopDepartureValue(index, value) {
                        const hiddenInput = findStopValueInput(index);
                        if (!hiddenInput) {
                            return;
                        }

                        hiddenInput.value = value || '';
                        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    // Find all stop departure inputs (date + time combined)
                    const stopDepartureInputs = document.querySelectorAll('[data-stop-departure-picker]');
                    stopDepartureInputs.forEach(function(input) {
                        const index = input.dataset.stopDeparturePicker;
                        const hiddenInput = findStopValueInput(index);
                        const currentValue = hiddenInput ? (hiddenInput.value || '') : (input.value || '');
                        const existingInstance = input._flatpickr;

                        if (existingInstance) {
                            if (currentValue !== existingInstance.input.value) {
                                existingInstance.setDate(currentValue, false, 'Y-m-d H:i');
                            }
                            return;
                        }

                        flatpickr(input, {
                            enableTime: true,
                            dateFormat: 'Y-m-d H:i',
                            altInput: true,
                            altFormat: 'F d, Y at H:i',
                            minDate: 'today',
                            time_24hr: true,
                            minuteIncrement: 5,
                            closeOnSelect: true,
                            disableMobile: true,
                            allowInput: true,
                            defaultDate: currentValue || null,
                            onChange: function(selectedDates, dateStr, instance) {
                                syncStopDepartureValue(index, dateStr);
                                instance.close();
                            },
                            onValueUpdate: function(selectedDates, dateStr, instance) {
                                syncStopDepartureValue(index, dateStr);
                            },
                        });
                    });
                }

                // Initialize on load
                initializeStopDatePickers();
                syncAddStopButtonState();

                // Re-initialize after Livewire updates
                if (window.Livewire) {
                    if (typeof window.Livewire.hook === 'function') {
                        window.Livewire.hook('message.processed', function() {
                            setTimeout(function() {
                                initializeStopDatePickers();
                                syncAddStopButtonState();
                            }, 100);
                        });
                    }
                }
            });
        </script>
    @endonce
</div>
