<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\ClaimRewardMail;
use App\Mail\TopUpReceiptMail;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\Payout;
use App\Models\TopUpBalance;
use App\Models\Card;
use App\Models\MyWalletSettingDetail;
use App\Models\RewardPointSetting;
use App\Models\RewardPointSettingDetail;
use App\Models\RewardPoint;
use App\Models\ClaimReward;
use App\Models\Language;
use App\Models\SuccessMessagesSettingDetail;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Stripe\Stripe;

class WalletController extends Controller
{
    use StatusResponser;

    public function passengerMyRides(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $currentDate = date('Y-m-d H:i:s');
        
        $myRides = Booking::where('user_id', $user_id)->select('id', 'ride_id' , 'seats', 'status', 'booking_credit', 'fare', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
            ->where('status', '!=', '4')
            ->whereHas('ride', function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<=', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<=', now()->toTimeString());
                        });
                })
                ->whereHas('driver', function ($query) {
                    $query->whereNull('deleted_at'); // Exclude soft-deleted drivers
                });
            })
            ->with(['ride' => function ($query) {
                $query->with(['driver' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
                }]);
            }])
            ->with(['booking_transaction_sum', 'booking_cancel_transaction_sum', 'booking_credit_sum', 'booking_credit_cancel_sum'])
            ->orderBy('ride_id', 'desc')
            ->get();

        $walletSettingPage = null;
        $messages = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $walletSettingPage = MyWalletSettingDetail::where('language_id', $request->lang_id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('withdraw_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('withdraw_message')->first();
            }
        }

        $getCrBalance = TopUpBalance::where('user_id', $user_id)->sum('cr_amount');
        $getDrBalance = TopUpBalance::where('user_id', $user_id)->sum('dr_amount');
        $data = ['myRides' => $myRides, 'messages' => $messages, 'balance' => ($getDrBalance - $getCrBalance), 'walletSettingPage' => $walletSettingPage];
        return $this->successResponse($data, 'Get my rides successfully');
    }



    public function studentRewardPoints(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $currentDate = date('Y-m-d H:i:s');

        $langId = $request->lang_id;
        
        $rewardPointSettings = RewardPointSettingDetail::whereHas('rewardPointSetting', function ($query) {
            $query->where('type', 'student');
        })->with('rewardPointSetting')->where('language_id', $langId)->get();

        $studentTotalRewardPoint = RewardPoint::where('type', 'student')->where('user_id', $user_id)->where('status', 'pending')->sum('point');
        

        $data = ['rewardPointSettings' => $rewardPointSettings, 'studentTotalRewardPoint' => $studentTotalRewardPoint];
        return $this->successResponse($data, 'Get reward point setting successfully');
    }


    public function driverPaidout(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $currentDate = date('Y-m-d H:i:s');
        
        $getPaidout = Payout::with(['ride:id,destination,departure,added_by,random_id','ride.defaultRideDetail:id,ride_id,departure,destination,price', 'ride.bookings:id,ride_id,user_id', 'ride.bookings.booking_transaction_sum', 'ride.bookings.booking_cancel_transaction_sum', 'ride.bookings.booking_credit_sum', 'ride.bookings.booking_credit_cancel_sum', 'ride.bookings.passenger', 'driver'])
        ->select('ride_id','paid_out_date','user_id', DB::raw('SUM(amount) as total_payout_cost'))
        ->where('user_id', $user_id)
        ->where('status', 'completed')
        ->groupBy('ride_id', 'paid_out_date', 'user_id')
        ->get();
        

        $data = ['getPaidout' => $getPaidout];
        return $this->successResponse($data, 'Get paid out successfully');
    }

    public function driverAvailableBalance(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $currentDate = date('Y-m-d H:i:s');
        
        $getAvailableBalance = Payout::with(['ride:id,added_by,completed_date,completed_time,random_id','ride.defaultRideDetail:id,ride_id,departure,destination,price', 'ride.bookings:id,ride_id,user_id', 'ride.bookings.booking_transaction_sum', 'ride.bookings.booking_cancel_transaction_sum', 'ride.bookings.booking_credit_sum', 'ride.bookings.booking_credit_cancel_sum', 'ride.bookings.passenger', 'driver'])
        ->select('ride_id', DB::raw('MAX(status) as status') ,DB::raw('SUM(amount) as total_payout_cost'))
        ->where('user_id', $user_id)
        ->whereIn('status', ['pending', 'request'])
        ->where('available_date', '<=', $currentDate)
        ->groupBy('ride_id')
        ->get();

        $data = ['getAvailableBalance' => $getAvailableBalance];
        return $this->successResponse($data, 'Get driver balance successfully');
    }

    public function driverPendingBalance(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $currentDate = date('Y-m-d H:i:s');
        
        $getAvailableBalance = Payout::with(['ride:id,destination,departure,added_by,completed_date,completed_time,random_id','ride.defaultRideDetail:id,ride_id,departure,destination,price', 'ride.bookings:id,ride_id,user_id', 'ride.bookings.booking_transaction_sum', 'ride.bookings.booking_cancel_transaction_sum', 'ride.bookings.booking_credit_sum', 'ride.bookings.booking_credit_cancel_sum', 'ride.bookings.passenger', 'driver'])
        ->select('ride_id', DB::raw('SUM(amount) as total_payout_cost'))
        ->where('user_id', $user_id)
        ->where('status', 'pending')
        ->where('available_date', '>=', $currentDate)
        ->groupBy('ride_id')
        ->get();

        $data = ['getAvailableBalance' => $getAvailableBalance];
        return $this->successResponse($data, 'Get driver balance successfully');
    }    


    public function driverRewardPoints(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $currentDate = date('Y-m-d H:i:s');

        $langId = $request->lang_id;
        
        $rewardPointSettings = RewardPointSettingDetail::whereHas('rewardPointSetting', function ($query) {
            $query->where('type', 'driver');
        })->with('rewardPointSetting')->where('language_id', $langId)->get();

        $driverTotalRewardPoint = RewardPoint::where('type', 'driver')->where('user_id', $user_id)->where('status', 'pending')->sum('point');
        

        $data = ['rewardPointSettings' => $rewardPointSettings, 'driverTotalRewardPoint' => $driverTotalRewardPoint];
        return $this->successResponse($data, 'Get reward point setting successfully');
    }

    public function sendPayoutRequest(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        
        $currentDate = date('Y-m-d H:i:s');
        
        $getPayouts = Payout::where('user_id', $user_id)->where('status', 'pending')
        ->where('available_date', '<=', $currentDate)->get();

        foreach ($getPayouts as $key => $getPayout) {
            $getPayout->status = "request";
            $getPayout->save();
        }

        $selectedLanguage = app()->getLocale() ?? 'en';
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('payout_request_success_message')->first();

        $data = [];
        return $this->successResponse($data, strip_tags($message->payout_request_success_message) ?? 'Payout request send successfully to admin');
    }


    public function getTopUpBalance(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $currentDate = date('Y-m-d H:i:s');
        
        $topUpBalances = TopUpBalance::with(['booking:id,user_id', 'user:id,first_name,last_name'])
        ->where('user_id', $user_id)
        ->get();

        $data = ['topUpBalances' => $topUpBalances];
        return $this->successResponse($data, 'Get top up balance successfully');
    }

    public function storeTopUpBalance(Request $request){
        
        $selectedLanguage = app()->getLocale() ?? 'en';

        
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message', 'topup_balance_success_message','card_expiry_message')->first();

        $validated = $request->validate([
            'payment_method' => 'required',
            'paypal_id' => $request->payment_method == 'paypal' ? 'required' : 'nullable',
            'card_id' => $request->payment_method == 'credit_card' ? 'required' : 'nullable',
            'dr_amount' => 'required',
        ]);

        $user = Auth::guard('sanctum')->user();

        if ($request->payment_method == 'paypal') {
        
            // Payment successful, handle Top Up  Balance logic here
            $storeTopUpBalance = TopUpBalance::create([
                'user_id' => $user->id,
                'dr_amount' => $request->dr_amount,
                'paypal_id' => $request->gPay == "0" ? $request->paypal_id : NULL,
                'stripe_id' => $request->gPay == "0" ? NULL : $request->paypal_id,
                'added_date' => Carbon::now(),
            ]);

            $data = [
                'full_name' => $user->first_name.' '.$user->last_name,
                'amount' => $request->dr_amount,
                'transaction_id' => $storeTopUpBalance->random_id, // Use the random_id from top_up_balances
                'transaction_date' => Carbon::now()->format('F j, Y'),
                'payment_method' => 'paypal',
                'paypal_email' => $user->email, // Make sure this field exists in your users table
            ];
            
            if (isset($user->email_notification) && $user->email_notification == 1) {
                Mail::to($user->email)->send(new TopUpReceiptMail($data));
            }
    
            $data = ['topUpBalance' => $storeTopUpBalance];
            return $this->successResponse($data, strip_tags($message->topup_balance_success_message) ?? 'You have successfully buy top up balance');
        } elseif ($request->payment_method == 'credit_card') {
            $card = Card::where('id', $request->card_id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            Stripe::setApiKey(env('STRIPE_SECRET'));

            try {
                $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                $paymentIntent = PaymentIntent::create([
                    'amount' => $request->input('dr_amount') * 100,
                    'currency' => 'usd',
                    'customer' => $user->stripe_customer_id,
                    'payment_method' => $paymentMethod->id,
                    'off_session' => true,
                    'confirm' => true,
                ]);

                // Payment successful, handle booking logic here
                $storeTopUpBalance = TopUpBalance::create([
                    'user_id' => $user->id,
                    'dr_amount' => $request->dr_amount,
                    'stripe_id' => $request->stripe_id,
                    'added_date' => Carbon::now(),
                ]);

                $data = [
                    'full_name' => $user->first_name.' '.$user->last_name,
                    'amount' => $request->dr_amount,
                    'transaction_id' => $storeTopUpBalance->random_id, 
                    'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                    'payment_method' => 'credit_card',
                    'card_type' => isset($request->gPayApplePayId) && $request->gPayApplePayId != '0' ? 'Gpay/ApplePay' : $card->card_type, 
                ];
                
                if (isset($user->email_notification) && $user->email_notification == 1) {
                    Mail::to($user->email)->send(new TopUpReceiptMail($data));
                }

                $data = ['topUpBalance' => $storeTopUpBalance];
                return $this->successResponse($data, strip_tags($message->topup_balance_success_message) ?? 'You have successfully buy top up balance');
            } catch (\Stripe\Exception\CardException $e) {
                // Handle Stripe card-related errors
                if ($e->getError()->code === 'card_declined' && $e->getError()->decline_code === 'expired_card') {
                    return $this->apiErrorResponse(strip_tags($message->card_expiry_message) ?? 'The card has expired. Please use a different card', 200);
                }
            
                // General Stripe card-related error message
                return $this->apiErrorResponse($e->getMessage(), 200);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Handle error
                return $this->apiErrorResponse($e->getMessage(), 200);
            }
        }

        return $this->apiErrorResponse(strip_tags($message->general_error_message) ?? "top up not found", 404);
    }

    public function claimMyReward(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;


        $selectedLanguage = app()->getLocale() ?? 'en';
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('reward_not_found_message', 'claim_reward_student_success_message', 'claim_reward_driver_success_message')->first();
        
        $currentDate = date('Y-m-d H:i:s');
        
        $studentTotalRewardPoint = RewardPoint::where('type', $request->type)->where('user_id', $user_id)->where('status', 'pending')->sum('point');

        $checkStudentRewardSetting = RewardPointSetting::where('point', '<=', $studentTotalRewardPoint)->where('type', $request->type)->orderBy('point', 'desc')->first();

        if(isset($checkStudentRewardSetting) && !is_null($checkStudentRewardSetting)){

            $getClaimReward = ClaimReward::where('status', 'request')->where('type', $request->type)->where('user_id',$user_id)->first();
            if(isset($getClaimReward) && !empty($getClaimReward)){

            }else{
                $claimReward = new ClaimReward;
                $claimReward->reward_point_setting_id = $checkStudentRewardSetting->id;
                $claimReward->user_id = $user_id;
                $claimReward->type = $request->type;
                $claimReward->point = $checkStudentRewardSetting->point;
                $claimReward->request_date = date('Y-m-d');
                $claimReward->status = 'request';
                $claimReward->save();
            }
            
        }else{
            return $this->apiErrorResponse(strip_tags($message->reward_not_found_message) ?? "Please try later no reward found", 200);
        }

        
        $totalRewardPoint = RewardPoint::where('type', $request->type)->where('user_id', $user_id)->where('status', 'pending')->sum('point');

        $admin = Admin::first();

        $data = ['username' => $admin->username];
        Mail::to($admin->admin_email)->queue(new ClaimRewardMail($data));

        if($request->type == "student"){
            $data = ['studentTotalRewardPoint' => $totalRewardPoint];
            return $this->successResponse($data, strip_tags($message->claim_reward_student_success_message) ?? 'Student claim reward request send successfully');
        }else{
            $data = ['driverTotalRewardPoint' => $totalRewardPoint];
            return $this->successResponse($data, strip_tags($message->claim_reward_driver_success_message) ?? 'Driver claim reward request send successfully');
        }
        
    }

    
}
