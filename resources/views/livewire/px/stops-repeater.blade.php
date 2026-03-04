<div class="space-y-3">
    <label id="px-stops-origin-city" class="block text-sm font-semibold mb-1 origin_city">{{ $originLabel }}</label>
    <label class="block text-sm font-semibold mb-1">Stops Along the Way</label>
    @foreach($stops as $index => $stop)
        <div class="flex items-start gap-2 ml-6">
            <div class="flex-1 min-w-0">
                @livewire('px.city-autocomplete', [
                    'field' => "stops[$index]",
                    'placeholder' => 'Stop city (in route order)',
                    'initialLabel' => $stop['label'] ?? '',
                    'initialCityId' => $stop['city_id'] ?? null,
                ], key('px-stop-city-' . $index . '-' . md5(($stop['label'] ?? '') . '|' . ($stop['city_id'] ?? ''))))
                <input type="hidden" name="stops[{{ $index }}][is_pickup]" value="1">
                <input type="hidden" name="stops[{{ $index }}][is_dropoff]" value="1">
                <input type="hidden" name="stops[{{ $index }}][price_delta_minor]" value="{{ (int) ($stop['price_delta_minor'] ?? 0) }}">
            </div>
            <button
                type="button"
                wire:click="requestRemove({{ $index }})"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 hover:text-gray-800"
                aria-label="Remove stop"
                title="Remove stop"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endforeach
    <label id="px-stops-destination-city" class="block text-sm font-semibold mb-1 destination_city">{{ $destinationLabel }}</label>
    <button type="button" wire:click="addStop" class="button-exp-no-fill mt-1">Add a spot</button>

    @if(!is_null($pendingRemoveIndex))
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="text-lg font-semibold text-gray-900">Remove this stop?</h3>
                    <button type="button" wire:click="cancelRemove" class="text-gray-500 hover:text-gray-700">x</button>
                </div>
                <p class="mt-3 text-sm text-gray-600">Are you sure you want to delete this stop row?</p>
                <div class="mt-5 flex items-center justify-end gap-2">
                    <button type="button" wire:click="cancelRemove" class="button-exp-no-fill !px-3 !py-2">Cancel</button>
                    <button type="button" wire:click="confirmRemove" class="button-exp-fill !px-3 !py-2">Delete</button>
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
                        const livewireDestination = document.querySelector('[wire\\:model*="query"][name*="destination"]');
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

                // Listen for input changes
                document.addEventListener('input', function(event) {
                    const target = event.target;
                    if (!(target instanceof HTMLInputElement)) {
                        return;
                    }
                    if (target.name && (target.name.includes('origin[label]') || target.name.includes('destination[label]'))) {
                        syncStopsEdgeCitiesEnhanced();
                    }
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
                
                // Retry after a short delay to catch Livewire components that load later
                setTimeout(syncStopsEdgeCitiesEnhanced, 300);
                setTimeout(syncStopsEdgeCitiesEnhanced, 600);
                setTimeout(syncStopsEdgeCitiesEnhanced, 1000);
            });
        </script>
    @endonce
</div>
