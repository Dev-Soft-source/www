<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CountryResource;
use App\Models\Country;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $countries = Country::query()->orderBy('name', 'asc');

        $countries = $this->whereClause($countries);
        $countries = $this->loadRelations($countries);
        $countries = $this->sortingAndLimit($countries);
        return $this->apiSuccessResponse(CountryResource::collection($countries), 'Data Get Successfully!');
    }

    public function show(Country $country)
    {
        return $this->apiSuccessResponse(new CountryResource($country), 'Data Get Successfully!');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:50'],
        ];
        $this->validate($request, $rules, $this->customMessages);

        $country = Country::create([
            'name' => $request->name,
        ]);

        if ($country) {
            return $this->apiSuccessResponse(new CountryResource($country), 'Country has been added successfully.');
        }
        return $this->errorResponse();
    }

    public function update(Request $request, Country $country)
    {
        $rules = [
            'id' => ['required', 'exists:App\Models\Country,id'],
            'name' => ['required', 'string', 'max:50'],
        ];

        $this->validate($request, $rules, $this->customMessages);

        $result = Country::whereId($request->id)->update([
            'name' => $request->name,
        ]);

        if ($result) {
            return $this->apiSuccessResponse(new CountryResource($country), 'Country has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function destroy(Country $country)
    {
        if ($country->delete()) {
            return $this->apiSuccessResponse(new CountryResource($country), 'Country has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    private $customMessages = [
        'string' => 'This field must be a string',
        'max' => 'This field cannot exceed :max characters',
        'exists' => 'The selected :attribute does not exist in the database',
    ];

    protected function loadRelations($countries)
    {
        return $countries;
    }

    protected function sortingAndLimit($countries)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $countries->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'name', 'abbreviation', 'native_name'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $countries = $countries->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        }
        else {
            $limit = 1000;
        }

        return $countries->paginate($limit);
    }

    protected function whereClause($countries)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $countries = $countries->where('name', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('id', 'LIKE', '%' . $_GET['searchParam'] . '%');
        }
        return $countries;
    }
    public function getStates(Country $country)
    {
        $states = $country->states;
        return response()->json(['data' => $states]);
    }
}
