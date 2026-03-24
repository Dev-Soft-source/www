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

    protected function buildRideFeatureAssetMaps($optionGroups, string $groupKey): array
    {
        $options = collect($optionGroups[$groupKey] ?? []);

        return [
            'images' => $options
                ->mapWithKeys(function ($option) {
                    $icon = $option->icon ?? null;

                    return [(int) ($option->features_setting_id ?? $option->id ?? 0) => $icon ? asset('home_page_icons/' . $icon) : null];
                })
                ->all(),
            'tooltips' => $options
                ->mapWithKeys(function ($option) {
                    return [(int) ($option->features_setting_id ?? $option->id ?? 0) => $option->tooltip ?? null];
                })
                ->all(),
        ];
    }

    protected function getApiFindRidePage(?Language $language)
    {
        if (!$language) {
            return null;
        }

        $defaultLangId = $this->defaultLang?->id ?: $language->id;
        $findRidePage = FindRidePageSettingDetail::getByLanguageWithFallback($language->id, $defaultLangId);

        if ($findRidePage) {
            $findRidePage->mapMultipleOptionColumnsToDetails(
                ['ride_features', 'smoking', 'pets_allowed', 'payment_methods', 'animals', 'luggage', 'cancellation_policy'],
                $language->id,
                $defaultLangId
            );
        }

        return $findRidePage;
    }

    protected function getApiPostRidePage(?Language $language)
    {
        if (!$language) {
            return null;
        }

        $defaultLangId = $this->defaultLang?->id ?: $language->id;
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($language->id, $defaultLangId);

        if ($postRidePage) {
            $postRidePage->mapMultipleOptionColumnsToDetails(
                ['smoking', 'booking', 'payment_methods', 'animals', 'luggage', 'cancellation_policy'],
                $language->id,
                $defaultLangId
            );

            $this->hydrateLegacyFeatureOptions($postRidePage);
        }

        return $postRidePage;
    }

    protected function getApiGenderLabel(?Language $language)
    {
        if (!$language) {
            return null;
        }

        return Step1PageSettingDetail::where('language_id', $language->id)
            ->select('male_option_label', 'female_option_label', 'prefer_option_label')
            ->first();
    }

    protected function buildRideFeatureResponseMap($optionGroups, string $groupKey): array
    {
        return collect($optionGroups[$groupKey] ?? [])
            ->mapWithKeys(function ($option) {
                $featureId = (int) ($option->features_setting_id ?? $option->id ?? 0);
                $icon = $option->icon ?? null;

                return [$featureId => [
                    'id' => $featureId,
                    'title' => $option->name ?? null,
                    'image' => $icon ? asset('home_page_icons/' . $icon) : null,
                    'tooltip' => $option->tooltip ?? null,
                ]];
            })
            ->all();
    }

    protected function prepareRideFeatureContext($langId = null): array
    {
        $selectedLanguage = $this->resolveApiLanguage($langId);
        $findRidePage = $this->getApiFindRidePage($selectedLanguage);
        $postRidePage = $this->getApiPostRidePage($selectedLanguage);
        $genderLabel = $this->getApiGenderLabel($selectedLanguage);

        $defaultLanguage = $this->defaultLang;
        $rideFeatureOptionGroups = $this->getRideFeatureOptionGroups($selectedLanguage?->id, $defaultLanguage?->id);

        $bookingMethodAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'booking_method');
        $paymentMethodAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'payment_method');
        $smokingAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'smoking_allowed');
        $petsAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'pets_allowed');
        $luggageAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'luggage_size');

        return [
            'selectedLanguage' => $selectedLanguage,
            'findRidePage' => $findRidePage,
            'postRidePage' => $postRidePage,
            'genderLabel' => $genderLabel,
            'bookingMethodImages' => $bookingMethodAssets['images'],
            'bookingMethodTooltips' => $bookingMethodAssets['tooltips'],
            'paymentMethodImages' => $paymentMethodAssets['images'],
            'paymentMethodTooltips' => $paymentMethodAssets['tooltips'],
            'smokeImages' => $smokingAssets['images'],
            'smokeTooltips' => $smokingAssets['tooltips'],
            'petsImages' => $petsAssets['images'],
            'petsTooltips' => $petsAssets['tooltips'],
            'luggageImages' => $luggageAssets['images'],
            'luggageTooltips' => $luggageAssets['tooltips'],
            'featureResponseMap' => $this->buildRideFeatureResponseMap($rideFeatureOptionGroups, 'features'),
        ];
    }

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

        extract($this->prepareRideFeatureContext($request->lang_id));
    
        foreach ($bookings as $booking) {
            
            $booking->price = number_format($booking->price / 100, 2, '.', '');
            
            // Calculate seats left
            $bookedSeats = $booking->ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->withActivePassenger()
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

            $featureImages = $featureResponseMap;
    
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
                return optional($rating->ride)->added_by === optional($booking->ride)->added_by;
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

        extract($this->prepareRideFeatureContext($request->lang_id));

        foreach ($bookings as $booking) {
            // Calculate seats left
            $bookedSeats = $booking->ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->withActivePassenger()
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

            $featureImages = $featureResponseMap;

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
                return optional($rating->ride)->added_by === optional($booking->ride)->added_by;
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
                    $query->active(); // Exclude soft-deleted drivers
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

        extract($this->prepareRideFeatureContext($request->lang_id));

        foreach ($bookings as $booking) {
            // Calculate seats left
            $bookedSeats = $booking->ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->withActivePassenger()
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

            $featureImages = $featureResponseMap;

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
                return optional($rating->ride)->added_by === optional($booking->ride)->added_by;
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
                    'message' => getNotificationMessageText(
                        'booking_cancelled',
                        $booking->ride->driver,
                        [],
                        'Booking cancelled'
                    ),
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
            extract($this->prepareRideFeatureContext($request->lang_id));
            $messages = $this->getApiSuccessMessage($selectedLanguage);

            // Calculate seats left
            $bookedSeats = $booking->ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->withActivePassenger()
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

            $featureImages = $featureResponseMap;

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
                return optional($rating->ride)->added_by === optional($booking->ride)->added_by;
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


