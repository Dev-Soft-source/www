<?php

namespace App\Http\Controllers;

use App\Mail\ExtraCareRideMail;
use App\Mail\PinkExtraCareRideMail;
use App\Mail\PinkRideMail;
use App\Mail\RidePostedMail;
use App\Models\Booking;
use App\Models\BookingPageSettingDetail;
use App\Models\CancellationHistory;
use App\Models\CancelRideSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\FolkRideSetting;
use App\Models\Language;
use App\Models\Notification;
use App\Models\PinkRideSetting;
use App\Models\PostRidePageSettingDetail;
use App\Models\PostRidePageSettingSubDetail;
use App\Models\ChatsPageSettingDetail;
use App\Models\Rating;
use App\Models\RecentSearch;
use App\Models\ReviewSetting;
use App\Models\Ride;
use App\Models\RideDetail;
use App\Models\City;
use App\Models\FCMToken;
use App\Models\NoShowHistory;
use App\Models\RideDetailPageSettingDetail;
use App\Models\SiteSetting;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\SeatDetail;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{
    public function SearchRide(Request $request, $lang = null)
    {
        $rides = null;

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
                // Retrieve the HomePageSettingDetail associated with the selected language
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
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


                    $findRidePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_convertible_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_hatchback_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');
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
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();

                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
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
                    $findRidePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_convertible_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_hatchback_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $findRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($findRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');
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
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();

                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }
            }
        }

        if (!auth()->user()) {
            $rides = Ride::where('status', '!=', 2)
                ->where('suspand', '!=', 1)
                ->where('vehicle_id', '!=', null)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->whereDate('date', '>', now()->toDateString())
                            ->orWhere(function ($query) {
                                $query->whereDate('date', '=', now()->toDateString())
                                    ->whereTime('time', '>=', now()->toTimeString());
                            });
                    });
                })->with(['rideDetail' => function ($q) {
                    $q->where('default_ride', '1');
                }])
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->paginate(6);
        }

        if ($request->from && $request->to) {
            if (auth()->user()) {
                // Check if user has suspanded
                if (auth()->user()->suspand === '1') {
                    return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "Your account has been suspended by the admin"]);
                }

                // Check if the search already exists
                $existingSearch = RecentSearch::where('user_id', auth()->user()->id)
                    ->where('from', 'like', '%' . $request->from . '%')
                    ->where('to', 'like', '%' . $request->to . '%')
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
            $rides = Ride::whereHas('rideDetail', function ($q) use ($from, $to) {
                $q->where('departure', 'like', '%' . $from . '%')
                    ->where('destination', 'like', '%' . $to . '%')->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereDate('date', '>', now()->toDateString())
                                ->orWhere(function ($query) {
                                    $query->whereDate('date', '=', now()->toDateString())
                                        ->whereTime('time', '>=', now()->toTimeString());
                                });
                        });
                    });
            })->with(['rideDetail' => function ($q) use ($from, $to) {
                $q->where('departure', 'like', '%' . $from . '%')
                    ->where('destination', 'like', '%' . $to . '%');
            }])->where('status', '!=', 2)
                ->where('suspand', '!=', 1)
                ->where('vehicle_id', '!=', null);

            
            // ->where(function ($query) {
            //     $query->where(function ($query) {
            //         $query->whereDate('date', '>', now()->toDateString())
            //             ->orWhere(function ($query) {
            //                 $query->whereDate('date', '=', now()->toDateString())
            //                     ->whereTime('time', '>=', now()->toTimeString());
            //             });
            //     });
            // });

            if (auth()->user()) {
                $user_id = auth()->user()->id;
                $currentDate = date('Y-m-d H:i:s');
                $userBookings = Booking::where('user_id', $user_id)
                    ->where('removed_permanently', 1)
                    ->where('block_date_time', '>', $currentDate)
                    ->with('ride')
                    ->get();

                // Get the added_by values from userBookings
                $addedByValues = $userBookings->pluck('ride.added_by')->unique()->toArray();

                // Add additional condition to the rides query
                $rides = $rides->whereNotIn('added_by', $addedByValues);
            }

            if ($request->keyword) {

                $keyword = $request->keyword;

                $rides = $rides->where(function ($query) use ($keyword) {
                    $query->where('dropoff', 'like', "%$keyword%")
                        ->orWhere('pickup', 'like', "%$keyword%")
                        ->orWhere('details', 'like', "%$keyword%")
                        ->orWhere('notes', 'like', "%$keyword%");
                });
            }

            if ($request->date) {
                // $dateForQuery = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');
                $dateForQuery = Carbon::parse(strtotime($request->date))->format('Y-m-d');
                $rides = $rides->where('date', $dateForQuery);
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
            if ($request->features) {
                $features = explode(';', $request->features);

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
            $rides = $rides->orderBy('date', 'asc')->orderBy('time', 'asc')->paginate(6);
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

                // Now paginate the filtered rides
                $rides = new LengthAwarePaginator(
                    $rides->forPage(Paginator::resolveCurrentPage(), 6),
                    $rides->count(),
                    6,
                    Paginator::resolveCurrentPage(),
                    ['path' => Paginator::resolveCurrentPath()]
                );
            }
        }

        

        $ratings = Rating::all();
        $recentSearches = RecentSearch::orderBy('updated_at', 'desc')->limit(2)->get();

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

            $recentSearches = RecentSearch::where('user_id', $user_id)->orderBy('updated_at', 'desc')->limit(2)->get();
        }
        $pinkRideSetting = PinkRideSetting::first();
        $firm_cancellation_discount = SiteSetting::first();
        $firm_cancellation_discount = $firm_cancellation_discount->frim_discount;
        return view('search_ride', ['notificationPage' => $notificationPage, 'pinkRideSetting' => $pinkRideSetting, 'successMessage' => $successMessage, 'postRidePage' => $postRidePage, 'findRidePage' => $findRidePage, 'rides' => $rides, 'recentSearches' => $recentSearches, 'request' => $request, 'ratings' => $ratings, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'firm_cancellation_discount' => $firm_cancellation_discount]);
    }

    public function RideDetail(Request $request, $lang = null)
    {
        $id = $request->id;
        $from = $request->departure;
        $to = $request->destination;
        $ride = Ride::where('id', $request->id)
            ->with(['rideDetail' => function ($q) use ($from, $to, $id) {
                $q->where('departure', 'like', '%' . $from . '%')
                    ->where('destination', 'like', '%' . $to . '%')
                    ->where('ride_id', $id);
            }])->first();

        if (!isset($ride) && empty($ride)) {
            $lang = $lang ?? "en";
            return redirect(route('home', ['lang' => $lang]));
        }

        $setting = ReviewSetting::first();
        $cancelSetting = CancelRideSetting::first();
        $ratings = Rating::all();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $rideDetailPage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {

                // Retrieve the Page associated with the selected language
                $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $selectedLanguage->id)->first();

                $chatsPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('id', 'chats_page_setting_id', 'language_id', 'type_message_placeholder')->first();
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('popup_close_btn_text', 'popup_login_btn_text', 'popup_signup_btn_text', 'cancel_button', 'delete_button')->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();

                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
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
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
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
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }

                $ride->luggage = FeaturesSettingDetail::whereFeaturesSettingId($ride->luggage)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $ride->payment_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->payment_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->booking_type = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_type)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->animal_friendly = FeaturesSettingDetail::whereFeaturesSettingId($ride->animal_friendly)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $ride->booking_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $featureIds = explode('=', $ride->features);
                // Fetch data for each feature ID and concatenate with '='
                $featureNames = collect($featureIds)->map(function ($id) use ($selectedLanguage) {
                    return FeaturesSettingDetail::whereFeaturesSettingId($id)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                })->filter()->implode('='); // Filter out nulls and concatenate with '='
                $ride->features = $featureNames;
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('popup_close_btn_text', 'popup_login_btn_text', 'popup_signup_btn_text', 'cancel_button', 'delete_button')->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();


                $chatsPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('id', 'chats_page_setting_id', 'language_id', 'type_message_placeholder')->first();

                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
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
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
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
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }

                $ride->luggage = FeaturesSettingDetail::whereFeaturesSettingId($ride->luggage)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->payment_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->payment_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->booking_type = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_type)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->animal_friendly = FeaturesSettingDetail::whereFeaturesSettingId($ride->animal_friendly)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $ride->booking_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $featureIds = explode('=', $ride->features);
                // Fetch data for each feature ID and concatenate with '='
                $featureNames = collect($featureIds)->map(function ($id) use ($selectedLanguage) {
                    return FeaturesSettingDetail::whereFeaturesSettingId($id)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                })->filter()->implode('='); // Filter out nulls and concatenate with '='
                $ride->features = $featureNames;
            }
        }

        if (auth()->check()) {
            $user = auth()->user();
            if ($user->step === '1') {
                return redirect()->route('step1to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($user->step === '2') {
                return redirect()->route('step2to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($user->step === '3') {
                return redirect()->route('step3to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($user->step === '4') {
                return redirect()->route('step5to5', ['lang' => $selectedLanguage->abbreviation]);
            }
        }


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

            $ride_booking = Booking::where('ride_id', $ride->id)->where('user_id', auth()->user()->id)->select('status')->first();
        }
        $ride_cancelled = false;
        $completed_date_time = Carbon::parse($ride->completed_date . ' ' . $ride->completed_time);
        if (isset($ride_booking) && ($completed_date_time < Carbon::now() ||  $ride_booking->status == '3' ||  $ride_booking->status == '4')) {
            $ride_cancelled = true;
        }
        return view('ride_detail', ['notificationPage' => $notificationPage, 'ride_cancelled' => $ride_cancelled, 'ride_cancelled' => $ride_cancelled, 'rideDetailPage' => $rideDetailPage, 'ride' => $ride, 'setting' => $setting, 'cancelSetting' => $cancelSetting, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'postRidePage' => $postRidePage, 'ratings' => $ratings, 'chatsPage' => $chatsPage, 'successMessage' => $successMessage]);
    }

    public function MyCoPassengers(Request $request, $lang = null)
    {
        $ride = Ride::where('id', $request->id)->first();
        $setting = ReviewSetting::first();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'payment_methods_option3', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'luggage_option1', 'luggage_option2', 'luggage_option3', 'luggage_option4', 'luggage_option5', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'payment_methods_option3', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'luggage_option1', 'luggage_option2', 'luggage_option3', 'luggage_option4', 'luggage_option5', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
            }
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is 2 and booking_id belongs to the user
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is null and receiver_id belongs to the user
                    $query->where('type', null)
                        ->whereHas('receiver', function ($query) use ($user_id) {
                            $query->where('id', $user_id);
                        });
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        $ratings = Rating::all();
        return view('my_co_passengers', ['ride' => $ride, 'setting' => $setting, 'ratings' => $ratings, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'postRidePage' => $postRidePage]);
    }

    public function EditRide($lang, $id)
    {
        $ride = Ride::with(['defaultRideDetail', 'MoreRideDetail'])->where('id', $id)->first();


        if (!isset($ride) && empty($ride)) {
            $lang = $lang ?? "en";
            return redirect(route('home', ['lang' => $lang]));
        }

        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $pinkRideSetting = PinkRideSetting::first();
        $setting = FolkRideSetting::first();
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
                $overallRating = $ratings->avg('average_rating') ?? 0;
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $postRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
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
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
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
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();

                    $postRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');
                }
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
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
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
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
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();

                    $postRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');
                }
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


        return view('edit_ride', ['notificationPage' => $notificationPage, 'successMessage' => $successMessage, 'postRidePage' => $postRidePage, 'ride' => $ride, 'user' => $user, 'vehicles' => $vehicles, 'pinkRideSetting' => $pinkRideSetting, 'setting' => $setting, 'overallRating' => $overallRating, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'routeType' => 'edit']);
    }

    public function UpdateRide($lang, $ride_id, Request $request)
    {
        $ride = Ride::where('id', $ride_id)->first();
        $user = auth()->user();
        $user_id = $user->id;
        $rides = Ride::where('added_by', $user_id)->whereNotIn('id', [$ride_id])->get();
        $adminSetting = SiteSetting::first();
        
        // Check if ride has any bookings - if so, price cannot be changed
        $hasBookings = Booking::where('ride_id', $ride_id)
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
                return back()->with('error', 'You cannot change the price once passengers have booked this ride.')
                    ->with('heading', 'Price Change Not Allowed')
                    ->withInput();
            }
        }

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_post_message', 'ride_schedule_message', 'block_post_ride_message', 'overlap_ride_message', 'ride_dead_time_text', 'profile_photo_required_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_post_message', 'ride_schedule_message', 'block_post_ride_message', 'overlap_ride_message', 'ride_dead_time_text', 'profile_photo_required_message')->first();
        }

        if ($user->block_post_ride == '1') {
            return $this->apiErrorResponse($message->block_post_ride_message ?? null, 200);
        }


        // Check if user has suspanded
        if ($user->suspand === '1') {
            return back()->with('message', 'Your account has been suspended by the admin');
        }

        if (!isset($user->profile_image) || $user->profile_image == '' || in_array(basename($user->profile_image), ['male.png', 'female.png', 'neutral.png'])) {
            return back()->with('message', $message->profile_photo_required_message ?? 'For posting a ride profile photo is required');
        }

        $formattedDate = Carbon::parse($request->date)->format('Y-m-d');
        $formattedTime = Carbon::createFromFormat('H:i', $request->time)->format('H:i:s');
        $rides = DB::table('rides')
            ->where('status', '!=', 2)
            ->where('id', '!=', $ride->id)
            ->where('added_by', $user_id)
            ->where(function ($query) use ($formattedDate, $formattedTime) {
                $query->where('date', '<=', $formattedDate)
                    ->where('time', '<=', $formattedTime);
            })
            ->where(function ($query) use ($formattedDate, $formattedTime) {
                $query->where('destination_reached_date', '>=', $formattedDate)
                    ->where('destination_reached_time', '>=', $formattedTime);
            })
            ->first();

        if (isset($rides) && !empty($rides)) {
            $oldInput = $request->all();
            return back()->with('error', $message->overlap_ride_message ?? 'this ride overlaps with an existing ride you already have')->with('heading', 'Ride already schedule')->withInput($oldInput)->with('uploaded_image', $filename ?? null);
        }
        $rideDateTime = Carbon::parse($formattedDate . ' ' . $formattedTime);
        if ($rideDateTime->lte(Carbon::now()->addMinutes($adminSetting->ride_post_dead_time ?? 0))) {
            return redirect()->back()
                ->with('error', $message->ride_dead_time_text ?? 'The ride time you selected is too close. Please select a time that is more than 15 minutes in the future')
                ->withInput();
        }
        $skip_vehicle = $request->filled('skip_vehicle') ? $request->skip_vehicle : 0;
        $add_vehicle = $request->filled('add_vehicle') ? $request->add_vehicle : 0;
        $added_vehicle = $request->filled('added_vehicle') ? $request->added_vehicle : 0;

        $recurring = $request->filled('recurring') ? $request->recurring : '0';



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
            'features' => 'array|min:1',
            'booking_method' => 'required',
            'luggage' => 'required',
            'price' => 'required|numeric|gt:0',
            'payment_method' => 'required',
            'notes' => 'nullable|string|max:300',
            'middle_seats' => 'required|numeric|min:1',
            'back_seats' => 'required|numeric|min:1',
            'agree_terms' => 'required',
            'image' => $request->has('existing_image') || $skip_vehicle !== 0 ? 'nullable|mimes:jpeg,png,jpg,gif|max:10240' : 'required|mimes:jpeg,png,jpg,gif|max:10240',
            'make' => $add_vehicle == 1 ? 'required' : 'nullable',
            'model' => $add_vehicle == 1 ? 'required' : 'nullable',
            'vehicle_type' => $add_vehicle == 1 ? 'required' : 'nullable',
            'year' => $add_vehicle == 1 ? 'required' : 'nullable',
            'color' => $add_vehicle == 1 ? 'required' : 'nullable',
            'license_no' => $add_vehicle == 1 ? 'required' : 'nullable',
            'car_type' => $add_vehicle == 1 ? 'required' : 'nullable',
            'recurring_type' => $recurring !== '0' ? 'required' : 'nullable',
            'recurring_trips' => $recurring !== '0' ? 'required' : 'nullable',
        ]);



        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $file->move($destination_path, $filename);
        } elseif ($request->has('existing_image')) {
            $filename = $request->input('existing_image');
        } elseif ($skip_vehicle !== 0) {
            $filename = '';
        }

        // Check if any existing ride has the same date and time
        // foreach ($rides as $existingRide) {
        //     if (
        //         $existingRide->date == Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d') &&
        //         $existingRide->time == $request->time . ':00'
        //     ) {
        //         $oldInput = $request->all();
        //         return back()->with('error', 'You already have a ride scheduled for the same date and time.')->with('heading', 'Ride already schedule')->withInput($oldInput)->with('uploaded_image', $filename ?? null);
        //     }
        // }



        $max_back_seats = $request->filled('max_back_seats') ? $request->max_back_seats : 0;
        $accept_more_luggage = $request->filled('accept_more_luggage') ? $request->accept_more_luggage : 0;
        $open_customized = $request->filled('open_customized') ? $request->open_customized : 0;

        // Join the selected checkboxes with semicolons.
        $features = implode('=', $request->input('features', []));

        // Initialize vehicle variables to prevent undefined variable errors
        $make = '';
        $model = '';
        $vehicle_type = '';
        $year = '';
        $color = '';
        $license_no = '';
        $car_type = '';

        if ($skip_vehicle !== 0) {
            $make = '';
            $model = '';
            $vehicle_type = '';
            $year = '';
            $color = '';
            $license_no = '';
            $car_type = '';
        }

        if ($add_vehicle !== 0) {
            // Preserve original values if request values are empty (for edit mode when fields are readonly/disabled)
            $make = $request->make ?: $ride->make ?? '';
            $model = $request->model ?: $ride->model ?? '';
            $vehicle_type = $request->vehicle_type ?: $ride->vehicle_type ?? '';
            $year = $request->year ?: $ride->year ?? '';
            $color = $request->color ?: $ride->color ?? '';
            $license_no = $request->license_no ?: $ride->license_no ?? '';
            $car_type = $request->car_type ?: $ride->car_type ?? '';

            // Create new vehicle with the values (preserved from original if request was empty)
            if ($make || $model || $vehicle_type) {
                $vehicle = Vehicle::create([
                    'user_id' => auth()->user()->id,
                    'make' => $make,
                    'model' => $model,
                    'type' => $vehicle_type,
                    'liscense_no' => $license_no,
                    'color' => $color,
                    'year' => $year,
                    'car_type' => $car_type,
                    'image' => $filename,
                    'original_image' => $filename,
                ]);
                $vehicle_id = $vehicle->id;
            }
        }

        if ($added_vehicle !== 0) {
            $vehicle = Vehicle::whereId($request->vehicle_id)->first();
            if ($vehicle) {
                $make = $vehicle->make;
                $model = $vehicle->model;
                $vehicle_type = $vehicle->type;
                $year = $vehicle->year;
                $color = $vehicle->color;
                $license_no = $vehicle->liscense_no;
                $car_type = $vehicle->car_type;
                $vehicle_id = $vehicle->id;
                if ($vehicle->remove_image === '0') {
                    $imageName = basename($vehicle->image);
                    $filename = $imageName;
                } else {
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
            }
        }

        if ($recurring == '1') {
            $recurring_type = $request->recurring_type;
            $recurring_trips = $request->recurring_trips;
        } else {
            $recurring_type = '';
            $recurring_trips = '';
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
            'car_image' => $filename,
            'car_image_original' => $filename,
            'smoke' => $request->smoke,
            'animal_friendly' => $request->animal_friendly,
            'features' => $features,
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


        for ($i = 1; $i <= $ride->seats; $i++) {
            $seatDetail = new SeatDetail;
            $seatDetail->ride_id = $ride->id;
            $seatDetail->seat_number = $i;
            $seatDetail->status = 'pending';
            $seatDetail->save();
        }

        //Second - Get distance and duration from Google Maps API
        $duration = 0;
        $distance = 0;
        // Use original from/to values - getDataFromGoogleApi will properly encode them
        $from = $request->from;
        $to = $request->to;
        $fromArray = explode(',', $request->from);
        $toArray = explode(',', $request->to);
        
        Log::info('Calculating distance for ride update', [
            'ride_id' => $ride->id,
            'from' => $from,
            'to' => $to,
            'user_id' => $user_id
        ]);
        
        $googleApiData = $this->getDataFromGoogleApi($from, $to);
        if (isset($googleApiData) && !empty($googleApiData)) {
            $duration = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;

            $distance = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
        }

        if ($distance != 0) {
            $distance = round(($distance / 1000), 2);
        }
        
        Log::info('Distance calculation completed for ride update', [
            'ride_id' => $ride->id,
            'from' => $from,
            'to' => $to,
            'distance_km' => $distance,
            'duration_seconds' => $duration,
            'distance_meters' => $distance * 1000
        ]);

        if (isset($request->default_ride_detail_id)) {
            $rideDetail = RideDetail::where('id', $request->default_ride_detail_id)->first();
        } else {
            $rideDetail = new RideDetail();
        }

        $rideDetail->ride_id = $ride->id;
        $rideDetail->departure = $request->from;
        $rideDetail->destination = $request->to;
        $rideDetail->default_ride = 1;
        $rideDetail->total_distance = $distance;
        $rideDetail->total_duration = $duration;
        
        Log::info('Saving ride detail with distance', [
            'ride_id' => $ride->id,
            'ride_detail_id' => $rideDetail->id ?? 'new',
            'departure' => $request->from,
            'destination' => $request->to,
            'total_distance_km' => $distance,
            'total_duration_seconds' => $duration
        ]);
        
        // Cost-sharing cap validation: Error-Triggering Cap $0.72/km
        if ($distance > 0 && isset($request->price) && $request->price > 0) {
            $pricePerKm = $request->price / $distance;
            
            Log::info('Price per kilometer calculation (UpdateRide)', [
                'ride_id' => $ride->id,
                'price' => $request->price,
                'distance_km' => $distance,
                'price_per_km' => round($pricePerKm, 4),
                'error_cap' => 0.72,
                'warning_cap' => 0.66
            ]);
            
            // Error-Triggering Cap: $0.72 per km - BLOCK if exceeded
            if ($pricePerKm > 0.72) {
                Log::warning('Price exceeds error-triggering cap (UpdateRide)', [
                    'ride_id' => $ride->id,
                    'price_per_km' => round($pricePerKm, 4),
                    'cap' => 0.72
                ]);
                
                return back()->with('error', 'The price per kilometer ($' . number_format($pricePerKm, 2) . '/km) exceeds the maximum allowed for cost-sharing rides ($0.72/km). Please adjust your price.')
                    ->with('heading', 'Price Too High')
                    ->withInput();
            }
            
            // Soft Warning Cap: $0.66 per km - WARN but ALLOW
            if ($pricePerKm > 0.66) {
                Log::info('Price exceeds soft warning cap but within error cap (UpdateRide)', [
                    'ride_id' => $ride->id,
                    'price_per_km' => round($pricePerKm, 4),
                    'warning_cap' => 0.66
                ]);
                
                // Store warning in session - will be shown to user via modal/popup
                session()->flash('price_warning', [
                    'message' => 'Your price per kilometer ($' . number_format($pricePerKm, 2) . '/km) is above the recommended cost-sharing rate ($0.66/km) but within the allowed maximum ($0.72/km).',
                    'price_per_km' => round($pricePerKm, 2)
                ]);
            }
        }
        
        $rideDetail->price = $request->price;
        $rideDetail->time = $request->time;
        $rideDetail->date = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');

        if (isset($adminSetting)) {

            if (isset($ride->date) && isset($ride->time)) {
                $rideDateTime = Carbon::parse("$ride->date $ride->time");
                $apiTime = 0;
                if ($duration != 0) {
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

                $ride->completed_date = $completedDate ?? '';
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


        if (isset($request->from_spot) && !empty($request->from_spot)) {
            foreach ($request->from_spot as $key => $from_spot) {
                $duration = 0;
                $distance = 0;
                $fromArray = explode(',', $request->from_spot[$key]);
                $toArray = explode(',', $request->to_spot[$key]);
                $googleApiData = $this->getDataFromGoogleApi($request->from_spot[$key], $request->to_spot[$key]);
                if (isset($googleApiData) && !empty($googleApiData)) {
                    $duration = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;

                    $distance = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
                }

                if ($distance != 0) {
                    $distance = round(($distance / 1000), 2);
                }

                if (isset($request->ride_detail_ids) && isset($request->ride_detail_ids[$key]) && $request->ride_detail_ids[$key] != "0") {
                    $rideDetail = RideDetail::where('id', $request->ride_detail_ids[$key])->first();
                } else {
                    $rideDetail = new RideDetail();
                }
                $rideDetail->ride_id = $ride->id;
                $rideDetail->departure = $request->from_spot[$key];
                $rideDetail->destination = $request->to_spot[$key];
                $rideDetail->default_ride = 0;
                $rideDetail->total_distance = $distance;
                $rideDetail->total_duration = $duration;
                $rideDetail->price = $request->price_spot[$key];
                $rideDetail->time = $request->time;
                $rideDetail->date = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');

                if (isset($adminSetting)) {

                    if (isset($ride->date) && isset($ride->time)) {
                        $rideDateTime = Carbon::parse("$ride->date $ride->time");
                        $apiTime = 0;
                        if ($duration != 0) {
                            $apiTime = round(($duration / 3600), 2);
                        }

                        // $rideDateTime->addHours($adminSetting->destination_hours);
                        // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                        $totalHours = $duration / 3600;  // e.g., 109800 seconds → 30.5 hours
                        $fullHours = floor($totalHours);  // 30 hours
                        $minutes = round(($totalHours - $fullHours) * 60);  // 30 minutes
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

        // Check if the ride is recurring
        if ($recurring == '1') {
            // Determine the frequency and number of recurring trips
            $frequency = $request->input('recurring_type');
            $numRecurringTrips = $request->input('recurring_trips');

            // Calculate the date interval based on the frequency
            $dateInterval = ($frequency === 'Daily') ? 'P1D' : 'P7D';

            $existingRecurringRides = Ride::where('recurring_id', $ride_id)->get();
            $initialRide = Ride::where('id', $ride_id)->first();

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
                        'time' => $request->time,
                        'completed_date' => $nextCompletedDate->format('Y-m-d'),
                        'completed_time' => $initialRide->completed_time,
                        'destination_reached_date' => $nextDestinationReachedDate->format('Y-m-d'),
                        'destination_reached_time' => $initialRide->destination_reached_time,
                        'recurring' => $recurring,
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
                        'car_image' => $filename,
                        'car_image_original' => $filename,
                        'smoke' => $request->smoke,
                        'animal_friendly' => $request->animal_friendly,
                        'features' => $features,
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


                    for ($j = 1; $j <= $initialRide->seats; $j++) {
                        $seatDetail = new SeatDetail;
                        $seatDetail->ride_id = $initialRide->id;
                        $seatDetail->seat_number = $j;
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
                        'recurring_id' => $ride_id,
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
                        'car_image' => $filename,
                        'car_image_original' => $filename,
                        'smoke' => $request->smoke,
                        'animal_friendly' => $request->animal_friendly,
                        'features' => $features,
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
                        'added_on' => now(),
                    ]);


                    for ($j = 1; $j <= $initialRide->seats; $j++) {
                        $seatDetail = new SeatDetail;
                        $seatDetail->ride_id = $initialRide->id;
                        $seatDetail->seat_number = $j;
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

        return redirect()->route('my_rides', ['lang' => $selectedLanguage->abbreviation]);
        // return redirect()->route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->defaultRideDetail[0]->departure, 'destination' => $ride->defaultRideDetail[0]->destination, 'id' => $ride->id]);
    }

    public function CopyRide($lang, $id)
    {
        $ride = Ride::with(['defaultRideDetail', 'MoreRideDetail'])->where('id', $id)->first();
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $pinkRideSetting = PinkRideSetting::first();
        $setting = FolkRideSetting::first();
        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $rides = Ride::where('added_by', $user_id)->get();
        $noshows = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();

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
                $overallRating = $ratings->avg('average_rating') ?? 0;
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $postRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();

                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
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
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
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
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();

                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
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
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
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
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }
            }
        }

        $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '1')
                ->whereHas('ride', function ($query) use ($user_id) {
                    $query->where('added_by', $user_id);
                });
        })
            ->orWhere(function ($query) use ($user_id) {
                // Ratings where type is 2 and booking_id belongs to the user
                $query->where('type', '2')
                    ->whereHas('booking', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });
            })
            ->orWhere(function ($query) use ($user_id) {
                // Ratings where type is null and receiver_id belongs to the user
                $query->where('type', null)
                    ->whereHas('receiver', function ($query) use ($user_id) {
                        $query->where('id', $user_id);
                    });
            })
            ->orderBy('id', 'desc')
            ->get();

        $isNewForm = false;
        $noShowsCount = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
        $cancellationCount = CancellationHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();
        return view('post_ride', ['postRideSubDetailPage' => $postRideSubDetailPage, 'postRidePage' => $postRidePage, 'cancellationCount' => $cancellationCount, 'noShowsCount' => $noShowsCount, 'isNewForm' => $isNewForm, 'ride' => $ride, 'noshows' => $noshows, 'user' => $user, 'vehicles' => $vehicles, 'pinkRideSetting' => $pinkRideSetting, 'setting' => $setting, 'overallRating' => $overallRating, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'routeType' => 'copy']);
    }

    public function RepostRide($lang, $id)
    {
        $ride = Ride::with(['defaultRideDetail', 'MoreRideDetail'])->where('id', $id)->first();
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $pinkRideSetting = PinkRideSetting::first();
        $setting = FolkRideSetting::first();
        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $rides = Ride::where('added_by', $user_id)->get();

        $noshows = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();

        if ($ride) {
            // Swap departure and destination (From and To)
            $temp = $ride->defaultRideDetail[0]->departure;
            $ride->defaultRideDetail[0]->departure = $ride->defaultRideDetail[0]->destination;
            $ride->defaultRideDetail[0]->destination = $temp;

            // Swap pickup and dropoff locations
            $temp1 = $ride->pickup;
            $ride->pickup = $ride->dropoff;
            $ride->dropoff = $temp1;

            // Clear the date (user must select a new date for return ride)
            $ride->date = null;
            
            // Keep the time the same (already preserved from $ride->time)
        }

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
                $overallRating = $ratings->avg('average_rating') ?? 0;
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $postRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();

                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
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
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
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
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();

                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
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
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
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
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }
            }
        }

        $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '1')
                ->whereHas('ride', function ($query) use ($user_id) {
                    $query->where('added_by', $user_id);
                });
        })
            ->orWhere(function ($query) use ($user_id) {
                // Ratings where type is 2 and booking_id belongs to the user
                $query->where('type', '2')
                    ->whereHas('booking', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });
            })
            ->orWhere(function ($query) use ($user_id) {
                // Ratings where type is null and receiver_id belongs to the user
                $query->where('type', null)
                    ->whereHas('receiver', function ($query) use ($user_id) {
                        $query->where('id', $user_id);
                    });
            })
            ->orderBy('id', 'desc')
            ->get();

        $isNewForm = false;

        $totalNoOfRides = Ride::where('added_by', $user_id)
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
        $noShowsCount = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
        $cancellationCount = CancellationHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();

        return view('post_ride', ['postRideSubDetailPage' => $postRideSubDetailPage, 'postRidePage' => $postRidePage, 'isNewForm' => $isNewForm, 'ride' => $ride, 'user' => $user, 'vehicles' => $vehicles, 'pinkRideSetting' => $pinkRideSetting, 'setting' => $setting, 'overallRating' => $overallRating, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'routeType' => 'repost', 'noshows' => $noshows, 'totalNoOfRides' => $totalNoOfRides, 'noShowsCount' => $noShowsCount, 'cancellationCount' => $cancellationCount]);
    }

    public function PostRide($lang = null)
    {
        $user_id = auth()->user()->id;


        $user = User::whereId($user_id)->first();
        $pinkRideSetting = PinkRideSetting::first();
        $setting = FolkRideSetting::first();
        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $rides = Ride::where('added_by', $user_id)->get();
        $noshows = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();

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
                $overallRating = $ratings->avg('average_rating') ?? 0;
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $postRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
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
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
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
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();

                    $postRidePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');
                }
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
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
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
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
                    $postRidePage->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();

                    $postRidePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');

                    $postRidePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');
                }
            }
        }



        if ($user->step === '1') {
            return redirect()->route('step1to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step === '2') {
            return redirect()->route('step2to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step === '3') {
            return redirect()->route('step3to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step === '4') {
            return redirect()->route('step4to5', ['lang' => $selectedLanguage->abbreviation]);
        }

        // Check if user has suspanded
        if ($user->suspand === '1') {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "Your account has been suspended by the admin"]);
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


        $ride = new Ride();
        $isNewForm = true;

        $totalNoOfRides = Ride::where('added_by', $user_id)
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
        $noShowsCount = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
        $cancellationCount = CancellationHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();

        return view('post_ride', ['notificationPage' => $notificationPage, 'successMessage' => $successMessage, 'totalRides' => $totalNoOfRides, 'noShowsCount' => $noShowsCount, 'cancellationCount' => $cancellationCount, 'postRidePage' => $postRidePage, 'postRideSubDetailPage' => $postRideSubDetailPage, 'isNewForm' => $isNewForm, 'noshows' => $noshows, 'ride' => $ride, 'user' => $user, 'vehicles' => $vehicles, 'pinkRideSetting' => $pinkRideSetting, 'setting' => $setting, 'overallRating' => $overallRating, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'routeType' => 'create']);
    }

    public function PostRideStore(Request $request)
    {
        // dd($request->all());
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $rides = Ride::where('added_by', $user_id)->get();
        $adminSetting = SiteSetting::first();

        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_post_message', 'ride_schedule_message', 'block_post_ride_message', 'not_allowed_post_ride_state_wise_message', 'profile_photo_required_message', 'overlap_ride_message', 'ride_dead_time_text')->first();
                $cityErrorMessage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('city_not_in_record')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $cityErrorMessage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('city_not_in_record')->first();

                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_post_message', 'ride_schedule_message', 'block_post_ride_message', 'not_allowed_post_ride_state_wise_message', 'overlap_ride_message', 'profile_photo_required_message', 'ride_dead_time_text')->first();
            }
        }

        if ($user->block_post_ride == '1') {
            return back()->with('message', $message->block_post_ride_message);
        }

        if (!isset($user->profile_image) || $user->profile_image == '' || in_array(basename($user->profile_image), ['male.png', 'female.png', 'neutral.png'])) {
            return back()->with('message', $message->profile_photo_required_message ?? 'For posting a ride profile photo is required');
        }

        // Check if user has suspanded
        if ($user->suspand === '1') {
            return back()->with('message', 'Your account has been suspended by the admin');
        }

        // Feature gatekeeping logic for Pink Ride and Extra Care Ride
        if ($request->has('features') && is_array($request->features)) {
            $pinkRideSetting = \App\Models\PinkRideSetting::first();
            $folkRideSetting = \App\Models\FolkRideSetting::first();
            $postRidePage = \App\Models\PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();

            // Get feature IDs for Pink Ride and Extra Care Ride
            $pinkRideFeatureId = $postRidePage->features_option1->features_setting_id ?? null;
            $extraCareFeatureId = $postRidePage->features_option2->features_setting_id ?? null;

            // Check if Pink Ride feature is selected
            if ($pinkRideFeatureId && in_array($pinkRideFeatureId, $request->features)) {
                // Check if driver's license is required and uploaded
                if ($pinkRideSetting && $pinkRideSetting->driver_license === '1') {
                    if (empty($user->driver_license_upload)) {
                        return back()->with('message', 'A government-issued photo ID (driver\'s license) is required to post Pink Rides. Please upload your driver\'s license in your profile.');
                    }
                }
            }

            // Check if Extra Care Ride feature is selected
            if ($extraCareFeatureId && is_array($request->features) && in_array($extraCareFeatureId, $request->features)) {
                // Check if driver's license is required and uploaded
                if ($folkRideSetting && $folkRideSetting->driver_license === '1') {
                    if (empty($user->driver_license_upload)) {
                        return back()->with('message', 'A government-issued photo ID (driver\'s license) is required to post Extra Care Rides. Please upload your driver\'s license in your profile.');
                    }
                }
            }
        }

        // Safely get from_spot and to_spot values with null checks
        $fromSpot = $request->from_spot[0] ?? null;
        $from_city = $fromSpot ? trim(explode(',', $fromSpot)[0]) : null;

        $toSpot = $request->to_spot[0] ?? null;
        $to_city = $toSpot ? trim(explode(',', $toSpot)[0]) : null;


        if(!empty($fromSpot) && !empty($toSpot)){
            $validator = Validator::make($request->all(), [
                'from_spot' => 'required|exists:cities,name',
                'to_spot' => 'required|exists:cities,name',
                'price_spot' => 'required',
    
            ], [
                'from_spot.exists' => $cityErrorMessage->city_not_in_record,
                'to_spot.exists' => $cityErrorMessage->city_not_in_record,
            ]);
            // dd($request->price_spot);
    // dd(count(array_filter($request->price_spot, fn($value) => $value == null))>0);
            $priceSpotHasNull = is_array($request->price_spot) && count(array_filter($request->price_spot, fn($value) => $value === null || $value === '')) > 0;
            $cityNotInRecord = $cityErrorMessage->city_not_in_record ?? 'City not found in records';

            if ((!$from_city || !DB::table('cities')->where('name', $from_city)->exists()) && (!$to_city || !DB::table('cities')->where('name', $to_city)->exists()) || $priceSpotHasNull) {
                 $errors = [
                     'from_spot' => $cityNotInRecord,
                     'to_spot' => $cityNotInRecord,
                    ];
                    if($priceSpotHasNull){
                        $errors = [
                            'price' => __('validation.required', ['attribute' => 'price'])
                        ];
                     }
                return back()
                    ->with('custom_errors', $errors)
                    ->withInput();
            }


            if ($from_city && !DB::table('cities')->where('name', $from_city)->exists()) {
                $errors = [
                    'from_spot' => $cityNotInRecord,
                ];
                return back()
                    ->with('custom_errors', $errors)
                    ->withInput();
            }

            if ($to_city && !DB::table('cities')->where('name', $to_city)->exists()) {
                $errors = [
                    'to_spot' => $cityNotInRecord,
                ];
                return back()
                    ->with('custom_errors', $errors)
                    ->withInput();
            }
        }


        




        $customMessages = [
            'date' => 'Invalid date format',
            // 'time' => 'Invalid time format',
            'array' => 'The :attribute must be an array',
            'max' => 'The :attribute may not be greater than :max characters',
            'max_words' => 'The :attribute may not be greater than 300 words',
            'file' => 'Please select a valid file',
            'mimes' => 'The :attribute must be a file of type: jpeg, png',
            'image.max' => 'Can not upload image size greater than 10MB',
            'uploaded' => 'The image is not uploaded yet',
            'numeric' => 'This field must be a number'
        ];

        $skip_vehicle = $request->filled('skip_vehicle') ? $request->skip_vehicle : 0;
        $add_vehicle = $request->filled('add_vehicle') ? $request->add_vehicle : 0;
        $added_vehicle = $request->filled('added_vehicle') ? $request->added_vehicle : 0;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('car_images');
            $file->move($destination_path, $filename);
        } elseif ($request->has('existing_image')) {
            $filename = $request->input('existing_image');
        } elseif ($skip_vehicle !== 0) {
            $filename = '';
        }

        $recurring = $request->filled('recurring') ? $request->recurring : 0;



        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required',
            'pickup' => 'required',
            'dropoff' => 'required',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'details' => 'required|string|max_words:300',
            'seats' => 'required',
            'smoke' => 'required',
            'animal_friendly' => 'required',
            'features' => 'array',
            'booking_method' => 'required',
            'booking_type' => 'required',
            'luggage' => 'required',
            'price' => 'required|numeric|gt:0',
            'payment_method' => 'required',
            'notes' => 'nullable|string|max:300',
            'middle_seats' => 'required',
            'back_seats' => 'required',
            'agree_terms' => 'required',
            'image' => $request->has('existing_image') || $add_vehicle !== 0 ? 'nullable|mimes:jpeg,png,jpg,gif|max:10240' : 'required|mimes:jpeg,png,jpg,gif|max:10240',
            'make' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'model' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'vehicle_type' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'year' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'color' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'license_no' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'car_type' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'vehicle_id' => $added_vehicle !== 0 ? 'required' : 'nullable',
            'recurring_type' => $recurring !== 0 ? 'required' : 'nullable',
            'recurring_trips' => $recurring !== 0 ? 'required|numeric|max:10' : 'nullable',
        ], $customMessages);
        // $cityErrorMessage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('city_not_in_record')->first();

        $fromSpot = $request->input('from');
        $from = explode(',', $fromSpot)[0];

        $toSpot = $request->input('to');
        $to = explode(',', $toSpot)[0];
        $from = trim($from);
        $to = trim($to);
        $cityValidator = Validator::make([
            'from' => $from,
            'to' => $to,
        ], [
            'from' => 'required|exists:cities,name',
            'to' => 'required|exists:cities,name',
        ], [
            'from.exists' => $cityErrorMessage->city_not_in_record,
            'to.exists' => $cityErrorMessage->city_not_in_record,
        ]);
        if ($cityValidator->fails()) {
            return back()->withErrors($cityValidator)->withInput(); // Pass errors and old input back to the view
        }

        $nowDate = date('Y-m-d');
        $getRideCount = RideDetail::whereRaw('LOWER(`departure`) LIKE ? ', ['%' . $request->from . '%'])->where('date', $nowDate)->where('default_ride', '1')->whereHas('ride', function ($q) use ($nowDate, $user_id) {
            $q->where('date', $nowDate)->where('added_by', $user_id);
        })->count();

        $getRideCount = isset($getRideCount) ? $getRideCount : 0;

        $locationBeforeComma = explode(',', $request->from);

        $getFromState = City::with('state:id,abrv,ride_limit')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ', ['%' . $locationBeforeComma[0] . '%'])->first();


        if (isset($getFromState) && !empty($getFromState)) {
            if (isset($getFromState->state->ride_limit) && $getRideCount >= $getFromState->state->ride_limit) {
                return back()->with('message', $message->not_allowed_post_ride_state_wise_message)->withInput();
            }
        }

        // Add custom logic to check at least one checkbox is selected
        if (!$request->has('skip_vehicle') && !$request->has('add_vehicle') && !$request->has('added_vehicle')) {
            $validator->after(function ($validator) {
                $validator->errors()->add('vehicle_selection', 'You must select at least one vehicle option.');
            });
        }

        if ($validator->fails()) {
            // Check if there are validation errors for the 'uploaded' attribute
            $hasRequiredError = $validator->errors()->has('image') && $validator->errors()->first('image') === 'The image is not uploaded yet';
            // If there are other validation errors or the 'image' error is not present, return back with errors and the uploaded image path
            if (!$hasRequiredError || $validator->errors()->count() > 1) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('uploaded_image', $filename ?? null);
            }
        }
        // Check if any existing ride has the same date and time
        foreach ($rides as $existingRide) {
            if (
                $existingRide->date == Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d') &&
                $existingRide->time == $request->time . ':00'
            ) {
                $oldInput = $request->all();
                return back()->with('error', $message->ride_schedule_message)->with('heading', 'Ride already schedule')->withInput($oldInput)->with('uploaded_image', $filename ?? null);
            }
        }

        // dd( $request->time);
        // Parse the date and time once and store them for reuse

        // Execute the query with the formatted date and time
        // $rides = Ride::where('added_by', $user_id)
        //     ->whereDate('date', '<=', $formattedDate)
        //     ->whereTime('time', '<=', $formattedTime)
        //     ->whereTime('destination_reached_time', '>=', $formattedTime)
        //     ->whereDate('destination_reached_date', '>=', $formattedDate)
        //     ->first();
        $formattedDate = Carbon::parse($request->date)->format('Y-m-d');
        $formattedTime = Carbon::createFromFormat('H:i', $request->time)->format('H:i:s');

        //Second - Get distance and duration from Google Maps API
        $duration = 0;
        $distance = 0;
        // Use original from/to values - getDataFromGoogleApi will properly encode them
        $from = $request->from;
        $to = $request->to;
        $googleApiData = $this->getDataFromGoogleApi($from, $to);
        if (isset($googleApiData) && !empty($googleApiData)) {
            $duration = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;

            $distance = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
        }

        if ($distance != 0) {
            $distance = round(($distance / 1000), 2);
        }
        
        Log::info('Distance calculation completed for post ride', [
            'from' => $from,
            'to' => $to,
            'distance_km' => $distance,
            'duration_seconds' => $duration,
            'distance_meters' => $distance * 1000
        ]);
        
        // Cost-sharing cap validation: Error-Triggering Cap $0.72/km
        if ($distance > 0 && isset($request->price) && $request->price > 0) {
            $pricePerKm = $request->price / $distance;
            
            Log::info('Price per kilometer calculation (PostRideStore)', [
                'price' => $request->price,
                'distance_km' => $distance,
                'price_per_km' => round($pricePerKm, 4),
                'error_cap' => 0.72,
                'warning_cap' => 0.66
            ]);
            
            // Error-Triggering Cap: $0.72 per km - BLOCK if exceeded
            if ($pricePerKm > 0.72) {
                Log::warning('Price exceeds error-triggering cap (PostRideStore)', [
                    'price_per_km' => round($pricePerKm, 4),
                    'cap' => 0.72
                ]);
                
                return back()->with('error', 'The price per kilometer ($' . number_format($pricePerKm, 2) . '/km) exceeds the maximum allowed for cost-sharing rides ($0.72/km). Please adjust your price.')
                    ->with('heading', 'Price Too High')
                    ->withInput();
            }
            
            // Soft Warning Cap: $0.66 per km - WARN but ALLOW
            if ($pricePerKm > 0.66) {
                Log::info('Price exceeds soft warning cap but within error cap (PostRideStore)', [
                    'price_per_km' => round($pricePerKm, 4),
                    'warning_cap' => 0.66
                ]);
                
                // Store warning in session - will be shown to user via modal/popup
                session()->flash('price_warning', [
                    'message' => 'Your price per kilometer ($' . number_format($pricePerKm, 2) . '/km) is above the recommended cost-sharing rate ($0.66/km) but within the allowed maximum ($0.72/km).',
                    'price_per_km' => round($pricePerKm, 2)
                ]);
            }
        }

        if (isset($adminSetting)) {

            if (isset($request->date) && isset($request->time)) {
                $rideDateTime = Carbon::parse("$request->date $request->time");
                $apiTime = 0;
                if ($duration != 0) {
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

        if (isset($rides) && !empty($rides)) {
            $oldInput = $request->all();
            return back()->with('error', $message->overlap_ride_message ?? 'this ride overlaps with an existing ride you already have')->with('heading', 'Ride already schedule')->withInput($oldInput)->with('uploaded_image', $filename ?? null);
        }
        $rideDateTime = Carbon::parse($formattedDate . ' ' . $formattedTime);

        if ($rideDateTime->lte(Carbon::now()->addMinutes($adminSetting->ride_post_dead_time ?? 0))) {
            return redirect()->back()
                ->with('error', $message->ride_dead_time_text ?? 'The ride time you selected is too close. Please select a time that is more than 15 minutes in the future')
                ->withInput();
        }
        $max_back_seats = $request->filled('max_back_seats') ? $request->max_back_seats : 0;
        $accept_more_luggage = $request->filled('accept_more_luggage') ? $request->accept_more_luggage : 0;
        $open_customized = $request->filled('open_customized') ? $request->open_customized : 0;

        // Join the selected checkboxes with semicolons.
        $features = implode('=', $request->input('features', []));

        if ($skip_vehicle !== 0) {
            $make = '';
            $model = '';
            $vehicle_type = '';
            $year = '';
            $color = '';
            $license_no = '';
            $car_type = '';
        }

        if ($add_vehicle !== 0) {
            $make = $request->make;
            $model = $request->model;
            $vehicle_type = $request->vehicle_type;
            $year = $request->year;
            $color = $request->color;
            $license_no = $request->license_no;
            $car_type = $request->car_type;

            $vehicle = Vehicle::create([
                'user_id' => auth()->user()->id,
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

        if ($added_vehicle !== 0) {
            $vehicle = Vehicle::whereId($request->vehicle_id)->first();
            if ($vehicle) {
                $make = $vehicle->make;
                $model = $vehicle->model;
                $vehicle_type = $vehicle->type;
                $year = $vehicle->year;
                $color = $vehicle->color;
                $license_no = $vehicle->liscense_no;
                $car_type = $vehicle->car_type;
                $vehicle_id = $vehicle->id;
                if ($vehicle->remove_image === '0') {
                    $imageName = basename($vehicle->image);
                    $filename = $imageName;
                } else {
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
            }
        }

        if ($recurring == 0) {
            $recurring_type = '';
            $recurring_trips = '';
        } else {
            $recurring_type = $request->recurring_type;
            $recurring_trips = $request->recurring_trips;
        }


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
            'car_image' => $filename,
            'car_image_original' => $filename,
            'smoke' => $request->smoke,
            'animal_friendly' => $request->animal_friendly,
            'features' => $features,
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
            'added_on' => now(),
        ]);

        //Add Seat Detail
        for ($i = 1; $i <= $initialRide->seats; $i++) {
            $seatDetail = new SeatDetail;
            $seatDetail->ride_id = $initialRide->id;
            $seatDetail->seat_number = $i;
            $seatDetail->status = 'pending';
            $seatDetail->save();
        }

        //Add Ride Detail

        $rideDetail = new RideDetail();
        $rideDetail->ride_id = $initialRide->id;
        $rideDetail->departure = $request->from;
        $rideDetail->destination = $request->to;
        $rideDetail->default_ride = 1;
        $rideDetail->total_distance = $distance;
        $rideDetail->total_duration = $duration;
        $rideDetail->price = $request->price;
        $rideDetail->time = $request->time;
        $rideDetail->date = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');

        if (isset($adminSetting)) {

            if (isset($initialRide->date) && isset($initialRide->time)) {
                $rideDateTime = Carbon::parse("$initialRide->date $initialRide->time");
                $apiTime = 0;
                if ($duration != 0) {
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

        if (isset($request->from_spot) && !empty($request->from_spot)) {
            foreach ($request->from_spot as $key => $from_spot) {
                if (
                    empty($request->from_spot[$key]) ||
                    empty($request->to_spot[$key])
                ) {
                    continue;
                }

                $duration = 0;
                $distance = 0;

                $fromArray = explode(',', $request->from_spot[$key]);
                $toArray = explode(',', $request->to_spot[$key]);
                $googleApiData = $this->getDataFromGoogleApi($request->from_spot[$key], $request->to_spot[$key]);
                if (isset($googleApiData) && !empty($googleApiData)) {
                    $duration = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;

                    $distance = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
                }

                if ($distance != 0) {
                    $distance = round(($distance / 1000), 2);
                }

                $rideDetail = new RideDetail();
                $rideDetail->ride_id = $initialRide->id;
                $rideDetail->departure = $request->from_spot[$key];
                $rideDetail->destination = $request->to_spot[$key];
                $rideDetail->default_ride = 0;
                $rideDetail->total_distance = $distance;
                $rideDetail->total_duration = $duration;
                $rideDetail->price = $request->price_spot[$key];
                $rideDetail->time = $request->time;
                $rideDetail->date = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');

                if (isset($adminSetting)) {

                    if (isset($initialRide->date) && isset($initialRide->time)) {
                        $rideDateTime = Carbon::parse("$initialRide->date $initialRide->time");

                        $apiTime = 0;
                        if ($duration != 0) {
                            $apiTime = round(($duration / 3600), 2);
                        }

                        // $rideDateTime->addHours($adminSetting->destination_hours);
                        // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                        $totalHours = $duration / 3600;  // e.g., 109800 seconds → 30.5 hours
                        $fullHours = floor($totalHours);  // 30 hours
                        $minutes = round(($totalHours - $fullHours) * 60);  // 30 minutes
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


        $recurring_id = $initialRide->id;

        // Check if the ride is recurring
        if ($recurring !== 0) {
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
                    'time' => $request->time,
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
                    'car_image' => $filename,
                    'car_image_original' => $filename,
                    'smoke' => $request->smoke,
                    'animal_friendly' => $request->animal_friendly,
                    'features' => $features,
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
                    'added_on' => now(),
                ]);


                for ($j = 1; $j <= $initialRide->seats; $j++) {
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
                    $rideDetail->destination_time = $initialRide->destination_time;
                    $rideDetail->destination_date = $nextDestinationReachedDate;
                    $rideDetail->completed_time = $initialRide->completed_time;
                    $rideDetail->completed_date = $nextCompletedDate;
                    $rideDetail->save();
                }
            }
        }
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
                'posted_by' => $user->id,
                'message' =>  'Your Pink and Extra-Care ride is now live on ProximaRide',
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);

            $body = $notification->message;
            $fcmService = new FCMService();

            $fcmToken = $user->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }
        } elseif (in_array('1', $features)) {
            // Only Pink Ride
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user->id,
                'message' =>  'Your Pink Ride is now live on ProximaRide',
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);

            $body = $notification->message;
            $fcmService = new FCMService();

            $fcmToken = $user->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }
        } elseif (in_array('2', $features)) {
            // Only Extra-Care Ride
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user->id,
                'message' =>  'Your Extra-Care Ride is now live on ProximaRide',
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);

            $body = $notification->message;
            $fcmService = new FCMService();

            $fcmToken = $user->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }
        } else {
            // Regular ride (existing email)
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user->id,
                'message' =>  'Your ride is now live on ProximaRide',
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);

            $body = $notification->message;
            $fcmService = new FCMService();

            $fcmToken = $user->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }
        }

        // return redirect()->route('post_ride', ['lang' => $selectedLanguage->abbreviation])->with(['message' => $message->ride_post_message, 'id' => $initialRide->id]);
        return redirect()->route('my_rides', ['lang' => $selectedLanguage->abbreviation])->with(['message' => $message->ride_post_message, 'id' => $initialRide->id])->withInput();
    }


    public function addNewSpots(Request $request)
    {
        // dd($request->all());
        $fromSpot = $request->input('from_spot');
        $from_city = explode(',', $fromSpot)[0];
        $from_city = trim($from_city);

        $toSpot = $request->input('to_spot');
        $to_city = explode(',', $toSpot)[0];
        $to_city = trim($to_city);
        $selectedLanguage = session('selectedLanguage');
        $postRidePage = null;
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }
        $cityErrorMessage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('city_not_in_record')->first();


        $validator = Validator::make($request->all(), [
            'from_spot' => 'required|exists:cities,name',
            'to_spot' => 'required|exists:cities,name',
            'price' => 'required',

        ], [
            'from_spot.exists' => $cityErrorMessage->city_not_in_record,
            'to_spot.exists' => $cityErrorMessage->city_not_in_record,
        ]);

        if ((!$from_city || !DB::table('cities')->where('name', $from_city)->exists()) || (!$to_city || !DB::table('cities')->where('name', $to_city)->exists()) || is_null($request->price)) {
            // return response()->json([
            //     'status' => 'error',
            //     'errors' => $validator->errors(),
            // ]);
            if (is_null($request->price)) {

                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'price' => [__('validation.required', ['attribute' => 'price'])]
                    ],
                ]);
            }
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'from_spot' => [$cityErrorMessage->city_not_in_record],
                    'to_spot' =>  [$cityErrorMessage->city_not_in_record],
                ],
            ]);
        }




        $spotHtml = view('post_ride_partial.add_more_from_to_partial', ['postRideSubDetailPage' => $postRideSubDetailPage, 'index' => $request->index, 'postRidePage' => $postRidePage, 'ride_detail' => null, 'type' => 'create'])->render();
        return response()->json(['spotHtml' => $spotHtml]);
    }

    public function deleteSpots(Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        $message = null;
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_has_booking_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_has_booking_message')->first();
            }
        }

        $checkBooking = Booking::where('ride_detail_id', $request->rideDetailId)->first();
        if (isset($checkBooking) && !empty($checkBooking)) {
            return response()->json(['status' => 'error', 'message' => $message->ride_has_booking_message ?? "ride has booking"]);
        }

        RideDetail::where('id', $request->rideDetailId)->delete();

        return response()->json(['status' => 'success']);
    }


    public function getDataFromGoogleApi($from, $to)
    {
        $apiKey = env('GOOGLE_API_KEY');
        $ch = curl_init();

        // URL encode the addresses to properly handle spaces and special characters
        // This ensures city names like "Montreal, QC" and "Ottawa, ON" work correctly
        $fromEncoded = urlencode($from);
        $toEncoded = urlencode($to);

        $apiUrl = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $fromEncoded . "&destinations=" . $toEncoded . "&units=imperial&key=" . $apiKey . "";
        
        Log::info('Google Maps API Request', [
            'from' => $from,
            'to' => $to,
            'from_encoded' => $fromEncoded,
            'to_encoded' => $toEncoded,
            'url' => str_replace($apiKey, 'HIDDEN_KEY', $apiUrl)
        ]);

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('Google Maps API cURL Error: ' . curl_error($ch), [
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
            
            Log::info('Google Maps API Success', [
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
            Log::warning('Google Maps API returned non-OK status', [
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
