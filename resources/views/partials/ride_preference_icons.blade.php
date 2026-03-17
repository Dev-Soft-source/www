@php
    $iconClass = $iconClass ?? 'w-8 h-8';
    $optionGroups = [
        'booking_method' => 'booking_method',
        'payment_method' => 'payment_method',
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
        @endphp

        @if ($selectedValue == data_get($option, 'features_setting_id') && !empty(data_get($option, 'icon')))
            <img
                class="{{ $iconClass }}"
                src="{{ asset('home_page_icons/' . data_get($option, 'icon')) }}"
                alt=""
            >
            @break
        @endif
    @endforeach
@endforeach
