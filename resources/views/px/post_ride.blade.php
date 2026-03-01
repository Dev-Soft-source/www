@extends('layouts.template')

@section('title', 'Post PX Ride')

@section('content')
<div class="container mx-auto my-10 px-4">
    <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-xl shadow p-6 md:p-8">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-FuturaMdCnBT text-primary mb-1">Post a PX Ride</h1>
                <p class="text-sm text-gray-600">This creates rides in the new <code>px_*</code> schema.</p>
            </div>
            <a
                href="{{ route('post_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                class="button-exp-no-fill whitespace-nowrap"
            >
                Back to Legacy Post Ride
            </a>
        </div>

        @if(session('message'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 text-green-700 px-4 py-3">
                {{ session('message') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 text-red-700 px-4 py-3">
                <p class="font-semibold mb-1">Please fix the errors below:</p>
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('px.post_ride.store', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="space-y-8">
            @csrf

            <section>
                <h2 class="text-xl font-FuturaMdCnBT text-gray-900 mb-4">Route</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Origin</label>
                        <input name="origin[label]" value="{{ old('origin.label') }}" type="text" class="w-full rounded border-gray-300" placeholder="City, station, or address" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Destination</label>
                        <input name="destination[label]" value="{{ old('destination.label') }}" type="text" class="w-full rounded border-gray-300" placeholder="City, station, or address" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Pick-up location (optional)</label>
                        <input name="origin[pickup_location]" value="{{ old('origin.pickup_location') }}" type="text" class="w-full rounded border-gray-300" placeholder="Exact pick-up point">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Drop-off location (optional)</label>
                        <input name="destination[dropoff_location]" value="{{ old('destination.dropoff_location') }}" type="text" class="w-full rounded border-gray-300" placeholder="Exact drop-off point">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-semibold mb-1">Ordered Intermediate Stops (optional)</label>
                    <textarea
                        name="stops_text"
                        rows="5"
                        class="w-full rounded border-gray-300"
                        placeholder="One stop per line, in ride order&#10;Example:&#10;B&#10;C&#10;D&#10;E"
                    >{{ old('stops_text') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Search will use stop order direction. Example: B → E valid, E → B invalid.
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-FuturaMdCnBT text-gray-900 mb-4">Schedule & Price</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Departure</label>
                        <input name="departure_at" value="{{ old('departure_at') }}" type="datetime-local" class="w-full rounded border-gray-300" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Estimated Arrival (optional)</label>
                        <input name="arrival_estimated_at" value="{{ old('arrival_estimated_at') }}" type="datetime-local" class="w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Seats Total</label>
                        <input name="seats_total" value="{{ old('seats_total', 1) }}" type="number" min="1" max="8" class="w-full rounded border-gray-300" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Price (minor units)</label>
                        <input name="price_minor" value="{{ old('price_minor') }}" type="number" min="0" class="w-full rounded border-gray-300" placeholder="e.g. 2500 = 25.00" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Currency</label>
                        <input name="currency" value="{{ old('currency', 'USD') }}" type="text" maxlength="3" class="w-full rounded border-gray-300 uppercase" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Timezone</label>
                        <input name="timezone" value="{{ old('timezone', 'UTC') }}" type="text" class="w-full rounded border-gray-300">
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-FuturaMdCnBT text-gray-900 mb-4">Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Vehicle</label>
                        <select name="vehicle_id" class="w-full rounded border-gray-300">
                            <option value="">No vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected((string)old('vehicle_id') === (string)$vehicle->id)>
                                    #{{ $vehicle->id }} - {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->liscense_no }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Visibility</label>
                        <select name="visibility" class="w-full rounded border-gray-300">
                            <option value="public" @selected(old('visibility') === 'public')>Public</option>
                            <option value="private" @selected(old('visibility') === 'private')>Private</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Status</label>
                        <select name="status" class="w-full rounded border-gray-300">
                            <option value="published" @selected(old('status') === 'published')>Published</option>
                            <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                        </select>
                    </div>

                </div>
            </section>

            <section>
                <h2 class="text-xl font-FuturaMdCnBT text-gray-900 mb-4">Ride Options (Multilingual)</h2>
                <div class="space-y-5">
                    @foreach($optionGroups as $group)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">{{ ucwords(str_replace('_', ' ', $group->code)) }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($group->options as $option)
                                    <label class="flex items-start gap-2 text-sm">
                                        @if($group->is_checkbox)
                                        <input
                                            type="checkbox"
                                            name="ride_option_ids[]"
                                            value="{{ $option->id }}"
                                            @checked(in_array($option->id, old('ride_option_ids', [])))
                                            class="mt-0.5"
                                        >
                                        @else
                                        <input
                                            type="radio"
                                            name="{{ $group->code }}"
                                            value="{{ $option->id }}"
                                            @checked(old($group->code) == $option->id)
                                            class="mt-0.5"
                                        >
                                        @endif
                                        <span class="flex">
                                            <span class="font-medium text-gray-800">{{ $option->display_label }}</span>
                                            <span class="cursor-help ml-2 w-4 h-4" data-tippy-content="{{ $option->display_description }}"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 17.75C12.4142 17.75 12.75 17.4142 12.75 17V11C12.75 10.5858 12.4142 10.25 12 10.25C11.5858 10.25 11.25 10.5858 11.25 11V17C11.25 17.4142 11.5858 17.75 12 17.75ZM12 7C12.5523 7 13 7.44772 13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7Z" fill="#1C274C"></path> </g></svg></span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section>
                <h2 class="text-xl font-FuturaMdCnBT text-gray-900 mb-4">Notes</h2>
                <textarea name="notes" rows="5" class="w-full rounded border-gray-300" placeholder="Optional ride notes">{{ old('notes') }}</textarea>
            </section>

            <div class="pt-2">
                <button type="submit" class="button-exp-fill">Post PX Ride</button>
            </div>
        </form>
    </div>
</div>
@endsection
