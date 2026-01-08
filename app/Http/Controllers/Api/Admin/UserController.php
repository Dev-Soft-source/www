<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Mail\SuspendDriverMail;
use App\Mail\SuspendUserMail;
use App\Models\Booking;
use App\Models\CoffeeWallet;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;

class UserController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{

            $users = User::orderBy('first_name', 'asc');

            $users = $this->whereClause($users);
            $users = $this->loadRelations($users);
            $users = $this->sortingAndLimit($users);

            return $this->apiSuccessResponse(UserResource::collection($users), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        $user = User::whereId($id)->first();
        $user_id = $user->id;
        $rides = Ride::where('added_by',$user_id)->get();

        if ($rides->isNotEmpty()) {
            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                      ->whereHas('ride', function ($query) use ($user_id) {
                          $query->where('added_by', $user_id);
                      });
            })
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

            // Calculate total average
            $overallRating = $ratings->avg('average_rating') ?? 0;
        } else {
            $overallRating = 5;
        }

        return $this->apiSuccessResponse(new UserResource($user), $overallRating, 'Data Get Successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->delete()) {
            return $this->apiSuccessResponse(new UserResource($user), 'User has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    public function suspandUser($id, User $user)
    {
        $result = User::whereId($id)->update([
            'suspand' => 1,
        ]);

        $user = User::whereId($id)->first();

        if (isset($user->email_notification) && $user->email_notification == 1) {
        $data = ['first_name' => $user->first_name];
        Mail::to($user->email)->queue(new SuspendUserMail($data));
        }
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
        foreach ($rides as $ride) {
            $bookings = Booking::where('ride_id', $ride->id)->where('status', '1')->get();
            foreach ($bookings as $booking) {
                $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
                foreach ($transactions as $transaction) {
                    if ($transaction) {

                        $refundId = "";

                        $checkPrice = 0.0;
                        $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');
                        if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                            $getRefundEntryPrice = (double)$getRefundEntryPrice + (double)$transaction->booking_fee;
                        }
                        $checkPrice = (double)$transaction->price;

                        if(isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == $checkPrice){
                            if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
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
                        }else{
                            $transactionAmt = $checkPrice - $getRefundEntryPrice;

                            if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
                                $transactionAmt = $transactionAmt - $transaction->booking_fee;
                            }

                            if($transaction->pay_by_account == 0){
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
                            }else{
                                $topUpBalance = TopUpBalance::create([
                                    'booking_id' => $transaction->booking_id,
                                    'user_id' => $booking->user_id,
                                    'dr_amount' => $transactionAmt,
                                    'added_date' => date('Y-m-d'),
                                ]);
                            }

                            if(isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1){
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

                $data = ['first_name' => $passenger->first_name];
                Mail::to($passenger->email)->queue(new SuspendDriverMail($data));
                
                $booking->update([
                    'status' => '4'
                ]);
            }

            $ride->update([
                'status' => '2'
            ]);
        }

        if ($result) {
            return $this->apiSuccessResponse(new UserResource($user), 'User has been suspended successfully.');
        }
        return $this->errorResponse();
    }

    public function unSuspandUser($id, User $user)
    {
        $result = User::whereId($id)->update([
            'suspand' => 0,
        ]);

        if ($result) {
            return $this->apiSuccessResponse(new UserResource($user), 'User has been Unsuspend successfully.');
        }
        return $this->errorResponse();
    }

    protected function whereClause($users)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $users = $users->where('first_name', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('last_name', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('email', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('id', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%' . $_GET['searchParam'] . '%');
        }
        // Filter by closed status for soft-deleted records only
        $users->where(function ($query) {
            $query->where('closed', '1')->orWhereNull('deleted_at');
        });

        return $users;
    }

    protected function loadRelations($users)
    {
        return $users;
    }

    protected function sortingAndLimit($users)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $users->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $users = $users->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $users->paginate($limit);
    }
}
