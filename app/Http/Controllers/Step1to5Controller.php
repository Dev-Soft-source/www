<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SelectLocationSettingDetail;
use App\Models\Step1PageSettingDetail;
use App\Models\User;
use Illuminate\Http\Request;

class Step1to5Controller extends Controller
{
    public function create($lang = null){
        $user = auth()->user();
        $countries = Country::where('status', '1')->orderBy('name', 'asc')->get();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $step1Page = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $step1Page = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $selectLocationSettingPage = SelectLocationSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $step1Page = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $selectLocationSettingPage = SelectLocationSettingDetail::where('language_id', $selectedLanguage->id)->first();
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

        return view('step1to5',['step1Page' => $step1Page, 'selectLocationSettingPage' => $selectLocationSettingPage, 'user' => $user, 'countries' => $countries, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage]);
    }

    public function update($id, Request $request){
        $selectedLanguage = session('selectedLanguage');
        $step1Page = null;
        $niceNames = [];
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $step1Page = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'first_name' => isset($step1Page->first_name_error) ? $step1Page->first_name_error : '',
                'last_name' => isset($step1Page->last_name_error) ? $step1Page->last_name_error : '',
                'gender' => isset($step1Page->gender_error) ? $step1Page->gender_error : '',
                'dob' => isset($step1Page->dob_error) ? $step1Page->dob_error : '',
                'country' => isset($step1Page->country_error) ? $step1Page->country_error : '',
                'state' => isset($step1Page->state_error) ? $step1Page->state_error : '',
                'city' => isset($step1Page->city_error) ? $step1Page->city_error : '',
                'zipcode' => isset($step1Page->zip_code_error) ? $step1Page->zip_code_error : '',
                'bio' => isset($step1Page->bio_error) ? $step1Page->bio_error : '',
            ];
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $step1Page = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'first_name' => isset($step1Page->first_name_error) ? $step1Page->first_name_error : '',
                'last_name' => isset($step1Page->last_name_error) ? $step1Page->last_name_error : '',
                'gender' => isset($step1Page->gender_error) ? $step1Page->gender_error : '',
                'dob' => isset($step1Page->dob_error) ? $step1Page->dob_error : '',
                'country' => isset($step1Page->country_error) ? $step1Page->country_error : '',
                'state' => isset($step1Page->state_error) ? $step1Page->state_error : '',
                'city' => isset($step1Page->city_error) ? $step1Page->city_error : '',
                'zipcode' => isset($step1Page->zip_code_error) ? $step1Page->zip_code_error : '',
                'bio' => isset($step1Page->bio_error) ? $step1Page->bio_error : '',
            ];
        }

        $request->validate([
            'first_name' => 'required|string|max:25|regex:/^[a-zA-Z\s\-]+$/',
            'last_name' => 'required|string|max:25|regex:/^[a-zA-Z\s\-]+$/',
            'gender' => 'required',
            'dob' => 'required|date',
            'country' => 'required',
            'state' => 'nullable',
            'city' => 'nullable',
            'zipcode' => 'required|string|max:'. (request()->input('country') == 39 ? 7 : 10),
            'bio' => 'required|max:300',
        ], [], $niceNames);

        // Validate state and city existence if provided
        $stateValue = null;
        $cityValue = null;

        if ($request->state && $request->state != '0') {
            $stateExists = \App\Models\State::where('id', $request->state)
                ->where('country_id', $request->country)
                ->exists();
            if (!$stateExists) {
                return redirect()->back()->withErrors(['state' => 'Invalid state selected for the given country'])->withInput();
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
                return redirect()->back()->withErrors(['city' => 'Invalid city selected for the given state'])->withInput();
            }
            $cityValue = $request->city;
        }

        User::whereId($id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'country' => $request->country,
            'state' => $stateValue,
            'city' => $cityValue,
            'zipcode' => $request->zipcode,
            'about' => $request->bio,
            'step' => '2'
        ]);

        $user = User::whereId($id)->first();
        if (!$user->profile_image) {
            if ($request->gender === 'male') {
                User::whereId($id)->update([
                    'profile_image' => 'male.png',
                ]);
            } elseif ($request->gender === 'female') {
                User::whereId($id)->update([
                    'profile_image' => 'female.png',
                ]);
            } elseif ($request->gender === 'prefer not to say') {
                User::whereId($id)->update([
                    'profile_image' => 'neutral.png',
                ]);
            }
        }
        
        return redirect()->route('step2to5', ['lang' => $selectedLanguage->abbreviation]);
    }
}
