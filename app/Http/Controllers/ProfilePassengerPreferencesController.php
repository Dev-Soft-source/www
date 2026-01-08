<?php

namespace App\Http\Controllers;

use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Http\Request;

class ProfilePassengerPreferencesController extends Controller
{
    public function index($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $findRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
    
            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();
            $rides = Ride::where('added_by',$user_id)->get();
    
            if ($rides->isNotEmpty()) {
                // Fetch ratings where the driver_id matches the authenticated user's ID
                $ratings = Rating::where(function ($query) use ($user_id) {
                    // Ratings where type is 1 and ride_id belongs to the user
                    $query->where('type', '1')
                          ->whereHas('ride', function ($query) use ($user_id) {
                              $query->where('added_by', $user_id);
                          });
                })
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();

                // Calculate total average
                $overallRating = $ratings->avg('average_rating') ?? 0;
            } else {
                $overallRating = 5;
            }

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

            return view('profile_passenger_preferences',['user' => $user,'findRidePage' => $findRidePage,'overallRating' => $overallRating,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    public function update($id, Request $request){
        $customMessages = [
            'array' => 'The :attribute must be an array',
        ];

        $validated = $request->validate([
            'smoke' => 'required',
            'pets' => 'required',
            'features' => 'array',
        ], $customMessages);

        // Join the selected checkboxes with semicolons.
        $features = implode('=', $request->input('features', []));

        User::whereId($id)->update([
            'passenger_smoke' => $request->smoke,
            'passenger_pets' => $request->pets,
            'passenger_features' => $features,
        ]);

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        return redirect()->route('profile.passenger_preferences', ['lang' => $selectedLanguage->abbreviation])->with('message', 'Passenger preferences updated successfully');
    }
}
