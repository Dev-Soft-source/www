<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\DriverReceivedReviewMail;
use App\Mail\PassengerReceivedReviewMail;
use App\Mail\ReviewLeftMail;
use App\Models\Booking;
use App\Models\Language;
use App\Models\Rating;
use App\Models\ReviewReply;
use App\Models\ReviewSetting;
use App\Models\Ride;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Models\ReviewSettingDetail;
use App\Models\TripsPageSettingDetail;
use App\Models\ReferralDetail;
use App\Models\RegistrationRewardSetting;
use App\Models\RewardPoint;
use App\Models\ReferralSystemSetting;
use App\Models\TopUpBalance;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
    use StatusResponser;

    public function index(Request $request){
        $rating = Rating::whereId($request->id)
            ->with(['from' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])->first();

        if ($rating->from->gender) {
            $rating->from->gender = ucfirst($rating->from->gender);
        }

        if ($rating->type == 1) {
            $ride = $rating->ride()->with(['driver' => function ($query) {
                $query->withTrashed(); // Include soft deleted passengers
            }])->first();
            $rating->to = [
                'id' => $ride->driver->id,
                'first_name' => $ride->driver->first_name,
                'last_name' => $ride->driver->last_name,
                'gender' => ucfirst($ride->driver->gender),
                'profile_image' => $ride->driver->profile_image,
            ];
        } elseif ($rating->type == 2) {
            $booking = $rating->booking()->with(['passenger' => function ($query) {
                $query->withTrashed(); // Include soft deleted passengers
            }])->first();
            $rating->to = [
                'id' => $booking->passenger->id,
                'first_name' => $booking->passenger->first_name,
                'last_name' => $booking->passenger->last_name,
                'gender' => ucfirst($booking->passenger->gender),
                'profile_image' => $booking->passenger->profile_image,
            ];
        }

        $reviewDetailPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            // Retrieve the tripsPageSettingDetail associated with the selected language
            $reviewDetailPage = TripsPageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $reviewDetailPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['rating' => $rating, 'reviewDetailPage' => $reviewDetailPage];
        return $this->successResponse($data, 'Get review successfully');
    }

    public function ReviewsReceived(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $ratings = Rating::where(function ($query) use ($user_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '2')
                  ->whereHas('booking', function ($query) use ($user_id) {
                      $query->where('user_id', $user_id);
                  });
        })
        ->orWhere(function ($query) use ($user_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '1')
                  ->whereHas('ride', function ($query) use ($user_id) {
                      $query->where('added_by', $user_id);
                  });
        })
        ->with(['from' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image'); // Specify the columns you want to select
            $query->withTrashed(); // Include soft-deleted users
        }])
        ->orderBy('id', 'desc')
        ->paginate($request->paginate_limit);

        foreach ($ratings as $rating) {
            $reply = $rating->replies()->first();
            $rating->replies = $reply ? $reply : null;
            if ($rating->from->gender) {
                $rating->from->gender = ucfirst($rating->from->gender);
            }
        }

        $reviewSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $reviewSettingPage = ReviewSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $reviewSettingPage = ReviewSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['ratings' => $ratings, 'reviewSettingPage' => $reviewSettingPage];
        return $this->successResponse($data, 'Get reviews received successfully');
    }

    public function ReviewsLeft(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        $ratings = Rating::where('posted_by',$user_id)->whereIn('type',['1','2'])->orderBy('id', 'desc')->get();

        foreach ($ratings as $rating) {
            if ($rating->type == 1) {
                $ride = $rating->ride()->with(['driver' => function ($query) {
                    $query->withTrashed(); // Include soft deleted passengers
                }])->first();
                $rating->to = [
                    'id' => $ride->driver->id,
                    'first_name' => $ride->driver->first_name,
                    'last_name' => $ride->driver->last_name,
                    'gender' => ucfirst($ride->driver->gender),
                    'profile_image' => $ride->driver->profile_image,
                ];
            } elseif ($rating->type == 2) {
                $booking = $rating->booking()->with(['passenger' => function ($query) {
                    $query->withTrashed(); // Include soft deleted passengers
                }])->first();
                $rating->to = [
                    'id' => $booking->passenger->id,
                    'first_name' => $booking->passenger->first_name,
                    'last_name' => $booking->passenger->last_name,
                    'gender' => ucfirst($booking->passenger->gender),
                    'profile_image' => $booking->passenger->profile_image,
                ];
            }

            $reply = $rating->replies()->first();
            $rating->replies = $reply ? $reply : null;
        }

        $perPage = $request->paginate_limit ?? 15;
        $page = $request->input('page', 1);
        $offset = ($page * $perPage) - $perPage;

        $pagedData = $ratings->slice($offset, $perPage)->values()->all();

        $ratings = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, $ratings->count(), $perPage, $page, [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
        ]);

        $reviewSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $reviewSettingPage = ReviewSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $reviewSettingPage = ReviewSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['ratings' => $ratings, 'reviewSettingPage' => $reviewSettingPage];
        return $this->successResponse($data, 'Get reviews left successfully');
    }

    public function ReviewDriver(Request $request){
        $ride = Ride::whereId($request->ride_id)->first();

        $data = ['driver_profile_image' => $ride->driver->profile_image, 'driver_first_name' => $ride->driver->first_name, 'driver_last_name' => $ride->driver->last_name, 'ride_id' => $ride->id];
        return $this->successResponse($data, 'Get review a driver page successfully');
    }

    public function StoreReviewDriver(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        
        $ride = Ride::whereId($request->ride_id)->first();
        $setting = ReviewSetting::first();
        
        $customMessages = [
            'required_without_all' => 'At least one of the ratings must be filled',
            'max_words' => 'The :attribute may not be greater than 500 words',
        ];
        
        $request->validate([
            'review' => 'required|string|max_words:500',
            'vehicle_condition' => ['required_without_all:conscious,comfort,communication,attitude,hygiene,respect,safety,timeliness'],
        ], $customMessages);

        // Initialize variables to store sum and count of non-null values
        $sum = 0;
        $count = 0;
        
        // Iterate over the columns and calculate sum and count of non-null values
        $columns = ['vehicle_condition', 'timeliness', 'safety', 'conscious', 'comfort', 'communication', 'attitude', 'respect', 'hygiene'];
        foreach ($columns as $column) {
            if ($request->$column == null || $request->$column == '0' || $request->$column == 0) {
                
            }else{
                $sum += $request->$column;
                $count += 1;
            }
        }
        
        // Calculate average rating
        $averageRating = $count > 0 ? $sum / $count : null;

        // Format average rating with one decimal place
        $formattedAverageRating = $averageRating !== null ? number_format($averageRating, 1) : null;
        
        $rating = Rating::create([
            'ride_id' => $ride->id,
            'review' => $request->review,
            'type' => 1,
            'posted_by' => $user_id,
            'timeliness' => $request->timeliness,
            'vehicle_condition' => $request->vehicle_condition,
            'safety' => $request->safety,
            'conscious' => $request->conscious,
            'comfort' => $request->comfort,
            'communication' => $request->communication,
            'attitude' => $request->attitude,
            'respect' => $request->respect,
            'hygiene' => $request->hygiene,
            'average_rating' => $formattedAverageRating,
        ]);

        // Set the review reply deadline based on the setting
        if ($setting->respond_review_days != '') {
            $deadline = now()->addDays($setting->respond_review_days);
            $rating->update([
                'reply_deadline' => $deadline,
            ]);
        }

        // Set the review live limit based on the setting
        if ($setting->leave_review_days != '') {
            $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
            $deadline = $rideDateTime->addDays($setting->leave_review_days);
            $rating->update([
                'status' => 0,
                'live_limit' => $deadline,
            ]);
        } else {
            $deadline = null; // If no setting, no deadline
            $rating->update([
                'status' => 1,
            ]);
        }

        $existing = Rating::where('type','2')->where('ride_id',$ride->id)->whereHas('booking', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->where('posted_by',$ride->added_by)->first();

        if ($existing) {
            $existing->update([
                'status' => 1,
                'live_limit' => null,
            ]);
            $rating->update([
                'status' => 1,
                'live_limit' => null,
            ]);
        }

        $data = ['first_name' => $user->first_name];
        Mail::to($user->email)->queue(new ReviewLeftMail($data));

        $data = ['first_name' => $ride->driver->first_name];
        Mail::to($ride->driver->email)->queue(new DriverReceivedReviewMail($data));

        $selectedLanguage = app()->getLocale();
        $message = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reviewed_driver_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reviewed_driver_message')->first();
            }
        }

        $data = ['rating' => $rating];

        if(isset($ride) && !empty($ride)){
            $checkUserReferral = ReferralDetail::where('user_id', $ride->added_by)->where('status', 'pending')->first();
            if(isset($checkUserReferral) && !empty($checkUserReferral)){
                $getBookingCount = Booking::where('ride_id', $ride->id)->where('status', '!=', '4')->where('status', '!=', '3')->count();
                $getRatingCount = Rating::where('ride_id', $ride->id)->where('type', '1')->count();
                if($getBookingCount == $getRatingCount){
                    $getRatingSum = Rating::where('ride_id', $ride->id)->where('type', '1')->sum('average_rating');
                    $averageRating = $getRatingSum / $getRatingCount;
                    if($averageRating >= 4){
                        $getRegistrationPoint = RegistrationRewardSetting::value('driver_reward_point');
                        $rewardPoint = new RewardPoint;
                        $rewardPoint->type = "driver";
                        $rewardPoint->user_id = $ride->added_by;
                        $rewardPoint->point = $getRegistrationPoint ?? 0; 
                        $rewardPoint->status = "pending";
                        $rewardPoint->save();
                    }

                    $getReferralSetting = ReferralSystemSetting::first();

                    $getReferralUser = User::where('id', $checkUserReferral->referral_user_id)->first();
                    if(isset($getReferralUser) && !empty($getReferralUser)){
                        if($getReferralUser->driver == 1){
                            $rewardPoint = new RewardPoint;
                            $rewardPoint->type = "driver";
                            $rewardPoint->user_id = $getReferralUser->id;
                            $rewardPoint->point = $getReferralSetting->d_2_d_rewad_point ?? 0; 
                            $rewardPoint->status = "pending";
                            $rewardPoint->save();
                        }else if($getReferralUser->student == 1){
                            $rewardPoint = new RewardPoint;
                            $rewardPoint->type = "student";
                            $rewardPoint->user_id = $getReferralUser->id;
                            $rewardPoint->point = $getReferralSetting->s_2_d_reward_point ?? 0; 
                            $rewardPoint->status = "pending";
                            $rewardPoint->save();
                        }else{
                            $topUpBalance = new TopUpBalance;
                            $topUpBalance->user_id = $getReferralUser->id;
                            $topUpBalance->dr_amount = $getReferralSetting->p_2_d_booking_credit ?? 0;
                            $topUpBalance->added_date = date('Y-m-d');
                            $topUpBalance->save();
                        }
                    }
                    
                    $checkUserReferral->status = "completed";
                    $checkUserReferral->save();
                }
            }
        }








        return $this->successResponse($data, strip_tags($message->reviewed_driver_message ?? null));
    }

    public function ReviewPassenger(Request $request){
        $booking = Booking::whereId($request->booking_id)->first();

        $data = ['passenger_profile_image' => $booking->passenger->profile_image, 'passenger_first_name' => $booking->passenger->first_name];
        return $this->successResponse($data, 'Get review a passenger page successfully');
    }

    public function StoreReviewPassenger(Request $request){

        $selectedLanguage = app()->getLocale();
        $message = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reviewed_passenger_message', 'general_error_message','block_review_rating_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reviewed_passenger_message', 'general_error_message','block_review_rating_message')->first();
            }
        }

        $user = Auth::guard('sanctum')->user();

        if ($user->block_review_rating == '1') {
            return $this->apiErrorResponse(strip_tags($message->block_review_rating_message ?? null), 200);
        }

        $booking = Booking::whereId($request->booking_id)->first();
        if ($booking) {
            $setting = ReviewSetting::first();
    
            $customMessages = [
                'required_without_all' => 'At least one of the ratings must be filled',
                'max_words' => 'The :attribute may not be greater than 500 words',
            ];
    
            $request->validate([
                'review' => 'required|string|max_words:500',
                'conscious' => ['required_without_all:comfort,communication,attitude,hygiene,respect,safety,timeliness'],
            ], $customMessages);
            
            // Initialize variables to store sum and count of non-null values
            $sum = 0;
            $count = 0;
            
            // Iterate over the columns and calculate sum and count of non-null values
            $columns = ['timeliness', 'safety', 'conscious', 'comfort', 'communication', 'attitude', 'respect', 'hygiene'];
            foreach ($columns as $column) {
                if ($request->$column == null || $request->$column == '0' || $request->$column == 0) {
                
                }else{
                    $sum += $request->$column;
                    $count += 1;
                }
            }
            
            // Calculate average rating
            $averageRating = $count > 0 ? $sum / $count : null;

            // Format average rating with one decimal place
            $formattedAverageRating = $averageRating !== null ? number_format($averageRating, 1) : null;

            $rating = Rating::create([
                'ride_id' => $booking->ride_id,
                'review' => $request->review,
                'type' => 2,
                'posted_to' => $booking->id,
                'posted_by' => $booking->ride->driver->id,
                'timeliness' => $request->timeliness,
                'safety' => $request->safety,
                'conscious' => $request->conscious,
                'comfort' => $request->comfort,
                'communication' => $request->communication,
                'attitude' => $request->attitude,
                'respect' => $request->respect,
                'hygiene' => $request->hygiene,
                'average_rating' => $formattedAverageRating,
            ]);
    
            // Set the review reply deadline based on the setting
            if ($setting->respond_review_days != '') {
                $deadline = now()->addDays($setting->respond_review_days);
                $rating->update([
                    'reply_deadline' => $deadline,
                ]);
            }
            
            // Set the review live limit based on the setting
            if ($setting->leave_review_days != '') {
                $rideDateTime = Carbon::parse($booking->ride->date . ' ' . $booking->ride->time);
                $deadline = $rideDateTime->addDays($setting->leave_review_days);
                $rating->update([
                    'status' => 0,
                    'live_limit' => $deadline,
                ]);
            } else {
                $deadline = null; // If no setting, no deadline
                $rating->update([
                    'status' => 1,
                ]);
            }
    
            $existing = Rating::where('type','1')->where('ride_id',$booking->ride_id)->where('posted_by',$booking->user_id)->first();
    
            if ($existing) {
                $existing->update([
                    'status' => 1,
                    'live_limit' => null,
                ]);
                $rating->update([
                    'status' => 1,
                    'live_limit' => null,
                ]);
            }
    
            $data = ['first_name' => $booking->ride->driver->first_name];
            Mail::to($booking->ride->driver->email)->queue(new ReviewLeftMail($data));
    
            $data = ['first_name' => $booking->passenger->first_name];
            Mail::to($booking->passenger->email)->queue(new PassengerReceivedReviewMail($data));
        
            $data = ['rating' => $rating];


            if(isset($booking) && !empty($booking)){
                $checkUserReferral = ReferralDetail::where('user_id', $booking->user_id)->where('status', 'pending')->first();
                if(isset($checkUserReferral) && !empty($checkUserReferral)){
                    $getUserDetail = User::where('id', $checkUserReferral->user_id)->first();
                    $averageRating = $rating->average_rating;
                        if($averageRating >= 4){
                            if(isset($getUserDetail) && isset($getUserDetail->student) && $getUserDetail->student == 1){
                                $getRegistrationPoint = RegistrationRewardSetting::value('student_reward_point');
                                $rewardPoint = new RewardPoint;
                                $rewardPoint->type = "student";
                                $rewardPoint->user_id = $checkUserReferral->user_id;
                                $rewardPoint->point = $getRegistrationPoint ?? 0; 
                                $rewardPoint->status = "pending";
                                $rewardPoint->save();
                            }else{
                                $getRegistrationPoint = RegistrationRewardSetting::value('passenger_credit_booking');
                                $topUpBalance = new TopUpBalance;
                                $topUpBalance->user_id = $checkUserReferral->user_id;
                                $topUpBalance->dr_amount = $getRegistrationPoint ?? 0;
                                $topUpBalance->added_date = date('Y-m-d');
                                $topUpBalance->save();
                            }
                            
                        }
    
                        $getReferralSetting = ReferralSystemSetting::first();
    
                        $getReferralUser = User::where('id', $checkUserReferral->referral_user_id)->first();
                        if(isset($getReferralUser) && !empty($getReferralUser)){
                            if($getReferralUser->driver == 1){
                                if(isset($getUserDetail) && isset($getUserDetail->student) && $getUserDetail->student == 1){
                                    $rewardPoint = new RewardPoint;
                                    $rewardPoint->type = "driver";
                                    $rewardPoint->user_id = $getReferralUser->id;
                                    $rewardPoint->point = $getReferralSetting->d_2_s_reward_point ?? 0; 
                                    $rewardPoint->status = "pending";
                                    $rewardPoint->save();
                                }else{
                                    $rewardPoint = new RewardPoint;
                                    $rewardPoint->type = "driver";
                                    $rewardPoint->user_id = $getReferralUser->id;
                                    $rewardPoint->point = $getReferralSetting->d_2_p_reward_point ?? 0; 
                                    $rewardPoint->status = "pending";
                                    $rewardPoint->save();
                                }                                
                            }else if($getReferralUser->student == 1){

                                if(isset($getUserDetail) && isset($getUserDetail->student) && $getUserDetail->student == 1){
                                    $rewardPoint = new RewardPoint;
                                    $rewardPoint->type = "student";
                                    $rewardPoint->user_id = $getReferralUser->id;
                                    $rewardPoint->point = $getReferralSetting->s_2_s_reward_point ?? 0; 
                                    $rewardPoint->status = "pending";
                                    $rewardPoint->save();
                                }else{
                                    $rewardPoint = new RewardPoint;
                                    $rewardPoint->type = "student";
                                    $rewardPoint->user_id = $getReferralUser->id;
                                    $rewardPoint->point = $getReferralSetting->s_2_p_reward_point ?? 0; 
                                    $rewardPoint->status = "pending";
                                    $rewardPoint->save();
                                }
                            }else{
                                if(isset($getUserDetail) && isset($getUserDetail->student) && $getUserDetail->student == 1){

                                    $topUpBalance = new TopUpBalance;
                                    $topUpBalance->user_id = $getReferralUser->id;
                                    $topUpBalance->dr_amount = $getReferralSetting->p_2_s_booking_credit ?? 0;
                                    $topUpBalance->added_date = date('Y-m-d');
                                    $topUpBalance->save();
                                }else{
                                    $topUpBalance = new TopUpBalance;
                                    $topUpBalance->user_id = $getReferralUser->id;
                                    $topUpBalance->dr_amount = $getReferralSetting->p_2_p_booking_credit ?? 0;
                                    $topUpBalance->added_date = date('Y-m-d');
                                    $topUpBalance->save();
                                }
                            }
                        }
                        
                        $checkUserReferral->status = "completed";
                        $checkUserReferral->save();
                }
            }


            return $this->successResponse($data, strip_tags($message->reviewed_passenger_message ?? null));
        }
        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? "Booking not found"), 404);
    }

    public function ReviewReplyStore(Request $request){

        $selectedLanguage = app()->getLocale();
            $message = null;
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                if ($selectedLanguage) {
                    // Retrieve the HomePageSettingDetail associated with the selected language
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reply_already_exist_message', 'general_error_message','block_review_rating_message')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reply_already_exist_message', 'general_error_message','block_review_rating_message')->first();
                }
            }

        $user = Auth::guard('sanctum')->user();

        if ($user->block_review_rating == '1') {
            return $this->apiErrorResponse(strip_tags($message->block_review_rating_message ?? null), 200);
        }

        $rating = Rating::whereId($request->rating_id)->first();

        if ($rating) {
            // Check if a ReviewReply already exists for this rating
            $reply = ReviewReply::where('rating_id', $rating->id)->first();

            if ($reply) {
                // If a reply already exists, return it
                $data = ['reply' => $reply];
                return $this->successResponse($data, strip_tags($message->reply_already_exist_message ?? 'Reply already exists'));
            } else {                
                $request->validate([
                    'reply' => 'required',
                ]);
        
                $reply = ReviewReply::create([
                    'rating_id' => $rating->id,
                    'reply' => $request->reply,
                ]);
        
                if ($reply) {
                    $rating->update([
                        'reply_deadline' => null,
                    ]);
                }
        
                $message = null;
                $selectedLanguage = app()->getLocale();
                if ($selectedLanguage) {
                    // Find the language by abbreviation
                    $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                    if ($selectedLanguage) {
                        // Retrieve the HomePageSettingDetail associated with the selected language
                        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('replied_message', 'general_error_message')->first();
                    }
                } else {
                    $selectedLanguage = Language::where('is_default', 1)->first();
                    if ($selectedLanguage) {
                        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('replied_message','general_error_message')->first();
                    }
                }
                
                $data = ['reply' => $reply];
                return $this->successResponse($data, strip_tags($message->replied_message));
            }
            
        }

        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? "Review not found"), 404);
    }

    public function allReviews(Request $request){
        $user_id = $request->user_id;

        if ($request->type === 'passenger') {
            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 2 and user_id belongs to the user
                $query->where('type', '2')
                      ->where('live_limit', null)
                      ->whereHas('booking', function ($query) use ($user_id) {
                          $query->where('user_id', $user_id);
                      });
            })
            ->with(['from' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])
            ->orderBy('id', 'desc')
            ->paginate($request->paginate_limit);
        } elseif ($request->type === 'driver') {
            $ratings = Rating::where(function ($query) use ($user_id) {
                // OR Ratings where type is 1 and ride_id belongs to the user
                $query->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '1')
                          ->where('live_limit', null)
                          ->whereHas('ride', function ($query) use ($user_id) {
                              $query->where('added_by', $user_id);
                          });
                });
            })
            ->with(['from' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])
            ->orderBy('id', 'desc')
            ->paginate($request->paginate_limit);
        } else {
            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 2 and user_id belongs to the user
                $query->where('type', '2')
                      ->whereHas('booking', function ($query) use ($user_id) {
                          $query->where('user_id', $user_id);
                      });
                // OR Ratings where type is 1 and ride_id belongs to the user
                $query->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '1')
                          ->whereHas('ride', function ($query) use ($user_id) {
                              $query->where('added_by', $user_id);
                          });
                });
            })
            ->with(['from' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])
            ->orderBy('id', 'desc')
            ->paginate($request->paginate_limit);
        }

        foreach ($ratings as $rating) {
            $reply = $rating->replies()->first();
            $rating->replies = $reply ? $reply : null;
            if ($rating->from->gender) {
                $rating->from->gender = ucfirst($rating->from->gender);
            }
        }

        $user = User::whereId($user_id)->first();

        $reviewSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $reviewSettingPage = ReviewSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $reviewSettingPage = ReviewSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['ratings' => $ratings, 'user' => $user,'reviewSettingPage' => $reviewSettingPage];
        return $this->successResponse($data, 'Get reviews successfully');
    }
}