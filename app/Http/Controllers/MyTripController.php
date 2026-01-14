<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Booking;
use App\Models\Language;
use App\Models\Notification;
use App\Models\ReviewSetting;
use App\Http\Controllers\Controller;
use App\Models\ChatsPageSettingDetail;
use App\Models\FeaturesSettingDetail;
use App\Models\TripsPageSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\MyReviewSettingDetail;
use App\Models\PostRidePageSettingDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\Ride;
use App\Models\SuccessMessagesSettingDetail;

class MyTripController extends Controller
{
    public function CurrentTrips($lang = null){
        $user_id = auth()->user()->id;
        
        // Setup language first (needed for redirect)
        $languages = Language::all();
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        }
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        
        // Check if user has posted any rides (as a driver)
        $hasPostedRides = Ride::where('added_by', $user_id)->exists();
        
        // If user has posted rides, redirect to "Driver Rides" (my_rides)
        if ($hasPostedRides) {
            return redirect()->route('my_rides', ['lang' => $selectedLanguage->abbreviation ?? 'en']);
        }
        
        // Continue with passenger trips if user hasn't posted rides
        $bookings = Booking::where('user_id', $user_id)
            ->where('bookings.status', '!=', '3')
            ->where('bookings.status', '!=', '4')
            ->join('rides', 'bookings.ride_id', '=', 'rides.id')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '>', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '>=', now()->toTimeString());
                        });
                });
            })->select('bookings.*', 'rides.id', 'rides.date', 'rides.time', 'rides.completed_date', 'rides.completed_time') 
            ->orderBy('rides.date', 'asc')
            ->orderBy('rides.time', 'asc')
            ->paginate(6);

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
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
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
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('popup_close_btn_text','confirm_cancel_noshow','cancel_noshow_take_me_back','cancel_noshow_are_you_sure','no_show_driver_button','revert_arbitration_button')->first();

            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
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
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('popup_close_btn_text','confirm_cancel_noshow','cancel_noshow_take_me_back','cancel_noshow_are_you_sure','no_show_driver_button','revert_arbitration_button')->first();

            }
        }
        $ratings = Rating::all();

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

        }


        return view('my_trips',['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'reviewSetting' => $reviewSetting,'messages' => $messages,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'bookings' => $bookings,'ratings' => $ratings,'postRidePage' => $postRidePage,'rideDetailPage' => $rideDetailPage,'tripsPage' => $tripsPage,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function PastTrips($lang = null){
        $bookings = Booking::where('user_id', auth()->user()->id)
            ->where('bookings.status', '!=', '4')
            ->where('bookings.status', '!=', '3')
            ->join('rides', 'bookings.ride_id', '=', 'rides.id')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<', now()->toTimeString());
                        });
                });
            })->select('bookings.*', 'rides.id', 'rides.date', 'rides.time', 'rides.completed_date', 'rides.completed_time')
            ->orderBy(Ride::select('date')
            ->whereColumn('rides.id', 'bookings.ride_id')
            ->limit(1), 'asc')
            ->orderBy(Ride::select('time')
            ->whereColumn('rides.id', 'bookings.ride_id')
            ->limit(1), 'asc')
            ->orderBy('ride_id', 'desc')
            ->paginate(6);

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
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
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
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
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
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }
        $ratings = Rating::all();
        $setting = ReviewSetting::first();

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

        }

        return view('past_trips',['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'bookings' => $bookings,'rideDetailPage' => $rideDetailPage,'tripsPage' => $tripsPage,'ratings' => $ratings,'setting' => $setting,'postRidePage' => $postRidePage,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function CancelledTrips($lang = null){
        $bookings = Booking::join('rides', 'bookings.ride_id', '=', 'rides.id')
            ->where('bookings.user_id', auth()->user()->id)
            ->where('bookings.status', 4)
            ->select('bookings.*', 'rides.seats as ride_seats', 'bookings.seats as booking_seats')
            ->orderBy('rides.date', 'asc')
            ->orderBy('rides.time', 'asc')
            ->paginate(6);

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $rideDetailPage = null;
        $tripsPage = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
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
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
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
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }

        $ratings = Rating::all();
        $setting = ReviewSetting::first();
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

        }

        return view('cancelled_trips',['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'bookings' => $bookings,'postRidePage' => $postRidePage,'rideDetailPage' => $rideDetailPage,'tripsPage' => $tripsPage,'ratings' => $ratings,'setting' => $setting,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }
}
