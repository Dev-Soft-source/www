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
            $langId = $request->lang_id ?? 0;
            $rideId = $request->ride_id ?? null;
            $rideType = $request->ride_type ?? null;

            // Get selected language
            if ($langId && $langId != 0) {
                $selectedLanguage = Language::where('id', $langId)->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                $langId = $selectedLanguage ? $selectedLanguage->id : 0;
            }

            $user = Auth::guard('sanctum')->user();

            // Aggregate all data
            $data = [
                // 1. Labels and page settings
                'labels' => $this->getLabelsData($langId, $selectedLanguage),

                // 2. Post ride settings (pink ride, extra care, site settings)
                'postRideSettings' => $this->getPostRideSettingsData($user, $langId),

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
    private function getLabelsData($langId, $selectedLanguage)
    {
        $postRidePage = PostRidePageSettingDetail::where('language_id', $langId)->first();

        if (!$postRidePage && $selectedLanguage) {
            $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
        }

        if ($postRidePage) {
            // Add vehicle type values and labels
            $postRidePage->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
            $postRidePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($langId)->value('name');
            $postRidePage->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
            $postRidePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($langId)->value('name');
            $postRidePage->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
            $postRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($langId)->value('name');
            $postRidePage->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
            $postRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($langId)->value('name');
            $postRidePage->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
            $postRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($langId)->value('name');
            $postRidePage->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
            $postRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($langId)->value('name');
            $postRidePage->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
            $postRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($langId)->value('name');
            $postRidePage->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
            $postRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($langId)->value('name');
            $postRidePage->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
            $postRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($langId)->value('name');

            // Add error labels
            $postRideError = PostRidePageError::where('post_ride_page_setting_detail_id', $postRidePage->id)->first();
            if ($postRideError) {
                $postRidePage->from_error = $postRideError->from_error ?? null;
                $postRidePage->to_error = $postRideError->to_error ?? null;
                $postRidePage->pick_up_error = $postRideError->pick_up_error ?? null;
                $postRidePage->drop_off_error = $postRideError->drop_off_error ?? null;
                $postRidePage->date_error = $postRideError->date_error ?? null;
                $postRidePage->time_error = $postRideError->time_error ?? null;
                $postRidePage->recurring_type_error = $postRideError->recurring_type_error ?? null;
                $postRidePage->recurring_trips_error = $postRideError->recurring_trips_error ?? null;
                $postRidePage->meeting_drop_off_description_error = $postRideError->meeting_drop_off_description_error ?? null;
                $postRidePage->seats_error = $postRideError->seats_error ?? null;
                $postRidePage->seats_middle_error = $postRideError->seats_middle_error ?? null;
                $postRidePage->seats_back_error = $postRideError->seats_back_error ?? null;
                $postRidePage->vehicle_id_error = $postRideError->vehicle_id_error ?? null;
                $postRidePage->make_error = $postRideError->make_error ?? null;
                $postRidePage->model_error = $postRideError->model_error ?? null;
                $postRidePage->vehicle_type_error = $postRideError->vehicle_type_error ?? null;
                $postRidePage->color_error = $postRideError->color_error ?? null;
                $postRidePage->license_error = $postRideError->license_error ?? null;
                $postRidePage->year_error = $postRideError->year_error ?? null;
                $postRidePage->fuel_error = $postRideError->fuel_error ?? null;
                $postRidePage->photo_error = $postRideError->photo_error ?? null;
                $postRidePage->booking_method_error = $postRideError->booking_method_error ?? null;
                $postRidePage->anything_to_add_error = $postRideError->anything_to_add_error ?? null;
                $postRidePage->smoking_error = $postRideError->smoking_error ?? null;
                $postRidePage->animal_error = $postRideError->animal_error ?? null;
                $postRidePage->luggage_error = $postRideError->luggage_error ?? null;
                $postRidePage->price_error = $postRideError->price_error ?? null;
                $postRidePage->payment_method_error = $postRideError->payment_method_error ?? null;
                $postRidePage->booking_type_error = $postRideError->booking_type_error ?? null;
                $postRidePage->agree_terms_error = $postRideError->agree_terms_error ?? null;
            }
        }

        $messages = SuccessMessagesSettingDetail::where('language_id', $langId)->select('past_time_message', 'past_date_message')->first();

        // Set locale for translations
        if ($selectedLanguage) {
            App::setLocale($selectedLanguage->abbreviation ?? 'en');
        }

        // Validation messages using Laravel's trans() function
        $validationMessages = [
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

        $user = User::whereId($loggedInUser->id)->select('id', 'gender', 'email_verified', 'driver', 'dob', 'profile_complete', 'pink_ride', 'folks_ride')->first();

        $genderLabel = null;
        if ($langId && $langId != 0) {
            $genderLabel = Step1PageSettingDetail::where('language_id', $langId)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        // Calculate age
        if ($user->dob) {
            $dob = Carbon::parse($user->dob);
            $user->age = $dob->diffInYears(Carbon::now());
        } else {
            $user->age = null;
        }

        // Add gender label
        if ($user->gender && $genderLabel) {
            if ($user->gender === 'male') {
                $user->gender_label = $genderLabel->male_option_label;
            } elseif ($user->gender === 'female') {
                $user->gender_label = $genderLabel->female_option_label;
            } elseif ($user->gender === 'prefer not to say') {
                $user->gender_label = $genderLabel->prefer_option_label;
            }
        }

        // Calculate average rating
        $ratings = Rating::where('status', 1)->where('type', '1')->get();
        $filteredRatings = $ratings->filter(function ($rating) use ($user) {
            return $rating->ride && $rating->ride->added_by === $user->id;
        });
        $totalAverage = $filteredRatings->avg('average_rating');
        $user->average_rating = $totalAverage;

        // Check phone verification
        $phone_numbers = PhoneNumber::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        $phone_verified = '0';
        foreach ($phone_numbers as $phone_number) {
            if ($phone_number->verified === '1') {
                $phone_verified = $phone_number->verified;
                break;
            }
        }
        $user->phone_verified = $phone_verified;

        // Get cancellation and no-show counts
        $cancellationCount = CancellationHistory::where('user_id', $user->id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();
        $noShowsCount = NoShowHistory::where('user_id', $user->id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
        $totalNoOfRides = Ride::where('added_by', $user->id)
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
        $rides = Ride::where('added_by', $user_id)->get();

        if ($rides->isNotEmpty()) {
            $ratings = Rating::where(function ($query) use ($user_id) {
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

            if ($ratings->count() > 0) {
                $overallRating = $ratings->avg('average_rating');
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

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
        $preferencesOptions = PostRidePageSettingDetail::select(
            'post_ride_page_setting_detail.smoking_option1',
            'post_ride_page_setting_detail.smoking_option2',
            'post_ride_page_setting_detail.animals_option1',
            'post_ride_page_setting_detail.animals_option2',
            'post_ride_page_setting_detail.animals_option3'
        )
        ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
        ->where('languages.id', $langId)
        ->first();

        if ($preferencesOptions) {
            // Add labels for each option
            $preferencesOptions->smoking_option1_label = $preferencesOptions->smoking_option1 ?
                FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->smoking_option1)->whereLanguageId($langId)->value('name') : null;
            $preferencesOptions->smoking_option2_label = $preferencesOptions->smoking_option2 ?
                FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->smoking_option2)->whereLanguageId($langId)->value('name') : null;
            $preferencesOptions->animals_option1_label = $preferencesOptions->animals_option1 ?
                FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->animals_option1)->whereLanguageId($langId)->value('name') : null;
            $preferencesOptions->animals_option2_label = $preferencesOptions->animals_option2 ?
                FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->animals_option2)->whereLanguageId($langId)->value('name') : null;
            $preferencesOptions->animals_option3_label = $preferencesOptions->animals_option3 ?
                FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->animals_option3)->whereLanguageId($langId)->value('name') : null;
        }

        return ['preferencesOptions' => $preferencesOptions];
    }

    /**
     * Get ride features options
     */
    private function getRideFeaturesData($langId)
    {
        $featuresLabels = [];
        $featuresOptions = PostRidePageSettingDetail::select(
            'post_ride_page_setting_detail.features_option1', 'post_ride_page_setting_detail.features_option2',
            'post_ride_page_setting_detail.features_option3', 'post_ride_page_setting_detail.features_option4',
            'post_ride_page_setting_detail.features_option5', 'post_ride_page_setting_detail.features_option6',
            'post_ride_page_setting_detail.features_option7', 'post_ride_page_setting_detail.features_option8',
            'post_ride_page_setting_detail.features_option9', 'post_ride_page_setting_detail.features_option10',
            'post_ride_page_setting_detail.features_option11', 'post_ride_page_setting_detail.features_option12',
            'post_ride_page_setting_detail.features_option13', 'post_ride_page_setting_detail.features_option14',
            'post_ride_page_setting_detail.features_option15', 'post_ride_page_setting_detail.features_option16'
        )
        ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
        ->where('languages.id', $langId)
        ->first();

        if ($featuresOptions) {
            for ($i = 1; $i <= 16; $i++) {
                $optionField = "features_option{$i}";
                if ($featuresOptions->$optionField) {
                    $name = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->$optionField)
                        ->whereLanguageId($langId)
                        ->value('name');
                    $featuresLabels[] = $name ?? null;
                } else {
                    $featuresLabels[] = null;
                }
            }
        }

        return [
            'featuresOptions' => $featuresOptions ? array_values($featuresOptions->toArray()) : [],
            'featuresLabels' => $featuresLabels,
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
        $bookingLabels = [];
        $bookingOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.booking_option1', 'post_ride_page_setting_detail.booking_option2')
            ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
            ->where('languages.id', $langId)
            ->first();

        if ($bookingOptions) {
            if ($bookingOptions->booking_option1) {
                $name = FeaturesSettingDetail::whereFeaturesSettingId($bookingOptions->booking_option1)->whereLanguageId($langId)->value('name');
                $bookingLabels[] = $name ?? null;
            } else {
                $bookingLabels[] = null;
            }
            if ($bookingOptions->booking_option2) {
                $name = FeaturesSettingDetail::whereFeaturesSettingId($bookingOptions->booking_option2)->whereLanguageId($langId)->value('name');
                $bookingLabels[] = $name ?? null;
            } else {
                $bookingLabels[] = null;
            }
        }

        $bookingTooltips = PostRidePageSettingDetail::select('post_ride_page_setting_detail.booking_option1_tooltip', 'post_ride_page_setting_detail.booking_option2_tooltip')
            ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
            ->where('languages.id', $langId)
            ->first();

        return [
            'bookingOptions' => $bookingOptions ? array_values($bookingOptions->toArray()) : [],
            'bookingLabels' => $bookingLabels,
            'bookingTooltips' => $bookingTooltips ? array_values($bookingTooltips->toArray()) : [],
        ];
    }

    /**
     * Get cancellation options data
     */
    private function getCancellationOptionsData($langId)
    {
        $cancellationLabels = [];
        $cancellationOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.cancellation_policy_label1', 'post_ride_page_setting_detail.cancellation_policy_label2')
            ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
            ->where('languages.id', $langId)
            ->first();

        if ($cancellationOptions) {
            if ($cancellationOptions->cancellation_policy_label1) {
                $name = FeaturesSettingDetail::whereFeaturesSettingId($cancellationOptions->cancellation_policy_label1)->whereLanguageId($langId)->value('name');
                $cancellationLabels[] = $name ?? null;
            } else {
                $cancellationLabels[] = null;
            }
            if ($cancellationOptions->cancellation_policy_label2) {
                $name = FeaturesSettingDetail::whereFeaturesSettingId($cancellationOptions->cancellation_policy_label2)->whereLanguageId($langId)->value('name');
                $cancellationLabels[] = $name ?? null;
            } else {
                $cancellationLabels[] = null;
            }
        }

        $cancellationTooltips = PostRidePageSettingDetail::select('post_ride_page_setting_detail.cancellation_policy_label1_tooltip', 'post_ride_page_setting_detail.cancellation_policy_label2_tooltip')
            ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
            ->where('languages.id', $langId)
            ->first();

        return [
            'cancellationOptions' => $cancellationOptions ? array_values($cancellationOptions->toArray()) : [],
            'cancellationLabels' => $cancellationLabels,
            'cancellationTooltips' => $cancellationTooltips ? array_values($cancellationTooltips->toArray()) : [],
        ];
    }

    /**
     * Get luggage options data
     */
    private function getLuggageOptionsData($langId)
    {
        $luggageLabels = [];
        $luggageOptions = PostRidePageSettingDetail::select(
            'post_ride_page_setting_detail.luggage_option1',
            'post_ride_page_setting_detail.luggage_option2',
            'post_ride_page_setting_detail.luggage_option3',
            'post_ride_page_setting_detail.luggage_option4',
            'post_ride_page_setting_detail.luggage_option5'
        )
        ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
        ->where('languages.id', $langId)
        ->first();

        if ($luggageOptions) {
            for ($i = 1; $i <= 5; $i++) {
                $optionField = "luggage_option{$i}";
                if ($luggageOptions->$optionField) {
                    $name = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->$optionField)->whereLanguageId($langId)->value('name');
                    $luggageLabels[] = $name ?? null;
                } else {
                    $luggageLabels[] = null;
                }
            }
        }

        $luggageTooltips = PostRidePageSettingDetail::select(
            'post_ride_page_setting_detail.luggage_option1_tooltip',
            'post_ride_page_setting_detail.luggage_option2_tooltip',
            'post_ride_page_setting_detail.luggage_option3_tooltip',
            'post_ride_page_setting_detail.luggage_option4_tooltip',
            'post_ride_page_setting_detail.luggage_option5_tooltip'
        )
        ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
        ->where('languages.id', $langId)
        ->first();

        return [
            'luggageOptions' => $luggageOptions ? array_values($luggageOptions->toArray()) : [],
            'luggageLabels' => $luggageLabels,
            'luggageTooltips' => $luggageTooltips ? array_values($luggageTooltips->toArray()) : [],
        ];
    }

    /**
     * Get payment options data
     */
    private function getPaymentOptionsData($langId)
    {
        $paymentLabels = [];
        $paymentOptions = PostRidePageSettingDetail::select(
            'post_ride_page_setting_detail.payment_methods_option1',
            'post_ride_page_setting_detail.payment_methods_option2',
            'post_ride_page_setting_detail.payment_methods_option3'
        )
        ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
        ->where('languages.id', $langId)
        ->first();

        if ($paymentOptions) {
            for ($i = 1; $i <= 3; $i++) {
                $optionField = "payment_methods_option{$i}";
                if ($paymentOptions->$optionField) {
                    $name = FeaturesSettingDetail::whereFeaturesSettingId($paymentOptions->$optionField)->whereLanguageId($langId)->value('name');
                    $paymentLabels[] = $name ?? null;
                } else {
                    $paymentLabels[] = null;
                }
            }
        }

        $paymentTooltips = PostRidePageSettingDetail::select(
            'post_ride_page_setting_detail.payment_methods_option1_tooltip',
            'post_ride_page_setting_detail.payment_methods_option2_tooltip',
            'post_ride_page_setting_detail.payment_methods_option3_tooltip'
        )
        ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
        ->where('languages.id', $langId)
        ->first();

        return [
            'paymentOptions' => $paymentOptions ? array_values($paymentOptions->toArray()) : [],
            'paymentLabels' => $paymentLabels,
            'paymentTooltips' => $paymentTooltips ? array_values($paymentTooltips->toArray()) : [],
        ];
    }

    /**
     * Get ride data for editing/duplicating
     */
    private function getRideData($rideId, $userId, $rideType)
    {
        $ride = Ride::where('id', $rideId)->first();

        if (!$ride) {
            return null;
        }

        $rideDetail = RideDetail::where('ride_id', $rideId)->orderBy('id', 'asc')->get();

        return [
            'ride' => $ride,
            'rideDetail' => $rideDetail,
        ];
    }
}
