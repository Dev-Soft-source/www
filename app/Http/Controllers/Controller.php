<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use App\Models\Language;
use App\Models\Booking;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Notification;
use App\Models\Ride;
use App\Models\PostRidePageSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\SiteTextDetail;
use App\Models\City;
use App\Models\VideoDetail;
use App\Http\Controllers\ProfileStepRedirectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $defaultLang;

    protected $selectedLanguage;

    protected $successMessage;

    public function __construct()
    {

        $this->defaultLang = getDefaultLanguage();

        // Initialize language-dependent data per request so POST/PUT routes
        // without a {lang} segment still inherit the active locale correctly.
        $this->middleware(function ($request, $next) {
            $routeName = optional($request->route())->getName();
            $isApiRequest = $request->is('api/*')
                || str_starts_with((string) $routeName, 'app.')
                || $request->expectsJson();

            if ($isApiRequest) {
                $lang = $request->input('lang') ?? $request->query('lang');
                $langId = $request->input('lang_id') ?? $request->query('lang_id');

                if (!$lang && $langId) {
                    $lang = Language::whereKey($langId)->value('abbreviation');
                } elseif ($lang && !$langId) {
                    $lang = Language::whereKey($lang)->value('abbreviation');
                } elseif (!$lang && $langId && auth('sanctum')->check()) {
                    $lang = Language::whereKey(auth('sanctum')->user()->lang_id)->value('abbreviation');
                }
                \Log::info('api route', [Route::currentRouteName(), $lang]);
                // \Log::info('search ride', $request->all());

            } else {
                $lang = $request->route('lang') ?? $request->query('lang');

                if (!$lang) {
                    $lang = session('selectedLanguage');
                }

                // if (!$lang && auth('web')->check() && auth('web')->user()->lang) {
                //     $lang = auth('web')->user()->lang;
                // }
            }

            if (!$lang) {
                $lang = $this->defaultLang->abbreviation;
            }

            session(['selectedLanguage' => $lang]);
            app()->setLocale($lang);

            $this->selectedLanguage = Language::resolveLanguage(app()->getLocale());

            if (!$this->selectedLanguage) {
                $this->selectedLanguage = $this->defaultLang;
                session(['selectedLanguage' => $this->defaultLang->abbreviation]);
            }

            $languages = Language::getAllCached();
            $rideFeatureOptions = $this->getRideFeatureOptionGroups();

            $this->successMessage = SuccessMessagesSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
            $siteText = SiteTextDetail::getByLanguageKeyedBySlug($this->selectedLanguage->id, $this->defaultLang->id);

            View::share([
                'selectedLanguage' => $this->selectedLanguage,
                'languages' => $languages,
                'rideFeatureOptions' => $rideFeatureOptions,
                'siteText' => $siteText,
                'successMessage' => $this->successMessage,
            ]);

            if (auth()->check()) {
                $user = auth()->user();
                $user_id = $user->id;
                $lang = $this->selectedLanguage->abbreviation;

                $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                    $query->where('type', '1')->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    })
                        ->orWhere(function ($query) use ($user_id) {
                            $query->where('type', '2')->whereHas('booking', function ($query) use ($user_id) {
                                $query->where('user_id', $user_id);
                            });
                        })
                        ->orWhere(function ($query) use ($user_id) {
                            $query->where('type', null)->whereHas('receiver', function ($query) use ($user_id) {
                                $query->where('id', $user_id);
                            });
                        });
                })
                    ->orderBy('id', 'desc')
                    ->get();
                View::share('notifications', $notifications);
            }

            return $next($request);
        });
    }

    /**
     * Get post ride page with setting details for the selected language, with fallback to default language if not found.
     */
    public function getPostRidePageWithSettingDetail()
    {
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        // if ($postRidePage) {
        //     // Optimized: Batch load all option groups in a single query instead of 7 separate queries
        //     $postRidePage->mapMultipleOptionColumnsToDetails(
        //         ['smoking', 'booking', 'payment_methods', 'animals', 'luggage', 'cancellation_policy'],
        //         $this->selectedLanguage->id,
        //         $this->defaultLang->id
        //     );

        //     $this->hydrateLegacyFeatureOptions($postRidePage);
        // }

        return $postRidePage;
    }

    public function getFindRidePageWithSettingDetail()
    {
        $findRidePage = FindRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        if ($findRidePage) {
            // Optimized: Batch load all option groups in a single query instead of 7 separate queries
            $findRidePage->mapMultipleOptionColumnsToDetails(
                ['ride_features', 'smoking', 'pets_allowed', 'payment_methods', 'animals', 'luggage', 'cancellation_policy'],
                $this->selectedLanguage->id,
                $this->defaultLang->id
            );
        }

        return $findRidePage;
    }

    protected function getVehicleTypesByLanguage()
    {
        return $this->getVehicleTypesForLanguage($this->selectedLanguage->id, $this->defaultLang->id);
    }

    protected function getFeatureOptionsByLanguage()
    {
        return $this->getFeaturesForLanguage($this->selectedLanguage->id, $this->defaultLang->id);
    }

    protected function getVehicleTypesForLanguage(?int $languageId = null, ?int $fallbackLanguageId = null)
    {
        $languageId = $languageId ?: $this->selectedLanguage?->id ?: $this->defaultLang?->id;
        $fallbackLanguageId = $fallbackLanguageId ?: $this->defaultLang?->id ?: $languageId;
        $vehicleTypeMap = collect($this->getVehicleTypeFeatureMap());
        $featureIds = $vehicleTypeMap->pluck('id');

        $details = FeaturesSettingDetail::whereIn('features_setting_id', $featureIds)
            ->whereIn('language_id', array_unique(array_filter([$languageId, $fallbackLanguageId])))
            ->get()
            ->groupBy('features_setting_id');

        return $vehicleTypeMap->map(function ($vehicleType) use ($details, $languageId, $fallbackLanguageId) {
            $featureId = $vehicleType['id'];
            $localized = $details->get($featureId, collect())
                ->firstWhere('language_id', $languageId);

            $fallback = $details->get($featureId, collect())
                ->firstWhere('language_id', $fallbackLanguageId);

            $detail = $localized ?: $fallback;

            return [
                'id' => $featureId,
                'slug' => $vehicleType['slug'],
                'label' => $detail?->name ?? $fallback?->name,
            ];
        })->filter(fn($type) => !empty($type['id']) && !empty($type['label']))->values();
    }

    protected function getFeaturesForLanguage(?int $languageId = null, ?int $fallbackLanguageId = null)
    {
        $languageId = $languageId ?: $this->selectedLanguage?->id ?: $this->defaultLang?->id;
        $fallbackLanguageId = $fallbackLanguageId ?: $this->defaultLang?->id ?: $languageId;
        $featureIds = $this->getFeatureOptionIds();

        $featureSlugs = FeaturesSetting::whereIn('id', $featureIds)
            ->pluck('slug', 'id');

        $details = FeaturesSettingDetail::whereIn('features_setting_id', $featureIds)
            ->whereIn('language_id', array_unique(array_filter([$languageId, $fallbackLanguageId])))
            ->get()
            ->groupBy('features_setting_id');

        return collect($featureIds)->map(function ($featureId) use ($details, $featureSlugs, $languageId, $fallbackLanguageId) {
            $localized = $details->get($featureId, collect())
                ->firstWhere('language_id', $languageId);

            $fallback = $details->get($featureId, collect())
                ->firstWhere('language_id', $fallbackLanguageId);

            $detail = $localized ?: $fallback;

            return [
                'id' => $featureId,
                'slug' => $featureSlugs->get($featureId),
                'label' => $detail?->name ?? $fallback?->name,
                'icon' => $detail?->icon ?? $fallback?->icon,
                'tooltip' => $detail?->display_tooltip ?? $fallback?->display_tooltip,
            ];
        })->filter(fn($feature) => !empty($feature['id']) && !empty($feature['label']))->values();
    }

    protected function getVehicleTypeFeatureMap(): array
    {
        return [
            ['slug' => 'convertible', 'id' => 38],
            ['slug' => 'hatchback', 'id' => 39],
            ['slug' => 'coupe', 'id' => 40],
            ['slug' => 'minivan', 'id' => 41],
            ['slug' => 'sedan', 'id' => 42],
            ['slug' => 'station_wagon', 'id' => 43],
            ['slug' => 'suv', 'id' => 44],
            ['slug' => 'truck', 'id' => 45],
            ['slug' => 'van', 'id' => 46],
        ];
    }

    protected function getFeatureOptionIds(): array
    {
        // id is refered to db
        return array_merge(range(1, 16), [47]);
    }

    protected function hydrateLegacyFeatureOptions($postRidePage)
    {
        if (!$postRidePage) {
            return $postRidePage;
        }

        $featureOptions = $this->getFeatureOptionsByLanguage();

        foreach ($featureOptions as $featureOption) {
            $legacyKey = 'features_option' . $featureOption['id'];

            $postRidePage->{$legacyKey} = (object) [
                'features_setting_id' => $featureOption['id'],
                'name' => $featureOption['label'],
                'label' => $featureOption['label'],
                'icon' => $featureOption['icon'] ?? null,
                'tooltip' => $featureOption['tooltip'] ?? null,
            ];

            $postRidePage->{$legacyKey . '_tooltip'} = $featureOption['tooltip'] ?? '';
        }

        return $postRidePage;
    }


    protected function getRideFeatureOptionGroups(?int $selectedLangId = null, ?int $defaultLangId = null)
    {
        $selectedLangId = $selectedLangId ?: $this->selectedLanguage?->id ?: $this->defaultLang?->id;
        $defaultLangId = $defaultLangId ?: $this->defaultLang?->id ?: $selectedLangId;
        $cacheKey = implode(':', [
            'features:ride-option-groups',
            'v' . FeaturesSetting::getOptionGroupsCacheVersion(),
            'selected-' . (string) $selectedLangId,
            'default-' . (string) $defaultLangId,
        ]);

        return Cache::rememberForever($cacheKey, function () use ($selectedLangId, $defaultLangId) {
            $groupFeatureIds = [
                'features' => array_merge(range(1, 16), [47]),
                'luggage_size' => range(26, 30),
                'smoking_allowed' => [21, 22],
                'pets_allowed' => range(23, 25),
                'booking_method' => range(31, 32),
                'payment_method' => range(33, 35),
                'cancellation' => range(36, 37),
                'vehicle_type' => range(38, 46),
            ];

            $featureIds = collect($groupFeatureIds)->flatten()->unique()->values()->all();

            $featureSlugs = FeaturesSetting::query()
                ->whereIn('id', $featureIds)
                ->pluck('slug', 'id');

            $details = FeaturesSettingDetail::query()
                ->whereIn('features_setting_id', $featureIds)
                ->whereIn('language_id', array_unique(array_filter([$selectedLangId, $defaultLangId])))
                ->get()
                ->groupBy('features_setting_id');

            return collect($groupFeatureIds)->map(function ($ids, $code) use ($details, $featureSlugs, $selectedLangId, $defaultLangId) {
                $options = collect($ids)
                    ->map(function ($id) use ($details, $featureSlugs, $selectedLangId, $defaultLangId) {
                        $selected = $details->get($id, collect())
                            ->firstWhere('language_id', $selectedLangId);
                        $fallback = $details->get($id, collect())
                            ->firstWhere('language_id', $defaultLangId);
                        $detail = $selected ?: $fallback;

                        if (!$detail) {
                            return null;
                        }

                        return (object) [
                            'id' => $id,
                            'features_setting_id' => $id,
                            'slug' => $featureSlugs->get($id),
                            'icon' => $detail->icon ?? $fallback?->icon,
                            'name' => $detail->name ?? $fallback?->name ?? $featureSlugs->get($id) ?? (string) $id,
                            'tooltip' => $detail->display_tooltip ?? $fallback?->display_tooltip,
                        ];
                    })
                    ->filter()
                    ->values();
                return $options->keyBy('slug');
            });
        });
    }

    // will be removed in future
    protected function getSearchOptionGroups(?int $selectedLangId = null, ?int $defaultLangId = null)
    {
        $selectedLangId = $selectedLangId ?: $this->selectedLanguage?->id ?: $this->defaultLang?->id;
        $defaultLangId = $defaultLangId ?: $this->defaultLang?->id ?: $selectedLangId;

        $groupFeatureIds = [
            'features' => array_merge(range(1, 16), [47]),
            'luggage_size' => range(26, 30),
            'smoking_allowed' => [21, 22],
            'pets_allowed' => range(23, 25),
            'booking_method' => range(31, 32),
            'payment_method' => range(33, 35),
            'vehicle_type' => range(38, 46),
        ];

        $featureIds = collect($groupFeatureIds)->flatten()->unique()->values()->all();

        $featureSlugs = FeaturesSetting::query()
            ->whereIn('id', $featureIds)
            ->pluck('slug', 'id');

        $details = FeaturesSettingDetail::query()
            ->whereIn('features_setting_id', $featureIds)
            ->whereIn('language_id', array_unique(array_filter([$selectedLangId, $defaultLangId])))
            ->get()
            ->groupBy('features_setting_id');

        $groups = collect($groupFeatureIds)->map(function ($ids, $code) use ($details, $featureSlugs, $selectedLangId, $defaultLangId) {
            $options = collect($ids)
                ->map(function ($id) use ($details, $featureSlugs, $selectedLangId, $defaultLangId) {
                    $selected = $details->get($id, collect())
                        ->firstWhere('language_id', $selectedLangId);
                    $fallback = $details->get($id, collect())
                        ->firstWhere('language_id', $defaultLangId);
                    $detail = $selected ?: $fallback;

                    if (!$detail) {
                        return null;
                    }

                    return (object) [
                        'id' => $id,
                        'features_setting_id' => $id,
                        'code' => $featureSlugs->get($id) ?: (string) $id,
                        'slug' => $featureSlugs->get($id),
                        'icon' => $detail->icon ?? $fallback?->icon,
                        'display_label' => $detail->name ?? $fallback?->name ?? $featureSlugs->get($id) ?? (string) $id,
                        'display_description' => $detail->display_tooltip ?? $fallback?->display_tooltip,
                    ];
                })
                ->filter()
                ->values();

            return (object) [
                'code' => $code,
                'options' => $options,
            ];
        })->keyBy('code');

        if ($groups->has('payment_method')) {
            $groups->put('booking_method', $groups->get('payment_method'));
        }

        if ($groups->has('features')) {
            $groups->put('preference', $groups->get('features'));
        }

        return $groups;
    }

    protected function resolveApiLanguage($langId = null): ?Language
    {
        if (!empty($langId)) {
            $language = Language::find($langId);
            if ($language) {
                return $language;
            }
        }

        if ($this->selectedLanguage) {
            return $this->selectedLanguage;
        }

        return $this->defaultLang ?: Language::where('is_default', 1)->first();
    }


    protected function getApiSuccessMessageFields(array $fields, ?Language $language = null)
    {
        $message = $this->successMessage;

        if (!$message) {
            return null;
        }

        if (empty($fields)) {
            return $message;
        }

        return (object) $message->only($fields);
    }

    protected function extractIntermediateStopsForForm(Ride $ride): array
    {
        $originLabel = $ride->detail->departure ?? '';
        $destinationLabel = $ride->detail->destination ?? '';

        return $ride->rideStops
            ->filter(function ($stop) use ($originLabel, $destinationLabel) {
                $label = trim((string) $stop->label);

                if ($label === '') {
                    return false;
                }

                return !in_array(
                    strtolower($label),
                    [strtolower($originLabel), strtolower($destinationLabel)]
                );
            })
            ->map(fn($stop) => [
                'label' => $stop->label,
                'city_id' => $stop->city_id,
                'departure_at' => !empty($stop->departure_at)
                    ? Carbon::parse($stop->departure_at)->format('Y-m-d H:i')
                    : null,
                'depature_date' => !empty($stop->departure_at)
                    ? Carbon::parse($stop->departure_at)->format('Y-m-d')
                    : null,
                'depature_time' => !empty($stop->departure_at)
                    ? Carbon::parse($stop->departure_at)->format('H:i')
                    : null,
                'price_delta_minor' => $stop->price_delta_minor,
                'is_pickup' => $stop->is_pickup,
                'is_dropoff' => $stop->is_dropoff,
                'pickup_dropoff_location' => $stop->pickup_dropoff_location
                    ?? $stop->pickup_location
                    ?? $stop->dropoff_location,
                'pickup_location' => $stop->pickup_location ?? null,
                'dropoff_location' => $stop->dropoff_location ?? null,
            ])
            ->values()
            ->toArray();
    }

    protected function makeDetailOfRide(Ride $ride, $from_stop_id = null, $to_stop_id = null): Ride
    {

        if (!$from_stop_id || !$to_stop_id) {
            // main ride
            $from_stop_id = $ride->rideStops->first()?->id;
            $to_stop_id   = $ride->rideStops->last()?->id;

            $ride->matched_segment_price_minor = $ride->detail->price;
            // $ride->city_id = $ride->rideStops->first()?->city_id;
            ////
            $rideDetail = $ride->detail;
            $ride->departure = $rideDetail->departure;
            $ride->destination = $rideDetail->destination;
            $ride->pickup = $rideDetail->pickup;
            $ride->dropoff = $rideDetail->dropoff;
            $ride->date = $rideDetail->date;
            $ride->time = $rideDetail->time;
            $ride->price_minor = $rideDetail->price;
        } else {

            $stopSegment = $ride->rideStopSegments()
                ->where([
                    'from_stop_id' => $from_stop_id,
                    'to_stop_id' => $to_stop_id,
                ])
                ->first();
            
            $stopOfFrom = $ride->rideStops->firstWhere('id', $from_stop_id);
            $stopOfTo   = $ride->rideStops->firstWhere('id', $to_stop_id);
            // $ride->city_id = $stopOfFrom->city_id;

            $ride->matched_segment_price_minor = $stopSegment?->price_minor;

            $ride->matched_from_stop_index = $stopOfFrom
                ? ((int) $stopOfFrom->stop_order - 1)
                : 0;

            $ride->matched_to_stop_index = $stopOfTo
                ? ((int) $stopOfTo->stop_order - 1)
                : 1;

            $ride->departure = $stopOfFrom->label;
            $ride->destination = $stopOfTo->label;
            $ride->pickup = $stopOfFrom->pickup_dropoff_location;
            $ride->dropoff = $stopOfTo->pickup_dropoff_location;
            $ride->date = Carbon::parse($stopOfFrom->departure_at)->toDateString();
            $ride->time = Carbon::parse($stopOfFrom->departure_at)->toTimeString();
            $ride->price_minor = $ride->matched_segment_price_minor;
        }

        $ride->matched_from_stop_id = $from_stop_id;
        $ride->matched_to_stop_id   = $to_stop_id;

        // $ride->applyDisplaySummaryAttributes();

        $ride->matched_seats_available =
            ($ride->matched_from_stop_id && $ride->matched_to_stop_id && method_exists($ride, 'resolveSegmentAvailableSeats'))
            ? $ride->resolveSegmentAvailableSeats(
                (int) $ride->matched_from_stop_id,
                (int) $ride->matched_to_stop_id
            )
            : (int) ($ride->seats_available ?? $ride->seats ?? 0);
// dd($ride);
        return $ride;
    }

    // search
    protected function getPxSearchFilters(Request $request): array
    {
        $filters = [
            'driver_age' => $request->input('driver_age'),
            'driver_rating' => $request->input('driver_rating'),
            'driver_phone' => $request->boolean('driver_phone') ? 1 : null,
            'driver_name' => trim((string) $request->input('driver_name')),
            'booking_method' => $request->input('booking_method'),
            'vehicle_type' => trim((string) $request->input('vehicle_type')),
            'luggage_size' => $request->input('luggage_size'),
            'smoking_allowed' => $request->input('smoking_allowed'),
            'pets_allowed' => $request->input('pets_allowed'),
            'women_only' => $request->boolean('women_only') ? 1 : null,
            'extra_care' => $request->boolean('extra_care') ? 1 : null,
            'hide_full_rides' => $request->boolean('hide_full_rides') ? 1 : null,
            'ride_option_ids' => array_values(array_filter(array_map('intval', (array) $request->input('ride_option_ids', [])))),
        ];

        return collect($filters)
            ->map(function ($value) {
                return $value === '0' ? null : $value;
            })
            ->all();
    }

    protected function getTemporarilyBlockedDriverIds(int $userId): array
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

    protected function resolveRecentSearchCityId(?string $label): ?int
    {
        $label = trim((string) $label);
        if ($label === '') {
            return null;
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', $label)), fn($part) => $part !== ''));
        $cityName = $parts[0] ?? '';
        $stateAbbreviation = $parts[1] ?? null;
        $countryName = $parts[2] ?? null;

        if ($cityName === '') {
            return null;
        }

        $query = City::query()->where('status', '1')->where('name', $cityName);

        if ($stateAbbreviation) {
            $query->whereHas('state', function ($stateQuery) use ($stateAbbreviation) {
                $stateQuery->where('abrv', $stateAbbreviation);
            });
        }

        if ($countryName) {
            $query->whereHas('state.country', function ($countryQuery) use ($countryName) {
                $countryQuery->where('name', $countryName);
            });
        }

        $city = $query->first();

        if (!$city) {
            $city = City::query()
                ->where('status', '1')
                ->where('name', $cityName)
                ->first();
        }

        return $city ? (int) $city->id : null;
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

    
    protected function getRideDetail(Ride $ride, $originLabel = "", $destinationLabel = "", $originCityId = 0, $destinationCityId = 0, $hasLocationSearch = true,)
    {
        if($ride->rideStopSegments()->count() == 0 || $originLabel == '') {
            $rideDetail = $ride->detail;
            $ride->departure = $rideDetail->departure;
            $ride->destination = $rideDetail->destination;
            $ride->from_city_id = $rideDetail->origin_city_id;
            $ride->to_city_id = $rideDetail->destination_city_id;
            $ride->date = $rideDetail->date;
            $ride->time = $rideDetail->time;
            $ride->price_minor = $rideDetail->price;
            $ride->pickup = $rideDetail->pickup;
            $ride->dropoff = $rideDetail->dropoff;
            $ride->from_stop_id = 0;
            $ride->to_stop_id = 0;
            return $ride;
        }

        $stopsSource = $ride->stops ?? $ride->rideStops ?? null;
        $orderedStops = $stopsSource
            ? $stopsSource->sortBy('stop_order')->values()->all()
            : [];

        if ($hasLocationSearch) {
            [$matchedFromIndex, $matchedToIndex] = $this->findMatchingSegmentIndices(
                $orderedStops,
                $originCityId,
                $destinationCityId,
                (string) $originLabel,
                (string) $destinationLabel
            );
        } else {
            $matchedFromIndex = count($orderedStops) >= 2 ? 0 : null;
            $matchedToIndex = count($orderedStops) >= 2 ? count($orderedStops) - 1 : null;
        }

        $ride->matched_from_stop_index = $matchedFromIndex;
        $ride->matched_to_stop_index = $matchedToIndex;

        $ride->matched_from_stop_id = ($matchedFromIndex !== null && isset($orderedStops[$matchedFromIndex]))
            ? (int) ($orderedStops[$matchedFromIndex]->id ?? 0)
            : null;
        $ride->matched_to_stop_id = ($matchedToIndex !== null && isset($orderedStops[$matchedToIndex]))
            ? (int) ($orderedStops[$matchedToIndex]->id ?? 0)
            : null;

        $ride->matched_departure_at = ($matchedFromIndex !== null && isset($orderedStops[$matchedFromIndex]))
            ? Carbon::parse($orderedStops[$matchedFromIndex]->departure_at)
            : null;

        $ride->matched_pickup = ($matchedFromIndex !== null && isset($orderedStops[$matchedFromIndex]))
            ? (string) ($orderedStops[$matchedFromIndex]->pickup_dropoff_location)
            : null;
        $ride->matched_dropoff = ($matchedToIndex !== null && isset($orderedStops[$matchedToIndex]))
            ? (string) ($orderedStops[$matchedToIndex]->pickup_dropoff_location)
            : null;

        
        // \Log::info('recentSearches',[$matchedFromIndex,$matchedToIndex]);

        $ride->matched_segment_price_minor = $this->resolveMatchedSegmentPriceMinor(
            $ride,
            $originCityId,
            $destinationCityId,
            (string) $originLabel,
            (string) $destinationLabel,
            $matchedFromIndex,
            $matchedToIndex
        );

        
        $ride->matched_seats_available = ($ride->matched_from_stop_id && $ride->matched_to_stop_id && method_exists($ride, 'resolveSegmentAvailableSeats'))
        ? $ride->resolveSegmentAvailableSeats(
            (int) $ride->matched_from_stop_id,
            (int) $ride->matched_to_stop_id
            )
            : (int) ($ride->seats_available ?? $ride->seats ?? 0);
            
        // update with stops'
        $ride->departure = $originLabel;
        $ride->destination = $destinationLabel;
        $ride->from_city_id = $originCityId;
        $ride->to_city_id = $destinationCityId;
        $ride->date = $ride->matched_departure_at->toDateString();
        $ride->time = $ride->matched_departure_at->toTimeString();
        $ride->price_minor = $ride->matched_segment_price_minor;
        $ride->pickup = $ride->matched_pickup;
        $ride->dropoff = $ride->matched_dropoff;
        $ride->from_stop_id = $ride->matched_from_stop_id;
        $ride->to_stop_id = $ride->matched_to_stop_id;
        $ride->seats = $ride->matched_seats_available;

        return $ride;
    }

}
