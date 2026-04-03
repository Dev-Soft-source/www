<?php

namespace App\Services;

use App\Http\Controllers\RideController;
use App\Models\Booking;
use App\Models\Ride;
use App\Models\RideDetail;
use App\Models\SeatDetail;
use App\Models\SiteSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Shared persistence for posting or updating a ride (web + API).
 *
 * Controllers handle normalization, validation, and permission checks; this service runs
 * business rules and DB writes from the first datetime check through recurring cleanup.
 */
class RidePostService
{
    public function persist(
        Request $request,
        User $user,
        int $rideId,
        ?Ride $existingRide,
        $successMessage,
        RideController $rideController,
    ): PostRidePersistResult {
        $adminSetting = SiteSetting::getCached();
        $message = $successMessage;

        $seatCount = (int) $request->input('seats_total', $request->input('seats', 0));

        if ($rideId) {
            $lockedSeatCount = SeatDetail::where('ride_id', $rideId)
                ->whereIn('status', ['booked', 'hold'])
                ->count();

            if ($seatCount < $lockedSeatCount) {
                return PostRidePersistResult::failure(
                    'You cannot reduce seats below the number already reserved for this ride.',
                    'Seats Update Not Allowed',
                    true
                );
            }

            $hasBookings = Booking::where('ride_id', $rideId)
                ->bookedOrCompleted()
                ->withActivePassenger()
                ->exists();

            $currentPrice = $existingRide?->detail?->price;
            $newPrice = (int) $request->input('price_minor');
            if ($hasBookings && $currentPrice !== null && (int) $currentPrice !== $newPrice) {
                return PostRidePersistResult::failure(
                    'You cannot change the price once passengers have booked this ride.',
                    'Price Change Not Allowed',
                    true
                );
            }
        }

        $formattedDate = Carbon::parse($request->input('date'))->format('Y-m-d');
        $formattedTime = strlen((string) $request->input('time')) <= 5
            ? Carbon::createFromFormat('H:i', $request->input('time'))->format('H:i')
            : Carbon::parse($request->input('time'))->format('H:i');

        $rideDateTime = Carbon::parse($formattedDate . ' ' . $formattedTime);
        if ($rideDateTime->lte(Carbon::now()->addMinutes($adminSetting->ride_post_dead_time ?? 0))) {
            return PostRidePersistResult::failure(
                strip_tags($message->ride_dead_time_text ?? 'The ride time you selected is too close. Please select a time that is more than 15 minutes in the future'),
                null,
                true
            );
        }

        $rides = Ride::where('added_by', $user->id)
            ->when($rideId !== 0, fn($q) => $q->where('id', '!=', $rideId))
            ->get();

        foreach ($rides as $existingUserRide) {
            if ($existingUserRide->date == $formattedDate && $existingUserRide->time == $formattedTime) {
                return PostRidePersistResult::failure(
                    strip_tags($message->ride_schedule_message ?? 'Ride already scheduled'),
                    $message->overlap_ride_title ?? 'Ride already schedule',
                    true
                );
            }
        }

        $distance = round(((int) $request->input('distance_meters', 0)) / 1000, 2);
        $duration = (int) $request->input('duration', 0);

        $totalHours = $duration / 3600;
        $fullHours = floor($totalHours);
        $minutes = round(($totalHours - $fullHours) * 60);

        $destinationDateTime = (clone $rideDateTime)
            ->addHours(($adminSetting->destination_hours ?? 0) + $fullHours)
            ->addMinutes($minutes);

        $destinationReachedDate = $destinationDateTime->toDateString();
        $destinationReachedTime = $destinationDateTime->toTimeString();

        $completedDateTime = (clone $destinationDateTime)->addHours($adminSetting->ride_completed_hours ?? 0);
        $destinationCompletedDate = $completedDateTime->toDateString();
        $destinationCompletedTime = $completedDateTime->toTimeString();

        $duration += (($adminSetting->destination_hours ?? 0) * 3600);
        $duration += (($adminSetting->ride_completed_hours ?? 0) * 3600);

        $statDateTime = Carbon::parse($formattedDate . ' ' . $formattedTime);
        $endDateTime = Carbon::parse($destinationReachedDate . ' ' . $destinationReachedTime);

        $overlappedRide = Ride::notCancelled()
            ->when($rideId !== 0, fn($q) => $q->where('id', '!=', $rideId))
            ->where('added_by', $user->id)
            ->whereRaw("CONCAT(date, ' ', time) < ?", [$endDateTime])
            ->whereRaw("CONCAT(destination_reached_date, ' ', destination_reached_time) > ?", [$statDateTime])
            ->first();

        if ($overlappedRide) {
            return PostRidePersistResult::failure(
                strip_tags($message->overlap_ride_message ?? 'This ride overlaps with an existing ride you already have'),
                $message->overlap_ride_title ?? 'Ride already schedule',
                true
            );
        }

        $filename = '';
        if ($request->hasFile('vehicle_image')) {
            $file = $request->file('vehicle_image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('car_images'), $filename);
        } elseif ($request->has('existing_image')) {
            $filename = (string) $request->input('existing_image');
        }

        $origin = $request->input('origin.label') ?? data_get($request->input('origin'), 'label');
        $originCityId = $request->input('origin.city_id') ?? data_get($request->input('origin'), 'city_id');
        $destination = $request->input('destination.label') ?? data_get($request->input('destination'), 'label');
        $destinationCityId = $request->input('destination.city_id') ?? data_get($request->input('destination'), 'city_id');

        $vehiclePayload = [
            'vehicle_mode' => $request->input('vehicle_mode', 'skip'),
            'filename' => $filename,
            'make' => '',
            'model' => '',
            'vehicle_type' => '',
            'year' => '',
            'color' => '',
            'license_no' => '',
            'power_type' => '',
            'vehicle_id' => null,
            'skip_vehicle' => 0,
            'add_vehicle' => 0,
            'added_vehicle' => 0,
        ];
        $rideController->processPostRideVehicleMode($request, $vehiclePayload);
        extract($vehiclePayload, EXTR_OVERWRITE);

        $recurring = $request->filled('recurring') ? (int) $request->input('recurring') : 0;
        $recurring_type = $recurring !== 0 ? $request->input('recurring_type') : '';
        $recurring_trips = $recurring !== 0 ? $request->input('recurring_trips') : '';
        $features = implode('=', $request->input('features', []));
        $max_back_seats = $request->filled('max_back_seats') ? $request->input('max_back_seats') : 0;
        $accept_more_luggage = $request->filled('accept_more_luggage') ? $request->input('accept_more_luggage') : 0;
        $open_customized = $request->filled('open_customized') ? $request->input('open_customized') : 0;

        $data = array_filter([
            'departure' => $origin,
            'departure_lat' => $request->input('departure_lat'),
            'departure_lng' => $request->input('departure_lng'),
            'departure_place' => $request->input('departure_place'),
            'departure_route' => $request->input('departure_route'),
            'departure_zipcode' => $request->input('departure_zipcode'),
            'departure_city' => $request->input('departure_city'),
            'departure_state' => $request->input('departure_state'),
            'departure_state_short' => $request->input('departure_state_short'),
            'departure_country' => $request->input('departure_country'),
            'destination' => $destination,
            'destination_lat' => $request->input('destination_lat'),
            'destination_lng' => $request->input('destination_lng'),
            'destination_place' => $request->input('destination_place'),
            'destination_route' => $request->input('destination_route'),
            'destination_zipcode' => $request->input('destination_zipcode'),
            'destination_city' => $request->input('destination_city'),
            'destination_state' => $request->input('destination_state'),
            'destination_state_short' => $request->input('destination_state_short'),
            'destination_country' => $request->input('destination_country'),
            'total_distance' => $request->input('total_distance'),
            'total_time' => $request->input('total_time'),
            'date' => $formattedDate,
            'time' => $formattedTime,
            'recurring' => $recurring,
            'recurring_type' => $recurring_type,
            'recurring_trips' => $recurring_trips,
            'details' => $request->input('details'),
            'seats' => $seatCount,
            'vehicle_mode' => $vehicle_mode ?? $request->input('vehicle_mode', 'skip'),
            'vehicle_id' => $vehicle_id,
            'make' => $make,
            'model' => $model,
            'vehicle_type' => Ride::normalizeRideVehicleTypeId($vehicle_type),
            'year' => $year,
            'color' => $color,
            'license_no' => $license_no,
            'car_type' => $power_type,
            'car_image' => $filename,
            'car_image_original' => $filename,
            'smoke' => $request->input('smoke'),
            'animal_friendly' => $request->input('animal_friendly'),
            'features' => $features,
            'luggage' => $request->input('luggage'),
            'accept_more_luggage' => $accept_more_luggage,
            'max_back_seats' => $max_back_seats,
            'open_customized' => $open_customized,
            'booking_method' => $request->input('booking_method'),
            'booking_type' => $request->input('booking_type'),
            'price' => $request->input('price_minor'),
            'payment_method' => $request->input('payment_method'),
            'notes' => $request->input('notes'),
            'added_by' => $user->id,
            'until_date' => $request->input('until_date'),
            'until_limit' => $request->input('until_limit'),
            'pickup' => $request->input('pickup'),
            'dropoff' => $request->input('dropoff'),
            'middle_seats' => $request->input('middle_seats'),
            'back_seats' => $request->input('back_seats'),
            'added_on' => now(),
            'destination_reached_date' => $destinationReachedDate,
            'destination_reached_time' => $destinationReachedTime,
            'completed_date' => $destinationCompletedDate,
            'completed_time' => $destinationCompletedTime,
        ], fn($value) => !is_null($value) && $value !== '');

        $initialRide = $rideId
            ? Ride::with(['detail', 'rideStops', 'rideStopSegments'])->find($rideId)
            : Ride::create($data);

        if ($rideId) {
            $initialRide->update($data);
            $initialRide->refresh();
        }

        $rideDetail = $initialRide->detail ?? new RideDetail();
        $rideController->syncRideSeatDetails($initialRide, $seatCount);

        $rideDetail->ride_id = $initialRide->id;
        $rideDetail->departure = $origin;
        $rideDetail->origin_city_id = $originCityId;
        $rideDetail->destination = $destination;
        $rideDetail->destination_city_id = $destinationCityId;
        $rideDetail->pickup = $request->input('pickup');
        $rideDetail->dropoff = $request->input('dropoff');
        $rideDetail->default_ride = 1;
        $rideDetail->total_distance = $distance;
        $rideDetail->total_duration = $duration;
        $rideDetail->price = $request->input('price_minor');
        $rideDetail->date = $formattedDate;
        $rideDetail->time = $formattedTime;
        $rideDetail->destination_time = $destinationReachedTime;
        $rideDetail->destination_date = $destinationReachedDate;
        $rideDetail->completed_time = $destinationCompletedTime;
        $rideDetail->completed_date = $destinationCompletedDate;
        $rideDetail->save();

        $rideController->syncRideStopsAndSegments(
            $initialRide,
            $request,
            (string) $origin,
            $originCityId,
            (string) $destination,
            $destinationCityId,
            $destinationReachedDate,
            $destinationReachedTime
        );

        if ($recurring !== 0) {
            $frequency = $request->input('recurring_type');
            $numRecurringTrips = (int) $request->input('recurring_trips');
            $offsetDays = $frequency === 'Daily' ? 1 : 7;

            if (($offsetDays === 1 && $duration > 24 * 3600) || ($offsetDays === 7 && $duration > 7 * 24 * 3600)) {
                return PostRidePersistResult::failure(
                    'This ride\'s recurring overlaps with current ride. Total duration is greater than a ' . $frequency,
                    'Recurring info is overlapped',
                    true
                );
            }

            if (!$rideId) {
                $recurringEndDateTime = (clone $endDateTime)->addDays($offsetDays * $numRecurringTrips);
                $overlappedRecurringRide = Ride::notCancelled()
                    ->where('added_by', $user->id)
                    ->whereRaw("CONCAT(date, ' ', time) < ?", [$recurringEndDateTime])
                    ->whereRaw("CONCAT(destination_reached_date, ' ', destination_reached_time) > ?", [$statDateTime])
                    ->first();

                if ($overlappedRecurringRide) {
                    return PostRidePersistResult::failure(
                        strip_tags($message->overlap_ride_message ?? 'This ride\'s recurring overlaps with an existing ride you already have'),
                        $message->overlap_ride_title ?? 'Ride already schedule',
                        true,
                        $filename !== '' ? $filename : null
                    );
                }

                $templateRide = $initialRide->fresh(['detail', 'rideStops', 'rideStopSegments']);
                $sourceRideDetail = $templateRide->detail;
                $sourceRideStops = $templateRide->rideStops->sortBy('stop_order')->values();
                $sourceRideSegments = $templateRide->rideStopSegments;

                DB::transaction(function () use ($rideController, $numRecurringTrips, $templateRide, $sourceRideDetail, $sourceRideStops, $sourceRideSegments, $offsetDays, $user) {
                    for ($i = 1; $i <= $numRecurringTrips; $i++) {
                        $recurringRide = new Ride([
                            'added_by' => $user->id,
                            'recurring_id' => $templateRide->id,
                        ]);

                        $rideController->syncRecurringRideFromTemplate(
                            $recurringRide,
                            $templateRide,
                            $sourceRideDetail,
                            $sourceRideStops,
                            $sourceRideSegments,
                            $offsetDays * $i
                        );
                    }
                });
            } else {
                $existingRecurringRides = Ride::where('recurring_id', $initialRide->id)
                    ->orderBy('date')
                    ->orderBy('time')
                    ->get();

                $seriesRideIds = array_merge([$initialRide->id], $existingRecurringRides->pluck('id')->all());
                $recurringEndDateTime = (clone $endDateTime)->addDays($offsetDays * $numRecurringTrips);
                $overlappedRecurringRide = Ride::notCancelled()
                    ->where('added_by', $user->id)
                    ->whereNotIn('id', $seriesRideIds)
                    ->whereRaw("CONCAT(date, ' ', time) < ?", [$recurringEndDateTime])
                    ->whereRaw("CONCAT(destination_reached_date, ' ', destination_reached_time) > ?", [$statDateTime])
                    ->first();

                if ($overlappedRecurringRide) {
                    return PostRidePersistResult::failure(
                        strip_tags($message->overlap_ride_message ?? 'This ride\'s recurring overlaps with an existing ride you already have'),
                        $message->overlap_ride_title ?? 'Ride already schedule',
                        true
                    );
                }

                $templateRide = $initialRide->fresh(['detail', 'rideStops', 'rideStopSegments']);
                $sourceRideDetail = $templateRide->detail;
                $sourceRideStops = $templateRide->rideStops->sortBy('stop_order')->values();
                $sourceRideSegments = $templateRide->rideStopSegments;

                DB::transaction(function () use ($rideController, $existingRecurringRides, $numRecurringTrips, $templateRide, $sourceRideDetail, $sourceRideStops, $sourceRideSegments, $offsetDays, $user) {
                    for ($i = 1; $i <= $numRecurringTrips; $i++) {
                        $recurringRide = $existingRecurringRides[$i - 1] ?? new Ride([
                            'added_by' => $user->id,
                            'recurring_id' => $templateRide->id,
                        ]);

                        $rideController->syncRecurringRideFromTemplate(
                            $recurringRide,
                            $templateRide,
                            $sourceRideDetail,
                            $sourceRideStops,
                            $sourceRideSegments,
                            $offsetDays * $i
                        );
                    }

                    for ($i = $numRecurringTrips; $i < $existingRecurringRides->count(); $i++) {
                        $rideController->deleteRideCascade($existingRecurringRides[$i]);
                    }
                });
            }
        }

        if ($rideId && $recurring === 0) {
            Ride::where('recurring_id', $initialRide->id)
                ->orderBy('date')
                ->orderBy('time')
                ->get()
                ->each(function (Ride $recurringRide) use ($rideController) {
                    $rideController->deleteRideCascade($recurringRide);
                });
        }

        $initialRide = Ride::with(['detail', 'rideStops', 'rideStopSegments'])->find($initialRide->id);
        if (!$initialRide || !$initialRide->detail) {
            return PostRidePersistResult::failure('Could not save ride.', null, true);
        }

        return PostRidePersistResult::success($initialRide, $initialRide->detail);
    }
}
