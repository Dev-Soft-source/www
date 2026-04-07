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
use App\Models\NotificationMessageDetail;
use App\Models\SiteTextDetail;
use App\Models\City;
use App\Models\User;
use App\Models\SeatDetail;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use App\Models\CoffeeWallet;
use App\Http\Controllers\ProfileStepRedirectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Services\FCMService;
use App\Models\FCMToken;
use Twilio\Rest\Client;
use App\Models\PhoneNumber;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $defaultLang;

    protected $selectedLanguage;

    protected $successMessage;

    public function __construct()
    {

        if (Route::currentRouteName() == 'fcmToken') return;

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
                } elseif ($lang && !$langId && is_numeric($lang)) {
                    $lang = Language::whereKey($lang)->value('abbreviation');
                } elseif (!$lang && !$langId && auth('sanctum')->check()) {
                    $lang = Language::whereKey(auth('sanctum')->user()->lang_id)->value('abbreviation');
                }
                Log::info('api route', [Route::currentRouteName(), $lang]);
                Log::info('payload', $request->all());
            } else {
                $lang = $request->route('lang') ?? $request->query('lang');

                if (!$lang) {
                    $lang = session('selectedLanguage');
                }
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

            $stripeElementsLocale = config('stripe.elements_locale');
            if (! is_string($stripeElementsLocale) || trim($stripeElementsLocale) === '') {
                $abbr = strtolower((string) ($this->selectedLanguage->abbreviation ?? 'en'));
                $stripeElementsLocale = $abbr === 'fr' ? 'fr-CA' : 'en';
            }

            View::share([
                'selectedLanguage' => $this->selectedLanguage,
                'languages' => $languages,
                'rideFeatureOptions' => $rideFeatureOptions,
                'siteText' => $siteText,
                'successMessage' => $this->successMessage,
                'stripeConfig' => [
                    'country' => config('stripe.account_country'),
                    'currency' => config('stripe.account_currency'),
                ],
                'stripeElementsLocale' => $stripeElementsLocale,
            ]);

            if (auth()->check() && !$request->ajax()) {
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



    protected function sendSmsCode($phoneNumber, $user, $sms_message): void
    {
        if (
            !$phoneNumber ||
            env('APP_ENV') === 'development' ||
            !isset($user->sms_notification) ||
            (int) $user->sms_notification !== 1
        ) {
            return;
        }

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        $twilio = new Client($sid, $token);
        $to = $phoneNumber->phone;
        $currentHour = (int) date('H');
        $title = $currentHour < 12
            ? "Good morning {$user->first_name},"
            : ($currentHour < 17
                ? "Good afternoon {$user->first_name},"
                : "Good evening {$user->first_name},");

        $sms_message = $title . "\n" . $sms_message;


        try {
            $twilio->messages->create($to, [
                'from' => $from,
                'body' => $sms_message,
            ]);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logTwilioSmsFailure')) {
                $this->logTwilioSmsFailure($to, $sms_message, $e);
                return;
            }

            $msgPreview = strlen($sms_message) > 80 ? substr($sms_message, 0, 80) . '...' : $sms_message;
            Log::info('SMS failed to ' . $to . '. Message: ' . $msgPreview . ' because ' . $e->getMessage());
        }
    }

    protected function getFeatureOptionsByLanguage()
    {
        return $this->getFeaturesForLanguage($this->selectedLanguage->id, $this->defaultLang->id);
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

    protected function getFeatureOptionIds(): array
    {
        return FeaturesSetting::rideFeaturesSettingIds();
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

        // Legacy UI columns 8–11: heating / AC / ski rack merged into winter tires (id 12).
        if (isset($postRidePage->features_option12)) {
            $winter = $postRidePage->features_option12;
            $tt = $postRidePage->features_option12_tooltip ?? '';
            $postRidePage->features_option8 = $winter;
            $postRidePage->features_option9 = $winter;
            $postRidePage->features_option11 = $winter;
            $postRidePage->features_option8_tooltip = $tt;
            $postRidePage->features_option9_tooltip = $tt;
            $postRidePage->features_option11_tooltip = $tt;
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
                'features' => FeaturesSetting::rideFeaturesSettingIds(),
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
                            'label' => $detail->label ?? $fallback?->label,
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

    /**
     * Payload for search-ride filter preferences (shared: {@see \App\Http\Controllers\Api\App\PreferencesSettingController::getInitData} and search bootstrap).
     *
     * @return array<string, mixed>
     */
    protected function buildSearchRideInitPreferencePayload(): array
    {
        $groups = $this->getRideFeatureOptionGroups($this->selectedLanguage->id);

        $smokingOptions = collect($groups->get('smoking_allowed', collect()))
            ->sortBy('id')
            ->values();
        $petOptions = collect($groups->get('pets_allowed', collect()))
            ->sortBy('id')
            ->values();

        $data['preferencesOptions'] = [
            'smoking_option1' => $smokingOptions->get(0)?->features_setting_id,
            'smoking_option2' => $smokingOptions->get(1)?->features_setting_id,
            'smoking_option1_label' => $smokingOptions->get(0)?->name,
            'smoking_option2_label' => $smokingOptions->get(1)?->name,
            'animals_option1' => $petOptions->get(0)?->features_setting_id,
            'animals_option2' => $petOptions->get(1)?->features_setting_id,
            'animals_option3' => $petOptions->get(2)?->features_setting_id,
            'animals_option1_label' => $petOptions->get(0)?->name,
            'animals_option2_label' => $petOptions->get(1)?->name,
            'animals_option3_label' => $petOptions->get(2)?->name,
        ];

        $cancellationOptions = collect($groups->get('cancellation', collect()))
            ->sortBy('id')
            ->values();

        $data['cancellation'] = [
            'cancellationOptions' => $cancellationOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'cancellationLabels' => $cancellationOptions
                ->pluck('name')
                ->values()
                ->all(),
            'cancellationTooltips' => $cancellationOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];

        $paymentOptions = collect($groups->get('payment_method', collect()))
            ->sortBy('id')
            ->values();

        $data['payment'] = [
            'paymentOptions' => $paymentOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'paymentLabels' => $paymentOptions
                ->pluck('name')
                ->values()
                ->all(),
            'paymentTooltips' => $paymentOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];

        $luggageOptions = collect($groups->get('luggage_size', collect()))
            ->sortBy('id')
            ->values();

        $data['luggage'] = [
            'luggageOptions' => $luggageOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'luggageLabels' => $luggageOptions
                ->pluck('name')
                ->values()
                ->all(),
            'luggageTooltips' => $luggageOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];

        $orderedFeatures = collect($groups->get('features', collect()))
            ->sortBy('id')
            ->filter(fn ($feature) => ($feature->id >= 1 && $feature->id <= 12) || $feature->id == 47)
            ->values();

        $data['features'] = [
            'featuresOptions' => $orderedFeatures->pluck('features_setting_id')->values()->all(),
            'featuresLabels' => $orderedFeatures->pluck('name')->values()->all(),
        ];

        $orderedPassengers = collect($groups->get('features', collect()))
            ->sortBy('id')
            ->filter(fn ($feature) => $feature->id >= 13 && $feature->id <= 16)
            ->values();

        $data['passengers'] = [
            'passengerRatingOptions' => $orderedPassengers->pluck('features_setting_id')->values()->all(),
            'passengerRatingLabels' => $orderedPassengers->pluck('name')->values()->all(),
        ];

        $bookingMethodOptions = collect($groups->get('booking_method', collect()))
            ->sortBy('id')
            ->values();

        $data['booking'] = [
            'bookingOptions' => $bookingMethodOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'bookingLabels' => $bookingMethodOptions
                ->pluck('name')
                ->values()
                ->all(),
            'bookingTooltips' => $bookingMethodOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];

        return $data;
    }

    /**
     * Selected language id for API responses and shared lookups (e.g. getByLanguageWithFallback).
     * Works after middleware sets selectedLanguage; falls back when unset (e.g. tests or edge routes).
     */
    protected function getSelectedLanguageId(): int
    {
        $id = $this->selectedLanguage?->id ?? $this->defaultLang?->id;

        if ($id !== null && (int) $id > 0) {
            return (int) $id;
        }

        $fallback = Language::where('is_default', 1)->value('id')
            ?? Language::query()->orderBy('id')->value('id')
            ?? 1;

        return (int) $fallback;
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
            $ride->departure_city_id = $rideDetail->origin_city_id;
            $ride->destination_city_id = $rideDetail->destination_city_id;
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
            $ride->departure_city_id = $stopOfFrom->city_id;
            $ride->destination_city_id = $stopOfTo->city_id;
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

        return $ride;
    }

    /**
     * Localized departure date/time for mobile JSON (language locale + ride detail noon/midnight labels).
     */
    protected function appendRideDepartureDisplayForApi(Ride $ride, $rideDetailPage, ?string $localeAbbrev): void
    {
        if (empty($ride->date) || $ride->time === null || (string) $ride->time === '') {
            return;
        }

        $locale = $localeAbbrev !== null && $localeAbbrev !== ''
            ? strtolower(str_replace('_', '-', trim($localeAbbrev)))
            : 'en';

        $noonLabel = optional($rideDetailPage)->noon_label ?? 'noon';
        $midnightLabel = optional($rideDetailPage)->midnight_label ?? 'midnight';

        try {
            Carbon::setLocale($locale);
        } catch (\Throwable $e) {
            Carbon::setLocale('en');
        }

        try {
            $dateCarbon = Carbon::parse($ride->date);
            $ride->setAttribute('departure_date_display', $dateCarbon->translatedFormat('F j, Y'));

            $timeStr = is_string($ride->time) ? $ride->time : (string) $ride->time;
            $dt = Carbon::parse(trim($ride->date . ' ' . $timeStr));

            if ($dt->hour === 12 && $dt->minute === 0) {
                $ride->setAttribute('departure_time_display', $dt->format('g:i') . ' ' . $noonLabel);
            } elseif ($dt->hour === 0 && $dt->minute === 0) {
                $ride->setAttribute('departure_time_display', $dt->format('g:i') . ' ' . $midnightLabel);
            } else {
                $ride->setAttribute('departure_time_display', $dt->translatedFormat('g:i a'));
            }
        } catch (\Throwable $e) {
            // Raw date/time still available on the model
        }
    }

    /**
     * Localized date/time for notification list rows (`added_on`) in API JSON.
     * Uses the same month/day/year and noon/midnight rules as ride departure display.
     */
    protected function appendNotificationAddedOnDisplayForApi($notification, $rideDetailPage, ?string $localeAbbrev): void
    {
        $addedOn = $notification->added_on ?? null;
        if ($addedOn === null || $addedOn === '') {
            return;
        }

        $locale = $localeAbbrev !== null && $localeAbbrev !== ''
            ? strtolower(str_replace('_', '-', trim($localeAbbrev)))
            : 'en';

        $noonLabel = optional($rideDetailPage)->noon_label ?? 'noon';
        $midnightLabel = optional($rideDetailPage)->midnight_label ?? 'midnight';

        try {
            Carbon::setLocale($locale);
        } catch (\Throwable $e) {
            Carbon::setLocale('en');
        }

        try {
            $dt = Carbon::parse($addedOn);
            $notification->setAttribute('added_on_date_display', $dt->translatedFormat('F j, Y'));

            if ($dt->hour === 12 && $dt->minute === 0) {
                $notification->setAttribute('added_on_time_display', $dt->format('g:i') . ' ' . $noonLabel);
            } elseif ($dt->hour === 0 && $dt->minute === 0) {
                $notification->setAttribute('added_on_time_display', $dt->format('g:i') . ' ' . $midnightLabel);
            } else {
                $notification->setAttribute('added_on_time_display', $dt->translatedFormat('g:i a'));
            }
        } catch (\Throwable $e) {
            // Raw added_on still on the model
        }
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
        if ($ride->rideStopSegments()->count() == 0 || $originLabel == '') {
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


    protected function sendFCM($message = '', ?User $user = null)
    {
        if (!$user) {
            return;
        }
        $fcmService = new FCMService();
        $tokens = collect([$user->mobile_fcm_token])
            ->merge(
                FCMToken::where('user_id', $user->id)->pluck('token')
            )
            ->filter()
            ->unique()
            ->values();

        foreach ($tokens as $token) {
            try {
                $fcmService->sendNotification($token, $message);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: {$token}, Error: " . $e->getMessage());
            }
        }
    }

    protected function resolveSmsPhoneNumberObject(?User $user)
    {
        if (!$user) {
            return null;
        }

        if (isset($user->primaryPhone) && $user->primaryPhone) {
            return $user->primaryPhone;
        }

        $fallback = $user->phone ?? null;
        if (!$fallback) {
            return null;
        }

        return (object) ['phone' => $fallback];
    }

    /**
     * @see BookingWebNotificationController::dispatchDriverPassengerCancelledNotifications()
     */
    protected function notifyDriverPassengerCancelledWebFlow(
        Booking $booking,
        Ride $ride,
        User $actor,
        string $cancellationMessage,
        int $originalSeats,
        int $cancelSeats,
        float $payoutAmt
    ): void {
        app(BookingWebNotificationController::class)->dispatchDriverPassengerCancelledNotifications(
            $booking->id,
            $actor->id,
            $cancellationMessage,
            $originalSeats,
            $cancelSeats,
            $payoutAmt
        );
    }

    /**
     * Shared "complete booking" flow used by both web and API controllers.
     *
     * Handles:
     * - ride loading + segment details
     * - booking fee/tax adjustments (student waiver / < $15 rule)
     * - booking upsert
     * - seat detail updates
     * - transaction creation (stripe vs wallet) + coffee wall
     * - notifications/emails/FCM/SMS (queued via {@see BookingWebNotificationController})
     *
     * Returns core values needed by the caller response.
     */
    protected function completeBookingUnifiedFlow(int $rideId, int $userId, $stripId, Request $request): array
    {
        $ride = Ride::with([
            'rideStops' => fn($query) => $query->orderBy('stop_order'),
            'rideStopSegments',
            'detail',
        ])->where('id', $rideId)->first();

        $from_stop_id = $request->input('from_stop_id', 0);
        $to_stop_id = $request->input('to_stop_id', 0);

        $ride = $this->makeDetailOfRide($ride, $from_stop_id, $to_stop_id);

        $user = User::where('id', $userId)->with('primaryPhone')->first();

        // if booking method is manual : request book
        $expiryTime = null;
        if ($ride->isRequestBooking()) {
            $currentTime = now();
            $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);

            // Use signed difference
            $difference = $currentTime->diffInHours($rideDateTime, false);

            if ($difference > 48) {
                $expiryTime = $currentTime->copy()->addHours(12);
            } elseif ($difference >= 24) {
                $expiryTime = $currentTime->copy()->addHours(6);
            } elseif ($difference >= 6) {
                $expiryTime = $currentTime->copy()->addHours(2);
            } else {
                $expiryTime = $currentTime->copy()->addMinutes(30);
            }
        }

        $tax_amount = $request->input('tax_amount', 0);

        if ((int) $ride->price_minor < 1500) {
            // ProximaLocal: no booking fee on rides under $15 per seat
            $booking_fee = 0;
            $tax_amount = 0;
        } else {
            // Student booking fee waiver: Validate and apply waiver with card expiration check
            $booking_fee = $this->validateStudentBookingFee($user, $request->booking_credit);
            $tax_amount = $this->validateStudentBookingFee($user, $tax_amount);
        }

        $seats_amount = (float) $request->seats_amount;
        $payment_amount = $seats_amount + (float) $booking_fee + (float) $tax_amount;

        if ($ride->isCashPayment()) {
            $payment_amount = (float) $booking_fee + (float) $tax_amount;
        }

        $secured_cash = null;
        $secured_cash_code = null;
        if ($ride->isSecureCashPayment() && $ride->isInstantBooking()) {
            // send sms for only instant booking
            $secured_cash = '1';
            $secured_cash_code = rand(1000, 9999);
        }

        $seat_ids = $this->normalizeSeatIds($request);

        $seats_number = (int) $request->seats;
        $booking_type = $request->booking_type;

        $total = $request->input('total', 0);
        $tax_percentage = $request->input('tax_percentage', 0);
        $tax_type = $request->input('tax_type');
        $deduct_type = $request->input('deduct_tax');
        $driver_message = (string) ($request->input('driver_message', ''));

        $bookedByWalletRaw = $request->input('booked_by_wallet');
        $bookedByWallet = in_array((string) $bookedByWalletRaw, ['1', 'true', 'True'], true) || (int) $bookedByWalletRaw === 1;

        $isCoffeeWall = (int) $request->input('coffee_wall');
        $payment_method = $request->input('card_id', 'paypal');

        $booking = Booking::where('ride_id', $rideId)
            ->waiting()
            ->where('from_stop_id', $from_stop_id)
            ->where('to_stop_id', $to_stop_id)
            ->where('user_id', $user->id)
            ->first();


        if (isset($booking)) {
            $seats_amount += (float) $booking->fare;
            $seats_number += (int) $booking->seats;
            $_tax_amount = (float) $booking->tax_amount + (float) $tax_amount;
            $_booking_fee = (float) $booking->booking_credit + (float)$booking_fee;

            $booking->update([
                'seats' => $seats_number,
                'fare' => $seats_amount,
                'secured_cash' => $secured_cash,
                'secured_cash_code' => $secured_cash_code,
                'booked_on' => now(),
                'expires_at' => $expiryTime,
                'tax_amount' => $_tax_amount,
                'booking_credit' => $_booking_fee,
                'status' => $ride->isRequestBooking() ? '0' : '1',
            ]);

            $total += (float) $booking->fare + (float) $booking->booking_credit + (float) $tax_amount;
        } else {
            $booking = Booking::create([
                'user_id' => $user->id,
                'ride_id' => $ride->id,
                'from_stop_id' => $from_stop_id,
                'to_stop_id' => $to_stop_id,
                'status' => $ride->isRequestBooking() ? '0' : '1',
                'seats' => $seats_number,
                'type' => $booking_type,
                'booked_on' => now(),
                'booking_credit' => $booking_fee,
                'fare' => $seats_amount,
                'tax_amount' => $tax_amount,
                'secured_cash' => $secured_cash,
                'secured_cash_code' => $secured_cash_code,
                'expires_at' => $expiryTime,
                'departure' => $ride->departure,
                'destination' => $ride->destination,
                'price' => $ride->price_minor,
            ]);
        }

        // update seats in seats table to booked for the selected seats
        if (!empty($seat_ids)) {
            SeatDetail::whereIn('id', $seat_ids)->update([
                'status' => 'booked',
                'booking_id' => $booking->id,
                'user_id' => $user->id,
            ]);
        }

        // Transaction record creation based on payment method
        $txnData = [
            'booking_id' => $booking->id,
            'type' => '1',
            'booking_fee' => $booking_fee,
            'price' => $payment_amount,
            'coffee_from_wall' => $isCoffeeWall,
            'tax_amount' => $tax_amount,
            'tax_percentage' => $tax_percentage,
            'tax_type' => $tax_type,
            'deduct_type' => $deduct_type,
        ];

        if ($bookedByWallet) {
            $txnData['pay_by_account'] = true;
            TopUpBalance::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'cr_amount' => $payment_amount,
                'added_date' => now()->format('Y-m-d'),
            ]);
        } else {
            $txnData['stripe_id'] = $stripId;
        }

        $transaction = Transaction::create($txnData);
        $transcationId = $transaction->random_id;

        if ($isCoffeeWall) {
            $transaction = Transaction::create([
                ...$txnData,
                'price' => $booking_fee,
                'coffee_from_wall' => true,
            ]);
            $transcationId = $transaction->random_id;

            CoffeeWallet::create([
                'booking_id' => $booking->id,
                'ride_id' => $ride->id,
                'user_id' => $user->id,
                'cr_amount' => $booking_fee,
            ]);
        }

        app(BookingWebNotificationController::class)->dispatchCompleteBookingNotifications($booking->id, [
            'transaction_random_id' => $transcationId,
            'seats_amount' => $seats_amount,
            'payment_amount' => $payment_amount,
            'booked_by_wallet' => $bookedByWallet,
            'payment_method' => $payment_method,
            'invoice_form' => [
                'card_type' => $request->input('card_type', ''),
                'cardholder_name' => $request->input('cardholder_name', ''),
                'last_four_digits' => $request->input('last_four_digits', '****'),
                'expiration_date' => $request->input('expiration_date', ''),
                'paypal_email' => $request->input('paypal_email', $user->email ?? 'N/A'),
                'card_id' => $request->input('card_id'),
            ],
            'driver_message' => $driver_message,
            'selected_language_abbr' => $this->selectedLanguage?->abbreviation
                ?? getDefaultLanguage()?->abbreviation
                ?? 'en',
        ]);

        return [
            'booking' => $booking,
            'seats_number' => $seats_number,
            'transcationId' => $transcationId,
            'payment_amount' => $payment_amount,
        ];
    }

    /**
     * @see BookingWebNotificationController::dispatchBookingRequestApprovedNotifications()
     */
    protected function notifyBookingRequestApprovedWebFlow(Booking $booking, User $driver, bool $statusAlreadyBooked = false): void
    {
        app(BookingWebNotificationController::class)->dispatchBookingRequestApprovedNotifications($booking, $driver, $statusAlreadyBooked);
    }

    /**
     * @see BookingWebNotificationController::dispatchBookingRequestRejectedNotifications()
     *
     * @param  string  $channel  {@code web} or {@code api} — controls email/SMS parity in the queued passenger notifications.
     */
    protected function notifyBookingRequestRejectedWebFlow(Booking $booking, User $driver, string $channel = 'web'): void
    {
        app(BookingWebNotificationController::class)->dispatchBookingRequestRejectedNotifications($booking->id, $driver->id, $channel);
    }

    /**
     * @see BookingWebNotificationController::dispatchDriverRideCancelledPassengerNotifications()
     *
     * @param  list<int>  $bookingIds
     * @param  string  $channel  {@code web} or {@code api}
     */
    protected function dispatchDriverRideCancelledPassengerWebFlow(
        Ride $ride,
        User $driver,
        array $bookingIds,
        string $cancellationMessage,
        string $channel = 'web'
    ): void {
        app(BookingWebNotificationController::class)->dispatchDriverRideCancelledPassengerNotifications(
            $ride->id,
            $driver->id,
            $bookingIds,
            $cancellationMessage,
            $channel
        );
    }
    /**
     * Helper method to validate and apply student booking fee waiver
     * Checks both charge_booking field and student card expiration date
     * 
     * @param User $user The user making the booking
     * @param float|string $bookingCredit The booking credit amount from request
     * @return float|string The adjusted booking credit (0 if waived, original if not)
     */
    protected function validateStudentBookingFee($user, $bookingCredit)
    {
        if ($user->hasBookingFeeWaiverFlag()) {
            if ($user->isBookingFeeCurrentlyWaived()) {
                // If student is verified (student == '1') and card is expired, charge booking fee
                return $bookingCredit;
            }
            // Student with valid card - booking fee is waived
            return 0;
        }
        // Regular user or student with expired card - charge booking fee
        return $bookingCredit;
    }

    protected function normalizeSeatIds(Request $request, string $primaryKey = 'seats_id', string $fallbackKey = 'booked_seat_ids'): array
    {
        $seatIds = $request->input($primaryKey);

        if (is_null($seatIds) && $fallbackKey !== '') {
            $seatIds = $request->input($fallbackKey, []);
        }

        if (is_string($seatIds)) {
            $decodedSeatIds = json_decode($seatIds, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedSeatIds)) {
                $seatIds = $decodedSeatIds;
            } else {
                $seatIds = array_filter(
                    array_map('trim', explode(',', trim($seatIds, "[] \t\n\r\0\x0B"))),
                    static fn($value) => $value !== ''
                );
            }
        }

        if (!is_array($seatIds)) {
            return [];
        }

        return array_values(array_filter(array_map('intval', $seatIds), static fn($seatId) => $seatId > 0));
    }
}
