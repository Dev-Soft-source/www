<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChatsPageSettingDetail;
use App\Models\FeaturesSettingDetail;
use Carbon\Carbon;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\PinkRideFaqDetail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\PostRidePageSettingDetail;
use App\Models\Rating;
use App\Models\RecentSearch;
use App\Models\Ride;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Http\Request;

class PinkRideController extends Controller
{
    public function SearchRide(Request $request, $lang = null){
        $rides = null;
        $otherRides = null;
        $paginatedRides = null;

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $findRidePage = null;
        $postRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                $pinkRideFaqs = PinkRideFaqDetail::where('language_id', $selectedLanguage->id)->get();
                // Retrieve the HomePageSettingDetail associated with the selected language
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();

                if ($findRidePage) {
                    // Add features_option1_1 as an additional property
                    $findRidePage->ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option6)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option7)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option16)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->luggage_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->luggage_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->luggage_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->luggage_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->luggage_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->luggage_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->luggage_option4 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->luggage_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->luggage_option5 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->luggage_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->payment_methods_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->payment_methods_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->payment_methods_option4 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->payment_methods_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->smoking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->smoking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->pets_allowed_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->pets_allowed_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->pets_allowed_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->pets_allowed_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->pets_allowed_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->pets_allowed_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }

                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
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
                }
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $pinkRideFaqs = PinkRideFaqDetail::where('language_id', $selectedLanguage->id)->get();
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();

                if ($findRidePage) {
                    // Add features_option1_1 as an additional property
                    $findRidePage->ride_features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option6)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option7)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->ride_features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->ride_features_option16)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->luggage_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->luggage_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->luggage_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->luggage_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->luggage_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->luggage_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->luggage_option4 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->luggage_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->luggage_option5 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->luggage_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->payment_methods_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->payment_methods_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->payment_methods_option4 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->payment_methods_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->smoking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->smoking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->pets_allowed_option1 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->pets_allowed_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->pets_allowed_option2 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->pets_allowed_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $findRidePage->pets_allowed_option3 = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->pets_allowed_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }

                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
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
                }
            }
        }
        
        if ($request->from && $request->to) {
            if (auth()->user()) {
                // Check if user has suspanded
                if (auth()->user()->suspand === '1') {
                    return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "Your account has been suspended by the admin"]);
                }

                // Check if the search already exists
                $existingSearch = RecentSearch::where('user_id', auth()->user()->id)
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
                        'user_id' => auth()->user()->id,
                    ]);
                }
            }

            $from = $request->from;
            $to = $request->to;
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
                })->with(['rideDetail' => function($q) use($from, $to){
                    $q->where('departure','like', '%'.$from.'%')
                    ->where('destination','like', '%'.$to.'%');
                }])
                ->where('status', '!=', 2)
                ->where('suspand', '!=', 1)
                ->where('vehicle_id', '!=', null);

            $otherRides = Ride::whereHas('rideDetail', function($q) use($from, $to){
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
                })->with(['rideDetail' => function($q) use($from, $to){
                    $q->where('departure','like', '%'.$from.'%')
                    ->where('destination','like', '%'.$to.'%');
                }])
                ->where('status', '!=', 2)
                ->where('suspand', '!=', 1)
                ->where('vehicle_id', '!=', null);

            if (auth()->user()) {
                $user_id = auth()->user()->id;
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
                $otherRides = $otherRides->whereNotIn('added_by', $addedByValues);
            }

            if ($request->date) {
                $dateForQuery = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');
                $rides = $rides->where('date', $dateForQuery);
                $otherRides = $otherRides->where('date', $dateForQuery);
            }
            if ($request->driver_age) {
                $rides = $rides->whereHas('driver', function ($query) use ($request) {
                    $query->whereRaw('YEAR(CURDATE()) - YEAR(STR_TO_DATE(dob, "%M %d, %Y")) >= ?', [$request->driver_age]);
                });
                $otherRides = $otherRides->whereHas('driver', function ($query) use ($request) {
                    $query->whereRaw('YEAR(CURDATE()) - YEAR(STR_TO_DATE(dob, "%M %d, %Y")) >= ?', [$request->driver_age]);
                });
            }
            if ($request->driver_phone == 1) {
                $rides = $rides->whereHas('driver', function ($query) {
                    $query->where('phone', '!=', '');
                });
                $otherRides = $otherRides->whereHas('driver', function ($query) {
                    $query->where('phone', '!=', '');
                });
            }
            if ($request->driver_name) {
                $rides = $rides->whereHas('driver', function ($query) use ($request) {
                    $query->where('first_name', $request->driver_name);
                });
                $otherRides = $otherRides->whereHas('driver', function ($query) use ($request) {
                    $query->where('first_name', $request->driver_name);
                });
            }
            if ($request->passenger_rating) {
                $rides->where('features', 'like', '%' . $request->passenger_rating . '%');
                $otherRides->where('features', 'like', '%' . $request->passenger_rating . '%');
            }
            if ($request->payment_method) {
                $rides = $rides->where('payment_method', $request->payment_method);
                $otherRides = $otherRides->where('payment_method', $request->payment_method);
            }
            if ($request->vehicle_type) {
                $rides = $rides->where('vehicle_type', $request->vehicle_type);
                $otherRides = $otherRides->where('vehicle_type', $request->vehicle_type);
            }
            if ($request->luggage) {
                $luggages = explode(';', $request->luggage);
                $rides = $rides->whereIn('luggage', $luggages);
                $otherRides = $otherRides->whereIn('luggage', $luggages);
            }
            if ($request->smoking) {
                $smoking = explode(';', $request->smoking);
                if (in_array($findRidePage->smoking_option1, $smoking)) {
                    $rides = $rides->whereIn('smoke', $smoking);
                    $otherRides = $otherRides->whereIn('smoke', $smoking);
                }
            }
            if ($request->pets) {
                $pets = explode(';', $request->pets);
                $rides = $rides->whereIn('animal_friendly', $pets);
                $otherRides = $otherRides->whereIn('animal_friendly', $pets);
            }
            if ($request->features) {
                $features = explode(';', $request->features);

                $filteredFeatures = array_diff($features, ['1']);

                if (in_array($postRidePage->features_option4, $filteredFeatures)) {
                    $otherRides = $otherRides->where(function ($query) use ($postRidePage, $filteredFeatures) {
                        $query->where(function ($query) use ($postRidePage) {
                            $query->where('features', 'like', '%' . $postRidePage->features_option4 . '%')
                                  ->orWhere('features', 'like', '%' . $postRidePage->features_option5 . '%');
                        });
                    
                        // Check if any feature other than post ride features is present in the features array
                        if (count(array_diff($filteredFeatures, [$postRidePage->features_option4, $postRidePage->features_option5])) > 0) {
                            $query->where(function ($query) use ($filteredFeatures, $postRidePage) {
                                foreach ($filteredFeatures as $feature) {
                                    if ($feature != $postRidePage->features_option4 && $feature != $postRidePage->features_option5) {
                                        $query->where('features', 'like', '%' . $feature . '%');
                                    }
                                }
                            });
                        }
                    });
                }

                if (in_array($postRidePage->features_option6, $filteredFeatures)) {
                    $otherRides = $otherRides->where(function ($query) use ($postRidePage, $filteredFeatures) {
                        $query->where(function ($query) use ($postRidePage) {
                            $query->where('features', 'like', '%' . $postRidePage->features_option6 . '%')
                                  ->orWhere('features', 'like', '%' . $postRidePage->features_option7 . '%');
                        });
                    
                        // Check if any feature other than post ride features is present in the features array
                        if (count(array_diff($filteredFeatures, [$postRidePage->features_option6, $postRidePage->features_option7])) > 0) {
                            $query->where(function ($query) use ($filteredFeatures, $postRidePage) {
                                foreach ($filteredFeatures as $feature) {
                                    if ($feature != $postRidePage->features_option6 && $feature != $postRidePage->features_option7) {
                                        $query->where('features', 'like', '%' . $feature . '%');
                                    }
                                }
                            });
                        }
                    });
                }

                if (!in_array($postRidePage->features_option4, $filteredFeatures) && !in_array($postRidePage->features_option6, $filteredFeatures)) {
                    foreach ($filteredFeatures as $feature) {
                        $otherRides->whereRaw("FIND_IN_SET(?, REPLACE(features, '=', ','))", [$feature]);
                    }
                }

                $otherRides->whereRaw("NOT FIND_IN_SET(?, REPLACE(features, '=', ','))", ['1']);

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
            
            $rides = $rides->orderBy('date', 'asc')->orderBy('time', 'asc')->get()->map(function ($ride) {
                $ride->type = 'ride'; // Identify as ride
                return $ride;
            });
            $otherRides = $otherRides->orderBy('date', 'asc')->orderBy('time', 'asc')->get()->map(function ($ride) {
                $ride->type = 'otherRide'; // Identify as other ride
                return $ride;
            });
            $allRides = $rides->merge($otherRides);
            
            $paginatedRides = new LengthAwarePaginator(
                $allRides->forPage(Paginator::resolveCurrentPage(), 6),
                $allRides->count(),
                6,
                Paginator::resolveCurrentPage(),
                ['path' => Paginator::resolveCurrentPath()]
            );

            if ($request->driver_rating) {
                $filteredRides = [];
                foreach ($rides as $ride) {
                    $ratings = Rating::where(function ($query) use ($ride) {
                        // Ratings where type is 1 and ride_id belongs to the user
                        $query->where('type', '1')
                              ->whereHas('ride', function ($query) use ($ride) {
                                  $query->where('added_by', $ride->added_by);
                              });
                    })
                    ->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->get();

                    // Calculate total average
                    $overallRating = $ratings->avg('average_rating') ?? 0;
                    if ($overallRating >= $request->driver_rating) {
                        $filteredRides[] = $ride;
                    }
                }
                $rides = collect($filteredRides);

                $otherFilteredRides = [];
                foreach ($otherRides as $ride) {
                    $ratings = Rating::where(function ($query) use ($ride) {
                        // Ratings where type is 1 and ride_id belongs to the user
                        $query->where('type', '1')
                              ->whereHas('ride', function ($query) use ($ride) {
                                  $query->where('added_by', $ride->added_by);
                              });
                    })
                    ->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->get();

                    // Calculate total average
                    $overallRating = $ratings->avg('average_rating') ?? 0;
                    if ($overallRating >= $request->driver_rating) {
                        $otherFilteredRides[] = $ride;
                    }
                }
                $otherRides = collect($otherFilteredRides);

                $allRides = $rides->merge($otherRides);
            
                $paginatedRides = new LengthAwarePaginator(
                    $allRides->forPage(Paginator::resolveCurrentPage(), 6),
                    $allRides->count(),
                    6,
                    Paginator::resolveCurrentPage(),
                    ['path' => Paginator::resolveCurrentPath()]
                );
            }
        }

        $ratings = Rating::all();
        $recentSearches = RecentSearch::orderBy('updated_at', 'desc')->limit(3)->get();

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
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

            $recentSearches = RecentSearch::where('user_id', $user_id)->orderBy('updated_at', 'desc')->limit(3)->get();

        }
        return view('pink_ride',['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'postRidePage' => $postRidePage,'pinkRideFaqs' => $pinkRideFaqs,'findRidePage' => $findRidePage,'paginatedRides' => $paginatedRides,'recentSearches' => $recentSearches,'request' => $request,'ratings' => $ratings,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }
}
