<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\PassengerCancelBookingMail;
use App\Models\Booking;
use App\Models\CancellationHistory;
use App\Models\FeaturesSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Models\Rating;
use App\Models\ReviewSetting;
use App\Models\Ride;
use App\Models\City;
use App\Models\SiteSetting;
use App\Models\Transaction;
use App\Models\TopUpBalance;
use App\Models\CoffeeWallet;
use App\Models\FeaturesSettingDetail;
use App\Models\Payout;
use App\Models\PhoneNumber;
use App\Models\TripsPageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Models\SeatDetail;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Message;
use App\Models\Notification;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;
use Twilio\Rest\Client;

class MyTripController extends Controller
{
    use StatusResponser;

    public function CurrentTrips(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $bookings = Booking::where('user_id', $user_id)->select('id', 'ride_id' , 'seats', 'status', 'booking_credit', 'fare', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price', 'booked_on','type')
            ->where('status', '!=', '3')
            ->where('status', '!=', '4')
            ->whereHas('ride', function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '>', now()->toDateString())
                        ->orWhere(function ($query) {
                        $query->whereDate('completed_date', '=', now()->toDateString())
                            ->whereTime('completed_time', '>=', now()->toTimeString());
                    });
                });
            })
            ->with(['ride.vehicle','ride' => function ($query) {
                $query->with(['driver' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
                }]);
            }])
            ->orderBy(Ride::select('date')
            ->whereColumn('rides.id', 'bookings.ride_id')
            ->limit(1), 'asc')
            ->orderBy(Ride::select('time')
            ->whereColumn('rides.id', 'bookings.ride_id')
            ->limit(1), 'asc')
            ->orderBy('ride_id', 'desc')
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
    
        foreach ($bookings as $booking) {
            // Calculate seats left
            $bookedSeats = $booking->ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->whereHas('passenger', function($query) {
                    $query->whereNull('deleted_at');
                })
                ->sum('seats');
            $booking->ride->seats_left = intval($booking->ride->seats) - intval($bookedSeats);
    
            $booking->ride->booking_method_image = $bookingMethodImages[$booking->ride->booking_method] ?? null;
            $booking->ride->booking_method_tooltip = $bookingMethodTooltips[$booking->ride->booking_method] ?? null;
            $booking->ride->payment_method_image = $paymentMethodImages[$booking->ride->payment_method] ?? null;
            $booking->ride->payment_method_tooltip = $paymentMethodTooltips[$booking->ride->payment_method] ?? null;
            $booking->ride->smoke_image = $smokeImages[$booking->ride->smoke] ?? null;
            $booking->ride->smoke_tooltip = $smokeTooltips[$booking->ride->smoke] ?? null;
            $booking->ride->animal_friendly_image = $petsImages[$booking->ride->animal_friendly] ?? null;
            $booking->ride->animal_friendly_tooltip = $petsTooltips[$booking->ride->animal_friendly] ?? null;
            $booking->ride->luggage_image = $luggageImages[$booking->ride->luggage] ?? null;
            $booking->ride->luggage_tooltip = $luggageTooltips[$booking->ride->luggage] ?? null;
    
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
            $rideFeatures = is_string($booking->ride->features) ? explode('=', $booking->ride->features) : $booking->ride->features;
            // Loop through each feature and add the corresponding image and title
            foreach ($rideFeatures as $feature) {
                if (is_string($feature) || is_int($feature)) {
                    if (isset($featureImages[$feature])) {
                        $features[] = $featureImages[$feature];
                    }
                }
            }
            // Assign the features array to the ride's features attribute
            $booking->ride->features = $features;
    
            // Calculate age
            if ($booking->ride->driver->dob) {
                $dob = Carbon::parse($booking->ride->driver->dob);
                $booking->ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $booking->ride->driver->age = null; // Handle case where dob is not set
            }

            if ($booking->ride->driver->gender) {
                if ($booking->ride->driver->gender === 'male') {
                    $booking->ride->driver->gender_label = $genderLabel->male_option_label;
                } elseif ($booking->ride->driver->gender === 'female') {
                    $booking->ride->driver->gender_label = $genderLabel->female_option_label;
                } elseif ($booking->ride->driver->gender === 'prefer not to say') {
                    $booking->ride->driver->gender_label = $genderLabel->prefer_option_label;
                }
            }
    
            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($booking) {
                return $rating->ride->added_by === $booking->ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $booking->ride->driver->average_rating = $totalAverage;
    
            $booking->ride->driver->driven_rides = $booking->ride->driver->rides()
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


        $data = ['bookings' => $bookings,'tripsPage' => $tripsPage, 'rideDetailPage' => $rideDetailPage];
        return $this->successResponse($data, 'Get my upcoming trips');
    }

    public function PastTrips(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $bookings = Booking::where('user_id', $user_id)->select('id', 'ride_id' , 'seats', 'status', 'booking_credit', 'fare', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price', 'booked_on','type')
            ->where('status', '!=', '4')
            ->where('bookings.status', '!=', '3')
            ->whereHas('ride', function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())->whereTime('completed_time', '<', now()->toTimeString());
                        });
                });
            })
            ->with(['ride.vehicle','ride' => function ($query) {
                $query->with(['driver' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
                }]);
            }])
            ->orderBy(Ride::select('date')
            ->whereColumn('rides.id', 'bookings.ride_id')
            ->limit(1), 'asc')
            ->orderBy(Ride::select('time')
            ->whereColumn('rides.id', 'bookings.ride_id')
            ->limit(1), 'asc')
            ->orderBy('ride_id', 'desc')
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

        foreach ($bookings as $booking) {
            // Calculate seats left
            $bookedSeats = $booking->ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->whereHas('passenger', function($query) {
                    $query->whereNull('deleted_at');
                })
                ->sum('seats');
            $booking->ride->seats_left = intval($booking->ride->seats) - intval($bookedSeats);

            $booking->ride->booking_method_image = $bookingMethodImages[$booking->ride->booking_method] ?? null;
            $booking->ride->booking_method_tooltip = $bookingMethodTooltips[$booking->ride->booking_method] ?? null;
            $booking->ride->payment_method_image = $paymentMethodImages[$booking->ride->payment_method] ?? null;
            $booking->ride->payment_method_tooltip = $paymentMethodTooltips[$booking->ride->payment_method] ?? null;
            $booking->ride->smoke_image = $smokeImages[$booking->ride->smoke] ?? null;
            $booking->ride->smoke_tooltip = $smokeTooltips[$booking->ride->smoke] ?? null;
            $booking->ride->animal_friendly_image = $petsImages[$booking->ride->animal_friendly] ?? null;
            $booking->ride->animal_friendly_tooltip = $petsTooltips[$booking->ride->animal_friendly] ?? null;
            $booking->ride->luggage_image = $luggageImages[$booking->ride->luggage] ?? null;
            $booking->ride->luggage_tooltip = $luggageTooltips[$booking->ride->luggage] ?? null;

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
            $rideFeatures = is_string($booking->ride->features) ? explode('=', $booking->ride->features) : $booking->ride->features;
            // Loop through each feature and add the corresponding image and title
            foreach ($rideFeatures as $feature) {
                if (isset($feature) && is_scalar($feature) && isset($featureImages[$feature])) {
                    $features[] = $featureImages[$feature];
                }
            }
            // Assign the features array to the ride's features attribute
            $booking->ride->features = $features;

            // Calculate age
            if ($booking->ride->driver->dob) {
                $dob = Carbon::parse($booking->ride->driver->dob);
                $booking->ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $booking->ride->driver->age = null; // Handle case where dob is not set
            }

            if ($booking->ride->driver->gender) {
                if ($booking->ride->driver->gender === 'male') {
                    $booking->ride->driver->gender_label = $genderLabel->male_option_label;
                } elseif ($booking->ride->driver->gender === 'female') {
                    $booking->ride->driver->gender_label = $genderLabel->female_option_label;
                } elseif ($booking->ride->driver->gender === 'prefer not to say') {
                    $booking->ride->driver->gender_label = $genderLabel->prefer_option_label;
                }
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($booking) {
                return $rating->ride->added_by === $booking->ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $booking->ride->driver->average_rating = $totalAverage;

            $booking->ride->driver->driven_rides = $booking->ride->driver->rides()
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

            $booking->rating = Rating::where('type', '1')->where('ride_id', $booking->ride_id)->where('posted_by', $user_id)->first();
        }
        $setting = ReviewSetting::first();

        $data = ['bookings' => $bookings,'setting' => $setting];
        return $this->successResponse($data, 'Get my completed trips');
    }

    public function CancelledTrips(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $bookings = Booking::where('user_id', $user_id)->select('id', 'ride_id' , 'seats', 'status', 'booking_credit', 'fare', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price', 'booked_on','type')
            ->where('status', 4)
            ->whereHas('ride', function ($query) {
                $query->whereHas('driver', function ($query) {
                    $query->whereNull('deleted_at'); // Exclude soft-deleted drivers
                });
            })
            ->with(['ride.vehicle','ride' => function ($query) {
                $query->with(['driver' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
                }]);
            }])
            ->orderBy(Ride::select('date')
            ->whereColumn('rides.id', 'bookings.ride_id')
            ->limit(1), 'asc')
            ->orderBy(Ride::select('time')
            ->whereColumn('rides.id', 'bookings.ride_id')
            ->limit(1), 'asc')
            ->orderBy('ride_id', 'desc')
            ->paginate($request->paginate_limit);

        $selectedLanguage = session('selectedLanguage');
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

        foreach ($bookings as $booking) {
            // Calculate seats left
            $bookedSeats = $booking->ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->whereHas('passenger', function($query) {
                    $query->whereNull('deleted_at');
                })
                ->sum('seats');
            $booking->ride->seats_left = intval($booking->ride->seats) - intval($bookedSeats);

            $booking->ride->booking_method_image = $bookingMethodImages[$booking->ride->booking_method] ?? null;
            $booking->ride->booking_method_tooltip = $bookingMethodTooltips[$booking->ride->booking_method] ?? null;
            $booking->ride->payment_method_image = $paymentMethodImages[$booking->ride->payment_method] ?? null;
            $booking->ride->payment_method_tooltip = $paymentMethodTooltips[$booking->ride->payment_method] ?? null;
            $booking->ride->smoke_image = $smokeImages[$booking->ride->smoke] ?? null;
            $booking->ride->smoke_tooltip = $smokeTooltips[$booking->ride->smoke] ?? null;
            $booking->ride->animal_friendly_image = $petsImages[$booking->ride->animal_friendly] ?? null;
            $booking->ride->animal_friendly_tooltip = $petsTooltips[$booking->ride->animal_friendly] ?? null;
            $booking->ride->luggage_image = $luggageImages[$booking->ride->luggage] ?? null;
            $booking->ride->luggage_tooltip = $luggageTooltips[$booking->ride->luggage] ?? null;

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
            $rideFeatures = is_string($booking->ride->features) ? explode('=', $booking->ride->features) : $booking->ride->features;
            // Loop through each f
            foreach ($rideFeatures as $feature) {
                if (is_scalar($feature) && isset($featureImages) && isset($featureImages[$feature])) {
                    $features[] = $featureImages[$feature];
                }
            }
            // Assign the features array to the ride's features attribute
            $booking->ride->features = $features;

            // Calculate age
            if ($booking->ride->driver->dob) {
                $dob = Carbon::parse($booking->ride->driver->dob);
                $booking->ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $booking->ride->driver->age = null; // Handle case where dob is not set
            }

            if ($booking->ride->driver->gender) {
                if ($booking->ride->driver->gender === 'male') {
                    $booking->ride->driver->gender_label = $genderLabel->male_option_label;
                } elseif ($booking->ride->driver->gender === 'female') {
                    $booking->ride->driver->gender_label = $genderLabel->female_option_label;
                } elseif ($booking->ride->driver->gender === 'prefer not to say') {
                    $booking->ride->driver->gender_label = $genderLabel->prefer_option_label;
                }
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($booking) {
                return $rating->ride->added_by === $booking->ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $booking->ride->driver->average_rating = $totalAverage;

            $booking->ride->driver->driven_rides = $booking->ride->driver->rides()
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
        }
        $data = ['bookings' => $bookings];
        return $this->successResponse($data, 'Get my cancelled trips');
    }

    public function cancelBooking(Request $request){
        $user = Auth::guard('sanctum')->user();
        $booking = Booking::where('id', $request->booking_id)->first();
        
        $getSetting = SiteSetting::first();

        $taxAmt = 0;

        $getPaymentMethodId = FeaturesSetting::where('slug', 'cash')->value('id');

        if ($booking) {
            $request->validate([
                'cancel_seats' => 'required',
                'message' => 'required'
            ]);

            $ride = Ride::where('id', $booking->ride_id)->first();
            
            $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
            $bookingDateTime = Carbon::parse($booking->booked_on);

            $hoursDifference = $rideDateTime->diffInHours($bookingDateTime);

            if ($request->cancel_seats <= $booking->seats) {
                $type = FeaturesSetting::whereId($booking->type)->first();
                if ($booking->type == "37") {
                    $transactions = Transaction::where('booking_id', $booking->id)
                            ->where('type', '1')
                            ->get();

                    $totalPrice = $transactions->sum('price');

                    $getSeatPrice = $booking->fare / $booking->seats;
                    $getSeatBookingPrice = $booking->booking_credit / $booking->seats;
                    $refundBookingFee = $request->cancel_seats * $getSeatBookingPrice;
                    $refundAmount = $request->cancel_seats * $getSeatPrice;
                    $refundTotalAmount = $request->cancel_seats * $getSeatPrice;
                    $refundTotalBookingFee = $request->cancel_seats * ($booking->booking_credit / $booking->seats);

                    

                    // Step 2: Process each transaction for the refund
                    foreach ($transactions as $transaction) {

                        $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');

                        if(isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == ((double)$transaction->price - (double)$transaction->booking_fee)){
                            
                        }else{
                            $transactionAmount = ((double)$transaction->price - (double)$transaction->booking_fee);

                            if ($refundAmount <= 0) {
                                break; // No need to process further if refund is already completed
                            }
    
                            // Check if the current transaction can cover the remaining refund amount
                            if ($transactionAmount >= $refundAmount) {
                                
                                $newTransaction = Transaction::create([
                                    'booking_id' => $transaction->booking_id,
                                    'ride_id' => $booking->ride_id,
                                    'parent_id' => $transaction->id,
                                    'type' => '3',
                                    'price' => $refundAmount,
                                ]);
                                $refundAmount = 0; // Refund is completed
                                break;
                            } else {
    
                                $newTransaction = Transaction::create([
                                    'booking_id' => $transaction->booking_id,
                                    'ride_id' => $booking->ride_id,
                                    'parent_id' => $transaction->id,
                                    'type' => '3',
                                    'price' => $transactionAmount,
                                ]);
                                
                                $refundAmount -= $transactionAmount; // Reduce the remaining refund amount
                            }
                        }
                        
                    }
                    //Add Payout Data

                    $getPayout = Payout::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)->first();
                    if(isset($getPayout) && !is_null($getPayout)){

                    }else{
                        $getPayout = new Payout();
                    }
                    if(isset($getSetting->booking_fee_give_to_driver) && $getSetting->booking_fee_give_to_driver == 1){
                        $payoutAmt = $refundTotalAmount + $refundTotalBookingFee;
                    }else{
                        $payoutAmt = $refundTotalAmount;
                    }

                    if(isset($getSetting) && !empty($getSetting)){
                        if(isset($getSetting->deduct_tax) && $getSetting->deduct_tax == "deduct_from_driver"){
                            $deduct_tax = $getSetting->deduct_tax;
                            $tax_type = $getSetting->tax_type;
                            if(isset($getSetting->tax_type) && $getSetting->tax_type == "state_wise_tax"){
                                $locationBeforeComma = explode(',', $booking->departure);
                                $getFromState = City::with('state:id,tax')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ',['%'.$locationBeforeComma[0].'%'])->first();
                                if(isset($getFromState) && !empty($getFromState)){
                                    $tax = $getFromState->state->tax;
                                }
                            }else{
                                $tax = $getSetting->tax;  
                            }
                            
                            $taxAmt = round((($payoutAmt * $tax) / 100), 2);
                            $payoutAmt = $payoutAmt - $taxAmt;

                        }
                    }



                    if(isset($getPayout->amount)){
                        $payoutAmt = $getPayout->amount + $payoutAmt; 
                    }

                    $rideDateTime = Carbon::parse("$ride->completed_date $ride->completed_time");

                    $getPayout->ride_id = $booking->ride_id;
                    $getPayout->booking_id = $booking->id;
                    $getPayout->user_id = $ride->added_by;
                    $getPayout->amount = $payoutAmt;
                    $getPayout->available_date = $rideDateTime;
                    $getPayout->status = "pending";
                    $getPayout->tax_amount = $taxAmt;
                    $getPayout->tax_percentage = isset($tax) && $tax != 0 ? $tax : 0;
                    $getPayout->tax_type = isset($tax_type) && $tax_type != "" ? $tax_type : "";
                    $getPayout->deduct_type = isset($deduct_tax) && $deduct_tax != "" ? $deduct_tax : NULL;
                    $getPayout->save();
                    
                } elseif ($booking->type === '36') {

                    $transactions = Transaction::where('booking_id', $booking->id)
                            ->where('type', '1')
                            ->get();

                    $totalPrice = $transactions->sum('price');
                    $getSeatPrice = $booking->fare / $booking->seats;
                    $getSeatBookingPrice = $booking->booking_credit / $booking->seats;
                    $refundBookingFee = $request->cancel_seats * $getSeatBookingPrice;
                    $refundAmount = $request->cancel_seats * $getSeatPrice;
                    $refundTotalAmount = $request->cancel_seats * $getSeatPrice;
                    $refundTotalBookingFee = $request->cancel_seats * ($booking->booking_credit / $booking->seats);

                    if($hoursDifference > 48){

                        $refundAmount = $refundAmount + $refundBookingFee;
                        $refundTotalAmount = $refundTotalAmount + $refundBookingFee;
                        foreach ($transactions as $transaction) {

                            $checkPrice = 0.0;
                            if($ride->payment_method != $getPaymentMethodId){
                                $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');
                                $checkPrice = (double)$transaction->price;
                            }else{
                                $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('booking_fee');
                                $checkPrice = (double)$transaction->booking_fee;
                            }

                            

                            if(isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == $checkPrice){
                                
                            }else{
                                $transactionAmount = $ride->payment_method != $getPaymentMethodId ? (double)$transaction->price : (double)$transaction->booking_fee;

                                if ($refundAmount <= 0) {
                                    break; // No need to process further if refund is already completed
                                }
        
                                // Check if the current transaction can cover the remaining refund amount

                                $refundId = "";
                                if ($transactionAmount >= $refundAmount) {


                                    if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                                        $totalBookingFee = $getSeatBookingPrice * $booking->seats;
                                        if($transaction->booking_fee >= $totalBookingFee){
                                            $coffeeWallet = CoffeeWallet::create([
                                                'booking_id' => $booking->id,
                                                'ride_id' => $ride->id,
                                                'user_id' => $booking->user_id,
                                                'dr_amount' => $totalBookingFee,
                                            ]);
                                            //$refundAmount = $refundAmount - $totalBookingFee;
                                        }else{
                                            $coffeeWallet = CoffeeWallet::create([
                                                'booking_id' => $booking->id,
                                                'ride_id' => $booking->ride_id,
                                                'user_id' => $booking->user_id,
                                                'dr_amount' => $transaction->booking_fee,
                                            ]);
                                            //$refundAmount = $refundAmount - $transaction->booking_fee;
                                        }
                                        
                                    }


                                    if($transaction->pay_by_account == 0){
                                        if ($transaction->paypal_id) {

                                            try {
                                                $uniqueId = strtotime(date('Y-m-d H:i:s'));
                                                $paypal = new PayPalClient;
                                                $paypal->setApiCredentials(config('paypal'));
                                                $token = $paypal->getAccessToken();
                                                $paypal->setAccessToken($token);
                                                $response = $paypal->refundCapturedPayment(
                                                    $transaction->paypal_id,
                                                    'Invoice-' . $uniqueId,
                                                    $refundAmount,
                                                    'Refund issued.'
                                                );
    
                                                $refundId = isset($response['id']) ? $response['id'] : "";
                        
                                            } catch (\PayPal\Exception\PayPalConnectionException $e) {
                                                $errorData = json_decode($e->getData(), true);
                                                Log::error("PayPal error: " . $errorData['message']);   
                                            }
    
                                        } elseif ($transaction->stripe_id) {
                                            // Set your Stripe API key
                                            Stripe::setApiKey(env('STRIPE_SECRET'));
                        
                                            try {
                                                // Create a refund using the payment intent ID
                                                $refund = Refund::create([
                                                    'payment_intent' => $transaction->stripe_id,
                                                    'amount' => $refundAmount * 100, // Refund amount in cents
                                                ]);
    
                                                $refundId = $refund->id;
                        
                                            } catch (\Stripe\Exception\ApiErrorException $e) {
                                                
                                            }
                                        }
                                    }else{
                                        $topUpBalance = TopUpBalance::create([
                                            'booking_id' => $transaction->booking_id,
                                            'user_id' => $booking->user_id,
                                            'dr_amount' => $refundAmount,
                                            'added_date' => date('Y-m-d'),
                                        ]);
                                    }
                                    
                                    $newTransaction = Transaction::create([
                                        'booking_id' => $transaction->booking_id,
                                        'ride_id' => $booking->ride_id,
                                        'parent_id' => $transaction->id,
                                        'type' => '3',
                                        'price' => $ride->payment_method != $getPaymentMethodId ? $refundAmount : 0,
                                        'booking_fee' => $ride->payment_method == $getPaymentMethodId ? $refundAmount : 0,
                                        'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                        'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                                    ]);
                                    $refundAmount = 0; // Refund is completed
                                    break;
                                } else {


                                    if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                                        $totalBookingFee = $getSeatBookingPrice * $booking->seats;
                                        if($transaction->booking_fee >= $totalBookingFee){
                                            $coffeeWallet = CoffeeWallet::create([
                                                'booking_id' => $booking->id,
                                                'ride_id' => $ride->id,
                                                'user_id' => $booking->user_id,
                                                'dr_amount' => $totalBookingFee,
                                            ]);
                                            //$refundAmount = $refundAmount - $totalBookingFee;
                                        }else{
                                            $coffeeWallet = CoffeeWallet::create([
                                                'booking_id' => $booking->id,
                                                'ride_id' => $booking->ride_id,
                                                'user_id' => $booking->user_id,
                                                'dr_amount' => $transaction->booking_fee,
                                            ]);
                                            //$refundAmount = $refundAmount - $transaction->booking_fee;
                                        }
                                        
                                    }

                                    if($transaction->pay_by_account == 0){
                                        if ($transaction->paypal_id) {

                                            try {
                                                $uniqueId = strtotime(date('Y-m-d H:i:s'));
                                                $paypal = new PayPalClient;
                                                $paypal->setApiCredentials(config('paypal'));
                                                $token = $paypal->getAccessToken();
                                                $paypal->setAccessToken($token);
                                                $response = $paypal->refundCapturedPayment(
                                                    $transaction->paypal_id,
                                                    'Invoice-' . $uniqueId,
                                                    $transactionAmount,
                                                    'Refund issued.'
                                                );
    
                                                $refundId = isset($response['id']) ? $response['id'] : "";
                        
                                            } catch (\PayPal\Exception\PayPalConnectionException $e) {
                                                $errorData = json_decode($e->getData(), true);
                                                Log::error("PayPal error: " . $errorData['message']);   
                                            }
    
                                        } elseif ($transaction->stripe_id) {
                                            // Set your Stripe API key
                                            Stripe::setApiKey(env('STRIPE_SECRET'));
                        
                                            try {
                                                // Create a refund using the payment intent ID
                                                $refund = Refund::create([
                                                    'payment_intent' => $transaction->stripe_id,
                                                    'amount' => $transactionAmount * 100, // Refund amount in cents
                                                ]);
    
                                                $refundId = $refund->id;
                        
                                            } catch (\Stripe\Exception\ApiErrorException $e) {
                                                
                                            }
                                        }
                                    }else{
                                        $topUpBalance = TopUpBalance::create([
                                            'booking_id' => $transaction->booking_id,
                                            'user_id' => $booking->user_id,
                                            'dr_amount' => $transactionAmount,
                                            'added_date' => date('Y-m-d'),
                                        ]);
                                    }
        
                                    $newTransaction = Transaction::create([
                                        'booking_id' => $transaction->booking_id,
                                        'ride_id' => $booking->ride_id,
                                        'parent_id' => $transaction->id,
                                        'type' => '3',
                                        'price' => $ride->payment_method != $getPaymentMethodId ? $transactionAmount : 0,
                                        'booking_fee' => $ride->payment_method == $getPaymentMethodId ? $transactionAmount : 0,
                                        'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                        'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                                    ]);
                                    
                                    $refundAmount -= $transactionAmount; // Reduce the remaining refund amount
                                }
                            }
                            
                        }
                    }elseif($hoursDifference >= 12 && $hoursDifference <= 48){

                        if($ride->payment_method != $getPaymentMethodId){
                            $passengerAndDriverRefundAmt = $refundAmount * 0.5;
                            $passengerAndDriverRefundBookingFee = $refundTotalBookingFee * 0.5;


                            foreach ($transactions as $transaction) {


                                $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');

                                if(isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == ((double)$transaction->price - (double)$transaction->booking_fee)){
                                    
                                }else{
                                    $transactionAmount = $ride->payment_method != $getPaymentMethodId ? ((double)$transaction->price - (double)$transaction->booking_fee) : (double)$transaction->booking_fee;

                                    if ($refundAmount <= 0) {
                                        break; // No need to process further if refund is already completed
                                    }
            
                                    // Check if the current transaction can cover the remaining refund amount

                                    $refundId = "";
                                    if ($transactionAmount >= $refundAmount) {

                                        if($transaction->pay_by_account == 0){
                                            if ($transaction->paypal_id) {

                                                try {
                                                    $uniqueId = strtotime(date('Y-m-d H:i:s'));
                                                    $paypal = new PayPalClient;
                                                    $paypal->setApiCredentials(config('paypal'));
                                                    $token = $paypal->getAccessToken();
                                                    $paypal->setAccessToken($token);
                                                    $response = $paypal->refundCapturedPayment(
                                                        $transaction->paypal_id,
                                                        'USD',
                                                        $passengerAndDriverRefundAmt,
                                                        'Invoice-' . $uniqueId,
                                                    );
        
                                                    $refundId = isset($response['id']) ? $response['id'] : "";
                            
                                                } catch (\PayPal\Exception\PayPalConnectionException $e) {
                                                    $errorData = json_decode($e->getData(), true);
                                                    Log::error("PayPal error: " . $errorData['message']);   
                                                }
        
                                            } elseif ($transaction->stripe_id) {
                                                // Set your Stripe API key
                                                Stripe::setApiKey(env('STRIPE_SECRET'));
                            
                                                try {
                                                    // Create a refund using the payment intent ID
                                                    $refund = Refund::create([
                                                        'payment_intent' => $transaction->stripe_id,
                                                        'amount' => $passengerAndDriverRefundAmt * 100, // Refund amount in cents
                                                    ]);
        
                                                    $refundId = $refund->id;
                            
                                                } catch (\Stripe\Exception\ApiErrorException $e) {
                                                    
                                                }
                                            }
                                        }else{
                                            $topUpBalance = TopUpBalance::create([
                                                'booking_id' => $transaction->booking_id,
                                                'user_id' => $booking->user_id,
                                                'dr_amount' => $passengerAndDriverRefundAmt,
                                                'added_date' => date('Y-m-d'),
                                            ]);
                                        }

                                        //Passenger Entry
                                        $passengerTransaction = Transaction::create([
                                            'booking_id' => $transaction->booking_id,
                                            'ride_id' => $booking->ride_id,
                                            'parent_id' => $transaction->id,
                                            'type' => '3',
                                            'price' => $passengerAndDriverRefundAmt,
                                            'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                            'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                                        ]);
                                        //Driver Entry
                                        $driverTransaction = Transaction::create([
                                            'booking_id' => $transaction->booking_id,
                                            'ride_id' => $booking->ride_id,
                                            'parent_id' => $transaction->id,
                                            'type' => '3',
                                            'price' => $passengerAndDriverRefundAmt
                                        ]);
                                        $refundAmount = 0; // Refund is completed
                                        break;
                                    } else {

                                        $passengerAndDriverRefundAmt = $transactionAmount * 0.5;

                                        if($transaction->pay_by_account == 0){
                                            if ($transaction->paypal_id) {

                                                try {
                                                    $uniqueId = strtotime(date('Y-m-d H:i:s'));
                                                    $paypal = new PayPalClient;
                                                    $paypal->setApiCredentials(config('paypal'));
                                                    $token = $paypal->getAccessToken();
                                                    $paypal->setAccessToken($token);
                                                    $response = $paypal->refundCapturedPayment(
                                                        $transaction->paypal_id,
                                                        'USD',
                                                        $passengerAndDriverRefundAmt,
                                                        'Invoice-' . $uniqueId,
                                                    );
        
                                                    $refundId = isset($response['id']) ? $response['id'] : "";
                            
                                                } catch (\PayPal\Exception\PayPalConnectionException $e) {
                                                    $errorData = json_decode($e->getData(), true);
                                                    Log::error("PayPal error: " . $errorData['message']);   
                                                }
        
                                                
                                            } elseif ($transaction->stripe_id) {
                                                // Set your Stripe API key
                                                Stripe::setApiKey(env('STRIPE_SECRET'));
                            
                                                try {
                                                    // Create a refund using the payment intent ID
                                                    $refund = Refund::create([
                                                        'payment_intent' => $transaction->stripe_id,
                                                        'amount' => $passengerAndDriverRefundAmt * 100, // Refund amount in cents
                                                    ]);
        
                                                    $refundId = $refund->id;
                            
                                                } catch (\Stripe\Exception\ApiErrorException $e) {
                                                    
                                                }
                                            }
                                        }else{
                                            $topUpBalance = TopUpBalance::create([
                                                'booking_id' => $transaction->booking_id,
                                                'user_id' => $booking->user_id,
                                                'dr_amount' => $passengerAndDriverRefundAmt,
                                                'added_date' => date('Y-m-d'),
                                            ]);
                                        }
        
                                        //Passenger Transction 
                                        $passengerTransaction = Transaction::create([
                                            'booking_id' => $transaction->booking_id,
                                            'ride_id' => $booking->ride_id,
                                            'parent_id' => $transaction->id,
                                            'type' => '3',
                                            'price' => $passengerAndDriverRefundAmt,
                                            'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                            'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                                        ]);

                                        //Driver Transction
                                        $driverTransaction = Transaction::create([
                                            'booking_id' => $transaction->booking_id,
                                            'ride_id' => $booking->ride_id,
                                            'parent_id' => $transaction->id,
                                            'type' => '3',
                                            'price' => $passengerAndDriverRefundAmt
                                        ]);
                                        
                                        $refundAmount -= $transactionAmount; // Reduce the remaining refund amount
                                    }
                                }
                                
                            }

                            //Add Payout Data

                            $getPayout = Payout::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)->first();
                            if(isset($getPayout) && !is_null($getPayout)){

                            }else{
                                $getPayout = new Payout();
                            }

                            if(isset($getSetting->booking_fee_give_to_driver) && $getSetting->booking_fee_give_to_driver == 1){
                                $payoutAmt = $passengerAndDriverRefundAmt + $passengerAndDriverRefundBookingFee;
                            }else{
                                $payoutAmt = $passengerAndDriverRefundAmt;
                            }

                            if(isset($getSetting) && !empty($getSetting)){
                                if(isset($getSetting->deduct_tax) && $getSetting->deduct_tax == "deduct_from_driver"){
                                    $deduct_tax = $getSetting->deduct_tax;
                                    $tax_type = $getSetting->tax_type;
                                    if(isset($getSetting->tax_type) && $getSetting->tax_type == "state_wise_tax"){
                                        $locationBeforeComma = explode(',', $booking->departure);
                                        $getFromState = City::with('state:id,tax')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ',['%'.$locationBeforeComma[0].'%'])->first();
                                        if(isset($getFromState) && !empty($getFromState)){
                                            $tax = $getFromState->state->tax;
                                        }
                                    }else{
                                        $tax = $getSetting->tax;  
                                    }
                                    
                                    $taxAmt = round((($payoutAmt * $tax) / 100), 2);
                                    $payoutAmt = $payoutAmt - $taxAmt;

                                }
                            }

                            if(isset($getPayout->amount)){
                                $payoutAmt = $getPayout->amount + $payoutAmt; 
                            }

                            $rideDateTime = Carbon::parse("$ride->completed_date $ride->completed_time");

                            $getPayout->ride_id = $booking->ride_id;
                            $getPayout->booking_id = $booking->id;
                            $getPayout->user_id = $ride->added_by;
                            $getPayout->amount = $payoutAmt;
                            $getPayout->available_date = $rideDateTime;
                            $getPayout->status = "pending";        
                            $getPayout->tax_amount = $taxAmt;
                            $getPayout->tax_percentage = isset($tax) && $tax != 0 ? $tax : 0;
                            $getPayout->tax_type = isset($tax_type) && $tax_type != "" ? $tax_type : NULL;
                            $getPayout->deduct_type = isset($deduct_tax) && $deduct_tax != "" ? $deduct_tax : NULL;
                            $getPayout->save();
                        }

                    }elseif($hoursDifference < 12){

                        if($ride->payment_method != $getPaymentMethodId){
                            foreach ($transactions as $transaction) {

                                $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');
        
                                if(isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == ((double)$transaction->price - (double)$transaction->booking_fee)){
                                    
                                }else{
                                    $transactionAmount = ((double)$transaction->price - (double)$transaction->booking_fee);
        
                                    if ($refundAmount <= 0) {
                                        break; // No need to process further if refund is already completed
                                    }
            
                                    // Check if the current transaction can cover the remaining refund amount
                                    if ($transactionAmount >= $refundAmount) {
                                        
                                        $newTransaction = Transaction::create([
                                            'booking_id' => $transaction->booking_id,
                                            'ride_id' => $booking->ride_id,
                                            'parent_id' => $transaction->id,
                                            'type' => '3',
                                            'price' => $refundAmount,
                                        ]);
                                        $refundAmount = 0; // Refund is completed
                                        break;
                                    } else {
            
                                        $newTransaction = Transaction::create([
                                            'booking_id' => $transaction->booking_id,
                                            'ride_id' => $booking->ride_id,
                                            'parent_id' => $transaction->id,
                                            'type' => '3',
                                            'price' => $transactionAmount,
                                        ]);
                                        
                                        $refundAmount -= $transactionAmount; // Reduce the remaining refund amount
                                    }
                                }
                                
                            }
                            //Add Payout Data
        
                            $getPayout = Payout::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)->first();
                            if(isset($getPayout) && !is_null($getPayout)){
        
                            }else{
                                $getPayout = new Payout();
                            }
                            
                            if(isset($getSetting->booking_fee_give_to_driver) && $getSetting->booking_fee_give_to_driver == 1){
                                $payoutAmt = $refundTotalAmount + $refundTotalBookingFee;
                            }else{
                                $payoutAmt = $refundTotalAmount;
                            }

                            $getSetting = SiteSetting::first();
                            if(isset($getSetting) && !empty($getSetting)){
                                if(isset($getSetting->deduct_tax) && $getSetting->deduct_tax == "deduct_from_driver"){
                                    $deduct_tax = $getSetting->deduct_tax;
                                    $tax_type = $getSetting->tax_type;
                                    if(isset($getSetting->tax_type) && $getSetting->tax_type == "state_wise_tax"){
                                        $locationBeforeComma = explode(',', $getRide->departure);
                                        $getFromState = City::with('state:id,tax')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ',['%'.$locationBeforeComma[0].'%'])->first();
                                        if(isset($getFromState) && !empty($getFromState)){
                                            $tax = $getFromState->state->tax;
                                        }
                                    }else{
                                        $tax = $getSetting->tax;  
                                    }
                                    
                                    $taxAmt = round((($payoutAmt * $tax) / 100), 2);
                                    $payoutAmt = $payoutAmt - $taxAmt;

                                }
                            }

                            if(isset($getPayout->amount)){
                                $payoutAmt = $getPayout->amount + $payoutAmt; 
                            }
    
                            $rideDateTime = Carbon::parse("$ride->completed_date $ride->completed_time");
        
                            $getPayout->ride_id = $booking->ride_id;
                            $getPayout->booking_id = $booking->id;
                            $getPayout->user_id = $ride->added_by;
                            $getPayout->amount = $payoutAmt;
                            $getPayout->available_date = $rideDateTime;
                            $getPayout->status = "pending";    
                            $getPayout->tax_amount = $taxAmt;
                            $getPayout->tax_percentage = isset($tax) && $tax != 0 ? $tax : 0;
                            $getPayout->tax_type = isset($tax_type) && $tax_type != "" ? $tax_type : NULL;
                            $getPayout->deduct_type = isset($deduct_tax) && $deduct_tax != "" ? $deduct_tax : NULL;
                            $getPayout->save();
                        }
                    }
                }

                $updatedSeats = $booking->seats - $request->cancel_seats;
                $perSeatBookingCredit = $booking->booking_credit / $booking->seats;
                $updatedBookingCredit = $perSeatBookingCredit * $updatedSeats;
                $perSeatFare = $booking->fare / $booking->seats;
                $updatedFare = $perSeatFare * $updatedSeats;

                if($request->cancel_seats == $booking->seats){
                    $booking->update([
                        'status' => '4',
                    ]);
                }else{
                    $booking->update([
                        'seats' => $updatedSeats,
                        'booking_credit' => $updatedBookingCredit,
                        'fare' => $updatedFare
                    ]);
                }

                $getSeatDetails = SeatDetail::where('booking_id', $booking->id)->get();
                $cancelSeatsCount = $request->cancel_seats;
                if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                    foreach ($getSeatDetails->take($cancelSeatsCount) as $key => $getSeatDetail) {
                        $getSeatDetail->status = 'pending';
                        $getSeatDetail->booking_id = NULL;
                        $getSeatDetail->user_id = NULL;
                        $getSeatDetail->save();
                    }
                }

                CancellationHistory::create([
                    'ride_id' => $booking->ride_id,
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'type' => 'passenger',
                ]);

                $notification = Notification::create([
                    'ride_id' => $booking->ride_id,
                    'posted_by' => $booking->user_id,
                    'message' =>  'Booking cancelled',
                    'status' => 'cancelled',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);

                // Assuming $user and $fcmToken are defined
                $fcmToken = $booking->ride->driver->mobile_fcm_token;
                $body = $notification->message;
    
                if ($fcmToken) {
                    $fcmService = new FCMService();
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }
            }

            $data = ['driver_name' => $booking->ride->driver->first_name,'passenger_name' => $booking->passenger->first_name, 'seats' => $booking->seats, 'cancelled_searts' => $request->cancel_seats, 'price' => $booking->price, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($booking->ride->date)->format('F d, Y') ,'time' => $booking->ride->time];
            // Send email to driver
            Mail::to($booking->ride->driver->email)->queue(new PassengerCancelBookingMail($data));


            $message = Message::create([
                'ride_id' => $booking->ride->id,
                'receiver' => $booking->ride->added_by,
                'sender' => $booking->user_id,
                'message' => $request->input('message'),
                'ride_detail_id' => $booking->ride_detail_id
            ]);

            $phoneNumber = PhoneNumber::where('user_id', $booking->ride->added_by)->where('verified', '1')->where('default', '1')->first();

            if (!$phoneNumber) {
                $phoneNumber = PhoneNumber::where('user_id', $booking->ride->added_by)->where('verified', '1')->first();
            }

            if ($phoneNumber && env('APP_ENV') != 'local') {
                $passengerName = $booking->passenger->first_name;

                // Send the secured cash code via Twilio
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

                $message = "".$title."\nPassenger has cancelled seat from your ride\nTrip detail\nOrigin: ".$booking->departure."\nDestination: ".$booking->destination."\nDeparture date: ".$depatureDate."\Passenger name: ".$booking->passenger->first_name."\Passenger phone number: ".$booking->passenger->phone."";
        
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
            
                    // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                }
            }

            $ride_time = strtotime($ride->time);
            $current_time = time();
            $current_date = date('Y-m-d');
            $time_left = $ride_time - $current_time;
            if ($current_date == date('Y-m-d', strtotime($ride->data)) && $time_left <= 3600) {
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
            $messages = null;
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
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_booking_message','general_error_message')->first();
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
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_booking_message','general_error_message')->first();
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

            // Calculate seats left
            $bookedSeats = $booking->ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->whereHas('passenger', function($query) {
                    $query->whereNull('deleted_at');
                })
                ->sum('seats');
            $booking->ride->seats_left = intval($booking->ride->seats) - intval($bookedSeats);

            $booking->ride->booking_method_image = $bookingMethodImages[$booking->ride->booking_method] ?? null;
            $booking->ride->booking_method_tooltip = $bookingMethodTooltips[$booking->ride->booking_method] ?? null;
            $booking->ride->payment_method_image = $paymentMethodImages[$booking->ride->payment_method] ?? null;
            $booking->ride->payment_method_tooltip = $paymentMethodTooltips[$booking->ride->payment_method] ?? null;
            $booking->ride->smoke_image = $smokeImages[$booking->ride->smoke] ?? null;
            $booking->ride->smoke_tooltip = $smokeTooltips[$booking->ride->smoke] ?? null;
            $booking->ride->animal_friendly_image = $petsImages[$booking->ride->animal_friendly] ?? null;
            $booking->ride->animal_friendly_tooltip = $petsTooltips[$booking->ride->animal_friendly] ?? null;
            $booking->ride->luggage_image = $luggageImages[$booking->ride->luggage] ?? null;
            $booking->ride->luggage_tooltip = $luggageTooltips[$booking->ride->luggage] ?? null;

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
            $rideFeatures = is_string($booking->ride->features) ? explode('=', $booking->ride->features) : $booking->ride->features;
            // Loop through each feature and add the corresponding image and title
            foreach ($rideFeatures as $feature) {
                if (isset($featureImages[$feature])) {
                    $features[] = $featureImages[$feature];
                }
            }
            // Assign the features array to the ride's features attribute
            $booking->ride->features = $features;

            // Calculate age
            if ($booking->ride->driver->dob) {
                $dob = Carbon::parse($booking->ride->driver->dob);
                $booking->ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $booking->ride->driver->age = null; // Handle case where dob is not set
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($booking) {
                return $rating->ride->added_by === $booking->ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $booking->ride->driver->average_rating = $totalAverage;

            $booking->ride->driver->driven_rides = $booking->ride->driver->rides()
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
            $data = ['booking' => $booking];
            return $this->successResponse($data, $messages->cancel_booking_message ?? null);
        }

        return $this->apiErrorResponse($messages->general_error_message ?? "Booking not found", 404);
    }

    public function tripsIndex(Request $request)
    {
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

        $data = ['tripsPage' => $tripsPage, 'rideDetailPage' => $rideDetailPage];
        return $this->successResponse($data, 'My trips page get successfully');
    }
}
