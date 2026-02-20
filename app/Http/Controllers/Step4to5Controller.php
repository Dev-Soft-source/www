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

        User::whereId($user->id)->update([
            'step' => '4'
        ]);

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
            // Manual validation for file extensions if file is uploaded (to avoid requiring php_fileinfo extension)
            if ($request->hasFile('driver_liscense')) {
                $file = $request->file('driver_liscense');
                $extension = strtolower($file->getClientOriginalExtension());
                $allowedExtensions = ['pdf', 'jpeg', 'jpg', 'png', 'gif'];

                if (!in_array($extension, $allowedExtensions)) {
                    return redirect()->back()->withErrors(['driver_liscense' => 'The driver license must be a file of type: pdf, jpeg, png, jpg, gif.'])->withInput();
                }
            }

            $validated = $request->validate([
                'driver_liscense' => 'required|file|max:10240',
            ], [], $niceNames);

            if ($request->hasFile('driver_liscense')) {
                $file = $request->file('driver_liscense');
                $filename = $file->getClientOriginalName();
                $destination_path = public_path('/driver_liscenses');
                $file->move($destination_path, $filename);
                User::whereId($id)->update([
                    'driver_liscense' => $filename,
                    'driver_license_original_upload' => $filename,
                    'driver_license_upload' => Carbon::now(),
                    'driver' => 2,
                    'step' => '4'
                ]);
            }
        }

        session()->forget('uploaded_profile_image');

        return redirect()->route('step5to5', ['lang' => $selectedLanguage->abbreviation]);
    }
}
