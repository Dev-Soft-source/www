<?php

namespace App\Http\Controllers;

use App\Models\BookingPageSettingDetail;
use App\Models\Card;
use App\Models\City;
use App\Models\CoffeeWallet;
use App\Models\PxBooking;
use App\Models\PxOptionGroup;
use App\Models\PxRide;
use App\Models\PxRideStop;
use App\Models\PxTransaction;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod as StripePaymentMethod;
use Stripe\Stripe;

class PxBookingWebController extends Controller
{
    public function booking($lang = null, $from_stop_id = null, $to_stop_id = null)
    {
        $fromStop = PxRideStop::query()->find($from_stop_id);
        $toStop = PxRideStop::query()->find($to_stop_id);

        if (!$fromStop || !$toStop || (int) $fromStop->ride_id !== (int) $toStop->ride_id) {
            return redirect()
                ->route('px.search_ride', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Invalid booking segment.');
        }

        $ride = PxRide::query()
            ->with(['route', 'stops', 'driver', 'vehicle'])
            ->published()
            ->where('id', $fromStop->ride_id)
            ->first();

        if (!$ride) {
            return redirect()
                ->route('px.search_ride', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Ride not found or unavailable.');
        }

        $orderedStops = $ride->stops->sortBy('stop_order')->values()->all();
        $fromIndex = null;
        $toIndex = null;
        foreach ($orderedStops as $idx => $stop) {
            $stopId = (int) ($stop->id ?? 0);
            if ($stopId === (int) $from_stop_id) {
                $fromIndex = $idx;
            }
            if ($stopId === (int) $to_stop_id) {
                $toIndex = $idx;
            }
        }

        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return redirect()
                ->route('px.ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $ride->id])
                ->with('error', 'Invalid route section for booking.');
        }

        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;
        $ride->options->transform(function ($option) use ($selectedLangId, $defaultLangId) {
            $selected = $option->translations->firstWhere('language_id', $selectedLangId);
            $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
            $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
            $option->display_description = optional($selected)->description ?: optional($fallback)->description;
            return $option;
        });
        $optionGroups = PxOptionGroup::whereIn('code', ['booking_mode', 'booking_method'])
            ->with(['options' => function ($q) use ($selectedLangId, $defaultLangId) {
                $q->where('is_active', true)
                    ->with(['translations' => function ($tq) use ($selectedLangId, $defaultLangId) {
                        $tq->whereIn('language_id', array_filter([$selectedLangId, $defaultLangId]));
                    }]);
            }])
            ->get()
            ->keyBy('code');

        $bookingModeCode = $this->getOptionCode($optionGroups->get('booking_mode'), $ride->booking_mode, '');
        $bookingMethodCode = $this->getOptionCode($optionGroups->get('booking_method'), $ride->booking_method, '');
        $bookingModeLabel = $this->getOptionLabel($optionGroups->get('booking_mode'), $ride->booking_mode, $selectedLangId, $defaultLangId, 'N/A');
        $bookingMethodLabel = $this->getOptionLabel($optionGroups->get('booking_method'), $ride->booking_method, $selectedLangId, $defaultLangId, 'N/A');
        $segmentPriceMinor = $this->resolveMatchedSegmentPriceMinor($ride, null, null, '', '', $fromIndex, $toIndex);
        $segmentAvailableSeats = $ride->resolveSegmentAvailableSeats((int) $from_stop_id, (int) $to_stop_id);

        if ($segmentAvailableSeats <= 0) {
            return redirect()
                ->route('px.ride_detail', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $ride->id,
                    'from_stop_id' => (int) $from_stop_id,
                    'to_stop_id' => (int) $to_stop_id,
                ])
                ->with('error', 'Not enough available seats for this route section.');
        }

        $cards = Card::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('primary_card')
            ->orderByDesc('id')
            ->get();

        // Fetch booking fee related data
        $setting = SiteSetting::first();
        $getCoffeeCrBalance = CoffeeWallet::where('user_id', auth()->id())->sum('cr_amount');
        $getCoffeeDrBalance = CoffeeWallet::where('user_id', auth()->id())->sum('dr_amount');
        $coffeeBalance = $getCoffeeDrBalance - $getCoffeeCrBalance;

        $stateTax = 0;
        if ($setting && isset($setting->deduct_tax) && $setting->deduct_tax == "deduct_from_passenger" && $setting->tax_type == "state_wise_tax") {
            $fromCityId = $fromStop->city_id;
            if ($fromCityId) {
                $getFromState = City::with('state:id,tax')->where('id', $fromCityId)->where('status', '1')->first();
                if ($getFromState && $getFromState->state) {
                    $stateTax = $getFromState->state->tax ?? 0;
                }
            }
        }

        $bookingPage = BookingPageSettingDetail::where('language_id', $selectedLangId)->first();
        if (!$bookingPage && $defaultLangId) {
            $bookingPage = BookingPageSettingDetail::where('language_id', $defaultLangId)->first();
        }

        $postRidePage = $this->getPostRidePageWithSettingDetail();

        return view('px.booking', [
            'ride' => $ride,
            'fromStop' => $orderedStops[$fromIndex],
            'toStop' => $orderedStops[$toIndex],
            'segmentStops' => collect($orderedStops)->slice($fromIndex + 1, max(0, $toIndex - $fromIndex - 1))->values(),
            'segmentPriceMinor' => $segmentPriceMinor,
            'segmentAvailableSeats' => $segmentAvailableSeats,
            'bookingModeCode' => $bookingModeCode,
            'bookingModeLabel' => $bookingModeLabel,
            'bookingMethodCode' => $bookingMethodCode,
            'bookingMethodLabel' => $bookingMethodLabel,
            'cards' => $cards,
            'setting' => $setting,
            'coffeeBalance' => $coffeeBalance,
            'stateTax' => $stateTax,
            'bookingPage' => $bookingPage,
            'postRidePage' => $postRidePage,
        ]);
    }

    public function editBooking($lang = null, $id = null)
    {
        $booking = PxBooking::query()
            ->where('id', (int) $id)
            ->where('passenger_id', (int) auth()->id())
            ->whereNotIn('status', ['cancelled', 'refunded', 'failed'])
            ->with(['ride.route', 'ride.stops', 'ride.driver', 'ride.vehicle', 'ride.seatDetail'])
            ->first();

        if (!$booking || !$booking->ride) {
            return redirect()
                ->route('px.my_trips', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Booking not found.');
        }

        $ride = $booking->ride;
        if ($ride->status === 'cancelled' || ($ride->departure_at && $ride->departure_at <= now())) {
            return redirect()
                ->route('px.ride_detail', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $ride->id,
                    'from_stop_id' => (int) $booking->from_stop_id,
                    'to_stop_id' => (int) $booking->to_stop_id,
                ])
                ->with('error', 'This booking can no longer be updated.');
        }

        [$fromIndex, $toIndex] = $this->resolveRideStopIndexes($ride, (int) $booking->from_stop_id, (int) $booking->to_stop_id);

        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return redirect()
                ->route('px.ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $ride->id])
                ->with('error', 'Invalid route section for booking.');
        }

        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        $ride->options->transform(function ($option) use ($selectedLangId, $defaultLangId) {
            $selected = $option->translations->firstWhere('language_id', $selectedLangId);
            $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
            $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
            $option->display_description = optional($selected)->description ?: optional($fallback)->description;
            return $option;
        });

        $optionGroups = PxOptionGroup::whereIn('code', ['booking_mode', 'booking_method'])
            ->with(['options' => function ($q) use ($selectedLangId, $defaultLangId) {
                $q->where('is_active', true)
                    ->with(['translations' => function ($tq) use ($selectedLangId, $defaultLangId) {
                        $tq->whereIn('language_id', array_filter([$selectedLangId, $defaultLangId]));
                    }]);
            }])
            ->get()
            ->keyBy('code');

        $bookingModeCode = $this->getOptionCode($optionGroups->get('booking_mode'), $ride->booking_mode, '');
        $bookingMethodCode = $this->getOptionCode($optionGroups->get('booking_method'), $ride->booking_method, '');
        $bookingModeLabel = $this->getOptionLabel($optionGroups->get('booking_mode'), $ride->booking_mode, $selectedLangId, $defaultLangId, 'N/A');
        $bookingMethodLabel = $this->getOptionLabel($optionGroups->get('booking_method'), $ride->booking_method, $selectedLangId, $defaultLangId, 'N/A');
        $segmentPriceMinor = $this->resolveMatchedSegmentPriceMinor($ride, null, null, '', '', $fromIndex, $toIndex);
        $segmentAvailableSeats = $ride->resolveSegmentAvailableSeats((int) $booking->from_stop_id, (int) $booking->to_stop_id);

        $cards = Card::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('primary_card')
            ->orderByDesc('id')
            ->get();

        // Fetch booking fee related data
        $setting = SiteSetting::first();
        $getCoffeeCrBalance = CoffeeWallet::where('user_id', auth()->id())->sum('cr_amount');
        $getCoffeeDrBalance = CoffeeWallet::where('user_id', auth()->id())->sum('dr_amount');
        $coffeeBalance = $getCoffeeDrBalance - $getCoffeeCrBalance;

        $stateTax = 0;
        if ($setting && isset($setting->deduct_tax) && $setting->deduct_tax == "deduct_from_passenger" && $setting->tax_type == "state_wise_tax") {
            $fromCityId = $ride->stops->sortBy('stop_order')->values()->get($fromIndex)->city_id;
            if ($fromCityId) {
                $getFromState = City::with('state:id,tax')->where('id', $fromCityId)->where('status', '1')->first();
                if ($getFromState && $getFromState->state) {
                    $stateTax = $getFromState->state->tax ?? 0;
                }
            }
        }

        $bookingPage = BookingPageSettingDetail::where('language_id', $selectedLangId)->first();
        if (!$bookingPage && $defaultLangId) {
            $bookingPage = BookingPageSettingDetail::where('language_id', $defaultLangId)->first();
        }

        $postRidePage = $this->getPostRidePageWithSettingDetail();

        return view('px.booking', [
            'ride' => $ride,
            'fromStop' => $ride->stops->sortBy('stop_order')->values()->get($fromIndex),
            'toStop' => $ride->stops->sortBy('stop_order')->values()->get($toIndex),
            'segmentStops' => $ride->stops->sortBy('stop_order')->values()->slice($fromIndex + 1, max(0, $toIndex - $fromIndex - 1))->values(),
            'segmentPriceMinor' => $segmentPriceMinor,
            'segmentAvailableSeats' => $segmentAvailableSeats,
            'bookingModeCode' => $bookingModeCode,
            'bookingMethodCode' => $bookingMethodCode,
            'bookingModeLabel' => $bookingModeLabel,
            'bookingMethodLabel' => $bookingMethodLabel,
            'cards' => $cards,
            'existingBooking' => $booking,
            'isEditMode' => true,
            'setting' => $setting,
            'coffeeBalance' => $coffeeBalance,
            'stateTax' => $stateTax,
            'bookingPage' => $bookingPage,
            'postRidePage' => $postRidePage,
        ]);
    }

    public function updateBooking(Request $request, $lang = null, $id = null)
    {
        $booking = PxBooking::query()
            ->where('id', (int) $id)
            ->where('passenger_id', (int) auth()->id())
            ->whereNotIn('status', ['cancelled', 'refunded', 'failed'])
            ->with(['ride.stops'])
            ->first();

        if (!$booking || !$booking->ride) {
            return redirect()
                ->route('px.my_trips', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Booking not found.');
        }

        $validated = $this->validateBookingRequest($request, $booking->ride);

        $ride = $booking->ride;
        if ($ride->status === 'cancelled' || ($ride->departure_at && $ride->departure_at <= now())) {
            return redirect()
                ->route('px.booking.edit', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $booking->id,
                ])
                ->withInput()
                ->with('error', 'This booking can no longer be updated.');
        }

        [$fromIndex, $toIndex] = $this->resolveRideStopIndexes(
            $ride,
            (int) $booking->from_stop_id,
            (int) $booking->to_stop_id
        );
        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return redirect()
                ->route('px.booking.edit', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $booking->id,
                ])
                ->withInput()
                ->with('error', 'Invalid route section for booking.');
        }

        $updatedBooking = null;

        DB::transaction(function () use (&$updatedBooking, $booking, $ride, $validated, $fromIndex, $toIndex) {
            $bookingForUpdate = PxBooking::query()
                ->where('id', (int) $booking->id)
                ->lockForUpdate()
                ->first();
            $rideForUpdate = PxRide::query()
                ->with('stops')
                ->where('id', (int) $ride->id)
                ->lockForUpdate()
                ->first();

            if (
                !$bookingForUpdate
                || !$rideForUpdate
                || in_array((string) $bookingForUpdate->status, ['cancelled', 'refunded', 'failed'], true)
            ) {
                throw new \RuntimeException('This booking can no longer be updated.');
            }

            $segmentPriceMinor = $this->resolveMatchedSegmentPriceMinor($ride, null, null, '', '', $fromIndex, $toIndex);
            $requestedSeats = (int) $validated['seats'];
            $currentSeats = (int) $bookingForUpdate->seats;
            $seatDiff = $requestedSeats - $currentSeats;

            if ($seatDiff > 0 && $seatDiff > $rideForUpdate->resolveSegmentAvailableSeats(
                (int) $bookingForUpdate->from_stop_id,
                (int) $bookingForUpdate->to_stop_id
            )) {
                throw new \RuntimeException('Not enough available seats for this update.');
            }

            if ($seatDiff !== 0) {
                $rideForUpdate->adjustSegmentSeatAvailability(
                    (int) $bookingForUpdate->from_stop_id,
                    (int) $bookingForUpdate->to_stop_id,
                    $seatDiff
                );
            }

            $bookingForUpdate->seats = $requestedSeats;
            $bookingForUpdate->segment_price_minor = (int) $segmentPriceMinor;
            $bookingForUpdate->total_price_minor = (int) $segmentPriceMinor * $requestedSeats;

            if (!empty($validated['card_id'])) {
                $card = Card::query()
                    ->where('id', (int) $validated['card_id'])
                    ->where('user_id', (int) auth()->id())
                    ->first();
                if (!$card) {
                    throw new \RuntimeException('Selected card is invalid.');
                }
                $bookingForUpdate->card_id = (int) $card->id;
            }

            $meta = is_array($bookingForUpdate->meta) ? $bookingForUpdate->meta : [];
            $meta['updated_at'] = now()->toDateTimeString();
            $meta['updated_by'] = 'passenger_web';
            $meta['seats'] = $requestedSeats;
            if (isset($validated['driver_message'])) {
                $meta['driver_message'] = $validated['driver_message'];
            }
            $bookingForUpdate->meta = $meta;

            $bookingForUpdate->save();
            $updatedBooking = $bookingForUpdate;
        });

        return redirect()
            ->route('px.ride_detail', [
                'lang' => optional($this->selectedLanguage)->abbreviation,
                'id' => $ride->id,
                'from_stop_id' => (int) $booking->from_stop_id,
                'to_stop_id' => (int) $booking->to_stop_id,
            ])
            ->with('success', 'Booking updated successfully.');
    }

    public function cancelBooking(Request $request, $lang = null, $id = null)
    {
        $booking = PxBooking::query()
            ->where('id', (int) $id)
            ->where('passenger_id', (int) auth()->id())
            ->with('ride')
            ->first();

        if (!$booking || !$booking->ride) {
            return redirect()
                ->route('px.my_trips', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Booking not found.');
        }

        if (in_array((string) $booking->status, ['cancelled', 'refunded', 'failed'], true)) {
            return redirect()
                ->route('px.ride_detail', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $booking->ride_id,
                    'from_stop_id' => (int) $booking->from_stop_id,
                    'to_stop_id' => (int) $booking->to_stop_id,
                ])
                ->with('error', 'Booking is already cancelled.');
        }

        DB::transaction(function () use ($booking) {
            $bookingForUpdate = PxBooking::query()
                ->where('id', (int) $booking->id)
                ->lockForUpdate()
                ->first();
            $rideForUpdate = PxRide::query()
                ->with('stops')
                ->where('id', (int) $booking->ride_id)
                ->lockForUpdate()
                ->first();

            if (
                !$bookingForUpdate
                || !$rideForUpdate
                || in_array((string) $bookingForUpdate->status, ['cancelled', 'refunded', 'failed'], true)
            ) {
                return;
            }

            $rideForUpdate->adjustSegmentSeatAvailability(
                (int) $bookingForUpdate->from_stop_id,
                (int) $bookingForUpdate->to_stop_id,
                -(int) $bookingForUpdate->seats
            );

            $meta = is_array($bookingForUpdate->meta) ? $bookingForUpdate->meta : [];
            $meta['cancelled_at'] = now()->toDateTimeString();
            $meta['cancelled_by'] = 'passenger_web';
            $bookingForUpdate->meta = $meta;
            $bookingForUpdate->status = 'cancelled';
            $bookingForUpdate->save();
        });

        return redirect()
            ->route('px.ride_detail', [
                'lang' => optional($this->selectedLanguage)->abbreviation,
                'id' => $booking->ride_id,
                'from_stop_id' => (int) $booking->from_stop_id,
                'to_stop_id' => (int) $booking->to_stop_id,
            ])
            ->with('success', 'Booking cancelled successfully.');
    }

    public function payBooking(Request $request, $lang = null)
    {
        $validated = $request->validate([
            'from_stop_id' => ['required', 'integer', 'exists:px_ride_stops,id'],
            'to_stop_id' => ['required', 'integer', 'exists:px_ride_stops,id'],
            'card_id' => ['nullable', 'integer', 'exists:cards,id'],
            'seats' => ['required', 'integer', 'min:1', 'max:8'],
            'driver_message' => ['required', 'string', 'max:5000'],
            'coffee_wall' => ['nullable', 'boolean'],
        ], [
            'driver_message.required' => 'The message to driver field is required.',
        ]);

        $fromStop = PxRideStop::query()->find((int) $validated['from_stop_id']);
        $toStop = PxRideStop::query()->find((int) $validated['to_stop_id']);
        if (!$fromStop || !$toStop || (int) $fromStop->ride_id !== (int) $toStop->ride_id) {
            return redirect()
                ->route('px.booking', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $fromStop->ride_id,
                    'from_stop_id' => $fromStop->id,
                    'to_stop_id' => $toStop->id,
                ])
                ->withInput()
                ->with('error', 'Invalid booking segment.');
        }

        $ride = PxRide::query()
            ->with('stops')
            ->published()
            ->where('id', $fromStop->ride_id)
            ->first();
        if (!$ride) {
            return redirect()
                ->route('px.ride_detail', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $fromStop->ride_id,
                    'from_stop_id' => $fromStop->id,
                    'to_stop_id' => $toStop->id,
                ])
                ->with('error', 'Ride not found or unavailable.');
        }

        $validated = array_merge($validated, $this->validateBookingRequest($request, $ride));

        [$fromIndex, $toIndex] = $this->resolveRideStopIndexes($ride, (int) $validated['from_stop_id'], (int) $validated['to_stop_id']);
        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return redirect()
                ->route('px.booking', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $ride->id,
                    'from_stop_id' => $fromStop->id,
                    'to_stop_id' => $toStop->id,
                ])
                ->withInput()
                ->with('error', 'Invalid route section for booking.');
        }

        $segmentPriceMinor = $this->resolveMatchedSegmentPriceMinor($ride, null, null, '', '', $fromIndex, $toIndex);
        $amountMinor = (int) $segmentPriceMinor * (int) $validated['seats'];
        $rideCurrencyCode = strtoupper((string) ($ride->currency ?: 'USD'));
        $stripeCurrencyCode = strtolower($rideCurrencyCode);

        $bookingMethodGroup = PxOptionGroup::query()
            ->where('code', 'booking_method')
            ->with(['options' => function ($q) {
                $q->where('is_active', true);
            }])
            ->first();
        $bookingMethodCode = strtolower(trim($this->getOptionCode($bookingMethodGroup, $ride->booking_method, '')));
        $isCashBookingMethod = ($bookingMethodCode === 'cash');
        $bookingModeGroup = PxOptionGroup::query()
            ->where('code', 'booking_mode')
            ->with(['options' => function ($q) {
                $q->where('is_active', true);
            }])
            ->first();
        $bookingModeCode = strtolower(trim($this->getOptionCode($bookingModeGroup, $ride->booking_mode, 'manual')));
        $cashBookingStatus = $bookingModeCode === 'instant' ? 'approved' : 'waiting';

        if ($amountMinor <= 0) {
            return redirect()
                ->route('px.booking', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $ride->id,
                    'from_stop_id' => $fromStop->id,
                    'to_stop_id' => $toStop->id,
                ])
                ->withInput()
                ->with('error', 'Invalid payment amount.');
        }

        $user = auth()->user();
        $existingBooking = $this->findActiveDuplicateBooking(
            (int) $ride->id,
            (int) $user->id,
            (int) $fromStop->id,
            (int) $toStop->id
        );
        if ($existingBooking) {
            return redirect()
                ->route('px.booking', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $ride->id,
                    'from_stop_id' => $fromStop->id,
                    'to_stop_id' => $toStop->id,
                ])
                ->withInput()
                ->with('error', 'You already booked this route section.');
        }

        $seatsRequested = (int) $validated['seats'];
        if ($seatsRequested > $ride->resolveSegmentAvailableSeats((int) $fromStop->id, (int) $toStop->id)) {
            return redirect()
                ->route('px.booking', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $ride->id,
                    'from_stop_id' => $fromStop->id,
                    'to_stop_id' => $toStop->id,
                ])
                ->withInput()
                ->with('error', 'Not enough available seats for this route section.');
        }

        if ($isCashBookingMethod) {
            try {
                $booking = $this->createCashBookingAndReserveSeats(
                    $ride,
                    $fromStop,
                    $toStop,
                    $user,
                    $seatsRequested,
                    $segmentPriceMinor,
                    $amountMinor,
                    $rideCurrencyCode,
                    $bookingMethodCode,
                    $cashBookingStatus,
                    $validated['driver_message'] ?? ''
                );

                $successMessage = $cashBookingStatus === 'approved'
                    ? 'Booking confirmed.'
                    : 'Booking request submitted. Waiting for driver approval.';

                return redirect()
                    ->route('px.ride_detail', [
                        'lang' => optional($this->selectedLanguage)->abbreviation,
                        'id' => $ride->id,
                        'from_stop_id' => $fromStop->id,
                        'to_stop_id' => $toStop->id,
                    ])
                    ->with('success', $successMessage);
            } catch (\RuntimeException $e) {
                return redirect()
                    ->route('px.booking', [
                        'lang' => optional($this->selectedLanguage)->abbreviation,
                        'id' => $ride->id,
                        'from_stop_id' => $fromStop->id,
                        'to_stop_id' => $toStop->id,
                    ])
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        }

        if (empty($validated['card_id'])) {
            return redirect()
                ->route('px.booking', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $ride->id,
                    'from_stop_id' => $fromStop->id,
                    'to_stop_id' => $toStop->id,
                ])
                ->withInput()
                ->with('error', 'Please select a saved card first.');
        }

        $card = Card::query()
            ->where('id', (int) $validated['card_id'])
            ->where('user_id', auth()->id())
            ->first();
        if (!$card || empty($card->stripe_payment_method_id)) {
            return redirect()
                ->route('px.booking', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $ride->id,
                    'from_stop_id' => $fromStop->id,
                    'to_stop_id' => $toStop->id,
                ])
                ->withInput()
                ->with('error', 'Selected card is invalid.');
        }

        if (empty($user->stripe_customer_id)) {
            return redirect()
                ->route('px.booking', [
                    'lang' => optional($this->selectedLanguage)->abbreviation,
                    'id' => $ride->id,
                    'from_stop_id' => $fromStop->id,
                    'to_stop_id' => $toStop->id,
                ])
                ->withInput()
                ->with('error', 'No Stripe customer found for this user.');
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $paymentMethod = StripePaymentMethod::retrieve($card->stripe_payment_method_id);
            try {
                $paymentMethod->attach(['customer' => $user->stripe_customer_id]);
            } catch (\Throwable $e) {
                // Ignore if already attached; Stripe will validate on create.
            }

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountMinor,
                'currency' => $stripeCurrencyCode,
                'payment_method' => $paymentMethod->id,
                'customer' => $user->stripe_customer_id,
                'confirmation_method' => 'automatic',
                'confirm' => true,
                'off_session' => true,
                'description' => 'PX booking payment for ride ' . $ride->id,
                'metadata' => [
                    'px_ride_id' => (string) $ride->id,
                    'from_stop_id' => (string) $validated['from_stop_id'],
                    'to_stop_id' => (string) $validated['to_stop_id'],
                    'seats' => (string) $validated['seats'],
                ],
            ]);

            $paymentIntentId = (string) ($paymentIntent->id ?? '');
            if ($paymentIntentId === '') {
                return response()->json(['message' => 'Payment provider did not return a payment intent ID.'], 422);
            }

            $existingTransaction = $this->findTransactionByPaymentIntentId($paymentIntentId);
            if ($existingTransaction) {
                return response()->json([
                    'status' => 'succeeded',
                    'payment_intent_id' => $paymentIntentId,
                    'amount_minor' => (int) $existingTransaction->amount_minor,
                    'currency' => (string) ($existingTransaction->currency ?? $rideCurrencyCode),
                    'booking_id' => (int) $existingTransaction->booking_id,
                    'transaction_id' => (int) $existingTransaction->id,
                    'idempotent' => true,
                ]);
            }

            [$booking, $transaction] = $this->createStripeBookingAndTransaction(
                $ride,
                $fromStop,
                $toStop,
                $user,
                $card,
                $seatsRequested,
                $segmentPriceMinor,
                $amountMinor,
                $rideCurrencyCode,
                $paymentIntent,
                $paymentIntentId
            );

            return response()->json([
                'status' => 'succeeded',
                'payment_intent_id' => $paymentIntentId,
                'amount_minor' => $amountMinor,
                'currency' => $rideCurrencyCode,
                'booking_id' => (int) ($booking->id ?? 0),
                'transaction_id' => (int) ($transaction->id ?? 0),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            $maybePaymentIntentId = isset($paymentIntent) ? (string) ($paymentIntent->id ?? '') : '';
            if ($maybePaymentIntentId !== '') {
                $existingTransaction = $this->findTransactionByPaymentIntentId($maybePaymentIntentId);
                if ($existingTransaction) {
                    return response()->json([
                        'status' => 'succeeded',
                        'payment_intent_id' => $maybePaymentIntentId,
                        'amount_minor' => (int) $existingTransaction->amount_minor,
                        'currency' => (string) ($existingTransaction->currency ?? $rideCurrencyCode),
                        'booking_id' => (int) $existingTransaction->booking_id,
                        'transaction_id' => (int) $existingTransaction->id,
                        'idempotent' => true,
                    ]);
                }
            }
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    protected function getOptionLabel($group, $optionId, $selectedLangId, $defaultLangId, $defaultLabel = 'N/A'): string
    {
        if (!$optionId || !$group) {
            return $defaultLabel;
        }

        $option = $group->options->firstWhere('id', $optionId);
        if (!$option) {
            return $defaultLabel;
        }

        $selected = $option->translations->firstWhere('language_id', $selectedLangId);
        $fallback = $option->translations->firstWhere('language_id', $defaultLangId);

        return optional($selected)->label ?: optional($fallback)->label ?: $option->code;
    }

    protected function validateBookingRequest(Request $request, PxRide $ride): array
    {
        return $request->validate(
            $this->agreementValidationRules($ride),
            $this->agreementValidationMessages()
        );
    }

    protected function agreementValidationRules(PxRide $ride): array
    {
        $rules = [
            'agree_terms' => ['required', 'accepted'],
        ];

        if (!empty($ride->cancelation_policy)) {
            $rules['firm_agree_terms'] = ['required', 'accepted'];
            $rules['firm_cancellation_understand'] = ['required', 'accepted'];
        }

        if (!empty($ride->women_only)) {
            $rules['pink_ride_agree_terms'] = ['required', 'accepted'];
        }

        if (!empty($ride->extra_care)) {
            $rules['extra_care_ride_agree_terms'] = ['required', 'accepted'];
        }

        return $rules;
    }

    protected function agreementValidationMessages(): array
    {
        return [
            'agree_terms.required' => 'You must agree to the terms and conditions.',
            'agree_terms.accepted' => 'You must agree to the terms and conditions.',
            'firm_agree_terms.required' => 'You must agree to the Firm Cancellation Policy for this ride.',
            'firm_agree_terms.accepted' => 'You must agree to the Firm Cancellation Policy for this ride.',
            'firm_cancellation_understand.required' => 'Please confirm that you understand the Firm Cancellation Policy.',
            'firm_cancellation_understand.accepted' => 'Please confirm that you understand the Firm Cancellation Policy.',
            'pink_ride_agree_terms.required' => 'You must agree to the Pink Ride terms before booking.',
            'pink_ride_agree_terms.accepted' => 'You must agree to the Pink Ride terms before booking.',
            'extra_care_ride_agree_terms.required' => 'You must agree to the Extra+ Ride terms before booking.',
            'extra_care_ride_agree_terms.accepted' => 'You must agree to the Extra+ Ride terms before booking.',
        ];
    }

    protected function getOptionCode($group, $optionId, $defaultCode = ''): string
    {
        if (!$optionId || !$group) {
            return (string) $defaultCode;
        }

        $option = $group->options->firstWhere('id', $optionId);
        if (!$option) {
            return (string) $defaultCode;
        }

        return (string) ($option->code ?? $defaultCode);
    }

    protected function resolveRideStopIndexes(PxRide $ride, int $fromStopId, int $toStopId): array
    {
        return $ride->resolveStopIndexes($fromStopId, $toStopId);
    }

    protected function findTransactionByPaymentIntentId(string $paymentIntentId): ?PxTransaction
    {
        if ($paymentIntentId === '') {
            return null;
        }

        return PxTransaction::query()
            ->with('booking')
            ->where('stripe_payment_intent_id', $paymentIntentId)
            ->first();
    }

    protected function createCashBookingAndReserveSeats(
        PxRide $ride,
        PxRideStop $fromStop,
        PxRideStop $toStop,
        $user,
        int $seatsRequested,
        int $segmentPriceMinor,
        int $amountMinor,
        string $rideCurrencyCode,
        string $bookingMethodCode,
        string $bookingStatus = 'waiting',
        string $driverMessage = ''
    ): PxBooking {
        $booking = null;

        DB::transaction(function () use (
            &$booking,
            $ride,
            $fromStop,
            $toStop,
            $user,
            $seatsRequested,
            $segmentPriceMinor,
            $amountMinor,
            $rideCurrencyCode,
            $bookingMethodCode,
            $bookingStatus,
            $driverMessage
        ) {
            $rideForUpdate = PxRide::query()
                ->with('stops')
                ->where('id', $ride->id)
                ->lockForUpdate()
                ->first();

            if (
                !$rideForUpdate
                || $rideForUpdate->resolveSegmentAvailableSeats((int) $fromStop->id, (int) $toStop->id) < $seatsRequested
            ) {
                throw new \RuntimeException('Not enough available seats for this route section.');
            }

            $duplicate = $this->findActiveDuplicateBooking(
                (int) $ride->id,
                (int) $user->id,
                (int) $fromStop->id,
                (int) $toStop->id
            );
            if ($duplicate) {
                throw new \RuntimeException('You already booked this route section.');
            }

            $booking = PxBooking::query()->create([
                'ride_id' => (int) $ride->id,
                'passenger_id' => (int) $user->id,
                'driver_id' => (int) $ride->driver_id,
                'from_stop_id' => (int) $fromStop->id,
                'to_stop_id' => (int) $toStop->id,
                'card_id' => null,
                'seats' => $seatsRequested,
                'segment_price_minor' => (int) $segmentPriceMinor,
                'total_price_minor' => (int) $amountMinor,
                'currency' => $rideCurrencyCode,
                'status' => $bookingStatus,
                'booked_at' => now(),
                'meta' => [
                    'booking_source' => 'px_web',
                    'payment_provider' => 'cash',
                    'seats_reserved_immediately' => true,
                    'booking_mode' => $ride->booking_mode,
                    'booking_method' => $ride->booking_method,
                    'booking_method_code' => $bookingMethodCode,
                    'from_stop_label' => (string) ($fromStop->label ?? ''),
                    'to_stop_label' => (string) ($toStop->label ?? ''),
                    'seats' => $seatsRequested,
                    'driver_message' => $driverMessage,
                ],
            ]);

            $rideForUpdate->adjustSegmentSeatAvailability((int) $fromStop->id, (int) $toStop->id, $seatsRequested);
        });

        return $booking;
    }

    protected function createStripeBookingAndTransaction(
        PxRide $ride,
        PxRideStop $fromStop,
        PxRideStop $toStop,
        $user,
        Card $card,
        int $seatsRequested,
        int $segmentPriceMinor,
        int $amountMinor,
        string $rideCurrencyCode,
        $paymentIntent,
        string $paymentIntentId,
        string $driverMessage = ''
    ): array {
        $booking = null;
        $transaction = null;

        DB::transaction(function () use (
            &$booking,
            &$transaction,
            $ride,
            $fromStop,
            $toStop,
            $user,
            $card,
            $seatsRequested,
            $segmentPriceMinor,
            $amountMinor,
            $rideCurrencyCode,
            $paymentIntent,
            $paymentIntentId,
            $driverMessage
        ) {
            $rideForUpdate = PxRide::query()
                ->with('stops')
                ->where('id', $ride->id)
                ->lockForUpdate()
                ->first();

            if (
                !$rideForUpdate
                || $rideForUpdate->resolveSegmentAvailableSeats((int) $fromStop->id, (int) $toStop->id) < $seatsRequested
            ) {
                throw new \RuntimeException('Not enough available seats for this route section.');
            }

            $duplicate = $this->findActiveDuplicateBooking(
                (int) $ride->id,
                (int) $user->id,
                (int) $fromStop->id,
                (int) $toStop->id
            );
            if ($duplicate) {
                throw new \RuntimeException('You already booked this route section.');
            }

            $booking = PxBooking::query()->create([
                'ride_id' => (int) $ride->id,
                'passenger_id' => (int) $user->id,
                'driver_id' => (int) $ride->driver_id,
                'from_stop_id' => (int) $fromStop->id,
                'to_stop_id' => (int) $toStop->id,
                'card_id' => (int) $card->id,
                'seats' => $seatsRequested,
                'segment_price_minor' => (int) $segmentPriceMinor,
                'total_price_minor' => (int) $amountMinor,
                'currency' => $rideCurrencyCode,
                'status' => 'waiting',
                'booked_at' => now(),
                'meta' => [
                    'booking_source' => 'px_web',
                    'payment_provider' => 'stripe',
                    'booking_mode' => $ride->booking_mode,
                    'booking_method' => $ride->booking_method,
                    'from_stop_label' => (string) ($fromStop->label ?? ''),
                    'to_stop_label' => (string) ($toStop->label ?? ''),
                    'seats' => $seatsRequested,
                    'driver_message' => $driverMessage,
                ],
            ]);

            $transaction = PxTransaction::query()->create([
                'booking_id' => (int) $booking->id,
                'ride_id' => (int) $ride->id,
                'user_id' => (int) $user->id,
                'amount_minor' => (int) $amountMinor,
                'currency' => $rideCurrencyCode,
                'provider' => 'stripe',
                'type' => 'charge',
                'status' => (string) ($paymentIntent->status ?: 'succeeded'),
                'stripe_payment_intent_id' => $paymentIntentId,
                'stripe_payment_method_id' => (string) ($paymentIntent->payment_method ?: $card->stripe_payment_method_id),
                'provider_payload' => method_exists($paymentIntent, 'toArray')
                    ? $paymentIntent->toArray()
                    : ['id' => $paymentIntentId],
                'processed_at' => now(),
            ]);

            $rideForUpdate->adjustSegmentSeatAvailability((int) $fromStop->id, (int) $toStop->id, $seatsRequested);
        });

        return [$booking, $transaction];
    }

    protected function findActiveDuplicateBooking(
        int $rideId,
        int $passengerId,
        int $fromStopId,
        int $toStopId,
        ?int $ignoreBookingId = null
    ): ?PxBooking {
        $query = PxBooking::query()
            ->where('ride_id', $rideId)
            ->where('passenger_id', $passengerId)
            ->where('from_stop_id', $fromStopId)
            ->where('to_stop_id', $toStopId)
            ->whereNotIn('status', ['cancelled', 'refunded', 'failed'])
            ->latest('id');

        if ($ignoreBookingId) {
            $query->where('id', '!=', $ignoreBookingId);
        }

        return $query->first();
    }

    protected function resolveMatchedSegmentPriceMinor(PxRide $ride, $fromCityId, $toCityId, string $fromLabel, string $toLabel, $fromIndex = null, $toIndex = null): int
    {
        $stops = $ride->stops
            ? $ride->stops->sortBy('stop_order')->values()->all()
            : [];

        if (count($stops) < 2) {
            return (int) ($ride->price_minor ?? 0);
        }

        if ($fromIndex === null || $toIndex === null) {
            [$fromIndex, $toIndex] = $this->findMatchingStopPair($stops, $fromCityId, $toCityId, $fromLabel, $toLabel);
        }

        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return (int) ($ride->price_minor ?? 0);
        }

        $configuredSegmentPriceMinor = $ride->resolveConfiguredSegmentPriceMinor((int) $fromIndex, (int) $toIndex);
        if ($configuredSegmentPriceMinor !== null) {
            return $configuredSegmentPriceMinor;
        }

        $lastIndex = count($stops) - 1;
        $totalPriceMinor = (int) ($ride->price_minor ?? 0);
        $intermediateLegsSum = 0;

        foreach ($stops as $idx => $stop) {
            if ($idx === 0 || $idx === $lastIndex) {
                continue;
            }
            $intermediateLegsSum += (int) ($stop->price_delta_minor ?? 0);
        }

        $storedFinalLegPrice = (int) ($stops[$lastIndex]->price_delta_minor ?? 0);
        $finalLegPrice = $storedFinalLegPrice > 0
            ? $storedFinalLegPrice
            : max(0, $totalPriceMinor - $intermediateLegsSum);
        $segmentPriceMinor = 0;

        for ($i = $fromIndex; $i < $toIndex; $i++) {
            $destIdx = $i + 1;
            $segmentPriceMinor += ($destIdx === $lastIndex)
                ? $finalLegPrice
                : (int) ($stops[$destIdx]->price_delta_minor ?? 0);
        }

        return max(0, $segmentPriceMinor);
    }

    protected function findMatchingStopPair(array $stops, $fromCityId, $toCityId, string $fromLabel, string $toLabel): array
    {
        $fromCandidates = [];
        $toCandidates = [];

        foreach ($stops as $idx => $stop) {
            if ($this->stopMatches($stop, $fromCityId, $fromLabel)) {
                $fromCandidates[] = $idx;
            }
            if ($this->stopMatches($stop, $toCityId, $toLabel)) {
                $toCandidates[] = $idx;
            }
        }

        foreach ($fromCandidates as $fromIdx) {
            foreach ($toCandidates as $toIdx) {
                if ($toIdx > $fromIdx) {
                    return [$fromIdx, $toIdx];
                }
            }
        }

        return [null, null];
    }

    protected function stopMatches($stop, $cityId, string $label): bool
    {
        if (!empty($cityId)) {
            return (int) ($stop->city_id ?? 0) === (int) $cityId;
        }

        $needle = mb_strtolower(trim($label));
        if ($needle === '') {
            return false;
        }

        $haystack = mb_strtolower((string) ($stop->label ?? ''));
        return str_contains($haystack, $needle);
    }
}
