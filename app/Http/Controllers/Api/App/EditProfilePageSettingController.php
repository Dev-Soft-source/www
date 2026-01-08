<?php

namespace App\Http\Controllers\Api\App;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Models\EditProfilePageSettingDetail;

class EditProfilePageSettingController extends Controller
{
    use StatusResponser;
    public function EditProfilePageSettingIndex(Request $request)
    {
        $editProfilePage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $editProfilePage = EditProfilePageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $editProfilePage = EditProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['editProfilePage' => $editProfilePage];
        return $this->successResponse($data, 'Search edit profile page get successfully');
    }
}
