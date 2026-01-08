<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RideResource;
use App\Models\Ride;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class RideController extends Controller
{
    use StatusResponser;

    public function index(Request $request)
    {
        try{
            // Update the status of rides that have passed their date and time
            if($request->input('s')=='2')
                $rides = Ride::where(function ($query) {
                    $query->where(function ($query) {
                        $query->whereDate('completed_date', '<=', now()->toDateString())
                            ->orWhere(function ($query) {
                                $query->whereDate('completed_date', '=', now()->toDateString())
                                    ->whereTime('completed_time', '<=', now()->toTimeString());
                            });
                    })
                    // ->orWhere('suspand', 1)
                    ->where('status', 2);
                })->with(['rideDetail' => function($q){
                    $q->where('default_ride','1');
                }])
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->orderByDesc('id');
            else if($request->input('s')=='1')
                $rides = Ride::
            // where('suspand', 0)->
                    where('status', '!=', 2)
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereDate('completed_date', '>=', now()->toDateString())
                                ->orWhere(function ($query) {
                                    $query->whereDate('completed_date', '=', now()->toDateString())
                                        ->whereTime('completed_time', '>=', now()->toTimeString());
                                });
                        });
                    })->with(['rideDetail' => function($q){
                        $q->where('default_ride','1');
                    }])
                    ->orderByDesc('date')
                    ->orderByDesc('time')
                    ->orderByDesc('id');

            $rides = $this->whereClause($rides);
            $rides = $this->loadRelations($rides);
            $rides = $this->sortingAndLimit($rides);

            return $this->apiSuccessResponse(RideResource::collection($rides), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function show($id, Ride $ride)
    {
        $ride = Ride::whereId($id)->with('postRideLogs')->first();

        return $this->apiSuccessResponse(new RideResource($ride), 'Data Get Successfully!');
    }

    public function cancelRide($id, Ride $ride)
    {
        $result = Ride::whereId($id)->update([
            'status' => 2,
        ]);

        if ($result) {
            return $this->apiSuccessResponse(new RideResource($ride), 'Ride has been cancelled successfully.');
        }
        return $this->errorResponse();
    }

    public function removeRide($id, Ride $ride)
    {
        $result = Ride::whereId($id)->delete();

        if ($result) {
            return $this->apiSuccessResponse(new RideResource($ride), 'Ride has been removed successfully.');
        }
        return $this->errorResponse();
    }

    public function suspandRide($id)
    {
        $result = Ride::whereId($id)->update([
            'suspand' => 1,
        ]);

        $ride=Ride::where('id',$id)->first();
        if ($result) {
            return $this->apiSuccessResponse(new RideResource($ride), 'Ride has been suspended successfully.');
        }
        return $this->errorResponse();
    }

    public function unSuspandRide($id, Ride $ride)
    {
        $result = Ride::whereId($id)->update([
            'suspand' => 0,
        ]);
        $ride=Ride::where('id',$id)->first();

        if ($result) {
            return $this->apiSuccessResponse(new RideResource($ride), 'Ride has been Unsuspend successfully.');
        }
        return $this->errorResponse();
    }

    protected function whereClause($rides)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];

            $rides = $rides->whereHas('rideDetail', function($q) use ($searchParam){
                $q->where(function ($query) use ($searchParam) {
                    $query->where(function ($subquery) use ($searchParam) {
                            // Add conditions to search
                            $subquery->where('departure', 'LIKE', '%' . $searchParam . '%')
                                ->orWhere('destination', 'LIKE', '%' . $searchParam . '%')
                                ->orWhere('date', 'LIKE', '%' . $searchParam . '%')
                                ->orWhere('price', 'LIKE', '%' . $searchParam . '%');
                        });
                });
            });
        }

        return $rides;
    }

    protected function loadRelations($rides)
    {
        return $rides;
    }

    protected function sortingAndLimit($rides)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $rides->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'date', 'time'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $rides = $rides->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $rides->paginate($limit);
    }
}
