@php
    $iconClass = $iconClass ?? 'w-8 h-8';
    $rideOptions = [];
    
    foreach ($rideFeatureOptions as $slug=>$optionGroup) {
        if(!in_array($slug, ['luggage_size', 'smoking_allowed', 'pets_allowed'])) continue;
        
        if($slug == 'luggage_size') $label = $rideDetailPage->luggage_label;
        elseif($slug == 'pets_allowed') $label = $rideDetailPage->pets_label;
        elseif($slug == 'smoking_allowed') $label = $rideDetailPage->smoking_label;
        else $label = "";
        
        foreach ($optionGroup as $id => $option) {
            if(in_array($option->id, [(int) $ride->smoke, (int) $ride->animal_friendly, (int) $ride->luggage])) {
                $option->label = $label;
                $rideOptions[] = $option;
            }

        }
    }
@endphp


@foreach ($rideOptions as $option)
    <div class="flex items-center gap-2">
        <img class="{{ $iconClass ?? 'w-8 h-8' }}"
            src="{{ asset('home_page_icons/' . $option->icon) }}"
            alt="">
        <p class="font-semibold flex items-center gap-1">
            {{ $option->label }}: {{ $option->name }}
        </p>
        @if ($option->tooltip !== '')
        <span class="h-4 w-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
            class="cursor-help"
            data-tippy-content="{{ $option->tooltip }}" viewBox="0 0 16 16">
            <path
            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
            </svg>
        </span>
        @endif
    </div>
@endforeach
