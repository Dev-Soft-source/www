<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\CancellationHistory;
use App\Models\FolkRideSetting;
use App\Models\Language;
use App\Models\NoShowHistory;
use App\Models\PhoneNumber;
use App\Models\PinkRideSetting;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\SelectLocationSettingDetail;
use App\Models\SiteSetting;
use App\Models\Step1PageSettingDetail;
use App\Models\User;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfoIconController extends Controller
{
    use StatusResponser;

    public function pinkRideInfo(){
        $pinkRideSetting = PinkRideSetting::first();

        $loggedInUser = Auth::guard('sanctum')->user();
        $user = User::whereId($loggedInUser->id)->select('id', 'gender', 'email_verified', 'driver', 'dob', 'profile_complete', 'pink_ride', 'folks_ride')->first();

        $data = ['pinkRideSetting' => $pinkRideSetting, 'user' => $user];
        return $this->successResponse($data, 'Get pink ride settings successfully');
    }

    public function extraCareRideInfo(){
        $folkRideSetting = FolkRideSetting::first();

        
        $loggedInUser = Auth::guard('sanctum')->user();
        $user = User::whereId($loggedInUser->id)->select('id', 'gender', 'email_verified', 'driver', 'dob', 'profile_complete', 'pink_ride', 'folks_ride')->first();

        $data = ['folkRideSetting' => $folkRideSetting, 'user' => $user];
        return $this->successResponse($data, 'Get extra care ride settings successfully');
    }

    public function postRideSetting(Request $request){
        $pinkRideSetting = PinkRideSetting::first();
        $folkRideSetting = FolkRideSetting::first();
        $siteSetting = SiteSetting::first();

        $loggedInUser = Auth::guard('sanctum')->user();
        $user = User::whereId($loggedInUser->id)->select('id', 'gender', 'email_verified', 'driver', 'dob', 'profile_complete', 'pink_ride', 'folks_ride')->first();

        if ($request->lang_id && $request->lang_id != 0) {
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        // Calculate age
        if ($user->dob) {
            $dob = Carbon::parse($user->dob);
            $user->age = $dob->diffInYears(Carbon::now());
        } else {
            $user->age = null; // Handle case where dob is not set
        }

        if ($user->gender) {
            $user->gender = $user->gender;

            if ($user->gender === 'male') {
                $user->gender_label = $genderLabel->male_option_label;
            } elseif ($user->gender === 'female') {
                $user->gender_label = $genderLabel->female_option_label;
            } elseif ($user->gender === 'prefer not to say') {
                $user->gender_label = $genderLabel->prefer_option_label;
            }
        }

        $ratings = Rating::where('status', 1)->where('type', '1')->get();
        // Calculate average rating
        $filteredRatings = $ratings->filter(function ($rating) use ($user) {
            return $rating->ride->added_by === $user->id;
        });

        $totalAverage = $filteredRatings->avg('average_rating');
        $user->average_rating = $totalAverage;

        $phone_numbers = PhoneNumber::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        $phone_verified = '0';
        foreach ($phone_numbers as $phone_number) {
            if ($phone_number->verified === '1') {
                $phone_verified = $phone_number->verified;
                break;
            }
        }
        $user->phone_verified = $phone_verified;

        $cancellationCount = CancellationHistory::where('user_id', $user->id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();
        $noShowsCount = NoShowHistory::where('user_id', $user->id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
        $totalNoOfRides = Ride::where('added_by', $user->id)
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<', now()->toTimeString());
                        });
                });
            })
            ->count();

        $data = ['user' => $user, 'pinkRideSetting' => $pinkRideSetting, 'folkRideSetting' => $folkRideSetting, 'siteSetting' => $siteSetting, 'cancellationCount' => $cancellationCount, 'noShowsCount' => $noShowsCount, 'totalNoOfRides' => $totalNoOfRides];
        return $this->successResponse($data, 'Get post ride settings successfully');
    }

    public function selectLocationSetting(){
        $selectLocationSetting = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $selectLocationSetting = SelectLocationSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $selectLocationSetting = SelectLocationSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['selectLocationSetting' => $selectLocationSetting];
        return $this->successResponse($data, 'Get select location settings successfully');
    }
}
