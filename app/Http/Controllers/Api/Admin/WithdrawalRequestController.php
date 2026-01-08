<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\WithdrawalRequestResource;
use App\Mail\PayoutSuccessMail;
use App\Models\BankDetail;
use App\Models\Booking;
use App\Models\Payout;
use App\Models\User;
use App\Models\WithdrawalRequest;
use App\Traits\StatusResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class WithdrawalRequestController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try {
            $withdrawals = Payout::query()
                ->selectRaw('user_id, SUM(amount) as total_amount')
                ->where('status', 'request')
                ->groupBy('user_id')
                ->with('driver.bankDetail','bookings' ,'driver.bankDetail.bank')
                ->get()
                ->map(function ($item) {
                    // Get all booking_ids from payouts for this user with status 'request'
                    $bookingIds = Payout::where('user_id', $item->user_id)
                        ->where('status', 'request')
                        ->pluck('booking_id')
                        ->filter() // remove nulls, just in case
                        ->unique();
            
                    // Get only bookings that match those IDs
                    $bookings = Booking::whereIn('id', $bookingIds)->with('passenger','ride')->get();
            
                    $item->bookings = $bookings;
            
                    return $item;
                });

            // $withdrawals = $this->whereClause($withdrawals);
            // $withdrawals = $this->loadRelations($withdrawals);
            // $withdrawals = $this->sortingAndLimit($withdrawals);

            return $this->apiSuccessResponse(WithdrawalRequestResource::collection($withdrawals), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function acceptRequest($id, WithdrawalRequest $withdrawal)
    {
        $result = WithdrawalRequest::whereId($id)->update([
            'status' => 1,
        ]);

        if ($result) {
            return $this->apiSuccessResponse(new WithdrawalRequestResource($withdrawal), 'Request has been accepted successfully.');
        }
        return $this->errorResponse();
    }

    // public function rejectRequest($id)
    // {
    //     $result = Payout::where('user_id', $id)
    //         ->where('status', 'request')
    //         ->update([
    //             'status' => 'completed',
    //             'paid_out_date' => Carbon::now(),
    //         ]);

    //     BankDetail::where('user_id', $id)->update(['payment_status' => 'completed']);

    //     if ($result) {
    //         $withdrawals = Payout::query()
    //             ->selectRaw('user_id, SUM(amount) as total_amount')
    //             ->where('status', 'request')
    //             ->groupBy('user_id')
    //             ->with('driver.bankDetail', 'driver.bankDetail.bank')
    //             ->get();

    //         return $this->apiSuccessResponse(WithdrawalRequestResource::collection($withdrawals), 'Request has been tranfered successfully.');
    //     }
    //     return $this->errorResponse();
    // }
    public function rejectRequest($id)
    {
        // Get the payout before updating
        $payout = Payout::where('user_id', $id)
            ->where('status', 'request')
            ->first();

        if (!$payout) {
            return $this->errorResponse();
        }

        $result = Payout::where('user_id', $id)
            ->where('status', 'request')
            ->update([
                'status' => 'completed',
                'paid_out_date' => Carbon::now(),
            ]);

        BankDetail::where('user_id', $id)->update(['payment_status' => 'completed']);

        if ($result) {
            // Get driver details
            $driver = User::find($id);
            $bankDetail = BankDetail::where('user_id', $id)->first();

            // Prepare email data
            $emailData = [
                'first_name' => $driver->first_name,
                'transaction_id' => $payout->random_id,
                'transaction_date' => Carbon::now('EST')->format('F j, Y \a\t H:i') . ' EST',
                'amount' => $payout->amount,
                'payment_method' => $bankDetail->set_default === 'paypal' ? 'paypal' : 'bank',
                'paypal_email' => $bankDetail->paypal_email ?? null,
                'account_number' => $bankDetail->account_number ?? null,
                'bank_title' => $bankDetail->bank_title ?? null,
            ];

            // Send email
            if (isset($driver->email_notification) && $driver->email_notification == 1) {
            Mail::to($driver->email)->send(new PayoutSuccessMail($emailData));
            }
            $withdrawals = Payout::query()
                ->selectRaw('user_id, SUM(amount) as total_amount')
                ->where('status', 'request')
                ->groupBy('user_id')
                ->with('driver.bankDetail', 'driver.bankDetail.bank')
                ->get();

            return $this->apiSuccessResponse(WithdrawalRequestResource::collection($withdrawals), 'Request has been tranfered successfully.');
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
    protected function whereClause($withdrawals)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];

            $withdrawals = $withdrawals->where(function ($query) use ($searchParam) {
                $query
                    ->where(function ($subquery) use ($searchParam) {
                        // Add conditions to search
                        $subquery->where('id', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('amount', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('method', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('on_date', 'LIKE', '%' . $searchParam . '%');
                    });
            });
        }

        return $withdrawals;
    }

    protected function loadRelations($withdrawals)
    {
        return $withdrawals;
    }

    protected function sortingAndLimit($withdrawals)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $withdrawals->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'ride_id', 'added_on'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $withdrawals = $withdrawals->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $withdrawals->paginate($limit);
    }
}
