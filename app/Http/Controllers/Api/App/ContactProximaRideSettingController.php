<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\ContactProximaRideSettingDetail;
use App\Models\Language;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ContactProximaRideSettingController extends Controller
{
    use StatusResponser;
    public function ContactProximaRidePageSettingIndex(Request $request)
    {
        $contactPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $selectedLanguage = Language::whereId($request->lang_id)->first();
            $contactPage = ContactProximaRideSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $contactPage = ContactProximaRideSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        if ($selectedLanguage) {
            $locale = $selectedLanguage->abbreviation;
        } else {
            $locale = 'en';
        }

        App::setLocale($locale);

        $validationMessages = [
            'required' => trans('validation.required'),
            'string' => trans('validation.string'),
            'max' => trans('validation.max.string'),
            'email' => trans('validation.email'),
        ];

        $data = ['contactPage' => $contactPage, 'validationMessages' => $validationMessages];
    return $this->successResponse($data, 'Search contact proximaride get successfully');
    }
}
