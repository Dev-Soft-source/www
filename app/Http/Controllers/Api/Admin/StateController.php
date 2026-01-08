<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\StateResource;
use App\Models\State;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class StateController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $states = State::query()->orderBy('name', 'asc');

        $states = $this->whereClause($states);
        $states = $this->loadRelations($states);
        $states = $this->sortingAndLimit($states);

        return $this->apiSuccessResponse(StateResource::collection($states), 'Data Get Successfully!');
    }

    public function show(State $state)
    {
        return $this->apiSuccessResponse(new StateResource($state), 'Data Get Successfully!');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:50'],
            'country_id' => ['required'],
            'ride_limit' => ['nullable'],
            'tax' => ['nullable'],
        ];
        $this->validate($request, $rules, $this->customMessages);

        $state = State::create([
            'name' => $request->name,
            'country_id' => $request->country_id,
            'ride_limit' => $request->ride_limit ?? NULL,
            'tax' => $request->tax ?? NULL,
        ]);

        if ($state) {
            return $this->apiSuccessResponse(new StateResource($state), 'State has been added successfully.');
        }
        return $this->errorResponse();
    }

    public function update(Request $request, State $state)
    {
        $rules = [
            'id' => ['required', 'exists:App\Models\State,id'],
            'name' => ['required', 'string', 'max:50'],
            'country_id' => ['required'],
            'ride_limit' => ['nullable'],
            'tax' => ['nullable'],
        ];
        $this->validate($request, $rules, $this->customMessages);

        $result = State::whereId($request->id)->update([
            'name' => $request->name,
            'country_id' => $request->country_id,
            'ride_limit' => $request->ride_limit ?? NULL,
            'tax' => $request->tax ?? NULL,
        ]);

        if ($result) {
            return $this->apiSuccessResponse(new StateResource($state), 'State has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function destroy(State $state)
    {
        $state->cities()->delete();
        if ($state->delete()) {
            return $this->apiSuccessResponse(new StateResource($state), 'State has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    private $customMessages = [
        'string' => 'This field must be a string',
        'max' => 'This field cannot exceed :max characters',
        'exists' => 'The selected :attribute does not exist in the database',
    ];

    protected function loadRelations($states)
    {
        return $states;
    }

    protected function sortingAndLimit($states)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $states->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'name', 'abbreviation', 'native_name'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $states = $states->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10000;
        }

        return $states->paginate($limit);
    }

    protected function whereClause($states)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $states = $states->where('name', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('abrv', 'LIKE', '%' . $_GET['searchParam'] . '%');
        }
        return $states;
    }
}
