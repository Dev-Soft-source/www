<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ClaimRewardResource;
use App\Mail\RewardApprovedMail;
use App\Models\ClaimReward;
use App\Models\RewardPoint;
use App\Models\RewardPointSetting;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ClaimRewardsController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{
            $claim_rewards = ClaimReward::query();

            $claim_rewards = $this->whereClause($claim_rewards);
            $claim_rewards = $this->loadRelations($claim_rewards);
            $claim_rewards = $this->sortingAndLimit($claim_rewards);

            return $this->apiSuccessResponse(ClaimRewardResource::collection($claim_rewards), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function approveRequest($id, ClaimReward $claim_reward)
    {
        $reward = ClaimReward::whereId($id)->first();

        $checkStudentRewardSetting = RewardPointSetting::where('point', '<=', $reward->point)->where('type', $reward->type)->orderBy('point', 'desc')->first();

        if(isset($checkStudentRewardSetting) && !is_null($checkStudentRewardSetting)){

            $points = $checkStudentRewardSetting->point;

            while ($points != 0) {
                $getRewardPoint = RewardPoint::where('type', $reward->type)
                    ->where('user_id', $reward->user_id)
                    ->where('status', 'pending')
                    ->orderBy('id', 'asc')
                    ->first();
            
                if ($getRewardPoint) {
                    $points -= $getRewardPoint->point;
                    $getRewardPoint->status = "received";
                    $getRewardPoint->save();
            
                    if ($points < 0) {
                        $rewardPoint = new RewardPoint;
                        $rewardPoint->type = $reward->type;
                        $rewardPoint->user_id = $reward->user_id;
                        $rewardPoint->point = $points * -1;
                        $rewardPoint->status = "pending";
                        $rewardPoint->save();
                        $points = 0;
                    }
                } else {
                    break;
                }
            }
        }else{
            return $this->errorResponse('Please try later no reward found');
        }
        
        $result = ClaimReward::whereId($id)->update([
            'status' => 'deliver',
            'approved_date' => now(),
        ]);

        if ($result) {
            $user = User::whereId($reward->user_id)->first();

            if (isset($user->email_notification) && $user->email_notification == 1) {
            $data = ['first_name' => $user->first_name];
            Mail::to($user->email)->queue(new RewardApprovedMail($data));
            }
            
            return $this->apiSuccessResponse(new ClaimRewardResource($claim_reward), 'Reward delivered successfully');
        }
        return $this->errorResponse();
    }

    protected function whereClause($claim_rewards)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];

            $claim_rewards = $claim_rewards->where(function ($query) use ($searchParam) {
                $query
                    ->where(function ($subquery) use ($searchParam) {
                        // Add conditions to search
                        $subquery->where('id', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('status', 'LIKE', '%' . $searchParam . '%');
                    });
            });
        }

        return $claim_rewards;
    }

    protected function loadRelations($claim_rewards)
    {
        return $claim_rewards;
    }

    protected function sortingAndLimit($claim_rewards)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $claim_rewards->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $claim_rewards = $claim_rewards->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $claim_rewards->paginate($limit);
    }
}
