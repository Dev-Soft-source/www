<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CityResource;
use App\Models\City;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $cities = City::query()->orderBy('name', 'asc');

        $cities = $this->whereClause($cities);
        $cities = $this->loadRelations($cities);
        $cities = $this->sortingAndLimit($cities);

        return $this->apiSuccessResponse(CityResource::collection($cities), 'Data Get Successfully!');
    }

    public function show(City $city)
    {
        return $this->apiSuccessResponse(new CityResource($city), 'Data Get Successfully!');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:50'],
            'state_id' => ['required'],
        ];
        $this->validate($request, $rules, $this->customMessages);

        $city = City::create([
            'name' => $request->name,
            'state_id' => $request->state_id,
        ]);

        if ($city) {
            return $this->apiSuccessResponse(new CityResource($city), 'City has been added successfully.');
        }
        return $this->errorResponse();
    }

    public function update(Request $request, City $city)
    {
        $rules = [
            'id' => ['required', 'exists:App\Models\City,id'],
            'name' => ['required', 'string', 'max:50'],
            'state_id' => ['required'],
        ];
        $this->validate($request, $rules, $this->customMessages);

        $result = City::whereId($request->id)->update([
            'name' => $request->name,
            'state_id' => $request->state_id,
        ]);

        if ($result) {
            return $this->apiSuccessResponse(new CityResource($city), 'City has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function destroy(City $city)
    {
        if ($city->delete()) {
            return $this->apiSuccessResponse(new CityResource($city), 'City has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    private $customMessages = [
        'string' => 'This field must be a string',
        'max' => 'This field cannot exceed :max characters',
        'exists' => 'The selected :attribute does not exist in the database',
    ];

    protected function loadRelations($cities)
    {
        return $cities;
    }

    protected function sortingAndLimit($cities)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $cities->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'name', 'abbreviation', 'native_name'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $cities = $cities->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        }
         else {
            $limit = 10;
        }

        return $cities->paginate($limit);
    }

    protected function whereClause($cities)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $cities = $cities->where('name', 'LIKE', '%' . $_GET['searchParam'] . '%');
        }
        return $cities;
    }
}
