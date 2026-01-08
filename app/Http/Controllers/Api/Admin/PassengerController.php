<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{

            $users = User::query();

            // Add a condition to filter users with bookings
            $users->has('bookings');
    
            $users = $this->whereClause($users);
            $users = $this->loadRelations($users);
            $users = $this->sortingAndLimit($users);
    
            return $this->apiSuccessResponse(UserResource::collection($users), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    protected function whereClause($users)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $users = $users->where('first_name', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('last_name', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('email', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('id', 'LIKE', '%' . $_GET['searchParam'] . '%');
        }
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
