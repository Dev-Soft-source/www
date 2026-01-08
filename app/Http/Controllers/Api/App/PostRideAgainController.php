<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\PostRidePageSettingDetail;
use App\Models\Language;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostRideAgainController extends Controller
{
    use StatusResponser;

    public function CurrentRides(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $rides = Ride::where('added_by', $user_id)
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '>=', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '>=', now()->toTimeString());
                        });
                });
            })->with(['rideDetail' => function($q){
                $q->where('default_ride','1');
            }])
            ->select('id','pickup','dropoff')
            ->orderBy('id', 'desc')
            ->paginate($request->paginate_limit);

        $postRidePage = null;
        $messages = null;
        if ($request->lang_id && $request->lang_id != 0) {
            
            $selectedLanguage = Language::where('id', $request->lang_id)->first();
            // Retrieve the PostRidePageSettingDetail associated with the selected language
            $postRidePage = PostRidePageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['rides' => $rides, 'postRidePage' => $postRidePage];
        return $this->successResponse($data, 'Get upcoming rides successfully');
    }

    public function PastRides(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $rides = Ride::where('added_by', $user_id)
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<=', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<=', now()->toTimeString());
                        });
                });
            })->with(['rideDetail' => function($q){
                $q->where('default_ride','1');
            }])
            ->select('id','pickup','dropoff')
            ->orderBy('id', 'desc')
            ->paginate($request->paginate_limit);

        $data = ['rides' => $rides];
        return $this->successResponse($data, 'Get completed rides successfully');
    }

    public function CancelledRides(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $rides = Ride::where('added_by', $user_id)
            ->where('status', 2)
            ->with(['rideDetail' => function($q){
                $q->where('default_ride','1');
            }])
            ->select('id','pickup','dropoff')
            ->orderBy('id', 'desc')
            ->paginate($request->paginate_limit);

        $data = ['rides' => $rides];
        return $this->successResponse($data, 'Get cancelled rides successfully');
    }
}
