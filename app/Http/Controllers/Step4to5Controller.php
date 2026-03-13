<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Notification;
use App\Models\Step4PageSettingDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Step4to5Controller extends Controller
{
    public function create($lang = null)
    {
        $user = auth()->user();

        $step4Page = Step4PageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        // from step3 with skip -> update step3 to 1 and stay on step4 page (no validations)
        if (request()->has('skip')) {
            User::whereId($user->id)->update([
                'step3' => 2
            ]);
        }

        return view('step4to5', [
            'step4Page' => $step4Page,
            'user' => $user
        ]);
    }

    public function store($id, Request $request)
    {
        $selectedLanguage = $this->selectedLanguage;

        $step4Page = Step4PageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $niceNames = [
            'driver_liscense' => isset($step4Page->driver_license_error) ? $step4Page->driver_license_error : '',
        ];

        if ($request->input('action') != 'skip_license') {
            $removeLicense = $request->input('remove_driver_license') === '1';

            if ($request->hasFile('driver_liscense')) {
                $file = $request->file('driver_liscense');
                $extension = strtolower($file->getClientOriginalExtension());
                $allowedExtensions = ['pdf', 'jpeg', 'jpg', 'png', 'gif'];

                if (!in_array($extension, $allowedExtensions)) {
                    return redirect()->back()->withErrors(['driver_liscense' => 'The driver license must be a file of type: pdf, jpeg, png, jpg, gif.'])->withInput();
                }
            }

            $rules = [
                'driver_liscense' => $removeLicense ? 'nullable|file|max:10240' : 'required|file|max:10240',
            ];
            $validated = $request->validate($rules, [
                'driver_liscense.required' => 'The driver license is required',
                'driver_liscense.file' => 'The driver license must be a file',
                'driver_liscense.max' => 'The driver license must be less than 10MB',
            ], $niceNames);

            if ($removeLicense) {
                User::whereId($id)->update([
                    'driver_liscense' => null,
                    'driver_license_original_upload' => null,
                    'driver_license_upload' => null,
                    'driver' => 2,
                    'step4' => 1
                ]);
            } elseif ($request->hasFile('driver_liscense')) {
                $file = $request->file('driver_liscense');
                $filename = $file->getClientOriginalName();
                $destination_path = public_path('/driver_liscenses');
                $file->move($destination_path, $filename);
                User::whereId($id)->update([
                    'driver_liscense' => $filename,
                    'driver_license_original_upload' => $filename,
                    'driver_license_upload' => Carbon::now(),
                    'driver' => 2,
                    'step4' => 1
                ]);
            }
        }

        session()->forget('uploaded_profile_image');

        return redirect()->route('step5to5', ['lang' => $selectedLanguage->abbreviation]);
    }
}
