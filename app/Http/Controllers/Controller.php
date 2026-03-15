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
use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\SiteTextDetail;
use App\Models\VideoDetail;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $defaultLang;

    protected $selectedLanguage;

    protected $successMessage;

    public function __construct()
    {

        // todo : If admin, ...
        // 

        $this->defaultLang = getDefaultLanguage();

        // Initialize language-dependent data per request so POST/PUT routes
        // without a {lang} segment still inherit the active locale correctly.
        $this->middleware(function ($request, $next) {
            $lang = $request->route('lang');

            if ($lang) {
                session(['selectedLanguage' => $lang]);
            } else {
                $lang = $request->query('lang');

                if (!$lang) {
                    $lang = session('selectedLanguage');
                }

                if (!$lang && auth()->check() && auth()->user()->lang) {
                    $lang = auth()->user()->lang;
                }

                if (!$lang) {
                    $lang = $this->defaultLang->abbreviation;
                }

                session(['selectedLanguage' => $lang]);
            }

            $this->selectedLanguage = Language::resolveLanguage(session('selectedLanguage'));

            if (!$this->selectedLanguage) {
                $this->selectedLanguage = $this->defaultLang;
                session(['selectedLanguage' => $this->defaultLang->abbreviation]);
            }

            $languages = Language::all();
            $featureOptions = $this->getFeatureOptionsByLanguage();
            $this->successMessage = SuccessMessagesSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
            $siteText = SiteTextDetail::getByLanguageKeyedBySlug($this->selectedLanguage->id, $this->defaultLang->id);

            View::share([
                'selectedLanguage' => $this->selectedLanguage,
                'languages' => $languages,
                'featureOptions' => $featureOptions,
                'featureOptionsById' => $featureOptions->keyBy('id'),
                'featureOptionsByLabel' => $featureOptions->keyBy('label'),
                'siteText' => $siteText,
                'successMessage' => $this->successMessage,
                // 'ratings' => $ratings,
                // 'notificationPage' => $notificationPage,
            ]);

            if (auth()->check()) {
                $user = auth()->user();
                $user_id = $user->id;
                $lang = $this->selectedLanguage->abbreviation;

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
                
                // Only require vehicle/license/phone completion when posting a ride, not when viewing My Rides
                if ($routeName === 'post_ride') {
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
        if ($postRidePage) {
            // Optimized: Batch load all option groups in a single query instead of 7 separate queries
            $postRidePage->mapMultipleOptionColumnsToDetails(
                ['smoking', 'booking', 'payment_methods', 'animals', 'luggage', 'cancellation_policy'],
                $this->selectedLanguage->id,
                $this->defaultLang->id
            );

            $this->hydrateLegacyFeatureOptions($postRidePage);
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
        })->filter(fn ($type) => !empty($type['id']) && !empty($type['label']))->values();
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
        })->filter(fn ($feature) => !empty($feature['id']) && !empty($feature['label']))->values();
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
        return array_merge(range(1, 20), [47]);
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
}
