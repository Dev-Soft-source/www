<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\MyReviewSettingDetail;
use App\Models\Notification;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\ReviewSettingDetail;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function RatingsLeft($lang = null){
        $languages = Language::all();
        $reviewSettingPage = null;
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $reviewSettingPage = ReviewSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $reviewSettingPage = ReviewSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        }
        
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $ratings = Rating::where('posted_by',$user_id)->whereIn('type',['1','2'])->orderBy('id', 'desc')->get();
    
            // Filter out non-numeric values for the columns and then calculates the average
            $VehicleRatings = $ratings->filter(function ($rating) {
                return is_numeric($rating->vehicle_condition);
            });
            $VehicleCondition = $VehicleRatings->avg('vehicle_condition');
            $ConsciousRatings = $ratings->filter(function ($rating) {
                return is_numeric($rating->conscious);
            });
            $conscious = $ConsciousRatings->avg('conscious');
            $Comfort = $ratings->filter(function ($rating) {
                return is_numeric($rating->comfort);
            });
            $comfort = $Comfort->avg('comfort');
            $Communication = $ratings->filter(function ($rating) {
                return is_numeric($rating->communication);
            });
            $communication = $Communication->avg('communication');
            $Attitude = $ratings->filter(function ($rating) {
                return is_numeric($rating->attitude);
            });
            $attitude = $Attitude->avg('attitude');
            $Hygiene = $ratings->filter(function ($rating) {
                return is_numeric($rating->hygiene);
            });
            $hygiene = $Hygiene->avg('hygiene');
            $Respect = $ratings->filter(function ($rating) {
                return is_numeric($rating->respect);
            });
            $respect = $Respect->avg('respect');
            $Safety = $ratings->filter(function ($rating) {
                return is_numeric($rating->safety);
            });
            $safety = $Safety->avg('safety');
            $Timeliness = $ratings->filter(function ($rating) {
                return is_numeric($rating->timeliness);
            });
            $timeliness = $Timeliness->avg('timeliness');

            // Calculate averages for each rating category
            $validAverages = [];
            $validAverages[] = $VehicleCondition;
            $validAverages[] = $conscious;
            $validAverages[] = $comfort;
            $validAverages[] = $communication;
            $validAverages[] = $attitude;
            $validAverages[] = $hygiene;
            $validAverages[] = $respect;
            $validAverages[] = $safety;
            $validAverages[] = $timeliness;

            // Filter out non-empty averages
            $validAverages = array_filter($validAverages, function ($average) {
                return !is_null($average);
            });

            // Calculate total average
            $totalAverage = count($validAverages) > 0 ? array_sum($validAverages) / count($validAverages) : null;

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
    
            return view('ratings_left',[
                'reviewSetting' => $reviewSetting,
                'ProfilePage' => $ProfilePage,
                'ProfileSetting' => $ProfileSetting,
                'ratings' => $ratings,
                'VehicleCondition' => $VehicleCondition,
                'conscious' => $conscious,
                'comfort' => $comfort,
                'communication' => $communication,
                'attitude' => $attitude,
                'hygiene' => $hygiene,
                'respect' => $respect,
                'safety' => $safety,
                'timeliness' => $timeliness,
                'totalAverage' => $totalAverage,
                'notifications' => $notifications,
                'languages' => $languages,
                'selectedLanguage' => $selectedLanguage,
                'reviewSettingPage' => $reviewSettingPage
            ]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    public function RatingsLeftToPassengers($lang = null){
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
            $ratings = Rating::where('posted_by',$user_id)->where('type',['2','3'])->orderBy('id', 'desc')->get();
    
            // Filter out non-numeric values for the columns and then calculates the average
            $ConsciousRatings = $ratings->filter(function ($rating) {
                return is_numeric($rating->conscious);
            });
            $conscious = $ConsciousRatings->avg('conscious');
            $Comfort = $ratings->filter(function ($rating) {
                return is_numeric($rating->comfort);
            });
            $comfort = $Comfort->avg('comfort');
            $Communication = $ratings->filter(function ($rating) {
                return is_numeric($rating->communication);
            });
            $communication = $Communication->avg('communication');
            $Attitude = $ratings->filter(function ($rating) {
                return is_numeric($rating->attitude);
            });
            $attitude = $Attitude->avg('attitude');
            $Hygiene = $ratings->filter(function ($rating) {
                return is_numeric($rating->hygiene);
            });
            $hygiene = $Hygiene->avg('hygiene');
            $Respect = $ratings->filter(function ($rating) {
                return is_numeric($rating->respect);
            });
            $respect = $Respect->avg('respect');
            $Safety = $ratings->filter(function ($rating) {
                return is_numeric($rating->safety);
            });
            $safety = $Safety->avg('safety');
            $Timeliness = $ratings->filter(function ($rating) {
                return is_numeric($rating->timeliness);
            });
            $timeliness = $Timeliness->avg('timeliness');

            // Calculate averages for each rating category
            $validAverages = [];
            $validAverages[] = $conscious;
            $validAverages[] = $comfort;
            $validAverages[] = $communication;
            $validAverages[] = $attitude;
            $validAverages[] = $hygiene;
            $validAverages[] = $respect;
            $validAverages[] = $safety;
            $validAverages[] = $timeliness;

            // Filter out non-empty averages
            $validAverages = array_filter($validAverages, function ($average) {
                return !is_null($average);
            });

            // Calculate total average
            $totalAverage = count($validAverages) > 0 ? array_sum($validAverages) / count($validAverages) : null;
    
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

            return view('ratings_left_to_passengers',[
                'ratings' => $ratings,
                'conscious' => $conscious,
                'comfort' => $comfort,
                'communication' => $communication,
                'attitude' => $attitude,
                'hygiene' => $hygiene,
                'respect' => $respect,
                'safety' => $safety,
                'timeliness' => $timeliness,
                'totalAverage' => $totalAverage,
                'notifications' => $notifications,
                'languages' => $languages,
                'selectedLanguage' => $selectedLanguage,
            ]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    public function RatingsReceived($lang = null){
        $user_id = auth()->user()->id;

        $reviewSettingPage = null;

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $reviewSettingPage = ReviewSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $reviewSettingPage = ReviewSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        }

        $allRatings = Rating::where(function ($query) use ($user_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '2')
                  ->whereHas('booking', function ($query) use ($user_id) {
                      $query->where('user_id', $user_id);
                  });
        })
        ->orWhere(function ($query) use ($user_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '1')
                  ->whereHas('ride', function ($query) use ($user_id) {
                      $query->where('added_by', $user_id);
                  });
        })
        ->with(['from' => function ($query) {
            $query->withTrashed(); // Include soft-deleted users
        }])
        ->orderBy('id', 'desc')
        ->get();

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

        return view('ratings_received', [
            'reviewSetting' => $reviewSetting,
            'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,
            'allRatings' => $allRatings,
            'notifications' => $notifications,
            'languages' => $languages,
            'selectedLanguage' => $selectedLanguage,
            'reviewSettingPage' => $reviewSettingPage
        ]);
    }

    public function RatingsReceivedByPassengers($lang = null){
        $user_id = auth()->user()->id;

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

        $ratings = Rating::where(function ($query) use ($user_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '1')
                  ->whereHas('ride', function ($query) use ($user_id) {
                      $query->where('added_by', $user_id);
                  });
        })
        ->orWhere(function ($query) use ($user_id) {
            // Ratings where type is 3 and booking_id belongs to the user
            $query->where('type', '3')
                  ->whereHas('booking', function ($query) use ($user_id) {
                      $query->where('user_id', $user_id);
                  });
        })
        ->where('status', 1)
        ->orderBy('id', 'desc')
        ->get();

        // Filter out non-numeric values for the columns and then calculates the average
        $VehicleRatings = $ratings->filter(function ($rating) {
            return is_numeric($rating->vehicle_condition);
        });
        $VehicleCondition = $VehicleRatings->avg('vehicle_condition');
        $ConsciousRatings = $ratings->filter(function ($rating) {
            return is_numeric($rating->conscious);
        });
        $conscious = $ConsciousRatings->avg('conscious');
        $Comfort = $ratings->filter(function ($rating) {
            return is_numeric($rating->comfort);
        });
        $comfort = $Comfort->avg('comfort');
        $Communication = $ratings->filter(function ($rating) {
            return is_numeric($rating->communication);
        });
        $communication = $Communication->avg('communication');
        $Attitude = $ratings->filter(function ($rating) {
            return is_numeric($rating->attitude);
        });
        $attitude = $Attitude->avg('attitude');
        $Hygiene = $ratings->filter(function ($rating) {
            return is_numeric($rating->hygiene);
        });
        $hygiene = $Hygiene->avg('hygiene');
        $Respect = $ratings->filter(function ($rating) {
            return is_numeric($rating->respect);
        });
        $respect = $Respect->avg('respect');
        $Safety = $ratings->filter(function ($rating) {
            return is_numeric($rating->safety);
        });
        $safety = $Safety->avg('safety');
        $Timeliness = $ratings->filter(function ($rating) {
            return is_numeric($rating->timeliness);
        });
        $timeliness = $Timeliness->avg('timeliness');

        // Calculate averages for each rating category
        $validAverages = [];
        $validAverages[] = $VehicleCondition;
        $validAverages[] = $conscious;
        $validAverages[] = $comfort;
        $validAverages[] = $communication;
        $validAverages[] = $attitude;
        $validAverages[] = $hygiene;
        $validAverages[] = $respect;
        $validAverages[] = $safety;
        $validAverages[] = $timeliness;

        // Filter out non-empty averages
        $validAverages = array_filter($validAverages, function ($average) {
            return !is_null($average);
        });

        // Calculate total average
        $totalAverage = count($validAverages) > 0 ? array_sum($validAverages) / count($validAverages) : null;

        $allRatings = Rating::where(function ($query) use ($user_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '1')
                  ->whereHas('ride', function ($query) use ($user_id) {
                      $query->where('added_by', $user_id);
                  });
        })
        ->orWhere(function ($query) use ($user_id) {
            // Ratings where type is 3 and booking_id belongs to the user
            $query->where('type', '3')
                  ->whereHas('booking', function ($query) use ($user_id) {
                      $query->where('user_id', $user_id);
                  });
        })
        ->orderBy('id', 'desc')
        ->get();

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

        return view('ratings_received_by_passengers', [
            'ratings' => $ratings,
            'allRatings' => $allRatings,
            'VehicleCondition' => $VehicleCondition,
            'conscious' => $conscious,
            'comfort' => $comfort,
            'communication' => $communication,
            'attitude' => $attitude,
            'hygiene' => $hygiene,
            'respect' => $respect,
            'safety' => $safety,
            'timeliness' => $timeliness,
            'totalAverage' => $totalAverage,
            'notifications' => $notifications,
            'languages' => $languages,
            'selectedLanguage' => $selectedLanguage,
        ]);
    }
}