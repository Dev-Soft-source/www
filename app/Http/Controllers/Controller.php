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

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $defaultLang;

    protected $selectedLanguage;

    protected $siteText;
    protected $selectedCurrency;
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
        $currencyFromQuery = strtoupper((string) request()->query('currency', ''));
        if ($currencyFromQuery !== '' && array_key_exists($currencyFromQuery, $this->availableCurrencies)) {
            session(['selectedCurrency' => $currencyFromQuery]);
        }
        $sessionCurrency = strtoupper((string) session('selectedCurrency', ''));
        if (!array_key_exists($sessionCurrency, $this->availableCurrencies)) {
            $preferredCurrency = strtoupper((string) env('PX_DEFAULT_CURRENCY', 'CAD'));
            $sessionCurrency = array_key_exists($preferredCurrency, $this->availableCurrencies)
                ? $preferredCurrency
                : (array_key_first($this->availableCurrencies) ?: 'CAD');
            session(['selectedCurrency' => $sessionCurrency]);
        }
        $this->selectedCurrency = $sessionCurrency;

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
            'selectedCurrency' => $this->selectedCurrency,
            'availableCurrencies' => $this->availableCurrencies,
            'selectedCurrencySymbol' => $this->availableCurrencies[$this->selectedCurrency]['symbol'] ?? '$',
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
