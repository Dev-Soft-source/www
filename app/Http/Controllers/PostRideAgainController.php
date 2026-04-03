<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Notification;
use App\Models\PostRidePageSettingDetail;
use App\Models\Ride;
use App\Models\TripsPageSettingDetail;
use Illuminate\Http\Request;

class PostRideAgainController extends Controller
{
    public function CurrentRides($lang = null)
    {
        $rides = Ride::where('added_by', auth()->user()->id)
            ->notCancelled()
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '>=', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '>=', now()->toTimeString());
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(6);


        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);


        return view('post_ride_again', [
            'rides' => $rides,
            'tripsPage' => $tripsPage,
        ]);
    }

    public function PastRides($lang = null)
    {
        $rides = Ride::where('added_by', auth()->user()->id)
            ->notCancelled()
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<=', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<=', now()->toTimeString());
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(6);
        
        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('post_ride_again', [
            'rides' => $rides,
            'tripsPage' => $tripsPage,
        ]);
    }

    public function CancelledRides($lang = null)
    {
        $rides = Ride::where('added_by', auth()->user()->id)
            ->cancelled()
            ->orderBy('id', 'desc')
            ->paginate(6);


        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        
        return view('post_ride_again', [
            'rides' => $rides,
            'tripsPage' => $tripsPage,
            'postRidePage' => $postRidePage
        ]);
    }
}
