<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\InvitingDriverToReviewMail;
use App\Mail\InvitingPassengerToReviewMail;
use App\Models\Booking;
use App\Models\CoffeeWallet;
use App\Models\Language;
use App\Models\Ride;
use App\Models\SiteSetting;
use App\Models\Transaction;
use App\Models\Payout;
use App\Models\City;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\Message;
use App\Models\TopUpBalance;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;

class RideCompleteCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ride-complete:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $getAllRides = Ride::where('status','0')->where(function ($query) {
            $query->where(function ($query) {
                $query->whereDate('completed_date', '<', now()->toDateString())
                    ->orWhere(function ($query) {
                        $query->whereDate('completed_date', '=', now()->toDateString())
                            ->whereTime('completed_time', '<', now()->toTimeString());
                    });
            });
        })->get();

        $defaultLang = Language::where('is_default', 1)->first();

        foreach ($getAllRides as $key => $getRide) {

            $getRide->status = '3';
            $getRide->save();
            $getBookings = Booking::with('passenger')->where('ride_id', $getRide->id)->where('status', '!=', '4')
            ->where('status', '!=', '3')->get();

            $getSetting = SiteSetting::first();
            foreach ($getBookings as $key => $booking) {
                //Get Booked and Cancelled Ride Cost
                $getRideCost = Transaction::where('booking_id', $booking->id)->where('type', '1')->sum('price');
                $getRideCancelCost = Transaction::where('booking_id', $booking->id)->where('type', '3')->sum('price');

                //Get Book and Cancelled Ride Booking Fee
                $getRideBookingFee = Transaction::where('booking_id', $booking->id)->where('type', '1')->sum('booking_fee');
                $getRideCancelBookingFee = Transaction::where('booking_id', $booking->id)->where('type', '3')->sum('booking_fee');

                $payoutAmt = 0;


                if(isset($getSetting->booking_fee_give_to_driver) && $getSetting->booking_fee_give_to_driver == 1){
                    $payoutAmt = ($getRideCost) - ($getRideCancelCost + $getRideCancelBookingFee);
                }else{
                    $payoutAmt = ($getRideCost - $getRideCancelCost) - ($getRideBookingFee - $getRideCancelBookingFee);
                }


                $deduct_tax = $tax_type = "";
                $tax = 0;
                $taxAmt = 0;


                if(isset($getSetting) && !empty($getSetting)){
                    if(isset($getSetting->deduct_tax) && $getSetting->deduct_tax == "deduct_from_driver"){
                        $deduct_tax = $getSetting->deduct_tax;
                        $tax_type = $getSetting->tax_type;
                        if(isset($getSetting->tax_type) && $getSetting->tax_type == "state_wise_tax"){
                            $locationBeforeComma = explode(',', $booking->departure);
                            $getFromState = City::with('state:id,tax')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ',['%'.$locationBeforeComma[0].'%'])->first();
                            if(isset($getFromState) && !empty($getFromState)){
                                $tax = $getFromState->state->tax;
                            }
                        }else{
                            $tax = $getSetting->tax;
                        }

                        $taxAmt = round((($payoutAmt * $tax) / 100), 2);
                        $payoutAmt = $payoutAmt - $taxAmt;

                    }
                }


                

                if($payoutAmt > 0){
                    //Add Payout Data

                    $getPayout = Payout::where('ride_id', $getRide->id)->where('booking_id', $booking->id)->first();
                    if(isset($getPayout) && !is_null($getPayout)){

                    }else{
                        $getPayout = new Payout();
                    }

                    if(isset($getPayout->amount)){
                        $payoutAmt = $getPayout->amount + $payoutAmt;
                    }

                    $rideDateTime = Carbon::parse("$getRide->completed_date $getRide->completed_time");

                    $getPayout->ride_id = $booking->ride_id;
                    $getPayout->booking_id = $booking->id;
                    $getPayout->user_id = $getRide->added_by;
                    $getPayout->amount = $payoutAmt;
                    $getPayout->available_date = $rideDateTime;
                    $getPayout->status = "pending";
                    $getPayout->tax_amount = $taxAmt;
                    $getPayout->tax_percentage = isset($tax) && $tax != 0 ? $tax : 0;
                    $getPayout->tax_type = isset($tax_type) && $tax_type != "" ? $tax_type : NULL;
                    $getPayout->deduct_type = isset($deduct_tax) && $deduct_tax != "" ? $deduct_tax : NULL;

                    $getPayout->save();
                }


                
                if(isset($getSetting->booking_fee_give_to_student) && $getSetting->booking_fee_give_to_student == 1){
                    if($booking->passenger->student == 1 && Carbon::parse($booking->passenger->student_card_exp_date) > now()){

                        

                        $bookingFee = round(($booking->booking_credit / $booking->seats), 2);

                        $getTransactionDetails = Transaction::where('booking_id', $booking->id)->where('type', '1')->where('coffee_from_wall', '0')->get();
                        foreach ($getTransactionDetails as $key => $getTransactionDetail) {
                            if(isset($getTransactionDetail->booking_fee) && $getTransactionDetail->booking_fee != 0){

                                $refundId = "";

                                if($getTransactionDetail->pay_by_account == 0){
                                    if ($getTransactionDetail->paypal_id) {
                                        try {
                                            $uniqueId = strtotime(date('Y-m-d H:i:s'));
                                            $paypal = new PayPalClient;
                                            $paypal->setApiCredentials(config('paypal'));
                                            $token = $paypal->getAccessToken();
                                            $paypal->setAccessToken($token);
                                            $response = $paypal->refundCapturedPayment(
                                                $getTransactionDetail->paypal_id,
                                                'USD',
                                                $bookingFee,
                                                'Invoice-' . $uniqueId,
                                            );

                                            $refundId = isset($response['id']) ? $response['id'] : "";

                                        } catch (\PayPal\Exception\PayPalConnectionException $e) {
                                            $errorData = json_decode($e->getData(), true);
                                            Log::error("PayPal error: " . $errorData['message']);
                                        }


                                    } elseif ($getTransactionDetail->stripe_id) {
                                        // Set your Stripe API key
                                        Stripe::setApiKey(env('STRIPE_SECRET'));

                                        try {
                                            // Create a refund using the payment intent ID
                                            $refund = Refund::create([
                                                'payment_intent' => $getTransactionDetail->stripe_id,
                                                'amount' => $bookingFee * 100, // Refund amount in cents
                                            ]);

                                            $refundId = $refund->id;

                                        } catch (\Stripe\Exception\ApiErrorException $e) {

                                        }
                                    }
                                }else{
                                    $topUpBalance = TopUpBalance::create([
                                        'booking_id' => $getTransactionDetail->booking_id,
                                        'user_id' => $booking->user_id,
                                        'dr_amount' => $bookingFee,
                                        'added_date' => date('Y-m-d'),
                                    ]);
                                }
                                $passengerTransaction = Transaction::create([
                                    'booking_id' => $getTransactionDetail->booking_id,
                                    'ride_id' => $booking->ride_id,
                                    'parent_id' => $getTransactionDetail->id,
                                    'type' => '3',
                                    'booking_fee' => $bookingFee,
                                    'price' => '0',
                                    'paypal_id' => isset($getTransactionDetail->paypal_id) ? $refundId : NULL,
                                    'stripe_id' => isset($getTransactionDetail->stripe_id) ? $refundId : NULL
                                ]);
                            }
                        }

                        $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->where('coffee_from_wall', '1')->first();
                        if(isset($transactions) && !empty($transactions)){
                            foreach ($transactions as $key => $transaction) {
                                $coffeeWallet = CoffeeWallet::create([
                                    'booking_id' => $booking->id,
                                    'ride_id' => $booking->ride_id,
                                    'user_id' => $booking->user_id,
                                    'dr_amount' => $bookingFee,
                                ]);
                            }
                        }
                    }
                }

                $uniqueUserId = (string) Str::uuid();
                $booking->update([
                    'uuid' => $uniqueUserId,
                ]);
                if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {

                // Send review  mail to passengers
                $data = ['first_name' => $booking->passenger->first_name, 'uuid' => $uniqueUserId, 'abbreviation' => $defaultLang->abbreviation];
                Mail::to($booking->passenger->email)->queue(new InvitingPassengerToReviewMail($data));
                }
                $notification = Notification::create([
                    'type' => 2,
                    'ride_id' => $getRide->id,
                    'posted_to' => $booking->id,
                    'posted_by' => $getRide->added_by,
                    // 'message' =>  'Review your driver',
                    'message' =>  'How did your ride go?',
                    'status' => 'completed',
                    'notification_type' => 'review',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);

                $fcmToken = $booking->passenger->mobile_fcm_token;
                $body = $notification->message;

                if ($fcmToken) {
                    $fcmService = new FCMService();
                    // Send the booking notification
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

                $notification = Notification::create([
                    'ride_id' => $getRide->id,
                    'posted_by' => $booking->user_id,
                    // 'message' =>  'Review your passenger',
                    'message' =>  'How did your ride go?',
                    'status' => 'completed',
                    'notification_type' => 'review',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);

                $fcmToken = $getRide->driver->mobile_fcm_token;
                $body = $notification->message;

                if ($fcmToken) {
                    $fcmService = new FCMService();
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }

                $fcm_tokens = FCMToken::where('user_id', $getRide->added_by)->get();

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                    }
                }
            }
            if (isset($getRide->driver->email_notification) && $getRide->driver->email_notification == 1) {

            // Send review  mail to driver
            $data = ['first_name' => $getRide->driver->first_name, 'getBookings' => $getBookings, 'abbreviation' => $defaultLang->abbreviation];
            Mail::to($getRide->driver->email)->queue(new InvitingDriverToReviewMail($data));
            }
            $getNotifications = Notification::where('is_delete', '0')->where('ride_id', $getRide->id)->get();
            foreach ($getNotifications as $key => $getNotification) {
                $getNotification->is_delete = '1';
                $getNotification->save();
            }

            $getMessages = Message::where('ride_id', $getRide->id)->where('status', 'new')->get();
            foreach ($getMessages as $key => $getMessage) {
                $getMessage->status = 'old';
                $getMessage->save();
            }
        }

        Log::info("Success");
    }
}
