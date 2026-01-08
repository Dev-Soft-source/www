<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Mail\DriverReceivedReviewMail;
use App\Mail\PassengerReceivedReviewMail;
use App\Mail\ReviewLeftMail;
use App\Models\Booking;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Rating;
use App\Models\ReferralDetail;
use App\Models\ReferralSystemSetting;
use App\Models\RegistrationRewardSetting;
use App\Models\ReviewReply;
use App\Models\ReviewSetting;
use App\Models\Ride;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\ReviewSettingDetail;
use App\Models\RewardPoint;
use App\Models\TopUpBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
    public function reviewIndex($lang, $id){
        $rating = Rating::whereId($id)->with(['from' => function ($query) {
            $query->withTrashed(); // Include soft-deleted users
        }])->first();

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
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
        return view('review',['rating' => $rating, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
    }

    public function passengerReviewIndex($lang, $id){
        $rating = Rating::whereId($id)->with(['from' => function ($query) {
            $query->withTrashed(); // Include soft-deleted users
        }])->first();

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
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
        return view('passenger_review',['rating' => $rating, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
    }

    public function reviewLeftIndex($lang, $id){
        $rating = Rating::whereId($id)->first();

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
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
        return view('review_left',['rating' => $rating, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
    }

    public function passengerLeftReviewIndex($lang, $id){
        $rating = Rating::whereId($id)->first();

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
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
        return view('passenger_review_left',['rating' => $rating, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
    }

    public function ReviewPassenger($lang, $id){
        $booking = Booking::where('uuid', $id)->first();
        if ($booking) {
            $languages = Language::all();
            // Store the selected language in the session
            if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
                session(['selectedLanguage' => $lang]);
            }
            $selectedLanguage = session('selectedLanguage');
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
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

            $existingRating = Rating::where('type', 2)->where('posted_to', $booking->id)->where('posted_by', $booking->ride->driver->id)->first();
            if ($existingRating) {
                return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'Already reviewed']);
            }

            return view('review_passenger',['booking' => $booking, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
        }
        return view('errors/404');
    }

    public function StoreReviewPassenger($id, Request $request){
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reviewed_passenger_message', 'general_error_message','block_review_rating_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reviewed_passenger_message', 'general_error_message','block_review_rating_message')->first();
        }

        $user = auth()->user();

        if ($user->block_review_rating == '1') {
            return $this->apiErrorResponse($message->block_review_rating_message ?? null, 200);
        }

        $booking = Booking::whereId($id)->first();
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

        if ($booking->ride->driver->email_notification == 1) {
        $data = ['first_name' => $booking->ride->driver->first_name];
        Mail::to($booking->ride->driver->email)->queue(new ReviewLeftMail($data));
        }

        if ($booking->passenger->email_notification == 1) {
        $data = ['first_name' => $booking->passenger->first_name];
        Mail::to($booking->passenger->email)->queue(new PassengerReceivedReviewMail($data));
        }
        
        if(isset($booking) && !empty($booking)){
            $checkUserReferral = ReferralDetail::where('user_id', $booking->user_id)->where('status', 'pending')->first();
            if(isset($checkUserReferral) && !empty($checkUserReferral)){
                $getUserDetail = User::where('id', $checkUserReferral->user_id)->first();
                $averageRating = $rating->average_rating;
                    if($averageRating >= 4){
                        if(isset($getUserDetail) && isset($getUserDetail->student) && $getUserDetail->student == 1){
                            $getRegistrationPoint = RegistrationRewardSetting::value('student_reward_point');
                            $rewardPoint = new RewardPoint();
                            $rewardPoint->type = "student";
                            $rewardPoint->user_id = $checkUserReferral->user_id;
                            $rewardPoint->point = $getRegistrationPoint ?? 0; 
                            $rewardPoint->status = "pending";
                            $rewardPoint->save();
                        }else{
                            $getRegistrationPoint = RegistrationRewardSetting::value('passenger_credit_booking');
                            $topUpBalance = new TopUpBalance();
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
        
        if (auth()->user()) {
            return redirect()->route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id]);
        }
        return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'Reviewed successfully']);
    }

    public function ReviewDriver($lang, $id){
        $booking = Booking::where('uuid', $id)->first();
        if ($booking) {
            $ride = Ride::whereId($booking->ride_id)->first();
    
            $languages = Language::all();
            // Store the selected language in the session
            if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
                session(['selectedLanguage' => $lang]);
            }
            $selectedLanguage = session('selectedLanguage');
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
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

            $existingRating = Rating::where('ride_id', $booking->ride_id)->where('type', 1)->where('posted_by', $booking->user_id)->first();
            if ($existingRating) {
                return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'Already reviewed']);
            }

            return view('review_driver',['booking' => $booking, 'ride' => $ride, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
        }
        return view('errors/404');
    }

    public function StoreReviewDriver($id, Request $request){
        $booking = Booking::whereId($id)->first();
        $user_id = $booking->user_id;
        $ride = Ride::whereId($booking->ride_id)->first();
        $setting = ReviewSetting::first();
        
        $customMessages = [
            'required_without_all' => 'At least one of the ratings must be filled',
            'max_words' => 'The :attribute may not be greater than 500 words',
        ];
        
        $request->validate([
            'review' => 'required|string|max_words:500',
            'conscious' => ['required_without_all:vehicle_condition,comfort,communication,attitude,hygiene,respect,safety,timeliness'],
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

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        $data = ['first_name' => $booking->passenger->first_name];
        Mail::to($booking->passenger->email)->queue(new ReviewLeftMail($data));

        $data = ['first_name' => $ride->driver->first_name];
        if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {
        Mail::to($ride->driver->email)->queue(new DriverReceivedReviewMail($data));
        }

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
        
        if (auth()->user()) {
            return redirect()->route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $ride->id]);
        }
        return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'Reviewed successfully']);
    }

    public function ReviewReply($lang, $id){
        $rating = Rating::whereId($id)->first();
        $reviewSettingPage= null;

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $reviewSettingPage = ReviewSettingDetail::where('language_id', $selectedLanguage->id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $reviewSettingPage = ReviewSettingDetail::where('language_id', $selectedLanguage->id)->first();
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
        return view('review_reply', ['rating' => $rating, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'reviewSettingPage' => $reviewSettingPage]);
    }

    public function ReviewReplyStore($id, Request $request){
        $message = null;
        $reviewSettingPage = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('replied_message', 'block_review_rating_message')->first();

            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('replied_message', 'block_review_rating_message')->first();
            }
        }

        $user = auth()->user();

        if ($user->block_review_rating == '1') {
            return $this->apiErrorResponse($message->block_review_rating_message ?? null, 200);
        }

        $rating = Rating::whereId($id)->first();
        
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
        return redirect()->route('ratings.received', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $message->replied_message]);
    }
}
