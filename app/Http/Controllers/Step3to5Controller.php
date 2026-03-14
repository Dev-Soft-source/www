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


        $selectedLanguage = session('selectedLanguage');
        $step3Page = null;
        $niceNames = [];
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $step3Page = Step3PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'make' => isset($step3Page->make_error) ? $step3Page->make_error : '',
                'model' => isset($step3Page->model_error) ? $step3Page->model_error : '',
                'type' => isset($step3Page->vehicle_type_error) ? $step3Page->vehicle_type_error : '',
                'color' => isset($step3Page->color_error) ? $step3Page->color_error : '',
                'liscense_no' => isset($step3Page->license_error) ? $step3Page->license_error : '',
                'year' => isset($step3Page->year_error) ? $step3Page->year_error : '',
                'car_type' => isset($step3Page->fuel_error) ? $step3Page->fuel_error : '',
                'driver_liscense' => isset($step3Page->driver_license_error) ? $step3Page->driver_license_error : '',
                'image' => isset($step3Page->photo_error) ? $step3Page->photo_error : '',
            ];
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $step3Page = Step3PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'make' => isset($step3Page->make_error) ? $step3Page->make_error : '',
                'model' => isset($step3Page->model_error) ? $step3Page->model_error : '',
                'type' => isset($step3Page->vehicle_type_error) ? $step3Page->vehicle_type_error : '',
                'color' => isset($step3Page->color_error) ? $step3Page->color_error : '',
                'liscense_no' => isset($step3Page->license_error) ? $step3Page->license_error : '',
                'year' => isset($step3Page->year_error) ? $step3Page->year_error : '',
                'car_type' => isset($step3Page->fuel_error) ? $step3Page->fuel_error : '',
                'driver_liscense' => isset($step3Page->driver_license_error) ? $step3Page->driver_license_error : '',
                'image' => isset($step3Page->photo_error) ? $step3Page->photo_error : '',
            ];
        }

        // If user clicks Skip Vehicle Info -> go to Step 4 directly (no validations)
        // if ($request->input('action') === 'skip_vehicle_info') {
        //     User::whereId($id)->update(['step' => '4']);
        //     session()->forget('uploaded_profile_image');
        //     return redirect()->route('step4to5', ['lang' => $selectedLanguage->abbreviation]);
        // }

        // Manual validation for file extensions if file is uploaded (to avoid requiring php_fileinfo extension)
        $maxVehicleImageSizeKb = $this->getMaxVehicleImageSizeKb();
        $maxVehicleImageSizeMessage = 'The image must be less than ' . round($maxVehicleImageSizeKb / 1024, 2) . 'MB.';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif'];

            if ($file->getSize() > ($maxVehicleImageSizeKb * 1024)) {
                return redirect()->back()
                    ->withErrors(['image' => $maxVehicleImageSizeMessage])
                    ->withInput();
            }

            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->withErrors(['image' => 'The image must be a file of type: jpeg, png, jpg, gif.'])->withInput();
            }
        }

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
        ], [
            'make.required' => 'The make is required',
            'model.required' => 'The model is required',
            'type.required' => 'The vehicle type is required',
            'liscense_no.required' => 'The license number is required',
            'color.required' => 'The color is required',
            'year.required' => 'The year is required',
            'car_type.required' => 'The car type is required',
            'image.nullable' => 'The image may be null',
            'image.file' => 'The image must be a file',
            'image.max' => $maxVehicleImageSizeMessage,
        ], $niceNames);
        
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

        return redirect()->route('step4to5', ['lang' => $selectedLanguage->abbreviation]);
    }
}
