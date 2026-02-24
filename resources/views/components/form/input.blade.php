@props([
    'type' => 'text',
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}">{{ $label }} @if($required) <span class="text-red-500">*</span> @endif</label>
    @endif

    <div class="{{ $type === 'password' ? 'relative' : '' }}">
        <input 
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $attributes->merge([
                'class' => 'form-control can-exp-input rounded border border-gray-300 ' . ($errors->has($name) ? 'is-invalid' : '')
            ]) }}
            autocomplete="off"
        >

        @if($type === 'password')
            <span class="password-toggle-icon absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" data-target="{{ $name }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-600">
                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                    <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                </svg>
            </span>
        @endif
    </div>

    @error($name)
        <div class="tooltip-error shadow-lg">
            {{ $message }}
        </div>
    @enderror
</div>
