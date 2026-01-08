<?php

namespace App\Http\Controllers\Api\App;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProfilePhotoSettingDetail;
use App\Traits\StatusResponser;

class ProfilePhotoSettingController extends Controller
{
    use StatusResponser;
    public function ProfilePhotoSettingIndex(Request $request)
    {
        $profilePhotoPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $profilePhotoPage = ProfilePhotoSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $profilePhotoPage = ProfilePhotoSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['profilePhotoPage' => $profilePhotoPage];
        return $this->successResponse($data, 'Search profile photo page get successfully');
    }
}
