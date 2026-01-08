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
    public function create($lang = null)
    {
        $user = auth()->user();
        $postRidePage = null;
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $step3Page = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $step3Page = Step3PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $step3Page = Step3PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

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
        $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '1')
                ->whereHas('ride', function ($query) use ($user_id) {
                    $query->where('added_by', $user_id);
                });
        })
            ->orWhere(function ($query) use ($user_id) {
                // Ratings where type is 2 and booking_id belongs to the user
                $query->where('type', '2')
                    ->whereHas('booking', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });
            })
            ->orWhere(function ($query) use ($user_id) {
                // Ratings where type is null and receiver_id belongs to the user
                $query->where('type', null)
                    ->whereHas('receiver', function ($query) use ($user_id) {
                        $query->where('id', $user_id);
                    });
            })
            ->orderBy('id', 'desc')
            ->get();

        User::whereId($user_id)->update([
            'step' => '3'
        ]);

        return view('step3to5', ['step3Page' => $step3Page, 'user' => $user, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
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
        if ($request->input('action') === 'skip_vehicle_info') {
            User::whereId($id)->update(['step' => '4']);
            session()->forget('uploaded_profile_image');
            return redirect()->route('step4to5', ['lang' => $selectedLanguage->abbreviation]);
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
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:10240',
        ], [], $niceNames);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $file->move($destination_path, $filename);
        } else {
            $filename = '';
        }

        // Create the vehicle record
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
            ]);

        // Move user to step 4 after vehicle is added
        User::whereId($id)->update(['step' => '4']);

        

        session()->forget('uploaded_profile_image');

        return redirect()->route('step4to5', ['lang' => $selectedLanguage->abbreviation]);
    }
}
