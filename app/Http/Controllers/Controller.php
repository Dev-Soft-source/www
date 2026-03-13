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

        // $notificationPage = ChatsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $this->successMessage = SuccessMessagesSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $siteText = SiteTextDetail::getByLanguageKeyedBySlug($this->selectedLanguage->id, $this->defaultLang->id);

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

        // $ratings = Rating::all();

        View::share([
            'selectedLanguage' => $this->selectedLanguage,
            'languages' => $languages,
            'siteText' => $siteText,
            'successMessage' => $this->successMessage,
            // 'ratings' => $ratings,
            // 'notificationPage' => $notificationPage,
        ]);
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

            $postRidePage = $this->mapVehicleTypeFields($postRidePage, $postRidePage);
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

            $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
            $findRidePage = $this->mapVehicleTypeFields($findRidePage, $postRidePage);
        }

        return $findRidePage;
    }

    protected function mapVehicleTypeFields($targetPage, $sourcePage)
    {
        if (!$targetPage || !$sourcePage) {
            return $targetPage;
        }

        $vehicleTypeFields = [
            'vehicle_type_convertible_text',
            'vehicle_type_hatchback_text',
            'vehicle_type_coupe_text',
            'vehicle_type_minivan_text',
            'vehicle_type_sedan_text',
            'vehicle_type_station_wagon_text',
            'vehicle_type_suv_text',
            'vehicle_type_truck_text',
            'vehicle_type_van_text',
        ];

        $vehicleTypeIds = array_filter(array_map(
            fn ($field) => $sourcePage->{$field} ?? null,
            $vehicleTypeFields
        ));

        $vehicleTypeDetails = FeaturesSettingDetail::whereIn('features_setting_id', $vehicleTypeIds)
            ->where('language_id', $this->selectedLanguage->id)
            ->get()
            ->keyBy('features_setting_id');

        foreach ($vehicleTypeFields as $field) {
            $valueField = str_replace('_text', '_value', $field);
            $featureId = $sourcePage->{$field} ?? null;

            // $targetPage->{$valueField} = $featureId;
            $targetPage->{$field} = $featureId
                ? $vehicleTypeDetails->get($featureId)?->name
                : null;
        }

        return $targetPage;
    }
}
