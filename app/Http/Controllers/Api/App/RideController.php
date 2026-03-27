<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\RideController as WebRideController;
use App\Mail\ExtraCareRideMail;
use App\Mail\PinkExtraCareRideMail;
use App\Mail\PinkRideMail;
use App\Mail\RidePostedMail;
use App\Models\Booking;
use App\Models\CancelRideSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Models\Rating;
use App\Models\RecentSearch;
use App\Models\ReviewSetting;
use App\Models\Ride;
use App\Models\RideDetail;
use App\Models\RideStop;
use App\Models\RideStopSegment;
use App\Models\City;
use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\Vehicle;
use App\Models\SiteSetting;
use App\Models\RideDetailPageSettingDetail;
use App\Models\TripsPageSettingDetail;
use App\Models\MyPassengerSettingDetail;
use App\Models\NoShowHistory;
use App\Models\Notification;
use App\Models\PostRidePageError;
use App\Models\Step1PageSettingDetail;
use App\Models\SeatDetail;
use App\Models\User;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RideController extends WebRideController
{
    use StatusResponser;

    public function SearchRide(Request $request)
    {
        $rides = collect();

        $selectedLanguage = $this->resolveApiLanguage();
        $findRidePage = $this->getApiFindRidePage($selectedLanguage);
        $postRidePage = $this->getApiPostRidePage($selectedLanguage);

        $user = Auth::guard('sanctum')->user();

        if ($user && $request->filled('from') && $request->filled('to')) {
            $existingSearch = RecentSearch::where('user_id', $user->id)
                ->where('from', 'like', '%' . $request->from . '%')
                ->where('to', 'like', '%' . $request->to . '%')
                ->first();

            if ($existingSearch) {
                $existingSearch->touch();
            } else {
                RecentSearch::create([
                    'from' => $request->from,
                    'to' => $request->to,
                    'user_id' => $user->id,
                ]);
            }
        }

        $searchFilters = $this->buildAppSearchRideFilters($request, $user);
        if ($this->shouldRunAppSearch($searchFilters)) {
            $rides = Ride::searchRides($searchFilters, $user);
            $rides = $this->prepareAppSearchRideResults($rides, $user, $request);
        }

        Log::info('search', [
            'filters' => $searchFilters,
            'should_run' => $this->shouldRunAppSearch($searchFilters),
            'count' => method_exists($rides, 'count') ? $rides->count() : count($rides),
            'total' => method_exists($rides, 'total') ? $rides->total() : count($rides),
            'rides' => method_exists($rides, 'items') ? $rides->items() : $rides->toArray(),
        ]);

        $defaultLanguage = $this->defaultLang;
        $defaultPostRidePage = PostRidePageSettingDetail::where('language_id', $defaultLanguage->id)->first();
        $rideFeatureOptionGroups = $this->getRideFeatureOptionGroups($selectedLanguage?->id, $defaultLanguage?->id);
        $bookingMethodAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'booking_method');
        $paymentMethodAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'payment_method');
        $smokingAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'smoking_allowed');
        $petsAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'pets_allowed');
        $luggageAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'luggage_size');

        $bookingMethodImages = $bookingMethodAssets['images'];
        $bookingMethodTooltips = $bookingMethodAssets['tooltips'];
        $paymentMethodImages = $paymentMethodAssets['images'];
        $paymentMethodTooltips = $paymentMethodAssets['tooltips'];
        $smokeImages = $smokingAssets['images'];
        $smokeTooltips = $smokingAssets['tooltips'];
        $petsImages = $petsAssets['images'];
        $petsTooltips = $petsAssets['tooltips'];
        $luggageImages = $luggageAssets['images'];
        $luggageTooltips = $luggageAssets['tooltips'];
        $bookingMethodNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'booking_method');
        $paymentMethodNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'payment_method');
        $smokingNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'smoking_allowed');
        $petsNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'pets_allowed');
        $luggageNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'luggage_size');
        $bookingTypeNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'cancellation');
        $featureResponseMap = $this->buildRideFeatureResponseMap($rideFeatureOptionGroups, 'features');

        // Add the image URL to each ride
        foreach ($rides as $ride) {
            $ride->booking_method_id = $ride->booking_method;
            $ride->feature_ids = $ride->features;

            $ride->booking_method_image = $bookingMethodImages[$ride->booking_method] ?? null;
            $ride->booking_method_tooltip = $bookingMethodTooltips[$ride->booking_method] ?? null;
            $ride->booking_method = $bookingMethodNames[$ride->booking_method] ?? null;
            $ride->booking_type = $bookingTypeNames[$ride->booking_type] ?? null;
            $ride->payment_method_image = $paymentMethodImages[$ride->payment_method] ?? null;
            $ride->payment_method_tooltip = $paymentMethodTooltips[$ride->payment_method] ?? null;
            $ride->payment_method = $paymentMethodNames[$ride->payment_method] ?? null;
            $ride->smoke_image = $smokeImages[$ride->smoke] ?? null;
            $ride->smoke_tooltip = $smokeTooltips[$ride->smoke] ?? null;
            $ride->smoke = $smokingNames[$ride->smoke] ?? null;
            $ride->animal_friendly_image = $petsImages[$ride->animal_friendly] ?? null;
            $ride->animal_friendly_tooltip = $petsTooltips[$ride->animal_friendly] ?? null;
            $ride->animal_friendly = $petsNames[$ride->animal_friendly] ?? null;
            $ride->luggage_image = $luggageImages[$ride->luggage] ?? null;
            $ride->luggage_tooltip = $luggageTooltips[$ride->luggage] ?? null;
            $ride->luggage = $luggageNames[$ride->luggage] ?? null;

            // Initialize a temporary array for the features
            $features = [];

            // Check if the features are a string, then explode it into an array
            $rideFeatures = is_string($ride->features) ? explode('=', $ride->features) : $ride->features;

            // Loop through each feature and add the corresponding image and title
            foreach ($rideFeatures as $feature) {
                if (isset($featureResponseMap[$feature])) {
                    $features[] = $featureResponseMap[$feature];
                }
            }

            // Assign the features array to the ride's features attribute
            $ride->features = $features;

            $bookedSeats = $ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->withActivePassenger()
                ->sum('seats');
            $ride->seats_left = intval($ride->seats) - intval($bookedSeats);
        }

        // Add driven rides count to each driver
        $rides->each(function ($ride) {
            $ride->driver->driven_rides = $ride->driver->rides()
                ->notCancelled()
                ->where(function ($query) {
                    $query->whereDate('rides.date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('rides.date', '=', now()->toDateString())
                                ->whereTime('rides.time', '<=', now()->toTimeString());
                        });
                })
                ->get()
                ->flatMap(function ($ride) {
                    return $ride->bookings()->pluck('seats');
                })
                ->sum();

            // Calculate age
            if ($ride->driver->dob) {
                $dob = Carbon::parse($ride->driver->dob);
                $ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $ride->driver->age = null; // Handle case where dob is not set
            }

            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
                if ($selectedLanguage) {
                    $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
                }
            }

            if ($ride->driver->gender) {
                if ($ride->driver->gender === 'male') {
                    $ride->driver->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($ride->driver->gender === 'female') {
                    $ride->driver->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($ride->driver->gender === 'prefer not to say') {
                    $ride->driver->gender_label = $genderLabel->prefer_option_label ?? null;
                }
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($ride) {
                return (int) optional($rating->ride)->added_by === (int) $ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $ride->driver->average_rating = $totalAverage;
        });

        $recentSearches = RecentSearch::where('user_id', $user->id)->orderBy('updated_at', 'desc')->limit(10)->get();



        $data = ['rides' => $rides, 'recentSearches' => $recentSearches];
        return $this->successResponse($data, 'Success');
    }

    protected function buildAppSearchRideFilters(Request $request, $user): array
    {
        $rideOptionIds = array_values(array_unique(array_merge(
            $this->extractDelimitedIntegerValues($request->input('features'), '='),
            $this->extractDelimitedIntegerValues($request->input('passenger_rating'))
        )));

        $filters = [
            'keyword' => trim((string) $request->input('keyword')),
            'driver_age' => $request->input('driver_age'),
            'driver_rating' => $request->input('driver_rating'),
            'driver_phone' => $request->boolean('driver_phone') ? 1 : null,
            'driver_name' => trim((string) $request->input('driver_name')),
            'payment_method' => $request->input('payment_method'),
            'vehicle_type' => trim((string) $request->input('vehicle_type')),
            'luggage_size' => $this->normalizeAppMultiSelectFilter($request->input('luggage'), ';'),
            'smoking_allowed' => $this->normalizeAppMultiSelectFilter($request->input('smoking'), ';'),
            'pets_allowed' => $this->normalizeAppMultiSelectFilter($request->input('pet'), ';'),
            'women_only' => $request->boolean('pink_ride') ? 1 : null,
            'extra_care' => $request->boolean('extra_care') ? 1 : null,
            'ride_option_ids' => $rideOptionIds,
            'per_page' => max(1, (int) $request->input('limit', 10)),
            'exclude_admin_deactive' => true,
            'require_vehicle' => true,
            'excluded_driver_ids' => $user ? $this->getTemporarilyBlockedSearchDriverIds((int) $user->id) : [],
        ];

        $originLabel = trim((string) $request->input('from'));
        $destinationLabel = trim((string) $request->input('to'));
        $originCityId = (int) $request->input('from_city_id', 0);
        $destinationCityId = (int) $request->input('to_city_id', 0);

        if ($originCityId > 0) {
            $filters['origin_city_id'] = $originCityId;
        }
        if ($destinationCityId > 0) {
            $filters['destination_city_id'] = $destinationCityId;
        }

        if ($originLabel !== '') {
            $filters['origin_label'] = $originLabel;
        }
        if ($destinationLabel !== '') {
            $filters['destination_label'] = $destinationLabel;
        }

        $departureDate = $this->normalizeAppSearchDate($request->input('date'));
        if ($departureDate) {
            $filters['departure_date'] = $departureDate;
        }

        return collect($filters)
            ->map(function ($value) {
                if ($value === '' || $value === '0' || $value === []) {
                    return null;
                }

                return $value;
            })
            ->all();
    }

    protected function shouldRunAppSearch(array $filters): bool
    {
        foreach (
            [
                'origin_city_id',
                'destination_city_id',
                'origin_label',
                'destination_label',
                'keyword',
                'departure_date',
                'driver_age',
                'driver_rating',
                'driver_phone',
                'driver_name',
                'payment_method',
                'vehicle_type',
                'luggage_size',
                'smoking_allowed',
                'pets_allowed',
                'women_only',
                'extra_care',
                'ride_option_ids',
            ] as $key
        ) {
            $value = $filters[$key] ?? null;
            if ($value !== null && $value !== '' && $value !== []) {
                return true;
            }
        }

        return false;
    }

    protected function prepareAppSearchRideResults(LengthAwarePaginator $rides, $user, Request $request): LengthAwarePaginator
    {
        $userId = (int) optional($user)->id;
        $fromLabel = trim((string) $request->input('from'));
        $toLabel = trim((string) $request->input('to'));
        $fromCityId = (int) $request->input('from_city_id', 0);
        $toCityId = (int) $request->input('to_city_id', 0);

        $rides->setCollection(
            $rides->getCollection()->map(function (Ride $ride) use ($userId, $fromLabel, $toLabel, $fromCityId, $toCityId) {
                $ride->fromCityId = $fromCityId > 0 ? $fromCityId : null;
                $ride->toCityId = $toCityId > 0 ? $toCityId : null;
                $bookings = collect($ride->bookings ?? [])->filter(function ($booking) use ($userId) {
                    return $userId > 0
                        && (int) ($booking->user_id ?? 0) === $userId
                        && !in_array((int) ($booking->status ?? 0), [0, 3, 4], true);
                })->values();
                $ride->setRelation('bookings', $bookings);

                $matchedPriceMinor = $this->resolveMatchedSegmentPriceMinor($ride, $fromCityId, $toCityId, $fromLabel, $toLabel);
                $detailSource = $ride->detail
                    ? collect([$ride->detail])
                    : collect($ride->rideDetail ?? []);

                $matchedDetails = $detailSource->filter(function ($detail) use ($fromLabel, $toLabel, $fromCityId, $toCityId) {
                    $matchesFrom = $fromCityId > 0
                        ? (int) ($detail->origin_city_id ?? 0) === $fromCityId
                        : ($fromLabel === '' || stripos((string) ($detail->departure ?? ''), $fromLabel) !== false);
                    $matchesTo = $toCityId > 0
                        ? (int) ($detail->destination_city_id ?? 0) === $toCityId
                        : ($toLabel === '' || stripos((string) ($detail->destination ?? ''), $toLabel) !== false);

                    return $matchesFrom && $matchesTo;
                })->map(function ($detail) use ($ride) {
                    $detail->price = $ride->price_minor ?? $detail->price ?? 0;

                    return $detail;
                })->values();

                if ($matchedDetails->isEmpty() && ($fromLabel !== '' || $toLabel !== '')) {
                    $baseDetail = $detailSource->first();
                    if ($baseDetail instanceof RideDetail) {
                        $matchedDetail = $baseDetail->replicate();
                    } elseif (is_object($baseDetail)) {
                        $matchedDetail = clone $baseDetail;
                    } else {
                        $matchedDetail = new RideDetail(is_array($baseDetail) ? $baseDetail : []);
                    }
                    if ($fromLabel !== '') {
                        $matchedDetail->departure = $fromLabel;
                    }
                    if ($toLabel !== '') {
                        $matchedDetail->destination = $toLabel;
                    }
                    if ($fromCityId > 0) {
                        $matchedDetail->origin_city_id = $fromCityId;
                    }
                    if ($toCityId > 0) {
                        $matchedDetail->destination_city_id = $toCityId;
                    }
                    $matchedDetail->price = $ride->price_minor
                        ?? ($matchedPriceMinor > 0 ? number_format($matchedPriceMinor / 100, 2, '.', '') : ($matchedDetail->price ?? 0));

                    $matchedDetails = collect([$matchedDetail]);
                }

                if ($matchedDetails->isNotEmpty()) {
                    $ride->setRelation('detail', $matchedDetails->first());
                    $ride->setRelation('rideDetail', $matchedDetails);
                }

                return $ride;
            })
        );

        return $rides;
    }

    protected function getTemporarilyBlockedSearchDriverIds(int $userId): array
    {
        return Booking::query()
            ->join('rides', 'rides.id', '=', 'bookings.ride_id')
            ->where('bookings.user_id', $userId)
            ->where('bookings.removed_permanently', 1)
            ->where('bookings.block_date_time', '>', now())
            ->whereNotNull('rides.added_by')
            ->distinct()
            ->orderBy('rides.added_by')
            ->pluck('rides.added_by')
            ->map(static fn($driverId) => (int) $driverId)
            ->all();
    }

    protected function normalizeAppSearchDate($date): ?string
    {
        $date = trim((string) $date);
        if ($date === '') {
            return null;
        }

        foreach (['F d, Y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, $date)->format('Y-m-d');
            } catch (\Throwable $exception) {
                continue;
            }
        }

        return null;
    }

    protected function normalizeAppMultiSelectFilter($value, string $delimiter)
    {
        $values = $this->extractDelimitedIntegerValues($value, $delimiter);

        if (count($values) === 0) {
            return null;
        }

        return count($values) === 1 ? $values[0] : $values;
    }

    protected function extractDelimitedIntegerValues($value, string $delimiter = ','): array
    {
        $rawValues = is_array($value) ? $value : explode($delimiter, (string) $value);

        return array_values(array_unique(array_filter(array_map(static function ($item) {
            $normalized = (int) trim((string) $item);

            return $normalized > 0 ? $normalized : null;
        }, $rawValues))));
    }

    protected function buildRideFeatureAssetMaps($optionGroups, string $groupKey): array
    {
        $options = collect($optionGroups[$groupKey] ?? []);

        return [
            'images' => $options
                ->mapWithKeys(function ($option) {
                    $icon = $option->icon ?? null;

                    return [(int) ($option->features_setting_id ?? $option->id ?? 0) => $icon ? asset('home_page_icons/' . $icon) : null];
                })
                ->all(),
            'tooltips' => $options
                ->mapWithKeys(function ($option) {
                    return [(int) ($option->features_setting_id ?? $option->id ?? 0) => $option->tooltip ?? null];
                })
                ->all(),
        ];
    }

    protected function getApiFindRidePage(?Language $language)
    {
        if (!$language) {
            return null;
        }

        $defaultLangId = $this->defaultLang?->id ?: $language->id;
        $findRidePage = FindRidePageSettingDetail::getByLanguageWithFallback($language->id, $defaultLangId);

        if ($findRidePage) {
            $findRidePage->mapMultipleOptionColumnsToDetails(
                ['ride_features', 'smoking', 'pets_allowed', 'payment_methods', 'animals', 'luggage', 'cancellation_policy'],
                $language->id,
                $defaultLangId
            );
        }

        return $findRidePage;
    }

    protected function getApiPostRidePage(?Language $language)
    {
        if (!$language) {
            return null;
        }

        $defaultLangId = $this->defaultLang?->id ?: $language->id;
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($language->id, $defaultLangId);

        if ($postRidePage) {
            $postRidePage->mapMultipleOptionColumnsToDetails(
                ['smoking', 'booking', 'payment_methods', 'animals', 'luggage', 'cancellation_policy'],
                $language->id,
                $defaultLangId
            );

            $this->hydrateLegacyFeatureOptions($postRidePage);
        }

        return $postRidePage;
    }

    protected function getApiRideDetailPage(?Language $language)
    {
        if (!$language) {
            return null;
        }

        return RideDetailPageSettingDetail::where('language_id', $language->id)->first();
    }

    protected function getApiGenderLabel(?Language $language)
    {
        if (!$language) {
            return null;
        }

        return Step1PageSettingDetail::where('language_id', $language->id)
            ->select('male_option_label', 'female_option_label', 'prefer_option_label')
            ->first();
    }

    protected function buildRideFeatureNameMap($optionGroups, string $groupKey): array
    {
        return collect($optionGroups[$groupKey] ?? [])
            ->mapWithKeys(function ($option) {
                return [(int) ($option->features_setting_id ?? $option->id ?? 0) => $option->name ?? null];
            })
            ->all();
    }

    protected function buildRideFeatureOptionMap($optionGroups, string $groupKey): array
    {
        return collect($optionGroups[$groupKey] ?? [])
            ->mapWithKeys(function ($option) {
                return [(int) ($option->features_setting_id ?? $option->id ?? 0) => $option];
            })
            ->all();
    }

    protected function buildRideFeatureResponseMap($optionGroups, string $groupKey): array
    {
        return collect($optionGroups[$groupKey] ?? [])
            ->mapWithKeys(function ($option) {
                $featureId = (int) ($option->features_setting_id ?? $option->id ?? 0);
                $icon = $option->icon ?? null;

                return [$featureId => [
                    'id' => $featureId,
                    'title' => $option->name ?? null,
                    'image' => $icon ? asset('home_page_icons/' . $icon) : null,
                    'tooltip' => $option->tooltip ?? null,
                ]];
            })
            ->all();
    }

    protected function resolveMatchedSegmentPriceMinor($ride, $fromCityId, $toCityId, string $fromLabel, string $toLabel, $fromIndex = null, $toIndex = null): int
    {
        $stopsSource = $ride->stops ?? $ride->rideStops ?? null;
        $stops = $stopsSource
            ? $stopsSource->sortBy('stop_order')->values()->all()
            : [];

        if (count($stops) < 2) {
            return (int) ($ride->price_minor ?? 0);
        }

        if ($fromIndex === null || $toIndex === null) {
            [$fromIndex, $toIndex] = $this->findMatchingSegmentIndices($stops, $fromCityId, $toCityId, $fromLabel, $toLabel);
        }

        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return (int) ($ride->price_minor ?? 0);
        }

        if (method_exists($ride, 'resolveConfiguredSegmentPriceMinor')) {
            $configuredSegmentPriceMinor = $ride->resolveConfiguredSegmentPriceMinor((int) $fromIndex, (int) $toIndex);
            if ($configuredSegmentPriceMinor !== null) {
                return $configuredSegmentPriceMinor;
            }
        }

        $fromStopId = (int) ($stops[$fromIndex]->id ?? 0);
        $toStopId = (int) ($stops[$toIndex]->id ?? 0);
        $storedSegment = collect($ride->rideStopSegments ?? [])->first(function ($segment) use ($fromStopId, $toStopId) {
            return (int) ($segment->from_stop_id ?? 0) === $fromStopId
                && (int) ($segment->to_stop_id ?? 0) === $toStopId;
        });

        if ($storedSegment) {
            return (int) ($storedSegment->price_minor ?? 0);
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

    protected function findMatchingSegmentIndices(array $stops, $fromCityId, $toCityId, string $fromLabel, string $toLabel): array
    {
        [$fromIndex, $toIndex] = $this->findMatchingStopPair($stops, $fromCityId, $toCityId, $fromLabel, $toLabel);

        if ($fromIndex !== null && $toIndex !== null) {
            return [$fromIndex, $toIndex];
        }

        $lastIndex = count($stops) - 1;
        if ($lastIndex < 1) {
            return [null, null];
        }

        if ($fromIndex === null && (!empty($fromCityId) || trim($fromLabel) !== '')) {
            foreach ($stops as $idx => $stop) {
                if ($this->stopMatches($stop, $fromCityId, $fromLabel) && $idx < $lastIndex) {
                    return [$idx, $lastIndex];
                }
            }
        }

        if ($toIndex === null && (!empty($toCityId) || trim($toLabel) !== '')) {
            foreach ($stops as $idx => $stop) {
                if ($this->stopMatches($stop, $toCityId, $toLabel) && $idx > 0) {
                    return [0, $idx];
                }
            }
        }

        return [null, null];
    }

    protected function findMatchingStopPair(array $stops, $fromCityId, $toCityId, string $fromLabel, string $toLabel): array
    {
        $fromIndex = null;
        $toIndex = null;

        foreach ($stops as $idx => $stop) {
            if ($fromIndex === null && $this->stopMatches($stop, $fromCityId, $fromLabel)) {
                $fromIndex = $idx;
            }

            if ($fromIndex !== null && $idx > $fromIndex && $this->stopMatches($stop, $toCityId, $toLabel)) {
                $toIndex = $idx;
                break;
            }
        }

        return [$fromIndex, $toIndex];
    }

    protected function stopMatches($stop, $cityId, string $label): bool
    {
        if (!empty($cityId) && (int) ($stop->city_id ?? 0) === (int) $cityId) {
            return true;
        }

        $label = trim($label);
        if ($label === '') {
            return false;
        }

        return stripos((string) ($stop->label ?? ''), $label) !== false;
    }

    protected function applyAppRequestedRideSegmentContext(Ride $ride, Request $request): Ride
    {
        $fromLabel = trim((string) $request->input('from'));
        $toLabel = trim((string) $request->input('to'));
        $fromCityId = (int) $request->input('from_city_id', 0);
        $toCityId = (int) $request->input('to_city_id', 0);

        $ride->fromCityId = $fromCityId > 0 ? $fromCityId : null;
        $ride->toCityId = $toCityId > 0 ? $toCityId : null;

        if ($fromLabel === '' && $toLabel === '' && $fromCityId === 0 && $toCityId === 0) {
            return $ride;
        }

        $detailSource = $ride->detail
            ? collect([$ride->detail])
            : collect($ride->rideDetail ?? []);

        $matchedDetails = $detailSource->filter(function ($detail) use ($fromLabel, $toLabel, $fromCityId, $toCityId) {
            $matchesFrom = $fromCityId > 0
                ? (int) ($detail->origin_city_id ?? 0) === $fromCityId
                : ($fromLabel === '' || stripos((string) ($detail->departure ?? ''), $fromLabel) !== false);
            $matchesTo = $toCityId > 0
                ? (int) ($detail->destination_city_id ?? 0) === $toCityId
                : ($toLabel === '' || stripos((string) ($detail->destination ?? ''), $toLabel) !== false);

            return $matchesFrom && $matchesTo;
        })->values();

        if ($matchedDetails->isEmpty()) {
            $matchedPriceMinor = $this->resolveMatchedSegmentPriceMinor($ride, $fromCityId, $toCityId, $fromLabel, $toLabel);
            $baseDetail = $detailSource->first();

            if ($baseDetail instanceof RideDetail) {
                $matchedDetail = $baseDetail->replicate();
            } elseif (is_object($baseDetail)) {
                $matchedDetail = clone $baseDetail;
            } else {
                $matchedDetail = new RideDetail(is_array($baseDetail) ? $baseDetail : []);
            }

            if ($fromLabel !== '') {
                $matchedDetail->departure = $fromLabel;
            }
            if ($toLabel !== '') {
                $matchedDetail->destination = $toLabel;
            }
            if ($fromCityId > 0) {
                $matchedDetail->origin_city_id = $fromCityId;
            }
            if ($toCityId > 0) {
                $matchedDetail->destination_city_id = $toCityId;
            }
            if ($matchedPriceMinor > 0) {
                $matchedDetail->price = number_format($matchedPriceMinor / 100, 2, '.', '');
                $ride->price_minor = $matchedPriceMinor;
            }

            $matchedDetails = collect([$matchedDetail]);
        }

        if ($matchedDetails->isNotEmpty()) {
            $ride->setRelation('detail', $matchedDetails->first());
            $ride->setRelation('rideDetail', $matchedDetails);
        }

        return $ride;
    }

    public function _RideDetail(Request $request)
    {
        $rideDetailId = isset($request->ride_detail_id) ? $request->ride_detail_id : 0;
        $ride = Ride::where('id', $request->id);

        if ($rideDetailId == 0) {
            $ride = $ride->with(['rideDetail' => function ($q) {
                $q->where('default_ride', '1');
            }]);
        } else {
            $ride = $ride->with(['rideDetail' => function ($q) use ($rideDetailId) {
                $q->where('id', $rideDetailId);
            }]);
        }

        // $ride = $ride->with(['MoreRideDetail']);

        $ride = $ride->with(['driver' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns you want to select
            $query->withTrashed(); // Include soft-deleted users
        }])
            ->with('vehicle')
            ->with(['bookings' => function ($query) {
                // Select specific columns from bookings
                $query->select('id', 'ride_id', 'seats', 'user_id', 'booking_credit', 'status', 'secured_cash', 'secured_cash_code', 'fare', 'secured_cash_attempt_count', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
                    ->where('status', '<>', 0)
                    ->where('status', '<>', 3)
                    ->where('status', '<>', 4)
                    ->withActivePassenger()
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
                    }]);
            }])->first();

        if ($ride) {
            $ride = $this->applyAppRequestedRideSegmentContext($ride, $request);
        }

        $selectedLanguage = $this->resolveApiLanguage($request->lang_id);
        $findRidePage = $this->getApiFindRidePage($selectedLanguage);
        $postRidePage = $this->getApiPostRidePage($selectedLanguage);
        $rideDetailPage = $this->getApiRideDetailPage($selectedLanguage);
        $genderLabel = $this->getApiGenderLabel($selectedLanguage);

        $defaultLanguage = Language::where('is_default', 1)->first();
        $defaultPostRidePage = PostRidePageSettingDetail::where('language_id', $defaultLanguage->id)->first();

        $rideFeatureOptionGroups = $this->getRideFeatureOptionGroups($selectedLanguage?->id, $defaultLanguage?->id);
        $bookingMethodAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'booking_method');
        $paymentMethodAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'payment_method');
        $smokingAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'smoking_allowed');
        $petsAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'pets_allowed');
        $luggageAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'luggage_size');
        $featureResponseMap = $this->buildRideFeatureResponseMap($rideFeatureOptionGroups, 'features');

        $bookingMethodImages = $bookingMethodAssets['images'];
        $bookingMethodTooltips = $bookingMethodAssets['tooltips'];
        $paymentMethodImages = $paymentMethodAssets['images'];
        $paymentMethodTooltips = $paymentMethodAssets['tooltips'];
        $smokeImages = $smokingAssets['images'];
        $smokeTooltips = $smokingAssets['tooltips'];
        $petsImages = $petsAssets['images'];
        $petsTooltips = $petsAssets['tooltips'];
        $luggageImages = $luggageAssets['images'];
        $luggageTooltips = $luggageAssets['tooltips'];

        $bookingMethodNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'booking_method');
        $paymentMethodNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'payment_method');
        $smokingNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'smoking_allowed');
        $petsNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'pets_allowed');
        $luggageNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'luggage_size');
        $bookingTypeNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'cancellation');

        $bookingMethodOptions = $this->buildRideFeatureOptionMap($rideFeatureOptionGroups, 'booking_method');
        $paymentMethodOptions = $this->buildRideFeatureOptionMap($rideFeatureOptionGroups, 'payment_method');
        $bookingTypeOptions = $this->buildRideFeatureOptionMap($rideFeatureOptionGroups, 'cancellation');

        if ($ride) {
            $primaryDetail = $ride->detail ?: collect($ride->rideDetail ?? [])->first();
            $displayPrice = $ride->price_minor
                ?? (int) round(((float) ($primaryDetail->price ?? 0)) / 100);

            $ride->detail->price = $displayPrice;

            // Calculate seats left
            $bookedSeats = $ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->withActivePassenger()
                ->sum('seats');
            $ride->seats_left = intval($ride->seats) - intval($bookedSeats);

            $ride->booking_method_id = $ride->booking_method;
            // Add the image URL to ride
            $ride->booking_method_image = $bookingMethodImages[$ride->booking_method] ?? null;
            $ride->booking_method_tooltip = $bookingMethodTooltips[$ride->booking_method] ?? null;
            $ride->payment_method_image = $paymentMethodImages[$ride->payment_method] ?? null;
            $ride->payment_method_tooltip = $paymentMethodTooltips[$ride->payment_method] ?? null;
            $ride->smoke_image = $smokeImages[$ride->smoke] ?? null;
            $ride->smoke_tooltip = $smokeTooltips[$ride->smoke] ?? null;
            $ride->animal_friendly_image = $petsImages[$ride->animal_friendly] ?? null;
            $ride->animal_friendly_tooltip = $petsTooltips[$ride->animal_friendly] ?? null;
            $ride->luggage_image = $luggageImages[$ride->luggage] ?? null;
            $ride->luggage_tooltip = $luggageTooltips[$ride->luggage] ?? null;
            $paymentMethodOption = $paymentMethodOptions[(int) $ride->payment_method] ?? null;
            $bookingTypeOption = $bookingTypeOptions[(int) $ride->booking_type] ?? null;
            $smoke = $smokingNames[$ride->smoke] ?? null;
            $animalFriendly = $petsNames[$ride->animal_friendly] ?? null;
            $bookingMethod = $bookingMethodNames[$ride->booking_method] ?? null;
            $luggage = $luggageNames[$ride->luggage] ?? null;

            if ($ride->payment_method) {
                $ride->payment_method_slug = $paymentMethodOption->slug ?? null;
                $ride->payment_method = $paymentMethodNames[$ride->payment_method] ?? null;
            }

            if ($ride->luggage) {
                $ride->luggage = (optional($rideDetailPage)->luggage_label ?? '') . $luggage;
            }

            if ($ride->booking_type) {
                $ride->booking_type_slug = $bookingTypeOption->slug ?? null;
                $ride->booking_type_tooltip = $bookingTypeOption->tooltip ?? null;
                $ride->booking_type = $bookingTypeNames[$ride->booking_type] ?? null;
            }

            if ($ride->booking_method) {
                $ride->booking_method = $bookingMethod;
            }

            if ($ride->smoke) {
                $ride->smoke = (optional($rideDetailPage)->smoking_label ?? '') . $smoke;
            }

            if ($ride->animal_friendly) {
                $ride->animal_friendly = (optional($rideDetailPage)->pets_label ?? '') . $animalFriendly;
            }

            $ride->booked_seats = $bookedSeats;
            $ride->booking_fee = round($ride->bookings->sum('booking_credit'), 1);
            $ride->fare = round($ride->bookings->sum('fare'), 1);
            $ride->total_amount = $ride->booking_fee + $ride->fare;

            $features = [];
            $rideFeatures = is_string($ride->features) ? explode('=', $ride->features) : $ride->features;
            foreach ($rideFeatures as $feature) {
                if (isset($featureResponseMap[$feature])) {
                    $features[] = $featureResponseMap[$feature];
                }
            }
            $ride->features = $features;

            $ride->driver->driven_rides = $ride->driver->rides()
                ->notCancelled()
                ->where(function ($query) {
                    $query->whereDate('rides.date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('rides.date', '=', now()->toDateString())
                                ->whereTime('rides.time', '<=', now()->toTimeString());
                        });
                })
                ->get()
                ->flatMap(function ($ride) {
                    return $ride->bookings()->pluck('seats');
                })
                ->sum();

            // Calculate age
            if ($ride->driver->dob) {
                $dob = Carbon::parse($ride->driver->dob);
                $ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $ride->driver->age = null; // Handle case where dob is not set
            }

            if ($ride->driver->gender) {
                if ($ride->driver->gender === 'male') {
                    $ride->driver->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($ride->driver->gender === 'female') {
                    $ride->driver->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($ride->driver->gender === 'prefer not to say') {
                    $ride->driver->gender_label = $genderLabel->prefer_option_label ?? null;
                }
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($ride) {
                return (int) optional($rating->ride)->added_by === (int) $ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $ride->driver->average_rating = $totalAverage;

            foreach ($ride->bookings as $booking) {
                // Calculate age
                if ($booking->passenger->dob) {
                    $dob = Carbon::parse($booking->passenger->dob);
                    $booking->passenger->age = $dob->diffInYears(Carbon::now());
                } else {
                    $booking->passenger->age = null; // Handle case where dob is not set
                }

                if ($booking->passenger->gender) {
                    if ($booking->passenger->gender === 'male') {
                        $booking->passenger->gender_label = $genderLabel->male_option_label ?? null;
                    } elseif ($booking->passenger->gender === 'female') {
                        $booking->passenger->gender_label = $genderLabel->female_option_label ?? null;
                    } elseif ($booking->passenger->gender === 'prefer not to say') {
                        $booking->passenger->gender_label = $genderLabel->prefer_option_label ?? null;
                    }
                }

                $ratings = Rating::where('status', 1)->where('type', '2')->get();
                // Calculate average rating
                $filteredRatings = $ratings->filter(function ($rating) use ($booking) {
                    return (int) optional($rating->booking)->user_id === (int) $booking->user_id;
                });

                $totalAverage = $filteredRatings->avg('average_rating');
                $booking->passenger->average_rating = $totalAverage;
            }

            // Separate bookings based on status
            $ride->booking_requests = $ride->bookings()->where('status', 0)
                ->with(['passenger' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
                }])->get();

            foreach ($ride->booking_requests as $booking_request) {
                // Calculate age
                if ($booking_request->passenger->dob) {
                    $dob = Carbon::parse($booking_request->passenger->dob);
                    $booking_request->passenger->age = $dob->diffInYears(Carbon::now());
                } else {
                    $booking_request->passenger->age = null; // Handle case where dob is not set
                }

                if ($booking_request->passenger->gender) {
                    if ($booking_request->passenger->gender === 'male') {
                        $booking_request->passenger->gender_label = $genderLabel->male_option_label ?? null;
                    } elseif ($booking_request->passenger->gender === 'female') {
                        $booking_request->passenger->gender_label = $genderLabel->female_option_label ?? null;
                    } elseif ($booking_request->passenger->gender === 'prefer not to say') {
                        $booking_request->passenger->gender_label = $genderLabel->prefer_option_label ?? null;
                    }
                }

                $ratings = Rating::where('status', 1)->where('type', '2')->get();
                // Calculate average rating
                $filteredRatings = $ratings->filter(function ($rating) use ($booking_request) {
                    return (int) optional($rating->booking)->user_id === (int) $booking_request->user_id;
                });

                $totalAverage = $filteredRatings->avg('average_rating');
                $booking_request->passenger->average_rating = $totalAverage;
            }
        }

        $cancelRideSetting = CancelRideSetting::first();
        $reviewSetting = ReviewSetting::select('id', 'leave_review_days')->first();
        $siteSetting = SiteSetting::getCached();

        $rideDetailPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $tripsPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            // Retrieve the tripsPageSettingDetail associated with the selected language
            $tripsPage = TripsPageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['ride' => $ride, 'cancelRideSetting' => $cancelRideSetting, 'reviewSetting' => $reviewSetting, 'siteSetting' => $siteSetting, 'rideDetailPage' => $rideDetailPage, 'tripsPage' => $tripsPage];
        return $this->successResponse($data, 'Success');
    }

    public function checkBooking(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $selectedLanguage = $this->resolveApiLanguage();
        $message = $this->getApiSuccessMessageFields(['block_booking_message'], $selectedLanguage);

        if ($user->block_booking == '1') {
            return $this->apiErrorResponse(strip_tags($message->block_booking_message ?? null), 200);
        }

        $hasBooking = Booking::where('ride_id', $request->id)
            ->where('user_id', $user_id)
            ->where('status', ['1', '2'])
            ->exists();

        // Initialize the seats variable
        $seats = 0;

        // If the user has a booking, sum up the seats column
        if ($hasBooking) {
            $seats = Booking::where('ride_id', $request->id)
                ->where('user_id', $user_id)
                ->whereIn('status', ['1', '2'])
                ->sum('seats');
        }

        $data = ['hasBooking' => $hasBooking, 'seats' => $seats];
        return $this->successResponse($data, 'Success');
    }

    public function coPassengers(Request $request)
    {
        $myPassengerPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $myPassengerPage = MyPassengerSettingDetail::where('language_id', $request->lang_id)->first();
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $myPassengerPage = MyPassengerSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        $ride = Ride::where('id', $request->ride_id)
            ->with(['bookings' => function ($query) {
                // Select specific columns from bookings
                $query->select('id', 'ride_id', 'seats', 'user_id', 'fare', 'secured_cash_attempt_count', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
                    ->where('status', '<>', 3)
                    ->where('status', '<>', 4)
                    ->withActivePassenger()
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
                    }]);
            }])->first();

        foreach ($ride->bookings as $booking) {
            // Calculate age
            if ($booking->passenger->dob) {
                $dob = Carbon::parse($booking->passenger->dob);
                $booking->passenger->age = $dob->diffInYears(Carbon::now());
            } else {
                $booking->passenger->age = null; // Handle case where dob is not set
            }

            if ($booking->passenger->gender) {
                if ($booking->passenger->gender === 'male') {
                    $booking->passenger->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($booking->passenger->gender === 'female') {
                    $booking->passenger->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($booking->passenger->gender === 'prefer not to say') {
                    $booking->passenger->gender_label = $genderLabel->prefer_option_label ?? null;
                }
            }
        }

        $data = ['bookings' => $ride->bookings, 'myPassengerPage' => $myPassengerPage];
        return $this->successResponse($data, 'Success');
    }

    public function noShow(Request $request)
    {
        $request->validate([
            'ride_id' => 'required',
            'booking_id' => 'required',
            'user_id' => 'required',
            'type' => 'required',
        ]);

        $exist = NoShowHistory::where('ride_id', $request->ride_id)->where('booking_id', $request->booking_id)
            ->where('user_id', $request->user_id)->where('type', $request->type)->first();

        if ($exist) {
            $data = [];
            return $this->successResponse($data, 'Your response has already been submitted');
        }

        $response = NoShowHistory::create([
            'ride_id' => $request->ride_id,
            'booking_id' => $request->booking_id,
            'user_id' => $request->user_id,
            'type' => $request->type,
        ]);

        $data = ['response' => $response];
        return $this->successResponse($data, 'Response submitted successfully');
    }

    public function _PostRide()
    {
        \Log::info('dddddddd');
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $rides = Ride::where('added_by', $user_id)->get();

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
                $overallRating = $ratings->avg('average_rating');
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

        $data = ['vehicles' => $vehicles, 'overallRating' => $overallRating];
        return $this->successResponse($data, 'Post ride page get successfully');
    }

    public function _PostRideStore(Request $request, $ride_id = 0)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $this->errorResponse('Unauthorized');
        }

        auth()->setUser($user);

        $ride_id = (int) ($ride_id ?: $request->input('ride_id', 0));
        $existingRide = $ride_id ? Ride::where('id', $ride_id)->where('added_by', $user->id)->first() : null;

        if ($ride_id && !$existingRide) {
            return $this->errorResponse('Ride not found');
        }

        $this->normalizePostRideRequest($request, $existingRide);
        $this->normalizeAppPostRideRequest($request, $existingRide);

        $validator = $this->buildPostRideStoreValidator($request);
        $this->appendStopDepartureAtValidation($request, $validator);
        if ($validator->fails()) {
            $errors = $this->mapAppPostRideValidationErrors($validator->errors()->toArray());
            $firstError = collect($errors)->flatten()->first() ?? $validator->errors()->first();
            return $this->errorResponse($firstError, ['errors' => $errors]);
        }

        $message = $this->successMessage;

        if ($user->isBlockedPostRide()) {
            return $this->errorResponse(strip_tags($message->block_post_ride_message ?? 'Posting rides is blocked for your account.'));
        }

        if (!$user->hasCustomProfileImage()) {
            return $this->errorResponse(strip_tags($message->profile_photo_required_message ?? 'For posting a ride profile photo is required'));
        }

        if ($user->isSuspended()) {
            return $this->errorResponse(strip_tags($message->admin_block_account_message ?? 'Your account has been suspended by the admin'));
        }

        $selectedFeatureIds = is_array($request->input('features'))
            ? array_map('strval', $request->input('features'))
            : [];

        if (in_array('1', $selectedFeatureIds, true) && ($pinkRideError = $user->pinkRideEligibilityError())) {
            return $this->errorResponse($pinkRideError);
        }

        if (in_array('2', $selectedFeatureIds, true) && ($extraCareError = $user->extraRideEligibilityError())) {
            return $this->errorResponse($extraCareError);
        }

        $origin = $request->input('origin.label');
        $originCityId = $request->input('origin.city_id');
        $destination = $request->input('destination.label');
        $destinationCityId = $request->input('destination.city_id');
        $adminSetting = SiteSetting::getCached();
        $seatCount = (int) $request->input('seats_total', $request->input('seats', 0));

        if ($ride_id) {
            $lockedSeatCount = SeatDetail::where('ride_id', $ride_id)
                ->whereIn('status', ['booked', 'hold'])
                ->count();

            if ($seatCount < $lockedSeatCount) {
                return $this->errorResponse('You cannot reduce seats below the number already reserved for this ride.');
            }

            $hasBookings = Booking::where('ride_id', $ride_id)
                ->bookedOrCompleted()
                ->withActivePassenger()
                ->exists();

            $currentPrice = $existingRide?->detail?->price;
            $newPrice = (int) $request->input('price_minor');
            if ($hasBookings && $currentPrice !== null && (int) $currentPrice !== $newPrice) {
                return $this->errorResponse('You cannot change the price once passengers have booked this ride.');
            }
        }

        $formattedDate = Carbon::parse($request->input('date'))->format('Y-m-d');
        $formattedTime = strlen((string) $request->input('time')) <= 5
            ? Carbon::createFromFormat('H:i', $request->input('time'))->format('H:i')
            : Carbon::parse($request->input('time'))->format('H:i');

        $rideDateTime = Carbon::parse($formattedDate . ' ' . $formattedTime);
        if ($rideDateTime->lte(Carbon::now()->addMinutes($adminSetting->ride_post_dead_time ?? 0))) {
            return $this->errorResponse(strip_tags($message->ride_dead_time_text ?? 'The ride time you selected is too close. Please select a time that is more than 15 minutes in the future'));
        }

        $rides = Ride::where('added_by', $user->id)
            ->when($ride_id !== 0, fn($q) => $q->where('id', '!=', $ride_id))
            ->get();

        foreach ($rides as $existingUserRide) {
            if ($existingUserRide->date == $formattedDate && $existingUserRide->time == $formattedTime) {
                return $this->errorResponse(strip_tags($message->ride_schedule_message ?? 'Ride already scheduled'));
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
            ->when($ride_id !== 0, fn($q) => $q->where('id', '!=', $ride_id))
            ->where('added_by', $user->id)
            ->whereRaw("CONCAT(date, ' ', time) < ?", [$endDateTime])
            ->whereRaw("CONCAT(destination_reached_date, ' ', destination_reached_time) > ?", [$statDateTime])
            ->first();

        if ($overlappedRide) {
            return $this->errorResponse(strip_tags($message->overlap_ride_message ?? 'This ride overlaps with an existing ride you already have'));
        }

        if ($request->hasFile('vehicle_image')) {
            $file = $request->file('vehicle_image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('car_images'), $filename);
        } elseif ($request->has('existing_image')) {
            $filename = $request->input('existing_image');
        } else {
            $filename = '';
        }

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
        $this->processPostRideVehicleMode($request, $vehiclePayload);
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

        $initialRide = $ride_id
            ? Ride::with(['detail', 'rideStops', 'rideStopSegments'])->find($ride_id)
            : Ride::create($data);

        if ($ride_id) {
            $initialRide->update($data);
            $initialRide->refresh();
        }

        $rideDetail = $initialRide->detail ?? new RideDetail();
        $this->syncRideSeatDetails($initialRide, $seatCount);

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

        $this->syncRideStopsAndSegments(
            $initialRide,
            $request,
            $origin,
            $originCityId,
            $destination,
            $destinationCityId,
            $destinationReachedDate,
            $destinationReachedTime
        );

        if ($recurring !== 0) {
            $frequency = $request->input('recurring_type');
            $numRecurringTrips = (int) $request->input('recurring_trips');
            $offsetDays = $frequency === 'Daily' ? 1 : 7;

            if (($offsetDays === 1 && $duration > 24 * 3600) || ($offsetDays === 7 && $duration > 7 * 24 * 3600)) {
                return $this->errorResponse('This ride\'s recurring overlaps with current ride. Total duration is greater than a ' . $frequency);
            }

            if (!$ride_id) {
                $recurringEndDateTime = (clone $endDateTime)->addDays($offsetDays * $numRecurringTrips);
                $overlappedRecurringRide = Ride::notCancelled()
                    ->where('added_by', $user->id)
                    ->whereRaw("CONCAT(date, ' ', time) < ?", [$recurringEndDateTime])
                    ->whereRaw("CONCAT(destination_reached_date, ' ', destination_reached_time) > ?", [$statDateTime])
                    ->first();

                if ($overlappedRecurringRide) {
                    return $this->errorResponse(strip_tags($message->overlap_ride_message ?? 'This ride\'s recurring overlaps with an existing ride you already have'));
                }

                $templateRide = $initialRide->fresh(['detail', 'rideStops', 'rideStopSegments']);
                $sourceRideDetail = $templateRide->detail;
                $sourceRideStops = $templateRide->rideStops->sortBy('stop_order')->values();
                $sourceRideSegments = $templateRide->rideStopSegments;

                DB::transaction(function () use ($numRecurringTrips, $templateRide, $sourceRideDetail, $sourceRideStops, $sourceRideSegments, $offsetDays, $user) {
                    for ($i = 1; $i <= $numRecurringTrips; $i++) {
                        $recurringRide = new Ride([
                            'added_by' => $user->id,
                            'recurring_id' => $templateRide->id,
                        ]);

                        $this->syncRecurringRideFromTemplate(
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
                    return $this->errorResponse(strip_tags($message->overlap_ride_message ?? 'This ride\'s recurring overlaps with an existing ride you already have'));
                }

                $templateRide = $initialRide->fresh(['detail', 'rideStops', 'rideStopSegments']);
                $sourceRideDetail = $templateRide->detail;
                $sourceRideStops = $templateRide->rideStops->sortBy('stop_order')->values();
                $sourceRideSegments = $templateRide->rideStopSegments;

                DB::transaction(function () use ($existingRecurringRides, $numRecurringTrips, $templateRide, $sourceRideDetail, $sourceRideStops, $sourceRideSegments, $offsetDays, $user) {
                    for ($i = 1; $i <= $numRecurringTrips; $i++) {
                        $recurringRide = $existingRecurringRides[$i - 1] ?? new Ride([
                            'added_by' => $user->id,
                            'recurring_id' => $templateRide->id,
                        ]);

                        $this->syncRecurringRideFromTemplate(
                            $recurringRide,
                            $templateRide,
                            $sourceRideDetail,
                            $sourceRideStops,
                            $sourceRideSegments,
                            $offsetDays * $i
                        );
                    }

                    for ($i = $numRecurringTrips; $i < $existingRecurringRides->count(); $i++) {
                        $this->deleteRideCascade($existingRecurringRides[$i]);
                    }
                });
            }
        }

        if ($ride_id && $recurring === 0) {
            Ride::where('recurring_id', $initialRide->id)
                ->orderBy('date')
                ->orderBy('time')
                ->get()
                ->each(function (Ride $recurringRide) {
                    $this->deleteRideCascade($recurringRide);
                });
        }

        $initialRide = Ride::with(['detail', 'rideStops', 'rideStopSegments'])->find($initialRide->id);

        $data = ['ride' => $initialRide];
        return $this->successResponse(
            $data,
            strip_tags($ride_id ? ($message->post_ride_update_message ?? 'Ride updated successfully') : ($message->ride_post_message ?? 'Ride posted successfully'))
        );
    }

    public function _EditRide(Request $request)
    {
        $ride = Ride::where('id', $request->ride_id)->with(['vehicle', 'defaultRideDetail', 'MoreRideDetail'])
            ->with(['driver' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])
            ->with(['bookings' => function ($query) {
                // Select specific columns from bookings
                $query->select('id', 'ride_id', 'seats', 'user_id', 'booking_credit', 'fare', 'secured_cash_attempt_count', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
                    ->where('status', '<>', 3)
                    ->where('status', '<>', 4)
                    ->withActivePassenger()
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
                    }]);
            }])->first();

        $selectedLanguageAbbreviation = session('selectedLanguage');
        $selectedLanguage = $selectedLanguageAbbreviation
            ? Language::where('abbreviation', $selectedLanguageAbbreviation)->first()
            : null;
        $selectedLanguage = $selectedLanguage ?: Language::where('is_default', 1)->first();

        $findRidePage = $this->getApiFindRidePage($selectedLanguage);
        $postRidePage = $this->getApiPostRidePage($selectedLanguage);

        $defaultLanguage = $this->defaultLang ?: Language::where('is_default', 1)->first();
        $rideFeatureOptionGroups = $this->getRideFeatureOptionGroups($selectedLanguage?->id, $defaultLanguage?->id);
        $bookingMethodAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'booking_method');
        $paymentMethodAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'payment_method');
        $smokingAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'smoking_allowed');
        $petsAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'pets_allowed');
        $luggageAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'luggage_size');
        $featureResponseMap = $this->buildRideFeatureResponseMap($rideFeatureOptionGroups, 'features');

        $bookingMethodImages = $bookingMethodAssets['images'];
        $bookingMethodTooltips = $bookingMethodAssets['tooltips'];
        $paymentMethodImages = $paymentMethodAssets['images'];
        $paymentMethodTooltips = $paymentMethodAssets['tooltips'];
        $smokeImages = $smokingAssets['images'];
        $smokeTooltips = $smokingAssets['tooltips'];
        $petsImages = $petsAssets['images'];
        $petsTooltips = $petsAssets['tooltips'];
        $luggageImages = $luggageAssets['images'];
        $luggageTooltips = $luggageAssets['tooltips'];

        if ($ride) {
            // Calculate seats left
            $bookedSeats = $ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->withActivePassenger()
                ->sum('seats');
            $ride->seats_left = intval($ride->seats) - intval($bookedSeats);

            // Add the image URL to ride
            $ride->booking_method_image = $bookingMethodImages[$ride->booking_method] ?? null;
            $ride->booking_method_tooltip = $bookingMethodTooltips[$ride->booking_method] ?? null;
            $ride->payment_method_image = $paymentMethodImages[$ride->payment_method] ?? null;
            $ride->payment_method_tooltip = $paymentMethodTooltips[$ride->payment_method] ?? null;
            $ride->smoke_image = $smokeImages[$ride->smoke] ?? null;
            $ride->smoke_tooltip = $smokeTooltips[$ride->smoke] ?? null;
            $ride->animal_friendly_image = $petsImages[$ride->animal_friendly] ?? null;
            $ride->animal_friendly_tooltip = $petsTooltips[$ride->animal_friendly] ?? null;
            $ride->luggage_image = $luggageImages[$ride->luggage] ?? null;
            $ride->luggage_tooltip = $luggageTooltips[$ride->luggage] ?? null;

            $ride->booked_seats = $bookedSeats;
            $ride->booking_fee = round($ride->bookings->sum('booking_credit'), 1);
            $ride->fare = round($ride->bookings->sum('fare'), 1);
            $ride->total_amount = $ride->booking_fee + $ride->fare;

            $featureImages = $featureResponseMap;

            // Initialize a temporary array for the features
            $features = [];

            // Check if the features are a string, then explode it into an array
            $rideFeatures = is_string($ride->features) ? explode('=', $ride->features) : $ride->features;

            // Loop through each feature and add the corresponding image and title
            foreach ($rideFeatures as $feature) {
                if (isset($featureImages[$feature])) {
                    $features[] = $featureImages[$feature];
                }
            }

            // Assign the features array to the ride's features attribute
            $ride->features = $features;

            $ride->driver->driven_rides = $ride->driver->rides()
                ->notCancelled()
                ->where(function ($query) {
                    $query->whereDate('rides.date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('rides.date', '=', now()->toDateString())
                                ->whereTime('rides.time', '<=', now()->toTimeString());
                        });
                })
                ->get()
                ->flatMap(function ($ride) {
                    return $ride->bookings()->pluck('seats');
                })
                ->sum();

            // Calculate age
            if ($ride->driver->dob) {
                $dob = Carbon::parse($ride->driver->dob);
                $ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $ride->driver->age = null; // Handle case where dob is not set
            }

            if ($ride->driver->gender) {
                $ride->driver->gender = ucfirst($ride->driver->gender);
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($ride) {
                return (int) optional($rating->ride)->added_by === (int) $ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $ride->driver->average_rating = $totalAverage;

            foreach ($ride->bookings as $booking) {
                // Calculate age
                if ($booking->passenger->dob) {
                    $dob = Carbon::parse($booking->passenger->dob);
                    $booking->passenger->age = $dob->diffInYears(Carbon::now());
                } else {
                    $booking->passenger->age = null; // Handle case where dob is not set
                }

                if ($booking->passenger->gender) {
                    $booking->passenger->gender = ucfirst($booking->passenger->gender);
                }

                $ratings = Rating::where('status', 1)->where('type', '2')->get();
                // Calculate average rating
                $filteredRatings = $ratings->filter(function ($rating) use ($booking) {
                    return (int) optional($rating->booking)->user_id === (int) $booking->user_id;
                });

                $totalAverage = $filteredRatings->avg('average_rating');
                $booking->passenger->average_rating = $totalAverage;
            }
        }

        $data = ['ride' => $ride];
        return $this->successResponse($data, 'Edit ride page get successfully');
    }

    public function _UpdateRide(Request $request, $ride_id)
    {
        return $this->_PostRideStore($request, $ride_id);
    }

    public function postRideIndex(Request $request)
    {
        $postRidePage = null;
        $messages = null;
        if ($request->lang_id && $request->lang_id != 0) {

            $selectedLanguage = Language::where('id', $request->lang_id)->first();
            // Retrieve the PostRidePageSettingDetail associated with the selected language
            $postRidePage = PostRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            $postRideError = PostRidePageError::where('post_ride_page_setting_detail_id', $postRidePage->id)->first();
            $postRidePage->from_error = $postRideError->from_error ?? null;
            $postRidePage->to_error = $postRideError->to_error ?? null;
            $postRidePage->pick_up_error = $postRideError->pick_up_error ?? null;
            $postRidePage->drop_off_error = $postRideError->drop_off_error ?? null;
            $postRidePage->date_error = $postRideError->date_error ?? null;
            $postRidePage->time_error = $postRideError->time_error ?? null;
            $postRidePage->recurring_type_error = $postRideError->recurring_type_error ?? null;
            $postRidePage->recurring_trips_error = $postRideError->recurring_trips_error ?? null;
            $postRidePage->meeting_drop_off_description_error = $postRideError->meeting_drop_off_description_error ?? null;
            $postRidePage->seats_error = $postRideError->seats_error ?? null;
            $postRidePage->seats_middle_error = $postRideError->seats_middle_error ?? null;
            $postRidePage->seats_back_error = $postRideError->seats_back_error ?? null;
            $postRidePage->vehicle_id_error = $postRideError->vehicle_id_error ?? null;
            $postRidePage->make_error = $postRideError->make_error ?? null;
            $postRidePage->model_error = $postRideError->model_error ?? null;
            $postRidePage->vehicle_type_error = $postRideError->vehicle_type_error ?? null;
            $postRidePage->color_error = $postRideError->color_error ?? null;
            $postRidePage->license_error = $postRideError->license_error ?? null;
            $postRidePage->year_error = $postRideError->year_error ?? null;
            $postRidePage->fuel_error = $postRideError->fuel_error ?? null;
            $postRidePage->photo_error = $postRideError->photo_error ?? null;
            $postRidePage->booking_method_error = $postRideError->booking_method_error ?? null;
            $postRidePage->anything_to_add_error = $postRideError->anything_to_add_error ?? null;
            $postRidePage->smoking_error = $postRideError->smoking_error ?? null;
            $postRidePage->animal_error = $postRideError->animal_error ?? null;
            $postRidePage->luggage_error = $postRideError->luggage_error ?? null;
            $postRidePage->price_error = $postRideError->price_error ?? null;
            $postRidePage->payment_method_error = $postRideError->payment_method_error ?? null;
            $postRidePage->booking_type_error = $postRideError->booking_type_error ?? null;
            $postRidePage->agree_terms_error = $postRideError->agree_terms_error ?? null;
            $messages = $this->getApiSuccessMessageFields(['past_time_message', 'past_date_message'], $selectedLanguage);
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideError = PostRidePageError::where('post_ride_page_setting_detail_id', $postRidePage->id)->first();
                $postRidePage->from_error = $postRideError->from_error ?? null;
                $postRidePage->to_error = $postRideError->to_error ?? null;
                $postRidePage->pick_up_error = $postRideError->pick_up_error ?? null;
                $postRidePage->drop_off_error = $postRideError->drop_off_error ?? null;
                $postRidePage->date_error = $postRideError->date_error ?? null;
                $postRidePage->time_error = $postRideError->time_error ?? null;
                $postRidePage->recurring_type_error = $postRideError->recurring_type_error ?? null;
                $postRidePage->recurring_trips_error = $postRideError->recurring_trips_error ?? null;
                $postRidePage->meeting_drop_off_description_error = $postRideError->meeting_drop_off_description_error ?? null;
                $postRidePage->seats_error = $postRideError->seats_error ?? null;
                $postRidePage->seats_middle_error = $postRideError->seats_middle_error ?? null;
                $postRidePage->seats_back_error = $postRideError->seats_back_error ?? null;
                $postRidePage->vehicle_id_error = $postRideError->vehicle_id_error ?? null;
                $postRidePage->make_error = $postRideError->make_error ?? null;
                $postRidePage->model_error = $postRideError->model_error ?? null;
                $postRidePage->vehicle_type_error = $postRideError->vehicle_type_error ?? null;
                $postRidePage->color_error = $postRideError->color_error ?? null;
                $postRidePage->license_error = $postRideError->license_error ?? null;
                $postRidePage->year_error = $postRideError->year_error ?? null;
                $postRidePage->fuel_error = $postRideError->fuel_error ?? null;
                $postRidePage->photo_error = $postRideError->photo_error ?? null;
                $postRidePage->booking_method_error = $postRideError->booking_method_error ?? null;
                $postRidePage->anything_to_add_error = $postRideError->anything_to_add_error ?? null;
                $postRidePage->smoking_error = $postRideError->smoking_error ?? null;
                $postRidePage->animal_error = $postRideError->animal_error ?? null;
                $postRidePage->luggage_error = $postRideError->luggage_error ?? null;
                $postRidePage->price_error = $postRideError->price_error ?? null;
                $postRidePage->payment_method_error = $postRideError->payment_method_error ?? null;
                $postRidePage->booking_type_error = $postRideError->booking_type_error ?? null;
                $postRidePage->agree_terms_error = $postRideError->agree_terms_error ?? null;
                $messages = $this->getApiSuccessMessageFields(['past_time_message', 'past_date_message'], $selectedLanguage);
            }
        }


        if ($selectedLanguage) {
            $locale = $selectedLanguage->abbreviation;
        } else {
            $locale = 'en';
        }

        App::setLocale($locale);

        $validationMessages = [
            'required' => trans('validation.required'),
            'date' => trans('validation.date'),
            'date_format' => trans('validation.date_format'),
            'max.string' => trans('validation.max.string'),
            'string' => trans('validation.string'),
            'max_words' => trans('validation.max_words'),
            'numeric' => trans('validation.numeric'),
            'mimes' => trans('validation.mimes'),
            'max.file' => trans('validation.max.file'),
            'min' => trans('validation.min.numeric'),
        ];

        $postRidePageData = $postRidePage ? $postRidePage->toArray() : null;
        if ($postRidePageData !== null) {
            $postRidePageData['indicates_required_field_text'] = $postRidePage->indicates_required_field_text;
        }

        $data = ['postRidePage' => $postRidePageData, 'messages' => $messages, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Post ride page get successfully');
    }

    public function findRideIndex(Request $request)
    {
        $findRidePage = null;
        $messages = null;
        if ($request->lang_id && $request->lang_id != 0) {


            $selectedLanguage = Language::where('id', $request->lang_id)->first();
            // Retrieve the FindRidePageSettingDetail associated with the selected language
            $findRidePage = FindRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            $messages = $this->getApiSuccessMessageFields([
                'female_user_message',
                'star5_passenger_message',
                'star4_passenger_message',
                'star3_passenger_message',
                'passenger_with_review_message',
                'search_result_clear_message',
            ], $selectedLanguage);
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = $this->getApiSuccessMessageFields([
                    'female_user_message',
                    'star5_passenger_message',
                    'star4_passenger_message',
                    'star3_passenger_message',
                    'passenger_with_review_message',
                    'search_result_clear_message',
                ], $selectedLanguage);
            }
        }

        if ($selectedLanguage) {
            $locale = $selectedLanguage->abbreviation;
        } else {
            $locale = 'en';
        }

        App::setLocale($locale);

        $validationMessages = [
            'required' => trans('validation.required'),
        ];

        $vehicleTypeOptions = $this->getRideFeatureOptionGroups(
            $selectedLanguage?->id,
            $this->defaultLang?->id
        )->get('vehicle_type', collect())->values();

        $data = [
            'findRidePage' => $findRidePage,
            'vehicleTypeOptions' => $vehicleTypeOptions,
            'messages' => $messages,
            'validationMessages' => $validationMessages
        ];
        return $this->successResponse($data, 'Search ride page get successfully');
    }


    public function getDataFromGoogleApi($from, $to)
    {
        $apiKey = env('GOOGLE_API_KEY');
        $ch = curl_init();

        // URL encode the addresses to properly handle spaces and special characters
        // This ensures city names like "Montreal, QC" and "Ottawa, ON" work correctly
        $fromEncoded = urlencode($from);
        $toEncoded = urlencode($to);

        $apiUrl = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $fromEncoded . "&destinations=" . $toEncoded . "&units=imperial&key=" . $apiKey . "";

        Log::info('Google Maps API Request (API)', [
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
            Log::error('Google Maps API cURL Error (API): ' . curl_error($ch), [
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

            Log::info('Google Maps API Success (API)', [
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
            Log::warning('Google Maps API returned non-OK status (API)', [
                'status' => $data['status'] ?? 'unknown',
                'error_message' => $data['error_message'] ?? 'No error message',
                'from' => $from,
                'to' => $to,
                'response' => $data
            ]);
        }

        return $data;
    }

    protected function syncAppRideStopsAndSegments(Ride $ride, Request $request): void
    {
        RideStopSegment::where('ride_id', $ride->id)->delete();
        RideStop::where('ride_id', $ride->id)->delete();

        $originLabel = trim((string) $request->input('from', ''));
        $destinationLabel = trim((string) $request->input('to', ''));
        if ($originLabel === '' || $destinationLabel === '') {
            return;
        }

        $fromSpots = $this->decodeAppJsonArray($request->input('from_spot'));
        $stopCityIds = $this->decodeAppJsonArray($request->input('stop_city_ids'));
        $pickupSpots = $this->decodeAppJsonArray($request->input('pickup_spot'));
        $dropoffSpots = $this->decodeAppJsonArray($request->input('dropoff_spot'));
        $dateSpots = $this->decodeAppJsonArray($request->input('date_spot'));
        $timeSpots = $this->decodeAppJsonArray($request->input('time_spot'));
        $priceSpots = $this->decodeAppJsonArray($request->input('price_spot'));
        $routeFroms = $this->decodeAppJsonArray($request->input('stop_from'));
        $routeTos = $this->decodeAppJsonArray($request->input('stop_to'));
        $routePrices = $this->decodeAppJsonArray($request->input('stop_price_minor'));

        $routePriceMap = [];
        foreach ($routeFroms as $index => $fromLabelRaw) {
            $fromLabel = trim((string) $fromLabelRaw);
            $toLabel = trim((string) ($routeTos[$index] ?? ''));

            if ($fromLabel === '' || $toLabel === '') {
                continue;
            }

            $routePriceMap[strtolower($fromLabel) . '|' . strtolower($toLabel)] =
                (int) ($routePrices[$index] ?? 0);
        }

        $resolveRoutePrice = function (string $fromLabel, string $toLabel, int $fallback = 0) use ($routePriceMap): int {
            $key = strtolower(trim($fromLabel)) . '|' . strtolower(trim($toLabel));
            return $routePriceMap[$key] ?? $fallback;
        };

        $originDepartureAt = $this->parseAppRideDateTime(
            (string) $request->input('date', ''),
            (string) $request->input('time', '')
        );

        $destinationEtaAt = null;
        if (!empty($ride->destination_reached_date) && !empty($ride->destination_reached_time)) {
            $destinationEtaAt = Carbon::parse($ride->destination_reached_date . ' ' . $ride->destination_reached_time);
        }

        $stopRecords = [[
            'stop_order' => 1,
            'city_id' => (int) $request->input('from_city_id', 0) ?: $this->resolveAppCityId($originLabel),
            'label' => $originLabel,
            'departure_at' => $originDepartureAt,
            'pickup_dropoff_location' => $request->input('pickup'),
            'eta_at' => null,
            'price_delta_minor' => 0,
            'seats_available' => $ride->seats,
            'is_pickup' => true,
            'is_dropoff' => false,
        ]];

        $previousLabel = $originLabel;
        $segmentPrices = [];

        foreach ($fromSpots as $index => $stopLabelRaw) {
            $stopLabel = trim((string) $stopLabelRaw);
            if ($stopLabel === '') {
                continue;
            }

            $pickupDropoffLocation = trim((string) ($pickupSpots[$index] ?? $dropoffSpots[$index] ?? ''));
            $stopDepartureAt = $this->parseAppRideDateTime(
                (string) ($dateSpots[$index] ?? ''),
                (string) ($timeSpots[$index] ?? '')
            );
            $previousLabel = count($stopRecords) === 1
                ? $originLabel
                : (string) ($stopRecords[count($stopRecords) - 1]['label'] ?? $originLabel);
            $priceDeltaMinor = $resolveRoutePrice(
                $previousLabel,
                $stopLabel,
                (int) ($priceSpots[$index] ?? 0)
            );

            $stopRecords[] = [
                'stop_order' => count($stopRecords) + 1,
                'city_id' => (int) ($stopCityIds[$index] ?? 0) ?: $this->resolveAppCityId($stopLabel),
                'label' => $stopLabel,
                'departure_at' => $stopDepartureAt,
                'pickup_dropoff_location' => $pickupDropoffLocation !== '' ? $pickupDropoffLocation : null,
                'eta_at' => null,
                'price_delta_minor' => $priceDeltaMinor,
                'seats_available' => $ride->seats,
                'is_pickup' => true,
                'is_dropoff' => true,
            ];
        }

        $previousLabel = !empty($stopRecords)
            ? (string) ($stopRecords[count($stopRecords) - 1]['label'] ?? $originLabel)
            : $originLabel;

        $finalLegPrice = $resolveRoutePrice(
            $previousLabel,
            $destinationLabel,
            !empty($priceSpots)
                ? (int) ($priceSpots[count($priceSpots) - 1] ?? 0)
                : 0
        );

        $stopRecords[] = [
            'stop_order' => count($stopRecords) + 1,
            'city_id' => (int) $request->input('to_city_id', 0) ?: $this->resolveAppCityId($destinationLabel),
            'label' => $destinationLabel,
            'departure_at' => null,
            'pickup_dropoff_location' => $request->input('dropoff'),
            'eta_at' => $destinationEtaAt,
            'price_delta_minor' => $finalLegPrice,
            'seats_available' => $ride->seats,
            'is_pickup' => false,
            'is_dropoff' => true,
        ];

        $savedStopIds = [];
        $savedStopIdsByLabel = [];
        foreach ($stopRecords as $record) {
            $savedStop = RideStop::create([
                'ride_id' => $ride->id,
                'stop_order' => $record['stop_order'],
                'city_id' => $record['city_id'],
                'label' => $record['label'],
                'departure_at' => $record['departure_at'],
                'pickup_dropoff_location' => $record['pickup_dropoff_location'],
                'eta_at' => $record['eta_at'],
                'price_delta_minor' => $record['price_delta_minor'],
                'seats_available' => $record['seats_available'],
                'is_pickup' => $record['is_pickup'],
                'is_dropoff' => $record['is_dropoff'],
            ]);

            $savedStopIds[] = $savedStop->id;
            $savedStopIdsByLabel[strtolower(trim((string) $savedStop->label))] = $savedStop->id;
        }

        if (!empty($routePriceMap)) {
            foreach ($routeFroms as $index => $fromLabelRaw) {
                $fromLabel = strtolower(trim((string) $fromLabelRaw));
                $toLabel = strtolower(trim((string) ($routeTos[$index] ?? '')));

                $fromStopId = $savedStopIdsByLabel[$fromLabel] ?? null;
                $toStopId = $savedStopIdsByLabel[$toLabel] ?? null;

                if (!$fromStopId || !$toStopId || $fromStopId === $toStopId) {
                    continue;
                }

                RideStopSegment::create([
                    'ride_id' => $ride->id,
                    'from_stop_id' => $fromStopId,
                    'to_stop_id' => $toStopId,
                    'price_minor' => (int) ($routePrices[$index] ?? 0),
                ]);
            }
        } else {
            for ($index = 0; $index < count($savedStopIds) - 1; $index++) {
                $fromStopId = $savedStopIds[$index] ?? null;
                $toStopId = $savedStopIds[$index + 1] ?? null;
                if (!$fromStopId || !$toStopId || $fromStopId === $toStopId) {
                    continue;
                }

                $fromLabel = (string) ($stopRecords[$index]['label'] ?? '');
                $toLabel = (string) ($stopRecords[$index + 1]['label'] ?? '');

                RideStopSegment::create([
                    'ride_id' => $ride->id,
                    'from_stop_id' => $fromStopId,
                    'to_stop_id' => $toStopId,
                    'price_minor' => $resolveRoutePrice(
                        $fromLabel,
                        $toLabel,
                        (int) ($priceSpots[$index] ?? 0)
                    ),
                ]);
            }
        }
    }

    protected function decodeAppJsonArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    protected function normalizeAppPostRideRequest(Request $request, ?Ride $ride = null): void
    {
        $merge = [];

        if (!$request->filled('seats_total') && $request->filled('seats')) {
            $merge['seats_total'] = $request->input('seats');
        }

        if (!$request->has('origin') && $request->filled('from')) {
            $merge['origin'] = [
                'label' => (string) $request->input('from'),
                'city_id' => $request->input('from_city_id') ?: data_get($request->input('origin'), 'city_id'),
            ];
        }

        if (!$request->has('destination') && $request->filled('to')) {
            $merge['destination'] = [
                'label' => (string) $request->input('to'),
                'city_id' => $request->input('to_city_id') ?: data_get($request->input('destination'), 'city_id'),
            ];
        }

        if (!$request->filled('price_minor') && $request->filled('price')) {
            $merge['price_minor'] = $request->input('price');
        }

        if (!$request->filled('power_type') && $request->filled('car_type')) {
            $merge['power_type'] = $request->input('car_type');
        }

        if ($request->exists('recurring')) {
            $merge['recurring'] = $this->isTruthyAppValue($request->input('recurring')) ? 1 : 0;
        }

        if (!$request->filled('vehicle_mode')) {
            if ($this->isTruthyAppValue($request->input('add_vehicle'))) {
                $merge['vehicle_mode'] = 'add_new';
            } elseif ($this->isTruthyAppValue($request->input('added_vehicle')) || $request->filled('vehicle_id')) {
                $merge['vehicle_mode'] = 'existing';
            } elseif ($request->exists('skip_vehicle')) {
                $merge['vehicle_mode'] = 'skip';
            } else {
                $merge['vehicle_mode'] = $ride && $ride->vehicle_id ? 'existing' : 'skip';
            }
        }

        $features = $this->decodeAppFeatureList($request->input('features'));
        if (!empty($features) || $request->filled('features')) {
            $merge['features'] = $features;
        }

        if (!$request->has('stops')) {
            $fromSpots = $this->decodeAppJsonArray($request->input('from_spot'));
            $stopCityIds = $this->decodeAppJsonArray($request->input('stop_city_ids'));
            $pickupSpots = $this->decodeAppJsonArray($request->input('pickup_spot'));
            $dropoffSpots = $this->decodeAppJsonArray($request->input('dropoff_spot'));
            $dateSpots = $this->decodeAppJsonArray($request->input('date_spot'));
            $timeSpots = $this->decodeAppJsonArray($request->input('time_spot'));
            $priceSpots = $this->decodeAppJsonArray($request->input('price_spot'));

            $stops = [];
            foreach ($fromSpots as $index => $labelRaw) {
                $label = trim((string) $labelRaw);
                if ($label === '') {
                    continue;
                }

                $departureAt = $this->parseAppRideDateTime(
                    (string) ($dateSpots[$index] ?? ''),
                    (string) ($timeSpots[$index] ?? '')
                );

                $stops[] = [
                    'label' => $label,
                    'city_id' => (int) ($stopCityIds[$index] ?? 0) ?: $this->resolveAppCityId($label),
                    'pickup_dropoff_location' => trim((string) ($pickupSpots[$index] ?? $dropoffSpots[$index] ?? '')),
                    'departure_at' => $departureAt ? $departureAt->format('Y-m-d H:i') : null,
                    'price_delta_minor' => (int) ($priceSpots[$index] ?? 0),
                    'is_pickup' => true,
                    'is_dropoff' => true,
                ];
            }

            if (!empty($stops)) {
                $merge['stops'] = $stops;
            }
        }

        if (!empty($merge)) {
            $request->merge($merge);
        }

        if ($request->hasFile('image') && !$request->hasFile('vehicle_image')) {
            $request->files->set('vehicle_image', $request->file('image'));
        }
    }

    protected function decodeAppFeatureList($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('strval', $value), fn($item) => $item !== ''));
        }

        if (!is_string($value)) {
            return [];
        }

        $value = trim($value);
        if ($value === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return array_values(array_filter(array_map('strval', $decoded), fn($item) => $item !== ''));
        }

        $trimmed = trim($value, "[]");
        if ($trimmed === '') {
            return [];
        }

        $parts = preg_split('/\s*,\s*|\s*=\s*/', $trimmed);
        return array_values(array_filter(array_map(function ($item) {
            return trim((string) $item, " \t\n\r\0\x0B\"'");
        }, $parts), fn($item) => $item !== ''));
    }

    protected function isTruthyAppValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true);
    }

    protected function mapAppPostRideValidationErrors(array $errors): array
    {
        $mapped = $errors;

        if (isset($mapped['origin'])) {
            $mapped['from'] = $mapped['origin'];
            unset($mapped['origin']);
        }

        if (isset($mapped['origin.label'])) {
            $mapped['from'] = array_merge($mapped['from'] ?? [], $mapped['origin.label']);
            unset($mapped['origin.label']);
        }

        if (isset($mapped['origin.city_id'])) {
            $mapped['from'] = array_merge($mapped['from'] ?? [], $mapped['origin.city_id']);
            unset($mapped['origin.city_id']);
        }

        if (isset($mapped['destination'])) {
            $mapped['to'] = $mapped['destination'];
            unset($mapped['destination']);
        }

        if (isset($mapped['destination.label'])) {
            $mapped['to'] = array_merge($mapped['to'] ?? [], $mapped['destination.label']);
            unset($mapped['destination.label']);
        }

        if (isset($mapped['destination.city_id'])) {
            $mapped['to'] = array_merge($mapped['to'] ?? [], $mapped['destination.city_id']);
            unset($mapped['destination.city_id']);
        }

        if (isset($mapped['seats_total'])) {
            $mapped['seats'] = $mapped['seats_total'];
            unset($mapped['seats_total']);
        }

        if (isset($mapped['price_minor'])) {
            $mapped['price'] = $mapped['price_minor'];
            unset($mapped['price_minor']);
        }

        if (isset($mapped['vehicle_image'])) {
            $mapped['image'] = $mapped['vehicle_image'];
            unset($mapped['vehicle_image']);
        }

        return $mapped;
    }

    protected function parseAppRideDateTime(string $date, string $time): ?Carbon
    {
        $date = trim($date);
        $time = trim($time);
        if ($date === '' || $time === '') {
            return null;
        }

        foreach (['F d, Y H:i', 'F j, Y H:i', 'Y-m-d H:i', 'Y-m-d H:i:s'] as $format) {
            try {
                return Carbon::createFromFormat($format, $date . ' ' . $time);
            } catch (\Throwable $e) {
            }
        }

        try {
            return Carbon::parse($date . ' ' . $time);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function resolveAppCityId(string $label): ?int
    {
        $cityName = trim(explode(',', $label)[0] ?? $label);
        if ($cityName === '') {
            return null;
        }

        $city = City::where('name', $cityName)->first();
        return $city ? (int) $city->id : null;
    }
}
