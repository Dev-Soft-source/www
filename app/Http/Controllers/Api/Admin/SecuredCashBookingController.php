<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BookingResource;
use App\Models\Booking;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;

class SecuredCashBookingController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try {
            $bookings = Booking::query();

            $bookings = $this->whereClause($bookings);
            $bookings = $this->loadRelations($bookings);
            $bookings = $this->sortingAndLimit($bookings);

            return $this->apiSuccessResponse(BookingResource::collection($bookings), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function enterCode($id, Booking $booking)
    {
        $booking = Booking::whereId($id)->first();
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
                            $transaction->price,
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
                                'amount' => $transaction->price * 100, // Refund amount in cents
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
                        'dr_amount' => $transaction->price,
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

        if ($booking) {
            return $this->apiSuccessResponse(new BookingResource($booking), 'Amount refunded to passenger successfully');
        }
        return $this->errorResponse();
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);
        
        $admin = Auth::guard('admin')->user(); // 🔐 uses the admin guard
        // dd($admin);

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'valid' => false,
                'message' => 'Incorrect password',
            ], 401);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Password verified',
        ]);
    }


    protected function whereClause($bookings)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];

            $bookings = $bookings->where(function ($query) use ($searchParam) {
                $query
                    ->where(function ($subquery) use ($searchParam) {
                        // Add conditions to search
                        $subquery->where('id', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('booked_on', 'LIKE', '%' . $searchParam . '%');
                    });
            });
        }

        // Filter bookings with `secured_cash` value of 1
        $bookings = $bookings->where('secured_cash', 1)->where('status', '<>', 4);

        // Add condition to check associated ride `date` and `time`
        $bookings = $bookings->whereHas('ride', function ($query) {
            $query->where(function ($subquery) {
                $subquery
                    ->where('date', '<', Carbon::today()->toDateString())
                    ->orWhere(function ($timeQuery) {
                        $timeQuery->where('date', Carbon::today()->toDateString())
                            ->where('time', '<', Carbon::now()->toTimeString());
                    });
            });
        });

        return $bookings;
    }

    protected function loadRelations($bookings)
    {
        return $bookings;
    }

    protected function sortingAndLimit($bookings)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $bookings->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'booked_on'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $bookings = $bookings->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $bookings->paginate($limit);
    }
}
