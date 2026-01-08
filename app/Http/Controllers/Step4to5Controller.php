<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Notification;
use App\Models\Step5PageSettingDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Step4to5Controller extends Controller
{
    public function create($lang = null)
    {
        $user = auth()->user();
        $languages = Language::all();
        
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        
        $selectedLanguage = session('selectedLanguage');
        $step4Page = null;
        
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $step4Page = Step5PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $step4Page = Step5PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

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

        User::whereId($user->id)->update([
            'step' => '4'
        ]);

        return view('step4to5', [
            'step4Page' => $step4Page,
            'user' => $user,
            'languages' => $languages,
            'selectedLanguage' => $selectedLanguage,
            'notifications' => $notifications, 
        ]);
    }

    public function store($id, Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        $step4Page = null;
        $niceNames = [];

        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $step4Page = Step5PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'driver_liscense' => isset($step4Page->driver_license_error) ? $step4Page->driver_license_error : '',
            ];
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $step4Page = Step5PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'driver_liscense' => isset($step4Page->driver_license_error) ? $step4Page->driver_license_error : '',
            ];
        }

        if ($request->input('action') != 'skip_license') {
            $validated = $request->validate([
                'driver_liscense' => 'required|file|mimes:pdf,jpeg,png,jpg,gif|max:10240',
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