<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\ExtraCareRideMail;
use App\Mail\PinkExtraCareRideMail;
use App\Mail\PinkRideMail;
use App\Mail\RidePostedMail;
use App\Models\Booking;
use App\Models\CancelRideSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Models\Rating;
use App\Models\RecentSearch;
use App\Models\ReviewSetting;
use App\Models\Ride;
use App\Models\RideDetail;
use App\Models\City;
use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\Vehicle;
use App\Models\SiteSetting;
use App\Models\RideDetailPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\TripsPageSettingDetail;
use App\Models\MyPassengerSettingDetail;
use App\Models\NoShowHistory;
use App\Models\Notification;
use App\Models\PostRidePageError;
use App\Models\Step1PageSettingDetail;
use App\Models\SeatDetail;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RideController extends Controller
{
    use StatusResponser;

    public function SearchRide(Request $request)
    {
        $rides = collect();

        $selectedLanguage = app()->getLocale();
        $findRidePage = null;
        $postRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
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

        $user = Auth::guard('sanctum')->user();

        if ($request->from && $request->to) {
            // Check if the search already exists
            $existingSearch = RecentSearch::where('user_id', $user->id)
                ->where('from','like', '%'.$request->from.'%')
                ->where('to','like', '%'.$request->to.'%')
                ->first();

            if ($existingSearch) {
                // Update the updated_at timestamp
                $existingSearch->touch();
            } else {
                // Store recent search
                RecentSearch::create([
                    'from' => $request->from,
                    'to' => $request->to,
                    'user_id' => $user->id,
                ]);
            }

            $from = $request->from;
            $to = $request->to;
            $user_id = $user->id;

            $rides = Ride::whereHas('rideDetail', function($q) use($from, $to){
                $q->where('departure','like', '%'.$from.'%')
                ->where('destination','like', '%'.$to.'%')->where(function ($query) {
                    $query->where(function ($query) {
                        $query->whereDate('date', '>', now()->toDateString())
                            ->orWhere(function ($query) {
                                $query->whereDate('date', '=', now()->toDateString())
                                    ->whereTime('time', '>=', now()->toTimeString());
                            });
                    });
                });
            })
            ->with(['bookings' => function ($query) use($user_id){
                $query->select('id', 'ride_id', 'seats', 'user_id', 'booking_credit', 'status', 'secured_cash', 'secured_cash_code', 'fare', 'secured_cash_attempt_count', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
                    ->where('status', '<>', 0)
                    ->where('status', '<>', 3)
                    ->where('status', '<>', 4)
                    ->where('user_id', $user_id);
            }])
            ->with(['rideDetail' => function($q) use($from, $to){
                $q->where('departure','like', '%'.$from.'%')
                ->where('destination','like', '%'.$to.'%');
            }])
                ->where('status', '!=', 2)
                ->where('suspand', '!=', 1)
                ->where('vehicle_id', '!=', null)
                ->whereHas('driver', function ($query) {
                    $query->whereNull('deleted_at'); // Exclude soft-deleted drivers
                })
                ->with(['driver' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns you want to select
                }]);

            if ($request->keyword) {

                $keyword = $request->keyword;

                $rides = $rides->where(function ($query) use($keyword) {
                    $query->where('dropoff', 'like', "%$keyword%")
                    ->orWhere('pickup', 'like', "%$keyword%")
                    ->orWhere('details', 'like', "%$keyword%")
                    ->orWhere('notes', 'like', "%$keyword%");
                });
            }

            $user_id = $user->id;
            $currentDate = date('Y-m-d H:i:s');
            $userBookings = Booking::where('user_id', $user_id)
                        ->where('removed_permanently', 1)
                        ->where('block_date_time','>', $currentDate)
                        ->with('ride')
                        ->get();

            // Get the added_by values from userBookings
            $addedByValues = $userBookings->pluck('ride.added_by')->unique()->toArray();

            // Add additional condition to the rides query
            $rides = $rides->whereNotIn('added_by', $addedByValues);

            if ($request->date) {
                $dateForQuery = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');
                // Order rides to have the requested date first, then other dates
                $rides = $rides->orderByRaw("date = ? DESC, date ASC", [$dateForQuery]);
            }
            if ($request->driver_age) {
                $rides = $rides->whereHas('driver', function ($query) use ($request) {
                    $query->whereRaw('YEAR(CURDATE()) - YEAR(STR_TO_DATE(dob, "%M %d, %Y")) >= ?', [$request->driver_age]);
                });
            }
            if ($request->driver_phone == 1) {
                $rides = $rides->whereHas('driver', function ($query) {
                    $query->where('phone', '!=', '');
                });
            }
            if ($request->driver_name) {
                $rides = $rides->whereHas('driver', function ($query) use ($request) {
                    $query->where('first_name', $request->driver_name);
                });
            }
            if ($request->passenger_rating) {
                $rides->where('features', 'like', '%' . $request->passenger_rating . '%');
            }
            if ($request->payment_method) {
                $rides = $rides->where('payment_method', $request->payment_method);
            }
            if ($request->vehicle_type) {
                $rides = $rides->where('vehicle_type', $request->vehicle_type);
            }
            if ($request->pink_ride == '1') {
                $featureId1 = FeaturesSetting::whereSlug('pink_rides')->value('id');
                $rides->whereRaw("FIND_IN_SET(?, REPLACE(features, '=', ','))", [$featureId1]);
            }
            if ($request->extra_care == '1') {
                $featureId2 = FeaturesSetting::whereSlug('extra_care_rides')->value('id');
                $rides->whereRaw("FIND_IN_SET(?, REPLACE(features, '=', ','))", [$featureId2]);
            }
            if ($request->features) {
                $features = explode('=', $request->features);
                if(in_array($findRidePage->ride_features_option5, $features)){
                    $index = array_search($findRidePage->ride_features_option5,$features);
                    $features[$index] = $postRidePage->features_option5;
                }

                if(in_array($findRidePage->ride_features_option4, $features)){
                    $index = array_search($findRidePage->ride_features_option4,$features);
                    $features[$index] = $postRidePage->features_option4;
                }

                if (in_array($postRidePage->features_option4, $features)) {
                    $rides = $rides->where(function ($query) use ($postRidePage, $features) {
                        $query->where(function ($query) use ($postRidePage) {
                            $query->where('features', 'like', '%' . $postRidePage->features_option4 . '%')
                                ->orWhere('features', 'like', '%' . $postRidePage->features_option5 . '%');
                        });

                        // Check if any feature other than post ride features is present in the features array
                        if (count(array_diff($features, [$postRidePage->features_option4, $postRidePage->features_option5])) > 0) {
                            $query->where(function ($query) use ($features, $postRidePage) {
                                foreach ($features as $feature) {
                                    if ($feature != $postRidePage->features_option4 && $feature != $postRidePage->features_option5) {
                                        $query->where('features', 'like', '%' . $feature . '%');
                                    }
                                }
                            });
                        }
                    });
                }

                if(in_array($findRidePage->ride_features_option6, $features)){
                    $index = array_search($findRidePage->ride_features_option6,$features);
                    $features[$index] = $postRidePage->features_option6;
                }

                if(in_array($findRidePage->ride_features_option7, $features)){
                    $index = array_search($findRidePage->ride_features_option7,$features);
                    $features[$index] = $postRidePage->features_option7;
                }

                if (in_array($postRidePage->features_option6, $features)) {
                    $rides = $rides->where(function ($query) use ($postRidePage, $features) {
                        $query->where(function ($query) use ($postRidePage) {
                            $query->where('features', 'like', '%' . $postRidePage->features_option6 . '%')
                                ->orWhere('features', 'like', '%' . $postRidePage->features_option7 . '%');
                        });

                        // Check if any feature other than post ride features is present in the features array
                        if (count(array_diff($features, [$postRidePage->features_option6, $postRidePage->features_option7])) > 0) {
                            $query->where(function ($query) use ($features, $postRidePage) {
                                foreach ($features as $feature) {
                                    if ($feature != $postRidePage->features_option6 && $feature != $postRidePage->features_option7) {
                                        $query->where('features', 'like', '%' . $feature . '%');
                                    }
                                }
                            });
                        }
                    });
                }

                if (!in_array($postRidePage->features_option4, $features) && !in_array($postRidePage->features_option6, $features)) {
                    foreach ($features as $feature) {
                        $rides->whereRaw("FIND_IN_SET(?, REPLACE(features, '=', ','))", [$feature]);
                    }
                }
            }
            if ($request->luggage) {
                $luggages = explode(';', $request->luggage);
                $rides = $rides->whereIn('luggage', $luggages);
            }
            if ($request->smoking) {
                $smoking = explode(';', $request->smoking);
                if (in_array($findRidePage->smoking_option1, $smoking)) {
                    $rides = $rides->whereIn('smoke', $smoking);
                }
            }
            if ($request->pets) {
                $pets = explode(';', $request->pets);
                $rides = $rides->whereIn('animal_friendly', $pets);
            }

            $rides = $rides->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->orderBy('id', 'desc');

            if ($request->driver_rating) {
                $rides = $rides->get()->filter(function ($ride) use ($request) {
                    // Calculate average rating
                    $ratings = Rating::where('status', 1)
                        ->where('type', '1')
                        ->whereHas('ride', function ($query) use ($ride) {
                            $query->where('added_by', $ride->added_by);
                        })
                        ->get();

                    $overallRating = $ratings->avg('average_rating');

                    return $overallRating >= $request->driver_rating;
                });
            } else {
                $rides = $rides->get();
            }
            $rides = $rides->values()->all();

            $perPage = $request->paginate_limit ?? 10;
            $page = $request->page ?? 1;
            $rides = new LengthAwarePaginator(
                array_values(array_slice($rides, ($page - 1) * $perPage, $perPage)),
                count($rides),
                $perPage,
                $page,
                ['path' => $request->url()]
            );
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

        // Add the image URL to each ride
        foreach ($rides as $ride) {
            if ($selectedLanguage) {
                $smoke = FeaturesSettingDetail::whereFeaturesSettingId($ride->smoke)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                $animal_friendly = FeaturesSettingDetail::whereFeaturesSettingId($ride->animal_friendly)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                $booking_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_method)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                $booking_type = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_type)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                $luggage = FeaturesSettingDetail::whereFeaturesSettingId($ride->luggage)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                $payment_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->payment_method)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
            }

            $ride->booking_method_id = $ride->booking_method;
            $ride->feature_ids = $ride->features;

            $ride->booking_method_image = $bookingMethodImages[$ride->booking_method] ?? null;
            $ride->booking_method_tooltip = $bookingMethodTooltips[$ride->booking_method] ?? null;
            $ride->booking_method = $booking_method ?? null;
            $ride->booking_type = $booking_type ?? null;
            $ride->payment_method_image = $paymentMethodImages[$ride->payment_method] ?? null;
            $ride->payment_method_tooltip = $paymentMethodTooltips[$ride->payment_method] ?? null;
            $ride->payment_method = $payment_method ?? null;
            $ride->smoke_image = $smokeImages[$ride->smoke] ?? null;
            $ride->smoke_tooltip = $smokeTooltips[$ride->smoke] ?? null;
            $ride->smoke = $smoke ?? null;
            $ride->animal_friendly_image = $petsImages[$ride->animal_friendly] ?? null;
            $ride->animal_friendly_tooltip = $petsTooltips[$ride->animal_friendly] ?? null;
            $ride->animal_friendly = $animal_friendly ?? null;
            $ride->luggage_image = $luggageImages[$ride->luggage] ?? null;
            $ride->luggage_tooltip = $luggageTooltips[$ride->luggage] ?? null;
            $ride->luggage = $luggage ?? null;

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

            $bookedSeats = $ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->whereHas('passenger', function($query) {
                    $query->whereNull('deleted_at');
                })
                ->sum('seats');
            $ride->seats_left = intval($ride->seats) - intval($bookedSeats);
        }

        // Add driven rides count to each driver
        $rides->each(function ($ride) {
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

            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
                if ($selectedLanguage) {
                    $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
                }
            }

            if ($ride->driver->gender) {
                if ($ride->driver->gender === 'male') {
                    $ride->driver->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($ride->driver->gender === 'female') {
                    $ride->driver->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($ride->driver->gender === 'prefer not to say') {
                    $ride->driver->gender_label = $genderLabel->prefer_option_label ?? null;
                }
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($ride) {
                return $rating->ride->added_by === $ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $ride->driver->average_rating = $totalAverage;
        });

        $recentSearches = RecentSearch::where('user_id', $user->id)->orderBy('updated_at', 'desc')->limit(10)->get();

        $siteSettingDiscount = SiteSetting::value('frim_discount');
        $firm_cancellation_discount= $siteSettingDiscount;

        $data = ['rides' => $rides, 'recentSearches' => $recentSearches, 'firm_cancellation_discount' => $firm_cancellation_discount];
        return $this->successResponse($data, 'Success');
    }

    public function RideDetail(Request $request)
    {
        $rideDetailId = isset($request->ride_detail_id) ? $request->ride_detail_id : 0;
        $ride = Ride::where('id', $request->id);
        
        if($rideDetailId == 0){
            $ride = $ride->with(['rideDetail' => function($q){
                $q->where('default_ride', '1');
            }]);
        }else{
            $ride = $ride->with(['rideDetail' => function($q) use($rideDetailId){
                $q->where('id', $rideDetailId);
            }]);
        }

        $ride = $ride->with(['MoreRideDetail']);

        $ride = $ride->with(['driver' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])
            ->with('vehicle')
            ->with(['bookings' => function ($query) {
                // Select specific columns from bookings
                $query->select('id', 'ride_id', 'seats', 'user_id', 'booking_credit', 'status', 'secured_cash', 'secured_cash_code', 'fare', 'secured_cash_attempt_count', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
                    ->where('status', '<>', 0)
                    ->where('status', '<>', 3)
                    ->where('status', '<>', 4)
                    ->whereHas('passenger', function ($query) {
                        $query->whereNull('deleted_at');
                    })
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
                    }]);
            }])->first();

        $findRidePage = null;
        $postRidePage = null;
        $rideDetailPage = null;
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
            }if ($postRidePage) {
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
            $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $request->lang_id)->first();
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
                $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
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

        if ($ride) {
            // Calculate seats left
            $bookedSeats = $ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->whereHas('passenger', function ($query) {
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

            
            $ride->booking_method_id = $ride->booking_method;
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

            if ($request->lang_id && $request->lang_id != 0) {
                $smoke = FeaturesSettingDetail::whereFeaturesSettingId($ride->smoke)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $animal_friendly = FeaturesSettingDetail::whereFeaturesSettingId($ride->animal_friendly)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $booking_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_method)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $booking_type = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_type)->whereLanguageId($request->lang_id)->first() ?? null;
                $luggage = FeaturesSettingDetail::whereFeaturesSettingId($ride->luggage)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $payment_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->payment_method)->whereLanguageId($request->lang_id)->first() ?? null;
                $ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)->whereLanguageId($request->lang_id)->value('name') ?? null;
                $ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)->whereLanguageId($request->lang_id)->value('name') ?? null;
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $smoke = FeaturesSettingDetail::whereFeaturesSettingId($ride->smoke)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $animal_friendly = FeaturesSettingDetail::whereFeaturesSettingId($ride->animal_friendly)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $booking_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_method)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $booking_type = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_type)->whereLanguageId($selectedLanguage->id)->first() ?? null;
                    $payment_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->payment_method)->whereLanguageId($selectedLanguage->id)->first() ?? null;
                    $luggage = FeaturesSettingDetail::whereFeaturesSettingId($ride->luggage)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                    $ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)->whereLanguageId($selectedLanguage->id)->value('name') ?? null;
                }
            }


            if ($ride->payment_method) {
                $ride->payment_method_slug = $payment_method->featuresSetting->slug;
                $ride->payment_method = $payment_method->name;
            }

            if ($ride->luggage) {
                $ride->luggage = $rideDetailPage->luggage_label . $luggage;
            }

            if ($ride->booking_type) {
                $ride->booking_type_slug = $booking_type->featuresSetting->slug;
                if ($ride->booking_type == $postRidePage->cancellation_policy_label1) {
                    $ride->booking_type_tooltip = $postRidePage->cancellation_policy_label1_tooltip;
                } elseif ($ride->booking_type == $postRidePage->cancellation_policy_label2) {
                    $ride->booking_type_tooltip = $postRidePage->cancellation_policy_label2_tooltip;
                }
                $ride->booking_type = $booking_type->name;
            }

            if ($ride->booking_method) {
                $ride->booking_method = $booking_method;
            }

            if ($ride->smoke) {
                $ride->smoke = $rideDetailPage->smoking_label . $smoke;
            }

            if ($ride->animal_friendly) {
                $ride->animal_friendly = $rideDetailPage->pets_label . $animal_friendly;
            }

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

            if ($ride->driver->gender) {
                if ($ride->driver->gender === 'male') {
                    $ride->driver->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($ride->driver->gender === 'female') {
                    $ride->driver->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($ride->driver->gender === 'prefer not to say') {
                    $ride->driver->gender_label = $genderLabel->prefer_option_label ?? null;
                }
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

                if ($booking->passenger->gender) {
                    if ($booking->passenger->gender === 'male') {
                        $booking->passenger->gender_label = $genderLabel->male_option_label ?? null;
                    } elseif ($booking->passenger->gender === 'female') {
                        $booking->passenger->gender_label = $genderLabel->female_option_label ?? null;
                    } elseif ($booking->passenger->gender === 'prefer not to say') {
                        $booking->passenger->gender_label = $genderLabel->prefer_option_label ?? null;
                    }
                }

                $ratings = Rating::where('status', 1)->where('type', '2')->get();
                // Calculate average rating
                $filteredRatings = $ratings->filter(function ($rating) use ($booking) {
                    return $rating->booking->user_id === $booking->user_id;
                });

                $totalAverage = $filteredRatings->avg('average_rating');
                $booking->passenger->average_rating = $totalAverage;
            }

            // Separate bookings based on status
            $ride->booking_requests = $ride->bookings()->where('status', 0)
                ->with(['passenger' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
                }])->get();

            foreach ($ride->booking_requests as $booking_request) {
                // Calculate age
                if ($booking_request->passenger->dob) {
                    $dob = Carbon::parse($booking_request->passenger->dob);
                    $booking_request->passenger->age = $dob->diffInYears(Carbon::now());
                } else {
                    $booking_request->passenger->age = null; // Handle case where dob is not set
                }

                if ($booking_request->passenger->gender) {
                    if ($booking_request->passenger->gender === 'male') {
                        $booking_request->passenger->gender_label = $genderLabel->male_option_label ?? null;
                    } elseif ($booking_request->passenger->gender === 'female') {
                        $booking_request->passenger->gender_label = $genderLabel->female_option_label ?? null;
                    } elseif ($booking_request->passenger->gender === 'prefer not to say') {
                        $booking_request->passenger->gender_label = $genderLabel->prefer_option_label ?? null;
                    }
                }

                $ratings = Rating::where('status', 1)->where('type', '2')->get();
                // Calculate average rating
                $filteredRatings = $ratings->filter(function ($rating) use ($booking_request) {
                    return $rating->booking->user_id === $booking_request->user_id;
                });

                $totalAverage = $filteredRatings->avg('average_rating');
                $booking_request->passenger->average_rating = $totalAverage;
            }
        }

        $cancelRideSetting = CancelRideSetting::first();
        $reviewSetting = ReviewSetting::select('id', 'leave_review_days')->first();
        $siteSetting = SiteSetting::first();

        $rideDetailPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
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



        $data = ['ride' => $ride, 'cancelRideSetting' => $cancelRideSetting, 'reviewSetting' => $reviewSetting, 'siteSetting' => $siteSetting, 'rideDetailPage' => $rideDetailPage, 'tripsPage' => $tripsPage];
        return $this->successResponse($data, 'Success');
    }

    public function checkBooking(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
                if ($selectedLanguage) {
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('block_booking_message')->first();

                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('block_booking_message')->first();
                }
            }

        if ($user->block_booking == '1') {
            return $this->apiErrorResponse(strip_tags($message->block_booking_message ?? null), 200);
        }

        $hasBooking = Booking::where('ride_id', $request->id)
            ->where('user_id', $user_id)
            ->where('status', ['1', '2'])
            ->exists();

        // Initialize the seats variable
        $seats = 0;

        // If the user has a booking, sum up the seats column
        if ($hasBooking) {
            $seats = Booking::where('ride_id', $request->id)
                ->where('user_id', $user_id)
                ->whereIn('status', ['1', '2'])
                ->sum('seats');
        }

        $data = ['hasBooking' => $hasBooking, 'seats' => $seats];
        return $this->successResponse($data, 'Success');
    }

    public function coPassengers(Request $request)
    {
        $myPassengerPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $myPassengerPage = MyPassengerSettingDetail::where('language_id', $request->lang_id)->first();
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $myPassengerPage = MyPassengerSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        $ride = Ride::where('id', $request->ride_id)
            ->with(['bookings' => function ($query) {
                // Select specific columns from bookings
                $query->select('id', 'ride_id', 'seats', 'user_id', 'fare', 'secured_cash_attempt_count', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
                    ->where('status', '<>', 3)
                    ->where('status', '<>', 4)
                    ->whereHas('passenger', function ($query) {
                        $query->whereNull('deleted_at');
                    })
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
                    }]);
            }])->first();

        foreach ($ride->bookings as $booking) {
            // Calculate age
            if ($booking->passenger->dob) {
                $dob = Carbon::parse($booking->passenger->dob);
                $booking->passenger->age = $dob->diffInYears(Carbon::now());
            } else {
                $booking->passenger->age = null; // Handle case where dob is not set
            }

            if ($booking->passenger->gender) {
                if ($booking->passenger->gender === 'male') {
                    $booking->passenger->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($booking->passenger->gender === 'female') {
                    $booking->passenger->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($booking->passenger->gender === 'prefer not to say') {
                    $booking->passenger->gender_label = $genderLabel->prefer_option_label ?? null;
                }
            }
        }

        $data = ['bookings' => $ride->bookings, 'myPassengerPage' => $myPassengerPage];
        return $this->successResponse($data, 'Success');
    }

    public function noShow(Request $request){
        $request->validate([
            'ride_id' => 'required',
            'booking_id' => 'required',
            'user_id' => 'required',
            'type' => 'required',
        ]);

        $exist = NoShowHistory::where('ride_id', $request->ride_id)->where('booking_id', $request->booking_id)
            ->where('user_id', $request->user_id)->where('type', $request->type)->first();

        if ($exist) {
            $data = [];
            return $this->successResponse($data, 'Your response has already been submitted');
        }

        $response = NoShowHistory::create([
            'ride_id' => $request->ride_id,
            'booking_id' => $request->booking_id,
            'user_id' => $request->user_id,
            'type' => $request->type,
        ]);

        $data = ['response' => $response];
        return $this->successResponse($data, 'Response submitted successfully');
    }

    public function PostRide()
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $rides = Ride::where('added_by', $user_id)->get();

        if ($rides->isNotEmpty()) {
            // Fetch ratings where the driver_id matches the authenticated user's ID
            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

            if ($ratings->count() > 0) {
                // Calculate total average
                $overallRating = $ratings->avg('average_rating');
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

        $data = ['vehicles' => $vehicles, 'overallRating' => $overallRating];
        return $this->successResponse($data, 'Post ride page get successfully');
    }

    public function PostRideStore(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $adminSetting = SiteSetting::first();

        $user_id = $user->id;

        $message = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_post_message', 'ride_schedule_message', 'acc_suspend_message', 'overlap_ride_message','block_post_ride_message', 'not_allowed_post_ride_state_wise_message', 'profile_photo_required_message', 'ride_dead_time_text')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_post_message', 'ride_schedule_message', 'acc_suspend_message','overlap_ride_message','block_post_ride_message', 'not_allowed_post_ride_state_wise_message', 'profile_photo_required_message', 'ride_dead_time_text')->first();
            }
        }

        // Check if user has suspanded
        if ($user->suspand === '1') {
            return $this->apiErrorResponse(strip_tags($message->acc_suspend_message ?? null), 200);
        }

        if ($user->block_post_ride == '1') {
            return $this->apiErrorResponse(strip_tags($message->block_post_ride_message ?? null), 200);
        }

        if (!isset($user->profile_image) || $user->profile_image == '' || in_array(basename($user->profile_image), ['male.png', 'female.png', 'neutral.png'])) {
            return $this->apiErrorResponse(strip_tags($message->profile_photo_required_message ?? null), 200);
        }



        // Check if any existing ride has the same date and time
        if(isset($request->date) && isset($request->time)){
            $tripDate = date('Y-m-d', strtotime($request->date));
            $tripTime = date('H:i:s', strtotime($request->time));
            $rides = DB::table('rides')
                ->where('status', '!=', 2)
                ->where('added_by', $user_id)
                ->where(function($query) use ($tripDate, $tripTime) {
                    $query->where('date', '<=', $tripDate)
                        ->where('time', '<=', $tripTime);
                })
                ->where(function($query)use ($tripDate, $tripTime)  {
                    $query->where('destination_reached_date', '>=', $tripDate)
                        ->where('destination_reached_time', '>=', $tripTime);
                })
                ->first();
            if(isset($rides) && !empty($rides)){
                $data = ['ride' => $request->all(), 'uploaded_image' => $filename ?? null];
                return $this->apiErrorResponse(strip_tags($message->ride_schedule_message), 200, $data);
            }

            //Second
            $duration = 0;
            $distance= 0;
            $from = str_replace(" ", "", $request->from);
            $to = str_replace(" ", "", $request->to);
            $googleApiData = $this->getDataFromGoogleApi($from, $to);
            if(isset($googleApiData) && !empty($googleApiData)){
                $duration = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;

                $distance = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
            }

            if($distance != 0){
                $distance = round(($distance / 1000), 2);
            }

            if(isset($adminSetting)){

                if(isset($request->date) && isset($request->time)){
                    $rideDateTime = Carbon::parse("$request->date $request->time");
    
                    $apiTime = 0;
                    if($duration != 0){
                        $apiTime = round(($duration / 3600), 2);
                    }
    
                    // $rideDateTime->addHours($adminSetting->destination_hours);
                    // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                    $totalHours = $duration / 3600;  
                    $fullHours = floor($totalHours);  
                    $minutes = round(($totalHours - $fullHours) * 60); 
                    $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                        ->addMinutes($minutes);
    
                    $destinationReachedDate = $rideDateTime->toDateString();
                    $destinationReachedTime = $rideDateTime->toTimeString();
                }
    
            }

            $newStart = Carbon::parse("$request->date $request->time");
            $newEnd = Carbon::parse("$destinationReachedDate $destinationReachedTime");

            $rides = DB::table('rides')
                ->where('status', '!=', 2)
                ->where('added_by', $user_id)
                ->whereRaw("CONCAT(date, ' ', time) < ?", [$newEnd])
                ->whereRaw("CONCAT(destination_reached_date, ' ', destination_reached_time) > ?", [$newStart])
                ->first();
                
            if(isset($rides) && !empty($rides)){
                $data = ['ride' => $request->all(), 'uploaded_image' => $filename ?? null];
                return $this->apiErrorResponse(($message->overlap_ride_message ?? "this ride overlaps with an existing ride you already have"), 200, $data);
            }

            $rideDateTime = Carbon::parse($tripDate . ' ' . $tripTime);

            if ($rideDateTime->lte(Carbon::now()->addMinutes($adminSetting->ride_post_dead_time ?? 0))) {
                
                $data = ['ride' => $request->all(), 'uploaded_image' => $filename ?? null];
                return $this->apiErrorResponse(strip_tags($message->ride_dead_time_text ?? 'The ride time you selected is too close. Please select a time that is more than 15 minutes in the future'), 200, $data);
            }

        }

        $skip_vehicle = $request->filled('skip_vehicle') ? $request->skip_vehicle : '0';
        $add_vehicle = $request->filled('add_vehicle') ? $request->add_vehicle : '0';
        $added_vehicle = $request->filled('added_vehicle') ? $request->added_vehicle : '0';

        $recurring = $request->filled('recurring') ? '1' : '0';

        $customMessages = [
            'image.max' => 'Can not upload image size greater than 10MB',
        ];

        $request->validate([
            'from' => 'required',
            'to' => 'required',
            'pickup' => 'required',
            'dropoff' => 'required',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'details' => 'required|string|max_words:300',
            'seats' => 'required|numeric|min:1',
            'smoke' => 'required',
            'animal_friendly' => 'required',
            'features' => 'required|string',
            'booking_method' => 'required',
            'booking_type' => 'required',
            'luggage' => 'required',
            'price' => 'required|numeric|gt:0',
            'payment_method' => 'required',
            'notes' => 'nullable|string|max:300',
            'middle_seats' => 'required|numeric|min:1',
            'back_seats' => 'required|numeric|min:1',
            'agree_terms' => 'required',
            'image' => $add_vehicle !== '0' ? 'nullable|mimes:jpeg,png,jpg,gif|max:10240' : 'nullable|mimes:jpeg,png,jpg,gif|max:10240',
            'make' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'model' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'vehicle_type' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'year' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'color' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'license_no' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'car_type' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'recurring_type' => $recurring == '1' ? 'required' : 'nullable',
            'recurring_trips' => $recurring == '1' ? 'required' : 'nullable',
            'vehicle_id' => $added_vehicle == '1' ? 'required' : 'nullable',
        ], $customMessages);




        $nowDate = date('Y-m-d');
        $getRideCount = RideDetail::whereRaw('LOWER(`departure`) LIKE ? ', ['%' . $request->from . '%'])->where('date', $nowDate)->where('default_ride', '1')->whereHas('ride', function($q) use ($nowDate, $user_id){ $q->where('date', $nowDate)->where('added_by', $user_id); })->count();

        $getRideCount = isset($getRideCount) ? $getRideCount : 0;

        $fromArray = explode(',', $request->from);

        $getFromState = City::with('state:id,abrv,ride_limit')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ',['%'.$fromArray[0].'%'])->first();
        if(isset($getFromState) && !empty($getFromState)){
            if(isset($getFromState->state->ride_limit) && $getRideCount >= $getFromState->state->ride_limit){
                return $this->apiErrorResponse(strip_tags($message->not_allowed_post_ride_state_wise_message ?? null), 200);
            }
        }

        $make = '';
        $model = '';
        $vehicle_type = '';
        $year = '';
        $color = '';
        $license_no = '';
        $car_type = '';
        $filename = '';
        $filenameOriginal = '';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('car_images');
            $file->move($destination_path, $filename);

            $fileOriginal = $request->file('car_image_original');
            $filenameOriginal = $fileOriginal->getClientOriginalName();
            $destination_path = public_path('car_images');
            $fileOriginal->move($destination_path, $filenameOriginal);
        }



        $max_back_seats = $request->filled('max_back_seats') ? $request->max_back_seats : 0;
        $accept_more_luggage = $request->filled('accept_more_luggage') ? $request->accept_more_luggage : 0;
        $open_customized = $request->filled('open_customized') ? $request->open_customized : 0;

        if ($skip_vehicle == '1') {
            $make = '';
            $model = '';
            $vehicle_type = '';
            $year = '';
            $color = '';
            $license_no = '';
            $car_type = '';
        }

        if ($add_vehicle == '1') {
            $make = $request->make;
            $model = $request->model;
            $vehicle_type = $request->vehicle_type;
            $year = $request->year;
            $color = $request->color;
            $license_no = $request->license_no;
            $car_type = $request->car_type;
            $vehicle = Vehicle::create([
                'user_id' => $user_id,
                'make' => $request->make,
                'model' => $request->model,
                'type' => $request->vehicle_type,
                'liscense_no' => $request->license_no,
                'color' => $request->color,
                'year' => $request->year,
                'car_type' => $request->car_type,
                'image' => $filename,
            ]);
            $vehicle_id = $vehicle->id;
        }

        if ($added_vehicle == '1') {
            if ($request->vehicle_id !== '') {
                $vehicle = Vehicle::whereId($request->vehicle_id)->first();
                if ($vehicle) {
                    $make = $vehicle->make;
                    $model = $vehicle->model;
                    $vehicle_type = $vehicle->type;
                    $year = $vehicle->year;
                    $color = $vehicle->color;
                    $license_no = $vehicle->liscense_no;
                    $filename = basename($vehicle->image);
                    $car_type = $vehicle->car_type;
                    $vehicle_id = $vehicle->id;
                } else {
                    $make = '';
                    $model = '';
                    $vehicle_type = '';
                    $year = '';
                    $color = '';
                    $license_no = '';
                    $car_type = '';
                    $filename = '';
                }
            } else {
                $make = '';
                $model = '';
                $vehicle_type = '';
                $year = '';
                $color = '';
                $license_no = '';
                $car_type = '';
                $filename = '';
            }
        }

        if ($recurring == '1') {
            $recurring_type = $request->recurring_type;
            $recurring_trips = $request->recurring_trips;
        } else {
            $recurring_type = '';
            $recurring_trips = '';
        }

        $from = $to = "";

        $from = $request->from;
        $to = $request->to;


        if(isset($request->booking_type) && $request->booking_type != ""){

        }else{
            $getStandardId = FeaturesSetting::where('slug', 'standard')->value('id');
            if(isset($getStandardId) && !is_null($getStandardId)){
                $request->booking_type = $getStandardId;
            }
        }


        $initialRide = Ride::create([
            'departure' => '',
            'departure_lat' => '',
            'departure_lng' => '',
            'departure_place' => '',
            'departure_route' => '',
            'departure_zipcode' => '',
            'departure_city' => '',
            'departure_state' => '',
            'departure_state_short' => '',
            'departure_country' => '',

            'destination' => '',
            'destination_lat' => '',
            'destination_lng' => '',
            'destination_place' => '',
            'destination_route' => '',
            'destination_zipcode' => '',
            'destination_city' => '',
            'destination_state' => '',
            'destination_state_short' => '',
            'destination_country' => '',
            'total_distance' => '',
            'total_time' => '',
            'date' => Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d'),
            'time' => $request->time,

            'recurring' => $recurring,
            'recurring_type' => $recurring_type,
            'recurring_trips' => $recurring_trips,
            'details' => $request->details,
            'seats' => $request->seats,

            'skip_vehicle' => $skip_vehicle,
            'add_vehicle' => $add_vehicle,
            'added_vehicle' => $added_vehicle,
            'vehicle_id' => $vehicle_id ?? null,
            'make' => $make,
            'model' => $model,
            'vehicle_type' => $vehicle_type,
            'year' => $year,
            'color' => $color,
            'license_no' => $license_no,
            'car_type' => $car_type,
            'car_image' => $filename == "" ? NULL : $filename,
            'car_image_original' => $filenameOriginal == "" ? NULL : $filenameOriginal,
            'smoke' => $request->smoke,
            'animal_friendly' => $request->animal_friendly,
            'features' => $request->features,
            'booking_method' => $request->booking_method,
            'booking_type' => $request->booking_type,
            'max_back_seats' => $max_back_seats,
            'luggage' => $request->luggage,
            'accept_more_luggage' => $accept_more_luggage,
            'open_customized' => $open_customized,
            'price' => "",
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'added_by' => $user_id,
            'until_date' => null,
            'until_limit' => '',

            'pickup' => $request->pickup,
            'dropoff' => $request->dropoff,

            'middle_seats' => $request->middle_seats,
            'back_seats' => $request->back_seats,
        ]);

        $rideDetail = new RideDetail();
        $rideDetail->ride_id = $initialRide->id;
        $rideDetail->departure = $from;
        $rideDetail->destination = $to;
        $rideDetail->default_ride = 1;
        $rideDetail->total_distance = $distance;
        $rideDetail->total_duration = $duration;
        $rideDetail->price = $request->price;
        $rideDetail->time = $request->time;
        $rideDetail->date = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');


        if(isset($adminSetting)){

            if(isset($initialRide->date) && isset($initialRide->time)){
                $rideDateTime = Carbon::parse("$initialRide->date $initialRide->time");

                $apiTime = 0;
                if($duration != 0){
                    $apiTime = round(($duration / 3600), 2);
                }

                // $rideDateTime->addHours($adminSetting->destination_hours);
                // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                $totalHours = $duration / 3600;  
                $fullHours = floor($totalHours);  
                $minutes = round(($totalHours - $fullHours) * 60); 
                $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                    ->addMinutes($minutes);

                $destinationReachedDate = $rideDateTime->toDateString();
                $destinationReachedTime = $rideDateTime->toTimeString();


                $rideDateTime->addHours($adminSetting->ride_completed_hours);
                $completedDate = $rideDateTime->toDateString();
                $completedTime = $rideDateTime->toTimeString();

                $initialRide->completed_date = $completedDate ?? '';
                $initialRide->completed_time = $completedTime;
                $initialRide->destination_reached_date = $destinationReachedDate;
                $initialRide->destination_reached_time = $destinationReachedTime;
                $initialRide->save();



                $rideDetail->destination_time = $destinationReachedTime;
                $rideDetail->destination_date = $destinationReachedDate;
                $rideDetail->completed_time = $completedTime;
                $rideDetail->completed_date = $completedDate;
            }

        }
        $rideDetail->save();

        $fromSpots = [];
        if(isset($request->from_spot)){
            $fromSpots = json_decode($request->from_spot, true);
        }

        

        if(isset($request->from_spot) && !empty($request->from_spot)){
            foreach ($fromSpots as $key => $from_spot) {

                $toSpots = json_decode($request->to_spot, true);
                $priceSpots = json_decode($request->price_spot, true);

                $duration = 0;
                $distance= 0;

                $fromArray = explode(',', $fromSpots[$key]);
                $toArray = explode(',', $toSpots[$key]);

                $googleApiData = $this->getDataFromGoogleApi($fromSpots[$key], $toSpots[$key]);
                if(isset($googleApiData) && !empty($googleApiData)){
                    $duration = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;

                    $distance = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
                }

                if($distance != 0){
                    $distance = round(($distance / 1000), 2);
                }

                $fromRideDetail = $toRideDetail = "";
                $fromRideDetail = $fromSpots[$key];
                $toRideDetail = $toSpots[$key];
                

                $rideDetail = new RideDetail();
                $rideDetail->ride_id = $initialRide->id;
                $rideDetail->departure = $fromRideDetail;
                $rideDetail->destination = $toRideDetail;
                $rideDetail->default_ride = 0;
                $rideDetail->total_distance = $distance;
                $rideDetail->total_duration = $duration;
                $rideDetail->price = $priceSpots[$key];
                $rideDetail->time = $request->time;
                $rideDetail->date = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');

                if(isset($adminSetting)){

                    if(isset($initialRide->date) && isset($initialRide->time)){
                        $rideDateTime = Carbon::parse("$initialRide->date $initialRide->time");
                        $apiTime = 0;
                        if($duration != 0){
                            $apiTime = round(($duration / 3600), 2);
                        }

                        // $rideDateTime->addHours($adminSetting->destination_hours);
                        // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                        $totalHours = $duration / 3600;  
                        $fullHours = floor($totalHours);  
                        $minutes = round(($totalHours - $fullHours) * 60); 
                        $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                            ->addMinutes($minutes);

                        $destinationReachedDate = $rideDateTime->toDateString();
                        $destinationReachedTime = $rideDateTime->toTimeString();


                        $rideDateTime->addHours($adminSetting->ride_completed_hours);
                        $completedDate = $rideDateTime->toDateString();
                        $completedTime = $rideDateTime->toTimeString();

                        $rideDetail->destination_time = $destinationReachedTime;
                        $rideDetail->destination_date = $destinationReachedDate;
                        $rideDetail->completed_time = $completedTime;
                        $rideDetail->completed_date = $completedDate;
                    }

                }
                $rideDetail->save();
            }
        }





        //Add Seat Detail

        for ($i=1; $i <= $initialRide->seats; $i++) {
            $seatDetail = new SeatDetail;
            $seatDetail->ride_id = $initialRide->id;
            $seatDetail->seat_number = $i;
            $seatDetail->status = 'pending';
            $seatDetail->save();
        }



        $recurring_id = $initialRide->id;

        // Check if the ride is recurring
        if ($recurring !== '0') {
            // Determine the frequency and number of recurring trips
            $frequency = $request->input('recurring_type');
            $numRecurringTrips = $request->input('recurring_trips');

            // Calculate the date interval based on the frequency
            $dateInterval = ($frequency === 'Daily') ? 'P1D' : 'P7D';

            // Create additional rides based on the recurring settings
            for ($i = 1; $i <= $numRecurringTrips; $i++) {

                $nextDate = Carbon::parse($initialRide->date)->add(new \DateInterval($dateInterval));
                $nextCompletedDate = Carbon::parse($initialRide->completed_date)->add(new \DateInterval($dateInterval));
                $nextDestinationReachedDate = Carbon::parse($initialRide->destination_reached_date)->add(new \DateInterval($dateInterval));

                $initialRide = Ride::create([
                    'departure' => "",
                    'departure_lat' => '',
                    'departure_lng' => '',
                    'departure_place' => '',
                    'departure_route' => '',
                    'departure_zipcode' => '',
                    'departure_city' => '',
                    'departure_state' => '',
                    'departure_state_short' => '',
                    'departure_country' => '',

                    'destination' => "",
                    'destination_lat' => '',
                    'destination_lng' => '',
                    'destination_place' => '',
                    'destination_route' => '',
                    'destination_zipcode' => '',
                    'destination_city' => '',
                    'destination_state' => '',
                    'destination_state_short' => '',
                    'destination_country' => '',

                    'total_distance' => "",
                    'total_time' => "",
                    'date' => $nextDate->format('Y-m-d'),
                    'time' => $initialRide->time,
                    'completed_date' => $nextCompletedDate->format('Y-m-d'),
                    'completed_time' => $initialRide->completed_time,
                    'destination_reached_date' => $nextDestinationReachedDate->format('Y-m-d'),
                    'destination_reached_time' => $initialRide->destination_reached_time,

                    'recurring' => $recurring,
                    'recurring_type' => '',
                    'recurring_trips' => '',
                    'recurring_id' => $recurring_id,
                    'details' => $request->details,
                    'seats' => $request->seats,

                    'skip_vehicle' => $skip_vehicle,
                    'add_vehicle' => $add_vehicle,
                    'added_vehicle' => $added_vehicle,
                    'vehicle_id' => $vehicle_id ?? null,
                    'make' => $make,
                    'model' => $model,
                    'vehicle_type' => $vehicle_type,
                    'year' => $year,
                    'color' => $color,
                    'license_no' => $license_no,
                    'car_type' => $car_type,
                    'car_image' => $filename == "" ? NULL : $filename,
                    'car_image_original' => $filenameOriginal == "" ? NULL : $filenameOriginal,
                    'smoke' => $request->smoke,
                    'animal_friendly' => $request->animal_friendly,
                    'features' => $request->features,
                    'booking_method' => $request->booking_method,
                    'booking_type' => $request->booking_type,
                    'max_back_seats' => $max_back_seats,
                    'luggage' => $request->luggage,
                    'accept_more_luggage' => $accept_more_luggage,
                    'open_customized' => $open_customized,
                    'price' => "",
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                    'added_by' => $user_id,
                    'until_date' => null,
                    'until_limit' => '',

                    'pickup' => $request->pickup,
                    'dropoff' => $request->dropoff,

                    'middle_seats' => $request->middle_seats,
                    'back_seats' => $request->back_seats,
                ]);


                for ($j=1; $j <= $initialRide->seats; $j++) {
                    $seatDetail = new SeatDetail;
                    $seatDetail->ride_id = $initialRide->id;
                    $seatDetail->seat_number = $j;
                    $seatDetail->status = 'pending';
                    $seatDetail->save();
                }

                $getRideDetails = RideDetail::where('ride_id', $recurring_id)->get();
                foreach ($getRideDetails as $key => $getRideDetail) {
                    $nextDate = Carbon::parse($initialRide->date)->add(new \DateInterval($dateInterval));
                    $nextCompletedDate = Carbon::parse($initialRide->completed_date)->add(new \DateInterval($dateInterval));
                    $nextDestinationReachedDate = Carbon::parse($initialRide->destination_reached_date)->add(new \DateInterval($dateInterval));

                    $rideDetail = new RideDetail();
                    $rideDetail->ride_id = $initialRide->id;
                    $rideDetail->departure = $getRideDetail->departure;
                    $rideDetail->destination = $getRideDetail->destination;
                    $rideDetail->default_ride = $getRideDetail->default_ride;
                    $rideDetail->total_distance = $getRideDetail->total_distance;
                    $rideDetail->total_duration = $getRideDetail->total_duration;
                    $rideDetail->price = $getRideDetail->price;
                    $rideDetail->time = $getRideDetail->time;
                    $rideDetail->date = $nextDate;
                    $rideDetail->destination_time = $getRideDetail->destination_time;
                    $rideDetail->destination_date = $nextDestinationReachedDate;
                    $rideDetail->completed_time = $getRideDetail->completed_time;
                    $rideDetail->completed_date = $nextCompletedDate;
                    $rideDetail->save();
                }
            }
        }

        $initialRide = Ride::where('id', $recurring_id)->first();

        if (isset($user->email_notification) && $user->email_notification == 1) {
            $features = explode('=', $initialRide->features);

            $data = [
                'username' => $user->first_name,
                'from' => $request->from,
                'to' => $request->to,
                'on' => $request->date,
                'at' => $request->time,
                'seats' => $request->seats,
                'price' => $request->price,
                'redirect' => env('APP_URL') . '/' . $selectedLanguage->abbreviation . '/my-rides',

            ];
            if (in_array('1', $features) && in_array('2', $features)) {
                // Both Pink and Extra-Care
                Mail::to($user->email)->queue(new PinkExtraCareRideMail($data));
            } elseif (in_array('1', $features)) {
                // Only Pink Ride
                Mail::to($user->email)->queue(new PinkRideMail($data));
            } elseif (in_array('2', $features)) {
                // Only Extra-Care Ride
                Mail::to($user->email)->queue(new ExtraCareRideMail($data));
            } else {
                // Regular ride (existing email)
                Mail::to($user->email)->queue(new RidePostedMail($data));
            }
        }

        $features = explode('=', $initialRide->features);

        if (in_array('1', $features) && in_array('2', $features)) {
            // Both Pink and Extra-Care
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user_id,
                'message' =>  'Your Pink and Extra-Care ride is now live on ProximaRide',
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);
    
            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;
            $fcmService = new FCMService();
    
            if ($fcmToken) {
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }
        } elseif (in_array('1', $features)) {
            // Only Pink Ride
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user_id,
                'message' =>  'Your Pink Ride is now live on ProximaRide',
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);
    
            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;
            $fcmService = new FCMService();
    
            if ($fcmToken) {
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }
        } elseif (in_array('2', $features)) {
            // Only Extra-Care Ride
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user_id,
                'message' =>  'Your Extra-Care Ride is now live on ProximaRide',
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);
    
            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;
            $fcmService = new FCMService();
    
            if ($fcmToken) {
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }
        } else {
            // Regular ride (existing email)
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user_id,
                'message' =>  'Your ride is now live on ProximaRide',
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);
    
            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;
            $fcmService = new FCMService();
    
            if ($fcmToken) {
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }
        }

        $data = ['ride' => $initialRide];
        return $this->successResponse($data, strip_tags($message->ride_post_message));
    }

    public function EditRide(Request $request)
    {
        $ride = Ride::where('id', $request->ride_id)->with(['vehicle', 'defaultRideDetail', 'MoreRideDetail'])
            ->with(['driver' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])
            ->with(['bookings' => function ($query) {
                // Select specific columns from bookings
                $query->select('id', 'ride_id', 'seats', 'user_id', 'booking_credit', 'fare', 'secured_cash_attempt_count', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
                    ->where('status', '<>', 3)
                    ->where('status', '<>', 4)
                    ->whereHas('passenger', function ($query) {
                        $query->whereNull('deleted_at');
                    })
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
                    }]);
            }])->first();

        $selectedLanguage = session('selectedLanguage');
        $findRidePage = null;
        $postRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
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
                ->whereHas('passenger', function ($query) {
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
                optional($findRidePage->ride_features_option1)->features_setting_id ?? $default_features_option1->features_setting_id => ['id' => $default_features_option1->features_setting_id, 'title' => optional($findRidePage->ride_features_option1)->name ?? $default_features_option1->name, 'image' => $findRidePage->ride_features_option1 ? asset('home_page_icons/' . $findRidePage->ride_features_option1->icon) : asset('home_page_icons/' . $default_features_option1->icon), 'tooltip' => $postRidePage->features_option1_tooltip ?? $defaultPostRidePage->features_option1_tooltip],
                optional($findRidePage->ride_features_option2)->features_setting_id ?? $default_features_option2->features_setting_id => ['id' => $default_features_option2->features_setting_id, 'title' => optional($findRidePage->ride_features_option2)->name ?? $default_features_option2->name, 'image' => $findRidePage->ride_features_option2 ? asset('home_page_icons/' . $findRidePage->ride_features_option2->icon) : asset('home_page_icons/' . $default_features_option2->icon), 'tooltip' => $postRidePage->features_option2_tooltip ?? $defaultPostRidePage->features_option2_tooltip],
                optional($findRidePage->ride_features_option3)->features_setting_id ?? $default_features_option3->features_setting_id => ['id' => $default_features_option3->features_setting_id, 'title' => optional($findRidePage->ride_features_option3)->name ?? $default_features_option3->name, 'image' => $findRidePage->ride_features_option3 ? asset('home_page_icons/' . $findRidePage->ride_features_option3->icon) : asset('home_page_icons/' . $default_features_option3->icon), 'tooltip' => $postRidePage->features_option3_tooltip ?? $defaultPostRidePage->features_option3_tooltip],
                optional($findRidePage->ride_features_option8)->features_setting_id ?? $default_features_option8->features_setting_id => ['id' => $default_features_option8->features_setting_id, 'title' => optional($findRidePage->ride_features_option8)->name ?? $default_features_option8->name, 'image' => $findRidePage->ride_features_option8 ? asset('home_page_icons/' . $findRidePage->ride_features_option8->icon) : asset('home_page_icons/' . $default_features_option8->icon), 'tooltip' => $postRidePage->features_option8_tooltip ?? $defaultPostRidePage->features_option8_tooltip],
                optional($findRidePage->ride_features_option9)->features_setting_id ?? $default_features_option9->features_setting_id => ['id' => $default_features_option9->features_setting_id, 'title' => optional($findRidePage->ride_features_option9)->name ?? $default_features_option9->name, 'image' => $findRidePage->ride_features_option9 ? asset('home_page_icons/' . $findRidePage->ride_features_option9->icon) : asset('home_page_icons/' . $default_features_option9->icon), 'tooltip' => $postRidePage->features_option9_tooltip ?? $defaultPostRidePage->features_option9_tooltip],
                optional($findRidePage->ride_features_option10)->features_setting_id ?? $default_features_option10->features_setting_id => ['id' => $default_features_option10->features_setting_id, 'title' => optional($findRidePage->ride_features_option10)->name ?? $default_features_option10->name, 'image' => $findRidePage->ride_features_option10 ? asset('home_page_icons/' . $findRidePage->ride_features_option10->icon) : asset('home_page_icons/' . $default_features_option10->icon), 'tooltip' => $postRidePage->features_option10_tooltip ?? $defaultPostRidePage->features_option10_tooltip],
                optional($findRidePage->ride_features_option11)->features_setting_id ?? $default_features_option11->features_setting_id => ['id' => $default_features_option11->features_setting_id, 'title' => optional($findRidePage->ride_features_option11)->name ?? $default_features_option11->name, 'image' => $findRidePage->ride_features_option11 ? asset('home_page_icons/' . $findRidePage->ride_features_option11->icon) : asset('home_page_icons/' . $default_features_option11->icon), 'tooltip' => $postRidePage->features_option11_tooltip ?? $defaultPostRidePage->features_option11_tooltip],
                optional($findRidePage->ride_features_option12)->features_setting_id ?? $default_features_option12->features_setting_id => ['id' => $default_features_option12->features_setting_id, 'title' => optional($findRidePage->ride_features_option12)->name ?? $default_features_option12->name, 'image' => $findRidePage->ride_features_option12 ? asset('home_page_icons/' . $findRidePage->ride_features_option12->icon) : asset('home_page_icons/' . $default_features_option12->icon), 'tooltip' => $postRidePage->features_option12_tooltip ?? $defaultPostRidePage->features_option12_tooltip],
                optional($findRidePage->ride_features_option13)->features_setting_id ?? $default_features_option13->features_setting_id => ['id' => $default_features_option13->features_setting_id, 'title' => optional($findRidePage->ride_features_option13)->name ?? $default_features_option13->name, 'image' => $findRidePage->ride_features_option13 ? asset('home_page_icons/' . $findRidePage->ride_features_option13->icon) : asset('home_page_icons/' . $default_features_option13->icon), 'tooltip' => $postRidePage->features_option13_tooltip ?? $defaultPostRidePage->features_option13_tooltip],
                optional($findRidePage->ride_features_option14)->features_setting_id ?? $default_features_option14->features_setting_id => ['id' => $default_features_option14->features_setting_id, 'title' => optional($findRidePage->ride_features_option14)->name ?? $default_features_option14->name, 'image' => $findRidePage->ride_features_option14 ? asset('home_page_icons/' . $findRidePage->ride_features_option14->icon) : asset('home_page_icons/' . $default_features_option14->icon), 'tooltip' => $postRidePage->features_option14_tooltip ?? $defaultPostRidePage->features_option14_tooltip],
                optional($findRidePage->ride_features_option15)->features_setting_id ?? $default_features_option15->features_setting_id => ['id' => $default_features_option15->features_setting_id, 'title' => optional($findRidePage->ride_features_option15)->name ?? $default_features_option15->name, 'image' => $findRidePage->ride_features_option15 ? asset('home_page_icons/' . $findRidePage->ride_features_option15->icon) : asset('home_page_icons/' . $default_features_option15->icon), 'tooltip' => $postRidePage->features_option15_tooltip ?? $defaultPostRidePage->features_option15_tooltip],
                optional($postRidePage->features_option4)->features_setting_id ?? $default_features_option4->features_setting_id => ['id' => $default_features_option4->features_setting_id, 'title' => optional($postRidePage->features_option4)->name ?? $default_features_option4->name, 'image' => $postRidePage->ride_features_option4 ? asset('home_page_icons/' . $postRidePage->features_option4->icon) : asset('home_page_icons/' . $default_features_option4->icon), 'tooltip' => $postRidePage->features_option4_tooltip ?? $defaultPostRidePage->features_option4_tooltip],
                optional($postRidePage->features_option5)->features_setting_id ?? $default_features_option5->features_setting_id => ['id' => $default_features_option5->features_setting_id, 'title' => optional($postRidePage->features_option5)->name ?? $default_features_option5->name, 'image' => $postRidePage->ride_features_option5 ? asset('home_page_icons/' . $postRidePage->features_option5->icon) : asset('home_page_icons/' . $default_features_option5->icon), 'tooltip' => $postRidePage->features_option5_tooltip ?? $defaultPostRidePage->features_option5_tooltip],
                optional($postRidePage->features_option6)->features_setting_id ?? $default_features_option6->features_setting_id => ['id' => $default_features_option6->features_setting_id, 'title' => optional($postRidePage->features_option6)->name ?? $default_features_option6->name, 'image' => $postRidePage->ride_features_option6 ? asset('home_page_icons/' . $postRidePage->features_option6->icon) : asset('home_page_icons/' . $default_features_option6->icon), 'tooltip' => $postRidePage->features_option6_tooltip ?? $defaultPostRidePage->features_option6_tooltip],
                optional($postRidePage->features_option7)->features_setting_id ?? $default_features_option7->features_setting_id => ['id' => $default_features_option7->features_setting_id, 'title' => optional($postRidePage->features_option7)->name ?? $default_features_option7->name, 'image' => $postRidePage->ride_features_option7 ? asset('home_page_icons/' . $postRidePage->features_option7->icon) : asset('home_page_icons/' . $default_features_option7->icon), 'tooltip' => $postRidePage->features_option7_tooltip ?? $defaultPostRidePage->features_option7_tooltip],
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

            if ($ride->driver->gender) {
                $ride->driver->gender = ucfirst($ride->driver->gender);
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

                if ($booking->passenger->gender) {
                    $booking->passenger->gender = ucfirst($booking->passenger->gender);
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
        return $this->successResponse($data, 'Edit ride page get successfully');
    }

    public function UpdateRide(Request $request)
    {
        $ride = Ride::where('id', $request->ride_id)->first();
        $user = Auth::guard('sanctum')->user();
        $adminSetting = SiteSetting::first();
        $user_id = $user->id;
        $rides = Ride::where('added_by', $user_id)->whereNotIn('id', [$request->ride_id])->get();
        
        // Check if ride has any bookings - if so, price cannot be changed
        $hasBookings = Booking::where('ride_id', $request->ride_id)
            ->where('status', '<>', 3)
            ->where('status', '<>', 4)
            ->whereHas('passenger', function($query) {
                $query->whereNull('deleted_at');
            })
            ->exists();
        
        // If bookings exist, check if price is being changed
        if ($hasBookings && $ride->defaultRideDetail && isset($ride->defaultRideDetail[0])) {
            $currentPrice = $ride->defaultRideDetail[0]->price;
            $newPrice = $request->price;
            
            if ($currentPrice != $newPrice) {
                return $this->apiErrorResponse('You cannot change the price once passengers have booked this ride.', 200);
            }
        }

        $message = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_schedule_message', 'acc_suspend_message','overlap_ride_message','post_ride_update_message','block_post_ride_message', 'profile_photo_required_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_schedule_message', 'acc_suspend_message','overlap_ride_message','block_post_ride_message', 'profile_photo_required_message')->first();
            }
        }

        // Check if user has suspanded
        if ($user->suspand === '1') {
            return $this->apiErrorResponse(strip_tags($message->acc_suspend_message ?? null), 200);
        }

        if ($user->block_post_ride == '1') {
            return $this->apiErrorResponse(strip_tags($message->block_post_ride_message ?? null), 200);
        }

        if (!isset($user->profile_image) && $user->profile_image == '') {
            return $this->apiErrorResponse(strip_tags($message->profile_photo_required_message ?? null), 200);
        }

        // Check if any existing ride has the same date and time
        if(isset($request->date) && isset($request->time)){
            $tripDate = date('Y-m-d', strtotime($request->date));
            $tripTime = date('H:i:s', strtotime($request->time));
            $rides = Ride::where('added_by', $user_id)->where('id','!=',$request->ride_id)
            ->whereDate('date', '=', $tripDate)
            ->whereTime('time', '=', $tripTime)
            ->first();
            if(isset($rides) && !empty($rides)){
                $data = ['ride' => $request->all(), 'uploaded_image' => $filename ?? null];
                return $this->apiErrorResponse(strip_tags($message->ride_schedule_message), 200, $data);
            }

            //Second - Get distance and duration from Google Maps API
            $duration = 0;
            $distance= 0;
            // Use original from/to values - getDataFromGoogleApi will properly encode them
            $from = $request->from;
            $to = $request->to;
            $fromArray = explode(',', $request->from);
            $toArray = explode(',', $request->to);
            
            Log::info('Calculating distance for ride update (API)', [
                'ride_id' => $request->ride_id,
                'from' => $from,
                'to' => $to,
                'user_id' => $user_id
            ]);
            
            $googleApiData = $this->getDataFromGoogleApi($from, $to);
            if(isset($googleApiData) && !empty($googleApiData)){
                $duration = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;

                $distance = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
            }

            if($distance != 0){
                $distance = round(($distance / 1000), 2);
            }
            
            Log::info('Distance calculation completed for ride update (API)', [
                'ride_id' => $request->ride_id,
                'from' => $from,
                'to' => $to,
                'distance_km' => $distance,
                'duration_seconds' => $duration,
                'distance_meters' => $distance * 1000
            ]);

            if(isset($adminSetting)){

                if(isset($initialRide->date) && isset($initialRide->time)){
                    $rideDateTime = Carbon::parse("$initialRide->date $initialRide->time");
    
                    $apiTime = 0;
                    if($duration != 0){
                        $apiTime = round(($duration / 3600), 2);
                    }
    
                    // $rideDateTime->addHours($adminSetting->destination_hours);
                    // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                    $totalHours = $duration / 3600;  
                    $fullHours = floor($totalHours);  
                    $minutes = round(($totalHours - $fullHours) * 60); 
                    $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                        ->addMinutes($minutes);
    
                    $destinationReachedDate = $rideDateTime->toDateString();
                    $destinationReachedTime = $rideDateTime->toTimeString();
                }
    
            }
            // Ensure destination reached values exist even if Google/API block didn't set them
            $fallbackDate = null;
            if (isset($request->date) && !empty($request->date)) {
                try { $fallbackDate = Carbon::createFromFormat('F d, Y', $request->date)->toDateString(); }
                catch (\Exception $e) { $fallbackDate = Carbon::parse($request->date)->toDateString(); }
            } else { $fallbackDate = Carbon::now()->toDateString(); }

            $fallbackTime = null;
            if (isset($request->time) && !empty($request->time)) {
                try { $fallbackTime = Carbon::createFromFormat('H:i', $request->time)->format('H:i:s'); }
                catch (\Exception $e) { $fallbackTime = Carbon::parse($request->time)->format('H:i:s'); }
            } else { $fallbackTime = Carbon::now()->format('H:i:s'); }

            $safeDestinationDate = isset($destinationReachedDate) && !empty($destinationReachedDate) ? $destinationReachedDate : $fallbackDate;
            $safeDestinationTime = isset($destinationReachedTime) && !empty($destinationReachedTime) ? $destinationReachedTime : $fallbackTime;

            $newStart = Carbon::parse("$fallbackDate $fallbackTime");
            $newEnd = Carbon::parse("$safeDestinationDate $safeDestinationTime");



            $rides = DB::table('rides')
                ->where('status', '!=', 2)
                ->where('added_by', $user_id)
                ->whereRaw("CONCAT(date, ' ', time) < ?", [$newEnd])
                ->whereRaw("CONCAT(destination_reached_date, ' ', destination_reached_time) > ?", [$newStart])
                ->first();
                
            if(isset($rides) && !empty($rides)){
                $data = ['ride' => $request->all(), 'uploaded_image' => $filename ?? null];
                return $this->apiErrorResponse(strip_tags($message->overlap_ride_message ?? "this ride overlaps with an existing ride you already have"), 200, $data);
            }
        }

        $skip_vehicle = $request->filled('skip_vehicle') ? $request->skip_vehicle : 0;
        $add_vehicle = $request->filled('add_vehicle') ? $request->add_vehicle : '0';
        $added_vehicle = $request->filled('added_vehicle') ? $request->added_vehicle : '0';

        $recurring = $request->filled('recurring') ? '1' : '0';

        $customMessages = [
            'image.max' => 'Can not upload image size greater than 10MB',
        ];
        $request->validate([
            'from' => 'required',
            'to' => 'required',
            'pickup' => 'required',
            'dropoff' => 'required',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'details' => 'required|string|max_words:300',
            'seats' => 'required|numeric|min:1',
            'smoke' => 'required',
            'animal_friendly' => 'required',
            'features' => 'required|string',
            'booking_method' => 'required',
            'booking_type' => 'required',
            'luggage' => 'required',
            'price' => 'required|numeric|gt:0',
            'payment_method' => 'required',
            'notes' => 'nullable|string|max:300',
            'middle_seats' => 'required|numeric|min:1',
            'back_seats' => 'required|numeric|min:1',
            'agree_terms' => 'required',
            'image' => $add_vehicle !== '0' ? 'nullable|mimes:jpeg,png,jpg,gif|max:10240' : 'nullable|mimes:jpeg,png,jpg,gif|max:10240',
            'make' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'model' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'vehicle_type' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'year' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'color' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'license_no' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'car_type' => $add_vehicle !== '0' ? 'required' : 'nullable',
            'recurring_type' => $recurring !== '0' ? 'required' : 'nullable',
            'recurring_trips' => $recurring !== '0' ? 'required' : 'nullable',
            'vehicle_id' => $added_vehicle == '1' ? 'required' : 'nullable',
        ], $customMessages);

        $make = '';
        $model = '';
        $vehicle_type = '';
        $year = '';
        $color = '';
        $license_no = '';
        $car_type = '';
        $filename = '';
        $filenameOriginal = "";

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $file->getClientOriginalName());
            $destination_path = public_path('car_images');
            $file->move($destination_path, $filename);


            $fileOriginal = $request->file('car_image_original');
            $filenameOriginal = $fileOriginal->getClientOriginalName();
            $destination_path = public_path('car_images');
            $fileOriginal->move($destination_path, $filenameOriginal);
        }



        $max_back_seats = $request->filled('max_back_seats') ? $request->max_back_seats : 0;
        $accept_more_luggage = $request->filled('accept_more_luggage') ? $request->accept_more_luggage : 0;
        $open_customized = $request->filled('open_customized') ? $request->open_customized : 0;

        if ($skip_vehicle == '1') {
            $make = '';
            $model = '';
            $vehicle_type = '';
            $year = '';
            $color = '';
            $license_no = '';
            $car_type = '';
        }

        if ($add_vehicle == '1') {
            $make = $request->make;
            $model = $request->model;
            $vehicle_type = $request->vehicle_type;
            $year = $request->year;
            $color = $request->color;
            $license_no = $request->license_no;
            $car_type = $request->car_type;
            $vehicle = Vehicle::create([
                'user_id' => $user_id,
                'make' => $request->make,
                'model' => $request->model,
                'type' => $request->vehicle_type,
                'liscense_no' => $request->license_no,
                'color' => $request->color,
                'year' => $request->year,
                'car_type' => $request->car_type,
                'image' => $filename,
            ]);
            $vehicle_id = $vehicle->id;
        }

        if ($added_vehicle == '1') {
            if ($request->vehicle_id !== '') {
                $vehicle = Vehicle::whereId($request->vehicle_id)->first();
                if ($vehicle) {
                    $make = $vehicle->make;
                    $model = $vehicle->model;
                    $vehicle_type = $vehicle->type;
                    $year = $vehicle->year;
                    $color = $vehicle->color;
                    $license_no = $vehicle->liscense_no;
                    $filename = basename($vehicle->image);
                    $car_type = $vehicle->car_type;
                    $vehicle_id = $vehicle->id;
                } else {
                    $make = '';
                    $model = '';
                    $vehicle_type = '';
                    $year = '';
                    $color = '';
                    $license_no = '';
                    $car_type = '';
                    $filename = '';
                }
            } else {
                $make = '';
                $model = '';
                $vehicle_type = '';
                $year = '';
                $color = '';
                $license_no = '';
                $car_type = '';
                $filename = '';
            }
        }

        if ($recurring == '1') {
            $recurring_type = $request->recurring_type;
            $recurring_trips = $request->recurring_trips;
        } else {
            $recurring_type = '';
            $recurring_trips = '';
        }

        if(isset($request->booking_type) && $request->booking_type != ""){

        }else{
            $getStandardId = FeaturesSetting::where('slug', 'standard')->value('id');
            if(isset($getStandardId) && !is_null($getStandardId)){
                $request->booking_type = $getStandardId;
            }
        }

        $ride->update([
            'departure' => "",
            'departure_lat' => '',
            'departure_lng' => '',
            'departure_place' => '',
            'departure_route' => '',
            'departure_zipcode' => '',
            'departure_city' => '',
            'departure_state' => '',
            'departure_state_short' => '',
            'departure_country' => '',

            'destination' => "",
            'destination_lat' => '',
            'destination_lng' => '',
            'destination_place' => '',
            'destination_route' => '',
            'destination_zipcode' => '',
            'destination_city' => '',
            'destination_state' => '',
            'destination_state_short' => '',
            'destination_country' => '',

            'total_distance' => "",
            'total_time' => "",
            'date' => Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d'),
            'time' => $request->time,

            'recurring' => $recurring,
            'recurring_type' => $recurring_type,
            'recurring_trips' => $recurring_trips,
            'details' => $request->details,
            'seats' => $request->seats,

            'skip_vehicle' => $skip_vehicle,
            'add_vehicle' => $add_vehicle,
            'added_vehicle' => $added_vehicle,
            'vehicle_id' => $vehicle_id ?? null,
            'make' => $make,
            'model' => $model,
            'vehicle_type' => $vehicle_type,
            'year' => $year,
            'color' => $color,
            'license_no' => $license_no,
            'car_type' => $car_type,
            'car_image' => $filename == "" ? NULL : $filename,
            'car_image_original' => $filenameOriginal == "" ? NULL : $filenameOriginal,
            'smoke' => $request->smoke,
            'animal_friendly' => $request->animal_friendly,
            'features' => $request->features,
            'booking_method' => $request->booking_method,
            'booking_type' => $request->booking_type,
            'max_back_seats' => $max_back_seats,
            'luggage' => $request->luggage,
            'accept_more_luggage' => $accept_more_luggage,
            'open_customized' => $open_customized,
            'price' => "",
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'added_by' => $user_id,
            'until_date' => null,
            'until_limit' => '',

            'pickup' => $request->pickup,
            'dropoff' => $request->dropoff,

            'middle_seats' => $request->middle_seats,
            'back_seats' => $request->back_seats,
        ]);


        $getSeatDetails = SeatDetail::where('ride_id', $ride->id)->get();
        foreach ($getSeatDetails as $key => $getSeatDetail) {
            $getSeatDetail->delete();
        }


        for ($i=1; $i <= $ride->seats; $i++) {
            $seatDetail = new SeatDetail;
            $seatDetail->ride_id = $ride->id;
            $seatDetail->seat_number = $i;
            $seatDetail->status = 'pending';
            $seatDetail->save();
        }

        $from = $to = "";
        $from = $request->from;
        $to = $request->to;

        if(isset($request->default_ride_detail_id)){
            $rideDetail = RideDetail::where('id', $request->default_ride_detail_id)->first();
         }else{
           $rideDetail = new RideDetail();
        }

       $rideDetail->ride_id = $ride->id;
       $rideDetail->departure = $from;
       $rideDetail->destination = $to;
       $rideDetail->default_ride = 1;
       $rideDetail->total_distance = $distance;
       $rideDetail->total_duration = $duration;
       $rideDetail->price = $request->price;
       $rideDetail->time = $request->time;
       $rideDetail->date = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');
       
       Log::info('Saving ride detail with distance (API)', [
           'ride_id' => $ride->id,
           'ride_detail_id' => $rideDetail->id ?? 'new',
           'departure' => $from,
           'destination' => $to,
           'total_distance_km' => $distance,
           'total_duration_seconds' => $duration
       ]);

       if(isset($adminSetting)){

            if(isset($ride->date) && isset($ride->time)){
                $rideDateTime = Carbon::parse("$ride->date $ride->time");
                $apiTime = 0;
                if($duration != 0){
                    $apiTime = round(($duration / 3600), 2);
                }

                // $rideDateTime->addHours($adminSetting->destination_hours);
                // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                $totalHours = $duration / 3600;  
                $fullHours = floor($totalHours);  
                $minutes = round(($totalHours - $fullHours) * 60); 
                $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                    ->addMinutes($minutes);

                $destinationReachedDate = $rideDateTime->toDateString();
                $destinationReachedTime = $rideDateTime->toTimeString();


                $rideDateTime->addHours($adminSetting->ride_completed_hours);
                $completedDate = $rideDateTime->toDateString();
                $completedTime = $rideDateTime->toTimeString();

                $ride->completed_date = $completedDate;
                $ride->completed_time = $completedTime;
                $ride->destination_reached_date = $destinationReachedDate;
                $ride->destination_reached_time = $destinationReachedTime;
                $ride->save();

               $rideDetail->destination_time = $destinationReachedTime;
               $rideDetail->destination_date = $destinationReachedDate;
               $rideDetail->completed_time = $completedTime;
               $rideDetail->completed_date = $completedDate;
            }

        }
       $rideDetail->save();

       $fromSpots = [];
       if(isset($request->from_spot)){
            $fromSpots = json_decode($request->from_spot, true);
        }

       if(isset($fromSpots) && !empty($fromSpots)){
           foreach ($fromSpots as $key => $from_spot) {
               $duration = 0;
               $distance= 0;


                $toSpots = json_decode($request->to_spot, true);

                $priceSpots = json_decode($request->price_spot, true);

                $fromArray = explode(',', $fromSpots[$key]);
                $toArray = explode(',', $toSpots[$key]);

               $googleApiData = $this->getDataFromGoogleApi($fromSpots[$key], $toSpots[$key]);
               if(isset($googleApiData) && !empty($googleApiData)){
                   $duration = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;

                   $distance = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
               }

               if($distance != 0){
                   $distance = round(($distance / 1000), 2);
               }

               $fromRide = $toRide = "";
               $fromRide = $fromSpots[$key];
               $toRide = $toSpots[$key];


                $ride_detail_ids = [];

               if(isset($request->ride_detail_ids)){
                    $ride_detail_ids = json_decode($request->ride_detail_ids, true);
               }

               if(isset($ride_detail_ids) && isset($ride_detail_ids[$key]) && $ride_detail_ids[$key] != "0"){
                   $rideDetail = RideDetail::where('id', $ride_detail_ids[$key])->first();
              }else{
                  $rideDetail = new RideDetail();
              }
               $rideDetail->ride_id = $ride->id;
               $rideDetail->departure = $fromRide;
               $rideDetail->destination = $toRide;
               $rideDetail->default_ride = 0;
               $rideDetail->total_distance = $distance;
               $rideDetail->total_duration = $duration;
               $rideDetail->price = $priceSpots[$key];
               $rideDetail->time = $request->time;
               $rideDetail->date = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');

               if(isset($adminSetting)){

                   if(isset($ride->date) && isset($ride->time)){
                       $rideDateTime = Carbon::parse("$ride->date $ride->time");
                       $apiTime = 0;
                        if($duration != 0){
                            $apiTime = round(($duration / 3600), 2);
                        }

                        $rideDateTime->addHours($adminSetting->destination_hours);
                        $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);

                       $destinationReachedDate = $rideDateTime->toDateString();
                       $destinationReachedTime = $rideDateTime->toTimeString();


                       $rideDateTime->addHours($adminSetting->ride_completed_hours);
                       $completedDate = $rideDateTime->toDateString();
                       $completedTime = $rideDateTime->toTimeString();

                       $rideDetail->destination_time = $destinationReachedTime;
                       $rideDetail->destination_date = $destinationReachedDate;
                       $rideDetail->completed_time = $completedTime;
                       $rideDetail->completed_date = $completedDate;
                   }

               }
               $rideDetail->save();
           }
       }


        // Check if the ride is recurring
        if ($recurring == '1') {
            // Determine the frequency and number of recurring trips
            $frequency = $request->input('recurring_type');
            $numRecurringTrips = $request->input('recurring_trips');

            // Calculate the date interval based on the frequency
            $dateInterval = ($frequency === 'Daily') ? 'P1D' : 'P7D';

            $existingRecurringRides = Ride::where('recurring_id', $request->ride_id)->get();
            $initialRide = Ride::where('id', $request->ride_id)->first();

            // Create additional rides based on the recurring settings
            for ($i = 1; $i <= $numRecurringTrips; $i++) {
                $nextDate = Carbon::parse($initialRide->date)->add(new \DateInterval($dateInterval));
                $nextCompletedDate = Carbon::parse($initialRide->completed_date)->add(new \DateInterval($dateInterval));
                $nextDestinationReachedDate = Carbon::parse($initialRide->destination_reached_date)->add(new \DateInterval($dateInterval));

                if (isset($existingRecurringRides[$i - 1])) {
                    // Update existing recurring ride
                    $initialRide = $existingRecurringRides[$i - 1]->update([
                        'departure' => "",
                        'destination' => "",
                        'date' => $nextDate->format('Y-m-d'),
                        'time' => $ride->time,
                        'completed_date' => $nextCompletedDate->format('Y-m-d'),
                        'completed_time' => $ride->completed_time,
                        'destination_reached_date' => $nextDestinationReachedDate->format('Y-m-d'),
                        'destination_reached_time' => $ride->destination_reached_time,
                        'recurring' => $recurring,
                        'details' => $request->details,
                        'seats' => $request->seats,
                        'total_distance' => $distance,
                        'total_time' => $duration,
                        'skip_vehicle' => $skip_vehicle,
                        'add_vehicle' => $add_vehicle,
                        'added_vehicle' => $added_vehicle,
                        'vehicle_id' => $vehicle_id ?? null,
                        'make' => $make,
                        'model' => $model,
                        'vehicle_type' => $vehicle_type,
                        'year' => $year,
                        'color' => $color,
                        'license_no' => $license_no,
                        'car_type' => $car_type,

                        'car_image' => $filename == "" ? NULL : $filename,
                        'car_image_original' => $filenameOriginal == "" ? NULL : $filenameOriginal,

                        'smoke' => $request->smoke,
                        'animal_friendly' => $request->animal_friendly,
                        'features' => $request->features,
                        'booking_method' => $request->booking_method,
                        'booking_type' => $request->booking_type,
                        'max_back_seats' => $max_back_seats,
                        'luggage' => $request->luggage,
                        'accept_more_luggage' => $accept_more_luggage,
                        'open_customized' => $open_customized,
                        'price' => "",
                        'payment_method' => $request->payment_method,
                        'notes' => $request->notes,
                        'pickup' => $request->pickup,
                        'dropoff' => $request->dropoff,
                        'middle_seats' => $request->middle_seats,
                        'back_seats' => $request->back_seats,
                    ]);


                    $getSeatDetails = SeatDetail::where('ride_id', $initialRide->id)->get();
                    foreach ($getSeatDetails as $key => $getSeatDetail) {
                        $getSeatDetail->delete();
                    }


                    for ($i=1; $i <= $initialRide->seats; $i++) {
                        $seatDetail = new SeatDetail;
                        $seatDetail->ride_id = $initialRide->id;
                        $seatDetail->seat_number = $i;
                        $seatDetail->status = 'pending';
                        $seatDetail->save();
                    }

                    $getRideDetails = RideDetail::where('ride_id', $initialRide->id)->get();
                    foreach ($getRideDetails as $key => $getRideDetail) {
                        $getRideDetail->delete();
                    }
                } else {
                    // Create new recurring ride
                    $initialRide = Ride::create([
                        'departure' => "",
                        'departure_lat' => '',
                        'departure_lng' => '',
                        'departure_place' => '',
                        'departure_route' => '',
                        'departure_zipcode' => '',
                        'departure_city' => '',
                        'departure_state' => '',
                        'departure_state_short' => '',
                        'departure_country' => '',

                        'destination' => "",
                        'destination_lat' => '',
                        'destination_lng' => '',
                        'destination_place' => '',
                        'destination_route' => '',
                        'destination_zipcode' => '',
                        'destination_city' => '',
                        'destination_state' => '',
                        'destination_state_short' => '',
                        'destination_country' => '',

                        'total_distance' => '',
                        'total_time' => '',
                        'date' => $nextDate->format('Y-m-d'),
                        'time' => $request->time,

                        'recurring' => '1',
                        'recurring_type' => '',
                        'recurring_trips' => '',
                        'recurring_id' => $request->ride_id,
                        'details' => $request->details,
                        'seats' => $request->seats,

                        'skip_vehicle' => $skip_vehicle,
                        'add_vehicle' => $add_vehicle,
                        'added_vehicle' => $added_vehicle,
                        'make' => $make,
                        'model' => $model,
                        'vehicle_type' => $vehicle_type,
                        'year' => $year,
                        'color' => $color,
                        'license_no' => $license_no,
                        'car_type' => $car_type,

                        'car_image' => $filename == "" ? NULL : $filename,
                        'car_image_original' => $filenameOriginal == "" ? NULL : $filenameOriginal,

                        'smoke' => $request->smoke,
                        'animal_friendly' => $request->animal_friendly,
                        'features' => $request->features,
                        'booking_method' => $request->booking_method,
                        'booking_type' => $request->booking_type,
                        'max_back_seats' => $max_back_seats,
                        'luggage' => $request->luggage,
                        'accept_more_luggage' => $accept_more_luggage,
                        'open_customized' => $open_customized,
                        'price' => "",
                        'payment_method' => $request->payment_method,
                        'notes' => $request->notes,
                        'added_by' => $user_id,
                        'until_date' => null,
                        'until_limit' => '',

                        'pickup' => $request->pickup,
                        'dropoff' => $request->dropoff,

                        'middle_seats' => $request->middle_seats,
                        'back_seats' => $request->back_seats,
                    ]);


                    for ($i=1; $i <= $initialRide->seats; $i++) {
                        $seatDetail = new SeatDetail;
                        $seatDetail->ride_id = $initialRide->id;
                        $seatDetail->seat_number = $i;
                        $seatDetail->status = 'pending';
                        $seatDetail->save();
                    }
                }

                $getRideDetails = RideDetail::where('ride_id', $initialRide->id)->get();
                foreach ($getRideDetails as $key => $getRideDetail) {
                    $nextDate = Carbon::parse($initialRide->date)->add(new \DateInterval($dateInterval));
                    $nextCompletedDate = Carbon::parse($initialRide->completed_date)->add(new \DateInterval($dateInterval));
                    $nextDestinationReachedDate = Carbon::parse($initialRide->destination_reached_date)->add(new \DateInterval($dateInterval));

                    $rideDetail = new RideDetail();
                    $rideDetail->ride_id = $initialRide->id;
                    $rideDetail->departure = $getRideDetail->departure;
                    $rideDetail->destination = $getRideDetail->destination;
                    $rideDetail->default_ride = $getRideDetail->default_ride;
                    $rideDetail->total_distance = $getRideDetail->total_distance;
                    $rideDetail->total_duration = $getRideDetail->total_duration;
                    $rideDetail->price = $getRideDetail->price;
                    $rideDetail->time = $getRideDetail->time;
                    $rideDetail->date = $nextDate;
                    $rideDetail->destination_time = $getRideDetail->destination_time;
                    $rideDetail->destination_date = $nextDestinationReachedDate;
                    $rideDetail->completed_time = $getRideDetail->completed_time;
                    $rideDetail->completed_date = $nextCompletedDate;
                    $rideDetail->save();
                }
            }

            // Remove any excess rides
            for ($i = $numRecurringTrips; $i < count($existingRecurringRides); $i++) {
                $existingRecurringRides[$i]->delete();

                $rideId = $existingRecurringRides[$i]->id;

                $getRideDetails = RideDetail::where('ride_id', $rideId)->get();
                foreach ($getRideDetails as $key => $getRideDetail) {
                    $getRideDetail->delete();
                }
            }
        }

        $initialRide = Ride::where('id', $request->ride_id)->first();
        $data = ['ride' => $initialRide];
        return $this->successResponse($data, strip_tags($message->post_ride_update_message ?? 'Ride updated successfully'));
    }

    public function postRideIndex(Request $request)
    {
        $postRidePage = null;
        $messages = null;
        if ($request->lang_id && $request->lang_id != 0) {

            $selectedLanguage = Language::where('id', $request->lang_id)->first();
            // Retrieve the PostRidePageSettingDetail associated with the selected language
            $postRidePage = PostRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            $postRidePage->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
            $postRidePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($request->lang_id)->value('name');
            $postRidePage->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
            $postRidePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($request->lang_id)->value('name');
            $postRidePage->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
            $postRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($request->lang_id)->value('name');
            $postRidePage->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
            $postRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($request->lang_id)->value('name');
            $postRidePage->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
            $postRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($request->lang_id)->value('name');
            $postRidePage->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
            $postRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($request->lang_id)->value('name');
            $postRidePage->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
            $postRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($request->lang_id)->value('name');
            $postRidePage->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
            $postRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($request->lang_id)->value('name');
            $postRidePage->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
            $postRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($request->lang_id)->value('name');
            $postRideError = PostRidePageError::where('post_ride_page_setting_detail_id', $postRidePage->id)->first();
            $postRidePage->from_error = $postRideError->from_error ?? null ;
            $postRidePage->to_error = $postRideError->to_error ?? null ;
            $postRidePage->pick_up_error = $postRideError->pick_up_error ?? null ;
            $postRidePage->drop_off_error = $postRideError->drop_off_error ?? null ;
            $postRidePage->date_error = $postRideError->date_error ?? null ;
            $postRidePage->time_error = $postRideError->time_error ?? null ;
            $postRidePage->recurring_type_error = $postRideError->recurring_type_error ?? null ;
            $postRidePage->recurring_trips_error = $postRideError->recurring_trips_error ?? null ;
            $postRidePage->meeting_drop_off_description_error = $postRideError->meeting_drop_off_description_error ?? null ;
            $postRidePage->seats_error = $postRideError->seats_error ?? null ;
            $postRidePage->seats_middle_error = $postRideError->seats_middle_error ?? null ;
            $postRidePage->seats_back_error = $postRideError->seats_back_error ?? null ;
            $postRidePage->vehicle_id_error = $postRideError->vehicle_id_error ?? null ;
            $postRidePage->make_error = $postRideError->make_error ?? null ;
            $postRidePage->model_error = $postRideError->model_error ?? null ;
            $postRidePage->vehicle_type_error = $postRideError->vehicle_type_error ?? null ;
            $postRidePage->color_error = $postRideError->color_error ?? null ;
            $postRidePage->license_error = $postRideError->license_error ?? null ;
            $postRidePage->year_error = $postRideError->year_error ?? null ;
            $postRidePage->fuel_error = $postRideError->fuel_error ?? null ;
            $postRidePage->photo_error = $postRideError->photo_error ?? null ;
            $postRidePage->booking_method_error = $postRideError->booking_method_error ?? null ;
            $postRidePage->anything_to_add_error = $postRideError->anything_to_add_error ?? null ;
            $postRidePage->smoking_error = $postRideError->smoking_error ?? null ;
            $postRidePage->animal_error = $postRideError->animal_error ?? null ;
            $postRidePage->luggage_error = $postRideError->luggage_error ?? null ;
            $postRidePage->price_error = $postRideError->price_error ?? null ;
            $postRidePage->payment_method_error = $postRideError->payment_method_error ?? null ;
            $postRidePage->booking_type_error = $postRideError->booking_type_error ?? null ;
            $postRidePage->agree_terms_error = $postRideError->agree_terms_error ?? null ;
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('past_time_message', 'past_date_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRidePage->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
                $postRidePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $postRidePage->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
                $postRidePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $postRidePage->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
                $postRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $postRidePage->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
                $postRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $postRidePage->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
                $postRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $postRidePage->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
                $postRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $postRidePage->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
                $postRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $postRidePage->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
                $postRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $postRidePage->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
                $postRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $postRideError = PostRidePageError::where('post_ride_page_setting_detail_id', $postRidePage->id)->first();
                $postRidePage->from_error = $postRideError->from_error ?? null ;
                $postRidePage->to_error = $postRideError->to_error ?? null ;
                $postRidePage->pick_up_error = $postRideError->pick_up_error ?? null ;
                $postRidePage->drop_off_error = $postRideError->drop_off_error ?? null ;
                $postRidePage->date_error = $postRideError->date_error ?? null ;
                $postRidePage->time_error = $postRideError->time_error ?? null ;
                $postRidePage->recurring_type_error = $postRideError->recurring_type_error ?? null ;
                $postRidePage->recurring_trips_error = $postRideError->recurring_trips_error ?? null ;
                $postRidePage->meeting_drop_off_description_error = $postRideError->meeting_drop_off_description_error ?? null ;
                $postRidePage->seats_error = $postRideError->seats_error ?? null ;
                $postRidePage->seats_middle_error = $postRideError->seats_middle_error ?? null ;
                $postRidePage->seats_back_error = $postRideError->seats_back_error ?? null ;
                $postRidePage->vehicle_id_error = $postRideError->vehicle_id_error ?? null ;
                $postRidePage->make_error = $postRideError->make_error ?? null ;
                $postRidePage->model_error = $postRideError->model_error ?? null ;
                $postRidePage->vehicle_type_error = $postRideError->vehicle_type_error ?? null ;
                $postRidePage->color_error = $postRideError->color_error ?? null ;
                $postRidePage->license_error = $postRideError->license_error ?? null ;
                $postRidePage->year_error = $postRideError->year_error ?? null ;
                $postRidePage->fuel_error = $postRideError->fuel_error ?? null ;
                $postRidePage->photo_error = $postRideError->photo_error ?? null ;
                $postRidePage->booking_method_error = $postRideError->booking_method_error ?? null ;
                $postRidePage->anything_to_add_error = $postRideError->anything_to_add_error ?? null ;
                $postRidePage->smoking_error = $postRideError->smoking_error ?? null ;
                $postRidePage->animal_error = $postRideError->animal_error ?? null ;
                $postRidePage->luggage_error = $postRideError->luggage_error ?? null ;
                $postRidePage->price_error = $postRideError->price_error ?? null ;
                $postRidePage->payment_method_error = $postRideError->payment_method_error ?? null ;
                $postRidePage->booking_type_error = $postRideError->booking_type_error ?? null ;
                $postRidePage->agree_terms_error = $postRideError->agree_terms_error ?? null ;
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('past_time_message', 'past_date_message')->first();
            }
        }


        if ($selectedLanguage) {
            $locale = $selectedLanguage->abbreviation;
        } else {
            $locale = 'en';
        }

        App::setLocale($locale);

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

        $data = ['postRidePage' => $postRidePage, 'messages' => $messages, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Post ride page get successfully');
    }

    public function findRideIndex(Request $request)
    {
        $findRidePage = null;
        $messages = null;
        if ($request->lang_id && $request->lang_id != 0) {


            $selectedLanguage = Language::where('id', $request->lang_id)->first();
            // Retrieve the FindRidePageSettingDetail associated with the selected language
            $findRidePage = FindRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            $postRidePage = PostRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            $findRidePage->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
            $findRidePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($request->lang_id)->value('name');
            $findRidePage->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
            $findRidePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($request->lang_id)->value('name');
            $findRidePage->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
            $findRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($request->lang_id)->value('name');
            $findRidePage->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
            $findRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($request->lang_id)->value('name');
            $findRidePage->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
            $findRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($request->lang_id)->value('name');
            $findRidePage->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
            $findRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($request->lang_id)->value('name');
            $findRidePage->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
            $findRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($request->lang_id)->value('name');
            $findRidePage->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
            $findRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($request->lang_id)->value('name');
            $findRidePage->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
            $findRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($request->lang_id)->value('name');
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('female_user_message', 'star5_passenger_message', 'star4_passenger_message', 'star3_passenger_message', 'passenger_with_review_message', 'search_result_clear_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $findRidePage->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
                $findRidePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $findRidePage->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
                $findRidePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $findRidePage->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
                $findRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $findRidePage->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
                $findRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $findRidePage->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
                $findRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $findRidePage->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
                $findRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $findRidePage->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
                $findRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $findRidePage->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
                $findRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $findRidePage->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
                $findRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('female_user_message', 'star5_passenger_message', 'star4_passenger_message', 'star3_passenger_message', 'passenger_with_review_message', 'search_result_clear_message')->first();
            }
        }

        if ($selectedLanguage) {
            $locale = $selectedLanguage->abbreviation;
        } else {
            $locale = 'en';
        }

        App::setLocale($locale);

        $validationMessages = [
            'required' => trans('validation.required'),
        ];

        $data = ['findRidePage' => $findRidePage, 'messages' => $messages, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Search ride page get successfully');
    }


    public function getDataFromGoogleApi($from, $to){
        $apiKey = env('GOOGLE_API_KEY');
        $ch = curl_init();

        // URL encode the addresses to properly handle spaces and special characters
        // This ensures city names like "Montreal, QC" and "Ottawa, ON" work correctly
        $fromEncoded = urlencode($from);
        $toEncoded = urlencode($to);

        $apiUrl = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$fromEncoded."&destinations=".$toEncoded."&units=imperial&key=".$apiKey."";
        
        Log::info('Google Maps API Request (API)', [
            'from' => $from,
            'to' => $to,
            'from_encoded' => $fromEncoded,
            'to_encoded' => $toEncoded,
            'url' => str_replace($apiKey, 'HIDDEN_KEY', $apiUrl)
        ]);

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            Log::error('Google Maps API cURL Error (API): ' . curl_error($ch), [
                'from' => $from,
                'to' => $to,
                'curl_error' => curl_error($ch),
                'curl_errno' => curl_errno($ch)
            ]);
        }

        curl_close($ch);

        $data = json_decode($response, true);

        // Log API response
        if (isset($data['status']) && $data['status'] === 'OK') {
            $distance = isset($data['rows'][0]['elements'][0]['distance']['value']) ? $data['rows'][0]['elements'][0]['distance']['value'] : 0;
            $distanceText = isset($data['rows'][0]['elements'][0]['distance']['text']) ? $data['rows'][0]['elements'][0]['distance']['text'] : 'N/A';
            $duration = isset($data['rows'][0]['elements'][0]['duration']['value']) ? $data['rows'][0]['elements'][0]['duration']['value'] : 0;
            $durationText = isset($data['rows'][0]['elements'][0]['duration']['text']) ? $data['rows'][0]['elements'][0]['duration']['text'] : 'N/A';
            
            Log::info('Google Maps API Success (API)', [
                'from' => $from,
                'to' => $to,
                'distance_meters' => $distance,
                'distance_km' => round($distance / 1000, 2),
                'distance_text' => $distanceText,
                'duration_seconds' => $duration,
                'duration_text' => $durationText,
                'status' => $data['status']
            ]);
        } else {
            // Log if API returns an error status
            Log::warning('Google Maps API returned non-OK status (API)', [
                'status' => $data['status'] ?? 'unknown',
                'error_message' => $data['error_message'] ?? 'No error message',
                'from' => $from,
                'to' => $to,
                'response' => $data
            ]);
        }

        return $data;
    }

}
