<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RatingResource;
use App\Models\Rating;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Stripe\Review;

class ReviewController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{
            $ratings = Rating::query();

            $ratings = $this->whereClause($ratings);
            $ratings = $this->loadRelations($ratings);
            $ratings = $this->sortingAndLimit($ratings);

            return $this->apiSuccessResponse(RatingResource::collection($ratings), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function show($id, Rating $rating)
    {
        $rating = Rating::whereId($id)->first();

        return $this->apiSuccessResponse(new RatingResource($rating), 'Data Get Successfully!');
    }

    public function updateDisplyCheckbox(Request $request)
    {
        $rating = Rating::whereId($request->id)->update(['is_disply' => $request->val == true ? '1' : '0']);

        return $this->apiSuccessResponse(new RatingResource(Rating::whereId($request->id)->first()), 'Data Get Successfully!');
    }

    public function unSuspend($id, Rating $rating)
    {
        $rating = Rating::whereId($id)->first();

        if ($rating) {
            $rating->update([
                'status' => 1,
            ]);
            return $this->apiSuccessResponse(new RatingResource($rating), 'Review has been suspended successfully.');
        }
        return $this->errorResponse();
    }

    public function destroy($id, Rating $rating)
    {
        $rating = Rating::whereId($id)->first();

        if ($rating) {
            $rating->update([
                'status' => '2',
                'live_limit' => null,
            ]);
            return $this->apiSuccessResponse(new RatingResource($rating), 'Review has been suspended successfully.');
        }
        return $this->errorResponse();
    }

    protected function whereClause($ratings)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];

            $ratings = $ratings->where(function ($query) use ($searchParam) {
                $query
                    ->where(function ($subquery) use ($searchParam) {
                        // Add conditions to search
                        $subquery->where('id', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('ride_id', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('added_on', 'LIKE', '%' . $searchParam . '%');
                    });
            });
        }

        return $ratings;
    }

    protected function loadRelations($ratings)
    {
        return $ratings;
    }

    protected function sortingAndLimit($ratings)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $ratings->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'ride_id','added_on'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $ratings = $ratings->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $ratings->paginate($limit);
    }
}
