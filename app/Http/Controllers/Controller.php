<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use App\Models\Language;
use App\Models\ChatsPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Notification;
use App\Models\Rating;
use App\Models\PostRidePageSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\FeaturesSettingDetail;
use App\Models\VideoDetail;
use App\Models\PxOptionGroup;
use App\Models\PxRide;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $defaultLang;

    protected $selectedLanguage;

    protected $siteText;
    protected $availableCurrencies = [];

    public function __construct()
    {

        // todo : If admin, ...
        // 

        $this->defaultLang = getDefaultLanguage();

        $lang = request()->route('lang'); // get route parameter
        if ($lang) {
            session(['selectedLanguage' => $lang]);
        } else {
            $lang = request()->query('lang');
            if (!$lang) {
                $lang = session('selectedLanguage', $this->defaultLang->abbreviation);
            }
            session(['selectedLanguage' => $lang]);
        }
        $this->selectedLanguage = Language::resolveLanguage(session('selectedLanguage'));
        $languages = Language::all();
        
        $this->availableCurrencies = $this->resolveAvailableCurrencies();

        // $notificationPage = ChatsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $successMessage = SuccessMessagesSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $this->siteText = getCurrentSiteText();

        // Share notifications with all views
        $this->middleware(function ($request, $next) use ($lang) {
            if (auth()->check()) {
                $user = auth()->user();
                $user_id = $user->id;
                // Redirect users based on their profile completion step when they try to access certain routes
                $routeName = request()->route()->getName();
                if ($routeName === 'profile' || $routeName === 'welcomeRoute') {
                    if ($user->step1 == 0) {
                        // personal information
                        return redirect()->route('step1to5', ['lang' => $lang]);
                    } elseif ($user->step2 == 0) {
                        // profile image
                        return redirect()->route('step2to5', ['lang' => $lang]);
                    } elseif ($user->step3 == 0) {
                        // my vehicle information
                        return redirect()->route('step3to5', ['lang' => $lang]);
                    } elseif ($user->step4 == 0) {
                        // driver license information
                        return redirect()->route('step4to5', ['lang' => $lang]);
                    } elseif ($user->step5 == 0) {
                        // phone number verification
                        return redirect()->route('step5to5', ['lang' => $lang]);
                    }
                }

                if ($routeName === 'my_rides' || $routeName === 'post_ride') { // || $routeName === 'ride_detail' 
                    if ($user->step3 !== 1) {
                        // vehicle information
                        return redirect()->route('profile.vehicle', ['lang' => $lang]);
                    } elseif ($user->step4 !== 1) {
                        // driver license information
                        return redirect()->route('driver.verify', ['lang' => $lang]);
                    } elseif ($user->step5 !== 1) {
                        // phone number verification
                        return redirect()->route('phone', ['lang' => $lang]);
                    }
                }

                if ($routeName === 'my_chats') {
                    if ($user->step1 !== 1) {
                        // personal information
                        return redirect()->route('profile.edit', ['lang' => $lang]);
                    }
                }

                $notifications = Notification::where('is_delete', '0');
                $notifications = $notifications->where(function ($query) use ($user_id) {
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

                // // ratings
                // $ratings = Rating::where(function ($query) use ($user_id) {
                //     // Ratings where type is 2 and user_id belongs to the user
                //     $query->where('type', '2')
                //         ->whereHas('booking', function ($query) use ($user_id) {
                //             $query->where('user_id', $user_id);
                //         });
                //     // OR Ratings where type is 1 and ride_id belongs to the user
                //     $query->orWhere(function ($query) use ($user_id) {
                //         $query->where('type', '1')
                //             ->whereHas('ride', function ($query) use ($user_id) {
                //                 $query->where('added_by', $user_id);
                //             });
                //     });
                // })
                //     ->with(['from' => function ($query) {
                //         $query->withTrashed(); // Include soft-deleted users
                //     }])
                //     ->where('status', 1)
                //     ->orderBy('id', 'desc')
                //     ->get();
                // View::share('ratings', $ratings);
            }

            return $next($request);
        });

        // $ratings = Rating::all();

        View::share([
            'selectedLanguage' => $this->selectedLanguage,
            'languages' => $languages,
            'siteText' => $this->siteText,
            'successMessage' => $successMessage,
            'availableCurrencies' => $this->availableCurrencies,
            // 'ratings' => $ratings,
            // 'notificationPage' => $notificationPage,
        ]);
    }

    protected function resolveAvailableCurrencies(): array
    {
        $fallback = [
            'USD' => ['code' => 'USD', 'label' => 'USD', 'symbol' => '$'],
            'CAD' => ['code' => 'CAD', 'label' => 'CAD', 'symbol' => 'C$'],
        ];

        try {
            $group = PxOptionGroup::query()
                ->where('code', 'currency')
                ->with(['options' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                }])
                ->first();

            if (!$group || $group->options->isEmpty()) {
                return $fallback;
            }

            $resolved = [];
            foreach ($group->options as $option) {
                $code = strtoupper((string) ($option->code ?? ''));
                if ($code === '' || strlen($code) !== 3) {
                    continue;
                }
                $meta = is_array($option->meta) ? $option->meta : [];
                $resolved[$code] = [
                    'code' => $code,
                    'label' => (string) ($meta['label'] ?? $code),
                    'symbol' => (string) ($meta['symbol'] ?? $this->currencySymbolFor($code)),
                ];
            }

            return !empty($resolved) ? $resolved : $fallback;
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    protected function currencySymbolFor(string $code): string
    {
        $upper = strtoupper($code);
        if ($upper === 'USD') {
            return '$';
        }
        if ($upper === 'CAD') {
            return 'C$';
        }
        return $upper . ' ';
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

    protected function resolvePxOptionDisplayData(
        PxRide $ride,
        $selectedLangId,
        $defaultLangId,
        array $groupCodes = ['booking_mode', 'booking_method']
    ): array {
        $optionGroups = PxOptionGroup::query()
            ->whereIn('code', $groupCodes)
            ->with(['options' => function ($query) use ($selectedLangId, $defaultLangId) {
                $query->where('is_active', true)
                    ->with(['translations' => function ($translationQuery) use ($selectedLangId, $defaultLangId) {
                        $translationQuery->whereIn('language_id', array_filter([$selectedLangId, $defaultLangId]));
                    }]);
            }])
            ->get()
            ->keyBy('code');

        $display = [
            'bookingModeLabel' => $this->getOptionLabel($optionGroups->get('booking_mode'), $ride->booking_mode, $selectedLangId, $defaultLangId, 'N/A'),
            'bookingModeCode' => $this->getOptionCode($optionGroups->get('booking_mode'), $ride->booking_mode, ''),
            'bookingMethodLabel' => $this->getOptionLabel($optionGroups->get('booking_method'), $ride->booking_method, $selectedLangId, $defaultLangId, 'N/A'),
            'bookingMethodCode' => $this->getOptionCode($optionGroups->get('booking_method'), $ride->booking_method, ''),
        ];

        if (in_array('cancelation_policy', $groupCodes, true)) {
            $display['cancelationPolicyLabel'] = $this->getOptionLabel(
                $optionGroups->get('cancelation_policy'),
                $ride->cancelation_policy,
                $selectedLangId,
                $defaultLangId,
                'Standard'
            );
        }

        return $display;
    }

    protected function translatePxRideOptions(PxRide $ride, $selectedLangId, $defaultLangId): PxRide
    {
        if (!$ride->relationLoaded('options') || !$ride->options) {
            return $ride;
        }

        $ride->options->transform(function ($option) use ($selectedLangId, $defaultLangId) {
            $selected = $option->translations->firstWhere('language_id', $selectedLangId);
            $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
            $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
            $option->display_description = optional($selected)->description ?: optional($fallback)->description;
            return $option;
        });

        return $ride;
    }

    protected function translatePxRideOptionCollection($rides, $selectedLangId, $defaultLangId)
    {
        foreach ($rides as $ride) {
            $this->translatePxRideOptions($ride, $selectedLangId, $defaultLangId);
        }

        return $rides;
    }

    protected function resolveMatchedSegmentPriceMinor(
        PxRide $ride,
        $fromCityId,
        $toCityId,
        string $fromLabel,
        string $toLabel,
        $fromIndex = null,
        $toIndex = null
    ): int {
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

    protected function buildPxRideDetailsData(PxRide $ride, array $overrides = []): array
    {
        $parentOrigin = (string) ($ride->route->origin_label ?? 'N/A');
        $parentDestination = (string) ($ride->route->destination_label ?? 'N/A');
        $origin = (string) ($overrides['origin'] ?? $parentOrigin);
        $destination = (string) ($overrides['destination'] ?? $parentDestination);

        $orderedStops = $ride->stops ? $ride->stops->sortBy('stop_order')->values() : collect();
        $firstStop = $orderedStops->first();
        $lastStop = $orderedStops->last();

        $originStop = $orderedStops->first(function ($stop) use ($origin) {
            return trim((string) ($stop->label ?? '')) === trim($origin);
        });
        $destinationStop = $orderedStops->first(function ($stop) use ($destination) {
            return trim((string) ($stop->label ?? '')) === trim($destination);
        });

        $pickupLocation = $overrides['pickupLocation'] ?? null;
        if ($pickupLocation === null) {
            if ($originStop && $originStop->pickup_dropoff_location) {
                $pickupLocation = $originStop->pickup_dropoff_location;
            } elseif ($firstStop && $firstStop->pickup_dropoff_location) {
                $pickupLocation = $firstStop->pickup_dropoff_location;
            } else {
                $pickupLocation = $ride->meta['pickup_location'] ?? null;
            }
        }

        $dropoffLocation = $overrides['dropoffLocation'] ?? null;
        if ($dropoffLocation === null) {
            if ($destinationStop && $destinationStop->pickup_dropoff_location) {
                $dropoffLocation = $destinationStop->pickup_dropoff_location;
            } elseif ($lastStop && $lastStop->pickup_dropoff_location) {
                $dropoffLocation = $lastStop->pickup_dropoff_location;
            } else {
                $dropoffLocation = $ride->meta['dropoff_location'] ?? null;
            }
        }

        $originDepartureAt = $overrides['originDepartureAt'] ?? null;
        if ($originDepartureAt === null) {
            if ($originStop && $originStop->eta_at) {
                $originDepartureAt = $originStop->eta_at;
            } elseif ($firstStop && $firstStop->eta_at) {
                $originDepartureAt = $firstStop->eta_at;
            } else {
                $originDepartureAt = $ride->departure_at;
            }
        }

        $currencyCode = strtoupper((string) ($ride->currency ?? 'USD'));
        $currencyMap = ['USD' => '$', 'CAD' => 'C$'];
        $segmentStops = collect($overrides['segmentStops'] ?? []);

        return [
            'parentOrigin' => $parentOrigin,
            'parentDestination' => $parentDestination,
            'origin' => $origin,
            'destination' => $destination,
            'pickupLocation' => $pickupLocation,
            'dropoffLocation' => $dropoffLocation,
            'originDepartureAt' => $originDepartureAt,
            'pricePerSeatMinor' => (int) ($overrides['pricePerSeatMinor'] ?? $ride->price_minor ?? 0),
            'currency' => $currencyMap[$currencyCode] ?? ($currencyCode . ' '),
            'segmentStops' => $segmentStops,
            'segmentMode' => (bool) ($overrides['segmentMode'] ?? $segmentStops->isNotEmpty()),
        ];
    }

    /**
     * Get post ride page with setting details for the selected language, with fallback to default language if not found.
     */
    public function getPostRidePageWithSettingDetail()
    {
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        if ($postRidePage) {
            // Optimized: Batch load all option groups in a single query instead of 7 separate queries
            $postRidePage->mapMultipleOptionColumnsToDetails(
                ['features', 'smoking', 'booking', 'payment_methods', 'animals', 'luggage', 'cancellation_policy'],
                $this->selectedLanguage->id,
                $this->defaultLang->id
            );
        }

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

            // todo : there is not bellow fields in the table find_ride_page_setting_detail ???
            // Optimize vehicle type fields loading - batch load in a single query
            $vehicleTypeIds = [
                $findRidePage->vehicle_type_convertible_text,
                $findRidePage->vehicle_type_hatchback_text,
                $findRidePage->vehicle_type_coupe_text,
                $findRidePage->vehicle_type_minivan_text,
                $findRidePage->vehicle_type_sedan_text,
                $findRidePage->vehicle_type_station_wagon_text,
                $findRidePage->vehicle_type_suv_text,
                $findRidePage->vehicle_type_truck_text,
                $findRidePage->vehicle_type_van_text,
            ];

            $vehicleTypeIds = array_filter($vehicleTypeIds);
            if (!empty($vehicleTypeIds)) {
                $vehicleTypeDetails = FeaturesSettingDetail::whereIn('features_setting_id', $vehicleTypeIds)
                    ->where('language_id', $this->selectedLanguage->id)
                    ->get()
                    ->keyBy('features_setting_id');

                $findRidePage->vehicle_type_convertible_text = $vehicleTypeDetails->get($findRidePage->vehicle_type_convertible_text)?->name;
                $findRidePage->vehicle_type_hatchback_text = $vehicleTypeDetails->get($findRidePage->vehicle_type_hatchback_text)?->name;
                $findRidePage->vehicle_type_coupe_text = $vehicleTypeDetails->get($findRidePage->vehicle_type_coupe_text)?->name;
                $findRidePage->vehicle_type_minivan_text = $vehicleTypeDetails->get($findRidePage->vehicle_type_minivan_text)?->name;
                $findRidePage->vehicle_type_sedan_text = $vehicleTypeDetails->get($findRidePage->vehicle_type_sedan_text)?->name;
                $findRidePage->vehicle_type_station_wagon_text = $vehicleTypeDetails->get($findRidePage->vehicle_type_station_wagon_text)?->name;
                $findRidePage->vehicle_type_suv_text = $vehicleTypeDetails->get($findRidePage->vehicle_type_suv_text)?->name;
                $findRidePage->vehicle_type_truck_text = $vehicleTypeDetails->get($findRidePage->vehicle_type_truck_text)?->name;
                $findRidePage->vehicle_type_van_text = $vehicleTypeDetails->get($findRidePage->vehicle_type_van_text)?->name;
            }
        }

        return $findRidePage;
    }
}
