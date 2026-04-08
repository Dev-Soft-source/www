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
    private const DEFAULT_MAX_IMAGE_SIZE_KB = 10240;

    private function getMaxVehicleImageSizeKb(): int
    {
        return (int) env('DEFAULT_MAX_IMAGE_SIZE_KB', self::DEFAULT_MAX_IMAGE_SIZE_KB);
    }

    public function create($lang = null)
    {
        $user = auth()->user();

        $step3Page = Step3PageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $user_id = auth()->user()->id;

        // from step2 with skip -> update step2 to 1 and stay on step3 page (no validations)
        // if (request()->has('skip')) {
        //     User::whereId($user_id)->update([
        //         'step2' => 2
        //     ]);
        // }

        return view('step3to5', [
            'step3Page' => $step3Page,
            'user' => $user,
        ]);
    }

    public function store($id, Request $request)
    {


        $maxVehicleImageSizeKb = $this->getMaxVehicleImageSizeKb();

        // Otherwise, user is adding vehicle -> validate only vehicle fields
        $validated = $request->validate([
            'make' => 'required',
            'model' => 'required',
            'type' => 'required|integer|exists:features_setting_detail,features_setting_id',
            'license_no' => 'required',
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
            'type' => Vehicle::normalizeVehicleTypeId($request->type),
            'license_no' => $request->license_no ?? '',
            'color' => $request->color ?? '',
            'year' => $request->year ?? '',
            'car_type' => $request->car_type ?? '',
            'image' => $filename,
            'original_image' => $filename,
            'primary_vehicle' => 1,
        ]);


        User::whereId($id)->update([
            // 'step3' => 1,
            'step' => 4
        ]);

        session()->forget('uploaded_profile_image');

        return redirect()->route('step4to5', ['lang' => $this->selectedLanguage->abbreviation]);
    }
}
