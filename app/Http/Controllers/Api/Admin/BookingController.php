<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BookingResource;
use App\Models\Booking;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{
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

    protected function whereClause($bookings)
    {
        $searchParam = request()->get('searchParam');

        if ($searchParam && $searchParam != '') {

            $bookings = $bookings->where(function ($query) use ($searchParam) {
                $query->where('id', 'LIKE', '%' . $searchParam . '%')
                    ->orWhere('booked_on', 'LIKE', '%' . $searchParam . '%')
                    ->orWhere('departure', 'LIKE', '%' . $searchParam . '%')
                    ->orWhere('destination', 'LIKE', '%' . $searchParam . '%')
                    ->orWhere('price', 'LIKE', '%' . $searchParam . '%')
                    ->orWhereHas('ride', function ($rideQuery) use ($searchParam) {
                        $rideQuery->orWhere('payment_method', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('date', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('time', 'LIKE', '%' . $searchParam . '%')
                            ->orWhereHas('driver', function ($driverQuery) use ($searchParam) {
                                $driverQuery->where('first_name', 'LIKE', '%' . $searchParam . '%')
                                    ->orWhere('last_name', 'LIKE', '%' . $searchParam . '%')
                                    ->orWhere('email', 'LIKE', '%' . $searchParam . '%');
                            });
                    })
                    ->orWhereHas('passenger', function ($passengerQuery) use ($searchParam) {
                        $passengerQuery->where('first_name', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('email', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('gender', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('student', 'LIKE', '%' . $searchParam . '%');
                    });
            });
        }

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
