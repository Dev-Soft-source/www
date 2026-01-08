<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BankResource;
use App\Models\Bank;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BankController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $banks = Bank::query()->orderBy('name', 'asc');

        $banks = $this->whereClause($banks);
        $banks = $this->loadRelations($banks);
        $banks = $this->sortingAndLimit($banks);

        return $this->apiSuccessResponse(BankResource::collection($banks), 'Data Get Successfully!');
    }

    public function show(Bank $bank)
    {
        return $this->apiSuccessResponse(new BankResource($bank), 'Data Get Successfully!');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:50', 'unique:banks'],
            'bank_to_bank_fee' => ['required'],
            'other_bank_fee' => ['required'],
        ];
        $this->validate($request, $rules, $this->customMessages);

        $bank = Bank::create([
            'name' => $request->name,
            'bank_to_bank_fee' => $request->bank_to_bank_fee,
            'other_bank_fee' => $request->other_bank_fee,
        ]);

        if ($bank) {
            return $this->apiSuccessResponse(new BankResource($bank), 'Bank has been added successfully.');
        }
        return $this->errorResponse();
    }

    public function update(Request $request, Bank $bank)
    {
        $rules = [
            'id' => ['required', 'exists:App\Models\Bank,id'],
            'name' => ['required', 'string', 'max:50', Rule::unique('banks')->ignore($bank->id)],
            'bank_to_bank_fee' => ['required'],
            'other_bank_fee' => ['required'],
        ];
        $this->validate($request, $rules, $this->customMessages);

        $result = Bank::whereId($request->id)->update([
            'name' => $request->name,
            'bank_to_bank_fee' => $request->bank_to_bank_fee,
            'other_bank_fee' => $request->other_bank_fee,
        ]);

        if ($result) {
            return $this->apiSuccessResponse(new BankResource($bank), 'Bank has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function destroy(Bank $bank)
    {
        if ($bank->delete()) {
            return $this->apiSuccessResponse(new BankResource($bank), 'Bank has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    private $customMessages = [
        'string' => 'This field must be a string',
        'max' => 'This field cannot exceed :max characters',
        'unique' => 'The :attribute has already been taken',
    ];

    protected function loadRelations($banks)
    {
        return $banks;
    }

    protected function sortingAndLimit($banks)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $banks->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'name'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $banks = $banks->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $banks->paginate($limit);
    }

    protected function whereClause($banks)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $banks = $banks->where('name', 'LIKE', '%' . $_GET['searchParam'] . '%');
        }
        return $banks;
    }
}
