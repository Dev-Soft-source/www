<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BankDetailResource;
use App\Models\BankDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class VerifyBanksController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{
            $verify_banks = BankDetail::query();

            $verify_banks = $this->whereClause($verify_banks);
            $verify_banks = $this->loadRelations($verify_banks);
            $verify_banks = $this->sortingAndLimit($verify_banks);

            return $this->apiSuccessResponse(BankDetailResource::collection($verify_banks), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function verifyRequest($id, Request $request, BankDetail $verify_bank)
    {
        $rules = [
            'admin_verify_amount' => 'required',
        ];
        $this->validate($request, $rules);
        $result = BankDetail::whereId($id)->update([
            'status' => 'admin_verify',
            'admin_verify_amount' => $request->admin_verify_amount,
        ]);

        if ($result) {
            return $this->apiSuccessResponse(new BankDetailResource($verify_bank), 'Amount entered successfully.');
        }
        return $this->errorResponse();
    }

    protected function whereClause($verify_banks)
    {
        $verify_banks = $verify_banks->where('type', 'user');
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];

            $verify_banks = $verify_banks->where(function ($query) use ($searchParam) {
                $query
                    ->where(function ($subquery) use ($searchParam) {
                        // Add conditions to search
                        $subquery->where('id', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('status', 'LIKE', '%' . $searchParam . '%');
                    });
            });
        }

        return $verify_banks;
    }

    protected function loadRelations($verify_banks)
    {
        return $verify_banks;
    }

    protected function sortingAndLimit($verify_banks)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $verify_banks->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $verify_banks = $verify_banks->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $verify_banks->paginate($limit);
    }
}
