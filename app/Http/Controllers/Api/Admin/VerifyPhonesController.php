<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PhoneVerificationResource;
use App\Models\PhoneNumber;
use App\Models\PhoneVerification;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class VerifyPhonesController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{
            $verify_phones = PhoneVerification::query();

            $verify_phones = $this->whereClause($verify_phones);
            $verify_phones = $this->loadRelations($verify_phones);
            $verify_phones = $this->sortingAndLimit($verify_phones);

            return $this->apiSuccessResponse(PhoneVerificationResource::collection($verify_phones), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function verifyRequest($id, PhoneVerification $verify_phone)
    {
        $existingRecord = PhoneVerification::whereId($id)->first();
        $phone_number = PhoneNumber::whereId($existingRecord->phone_number_id)->first();
        $result = $phone_number->update([
            'verified' => '1',
        ]);

        PhoneVerification::whereId($id)->delete();

        // Auto-mark as default if this is the first/only verified phone number
        $verifiedPhoneCount = PhoneNumber::where('user_id', $phone_number->user_id)
            ->where('verified', '1')
            ->count();
        
        if ($verifiedPhoneCount === 1) {
            // This is the only verified phone number, make it default
            // First, remove default from any existing numbers
            PhoneNumber::where('user_id', $phone_number->user_id)
                ->update(['default' => '0']);
            // Then set this one as default
            $phone_number->update(['default' => '1']);
        }

        if ($result) {
            return $this->apiSuccessResponse(new PhoneVerificationResource($verify_phone), 'Phone number is verified successfully');
        }
        return $this->errorResponse();
    }

    protected function whereClause($verify_phones)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];

            $verify_phones = $verify_phones->where(function ($query) use ($searchParam) {
                $query
                    ->where(function ($subquery) use ($searchParam) {
                        // Add conditions to search
                        $subquery->where('id', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('status', 'LIKE', '%' . $searchParam . '%');
                    });
            });
        }

        return $verify_phones;
    }

    protected function loadRelations($verify_phones)
    {
        return $verify_phones;
    }

    protected function sortingAndLimit($verify_phones)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $verify_phones->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $verify_phones = $verify_phones->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $verify_phones->paginate($limit);
    }
}
