@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 container mx-auto my-14">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 md:col-span-9 shadow">
        <div class="border-b pb-2">
            <h1 class="mb-0">Booking credits</h1>
        </div>

        <div class="mt-4 w-full">
            <p class="text-lg py-2">${{ $user->booking_credits }} CAD</p>
        </div>

        <div class="border-b py-2 mt-4">
            <h2>Purchase booking credits</h2>
        </div>

        <form method="POST" action="{{ route('booking.credits.update',$user->id) }}">
            @csrf
            @method('PUT')
            <div class="grid sm:grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                @foreach ($packages as $package)
                    <div class="package-item hover:border-blue-600 p-6 border w-full rounded bg-white shadow cursor-pointer" data-package-id="{{ $package->id }}">
                        <div>
                            <p class="text-blue-600 text-lg">Buy {{ $package->credits_buy }} credit Get {{ $package->credits_get }} credit</p>
                            <p>${{ $package->credits_price }} CAD</p>
                        </div>
                    </div>
                @endforeach
            </div>
    
            <div class="relative md:w-1/2 w-full mt-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 absolute top-2.5 left-3 text-gray-900">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                </svg>
                <input type="text" name="card" class="block mt-1 border p-2.5 pl-12 text-gray-900 placeholder-gray-900 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" placeholder="Card number">
                @error('card')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="md:col-span-2 mt-6">
                <button type="submit" name="card" class="button-exp-fill">Buy Now</button>
            </div>
        </form>
    </div>
</div>
    
@endsection

@section('script')

<script>
    // Get all package elements
    const packageItems = document.querySelectorAll('.package-item');

    // Add a click event listener to each package element
    packageItems.forEach((packageItem) => {
        packageItem.addEventListener('click', () => {
            // Remove the 'selected' class from all package elements
            packageItems.forEach((item) => {
                item.classList.remove('border-blue-600');
            });

            // Add the 'selected' class to the clicked package element
            packageItem.classList.add('border-blue-600');
        });
    });
</script>
    
@endsection