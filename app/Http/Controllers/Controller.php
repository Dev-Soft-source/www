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
            $this->successMessage = SuccessMessagesSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
            $siteText = SiteTextDetail::getByLanguageKeyedBySlug($this->selectedLanguage->id, $this->defaultLang->id);

            View::share([
                'selectedLanguage' => $this->selectedLanguage,
                'languages' => $languages,
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
        }

        return $findRidePage;
    }

    protected function getVehicleTypesByLanguage()
    {
        return $this->getVehicleTypesForLanguage($this->selectedLanguage->id, $this->defaultLang->id);
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

    protected function attachVehicleTypeOptions($page, ?int $languageId = null, ?int $fallbackLanguageId = null)
    {
        if (!$page) {
            return $page;
        }

        $vehicleTypes = $this->getVehicleTypesForLanguage($languageId, $fallbackLanguageId);
        $page->vehicle_types = $vehicleTypes->values();

        foreach ($vehicleTypes as $vehicleType) {
            $fieldBase = 'vehicle_type_' . $vehicleType['slug'];
            $page->{$fieldBase . '_value'} = $vehicleType['id'];
            $page->{$fieldBase . '_text'} = $vehicleType['label'];
        }

        return $page;
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
}
