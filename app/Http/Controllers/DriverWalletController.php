<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\MyReviewSettingDetail;
use App\Models\Notification;
use App\Models\Payout;
use App\Models\MyWalletSettingDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\RewardPoint;
use App\Models\RewardPointSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Support\Facades\DB;

class DriverWalletController extends Controller
{
    public function pending($lang = null){
        $languages = Language::all();

        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }

        $ProfilePage = null;
        $ProfileSetting = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
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

        $currentDate = date('Y-m-d H:i:s');
        $getAvailableBalance = Payout::with(['ride:id,destination,departure,added_by,completed_date,completed_time,random_id','ride.defaultRideDetail:id,ride_id,destination,departure,price', 'ride.bookings:id,ride_id,user_id,ride_detail_id,destination,departure,price', 'ride.bookings.booking_transaction_sum', 'ride.bookings.booking_cancel_transaction_sum', 'ride.bookings.booking_credit_sum', 'ride.bookings.booking_credit_cancel_sum', 'ride.bookings.passenger', 'driver'])
            ->select('ride_id', DB::raw('SUM(amount) as total_payout_cost'))
            ->where('user_id', $user_id)
            ->where('status', 'pending')
            ->where('available_date', '>=', $currentDate)
            ->groupBy('ride_id')
            ->get();

        return view('driver_wallet_pending',['reviewSetting' => $reviewSetting, 'walletSettingPage' => $walletSettingPage, 'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'getAvailableBalance' => $getAvailableBalance,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function reward($lang = null){
        $walletSettingPage = null;

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
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

        $rewardPointSettings = RewardPointSettingDetail::whereHas('rewardPointSetting', function ($query) {
            $query->where('type', 'driver');
        })->with('rewardPointSetting')->where('language_id', $selectedLanguage->id)->get();

        $driverTotalRewardPoint = RewardPoint::where('type', 'driver')->where('user_id', $user_id)->where('status', 'pending')->sum('point');

        return view('driver_wallet_rewards',['reviewSetting' => $reviewSetting,'rewardPointSettings' => $rewardPointSettings,'driverTotalRewardPoint' => $driverTotalRewardPoint,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage, 'walletSettingPage' => $walletSettingPage,'ProfileSetting' => $ProfileSetting,'ProfilePage' => $ProfilePage]);
    }

    public function available($lang = null){
        $walletSettingPage = null;

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
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

        $currentDate = date('Y-m-d H:i:s');
        $getAvailableBalance = Payout::with(['ride:id,destination,departure,added_by,completed_date,completed_time,random_id','ride.defaultRideDetail:id,ride_id,destination,departure,price', 'ride.bookings:id,ride_id,user_id,ride_detail_id,destination,departure,price', 'ride.bookings.booking_transaction_sum', 'ride.bookings.booking_cancel_transaction_sum', 'ride.bookings.booking_credit_sum', 'ride.bookings.booking_credit_cancel_sum', 'ride.bookings.passenger', 'driver'])
            ->select('ride_id' ,DB::raw('MAX(status) as status') ,DB::raw('SUM(amount) as total_payout_cost'))
            ->where('user_id', $user_id)
            ->whereIn('status', ['pending', 'request'])
            ->where('available_date', '<=', $currentDate)
            ->groupBy('ride_id')
            ->get();

        return view('driver_wallet_available',['reviewSetting' => $reviewSetting,'messages' => $messages,'walletSettingPage' => $walletSettingPage,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'getAvailableBalance' => $getAvailableBalance,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function paid($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        
        $ProfilePage = null;
        $ProfileSetting = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
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

        $getPaidout = Payout::with(['ride:id,destination,departure,added_by,completed_date,random_id','ride.defaultRideDetail:id,ride_id,destination,departure,price', 'ride.bookings:id,ride_id,user_id,ride_detail_id,destination,departure,price', 'ride.bookings.booking_transaction_sum', 'ride.bookings.booking_cancel_transaction_sum', 'ride.bookings.booking_credit_sum', 'ride.bookings.booking_credit_cancel_sum', 'ride.bookings.passenger', 'driver'])
            ->select('ride_id','paid_out_date','user_id', DB::raw('SUM(amount) as total_payout_cost'))
            ->where('user_id', $user_id)
            ->where('status', 'completed')
            ->groupBy('ride_id', 'paid_out_date', 'user_id')
            ->get();

        return view('driver_wallet_paid',['reviewSetting' => $reviewSetting,'walletSettingPage' => $walletSettingPage,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'getAvailableBalance' => $getPaidout,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function detail($lang = null, $id){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        $reviewSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
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

        $getPaidout = Payout::with(['ride:id,destination,departure,added_by,random_id','ride.defaultRideDetail:id,ride_id,destination,departure,price', 'ride.bookings:id,ride_id,user_id,ride_detail_id,destination,departure,price,status', 'ride.bookings.booking_transaction_sum', 'ride.bookings.booking_cancel_transaction_sum', 'ride.bookings.booking_credit_sum', 'ride.bookings.booking_credit_cancel_sum', 'ride.bookings.passenger', 'driver'])
            ->select('ride_id', DB::raw('SUM(amount) as total_payout_cost'))
            ->where('user_id', $user_id)
            ->where('ride_id', $id)
            ->groupBy('ride_id')
            ->get();

        return view('ride_fair_detail',['getAvailableBalance' => $getPaidout,'reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function sendPayoutRequest($lang = null){
        $user = auth()->user();
        $user_id = $user->id;
        
        $currentDate = date('Y-m-d H:i:s');
        
        $getPayouts = Payout::where('user_id', $user_id)->where('status', 'pending')
        ->where('available_date', '<=', $currentDate)->get();

        foreach ($getPayouts as $key => $getPayout) {
            $getPayout->status = "request";
            $getPayout->save();
        }

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('payout_request_success_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('payout_request_success_message')->first();
        }

        return redirect()->route('driver_wallet_available', ['lang' => $selectedLanguage->abbreviation])->with(['message' => $messages->payout_request_success_message ?? 'Payout request send successfully to admin']);
    }
}
