@props([
    'text',
    'position' => 'top', // top | bottom | left | right | auto
])

<span 
    class="tooltip-trigger" 
    data-tippy-content="{{ $text }}"
    data-tippy-placement="{{ $position === 'auto' ? 'auto' : $position }}"
    {{ $attributes }}
>
    {{ $slot }}
</span>
