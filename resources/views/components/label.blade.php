@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-gray-900 font-FuturaMdCnBT']) }}>
    {{ $value ?? $slot }}
</label>
