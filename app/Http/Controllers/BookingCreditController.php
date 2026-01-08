<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use App\Models\Language;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class BookingCreditController extends Controller
{
    public function index($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();
            $packages = CreditPackage::all();

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

            return view('booking_credits',['user' => $user, 'packages' => $packages,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
        
    }

    public function update($id, Request $request){
        $validated = $request->validate([
            'card' => 'required',
        ]);

        return redirect()->back();
    }
}