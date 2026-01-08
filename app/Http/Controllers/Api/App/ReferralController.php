<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ReferralPageSettingDetail;
use App\Models\ReferralDetail;
use App\Models\User;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    use StatusResponser;

    public function myReferralList(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $currentDate = date('Y-m-d H:i:s');
        
        $myReferralList = ReferralDetail::with(['user' => function ($query) {
            $query->select('id', 'first_name', 'last_name');
        }])->where('referral_user_id', $user_id)->get();

        $referralSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $referralSettingPage = ReferralPageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $referralSettingPage = ReferralPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $referralLink = User::where('id', $user_id)->value('referral_uuid');

        $data = ['myReferralList' => $myReferralList, 'referralLink' => $referralLink, 'referralSettingPage' => $referralSettingPage];
        return $this->successResponse($data, 'Get my referral successfully');
    }
}
