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

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $defaultLang;

    protected $selectedLanguage;

    public function __construct()
    {
        
        // todo : If admin, ...
        // 

        $this->defaultLang = getDefaultLanguage();

        $lang = request()->route('lang'); // get route parameter
        if ($lang) {
            session(['selectedLanguage' => $lang]);
        } else {
            $lang = session('selectedLanguage', $this->defaultLang->abbreviation);
            session(['selectedLanguage' => $lang]);
        }
        $this->selectedLanguage = Language::resolveLanguage(session('selectedLanguage'));

        $languages = Language::all();

        $notificationPage = ChatsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $successMessage = SuccessMessagesSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        
        // Share notifications with all views
        $this->middleware(function ($request, $next) use ($lang) {
            $notifications = null;
            if (auth()->check()) {
                $user = auth()->user();
                $user_id = $user->id;
                
                // Redirect users based on their profile completion step when they try to access certain routes
                $routeName = request()->route()->getName();
                if ($routeName === 'profile' || $routeName === 'welcomeRoute' || $routeName === 'my_chats' || $routeName === 'my_rides' || $routeName === 'ride_detail' || $routeName === 'post_ride') {
                    if ($user->step === '1') {
                        return redirect()->route('step1to5', ['lang' => $lang]);
                    } elseif ($user->step === '2') {
                        return redirect()->route('step2to5', ['lang' => $lang]);
                    } elseif ($user->step === '3') {
                        return redirect()->route('step3to5', ['lang' => $lang]);
                    } elseif ($user->step === '4') {
                        return redirect()->route('step4to5', ['lang' => $lang]);
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

                // ratings
                $ratings = Rating::where(function ($query) use ($user_id) {
                    // Ratings where type is 2 and user_id belongs to the user
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                    // OR Ratings where type is 1 and ride_id belongs to the user
                    $query->orWhere(function ($query) use ($user_id) {
                        $query->where('type', '1')
                            ->whereHas('ride', function ($query) use ($user_id) {
                                $query->where('added_by', $user_id);
                            });
                    });
                })
                    ->with(['from' => function ($query) {
                        $query->withTrashed(); // Include soft-deleted users
                    }])
                    ->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->get();
            }

            View::share('ratings', $ratings);

            return $next($request);
        });

        View::share([
            'selectedLanguage' => $this->selectedLanguage,
            'languages' => $languages,
            'notificationPage' => $notificationPage,
            'successMessage' => $successMessage,
        ]);
    }

    /**
     * Get post ride page with setting details for the selected language, with fallback to default language if not found.
     */
    public function getPostRidePageWithSettingDetail()
    {
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        if ($postRidePage) {
            $postRidePage->mapOptionColumnsToDetails('features', $this->selectedLanguage->id, $this->defaultLang->id);
            $postRidePage->mapOptionColumnsToDetails('smoking', $this->selectedLanguage->id, $this->defaultLang->id);
            $postRidePage->mapOptionColumnsToDetails('booking', $this->selectedLanguage->id, $this->defaultLang->id);
            $postRidePage->mapOptionColumnsToDetails('payment_methods', $this->selectedLanguage->id, $this->defaultLang->id);
            $postRidePage->mapOptionColumnsToDetails('animals', $this->selectedLanguage->id, $this->defaultLang->id);
            $postRidePage->mapOptionColumnsToDetails('luggage', $this->selectedLanguage->id, $this->defaultLang->id);
            $postRidePage->mapOptionColumnsToDetails('cancellation_policy', $this->selectedLanguage->id, $this->defaultLang->id);
        }
        
        return $postRidePage;
    }
    
    public function getFindRidePageWithSettingDetail()
    {
        $findRidePage = FindRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        if ($findRidePage) {
            $findRidePage->mapOptionColumnsToDetails('ride_features', $this->selectedLanguage->id, $this->defaultLang->id);
            $findRidePage->mapOptionColumnsToDetails('smoking', $this->selectedLanguage->id, $this->defaultLang->id);
            $findRidePage->mapOptionColumnsToDetails('pets_allowed', $this->selectedLanguage->id, $this->defaultLang->id);
            $findRidePage->mapOptionColumnsToDetails('payment_methods', $this->selectedLanguage->id, $this->defaultLang->id);
            $findRidePage->mapOptionColumnsToDetails('animals', $this->selectedLanguage->id, $this->defaultLang->id);
            $findRidePage->mapOptionColumnsToDetails('luggage', $this->selectedLanguage->id, $this->defaultLang->id);
            $findRidePage->mapOptionColumnsToDetails('cancellation_policy', $this->selectedLanguage->id, $this->defaultLang->id);

            // todo : there is not bellow fields in the table find_ride_page_setting_detail ???
            $findRidePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_convertible_text)->whereLanguageId($this->selectedLanguage->id)->value('name');
            $findRidePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_hatchback_text)->whereLanguageId($this->selectedLanguage->id)->value('name');
            $findRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_coupe_text)->whereLanguageId($this->selectedLanguage->id)->value('name');
            $findRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_minivan_text)->whereLanguageId($this->selectedLanguage->id)->value('name');
            $findRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_sedan_text)->whereLanguageId($this->selectedLanguage->id)->value('name');
            $findRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_station_wagon_text)->whereLanguageId($this->selectedLanguage->id)->value('name');
            $findRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_suv_text)->whereLanguageId($this->selectedLanguage->id)->value('name');
            $findRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_truck_text)->whereLanguageId($this->selectedLanguage->id)->value('name');
            $findRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_van_text)->whereLanguageId($this->selectedLanguage->id)->value('name');
        }

        return $findRidePage;
    }
}
