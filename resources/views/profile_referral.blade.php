@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        <div class="border-b pb-2 mt-5">
            <h1 class="mb-0">{{ $referralSettingPage->main_heading ?? "Refer a friend"}} </h1>
        </div>

        <p class="text-gray-500 text-sm mt-1">{!! $referralSettingPage->referral_description ?? "At ProximaRide, we value great experiences. That's why we've introduced a referral rewards system to thank you for bringing trustworthy users to our community. <br><br>Refer a passenger who maintains a good reputation (no negative reviews), and you'll earn 1 point. Refer a driver with a positive attitude (no negative reviews), and you'll earn 5 points." !!}</p>

        <div class="mt-4 border border-gray-300 bg-white rounded flex justify-center items-center md:py-10 w-full">
            <div class="md:w-4/5 w-full text-center p-4">
                <p class="text-2xl font-medium font-FuturaMdCnBT">{{ $referralSettingPage->your_referral_url_label ?? "Your Referral URL"}}</p>
                <div class="relative bg-gray-50 mt-2">
                    <div class="absolute right-2 top-1.5 pl-2 border-l border-gray-300">
                        <svg id="copyIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-gray-500 cursor-pointer hover:text-primary transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                        </svg>
                    </div>
                    <input type="text" id="referralUrl" value="{{ url('/signup-with-referral/' . auth()->user()->referral_uuid) }}" readonly
                        class="block mt-1 border p-1.5 w-full rounded text-sm md:text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 bg-gray-50 pr-12">
                </div>
                <p id="copyMessage" class="text-green-600 text-sm mt-2 hidden">Copied to clipboard!</p>
            </div>
        </div>

        <div class="border-b py-2 mt-4">
            <h2 class="text-primary">{{ $referralSettingPage->my_referral_text ?? "My Referrals"}}</h2>
        </div>
        @if ($referrals->count() > 0)
            <div class="overflow-auto">
                <table class="border border-collapse overflow-auto w-full mt-6">
                    <thead class="">
                        <tr>
                            <th class="p-2 border text-lg text-left">{{ $referralSettingPage->account_id_label ?? "#ID"}}</th>
                            <th class="p-2 border text-lg text-left">{{ $referralSettingPage->user_label ?? "User"}}</th>
                            <th class="p-2 border text-lg text-left">{{ $referralSettingPage->registered_on_label ?? "Registered on"}}</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($referrals as $referral)
                            <tr>
                                <td class="p-2 border text-lg">{{ $referral->id }}</td>
                                <td class="p-2 border text-lg">{{ $referral->user->first_name ?? '' }} {{ $referral->user->last_name ?? '' }}</td>
                                <td class="p-2 border text-lg">{{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="mt-6 text-center text-gray-500 py-8">
                <p>You haven't referred anyone yet. Share your referral URL to get started!</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('script')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyIcon = document.getElementById('copyIcon');
        const inputField = document.getElementById('referralUrl');
        const copyMessage = document.getElementById('copyMessage');

        copyIcon.addEventListener('click', function () {
            // Select the input field
            inputField.select();
            inputField.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the input field to the clipboard
            navigator.clipboard.writeText(inputField.value).then(function() {
                // Show success message
                copyMessage.classList.remove('hidden');
                setTimeout(function() {
                    copyMessage.classList.add('hidden');
                }, 2000);
            }).catch(function() {
                // Fallback for older browsers
                document.execCommand('copy');
                copyMessage.classList.remove('hidden');
                setTimeout(function() {
                    copyMessage.classList.add('hidden');
                }, 2000);
            });
        });
    });
</script>

@endsection
