<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\FeaturesSettingDetail;
use App\Models\User;
use App\Models\Vehicle;
use App\Traits\StatusResponser;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Models\Step1PageSettingDetail;
use App\Models\Step2PageSettingDetail;
use App\Models\Step3PageSettingDetail;
use App\Models\Step4PageSettingDetail;
use App\Models\Step5PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class StepController extends Controller
{
    use StatusResponser;

    public function step1to5(Request $request){
        $user_id = Auth::guard('sanctum')->user()->id;

        $request->validate([
            'first_name' => 'required|string|max:25|regex:/^[a-zA-Z\s\-]+$/',
            'last_name' => 'required|string|max:25|regex:/^[a-zA-Z\s\-]+$/',
            'gender' => 'required',
            'dob' => 'required|date',
            'country' => 'required',
            'state' => 'nullable',
            'city' => 'nullable',
            'zipcode' => 'required|string|max:'. (request()->input('country') == 39 ? 7 : 10),
            'about' => 'required|max:300',
        ]);

        // Validate state and city existence if provided
        $stateValue = null;
        $cityValue = null;

        if ($request->state && $request->state != '0') {
            $stateExists = \App\Models\State::where('id', $request->state)
                ->where('country_id', $request->country)
                ->exists();
            if (!$stateExists) {
                return $this->errorResponse('Invalid state selected for the given country', 400);
            }
            $stateValue = $request->state;
        }

        if ($request->city && $request->city != '0') {
            $cityQuery = \App\Models\City::where('id', $request->city);
            if ($stateValue) {
                $cityQuery->where('state_id', $stateValue);
            }
            $cityExists = $cityQuery->exists();
            if (!$cityExists) {
                return $this->errorResponse('Invalid city selected for the given state', 400);
            }
            $cityValue = $request->city;
        }

        User::whereId($user_id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'country' => $request->country,
            'state' => $stateValue,
            'city' => $cityValue,
            'zipcode' => $request->zipcode,
            'about' => $request->about,
            'step' => '2'
        ]);

        $user = User::whereId($user_id)->first();
        
        $defaultLangId = Language::where('is_default', '1')->value('id');

        $data = ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'gender' => ucfirst($user->gender), 'profile_image' => $user->profile_image, 'email' => $user->email,
        'about' => $user->about, 'step' => $user->step, 'id' => $user->id, 'langId' => isset($user->lang_id) ? $user->lang_id : $defaultLangId];
        return $this->successResponse($data, 'Step 1 completed successfully');
    }

    public function step2to5(Request $request){
        $user_id = Auth::guard('sanctum')->user()->id;

        $skip = $request->filled('skip') ? $request->skip : '0';

        $customMessages = [
            'max' => 'Can not upload image size greater than 10MB',
        ];

        $request->validate([
            'image' => 'nullable|file|mimes:jpeg,png,gif|max:10240',
        ], $customMessages);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/users_images');
            $file->move($destination_path, $filename);
            User::whereId($user_id)->update([
                'profile_image' => $filename,
            ]);
        }

        User::whereId($user_id)->update([
            'step' => '3',
        ]);

        $user = User::whereId($user_id)->first();
        
        $defaultLangId = Language::where('is_default', '1')->value('id');

        $data = ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'gender' => ucfirst($user->gender), 'profile_image' => $user->profile_image, 'email' => $user->email, 'step' => $user->step, 'id' => $user->id, 'langId' => isset($user->lang_id) ? $user->lang_id : $defaultLangId];
        return $this->successResponse($data, 'Step 2 completed successfully');
    }

    public function step3to5(Request $request){
        $user_id = Auth::guard('sanctum')->user()->id;
        $skip = $request->filled('skip') ? $request->skip : '0';
        $skip_vehicle = $request->filled('skip_vehicle') ? $request->skip_vehicle : '0';
        $skip_license = $request->filled('skip_license') ? $request->skip_license : '0';

        $customMessages = [
            'max' => 'Can not upload image size greater than 10MB',
        ];

        if($skip == "1"){

            User::whereId($user_id)->update([
                'step' => '4',
            ]);

            $user = User::whereId($user_id)->first();
            $defaultLangId = Language::where('is_default', '1')->value('id');
            $data = ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'gender' => ucfirst($user->gender), 'profile_image' => $user->profile_image, 'email' => $user->email, 'step' => $user->step, 'id' => $user->id, 'langId' => isset($user->lang_id) ? $user->lang_id : $defaultLangId];
            return $this->successResponse($data, 'Step 3 completed successfully');

        }else if($skip_vehicle == "0"){
            $request->validate([
                'make' => 'required',
                'model' => 'required',
                'type' => 'required',
                'license_no' => 'required',
                'color' => 'required',
                'year' => 'required',
                'car_type' => 'required',
                'driver_liscense' => 'nullable|file|mimes:pdf,jpeg,png|max:10240',
                'image' => 'nullable|file|mimes:jpeg,png|max:10240',
            ], $customMessages);
        }else if($skip_license == "0"){
            $request->validate([
                'driver_liscense' => 'required|file|mimes:pdf,jpeg,png|max:10240',
            ], $customMessages);
        }


        

        if (isset($request->driver_liscense) && $request->hasFile('driver_liscense')) {
            $file = $request->file('driver_liscense');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/driver_liscenses');
            $file->move($destination_path,$filename);

            $fileOriginal = $request->file('driver_license_original_upload');
            $filenameOriginal = $fileOriginal->getClientOriginalName();
            $destination_path = public_path('/driver_liscenses');
            $fileOriginal->move($destination_path,$filenameOriginal);

            User::whereId($user_id)->update([
                'driver_liscense' => $filename,
                'driver_license_original_upload' => $filenameOriginal,
                'driver_license_upload' => Carbon::now(),
                'driver' => 2,
            ]);
        }

        User::whereId($user_id)->update([
            'step' => '4',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $file->move($destination_path,$filename);
        } else {
            $filename = '';
        }
        
        if ($skip_vehicle == '0') {
            Vehicle::create([
                'user_id' => $user_id,
                'make' => $request->make,
                'model' => $request->model,
                'type' => $request->type,
                'liscense_no' => $request->license_no,
                'color' => $request->color,
                'year' => $request->year,
                'car_type' => $request->car_type,
                'image' => $filename,
            ]);
        }

        $user = User::whereId($user_id)->first();
        
        $defaultLangId = Language::where('is_default', '1')->value('id');

        $data = ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'gender' => ucfirst($user->gender), 'profile_image' => $user->profile_image, 'email' => $user->email, 'step' => $user->step, 'id' => $user->id, 'langId' => isset($user->lang_id) ? $user->lang_id : $defaultLangId];
        return $this->successResponse($data, 'Step 3 completed successfully');
    }

    public function online_status(Request $request){
        $user_id = Auth::guard('sanctum')->user()->id;

        $request->validate([
            'online' => 'required',
        ]);

        User::whereId($user_id)->update([
            'online' => $request->online,
        ]);

        $user = User::whereId($user_id)->first();

        $data = ['user' => $user];
        return $this->successResponse($data, 'Online status updated successfully');
    }

    public function step1Index(Request $request)
    {
        $step1Page = null;
        if ($request->lang_id && $request->lang_id != 0) {
            
            $selectedLanguage = Language::where('id', $request->lang_id)->first();
            // Retrieve the Step1PageSettingDetail associated with the selected language
            $step1Page = Step1PageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $step1Page = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
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
            'email' => trans('validation.email'),
            'string' => trans('validation.string'),
            'max' => trans('validation.max.string'),
            'date' => trans('validation.date'),
        ];


        $data = ['step1Page' => $step1Page, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Step1 page get successfully');
    }

    public function step2Index(Request $request)
    {
        $step2Page = null;
        if ($request->lang_id && $request->lang_id != 0) {
            
            $selectedLanguage = Language::where('id', $request->lang_id)->first();
            // Retrieve the Step2PageSettingDetail associated with the selected language
            $step2Page = Step2PageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $step2Page = Step2PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
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
            'image' => trans('validation.image'),
            'mimes' => trans('validation.mimes'),
            'max.file' => trans('validation.max.file'),
        ];

        $data = ['step2Page' => $step2Page, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Step2 page get successfully');
    }

    public function step3Index(Request $request)
    {
        $step3Page = null;
        if ($request->lang_id && $request->lang_id != 0) {
            
            $selectedLanguage = Language::where('id', $request->lang_id)->first();
            // Retrieve the Step3PageSettingDetail associated with the selected language
            $step3Page = Step3PageSettingDetail::where('language_id', $request->lang_id)->first();
            $step4Page = Step5PageSettingDetail::where('language_id', $request->lang_id)->first();
            $postRidePage = PostRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            $step3Page->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
            $step3Page->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($request->lang_id)->value('name');
            $step3Page->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
            $step3Page->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($request->lang_id)->value('name');
            $step3Page->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
            $step3Page->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($request->lang_id)->value('name');
            $step3Page->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
            $step3Page->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($request->lang_id)->value('name');
            $step3Page->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
            $step3Page->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($request->lang_id)->value('name');
            $step3Page->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
            $step3Page->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($request->lang_id)->value('name');
            $step3Page->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
            $step3Page->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($request->lang_id)->value('name');
            $step3Page->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
            $step3Page->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($request->lang_id)->value('name');
            $step3Page->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
            $step3Page->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($request->lang_id)->value('name');
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $step3Page = Step3PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $step4Page = Step5PageSettingDetail::where('language_id', $request->lang_id)->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $step3Page->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
                $step3Page->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $step3Page->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
                $step3Page->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $step3Page->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
                $step3Page->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $step3Page->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
                $step3Page->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $step3Page->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
                $step3Page->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $step3Page->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
                $step3Page->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $step3Page->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
                $step3Page->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $step3Page->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
                $step3Page->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $step3Page->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
                $step3Page->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');
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
            'image' => trans('validation.image'),
            'mimes' => trans('validation.mimes'),
            'max.file' => trans('validation.max.file'),
        ];

        $data = ['step3Page' => $step3Page, 'step4Page' => $step4Page, 'validationMessages' => $validationMessages]; 
        return $this->successResponse($data, 'Step3 page get successfully');
    }

    public function step4Index(Request $request)
    {
        $step4Page = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $selectedLanguage = Language::where('id', $request->lang_id)->first();
            // Retrieve the Step4PageSettingDetail associated with the selected language
            $step4Page = Step4PageSettingDetail::where('language_id', $request->lang_id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('enter_code_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $step4Page = Step4PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('enter_code_message')->first();
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
        ];

        $data = ['step4Page' => $step4Page, 'messages' => $messages, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Step4 page get successfully');
    }
}
