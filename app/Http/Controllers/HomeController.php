<?php

namespace App\Http\Controllers;

use App\Mail\CoffeeOnWallReceiptMail;
use App\Mail\AdminCoffeeOnWallDonationMail;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Article;
use App\Models\CoffeeWallet;
use App\Models\CoffeeWallPageSettingDetail;
use App\Models\FCMToken;
use App\Models\HomePageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\Booking;
use App\Models\BillingAddressSettingDetail;
use App\Models\ChatsPageSettingDetail;
use App\Models\RideDetail;
use App\Models\Message;
use App\Models\FindRidePageSettingDetail;
use App\Models\PostRidePageSettingDetail;
use App\Models\FeaturesSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;

class HomeController extends Controller
{
    public function index($lang = null){


    //     $getRides = Ride::get();
    //     foreach ($getRides as $key => $getRide) {

    //         $getRideDetail = RideDetail::where('ride_id', $getRide->id)->first();
    //         if(isset($getRideDetail) && !empty($getRideDetail)){

    //         }else{
    //             $rideDetail = new RideDetail();
    //             $rideDetail->ride_id = $getRide->id;
    //             $rideDetail->departure = $getRide->departure;
    //             $rideDetail->destination = $getRide->destination;
    //             $rideDetail->default_ride = 1;
    //             $rideDetail->total_distance = $getRide->total_distance;
    //             $rideDetail->total_duration = $getRide->total_time;
    //             $rideDetail->price = isset($getRide->price) && $getRide->price != "" ? $getRide->price : 0;
    //             $rideDetail->time = $getRide->time;
    //             $rideDetail->date = $getRide->date;
    //             $rideDetail->destination_time = $getRide->destination_time;
    //             $rideDetail->destination_date = $getRide->destination_date;
    //             $rideDetail->completed_time = $getRide->completed_time;
    //             $rideDetail->completed_date = $getRide->completed_date;
    //             $rideDetail->save();
    //         }

    //         $getRide->departure= "";
    //         $getRide->destination= "";
    //         $getRide->total_distance= "";
    //         $getRide->total_time= "";
    //         $getRide->price= "";
    //         $getRide->save();

    //     }


    // //  //   Update Booking

    //     $getBooking = Booking::all();
    //     foreach ($getBooking as $key => $booking) {
    //         $getRideDetail = RideDetail::where('ride_id', $booking->ride_id)->where('default_ride', '1')->first();
    //         $booking->ride_detail_id = $getRideDetail->id;
    //         $booking->departure = $getRideDetail->departure;
    //         $booking->destination = $getRideDetail->destination;
    //         $booking->price = $getRideDetail->price;
    //         $booking->save();
    //     }


    //     $getNotifcations = Notification::get();
    //     foreach ($getNotifcations as $key => $getNotifcation) {
    //         $getRideDetail = RideDetail::where('ride_id', $getNotifcation->ride_id)->where('default_ride', '1')->first();
    //         if(isset($getRideDetail)){
    //             $getNotifcation->ride_detail_id = $getRideDetail->id;
    //             $getNotifcation->departure = $getRideDetail->departure;
    //             $getNotifcation->destination = $getRideDetail->destination;
    //             $getNotifcation->save();
    //         }
    //     }

    //     $getMessages = Message::get();
    //     foreach ($getMessages as $key => $getMessage) {
    //         $getRideDetail = RideDetail::where('ride_id', $getMessage->ride_id)->where('default_ride', '1')->first();
    //         if(isset($getRideDetail)){
    //             $getMessage->ride_detail_id = $getRideDetail->id;
    //             $getMessage->save();
    //         }
    //     }

    //      return "Hello";

    //    // Initialize cURL session
    //     $ch = curl_init();

    //     // Set the cURL options
    //     curl_setopt($ch, CURLOPT_URL, "https://maps.googleapis.com/maps/api/distancematrix/json?origins=Faisalabad&destinations=Lahore&units=imperial&key=AIzaSyBZWXZAUBOyBMVtY0Zs0M8sNvkbZd_OzFM");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string

    //     // Execute the cURL session and store the result
    //     $response = curl_exec($ch);

    //     // Check for cURL errors
    //     if(curl_errno($ch)) {
    //         echo 'cURL Error: ' . curl_error($ch);
    //     }

    //     // Close the cURL session
    //     curl_close($ch);

    //     // Optionally, decode the JSON response if needed
    //     $data = json_decode($response, true);

    //     // Display the result
    //     return $data;


    //     $adminSetting = SiteSetting::first();
    //     if(isset($adminSetting)){

    //         $getRides = Ride::get();
    //         if(isset($getRides) && !empty($getRides)){
    //             foreach ($getRides as $key => $initialRide) {
    //                 if(isset($initialRide->date) && isset($initialRide->time)){
    //                     $rideDateTime = Carbon::parse("$initialRide->date $initialRide->time");
    //                     $rideDateTime->addHours($adminSetting->destination_hours);
    //                     $rideDateTime->addHours($adminSetting->ride_completed_hours);
    //                     $completedDate = $rideDateTime->toDateString();
    //                     $completedTime = $rideDateTime->toTimeString();

    //                     $initialRide->completed_date = $completedDate;
    //                     $initialRide->completed_time = $completedTime;
    //                     $initialRide->save();
    //                 }

    //             }
    //         }

    //     }

    //     return $getRides;

        $rides = Ride::with(['defaultRideDetail'])->latest('added_on')->where('status', '!=', 2)->where('suspand', '!=', 1)->take(4)->get();
        // $reviews = Rating::latest('added_on')->where('status', '!=', 0)->get();
        // Filter the reviews with average rating above 4.5
        // $filteredReviews = $reviews->filter(function ($review) {
        //     $sum = 0;
        //     $count = 0;

        //     $columns = ['vehicle_condition', 'timeliness', 'safety', 'conscious', 'comfort', 'communication', 'attitude', 'respect', 'hygiene'];

        //     foreach ($columns as $column) {
        //         if (!is_null($review->$column)) {
        //             $sum += $review->$column;
        //             $count++;
        //         }
        //     }

        //     // Calculate average rating for the review
        //     $averageRating = $count > 0 ? $sum / $count : null;

        //     return $averageRating >= 4.5;
        // });
        // Take the latest 4 filtered reviews
        // $latestFilteredReviews = $filteredReviews->take(4);
        $latestFilteredReviews = Rating::latest('added_on')->where('is_disply', 1)->get();

        $video = Video::where('page','Introduction Video')->orderBy('id', 'desc')->first();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $videoDetails = null;
        $homePage = null;
        $findRidePage = null;
        $token = null;
        $postRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {

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

                // Retrieve the HomePageSettingDetail associated with the selected language
                $homePage = HomePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                // Fetch video_details for the Introduction Video using the video's ID.
                if($video){
                    $videoDetails = VideoDetail::where('video_id', $video->id)
                        ->where('language_id', $selectedLanguage->id)
                        ->first();
                }
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();                

                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
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

                $homePage = HomePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if($video){
                    $videoDetails = VideoDetail::where('video_id', $video->id)
                        ->whereHas('language', function ($query) {
                            $query->where('is_default', 1);
                        })->first();
                }
            }
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')->where('is_read', '0');

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

            $token = auth()->user()->createToken('auth_token')->plainTextToken;
        }


        $ratings = Rating::all();

        $langId = $selectedLanguage->id;

        $articles = Article::whereHas('articleDetail', function ($query) use ($langId) {
            $query->where('language_id', $langId);
        })->with('articleDetail')->orderBy('id', 'desc')->limit(8)->get();

        return view('index',['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage, 'token' => $token, 'rides' => $rides,'video' => $videoDetails,'articles' => $articles,'reviews' => $latestFilteredReviews,'homePage' => $homePage,'languages' => $languages,'selectedLanguage' => $selectedLanguage,'notifications' => $notifications, 'ratings' => $ratings, 'findRidePage' => $findRidePage, 'postRidePage' => $postRidePage]);
    }

    function redirectToAdminDashboard()  {
        return view('admin.app');
    }

    public function updateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user_id = auth()->user()->id;

        $fcm_token = FCMToken::where('user_id',$user_id)->where('token',$request->token)->first();

        if (!$fcm_token) {
            FCMToken::create([
                'user_id' => $user_id,
                'token' => $request->token,
            ]);
        }

        return response()->json(['message' => 'FCM token updated successfully.']);
    }

    public function createSubscription(Request $request)
    {
        if ($request->package_id) {
            $package = Package::whereId($request->package_id)->first();
        } else {
            $package = Package::where('price', $request->custom_amount)->first();
            if (!$package) {
                DB::beginTransaction();
                $package = Package::create([
                    'price' => $request->custom_amount ?? 0,
                    'custom' => 1,
                ]);

                $packageName = $request->name ?? env('APP_NAME');
                $packageDescription = 'custom' ?? env('APP_NAME');

                Stripe::setApiKey(env('STRIPE_SECRET'));

                if ($package->stripe_product_id) {
                    $product = Product::retrieve($package->stripe_product_id);
                    $product->name = $packageName;
                    $product->save();
                } else {
                    $product = Product::create([
                        'name' => $packageName,
                        'type' => 'service',
                    ]);
                    $package->update(['stripe_product_id' => $product->id]);
                }

                if ($package->price) {
                    $priceData = [
                        'product' => $product->id,
                        'unit_amount' => $package->price * 100,
                        'currency' => 'usd',
                        'recurring' => ['interval' => 'month', 'interval_count' => 1],
                    ];

                    $price = Price::create($priceData);
                }

                $package->update(['stripe_price_id' => $price->id ?? null]);

                $paypal_plan_id = null;

                $paypal = new PayPalClient;
                $paypal->setApiCredentials(config('paypal'));
                $token = $paypal->getAccessToken();
                $paypal->setAccessToken($token);

                if ($package->paypal_product_id) {
                    $product = $paypal->showProductDetails($package->paypal_product_id);
                    $paypal_plan_id = $product['id'] ?? null;
                }
                if (!$paypal_plan_id) {
                    $data = [
                        'name' => $packageName,
                        'type' => 'SERVICE',
                        'description' => $packageDescription,
                        'category' => 'SOFTWARE',
                    ];
                    $product = $paypal->createProduct($data);
                    $paypal_plan_id = $product['id'] ?? null;

                    $package->update(['paypal_product_id' => $paypal_plan_id]);
                }
                if ($paypal_plan_id && $package) {
                    if ($package->price > 0) {
                        $productId = $package->paypal_product_id;

                        $interval_count = 1;
                        $price = $package->price;

                        $billing_detail = [
                            [
                                'frequency' => [
                                    'interval_unit' => 'MONTH',
                                    'interval_count' => $interval_count, // Interval count
                                ],
                                'tenure_type' => 'REGULAR', // Tenure type
                                'sequence' => 1, // Cycle sequence number
                                'total_cycles' => 0, // Total cycles
                                'pricing_scheme' => [
                                    'fixed_price' => [
                                        'value' => $price, // Price value
                                        'currency_code' => 'USD',
                                    ],
                                ],
                            ]
                        ];

                        $data = [
                            'product_id' => $productId, // Replace with your product ID
                            'name' => $packageName . ' for 1 month ', // Plan name
                            'description' => $packageName . ' for 1 month plan is auto renewal', // Plan description
                            'status' => 'ACTIVE', // Plan status
                            'billing_cycles' => $billing_detail,
                            'payment_preferences' => [
                                'auto_bill_outstanding' => true,
                                'auto_renewal' => true,
                                'setup_fee' => [
                                    'value' => '0',
                                    'currency_code' => 'USD',
                                ],
                                'setup_fee_failure_action' => 'CONTINUE',
                                'payment_failure_threshold' => 5,
                            ],
                        ];

                        $plan = $paypal->createPlan($data);

                        if ($package == null) {
                            $plan['id'] = null;
                        } else {
                            $package->update(['paypal_price_id' => $plan['id']]);
                        }
                    } else {
                        $package->update(['paypal_price_id' => null]);
                    }
                }

                DB::commit();
            }
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $customer = Customer::create([
            'email' => $request->email ?? null,
            'payment_method' => $request->payment_method,
            'invoice_settings' => [
                'default_payment_method' => $request->payment_method,
            ],
        ]);

        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [[
                'price' => $package->stripe_price_id, // The recurring price ID from Stripe dashboard
            ]],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        return response()->json([
            'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret,
            'subscriptionId' => $subscription->id
        ]);
    }

    public function coffeeOnWall($lang = null)
    {
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $coffeeWallPage = null;
        $paymentSettingDetail = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
                $coffeeWallPage = CoffeeWallPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $paymentSettingDetail = BillingAddressSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
                $coffeeWallPage = CoffeeWallPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $paymentSettingDetail = BillingAddressSettingDetail::where('language_id', $selectedLanguage->id)->first();
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

        $packages = Package::where('custom', 0)->with(['PackageDetail' => function ($query) use ($selectedLanguage) {
            $query->where('language_id', $selectedLanguage->id);
        }])
        ->get();
        return view('coffee_wall',['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'coffeeWallPage' => $coffeeWallPage,'packages' => $packages,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage, 'paymentSettingDetail' => $paymentSettingDetail, 'stripeKey' => env('STRIPE_KEY')]);
    }

    public function coffeeOnWallStore(Request $request)
    {
        // dd($request->all());
        // Validate the form data
        $validatedData = $request->validate([
            'package' => $request->custom_amount ? 'nullable' : 'required',
            'custom_amount' => $request->package == 'custom' ? 'required' : 'nullable',
            'name' => $request->anonymous ? 'nullable' : 'required',
            'email' => $request->anonymous ? 'nullable|email' : 'required|email',
            'payment_method' => 'required|in:stripe,paypal',
            'donation_acknowledgment' => 'required',
            'terms_privacy' => 'required',
        ], [
            'email.email' => 'Please use a valid email',
        ]);

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('coffee_wall_heading_success_message', 'coffee_wall_text_success_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('coffee_wall_heading_success_message', 'coffee_wall_text_success_message')->first();
        }

        if ($request->package) {
            $package = Package::whereId($request->package)->first();
        } else {
            $package = Package::where('price', $request->custom_amount)->first();
            if (!$package) {
                DB::beginTransaction();
                $package = Package::create([
                    'price' => $request->custom_amount ?? 0,
                    'custom' => 1,
                ]);

                $packageName = $request->name ?? env('APP_NAME');
                $packageDescription = 'custom' ?? env('APP_NAME');

                Stripe::setApiKey(env('STRIPE_SECRET'));

                if ($package->stripe_product_id) {
                    $product = Product::retrieve($package->stripe_product_id);
                    $product->name = $packageName;
                    $product->save();
                } else {
                    $product = Product::create([
                        'name' => $packageName,
                        'type' => 'service',
                    ]);
                    $package->update(['stripe_product_id' => $product->id]);
                }

                if ($package->price) {
                    $priceData = [
                        'product' => $product->id,
                        'unit_amount' => $package->price * 100,
                        'currency' => 'usd',
                        'recurring' => ['interval' => 'month', 'interval_count' => 1],
                    ];

                    $price = Price::create($priceData);
                }

                $package->update(['stripe_price_id' => $price->id ?? null]);

                $paypal_plan_id = null;

                $paypal = new PayPalClient;
                $paypal->setApiCredentials(config('paypal'));
                $token = $paypal->getAccessToken();
                $paypal->setAccessToken($token);

                if ($package->paypal_product_id) {
                    $product = $paypal->showProductDetails($package->paypal_product_id);
                    $paypal_plan_id = $product['id'] ?? null;
                }
                if (!$paypal_plan_id) {
                    $data = [
                        'name' => $packageName,
                        'type' => 'SERVICE',
                        'description' => $packageDescription,
                        'category' => 'SOFTWARE',
                    ];
                    $product = $paypal->createProduct($data);
                    $paypal_plan_id = $product['id'] ?? null;

                    $package->update(['paypal_product_id' => $paypal_plan_id]);
                }
                if ($paypal_plan_id && $package) {
                    if ($package->price > 0) {
                        $productId = $package->paypal_product_id;

                        $interval_count = 1;
                        $price = $package->price;

                        $billing_detail = [
                            [
                                'frequency' => [
                                    'interval_unit' => 'MONTH',
                                    'interval_count' => $interval_count, // Interval count
                                ],
                                'tenure_type' => 'REGULAR', // Tenure type
                                'sequence' => 1, // Cycle sequence number
                                'total_cycles' => 0, // Total cycles
                                'pricing_scheme' => [
                                    'fixed_price' => [
                                        'value' => $price, // Price value
                                        'currency_code' => 'USD',
                                    ],
                                ],
                            ]
                        ];

                        $data = [
                            'product_id' => $productId, // Replace with your product ID
                            'name' => $packageName . ' for 1 month ', // Plan name
                            'description' => $packageName . ' for 1 month plan is auto renewal', // Plan description
                            'status' => 'ACTIVE', // Plan status
                            'billing_cycles' => $billing_detail,
                            'payment_preferences' => [
                                'auto_bill_outstanding' => true,
                                'auto_renewal' => true,
                                'setup_fee' => [
                                    'value' => '0',
                                    'currency_code' => 'USD',
                                ],
                                'setup_fee_failure_action' => 'CONTINUE',
                                'payment_failure_threshold' => 5,
                            ],
                        ];

                        $plan = $paypal->createPlan($data);

                        if ($package == null) {
                            $plan['id'] = null;
                        } else {
                            $package->update(['paypal_price_id' => $plan['id']]);
                        }
                    } else {
                        $package->update(['paypal_price_id' => null]);
                    }
                }

                DB::commit();
            }
        }

        if ($package->price > 0) {
            if (isset($request->payment_method) && $request->payment_method == 'stripe') {
                $request->validate([
                    'stripeToken' => !isset($request->gPayApplePayId) && $request->gPayApplePayId == "" ? 'required' : 'nullable',
                ]);
            }
        }

        $package_price = $package->price;

        if ($request->payment_method == 'stripe') {
            Stripe::setApiKey(env('STRIPE_SECRET'));
        }

        try {
            if ($request->payment_method == 'stripe' && $package_price > 0) {
                if (isset($request->gPayApplePayId) && $request->gPayApplePayId != '') {
                    $subscription_id = $request->gPayApplePayId;
                } else {
                    $interval = null;
    
                    if ($request->frequency == 'weekly') {
                        $interval = 'week';
                        $interval_count = 1; // Charge every 1 week
                    } else if ($request->frequency == 'monthly') {
                        $interval = 'month';
                    } else if ($request->frequency == 'quarterly') {
                        $interval = 'month';
                        $interval_count = 3;
                    } else if ($request->frequency == 'semi_annually') {
                        $interval = 'month';
                        $interval_count = 6;
                    } else if ($request->frequency == 'annually') {
                        $interval = 'year';
                    }
    
                    // Create a PaymentMethod with Stripe using the token
                    $paymentMethods = PaymentMethod::create([
                        'type' => 'card',
                        'card' => ['token' => $request->stripeToken],
                        'billing_details' => [
                            'name' => $request->name_on_card,
                            'address' => [
                                'line1' => $request->address,
                            ],
                        ],
                    ]);
    
                    $stripeCustomer = Customer::create([
                        'name' => $request->name,
                        'email' => $request->email,
                    ]);
    
                    $stripe_customer_id = $stripeCustomer->id;
    
                    // Attach a payment method to the customer
                    $paymentMethods->attach(['customer' => $stripe_customer_id]);
    
                    // Set the attached payment method as the default for the customer
                    $stripeCustomer->update(
                        $stripe_customer_id,
                        ['invoice_settings' => ['default_payment_method' => $paymentMethods->id]]
                    );
    
                    $subscription_items = [
                        ['price' => $package->stripe_price_id],
                    ];
    
                    $subscription_params = [
                        'customer' => $stripe_customer_id,
                        'items' => $subscription_items,
                        'cancel_at_period_end' => false,
                    ];
    
                    // Add the interval and interval_count if applicable
                    if ($interval) {
    
                        $timeStamp = now()->timestamp;
                        if($interval == "week"){
                            $timeStamp = now()->addDays(7)->timestamp;
                        }elseif($interval == 'month'){
                            $timeStamp = now()->addMonth(1)->timestamp;
                        }elseif($interval == 'year'){
                            $timeStamp = now()->addMonth(12)->timestamp;
                        }else{
                            $timeStamp = now()->addMonth($interval_count)->timestamp;
                        }
    
                        $subscription_params['billing_cycle_anchor'] = $timeStamp; // Anchor the subscription
                        $subscription_items[0]['plan'] = [
                            'interval' => $interval,
                        ];
                        if (isset($interval_count)) {
                            $subscription_items[0]['plan']['interval_count'] = $interval_count;
                        }
                    }
    
                    $subscription = Subscription::create($subscription_params);
    
                    $subscription_id = $subscription->id;
                    $stripe_item_id = isset($subscription->items->data[0]) ? $subscription->items->data[0]->id : null;
                }
            } else if ($request->payment_method == 'paypal' && $package_price > 0) {
                $paypal = new PayPalClient;
                $paypal->setApiCredentials(config('paypal'));
                $token = $paypal->getAccessToken();
                $paypal->setAccessToken($token);

                $planId = $package->paypal_price_id;

                $interval_unit = null;
                $interval_count = 1;
                if ($request->frequency == 'weekly') {
                    $interval_unit = 'DAY';
                    $interval_count = 7; // Every 7 days
                } else if ($request->frequency == 'monthly') {
                    $interval_unit = 'MONTH';
                } else if ($request->frequency == 'quarterly') {
                    $interval_unit = 'MONTH';
                    $interval_count = 3;
                } else if ($request->frequency == 'semi_annually') {
                    $interval_unit = 'MONTH';
                    $interval_count = 6;
                } else if ($request->frequency == 'annually') {
                    $interval_unit = 'YEAR';
                }

                $data = [
                    'plan_id' => $planId, // Replace with your actual plan ID
                    'subscriber' => [
                        'name' => [
                            'given_name' => $request->name,
                            'surname' => '',
                        ],
                        'email_address' => $request->email,
                    ],
                    'application_context' => [
                        'return_url' => route('paypal.subscription.success', ['name' => $request->name, 'email' => $request->email, 'package_id' => $package->id, 'phone' => $request->phone ?? null, 'anonymous' => $request->anonymous ?? null, 'designation' => is_array($request->designation) ? implode(', ', $request->designation) : ($request->designation ?? null), 'frequency' => $request->frequency ?? null]),
                        'cancel_url' => route('paypal.cancel')
                    ],
                ];

                // Attach interval and count if applicable
                if ($interval_unit) {
                    $data['plan'] = [
                        'billing_cycles' => [
                            [
                                'frequency' => [
                                    'interval_unit' => $interval_unit,
                                    'interval_count' => $interval_count,
                                ],
                                'tenure_type' => 'REGULAR',
                                'sequence' => 1,
                                'total_cycles' => 0, // Ongoing subscription
                                'pricing_scheme' => [
                                    'fixed_price' => [
                                        'value' => $package_price,
                                        'currency_code' => 'USD',
                                    ],
                                ],
                            ],
                        ],
                    ];
                }

                $responseData = $paypal->createSubscription($data);

                if (isset($responseData['links'][0]['href'])) {
                    $paypalResponse = [
                        'status' => 'Success',
                        'redirect_url' => $responseData['links'][0]['href']
                    ];
                } else {
                    if (isset($responseData->details)) {
                        $errorMessage = $responseData->details[0]->issue;
                    } else {
                        $errorMessage = "An unknown error occurred.";
                    }

                    $paypalResponse = [
                        'status' => 'Error',
                        'message' => "Subscription creation failed. Error: $errorMessage"
                    ];
                }

                if ($paypalResponse['status'] == 'Error') {
                    return $paypalResponse['message'];
                } else if ($paypalResponse['status'] == 'Success') {
                    return redirect()->to($paypalResponse['redirect_url']);
                }
                return redirect()->route('coffee_on_wall', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "Amount not processed"]);
            }

            // Handle designation array - convert to comma-separated string
            $designation = null;
            if ($request->designation) {
                $designation = is_array($request->designation) ? implode(', ', $request->designation) : $request->designation;
            }

            $coffeeWallet =  CoffeeWallet::create([
                'user_id' => auth()->id(),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'anonymous' => $request->anonymous ? $request->anonymous : 0,
                'designation' => $designation,
                'notify_coffee_used' => $request->notify_coffee_used ? true : false,
                'donation_acknowledgment' => $request->donation_acknowledgment ? true : false,
                'terms_privacy' => $request->terms_privacy ? true : false,
                'package_id' => $package->id,
                'frequency' => $request->frequency ? $request->frequency : null,
                'dr_amount' => $package_price,
                'paypal_id' => null,
                'stripe_id' => isset($stripe_item_id) ? $stripe_item_id : null,
                'subscription_id' => $request->payment_method == 'stripe' && $package_price > 0 ? $subscription_id : null,
                'payment_method' => $package_price > 0 ? $request->payment_method : null,
                'status' => 'completed',
            ]);
            $user = auth()->user() ?? User::where('email', $request->email)->first();
            $fullName = $user ? $user->first_name . ' ' . $user->last_name : ($request->name ?? 'Anonymous Donor');
            $data = [
                // 'full_name' => $request->name ?? 'Anonymous Donor',
                'full_name' => $fullName,
                'amount' => $package_price,
                'transaction_id' =>  $coffeeWallet->random_id,
                'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                'payment_method' => $request->payment_method,
            ];
            
            if ($request->payment_method == 'paypal') {
                $data['paypal_email'] = $request->email;
            } elseif ($request->payment_method == 'stripe') {
                $card = $coffeeWallet->card;
                $data['card_type'] = $request->card_type ?? $card->card_type ?? 'Card';
                $data['cardholder_name'] = $request->name ?? $card->name ?? 'Cardholder';
                $data['last_four_digits'] = $request->card_number ?? $card->card_number ?? '****';
                $data['expiration_date'] = ($request->exp_month ?? $card->exp_month ?? 'MM').'/'.($request->exp_year ?? $card->exp_year ?? 'YY');
            }
            
            if ($request->email) {
                Mail::to($request->email)->send(new CoffeeOnWallReceiptMail($data));
            }

            // Send admin notification about Coffee on Wall donation
            $admin = Admin::first();
            if ($admin && $admin->admin_email) {
                $adminData = [
                    'donor_name' => $request->anonymous ? 'Anonymous Donor' : $fullName,
                    'donor_email' => $request->anonymous ? null : $request->email,
                    'amount' => $package_price,
                    'transaction_id' => $coffeeWallet->random_id,
                    'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                    'payment_method' => $request->payment_method,
                    'frequency' => $request->frequency ?? null,
                ];
                
                // Add payment method specific details
                if ($request->payment_method == 'paypal') {
                    $adminData['paypal_email'] = $request->email;
                } elseif ($request->payment_method == 'stripe') {
                    $card = $coffeeWallet->card;
                    $adminData['card_type'] = $request->card_type ?? $card->card_type ?? 'Card';
                    $adminData['cardholder_name'] = $request->name ?? $card->name ?? 'Cardholder';
                    $adminData['last_four_digits'] = $request->card_number ?? $card->card_number ?? '****';
                    $adminData['expiration_date'] = ($request->exp_month ?? $card->exp_month ?? 'MM').'/'.($request->exp_year ?? $card->exp_year ?? 'YY');
                }
                
                Mail::to($admin->admin_email)->queue(new AdminCoffeeOnWallDonationMail($adminData));
            }

            return redirect()->route('coffee_on_wall', ['lang' => $selectedLanguage->abbreviation])->with(['message' => $messages->coffee_wall_text_success_message ?? "Thank you for your generosity. Please accept our best wishes"])->with('heading', $messages->coffee_wall_heading_success_message ?? 'Payment successful');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function paypalSuccessResponse(Request $request)
    {
        $package = null;
        if (!isset($request->subscription_id)) {
            return false;
        }
        if (isset($_GET['package_id']) && $_GET['package_id'] != '') {
            $package = Package::whereId($_GET['package_id'])->first();
        }

        $name = isset($_GET['name']) ? $_GET['name'] : null;
        $email = isset($_GET['email']) ? $_GET['email'] : null;
        $phone = isset($_GET['phone']) ? $_GET['phone'] : null;
        $anonymous = isset($_GET['anonymous']) ? $_GET['anonymous'] : 0;
        $designation = isset($_GET['designation']) ? $_GET['designation'] : null;
        $frequency = isset($_GET['frequency']) ? $_GET['frequency'] : null;

        $coffeeWallet = CoffeeWallet::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'anonymous' => $anonymous,
            'designation' => $designation,
            'notify_coffee_used' => isset($_GET['notify_coffee_used']) ? ($_GET['notify_coffee_used'] ? true : false) : false,
            'donation_acknowledgment' => isset($_GET['donation_acknowledgment']) ? ($_GET['donation_acknowledgment'] ? true : false) : false,
            'terms_privacy' => isset($_GET['terms_privacy']) ? ($_GET['terms_privacy'] ? true : false) : false,
            'package_id' => $_GET['package_id'],
            'frequency' => $frequency,
            'dr_amount' => $package->price,
            'paypal_id' => $request->subscription_id,
            'stripe_id' => null,
            'subscription_id' => null,
            'payment_method' => 'paypal',
            'status' => 'completed',
        ]);
        $user = User::where('email', $email)->first();
        $fullName = $user 
            ? $user->first_name . ' ' . $user->last_name 
            : ($name ?? 'Anonymous Donor');
        
        $data = [
            'full_name' => $fullName,
            'amount' => $package->price,
            'transaction_id' => $coffeeWallet->random_id,
            'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
            'payment_method' => 'paypal',
            'paypal_email' => $email,
        ];
        
        if ($email) {
            Mail::to($email)->send(new CoffeeOnWallReceiptMail($data));
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('coffee_wall_heading_success_message', 'coffee_wall_text_success_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('coffee_wall_heading_success_message', 'coffee_wall_text_success_message')->first();
        }

        return redirect()->route('coffee_on_wall', ['lang' => $selectedLanguage->abbreviation])->with(['message' => $messages->coffee_wall_text_success_message ?? "Thank you for your generosity. Please accept our best wishes"])->with('heading', $messages->coffee_wall_heading_success_message ?? 'Payment successful');
    }

    public function getPackages(Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        $data['packages'] = Package::with(['PackageDetail' => function ($query) use ($selectedLanguage) {
                        $query->where('language_id', $selectedLanguage->id);
                    }])
                    ->get();
        return response()->json($data);
    }
}
