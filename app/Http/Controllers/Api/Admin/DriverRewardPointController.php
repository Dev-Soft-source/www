<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RewardPointSettingResource;
use App\Models\RewardPointSetting;
use App\Models\Language;
use App\Models\RewardPointSettingDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class DriverRewardPointController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $rewardPointSettings = RewardPointSetting::query()->orderBy('id', 'asc')->where('type', 'driver');

        $rewardPointSettings = $this->whereClause($rewardPointSettings);
        $rewardPointSettings = $this->loadRelations($rewardPointSettings);
        $rewardPointSettings = $this->sortingAndLimit($rewardPointSettings);

        return $this->apiSuccessResponse(RewardPointSettingResource::collection($rewardPointSettings), 'Data Get Successfully!');
    }

    public function show($id)
    {
        $rewardPointSetting = RewardPointSetting::whereId($id)->with('rewardPointSettingDetail')->first();
        return $this->apiSuccessResponse(new RewardPointSettingResource($rewardPointSetting), 'Data Get Successfully!');
    }

    public function store(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['reward_name.reward_name_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reward_name.reward_name_' . $language->id . '.required' => 'Reward name in ' . $language->name . ' is required']);
    
                $validationRule = array_merge($validationRule, ['point' => ['required', 'string']]);
    
                $errorMessages = array_merge($errorMessages, ['point' . '.required' => 'Point is required']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages
        );

        $rewardPointSetting = RewardPointSetting::create([
            'point' => $request->point,
            'type' => 'driver',
        ]);

        foreach ($languages as $language) {
            $rewardPointSettingDetail = RewardPointSettingDetail::whereLanguageId($language->id)->whereRewardPointSettingId($rewardPointSetting['id'])->first();

            $rewardPointSettingDetailData = [
                'reward_point_setting_id' => $rewardPointSetting['id'],
                'language_id' => $language->id,
                'reward_name' => $request['reward_name']['reward_name_' . $language->id] ?? null,
            ];

            if ($rewardPointSettingDetail) {
                $rewardPointSettingDetail->update($rewardPointSettingDetailData);
            } else {
                RewardPointSettingDetail::create($rewardPointSettingDetailData);
            }
        }

        if ($rewardPointSetting) {
            return $this->apiSuccessResponse(new RewardPointSettingResource($rewardPointSetting), 'Reward point setting has been added successfully.');
        }
        return $this->errorResponse();
    }

    public function update(Request $request, RewardPointSetting $rewardPointSetting)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['reward_name.reward_name_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reward_name.reward_name_' . $language->id . '.required' => 'Reward name in ' . $language->name . ' is required']);
    
                $validationRule = array_merge($validationRule, ['point' => ['required']]);
    
                $errorMessages = array_merge($errorMessages, ['point' . '.required' => 'Point is required']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages
        );

        RewardPointSetting::whereId($request->id)->update([
            'point' => $request->point,
            'type' => 'driver',
        ]);

        $result = RewardPointSetting::whereId($request->id)->first();

        foreach ($languages as $language) {
            $rewardPointSettingDetail = RewardPointSettingDetail::whereLanguageId($language->id)->whereRewardPointSettingId($result['id'])->first();

            $rewardPointSettingDetailData = [
                'reward_point_setting_id' => $result['id'],
                'language_id' => $language->id,
                'reward_name' => $request['reward_name']['reward_name_' . $language->id] ?? null,
            ];

            if ($rewardPointSettingDetail) {
                $rewardPointSettingDetail->update($rewardPointSettingDetailData);
            } else {
                RewardPointSettingDetail::create($rewardPointSettingDetailData);
            }
        }

        if ($result) {
            return $this->apiSuccessResponse(new RewardPointSettingResource($rewardPointSetting), 'Reward point setting has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function destroy($id)
    {
        $rewardPointSetting = RewardPointSetting::whereId($id)->delete();
        if ($rewardPointSetting) {
            $rewardPointSettings = RewardPointSetting::query()->orderBy('id', 'asc')->get();
            return $this->apiSuccessResponse(RewardPointSettingResource::collection($rewardPointSettings), 'Reward point setting has been deleted successfully.');
        }
        return $this->errorResponse();
    }


    protected function loadRelations($rewardPointSettings)
    {

        $defaultLang = getDefaultLanguage();
        $rewardPointSettings = $rewardPointSettings->with(['rewardPointSettingDetail' => function ($q) use ($defaultLang) {
            $q->where('language_id', $defaultLang->id);
        }]);
        if (isset($_GET['withRewardPointSettingDetail']) && $_GET['withRewardPointSettingDetail'] == '1') {
            $rewardPointSettings = $rewardPointSettings->with('rewardPointSettingDetail');
        }
        return $rewardPointSettings;
    }

    protected function sortingAndLimit($rewardPointSettings)
    {


        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'reward_name'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            if ($_GET['sortBy'] == 'reward_name') {
                $rewardPointSettings = $rewardPointSettings->orderBy(function ($q) {
                    $q->select('reward_name')
                      ->from('reward_point_setting_details')
                      ->whereColumn('reward_point_setting_details.reward_point_setting_id', 'reward_point_settings.id')
                      ->limit(1);
                }, $_GET['sortType']);
            } else {
                $rewardPointSettings = $rewardPointSettings->OrderBy($_GET['sortBy'], $_GET['sortType']);
            }
        }

        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $rewardPointSettings->paginate($limit);
    }

    protected function whereClause($rewardPointSettings)
    {

        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $rewardPointSettings = $rewardPointSettings->whereHas('rewardPointSettingDetail', function ($q) {
                $q->where('reward_name', 'LIKE', '%' . $_GET['searchParam'] . '%');
            });
        }
        return $rewardPointSettings;
    }
}
