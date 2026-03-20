@foreach (($features ?? []) as $feature)

@continue((int)$feature == 1 || (int)$feature == 2)
    @php
    
        $featureOption = null;
        $featureLabel = $feature;

        if (is_numeric($feature)) {
            $featureOption = $featureOptionsById[(int) $feature] ?? null;
            $featureLabel = $featureOption['label'] ?? $feature;
        } else {
            $featureOption = $featureOptionsByLabel[$feature] ?? null;
            $featureLabel = $featureOption['label'] ?? $feature;
        }

        $featureTooltip = $featureOption['tooltip'] ?? '';
    @endphp
    <div class="flex items-center gap-2">
        @if (!empty($featureOption['icon']))
            <img class="{{ $iconClass ?? 'w-8 h-8' }}"
                src="{{ asset('home_page_icons/' . $featureOption['icon']) }}"
                alt="">
        @else
            <input id="wi-fi" type="checkbox" name="features[]" value="" checked disabled
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
        @endif
        <p class="font-semibold flex items-center gap-1">
            {{ $featureLabel }}
            @if ($featureTooltip !== '')
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-exclamation-circle-fill text-black cursor-help inline-block"
                    data-tippy-content="{{ $featureTooltip }}" viewBox="0 0 16 16">
                    <path
                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                </svg>
            @endif
        </p>
    </div>
@endforeach
