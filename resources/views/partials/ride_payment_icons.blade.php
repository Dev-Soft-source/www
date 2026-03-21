@php
    $iconClass = $iconClass ?? 'w-8 h-8';
    $rideOptions = [];
    foreach ($rideFeatureOptions as $slug=>$optionGroup) {
        if($slug == 'features') continue;
        foreach ($optionGroup as $id => $option) {
            if(in_array($option->id, [(int) $ride->booking_method, (int) $ride->payment_method,  
            // (int) $ride->booking_type, 
            ])) {
                $rideOptions[] = $option;
            }
        }
    }
@endphp


@foreach ($rideOptions as $option)
    <img class="{{ $iconClass ?? 'w-8 h-8' }}" data-tippy-content="{{ $option->tooltip }}"
        src="{{ asset('home_page_icons/' . $option->icon) }}"
        alt="">
@endforeach

