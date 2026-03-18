@php
    $iconClass = $iconClass ?? 'w-8 h-8';
    $optionGroups = [
        // 'booking_method' => 'booking_method',
        // 'payment_method' => 'payment_method',
        'smoke' => 'smoking_allowed',
        'animal_friendly' => 'pets_allowed',
        'luggage' => 'luggage_size',
    ];
    $searchOptionGroups = $searchOptionGroups ?? collect();
@endphp
@foreach ($optionGroups as $rideField => $groupKey)
    @foreach (data_get($searchOptionGroups->get($groupKey), 'options', []) as $option)
        @php
            $selectedValue = data_get($ride ?? null, $rideField . '.features_setting_id')
                ?? data_get($ride ?? null, $rideField);

            $label = data_get($option, 'display_label');
            if($groupKey == 'luggage_size') $label = $rideDetailPage->luggage_label . ' ' . $label;
            if($groupKey == 'smoking_allowed') $label = $rideDetailPage->smoking_label . ' ' . $label;
            if($groupKey == 'pets_allowed') $label = $rideDetailPage->pets_label . ' ' . $label;
            $tooltip = data_get($option, 'display_description');
        @endphp

        @if ($selectedValue == data_get($option, 'features_setting_id') && !empty(data_get($option, 'icon')))
            <div class="flex items-center gap-2">
                <img
                    class="{{ $iconClass }}"
                    src="{{ asset('home_page_icons/' . data_get($option, 'icon')) }}"
                    alt=""
                >
                <p class="font-semibold flex items-center gap-2">
                    {{ $label }}
                    @if ($tooltip !== '')
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-exclamation-circle-fill text-black cursor-help inline-block"
                            data-tippy-content="{{ $tooltip }}" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                        </svg>
                    @endif
                </p>
            </div>
            @break
        @endif
    @endforeach
@endforeach
