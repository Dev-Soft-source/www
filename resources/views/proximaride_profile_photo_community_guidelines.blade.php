@extends('layouts.template')

@section('content')
    <div class="container mx-auto my-10 md:my-14 px-4 pt-safe hideheader1">
        <div class="pb-2">
            <h1 class="text-2xl md:text-3xl font-FuturaBdCnBT text-blue-600 mb-4 text-center">ProximaRide Profile Photo Guidelines</h1>

            <p class="mb-4">To participate in Pink Rides or Extra-Care Rides as a driver or passenger, you must upload a profile photo that adheres to the following guidelines to ensure trust and safety within the ProximaRide community:</p>

            <ul class="list-disc pl-6 space-y-3 mb-6">
                <li><strong>Clear and Visible Face:</strong> The photo must clearly show your full face, with no obstructions (e.g., sunglasses, hats, or masks) to allow for easy identification.</li>
                <li><strong>Recent Image:</strong> The photo should be taken within the last 12 months to accurately represent your current appearance.</li>
                <li><strong>Solo Portrait:</strong> The image must feature only you, with no other people, pets, or objects to avoid confusion.</li>
                <li><strong>Neutral Background:</strong> Use a plain, well-lit background to keep the focus on your face and ensure clarity.</li>
                <li><strong>Appropriate Content:</strong> The photo must be professional and respectful, excluding offensive gestures, symbols, or attire that violates ProximaRide's community standards.</li>
                <li><strong>High Quality:</strong> The image should be high-resolution, well-lit, and not blurry to ensure recognizability.</li>
                <li><strong>No Filters or Edits:</strong> Avoid using filters, heavy editing, or alterations that change your natural appearance.</li>
            </ul>

            <p class="mb-6"><strong>Why This Matters:</strong> A clear and compliant profile photo builds trust and enhances safety by helping drivers and passengers verify each other's identity during rides.</p>

            <p class="mb-2"><strong>Important Reminders</strong></p>
            <ul class="list-disc pl-6 space-y-2 mb-6">
                <li>If the driver does not match their profile photo, passengers can refer to the options outlined on ProximaRide's "Policies & Disputes" page.</li>
                <li>If the passenger who arrives does not match the profile photo, the driver is not obligated to allow them into the vehicle. This will be treated as a no-show for that passenger.</li>
            </ul>

            <h2 class="text-2xl md:text-3xl font-FuturaBdCnBT text-blue-600 mb-4 mt-8 text-center">Here is a Good Example</h2>
            <div class="flex justify-center mb-6">
                <img class="max-w-full h-auto rounded-lg shadow-md" src="{{ asset('home_page_icons/1749147041-Profile Photo Guidelines-2.jpg') }}" alt="Good profile photo example" style="max-width: 400px;">
            </div>
        </div>
    </div>
@endsection
