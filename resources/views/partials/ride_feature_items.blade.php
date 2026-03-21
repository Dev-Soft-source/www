@php
    $selectedFeatureIds = is_array($features ?? null) ? $features : explode('=', (string) ($features ?? ''));
    $selectedFeatureIds = array_map('strval', array_filter($selectedFeatureIds));
@endphp

@foreach (($rideFeatureOptions['features'] ?? collect()) as $featureOption)
    @continue($featureOption->slug == 'pink_rides' || $featureOption->slug == 'extra_care_rides')

    @if (in_array((string) $featureOption->id, $selectedFeatureIds, true))

        <div class="flex items-center gap-2">

            <img class="{{ $iconClass ?? 'w-8 h-8' }}"
                src="{{ asset('home_page_icons/' . $featureOption->icon) }}"
                alt="">

            <p class="font-semibold flex items-center gap-1">
                {{ $featureOption->name }}
            </p>
            @if ($featureOption->tooltip !== '')
            <span class="h-4 w-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="cursor-help"
                    data-tippy-content="{{ $featureOption->tooltip }}" viewBox="0 0 16 16">
                    <path
                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                </svg>
            </span>
            @endif
        </div>
    @endif
@endforeach
