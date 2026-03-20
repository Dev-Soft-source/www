@php
    $selectedFeatureIds = is_array($rideFeatures ?? null) ? $rideFeatures : explode('=', (string) ($rideFeatures ?? ''));
    $selectedFeatureIds = array_map('strval', array_filter($selectedFeatureIds));
    $iconClass = $iconClass ?? 'w-8 h-8';
@endphp

@foreach (($featureOptions ?? collect()) as $featureOption)
    @if (in_array((string) $featureOption['id'], $selectedFeatureIds, true) 
    // && !empty($featureOption['icon'])
    )
    @continue($featureOption['id'] == 1 || $featureOption['id'] == 2)
        <img
            class="{{ $iconClass }}"
            src="{{ asset('home_page_icons/' . $featureOption['icon']) }}"
            alt=""
            @if (!empty($featureOption['tooltip'])) data-tippy-content="{{ $featureOption['tooltip'] }}" @endif
        >
    @endif
@endforeach
