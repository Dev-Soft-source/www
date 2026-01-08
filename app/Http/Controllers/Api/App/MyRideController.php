<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\CancelPassengerAdminMail;
use App\Mail\CancelPassengerMail;
use App\Mail\DriverCancelRideMail;
use App\Mail\SecuredCashDriverMail;
use App\Mail\SecuredCashPassengerMail;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\CancellationHistory;
use App\Models\CancelRideSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\TripsPageSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Models\Rating;
use App\Models\ReviewSetting;
use App\Models\Ride;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\PhoneNumber;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\TopUpBalance;
use App\Models\FeaturesSetting;
use App\Models\MyPassengerSettingDetail;
use App\Models\SeatDetail;
use App\Models\CoffeeWallet;
use App\Models\FeaturesSettingDetail;
use App\Models\Message;
use App\Models\Notification;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;
use Twilio\Rest\Client;

class MyRideController extends Controller
{
    use StatusResponser;

    public function CurrentRides(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $rides = Ride::where('added_by', $user_id)
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '>', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '>=', now()->toTimeString());
                        });
                });
            })
            ->whereHas('driver', function ($query) {
                $query->whereNull('deleted_at'); // Exclude soft-deleted drivers
            })
            ->with(['rideDetail' => function($q){
                $q->where('default_ride','1');
            }])
            ->with(['vehicle','driver' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
            },
            'bookings' => function ($query) {
                $query->where('status', '<>', 0)
                      ->where('status', '<>', 3)
                      ->where('status', '<>', 4)
                      ->with(['passenger' => function ($query) {
                          $query->select('id', 'profile_image', 'gender'); // Specify the columns to select
                      }]);
            }])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($request->paginate_limit);

        $findRidePage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $findRidePage = FindRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            $postRidePage = PostRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            if ($postRidePage) {
                $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option4)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option5)
                    ->whereLanguageId($request->lang_id)
                    ->first();
            }
            if ($findRidePage) {
                $findRidePage->ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option16)
                    ->whereLanguageId($request->lang_id)
                    ->first();
            }
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }
                if ($findRidePage) {
                    $findRidePage->ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option16)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                }
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        $defaultLanguage = Language::where('is_default', 1)->first();
        $defaultPostRidePage = PostRidePageSettingDetail::where('language_id', $defaultLanguage->id)->first();

        $default_booking_option1 = FeaturesSetting::whereSlug('instant')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_booking_option2 = FeaturesSetting::whereSlug('manual')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the booking methods
        $bookingMethodImages = [
            optional($postRidePage->booking_option1)->features_setting_id ?? $default_booking_option1->features_setting_id => $postRidePage->booking_option1 ? asset('home_page_icons/' . $postRidePage->booking_option1->icon) : asset('home_page_icons/' . $default_booking_option1->icon),
            optional($postRidePage->booking_option2)->features_setting_id ?? $default_booking_option2->features_setting_id => $postRidePage->booking_option2 ? asset('home_page_icons/' . $postRidePage->booking_option2->icon) : asset('home_page_icons/' . $default_booking_option2->icon),
        ];
        $bookingMethodTooltips = [
            optional($postRidePage->booking_option1)->features_setting_id ?? $default_booking_option1->features_setting_id => $postRidePage->booking_option1 ? $postRidePage->booking_option1_tooltip : $defaultPostRidePage->booking_option1_tooltip,
            optional($postRidePage->booking_option2)->features_setting_id ?? $default_booking_option2->features_setting_id => $postRidePage->booking_option2 ? $postRidePage->booking_option2_tooltip : $defaultPostRidePage->booking_option2_tooltip,
        ];

        $default_payment_methods_option1 = FeaturesSetting::whereSlug('cash')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_payment_methods_option2 = FeaturesSetting::whereSlug('online')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_payment_methods_option3 = FeaturesSetting::whereSlug('secured')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the payment methods
        $paymentMethodImages = [
            $findRidePage->payment_methods_option2 ?? $default_payment_methods_option1->features_setting_id => $postRidePage->payment_methods_option1 ? asset('home_page_icons/' . $postRidePage->payment_methods_option1->icon) : asset('home_page_icons/' . $default_payment_methods_option1->icon),
            $findRidePage->payment_methods_option3 ?? $default_payment_methods_option2->features_setting_id => $postRidePage->payment_methods_option2 ? asset('home_page_icons/' . $postRidePage->payment_methods_option2->icon) : asset('home_page_icons/' . $default_payment_methods_option2->icon),
            $findRidePage->payment_methods_option4 ?? $default_payment_methods_option3->features_setting_id => $postRidePage->payment_methods_option3 ? asset('home_page_icons/' . $postRidePage->payment_methods_option3->icon) : asset('home_page_icons/' . $default_payment_methods_option3->icon),
        ];
        $paymentMethodTooltips = [
            $findRidePage->payment_methods_option2 ?? $default_payment_methods_option1->features_setting_id => $postRidePage->payment_methods_option1 ? $postRidePage->payment_methods_option1_tooltip : $defaultPostRidePage->payment_methods_option1_tooltip,
            $findRidePage->payment_methods_option3 ?? $default_payment_methods_option2->features_setting_id => $postRidePage->payment_methods_option2 ? $postRidePage->payment_methods_option2_tooltip : $defaultPostRidePage->payment_methods_option2_tooltip,
            $findRidePage->payment_methods_option4 ?? $default_payment_methods_option3->features_setting_id => $postRidePage->payment_methods_option3 ? $postRidePage->payment_methods_option3_tooltip : $defaultPostRidePage->payment_methods_option3_tooltip,
        ];

        $default_smoking_option1 = FeaturesSetting::whereSlug('no_smoking')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_smoking_option2 = FeaturesSetting::whereSlug('indifferent_smoking')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        // Define the image URLs for the smoke
        $smokeImages = [
            $findRidePage->smoking_option1 ?? $default_smoking_option1->features_setting_id => $postRidePage->smoking_option1 ? asset('home_page_icons/' . $postRidePage->smoking_option1->icon) : asset('home_page_icons/' . $default_smoking_option1->icon),
            $findRidePage->smoking_option2 ?? $default_smoking_option2->features_setting_id => $postRidePage->smoking_option2 ? asset('home_page_icons/' . $postRidePage->smoking_option2->icon) : asset('home_page_icons/' . $default_smoking_option2->icon),
        ];
        $smokeTooltips = [
            $findRidePage->smoking_option1 ?? $default_smoking_option1->features_setting_id => $postRidePage->smoking_option1 ? $postRidePage->smoking_option1_tooltip : $defaultPostRidePage->smoking_option1_tooltip,
            $findRidePage->smoking_option2 ?? $default_smoking_option2->features_setting_id => $postRidePage->smoking_option2 ? $postRidePage->smoking_option2_tooltip : $defaultPostRidePage->smoking_option2_tooltip,
        ];

        $default_animals_option1 = FeaturesSetting::whereSlug('no_animals')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_animals_option2 = FeaturesSetting::whereSlug('yes_animals')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_animals_option3 = FeaturesSetting::whereSlug('caged_animals')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the pets
        $petsImages = [
            $findRidePage->pets_allowed_option1 ?? $default_animals_option1->features_setting_id => $postRidePage->animals_option1 ? asset('home_page_icons/' . $postRidePage->animals_option1->icon) : asset('home_page_icons/' . $default_animals_option1->icon),
            $findRidePage->pets_allowed_option2 ?? $default_animals_option2->features_setting_id => $postRidePage->animals_option2 ? asset('home_page_icons/' . $postRidePage->animals_option2->icon) : asset('home_page_icons/' . $default_animals_option2->icon),
            $findRidePage->pets_allowed_option3 ?? $default_animals_option3->features_setting_id => $postRidePage->animals_option3 ? asset('home_page_icons/' . $postRidePage->animals_option3->icon) : asset('home_page_icons/' . $default_animals_option3->icon),
        ];
        $petsTooltips = [
            $findRidePage->pets_allowed_option1 ?? $default_animals_option1->features_setting_id => $postRidePage->animals_option1 ? $postRidePage->animals_option1_tooltip : $defaultPostRidePage->animals_option1_tooltip,
            $findRidePage->pets_allowed_option2 ?? $default_animals_option2->features_setting_id => $postRidePage->animals_option2 ? $postRidePage->animals_option2_tooltip : $defaultPostRidePage->animals_option2_tooltip,
            $findRidePage->pets_allowed_option3 ?? $default_animals_option3->features_setting_id => $postRidePage->animals_option3 ? $postRidePage->animals_option3_tooltip : $defaultPostRidePage->animals_option3_tooltip,
        ];

        $default_luggage_option1 = FeaturesSetting::whereSlug('no_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option2 = FeaturesSetting::whereSlug('small_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option3 = FeaturesSetting::whereSlug('medium_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option4 = FeaturesSetting::whereSlug('large_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option5 = FeaturesSetting::whereSlug('xl_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the luggage
        $luggageImages = [
            $findRidePage->luggage_option1 ?? $default_luggage_option1->features_setting_id => $postRidePage->luggage_option1 ? asset('home_page_icons/' . $postRidePage->luggage_option1->icon) : asset('home_page_icons/' . $default_luggage_option1->icon),
            $findRidePage->luggage_option2 ?? $default_luggage_option2->features_setting_id => $postRidePage->luggage_option2 ? asset('home_page_icons/' . $postRidePage->luggage_option2->icon) : asset('home_page_icons/' . $default_luggage_option2->icon),
            $findRidePage->luggage_option3 ?? $default_luggage_option3->features_setting_id => $postRidePage->luggage_option3 ? asset('home_page_icons/' . $postRidePage->luggage_option3->icon) : asset('home_page_icons/' . $default_luggage_option3->icon),
            $findRidePage->luggage_option4 ?? $default_luggage_option4->features_setting_id => $postRidePage->luggage_option4 ? asset('home_page_icons/' . $postRidePage->luggage_option4->icon) : asset('home_page_icons/' . $default_luggage_option4->icon),
            $findRidePage->luggage_option5 ?? $default_luggage_option5->features_setting_id => $postRidePage->luggage_option5 ? asset('home_page_icons/' . $postRidePage->luggage_option5->icon) : asset('home_page_icons/' . $default_luggage_option5->icon),
        ];
        $luggageTooltips = [
            $findRidePage->luggage_option1 ?? $default_luggage_option1->features_setting_id => $postRidePage->luggage_option1 ? $postRidePage->luggage_option1_tooltip : $defaultPostRidePage->luggage_option1_tooltip,
            $findRidePage->luggage_option2 ?? $default_luggage_option2->features_setting_id => $postRidePage->luggage_option2 ? $postRidePage->luggage_option2_tooltip : $defaultPostRidePage->luggage_option2_tooltip,
            $findRidePage->luggage_option3 ?? $default_luggage_option3->features_setting_id => $postRidePage->luggage_option3 ? $postRidePage->luggage_option3_tooltip : $defaultPostRidePage->luggage_option3_tooltip,
            $findRidePage->luggage_option4 ?? $default_luggage_option4->features_setting_id => $postRidePage->luggage_option4 ? $postRidePage->luggage_option4_tooltip : $defaultPostRidePage->luggage_option4_tooltip,
            $findRidePage->luggage_option5 ?? $default_luggage_option5->features_setting_id => $postRidePage->luggage_option5 ? $postRidePage->luggage_option5_tooltip : $defaultPostRidePage->luggage_option5_tooltip,
        ];

        foreach ($rides as $ride) {
            // Calculate seats left
            $bookedSeats = $ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->whereHas('passenger', function($query) {
                    $query->whereNull('deleted_at');
                })
                ->sum('seats');
            $ride->seats_left = intval($ride->seats) - intval($bookedSeats);

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

            $default_features_option1 = FeaturesSetting::whereSlug('pink_rides')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option2 = FeaturesSetting::whereSlug('extra_care_rides')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option3 = FeaturesSetting::whereSlug('wi_fi')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option8 = FeaturesSetting::whereSlug('heating')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option9 = FeaturesSetting::whereSlug('ac')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option10 = FeaturesSetting::whereSlug('bike_rack')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option11 = FeaturesSetting::whereSlug('ski_rack')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option12 = FeaturesSetting::whereSlug('winter_tires')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option13 = FeaturesSetting::whereSlug('star5_passenger')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option14 = FeaturesSetting::whereSlug('star4_passenger')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option15 = FeaturesSetting::whereSlug('star3_passenger')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option4 = FeaturesSetting::whereSlug('driver_features_option4')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option5 = FeaturesSetting::whereSlug('driver_features_option5')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option6 = FeaturesSetting::whereSlug('driver_features_option6')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option7 = FeaturesSetting::whereSlug('driver_features_option7')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();

            // Define the image URLs and titles for the features
            $featureImages = [
                optional($findRidePage->ride_features_option1)->features_setting_id ?? $default_features_option1->features_setting_id => ['title' => optional($findRidePage->ride_features_option1)->name ?? $default_features_option1->name, 'image' => $findRidePage->ride_features_option1 ? asset('home_page_icons/' . $findRidePage->ride_features_option1->icon) : asset('home_page_icons/' . $default_features_option1->icon), 'tooltip' => $postRidePage->features_option1_tooltip ?? $defaultPostRidePage->features_option1_tooltip],
                optional($findRidePage->ride_features_option2)->features_setting_id ?? $default_features_option2->features_setting_id => ['title' => optional($findRidePage->ride_features_option2)->name ?? $default_features_option2->name, 'image' => $findRidePage->ride_features_option2 ? asset('home_page_icons/' . $findRidePage->ride_features_option2->icon) : asset('home_page_icons/' . $default_features_option2->icon), 'tooltip' => $postRidePage->features_option2_tooltip ?? $defaultPostRidePage->features_option2_tooltip],
                optional($findRidePage->ride_features_option3)->features_setting_id ?? $default_features_option3->features_setting_id => ['title' => optional($findRidePage->ride_features_option3)->name ?? $default_features_option3->name, 'image' => $findRidePage->ride_features_option3 ? asset('home_page_icons/' . $findRidePage->ride_features_option3->icon) : asset('home_page_icons/' . $default_features_option3->icon), 'tooltip' => $postRidePage->features_option3_tooltip ?? $defaultPostRidePage->features_option3_tooltip],
                optional($findRidePage->ride_features_option8)->features_setting_id ?? $default_features_option8->features_setting_id => ['title' => optional($findRidePage->ride_features_option8)->name ?? $default_features_option8->name, 'image' => $findRidePage->ride_features_option8 ? asset('home_page_icons/' . $findRidePage->ride_features_option8->icon) : asset('home_page_icons/' . $default_features_option8->icon), 'tooltip' => $postRidePage->features_option8_tooltip ?? $defaultPostRidePage->features_option8_tooltip],
                optional($findRidePage->ride_features_option9)->features_setting_id ?? $default_features_option9->features_setting_id => ['title' => optional($findRidePage->ride_features_option9)->name ?? $default_features_option9->name, 'image' => $findRidePage->ride_features_option9 ? asset('home_page_icons/' . $findRidePage->ride_features_option9->icon) : asset('home_page_icons/' . $default_features_option9->icon), 'tooltip' => $postRidePage->features_option9_tooltip ?? $defaultPostRidePage->features_option9_tooltip],
                optional($findRidePage->ride_features_option10)->features_setting_id ?? $default_features_option10->features_setting_id => ['title' => optional($findRidePage->ride_features_option10)->name ?? $default_features_option10->name, 'image' => $findRidePage->ride_features_option10 ? asset('home_page_icons/' . $findRidePage->ride_features_option10->icon) : asset('home_page_icons/' . $default_features_option10->icon), 'tooltip' => $postRidePage->features_option10_tooltip ?? $defaultPostRidePage->features_option10_tooltip],
                optional($findRidePage->ride_features_option11)->features_setting_id ?? $default_features_option11->features_setting_id => ['title' => optional($findRidePage->ride_features_option11)->name ?? $default_features_option11->name, 'image' => $findRidePage->ride_features_option11 ? asset('home_page_icons/' . $findRidePage->ride_features_option11->icon) : asset('home_page_icons/' . $default_features_option11->icon), 'tooltip' => $postRidePage->features_option11_tooltip ?? $defaultPostRidePage->features_option11_tooltip],
                optional($findRidePage->ride_features_option12)->features_setting_id ?? $default_features_option12->features_setting_id => ['title' => optional($findRidePage->ride_features_option12)->name ?? $default_features_option12->name, 'image' => $findRidePage->ride_features_option12 ? asset('home_page_icons/' . $findRidePage->ride_features_option12->icon) : asset('home_page_icons/' . $default_features_option12->icon), 'tooltip' => $postRidePage->features_option12_tooltip ?? $defaultPostRidePage->features_option12_tooltip],
                optional($findRidePage->ride_features_option13)->features_setting_id ?? $default_features_option13->features_setting_id => ['title' => optional($findRidePage->ride_features_option13)->name ?? $default_features_option13->name, 'image' => $findRidePage->ride_features_option13 ? asset('home_page_icons/' . $findRidePage->ride_features_option13->icon) : asset('home_page_icons/' . $default_features_option13->icon), 'tooltip' => $postRidePage->features_option13_tooltip ?? $defaultPostRidePage->features_option13_tooltip],
                optional($findRidePage->ride_features_option14)->features_setting_id ?? $default_features_option14->features_setting_id => ['title' => optional($findRidePage->ride_features_option14)->name ?? $default_features_option14->name, 'image' => $findRidePage->ride_features_option14 ? asset('home_page_icons/' . $findRidePage->ride_features_option14->icon) : asset('home_page_icons/' . $default_features_option14->icon), 'tooltip' => $postRidePage->features_option14_tooltip ?? $defaultPostRidePage->features_option14_tooltip],
                optional($findRidePage->ride_features_option15)->features_setting_id ?? $default_features_option15->features_setting_id => ['title' => optional($findRidePage->ride_features_option15)->name ?? $default_features_option15->name, 'image' => $findRidePage->ride_features_option15 ? asset('home_page_icons/' . $findRidePage->ride_features_option15->icon) : asset('home_page_icons/' . $default_features_option15->icon), 'tooltip' => $postRidePage->features_option15_tooltip ?? $defaultPostRidePage->features_option15_tooltip],
                optional($postRidePage->features_option4)->features_setting_id ?? $default_features_option4->features_setting_id => ['title' => optional($postRidePage->features_option4)->name ?? $default_features_option4->name, 'image' => $postRidePage->ride_features_option4 ? asset('home_page_icons/' . $postRidePage->features_option4->icon) : asset('home_page_icons/' . $default_features_option4->icon), 'tooltip' => $postRidePage->features_option4_tooltip ?? $defaultPostRidePage->features_option4_tooltip],
                optional($postRidePage->features_option5)->features_setting_id ?? $default_features_option5->features_setting_id => ['title' => optional($postRidePage->features_option5)->name ?? $default_features_option5->name, 'image' => $postRidePage->ride_features_option5 ? asset('home_page_icons/' . $postRidePage->features_option5->icon) : asset('home_page_icons/' . $default_features_option5->icon), 'tooltip' => $postRidePage->features_option5_tooltip ?? $defaultPostRidePage->features_option5_tooltip],
                optional($postRidePage->features_option6)->features_setting_id ?? $default_features_option6->features_setting_id => ['title' => optional($postRidePage->features_option6)->name ?? $default_features_option6->name, 'image' => $postRidePage->ride_features_option6 ? asset('home_page_icons/' . $postRidePage->features_option6->icon) : asset('home_page_icons/' . $default_features_option6->icon), 'tooltip' => $postRidePage->features_option6_tooltip ?? $defaultPostRidePage->features_option6_tooltip],
                optional($postRidePage->features_option7)->features_setting_id ?? $default_features_option7->features_setting_id => ['title' => optional($postRidePage->features_option7)->name ?? $default_features_option7->name, 'image' => $postRidePage->ride_features_option7 ? asset('home_page_icons/' . $postRidePage->features_option7->icon) : asset('home_page_icons/' . $default_features_option7->icon), 'tooltip' => $postRidePage->features_option7_tooltip ?? $defaultPostRidePage->features_option7_tooltip],
            ];

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

            // Calculate age
            if ($ride->driver->dob) {
                $dob = Carbon::parse($ride->driver->dob);
                $ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $ride->driver->age = null; // Handle case where dob is not set
            }

            if ($ride->driver->gender) {
                if ($ride->driver->gender === 'male') {
                    $ride->driver->gender_label = $genderLabel->male_option_label;
                } elseif ($ride->driver->gender === 'female') {
                    $ride->driver->gender_label = $genderLabel->female_option_label;
                } elseif ($ride->driver->gender === 'prefer not to say') {
                    $ride->driver->gender_label = $genderLabel->prefer_option_label;
                }
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($user) {
                return $rating->ride->added_by === $user->id;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $ride->driver->average_rating = $totalAverage;

            $ride->driver->driven_rides = $ride->driver->rides()
                ->where('status', '!=', 2)
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

            foreach ($ride->bookings as $booking) {
                if ($booking->passenger->gender) {
                    if ($booking->passenger->gender === 'male') {
                        $booking->passenger->gender_label = $genderLabel->male_option_label;
                    } elseif ($booking->passenger->gender === 'female') {
                        $booking->passenger->gender_label = $genderLabel->female_option_label;
                    } elseif ($booking->passenger->gender === 'prefer not to say') {
                        $booking->passenger->gender_label = $genderLabel->prefer_option_label;
                    }
                }
            }
        }

        // Separate bookings based on status
        $rides->getCollection()->transform(function ($ride) {
            $ride->booking_requests = $ride->bookings()->where('status', 0)
                ->with(['passenger' => function ($query) {
                    $query->select('id', 'profile_image', 'gender'); // Specify the columns to select
                }])->get();
            return $ride;
        });


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


        $rideDetailPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['rides' => $rides, 'rideDetailPage' => $rideDetailPage, 'tripsPage' => $tripsPage];
        return $this->successResponse($data, 'Get my upcoming rides');
    }

    public function PastRides(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $rides = Ride::where('added_by', $user_id)
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<', now()->toTimeString());
                        });
                });
            })
            ->whereHas('driver', function ($query) {
                $query->whereNull('deleted_at'); // Exclude soft-deleted drivers
            })
            ->with(['rideDetail' => function($q){
                $q->where('default_ride','1');
            }])
            ->with(['vehicle','driver' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
            },
            'bookings' => function ($query) {
                $query->where('status', '<>', 0)
                      ->where('status', '<>', 3)
                      ->where('status', '<>', 4)
                      ->with(['passenger' => function ($query) {
                          $query->select('id', 'first_name', 'last_name', 'profile_image', 'gender'); // Specify the columns to select
                      }]);
            }])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($request->paginate_limit);

        $findRidePage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $findRidePage = FindRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            $postRidePage = PostRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            if ($postRidePage) {
                $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option4)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option5)
                    ->whereLanguageId($request->lang_id)
                    ->first();
            }
            if ($findRidePage) {
                $findRidePage->ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option16)
                    ->whereLanguageId($request->lang_id)
                    ->first();
            }
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }
                if ($findRidePage) {
                    $findRidePage->ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option16)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                }
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        $defaultLanguage = Language::where('is_default', 1)->first();
        $defaultPostRidePage = PostRidePageSettingDetail::where('language_id', $defaultLanguage->id)->first();

        $default_booking_option1 = FeaturesSetting::whereSlug('instant')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_booking_option2 = FeaturesSetting::whereSlug('manual')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the booking methods
        $bookingMethodImages = [
            optional($postRidePage->booking_option1)->features_setting_id ?? $default_booking_option1->features_setting_id => $postRidePage->booking_option1 ? asset('home_page_icons/' . $postRidePage->booking_option1->icon) : asset('home_page_icons/' . $default_booking_option1->icon),
            optional($postRidePage->booking_option2)->features_setting_id ?? $default_booking_option2->features_setting_id => $postRidePage->booking_option2 ? asset('home_page_icons/' . $postRidePage->booking_option2->icon) : asset('home_page_icons/' . $default_booking_option2->icon),
        ];
        $bookingMethodTooltips = [
            optional($postRidePage->booking_option1)->features_setting_id ?? $default_booking_option1->features_setting_id => $postRidePage->booking_option1 ? $postRidePage->booking_option1_tooltip : $defaultPostRidePage->booking_option1_tooltip,
            optional($postRidePage->booking_option2)->features_setting_id ?? $default_booking_option2->features_setting_id => $postRidePage->booking_option2 ? $postRidePage->booking_option2_tooltip : $defaultPostRidePage->booking_option2_tooltip,
        ];

        $default_payment_methods_option1 = FeaturesSetting::whereSlug('cash')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_payment_methods_option2 = FeaturesSetting::whereSlug('online')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_payment_methods_option3 = FeaturesSetting::whereSlug('secured')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the payment methods
        $paymentMethodImages = [
            $findRidePage->payment_methods_option2 ?? $default_payment_methods_option1->features_setting_id => $postRidePage->payment_methods_option1 ? asset('home_page_icons/' . $postRidePage->payment_methods_option1->icon) : asset('home_page_icons/' . $default_payment_methods_option1->icon),
            $findRidePage->payment_methods_option3 ?? $default_payment_methods_option2->features_setting_id => $postRidePage->payment_methods_option2 ? asset('home_page_icons/' . $postRidePage->payment_methods_option2->icon) : asset('home_page_icons/' . $default_payment_methods_option2->icon),
            $findRidePage->payment_methods_option4 ?? $default_payment_methods_option3->features_setting_id => $postRidePage->payment_methods_option3 ? asset('home_page_icons/' . $postRidePage->payment_methods_option3->icon) : asset('home_page_icons/' . $default_payment_methods_option3->icon),
        ];
        $paymentMethodTooltips = [
            $findRidePage->payment_methods_option2 ?? $default_payment_methods_option1->features_setting_id => $postRidePage->payment_methods_option1 ? $postRidePage->payment_methods_option1_tooltip : $defaultPostRidePage->payment_methods_option1_tooltip,
            $findRidePage->payment_methods_option3 ?? $default_payment_methods_option2->features_setting_id => $postRidePage->payment_methods_option2 ? $postRidePage->payment_methods_option2_tooltip : $defaultPostRidePage->payment_methods_option2_tooltip,
            $findRidePage->payment_methods_option4 ?? $default_payment_methods_option3->features_setting_id => $postRidePage->payment_methods_option3 ? $postRidePage->payment_methods_option3_tooltip : $defaultPostRidePage->payment_methods_option3_tooltip,
        ];

        $default_smoking_option1 = FeaturesSetting::whereSlug('no_smoking')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_smoking_option2 = FeaturesSetting::whereSlug('indifferent_smoking')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        // Define the image URLs for the smoke
        $smokeImages = [
            $findRidePage->smoking_option1 ?? $default_smoking_option1->features_setting_id => $postRidePage->smoking_option1 ? asset('home_page_icons/' . $postRidePage->smoking_option1->icon) : asset('home_page_icons/' . $default_smoking_option1->icon),
            $findRidePage->smoking_option2 ?? $default_smoking_option2->features_setting_id => $postRidePage->smoking_option2 ? asset('home_page_icons/' . $postRidePage->smoking_option2->icon) : asset('home_page_icons/' . $default_smoking_option2->icon),
        ];
        $smokeTooltips = [
            $findRidePage->smoking_option1 ?? $default_smoking_option1->features_setting_id => $postRidePage->smoking_option1 ? $postRidePage->smoking_option1_tooltip : $defaultPostRidePage->smoking_option1_tooltip,
            $findRidePage->smoking_option2 ?? $default_smoking_option2->features_setting_id => $postRidePage->smoking_option2 ? $postRidePage->smoking_option2_tooltip : $defaultPostRidePage->smoking_option2_tooltip,
        ];

        $default_animals_option1 = FeaturesSetting::whereSlug('no_animals')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_animals_option2 = FeaturesSetting::whereSlug('yes_animals')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_animals_option3 = FeaturesSetting::whereSlug('caged_animals')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the pets
        $petsImages = [
            $findRidePage->pets_allowed_option1 ?? $default_animals_option1->features_setting_id => $postRidePage->animals_option1 ? asset('home_page_icons/' . $postRidePage->animals_option1->icon) : asset('home_page_icons/' . $default_animals_option1->icon),
            $findRidePage->pets_allowed_option2 ?? $default_animals_option2->features_setting_id => $postRidePage->animals_option2 ? asset('home_page_icons/' . $postRidePage->animals_option2->icon) : asset('home_page_icons/' . $default_animals_option2->icon),
            $findRidePage->pets_allowed_option3 ?? $default_animals_option3->features_setting_id => $postRidePage->animals_option3 ? asset('home_page_icons/' . $postRidePage->animals_option3->icon) : asset('home_page_icons/' . $default_animals_option3->icon),
        ];
        $petsTooltips = [
            $findRidePage->pets_allowed_option1 ?? $default_animals_option1->features_setting_id => $postRidePage->animals_option1 ? $postRidePage->animals_option1_tooltip : $defaultPostRidePage->animals_option1_tooltip,
            $findRidePage->pets_allowed_option2 ?? $default_animals_option2->features_setting_id => $postRidePage->animals_option2 ? $postRidePage->animals_option2_tooltip : $defaultPostRidePage->animals_option2_tooltip,
            $findRidePage->pets_allowed_option3 ?? $default_animals_option3->features_setting_id => $postRidePage->animals_option3 ? $postRidePage->animals_option3_tooltip : $defaultPostRidePage->animals_option3_tooltip,
        ];

        $default_luggage_option1 = FeaturesSetting::whereSlug('no_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option2 = FeaturesSetting::whereSlug('small_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option3 = FeaturesSetting::whereSlug('medium_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option4 = FeaturesSetting::whereSlug('large_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option5 = FeaturesSetting::whereSlug('xl_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the luggage
        $luggageImages = [
            $findRidePage->luggage_option1 ?? $default_luggage_option1->features_setting_id => $postRidePage->luggage_option1 ? asset('home_page_icons/' . $postRidePage->luggage_option1->icon) : asset('home_page_icons/' . $default_luggage_option1->icon),
            $findRidePage->luggage_option2 ?? $default_luggage_option2->features_setting_id => $postRidePage->luggage_option2 ? asset('home_page_icons/' . $postRidePage->luggage_option2->icon) : asset('home_page_icons/' . $default_luggage_option2->icon),
            $findRidePage->luggage_option3 ?? $default_luggage_option3->features_setting_id => $postRidePage->luggage_option3 ? asset('home_page_icons/' . $postRidePage->luggage_option3->icon) : asset('home_page_icons/' . $default_luggage_option3->icon),
            $findRidePage->luggage_option4 ?? $default_luggage_option4->features_setting_id => $postRidePage->luggage_option4 ? asset('home_page_icons/' . $postRidePage->luggage_option4->icon) : asset('home_page_icons/' . $default_luggage_option4->icon),
            $findRidePage->luggage_option5 ?? $default_luggage_option5->features_setting_id => $postRidePage->luggage_option5 ? asset('home_page_icons/' . $postRidePage->luggage_option5->icon) : asset('home_page_icons/' . $default_luggage_option5->icon),
        ];
        $luggageTooltips = [
            $findRidePage->luggage_option1 ?? $default_luggage_option1->features_setting_id => $postRidePage->luggage_option1 ? $postRidePage->luggage_option1_tooltip : $defaultPostRidePage->luggage_option1_tooltip,
            $findRidePage->luggage_option2 ?? $default_luggage_option2->features_setting_id => $postRidePage->luggage_option2 ? $postRidePage->luggage_option2_tooltip : $defaultPostRidePage->luggage_option2_tooltip,
            $findRidePage->luggage_option3 ?? $default_luggage_option3->features_setting_id => $postRidePage->luggage_option3 ? $postRidePage->luggage_option3_tooltip : $defaultPostRidePage->luggage_option3_tooltip,
            $findRidePage->luggage_option4 ?? $default_luggage_option4->features_setting_id => $postRidePage->luggage_option4 ? $postRidePage->luggage_option4_tooltip : $defaultPostRidePage->luggage_option4_tooltip,
            $findRidePage->luggage_option5 ?? $default_luggage_option5->features_setting_id => $postRidePage->luggage_option5 ? $postRidePage->luggage_option5_tooltip : $defaultPostRidePage->luggage_option5_tooltip,
        ];

        foreach ($rides as $ride) {
            // Calculate seats left
            $bookedSeats = $ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->whereHas('passenger', function($query) {
                    $query->whereNull('deleted_at');
                })
                ->sum('seats');
            $ride->seats_left = intval($ride->seats) - intval($bookedSeats);

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

            $default_features_option1 = FeaturesSetting::whereSlug('pink_rides')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option2 = FeaturesSetting::whereSlug('extra_care_rides')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option3 = FeaturesSetting::whereSlug('wi_fi')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option8 = FeaturesSetting::whereSlug('heating')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option9 = FeaturesSetting::whereSlug('ac')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option10 = FeaturesSetting::whereSlug('bike_rack')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option11 = FeaturesSetting::whereSlug('ski_rack')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option12 = FeaturesSetting::whereSlug('winter_tires')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option13 = FeaturesSetting::whereSlug('star5_passenger')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option14 = FeaturesSetting::whereSlug('star4_passenger')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option15 = FeaturesSetting::whereSlug('star3_passenger')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option4 = FeaturesSetting::whereSlug('driver_features_option4')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option5 = FeaturesSetting::whereSlug('driver_features_option5')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option6 = FeaturesSetting::whereSlug('driver_features_option6')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option7 = FeaturesSetting::whereSlug('driver_features_option7')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();

            // Define the image URLs and titles for the features
            $featureImages = [
                optional($findRidePage->ride_features_option1)->features_setting_id ?? $default_features_option1->features_setting_id => ['title' => optional($findRidePage->ride_features_option1)->name ?? $default_features_option1->name, 'image' => $findRidePage->ride_features_option1 ? asset('home_page_icons/' . $findRidePage->ride_features_option1->icon) : asset('home_page_icons/' . $default_features_option1->icon), 'tooltip' => $postRidePage->features_option1_tooltip ?? $defaultPostRidePage->features_option1_tooltip],
                optional($findRidePage->ride_features_option2)->features_setting_id ?? $default_features_option2->features_setting_id => ['title' => optional($findRidePage->ride_features_option2)->name ?? $default_features_option2->name, 'image' => $findRidePage->ride_features_option2 ? asset('home_page_icons/' . $findRidePage->ride_features_option2->icon) : asset('home_page_icons/' . $default_features_option2->icon), 'tooltip' => $postRidePage->features_option2_tooltip ?? $defaultPostRidePage->features_option2_tooltip],
                optional($findRidePage->ride_features_option3)->features_setting_id ?? $default_features_option3->features_setting_id => ['title' => optional($findRidePage->ride_features_option3)->name ?? $default_features_option3->name, 'image' => $findRidePage->ride_features_option3 ? asset('home_page_icons/' . $findRidePage->ride_features_option3->icon) : asset('home_page_icons/' . $default_features_option3->icon), 'tooltip' => $postRidePage->features_option3_tooltip ?? $defaultPostRidePage->features_option3_tooltip],
                optional($findRidePage->ride_features_option8)->features_setting_id ?? $default_features_option8->features_setting_id => ['title' => optional($findRidePage->ride_features_option8)->name ?? $default_features_option8->name, 'image' => $findRidePage->ride_features_option8 ? asset('home_page_icons/' . $findRidePage->ride_features_option8->icon) : asset('home_page_icons/' . $default_features_option8->icon), 'tooltip' => $postRidePage->features_option8_tooltip ?? $defaultPostRidePage->features_option8_tooltip],
                optional($findRidePage->ride_features_option9)->features_setting_id ?? $default_features_option9->features_setting_id => ['title' => optional($findRidePage->ride_features_option9)->name ?? $default_features_option9->name, 'image' => $findRidePage->ride_features_option9 ? asset('home_page_icons/' . $findRidePage->ride_features_option9->icon) : asset('home_page_icons/' . $default_features_option9->icon), 'tooltip' => $postRidePage->features_option9_tooltip ?? $defaultPostRidePage->features_option9_tooltip],
                optional($findRidePage->ride_features_option10)->features_setting_id ?? $default_features_option10->features_setting_id => ['title' => optional($findRidePage->ride_features_option10)->name ?? $default_features_option10->name, 'image' => $findRidePage->ride_features_option10 ? asset('home_page_icons/' . $findRidePage->ride_features_option10->icon) : asset('home_page_icons/' . $default_features_option10->icon), 'tooltip' => $postRidePage->features_option10_tooltip ?? $defaultPostRidePage->features_option10_tooltip],
                optional($findRidePage->ride_features_option11)->features_setting_id ?? $default_features_option11->features_setting_id => ['title' => optional($findRidePage->ride_features_option11)->name ?? $default_features_option11->name, 'image' => $findRidePage->ride_features_option11 ? asset('home_page_icons/' . $findRidePage->ride_features_option11->icon) : asset('home_page_icons/' . $default_features_option11->icon), 'tooltip' => $postRidePage->features_option11_tooltip ?? $defaultPostRidePage->features_option11_tooltip],
                optional($findRidePage->ride_features_option12)->features_setting_id ?? $default_features_option12->features_setting_id => ['title' => optional($findRidePage->ride_features_option12)->name ?? $default_features_option12->name, 'image' => $findRidePage->ride_features_option12 ? asset('home_page_icons/' . $findRidePage->ride_features_option12->icon) : asset('home_page_icons/' . $default_features_option12->icon), 'tooltip' => $postRidePage->features_option12_tooltip ?? $defaultPostRidePage->features_option12_tooltip],
                optional($findRidePage->ride_features_option13)->features_setting_id ?? $default_features_option13->features_setting_id => ['title' => optional($findRidePage->ride_features_option13)->name ?? $default_features_option13->name, 'image' => $findRidePage->ride_features_option13 ? asset('home_page_icons/' . $findRidePage->ride_features_option13->icon) : asset('home_page_icons/' . $default_features_option13->icon), 'tooltip' => $postRidePage->features_option13_tooltip ?? $defaultPostRidePage->features_option13_tooltip],
                optional($findRidePage->ride_features_option14)->features_setting_id ?? $default_features_option14->features_setting_id => ['title' => optional($findRidePage->ride_features_option14)->name ?? $default_features_option14->name, 'image' => $findRidePage->ride_features_option14 ? asset('home_page_icons/' . $findRidePage->ride_features_option14->icon) : asset('home_page_icons/' . $default_features_option14->icon), 'tooltip' => $postRidePage->features_option14_tooltip ?? $defaultPostRidePage->features_option14_tooltip],
                optional($findRidePage->ride_features_option15)->features_setting_id ?? $default_features_option15->features_setting_id => ['title' => optional($findRidePage->ride_features_option15)->name ?? $default_features_option15->name, 'image' => $findRidePage->ride_features_option15 ? asset('home_page_icons/' . $findRidePage->ride_features_option15->icon) : asset('home_page_icons/' . $default_features_option15->icon), 'tooltip' => $postRidePage->features_option15_tooltip ?? $defaultPostRidePage->features_option15_tooltip],
                optional($postRidePage->features_option4)->features_setting_id ?? $default_features_option4->features_setting_id => ['title' => optional($postRidePage->features_option4)->name ?? $default_features_option4->name, 'image' => $postRidePage->ride_features_option4 ? asset('home_page_icons/' . $postRidePage->features_option4->icon) : asset('home_page_icons/' . $default_features_option4->icon), 'tooltip' => $postRidePage->features_option4_tooltip ?? $defaultPostRidePage->features_option4_tooltip],
                optional($postRidePage->features_option5)->features_setting_id ?? $default_features_option5->features_setting_id => ['title' => optional($postRidePage->features_option5)->name ?? $default_features_option5->name, 'image' => $postRidePage->ride_features_option5 ? asset('home_page_icons/' . $postRidePage->features_option5->icon) : asset('home_page_icons/' . $default_features_option5->icon), 'tooltip' => $postRidePage->features_option5_tooltip ?? $defaultPostRidePage->features_option5_tooltip],
                optional($postRidePage->features_option6)->features_setting_id ?? $default_features_option6->features_setting_id => ['title' => optional($postRidePage->features_option6)->name ?? $default_features_option6->name, 'image' => $postRidePage->ride_features_option6 ? asset('home_page_icons/' . $postRidePage->features_option6->icon) : asset('home_page_icons/' . $default_features_option6->icon), 'tooltip' => $postRidePage->features_option6_tooltip ?? $defaultPostRidePage->features_option6_tooltip],
                optional($postRidePage->features_option7)->features_setting_id ?? $default_features_option7->features_setting_id => ['title' => optional($postRidePage->features_option7)->name ?? $default_features_option7->name, 'image' => $postRidePage->ride_features_option7 ? asset('home_page_icons/' . $postRidePage->features_option7->icon) : asset('home_page_icons/' . $default_features_option7->icon), 'tooltip' => $postRidePage->features_option7_tooltip ?? $defaultPostRidePage->features_option7_tooltip],
            ];

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

            // Calculate age
            if ($ride->driver->dob) {
                $dob = Carbon::parse($ride->driver->dob);
                $ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $ride->driver->age = null; // Handle case where dob is not set
            }

            if ($ride->driver->gender) {
                if ($ride->driver->gender === 'male') {
                    $ride->driver->gender_label = $genderLabel->male_option_label;
                } elseif ($ride->driver->gender === 'female') {
                    $ride->driver->gender_label = $genderLabel->female_option_label;
                } elseif ($ride->driver->gender === 'prefer not to say') {
                    $ride->driver->gender_label = $genderLabel->prefer_option_label;
                }
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($ride) {
                return $rating->ride->added_by === $ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $ride->driver->average_rating = $totalAverage;

            $ride->driver->driven_rides = $ride->driver->rides()
                ->where('status', '!=', 2)
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

            foreach ($ride->bookings as $booking) {
                if ($booking->passenger->gender) {
                    if ($booking->passenger->gender === 'male') {
                        $booking->passenger->gender_label = $genderLabel->male_option_label;
                    } elseif ($booking->passenger->gender === 'female') {
                        $booking->passenger->gender_label = $genderLabel->female_option_label;
                    } elseif ($booking->passenger->gender === 'prefer not to say') {
                        $booking->passenger->gender_label = $genderLabel->prefer_option_label;
                    }
                }

                $booking->rating = Rating::where('type', '2')->where('ride_id', $booking->ride_id)->where('posted_to', $booking->id)->first();

                $ratings = Rating::where('status', 1)->where('type', '2')->get();
                // Calculate average rating
                $filteredRatings = $ratings->filter(function ($rating) use ($booking) {
                    return $rating->booking->user_id === $booking->user_id;
                });

                $totalAverage = $filteredRatings->avg('average_rating');
                $booking->passenger_average_rating = $totalAverage;
            }
        }

        // Separate bookings based on status
        $rides->getCollection()->transform(function ($ride) {
            $ride->booking_requests = $ride->bookings()->where('status', 0)
                ->with(['passenger' => function ($query) {
                    $query->select('id', 'profile_image', 'gender'); // Specify the columns to select
                }])->get();
            return $ride;
        });
        $setting = ReviewSetting::first();

        $data = ['rides' => $rides,'setting' => $setting];
        return $this->successResponse($data, 'Get my completed rides');
    }

    public function CancelledRides(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $rides = Ride::where('added_by', $user_id)
            ->where('status', 2)
            ->where(function ($query) {
                $query->whereHas('driver', function ($query) {
                    $query->whereNull('deleted_at'); // Exclude soft-deleted drivers
                });
            })
            ->with(['vehicle','driver' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
            },
            'bookings' => function ($query) {
                $query->where('status', '<>', 3)
                      ->where('status', '<>', 4)
                      ->with(['passenger' => function ($query) {
                          $query->select('id', 'profile_image', 'gender'); // Specify the columns to select
                      }]);
            }])
            ->with(['rideDetail' => function($q){
                $q->where('default_ride','1');
            }])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($request->paginate_limit);

        $findRidePage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $findRidePage = FindRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            $postRidePage = PostRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            if ($postRidePage) {
                $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option4)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $postRidePage->luggage_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option5)
                    ->whereLanguageId($request->lang_id)
                    ->first();
            }
            if ($findRidePage) {
                $findRidePage->ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)
                    ->whereLanguageId($request->lang_id)
                    ->first();
                $findRidePage->ride_features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option16)
                    ->whereLanguageId($request->lang_id)
                    ->first();
            }
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->luggage_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }
                if ($findRidePage) {
                    $findRidePage->ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                    $findRidePage->ride_features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option16)
                        ->whereLanguageId($request->lang_id)
                        ->first();
                }
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        $defaultLanguage = Language::where('is_default', 1)->first();
        $defaultPostRidePage = PostRidePageSettingDetail::where('language_id', $defaultLanguage->id)->first();

        $default_booking_option1 = FeaturesSetting::whereSlug('instant')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_booking_option2 = FeaturesSetting::whereSlug('manual')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the booking methods
        $bookingMethodImages = [
            optional($postRidePage->booking_option1)->features_setting_id ?? $default_booking_option1->features_setting_id => $postRidePage->booking_option1 ? asset('home_page_icons/' . $postRidePage->booking_option1->icon) : asset('home_page_icons/' . $default_booking_option1->icon),
            optional($postRidePage->booking_option2)->features_setting_id ?? $default_booking_option2->features_setting_id => $postRidePage->booking_option2 ? asset('home_page_icons/' . $postRidePage->booking_option2->icon) : asset('home_page_icons/' . $default_booking_option2->icon),
        ];
        $bookingMethodTooltips = [
            optional($postRidePage->booking_option1)->features_setting_id ?? $default_booking_option1->features_setting_id => $postRidePage->booking_option1 ? $postRidePage->booking_option1_tooltip : $defaultPostRidePage->booking_option1_tooltip,
            optional($postRidePage->booking_option2)->features_setting_id ?? $default_booking_option2->features_setting_id => $postRidePage->booking_option2 ? $postRidePage->booking_option2_tooltip : $defaultPostRidePage->booking_option2_tooltip,
        ];

        $default_payment_methods_option1 = FeaturesSetting::whereSlug('cash')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_payment_methods_option2 = FeaturesSetting::whereSlug('online')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_payment_methods_option3 = FeaturesSetting::whereSlug('secured')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the payment methods
        $paymentMethodImages = [
            $findRidePage->payment_methods_option2 ?? $default_payment_methods_option1->features_setting_id => $postRidePage->payment_methods_option1 ? asset('home_page_icons/' . $postRidePage->payment_methods_option1->icon) : asset('home_page_icons/' . $default_payment_methods_option1->icon),
            $findRidePage->payment_methods_option3 ?? $default_payment_methods_option2->features_setting_id => $postRidePage->payment_methods_option2 ? asset('home_page_icons/' . $postRidePage->payment_methods_option2->icon) : asset('home_page_icons/' . $default_payment_methods_option2->icon),
            $findRidePage->payment_methods_option4 ?? $default_payment_methods_option3->features_setting_id => $postRidePage->payment_methods_option3 ? asset('home_page_icons/' . $postRidePage->payment_methods_option3->icon) : asset('home_page_icons/' . $default_payment_methods_option3->icon),
        ];
        $paymentMethodTooltips = [
            $findRidePage->payment_methods_option2 ?? $default_payment_methods_option1->features_setting_id => $postRidePage->payment_methods_option1 ? $postRidePage->payment_methods_option1_tooltip : $defaultPostRidePage->payment_methods_option1_tooltip,
            $findRidePage->payment_methods_option3 ?? $default_payment_methods_option2->features_setting_id => $postRidePage->payment_methods_option2 ? $postRidePage->payment_methods_option2_tooltip : $defaultPostRidePage->payment_methods_option2_tooltip,
            $findRidePage->payment_methods_option4 ?? $default_payment_methods_option3->features_setting_id => $postRidePage->payment_methods_option3 ? $postRidePage->payment_methods_option3_tooltip : $defaultPostRidePage->payment_methods_option3_tooltip,
        ];

        $default_smoking_option1 = FeaturesSetting::whereSlug('no_smoking')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        $default_smoking_option2 = FeaturesSetting::whereSlug('indifferent_smoking')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
        // Define the image URLs for the smoke
        $smokeImages = [
            $findRidePage->smoking_option1 ?? $default_smoking_option1->features_setting_id => $postRidePage->smoking_option1 ? asset('home_page_icons/' . $postRidePage->smoking_option1->icon) : asset('home_page_icons/' . $default_smoking_option1->icon),
            $findRidePage->smoking_option2 ?? $default_smoking_option2->features_setting_id => $postRidePage->smoking_option2 ? asset('home_page_icons/' . $postRidePage->smoking_option2->icon) : asset('home_page_icons/' . $default_smoking_option2->icon),
        ];
        $smokeTooltips = [
            $findRidePage->smoking_option1 ?? $default_smoking_option1->features_setting_id => $postRidePage->smoking_option1 ? $postRidePage->smoking_option1_tooltip : $defaultPostRidePage->smoking_option1_tooltip,
            $findRidePage->smoking_option2 ?? $default_smoking_option2->features_setting_id => $postRidePage->smoking_option2 ? $postRidePage->smoking_option2_tooltip : $defaultPostRidePage->smoking_option2_tooltip,
        ];

        // Define the image URLs for the pets
        $default_animals_option1 = FeaturesSetting::whereSlug('no_animals')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_animals_option2 = FeaturesSetting::whereSlug('yes_animals')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_animals_option3 = FeaturesSetting::whereSlug('caged_animals')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the pets
        $petsImages = [
            $findRidePage->pets_allowed_option1 ?? $default_animals_option1->features_setting_id => $postRidePage->animals_option1 ? asset('home_page_icons/' . $postRidePage->animals_option1->icon) : asset('home_page_icons/' . $default_animals_option1->icon),
            $findRidePage->pets_allowed_option2 ?? $default_animals_option2->features_setting_id => $postRidePage->animals_option2 ? asset('home_page_icons/' . $postRidePage->animals_option2->icon) : asset('home_page_icons/' . $default_animals_option2->icon),
            $findRidePage->pets_allowed_option3 ?? $default_animals_option3->features_setting_id => $postRidePage->animals_option3 ? asset('home_page_icons/' . $postRidePage->animals_option3->icon) : asset('home_page_icons/' . $default_animals_option3->icon),
        ];
        $petsTooltips = [
            $findRidePage->pets_allowed_option1 ?? $default_animals_option1->features_setting_id => $postRidePage->animals_option1 ? $postRidePage->animals_option1_tooltip : $defaultPostRidePage->animals_option1_tooltip,
            $findRidePage->pets_allowed_option2 ?? $default_animals_option2->features_setting_id => $postRidePage->animals_option2 ? $postRidePage->animals_option2_tooltip : $defaultPostRidePage->animals_option2_tooltip,
            $findRidePage->pets_allowed_option3 ?? $default_animals_option3->features_setting_id => $postRidePage->animals_option3 ? $postRidePage->animals_option3_tooltip : $defaultPostRidePage->animals_option3_tooltip,
        ];

        $default_luggage_option1 = FeaturesSetting::whereSlug('no_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option2 = FeaturesSetting::whereSlug('small_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option3 = FeaturesSetting::whereSlug('medium_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option4 = FeaturesSetting::whereSlug('large_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();
        $default_luggage_option5 = FeaturesSetting::whereSlug('xl_luggage')
            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            }])
            ->first()?->featuresSettingDetail->first();

        // Define the image URLs for the luggage
        $luggageImages = [
            $findRidePage->luggage_option1 ?? $default_luggage_option1->features_setting_id => $postRidePage->luggage_option1 ? asset('home_page_icons/' . $postRidePage->luggage_option1->icon) : asset('home_page_icons/' . $default_luggage_option1->icon),
            $findRidePage->luggage_option2 ?? $default_luggage_option2->features_setting_id => $postRidePage->luggage_option2 ? asset('home_page_icons/' . $postRidePage->luggage_option2->icon) : asset('home_page_icons/' . $default_luggage_option2->icon),
            $findRidePage->luggage_option3 ?? $default_luggage_option3->features_setting_id => $postRidePage->luggage_option3 ? asset('home_page_icons/' . $postRidePage->luggage_option3->icon) : asset('home_page_icons/' . $default_luggage_option3->icon),
            $findRidePage->luggage_option4 ?? $default_luggage_option4->features_setting_id => $postRidePage->luggage_option4 ? asset('home_page_icons/' . $postRidePage->luggage_option4->icon) : asset('home_page_icons/' . $default_luggage_option4->icon),
            $findRidePage->luggage_option5 ?? $default_luggage_option5->features_setting_id => $postRidePage->luggage_option5 ? asset('home_page_icons/' . $postRidePage->luggage_option5->icon) : asset('home_page_icons/' . $default_luggage_option5->icon),
        ];
        $luggageTooltips = [
            $findRidePage->luggage_option1 ?? $default_luggage_option1->features_setting_id => $postRidePage->luggage_option1 ? $postRidePage->luggage_option1_tooltip : $defaultPostRidePage->luggage_option1_tooltip,
            $findRidePage->luggage_option2 ?? $default_luggage_option2->features_setting_id => $postRidePage->luggage_option2 ? $postRidePage->luggage_option2_tooltip : $defaultPostRidePage->luggage_option2_tooltip,
            $findRidePage->luggage_option3 ?? $default_luggage_option3->features_setting_id => $postRidePage->luggage_option3 ? $postRidePage->luggage_option3_tooltip : $defaultPostRidePage->luggage_option3_tooltip,
            $findRidePage->luggage_option4 ?? $default_luggage_option4->features_setting_id => $postRidePage->luggage_option4 ? $postRidePage->luggage_option4_tooltip : $defaultPostRidePage->luggage_option4_tooltip,
            $findRidePage->luggage_option5 ?? $default_luggage_option5->features_setting_id => $postRidePage->luggage_option5 ? $postRidePage->luggage_option5_tooltip : $defaultPostRidePage->luggage_option5_tooltip,
        ];

        foreach ($rides as $ride) {
            // Calculate seats left
            $bookedSeats = $ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->whereHas('passenger', function($query) {
                    $query->whereNull('deleted_at');
                })
                ->sum('seats');
            $ride->seats_left = intval($ride->seats) - intval($bookedSeats);

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

            $default_features_option1 = FeaturesSetting::whereSlug('pink_rides')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option2 = FeaturesSetting::whereSlug('extra_care_rides')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option3 = FeaturesSetting::whereSlug('wi_fi')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option8 = FeaturesSetting::whereSlug('heating')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option9 = FeaturesSetting::whereSlug('ac')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option10 = FeaturesSetting::whereSlug('bike_rack')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option11 = FeaturesSetting::whereSlug('ski_rack')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option12 = FeaturesSetting::whereSlug('winter_tires')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option13 = FeaturesSetting::whereSlug('star5_passenger')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option14 = FeaturesSetting::whereSlug('star4_passenger')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option15 = FeaturesSetting::whereSlug('star3_passenger')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option4 = FeaturesSetting::whereSlug('driver_features_option4')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option5 = FeaturesSetting::whereSlug('driver_features_option5')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option6 = FeaturesSetting::whereSlug('driver_features_option6')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();
            $default_features_option7 = FeaturesSetting::whereSlug('driver_features_option7')
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                    $query->where('language_id', $defaultLanguage->id);
                }])
                ->first()?->featuresSettingDetail->first();

            // Define the image URLs and titles for the features
            $featureImages = [
                optional($findRidePage->ride_features_option1)->features_setting_id ?? $default_features_option1->features_setting_id => ['title' => optional($findRidePage->ride_features_option1)->name ?? $default_features_option1->name, 'image' => $findRidePage->ride_features_option1 ? asset('home_page_icons/' . $findRidePage->ride_features_option1->icon) : asset('home_page_icons/' . $default_features_option1->icon), 'tooltip' => $postRidePage->features_option1_tooltip ?? $defaultPostRidePage->features_option1_tooltip],
                optional($findRidePage->ride_features_option2)->features_setting_id ?? $default_features_option2->features_setting_id => ['title' => optional($findRidePage->ride_features_option2)->name ?? $default_features_option2->name, 'image' => $findRidePage->ride_features_option2 ? asset('home_page_icons/' . $findRidePage->ride_features_option2->icon) : asset('home_page_icons/' . $default_features_option2->icon), 'tooltip' => $postRidePage->features_option2_tooltip ?? $defaultPostRidePage->features_option2_tooltip],
                optional($findRidePage->ride_features_option3)->features_setting_id ?? $default_features_option3->features_setting_id => ['title' => optional($findRidePage->ride_features_option3)->name ?? $default_features_option3->name, 'image' => $findRidePage->ride_features_option3 ? asset('home_page_icons/' . $findRidePage->ride_features_option3->icon) : asset('home_page_icons/' . $default_features_option3->icon), 'tooltip' => $postRidePage->features_option3_tooltip ?? $defaultPostRidePage->features_option3_tooltip],
                optional($findRidePage->ride_features_option8)->features_setting_id ?? $default_features_option8->features_setting_id => ['title' => optional($findRidePage->ride_features_option8)->name ?? $default_features_option8->name, 'image' => $findRidePage->ride_features_option8 ? asset('home_page_icons/' . $findRidePage->ride_features_option8->icon) : asset('home_page_icons/' . $default_features_option8->icon), 'tooltip' => $postRidePage->features_option8_tooltip ?? $defaultPostRidePage->features_option8_tooltip],
                optional($findRidePage->ride_features_option9)->features_setting_id ?? $default_features_option9->features_setting_id => ['title' => optional($findRidePage->ride_features_option9)->name ?? $default_features_option9->name, 'image' => $findRidePage->ride_features_option9 ? asset('home_page_icons/' . $findRidePage->ride_features_option9->icon) : asset('home_page_icons/' . $default_features_option9->icon), 'tooltip' => $postRidePage->features_option9_tooltip ?? $defaultPostRidePage->features_option9_tooltip],
                optional($findRidePage->ride_features_option10)->features_setting_id ?? $default_features_option10->features_setting_id => ['title' => optional($findRidePage->ride_features_option10)->name ?? $default_features_option10->name, 'image' => $findRidePage->ride_features_option10 ? asset('home_page_icons/' . $findRidePage->ride_features_option10->icon) : asset('home_page_icons/' . $default_features_option10->icon), 'tooltip' => $postRidePage->features_option10_tooltip ?? $defaultPostRidePage->features_option10_tooltip],
                optional($findRidePage->ride_features_option11)->features_setting_id ?? $default_features_option11->features_setting_id => ['title' => optional($findRidePage->ride_features_option11)->name ?? $default_features_option11->name, 'image' => $findRidePage->ride_features_option11 ? asset('home_page_icons/' . $findRidePage->ride_features_option11->icon) : asset('home_page_icons/' . $default_features_option11->icon), 'tooltip' => $postRidePage->features_option11_tooltip ?? $defaultPostRidePage->features_option11_tooltip],
                optional($findRidePage->ride_features_option12)->features_setting_id ?? $default_features_option12->features_setting_id => ['title' => optional($findRidePage->ride_features_option12)->name ?? $default_features_option12->name, 'image' => $findRidePage->ride_features_option12 ? asset('home_page_icons/' . $findRidePage->ride_features_option12->icon) : asset('home_page_icons/' . $default_features_option12->icon), 'tooltip' => $postRidePage->features_option12_tooltip ?? $defaultPostRidePage->features_option12_tooltip],
                optional($findRidePage->ride_features_option13)->features_setting_id ?? $default_features_option13->features_setting_id => ['title' => optional($findRidePage->ride_features_option13)->name ?? $default_features_option13->name, 'image' => $findRidePage->ride_features_option13 ? asset('home_page_icons/' . $findRidePage->ride_features_option13->icon) : asset('home_page_icons/' . $default_features_option13->icon), 'tooltip' => $postRidePage->features_option13_tooltip ?? $defaultPostRidePage->features_option13_tooltip],
                optional($findRidePage->ride_features_option14)->features_setting_id ?? $default_features_option14->features_setting_id => ['title' => optional($findRidePage->ride_features_option14)->name ?? $default_features_option14->name, 'image' => $findRidePage->ride_features_option14 ? asset('home_page_icons/' . $findRidePage->ride_features_option14->icon) : asset('home_page_icons/' . $default_features_option14->icon), 'tooltip' => $postRidePage->features_option14_tooltip ?? $defaultPostRidePage->features_option14_tooltip],
                optional($findRidePage->ride_features_option15)->features_setting_id ?? $default_features_option15->features_setting_id => ['title' => optional($findRidePage->ride_features_option15)->name ?? $default_features_option15->name, 'image' => $findRidePage->ride_features_option15 ? asset('home_page_icons/' . $findRidePage->ride_features_option15->icon) : asset('home_page_icons/' . $default_features_option15->icon), 'tooltip' => $postRidePage->features_option15_tooltip ?? $defaultPostRidePage->features_option15_tooltip],
                optional($postRidePage->features_option4)->features_setting_id ?? $default_features_option4->features_setting_id => ['title' => optional($postRidePage->features_option4)->name ?? $default_features_option4->name, 'image' => $postRidePage->ride_features_option4 ? asset('home_page_icons/' . $postRidePage->features_option4->icon) : asset('home_page_icons/' . $default_features_option4->icon), 'tooltip' => $postRidePage->features_option4_tooltip ?? $defaultPostRidePage->features_option4_tooltip],
                optional($postRidePage->features_option5)->features_setting_id ?? $default_features_option5->features_setting_id => ['title' => optional($postRidePage->features_option5)->name ?? $default_features_option5->name, 'image' => $postRidePage->ride_features_option5 ? asset('home_page_icons/' . $postRidePage->features_option5->icon) : asset('home_page_icons/' . $default_features_option5->icon), 'tooltip' => $postRidePage->features_option5_tooltip ?? $defaultPostRidePage->features_option5_tooltip],
                optional($postRidePage->features_option6)->features_setting_id ?? $default_features_option6->features_setting_id => ['title' => optional($postRidePage->features_option6)->name ?? $default_features_option6->name, 'image' => $postRidePage->ride_features_option6 ? asset('home_page_icons/' . $postRidePage->features_option6->icon) : asset('home_page_icons/' . $default_features_option6->icon), 'tooltip' => $postRidePage->features_option6_tooltip ?? $defaultPostRidePage->features_option6_tooltip],
                optional($postRidePage->features_option7)->features_setting_id ?? $default_features_option7->features_setting_id => ['title' => optional($postRidePage->features_option7)->name ?? $default_features_option7->name, 'image' => $postRidePage->ride_features_option7 ? asset('home_page_icons/' . $postRidePage->features_option7->icon) : asset('home_page_icons/' . $default_features_option7->icon), 'tooltip' => $postRidePage->features_option7_tooltip ?? $defaultPostRidePage->features_option7_tooltip],
            ];

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

            // Calculate age
            if ($ride->driver->dob) {
                $dob = Carbon::parse($ride->driver->dob);
                $ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $ride->driver->age = null; // Handle case where dob is not set
            }

            if ($ride->driver->gender) {
                if ($ride->driver->gender === 'male') {
                    $ride->driver->gender_label = $genderLabel->male_option_label;
                } elseif ($ride->driver->gender === 'female') {
                    $ride->driver->gender_label = $genderLabel->female_option_label;
                } elseif ($ride->driver->gender === 'prefer not to say') {
                    $ride->driver->gender_label = $genderLabel->prefer_option_label;
                }
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($ride) {
                return $rating->ride->added_by === $ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $ride->driver->average_rating = $totalAverage;

            $ride->driver->driven_rides = $ride->driver->rides()
                ->where('status', '!=', 2)
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

            foreach ($ride->bookings as $booking) {
                if ($booking->passenger->gender) {
                    if ($booking->passenger->gender === 'male') {
                        $booking->passenger->gender_label = $genderLabel->male_option_label;
                    } elseif ($booking->passenger->gender === 'female') {
                        $booking->passenger->gender_label = $genderLabel->female_option_label;
                    } elseif ($booking->passenger->gender === 'prefer not to say') {
                        $booking->passenger->gender_label = $genderLabel->prefer_option_label;
                    }
                }
            }
        }

        $data = ['rides' => $rides];
        return $this->successResponse($data, 'Get my cancelled rides');
    }

    public function MyPassengers(Request $request){

        $selectedLanguage = app()->getLocale() ?? 'en';
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message')->first();

        $ride = Ride::where('id', $request->id)->first();
        if ($ride) {
            $bookings = Booking::where('ride_id', $ride->id)->where('status', 1)
                ->whereHas('passenger', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->with(['passenger' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'dob', 'profile_image');
                }])
                ->with(['ride' => function ($query) {
                    $query->select('id', 'date', 'time');
                }])->get();

            if ($request->lang_id && $request->lang_id != 0) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
                }
            }

            foreach ($bookings as $booking) {

                // Calculate age
                if ($booking->passenger->dob) {
                    $dob = Carbon::parse($booking->passenger->dob);
                    $booking->passenger->age = $dob->diffInYears(Carbon::now());
                } else {
                    $booking->passenger->age = null; // Handle case where dob is not set
                }

                if ($booking->passenger->gender) {
                    if ($booking->passenger->gender === 'male') {
                        $booking->passenger->gender_label = $genderLabel->male_option_label;
                    } elseif ($booking->passenger->gender === 'female') {
                        $booking->passenger->gender_label = $genderLabel->female_option_label;
                    } elseif ($booking->passenger->gender === 'prefer not to say') {
                        $booking->passenger->gender_label = $genderLabel->prefer_option_label;
                    }
                }
            }

            $cancelRideSetting = CancelRideSetting::select('id', 'driver_cancel_hours')->first();

            $myPassengerPage = null;
            if ($request->lang_id && $request->lang_id != 0) {
                $myPassengerPage = MyPassengerSettingDetail::where('language_id', $request->lang_id)->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $myPassengerPage = MyPassengerSettingDetail::where('language_id', $selectedLanguage->id)->first();
                }
            }

            $data = ['bookings' => $bookings, 'cancelRideSetting' => $cancelRideSetting, 'myPassengerPage' => $myPassengerPage];
            return $this->successResponse($data, 'Success');
        }

        return $this->apiErrorResponse($message->general_error_message ?? "Ride not found", 404);
    }

    public function removePassenger(Request $request){
        $booking = Booking::with('passenger')->where('id', $request->booking_id)->first();

        $selectedLanguage = app()->getLocale() ?? 'en';
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message', 'removed_passenger_message')->first();


        $getPaymentMethodId = FeaturesSetting::where('slug', 'cash')->value('id');
        if ($booking) {
            $ride = Ride::with('driver')->where('id', $booking->ride_id)->first();


            $removed_permanently = $request->filled('removed_permanently') ? $request->removed_permanently : 0;
            $remove_type = $request->filled('remove_type') ? $request->remove_type : null;

            $request->validate([
                'admin_message' => 'required',
                'passenger_message' => 'required',
                'remove_type' => $removed_permanently == "1" ? 'required' : 'nullable',
                'block_day' => $remove_type == "temporarily" ? 'required' : 'nullable',
            ]);


            $blockDay = "";
            $blockDateTime = "";

            if($removed_permanently == "1" && isset($remove_type) && $remove_type == "temporarily"){
                $blockDay = $request->block_day;
                $currentDate = date('Y-m-d H:i:s');
                $getDate = date('Y-m-d H:i:s', strtotime($currentDate. "+ ".$blockDay." days"));
                $blockDateTime = $getDate;
            }else if($removed_permanently == "1" && isset($remove_type) && $remove_type == "permanently"){
                $blockDay = 1000;
                $currentDate = date('Y-m-d H:i:s');
                $getDate = date('Y-m-d H:i:s', strtotime($currentDate . "+ " . $blockDay . " days"));
                $blockDateTime = $getDate;
            }



            $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
            foreach ($transactions as $transaction) {
                if ($transaction) {
                    $refundId = "";

                    $checkPrice = 0.0;
                    if($ride->payment_method != $getPaymentMethodId){
                        $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');

                        if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                            $getRefundEntryPrice = (double)$getRefundEntryPrice + (double)$transaction->booking_fee;
                        }

                        $checkPrice = (double)$transaction->price;
                    }else{
                        $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('booking_fee');
                        $checkPrice = (double)$transaction->booking_fee;
                    }


                    if(isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == $checkPrice){

                    }else{

                        $transactionAmt = $checkPrice - $getRefundEntryPrice;

                        if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                            $transactionAmt = (double)$transactionAmt - (double)$transaction->booking_fee;
                        }



                        if($transaction->pay_by_account == 0){
                            if ($transaction->paypal_id) {
                                $paypal = new PayPalClient;
                                $paypal->setApiCredentials(config('paypal'));
                                $token = $paypal->getAccessToken();
                                $paypal->setAccessToken($token);
                                $response = $paypal->refundCapturedPayment(
                                    $transaction->paypal_id,
                                    'Invoice-' . $transaction->paypal_id,
                                    $transactionAmt,
                                    'Refund issued.'
                                );
                                $refundId = isset($response['id']) ? $response['id'] : "";
                            } elseif ($transaction->stripe_id) {
                                // Set your Stripe API key
                                Stripe::setApiKey(env('STRIPE_SECRET'));

                                try {
                                    // Create a refund using the payment intent ID
                                    $refund = Refund::create([
                                        'payment_intent' => $transaction->stripe_id,
                                        'amount' => $transactionAmt * 100, // Refund amount in cents
                                    ]);

                                    $refundId = $refund->id;

                                } catch (\Stripe\Exception\ApiErrorException $e) {
                                    // Handle error
                                    return $this->apiErrorResponse($e->getMessage(), 200);
                                }
                            }
                        }else{
                            $topUpBalance = TopUpBalance::create([
                                'booking_id' => $transaction->booking_id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transactionAmt,
                                'added_date' => date('Y-m-d'),
                            ]);
                        }


                        if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transaction->booking_fee,
                            ]);
                        }

                        if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                            $newTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $ride->payment_method != $getPaymentMethodId ? $transactionAmt : 0,
                                'booking_fee' => $ride->payment_method == $getPaymentMethodId ? $transactionAmt : $transaction->booking_fee,
                                'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                            ]);
                        }else{
                            $newTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $ride->payment_method != $getPaymentMethodId ? $transactionAmt : 0,
                                'booking_fee' => $ride->payment_method == $getPaymentMethodId ? $transactionAmt : 0,
                                'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                            ]);
                        }
                    }

                }
            }

            $booking->update([
                'status' => '4',
                'remove_type' => isset($remove_type) ? $remove_type : NULL,
                'removed_permanently' => $removed_permanently,
                'block_days' => isset($blockDay) && $blockDay != "" ? $blockDay : NULL,
                'block_date_time' => isset($blockDateTime) && $blockDateTime != "" ? $blockDateTime : NULL,
            ]);

            $getSeatDetails = SeatDetail::where('booking_id', $booking->id)->get();
            if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                foreach ($getSeatDetails as $key => $getSeatDetail) {
                    $getSeatDetail->status = 'pending';
                    $getSeatDetail->booking_id = NULL;
                    $getSeatDetail->user_id = NULL;
                    $getSeatDetail->save();
                }
            }

            CancellationHistory::create([
                'ride_id' => $booking->ride_id,
                'booking_id' => $booking->id,
                'user_id' => $ride->added_by,
            ]);

            $notification = Notification::create([
                'type' => 2,
                'ride_id' => $booking->ride_id,
                'posted_to' => $booking->id,
                'posted_by' => $booking->ride->added_by,
                'message' =>  'Driver cancelled your booking',
                'status' => 'cancelled',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);
            $message = Message::create([
                'ride_id' => $booking->ride_id,
                'receiver' => $booking->user_id,
                'sender' => $ride->added_by,
                'message' => $request->admin_message,
                'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
            ]);
            $user = User::where('id', $booking->user_id)->first();
            // Assuming $user and $fcmToken are defined
            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;

            if ($fcmToken) {
                $fcmService = new FCMService();
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }

            $data = ['passenger_name' => $booking->passenger->first_name, 'driver_name' => $booking->ride->driver->first_name, 'message' => $request->passenger_message, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($booking->ride->date)->format('F d, Y') ,'time' => $booking->ride->time, 'seats' => $booking->seats, 'total_price' => $booking->fare];
            // Send email to passenger
            Mail::to($booking->passenger->email)->queue(new CancelPassengerMail($data));

            $admin = Admin::first();
            $data = ['admin_username' => $admin->username, 'driver_name' => $booking->ride->driver->first_name, 'passenger_name' => $booking->passenger->first_name, 'departure' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'message' => $request->admin_message];
            // Send email to admin
            Mail::to($admin->admin_email)->queue(new CancelPassengerAdminMail($data));


            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->where('default', '1')->first();

            if (!$phoneNumber) {
                $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->first();
            }

            if ($phoneNumber && env('APP_ENV') != 'local') {
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_PHONE_NUMBER');

                $twilio = new Client($sid, $token);
                $to = $phoneNumber->phone;

                $title = "";
                $currentHour = date('H');
                if ($currentHour >= 0 && $currentHour < 12) {
                    $title = "Good morning ".$booking->passenger->first_name."";
                } elseif ($currentHour >= 12 && $currentHour < 17) {
                    $title = "Good afternoon ".$booking->passenger->first_name."";
                } else {
                    $title = "Good evening ".$booking->passenger->first_name."";
                }

                $depatureDate = date('d F, Y H:i:s', strtotime(''.$ride->date.' '.$ride->time.''));

                $message = "".$title."\nDriver remove your seat from this ride\nTrip detail\nOrigin: ".$booking->departure."\nDestination: ".$booking->destination."\nDeparture date: ".$depatureDate."\nDriver name: ".$ride->driver->first_name."\nDriver phone number: ".$ride->driver->phone."";

                try {
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                } catch (\Exception  $e) {
                    Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                }
            }

            $ride_time = strtotime($ride->time);
            $current_time = time();
            $current_date = date('Y-m-d');
            $time_left = $ride_time - $current_time;
            if ( $current_date == date('Y-m-d', strtotime($ride->data)) && $time_left <= 3600) {
                $getBookings = Booking::with('passenger')
                ->where('ride_id', $ride->id)
                ->where('status', '!=', '3')
                ->where('status', '!=', '0')
                ->where('status', '!=', '4')
                ->get();
                $messageContent = "";
                if(isset($getBookings) && count($getBookings) > 0){
                    foreach ($getBookings as $key => $getBooking) {
                        if($messageContent == ""){
                            $messageContent = "".$getBooking->passenger->first_name."(".$getBooking->passenger->phone.")";
                        }else{
                            $messageContent .= "\n".$getBooking->passenger->first_name."(".$getBooking->passenger->phone.")";
                        }
                    }
                    $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

                    if (!$phoneNumber) {
                        $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
                    }

                    if ($phoneNumber && env('APP_ENV') != 'local') {
                        $sid = env('TWILIO_ACCOUNT_SID');
                        $token = env('TWILIO_AUTH_TOKEN');
                        $from = env('TWILIO_PHONE_NUMBER');

                        $twilio = new Client($sid, $token);
                        $to = $phoneNumber->phone;

                        $title = "";
                        $currentHour = date('H');
                        if ($currentHour >= 0 && $currentHour < 12) {
                            $title = "Good morning ".$ride->driver->first_name."";
                        } elseif ($currentHour >= 12 && $currentHour < 17) {
                            $title = "Good afternoon ".$ride->driver->first_name."";
                        } else {
                            $title = "Good evening ".$ride->driver->first_name."";
                        }

                        $depatureDate = date('d F, Y H:i:s', strtotime(''.$ride->date.' '.$ride->time.''));

                        $message = "".$title."\nTrip detail\nOrigin: ".$booking->departure."\nDestination: ".$booking->destination."\nDeparture date: ".$depatureDate."\nHere is your passengers’ list\n".$messageContent."";

                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                        } catch (\Exception  $e) {
                            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                        }
                    }
                }

            }

            $findRidePage = null;
            $postRidePage = null;
            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                if ($selectedLanguage) {
                    // Retrieve the HomePageSettingDetail associated with the selected language
                    $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                    $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                    if ($postRidePage) {
                        $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option2)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option3)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->luggage_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option1)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->luggage_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option2)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->luggage_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option3)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->luggage_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option4)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $postRidePage->luggage_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option5)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                    }
                    if ($findRidePage) {
                        $findRidePage->ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option16)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                    }
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                    $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                    if ($postRidePage) {
                        $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option2)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option3)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->luggage_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option1)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->luggage_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option2)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->luggage_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option3)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->luggage_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option4)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                        $postRidePage->luggage_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->luggage_option5)
                            ->whereLanguageId($selectedLanguage->id)
                            ->first();
                    }
                    if ($findRidePage) {
                        $findRidePage->ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                        $findRidePage->ride_features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option16)
                            ->whereLanguageId($request->lang_id)
                            ->first();
                    }
                }
            }

            $defaultLanguage = Language::where('is_default', 1)->first();
            $defaultPostRidePage = PostRidePageSettingDetail::where('language_id', $defaultLanguage->id)->first();

            $default_booking_option1 = FeaturesSetting::whereSlug('instant')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
            $default_booking_option2 = FeaturesSetting::whereSlug('manual')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();

            // Define the image URLs for the booking methods
            $bookingMethodImages = [
                optional($postRidePage->booking_option1)->features_setting_id ?? $default_booking_option1->features_setting_id => $postRidePage->booking_option1 ? asset('home_page_icons/' . $postRidePage->booking_option1->icon) : asset('home_page_icons/' . $default_booking_option1->icon),
                optional($postRidePage->booking_option2)->features_setting_id ?? $default_booking_option2->features_setting_id => $postRidePage->booking_option2 ? asset('home_page_icons/' . $postRidePage->booking_option2->icon) : asset('home_page_icons/' . $default_booking_option2->icon),
            ];
            $bookingMethodTooltips = [
                optional($postRidePage->booking_option1)->features_setting_id ?? $default_booking_option1->features_setting_id => $postRidePage->booking_option1 ? $postRidePage->booking_option1_tooltip : $defaultPostRidePage->booking_option1_tooltip,
                optional($postRidePage->booking_option2)->features_setting_id ?? $default_booking_option2->features_setting_id => $postRidePage->booking_option2 ? $postRidePage->booking_option2_tooltip : $defaultPostRidePage->booking_option2_tooltip,
            ];

            if ($ride) {
                // Calculate seats left
                $bookedSeats = $ride->bookings()
                    ->where('status', '<>', 3)
                    ->where('status', '<>', 4)
                    ->whereHas('passenger', function($query) {
                        $query->whereNull('deleted_at');
                    })
                    ->sum('seats');
                $ride->seats_left = intval($ride->seats) - intval($bookedSeats);

                $default_payment_methods_option1 = FeaturesSetting::whereSlug('cash')
                        ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                            $query->where('language_id', $defaultLanguage->id);
                        }])
                        ->first()?->featuresSettingDetail->first();
                $default_payment_methods_option2 = FeaturesSetting::whereSlug('online')
                        ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                            $query->where('language_id', $defaultLanguage->id);
                        }])
                        ->first()?->featuresSettingDetail->first();
                $default_payment_methods_option3 = FeaturesSetting::whereSlug('secured')
                        ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                            $query->where('language_id', $defaultLanguage->id);
                        }])
                        ->first()?->featuresSettingDetail->first();

                // Define the image URLs for the payment methods
                $paymentMethodImages = [
                    $findRidePage->payment_methods_option2 ?? $default_payment_methods_option1->features_setting_id => $postRidePage->payment_methods_option1 ? asset('home_page_icons/' . $postRidePage->payment_methods_option1->icon) : asset('home_page_icons/' . $default_payment_methods_option1->icon),
                    $findRidePage->payment_methods_option3 ?? $default_payment_methods_option2->features_setting_id => $postRidePage->payment_methods_option2 ? asset('home_page_icons/' . $postRidePage->payment_methods_option2->icon) : asset('home_page_icons/' . $default_payment_methods_option2->icon),
                    $findRidePage->payment_methods_option4 ?? $default_payment_methods_option3->features_setting_id => $postRidePage->payment_methods_option3 ? asset('home_page_icons/' . $postRidePage->payment_methods_option3->icon) : asset('home_page_icons/' . $default_payment_methods_option3->icon),
                ];
                $paymentMethodTooltips = [
                    $findRidePage->payment_methods_option2 ?? $default_payment_methods_option1->features_setting_id => $postRidePage->payment_methods_option1 ? $postRidePage->payment_methods_option1_tooltip : $defaultPostRidePage->payment_methods_option1_tooltip,
                    $findRidePage->payment_methods_option3 ?? $default_payment_methods_option2->features_setting_id => $postRidePage->payment_methods_option2 ? $postRidePage->payment_methods_option2_tooltip : $defaultPostRidePage->payment_methods_option2_tooltip,
                    $findRidePage->payment_methods_option4 ?? $default_payment_methods_option3->features_setting_id => $postRidePage->payment_methods_option3 ? $postRidePage->payment_methods_option3_tooltip : $defaultPostRidePage->payment_methods_option3_tooltip,
                ];

                $default_smoking_option1 = FeaturesSetting::whereSlug('no_smoking')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_smoking_option2 = FeaturesSetting::whereSlug('indifferent_smoking')
                        ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();

                // Define the image URLs for the smoke
                $smokeImages = [
                    $findRidePage->smoking_option1 ?? $default_smoking_option1->features_setting_id => $postRidePage->smoking_option1 ? asset('home_page_icons/' . $postRidePage->smoking_option1->icon) : asset('home_page_icons/' . $default_smoking_option1->icon),
                    $findRidePage->smoking_option2 ?? $default_smoking_option2->features_setting_id => $postRidePage->smoking_option2 ? asset('home_page_icons/' . $postRidePage->smoking_option2->icon) : asset('home_page_icons/' . $default_smoking_option2->icon),
                ];
                $smokeTooltips = [
                    $findRidePage->smoking_option1 ?? $default_smoking_option1->features_setting_id => $postRidePage->smoking_option1 ? $postRidePage->smoking_option1_tooltip : $defaultPostRidePage->smoking_option1_tooltip,
                    $findRidePage->smoking_option2 ?? $default_smoking_option2->features_setting_id => $postRidePage->smoking_option2 ? $postRidePage->smoking_option2_tooltip : $defaultPostRidePage->smoking_option2_tooltip,
                ];

                $default_animals_option1 = FeaturesSetting::whereSlug('no_animals')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_animals_option2 = FeaturesSetting::whereSlug('yes_animals')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_animals_option3 = FeaturesSetting::whereSlug('caged_animals')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();

                // Define the image URLs for the pets
                $petsImages = [
                    $findRidePage->pets_allowed_option1 ?? $default_animals_option1->features_setting_id => $postRidePage->animals_option1 ? asset('home_page_icons/' . $postRidePage->animals_option1->icon) : asset('home_page_icons/' . $default_animals_option1->icon),
                    $findRidePage->pets_allowed_option2 ?? $default_animals_option2->features_setting_id => $postRidePage->animals_option2 ? asset('home_page_icons/' . $postRidePage->animals_option2->icon) : asset('home_page_icons/' . $default_animals_option2->icon),
                    $findRidePage->pets_allowed_option3 ?? $default_animals_option3->features_setting_id => $postRidePage->animals_option3 ? asset('home_page_icons/' . $postRidePage->animals_option3->icon) : asset('home_page_icons/' . $default_animals_option3->icon),
                ];
                $petsTooltips = [
                    $findRidePage->pets_allowed_option1 ?? $default_animals_option1->features_setting_id => $postRidePage->animals_option1 ? $postRidePage->animals_option1_tooltip : $defaultPostRidePage->animals_option1_tooltip,
                    $findRidePage->pets_allowed_option2 ?? $default_animals_option2->features_setting_id => $postRidePage->animals_option2 ? $postRidePage->animals_option2_tooltip : $defaultPostRidePage->animals_option2_tooltip,
                    $findRidePage->pets_allowed_option3 ?? $default_animals_option3->features_setting_id => $postRidePage->animals_option3 ? $postRidePage->animals_option3_tooltip : $defaultPostRidePage->animals_option3_tooltip,
                ];

                $default_luggage_option1 = FeaturesSetting::whereSlug('no_luggage')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_luggage_option2 = FeaturesSetting::whereSlug('small_luggage')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_luggage_option3 = FeaturesSetting::whereSlug('medium_luggage')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_luggage_option4 = FeaturesSetting::whereSlug('large_luggage')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_luggage_option5 = FeaturesSetting::whereSlug('xl_luggage')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();

                // Define the image URLs for the luggage
                $luggageImages = [
                    $findRidePage->luggage_option1 ?? $default_luggage_option1->features_setting_id => $postRidePage->luggage_option1 ? asset('home_page_icons/' . $postRidePage->luggage_option1->icon) : asset('home_page_icons/' . $default_luggage_option1->icon),
                    $findRidePage->luggage_option2 ?? $default_luggage_option2->features_setting_id => $postRidePage->luggage_option2 ? asset('home_page_icons/' . $postRidePage->luggage_option2->icon) : asset('home_page_icons/' . $default_luggage_option2->icon),
                    $findRidePage->luggage_option3 ?? $default_luggage_option3->features_setting_id => $postRidePage->luggage_option3 ? asset('home_page_icons/' . $postRidePage->luggage_option3->icon) : asset('home_page_icons/' . $default_luggage_option3->icon),
                    $findRidePage->luggage_option4 ?? $default_luggage_option4->features_setting_id => $postRidePage->luggage_option4 ? asset('home_page_icons/' . $postRidePage->luggage_option4->icon) : asset('home_page_icons/' . $default_luggage_option4->icon),
                    $findRidePage->luggage_option5 ?? $default_luggage_option5->features_setting_id => $postRidePage->luggage_option5 ? asset('home_page_icons/' . $postRidePage->luggage_option5->icon) : asset('home_page_icons/' . $default_luggage_option5->icon),
                ];
                $luggageTooltips = [
                    $findRidePage->luggage_option1 ?? $default_luggage_option1->features_setting_id => $postRidePage->luggage_option1 ? $postRidePage->luggage_option1_tooltip : $defaultPostRidePage->luggage_option1_tooltip,
                    $findRidePage->luggage_option2 ?? $default_luggage_option2->features_setting_id => $postRidePage->luggage_option2 ? $postRidePage->luggage_option2_tooltip : $defaultPostRidePage->luggage_option2_tooltip,
                    $findRidePage->luggage_option3 ?? $default_luggage_option3->features_setting_id => $postRidePage->luggage_option3 ? $postRidePage->luggage_option3_tooltip : $defaultPostRidePage->luggage_option3_tooltip,
                    $findRidePage->luggage_option4 ?? $default_luggage_option4->features_setting_id => $postRidePage->luggage_option4 ? $postRidePage->luggage_option4_tooltip : $defaultPostRidePage->luggage_option4_tooltip,
                    $findRidePage->luggage_option5 ?? $default_luggage_option5->features_setting_id => $postRidePage->luggage_option5 ? $postRidePage->luggage_option5_tooltip : $defaultPostRidePage->luggage_option5_tooltip,
                ];

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

                $default_features_option1 = FeaturesSetting::whereSlug('pink_rides')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option2 = FeaturesSetting::whereSlug('extra_care_rides')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option3 = FeaturesSetting::whereSlug('wi_fi')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option8 = FeaturesSetting::whereSlug('heating')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option9 = FeaturesSetting::whereSlug('ac')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option10 = FeaturesSetting::whereSlug('bike_rack')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option11 = FeaturesSetting::whereSlug('ski_rack')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option12 = FeaturesSetting::whereSlug('winter_tires')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option13 = FeaturesSetting::whereSlug('star5_passenger')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option14 = FeaturesSetting::whereSlug('star4_passenger')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option15 = FeaturesSetting::whereSlug('star3_passenger')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option4 = FeaturesSetting::whereSlug('driver_features_option4')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option5 = FeaturesSetting::whereSlug('driver_features_option5')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option6 = FeaturesSetting::whereSlug('driver_features_option6')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();
                $default_features_option7 = FeaturesSetting::whereSlug('driver_features_option7')
                    ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguage) {
                        $query->where('language_id', $defaultLanguage->id);
                    }])
                    ->first()?->featuresSettingDetail->first();

                // Define the image URLs and titles for the features
                $featureImages = [
                    optional($findRidePage->ride_features_option1)->features_setting_id ?? $default_features_option1->features_setting_id => ['title' => optional($findRidePage->ride_features_option1)->name ?? $default_features_option1->name, 'image' => $findRidePage->ride_features_option1 ? asset('home_page_icons/' . $findRidePage->ride_features_option1->icon) : asset('home_page_icons/' . $default_features_option1->icon), 'tooltip' => $postRidePage->features_option1_tooltip ?? $defaultPostRidePage->features_option1_tooltip],
                    optional($findRidePage->ride_features_option2)->features_setting_id ?? $default_features_option2->features_setting_id => ['title' => optional($findRidePage->ride_features_option2)->name ?? $default_features_option2->name, 'image' => $findRidePage->ride_features_option2 ? asset('home_page_icons/' . $findRidePage->ride_features_option2->icon) : asset('home_page_icons/' . $default_features_option2->icon), 'tooltip' => $postRidePage->features_option2_tooltip ?? $defaultPostRidePage->features_option2_tooltip],
                    optional($findRidePage->ride_features_option3)->features_setting_id ?? $default_features_option3->features_setting_id => ['title' => optional($findRidePage->ride_features_option3)->name ?? $default_features_option3->name, 'image' => $findRidePage->ride_features_option3 ? asset('home_page_icons/' . $findRidePage->ride_features_option3->icon) : asset('home_page_icons/' . $default_features_option3->icon), 'tooltip' => $postRidePage->features_option3_tooltip ?? $defaultPostRidePage->features_option3_tooltip],
                    optional($findRidePage->ride_features_option8)->features_setting_id ?? $default_features_option8->features_setting_id => ['title' => optional($findRidePage->ride_features_option8)->name ?? $default_features_option8->name, 'image' => $findRidePage->ride_features_option8 ? asset('home_page_icons/' . $findRidePage->ride_features_option8->icon) : asset('home_page_icons/' . $default_features_option8->icon), 'tooltip' => $postRidePage->features_option8_tooltip ?? $defaultPostRidePage->features_option8_tooltip],
                    optional($findRidePage->ride_features_option9)->features_setting_id ?? $default_features_option9->features_setting_id => ['title' => optional($findRidePage->ride_features_option9)->name ?? $default_features_option9->name, 'image' => $findRidePage->ride_features_option9 ? asset('home_page_icons/' . $findRidePage->ride_features_option9->icon) : asset('home_page_icons/' . $default_features_option9->icon), 'tooltip' => $postRidePage->features_option9_tooltip ?? $defaultPostRidePage->features_option9_tooltip],
                    optional($findRidePage->ride_features_option10)->features_setting_id ?? $default_features_option10->features_setting_id => ['title' => optional($findRidePage->ride_features_option10)->name ?? $default_features_option10->name, 'image' => $findRidePage->ride_features_option10 ? asset('home_page_icons/' . $findRidePage->ride_features_option10->icon) : asset('home_page_icons/' . $default_features_option10->icon), 'tooltip' => $postRidePage->features_option10_tooltip ?? $defaultPostRidePage->features_option10_tooltip],
                    optional($findRidePage->ride_features_option11)->features_setting_id ?? $default_features_option11->features_setting_id => ['title' => optional($findRidePage->ride_features_option11)->name ?? $default_features_option11->name, 'image' => $findRidePage->ride_features_option11 ? asset('home_page_icons/' . $findRidePage->ride_features_option11->icon) : asset('home_page_icons/' . $default_features_option11->icon), 'tooltip' => $postRidePage->features_option11_tooltip ?? $defaultPostRidePage->features_option11_tooltip],
                    optional($findRidePage->ride_features_option12)->features_setting_id ?? $default_features_option12->features_setting_id => ['title' => optional($findRidePage->ride_features_option12)->name ?? $default_features_option12->name, 'image' => $findRidePage->ride_features_option12 ? asset('home_page_icons/' . $findRidePage->ride_features_option12->icon) : asset('home_page_icons/' . $default_features_option12->icon), 'tooltip' => $postRidePage->features_option12_tooltip ?? $defaultPostRidePage->features_option12_tooltip],
                    optional($findRidePage->ride_features_option13)->features_setting_id ?? $default_features_option13->features_setting_id => ['title' => optional($findRidePage->ride_features_option13)->name ?? $default_features_option13->name, 'image' => $findRidePage->ride_features_option13 ? asset('home_page_icons/' . $findRidePage->ride_features_option13->icon) : asset('home_page_icons/' . $default_features_option13->icon), 'tooltip' => $postRidePage->features_option13_tooltip ?? $defaultPostRidePage->features_option13_tooltip],
                    optional($findRidePage->ride_features_option14)->features_setting_id ?? $default_features_option14->features_setting_id => ['title' => optional($findRidePage->ride_features_option14)->name ?? $default_features_option14->name, 'image' => $findRidePage->ride_features_option14 ? asset('home_page_icons/' . $findRidePage->ride_features_option14->icon) : asset('home_page_icons/' . $default_features_option14->icon), 'tooltip' => $postRidePage->features_option14_tooltip ?? $defaultPostRidePage->features_option14_tooltip],
                    optional($findRidePage->ride_features_option15)->features_setting_id ?? $default_features_option15->features_setting_id => ['title' => optional($findRidePage->ride_features_option15)->name ?? $default_features_option15->name, 'image' => $findRidePage->ride_features_option15 ? asset('home_page_icons/' . $findRidePage->ride_features_option15->icon) : asset('home_page_icons/' . $default_features_option15->icon), 'tooltip' => $postRidePage->features_option15_tooltip ?? $defaultPostRidePage->features_option15_tooltip],
                    optional($postRidePage->features_option4)->features_setting_id ?? $default_features_option4->features_setting_id => ['title' => optional($postRidePage->features_option4)->name ?? $default_features_option4->name, 'image' => $postRidePage->ride_features_option4 ? asset('home_page_icons/' . $postRidePage->features_option4->icon) : asset('home_page_icons/' . $default_features_option4->icon), 'tooltip' => $postRidePage->features_option4_tooltip ?? $defaultPostRidePage->features_option4_tooltip],
                    optional($postRidePage->features_option5)->features_setting_id ?? $default_features_option5->features_setting_id => ['title' => optional($postRidePage->features_option5)->name ?? $default_features_option5->name, 'image' => $postRidePage->ride_features_option5 ? asset('home_page_icons/' . $postRidePage->features_option5->icon) : asset('home_page_icons/' . $default_features_option5->icon), 'tooltip' => $postRidePage->features_option5_tooltip ?? $defaultPostRidePage->features_option5_tooltip],
                    optional($postRidePage->features_option6)->features_setting_id ?? $default_features_option6->features_setting_id => ['title' => optional($postRidePage->features_option6)->name ?? $default_features_option6->name, 'image' => $postRidePage->ride_features_option6 ? asset('home_page_icons/' . $postRidePage->features_option6->icon) : asset('home_page_icons/' . $default_features_option6->icon), 'tooltip' => $postRidePage->features_option6_tooltip ?? $defaultPostRidePage->features_option6_tooltip],
                    optional($postRidePage->features_option7)->features_setting_id ?? $default_features_option7->features_setting_id => ['title' => optional($postRidePage->features_option7)->name ?? $default_features_option7->name, 'image' => $postRidePage->ride_features_option7 ? asset('home_page_icons/' . $postRidePage->features_option7->icon) : asset('home_page_icons/' . $default_features_option7->icon), 'tooltip' => $postRidePage->features_option7_tooltip ?? $defaultPostRidePage->features_option7_tooltip],
                ];

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
                    ->where('status', '!=', 2)
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

                $ratings = Rating::where('status', 1)->where('type', '1')->get();
                // Calculate average rating
                $filteredRatings = $ratings->filter(function ($rating) use ($ride) {
                    return $rating->ride->added_by === $ride->added_by;
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

                    $ratings = Rating::where('status', 1)->where('type', '2')->get();
                    // Calculate average rating
                    $filteredRatings = $ratings->filter(function ($rating) use ($booking) {
                        return $rating->booking->user_id === $booking->user_id;
                    });

                    $totalAverage = $filteredRatings->avg('average_rating');
                    $booking->passenger->average_rating = $totalAverage;
                }
            }

            $data = ['ride' => $ride];
            return $this->successResponse($data, strip_tags($message->removed_passenger_message ?? 'Removed successfully'));
        }



        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? "Booking not found"), 404);
    }

    public function enterCode(Request $request){
        $booking = Booking::where('id', $request->booking_id)->first();
        
        $siteSetting = SiteSetting::first();

        $message = null;
            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                if ($selectedLanguage) {
                    // Retrieve the HomePageSettingDetail associated with the selected language
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('incorrect_code_message','general_error_message', 'too_many_secured_cash_attempt_message', 'secured_cash_success_message')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('incorrect_code_message','general_error_message', 'too_many_secured_cash_attempt_message', 'secured_cash_success_message')->first();
                }
            }

        if ($booking) {
            $request->validate([
                'code' => 'required|max:4',
            ]);

            $messageData = "";

            if ($request->code === $booking->secured_cash_code) {
                $booking->update([
                    'secured_cash' => null,
                    'secured_cash_code' => null,
                ]);

                $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
                foreach ($transactions as $transaction) {
                    if ($transaction) {
                        $refundId = "";
                        if($transaction->pay_by_account == 0){
                            if ($transaction->paypal_id) {
                                $paypal = new PayPalClient;
                                $paypal->setApiCredentials(config('paypal'));
                                $token = $paypal->getAccessToken();
                                $paypal->setAccessToken($token);
                                $response = $paypal->refundCapturedPayment(
                                    $transaction->paypal_id,
                                    'USD',
                                    $transaction->price - $transaction->booking_fee,
                                    'Invoice-' . $transaction->paypal_id
                                );
                                $refundId = isset($response['id']) ? $response['id'] : "";
                            } elseif ($transaction->stripe_id) {
                                // Set your Stripe API key
                                Stripe::setApiKey(env('STRIPE_SECRET'));

                                try {
                                    // Create a refund using the payment intent ID
                                    $refund = Refund::create([
                                        'payment_intent' => $transaction->stripe_id,
                                        'amount' => ($transaction->price - $transaction->booking_fee) * 100, // Refund amount in cents
                                    ]);
                                    $refundId = $refund->id;
                                } catch (\Stripe\Exception\ApiErrorException $e) {
                                    // Handle error
                                    return $this->apiErrorResponse($e->getMessage(), 200);
                                }
                            }
                        }else{
                            $topUpBalance = TopUpBalance::create([
                                'booking_id' => $transaction->booking_id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transaction->price - $transaction->booking_fee,
                                'added_date' => date('Y-m-d'),
                            ]);
                        }

                        $newTransaction = Transaction::create([
                            'booking_id' => $transaction->booking_id,
                            'ride_id' => $booking->ride_id,
                            'parent_id' => $transaction->id,
                            'type' => '3',
                            'price' => $transaction->price,
                            'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                            'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                        ]);

                    }
                }

                $notification = Notification::create([
                    'ride_id' => $booking->ride_id,
                    'posted_by' => $booking->user_id,
                    'message' =>  'Secured-cash payment code successful',
                    'status' => 'upcoming',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);
        
                $fcmToken = $booking->ride->driver->mobile_fcm_token;
                $body = $notification->message;
                $fcmService = new FCMService();
    
                if ($fcmToken) {
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }

                $notification = Notification::create([
                    'type' => 2,
                    'ride_id' => $booking->ride_id,
                    'posted_to' => $booking->id,
                    'posted_by' => $booking->ride->added_by,
                    'message' =>  'Secured-cash payment code successful',
                    'status' => 'upcoming',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);
        
                $fcmToken = $booking->passenger->mobile_fcm_token;
                $body = $notification->message;
                $fcmService = new FCMService();
    
                if ($fcmToken) {
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }

                $driverPhoneNumber = PhoneNumber::where('user_id', $booking->ride->driver->id)
                    ->where('default', '1')
                    ->first();
                $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $booking->ride->driver->phone;

                $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->passenger->id)
                    ->where('default', '1')
                    ->first();
                $passengerPhoneToUse = $passengerPhoneNumber ? $passengerPhoneNumber->phone : $booking->passenger->phone;

                $passengerData = [
                    'passenger_first_name' => $booking->passenger->first_name,
                    'seats_booked' => $booking->seats,
                    'booking_price' => $booking->price,
                    'from' => $booking->departure,
                    'to' => $booking->destination,
                    'on' => $booking->ride->date,
                    'at' => $booking->ride->time,
                    'driver_first_name' => $booking->ride->driver->first_name,
                    'driver_phone' => $driverPhoneToUse,
                    'passenger_email' => $booking->ride->driver->email,
                ];
                if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {
                    Mail::to($booking->passenger->email)->send(new SecuredCashPassengerMail($passengerData));
                }

                // Send email to driver
                $driverData = [
                    'driver_first_name' => $booking->ride->driver->first_name,
                    'seats_booked' => $booking->seats,
                    'booking_price' => $booking->price,
                    'from' => $booking->departure,
                    'to' => $booking->destination,
                    'on' => $booking->ride->date,
                    'at' => $booking->ride->time,
                    'passenger_first_name' => $booking->passenger->first_name,
                    'passenger_phone' => $passengerPhoneToUse,
                    'passenger_email' => $booking->passenger->email,
                ];
                if (isset($booking->ride->driver->email_notification) && $booking->ride->driver->email_notification == 1) {
                    Mail::to($booking->ride->driver->email)->send(new SecuredCashDriverMail($driverData));
                }

                return $this->successResponse('', strip_tags($message->secured_cash_success_message ?? "Code submitted and the booking price has been released back to the passenger. Now, get your payment in cash from them"));
            }else{
                if($booking->secured_cash_attempt_count < $siteSetting->secured_cash_attempt){
                    $count = isset($booking->secured_cash_attempt_count) ? $booking->secured_cash_attempt_count : 0;
                    $count = $count + 1;
                    $booking->secured_cash_attempt_count = $count;
                    $booking->save();
                    $messageData = strip_tags($message->incorrect_code_message);
                }else{
                   $messageData = strip_tags($message->too_many_secured_cash_attempt_message);
                }
            }


            
            return $this->apiErrorResponse($messageData, 200, $booking->secured_cash_attempt_count);
        }
        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? "Booking not found"), 404);
    }

    public function CancelRide(Request $request){
        $ride = Ride::where('id', $request->id)->first();

        $messages = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_cancel_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_cancel_message')->first();
            }
        }

        $bookedSeats = $ride->bookings()
            ->where('status', '!=', '0') // Exclude pending bookings
            ->where('status', '!=', '3') // Exclude canceled bookings
            ->where('status', '!=', '4') // Exclude completed bookings
            ->sum('seats');
        if ($bookedSeats == 0) {
            $ride->update(['status' => '2']);
            
            // Add cancellation history
            CancellationHistory::create([
                'ride_id' => $ride->id,
                'user_id' => $ride->added_by,
                'type' => 'driver',
            ]);
            
            $data = ['ride' => $ride];
            return $this->successResponse($data, strip_tags($messages->ride_cancel_message));
        }

        $getPaymentMethodId = FeaturesSetting::where('slug', 'cash')->value('id');

        $request->validate([
            'message' => 'required',
            'reason' => 'required',
        ]);

        $bookings = Booking::where('ride_id', $request->id)->where('status', '!=', '0')->where('status', '!=', '3')->where('status', '!=', '4')->get();

        foreach ($bookings as $booking) {

            $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
            foreach ($transactions as $transaction) {
                if ($transaction) {

                    $refundId = "";

                    $checkPrice = 0.0;
                    if($ride->payment_method != $getPaymentMethodId){
                        $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');
                        if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                            $getRefundEntryPrice = (double)$getRefundEntryPrice + (double)$transaction->booking_fee;
                        }
                        $checkPrice = (double)$transaction->price;

                    }else{
                        $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('booking_fee');
                        $checkPrice = (double)$transaction->booking_fee;
                    }


                    if(isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == $checkPrice){

                    }else{

                        $transactionAmt = $checkPrice - $getRefundEntryPrice;

                        if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                            $transactionAmt = $transactionAmt - $transaction->booking_fee;
                        }

                        if($transaction->pay_by_account == 0){
                            if ($transaction->paypal_id) {
                                $paypal = new PayPalClient;
                                $paypal->setApiCredentials(config('paypal'));
                                $token = $paypal->getAccessToken();
                                $paypal->setAccessToken($token);
                                $response = $paypal->refundCapturedPayment(
                                    $transaction->paypal_id,
                                    'Invoice-' . $transaction->paypal_id,
                                    $transactionAmt,
                                    'Refund issued.'
                                );

                                $refundId = isset($response['id']) ? $response['id'] : "";
                            } elseif ($transaction->stripe_id) {
                                // Set your Stripe API key
                                Stripe::setApiKey(env('STRIPE_SECRET'));

                                try {
                                    // Create a refund using the payment intent ID
                                    $refund = Refund::create([
                                        'payment_intent' => $transaction->stripe_id,
                                        'amount' => $transactionAmt * 100, // Refund amount in cents
                                    ]);

                                    $refundId = $refund->id;

                                } catch (\Stripe\Exception\ApiErrorException $e) {
                                    // Handle error
                                    return $this->apiErrorResponse($e->getMessage(), 200);
                                }
                            }
                        }else{
                            $topUpBalance = TopUpBalance::create([
                                'booking_id' => $transaction->booking_id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transactionAmt,
                                'added_date' => date('Y-m-d'),
                            ]);
                        }


                        if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transaction->booking_fee,
                            ]);
                        }

                        if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                            $newTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $ride->payment_method != $getPaymentMethodId ? $transactionAmt : 0,
                                'booking_fee' => $ride->payment_method == $getPaymentMethodId ? $transactionAmt : $transaction->booking_fee,
                                'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                            ]);
                        }else{
                            $newTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $ride->payment_method != $getPaymentMethodId ? $transactionAmt : 0,
                                'booking_fee' => $ride->payment_method == $getPaymentMethodId ? $transactionAmt : 0,
                                'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                            ]);
                        }


                    }

                }
            }

            $booking->update([
                'status' => '4',
            ]);

            CancellationHistory::create([
                'ride_id' => $booking->ride_id,
                'booking_id' => $booking->id,
                'user_id' => $ride->added_by,
            ]);

            $notification = Notification::create([
                'type' => 2,
                'ride_id' => $ride->id,
                'posted_to' => $booking->id,
                'posted_by' => $ride->added_by,
                'message' =>  'Your ride has been cancelled',
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            $user = User::whereId($booking->user_id)->first();
            // Assuming $user and $fcmToken are defined
            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;

            if ($fcmToken) {
                $fcmService = new FCMService();
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }

            $data = ['driver_name' => $ride->driver->first_name,'passenger_name' => $booking->passenger->first_name, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time, 'seats' => $booking->seats, 'total_price' => $booking->fare];
            Mail::to($booking->passenger->email)->queue(new DriverCancelRideMail($data));


            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->where('default', '1')->first();

            if (!$phoneNumber) {
                $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->first();
            }

            if ($phoneNumber && env('APP_ENV') != 'local') {
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_PHONE_NUMBER');

                $twilio = new Client($sid, $token);
                $to = $phoneNumber->phone;

                $title = "";
                $currentHour = date('H');
                if ($currentHour >= 0 && $currentHour < 12) {
                    $title = "Good morning ".$booking->passenger->first_name."";
                } elseif ($currentHour >= 12 && $currentHour < 17) {
                    $title = "Good afternoon ".$booking->passenger->first_name."";
                } else {
                    $title = "Good evening ".$booking->passenger->first_name."";
                }

                $depatureDate = date('d F, Y H:i:s', strtotime(''.$ride->date.' '.$ride->time.''));

                $message = "".$title."\nDriver cancelled this ride\nTrip detail\nOrigin: ".$booking->departure."\nDestination: ".$booking->destination."\nDeparture date: ".$depatureDate."\nDriver name: ".$ride->driver->first_name."\nDriver phone number: ".$ride->driver->phone."";

                try {
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                } catch (\Exception  $e) {
                    Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                }
            }

        }

        $ride->update([
            'status' => '2',
        ]);

        $data = ['ride' => $ride];
        return $this->successResponse($data, strip_tags($messages->ride_cancel_message));
    }
}
