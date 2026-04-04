<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\PassengerCancelBookingMail;
use App\Mail\PassengerListMail;
use App\Models\Booking;
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
use App\Models\PhoneNumber;
use App\Models\TripsPageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Models\Step1PageSettingDetail;
use App\Models\Message;
use App\Models\BookingPageSettingDetail;
use App\Services\BookingCancellationService;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;

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

    public function CurrentTrips(Request $request, $kind = 'upcoming')
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $query = Booking::where('user_id', $user_id)->select('id', 'ride_id', 'seats', 'status', 'booking_credit', 'fare', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price', 'booked_on', 'type');

        switch ($kind) {
            case 'upcoming':
                // include past rides even if they are not marked as completed, as long as their departure time has passed
                $query->notRejected()
                    ->whereHas('ride', function ($query) {
                        $query->where(function ($query) {
                            $query->whereDate('completed_date', '>', now()->toDateString())
                                ->orWhere(function ($query) {
                                    $query->whereDate('completed_date', '=', now()->toDateString())
                                        ->whereTime('completed_time', '>=', now()->toTimeString());
                                });
                        });
                    });
                break;
            case 'completed':
                $query->notRejected()
                    ->whereHas('ride', function ($query) {
                        $query->where(function ($query) {
                            $query->whereDate('completed_date', '<', now()->toDateString())
                                ->orWhere(function ($query) {
                                    $query->whereDate('completed_date', '=', now()->toDateString())->whereTime('completed_time', '<', now()->toTimeString());
                                });
                        });
                    });

                break;
            case 'cancelled':
                $query->cancelled();
                break;
            default:
                break;
        }

        $bookings = $query->with(['ride.vehicle', 'ride' => function ($query) {
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

        $ratingsByRideOwner = Rating::where('status', 1)
            ->where('type', '1')
            ->with('ride:id,added_by')
            ->get()
            ->groupBy(function ($rating) {
                return optional($rating->ride)->added_by;
            });

        $bookedSeatsByRide = [];
        $driverAverageRatings = [];
        $driverDrivenRides = [];

        foreach ($bookings as $booking) {

            $booking->price = number_format($booking->price / 100, 2, '.', '');

            // Calculate seats left
            $rideId = $booking->ride->id;
            if (!array_key_exists($rideId, $bookedSeatsByRide)) {
                $bookedSeatsByRide[$rideId] = $booking->ride->bookings()
                    ->notRejected()
                    ->withActivePassenger()
                    ->sum('seats');
            }
            $bookedSeats = $bookedSeatsByRide[$rideId];
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
            $rideFeatures = collect($booking->ride->features)
                ->when(is_string($booking->ride->features), fn($c) => collect(explode('=', $booking->ride->features)))
                ->filter()
                ->values()
                ->all();
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

            $rideOwnerId = optional($booking->ride)->added_by;
            if (!array_key_exists($rideOwnerId, $driverAverageRatings)) {
                $driverAverageRatings[$rideOwnerId] = optional($ratingsByRideOwner->get($rideOwnerId))->avg('average_rating');
            }
            $booking->ride->driver->average_rating = $driverAverageRatings[$rideOwnerId];

            $driverId = optional($booking->ride->driver)->id;
            if ($driverId && !array_key_exists($driverId, $driverDrivenRides)) {
                $driverDrivenRides[$driverId] = $booking->ride->driver->rides()
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
            $booking->ride->driver->driven_rides = $driverId ? ($driverDrivenRides[$driverId] ?? 0) : 0;
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

        $localeAbbrev = 'en';
        if ($request->lang_id && (int) $request->lang_id !== 0) {
            $localeAbbrev = optional(Language::find($request->lang_id))->abbreviation ?? 'en';
        } else {
            $localeAbbrev = optional(Language::where('is_default', 1)->first())->abbreviation ?? 'en';
        }

        foreach ($bookings as $booking) {
            if ($booking->ride) {
                $this->appendRideDepartureDisplayForApi($booking->ride, $rideDetailPage, $localeAbbrev);
            }
        }

        $setting = ReviewSetting::getCached();

        $data = [
            'bookings' => $bookings,
            'setting' => $setting,
            'tripsPage' => $tripsPage,
            'rideDetailPage' => $rideDetailPage,
            'findRidePage' => $findRidePage,
        ];
        return $this->successResponse($data, 'Get my ' . $kind . ' trips');
    }

    public function PastTrips(Request $request)
    {
        return $this->CurrentTrips($request, 'completed');
    }

    public function CancelledTrips(Request $request)
    {
        return $this->CurrentTrips($request, 'cancelled');
    }


    public function cancelBooking(Request $request)
    {
        $request->validate([
            'cancel_seats' => 'required',
            'message' => 'required'
        ]);

        
        $user = Auth::guard('sanctum')->user();
        
        $getSetting = SiteSetting::getCached();
        $messages = $this->successMessage;
        
        $cancellationCount = $user->recentPassengerCancellationCount($getSetting->booking_cancel_duration);
        
        if ($cancellationCount >= $getSetting->booking_cancel_limit) {
            $bookingPage = BookingPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
            return $this->apiErrorResponse($bookingPage->booking_cancellation_limit_exceed ?? "Booking cancellation limit exceeded", 404);
        }
            
        $booking = Booking::with(['ride.driver', 'passenger'])->where('id', $request->booking_id)->first();
        if (!$booking) {
            return $this->apiErrorResponse($messages->general_error_message ?? "Booking not found", 404);
        }
        
        $originalSeats = (int) $booking->seats;
        $cancelSeats = (int) $request->cancel_seats;
        if ($cancelSeats <= 0 || $cancelSeats > $booking->seats) {
            return $this->apiErrorResponse($messages->general_error_message ?? "Invalid number of seats to cancel.", 400);
        }

        $ride = $booking->ride;

        $cancellationService = app(BookingCancellationService::class);

        $result = $cancellationService->cancelPassengerBookingWebFlow($booking, $ride, $cancelSeats, (int) $booking->user_id, $getSetting);
        $booking = $result['booking'];
        $payoutAmt = $result['payoutAmt'];
        $originalSeats = $result['originalSeats'];

        $this->notifyDriverPassengerCancelledWebFlow(
            $booking,
            $ride,
            $user,
            (string) $request->input('message'),
            (int) $originalSeats,
            (int) $cancelSeats,
            (float) $payoutAmt
        );
        
        return $this->successResponse([], $messages->cancel_booking_message ?? "null");
        

        
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

        $selectedLanguageForFind = $this->resolveApiLanguage($request->lang_id);
        $findRidePageForIndex = $this->getApiFindRidePage($selectedLanguageForFind);

        $data = [
            'tripsPage' => $tripsPage,
            'rideDetailPage' => $rideDetailPage,
            'findRidePage' => $findRidePageForIndex,
        ];
        return $this->successResponse($data, 'My trips page get successfully');
    }
}
