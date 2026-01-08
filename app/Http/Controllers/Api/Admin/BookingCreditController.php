<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BookingCreditResource;
use App\Models\CreditPackage;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class BookingCreditController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $credits = CreditPackage::query();

        $credits = $this->whereClause($credits);
        $credits = $this->loadRelations($credits);
        $credits = $this->sortingAndLimit($credits);

        return $this->apiSuccessResponse(BookingCreditResource::collection($credits), 'Data Get Successfully!');
    }

    public function store(Request $request)
    {
        $rules = [
            'credits_buy' => ['required', 'numeric'],
            'credits_get' => ['required', 'numeric'],
            'credits_price' => ['required', 'numeric'],
        ];
        $this->validate($request, $rules, $this->customMessages);

        $credit = CreditPackage::create([
            'credits_buy' => $request->credits_buy,
            'credits_get' => $request->credits_get,
            'credits_price' => $request->credits_price,
        ]);

        if ($credit) {
            return $this->apiSuccessResponse(new BookingCreditResource($credit), 'Booking Credit has been added successfully.');
        }
        return $this->errorResponse();
    }

    public function destroy($id, CreditPackage $credit)
    {
        $result = CreditPackage::whereId($id)->delete();
        if ($result) {
            return $this->apiSuccessResponse(new BookingCreditResource($credit), 'Booking Credit has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    private $customMessages = [
        'numeric' => 'This field must be a number',
    ];

    protected function loadRelations($credits)
    {
        return $credits;
    }

    protected function sortingAndLimit($credits)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $credits->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'name', 'abbreviation', 'native_name'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $credits = $credits->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $credits->paginate($limit);
    }

    protected function whereClause($credits)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $credits = $credits->where('credits_buy', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('credits_get', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('credits_price', 'LIKE', '%' . $_GET['searchParam'] . '%');
        }
        return $credits;
    }
}
