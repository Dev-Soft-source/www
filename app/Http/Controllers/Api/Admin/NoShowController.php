<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\NoShowHistoryResource;
use App\Mail\SuspendDriverMail;
use App\Models\Booking;
use App\Models\CancellationHistory;
use App\Models\CoffeeWallet;
use App\Models\NoShowHistory;
use App\Models\Ride;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;

class NoShowController extends Controller
{
    use StatusResponser;

    public function index(Request $request)
    {
        try {
            if ($request->input('type') == 'passengers') {
                $no_shows = NoShowHistory::with(['ride.rideDetail', 'user'])->where('type', 'passenger')->get();
            } elseif ($request->input('type') == 'drivers') {
                $no_shows = NoShowHistory::with(['ride.rideDetail', 'user'])->where('type', 'driver')->get();

                // Group records by ride_id
                $grouped_no_shows = $no_shows->groupBy('ride_id');

                // Optional: Transform the grouped data (e.g., take the first record for each ride_id)
                $no_shows = $grouped_no_shows->map(function ($group) {
                    return $group->first(); // You can modify this logic if needed
                })->values();
            }

            // return $no_shows;
            return $this->apiSuccessResponse(NoShowHistoryResource::collection($no_shows), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function count($id)
    {
        try {
            $no_shows = NoShowHistory::where('user_id', $id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();

            return response()->json([
                'status' => 'Success',
                'message' => 'Data Get Successfully!',
                'status_code' => 200,
                'data' => ['no_show_count' => $no_shows],
            ], 200);
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function cancellationCount($id)
    {
        try {
            $cancellation_count = CancellationHistory::where('user_id', $id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();

            return response()->json([
                'status' => 'Success',
                'message' => 'Data Get Successfully!',
                'status_code' => 200,
                'data' => ['cancellation_count' => $cancellation_count],
            ], 200);
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function updateStatus($id, Request $request, NoShowHistory $verify_bank)
    {
        $rules = [
            'restriction' => 'required',
        ];
        $this->validate($request, $rules);

        $user = User::whereId($id)->first();
        if ($user) {
            if ($request->restriction == '1') {
                $rides = Ride::where('added_by', $user->id)
                    ->where('status', '!=', 2)
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereDate('date', '>', now()->toDateString())
                                ->orWhere(function ($query) {
                                    $query->whereDate('date', '=', now()->toDateString())
                                        ->whereTime('time', '>=', now()->toTimeString());
                                });
                        });
                    })
                    ->get();

                $no_show_refund = NoShowHistory::where('user_id', $id)->where('type', $request->type)->first();
                // dd($no_show_refund->is_refund);
                if ($no_show_refund->is_refund === 0) {
                    foreach ($rides as $ride) {
                        $bookings = Booking::where('ride_id', $ride->id)->where('status', '1')->get();
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
                                                    return $this->apiErrorResponse($e->getMessage(), 200);
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

                            $passenger = User::whereId($booking->user_id)->first();

                            if (isset($passenger->email_notification) && $passenger->email_notification == 1) {
                            $data = ['first_name' => $passenger->first_name];
                            Mail::to($passenger->email)->queue(new SuspendDriverMail($data));
                            }
                            
                            $booking->update([
                                'status' => '4'
                            ]);
                        }

                        $ride->update([
                            'status' => '2'
                        ]);
                    }
                    NoShowHistory::where('user_id', $id)->where('type', $request->type)->update([
                        'is_refund' => '1',
                    ]);
                }

                $user->update([
                    'admin_deactive_account' => '1',
                ]);
            } elseif ($request->restriction == '2') {
                $user->update([
                    'block_booking' => '1',
                ]);
            } elseif ($request->restriction == '3') {
                $user->update([
                    'block_post_ride' => '1',
                ]);
            } elseif ($request->restriction == '4') {
                $user->update([
                    'block_review_rating' => '1',
                ]);
            }

            NoShowHistory::where('user_id', $id)->where('type', $request->type)->update([
                'status' => '1',
            ]);
            return $this->apiSuccessResponse(new NoShowHistoryResource($verify_bank), 'Action performed successfully.');
        }
        return $this->errorResponse();
    }


    public function undoUpdateStatus($id, Request $request, NoShowHistory $verify_bank)
    {
        // dd($id);


        $user = User::whereId($id)->first();
        if ($user) {
            $user->update([
                'admin_deactive_account' => '0',
            ]);
            if ($request->type == 'passenger') {

                $user->update([
                    'block_booking' => '0',

                ]);
            } elseif ($request->type == 'driver') {
                $user->update([
                    'block_post_ride' => '0',
                    'block_review_rating' => '0',
                ]);
            }

            NoShowHistory::where('user_id', $id)->where('type', $request->type)->update([
                'status' => NULL,
            ]);
            return $this->apiSuccessResponse(new NoShowHistoryResource($verify_bank), 'Action performed successfully.');
        }
        return $this->errorResponse();
    }

    protected function whereClause($no_shows)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];
        }

        return $no_shows;
    }

    protected function loadRelations($no_shows)
    {
        return $no_shows;
    }

    protected function sortingAndLimit($no_shows)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $no_shows->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'date', 'time'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $no_shows = $no_shows->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $no_shows->paginate($limit);
    }
}
