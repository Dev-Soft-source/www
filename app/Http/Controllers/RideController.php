<?php

namespace App\Http\Controllers;

use App\Mail\ExtraCareRideMail;
use App\Mail\PinkExtraCareRideMail;
use App\Mail\PinkRideMail;
use App\Mail\RidePostedMail;
use App\Models\Booking;
use App\Models\BookingPageSettingDetail;
use App\Models\CancellationHistory;
use App\Models\CancelRideSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\FolkRideSetting;
use App\Models\Language;
use App\Models\Notification;
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
use App\Models\SiteTextDetail;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\SeatDetail;
use App\Services\FCMService;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{

    public function RideDetail($lang = null, $id, $from_stop_id = null, $to_stop_id = null)
    {
        $from = '$request->departure';
        $to = '$request->destination';

        $ride = Ride::where('id', $id)
            ->with([
                'rideDetail' => function ($q) {
                    $q->orderBy('id');
                },
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

        $setting = ReviewSetting::first();
        $cancelSetting = CancelRideSetting::first();
        $ratings = Rating::all();

        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback(
            $this->selectedLanguage->id,
            $this->defaultLang->id
        );

        $chatsPage = ChatsPageSettingDetail::getByLanguageWithFallback(
            $this->selectedLanguage->id,
            $this->defaultLang->id
        );

        $postRidePage = $this->getPostRidePageWithSettingDetail();
        
        $searchOptionGroups = $this->getSearchOptionGroups(
            $this->selectedLanguage->id,
            $this->defaultLang->id
        );

        $ride->mapMultipleOptionColumnsToDetails(
            ['luggage', 'payment_method', 'booking_type', 'animal_friendly', 'booking_method'],
            $this->selectedLanguage->id,
            $this->defaultLang->id,
            false
        );

        $ride = $this->makeDetailOfRide($ride, $from_stop_id, $to_stop_id);

        $featureIds = array_filter(explode('=', $ride->features ?? ''));
        $ride->pink_ride = $postRidePage->features_option1 &&
            in_array((string) $postRidePage->features_option1->features_setting_id, $featureIds)
            ? $postRidePage->features_option1
            : null;
        $ride->extra_care_ride = $postRidePage->features_option2 &&
            in_array((string) $postRidePage->features_option2->features_setting_id, $featureIds)
            ? $postRidePage->features_option2
            : null;



        $ride_cancelled = false;
        $completed_date_time = Carbon::parse($ride->completed_date . ' ' . $ride->completed_time);
        if (isset($ride_booking) && ($completed_date_time < Carbon::now() || $ride_booking->status == '3' || $ride_booking->status == '4')) {
            $ride_cancelled = true;
        }

        $findRidePage = FindRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('ride_detail', [
            'from_stop_id' => $from_stop_id,
            'to_stop_id' => $to_stop_id,

            'fromLabel' => $from ?? null,
            'toLabel' => $to ?? null,
            'ride_cancelled' => $ride_cancelled,
            'rideDetailPage' => $rideDetailPage,
            'ride' => $ride,
            'searchOptionGroups' => $searchOptionGroups,
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
        $setting = ReviewSetting::first();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'payment_methods_option3', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'luggage_option1', 'luggage_option2', 'luggage_option3', 'luggage_option4', 'luggage_option5', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'payment_methods_option3', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'luggage_option1', 'luggage_option2', 'luggage_option3', 'luggage_option4', 'luggage_option5', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
            }
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is 2 and booking_id belongs to the user
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is null and receiver_id belongs to the user
                    $query->where('type', null)
                        ->whereHas('receiver', function ($query) use ($user_id) {
                            $query->where('id', $user_id);
                        });
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        $ratings = Rating::all();
        return view('my_co_passengers', ['ride' => $ride, 'setting' => $setting, 'ratings' => $ratings, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'postRidePage' => $postRidePage]);
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


        $postRidePage = $this->getPostRidePageWithSettingDetail();
        $postRideSubDetailPage = PostRidePageSettingSubDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $postRideStats = $user->driverPostRideStats();

        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $vehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $vehicleTypes = $this->getVehicleTypesByLanguage();

        $isEditMode = true;
        $isCopyMode = false;
        if($routeType == 'copy') {
            $isEditMode = false;
            $isCopyMode = true;
        }

        return view('post_ride', [
            'postRidePage' => $postRidePage,
            'postRideSubDetailPage' => $postRideSubDetailPage,
            'ride' => $ride,
            'user' => $user,
            'vehicles' => $vehicles,
            'vehicleTypes' => $vehicleTypes,
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
        $pinkRideSetting = PinkRideSetting::first();
        $setting = FolkRideSetting::first();
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
            View::share('projectToday', "$ride->detail->date");

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
        $postRidePage = $this->getPostRidePageWithSettingDetail();

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
        $noShowsCount = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
        $cancellationCount = CancellationHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();

        $vehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $vehicleTypes = $this->getVehicleTypesByLanguage();

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
            'vehicleTypes' => $vehicleTypes,
            'vehiclePage' => $vehiclePage,
            'setting' => $setting,
            'overallRating' => $overallRating,
            'noshows' => $noshows,
            'totalNoOfRides' => $totalNoOfRides,
            'noShowsCount' => $noShowsCount,
            'cancellationCount' => $cancellationCount
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

        $postRidePage = $this->getPostRidePageWithSettingDetail();
        $postRideSubDetailPage = PostRidePageSettingSubDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $ride = new Ride();
        $isNewForm = true;
        $postRideStats = $user->driverPostRideStats();

        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $vehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $vehicleTypes = $this->getVehicleTypesByLanguage();

        return view('post_ride', [
            'postRidePage' => $postRidePage,
            'postRideSubDetailPage' => $postRideSubDetailPage,
            'isNewForm' => $isNewForm,
            'ride' => $ride,
            'user' => $user,
            'vehicles' => $vehicles,
            'vehicleTypes' => $vehicleTypes,
            'vehiclePage' => $vehiclePage,
            'routeType' => 'create'
        ] + $postRideStats);
    }

    /**
     * Process vehicle mode and handle vehicle creation if needed
     *
     * @param \Illuminate\Http\Request $request
     * @param array &$payload Reference to payload array to modify
     * @return void
     */
    protected function processVehicleMode(Request $request, array &$payload): void
    {
        $vehicleMode = (string) ($payload['vehicle_mode'] ?? '');

        if ($vehicleMode === 'skip') {
            $payload['vehicle_id'] = null;
            return;
        }

        if ($vehicleMode === 'add_new') {
            $newVehicle = (array) ($payload['new_vehicle'] ?? []);
            $primaryVehicle = (string) ($newVehicle['primary_vehicle'] ?? '0');
            $vehicleImageFilename = '';

            if ($request->hasFile('new_vehicle_image')) {
                $file = $request->file('new_vehicle_image');
                $vehicleImageFilename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('/car_images'), $vehicleImageFilename);
            }

            if ($primaryVehicle === '1') {
                Vehicle::query()
                    ->where('user_id', auth()->id())
                    ->update(['primary_vehicle' => 0]);
            }

            $createdVehicle = Vehicle::query()->create([
                'user_id' => auth()->id(),
                'make' => (string) ($newVehicle['make'] ?? ''),
                'model' => (string) ($newVehicle['model'] ?? ''),
                'type' => (string) ($newVehicle['type'] ?? ''),
                'license_no' => (string) ($newVehicle['license_no'] ?? ''),
                'color' => (string) ($newVehicle['color'] ?? ''),
                'year' => (string) ($newVehicle['year'] ?? ''),
                'car_type' => (string) ($newVehicle['car_type'] ?? ''),
                'primary_vehicle' => $primaryVehicle,
                'image' => $vehicleImageFilename,
                'original_image' => $vehicleImageFilename !== '' ? $vehicleImageFilename : null,
                'remove_image' => '0',
            ]);

            $payload['vehicle_id'] = $createdVehicle->id;
        }
    }

    protected function normalizePostRideRequest(Request $request, ?Ride $ride = null): void
    {
        $merge = [];

        if (!$request->filled('seats') && $request->filled('seats_total')) {
            $merge['seats'] = $request->input('seats_total');
        }

        if (!$request->filled('vehicle_mode')) {
            $merge['vehicle_mode'] = $ride && $ride->vehicle_id ? 'existing' : 'skip';
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

    public function PostRideUpdate($lang, $ride_id, Request $request)
    {
        $ride = Ride::with(['detail', 'rideStops', 'rideStopSegments'])->where('id', $ride_id)->first();
        if (!$ride || (int) $ride->added_by !== (int) auth()->id()) {
            abort(404);
        }

        $this->normalizePostRideRequest($request, $ride);

        $user = auth()->user();
        $user_id = $user->id;
        $message = $this->successMessage;

        $permissionValidationResponse = $this->validatePostRidePermissions($user, $message);
        if ($permissionValidationResponse) {
            return $permissionValidationResponse;
        }

        $featureValidationResponse = $this->validatePostRideFeatureEligibility($request, $user);
        if ($featureValidationResponse) {
            return $featureValidationResponse;
        }

        $validator = $this->buildPostRideStoreValidator($request);
        $this->appendStopDepartureAtValidation($request, $validator);
        $validatorFailureResponse = $this->handlePostRideValidationFailure($validator);
        if ($validatorFailureResponse) {
            return $validatorFailureResponse;
        }

        $origin = $request->origin['label'];
        $originCityId = $request->origin['city_id'];
        $destination = $request->destination['label'];
        $destinationCityId = $request->destination['city_id'];

        $nowDate = date('Y-m-d');
        $getRideCount = RideDetail::whereRaw('LOWER(`departure`) LIKE ? ', ['%' . $origin . '%'])
            ->where('date', $nowDate)
            ->where('default_ride', '1')
            ->whereHas('ride', function ($q) use ($nowDate, $user_id, $ride_id) {
                $q->where('date', $nowDate)
                    ->where('added_by', $user_id)
                    ->where('id', '!=', $ride_id);
            })
            ->count();

        $locationBeforeComma = explode(',', $origin);
        $getFromState = City::with('state:id,abrv,ride_limit')
            ->where('status', '1')
            ->whereRaw('LOWER(`name`) LIKE ? ', ['%' . $locationBeforeComma[0] . '%'])
            ->first();

        $stateLimitValidationResponse = $this->validatePostRideStateLimit($getFromState, (int) $getRideCount, $message);
        if ($stateLimitValidationResponse) {
            return $stateLimitValidationResponse;
        }

        $formattedDate = Carbon::parse($request->date)->format('Y-m-d');
        $formattedTime = strlen($request->time) <= 5
            ? Carbon::createFromFormat('H:i', $request->time)->format('H:i:s')
            : Carbon::parse($request->time)->format('H:i:s');

        $otherRides = Ride::where('added_by', $user_id)
            ->where('id', '!=', $ride->id)
            ->get();

        $duplicateRideResponse = $this->validatePostRideDuplicateDateTime($otherRides, $formattedDate, $formattedTime, $message, $request);
        if ($duplicateRideResponse) {
            return $duplicateRideResponse;
        }

        $adminSetting = SiteSetting::first();
        $rideDateTime = Carbon::parse($formattedDate . ' ' . $formattedTime);
        if ($rideDateTime->lte(Carbon::now()->addMinutes($adminSetting->ride_post_dead_time ?? 0))) {
            return redirect()->back()
                ->with('error', $message->ride_dead_time_text ?? 'The ride time you selected is too close. Please select a time that is more than 15 minutes in the future')
                ->withInput();
        }

        $distance = (int) $request->input('distance_meters', 0);
        $duration = (int) $request->input('duration', 0);

        if ($distance <= 0 || $duration <= 0) {
            $googleApiData = $this->getDataFromGoogleApi($origin, $destination);
            $elementStatus = isset($googleApiData['rows'][0]['elements'][0]['status'])
                ? $googleApiData['rows'][0]['elements'][0]['status']
                : null;

            if ($elementStatus === 'OK') {
                $distance = isset($googleApiData['rows'][0]['elements'][0]['distance']['value'])
                    ? (int) $googleApiData['rows'][0]['elements'][0]['distance']['value']
                    : $distance;
                $duration = isset($googleApiData['rows'][0]['elements'][0]['duration']['value'])
                    ? (int) $googleApiData['rows'][0]['elements'][0]['duration']['value']
                    : $duration;
            }
        }

        $distanceKm = $distance != 0 ? round(($distance / 1000), 2) : 0;

        $destinationReachedDate = $formattedDate;
        $destinationReachedTime = $formattedTime;
        $completedDate = $formattedDate;
        $completedTime = $formattedTime;

        if (isset($adminSetting)) {
            $destinationDateTime = Carbon::parse($formattedDate . ' ' . $formattedTime);
            $totalHours = $duration / 3600;
            $fullHours = floor($totalHours);
            $minutes = round(($totalHours - $fullHours) * 60);

            $destinationDateTime->addHours($adminSetting->destination_hours + $fullHours)
                ->addMinutes($minutes);

            $destinationReachedDate = $destinationDateTime->toDateString();
            $destinationReachedTime = $destinationDateTime->toTimeString();

            $destinationDateTime->addHours($adminSetting->ride_completed_hours);
            $completedDate = $destinationDateTime->toDateString();
            $completedTime = $destinationDateTime->toTimeString();
        }

        $newStart = Carbon::parse($formattedDate . ' ' . $formattedTime);
        $newEnd = Carbon::parse($destinationReachedDate . ' ' . $destinationReachedTime);

        $overlappingRide = Ride::where('status', '!=', 2)
            ->where('added_by', $user_id)
            ->where('id', '!=', $ride->id)
            ->whereRaw("CONCAT(date, ' ', time) < ?", [$newEnd])
            ->whereRaw("CONCAT(destination_reached_date, ' ', destination_reached_time) > ?", [$newStart])
            ->first();

        if ($overlappingRide) {
            return back()
                ->with('error', $message->overlap_ride_message ?? 'This ride overlaps with an existing ride you already have')
                ->with('heading', $message->overlap_ride_title ?? 'Ride already schedule')
                ->withInput();
        }

        $defaultRideDetail = $ride->detail;
        $currentPrice = $defaultRideDetail->price ?? null;
        $newPrice = (int) $request->input('price_minor');

        $lockedSeatCount = SeatDetail::where('ride_id', $ride->id)
            ->whereIn('status', ['booked', 'hold'])
            ->count();

        if ((int) $request->input('seats') < $lockedSeatCount) {
            return back()
                ->with('error', 'You cannot reduce seats below the number already reserved for this ride.')
                ->with('heading', 'Seats Update Not Allowed')
                ->withInput();
        }

        $hasBookings = Booking::where('ride_id', $ride->id)
            ->bookedOrCompleted()
            ->withActivePassenger()
            ->exists();

        if ($hasBookings && $currentPrice !== null && (int) $currentPrice !== $newPrice) {
            return back()
                ->with('error', 'You cannot change the price once passengers have booked this ride.')
                ->with('heading', 'Price Change Not Allowed')
                ->withInput();
        }

        $max_back_seats = $request->filled('max_back_seats') ? $request->max_back_seats : 0;
        $accept_more_luggage = $request->filled('accept_more_luggage') ? $request->accept_more_luggage : 0;
        $open_customized = $request->filled('open_customized') ? $request->open_customized : 0;
        $features = implode('=', $request->input('features', []));

        $filename = $ride->getRawOriginal('car_image') ?? $ride->getRawOriginal('car_image_original') ?? '';
        if ($request->hasFile('vehicle_image')) {
            $file = $request->file('vehicle_image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('car_images'), $filename);
        } elseif ($request->has('existing_image')) {
            $filename = (string) $request->input('existing_image');
        }

        $vehiclePayload = [
            'vehicle_mode' => $request->vehicle_mode ?? ($ride->vehicle_id ? 'existing' : 'skip'),
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
        $this->processPostRideVehicleMode($request, $vehiclePayload);
        extract($vehiclePayload, EXTR_OVERWRITE);

        if (($vehicle_mode ?? 'skip') === 'skip') {
            $filename = '';
        }

        $recurring = $request->filled('recurring') ? $request->recurring : 0;
        if ($recurring == 0) {
            $recurring_type = '';
            $recurring_trips = '';
        } else {
            $recurring_type = $request->recurring_type;
            $recurring_trips = $request->recurring_trips;
        }

        DB::transaction(function () use (
            $request,
            $ride,
            $formattedDate,
            $formattedTime,
            $recurring,
            $recurring_type,
            $recurring_trips,
            $vehicle_mode,
            $vehicle_id,
            $make,
            $model,
            $vehicle_type,
            $year,
            $color,
            $license_no,
            $power_type,
            $filename,
            $max_back_seats,
            $accept_more_luggage,
            $open_customized,
            $features,
            $user_id,
            $completedDate,
            $completedTime,
            $destinationReachedDate,
            $destinationReachedTime,
            $origin,
            $originCityId,
            $destination,
            $destinationCityId,
            $distanceKm,
            $duration,
            $newPrice
        ) {
            $ride->update([
                'departure' => '',
                'departure_lat' => '',
                'departure_lng' => '',
                'departure_place' => '',
                'departure_route' => '',
                'departure_zipcode' => '',
                'departure_city' => '',
                'departure_state' => '',
                'departure_state_short' => '',
                'departure_country' => '',
                'destination' => '',
                'destination_lat' => '',
                'destination_lng' => '',
                'destination_place' => '',
                'destination_route' => '',
                'destination_zipcode' => '',
                'destination_city' => '',
                'destination_state' => '',
                'destination_state_short' => '',
                'destination_country' => '',
                'total_distance' => '',
                'total_time' => '',
                'date' => $formattedDate,
                'time' => $formattedTime,
                'completed_date' => $completedDate,
                'completed_time' => $completedTime,
                'destination_reached_date' => $destinationReachedDate,
                'destination_reached_time' => $destinationReachedTime,
                'recurring' => $recurring,
                'recurring_type' => $recurring_type,
                'recurring_trips' => $recurring_trips,
                'details' => $request->details,
                'seats' => $request->seats,
                'vehicle_mode' => $vehicle_mode ?? ($request->vehicle_mode ?? 'skip'),
                'vehicle_id' => $vehicle_id ?? null,
                'make' => $make,
                'model' => $model,
                'vehicle_type' => Ride::normalizeRideVehicleTypeId($vehicle_type),
                'year' => $year,
                'color' => $color,
                'license_no' => $license_no,
                'car_type' => $power_type,
                'car_image' => $filename,
                'car_image_original' => $filename,
                'smoke' => $request->smoke,
                'animal_friendly' => $request->animal_friendly,
                'features' => $features,
                'booking_method' => $request->booking_method,
                'booking_type' => $request->booking_type,
                'max_back_seats' => $max_back_seats,
                'luggage' => $request->luggage,
                'accept_more_luggage' => $accept_more_luggage,
                'open_customized' => $open_customized,
                'price' => '',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'added_by' => $user_id,
                'until_date' => null,
                'until_limit' => '',
                'pickup' => $request->pickup,
                'dropoff' => $request->dropoff,
                'middle_seats' => $request->middle_seats,
                'back_seats' => $request->back_seats,
            ]);

            $this->syncRideSeatDetails($ride, (int) $request->seats);

            $rideDetail = RideDetail::where('ride_id', $ride->id) ->first();

            if (!$rideDetail) {
                $rideDetail = new RideDetail();
            }

            $rideDetail->ride_id = $ride->id;
            $rideDetail->departure = $origin;
            $rideDetail->origin_city_id = $originCityId;
            $rideDetail->destination = $destination;
            $rideDetail->destination_city_id = $destinationCityId;
            $rideDetail->pickup = $request->pickup ?? null;
            $rideDetail->dropoff = $request->dropoff ?? null;
            $rideDetail->default_ride = 1;
            $rideDetail->total_distance = $distanceKm;
            $rideDetail->total_duration = $duration;
            $rideDetail->price = $newPrice;
            $rideDetail->time = $formattedTime;
            $rideDetail->date = $formattedDate;
            $rideDetail->destination_time = $destinationReachedTime;
            $rideDetail->destination_date = $destinationReachedDate;
            $rideDetail->completed_time = $completedTime;
            $rideDetail->completed_date = $completedDate;
            $rideDetail->save();

            $this->syncRideStopsAndSegments(
                $ride,
                $request,
                $origin,
                $originCityId,
                $destination,
                $destinationCityId,
                $destinationReachedDate,
                $destinationReachedTime
            );
        });

        return redirect()
            ->route('my_rides', ['lang' => $this->selectedLanguage->abbreviation])
            ->with([
                'message' => $this->successMessage->post_ride_update_message ?? 'Ride updated successfully',
                'id' => $ride->id,
            ]);
    }

    protected function syncRideSeatDetails(Ride $ride, int $seatCount): void
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

    protected function syncRideStopsAndSegments(
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
        $stopFrom = is_array($request->input('stop_from')) ? $request->input('stop_from') : [];
        $stopTo = is_array($request->input('stop_to')) ? $request->input('stop_to') : [];
        $stopPriceMinor = is_array($request->input('stop_price_minor')) ? $request->input('stop_price_minor') : [];

        $destinationPriceDeltaMinor = 0;
        $lastLegFromLabel = !empty($requestStops)
            ? trim((string) ($requestStops[count($requestStops) - 1]['label'] ?? ''))
            : $originLabel;

        foreach ($stopFrom as $idx => $fromLabel) {
            if (
                trim((string) $fromLabel) === $lastLegFromLabel &&
                trim((string) ($stopTo[$idx] ?? '')) === $destinationLabel
            ) {
                $destinationPriceDeltaMinor = (int) ($stopPriceMinor[$idx] ?? 0);
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

        foreach ($stopFrom as $idx => $fromLabel) {
            $fromLabel = trim((string) $fromLabel);
            $toLabel = trim((string) ($stopTo[$idx] ?? ''));
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
                'price_minor' => (int) ($stopPriceMinor[$idx] ?? 0),
            ]);
        }
    }

    public function PostRideStore(Request $request)
    {
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $rides = Ride::where('added_by', $user_id)->get();

        $message = $this->successMessage;

        if (!$request->filled('seats') && $request->filled('seats_total')) {
            $request->merge([
                'seats' => $request->input('seats_total'),
            ]);
        }

        $permissionValidationResponse = $this->validatePostRidePermissions($user, $message);
        if ($permissionValidationResponse) {
            return $permissionValidationResponse;
        }

        $featureValidationResponse = $this->validatePostRideFeatureEligibility($request, $user);
        if ($featureValidationResponse) {
            return $featureValidationResponse;
        }

        // form validation
        $validator = $this->buildPostRideStoreValidator($request);
        $this->appendStopDepartureAtValidation($request, $validator);
        $validatorFailureResponse = $this->handlePostRideValidationFailure($validator);
        if ($validatorFailureResponse) {
            return $validatorFailureResponse;
        }

        $origin = $request->origin['label'];
        $originCityId = $request->origin['city_id'];
        $destination = $request->destination['label'];
        $destinationCityId = $request->destination['city_id'];

        $nowDate = date('Y-m-d');
        $getRideCount = RideDetail::whereRaw('LOWER(`departure`) LIKE ? ', ['%' . $origin . '%'])->where('date', $nowDate)->where('default_ride', '1')->whereHas('ride', function ($q) use ($nowDate, $user_id) {
            $q->where('date', $nowDate)->where('added_by', $user_id);
        })->count();

        $getRideCount = isset($getRideCount) ? $getRideCount : 0;

        $locationBeforeComma = explode(',', $origin);

        $getFromState = City::with('state:id,abrv,ride_limit')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ', ['%' . $locationBeforeComma[0] . '%'])->first();


        $stateLimitValidationResponse = $this->validatePostRideStateLimit($getFromState, $getRideCount, $message);
        if ($stateLimitValidationResponse) {
            return $stateLimitValidationResponse;
        }


        $formattedDate = Carbon::parse($request->date)->format('Y-m-d');
        $formattedTime = strlen($request->time) <= 5
            ? Carbon::createFromFormat('H:i', $request->time)->format('H:i:s')
            : Carbon::parse($request->time)->format('H:i:s');

        // ??
        $duplicateRideResponse = $this->validatePostRideDuplicateDateTime($rides, $formattedDate, $formattedTime, $message, $request);
        if ($duplicateRideResponse) {
            return $duplicateRideResponse;
        }



        $adminSetting = SiteSetting::first();
        $distance = (int) $request->input('distance_meters', 0);
        $duration = (int) $request->input('duration', 0);

        if ($distance <= 0 || $duration <= 0) {
            $googleApiData = $this->getDataFromGoogleApi($origin, $destination);
            $elementStatus = isset($googleApiData['rows'][0]['elements'][0]['status'])
                ? $googleApiData['rows'][0]['elements'][0]['status']
                : null;

            if ($elementStatus === 'OK') {
                $distance = isset($googleApiData['rows'][0]['elements'][0]['distance']['value'])
                    ? (int) $googleApiData['rows'][0]['elements'][0]['distance']['value']
                    : $distance;
                $duration = isset($googleApiData['rows'][0]['elements'][0]['duration']['value'])
                    ? (int) $googleApiData['rows'][0]['elements'][0]['duration']['value']
                    : $duration;
            }
        }
        if ($distance != 0) {
            $distance = round(($distance / 1000), 2);
        }

        if (isset($adminSetting)) {

            $rideDateTime = Carbon::parse("$request->date $request->time");

            // $rideDateTime->addHours($adminSetting->destination_hours);
            // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
            $totalHours = $duration / 3600;
            $fullHours = floor($totalHours);
            $minutes = round(($totalHours - $fullHours) * 60);
            $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                ->addMinutes($minutes);
            $destinationReachedDate = $rideDateTime->toDateString();
            $destinationReachedTime = $rideDateTime->toTimeString();
        }

        $newStart = Carbon::parse("$request->date $request->time");
        $newEnd = Carbon::parse("$destinationReachedDate $destinationReachedTime");

        $rides = Ride::where('status', '!=', 2)
            ->where('added_by', $user_id)
            ->whereRaw("CONCAT(date, ' ', time) < ?", [$newEnd])
            ->whereRaw("CONCAT(destination_reached_date, ' ', destination_reached_time) > ?", [$newStart])
            ->first();

        if (isset($rides) && !empty($rides)) {
            $oldInput = $request->all();
            return back()->with('error', $message->overlap_ride_message ?? 'This ride overlaps with an existing ride you already have')->with('heading', $message->overlap_ride_title ?? 'Ride already schedule')->withInput($oldInput)->with('uploaded_image', $filename ?? null);
        }
        $rideDateTime = Carbon::parse($formattedDate . ' ' . $formattedTime);

        if ($rideDateTime->lte(Carbon::now()->addMinutes($adminSetting->ride_post_dead_time ?? 0))) {
            return redirect()->back()
                ->with('error', $message->ride_dead_time_text ?? 'The ride time you selected is too close. Please select a time that is more than 15 minutes in the future')
                ->withInput();
        }

        $max_back_seats = $request->filled('max_back_seats') ? $request->max_back_seats : 0;
        $accept_more_luggage = $request->filled('accept_more_luggage') ? $request->accept_more_luggage : 0;
        $open_customized = $request->filled('open_customized') ? $request->open_customized : 0;

        // Join the selected checkboxes with semicolons.
        $features = implode('=', $request->input('features', []));

        if ($request->hasFile('vehicle_image')) {
            $file = $request->file('vehicle_image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('car_images');
            $file->move($destination_path, $filename);
        } elseif ($request->has('existing_image')) {
            $filename = $request->input('existing_image');
        } else {
            $filename = '';
        }

        $vehiclePayload = [
            'vehicle_mode' => $request->vehicle_mode,
            'filename' => $filename ?? '',
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
        $this->processPostRideVehicleMode($request, $vehiclePayload);
        extract($vehiclePayload, EXTR_OVERWRITE);

        $recurring = $request->filled('recurring') ? $request->recurring : 0;
        if ($recurring == 0) {
            $recurring_type = '';
            $recurring_trips = '';
        } else {
            $recurring_type = $request->recurring_type;
            $recurring_trips = $request->recurring_trips;
        }


        $initialRide = Ride::create([
            'departure' => "",
            'departure_lat' => '',
            'departure_lng' => '',
            'departure_place' => '',
            'departure_route' => '',
            'departure_zipcode' => '',
            'departure_city' => '',
            'departure_state' => '',
            'departure_state_short' => '',
            'departure_country' => '',

            'destination' => "",
            'destination_lat' => '',
            'destination_lng' => '',
            'destination_place' => '',
            'destination_route' => '',
            'destination_zipcode' => '',
            'destination_city' => '',
            'destination_state' => '',
            'destination_state_short' => '',
            'destination_country' => '',

            'total_distance' => "",
            'total_time' => "",
            'date' => $formattedDate,
            'time' => $formattedTime,

            'recurring' => $recurring,
            'recurring_type' => $recurring_type,
            'recurring_trips' => $recurring_trips,
            'details' => $request->details,
            'seats' => $request->seats,
            'vehicle_mode' => $vehicle_mode ?? ($request->vehicle_mode ?? 'skip'),
            // 'skip_vehicle' => (($vehicle_mode ?? $request->vehicle_mode ?? 'skip') === 'skip') ? '1' : '0',
            // 'add_vehicle' => (($vehicle_mode ?? $request->vehicle_mode ?? 'skip') === 'add_new') ? '1' : '0',
            // 'added_vehicle' => (($vehicle_mode ?? $request->vehicle_mode ?? 'skip') === 'existing') ? '1' : '0',
            'vehicle_id' => $vehicle_id ?? null,
            'make' => $make,
            'model' => $model,
            'vehicle_type' => Ride::normalizeRideVehicleTypeId($vehicle_type),
            'year' => $year,
            'color' => $color,
            'license_no' => $license_no,
            'car_type' => $power_type,
            'car_image' => $filename,
            'car_image_original' => $filename,
            'smoke' => $request->smoke,
            'animal_friendly' => $request->animal_friendly,
            'features' => $features,
            'booking_method' => $request->booking_method,
            'booking_type' => $request->booking_type,
            'max_back_seats' => $max_back_seats,
            'luggage' => $request->luggage,
            'accept_more_luggage' => $accept_more_luggage,
            'open_customized' => $open_customized,
            'price' => "",
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'added_by' => $user_id,
            'until_date' => null,
            'until_limit' => '',

            'pickup' => $request->pickup,
            'dropoff' => $request->dropoff,

            'middle_seats' => $request->middle_seats,
            'back_seats' => $request->back_seats,
            'added_on' => now(),
        ]);

        //Add Seat Detail
        for ($i = 1; $i <= $initialRide->seats; $i++) {
            $seatDetail = new SeatDetail;
            $seatDetail->ride_id = $initialRide->id;
            $seatDetail->seat_number = $i;
            $seatDetail->status = 'pending';
            $seatDetail->save();
        }

        //Add Ride Detail

        $rideDetail = new RideDetail();
        $rideDetail->ride_id = $initialRide->id;
        $rideDetail->departure = $origin;
        $rideDetail->origin_city_id = $originCityId;
        $rideDetail->destination = $destination;
        $rideDetail->destination_city_id = $destinationCityId;
        $rideDetail->pickup = $request->pickup ?? null;
        $rideDetail->dropoff = $request->dropoff ?? null;
        $rideDetail->default_ride = 1;
        $rideDetail->total_distance = $distance;
        $rideDetail->total_duration = $duration;
        $rideDetail->price = $request->price_minor;
        $rideDetail->time = $formattedTime;
        $rideDetail->date = $formattedDate;

        if (isset($adminSetting)) {

            if (isset($initialRide->date) && isset($initialRide->time)) {
                $rideDateTime = Carbon::parse("$initialRide->date $initialRide->time");
                $apiTime = 0;
                if ($duration != 0) {
                    $apiTime = round(($duration / 3600), 2);
                }

                // $rideDateTime->addHours($adminSetting->destination_hours);
                // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                $totalHours = $duration / 3600;
                $fullHours = floor($totalHours);
                $minutes = round(($totalHours - $fullHours) * 60);
                $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                    ->addMinutes($minutes);
                $destinationReachedDate = $rideDateTime->toDateString();
                $destinationReachedTime = $rideDateTime->toTimeString();


                $rideDateTime->addHours($adminSetting->ride_completed_hours);
                $completedDate = $rideDateTime->toDateString();
                $completedTime = $rideDateTime->toTimeString();

                $initialRide->completed_date = $completedDate ?? '';
                $initialRide->completed_time = $completedTime;
                $initialRide->destination_reached_date = $destinationReachedDate;
                $initialRide->destination_reached_time = $destinationReachedTime;
                $initialRide->save();

                $rideDetail->destination_time = $destinationReachedTime;
                $rideDetail->destination_date = $destinationReachedDate;
                $rideDetail->completed_time = $completedTime;
                $rideDetail->completed_date = $completedDate;
            }
        }
        $rideDetail->save();

        $requestStops = is_array($request->input('stops')) ? $request->input('stops') : [];
        $stopFrom = is_array($request->input('stop_from')) ? $request->input('stop_from') : [];
        $stopTo = is_array($request->input('stop_to')) ? $request->input('stop_to') : [];
        $stopPriceMinor = is_array($request->input('stop_price_minor')) ? $request->input('stop_price_minor') : [];

        $originLabel = $request->origin['label'];
        $destinationLabel = $request->destination['label'];
        $originCityId = $request->origin['city_id'];
        $destinationCityId = $request->destination['city_id'];
        $destinationPriceDeltaMinor = 0;
        $lastLegFromLabel = !empty($requestStops)
            ? trim((string) ($requestStops[count($requestStops) - 1]['label'] ?? ''))
            : $originLabel;

        foreach ($stopFrom as $idx => $fromLabel) {
            if (
                trim((string) $fromLabel) === $lastLegFromLabel &&
                trim((string) ($stopTo[$idx] ?? '')) === $destinationLabel
            ) {
                $destinationPriceDeltaMinor = (int) ($stopPriceMinor[$idx] ?? 0);
                break;
            }
        }

        $stopRecords = [
            [
                'stop_order' => 1,
                'city_id' => $originCityId,
                'label' => $originLabel,
                'departure_at' => Carbon::parse($formattedDate . ' ' . $formattedTime),
                'pickup_dropoff_location' => $request->pickup ?? null,
                'eta_at' => null,
                'price_delta_minor' => 0,
                'seats_available' => $initialRide->seats,
                'is_pickup' => true,
                'is_dropoff' => false,
            ],
        ];

        foreach ($requestStops as $index => $stop) {
            $label = trim((string) ($stop['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $stopRecords[] = [
                'stop_order' => count($stopRecords) + 1,
                'city_id' => !empty($stop['city_id']) ? (int) $stop['city_id'] : null,
                'label' => $label,
                'departure_at' => !empty($stop['departure_at']) ? Carbon::parse($stop['departure_at']) : null,
                'pickup_dropoff_location' => null,
                'eta_at' => null,
                'price_delta_minor' => (int) ($stop['price_delta_minor'] ?? 0),
                'seats_available' => $initialRide->seats,
                'is_pickup' => isset($stop['is_pickup']) ? (bool) $stop['is_pickup'] : true,
                'is_dropoff' => isset($stop['is_dropoff']) ? (bool) $stop['is_dropoff'] : true,
            ];
        }

        $destinationEtaAt = $this->resolveDestinationStopEtaAt(
            $requestStops,
            $originLabel,
            $destinationLabel,
            Carbon::parse($formattedDate . ' ' . $formattedTime),
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
            'seats_available' => $initialRide->seats,
            'is_pickup' => false,
            'is_dropoff' => true,
        ];

        $stopIdByLabel = [];
        foreach ($stopRecords as $stopRecord) {
            $savedStop = RideStop::create([
                'ride_id' => $initialRide->id,
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

        foreach ($stopFrom as $idx => $fromLabel) {
            $fromLabel = trim((string) $fromLabel);
            $toLabel = trim((string) ($stopTo[$idx] ?? ''));
            if ($fromLabel === '' || $toLabel === '') {
                continue;
            }

            $fromStopId = $stopIdByLabel[mb_strtolower($fromLabel)] ?? null;
            $toStopId = $stopIdByLabel[mb_strtolower($toLabel)] ?? null;

            if (!$fromStopId || !$toStopId || $fromStopId === $toStopId) {
                continue;
            }

            RideStopSegment::create([
                'ride_id' => $initialRide->id,
                'from_stop_id' => $fromStopId,
                'to_stop_id' => $toStopId,
                'price_minor' => (int) ($stopPriceMinor[$idx] ?? 0),
            ]);
        }

        $recurring_id = $initialRide->id;

        // Check if the ride is recurring
        if ($recurring !== 0) {
            // Determine the frequency and number of recurring trips
            $frequency = $request->input('recurring_type');
            $numRecurringTrips = $request->input('recurring_trips');
            $templateRide = $initialRide;
            $sourceRideDetails = RideDetail::where('ride_id', $recurring_id)->get();
            $sourceRideStops = RideStop::where('ride_id', $recurring_id)->orderBy('stop_order')->get();
            $sourceRideSegments = RideStopSegment::where('ride_id', $recurring_id)->get();
            $offsetDays = $frequency === 'Daily' ? 1 : 7;

            // Create additional rides based on the recurring settings
            for ($i = 1; $i <= $numRecurringTrips; $i++) {
                $nextDate = Carbon::parse($templateRide->date)->addDays($offsetDays * $i);
                $nextCompletedDate = !empty($templateRide->completed_date)
                    ? Carbon::parse($templateRide->completed_date)->addDays($offsetDays * $i)
                    : null;
                $nextDestinationReachedDate = !empty($templateRide->destination_reached_date)
                    ? Carbon::parse($templateRide->destination_reached_date)->addDays($offsetDays * $i)
                    : null;

                $initialRide = Ride::create([
                    'date' => $nextDate->format('Y-m-d'),
                    'time' => $request->time,
                    'completed_date' => $nextCompletedDate ? $nextCompletedDate->format('Y-m-d') : null,
                    'completed_time' => $templateRide->completed_time,
                    'destination_reached_date' => $nextDestinationReachedDate ? $nextDestinationReachedDate->format('Y-m-d') : null,
                    'destination_reached_time' => $templateRide->destination_reached_time,

                    'recurring' => $recurring,
                    'recurring_type' => '',
                    'recurring_trips' => '',
                    'recurring_id' => $recurring_id,
                    'details' => $request->details,
                    'seats' => $request->seats,

                    'skip_vehicle' => $skip_vehicle,
                    'add_vehicle' => $add_vehicle,
                    'added_vehicle' => $added_vehicle,
                    'vehicle_id' => $vehicle_id ?? null,
                    'make' => $make,
                    'model' => $model,
                    'vehicle_type' => Ride::normalizeRideVehicleTypeId($vehicle_type),
                    'year' => $year,
                    'color' => $color,
                    'license_no' => $license_no,
                    'car_type' => $power_type,
                    'car_image' => $filename,
                    'car_image_original' => $filename,
                    'smoke' => $request->smoke,
                    'animal_friendly' => $request->animal_friendly,
                    'features' => $features,
                    'booking_method' => $request->booking_method,
                    'booking_type' => $request->booking_type,
                    'max_back_seats' => $max_back_seats,
                    'luggage' => $request->luggage,
                    'accept_more_luggage' => $accept_more_luggage,
                    'open_customized' => $open_customized,
                    'price' => "",
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                    'added_by' => $user_id,
                    'until_date' => null,
                    'until_limit' => '',

                    'pickup' => $request->pickup,
                    'dropoff' => $request->dropoff,

                    'middle_seats' => $request->middle_seats,
                    'back_seats' => $request->back_seats,
                    'added_on' => now(),
                ]);


                for ($j = 1; $j <= $initialRide->seats; $j++) {
                    $seatDetail = new SeatDetail;
                    $seatDetail->ride_id = $initialRide->id;
                    $seatDetail->seat_number = $j;
                    $seatDetail->status = 'pending';
                    $seatDetail->save();
                }

                foreach ($sourceRideDetails as $key => $getRideDetail) {
                    $rideDetail = new RideDetail();
                    $rideDetail->ride_id = $initialRide->id;
                    $rideDetail->departure = $getRideDetail->departure;
                    $rideDetail->origin_city_id = $getRideDetail->origin_city_id;
                    $rideDetail->destination = $getRideDetail->destination;
                    $rideDetail->destination_city_id = $getRideDetail->destination_city_id;
                    $rideDetail->pickup = $getRideDetail->pickup ?? null;
                    $rideDetail->dropoff = $getRideDetail->dropoff ?? null;
                    $rideDetail->default_ride = $getRideDetail->default_ride;
                    $rideDetail->total_distance = $getRideDetail->total_distance;
                    $rideDetail->total_duration = $getRideDetail->total_duration;
                    $rideDetail->price = $getRideDetail->price;
                    $rideDetail->time = $getRideDetail->time;
                    $rideDetail->date = $nextDate->format('Y-m-d');
                    $rideDetail->destination_time = $initialRide->destination_reached_time;
                    $rideDetail->destination_date = $initialRide->destination_reached_date;
                    $rideDetail->completed_time = $initialRide->completed_time;
                    $rideDetail->completed_date = $initialRide->completed_date;
                    $rideDetail->save();
                }

                $recurringStopIdMap = [];
                foreach ($sourceRideStops as $sourceRideStop) {
                    $departureAt = null;
                    if (!empty($sourceRideStop->departure_at)) {
                        try {
                            $departureAt = Carbon::parse($sourceRideStop->departure_at)->addDays($offsetDays * $i);
                        } catch (\Throwable $e) {
                            $departureAt = $sourceRideStop->departure_at;
                        }
                    }

                    $etaAt = null;
                    if (!empty($sourceRideStop->eta_at)) {
                        try {
                            $etaAt = Carbon::parse($sourceRideStop->eta_at)->addDays($offsetDays * $i);
                        } catch (\Throwable $e) {
                            $etaAt = $sourceRideStop->eta_at;
                        }
                    }

                    $newRideStop = RideStop::create([
                        'ride_id' => $initialRide->id,
                        'stop_order' => $sourceRideStop->stop_order,
                        'city_id' => $sourceRideStop->city_id,
                        'label' => $sourceRideStop->label,
                        'lat' => $sourceRideStop->lat,
                        'lng' => $sourceRideStop->lng,
                        'departure_at' => $departureAt,
                        'pickup_dropoff_location' => $sourceRideStop->pickup_dropoff_location,
                        'eta_at' => $etaAt,
                        'price_delta_minor' => $sourceRideStop->price_delta_minor,
                        'seats_available' => $sourceRideStop->seats_available,
                        'is_pickup' => $sourceRideStop->is_pickup,
                        'is_dropoff' => $sourceRideStop->is_dropoff,
                    ]);

                    $recurringStopIdMap[$sourceRideStop->id] = $newRideStop->id;
                }

                foreach ($sourceRideSegments as $sourceRideSegment) {
                    $fromStopId = $recurringStopIdMap[$sourceRideSegment->from_stop_id] ?? null;
                    $toStopId = $recurringStopIdMap[$sourceRideSegment->to_stop_id] ?? null;

                    if (!$fromStopId || !$toStopId) {
                        continue;
                    }

                    RideStopSegment::create([
                        'ride_id' => $initialRide->id,
                        'from_stop_id' => $fromStopId,
                        'to_stop_id' => $toStopId,
                        'price_minor' => $sourceRideSegment->price_minor,
                        'distance_meters' => $sourceRideSegment->distance_meters,
                        'duration_seconds' => $sourceRideSegment->duration_seconds,
                    ]);
                }
            }
        }
        if (isset($user->email_notification) && $user->email_notification == 1) {
            $features = explode('=', $initialRide->features);

            $data = [
                'username' => $user->first_name,
                'from' => $origin,
                'to' => $destination,
                'on' => $request->date,
                'at' => $request->time,
                'seats' => $request->seats,
                'price' => $request->price_minor,
                'redirect' => route('my_rides', ['lang' => $this->selectedLanguage->abbreviation]),
            ];

            if ($initialRide->isPinkRide() && $initialRide->isExtraCareRide()) {
                // Both Pink and Extra+
                Mail::to($user->email)->queue(new PinkExtraCareRideMail($data));
            } elseif ($initialRide->isPinkRide()) {
                // Only Pink Ride
                Mail::to($user->email)->queue(new PinkRideMail($data));
            } elseif ($initialRide->isExtraCareRide()) {
                // Only Extra+ Ride
                Mail::to($user->email)->queue(new ExtraCareRideMail($data));
            } else {
                // Regular ride (existing email)
                Mail::to($user->email)->queue(new RidePostedMail($data));
            }
        }

        // Notification
        $hasVehicle = !empty($initialRide->vehicle_id);

        // Determine ride type
        if ($initialRide->isPinkRide() && $initialRide->isExtraCareRide()) {
            $type = 'pink_extra_care';
        } elseif ($initialRide->isPinkRide()) {
            $type = 'pink';
        } elseif ($initialRide->isExtraCareRide()) {
            $type = 'extra_care';
        } else {
            $type = 'standard';
        }

        // Message config
        $messageConfig = [
            'standard' => [
                'key' => 'ride_live_standard',
                'fallback' => 'Your ride is now live on ProximaRide'
            ],
            'pink' => [
                'key' => 'ride_live_pink',
                'fallback' => 'Your Pink Ride is now live on ProximaRide'
            ],
            'extra_care' => [
                'key' => 'ride_live_extra_care',
                'fallback' => 'Your Extra+ Ride is now live on ProximaRide'
            ],
            'pink_extra_care' => [
                'key' => 'ride_live_pink_extra_care',
                'fallback' => 'Your Pink and Extra+ ride is now live on ProximaRide'
            ]
        ];

        // Choose correct notification key
        $key = $hasVehicle ? $messageConfig[$type]['key'] : 'ride_live_requires_vehicle';
        $fallback = $hasVehicle
            ? $messageConfig[$type]['fallback']
            : 'Add your vehicle to make your ride live';

        // Generate message
        $message = getNotificationMessageText($key, $user, [], $fallback);

        // Ride detail
        $rideDetail = $initialRide->rideDetail[0];

        // Create notification
        $notification = Notification::create([
            'ride_id' => $initialRide->id,
            'posted_by' => $user->id,
            'message' => $message,
            'status' => 'upcoming',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $rideDetail->id,
            'departure' => $rideDetail->departure,
            'destination' => $rideDetail->destination
        ]);

        // Send FCM
        $body = $notification->message;
        $fcmService = new FCMService();

        // Collect tokens
        $tokens = [];

        // mobile token
        if ($user->mobile_fcm_token) {
            $tokens[] = $user->mobile_fcm_token;
        }

        // additional tokens
        $tokens = array_merge(
            $tokens,
            FCMToken::where('user_id', $user->id)->pluck('token')->toArray()
        );

        // send notifications
        foreach ($tokens as $token) {
            try {
                $fcmService->sendNotification($token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: $token. Error: " . $e->getMessage());
            }
        }

        // Prepare redirect data
        $redirectData = [
            'message' => $this->successMessage->ride_post_message,
            'id' => $initialRide->id
        ];

        return redirect()->route('my_rides', ['lang' => $this->selectedLanguage->abbreviation])->with($redirectData)->withInput();
    }

    



    protected function validatePostRidePermissions(User $user, $message)
    {
        if ($user->isBlockedPostRide()) {
            return back()->with('message', $message->block_post_ride_message);
        }

        if (!$user->hasCustomProfileImage()) {
            return back()->with('message', $message->profile_photo_required_message ?? 'For posting a ride profile photo is required');
        }

        if ($user->isSuspended()) {
            return back()->with('message', $this->successMessage['admin_block_account_message'] ?? 'Your account has been suspended by the admin');
        }

        return null;
    }

    protected function validatePostRideFeatureEligibility(Request $request, User $user)
    {
        if (!$request->has('features') || !is_array($request->features)) {
            return null;
        }

        $featureRide = new Ride();

        if ($featureRide->isPinkRide()) {
            $pinkRideError = $user->pinkRideEligibilityError();
            if ($pinkRideError) {
                return back()->with('message', $pinkRideError);
            }
        }

        if ($featureRide->isExtraCareRide()) {
            $extraCareError = $user->extraRideEligibilityError();
            if ($extraCareError) {
                return back()->with('message', $extraCareError);
            }
        }

        return null;
    }

    protected function buildPostRideStoreValidator(Request $request)
    {

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
            'seats' => 'required',
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
            'agree_terms' => 'accepted',
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
            'stops.*.departure_at' => ['required_with:stops', 'date_format:Y-m-d H:i'],
            'stops.*.pickup_dropoff_location' => ['required_with:stops', 'string', 'max:500'],
            'stops.*.price' => ['nullable', 'integer', 'min:0'],
        ], [], []);
    }

    protected function appendStopDepartureAtValidation(Request $request, $validator): void
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

    protected function validatePostRideDuplicateDateTime($rides, string $formattedDate, string $formattedTime, $message, Request $request)
    {
        foreach ($rides as $existingRide) {
            if ($existingRide->date == $formattedDate && $existingRide->time == $formattedTime) {
                return back()
                    ->with('error', $message->ride_schedule_message)
                    ->with('heading', $message->overlap_ride_title ?? 'Ride already schedule')
                    ->withInput($request->all());
            }
        }

        return null;
    }

    protected function processPostRideVehicleMode(Request $request, array &$payload): void
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

            Log::info('Google Maps API Success', [
                'from' => $from,
                'to' => $to,
                'distance_meters' => $distance,
                'distance_km' => round($distance / 1000, 2),
                'distance_text' => $distanceText,
                'duration_seconds' => $duration,
                'duration_text' => $durationText,
                'status' => $data['status']
            ]);
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
