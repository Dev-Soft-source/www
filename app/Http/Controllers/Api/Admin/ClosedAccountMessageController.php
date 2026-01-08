<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CloseAccountSubmissionResource;
use App\Models\CloseAccountSubmission;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class ClosedAccountMessageController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{

            $messages = CloseAccountSubmission::query();
    
            $messages = $this->whereClause($messages);
            $messages = $this->loadRelations($messages);
            $messages = $this->sortingAndLimit($messages);
    
            return $this->apiSuccessResponse(CloseAccountSubmissionResource::collection($messages), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    protected function whereClause($messages)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $messages = $messages->where('name', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('id', 'LIKE', '%' . $_GET['searchParam'] . '%');
        }
        return $messages;
    }

    protected function loadRelations($messages)
    {
        return $messages;
    }

    protected function sortingAndLimit($messages)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $messages->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $messages = $messages->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $messages->paginate($limit);
    }
}
