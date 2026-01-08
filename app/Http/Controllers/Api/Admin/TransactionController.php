<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\TransactionResource;
use App\Models\Transaction;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{
            $transactions = Transaction::query();
    
            $transactions = $this->whereClause($transactions);
            $transactions = $this->loadRelations($transactions);
            $transactions = $this->sortingAndLimit($transactions);
    
            return $this->apiSuccessResponse(TransactionResource::collection($transactions), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    protected function whereClause($transactions)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];
            
            $transactions = $transactions->where(function ($query) use ($searchParam) {
                $query
                    ->where(function ($subquery) use ($searchParam) {
                        // Add conditions to search
                        $subquery->where('id', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('on_date', 'LIKE', '%' . $searchParam . '%');
                    });
            });
        }
    
        return $transactions;
    }

    protected function loadRelations($transactions)
    {
        return $transactions;
    }

    protected function sortingAndLimit($transactions)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $transactions->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'ride_id','added_on'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $transactions = $transactions->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $transactions->paginate($limit);
    }
}
