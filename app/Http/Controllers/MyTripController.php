<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Booking;
use App\Models\Language;
use App\Models\Notification;
use App\Models\ReviewSetting;
use App\Http\Controllers\Controller;
use App\Models\ChatsPageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Models\TripsPageSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\MyReviewSettingDetail;
use App\Models\SiteSetting;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\Ride;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Support\Facades\View;

class MyTripController extends Controller
{
    public function MyTrips($lang = null)
    {

        $user_id = auth()->user()->id;

        // Get tab filter from query parameter (default to 'upcoming')
        $tab = request()->query('tab', 'upcoming');

        $nowDate = now()->toDateString();
        $nowTime = now()->toTimeString();

        // Continue with passenger trips (users can be both drivers and passengers)
        $bookingsQuery = Booking::where('bookings.user_id', $user_id)
            ->join('rides', 'bookings.ride_id', '=', 'rides.id')
            ->select('bookings.*', 'rides.date', 'rides.time', 'rides.completed_date', 'rides.completed_time');

        switch ($tab) {
            case 'completed':
                $bookingsQuery
                    ->where('bookings.status', '!=', Booking::STATUS_DECLINED)
                    ->where('bookings.status', '!=', Booking::STATUS_CANCELLED)
                    // Include rides already marked as completed, or rides whose trip end time has passed.
                    ->where(function ($query) use ($nowDate, $nowTime) {
                        $query->where('bookings.status', Booking::STATUS_COMPLETED)
                            ->orWhere(function ($query) use ($nowDate, $nowTime) {
                                $query->whereDate('rides.completed_date', '<', $nowDate)
                                    ->orWhere(function ($query) use ($nowDate, $nowTime) {
                                        $query->whereDate('rides.completed_date', '=', $nowDate)
                                            ->whereTime('rides.completed_time', '<', $nowTime);
                                    });
                            });
                    })
                    ->orderBy('rides.date', 'desc')
                    ->orderBy('rides.time', 'desc');
                break;

            case 'cancelled':
                $bookingsQuery
                    ->where('bookings.status', Booking::STATUS_CANCELLED)
                    ->orderBy('rides.date', 'desc')
                    ->orderBy('rides.time', 'desc');
                break;

            case 'upcoming':
            default:
                $bookingsQuery
                    ->where('bookings.status', '!=', Booking::STATUS_DECLINED)
                    ->where('bookings.status', '!=', Booking::STATUS_CANCELLED)
                    ->where(function ($query) use ($nowDate, $nowTime) {
                        $query->whereDate('rides.completed_date', '>', $nowDate)
                            ->orWhere(function ($query) use ($nowDate, $nowTime) {
                                $query->whereDate('rides.completed_date', '=', $nowDate)
                                    ->whereTime('rides.completed_time', '>=', $nowTime);
                            });
                    })
                    ->orderBy('rides.date', 'asc')
                    ->orderBy('rides.time', 'asc');
                break;
        }

        $bookings = $bookingsQuery->paginate(6);

        foreach ($bookings as $booking) {
            $from_stop_id = $booking->from_stop_id;
            $to_stop_id = $booking->to_stop_id;
            $booking->ride = $this->makeDetailOfRide($booking->ride, $from_stop_id, $to_stop_id);
        }

        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $firm_cancellation_discount = SiteSetting::value('frim_discount');

        View::share([
            'rideDetailPage' => $rideDetailPage,
            'firm_cancellation_discount' => $firm_cancellation_discount,
            'tripsPage' => $tripsPage
        ]);

        $ratings = Rating::all();


        return view('my_trips', [
            'reviewSetting' => $reviewSetting,
            'messages' => $this->successMessage,
            'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,
            'bookings' => $bookings,
            'activeTab' => $tab,
            'ratings' => $ratings,
            
        ]);
    }



}
