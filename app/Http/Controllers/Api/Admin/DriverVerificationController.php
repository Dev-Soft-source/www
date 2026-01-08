<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Mail\DriverLicenseApprovedMail;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class DriverVerificationController extends Controller
{
    use StatusResponser;
    
    public function index()
    {
        try{

            $users = User::query()->whereIn('driver',[1, 2, 3]);
    
            $users = $this->whereClause($users);
            $users = $this->loadRelations($users);
            $users = $this->sortingAndLimit($users);

            // $users->orderBy('driver_license_upload', 'desc');
    
            return $this->apiSuccessResponse(UserResource::collection($users), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function approveDriver($id, User $user)
    {
        $user = User::whereId($id)->first();
        User::whereId($id)->update([
            'driver' => 1,
        ]);

        if (isset($user->email_notification) && $user->email_notification == 1) {
        $data = ['first_name' => $user->first_name];
        // Send driver license approved email
        Mail::to($user->email)->queue(new DriverLicenseApprovedMail($data));
        }

        if ($user) {
            return $this->apiSuccessResponse(new UserResource($user), 'Driver has been approved successfully.');
        }
        return $this->errorResponse();
    }

    public function rejectDriver($id, User $user)
    {
        $result = User::whereId($id)->update([
            'driver' => 3,
        ]);

        if ($result) {
            return $this->apiSuccessResponse(new UserResource($user), 'Driver has been rejected successfully.');
        }
        return $this->errorResponse();
    }

    protected function whereClause($users)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];
            
            // Apply a condition to filter records with driver = 2 and 3
            $users = $users->where(function ($query) use ($searchParam) {
                $query->whereIn('driver', [2, 3])
                    ->where(function ($subquery) use ($searchParam) {
                        // Add conditions to search by first_name, last_name, email, or ID
                        $subquery->where('first_name', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('email', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('id', 'LIKE', '%' . $searchParam . '%');
                    });
            });
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
        $sortBy = ['driver_license_upload'];
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
