<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\AcceptBookingRequestMail;
use App\Mail\BookingRequestConfirmationMail;
use App\Mail\BookingRequestMail;
use App\Mail\DriverDetailsMail;
use App\Mail\PassengerDetailsMail;
use App\Mail\PaymentInvoiceMail;
use App\Mail\RejectBookingRequestMail;
use App\Models\Booking;
use App\Models\BookingPageSettingDetail;
use App\Models\Card;
use App\Models\RideDetail;
use App\Models\FeaturesSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\PostRidePageSettingDetail;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\City;
use App\Models\SiteSetting;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SeatDetail;
use App\Models\TopUpBalance;
use App\Models\CoffeeWallet;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Stripe\Stripe;
use Twilio\Rest\Client;
use App\Jobs\UpdateSeatOnHold;
use App\Mail\SecuredCashPaymentCodeMail;
use App\Models\FeaturesSettingDetail;
use App\Models\Message;

class BookingController extends Controller
{
    use StatusResponser;

    public function create(Request $request){
        $user = Auth::guard('sanctum')->user();

        $findRidePage = null;
        $postRidePage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
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
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('need_to_select_card_message', 'paypal_not_completed_message', 'acc_suspend_message', 'seat_unavailable_message','general_error_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
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
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('need_to_select_card_message', 'paypal_not_completed_message', 'acc_suspend_message', 'seat_unavailable_message', 'general_error_message')->first();
            }
        }

        // Check if user has suspanded
        if ($user->suspand === '1') {
            return $this->apiErrorResponse($messages->acc_suspend_message ?? null, 200);
        }

        $rideDetailId = isset($request->ride_detail_id) ? $request->ride_detail_id : 0;

        $ride = Ride::where('id', $request->id);

        if($rideDetailId != 0){
            $ride = $ride->with(['rideDetail' => function($q) use($rideDetailId){
                $q->where('id',$rideDetailId);
            }]);
        }else{
            $ride = $ride->with(['rideDetail' => function($q){
                $q->where('default_ride','1');
            }]);
        }

        $ride = $ride->with(['driver' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])->with('pendingSeatDetail')
            ->with(['bookings' => function ($query) {
                // Select specific columns from bookings
                $query->select('id', 'ride_id', 'seats', 'user_id', 'type', 'secured_cash_attempt_count', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
                    ->where('status', '<>', 3)
                    ->where('status', '<>', 4)
                    ->whereHas('passenger', function ($query) {
                        $query->whereNull('deleted_at');
                    })
                    ->with('transaction_no_coffee_sum')
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'profile_image'); 
                    }]);
            }]);

            $ride = $ride->first();

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
            $ride->booking_method_slug = FeaturesSetting::whereId($ride->booking_method)->value('slug') ?? null;
            $ride->booking_type_slug = FeaturesSetting::whereId($ride->booking_type)->value('slug') ?? null;
            $ride->payment_method_image = $paymentMethodImages[$ride->payment_method] ?? null;
            $ride->payment_method_tooltip = $paymentMethodTooltips[$ride->payment_method] ?? null;
            $ride->payment_method_slug = FeaturesSetting::whereId($ride->payment_method)->value('slug') ?? null;
            $ride->smoke_image = $smokeImages[$ride->smoke] ?? null;
            $ride->smoke_tooltip = $smokeTooltips[$ride->smoke] ?? null;
            $ride->animal_friendly_image = $petsImages[$ride->animal_friendly] ?? null;
            $ride->animal_friendly_tooltip = $petsTooltips[$ride->animal_friendly] ?? null;
            $ride->luggage_image = $luggageImages[$ride->luggage] ?? null;
            $ride->luggage_tooltip = $luggageTooltips[$ride->luggage] ?? null;
    
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


            $ride->feature_ids = $ride->features;
    
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

            if ($ride->driver->gender) {
                $ride->driver->gender = $ride->driver->gender;

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
        }

        $setting = SiteSetting::first();
        $stateTax = 0;
        if(isset($setting->deduct_tax) && $setting->deduct_tax == "deduct_from_passenger" && $setting->tax_type == "state_wise_tax"){
            $locationBeforeComma = explode(',', $ride->rideDetail[0]->departure);
            $getFromState = City::with('state:id,tax')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ',['%'.$locationBeforeComma[0].'%'])->first();
            if(isset($getFromState) && !empty($getFromState)){
                $stateTax = $getFromState->state->tax;
            }
        }

        $bookings = Booking::where('ride_id', $request->id)->where('status', '!=', '3')->where('status', '!=', '4')->get();

        $seatsBooked = $bookings->sum('seats');
        if ($seatsBooked >= $ride->seats) {
            return $this->apiErrorResponse($messages->seat_unavailable_message ?? null, 200);
        }


        $getCrBalance = TopUpBalance::where('user_id', $user->id)->sum('cr_amount');
        $getDrBalance = TopUpBalance::where('user_id', $user->id)->sum('dr_amount');


        
        $getCoffeeCrBalance = CoffeeWallet::sum('cr_amount');
        $getCoffeeDrBalance = CoffeeWallet::sum('dr_amount');

        $bookingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $bookingPage = BookingPageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $bookingPage = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }
        $data = ['ride' => $ride, 'messages' => $messages, 'setting' => $setting, 'bookingPage' => $bookingPage, 'balance' => ($getDrBalance - $getCrBalance) , 'coffeeBalance' => ($getCoffeeDrBalance - $getCoffeeCrBalance), 'stateTax' => $stateTax];
        return $this->successResponse($data, 'Get booking page successfully');
    }

    public function instantBooking(Request $request){
        $rideDetailId = isset($request->ride_detail_id) ? $request->ride_detail_id : 0;

        $ride = Ride::where('id', $request->id);
        if($rideDetailId != 0){
            $ride = $ride->with(['rideDetail' => function($q) use($rideDetailId){
                $q->where('id',$rideDetailId);
            }]);
        }else{
            $ride = $ride->with(['rideDetail' => function($q){
                $q->where('default_ride','1');
            }]);
        }
        
        $ride = $ride->first();

        
        $getPaymentMethodId = FeaturesSetting::where('slug', 'cash')->value('id');
        
        if ($ride) {
            $user = Auth::guard('sanctum')->user();
            $user_id = $user->id;
            $user = User::where('id', $user_id)->first();

            $messages = null;
            $findRidePage = null;
            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                if ($selectedLanguage) {
                    // Retrieve the HomePageSettingDetail associated with the selected language
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message', 'book_seat_message_end_part', 'seat_unavailable_message', 'verified_number_message','general_error_message','card_expiry_message','block_booking_message')->first();
                    $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message', 'book_seat_message_end_part', 'seat_unavailable_message', 'verified_number_message','general_error_message','card_expiry_message','block_booking_message')->first();
                    $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                }
            }

            if ($user->block_booking == '1') {
                return $this->apiErrorResponse(strip_tags($message->block_booking_message ?? null), 200);
            }

            $taxAmt = isset($request->tax_amount) ? $request->tax_amount : 0;
    
    
            $bookings = Booking::where('ride_id', $request->id)->where('status', '!=', '3')->where('status', '!=', '4')->get();
    
            $seatsBooked = $bookings->sum('seats') + $request->seats;
            if ($seatsBooked > $ride->seats) {
                return $this->apiErrorResponse($messages->seat_unavailable_message ?? null, 200);
            }
    
            $validated = $request->validate([
                'payment_method' => $request->online_payment > '0' ? 'required' : 'nullable',
                'paypal_id' => $request->online_payment > '0' && $request->payment_method == 'paypal' ? 'required' : 'nullable',
                'card_id' => $request->online_payment > '0' && $request->payment_method == 'credit_card' ? 'required' : 'nullable',
                'booking_credit' => 'required|max:25',
                'seats' => 'required',
                'type' => 'required',
                'seats_amount' => 'required',
                'cash_payment' => 'required',
                'online_payment' => 'required',
                'total' => 'required',
            ]);

            $type = FeaturesSetting::whereId($request->type)->first();
            if (isset($type) && $type->slug === 'firm') {
                $setting = SiteSetting::first();
                $seat_price = $ride->rideDetail[0]->price - ($ride->rideDetail[0]->price * $setting->frim_discount / 100);
            } else {
                $seat_price = $ride->rideDetail[0]->price;
            }
            $booking_credit = $request->booking_credit;

            if ($request->online_payment > '0') {
                if ($request->payment_method == 'paypal') {
                    if ($ride->payment_method == "35" && $ride->booking_method == "31") {
                        $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->where('default', '1')->first();
                        if (!$phoneNumber) {
                            $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
                        }
                        if (!$phoneNumber) {
                            return $this->apiErrorResponse("Phone numner required for secured cash payment", 404);
                        }
            
                        $secured_cash = '1';
                        $secured_cash_code = rand(1000, 9999);
            
                        if ($phoneNumber && env('APP_ENV') != 'local') {
                            $sid = env('TWILIO_ACCOUNT_SID');
                            $token = env('TWILIO_AUTH_TOKEN');
                            $from = env('TWILIO_PHONE_NUMBER');
            
                            $twilio = new Client($sid, $token);
                            $to = $phoneNumber->phone;
                            $title = "";
                            $currentHour = date('H');
                            if ($currentHour >= 0 && $currentHour < 12) {
                                $title = "Good morning " . $user->first_name . "";
                            } elseif ($currentHour >= 12 && $currentHour < 17) {
                                $title = "Good afternoon " . $user->first_name . "";
                            } else {
                                $title = "Good evening " . $user->first_name . "";
                            }
            
                            $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
            
                            $message = "" . $title . "\nYour secured cash code is: $secured_cash_code\nTrip detail\nOrigin: " . $ride->rideDetail[0]->departure . "\nDestination: " . $ride->rideDetail[0]->destination . "\nDeparture date: " . $depatureDate . "\Driver name: " . $ride->driver->first_name . "\nDriver phone number: " . $ride->driver->phone . "\nVehicle info: " . $ride->make ?? '' . "," . $ride->year ?? '' . "," . $ride->modal ?? '' . "\nVehicle type: " . $ride->car_type . "";
            
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

                        if ($secured_cash_code && isset($user->email_notification) && $user->email_notification == 1) {
                            $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                            ->where('default', '1')
                            ->first();
                            $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;

                            $emailData = [
                                'first_name' => $user->first_name,
                                'secured_cash_code' => $secured_cash_code,
                                'driver_first_name' => $ride->driver->first_name,
                                'driver_last_name' => $ride->driver->last_name,
                                'driver_phone' => $driverPhoneToUse,
                                'driver_email' => $ride->driver->email,
                                'departure' => $ride->rideDetail[0]->departure,
                                'destination' => $ride->rideDetail[0]->destination,
                                'date' => Carbon::parse($ride->date)->format('F d, Y'),
                                'time' => $ride->time,
                                'seats' => $request->seats,
                                'booking_price' => $ride->rideDetail[0]->price * $request->seats
                            ];
                    
                            Mail::to($user->email)->queue(new SecuredCashPaymentCodeMail($emailData));
                        }
                    }else{
                        $secured_cash = null;
                        $secured_cash_code = null;
                    }
                    // Payment successful, handle booking logic here
                    $booking = Booking::create([
                        'user_id' => $user->id,
                        'ride_id' => $request->id,
                        'seats' => $request->seats,
                        'type' => $request->type,
                        'booked_on' => Carbon::now(),
                        'status' => '1',
                        'booking_credit' => $booking_credit,
                        'fare' => $seat_price * $request->seats,
                        'tax_amount' => $taxAmt,
                        'secured_cash' => $secured_cash,
                        'secured_cash_code' => $secured_cash_code,
                        'departure' => $ride->rideDetail[0]->departure,
                        'destination' => $ride->rideDetail[0]->destination,
                        'price' => $ride->rideDetail[0]->price,
                        'ride_detail_id' => $ride->rideDetail[0]->id
                    ]);

                    
    
                    $getBookingFeeSum = Transaction::where('booking_id', $booking->id)->sum('booking_fee');
                    $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);

                    if($ride->payment_method == $getPaymentMethodId){
                        $transaction = Transaction::create([
                            'booking_id' => $booking->id,
                            'type' => '1',
                            'booking_fee' => $currentBookingFee,
                            'price' => '0',
                            'paypal_id' => $request->g_pay == "1" ? NULL : $request->paypal_id,
                            'stripe_id' => $request->g_pay == "1" ? $request->paypal_id : NULL,
                            'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                            'tax_amount' => $taxAmt,
                            'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                            'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                            'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax != "" ? $request->deduct_tax : NULL,
                        ]);

                        if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'cr_amount' => $currentBookingFee,
                            ]);
                        }
                    }else{
                        $transaction = Transaction::create([
                            'booking_id' => $booking->id,
                            'type' => '1',
                            'booking_fee' => $currentBookingFee,
                            'price' => $request->input('online_payment'),
                            'paypal_id' => $request->g_pay == "1" ? NULL : $request->paypal_id,
                            'stripe_id' => $request->g_pay == "1" ? $request->paypal_id : NULL,
                            'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                            'tax_amount' => $taxAmt,
                            'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                            'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                            'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax != "" ? $request->deduct_tax : NULL,
                        ]);

                        if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'cr_amount' => $currentBookingFee,
                            ]);
                        }
                    }

                    
    
                    $notification = Notification::create([
                        'ride_id' => $request->id,
                        'posted_by' => $user->id,
                        'message' => 'You have a new instant booking from ' . $user->first_name . "\n" . 
                        'Seats booked: ' . numberToWords($request->seats),
                        'status' => 'completed',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $ride->rideDetail[0]->id,
                        'departure' => $ride->rideDetail[0]->departure,
                        'destination' => $ride->rideDetail[0]->destination
                    ]);
    
                    // Assuming $user and $fcmToken are defined
                    $fcmToken = $ride->driver->mobile_fcm_token;
                    $body = $notification->message;
    
                    if ($fcmToken) {
                        $fcmService = new FCMService();
                        // Send the booking notification
                        $fcmService->sendNotification($fcmToken, $body);
                    }
            
                    $notification = Notification::create([
                        'type' => 2,
                        'ride_id' => $request->id,
                        'posted_to' => $booking->id,
                        'posted_by' => $ride->added_by,
                        'message' => 'Your booking details' . "\n" . 'Seats booked: ' . numberToWords($request->seats),
                        'status' => 'completed',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $ride->rideDetail[0]->id,
                        'departure' => $ride->rideDetail[0]->departure,
                        'destination' => $ride->rideDetail[0]->destination
                    ]);
    
                    // Assuming $user and $fcmToken are defined
                    $fcmToken = $user->mobile_fcm_token;
                    $body = $notification->message;
    
                    if ($fcmToken) {
                        $fcmService = new FCMService();
                        // Send the booking notification
                        $fcmService->sendNotification($fcmToken, $body);
                    }
            
                    $bookingPrice = $seat_price * $booking->seats;

                    // $data = ['first_name' => $ride->driver->first_name, 'passenger_first_name' => $user->first_name, 'secured_cash_code' => $secured_cash_code];
                    // Mail::to($ride->driver->email)->queue(new InstantBookingMail($data));
            
                    $data = ['first_name' => $ride->driver->first_name, 'lang' => $selectedLanguage->abbreviation, 'origin' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'time' => $ride->time, 'seats' => $booking->seats, 'booking_price' => $seat_price, 'total_price' => $bookingPrice, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'email' => $user->email, 'phone' => $user->phone];
                    Mail::to($ride->driver->email)->queue(new PassengerDetailsMail($data));
            
                    $data = ['first_name' => $user->first_name, 'driver_first_name' => $ride->driver->first_name, 'driver_last_name' => $ride->driver->last_name, 'gender' => $ride->driver->gender, 'email' => $ride->driver->email, 'phone' => $ride->driver->phone, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];
                    Mail::to($user->email)->queue(new DriverDetailsMail($data));
            
                    $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $request->seats_amount, 'booking_credit' => $booking->booking_credit, 'online_payment' => $request->online_payment, 'cash_payment' => $request->cash_payment, 'total' => $request->total];
                    Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
            
                    $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

                    if (!$phoneNumber) {
                        $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
                    }

                    if ($phoneNumber && env('APP_ENV') != 'local') {
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

                        $message = "".$title."\n".$user->first_name." has booked seat in your ride\nTrip detail\nOrigin: ".$booking->departure."\nDestination: ".$booking->destination."\nDeparture date: ".$depatureDate."\nPassenger phone number: ".$user->phone."";
        
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

                    $ids = json_decode($request->booked_seat_ids, true);
                    $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                    if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                        foreach ($getSeatDetails as $key => $getSeatDetail) {
                            $getSeatDetail->status = 'booked';
                            $getSeatDetail->booking_id = $booking->id;
                            $getSeatDetail->user_id = $booking->user_id;
                            $getSeatDetail->save();
                        }
                    }

                    // Check the ride first message
                    $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
                        $query->where('sender', $ride->added_by)
                            ->where('receiver', $booking->user_id);
                    })->orWhere(function ($query) use ($booking, $ride) {
                        $query->where('sender', $booking->user_id)
                            ->where('receiver', $ride->added_by);
                    })->where('ride_id', $ride->id)->first();
                    if (empty($rideFirstMessage)) {
                        $message1 = Message::create([
                            'ride_id' => $ride->id,
                            'receiver' => $ride->added_by,
                            'sender' => $booking->user_id,
                            'message' => $request->driver_message,
                            'redirect' => '1',
                            'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                        ]);
                    }
                    $message = Message::create([
                        'ride_id' => $ride->id,
                        'receiver' => $ride->added_by,
                        'sender' => $booking->user_id,
                        'message' => $request->driver_message,
                        'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                    ]);

                    $data = ['booking' => $booking];
                    return $this->successResponse($data, $messages->book_seat_message . ' ' . $request->seats . ' ' . $messages->book_seat_message_end_part);
                } elseif ($request->payment_method == 'credit_card') {
        
                    try {

                        $paymentIntent = null;

                        if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                        }else{
                            // Retrieve the selected card from the database
                            $card = Card::where('id', $request->card_id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

                            // Set your Stripe API key.
                            Stripe::setApiKey(env('STRIPE_SECRET'));
                            // Attach the payment method to the customer
                            $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                            $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                            $stripePay = $request->input('online_payment') + $taxAmt;
                            
                            // Create a payment intent
                            $paymentIntent = PaymentIntent::create([
                                'amount' => round(($stripePay * 100), 0),
                                'currency' => 'usd',
                                'customer' => $user->stripe_customer_id,
                                'payment_method' => $paymentMethod->id,
                                'off_session' => true,
                                'confirm' => true,
                            ]);
                        }
                        
                        if ($ride->payment_method == "35" && $ride->booking_method == "31") {
                            $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->where('default', '1')->first();
                            if (!$phoneNumber) {
                                $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
                            }
                            if (!$phoneNumber) {
                                return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $messages->verified_number_message ?? null]);
                            }
                
                            $secured_cash = '1';
                            $secured_cash_code = rand(1000, 9999);
                
                            if ($phoneNumber && env('APP_ENV') != 'local') {
                                $sid = env('TWILIO_ACCOUNT_SID');
                                $token = env('TWILIO_AUTH_TOKEN');
                                $from = env('TWILIO_PHONE_NUMBER');
                
                                $twilio = new Client($sid, $token);
                                $to = $phoneNumber->phone;
                                $title = "";
                                $currentHour = date('H');
                                if ($currentHour >= 0 && $currentHour < 12) {
                                    $title = "Good morning " . $user->first_name . "";
                                } elseif ($currentHour >= 12 && $currentHour < 17) {
                                    $title = "Good afternoon " . $user->first_name . "";
                                } else {
                                    $title = "Good evening " . $user->first_name . "";
                                }
                
                                $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                
                                $message = "" . $title . "\nYour secured cash code is: $secured_cash_code\nTrip detail\nOrigin: " . $ride->rideDetail[0]->departure . "\nDestination: " . $ride->rideDetail[0]->destination . "\nDeparture date: " . $depatureDate . "\Driver name: " . $ride->driver->first_name . "\nDriver phone number: " . $ride->driver->phone . "\nVehicle info: " . $ride->make ?? '' . "," . $ride->year ?? '' . "," . $ride->modal ?? '' . "\nVehicle type: " . $ride->car_type . "";
                
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

                            if ($secured_cash_code && isset($user->email_notification) && $user->email_notification == 1) {
                                $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                                ->where('default', '1')
                                ->first();
                                $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;

                                $emailData = [
                                    'first_name' => $user->first_name,
                                    'secured_cash_code' => $secured_cash_code,
                                    'driver_first_name' => $ride->driver->first_name,
                                    'driver_last_name' => $ride->driver->last_name,
                                    'driver_phone' => $driverPhoneToUse,
                                    'driver_email' => $ride->driver->email,
                                    'departure' => $ride->rideDetail[0]->departure,
                                    'destination' => $ride->rideDetail[0]->destination,
                                    'date' => Carbon::parse($ride->date)->format('F d, Y'),
                                    'time' => $ride->time,
                                    'seats' => $request->seats,
                                    'booking_price' => $ride->rideDetail[0]->price * $request->seats
                                ];
                    
                                Mail::to($user->email)->queue(new SecuredCashPaymentCodeMail($emailData));
                            }
                        }else{
                            $secured_cash = null;
                            $secured_cash_code = null;
                        }
        
                        // Payment successful, handle booking logic here
                        $booking = Booking::create([
                            'user_id' => $user->id,
                            'ride_id' => $request->id,
                            'seats' => $request->seats,
                            'type' => $request->type,
                            'booked_on' => Carbon::now(),
                            'status' => '1',
                            'booking_credit' => $booking_credit,
                            'fare' => $seat_price * $request->seats,
                            'tax_amount' => $taxAmt,
                            'secured_cash' => $secured_cash,
                            'secured_cash_code' => $secured_cash_code,    
                            'departure' => $ride->rideDetail[0]->departure,
                            'destination' => $ride->rideDetail[0]->destination,
                            'price' => $ride->rideDetail[0]->price,
                            'ride_detail_id' => $ride->rideDetail[0]->id
                        ]);

                        $getBookingFeeSum = Transaction::where('booking_id', $booking->id)->sum('booking_fee');
                        $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);
        

                        if($ride->payment_method == $getPaymentMethodId){
                            $transaction = Transaction::create([
                                'booking_id' => $booking->id,
                                'type' => '1',
                                'booking_fee' => $currentBookingFee,
                                'price' => '0',
                                'stripe_id' => isset($paymentIntent) && $paymentIntent != null ? $paymentIntent->id : NULL,
                                'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                                'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                                'tax_amount' => $taxAmt,
                                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax != "" ? $request->deduct_tax : NULL, 
                            ]);

                            if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                                $coffeeWallet = CoffeeWallet::create([
                                    'booking_id' => $booking->id,
                                    'ride_id' => $ride->id,
                                    'user_id' => $booking->user_id,
                                    'cr_amount' => $currentBookingFee,
                                ]);
                            }
                        }else{

                            $transaction = Transaction::create([
                                'booking_id' => $booking->id,
                                'type' => '1',
                                'booking_fee' => $currentBookingFee,
                                'price' => $request->input('online_payment'),
                                'stripe_id' => isset($paymentIntent) && $paymentIntent != null ? $paymentIntent->id : NULL,
                                'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                                'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                                'tax_amount' => $taxAmt,
                                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax != "" ? $request->deduct_tax : NULL,
                            ]);

                            if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                                $coffeeWallet = CoffeeWallet::create([
                                    'booking_id' => $booking->id,
                                    'ride_id' => $ride->id,
                                    'user_id' => $booking->user_id,
                                    'cr_amount' => $currentBookingFee,
                                ]);
                            }
                        }
                        
                        if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                            $topUpBalance = TopUpBalance::create([
                                'booking_id' => $booking->id,
                                'user_id' => $user->id,
                                'cr_amount' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? ($request->online_payment - $currentBookingFee) + $taxAmt : $request->online_payment + $taxAmt,
                                'added_date' => date('Y-m-d'),
                            ]);
                        }

                        
        
                        $notification = Notification::create([
                            'ride_id' => $request->id,
                            'posted_by' => $user->id,
                            'message' => 'You have a new instant booking from ' . $user->first_name . "\n" . 
                            'Seats booked: ' . numberToWords($request->seats),
                            'status' => 'completed',
                            'notification_type' => 'upcoming',
                            'ride_detail_id' => $ride->rideDetail[0]->id,
                            'departure' => $ride->rideDetail[0]->departure,
                            'destination' => $ride->rideDetail[0]->destination
                        ]);
    
                        // Assuming $user and $fcmToken are defined
                        $fcmToken = $ride->driver->mobile_fcm_token;
                        $body = $notification->message;
    
                        if ($fcmToken) {
                            $fcmService = new FCMService();
                            // Send the booking notification
                            $fcmService->sendNotification($fcmToken, $body);
                        }
                
                        $notification = Notification::create([
                            'type' => 2,
                            'ride_id' => $request->id,
                            'posted_to' => $booking->id,
                            'posted_by' => $ride->added_by,
                            'message' => 'Your booking details' . "\n" . 'Seats booked: ' . numberToWords($request->seats),
                            'status' => 'completed',
                            'notification_type' => 'upcoming',
                            'ride_detail_id' => $ride->rideDetail[0]->id,
                            'departure' => $ride->rideDetail[0]->departure,
                            'destination' => $ride->rideDetail[0]->destination
                        ]);
    
                        // Assuming $user and $fcmToken are defined
                        $fcmToken = $user->mobile_fcm_token;
                        $body = $notification->message;
    
                        if ($fcmToken) {
                            $fcmService = new FCMService();
                            // Send the booking notification
                            $fcmService->sendNotification($fcmToken, $body);
                        }
                
                        $bookingPrice = $seat_price * $booking->seats;

                        // $data = ['first_name' => $ride->driver->first_name, 'passenger_first_name' => $user->first_name, 'secured_cash_code' => $secured_cash_code];
                        // Mail::to($ride->driver->email)->queue(new InstantBookingMail($data));
                
                        $data = ['first_name' => $ride->driver->first_name, 'lang' => $selectedLanguage->abbreviation, 'origin' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'time' => $ride->time, 'seats' => $booking->seats, 'booking_price' => $seat_price, 'total_price' => $bookingPrice, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'email' => $user->email, 'phone' => $user->phone];
                        Mail::to($ride->driver->email)->queue(new PassengerDetailsMail($data));
                
                        $data = ['first_name' => $user->first_name, 'driver_first_name' => $ride->driver->first_name, 'driver_last_name' => $ride->driver->last_name, 'gender' => $ride->driver->gender, 'email' => $ride->driver->email, 'phone' => $ride->driver->phone, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];
                        Mail::to($user->email)->queue(new DriverDetailsMail($data));
                
                        $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $request->seats_amount, 'booking_credit' => $booking->booking_credit, 'online_payment' => $request->online_payment, 'cash_payment' => $request->cash_payment, 'total' => $request->total];
                        Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
                
                        $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

                        if (!$phoneNumber) {
                            $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
                        }

                        if ($phoneNumber && env('APP_ENV') != 'local') {
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

                            $message = "".$title."\n".$user->first_name." has booked seat in your ride\nTrip detail\nOrigin: ".$booking->departure."\nDestination: ".$booking->destination."\nDeparture date: ".$depatureDate."\nPassenger phone number: ".$user->phone."";
        
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

                        $ids = json_decode($request->booked_seat_ids, true);
                        $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                        if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                            foreach ($getSeatDetails as $key => $getSeatDetail) {
                                $getSeatDetail->status = 'booked';
                                $getSeatDetail->booking_id = $booking->id;
                                $getSeatDetail->user_id = $booking->user_id;
                                $getSeatDetail->save();
                            }
                        }

                        // Check the ride first message
                        $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
                            $query->where('sender', $ride->added_by)
                                ->where('receiver', $booking->user_id);
                        })->orWhere(function ($query) use ($booking, $ride) {
                            $query->where('sender', $booking->user_id)
                                ->where('receiver', $ride->added_by);
                        })->where('ride_id', $ride->id)->first();
                        if (empty($rideFirstMessage)) {
                            $message1 = Message::create([
                                'ride_id' => $ride->id,
                                'receiver' => $ride->added_by,
                                'sender' => $booking->user_id,
                                'message' => $request->driver_message,
                                'redirect' => '1',
                                'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                            ]);
                        }
                        $message = Message::create([
                            'ride_id' => $ride->id,
                            'receiver' => $ride->added_by,
                            'sender' => $booking->user_id,
                            'message' => $request->driver_message,
                            'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                        ]);

                        $data = ['booking' => $booking];
                        return $this->successResponse($data, $messages->book_seat_message . ' ' . $request->seats . ' ' . $messages->book_seat_message_end_part);
                    } catch (\Stripe\Exception\CardException $e) {
                        // Handle Stripe card-related errors
                        if ($e->getError()->code === 'card_declined' && $e->getError()->decline_code === 'expired_card') {
                            return $this->apiErrorResponse(strip_tags($message->card_expiry_message ?? 'The card has expired. Please use a different card'), 200);
                        }
                    
                        // General Stripe card-related error message
                        return $this->apiErrorResponse($e->getMessage(), 200);
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        // Handle error
                        return $this->apiErrorResponse($e->getMessage(), 200);
                    }
                }
            } else {

                if ($ride->payment_method == "35" && $ride->booking_method == "31") {
                    $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->where('default', '1')->first();
                    if (!$phoneNumber) {
                        $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
                    }
                    if (!$phoneNumber) {
                        return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $messages->verified_number_message ?? null]);
                    }
        
                    $secured_cash = '1';
                    $secured_cash_code = rand(1000, 9999);
        
                    if ($phoneNumber && env('APP_ENV') != 'local') {
                        $sid = env('TWILIO_ACCOUNT_SID');
                        $token = env('TWILIO_AUTH_TOKEN');
                        $from = env('TWILIO_PHONE_NUMBER');
        
                        $twilio = new Client($sid, $token);
                        $to = $phoneNumber->phone;
                        $title = "";
                        $currentHour = date('H');
                        if ($currentHour >= 0 && $currentHour < 12) {
                            $title = "Good morning " . $user->first_name . "";
                        } elseif ($currentHour >= 12 && $currentHour < 17) {
                            $title = "Good afternoon " . $user->first_name . "";
                        } else {
                            $title = "Good evening " . $user->first_name . "";
                        }
        
                        $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
        
                        $message = "" . $title . "\nYour secured cash code is: $secured_cash_code\nTrip detail\nOrigin: " . $ride->rideDetail[0]->departure . "\nDestination: " . $ride->rideDetail[0]->destination . "\nDeparture date: " . $depatureDate . "\Driver name: " . $ride->driver->first_name . "\nDriver phone number: " . $ride->driver->phone . "\nVehicle info: " . $ride->make ?? '' . "," . $ride->year ?? '' . "," . $ride->modal ?? '' . "\nVehicle type: " . $ride->car_type . "";
        
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

                    if ($secured_cash_code && isset($user->email_notification) && $user->email_notification == 1) {
                        $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                        ->where('default', '1')
                        ->first();
                        $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;

                        $emailData = [
                            'first_name' => $user->first_name,
                            'secured_cash_code' => $secured_cash_code,
                            'driver_first_name' => $ride->driver->first_name,
                            'driver_last_name' => $ride->driver->last_name,
                            'driver_phone' => $driverPhoneToUse,
                            'driver_email' => $ride->driver->email,
                            'departure' => $ride->rideDetail[0]->departure,
                            'destination' => $ride->rideDetail[0]->destination,
                            'date' => Carbon::parse($ride->date)->format('F d, Y'),
                            'time' => $ride->time,
                            'seats' => $request->seats,
                            'booking_price' => $ride->rideDetail[0]->price * $request->seats
                        ];
                    
                        Mail::to($user->email)->queue(new SecuredCashPaymentCodeMail($emailData));
                    }
                }else{
                    $secured_cash = null;
                    $secured_cash_code = null;
                }
                
                // Payment successful, handle booking logic here
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'ride_id' => $request->id,
                    'seats' => $request->seats,
                    'type' => $request->type,
                    'booked_on' => Carbon::now(),
                    'status' => '1',
                    'booking_credit' => $booking_credit,
                    'fare' => $seat_price * $request->seats,
                    'tax_amount' => $taxAmt,
                    'secured_cash' => $secured_cash,
                    'secured_cash_code' => $secured_cash_code,
                    'departure' => $ride->rideDetail[0]->departure,
                    'destination' => $ride->rideDetail[0]->destination,
                    'price' => $ride->rideDetail[0]->price,
                    'ride_detail_id' => $ride->rideDetail[0]->id
                ]);

                

                if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){

                    $getBookingFeeSum = Transaction::where('booking_id', $booking->id)->sum('booking_fee');
                    $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);
                    $transaction = Transaction::create([
                        'booking_id' => $booking->id,
                        'type' => '1',
                        'booking_fee' => $currentBookingFee,
                        'price' => '0',
                        'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                        'tax_amount' => $taxAmt,
                        'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                        'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                        'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax != "" ? $request->deduct_tax : NULL, 
                    ]);

                    $coffeeWallet = CoffeeWallet::create([
                        'booking_id' => $booking->id,
                        'ride_id' => $ride->id,
                        'user_id' => $booking->user_id,
                        'cr_amount' => $currentBookingFee,
                    ]);
                }

                $notification = Notification::create([
                    'ride_id' => $request->id,
                    'posted_by' => $user->id,
                    'message' => 'You have a new instant booking from ' . $user->first_name . "\n" . 
                    'Seats booked: ' . numberToWords($request->seats),
                    'status' => 'completed',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $ride->rideDetail[0]->id,
                    'departure' => $ride->rideDetail[0]->departure,
                    'destination' => $ride->rideDetail[0]->destination
                ]);

                // Assuming $user and $fcmToken are defined
                $fcmToken = $ride->driver->mobile_fcm_token;
                $body = $notification->message;

                if ($fcmToken) {
                    $fcmService = new FCMService();
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }
        
                $notification = Notification::create([
                    'type' => 2,
                    'ride_id' => $request->id,
                    'posted_to' => $booking->id,
                    'posted_by' => $ride->added_by,
                    'message' => 'Your booking details' . "\n" . 'Seats booked: ' . numberToWords($request->seats),
                    'status' => 'completed',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $ride->rideDetail[0]->id,
                    'departure' => $ride->rideDetail[0]->departure,
                    'destination' => $ride->rideDetail[0]->destination
                ]);

                // Assuming $user and $fcmToken are defined
                $fcmToken = $user->mobile_fcm_token;
                $body = $notification->message;

                if ($fcmToken) {
                    $fcmService = new FCMService();
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }
        
                $bookingPrice = $seat_price * $booking->seats;

                // $data = ['first_name' => $ride->driver->first_name, 'passenger_first_name' => $user->first_name, 'secured_cash_code' => $secured_cash_code];
                // Mail::to($ride->driver->email)->queue(new InstantBookingMail($data));
        
                $data = ['first_name' => $ride->driver->first_name, 'lang' => $selectedLanguage->abbreviation, 'origin' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'time' => $ride->time, 'seats' => $booking->seats, 'booking_price' => $seat_price, 'total_price' => $bookingPrice, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'email' => $user->email, 'phone' => $user->phone];
                Mail::to($ride->driver->email)->queue(new PassengerDetailsMail($data));
        
                $data = ['first_name' => $user->first_name, 'driver_first_name' => $ride->driver->first_name, 'driver_last_name' => $ride->driver->last_name, 'gender' => $ride->driver->gender, 'email' => $ride->driver->email, 'phone' => $ride->driver->phone, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];
                Mail::to($user->email)->queue(new DriverDetailsMail($data));
        
                $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $request->seats_amount, 'booking_credit' => $booking->booking_credit, 'online_payment' => $request->online_payment, 'cash_payment' => $request->cash_payment, 'total' => $request->total];
                Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
        
                $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

                if (!$phoneNumber) {
                    $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
                }

                if ($phoneNumber && env('APP_ENV') != 'local') {
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

                    $message = "".$title."\n".$user->first_name." has booked seat in your ride\nTrip detail\nOrigin: ".$booking->departure."\nDestination: ".$booking->destination."\nDeparture date: ".$depatureDate."\nPassenger phone number: ".$user->phone."";

        
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

                $ids = json_decode($request->booked_seat_ids, true);
                $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                    foreach ($getSeatDetails as $key => $getSeatDetail) {
                        $getSeatDetail->status = 'booked';
                        $getSeatDetail->booking_id = $booking->id;
                        $getSeatDetail->user_id = $booking->user_id;
                        $getSeatDetail->save();
                    }
                }

                // Check the ride first message
                $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
                    $query->where('sender', $ride->added_by)
                        ->where('receiver', $booking->user_id);
                })->orWhere(function ($query) use ($booking, $ride) {
                    $query->where('sender', $booking->user_id)
                        ->where('receiver', $ride->added_by);
                })->where('ride_id', $ride->id)->first();
                if (empty($rideFirstMessage)) {
                    $message1 = Message::create([
                        'ride_id' => $ride->id,
                        'receiver' => $ride->added_by,
                        'sender' => $booking->user_id,
                        'message' => $request->driver_message,
                        'redirect' => '1',
                        'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                    ]);
                }
                $message = Message::create([
                    'ride_id' => $ride->id,
                    'receiver' => $ride->added_by,
                    'sender' => $booking->user_id,
                    'message' => $request->driver_message,
                    'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                ]);
                
                $data = ['booking' => $booking];
                return $this->successResponse($data, $messages->book_seat_message . ' ' . $request->seats . ' ' . $messages->book_seat_message_end_part);
            }
            
        }

        return $this->apiErrorResponse($messages->general_error_message ?? "Ride not found", 404);
    }

    public function updateInstantBooking(Request $request){
        $booking = Booking::where('id', $request->booking_id)->first();

        
        $taxAmt = isset($request->tax_amount) ? $request->tax_amount : 0;
        
        $getPaymentMethodId = FeaturesSetting::where('slug', 'cash')->value('id');

        if ($booking) {
            $ride = Ride::where('id', $booking->ride_id)->first();
            $user = Auth::guard('sanctum')->user();

            $message = null;
            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                if ($selectedLanguage) {
                    // Retrieve the HomePageSettingDetail associated with the selected language
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message', 'book_seat_message_end_part', 'seat_unavailable_message','card_expiry_message','general_error_message','block_booking_message')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message', 'book_seat_message_end_part', 'seat_unavailable_message','card_expiry_message','general_error_message','block_booking_message')->first();
                }
            }


            if ($user->block_booking == '1') {
                return $this->apiErrorResponse(strip_tags($message->block_booking_message ?? null), 200);
            }
    
            $bookings = Booking::where('ride_id', $booking->ride_id)
                ->where('status', '!=', '3')
                ->where('status', '!=', '4')
                ->whereNotIn('id', [$request->booking_id])
                ->get();
    
            $seatsBooked = $bookings->sum('seats') + $request->seats;
            if ($seatsBooked > $ride->seats) {
                return $this->apiErrorResponse(strip_tags($message->seat_unavailable_message ?? null), 200);
            }
    
            $transactionPrice = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('price');

            
            $transactionTaxSum = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('tax_amount');

            $type = FeaturesSetting::whereId($request->type)->first();
            if (isset($type) && $type->slug === 'firm') {
                $setting = SiteSetting::first();
                $seat_price = $booking->price - ($booking->price * $setting->frim_discount / 100);
            } else {
                $seat_price = $booking->price;
            }
            
            $booking_credit = $request->booking_credit;
    
            if ($request->seats > $booking->seats) {
                $validated = $request->validate([
                    'payment_method' => $request->online_payment > '0' ? 'required' : 'nullable',
                    'paypal_id' => $request->online_payment > '0' && $request->payment_method == 'paypal' ? 'required' : 'nullable',
                    'card_id' => $request->online_payment > '0' && $request->payment_method == 'credit_card' ? 'required' : 'nullable',
                    'booking_credit' => 'required|max:25',
                    'seats' => 'required',
                    'type' => 'required',
                    'seats_amount' => 'required',
                    'cash_payment' => 'required',
                    'online_payment' => 'required',
                    'total' => 'required',
                ]);

                if ($request->online_payment > '0') {
                    if ($request->payment_method == 'paypal') {
                        $booking->update([
                            'seats' => $request->seats,
                            'type' => $request->type,
                            'fare' => $request->seats * $seat_price,
                            'booking_credit' => $booking_credit,
                            'tax_amount' => $taxAmt,
                        ]);
        
                        $payable_amount = $request->online_payment - $transactionPrice;

                        $getBookingFeeSum = Transaction::where('booking_id', $booking->id)->sum('booking_fee');
                        $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);
                        

                        if($ride->payment_method == $getPaymentMethodId){
                            $newTransaction = Transaction::create([
                                'booking_id' => $booking->id,
                                'type' => '1',
                                'price' => '0',
                                'booking_fee' => $currentBookingFee,
                                'paypal_id' => $request->g_pay == "1" ? NULL : $request->paypal_id,
                                'stripe_id' => $request->g_pay == "1" ? $request->paypal_id : NULL,
                                'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                                'tax_amount' => $taxAmt - $transactionTaxSum,
                                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax !="" ? $request->deduct_tax : NULL,
                            ]);
                            if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                                $coffeeWallet = CoffeeWallet::create([
                                    'booking_id' => $booking->id,
                                    'ride_id' => $ride->id,
                                    'user_id' => $booking->user_id,
                                    'cr_amount' => $currentBookingFee,
                                ]);
                            }
                        }else{
                            $newTransaction = Transaction::create([
                                'booking_id' => $booking->id,
                                'type' => '1',
                                'price' => $payable_amount,
                                'booking_fee' => $currentBookingFee,
                                'paypal_id' => $request->g_pay == "1" ? NULL : $request->paypal_id,
                                'stripe_id' => $request->g_pay == "1" ? $request->paypal_id : NULL,
                                'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                                'tax_amount' => $taxAmt - $transactionTaxSum,
                                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax !="" ? $request->deduct_tax : NULL,
                            ]);
                            if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                                $coffeeWallet = CoffeeWallet::create([
                                    'booking_id' => $booking->id,
                                    'ride_id' => $ride->id,
                                    'user_id' => $booking->user_id,
                                    'cr_amount' => $currentBookingFee,
                                ]);
                            }
                        }

                        
        
                        $notification = Notification::create([
                            'ride_id' => $ride->id,
                            'posted_by' => $user->id,
                            'message' =>  $request->seats . ' seats booked',
                            'status' => 'completed',
                            'notification_type' => 'upcoming',
                            'ride_detail_id' => $booking->ride_detail_id,
                            'departure' => $booking->departure,
                            'destination' => $booking->destination
                        ]);
    
                        // Assuming $user and $fcmToken are defined
                        $fcmToken = $ride->driver->mobile_fcm_token;
                        $body = $notification->message;
    
                        if ($fcmToken) {
                            $fcmService = new FCMService();
                            // Send the booking notification
                            $fcmService->sendNotification($fcmToken, $body);
                        }
                
                        $notification = Notification::create([
                            'type' => 2,
                            'ride_id' => $ride->id,
                            'posted_to' => $booking->id,
                            'posted_by' => $ride->added_by,
                            'message' =>  $request->seats . ' booked successfully',
                            'status' => 'completed',
                            'notification_type' => 'upcoming',
                            'ride_detail_id' => $booking->ride_detail_id,
                            'departure' => $booking->departure,
                            'destination' => $booking->destination
                        ]);
    
                        // Assuming $user and $fcmToken are defined
                        $fcmToken = $user->mobile_fcm_token;
                        $body = $notification->message;
    
                        if ($fcmToken) {
                            $fcmService = new FCMService();
                            // Send the booking notification
                            $fcmService->sendNotification($fcmToken, $body);
                        }


                        $ids = json_decode($request->booked_seat_ids, true);
                        $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                        if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                            foreach ($getSeatDetails as $key => $getSeatDetail) {
                                $getSeatDetail->status = 'booked';
                                $getSeatDetail->booking_id = $booking->id; 
                                $getSeatDetail->user_id = $booking->user_id;
                                $getSeatDetail->save();
                            }
                        }
        
                        $data = ['booking' => $booking];
                        return $this->successResponse($data, strip_tags($message->book_seat_message . ' ' . $request->seats . ' ' . $message->book_seat_message_end_part));
                    } elseif ($request->payment_method == 'credit_card') {
                        try {

                            $paymentIntent = null;
                            $payable_amount = '0';

                            if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                                
                            }else{
                                // Retrieve the selected card from the database
                                $card = Card::where('id', $request->card_id)
                                ->where('user_id', $user->id)
                                ->firstOrFail();
                    
                                // Set your Stripe API key.
                                Stripe::setApiKey(env('STRIPE_SECRET'));

                                // Attach the payment method to the customer
                                $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                                $paymentMethod->attach(['customer' => $user->stripe_customer_id]);
                
                                $payable_amount = $request->online_payment - $transactionPrice;
                
                                if ($payable_amount > '0') {
                                    $stripePay = $payable_amount - ($taxAmt - $transactionTaxSum);
                                    // Create a payment intent
                                    $paymentIntent = PaymentIntent::create([
                                        'amount' => round(($stripePay * 100), 0),
                                        'currency' => 'usd',
                                        'customer' => $user->stripe_customer_id,
                                        'payment_method' => $paymentMethod->id,
                                        'off_session' => true,
                                        'confirm' => true,
                                    ]);
                                }
                            }
                            
                            $booking->update([
                                'seats' => $request->seats,
                                'type' => $request->type,
                                'fare' => $request->seats * $seat_price,
                                'booking_credit' => $booking_credit,
                                'tax_amount' => $taxAmt
                            ]);

                            $getBookingFeeSum = Transaction::where('booking_id', $booking->id)->sum('booking_fee');
                            $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);


                            if($ride->payment_method == $getPaymentMethodId){
                                $newTransaction = Transaction::create([
                                    'booking_id' => $booking->id,
                                    'type' => '1',
                                    'price' => '0',
                                    'booking_fee' => $currentBookingFee,
                                    'stripe_id' => isset($paymentIntent) && $paymentIntent != null ? $paymentIntent->id : NULL,
                                    'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                                    'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,                                    
                                    'tax_amount' => $taxAmt - $transactionTaxSum,
                                    'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                    'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                    'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax !="" ? $request->deduct_tax : NULL,
                                ]);

                                if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                                    $coffeeWallet = CoffeeWallet::create([
                                        'booking_id' => $booking->id,
                                        'ride_id' => $ride->id,
                                        'user_id' => $booking->user_id,
                                        'cr_amount' => $currentBookingFee,
                                    ]);
                                }
                                

                                if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                                    $topUpBalance = TopUpBalance::create([
                                        'booking_id' => $booking->id,
                                        'user_id' => $booking->user_id,
                                        'cr_amount' => $currentBookingFee + ($taxAmt - $transactionTaxSum),
                                        'added_date' => date('Y-m-d'),
                                    ]);
                                }
                            }else{
    
                                $newTransaction = Transaction::create([
                                    'booking_id' => $booking->id,
                                    'type' => '1',
                                    'price' => $payable_amount,
                                    'booking_fee' => $currentBookingFee,
                                    'stripe_id' => isset($paymentIntent) && $paymentIntent != null ? $paymentIntent->id : NULL,
                                    'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                                    'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,                                    
                                    'tax_amount' => $taxAmt - $transactionTaxSum,
                                    'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                    'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                    'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax !="" ? $request->deduct_tax : NULL,
                                ]);

                                if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                                    $coffeeWallet = CoffeeWallet::create([
                                        'booking_id' => $booking->id,
                                        'ride_id' => $ride->id,
                                        'user_id' => $booking->user_id,
                                        'cr_amount' => $currentBookingFee,
                                    ]);
                                }

                                if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                                    $topUpBalance = TopUpBalance::create([
                                        'booking_id' => $booking->id,
                                        'user_id' => $booking->user_id,
                                        'cr_amount' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? (($payable_amount - $currentBookingFee) + ($taxAmt - $transactionTaxSum)) : $payable_amount + ($taxAmt - $transactionTaxSum),
                                        'added_date' => date('Y-m-d'),
                                    ]);
                                }
                            }
            
                            
            
                            $notification = Notification::create([
                                'ride_id' => $ride->id,
                                'posted_by' => $user->id,
                                'message' =>  $request->seats . ' seats booked',
                                'status' => 'completed',
                                'notification_type' => 'upcoming',
                            'ride_detail_id' => $booking->ride_detail_id,
                            'departure' => $booking->departure,
                            'destination' => $booking->destination
                            ]);
        
                            // Assuming $user and $fcmToken are defined
                            $fcmToken = $ride->driver->mobile_fcm_token;
                            $body = $notification->message;
        
                            if ($fcmToken) {
                                $fcmService = new FCMService();
                                // Send the booking notification
                                $fcmService->sendNotification($fcmToken, $body);
                            }
                    
                            $notification = Notification::create([
                                'type' => 2,
                                'ride_id' => $ride->id,
                                'posted_to' => $booking->id,
                                'posted_by' => $ride->added_by,
                                'message' =>  $request->seats . ' booked successfully',
                                'status' => 'completed',
                                'notification_type' => 'upcoming',
                            'ride_detail_id' => $booking->ride_detail_id,
                            'departure' => $booking->departure,
                            'destination' => $booking->destination
                            ]);
        
                            // Assuming $user and $fcmToken are defined
                            $fcmToken = $user->mobile_fcm_token;
                            $body = $notification->message;
        
                            if ($fcmToken) {
                                $fcmService = new FCMService();
                                // Send the booking notification
                                $fcmService->sendNotification($fcmToken, $body);
                            }

                            $ids = json_decode($request->booked_seat_ids, true);
                            $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                            if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                                foreach ($getSeatDetails as $key => $getSeatDetail) {
                                    $getSeatDetail->status = 'booked';
                                    $getSeatDetail->booking_id = $booking->id;
                                    $getSeatDetail->user_id = $booking->user_id;
                                    $getSeatDetail->save();
                                }
                            }
            
                            $data = ['booking' => $booking];
                            return $this->successResponse($data, 'You have successfully update booking in this ride');
                        } catch (\Stripe\Exception\CardException $e) {
                            // Handle Stripe card-related errors
                            if ($e->getError()->code === 'card_declined' && $e->getError()->decline_code === 'expired_card') {
                                return $this->apiErrorResponse(strip_tags($message->card_expiry_message ?? 'The card has expired. Please use a different card'), 200);
                            }
                        
                            // General Stripe card-related error message
                            return $this->apiErrorResponse($e->getMessage(), 200);
                        } catch (\Stripe\Exception\ApiErrorException $e) {
                            // Handle error
                            return $this->apiErrorResponse($e->getMessage(), 200);
                        }
                    }
                } else {
                    $booking->update([
                        'seats' => $request->seats,
                        'type' => $request->type,
                        'booking_credit' => $booking_credit,
                        'tax_amount' => $taxAmt
                    ]);


                    if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){

                        $getBookingFeeSum = Transaction::where('booking_id', $booking->id)->sum('booking_fee');
                        $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);
                        $transaction = Transaction::create([
                            'booking_id' => $booking->id,
                            'type' => '1',
                            'booking_fee' => $currentBookingFee,
                            'price' => '0',
                            'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                            'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,                            
                            'tax_amount' => $taxAmt - $transactionTaxSum,
                            'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                            'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                            'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax !="" ? $request->deduct_tax : NULL, 
                        ]);
    
                        $coffeeWallet = CoffeeWallet::create([
                            'booking_id' => $booking->id,
                            'ride_id' => $ride->id,
                            'user_id' => $booking->user_id,
                            'cr_amount' => $currentBookingFee + ($taxAmt - $transactionTaxSum),
                        ]);
                    }
    
                    $notification = Notification::create([
                        'ride_id' => $ride->id,
                        'posted_by' => $user->id,
                        'message' =>  $request->seats . ' seats booked',
                        'status' => 'completed',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $booking->ride_detail_id,
                        'departure' => $booking->departure,
                        'destination' => $booking->destination
                    ]);

                    // Assuming $user and $fcmToken are defined
                    $fcmToken = $ride->driver->mobile_fcm_token;
                    $body = $notification->message;

                    if ($fcmToken) {
                        $fcmService = new FCMService();
                        // Send the booking notification
                        $fcmService->sendNotification($fcmToken, $body);
                    }
            
                    $notification = Notification::create([
                        'type' => 2,
                        'ride_id' => $ride->id,
                        'posted_to' => $booking->id,
                        'posted_by' => $ride->added_by,
                        'message' =>  $request->seats . ' booked successfully',
                        'status' => 'completed',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $booking->ride_detail_id,
                        'departure' => $booking->departure,
                        'destination' => $booking->destination
                    ]);

                    // Assuming $user and $fcmToken are defined
                    $fcmToken = $user->mobile_fcm_token;
                    $body = $notification->message;

                    if ($fcmToken) {
                        $fcmService = new FCMService();
                        // Send the booking notification
                        $fcmService->sendNotification($fcmToken, $body);
                    }
                    $ids = json_decode($request->booked_seat_ids, true);
                    $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                    if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                        foreach ($getSeatDetails as $key => $getSeatDetail) {
                            $getSeatDetail->status = 'booked';
                            $getSeatDetail->booking_id = $booking->id;
                            $getSeatDetail->user_id = $booking->user_id;
                            $getSeatDetail->save();
                        }
                    }
    
                    $data = ['booking' => $booking];
                    return $this->successResponse($data, strip_tags($message->book_seat_message . ' ' . $request->seats . ' ' . $message->book_seat_message_end_part));
                }
                
            }
    
            $data = ['booking' => $booking];
            return $this->successResponse($data, strip_tags($message->booking_not_update_message ?? 'You did not update your booking in this ride'));
        }

        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? "Booking not found"), 404);
    }

    public function bookingRequest(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $rideDetailId = isset($request->ride_detail_id) ? $request->ride_detail_id : 0;
        $ride = Ride::where('id', $request->id);
        if($rideDetailId != 0){
            $ride = $ride->with(['rideDetail' => function($q) use($rideDetailId){
                $q->where('id',$rideDetailId);
            }]);
        }else{
            $ride = $ride->with(['rideDetail' => function($q){
                $q->where('default_ride','1');
            }]);
        }
        
        $ride = $ride->first();
        $user = User::where('id', $user_id)->first();

        
        $getPaymentMethodId = FeaturesSetting::where('slug', 'cash')->value('id');

        // Calculate expiry time based on ride date and time
        $currentTime = Carbon::now();
        $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
        $difference = $rideDateTime->diffInHours($currentTime);

        if ($difference > 48) {
            $expiryTime = $currentTime->addHours(12);
        } elseif ($difference >= 24 && $difference <= 48) {
            $expiryTime = $currentTime->addHours(6);
        } elseif ($difference >= 6 && $difference < 24) {
            $expiryTime = $currentTime->addHours(2);
        } else {
            $expiryTime = $currentTime->addMinutes(30);
        }

        $taxAmt = isset($request->tax_amount) ? $request->tax_amount : 0;

        $findRidePage = null;
        $messages = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('seat_unavailable_message', 'verified_number_message','general_error_message','card_expiry_message','block_booking_message', 'booking_request_success_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('seat_unavailable_message', 'verified_number_message','general_error_message','card_expiry_message','block_booking_message', 'booking_request_success_message')->first();
            }
        }


        if ($user->block_booking == '1') {
            return $this->apiErrorResponse(strip_tags($message->block_booking_message ?? null), 200);
        }

        $bookings = Booking::where('ride_id', $request->id)->where('status', '!=', '3')->where('status', '!=', '4')->get();

        $seatsBooked = $bookings->sum('seats') + $request->seats;
        if ($seatsBooked > $ride->seats) {
            return $this->apiErrorResponse($messages->seat_unavailable_message ?? null, 200);
        }

        $validated = $request->validate([
            'payment_method' => $request->online_payment > '0' ? 'required' : 'nullable',
            'paypal_id' => $request->online_payment > '0' && $request->payment_method == 'paypal' ? 'required' : 'nullable',
            'card_id' => $request->online_payment > '0' && $request->payment_method == 'credit_card' ? 'required' : 'nullable',
            'booking_credit' => 'required|max:25',
            'seats' => 'required',
            'type' => 'required',
            'seats_amount' => 'required',
            'cash_payment' => 'required',
            'online_payment' => 'required',
            'total' => 'required',
        ]);

        $type = FeaturesSetting::whereId($request->type)->first();
        if (isset($type) && $type->slug === 'firm') {
            $setting = SiteSetting::first();
            $seat_price = $ride->rideDetail[0]->price - ($ride->rideDetail[0]->price * $setting->frim_discount / 100);
        } else {
            $seat_price = $ride->rideDetail[0]->price;
        }

        
        $booking_credit = $request->booking_credit;

        if ($request->online_payment > '0') {
            if ($request->payment_method == 'paypal') {
                
                $secured_cash = null;
                $secured_cash_code = null;

                // Payment successful, handle booking logic here
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'ride_id' => $request->id,
                    'seats' => $request->seats,
                    'type' => $request->type,
                    'booked_on' => $currentTime,
                    'booking_credit' => $booking_credit,
                    'fare' => $seat_price * $request->seats,
                    'secured_cash' => $secured_cash,
                    'tax_amount' => $taxAmt,
                    'secured_cash_code' => $secured_cash_code,
                    'expires_at' => $expiryTime,
                    'departure' => $ride->rideDetail[0]->departure,
                    'destination' => $ride->rideDetail[0]->destination,
                    'price' => $ride->rideDetail[0]->price,
                    'ride_detail_id' => $ride->rideDetail[0]->id
                ]);

            

                if($ride->payment_method == $getPaymentMethodId){
                    $transaction = Transaction::create([
                        'booking_id' => $booking->id,
                        'type' => '1',
                        'price' => '0',
                        'booking_fee' => $booking_credit,
                        'paypal_id' => $request->input('paypal_id'),
                        'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                        'tax_amount' => $taxAmt,
                        'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                        'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                        'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax != "" ? $request->deduct_tax : NULL, 
                    ]);
                }else{
                    $transaction = Transaction::create([
                        'booking_id' => $booking->id,
                        'type' => '1',
                        'price' => $request->input('online_payment'),
                        'booking_fee' => $booking_credit,
                        'paypal_id' => $request->input('paypal_id'),
                        'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                        'tax_amount' => $taxAmt,
                        'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                        'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                        'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax != "" ? $request->deduct_tax : NULL, 
                    ]);
                }

                if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                    $coffeeWallet = CoffeeWallet::create([
                        'booking_id' => $booking->id,
                        'ride_id' => $ride->id,
                        'user_id' => $booking->user_id,
                        'cr_amount' => $booking_credit,
                    ]);
                }

                
    
                
        
                $notification = Notification::create([
                    'ride_id' => $request->id,
                    'posted_by' => $user->id,
                    // 'message' =>  'You have a new booking request from' . $user->first_name . '.' . $request->seats . ' Seats booked',
                    'message' => 'You have a new booking request from ' . $user->first_name . "\n" . 
                    'Seats booked: ' . numberToWords($request->seats),
                    'status' => 'request',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);
    
                // Assuming $user and $fcmToken are defined
                $fcmToken = $ride->driver->mobile_fcm_token;
                $body = $notification->message;
    
                if ($fcmToken) {
                    $fcmService = new FCMService();
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }
        
                $bookingPrice = $seat_price * $booking->seats;

                $data = ['first_name' => $ride->driver->first_name,'id' => $booking->id,'lang' => $selectedLanguage->abbreviation,'email' => $ride->driver->email,'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $seat_price, 'total_price' => $bookingPrice, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];
                // Send booking request email
                Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
        
                $data = ['first_name' => $user->first_name];
                Mail::to($user->email)->queue(new BookingRequestConfirmationMail($data));
        
        
                $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $request->seats_amount, 'booking_credit' => $booking->booking_credit, 'online_payment' => $request->online_payment, 'cash_payment' => $request->cash_payment, 'total' => $request->total];
                Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
        
                $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

                if (!$phoneNumber) {
                    $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
                }

                if ($phoneNumber && env('APP_ENV') != 'local') {
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

                    $title = "From ProximaRide: You have a new booking request from (" . $user->first_name . ")";
                    $depatureDate = date('F d, Y H:i', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                    $message = $title . "\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . "\n" . $user->first_name . ": " . $user->phone . "\nNumber of seats: " . $booking->seats . "\nClick here for accept(" . url("/accept/" . $booking->id) . ")\nClick here for reject(" . url("/reject/" . $booking->id) . ")";
        
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

                $ids = json_decode($request->booked_seat_ids, true);
                $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                    foreach ($getSeatDetails as $key => $getSeatDetail) {
                        $getSeatDetail->status = 'booked';
                        $getSeatDetail->booking_id = $booking->id;
                        $getSeatDetail->user_id = $booking->user_id;
                        $getSeatDetail->save();
                    }
                }

                // Check the ride first message
                $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
                    $query->where('sender', $ride->added_by)
                        ->where('receiver', $booking->user_id);
                })->orWhere(function ($query) use ($booking, $ride) {
                    $query->where('sender', $booking->user_id)
                        ->where('receiver', $ride->added_by);
                })->where('ride_id', $ride->id)->first();
                if (empty($rideFirstMessage)) {
                    $message1 = Message::create([
                        'ride_id' => $ride->id,
                        'receiver' => $ride->added_by,
                        'sender' => $booking->user_id,
                        'message' => $request->driver_message,
                        'redirect' => '1',
                        'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                    ]);
                }
                $message = Message::create([
                    'ride_id' => $ride->id,
                    'receiver' => $ride->added_by,
                    'sender' => $booking->user_id,
                    'message' => $request->driver_message,
                    'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                ]);

                $data = ['booking' => $booking];
                return $this->successResponse($data, $messages->booking_request_success_message ?? 'Your request has been successfully sent to the driver');
            } elseif ($request->payment_method == 'credit_card') {        
                try {


                    $paymentIntent = null;

                    if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                        
                    }else{
                        // Retrieve the selected card from the database
                            $card = Card::where('id', $request->card_id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();
                
                        // Set your Stripe API key.
                        Stripe::setApiKey(env('STRIPE_SECRET'));

                        // Attach the payment method to the customer
                        $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                        $paymentMethod->attach(['customer' => $user->stripe_customer_id]);
                
                        $stripePay = $request->input('online_payment') + $taxAmt;
                        // Create a payment intent
                        $paymentIntent = PaymentIntent::create([
                            'amount' => $stripePay * 100,
                            'currency' => 'usd',
                            'customer' => $user->stripe_customer_id,
                            'payment_method' => $paymentMethod->id,
                            'off_session' => true,
                            'confirm' => true,
                        ]);
                    }

                    $secured_cash = null;
                    $secured_cash_code = null;
                
                    // Payment successful, handle booking logic here
                    $booking = Booking::create([
                        'user_id' => $user->id,
                        'ride_id' => $request->id,
                        'seats' => $request->seats,
                        'type' => $request->type,
                        'booked_on' => $currentTime,
                        'booking_credit' => $booking_credit,
                        'fare' => $seat_price * $request->seats,
                        'secured_cash' => $secured_cash,
                        'tax_amount' => $taxAmt,
                        'secured_cash_code' => $secured_cash_code,
                        'expires_at' => $expiryTime,
                        'departure' => $ride->rideDetail[0]->departure,
                        'destination' => $ride->rideDetail[0]->destination,
                        'price' => $ride->rideDetail[0]->price,
                        'ride_detail_id' => $ride->rideDetail[0]->id
                    ]);

                    

                    if($ride->payment_method == $getPaymentMethodId){
                        $transaction = Transaction::create([
                            'booking_id' => $booking->id,
                            'type' => '1',
                            'price' => '0',
                            'booking_fee' => $booking_credit,
                            'stripe_id' => isset($paymentIntent) && $paymentIntent != null ? $paymentIntent->id : NULL,
                            'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                            'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                            'tax_amount' => $taxAmt,
                            'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                            'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                            'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax != "" ? $request->deduct_tax : NULL,
                        ]);
                    }else{
                        $transaction = Transaction::create([
                            'booking_id' => $booking->id,
                            'type' => '1',
                            'price' => $request->input('online_payment'),
                            'booking_fee' => $booking_credit,
                            'stripe_id' => isset($paymentIntent) && $paymentIntent != null ? $paymentIntent->id : NULL,
                            'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                            'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                            'tax_amount' => $taxAmt,
                            'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                            'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                            'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax != "" ? $request->deduct_tax : NULL,
                        ]);
                    }

                    if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                        $coffeeWallet = CoffeeWallet::create([
                            'booking_id' => $booking->id,
                            'ride_id' => $ride->id,
                            'user_id' => $booking->user_id,
                            'cr_amount' => $booking_credit + $taxAmt,
                        ]);
                    }


                    if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                        $topUpBalance = TopUpBalance::create([
                            'booking_id' => $booking->id,
                            'user_id' => $user->id,
                            'cr_amount' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? ($request->online_payment - $booking_credit) + $taxAmt : $request->online_payment + $taxAmt,
                            'added_date' => date('Y-m-d'),
                        ]);
                    }
        
                    
            
                    $notification = Notification::create([
                        'ride_id' => $request->id,
                        'posted_by' => $user->id,
                        'message' => 'You have a new booking request from ' . $user->first_name . "\n" . 
                        'Seats booked: ' . numberToWords($request->seats),
                        'status' => 'request',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $booking->ride_detail_id,
                        'departure' => $booking->departure,
                        'destination' => $booking->destination
                    ]);
    
                    // Assuming $user and $fcmToken are defined
                    $fcmToken = $ride->driver->mobile_fcm_token;
                    $body = $notification->message;
    
                    if ($fcmToken) {
                        $fcmService = new FCMService();
                        // Send the booking notification
                        $fcmService->sendNotification($fcmToken, $body);
                    }
            
                    $bookingPrice = $seat_price * $booking->seats;

                    $data = ['first_name' => $ride->driver->first_name,'id' => $booking->id,'lang' => $selectedLanguage->abbreviation,'email' => $ride->driver->email,'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $seat_price, 'total_price' => $bookingPrice, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];
                    // Send booking request email
                    Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
            
                    $data = ['first_name' => $user->first_name];
                    Mail::to($user->email)->queue(new BookingRequestConfirmationMail($data));
            
            
                    $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $request->seats_amount, 'booking_credit' => $booking->booking_credit, 'online_payment' => $request->online_payment, 'cash_payment' => $request->cash_payment, 'total' => $request->total];
                    Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
            
                    $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

                    if (!$phoneNumber) {
                        $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
                    }

                    if ($phoneNumber && env('APP_ENV') != 'local') {
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

                        $title = "From ProximaRide: You have a new booking request from (" . $user->first_name . ")";
                        $depatureDate = date('F d, Y H:i', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                        $message = $title . "\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . "\n" . $user->first_name . ": " . $user->phone . "\nNumber of seats: " . $booking->seats . "\nClick here for accept(" . url("/accept/" . $booking->id) . ")\nClick here for reject(" . url("/reject/" . $booking->id) . ")";
        
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

                    $ids = json_decode($request->booked_seat_ids, true);
                    $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                    if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                        foreach ($getSeatDetails as $key => $getSeatDetail) {
                            $getSeatDetail->status = 'booked';
                            $getSeatDetail->booking_id = $booking->id;
                            $getSeatDetail->user_id = $booking->user_id;
                            $getSeatDetail->save();
                        }
                    }

                    // Check the ride first message
                    $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
                        $query->where('sender', $ride->added_by)
                            ->where('receiver', $booking->user_id);
                    })->orWhere(function ($query) use ($booking, $ride) {
                        $query->where('sender', $booking->user_id)
                            ->where('receiver', $ride->added_by);
                    })->where('ride_id', $ride->id)->first();
                    if (empty($rideFirstMessage)) {
                        $message1 = Message::create([
                            'ride_id' => $ride->id,
                            'receiver' => $ride->added_by,
                            'sender' => $booking->user_id,
                            'message' => $request->driver_message,
                            'redirect' => '1',
                            'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                        ]);
                    }
                    $message = Message::create([
                        'ride_id' => $ride->id,
                        'receiver' => $ride->added_by,
                        'sender' => $booking->user_id,
                        'message' => $request->driver_message,
                        'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                    ]);

                    $data = ['booking' => $booking];
                    return $this->successResponse($data, $messages->booking_request_success_message ?? 'Your request has been successfully sent to the driver');
                } catch (\Stripe\Exception\CardException $e) {
                    // Handle Stripe card-related errors
                    if ($e->getError()->code === 'card_declined' && $e->getError()->decline_code === 'expired_card') {
                        return $this->apiErrorResponse(strip_tags($message->card_expiry_message ?? 'The card has expired. Please use a different card'), 200);
                    }
                
                    // General Stripe card-related error message
                    return $this->apiErrorResponse($e->getMessage(), 200);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    // Handle error
                    return $this->apiErrorResponse($e->getMessage(), 200);
                }
            }
        } else {
            
            $secured_cash = null;
            $secured_cash_code = null;
            // Payment successful, handle booking logic here
            $booking = Booking::create([
                'user_id' => $user->id,
                'ride_id' => $request->id,
                'seats' => $request->seats,
                'type' => $request->type,
                'booked_on' => $currentTime,
                'booking_credit' => $booking_credit,
                'fare' => $seat_price * $request->seats,
                'secured_cash' => $secured_cash,
                'tax_amount' => $taxAmt,
                'secured_cash_code' => $secured_cash_code,
                'expires_at' => $expiryTime,
                'departure' => $ride->rideDetail[0]->departure,
                'destination' => $ride->rideDetail[0]->destination,
                'price' => $ride->rideDetail[0]->price,
                'ride_detail_id' => $ride->rideDetail[0]->id
            ]);

            


            if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                $transaction = Transaction::create([
                    'booking_id' => $booking->id,
                    'type' => '1',
                    'booking_fee' => $booking_credit,
                    'price' => '0',
                    'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                    'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                    'tax_amount' => $taxAmt,
                    'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                    'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                    'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax != "" ? $request->deduct_tax : NULL, 
                ]);

                $coffeeWallet = CoffeeWallet::create([
                    'booking_id' => $booking->id,
                    'ride_id' => $ride->id,
                    'user_id' => $booking->user_id,
                    'cr_amount' => $booking_credit + $taxAmt,
                ]);
            }
    
            $notification = Notification::create([
                'ride_id' => $request->id,
                'posted_by' => $user->id,
                'message' => 'You have a new booking request from ' . $user->first_name . "\n" . 
                'Seats booked: ' . numberToWords($request->seats),
                'status' => 'request',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            // Assuming $user and $fcmToken are defined
            $fcmToken = $ride->driver->mobile_fcm_token;
            $body = $notification->message;

            if ($fcmToken) {
                $fcmService = new FCMService();
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }
    
            $bookingPrice = $seat_price * $booking->seats;
            
            $data = ['first_name' => $ride->driver->first_name,'id' => $booking->id,'lang' => $selectedLanguage->abbreviation,'email' => $ride->driver->email,'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $seat_price, 'total_price' => $bookingPrice, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];
            // Send booking request email
            Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
    
            $data = ['first_name' => $user->first_name];
            Mail::to($user->email)->queue(new BookingRequestConfirmationMail($data));
    
    
            $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $request->seats_amount, 'booking_credit' => $booking->booking_credit, 'online_payment' => $request->online_payment, 'cash_payment' => $request->cash_payment, 'total' => $request->total];
            Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
    
            $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

            if (!$phoneNumber) {
                $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
            }

            if ($phoneNumber && env('APP_ENV') != 'local') {
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

                $title = "From ProximaRide: You have a new booking request from (" . $user->first_name . ")";
                $depatureDate = date('F d, Y H:i', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                $message = $title . "\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . "\n" . $user->first_name . ": " . $user->phone . "\nNumber of seats: " . $booking->seats . "\nClick here for accept(" . url("/accept/" . $booking->id) . ")\nClick here for reject(" . url("/reject/" . $booking->id) . ")";
        
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

            $ids = json_decode($request->booked_seat_ids, true);
            $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
            if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                foreach ($getSeatDetails as $key => $getSeatDetail) {
                    $getSeatDetail->status = 'booked';
                    $getSeatDetail->booking_id = $booking->id;
                    $getSeatDetail->user_id = $booking->user_id;
                    $getSeatDetail->save();
                }
            }

            // Check the ride first message
            $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
                $query->where('sender', $ride->added_by)
                    ->where('receiver', $booking->user_id);
            })->orWhere(function ($query) use ($booking, $ride) {
                $query->where('sender', $booking->user_id)
                    ->where('receiver', $ride->added_by);
            })->where('ride_id', $ride->id)->first();
            if (empty($rideFirstMessage)) {
                $message1 = Message::create([
                    'ride_id' => $ride->id,
                    'receiver' => $ride->added_by,
                    'sender' => $booking->user_id,
                    'message' => $request->driver_message,
                    'redirect' => '1',
                    'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                ]);
            }
            $message = Message::create([
                'ride_id' => $ride->id,
                'receiver' => $ride->added_by,
                'sender' => $booking->user_id,
                'message' => $request->driver_message,
                'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
            ]);

            $data = ['booking' => $booking];
            return $this->successResponse($data, $messages->booking_request_success_message ?? 'Your request has been successfully sent to the driver');
        }
    }

    public function updateBookingRequest(Request $request){
        $booking = Booking::where('id', $request->booking_id)->first();

        $getRideDetail = RideDetail::where('id', $booking->ride_detail_id)->first();
        
        $getPaymentMethodId = FeaturesSetting::where('slug', 'cash')->value('id');

        if ($booking) {
            $user_id = Auth::guard('sanctum')->user()->id;
            $user = User::where('id', $user_id)->first();
            $ride = Ride::where('id', $booking->ride_id)->first();
    
            // Calculate expiry time based on ride date and time
            $currentTime = Carbon::now();
            $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
            $difference = $rideDateTime->diffInHours($currentTime);
    
            if ($difference > 48) {
                $expiryTime = $currentTime->addHours(12);
            } elseif ($difference >= 24 && $difference <= 48) {
                $expiryTime = $currentTime->addHours(6);
            } elseif ($difference >= 6 && $difference < 24) {
                $expiryTime = $currentTime->addHours(2);
            } else {
                $expiryTime = $currentTime->addMinutes(30);
            }
            
            $findRidePage = null;
            $messages = null;
            $taxAmt = isset($request->tax_amount) ? $request->tax_amount : 0;
            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                if ($selectedLanguage) {
                    // Retrieve the HomePageSettingDetail associated with the selected language
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message', 'book_seat_message_end_part', 'seat_unavailable_message', 'verified_number_message','general_error_message','card_expiry_message','booking_not_update_message','block_booking_message')->first();
                    $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message', 'book_seat_message_end_part', 'seat_unavailable_message', 'verified_number_message','general_error_message','card_expiry_message','booking_not_update_message','block_booking_message')->first();
                    $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                }
            }


            if ($user->block_booking == '1') {
                return $this->apiErrorResponse(strip_tags($message->block_booking_message ?? null), 200);
            }

            $type = FeaturesSetting::whereId($request->type)->first();
            if (isset($type) && $type->slug === 'firm') {
                $setting = SiteSetting::first();
                $seat_price = $booking->price - ($booking->price * $setting->frim_discount / 100);
            } else {
                $seat_price = $booking->price;
            }

            
            $booking_credit = $request->booking_credit;
    
            $bookings = Booking::where('ride_id', $booking->ride_id)
                ->where('status', '!=', '3')
                ->where('status', '!=', '4')
                ->whereNotIn('id', [$request->booking_id])
                ->get();
    
            $seatsBooked = $bookings->sum('seats') + $request->seats;
            if ($seatsBooked > $ride->seats) {
                return $this->apiErrorResponse($messages->seat_unavailable_message ?? null, 200);
            }

            $secured_cash = null;
            $secured_cash_code = null;
    
            $transaction = Transaction::where('booking_id', $booking->id)->first();

            $transactionPrice = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('price');

            $transactionTaxSum = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('tax_amount');
    

            if ($booking->status === '0') {
                if ($request->seats > $booking->seats) {
                    $request->validate([
                        'payment_method' => $request->online_payment > '0' ? 'required' : 'nullable',
                        'paypal_id' => $request->online_payment > '0' && $request->payment_method == 'paypal' ? 'required' : 'nullable',
                        'card_id' => $request->online_payment > '0' && $request->payment_method == 'credit_card' ? 'required' : 'nullable',
                        'booking_credit' => 'required|max:25',
                        'type' => 'required',
                    ]);

                    if ($request->online_payment > '0') {
                        if ($request->payment_method == 'paypal') {
                            $newBooking = Booking::create([
                                'user_id' => $user->id,
                                'ride_id' => $ride->id,
                                'seats' => $request->seats,
                                'type' => $request->type,
                                'booked_on' => $currentTime,
                                'booking_credit' => $booking_credit,
                                'fare' => $seat_price * $request->seats,
                                'tax_amount' => $taxAmt,
                                'secured_cash' => $secured_cash,
                                'secured_cash_code' => $secured_cash_code,
                                'expires_at' => $expiryTime,    
                                'departure' => $getRideDetail->departure,
                                'destination' => $getRideDetail->destination,
                                'price' => $getRideDetail->price,
                                'ride_detail_id' => $getRideDetail->id
                            ]);

                            $transaction->update([
                                'booking_id' => $newBooking->id,
                            ]);

                            
                            $coffeeWallet = CoffeeWallet::where('booking_id', $booking->id)->first();
                            if(isset($coffeeWallet) && !is_null($coffeeWallet)){
                                $coffeeWallet->booking_id = $newBooking->id;
                                $coffeeWallet->save();
                            }

                        
                            $booking->delete();
        
                            $payable_amount = $request->online_payment - $transactionPrice;

                            if ($payable_amount > '0') {

                                $getBookingFeeSum = Transaction::where('booking_id', $newBooking->id)->sum('booking_fee');
                                $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);

                                if($ride->payment_method == $getPaymentMethodId){
                                    $transaction = Transaction::create([
                                        'booking_id' => $newBooking->id,
                                        'type' => '1',
                                        'booking_fee' => $currentBookingFee,
                                        'price' => '0',
                                        'paypal_id' => $request->input('paypal_id'),
                                        'tax_amount' => $taxAmt - $transactionTaxSum,
                                        'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                        'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                        'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax !="" ? $request->deduct_tax : NULL,
                                    ]);
                                }else{
        
                                    $transaction = Transaction::create([
                                        'booking_id' => $newBooking->id,
                                        'type' => '1',
                                        'booking_fee' => $currentBookingFee,
                                        'price' => $payable_amount,
                                        'paypal_id' => $request->input('paypal_id'),
                                        'tax_amount' => $taxAmt - $transactionTaxSum,
                                        'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                        'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                        'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax !="" ? $request->deduct_tax : NULL,
                                    ]);
                                }

                                if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                                    $coffeeWallet = CoffeeWallet::create([
                                        'booking_id' => $newBooking->id,
                                        'ride_id' => $ride->id,
                                        'user_id' => $newBooking->user_id,
                                        'cr_amount' => $currentBookingFee,
                                    ]);
                                }

                                
                            }
        
                            $notification = Notification::create([
                                'ride_id' => $ride->id,
                                'posted_by' => $user->id,
                                'message' =>  $request->seats . ' seats needed',
                                'status' => 'request',
                                'notification_type' => 'upcoming',
                                'ride_detail_id' => $newBooking->ride_detail_id,
                                'departure' => $newBooking->departure,
                                'destination' => $newBooking->destination
                            ]);
    
                            // Assuming $user and $fcmToken are defined
                            $fcmToken = $ride->driver->mobile_fcm_token;
                            $body = $notification->message;
    
                            if ($fcmToken) {
                                $fcmService = new FCMService();
                                // Send the booking notification
                                $fcmService->sendNotification($fcmToken, $body);
                            }
        
                            $data = ['first_name' => $ride->driver->first_name,'id' => $newBooking->id,'lang' => $selectedLanguage->abbreviation,'email' => $ride->driver->email,'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $newBooking->seats, 'booking_price' => $newBooking->booking_credit, 'total_price' => $newBooking->fare, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];
                            // Send booking request email
                            Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));


                            $ids = json_decode($request->booked_seat_ids, true);
                            $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                            if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                                foreach ($getSeatDetails as $key => $getSeatDetail) {
                                    $getSeatDetail->status = 'booked';
                                    $getSeatDetail->booking_id = $newBooking->id;
                                    $getSeatDetail->user_id = $newBooking->user_id;
                                    $getSeatDetail->save();
                                }
                            }

                            
                            SeatDetail::where('booking_id', $booking->id)->update(['booking_id', $newBooking->id]);
        
                            $data = ['booking' => $newBooking];
                            return $this->successResponse($data, $messages->book_seat_message . ' ' . $request->seats . ' ' . $messages->book_seat_message_end_part);
                        } elseif ($request->payment_method == 'credit_card') {
                            
            
                            try {

                                $paymentIntent = null;
                                $payable_amount = '0';

                                if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                                    
                                }else{
                                    // Retrieve the selected card from the database
                                    $card = Card::where('id', $request->card_id)
                                    ->where('user_id', $user->id)
                                    ->firstOrFail();
                    
                                    // Set your Stripe API key.
                                    Stripe::setApiKey(env('STRIPE_SECRET'));

                                    // Attach the payment method to the customer
                                    $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                                    $paymentMethod->attach(['customer' => $user->stripe_customer_id]);
                
                                    $payable_amount = $request->online_payment - $transactionPrice;
                
                                    if ($payable_amount > '0') {
                                        // Create a payment intent
                                        $stripePay = $payable_amount + ($taxAmt - $transactionTaxSum);
                                        $paymentIntent = PaymentIntent::create([
                                            'amount' => round(($stripePay * 100), 0),
                                            'currency' => 'usd',
                                            'customer' => $user->stripe_customer_id,
                                            'payment_method' => $paymentMethod->id,
                                            'off_session' => true,
                                            'confirm' => true,
                                        ]);
                                    }
                                }
                                // Payment successful, handle booking logic here            
                                $newBooking = Booking::create([
                                    'user_id' => $user->id,
                                    'ride_id' => $ride->id,
                                    'seats' => $request->seats,
                                    'type' => $request->type,
                                    'booked_on' => $currentTime,
                                    'booking_credit' => $booking_credit,
                                    'fare' => $seat_price * $request->seats,
                                    'tax_amount' => $taxAmt,
                                    'secured_cash' => $secured_cash,
                                    'secured_cash_code' => $secured_cash_code,
                                    'expires_at' => $expiryTime,    
                                    'departure' => $getRideDetail->departure,
                                    'destination' => $getRideDetail->destination,
                                    'price' => $getRideDetail->price,
                                    'ride_detail_id' => $getRideDetail->id
                                ]);

                                $transaction->update([
                                    'booking_id' => $newBooking->id,
                                ]);

                                $coffeeWallet = CoffeeWallet::where('booking_id', $booking->id)->first();
                                if(isset($coffeeWallet) && !is_null($coffeeWallet)){
                                    $coffeeWallet->booking_id = $newBooking->id;
                                    $coffeeWallet->save();
                                }

                                $getTopUpBalance = TopUpBalance::where('booking_id', $booking->id)->first();
                                if(isset($getTopUpBalance) && !empty($getTopUpBalance)){
                                    $getTopUpBalance->booking_id = $newBooking->id;
                                    $getTopUpBalance->save();
                                }

                                $booking->delete();
            
                                if ($payable_amount > '0') {

                                    $getBookingFeeSum = Transaction::where('booking_id', $newBooking->id)->sum('booking_fee');
                                    $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);

                                    if($ride->payment_method == $getPaymentMethodId){
                                        $transaction = Transaction::create([
                                            'booking_id' => $newBooking->id,
                                            'type' => '1',
                                            'booking_fee' => $currentBookingFee, 
                                            'price' => '0',
                                            'stripe_id' => isset($paymentIntent) && $paymentIntent != null ? $paymentIntent->id : NULL,
                                            'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                                            'tax_amount' => $taxAmt - $transactionTaxSum,
                                            'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                            'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                            'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax !="" ? $request->deduct_tax : NULL,
                                        ]);

                                        if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                                            $topUpBalance = TopUpBalance::create([
                                                'booking_id' => $newBooking->id,
                                                'user_id' => $user->id,
                                                'cr_amount' => $currentBookingFee + ($taxAmt - $transactionTaxSum),
                                                'added_date' => date('Y-m-d'),
                                            ]);
                                        }

                                    }else{
            
                                        $transaction = Transaction::create([
                                            'booking_id' => $newBooking->id,
                                            'type' => '1',
                                            'booking_fee' => $currentBookingFee, 
                                            'price' => $payable_amount,
                                            'stripe_id' => isset($paymentIntent) && $paymentIntent != null ? $paymentIntent->id : NULL,
                                            'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                                            'tax_amount' => $taxAmt - $transactionTaxSum,
                                            'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                            'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                            'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax !="" ? $request->deduct_tax : NULL,
                                        ]);

                                        if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                                            $topUpBalance = TopUpBalance::create([
                                                'booking_id' => $newBooking->id,
                                                'user_id' => $user->id,
                                                'cr_amount' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? ($payable_amount - $currentBookingFee) + ($taxAmt - $transactionTaxSum) : $payable_amount + ($taxAmt - $transactionTaxSum),
                                                'added_date' => date('Y-m-d'),
                                            ]);
                                        }
                                    }

                                    if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                                        $coffeeWallet = CoffeeWallet::create([
                                            'booking_id' => $newBooking->id,
                                            'ride_id' => $ride->id,
                                            'user_id' => $newBooking->user_id,
                                            'cr_amount' => $currentBookingFee,
                                        ]);
                                    }

                                    
                                }
            
                                $notification = Notification::create([
                                    'ride_id' => $ride->id,
                                    'posted_by' => $user->id,
                                    'message' =>  $request->seats . ' seats needed',
                                    'status' => 'request',
                                    'notification_type' => 'upcoming',
                                    'ride_detail_id' => $newBooking->ride_detail_id,
                                    'departure' => $newBooking->departure,
                                    'destination' => $newBooking->destination
                                ]);
        
                                // Assuming $user and $fcmToken are defined
                                $fcmToken = $ride->driver->mobile_fcm_token;
                                $body = $notification->message;
        
                                if ($fcmToken) {
                                    $fcmService = new FCMService();
                                    // Send the booking notification
                                    $fcmService->sendNotification($fcmToken, $body);
                                }
            
                                $data = ['first_name' => $ride->driver->first_name,'id' => $newBooking->id,'lang' => $selectedLanguage->abbreviation,'email' => $ride->driver->email,'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $newBooking->seats, 'booking_price' => $newBooking->booking_credit, 'total_price' => $newBooking->fare, 'from' => $newBooking->departure,'to' => $newBooking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];
                                // Send booking request email
                                Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));


                                $ids = json_decode($request->booked_seat_ids, true);
                                $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                                if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                                    foreach ($getSeatDetails as $key => $getSeatDetail) {
                                        $getSeatDetail->status = 'booked';
                                        $getSeatDetail->booking_id = $newBooking->id;
                                        $getSeatDetail->user_id = $newBooking->user_id;
                                        $getSeatDetail->save();
                                    }
                                }

                                
                                SeatDetail::where('booking_id', $booking->id)->update(['booking_id', $newBooking->id]);
            
                                $data = ['booking' => $newBooking];
                                return $this->successResponse($data, 'Your new request has been successfully sent to the driver');
                            } catch (\Stripe\Exception\CardException $e) {
                                // Handle Stripe card-related errors
                                if ($e->getError()->code === 'card_declined' && $e->getError()->decline_code === 'expired_card') {
                                    return $this->apiErrorResponse(strip_tags($message->card_expiry_message ??'The card has expired. Please use a different card'), 200);
                                }
                            
                                // General Stripe card-related error message
                                return $this->apiErrorResponse($e->getMessage(), 200);
                            } catch (\Stripe\Exception\ApiErrorException $e) {
                                // Handle error
                                return $this->apiErrorResponse($e->getMessage(), 200);
                            }
                        }
                    } else {
                        //abcvvvv
                        $booking->update([
                            'user_id' => $user->id,
                            'ride_id' => $ride->id,
                            'seats' => $request->seats,
                            'type' => $request->type,
                            'booked_on' => $currentTime,
                            'booking_credit' => $booking_credit,
                            'secured_cash' => $secured_cash,
                            'secured_cash_code' => $secured_cash_code,
                            'tax_amount' => $taxAmt,
                            'expires_at' => $expiryTime,
                        ]);

                        if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){

                            $getBookingFeeSum = Transaction::where('booking_id', $booking->id)->sum('booking_fee');
                            $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);
                            $transaction = Transaction::create([
                                'booking_id' => $booking->id,
                                'type' => '1',
                                'booking_fee' => $currentBookingFee,
                                'price' => '0',
                                'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false,
                                'tax_amount' => $taxAmt - $transactionTaxSum,
                                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                'tax_type' =>isset($request->tax_type) && $request->tax_type != "" ? $request->tax_type : NULL,
                                'deduct_type' => isset($request->deduct_tax) && $request->deduct_tax !="" ? $request->deduct_tax : NULL, 
                            ]);
        
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'cr_amount' => $currentBookingFee + ($taxAmt - $transactionTaxSum),
                            ]);
                        }
        
                        $notification = Notification::create([
                            'ride_id' => $ride->id,
                            'posted_by' => $user->id,
                            'message' =>  $request->seats . ' seats needed',
                            'status' => 'request',
                            'notification_type' => 'upcoming',
                            'ride_detail_id' => $booking->ride_detail_id,
                            'departure' => $booking->departure,
                            'destination' => $booking->destination
                        ]);
    
                        // Assuming $user and $fcmToken are defined
                        $fcmToken = $ride->driver->mobile_fcm_token;
                        $body = $notification->message;
    
                        if ($fcmToken) {
                            $fcmService = new FCMService();
                            // Send the booking notification
                            $fcmService->sendNotification($fcmToken, $body);
                        }
        
                        $data = ['first_name' => $ride->driver->first_name,'id' => $booking->id,'lang' => $selectedLanguage->abbreviation,'email' => $ride->driver->email,'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $request->seats, 'booking_price' => $booking_credit, 'total_price' => $booking->fare, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];
                        // Send booking request email
                        Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));

                        $ids = json_decode($request->booked_seat_ids, true);
                        $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                        if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                            foreach ($getSeatDetails as $key => $getSeatDetail) {
                                $getSeatDetail->status = 'booked';
                                $getSeatDetail->booking_id = $booking->id;
                                $getSeatDetail->user_id = $booking->user_id;
                                $getSeatDetail->save();
                            }
                        }
        
                        $data = ['booking' => $booking];
                        return $this->successResponse($data, $messages->book_seat_message . ' ' . $request->seats . ' ' . $messages->book_seat_message_end_part);
                    }
                }

                $data = ['booking' => $booking];
                return $this->successResponse($data, $messages->booking_not_update_message ?? 'You did not update your booking in this ride');
            } elseif ($booking->status === '1') {
                if ($request->seats > $booking->seats) {
                    $request->validate([
                        'payment_method' => $request->online_payment > '0' ? 'required' : 'nullable',
                        'paypal_id' => $request->online_payment > '0' && $request->payment_method == 'paypal' ? 'required' : 'nullable',
                        'card_id' => $request->online_payment > '0' && $request->payment_method == 'credit_card' ? 'required' : 'nullable',
                        'booking_credit' => 'required|max:25',
                        'type' => 'required',
                    ]);
    
                    if ($request->online_payment > '0') {
                        if ($request->payment_method == 'paypal') {
                            $booking_credit = $booking_credit - $booking->booking_credit;
                            $seats = $request->seats - $booking->seats;
                            $booking = Booking::create([
                                'user_id' => $user->id,
                                'ride_id' => $ride->id,
                                'seats' => $seats,
                                'type' => $request->type,
                                'booked_on' => $currentTime,
                                'booking_credit' => $booking_credit,
                                'fare' => $seat_price * $seats,
                                'secured_cash' => $secured_cash,
                                'secured_cash_code' => $secured_cash_code,
                                'expires_at' => $expiryTime,    
                                'departure' => $getRideDetail->departure,
                                'destination' => $getRideDetail->destination,
                                'price' => $getRideDetail->price,
                                'ride_detail_id' => $getRideDetail->id
                            ]);
            
                            $payable_amount = $request->online_payment - $transactionPrice;

                            if($ride->payment_method == $getPaymentMethodId){
                                $transaction = Transaction::create([
                                    'booking_id' => $booking->id,
                                    'type' => '1',
                                    'price' => '0',
                                    'booking_fee' => $booking_credit,
                                    'paypal_id' => $request->input('paypal_id'),
                                    'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false
                                ]);
                            }else{
                                $transaction = Transaction::create([
                                    'booking_id' => $booking->id,
                                    'type' => '1',
                                    'price' => $payable_amount,
                                    'booking_fee' => $booking_credit,
                                    'paypal_id' => $request->input('paypal_id'),
                                    'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false
                                ]);
                            }


                            if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                                $coffeeWallet = CoffeeWallet::create([
                                    'booking_id' => $booking->id,
                                    'ride_id' => $ride->id,
                                    'user_id' => $booking->user_id,
                                    'cr_amount' => $booking_credit,
                                ]);
                            }
                            
                            
            
                            $notification = Notification::create([
                                'ride_id' => $ride->id,
                                'posted_by' => $user->id,
                                'message' =>  $seats . ' seats needed',
                                'status' => 'request',
                                'notification_type' => 'upcoming',
                                'ride_detail_id' => $booking->ride_detail_id,
                                'departure' => $booking->departure,
                                'destination' => $booking->destination
                            ]);
        
                            // Assuming $user and $fcmToken are defined
                            $fcmToken = $ride->driver->mobile_fcm_token;
                            $body = $notification->message;
        
                            if ($fcmToken) {
                                $fcmService = new FCMService();
                                // Send the booking notification
                                $fcmService->sendNotification($fcmToken, $body);
                            }
                
                            $data = ['first_name' => $ride->driver->first_name,'id' => $booking->id,'lang' => $selectedLanguage->abbreviation,'email' => $ride->driver->email,'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $booking->booking_credit, 'total_price' => $booking->fare, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];                
                            // Send booking request email
                            Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));

                            $ids = json_decode($request->booked_seat_ids, true);
                            $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                            if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                                foreach ($getSeatDetails as $key => $getSeatDetail) {
                                    $getSeatDetail->status = 'booked';
                                    $getSeatDetail->booking_id = $booking->id;
                                    $getSeatDetail->user_id = $booking->user_id;
                                    $getSeatDetail->save();
                                }
                            }
                
                            $data = ['booking' => $booking];
                            return $this->successResponse($data, $messages->book_seat_message . ' ' . $request->seats . ' ' . $messages->book_seat_message_end_part);
                        } elseif ($request->payment_method == 'credit_card') {

                            $paymentIntent = null;
                            $payable_amount = '0';
            
                            try {

                                if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                                    
                                }else{
    
                                    // Retrieve the selected card from the database
                                    $card = Card::where('id', $request->card_id)
                                    ->where('user_id', $user->id)
                                    ->firstOrFail();
                    
                                    // Set your Stripe API key.
                                    Stripe::setApiKey(env('STRIPE_SECRET'));

                                    // Attach the payment method to the customer
                                    $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                                    $paymentMethod->attach(['customer' => $user->stripe_customer_id]);
                
                                    $payable_amount = $request->online_payment - $transaction->price;
                
                                    if ($payable_amount > '0') {
                                        // Create a payment intent
                                        $paymentIntent = PaymentIntent::create([
                                            'amount' => $payable_amount * 100,
                                            'currency' => 'usd',
                                            'customer' => $user->stripe_customer_id,
                                            'payment_method' => $paymentMethod->id,
                                            'off_session' => true,
                                            'confirm' => true,
                                        ]);
                                    }
                                }

                                
                                
                                // Payment successful, handle booking logic here
                                $booking_credit = $booking_credit - $booking->booking_credit;
                                $seats = $request->seats - $booking->seats;
                                $booking = Booking::create([
                                    'user_id' => $user->id,
                                    'ride_id' => $ride->id,
                                    'seats' => $seats,
                                    'type' => $request->type,
                                    'booked_on' => $currentTime,
                                    'booking_credit' => $booking_credit,
                                    'fare' => $seat_price * $seats,
                                    'secured_cash' => $secured_cash,
                                    'secured_cash_code' => $secured_cash_code,
                                    'expires_at' => $expiryTime,    
                                    'departure' => $getRideDetail->departure,
                                    'destination' => $getRideDetail->destination,
                                    'price' => $getRideDetail->price,
                                    'ride_detail_id' => $getRideDetail->id
                                ]);
            

                                if($ride->payment_method == $getPaymentMethodId){
                                    $transaction = Transaction::create([
                                        'booking_id' => $booking->id,
                                        'type' => '1',
                                        'price' => '0',
                                        'booking_fee' => $booking_credit,
                                        'stripe_id' => isset($paymentIntent) && $paymentIntent != null ? $paymentIntent->id : NULL,
                                        'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                                        'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false
                                    ]);

                                    if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                                        $topUpBalance = TopUpBalance::create([
                                            'booking_id' => $booking->id,
                                            'user_id' => $user->id,
                                            'cr_amount' => $booking_credit,
                                            'added_date' => date('Y-m-d'),
                                        ]);
                                    }

                                }else{
                                    $transaction = Transaction::create([
                                        'booking_id' => $booking->id,
                                        'type' => '1',
                                        'price' => $payable_amount,
                                        'booking_fee' => $booking_credit,
                                        'stripe_id' => isset($paymentIntent) && $paymentIntent != null ? $paymentIntent->id : NULL,
                                        'pay_by_account' => isset($request->booked_by_wallet) && $request->booked_by_wallet == "true" ? 1 : 0,
                                        'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false
                                    ]);

                                    if(isset($request->booked_by_wallet) && $request->booked_by_wallet == "true"){
                                        $topUpBalance = TopUpBalance::create([
                                            'booking_id' => $booking->id,
                                            'user_id' => $user->id,
                                            'cr_amount' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? $payable_amount - $booking_credit : $payable_amount,
                                            'added_date' => date('Y-m-d'),
                                        ]);
                                    }
                                }

                                if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                                    $coffeeWallet = CoffeeWallet::create([
                                        'booking_id' => $booking->id,
                                        'ride_id' => $ride->id,
                                        'user_id' => $booking->user_id,
                                        'cr_amount' => $booking_credit,
                                    ]);
                                }
                                
            
                                $notification = Notification::create([
                                    'ride_id' => $ride->id,
                                    'posted_by' => $user->id,
                                    'message' =>  $seats . ' seats needed',
                                    'status' => 'request',
                                    'notification_type' => 'upcoming',
                                'ride_detail_id' => $booking->ride_detail_id,
                                'departure' => $booking->departure,
                                'destination' => $booking->destination
                                ]);
        
                                // Assuming $user and $fcmToken are defined
                                $fcmToken = $ride->driver->mobile_fcm_token;
                                $body = $notification->message;
        
                                if ($fcmToken) {
                                    $fcmService = new FCMService();
                                    // Send the booking notification
                                    $fcmService->sendNotification($fcmToken, $body);
                                }
                
                                $data = ['first_name' => $ride->driver->first_name,'id' => $booking->id,'lang' => $selectedLanguage->abbreviation,'email' => $ride->driver->email,'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $booking->booking_credit, 'total_price' => $booking->fare, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];                
                                // Send booking request email
                                Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));


                                $ids = json_decode($request->booked_seat_ids, true);
                                $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                                if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                                    foreach ($getSeatDetails as $key => $getSeatDetail) {
                                        $getSeatDetail->status = 'booked';
                                        $getSeatDetail->booking_id = $booking->id;
                                        $getSeatDetail->user_id = $booking->user_id;
                                        $getSeatDetail->save();
                                    }
                                }
                
                                $data = ['booking' => $booking];
                                return $this->successResponse($data, 'Your new request has been successfully sent to the driver');
                            } catch (\Stripe\Exception\CardException $e) {
                                // Handle Stripe card-related errors
                                if ($e->getError()->code === 'card_declined' && $e->getError()->decline_code === 'expired_card') {
                                    return $this->apiErrorResponse(strip_tags($message->card_expiry_message ??'The card has expired. Please use a different card'), 200);
                                }
                            
                                // General Stripe card-related error message
                                return $this->apiErrorResponse($e->getMessage(), 200);
                            } catch (\Stripe\Exception\ApiErrorException $e) {
                                // Handle error
                                return $this->apiErrorResponse($e->getMessage(), 200);
                            }
                        }
                    } else {
                        $booking_credit = $booking_credit - $booking->booking_credit;
                        $seats = $request->seats - $booking->seats;
                        $booking = Booking::create([
                            'user_id' => $user->id,
                            'ride_id' => $ride->id,
                            'seats' => $seats,
                            'booked_on' => $currentTime,
                            'booking_credit' => $booking_credit,
                            'fare' => $seat_price * $seats,
                            'secured_cash' => $secured_cash,
                            'secured_cash_code' => $secured_cash_code,
                            'expires_at' => $expiryTime,    
                            'departure' => $getRideDetail->departure,
                            'destination' => $getRideDetail->destination,
                            'price' => $getRideDetail->price,
                            'ride_detail_id' => $getRideDetail->id
                        ]);


                        if(isset($request->coffee_from_wall) && $request->coffee_from_wall == "true"){
                            $transaction = Transaction::create([
                                'booking_id' => $booking->id,
                                'type' => '1',
                                'booking_fee' => $booking_credit,
                                'price' => '0',
                                'coffee_from_wall' => isset($request->coffee_from_wall) && $request->coffee_from_wall == "true" ? true : false 
                            ]);
        
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'cr_amount' => $booking_credit,
                            ]);
                        }
            
                        $notification = Notification::create([
                            'ride_id' => $ride->id,
                            'posted_by' => $user->id,
                            'message' =>  $seats . ' seats needed',
                            'status' => 'request',
                            'notification_type' => 'upcoming',
                                'ride_detail_id' => $booking->ride_detail_id,
                                'departure' => $booking->departure,
                                'destination' => $booking->destination
                        ]);
        
                        // Assuming $user and $fcmToken are defined
                        $fcmToken = $ride->driver->mobile_fcm_token;
                        $body = $notification->message;
        
                        if ($fcmToken) {
                            $fcmService = new FCMService();
                            // Send the booking notification
                            $fcmService->sendNotification($fcmToken, $body);
                        }
                
                        $data = ['first_name' => $ride->driver->first_name,'id' => $booking->id,'lang' => $selectedLanguage->abbreviation,'email' => $ride->driver->email,'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $booking->booking_credit, 'total_price' => $booking->fare, 'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y') ,'time' => $ride->time];                
                        // Send booking request email
                        Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));


                        $ids = json_decode($request->booked_seat_ids, true);
                        $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                        if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                            foreach ($getSeatDetails as $key => $getSeatDetail) {
                                $getSeatDetail->status = 'booked';
                                $getSeatDetail->booking_id = $booking->id;
                                $getSeatDetail->user_id = $booking->user_id;
                                $getSeatDetail->save();
                            }
                        }
                
                        $data = ['booking' => $booking];
                        return $this->successResponse($data, $messages->book_seat_message . ' ' . $request->seats . ' ' . $messages->book_seat_message_end_part);
                    }
                }

                $data = ['booking' => $booking];
                return $this->successResponse($data, $messages->booking_not_update_message ?? 'You did not update your booking in this ride');
            }
        }

        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? "Booking not found"), 404);
    }

    public function AcceptBookingRequest(Request $request){
        $booking = Booking::whereId($request->booking_id)->with('ride')->first();

        $selectedLanguage = app()->getLocale() ?? 'en';
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('request_expired_message','request_accept_message')->first();

        $user = Auth::guard('sanctum')->user();
        if ($booking->ride->added_by != $user->id) {
            return $this->apiErrorResponse('Booking request not found', 200);
        }

        if ($booking && $booking->status === '0') {
            $existingRecord = Booking::where('user_id', $booking->user_id)
                             ->where('status', '1')
                             ->where('ride_id', $booking->ride_id)
                             ->where('id', '!=', $booking->id)
                             ->first();

            if ($request->lang_id && $request->lang_id != 0) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
                }
            }

            if ($existingRecord) {
                // Existing record found, update it
                $existingRecord->update([
                    'seats' => DB::raw('seats + ' . $booking->seats), // Adding the seats value
                    'booking_credit' => DB::raw('booking_credit + ' . $booking->booking_credit), // Adding the booking_credit value
                ]);

                $transactions = Transaction::where('booking_id', $booking->id)->get();
                foreach ($transactions as $transaction) {
                    $transaction->update([
                        'booking_id' => $existingRecord->id,
                    ]);
                }

                $topUpBalances = TopUpBalance::where('booking_id', $booking->id)->get();
                foreach ($topUpBalances as $topUpBalance) {
                    $topUpBalance->update([
                        'booking_id' => $existingRecord->id,
                    ]);
                }

                $coffeeWallets = CoffeeWallet::where('booking_id', $booking->id)->get();
                foreach ($coffeeWallets as $coffeeWallet) {
                    $coffeeWallet->update([
                        'booking_id' => $existingRecord->id,
                    ]);
                }
                
                $booking->delete();

                $notification1 = Notification::create([
                    'ride_id' => $existingRecord->ride_id,
                    'posted_by' => $existingRecord->user_id,
                    'message' =>  'You have approved ' . $existingRecord->passenger->first_name . "\n" . 
                    'Seats booked: ' . numberToWords($existingRecord->seats),
                    'status' => 'completed',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $existingRecord->ride_detail_id,
                    'departure' => $existingRecord->departure,
                    'destination' => $existingRecord->destination
                ]);

                // Assuming $user and $fcmToken are defined
                $fcmToken = $user->mobile_fcm_token;
                $body = $notification1->message;

                if ($fcmToken) {
                    $fcmService = new FCMService();
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }

                $notification = Notification::create([
                    'type' => 2,
                    'ride_id' => $existingRecord->ride_id,
                    'posted_to' => $existingRecord->id,
                    'posted_by' => $existingRecord->ride->added_by,
                    'message' =>  'Booking request approved by' . $existingRecord->ride->driver->first_name,
                    'status' => 'completed',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $existingRecord->ride_detail_id,
                    'departure' => $existingRecord->departure,
                    'destination' => $existingRecord->destination
                ]);

                $user = User::whereId($existingRecord->user_id)->first();
                // Assuming $user and $fcmToken are defined
                $fcmToken = $user->mobile_fcm_token;
                $body = $notification->message;

                if ($fcmToken) {
                    $fcmService = new FCMService();
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }

                $data = ['first_name' => $existingRecord->passenger->first_name, 'last_name' => $existingRecord->passenger->last_name, 'email' => $existingRecord->passenger->email,'phone' => $existingRecord->passenger->phone, 'from' => $existingRecord?->departure,'to' => $existingRecord?->destination,'date' => Carbon::parse($existingRecord?->ride?->date)->format('F d, Y') ,'date' => $existingRecord?->ride?->time];
                // Send booking request email
                Mail::to($existingRecord->passenger->email)->queue(new AcceptBookingRequestMail($data));

                $phoneNumber = PhoneNumber::where('user_id', $existingRecord->user_id)->where('verified', '1')->where('default', '1')->first();

                if (!$phoneNumber) {
                    $phoneNumber = PhoneNumber::where('user_id', $existingRecord->user_id)->where('verified', '1')->first();
                }

                if ($phoneNumber && env('APP_ENV') != 'local') {
                    // Send the secured cash code via Twilio
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');
        
                    $twilio = new Client($sid, $token);
                    $to = $phoneNumber->phone;
                    
                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning ".$existingRecord->passenger->first_name."";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon ".$existingRecord->passenger->first_name."";
                    } else {
                        $title = "Good evening ".$existingRecord->passenger->first_name."";
                    }

                    $depatureDate = date('d F, Y H:i:s', strtotime(''.$existingRecord->ride->date.' '.$existingRecord->ride->time.''));

                    $message = "".$title."\nThe driver has approved your booking request\nTrip detail\nOrigin: ".$existingRecord->departure."\nDestination: ".$existingRecord->destination."\nDeparture date: ".$depatureDate."\nDriver name: ".$existingRecord->ride->driver->first_name."\nDriver phone number: ".$existingRecord->ride->driver->phone."\nVehicle info: ".$existingRecord->ride->make ?? ''.",".$existingRecord->ride->year ?? ''.",".$existingRecord->ride->modal ?? ''."\nVehicle type: ".$existingRecord->ride->car_type."";
        
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


                    if ($existingRecord->ride->payment_method == "35") {
                        $secured_cash = '1';
                        $secured_cash_code = rand(1000, 9999);
                        $to = $phoneNumber->phone;

                        $existingRecord->update([
                            'secured_cash' => $secured_cash,
                            'secured_cash_code' => $secured_cash_code,
                        ]);
    
                        $title = "";
                        $currentHour = date('H');
                        if ($currentHour >= 0 && $currentHour < 12) {
                            $title = "Good morning " . $existingRecord->passenger->first_name . "";
                        } elseif ($currentHour >= 12 && $currentHour < 17) {
                            $title = "Good afternoon " . $existingRecord->passenger->first_name . "";
                        } else {
                            $title = "Good evening " . $existingRecord->passenger->first_name . "";
                        }

                        $depatureDate = date('d F, Y H:i:s', strtotime('' . $existingRecord->ride->date . ' ' . $existingRecord->ride->time . ''));

                        $message = "" . $title . "\nYour secured cash code is: $secured_cash_code\nTrip detail\nOrigin: " . $existingRecord->departure . "\nDestination: " . $existingRecord->destination . "\nDeparture date: " . $depatureDate . "\Driver name: " . $existingRecord->ride->driver->first_name . "\nDriver phone number: " . $existingRecord->ride->driver->phone . "\nVehicle info: " . $existingRecord->ride->make ?? '' . "," . $existingRecord->ride->year ?? '' . "," . $existingRecord->ride->modal ?? '' . "\nVehicle type: " . $existingRecord->ride->car_type . "";

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

                        $driverPhoneNumber = PhoneNumber::where('user_id', $existingRecord->ride->driver->id)
                            ->where('default', '1')
                            ->first();
                        $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $existingRecord->ride->driver->phone;
                
                        $emailData = [
                            'first_name' => $existingRecord->passenger->first_name,
                            'secured_cash_code' => $secured_cash_code,
                            'driver_first_name' => $existingRecord->ride->driver->first_name,
                            'driver_last_name' => $existingRecord->ride->driver->last_name,
                            'driver_phone' => $driverPhoneToUse,
                            'driver_email' => $existingRecord->ride->driver->email,
                            'departure' => $existingRecord->departure,
                            'destination' => $existingRecord->destination,
                            'date' => Carbon::parse($existingRecord->ride->date)->format('F d, Y'),
                            'time' => $existingRecord->ride->time,
                            'seats' => $existingRecord->seats,
                            'booking_price' => $existingRecord->price * $existingRecord->seats
                        ];
                        
                        Mail::to($existingRecord->passenger->email)->queue(new SecuredCashPaymentCodeMail($emailData));
                    }
                }

                $bookings = Booking::where('ride_id', $existingRecord->ride_id)->where('status', '1')
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
                    }])
                    ->get();

                foreach ($bookings as $booking) {
                    // Calculate age
                    if ($booking->passenger->dob) {
                        $dob = Carbon::parse($booking->passenger->dob);
                        $booking->passenger->age = $dob->diffInYears(Carbon::now());
                    } else {
                        $booking->passenger->age = null; // Handle case where dob is not set
                    }

                    if ($booking->passenger->gender) {
                        $booking->passenger->gender = $booking->passenger->gender;

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
                
                $data = ['bookings' => $bookings];
                return $this->successResponse($data, strip_tags($message->request_accept_message ?? 'You have accepted the request successfully'));
            } else {
                $booking->update([
                    'status' => '1',
                    'expires_at' => null,
                ]);

                $notification1 = Notification::create([
                    'ride_id' => $booking->ride_id,
                    'posted_by' => $booking->user_id,
                    'message' =>  'You have approved ' . $booking->passenger->first_name . "\n" . 
                    'Seats booked: ' . numberToWords($booking->seats),
                    'status' => 'completed',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);

                // Assuming $user and $fcmToken are defined
                $fcmToken = $user->mobile_fcm_token;
                $body = $notification1->message;

                if ($fcmToken) {
                    $fcmService = new FCMService();
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }

                $notification = Notification::create([
                    'type' => 2,
                    'ride_id' => $booking->ride_id,
                    'posted_to' => $booking->id,
                    'posted_by' => $booking->ride->added_by,
                    'message' =>  'Booking request approved by' . $booking->ride->driver->first_name,
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

                $data = ['first_name' => $booking->passenger->first_name, 'last_name' => $booking->passenger->last_name, 'email' => $booking->passenger->email,'phone' => $booking->passenger->phone, 'from' => $booking?->departure,'to' => $booking?->destination,'date' => Carbon::parse($booking?->ride?->date)->format('F d, Y') ,'date' => $booking?->ride?->time];
                // Send booking request email
                Mail::to($booking->passenger->email)->queue(new AcceptBookingRequestMail($data));

                $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->where('default', '1')->first();

                if (!$phoneNumber) {
                    $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->first();
                }

                if ($phoneNumber && env('APP_ENV') != 'local') {
                    // Send the secured cash code via Twilio
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

                    $depatureDate = date('d F, Y H:i:s', strtotime(''.$booking->ride->date.' '.$booking->ride->time.''));

                    $message = "".$title."\nThe driver has approved your booking request\nTrip detail\nOrigin: ".$booking->departure."\nDestination: ".$booking->destination."\nDeparture date: ".$depatureDate."\nDriver name: ".$booking->ride->driver->first_name."\nDriver phone number: ".$booking->ride->driver->phone."\nVehicle info: ".$booking->ride->make ?? ''.",".$booking->ride->year ?? ''.",".$booking->ride->modal ?? ''."\nVehicle type: ".$booking->ride->car_type."";
        
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

                if ($booking->ride->payment_method == "35") {
                    $secured_cash = '1';
                    $secured_cash_code = rand(1000, 9999);
                    $to = $phoneNumber->phone;

                    $booking->update([
                        'secured_cash' => $secured_cash,
                        'secured_cash_code' => $secured_cash_code,
                    ]);
                    
                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning " . $booking->passenger->first_name . "";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $booking->passenger->first_name . "";
                    } else {
                        $title = "Good evening " . $booking->passenger->first_name . "";
                    }

                    $depatureDate = date('d F, Y H:i:s', strtotime('' . $booking->ride->date . ' ' . $booking->ride->time . ''));

                    $message = "" . $title . "\nYour secured cash code is: $secured_cash_code\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\Driver name: " . $booking->ride->driver->first_name . "\nDriver phone number: " . $booking->ride->driver->phone . "\nVehicle info: " . $booking->ride->make ?? '' . "," . $booking->ride->year ?? '' . "," . $booking->ride->modal ?? '' . "\nVehicle type: " . $booking->ride->car_type . "";

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

                $bookings = Booking::where('ride_id', $booking->ride_id)->where('status', '1')
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
                    }])
                    ->get();

                foreach ($bookings as $booking) {
                    // Calculate age
                    if ($booking->passenger->dob) {
                        $dob = Carbon::parse($booking->passenger->dob);
                        $booking->passenger->age = $dob->diffInYears(Carbon::now());
                    } else {
                        $booking->passenger->age = null; // Handle case where dob is not set
                    }

                    if ($booking->passenger->gender) {
                        $booking->passenger->gender = $booking->passenger->gender;

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

                $data = ['bookings' => $bookings];
                return $this->successResponse($data, strip_tags($message->request_accept_message ?? 'You have accepted the request successfully'));
            }
        } else {
            return $this->apiErrorResponse(strip_tags($message->request_expired_message ?? 'Request expired'), 200);
        }
    }

    public function RejectBookingRequest(Request $request){

        $selectedLanguage = app()->getLocale() ?? 'en';
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('request_expired_message')->first();

        $booking = Booking::with('ride')->whereId($request->booking_id)->first();
        $getPaymentMethodId = FeaturesSetting::where('slug', 'cash')->value('id');


        
        $user = Auth::guard('sanctum')->user();

        if ($booking->ride->added_by != $user->id) {
            return $this->apiErrorResponse('Booking request not found', 200);
        }

        

        if ($booking && $booking->status === '0') {
            $booking->update([
                'status' => '3',
                'expires_at' => null,
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


            $getTranscationsSum = Transaction::where('booking_id', $booking->id)->where('type', '3')->sum('price');

            $getTranscationsSum = $getTranscationsSum == null ? 0 : $getTranscationsSum;

            $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
            foreach ($transactions as $transaction) {
                if ($transaction) { 
                    
                    $transactionAmt = 0.0;
                    if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                        $transactionAmt = ((double)$transaction->price - $getTranscationsSum) - (double)$transaction->booking_fee;
                    }else{
                        $transactionAmt = (double)$transaction->price - $getTranscationsSum;
                    }
                    
                    $refundId = ""; 
                    if($transaction->pay_by_account == 0){
                        if ($transaction->paypal_id) {
                            $uniqueId = strtotime(date('Y-m-d H:i:s'));
                            $paypal = new PayPalClient;
                            $paypal->setApiCredentials(config('paypal'));
                            $token = $paypal->getAccessToken();
                            $paypal->setAccessToken($token);
                            $response = $paypal->refundCapturedPayment(
                                $transaction->paypal_id,
                                'Invoice-' . $uniqueId,
                                $booking->ride->payment_method != $getPaymentMethodId ? $transactionAmt : $transaction->booking_fee,
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
                                    'amount' => $booking->ride->payment_method != $getPaymentMethodId ? $transactionAmt * 100 : $transaction->booking_fee * 100, // Refund amount in cents
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
                            'dr_amount' => $booking->ride->payment_method != $getPaymentMethodId ? $transactionAmt : $transaction->booking_fee,
                            'added_date' => date('Y-m-d'),
                        ]);
                    }
                    
                    if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                        $coffeeWallet = CoffeeWallet::create([
                            'booking_id' => $booking->id,
                            'ride_id' => $booking->ride_id,
                            'user_id' => $booking->user_id,
                            'dr_amount' => $transaction->booking_fee,
                        ]);
                    }
                    

                    $newTransaction = Transaction::create([
                        'booking_id' => $transaction->booking_id,
                        'ride_id' => $booking->ride_id,
                        'parent_id' => $transaction->id,
                        'type' => '3',
                        'price' => $booking->ride->payment_method != $getPaymentMethodId ? $transaction->price : 0,
                        'booking_fee' => $booking->ride->payment_method == $getPaymentMethodId ? $transaction->booking_fee : 0,
                        'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                        'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                    ]);
                }
            }    
        } else {
            return $this->apiErrorResponse(strip_tags($message->request_expired_message ?? 'Request expired'), 200);
        }

        $notification = Notification::create([
            'type' => 2,
            'ride_id' => $booking->ride_id,
            'posted_to' => $booking->id,
            'posted_by' => $booking->ride->added_by,
            'message' =>  'Booking request declined',
            'status' => 'reject',
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

        $data = ['first_name' => $booking->passenger->first_name , 'seats' => $booking->seats, 'price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];

        // Send booking request email
        Mail::to($booking->passenger->email)->queue(new RejectBookingRequestMail($data));

        $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->where('default', '1')->first();

        if (!$phoneNumber) {
            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->first();
        }

        if ($phoneNumber && env('APP_ENV') != 'local') {
            // Send the secured cash code via Twilio
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

            $depatureDate = date('d F, Y H:i:s', strtotime(''.$booking->ride->date.' '.$booking->ride->time.''));

            $message = "".$title."\nDriver reject your booking request from this ride\nTrip detail\nOrigin: ".$booking->departure."\nDestination: ".$booking->destination."\nDeparture date: ".$depatureDate."\nDriver name: ".$booking->ride->driver->first_name."\nDriver phone number: ".$booking->ride->driver->phone."";
        
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

        $selectedLanguage = app()->getLocale();
        $messages = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reject_booking_message','general_error_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reject_booking_message','general_error_message')->first();
            }
        }

        $bookings = Booking::where('ride_id', $booking->ride_id)->where('status', '1')
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
                    }])
                    ->get();

        $data = ['booking' => $booking, 'bookings' => $bookings];
        return $this->successResponse($data, $messages->reject_booking_message ?? null);
    }


    public function seatOnHold(Request $request){
        $selectedLanguage = app()->getLocale();
        $messages = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('seat_hold_message','general_error_message', 'seat_hold_success_message','seat_booked_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('seat_hold_message','general_error_message', 'seat_hold_success_message','seat_booked_message')->first();
            }
        }
        
        $user = Auth::guard('sanctum')->user();
        $getSeatDetail = SeatDetail::where('id', $request->seat_id)->first();
        if(isset($getSeatDetail) && !empty($getSeatDetail)){
            if($getSeatDetail->status == "pending"){
                $getSeatDetail->user_id = $user->id;
                $getSeatDetail->status = 'hold';
                $getSeatDetail->save();

                $jobTime = 10;

                $getRide = Ride::where('id', $getSeatDetail->ride_id)->first();

                if (isset($getRide) && !empty($getRide)) {
                    $ride_time_str = $getRide->date . ' ' . $getRide->time;
                    $ride_timestamp = strtotime($ride_time_str);
                    $current_timestamp = time();
                
                    if ($ride_timestamp > $current_timestamp) {
                        $time_difference = $ride_timestamp - $current_timestamp;
                        $hours_difference = $time_difference / 3600;
                
                        $delay_minutes = ($hours_difference <= 1) ? 5 : 10;
                
                        UpdateSeatOnHold::dispatch($getSeatDetail->id)->delay(now()->addMinutes((int)$delay_minutes));
                    }
                }

                $data = ['getSeatDetail' => $getSeatDetail];
                return $this->successResponse($data, strip_tags($message->seat_hold_success_message ?? 'Seat on hold successfully'));
            }else if($getSeatDetail->status == "booked"){
                return $this->apiErrorResponse(strip_tags($message->seat_booked_message ?? 'Seat booked please select another seat'), 200);
            }else if($getSeatDetail->status == "hold"){

                if($request->type == "remove"){
                    $getSeatDetail->user_id = NULL;
                    $getSeatDetail->status = 'pending';
                    $getSeatDetail->save();
                    $data = ['getSeatDetail' => $getSeatDetail];
                    return $this->successResponse($data, 'Seat on pending successfully');
                }else{
                    return $this->apiErrorResponse($messages->seat_hold_message ?? null, 200);
                }

            }
        }else{
            return $this->apiErrorResponse(strip_tags($message->general_error_message ?? 'Seat not found'), 200);
        }
    }


    public function createPaymentIntent(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'amount' => 'required|integer',
            'currency' => 'required|string',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount, // e.g. 5000 = $50.00
                'currency' => $request->currency,
                'payment_method_types' => ['card'],
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => ['token' => $request->stripeToken]
                ],
                'confirmation_method' => 'automatic',
                'confirm' => true,
            ]);

            return response()->json([
                'paymentIntentId' => $paymentIntent->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function bookingNumberCheck(Request $request)
    {
        $selectedLanguage = app()->getLocale();

        $messages =[];
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('verified_number_message', 'add_your_phone')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('verified_number_message', 'add_your_phone')->first();
            }
        }
        
        $user = Auth::guard('sanctum')->user();

        $user = User::where('id', $user->id)->first();
        $phoneNumber = PhoneNumber::where('user_id', $user->id)->first();
        if (is_null($phoneNumber)) {
            return response()->json([
                'status' => false,
                'message' => $messages->add_your_phone ?? 'Add your phone number'
            ]);
        }
        $phoneVerification = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
        if (!$phoneVerification) {
            return response()->json([
                'status' => false,
                'message' => $messages->verified_number_message ?? 'Verify your phone number'
            ]);
        }
    
        return response()->json([
            'status' => true
        ]);
    }
}
