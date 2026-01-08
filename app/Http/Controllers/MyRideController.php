<?php

namespace App\Http\Controllers;

use Stripe\Refund;
use Stripe\Stripe;
use App\Models\Ride;
use App\Models\User;
use App\Models\Admin;
use App\Models\Rating;
use App\Models\Booking;
use Twilio\Rest\Client;
use App\Models\Language;
use App\Models\PhoneNumber;
use App\Models\SiteSetting;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ReviewSetting;
use App\Mail\CancelPassengerMail;
use App\Models\CancelRideSetting;
use App\Mail\DriverCancelRideMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\FeaturesSettingDetail;
use App\Mail\CancelPassengerAdminMail;
use App\Mail\DriverCancelRideWithReasonMail;
use App\Mail\SecuredCashDriverMail;
use App\Mail\SecuredCashPassengerMail;
use App\Models\BookingPageSettingDetail;
use App\Models\CancellationHistory;
use App\Models\ChatsPageSettingDetail;
use App\Models\CoffeeWallet;
use App\Models\FCMToken;
use App\Models\TripsPageSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\Message;
use App\Models\PostRidePageSettingDetail;
use App\Models\MyPassengerSettingDetail;
use App\Models\MyReviewSettingDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\Step1PageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Models\SeatDetail;
use Carbon\Carbon;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\TopUpBalance;
use App\Services\FCMService;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class MyRideController extends Controller
{
    public function CurrentRides($lang = null)
    {


        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
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
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
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
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->lang_id)->first();
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }

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

        $rides = Ride::where('added_by', auth()->user()->id)
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '>', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '>=', now()->toTimeString());
                        });
                });
            })
            ->with(['rideDetail' => function ($q) {
                $q->where('default_ride', '1');
            }])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->paginate(6);



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

        return view('my_rides', ['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'rides' => $rides, 'reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 'ProfileSetting' => $ProfileSetting, 'postRidePage' => $postRidePage, 'notifications' => $notifications, 'rideDetailPage' => $rideDetailPage, 'tripsPage' => $tripsPage, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
    }

    public function PastRides($lang = null)
    {
        $pastRides = Ride::where('added_by', auth()->user()->id)
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
            ->with(['rideDetail' => function ($q) {
                $q->where('default_ride', '1');
            }])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
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

        return view('past_rides', ['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'pastRides' => $pastRides, 'reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 'ProfileSetting' => $ProfileSetting, 'rideDetailPage' => $rideDetailPage, 'tripsPage' => $tripsPage, 'postRidePage' => $postRidePage, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
    }

    public function CancelledRides($lang = null)
    {
        $cancelledRides = Ride::where('added_by', auth()->user()->id)
            ->where('status', 2)
            ->with(['rideDetail' => function ($q) {
                $q->where('default_ride', '1');
            }])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
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

        return view('cancelled_rides', ['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'cancelledRides' => $cancelledRides, 'reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 'ProfileSetting' => $ProfileSetting, 'rideDetailPage' => $rideDetailPage, 'tripsPage' => $tripsPage, 'postRidePage' => $postRidePage, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
    }

    public function MyRideDetail(Request $request, $lang = null)
    {
        $siteSetting = SiteSetting::first();

        $from = isset($request->departure) ? $request->departure : "";
        $to = isset($request->destination) ? $request->destination : "";
        $rideId = isset($request->id) ? $request->id : 0;

        $ride = Ride::where('id', $request->id)->with(['rideDetail' => function ($q) use ($from, $to, $rideId) {
            $q->where('ride_id', $rideId);
        }])->first();

        if (!isset($ride) && empty($ride)) {
            $lang = $lang ?? "en";
            return redirect(route('home', ['lang' => $lang]));
        }

        $setting = ReviewSetting::first();
        $cancelSetting = CancelRideSetting::first();
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
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button', 'too_many_secured_cash_attempt_message')->first();
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
                }

                $ride->luggage = FeaturesSettingDetail::whereFeaturesSettingId($ride->luggage)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $ride->payment_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->payment_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->booking_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->booking_type = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_type)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->animal_friendly = FeaturesSettingDetail::whereFeaturesSettingId($ride->animal_friendly)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $featureIds = explode('=', $ride->features);
                // Fetch data for each feature ID and concatenate with '='
                $featureNames = collect($featureIds)->map(function ($id) use ($selectedLanguage) {
                    return FeaturesSettingDetail::whereFeaturesSettingId($id)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                })->filter()->implode('=');
                $ride->features = $featureNames;
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button','too_many_secured_cash_attempt_message')->first();
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
                }

                $ride->luggage = FeaturesSettingDetail::whereFeaturesSettingId($ride->luggage)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->payment_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->payment_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->booking_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->booking_type = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_type)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->animal_friendly = FeaturesSettingDetail::whereFeaturesSettingId($ride->animal_friendly)
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
            // dd($notifications);

        }


        $rideDetailPage = null;
        $tripsPage = null;
        $messages = null;
        if ($selectedLanguage) {
            $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('popup_close_btn_text', 'popup_submit_btn_text')->first();
        }

        // $ride_cancelled=CancellationHistory::where('ride_id',$ride->id)->where('user_id',auth()->user()->id)->where('type','driver')->first();
        $ratings = Rating::all();
        $ride_cancelled=false;
        // $ride_booking=Booking::where('ride_id',$ride->id)->where('user_id',auth()->user()->id)->select('status')->first();
        $completed_date_time = Carbon::parse($ride->completed_date . ' ' . $ride->completed_time);
        if(($completed_date_time < Carbon::now() || $ride->status =='2'|| $ride->status =='3')){
            $ride_cancelled=true;
        }
        return view('my_ride_detail', ['notificationPage'=>$notificationPage, 'siteSetting'=>$siteSetting ,'successMessage'=>$successMessage,'ride_cancelled' => $ride_cancelled,'ride' => $ride, 'setting' => $setting, 'ratings' => $ratings, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'postRidePage' => $postRidePage, 'cancelSetting' => $cancelSetting, 'rideDetailPage' => $rideDetailPage, 'tripsPage' => $tripsPage, 'messages' => $messages]);
    }

    public function enterCode(Request $request)
    {
        $booking = Booking::where('id', $request->booking_id)->first();

        $siteSetting = SiteSetting::first();

        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('incorrect_code_message', 'general_error_message', 'too_many_secured_cash_attempt_message', 'secured_cash_success_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('incorrect_code_message', 'general_error_message', 'too_many_secured_cash_attempt_message', 'secured_cash_success_message')->first();
        }

        if ($booking) {
            $request->validate([
                'code' => 'required|max:4',
            ]);

            $messageData = "";
            if ($request->code === $booking->secured_cash_code) {
                $booking->update([
                    'secured_cash' => null,
                    'secured_cash_code' => null,
                ]);

                $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
                foreach ($transactions as $transaction) {
                    if ($transaction) {
                        $refundId = "";
                        if ($transaction->pay_by_account == 0) {
                            if ($transaction->paypal_id) {
                                $paypal = new PayPalClient;
                                $paypal->setApiCredentials(config('paypal'));
                                $token = $paypal->getAccessToken();
                                $paypal->setAccessToken($token);
                                $response = $paypal->refundCapturedPayment(
                                    $transaction->paypal_id,
                                    'Invoice-' . $transaction->paypal_id,
                                    $transaction->price - $transaction->booking_fee,
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
                                        'amount' => ($transaction->price - $transaction->booking_fee) * 100, // Refund amount in cents
                                    ]);
                                    $refundId = $refund->id;
                                } catch (\Stripe\Exception\ApiErrorException $e) {
                                    // Handle error
                                    Log::info($e->getMessage());
                                    // return $this->apiErrorResponse($e->getMessage(), 200);
                                }
                            }
                        } else {
                            $topUpBalance = TopUpBalance::create([
                                'booking_id' => $transaction->booking_id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transaction->price - $transaction->booking_fee,
                                'added_date' => date('Y-m-d'),
                            ]);
                        }

                        $newTransaction = Transaction::create([
                            'booking_id' => $transaction->booking_id,
                            'ride_id' => $booking->ride_id,
                            'parent_id' => $transaction->id,
                            'type' => '3',
                            'price' => $transaction->price,
                            'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                            'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                        ]);
                    }
                }
                
                $driverPhoneNumber = PhoneNumber::where('user_id', $booking->ride->driver->id)
                    ->where('default', '1')
                    ->first();
                $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $booking->ride->driver->phone;

                $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->passenger->id)
                    ->where('default', '1')
                    ->first();
                $passengerPhoneToUse = $passengerPhoneNumber ? $passengerPhoneNumber->phone : $booking->passenger->phone;
                $passengerData = [
                    'passenger_first_name' => $booking->passenger->first_name,
                    'seats_booked' => $booking->seats,
                    'booking_price' => $booking->price,
                    'from' => $booking->departure,
                    'to' => $booking->destination,
                    'on' => $booking->ride->date,
                    'at' => $booking->ride->time,
                    'driver_first_name' => $booking->ride->driver->first_name,
                    'driver_phone' => $driverPhoneToUse,
                    'passenger_email' => $booking->ride->driver->email,
                ];
                
                if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {
                Mail::to($booking->passenger->email)->send(new SecuredCashPassengerMail($passengerData));
                }

                // sms passneger 
                $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->passenger->id)
                        ->where('verified', '1')
                        ->where('default', '1')
                        ->first();

                    if (!$passengerPhoneNumber) {
                        $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->passenger->id)
                            ->where('verified', '1')
                            ->first();
                    }

                    if ($passengerPhoneNumber && env('APP_ENV') != 'local' && isset($booking->passenger->sms_notification) && $booking->passenger->sms_notification == 1) {
                        $sid = env('TWILIO_ACCOUNT_SID');
                        $token = env('TWILIO_AUTH_TOKEN');
                        $from = env('TWILIO_PHONE_NUMBER');

                        $twilio = new Client($sid, $token);
                        $to = $passengerPhoneNumber->phone;

                        
                        $title = "";
                        $currentHour = date('H');
                        if ($currentHour >= 0 && $currentHour < 12) {
                            $title = "Good morning " . $booking->passenger->first_name . ",";
                        } elseif ($currentHour >= 12 && $currentHour < 17) {
                            $title = "Good afternoon " . $booking->passenger->first_name . ",";
                        } else {
                            $title = "Good evening " . $booking->passenger->first_name . ",";
                        }

                        // Format phone number (123)456-7890
                        $driverPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1)$2-$3", $driverPhoneToUse);
                        
                        // Format date and time
                        $departureTime = date('H:i:s', strtotime($booking->ride->time));
                        $departureDate = date('d F, Y', strtotime($booking->ride->date));
                        
                        // Convert seat number to words if needed
                        $seatText = $booking->seats == 1 ? 'seat' : 'seats';

                        $seats = $booking->seats;
                        $pricePerSeat = $booking->price;
                        $bookingCredit = $booking->booking_credit;

                        // Calculate total amount due
                        $totalAmount = ($seats * $pricePerSeat) + $bookingCredit;
                        $formattedAmountForPassengerToPay = number_format($totalAmount, 2);

                        $message = $title . "\n" . "From ProximaRide: Secured-cash payment code was successful. Your booking price has been refunded to you. Now, please pay your driver in cash. Pay the booking price only, not the booking fee.\n" .
                                "Ride from " . $booking->departure . " to " . $booking->destination . 
                                " on " . $departureDate . " at " . $departureTime . "\n" .
                                "Driver name is (" . $booking->ride->driver->first_name . "). Phone " . $driverPhone . "\n" .
                                "You booked: " . $booking->seats . " " . $seatText . "\n" .
                                "Amount to pay to the driver: $" . $formattedAmountForPassengerToPay;

                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                        } catch (\Exception $e) {
                            Log::error("Cannot send secured cash success SMS to $to. Error: " . $e->getMessage());
                        }
                    }


                // Send email to driver
                $driverData = [
                    'driver_first_name' => $booking->ride->driver->first_name,
                    'seats_booked' => $booking->seats,
                    'booking_price' => $booking->price,
                    'from' => $booking->departure,
                    'to' => $booking->destination,
                    'on' => $booking->ride->date,
                    'at' => $booking->ride->time,
                    'passenger_first_name' => $booking->passenger->first_name,
                    'passenger_phone' => $passengerPhoneToUse,
                    'passenger_email' => $booking->passenger->email,
                ];
                if (isset($booking->ride->driver->email_notification) && $booking->ride->driver->email_notification == 1) {
                Mail::to($booking->ride->driver->email)->send(new SecuredCashDriverMail($driverData));
                }

                // driver sms
                $driverPhoneNumber = PhoneNumber::where('user_id', $booking->ride->driver->id)
                    ->where('verified', '1')
                    ->where('default', '1')
                    ->first();

                if (!$driverPhoneNumber) {
                    $driverPhoneNumber = PhoneNumber::where('user_id', $booking->ride->driver->id)
                        ->where('verified', '1')
                        ->first();
                }

                if ($driverPhoneNumber && env('APP_ENV') != 'local' && isset($booking->ride->driver->sms_notification) && $booking->ride->driver->sms_notification == 1) {
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');

                    $twilio = new Client($sid, $token);
                    $to = $driverPhoneNumber->phone;

                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning " . $booking->ride->driver->first_name . ",";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $booking->ride->driver->first_name . ",";
                    } else {
                        $title = "Good evening " . $booking->ride->driver->first_name . ",";
                    }

                    // Format phone number (123)456-7890
                    $passengerPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1)$2-$3", $passengerPhoneToUse);
                    
                    // Format date and time
                    $departureTime = date('H:i:s', strtotime($booking->ride->time));
                    $departureDate = date('d F, Y', strtotime($booking->ride->date));
                    
                    // Convert seat number to words if needed
                    $seatText = $booking->seats == 1 ? 'seat' : 'seats';

                    $seats = $booking->seats;
                    $pricePerSeat = $booking->price;
                    $bookingCredit = $booking->booking_credit;

                    // Calculate total amount due
                    $totalAmount = ($seats * $pricePerSeat) + $bookingCredit;
                    $formattedAmount = number_format($totalAmount, 2);

                    $message = $title . "\n" . "From ProximaRide: Secured-cash payment code was successful. Now, take your payment from the passenger in cash.\n" .
                            "Passenger name is (" . $booking->passenger->first_name . "). Phone " . $passengerPhone . "\n" .
                            "Ride from " . $booking->departure . " to " . $booking->destination . 
                            " on " . $departureDate . " at " . $departureTime . "\n" .
                            "Seats booked: " . $booking->seats . "\n" .
                            "Amount due to you: $" . $formattedAmount;

                    try {
                        $res = $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $message,
                            ]
                        );
                    } catch (\Exception $e) {
                        Log::error("Cannot send secured cash success SMS to driver $to. Error: " . $e->getMessage());
                    }
                }

                $notification = Notification::create([
                    'ride_id' => $booking->ride_id,
                    'posted_by' => $booking->user_id,
                    'message' =>  'Secured-cash payment code successful',
                    'status' => 'upcoming',
                    'notification_type' => 'upcoming',
                ]);
        
                $body = $notification->message;
                $fcmService = new FCMService();

                $fcmToken = $booking->ride->driver->mobile_fcm_token;
                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $body);
                }
        
                $fcm_tokens = FCMToken::where('user_id', $booking->ride->added_by)->get();
        
                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                    }
                }
                
                $notification = Notification::create([
                    'type' => 2,
                    'ride_id' => $booking->ride_id,
                    'posted_to' => $booking->id,
                    'posted_by' => $booking->ride->added_by,
                    'message' =>  'Secured-cash payment code successful',
                    'status' => 'upcoming',
                    'notification_type' => 'upcoming',
                ]);
        
                $body = $notification->message;
                $fcmService = new FCMService();

                $fcmToken = $booking->passenger->mobile_fcm_token;
                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $body);
                }
        
                $fcm_tokens = FCMToken::where('user_id', $booking->user_id)->get();
        
                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                    }
                }
                
                return redirect()->route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id])->with(['success' => $message->secured_cash_success_message ?? "Code submitted and the booking price has been released back to the passenger. Now, get your payment in cash from them"]);
            } else {

                if($booking->secured_cash_attempt_count < $siteSetting->secured_cash_attempt){
                    $count = isset($booking->secured_cash_attempt_count) ? $booking->secured_cash_attempt_count : 0;
                    $count = $count + 1;
                    $booking->secured_cash_attempt_count = $count;
                    $booking->save();
                    $messageData = $message->incorrect_code_message;
                }else{
                   $messageData = $message->too_many_secured_cash_attempt_message;
                }
            }
            return redirect()->route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id])->with(['message' => $messageData, 'secured_cash_attempt_count' => $booking->secured_cash_attempt_count]);
        }
        return $message->general_error_message ?? 'Booking not found';
    }

    public function MyPassengers(Request $request, $lang = null)
    {

        $from = $request->from;
        $to = $request->to;
        $rideId = $request->id;
        $ride = Ride::where('id', $request->id)
            ->with(['rideDetail' => function ($q) use ($rideId, $from, $to) {
                $q->where('departure', 'like', '%' . $from . '%')
                    ->where('destination', 'like', '%' . $to . '%')
                    ->where('ride_id', $rideId);
            }])->first();
        $setting = ReviewSetting::first();
        $cancelSetting = CancelRideSetting::first();
        $languages = Language::all();
        $myPassengerPage = null;
        $messages = null;


        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $myPassengerPage = MyPassengerSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('popup_close_btn_text', 'confirm_cancel_noshow', 'cancel_noshow_take_me_back', 'cancel_noshow_are_you_sure')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $myPassengerPage = MyPassengerSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('popup_close_btn_text', 'confirm_cancel_noshow', 'cancel_noshow_take_me_back', 'cancel_noshow_are_you_sure')->first();
        }


        if ($ride) {
            $bookings = Booking::where('ride_id', $ride->id)->where('status', 1)
                ->whereHas('passenger', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->with(['passenger' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'dob', 'profile_image');
                }])
                ->with(['ride' => function ($query) {
                    $query->select('id', 'date', 'time');
                }])->get();

            if ($selectedLanguage) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }

            foreach ($bookings as $booking) {

                // Calculate age
                if ($booking->passenger->dob) {
                    $dob = Carbon::parse($booking->passenger->dob);
                    $booking->passenger->age = $dob->diffInYears(Carbon::now());
                } else {
                    $booking->passenger->age = null; // Handle case where dob is not set
                }

                if ($booking->passenger->gender) {
                    if ($booking->passenger->gender === 'male') {
                        $booking->passenger->gender_label = $genderLabel->male_option_label;
                    } elseif ($booking->passenger->gender === 'female') {
                        $booking->passenger->gender_label = $genderLabel->female_option_label;
                    } elseif ($booking->passenger->gender === 'prefer not to say') {
                        $booking->passenger->gender_label = $genderLabel->prefer_option_label;
                    }
                }
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

        return view('my_passengers', ['ride' => $ride, 'setting' => $setting, 'cancelSetting' => $cancelSetting, 'ratings' => $ratings, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'myPassengerPage' => $myPassengerPage, 'messages' => $messages]);
    }

    public function cancel($lang = null, $id)
    {
        $ride = Ride::where('id', $id)->first();
        $setting = SiteSetting::first();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $tripsPage = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
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
        return view('cancel_ride', ['ride' => $ride, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'setting' => $setting, 'tripsPage' => $tripsPage]);
    }

    public function updateCancelRide($id, Request $request)
    {
        $ride = Ride::where('id', $id)->first();
        $user_id = auth()->user()->id;
        $setting = SiteSetting::first();
        $monthsAgo = Carbon::now()->subMonths($setting->booking_cancel_duration)->setTimezone('UTC');;

        $cancellationCount = CancellationHistory::where('user_id', $user_id)
            ->where('created_at', '>=', $monthsAgo)
            ->where('type', 'driver')

            ->count();
            $messages = null;
            $selectedLanguageAbbreviation = session('selectedLanguage');
    
            if ($selectedLanguageAbbreviation) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguageAbbreviation)->first();
                if ($selectedLanguage) {
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_cancel_message')->first();
                    $limitExceed = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_cancellation_limit_exceed')->first();
                }
            } else {
                // Use the default language
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_cancel_message')->first();
                    $limitExceed = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_cancellation_limit_exceed')->first();
                }
                $selectedLanguageAbbreviation = $selectedLanguage->abbreviation;
            }
            // dd($limitExceed);
            if ($cancellationCount >= $setting->booking_cancel_limit) {
            return redirect()->back()->with(['failure' => $limitExceed->booking_cancellation_limit_exceed ?? "Booking cancellation limit exceeded"]);
                
                // return response()->json(['error' => true, 'message' => $limitExceed->booking_cancellation_limit_exceed ?? "Booking cancellation limit exceeded"]);
            }

            // Check if there are any active bookings
            $bookedSeats = $ride->bookings()
            ->where('status', '!=', '0') // Exclude pending bookings
            ->where('status', '!=', '3') // Exclude canceled bookings
            ->where('status', '!=', '4') // Exclude completed bookings
            ->sum('seats');
            if ($bookedSeats == 0) {
                $ride->update(['status' => '2']);
                
                // Add cancellation history
                CancellationHistory::create([
                    'ride_id' => $ride->id,
                    'user_id' => $ride->added_by,
                    'type' => 'driver',
                ]);
                
                $selectedLanguageAbbreviation = session('selectedLanguage');
                
                if (!$selectedLanguageAbbreviation) {
                    $defaultLanguage = Language::where('is_default', 1)->first();
                    $selectedLanguageAbbreviation = $defaultLanguage->abbreviation;
                }
                return response()->json(['success' => true, 'message' => 'Ride canceled successfully.']);
            }
      
        $request->validate([
            'message' => 'required',
            'reason' => 'required'
        ]);

        $bookings = Booking::where('ride_id', $id)
            ->where('status', '!=', '0') // Exclude pending bookings
            ->where('status', '!=', '3') // Exclude canceled bookings
            ->where('status', '!=', '4') // Exclude completed bookings
            ->get();

        foreach ($bookings as $booking) {
            $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
            foreach ($transactions as $transaction) {
                if ($transaction) {
                    $refundId = "";

                    $checkPrice = 0.0;
                    $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');
                    if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                        $getRefundEntryPrice = (float)$getRefundEntryPrice + (float)$transaction->booking_fee;
                    }
                    $checkPrice = (float)$transaction->price;

                    if (isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == $checkPrice) {
                        if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transaction->booking_fee,
                            ]);

                            $newTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $transaction->booking_fee,
                                'paypal_id' => NULL,
                                'stripe_id' => NULL
                            ]);
                        }
                    } else {
                        $transactionAmt = $checkPrice - $getRefundEntryPrice;

                        if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                            $transactionAmt = $transactionAmt - $transaction->booking_fee;
                        }

                        if ($transaction->pay_by_account == 0) {
                            if ($transaction->paypal_id) {
                                $paypal = new PayPalClient;
                                $paypal->setApiCredentials(config('paypal'));
                                $token = $paypal->getAccessToken();
                                $paypal->setAccessToken($token);
                                $response = $paypal->refundCapturedPayment(
                                    $transaction->paypal_id,
                                    'Invoice-' . $transaction->paypal_id,
                                    $transactionAmt,
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
                                        'amount' => $transactionAmt * 100, // Refund amount in cents
                                    ]);

                                    $refundId = $refund->id;
                                } catch (\Stripe\Exception\ApiErrorException $e) {
                                    // Handle error
                                    Log::info($e->getMessage());
                                    // return $this->apiErrorResponse($e->getMessage(), 200);
                                }
                            }
                        } else {
                            $topUpBalance = TopUpBalance::create([
                                'booking_id' => $transaction->booking_id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transactionAmt,
                                'added_date' => date('Y-m-d'),
                            ]);
                        }

                        if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transaction->booking_fee,
                            ]);
                        }

                        $newTransaction = Transaction::create([
                            'booking_id' => $transaction->booking_id,
                            'ride_id' => $booking->ride_id,
                            'parent_id' => $transaction->id,
                            'type' => '3',
                            'price' => $transactionAmt,
                            'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                            'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                        ]);
                    }
                }
            }

            $booking->update([
                'status' => '4',
            ]);

            CancellationHistory::create([
                'ride_id' => $booking->ride_id,
                'booking_id' => $booking->id,
                'user_id' => $ride->added_by,
            ]);

            $notification = Notification::create([
                'type' => 2,
                'ride_id' => $ride->id,
                'posted_to' => $booking->id,
                'posted_by' => $ride->added_by,
                'message' =>  'Your ride has been cancelled',
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            $fcmService = new FCMService();
            $fcm_tokens = FCMToken::where('user_id', $booking->user_id)->get();
            $body = $notification->message;

            $fcmToken = $booking->passenger->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }

            if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {
            $data = ['driver_name' => $ride->driver->first_name, 'passenger_name' => $booking->passenger->first_name, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time, 'seats' => $booking->seats, 'total_price' => $booking->fare, 'cancellation_reason' => $request->message];

            Mail::to($booking->passenger->email)->queue(new DriverCancelRideMail($data));
            Mail::to($booking->passenger->email)->queue(new DriverCancelRideWithReasonMail($data));
            }

            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                ->where('verified', '1')
                ->where('default', '1')
                ->first();

            if (!$phoneNumber) {
                $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->first();
            }

            if ($phoneNumber && env('APP_ENV') != 'local' && isset($booking->passenger->sms_notification) && $booking->passenger->sms_notification == 1)  {
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_PHONE_NUMBER');

                $twilio = new Client($sid, $token);
                $to = $phoneNumber->phone;

                $title = "";
                $currentHour = date('H');
                if ($currentHour >= 0 && $currentHour < 12) {
                    $title = "Good morning " . $booking->passenger->first_name . ",";
                } elseif ($currentHour >= 12 && $currentHour < 17) {
                    $title = "Good afternoon " . $booking->passenger->first_name . ",";
                } else {
                    $title = "Good evening " . $booking->passenger->first_name . ",";
                }

                // $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                $departureTime = date('H:i', strtotime($ride->time));
                $departureDate = date('d F, Y', strtotime($ride->date));

                // $message = "" . $title . "\nDriver cancelled this ride\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nDriver name: " . $ride->driver->first_name . "\nDriver phone number: " . $ride->driver->phone . "";
                $message = $title . "\n" .
                "From ProximaRide: we are sorry to inform you that your ride from " . $booking->departure .
                " to " . $booking->destination .
                " on " . $departureDate .
                " at " . $departureTime . " has been cancelled by the driver.\n" .
                "All amounts that you have made for this booking will be refunded to you immediately.";

                try {
                    $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                } catch (\Exception $e) {
                    \Log::error("Failed to send SMS to {$to}: " . $e->getMessage());
                }
            }
        }

        $ride->update([
            'status' => '2',
        ]);

        return redirect()->route('home', ['lang' => $selectedLanguageAbbreviation])
            ->with(['success' => $messages->ride_cancel_message]);
    }
 
    public function cancelRide($id, Request $request)
    {
        \Log::info("Attempting to cancel ride with ID: {$id}");

        // Find the ride by ID
        $ride = Ride::find($id);

        if (!$ride) {
            \Log::error("Ride not found with ID: {$id}");
            return response()->json(['success' => false, 'message' => 'Ride not found.'], 404);
        }

        // Check if there are any active bookings for the ride
        $bookedSeats = $ride->bookings()
            ->where('status', '<>', 3) // Exclude canceled bookings
            ->where('status', '<>', 4) // Exclude completed bookings
            ->whereHas('passenger', function ($query) {
                $query->whereNull('deleted_at'); // Exclude deleted passengers
            })->sum('seats');

        \Log::info("Booked seats count: {$bookedSeats}");

        // If there are no active bookings, cancel the ride
        if ($bookedSeats == 0) {
            // Update the ride status to 'cancelled'
            $ride->update(['status' => 'cancelled']);
            \Log::info("Ride status updated to cancelled for ID: {$id}");

            // Add cancellation history
            CancellationHistory::create([
                'ride_id' => $ride->id,
                'user_id' => $ride->added_by,
                'type' => 'driver',
            ]);

            // Notify passengers (if any)
            $bookings = Booking::where('ride_id', $id)->get();
            foreach ($bookings as $booking) {
                // Create detailed in-app notification message
                $departureDate = Carbon::parse($ride->date)->format('F d, Y');
                $departureTime = $ride->time;

                $notificationMessage = "Good morning " . $booking->passenger->first_name . ",\n" .
                    "We are sorry to inform you that the driver has cancelled their ride. They will send you a separate message explaining their reasons.\n\n" .
                    "While we pride ProximaRide on being a RELIABLE platform, we never know what life can throw at us. We assure you that we conduct a thorough investigation on each cancellation and, in case of frequent cancellations, we block the driver's account.\n\n" .
                    "Seats booked: " . numberToWords($booking->seats) . "\n" .
                    "Total booking price: $" . number_format($booking->fare, 2) . "\n" .
                    "Ride from " . $booking->departure . " to " . $booking->destination .
                    " on " . $departureDate . " at " . $departureTime . "\n\n" .
                    "All amounts that you have paid for this booking will be refunded to you immediately.\n\n" .
                    "Do not be discouraged; go ahead and search for other trips on ProximaRide.\n\n" .
                    "Please accept our sincere apologies for the inconvenience and have a safe ride,\n" .
                    "ProximaRide Team";

                // Create notification
                $notification = Notification::create([
                    'type' => 2, // Passenger notification
                    'ride_id' => $ride->id,
                    'posted_to' => $booking->id,
                    'posted_by' => $ride->added_by,
                    'message' => $notificationMessage,
                    'status' => 'cancelled',
                    'notification_type' => 'ride_cancellation',
                    'departure' => $booking->departure,
                    'destination' => $booking->destination,
                    'ride_detail_id' => $booking->ride_detail_id,
                ]);

                // Send FCM notification
                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $booking->user_id)->get();
                $notificationTitle = 'Your ride has been cancelled';
                $notificationBody = 'The driver has cancelled their ride';

                $fcmToken = $booking->passenger->mobile_fcm_token;
                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $notificationBody);
                }

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification(
                            $fcm_token->token,
                            $notificationBody,
                            $notificationTitle,
                            [
                                'notification_type' => 'ride_cancelled',
                                'ride_id' => $ride->id,
                                'notification_id' => $notification->id,
                                'open_message' => true
                            ]
                        );
                    } catch (\Exception $e) {
                        \Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                    }
                }

                // Send email notification to passengers
                if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {
                $data = [
                    'driver_name' => $ride->driver->first_name,
                    'passenger_name' => $booking->passenger->first_name,
                    'from' => $booking->departure,
                    'to' => $booking->destination,
                    'date' => $departureDate,
                    'time' => $departureTime,
                    'seats' => $booking->seats,
                    'total_price' => $booking->fare,
                ];
                Mail::to($booking->passenger->email)->queue(new DriverCancelRideMail($data));
            }

                // Send SMS notification to passengers (if enabled)
                $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                    ->where('verified', '1')
                    ->where('default', '1')
                    ->first();

                if ($phoneNumber && env('APP_ENV') != 'local') {
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');

                    $twilio = new Client($sid, $token);
                    $to = $phoneNumber->phone;

                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning " . $booking->passenger->first_name;
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $booking->passenger->first_name;
                    } else {
                        $title = "Good evening " . $booking->passenger->first_name;
                    }

                    // $departureDateTime = date('d F, Y H:i:s', strtotime($ride->date . ' ' . $ride->time));
                    $departureDate = date('F d, Y', strtotime($ride->date)); 
                    $departureTime = date('H:i', strtotime($ride->time));

                    // $smsMessage = "{$title}\nWe regret to inform you that your ride from {$booking->departure} to {$booking->destination} on {$departureDateTime} has been cancelled by the driver.\n\nAll payments will be refunded immediately.\n\nWe apologize for the inconvenience and encourage you to search for alternative rides.\n\nProximaRide Team";
                    $smsMessage = "From ProximaRide: we are sorry to inform you that your ride from {$booking->departure} to {$booking->destination} on {$departureDate} at {$departureTime} has been cancelled by the driver.\n\nAll amounts that you have made for this booking will be refunded to you immediately.";

                    try {
                        $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $smsMessage,
                            ]
                        );
                    } catch (\Exception $e) {
                        \Log::error("Failed to send SMS to {$to}: " . $e->getMessage());
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Ride canceled successfully.']);
        } else {
            \Log::error("Cannot cancel ride with booked seats.");
            return response()->json(['success' => false, 'message' => 'Cannot cancel ride with booked seats.']);
        }
    }
    public function cancelPassenger($lang = null, $id)
    {
        $booking = Booking::where('id', $id)->first();
        $ride = Ride::where('id', $booking->ride_id)->first();
        $setting = SiteSetting::first();
        $languages = Language::all();
        $tripsPage = null;
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'features_option1', 'features_option2', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'features_option1', 'features_option2', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();
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
        return view('cancel_passenger', ['booking' => $booking, 'messages' => $messages, 'ride' => $ride, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'postRidePage' => $postRidePage, 'setting' => $setting, 'tripsPage' => $tripsPage]);
    }

    public function updateRemovePassenger($id, Request $request)
    {

        $booking = Booking::where('id', $id)->first();
        $ride = Ride::where('id', $booking->ride_id)->first();
        $user_id = auth()->user()->id;
        $setting = SiteSetting::first();
        $monthsAgo = Carbon::now()->subMonths($setting->booking_cancel_duration)->setTimezone('UTC');;
        
        $cancellationCount = CancellationHistory::where('user_id', $user_id)
        ->where('created_at', '>=', $monthsAgo)
        ->where('type', 'driver')
        
        ->count();
        $messages = null;

        $niceNames = [];

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $limitExceed = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_cancellation_limit_exceed')->first();
                $niceNames = [
                    'block_day' => isset($tripsPage->remove_day_error) ? $tripsPage->remove_day_error : '',
                    'admin_message' => isset($tripsPage->driver_remove_reason_error) ? $tripsPage->driver_remove_reason_error : '',
                    'passenger_message' => isset($tripsPage->passenger_remove_reason_error) ? $tripsPage->passenger_remove_reason_error : '',
                ];
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $limitExceed = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_cancellation_limit_exceed')->first();

                $niceNames = [
                    'block_day' => isset($tripsPage->remove_day_error) ? $tripsPage->remove_day_error : '',
                    'admin_message' => isset($tripsPage->driver_remove_reason_error) ? $tripsPage->driver_remove_reason_error : '',
                    'passenger_message' => isset($tripsPage->passenger_remove_reason_error) ? $tripsPage->passenger_remove_reason_error : '',
                ];
            }
        }
        if ($cancellationCount >= $setting->booking_cancel_limit) {
            return redirect()->back()->with(['failure' => $limitExceed->booking_cancellation_limit_exceed ?? "Booking cancellation limit exceeded"]);
                
                // return response()->json(['error' => true, 'message' => $limitExceed->booking_cancellation_limit_exceed ?? "Booking cancellation limit exceeded"]);
            }
        $removed_permanently = $request->filled('removed_permanently') ? $request->removed_permanently : 0;

        $remove_type = $request->filled('remove_type') ? $request->remove_type : null;

        $request->validate([
            'admin_message' => 'required',
            'passenger_message' => 'required',
            'remove_type' => $removed_permanently == "1" ? 'required' : 'nullable',
            'block_day' => $remove_type == "temporarily" ? 'required' : 'nullable',
        ], [], $niceNames);

        $blockDay = "";
        $blockDateTime = "";
        if ($removed_permanently == "1" && isset($remove_type) && $remove_type == "temporarily") {
            $blockDay = $request->block_day;
            $currentDate = date('Y-m-d H:i:s');
            $getDate = date('Y-m-d H:i:s', strtotime($currentDate . "+ " . $blockDay . " days"));
            $blockDateTime = $getDate;
        }else if($removed_permanently == "1" && isset($remove_type) && $remove_type == "permanently"){
            $blockDay = 1000;
            $currentDate = date('Y-m-d H:i:s');
            $getDate = date('Y-m-d H:i:s', strtotime($currentDate . "+ " . $blockDay . " days"));
            $blockDateTime = $getDate;
        }

        $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
        foreach ($transactions as $transaction) {
            if ($transaction) {
                $refundId = "";

                $checkPrice = 0.0;
                $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');

                if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                    $getRefundEntryPrice = (float)$getRefundEntryPrice + (float)$transaction->booking_fee;
                }

                $checkPrice = (float)$transaction->price;

                if (isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == $checkPrice) {
                    if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                        $coffeeWallet = CoffeeWallet::create([
                            'booking_id' => $booking->id,
                            'ride_id' => $ride->id,
                            'user_id' => $booking->user_id,
                            'dr_amount' => $transaction->booking_fee,
                        ]);

                        $newTransaction = Transaction::create([
                            'booking_id' => $transaction->booking_id,
                            'ride_id' => $booking->ride_id,
                            'parent_id' => $transaction->id,
                            'type' => '3',
                            'price' => $transaction->booking_fee,
                            'paypal_id' => NULL,
                            'stripe_id' => NULL
                        ]);
                    }
                } else {

                    $transactionAmt = $checkPrice - $getRefundEntryPrice;

                    if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                        $transactionAmt = (float)$transactionAmt - (float)$transaction->booking_fee;
                    }

                    if ($transaction->pay_by_account == 0) {
                        if ($transaction->paypal_id) {
                            $paypal = new PayPalClient;
                            $paypal->setApiCredentials(config('paypal'));
                            $token = $paypal->getAccessToken();
                            $paypal->setAccessToken($token);
                            $response = $paypal->refundCapturedPayment(
                                $transaction->paypal_id,
                                'Invoice-' . $transaction->paypal_id,
                                $transactionAmt,
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
                                    'amount' => $transactionAmt * 100, // Refund amount in cents
                                ]);

                                $refundId = $refund->id;
                            } catch (\Stripe\Exception\ApiErrorException $e) {
                                // Handle error
                                Log::info($e->getMessage());
                                // return $this->apiErrorResponse($e->getMessage(), 200);
                            }
                        }
                    } else {
                        $topUpBalance = TopUpBalance::create([
                            'booking_id' => $transaction->booking_id,
                            'user_id' => $booking->user_id,
                            'dr_amount' => $transactionAmt,
                            'added_date' => date('Y-m-d'),
                        ]);
                    }

                    if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                        $coffeeWallet = CoffeeWallet::create([
                            'booking_id' => $booking->id,
                            'ride_id' => $ride->id,
                            'user_id' => $booking->user_id,
                            'dr_amount' => $transaction->booking_fee,
                        ]);
                    }

                    $newTransaction = Transaction::create([
                        'booking_id' => $transaction->booking_id,
                        'ride_id' => $booking->ride_id,
                        'parent_id' => $transaction->id,
                        'type' => '3',
                        'price' => $transactionAmt,
                        'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                        'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                    ]);
                }
            }
        }

        $booking->update([
            'status' => '4',
            'remove_type' => isset($remove_type) ? $remove_type : NULL,
            'removed_permanently' => $removed_permanently,
            'block_days' => isset($blockDay) && $blockDay != "" ? $blockDay : NULL,
            'block_date_time' => isset($blockDateTime) && $blockDateTime != "" ? $blockDateTime : NULL,
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

        CancellationHistory::create([
            'ride_id' => $booking->ride_id,
            'booking_id' => $booking->id,
            'user_id' => $ride->added_by,
        ]);
        if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {

            $data = ['passenger_name' => $booking->passenger->first_name, 'driver_name' => $booking->ride->driver->first_name, 'message' => $request->passenger_message, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($booking->ride->date)->format('F d, Y'), 'time' => $booking->ride->time, 'seats' => $booking->seats, 'total_price' => $booking->fare];
            // Send email to passenger
            Mail::to($booking->passenger->email)->queue(new CancelPassengerMail($data));
        }
        $admin = Admin::first();
        $data = ['admin_username' => $admin->username, 'driver_name' => $booking->ride->driver->first_name, 'passenger_name' => $booking->passenger->first_name, 'departure' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'message' => $request->admin_message];
        // Send email to admin
        Mail::to($admin->admin_email)->queue(new CancelPassengerAdminMail($data));
        $notification = Notification::create([
            'type' => 2,
            'ride_id' => $booking->ride_id,
            'posted_to' => $booking->id,
            'posted_by' => $booking->ride->added_by,
            'message' =>  'Driver cancelled your booking',
            'status' => 'cancelled',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination
        ]);
        $message = Message::create([
            'ride_id' => $booking->ride_id,
            'receiver' => $booking->user_id,
            'sender' => $ride->added_by,
            'message' => $request->admin_message,
            'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
        ]);
        $fcmService = new FCMService();
        $fcm_tokens = FCMToken::where('user_id', $booking->user_id)->get();
        $body = $notification->message;

        $fcmToken = $booking->passenger->mobile_fcm_token;
        if ($fcmToken) {
            $fcmService->sendNotification($fcmToken, $body);
        }

        foreach ($fcm_tokens as $fcm_token) {
            try {
                $fcmService->sendNotification($fcm_token->token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
            }
        }

        $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->where('default', '1')->first();

        if (!$phoneNumber) {
            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->first();
        }

        if ($phoneNumber && env('APP_ENV') != 'local' && isset($booking->passenger->sms_notification) && $booking->passenger->sms_notification == 1) {
            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_PHONE_NUMBER');

            $twilio = new Client($sid, $token);
            $to = $phoneNumber->phone;

            $title = "";
            $currentHour = date('H');
            if ($currentHour >= 0 && $currentHour < 12) {
                $title = "Good morning " . $booking->passenger->first_name . ",";
            } elseif ($currentHour >= 12 && $currentHour < 17) {
                $title = "Good afternoon " . $booking->passenger->first_name . ",";
            } else {
                $title = "Good evening " . $booking->passenger->first_name . ",";
            }

            // $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
            $departureTime = date('H:i:s', strtotime($ride->time));
            $departureDate = date('d F, Y', strtotime($ride->date));
            $seatWords = numberToWords($booking->seats);

            // $message = "" . $title . "\nDriver remove your seat from this ride\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nDriver name: " . $ride->driver->first_name . "\nDriver phone number: " . $ride->driver->phone . "";
            $message = $title . "\n" . "From ProximaRide: We are sorry to inform you that your driver has cancelled your booking\n" .
            "Ride from " . $booking->departure . 
            " to " . $booking->destination . 
            " on " . $departureDate . 
            " at " . $departureTime . 
            "\nNumber of seats: " . $seatWords . 
            "\nAll payments that you have made to book on this ride will be refunded to you immediately";

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
            if (isset($getBookings) && count($getBookings) > 0) {
                foreach ($getBookings as $key => $getBooking) {
                    if ($messageContent == "") {
                        $messageContent = "" . $getBooking->passenger->first_name . "(" . $getBooking->passenger->phone . ")";
                    } else {
                        $messageContent .= "\n" . $getBooking->passenger->first_name . "(" . $getBooking->passenger->phone . ")";
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
                        $title = "Good morning " . $ride->driver->first_name . "";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $ride->driver->first_name . "";
                    } else {
                        $title = "Good evening " . $ride->driver->first_name . "";
                    }

                    $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));

                    $message = "" . $title . "\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nHere is your passengers’ list\n" . $messageContent . "";

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


        return redirect()->route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $ride->id])->with(['success' => "The passenger has been removed from your ride"]);
    }
}
