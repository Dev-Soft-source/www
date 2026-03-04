@extends('layouts.template')

@section('content')
    @php
        $fromLabel = $fromStop->label ?? 'N/A';
        $toLabel = $toStop->label ?? 'N/A';
        $perSeatMinor = (int) ($segmentPriceMinor ?? 0);
        $currencyCode = strtoupper((string) ($ride->currency ?? ($selectedCurrency ?? 'USD')));
        $currencyMap = ['USD' => '$', 'CAD' => 'C$'];
        $currencySymbol = $currencyMap[$currencyCode] ?? ($currencyCode . ' ');
    @endphp

    <div class="container mx-auto my-10 px-4">
        <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-xl shadow p-6 md:p-8">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-FuturaMdCnBT text-primary mb-1">PX Booking</h1>
                    <p class="text-sm text-gray-600">Book a full ride or a sub-ride section and prepare payment.</p>
                </div>
                <a href="{{ route('px.ride_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $ride->id, 'from_stop_id' => $fromStop->id, 'to_stop_id' => $toStop->id]) }}"
                    class="button-exp-no-fill whitespace-nowrap">
                    Back to Ride
                </a>
            </div>

            <div class="border border-gray-200 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Selected Route Section</h2>
                <p class="text-sm text-gray-600 mb-2">
                    <strong>Parent ride:</strong> {{ $ride->route->origin_label ?? 'N/A' }} -> {{ $ride->route->destination_label ?? 'N/A' }}
                </p>
                <p class="text-primary text-xl font-FuturaMdCnBT">{{ $fromLabel }} -> {{ $toLabel }}</p>
                @if ($segmentStops->isNotEmpty())
                    <p class="text-sm text-gray-600 mt-2">
                        <strong>Stops in this section:</strong> {{ $segmentStops->pluck('label')->join(', ') }}
                    </p>
                @endif
                <p class="text-sm text-gray-600 mt-2">
                    <strong>Booking mode:</strong> {{ $bookingModeCode ?: 'N/A' }} |
                    <strong>Payment method:</strong> {{ $bookingMethodLabel ?: 'N/A' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold mb-1 required">Seats</label>
                    <input id="px-booking-seats" type="number" min="1" max="{{ max(1, (int) $ride->seats_available) }}" value="1"
                        class="w-full rounded border-gray-300">
                    <p class="text-xs text-gray-500 mt-1">Available: {{ (int) $ride->seats_available }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Price</label>
                    <div class="rounded border border-gray-200 p-3 bg-gray-50">
                        <p class="text-sm text-gray-700">{{ $currencySymbol }}<span id="px-booking-per-seat">{{ number_format($perSeatMinor / 100, 2) }}</span> per seat</p>
                        <p class="text-lg font-semibold text-primary">Total: {{ $currencySymbol }}<span id="px-booking-total">{{ number_format($perSeatMinor / 100, 2) }}</span></p>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Saved Cards</h2>
                @if ($cards->isNotEmpty())
                    <div class="space-y-2">
                        @foreach ($cards as $card)
                            <label class="flex items-center gap-3 text-sm">
                                <input type="radio" name="card_id" value="{{ $card->id }}" @checked($loop->first)>
                                <span>
                                    {{ $card->card_type ?: 'Card' }}
                                    @if ($card->card_number)
                                        ending {{ substr((string) $card->card_number, -4) }}
                                    @endif
                                    @if ((string) $card->primary_card === '1')
                                        (Primary)
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-600">No saved cards found. Add a card in payment options before paying.</p>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button id="px-pay-now" type="button" class="button-exp-fill">Pay with Selected Card</button>
                <a href="{{ route('my_cards', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="button-exp-no-fill">Manage Cards</a>
                <span id="px-booking-status" class="text-sm text-gray-600"></span>
            </div>
            <p class="text-xs text-gray-500 mt-3">Charges the selected saved card for this route section.</p>
        </div>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const seatsInput = document.getElementById('px-booking-seats');
        const perSeatEl = document.getElementById('px-booking-per-seat');
        const totalEl = document.getElementById('px-booking-total');
        const payNowBtn = document.getElementById('px-pay-now');
        const statusEl = document.getElementById('px-booking-status');
        const perSeatMinor = {{ (int) $perSeatMinor }};
        const currencySymbol = @json($currencySymbol);
        
        const fromStopId = {{ (int) ($fromStop->id ?? 0) }};
        const toStopId = {{ (int) ($toStop->id ?? 0) }};

        function toAmountMinor(seats) {
            const safeSeats = Math.max(1, parseInt(seats || '1', 10) || 1);
            return perSeatMinor * safeSeats;
        }

        function formatMajor(minorValue) {
            return (minorValue / 100).toFixed(2);
        }

        function syncTotals() {
            const amountMinor = toAmountMinor(seatsInput?.value);
            if (totalEl) {
                totalEl.textContent = formatMajor(amountMinor);
            }
        }

        if (seatsInput) {
            seatsInput.addEventListener('input', syncTotals);
        }
        syncTotals();

        if (payNowBtn) {
            payNowBtn.addEventListener('click', async function () {
                const amountMinor = toAmountMinor(seatsInput?.value);
                const selectedCard = document.querySelector('input[name="card_id"]:checked');
                if (!selectedCard) {
                    if (statusEl) {
                        statusEl.textContent = 'Please select a saved card first.';
                    }
                    return;
                }
                if (statusEl) {
                    statusEl.textContent = 'Processing payment...';
                }

                try {
                    const response = await fetch('{{ route('px.booking.pay', ['lang' => optional($selectedLanguage)->abbreviation]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            from_stop_id: fromStopId,
                            to_stop_id: toStopId,
                            card_id: parseInt(selectedCard.value, 10),
                            seats: parseInt(seatsInput?.value || '1', 10),
                        }),
                    });

                    const payload = await response.json();
                    if (!response.ok) {
                        throw new Error(payload?.message || 'Payment failed');
                    }
                    if (statusEl) {
                        statusEl.textContent = `Payment successful. Charged ${currencySymbol}${formatMajor(amountMinor)}.`;
                    }
                } catch (error) {
                    if (statusEl) {
                        statusEl.textContent = error?.message || 'Could not process payment.';
                    }
                }
            });
        }
    });
</script>
@endsection
