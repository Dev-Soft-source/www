<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\CancellationHistory;
use App\Models\FeaturesSettingDetail;
use App\Models\FolkRideSetting;
use App\Models\Language;
use App\Models\NoShowHistory;
use App\Models\PhoneNumber;
use App\Models\PinkRideSetting;
use App\Models\PostRidePageError;
use App\Models\PostRidePageSettingDetail;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\RideDetail;
use App\Models\SiteSetting;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Models\Vehicle;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class PostRideInitController extends Controller
{
    use StatusResponser;

    /**
     * Get all initialization data for Post Ride screen in a single API call.
     * This consolidates 11 separate API calls into one for better performance.
     *
     * Query Parameters:
     * - lang_id: Language ID (required)
     * - ride_id: Ride ID for editing/duplicating (optional)
     * - ride_type: "new" for duplicate, "update" for edit (optional)
     */
    public function getInitData(Request $request)
    {
        try {
            $rideId = $request->ride_id ?? null;
            $rideType = $request->ride_type ?? null;

            $langId = $this->selectedLanguage->id;

            $user = Auth::guard('sanctum')->user();

            // Aggregate all data
            $data = [
                // 1. Labels and page settings
                'labels' => $this->getLabelsData(),

                // 2. Post ride settings (pink ride, extra care, site settings)
                // 'postRideSettings' => $this->getPostRideSettingsData($user, $langId),

                // 3. User vehicles and rating
                'userVehicles' => $this->getUserVehiclesData($user),

                // 4. Preferences options (smoking, pets)
                'preferences' => $this->getPreferencesData($langId),

                // 5. Ride features options
                'rideFeatures' => $this->getRideFeaturesData($langId),

                // 6. Pink ride info
                'pinkRide' => $this->getPinkRideData($user),

                // 7. Extra care ride info
                'extraCareRide' => $this->getExtraCareRideData($user),

                // 8. Booking options
                'bookingOptions' => $this->getBookingOptionsData($langId),

                // 9. Cancellation options
                'cancellationOptions' => $this->getCancellationOptionsData($langId),

                // 10. Luggage options
                'luggage' => $this->getLuggageOptionsData($langId),

                // 11. Payment options
                'paymentOptions' => $this->getPaymentOptionsData($langId),
            ];

            // Conditional: Get ride data if ride_id is provided
            if ($rideId && $rideId != 0) {
                $data['rideData'] = $this->getRideData($rideId, $user->id, $rideType);
            }

            return $this->successResponse($data, 'Post ride initialization data retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve initialization data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get labels and page settings data
     */
    private function getLabelsData()
    {
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        
        $messages = $this->successMessage;

        // Validation messages using Laravel's trans() function
        $validationMessages = [
            'required' => __('validation.required'),
            'origin' => __('validation.custom.origin.message'),
            'destination' => __('validation.custom.destination.message'),
            'pickup' => __('validation.custom.pickup.required'),
            'dropoff' => __('validation.custom.dropoff.required'),
            'details' => __('validation.custom.details.required'),
            'date' => __('validation.custom.date.required'),
            'time' => __('validation.custom.time.required'),



            'required' => trans('validation.required'),
            'date' => trans('validation.date'),
            'date_format' => trans('validation.date_format'),
            'max.string' => trans('validation.max.string'),
            'string' => trans('validation.string'),
            'max_words' => trans('validation.max_words'),
            'numeric' => trans('validation.numeric'),
            'mimes' => trans('validation.mimes'),
            'max.file' => trans('validation.max.file'),
            'min' => trans('validation.min.numeric'),
        ];


        return [
            'postRidePage' => $postRidePage,
            'messages' => $messages,
            'validationMessages' => $validationMessages,
        ];
    }

    /**
     * Get post ride settings data (pink ride, extra care, site settings, user eligibility)
     */
    private function getPostRideSettingsData($loggedInUser, $langId)
    {
        $pinkRideSetting = PinkRideSetting::first();
        $folkRideSetting = FolkRideSetting::first();
        $siteSetting = SiteSetting::first();

        $user = User::whereId($loggedInUser->id)->first();

        // $genderLabel = null;
        // if ($langId && $langId != 0) {
        //     $genderLabel = Step1PageSettingDetail::where('language_id', $langId)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        // } else {
        //     $selectedLanguage = Language::where('is_default', 1)->first();
        //     if ($selectedLanguage) {
        //         $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        //     }
        // }

        // Calculate age
        if ($user->dob) {
            $dob = Carbon::parse($user->dob);
            $user->age = $dob->diffInYears(Carbon::now());
        } else {
            $user->age = null;
        }

        // // Add gender label
        // if ($user->gender && $genderLabel) {
        //     if ($user->gender === 'male') {
        //         $user->gender_label = $genderLabel->male_option_label;
        //     } elseif ($user->gender === 'female') {
        //         $user->gender_label = $genderLabel->female_option_label;
        //     } elseif ($user->gender === 'prefer not to say') {
        //         $user->gender_label = $genderLabel->prefer_option_label;
        //     }
        // }

        // Calculate average rating
        $user->average_rating = Rating::where('status', 1)
            ->where('type', '1')
            ->whereHas('ride', function ($query) use ($user) {
                $query->where('added_by', $user->id);
            })
            ->avg('average_rating');

        // Check phone verification
        $user->phone_verified = PhoneNumber::where('user_id', $user->id)
            ->where('verified', '1')
            ->exists() ? '1' : '0';

        // Get cancellation and no-show counts
        $now = Carbon::now();
        $threeMonthsAgo = $now->copy()->subMonths(3);
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        $cancellationCount = CancellationHistory::where('user_id', $user->id)
            ->where('type', 'driver')
            ->whereBetween('created_at', [$threeMonthsAgo, $now])
            ->whereNotNull('booking_id')
            ->count();

        $noShowsCount = NoShowHistory::where('user_id', $user->id)
            ->where('type', 'driver')
            ->whereBetween('created_at', [$threeMonthsAgo, $now])
            ->count();

        $totalNoOfRides = Ride::where('added_by', $user->id)
            ->where('status', '!=', 2)
            ->where(function ($query) use ($today, $currentTime) {
                $query->where(function ($query) use ($today, $currentTime) {
                    $query->whereDate('completed_date', '<', $today)
                        ->orWhere(function ($query) use ($today, $currentTime) {
                            $query->whereDate('completed_date', '=', $today)
                                ->whereTime('completed_time', '<', $currentTime);
                        });
                });
            })
            ->count();

        return [
            'user' => $user,
            'pinkRideSetting' => $pinkRideSetting,
            'folkRideSetting' => $folkRideSetting,
            'siteSetting' => $siteSetting,
            'cancellationCount' => $cancellationCount,
            'noShowsCount' => $noShowsCount,
            'totalNoOfRides' => $totalNoOfRides,
        ];
    }

    /**
     * Get user vehicles and rating data
     */
    private function getUserVehiclesData($user)
    {
        $user_id = $user->id;
        $vehicles = Vehicle::where('user_id', $user_id)->get();

        $overallRating = Rating::where('type', '1')
            ->where('status', 1)
            ->whereHas('ride', function ($query) use ($user_id) {
                $query->where('added_by', $user_id);
            })
            ->avg('average_rating') ?? 5;

        return [
            'vehicles' => $vehicles,
            'overallRating' => $overallRating,
        ];
    }

    /**
     * Get preferences options (smoking, pets)
     */
    private function getPreferencesData($langId)
    {
        $groups = $this->getRideFeatureOptionGroups($langId);
        $smokingOptions = collect($groups->get('smoking_allowed', collect()))
            ->sortBy('id')
            ->values();
        $petOptions = collect($groups->get('pets_allowed', collect()))
            ->sortBy('id')
            ->values();

        return [
            'preferencesOptions' => [
                'smoking_option1' => $smokingOptions->get(0)?->features_setting_id,
                'smoking_option2' => $smokingOptions->get(1)?->features_setting_id,
                'smoking_option1_label' => $smokingOptions->get(0)?->name,
                'smoking_option2_label' => $smokingOptions->get(1)?->name,
                'animals_option1' => $petOptions->get(0)?->features_setting_id,
                'animals_option2' => $petOptions->get(1)?->features_setting_id,
                'animals_option3' => $petOptions->get(2)?->features_setting_id,
                'animals_option1_label' => $petOptions->get(0)?->name,
                'animals_option2_label' => $petOptions->get(1)?->name,
                'animals_option3_label' => $petOptions->get(2)?->name,
            ],
        ];
    }

    /**
     * Get ride features options
     */
    private function getRideFeaturesData($langId)
    {
        $featureGroup = $this->getRideFeatureOptionGroups($langId)->get('features', collect());

        $orderedFeatures = collect($featureGroup)
            ->sortBy('id')
            ->filter(fn($feature) => $feature->id >= 1 && $feature->id <= 16)
            ->values();

        return [
            'featuresOptions' => $orderedFeatures->pluck('features_setting_id')->values()->all(),
            'featuresLabels' => $orderedFeatures->pluck('name')->values()->all(),
        ];
    }

    /**
     * Get pink ride data
     */
    private function getPinkRideData($loggedInUser)
    {
        $pinkRideSetting = PinkRideSetting::first();
        $user = User::whereId($loggedInUser->id)->select('id', 'gender', 'email_verified', 'driver', 'dob', 'profile_complete', 'pink_ride', 'folks_ride')->first();

        return [
            'pinkRideSetting' => $pinkRideSetting,
            'user' => $user,
        ];
    }

    /**
     * Get extra care ride data
     */
    private function getExtraCareRideData($loggedInUser)
    {
        $folkRideSetting = FolkRideSetting::first();
        $user = User::whereId($loggedInUser->id)->select('id', 'gender', 'email_verified', 'driver', 'dob', 'profile_complete', 'pink_ride', 'folks_ride')->first();

        return [
            'folkRideSetting' => $folkRideSetting,
            'user' => $user,
        ];
    }

    /**
     * Get booking options data
     */
    private function getBookingOptionsData($langId)
    {
        $bookingMethodOptions = collect($this->getRideFeatureOptionGroups($langId)->get('booking_method', collect()))
            ->sortBy('id')
            ->values();

        return [
            'bookingOptions' => $bookingMethodOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'bookingLabels' => $bookingMethodOptions
                ->pluck('name')
                ->values()
                ->all(),
            'bookingTooltips' => $bookingMethodOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];
    }

    /**
     * Get cancellation options data
     */
    private function getCancellationOptionsData($langId)
    {
        $cancellationOptions = collect($this->getRideFeatureOptionGroups($langId)->get('cancellation', collect()))
            ->sortBy('id')
            ->values();

        return [
            'cancellationOptions' => $cancellationOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'cancellationLabels' => $cancellationOptions
                ->pluck('name')
                ->values()
                ->all(),
            'cancellationTooltips' => $cancellationOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];
    }

    /**
     * Get luggage options data
     */
    private function getLuggageOptionsData($langId)
    {
        $luggageOptions = collect($this->getRideFeatureOptionGroups($langId)->get('luggage_size', collect()))
            ->sortBy('id')
            ->values();

        return [
            'luggageOptions' => $luggageOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'luggageLabels' => $luggageOptions
                ->pluck('name')
                ->values()
                ->all(),
            'luggageTooltips' => $luggageOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];
    }

    /**
     * Get payment options data
     */
    private function getPaymentOptionsData($langId)
    {
        $paymentOptions = collect($this->getRideFeatureOptionGroups($langId)->get('payment_method', collect()))
            ->sortBy('id')
            ->values();

        return [
            'paymentOptions' => $paymentOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'paymentLabels' => $paymentOptions
                ->pluck('name')
                ->values()
                ->all(),
            'paymentTooltips' => $paymentOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];
    }

    /**
     * Get ride data for editing/duplicating
     */
    private function getRideData($rideId, $userId, $rideType)
    {
        $ride = Ride::with([
            'detail',
            'bookings',
            'rideStops',
            'rideStopSegments.fromStop',
            'rideStopSegments.toStop',
        ])->where('added_by', $userId)->where('id', $rideId)->first();

        if (!$ride) {
            return null;
        }

        $ride->intermediate_stops = $this->extractIntermediateStopsForForm($ride);
        $ride->route_price_segments = $ride->rideStopSegments
            ->map(function ($segment) {
                return [
                    'from_label' => $segment->fromStop?->label,
                    'to_label' => $segment->toStop?->label,
                    'price_minor' => (int) ($segment->price_minor ?? 0),
                ];
            })
            ->filter(fn($segment) => !empty($segment['from_label']) && !empty($segment['to_label']))
            ->values()
            ->toArray();

        return [
            'ride' => $ride,
        ];
    }
}
