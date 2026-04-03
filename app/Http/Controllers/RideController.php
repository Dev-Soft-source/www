<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPageSettingDetail;
use App\Models\CancellationHistory;
use App\Models\CancelRideSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\FolkRideSetting;
use App\Models\Language;
use App\Models\PinkRideSetting;
use App\Models\PostRidePageSettingDetail;
use App\Models\PostRidePageSettingSubDetail;
use App\Models\ChatsPageSettingDetail;
use App\Models\Rating;
use App\Models\RecentSearch;
use App\Models\ReviewSetting;
use App\Models\Ride;
use App\Models\RideDetail;
use App\Models\RideStop;
use App\Models\RideStopSegment;
use App\Models\City;
use App\Models\FCMToken;
use App\Models\MyVehicleSettingDetail;
use App\Models\NoShowHistory;
use App\Models\RideDetailPageSettingDetail;
use App\Models\SiteSetting;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\MyPassengerSettingDetail;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\SeatDetail;
use App\Services\FCMService;
use App\Services\RidePostService;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{

    public function RideDetail($lang = null, $id, $from_stop_id = 0, $to_stop_id = 0)
    {
        $from = '$request->departure';
        $to = '$request->destination';

        $ride = Ride::where('id', $id)
            ->with([
                'rideDetail',
                'rideStops' => function ($q) {
                    $q->orderBy('stop_order');
                },
                'rideStopSegments',
                'vehicle',
            ])
            ->first();

        if (!isset($ride) && empty($ride)) {
            $lang = $lang ?? 'en';
            return redirect(route('home', ['lang' => $lang]));
        }

        $setting = ReviewSetting::getCached();
        $cancelSetting = CancelRideSetting::getCached();
        $ratings = Rating::all();

        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback(
            $this->selectedLanguage->id,
            $this->defaultLang->id
        );

        $chatsPage = ChatsPageSettingDetail::getByLanguageWithFallback(
            $this->selectedLanguage->id,
            $this->defaultLang->id
        );

        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $ride = $this->makeDetailOfRide($ride, $from_stop_id, $to_stop_id);

        $ride_cancelled = false;
        $completed_date_time = Carbon::parse($ride->completed_date . ' ' . $ride->completed_time);
        if (isset($ride_booking) && ($completed_date_time < Carbon::now() || $ride_booking->status == '3' || $ride_booking->status == '4')) {
            $ride_cancelled = true;
        }

        $findRidePage = FindRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $booking = Booking::where('ride_id', $id)
            ->where('from_stop_id', $from_stop_id)
            ->where('to_stop_id', $to_stop_id)
            ->where('user_id', auth()->id())
            ->first();

        return view('ride_detail', [
            'from_stop_id' => $from_stop_id,
            'to_stop_id' => $to_stop_id,

            'booking' => $booking,

            'fromLabel' => $from ?? null,
            'toLabel' => $to ?? null,
            'ride_cancelled' => $ride_cancelled,
            'rideDetailPage' => $rideDetailPage,
            'ride' => $ride,
            'setting' => $setting,
            'cancelSetting' => $cancelSetting,
            'postRidePage' => $postRidePage,
            'findRidePage' => $findRidePage,
            'ratings' => $ratings,
            'chatsPage' => $chatsPage,

        ]);
    }

    protected function resolveRideStopIndices($orderedStops, $from, $to): array
    {
        $from = trim((string) $from);
        $to = trim((string) $to);
        $fromIndex = null;
        $toIndex = null;

        foreach ($orderedStops as $idx => $stop) {
            if ($fromIndex === null && $this->rideStopLabelMatches((string) ($stop->label ?? ''), $from)) {
                $fromIndex = $idx;
            }

            if ($fromIndex !== null && $idx > $fromIndex && $this->rideStopLabelMatches((string) ($stop->label ?? ''), $to)) {
                $toIndex = $idx;
                break;
            }
        }

        return [$fromIndex, $toIndex];
    }

    protected function rideStopLabelMatches(string $stopLabel, ?string $searchLabel): bool
    {
        $searchLabel = trim((string) $searchLabel);
        if ($searchLabel === '') {
            return false;
        }

        return strcasecmp(trim($stopLabel), $searchLabel) === 0
            || stripos($stopLabel, $searchLabel) !== false
            || stripos($searchLabel, $stopLabel) !== false;
    }

    protected function resolveRideDetailForStops(Ride $ride, ?string $fromLabel, ?string $toLabel)
    {
        $rideDetails = $ride->rideDetail->sortBy('id')->values();

        $matched = $rideDetails->first(function ($detail) use ($fromLabel, $toLabel) {
            return $this->rideStopLabelMatches((string) ($detail->departure ?? ''), (string) $fromLabel)
                && $this->rideStopLabelMatches((string) ($detail->destination ?? ''), (string) $toLabel);
        });

        if ($matched) {
            return $matched;
        }

        return $rideDetails->firstWhere('default_ride', 1) ?: $rideDetails->first();
    }

    public function MyCoPassengers(Request $request, $lang = null)
    {
        $ride = Ride::where('id', $request->id)->first();
        $setting = ReviewSetting::getCached();


        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $myPassengerPage = MyPassengerSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback(
            $this->selectedLanguage->id,
            $this->defaultLang->id
        );
        $ratings = Rating::all();

        return view('my_co_passengers', [
            'ride' => $ride,
            'setting' => $setting,
            'ratings' => $ratings,
            'rideDetailPage' => $rideDetailPage,
            'myPassengerPage' => $myPassengerPage,

            'postRidePage' => $postRidePage
        ]);
    }

    public function EditRide($lang, $id, $routeType = 'edit')
    {
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        if (!$user->hasVerifiedPhone()) {
            // phone number not verified, redirect to phone verification page
            return redirect()->route('phone', ['lang' => $lang]);
        }

        // Require driver's license on file (uploaded). Allow access once uploaded; admin approval (driver === '1') is not required to view/post ride form.
        if (!$user->hasDriverLicenseUpload()) {
            // driver license not on file, redirect to driver license verification page
            return redirect()->route('driver.verify', ['lang' => $lang]);
        }

        // Check if user has suspanded
        if ($user->isSuspended()) {
            return redirect()->route('home', ['lang' => $this->selectedLanguage->abbreviation])->with(['message' => "Your account has been suspended by the admin"]);
        }

        $ride = Ride::where('added_by', $user_id)->where('id', $id)->first();
        if (!$ride) {
            abort(404);
        }
        $ride->intermediate_stops = $this->extractIntermediateStopsForForm($ride);


        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $postRideSubDetailPage = PostRidePageSettingSubDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $postRideStats = $user->driverPostRideStats();

        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $vehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $isEditMode = true;
        $isCopyMode = false;
        if ($routeType == 'copy') {
            $isEditMode = false;
            $isCopyMode = true;
        }

        return view('post_ride', [
            'postRidePage' => $postRidePage,
            'postRideSubDetailPage' => $postRideSubDetailPage,
            'ride' => $ride,
            'user' => $user,
            'vehicles' => $vehicles,
            'vehiclePage' => $vehiclePage,
            'routeType' => $routeType,
            'isEditMode' => $isEditMode,
            'isCopyMode' => $isCopyMode,
            'isNewForm' => false,
        ] + $postRideStats);
    }

    public function CopyRide($lang, $id)
    {
        return $this->EditRide($lang, $id, 'copy');
    }

    /**
     * To make new post based on some ride
     * - A departure and destination are swapped
     */
    public function RepostRide($lang, $id)
    {
        $user_id = auth()->user()->id;

        $ride = Ride::where('added_by', $user_id)->where('id', $id)->first();
        if (!$ride) {
            abort(404);
        }

        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $pinkRideSetting = PinkRideSetting::getCached();
        $setting = FolkRideSetting::getCached();
        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $rides = Ride::where('added_by', $user_id)->get();

        $noshows = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();

        // To swap 
        if ($ride) {
            $originLabel = $ride->detail->departure ?? null;
            $originCityId = $ride->detail->origin_city_id ?? null;
            $destinationLabel = $ride->detail->destination ?? null;
            $destinationCityId = $ride->detail->destination_city_id ?? null;

            $ride->detail->departure = $destinationLabel;
            $ride->detail->origin_city_id = $destinationCityId;
            $ride->detail->destination = $originLabel;
            $ride->detail->destination_city_id = $originCityId;

            $pickup = $ride->detail->pickup;
            $ride->detail->pickup = $ride->detail->dropoff;
            $ride->detail->dropoff = $pickup;

            $lastStopEtaAt = $ride->rideStops->last()?->eta_at;
            $ride->detail->date = $lastStopEtaAt ? Carbon::parse($lastStopEtaAt)->format('F j, Y') : null;
            $ride->detail->time = $lastStopEtaAt ? Carbon::parse($lastStopEtaAt)->format('H:i') : null;
            View::share('projectToday', $ride->detail->date);

            $ride->intermediate_stops = $ride->rideStops
                ->filter(function ($stop) use ($originLabel, $destinationLabel) {
                    $label = trim((string) $stop->label);

                    if ($label === '') {
                        return false;
                    }

                    return !in_array(
                        strtolower($label),
                        [strtolower((string) $originLabel), strtolower((string) $destinationLabel)]
                    );
                })
                ->map(function ($stop) {
                    return [
                        'label' => $stop->label,
                        'city_id' => $stop->city_id,
                        'departure_at' => !empty($stop->departure_at)
                            ? Carbon::parse($stop->departure_at)->format('Y-m-d H:i')
                            : null,
                        'price_delta_minor' => $stop->price_delta_minor,
                        'is_pickup' => $stop->is_dropoff,
                        'is_dropoff' => $stop->is_pickup,
                        'pickup_dropoff_location' => $stop->pickup_dropoff_location,
                    ];
                })
                ->reverse()
                ->values()
                ->toArray();

            $ride->setRelation(
                'rideStopSegments',
                $ride->rideStopSegments->map(function ($segment) {
                    $fromStopId = $segment->from_stop_id;
                    $segment->from_stop_id = $segment->to_stop_id;
                    $segment->to_stop_id = $fromStopId;

                    return $segment;
                })
            );
        }



        if ($rides->isNotEmpty()) {
            // Fetch ratings where the driver_id matches the authenticated user's ID
            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();

            if ($ratings->count() > 0) {
                // Calculate total average
                $overallRating = $ratings->avg('average_rating') ?? 0;
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

        $postRideSubDetailPage = PostRidePageSettingSubDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $totalNoOfRides = Ride::where('added_by', $user_id)
            ->notCancelled()
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<', now()->toTimeString());
                        });
                });
            })
            ->count();

        $vehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('post_ride', [
            'postRideSubDetailPage' => $postRideSubDetailPage,
            'postRidePage' => $postRidePage,
            'isNewForm' => true,
            'isRepostMode' => true,
            'routeType' => 'repost',
            'ride' => $ride,
            'user' => $user,
            'vehicles' => $vehicles,
            'pinkRideSetting' => $pinkRideSetting,
            'vehiclePage' => $vehiclePage,
            'setting' => $setting,
            'overallRating' => $overallRating,
            'noshows' => $noshows,
            'totalNoOfRides' => $totalNoOfRides,
        ]);
    }

    public function PostRide($lang = null)
    {
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        if (!$user->hasVerifiedPhone()) {
            // phone number not verified, redirect to phone verification page
            return redirect()->route('phone', ['lang' => $lang]);
        }

        // Require driver's license on file (uploaded). Allow access once uploaded; admin approval (driver === '1') is not required to view/post ride form.
        if (!$user->hasDriverLicenseUpload()) {
            // driver license not on file, redirect to driver license verification page
            return redirect()->route('driver.verify', ['lang' => $lang]);
        }

        // Check if user has suspanded
        if ($user->isSuspended()) {
            return redirect()->route('home', ['lang' => $this->selectedLanguage->abbreviation])->with(['message' => "Your account has been suspended by the admin"]);
        }

        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $postRideSubDetailPage = PostRidePageSettingSubDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $ride = new Ride();
        $isNewForm = true;
        $postRideStats = $user->driverPostRideStats();

        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $vehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('post_ride', [
            'postRidePage' => $postRidePage,
            'postRideSubDetailPage' => $postRideSubDetailPage,
            'isNewForm' => $isNewForm,
            'ride' => $ride,
            'user' => $user,
            'vehicles' => $vehicles,
            'vehiclePage' => $vehiclePage,
            'routeType' => 'create'
        ] + $postRideStats);
    }


    public function normalizePostRideRequest(Request $request, ?Ride $ride = null): void
    {
        $merge = [];

        if (!$request->filled('seats') && $request->filled('seats_total')) {
            $merge['seats'] = $request->input('seats_total');
        }

        if (!$request->filled('vehicle_mode')) {
            // App multipart sends skip_vehicle / add_vehicle / added_vehicle instead of vehicle_mode;
            // do not pre-fill from DB or normalizeAppPostRideRequest cannot apply the user's choice.
            $hasAppVehicleFlags = $request->has('skip_vehicle')
                || $request->has('add_vehicle')
                || $request->has('added_vehicle');
            if (!$hasAppVehicleFlags) {
                $merge['vehicle_mode'] = $ride && $ride->vehicle_id ? 'existing' : 'skip';
            }
        }

        if (!$request->has('origin') && $request->filled('from')) {
            $merge['origin'] = [
                'label' => (string) $request->input('from'),
                'city_id' => data_get($request->input('origin'), 'city_id'),
            ];
        }

        if (!$request->has('destination') && $request->filled('to')) {
            $merge['destination'] = [
                'label' => (string) $request->input('to'),
                'city_id' => data_get($request->input('destination'), 'city_id'),
            ];
        }

        if (!$request->filled('price_minor') && $request->filled('price')) {
            $merge['price_minor'] = $request->input('price');
        }

        if (is_string($request->input('features'))) {
            $merge['features'] = array_values(array_filter(explode('=', (string) $request->input('features'))));
        }

        if (!empty($merge)) {
            $request->merge($merge);
        }
    }



    public function syncRideSeatDetails(Ride $ride, int $seatCount): void
    {
        $seatCount = max(1, $seatCount);

        $lockedSeats = SeatDetail::where('ride_id', $ride->id)
            ->whereIn('status', ['booked', 'hold'])
            ->orderBy('seat_number')
            ->get();

        $pendingSeats = SeatDetail::where('ride_id', $ride->id)
            ->whereNotIn('status', ['booked', 'hold'])
            ->orderBy('seat_number')
            ->get();

        $seatNumber = 1;
        foreach ($lockedSeats as $seatDetail) {
            if ((int) $seatDetail->seat_number !== $seatNumber) {
                $seatDetail->seat_number = $seatNumber;
                $seatDetail->save();
            }
            $seatNumber++;
        }

        $pendingTarget = max(0, $seatCount - $lockedSeats->count());
        $reusablePendingSeats = $pendingSeats->take($pendingTarget);

        foreach ($reusablePendingSeats as $seatDetail) {
            if ((int) $seatDetail->seat_number !== $seatNumber || $seatDetail->status !== 'pending') {
                $seatDetail->seat_number = $seatNumber;
                $seatDetail->status = 'pending';
                $seatDetail->booking_id = null;
                $seatDetail->save();
            }
            $seatNumber++;
        }

        $pendingSeats->slice($pendingTarget)->each(function ($seatDetail) {
            $seatDetail->delete();
        });

        while ($seatNumber <= $seatCount) {
            SeatDetail::create([
                'ride_id' => $ride->id,
                'seat_number' => $seatNumber,
                'status' => 'pending',
            ]);
            $seatNumber++;
        }
    }

    public function syncRideStopsAndSegments(
        Ride $ride,
        Request $request,
        string $originLabel,
        $originCityId,
        string $destinationLabel,
        $destinationCityId,
        ?string $destinationReachedDate,
        ?string $destinationReachedTime
    ): void {
        RideStopSegment::where('ride_id', $ride->id)->delete();
        RideStop::where('ride_id', $ride->id)->delete();

        $requestStops = is_array($request->input('stops')) ? $request->input('stops') : [];
        $stopsFrom = is_array($request->input('stop_from')) ? $request->input('stop_from') : [];
        $stopsTo = is_array($request->input('stop_to')) ? $request->input('stop_to') : [];
        $stopsPriceMinor = is_array($request->input('stop_price_minor')) ? $request->input('stop_price_minor') : [];

        $destinationPriceDeltaMinor = 0;
        $lastLegFromLabel = !empty($requestStops)
            ? trim((string) ($requestStops[count($requestStops) - 1]['label'] ?? ''))
            : $originLabel;

        foreach ($stopsFrom as $idx => $fromLabel) {
            if (
                trim((string) $fromLabel) === $lastLegFromLabel &&
                trim((string) ($stopsTo[$idx] ?? '')) === $destinationLabel
            ) {
                $destinationPriceDeltaMinor = (int) ($stopsPriceMinor[$idx] ?? 0);
                break;
            }
        }

        $stopRecords = [[
            'stop_order' => 1,
            'city_id' => $originCityId,
            'label' => $originLabel,
            'departure_at' => (!empty($ride->date) && !empty($ride->time))
                ? Carbon::parse($ride->date . ' ' . $ride->time)
                : null,
            'pickup_dropoff_location' => $request->pickup ?? null,
            'eta_at' => null,
            'price_delta_minor' => 0,
            'seats_available' => $ride->seats,
            'is_pickup' => true,
            'is_dropoff' => false,
        ]];

        foreach ($requestStops as $stop) {
            $label = trim((string) ($stop['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $stopRecords[] = [
                'stop_order' => count($stopRecords) + 1,
                'city_id' => !empty($stop['city_id']) ? (int) $stop['city_id'] : null,
                'label' => $label,
                'departure_at' => !empty($stop['departure_at']) ? Carbon::parse($stop['departure_at']) : null,
                'pickup_dropoff_location' => $stop['pickup_dropoff_location'] ?? null,
                'eta_at' => null,
                'price_delta_minor' => (int) ($stop['price_delta_minor'] ?? 0),
                'seats_available' => $ride->seats,
                'is_pickup' => isset($stop['is_pickup']) ? (bool) $stop['is_pickup'] : true,
                'is_dropoff' => isset($stop['is_dropoff']) ? (bool) $stop['is_dropoff'] : true,
            ];
        }

        $destinationEtaAt = $this->resolveDestinationStopEtaAt(
            $requestStops,
            $originLabel,
            $destinationLabel,
            !empty($ride->date) && !empty($ride->time)
                ? Carbon::parse($ride->date . ' ' . $ride->time)
                : null,
            !empty($destinationReachedDate) && !empty($destinationReachedTime)
                ? Carbon::parse($destinationReachedDate . ' ' . $destinationReachedTime)
                : null
        );

        $stopRecords[] = [
            'stop_order' => count($stopRecords) + 1,
            'city_id' => $destinationCityId,
            'label' => $destinationLabel,
            'departure_at' => null,
            'pickup_dropoff_location' => $request->dropoff ?? null,
            'eta_at' => $destinationEtaAt,
            'price_delta_minor' => $destinationPriceDeltaMinor,
            'seats_available' => $ride->seats,
            'is_pickup' => false,
            'is_dropoff' => true,
        ];

        $stopIdByLabel = [];
        foreach ($stopRecords as $stopRecord) {
            $savedStop = RideStop::create([
                'ride_id' => $ride->id,
                'stop_order' => $stopRecord['stop_order'],
                'city_id' => $stopRecord['city_id'],
                'label' => $stopRecord['label'],
                'departure_at' => $stopRecord['departure_at'],
                'pickup_dropoff_location' => $stopRecord['pickup_dropoff_location'],
                'eta_at' => $stopRecord['eta_at'],
                'price_delta_minor' => $stopRecord['price_delta_minor'],
                'seats_available' => $stopRecord['seats_available'],
                'is_pickup' => $stopRecord['is_pickup'],
                'is_dropoff' => $stopRecord['is_dropoff'],
            ]);

            $normalizedLabel = mb_strtolower(trim((string) $savedStop->label));
            if ($normalizedLabel !== '' && !isset($stopIdByLabel[$normalizedLabel])) {
                $stopIdByLabel[$normalizedLabel] = $savedStop->id;
            }
        }

        foreach ($stopsFrom as $idx => $fromLabel) {
            $fromLabel = trim((string) $fromLabel);
            $toLabel = trim((string) ($stopsTo[$idx] ?? ''));
            if ($fromLabel === '' || $toLabel === '') {
                continue;
            }

            $fromStopId = $stopIdByLabel[mb_strtolower($fromLabel)] ?? null;
            $toStopId = $stopIdByLabel[mb_strtolower($toLabel)] ?? null;

            if (!$fromStopId || !$toStopId || $fromStopId === $toStopId) {
                continue;
            }

            RideStopSegment::create([
                'ride_id' => $ride->id,
                'from_stop_id' => $fromStopId,
                'to_stop_id' => $toStopId,
                'price_minor' => (int) ($stopsPriceMinor[$idx] ?? 0),
            ]);
        }
    }

    public function syncRecurringRideFromTemplate(
        Ride $ride,
        Ride $templateRide,
        RideDetail $sourceRideDetail,
        $sourceRideStops,
        $sourceRideSegments,
        int $dayOffset
    ): void {
        $seatCount = max(
            (int) $templateRide->seats,
            (int) SeatDetail::where('ride_id', $ride->id)->whereIn('status', ['booked', 'hold'])->count()
        );

        $nextDate = Carbon::parse($templateRide->date)->addDays($dayOffset);
        $nextCompletedDate = !empty($templateRide->completed_date)
            ? Carbon::parse($templateRide->completed_date)->addDays($dayOffset)
            : null;
        $nextDestinationReachedDate = !empty($templateRide->destination_reached_date)
            ? Carbon::parse($templateRide->destination_reached_date)->addDays($dayOffset)
            : null;

        $ride->fill([
            'date' => $nextDate->format('Y-m-d'),
            'time' => $templateRide->time,
            'completed_date' => optional($nextCompletedDate)->format('Y-m-d'),
            'completed_time' => $templateRide->completed_time,
            'destination_reached_date' => optional($nextDestinationReachedDate)->format('Y-m-d'),
            'destination_reached_time' => $templateRide->destination_reached_time,
            'recurring' => $templateRide->recurring,
            'recurring_type' => '',
            'recurring_trips' => '',
            'recurring_id' => $templateRide->id,
            'details' => $templateRide->details,
            'seats' => $seatCount,
            'vehicle_mode' => $templateRide->vehicle_mode,
            'skip_vehicle' => $templateRide->skip_vehicle,
            'add_vehicle' => $templateRide->add_vehicle,
            'added_vehicle' => $templateRide->added_vehicle,
            'vehicle_id' => $templateRide->vehicle_id,
            'make' => $templateRide->make,
            'model' => $templateRide->model,
            'vehicle_type' => $templateRide->vehicle_type,
            'year' => $templateRide->year,
            'color' => $templateRide->color,
            'license_no' => $templateRide->license_no,
            'car_type' => $templateRide->car_type,
            'car_image' => $templateRide->car_image,
            'car_image_original' => $templateRide->car_image_original,
            'smoke' => $templateRide->smoke,
            'animal_friendly' => $templateRide->animal_friendly,
            'features' => $templateRide->features,
            'booking_method' => $templateRide->booking_method,
            'booking_type' => $templateRide->booking_type,
            'max_back_seats' => $templateRide->max_back_seats,
            'luggage' => $templateRide->luggage,
            'accept_more_luggage' => $templateRide->accept_more_luggage,
            'open_customized' => $templateRide->open_customized,
            'payment_method' => $templateRide->payment_method,
            'notes' => $templateRide->notes,
            'added_by' => $templateRide->added_by,
            'pickup' => $templateRide->pickup,
            'dropoff' => $templateRide->dropoff,
            'middle_seats' => $templateRide->middle_seats,
            'back_seats' => $templateRide->back_seats,
        ]);
        $ride->save();

        $this->syncRideSeatDetails($ride, $seatCount);

        $rideDetail = RideDetail::firstOrNew(['ride_id' => $ride->id]);
        $rideDetail->departure = $sourceRideDetail->departure;
        $rideDetail->origin_city_id = $sourceRideDetail->origin_city_id;
        $rideDetail->destination = $sourceRideDetail->destination;
        $rideDetail->destination_city_id = $sourceRideDetail->destination_city_id;
        $rideDetail->pickup = $sourceRideDetail->pickup;
        $rideDetail->dropoff = $sourceRideDetail->dropoff;
        $rideDetail->default_ride = $sourceRideDetail->default_ride;
        $rideDetail->total_distance = $sourceRideDetail->total_distance;
        $rideDetail->total_duration = $sourceRideDetail->total_duration;
        $rideDetail->price = $sourceRideDetail->price;
        $rideDetail->time = $templateRide->time;
        $rideDetail->date = $nextDate->format('Y-m-d');
        $rideDetail->destination_time = $templateRide->destination_reached_time;
        $rideDetail->destination_date = optional($nextDestinationReachedDate)->format('Y-m-d');
        $rideDetail->completed_time = $templateRide->completed_time;
        $rideDetail->completed_date = optional($nextCompletedDate)->format('Y-m-d');
        $rideDetail->save();

        RideStopSegment::where('ride_id', $ride->id)->delete();
        RideStop::where('ride_id', $ride->id)->delete();

        $stopMap = [];
        foreach ($sourceRideStops as $sourceRideStop) {
            $departureAt = !empty($sourceRideStop->departure_at)
                ? Carbon::parse($sourceRideStop->departure_at)->addDays($dayOffset)
                : null;
            $etaAt = !empty($sourceRideStop->eta_at)
                ? Carbon::parse($sourceRideStop->eta_at)->addDays($dayOffset)
                : null;

            $newStop = RideStop::create([
                'ride_id' => $ride->id,
                'stop_order' => $sourceRideStop->stop_order,
                'city_id' => $sourceRideStop->city_id,
                'label' => $sourceRideStop->label,
                'lat' => $sourceRideStop->lat,
                'lng' => $sourceRideStop->lng,
                'departure_at' => $departureAt,
                'pickup_dropoff_location' => $sourceRideStop->pickup_dropoff_location,
                'eta_at' => $etaAt,
                'price_delta_minor' => $sourceRideStop->price_delta_minor,
                'seats_available' => $seatCount,
                'is_pickup' => $sourceRideStop->is_pickup,
                'is_dropoff' => $sourceRideStop->is_dropoff,
            ]);

            $stopMap[$sourceRideStop->id] = $newStop->id;
        }

        $segmentData = [];
        foreach ($sourceRideSegments as $sourceRideSegment) {
            $fromStopId = $stopMap[$sourceRideSegment->from_stop_id] ?? null;
            $toStopId = $stopMap[$sourceRideSegment->to_stop_id] ?? null;

            if (!$fromStopId || !$toStopId) {
                continue;
            }

            $segmentData[] = [
                'ride_id' => $ride->id,
                'from_stop_id' => $fromStopId,
                'to_stop_id' => $toStopId,
                'price_minor' => $sourceRideSegment->price_minor,
                'distance_meters' => $sourceRideSegment->distance_meters,
                'duration_seconds' => $sourceRideSegment->duration_seconds,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($segmentData)) {
            RideStopSegment::insert($segmentData);
        }
    }

    public function deleteRideCascade(Ride $ride): void
    {
        RideStopSegment::where('ride_id', $ride->id)->delete();
        RideStop::where('ride_id', $ride->id)->delete();
        RideDetail::where('ride_id', $ride->id)->delete();
        SeatDetail::where('ride_id', $ride->id)->delete();
        $ride->delete();
    }

    /**
     * Store new ride or Updte a ride
     */
    public function PostRideStore(Request $request, $ride_id = 0)
    {

        if ($ride_id) {
            $existingRide = Ride::where('id', $ride_id)->where('added_by', auth()->id())->with('detail')->first();
            if (!$existingRide) {
                abort(404);
            }
            $this->normalizePostRideRequest($request, $existingRide);
        } else {
            $existingRide = null;
            $this->normalizePostRideRequest($request);
        }


        // form validation
        $validator = $this->buildPostRideStoreValidator($request, (int) $ride_id);
        $this->appendStopDepartureAtValidation($request, $validator);
        $validatorFailureResponse = $this->handlePostRideValidationFailure($validator);
        if ($validatorFailureResponse) {
            return $validatorFailureResponse;
        }

        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();

        $message = $this->successMessage;

        $permissionValidationResponse = $this->validatePostRidePermissions($user, $message);
        if ($permissionValidationResponse) {
            return $permissionValidationResponse;
        }

        $featureValidationResponse = $this->validatePostRideFeatureEligibility($request, $user);
        if ($featureValidationResponse) {
            return $featureValidationResponse;
        }
        $persist = app(RidePostService::class)->persist(
            $request,
            $user,
            (int) $ride_id,
            $existingRide,
            $message,
            $this
        );

        if (!$persist->ok) {
            $response = back()->with('error', $persist->errorMessage);
            if ($persist->errorHeading) {
                $response = $response->with('heading', $persist->errorHeading);
            }
            if ($persist->withFullRequestInput) {
                $response = $response->withInput($request->all());
            }
            if ($persist->uploadedImage !== null) {
                $response = $response->with('uploaded_image', $persist->uploadedImage);
            }
            return $response;
        }

        $initialRide = $persist->ride;

        if ($ride_id) {
            return redirect()
                ->route('my_rides', ['lang' => $this->selectedLanguage->abbreviation])
                ->with([
                    'message' => $this->successMessage->post_ride_update_message ?? 'Ride updated successfully',
                    'id' => (int) $ride_id,
                ]);
        }

        app(RideWebNotificationController::class)->dispatchPostRidePostedNotifications(
            $initialRide->id,
            $user->id,
            [
                'my_rides_lang_abbr' => $this->selectedLanguage->abbreviation,
                'posted_date' => $request->input('date'),
                'posted_time' => $request->input('time'),
                'seats' => $request->input('seats', $request->input('seats_total')),
                'price_minor' => (int) $request->input('price_minor'),
            ]
        );

        return redirect()->route('my_rides', ['lang' => $this->selectedLanguage->abbreviation])->with([
            'message' => $this->successMessage->ride_post_message,
            'id' => $initialRide->id,
        ])->withInput();
    }

    public function PostRideUpdate($lang, $ride_id, Request $request)
    {
        // $ride = Ride::where('id', $ride_id)->where('added_by', auth()->id())->first();
        // if (!$ride) {
        //     abort(404);
        // }

        // $this->normalizePostRideRequest($request, $ride);

        return $this->PostRideStore($request, (int) $ride_id);
    }

    protected function validatePostRidePermissions(User $user, $message)
    {
        if ($user->isBlockedPostRide()) {
            return back()->with('error', $message->block_post_ride_message);
        }

        if (!$user->hasCustomProfileImage()) {
            return back()->with('error', $message->profile_photo_required_message ?? 'For posting a ride profile photo is required');
        }

        if ($user->isSuspended()) {
            return back()->with('error', $message->admin_block_account_message ?? 'Your account has been suspended by the admin');
        }

        return null;
    }

    protected function validatePostRideFeatureEligibility(Request $request, User $user)
    {
        if (!$request->has('features') || !is_array($request->features)) {
            return null;
        }

        $features = $request->features;

        $selectedFeatureIds = is_array($features ?? null) ? $features : explode('=', (string) ($features ?? ''));
        $selectedFeatureIds = array_map('strval', array_filter($selectedFeatureIds));

        if (in_array("1", $selectedFeatureIds, true)) {
            $pinkRideError = $user->pinkRideEligibilityError();
            if ($pinkRideError) {
                return back()->with('error', $pinkRideError);
            }
        }

        if (in_array("2", $selectedFeatureIds, true)) {
            $extraCareError = $user->extraRideEligibilityError();
            if ($extraCareError) {
                return back()->with('error', $extraCareError);
            }
        }

        return null;
    }

    public function buildPostRideStoreValidator(Request $request, int $rideId = 0)
    {
        $isRideUpdate = $rideId > 0;

        $vehicle_mode = $request->filled('vehicle_mode') ? $request->vehicle_mode : 'skip';


        $recurring = $request->filled('recurring') ? $request->recurring : 0;

        return Validator::make($request->all(), [

            'origin' => ['required', 'array'],
            'origin.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'origin.label' => ['required', 'string', 'max:160'],
            'destination' => ['required', 'array'],
            'destination.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'destination.label' => ['required', 'string', 'max:160'],

            'pickup' => 'required',
            'dropoff' => 'required',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'details' => 'required|string|max_words:300',
            'seats_total' => 'required',
            'smoke' => 'required',
            'animal_friendly' => 'required',
            'features' => 'array',
            'booking_method' => 'required',
            'booking_type' => 'required',
            'luggage' => 'required',
            'payment_method' => 'required',
            'notes' => 'nullable|string|max:300',
            'middle_seats' => 'required',
            'back_seats' => 'required',
            'agree_terms' => $isRideUpdate ? 'nullable' : 'accepted',
            'price_minor' => 'required|numeric|gt:0',


            'make' => $vehicle_mode == 'add_new' ? 'required' : 'nullable',
            'model' => $vehicle_mode == 'add_new' ? 'required' : 'nullable',
            'year' => $vehicle_mode == 'add_new' ? 'required' : 'nullable',
            'color' => $vehicle_mode == 'add_new' ? 'required' : 'nullable',
            'license_no' => $vehicle_mode == 'add_new' ? 'required' : 'nullable',
            'vehicle_type' => $vehicle_mode == 'add_new' ? 'required' : 'nullable',
            'vehicle_image' => $vehicle_mode == 'add_new' ? 'required_without:existing_image|image|mimes:jpeg,png,jpg,gif|max:10240' : 'nullable',

            'vehicle_id' => $vehicle_mode == 'existing' ? 'required' : 'nullable',

            'recurring_type' => $recurring !== 0 ? 'required' : 'nullable',
            'recurring_trips' => $recurring !== 0 ? 'required|numeric|max:10' : 'nullable',

            'stops' => ['nullable', 'array', 'max:20'],
            'stops.*.label' => ['required_with:stops', 'string', 'max:160'],
            'stops.*.departure_at' => ['nullable', 'date_format:Y-m-d H:i'],
            'stops.*.pickup_dropoff_location' => ['nullable', 'string', 'max:500'],
            'stops.*.price' => ['nullable', 'integer', 'min:0'],
        ], [], []);
    }

    public function appendStopDepartureAtValidation(Request $request, $validator): void
    {
        $validator->after(function ($validator) use ($request) {
            $stops = is_array($request->input('stops')) ? $request->input('stops') : [];
            if (count($stops) < 1) {
                return;
            }

            try {
                $rideDepartureAt = Carbon::parse(
                    Carbon::parse($request->input('date'))->format('Y-m-d') . ' ' .
                        Carbon::createFromFormat('H:i', $request->input('time'))->format('H:i:s')
                );
            } catch (\Throwable $e) {
                return;
            }

            $previousDepartureAt = $rideDepartureAt;

            foreach ($stops as $index => $stop) {
                $departureAt = trim((string) ($stop['departure_at'] ?? ''));
                if ($departureAt === '') {
                    continue;
                }

                try {
                    $currentDepartureAt = Carbon::createFromFormat('Y-m-d H:i', $departureAt);
                } catch (\Throwable $e) {
                    continue;
                }

                if ($currentDepartureAt->lt($previousDepartureAt)) {
                    $validator->errors()->add(
                        "stops.$index.departure_at",
                        'The departure time from the current stop must be earlier than the departure time from the previous stop.'
                    );
                    return;
                }

                $previousDepartureAt = $currentDepartureAt;
            }
        });
    }

    protected function resolveDestinationStopEtaAt(
        array $requestStops,
        string $originLabel,
        string $destinationLabel,
        ?Carbon $rideDepartureAt,
        ?Carbon $fallbackEtaAt
    ): ?Carbon {
        $previousLabel = $originLabel;
        $previousDepartureAt = $rideDepartureAt ? $rideDepartureAt->copy() : null;

        foreach ($requestStops as $stop) {
            $label = trim((string) ($stop['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $previousLabel = $label;

            if (!empty($stop['departure_at'])) {
                try {
                    $previousDepartureAt = Carbon::createFromFormat('Y-m-d H:i', (string) $stop['departure_at']);
                } catch (\Throwable $e) {
                    $previousDepartureAt = $previousDepartureAt ? $previousDepartureAt->copy() : null;
                }
            }
        }

        if (!$previousDepartureAt) {
            return $fallbackEtaAt ? $fallbackEtaAt->copy() : null;
        }

        $googleApiData = $this->getDataFromGoogleApi($previousLabel, $destinationLabel);
        $elementStatus = isset($googleApiData['rows'][0]['elements'][0]['status'])
            ? $googleApiData['rows'][0]['elements'][0]['status']
            : null;

        if ($elementStatus !== 'OK') {
            return $fallbackEtaAt ? $fallbackEtaAt->copy() : null;
        }

        $durationSeconds = isset($googleApiData['rows'][0]['elements'][0]['duration']['value'])
            ? (int) $googleApiData['rows'][0]['elements'][0]['duration']['value']
            : 0;

        if ($durationSeconds <= 0) {
            return $fallbackEtaAt ? $fallbackEtaAt->copy() : null;
        }

        return $previousDepartureAt->copy()->addSeconds($durationSeconds);
    }

    protected function validatePostRideStateLimit($fromState, int $rideCount, $message)
    {
        if (isset($fromState) && !empty($fromState)) {
            if (isset($fromState->state->ride_limit) && $rideCount >= $fromState->state->ride_limit) {
                return back()->with('message', $message->not_allowed_post_ride_state_wise_message)->withInput();
            }
        }

        return null;
    }

    protected function appendPostRideVehicleSelectionValidation(Request $request, $validator): void
    {
        if ($request->has('skip_vehicle') || $request->has('add_vehicle') || $request->has('added_vehicle')) {
            return;
        }

        $validator->after(function ($validator) {
            $validator->errors()->add('vehicle_selection', 'You must select at least one vehicle option.');
        });
    }

    protected function handlePostRideValidationFailure($validator)
    {
        if (!$validator->fails()) {
            return null;
        }

        // $hasRequiredError = $validator->errors()->has('image')
        //     && $validator->errors()->first('image') === 'The image is not uploaded yet';

        // if ($hasRequiredError && $validator->errors()->count() <= 1) {
        //     return null;
        // }

        return back()
            ->withErrors($validator)
            ->withInput();
        // ->with('uploaded_image', $filename);
    }

    public function processPostRideVehicleMode(Request $request, array &$payload): void
    {
        // $payload['skip_vehicle'] = $payload['vehicle_mode'] == 'skip' ? 1 : 0;
        // $payload['add_vehicle'] = $payload['vehicle_mode'] == 'add_new' ? 1 : 0;
        // $payload['added_vehicle'] = $payload['vehicle_mode'] == 'existing' ? 1 : 0;

        if ($payload['vehicle_mode'] == 'skip') {
            $payload['make'] = '';
            $payload['model'] = '';
            $payload['vehicle_type'] = '';
            $payload['year'] = '';
            $payload['color'] = '';
            $payload['license_no'] = '';
            $payload['power_type'] = '';
            return;
        }

        if ($payload['vehicle_mode'] == 'add_new') {
            $payload['make'] = $request->make;
            $payload['model'] = $request->model;
            $payload['vehicle_type'] = Ride::normalizeRideVehicleTypeId($request->vehicle_type);
            $payload['year'] = $request->year;
            $payload['color'] = $request->color;
            $payload['license_no'] = $request->license_no;
            $payload['power_type'] = $request->power_type;

            $vehicle = Vehicle::create([
                'user_id' => auth()->user()->id,
                'make' => $request->make,
                'model' => $request->model,
                'type' => Vehicle::normalizeVehicleTypeId($request->vehicle_type),
                'license_no' => $request->license_no,
                'color' => $request->color,
                'year' => $request->year,
                'power_type' => $request->power_type,
                'image' => $payload['filename'] ?? '',
            ]);

            $payload['vehicle_id'] = $vehicle->id;
            return;
        }
        $vehicle = Vehicle::whereId($request->vehicle_id)->first();
        if ($vehicle) {
            $payload['make'] = $vehicle->make;
            $payload['model'] = $vehicle->model;
            $payload['vehicle_type'] = Ride::normalizeRideVehicleTypeId(
                $vehicle->vehicle_type ?? $vehicle->type
            );
            $payload['year'] = $vehicle->year;
            $payload['color'] = $vehicle->color;
            $payload['license_no'] = $vehicle->license_no;
            $payload['power_type'] = $vehicle->power_type;
            $payload['vehicle_id'] = $vehicle->id;
            $payload['filename'] = $vehicle->remove_image === '0'
                ? basename($vehicle->image)
                : '';
            return;
        }



        $payload['make'] = '';
        $payload['model'] = '';
        $payload['vehicle_type'] = '';
        $payload['year'] = '';
        $payload['color'] = '';
        $payload['license_no'] = '';
        $payload['power_type'] = '';
    }


    /*
     * AJAX only: returns HTML for a new "add more spots" row. Does NOT save to the database.
     * Extra spots are saved when the user submits the main form (UpdateRide or PostRideStore).
     */
    public function addNewSpots(Request $request)
    {
        $fromSpot = $request->input('from_spot');
        $toSpot = $request->input('to_spot');
        $from_city = $fromSpot ? trim(explode(',', $fromSpot)[0]) : '';
        $to_city = $toSpot ? trim(explode(',', $toSpot)[0]) : '';

        $selectedLanguage = session('selectedLanguage');
        $postRidePage = null;
        $postRideSubDetailPage = null;
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }
        $cityErrorMessage = $selectedLanguage
            ? PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('city_not_in_record')->first()
            : null;

        $validator = Validator::make($request->all(), [
            'from_spot' => 'required|exists:cities,name',
            'to_spot' => 'required|exists:cities,name',
            'price' => 'required',

        ], [
            'from_spot.exists' => $cityErrorMessage->city_not_in_record ?? 'City not in record',
            'to_spot.exists' => $cityErrorMessage->city_not_in_record ?? 'City not in record',
        ]);

        if ((!$from_city || !DB::table('cities')->where('name', $from_city)->exists()) || (!$to_city || !DB::table('cities')->where('name', $to_city)->exists()) || is_null($request->price)) {
            // return response()->json([
            //     'status' => 'error',
            //     'errors' => $validator->errors(),
            // ]);
            if (is_null($request->price)) {

                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'price' => [__('validation.required', ['attribute' => 'price'])]
                    ],
                ]);
            }
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'from_spot' => [$cityErrorMessage->city_not_in_record ?? 'City not in record'],
                    'to_spot' =>  [$cityErrorMessage->city_not_in_record ?? 'City not in record'],
                ],
            ]);
        }

        $spotHtml = view('post_ride_partial.add_more_from_to_partial', [
            'postRideSubDetailPage' => $postRideSubDetailPage,
            'index' => $request->index,
            'postRidePage' => $postRidePage,
            'ride_detail' => null,
            'type' => 'create',
        ])->render();
        return response()->json(['spotHtml' => $spotHtml]);
    }

    public function deleteSpots(Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        $message = null;
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_has_booking_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_has_booking_message')->first();
            }
        }

        $checkBooking = Booking::where('ride_detail_id', $request->rideDetailId)->first();
        if (isset($checkBooking) && !empty($checkBooking)) {
            return response()->json(['status' => 'error', 'message' => $message->ride_has_booking_message ?? "ride has booking"]);
        }

        RideDetail::where('id', $request->rideDetailId)->delete();

        return response()->json(['status' => 'success']);
    }


    public function getDataFromGoogleApi($from, $to)
    {
        $apiKey = env('GOOGLE_API_KEY');
        $ch = curl_init();

        Log::info('Google Maps API Key: ' . $apiKey);
        // URL encode the addresses to properly handle spaces and special characters
        // This ensures city names like "Montreal, QC" and "Ottawa, ON" work correctly
        $fromEncoded = urlencode($from);
        $toEncoded = urlencode($to);

        $apiUrl = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $fromEncoded . "&destinations=" . $toEncoded . "&units=imperial&key=" . $apiKey . "";

        Log::info('Google Maps API Request', [
            'from' => $from,
            'to' => $to,
            'from_encoded' => $fromEncoded,
            'to_encoded' => $toEncoded,
            'url' => str_replace($apiKey, 'HIDDEN_KEY', $apiUrl)
        ]);

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('Google Maps API cURL Error: ' . curl_error($ch), [
                'from' => $from,
                'to' => $to,
                'curl_error' => curl_error($ch),
                'curl_errno' => curl_errno($ch)
            ]);
        }

        curl_close($ch);

        $data = json_decode($response, true);

        // Log API response
        if (isset($data['status']) && $data['status'] === 'OK') {
            $distance = isset($data['rows'][0]['elements'][0]['distance']['value']) ? $data['rows'][0]['elements'][0]['distance']['value'] : 0;
            $distanceText = isset($data['rows'][0]['elements'][0]['distance']['text']) ? $data['rows'][0]['elements'][0]['distance']['text'] : 'N/A';
            $duration = isset($data['rows'][0]['elements'][0]['duration']['value']) ? $data['rows'][0]['elements'][0]['duration']['value'] : 0;
            $durationText = isset($data['rows'][0]['elements'][0]['duration']['text']) ? $data['rows'][0]['elements'][0]['duration']['text'] : 'N/A';

            // Log::info('Google Maps API Success', [
            //     'from' => $from,
            //     'to' => $to,
            //     'distance_meters' => $distance,
            //     'distance_km' => round($distance / 1000, 2),
            //     'distance_text' => $distanceText,
            //     'duration_seconds' => $duration,
            //     'duration_text' => $durationText,
            //     'status' => $data['status']
            // ]);
        } else {
            // Log if API returns an error status
            Log::warning('Google Maps API returned non-OK status', [
                'status' => $data['status'] ?? 'unknown',
                'error_message' => $data['error_message'] ?? 'No error message',
                'from' => $from,
                'to' => $to,
                'response' => $data
            ]);
        }

        return $data;
    }

    public function segmentDistanceEstimates(Request $request, $lang = null)
    {
        $validator = Validator::make($request->all(), [
            'point_labels' => ['required', 'array', 'min:2', 'max:22'],
            'point_labels.*' => ['required', 'string', 'max:160'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid route points.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pointLabels = array_values(array_map(
            fn($label) => trim((string) $label),
            (array) $request->input('point_labels', [])
        ));

        $legDistancesMeters = [];
        $legDurationsSeconds = [];
        $segmentDistancesMeters = [];
        $segmentDurationsSeconds = [];
        $resolvedLegs = 0;
        $resolvedSegments = 0;
        $pointCount = count($pointLabels);

        for ($fromIndex = 0; $fromIndex < $pointCount - 1; $fromIndex++) {
            for ($toIndex = $fromIndex + 1; $toIndex < $pointCount; $toIndex++) {
                $from = $pointLabels[$fromIndex] ?? '';
                $to = $pointLabels[$toIndex] ?? '';
                $distanceMeters = 0;
                $durationSeconds = 0;

                if ($from !== '' && $to !== '') {
                    $googleApiData = $this->getDataFromGoogleApi($from, $to);
                    $elementStatus = isset($googleApiData['rows'][0]['elements'][0]['status'])
                        ? $googleApiData['rows'][0]['elements'][0]['status']
                        : null;

                    if ($elementStatus === 'OK') {
                        $distanceMeters = isset($googleApiData['rows'][0]['elements'][0]['distance']['value'])
                            ? (int) $googleApiData['rows'][0]['elements'][0]['distance']['value']
                            : 0;
                        $durationSeconds = isset($googleApiData['rows'][0]['elements'][0]['duration']['value'])
                            ? (int) $googleApiData['rows'][0]['elements'][0]['duration']['value']
                            : 0;
                    }
                }

                if ($toIndex === $fromIndex + 1) {
                    if ($distanceMeters > 0) {
                        $resolvedLegs++;
                    }
                    $legDistancesMeters[] = $distanceMeters;
                    $legDurationsSeconds[] = $durationSeconds;
                }

                if ($distanceMeters > 0) {
                    $resolvedSegments++;
                }

                $segmentDistancesMeters["{$fromIndex}:{$toIndex}"] = $distanceMeters;
                $segmentDurationsSeconds["{$fromIndex}:{$toIndex}"] = $durationSeconds;
            }
        }

        return response()->json([
            'leg_distances_meters' => $legDistancesMeters,
            'leg_durations_seconds' => $legDurationsSeconds,
            'total_distance_meters' => array_sum($legDistancesMeters),
            'total_duration_seconds' => array_sum($legDurationsSeconds),
            'resolved_legs' => $resolvedLegs,
            'segment_distances_meters' => $segmentDistancesMeters,
            'segment_durations_seconds' => $segmentDurationsSeconds,
            'resolved_segments' => $resolvedSegments,
        ]);
    }
}
