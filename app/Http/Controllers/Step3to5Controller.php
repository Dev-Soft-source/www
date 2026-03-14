<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Notification;
use App\Models\Step3PageSettingDetail;
use App\Models\PostRidePageSettingDetail;
use App\Models\FeaturesSettingDetail;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Step3to5Controller extends Controller
{
    private const DEFAULT_MAX_VEHICLE_IMAGE_SIZE_KB = 10240;

    private function getMaxVehicleImageSizeKb(): int
    {
        return (int) env('MAX_VEHICLE_IMAGE_SIZE_KB', self::DEFAULT_MAX_VEHICLE_IMAGE_SIZE_KB);
    }

    public function create($lang = null)
    {
        $user = auth()->user();
        
        $step3Page = Step3PageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $selectedLanguage = $this->selectedLanguage;
        
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

        $user_id = auth()->user()->id;

        // from step2 with skip -> update step2 to 1 and stay on step3 page (no validations)
        if (request()->has('skip')) {
            User::whereId($user_id)->update([
                'step2' => 2
            ]);
        }

        $maxVehicleImageSizeKb = $this->getMaxVehicleImageSizeKb();

        return view('step3to5', [
            'step3Page' => $step3Page,
            'user' => $user,
            'maxVehicleImageSizeKb' => $maxVehicleImageSizeKb,
            'maxVehicleImageSizeMb' => round($maxVehicleImageSizeKb / 1024, 2),
        ]);
    }

    public function store($id, Request $request)
    {


        $maxVehicleImageSizeKb = $this->getMaxVehicleImageSizeKb();

        // Otherwise, user is adding vehicle -> validate only vehicle fields
        $validated = $request->validate([
            'make' => 'required',
            'model' => 'required',
            'type' => 'required',
            'liscense_no' => 'required',
            'color' => 'required',
            'year' => 'required',
            'car_type' => 'required',
            'image' => 'nullable|file|max:' . $maxVehicleImageSizeKb,
        ]);
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $file->move($destination_path, $filename);
        } else {
            $filename = '';
        }

        // Set any existing vehicles as non-primary so the new one can be primary by default
        Vehicle::where('user_id', auth()->user()->id)->update(['primary_vehicle' => 0]);

        // Create the vehicle record (primary by default)
        Vehicle::create([
                'user_id' => auth()->user()->id,
                'make' => $request->make ?? '',
                'model' => $request->model ?? '',
                'type' => $request->type ?? '',
                'liscense_no' => $request->liscense_no ?? '',
                'color' => $request->color ?? '',
                'year' => $request->year ?? '',
                'car_type' => $request->car_type ?? '',
                'image' => $filename,
                'original_image' => $filename,
                'primary_vehicle' => 1,
            ]);


        User::whereId($id)->update(['step3' => 1]);

        session()->forget('uploaded_profile_image');

        return redirect()->route('step4to5', ['lang' => $this->selectedLanguage->abbreviation]);
    }
}
