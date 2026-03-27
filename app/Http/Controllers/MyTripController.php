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
            'tripsPage' => $tripsPage
        ]);
    }

    public function PastTrips($lang = null)
    {
        $bookings = Booking::where('user_id', auth()->user()->id)
            ->where('bookings.status', '!=', '4')
            ->where('bookings.status', '!=', '3')
            ->join('rides', 'bookings.ride_id', '=', 'rides.id')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<', now()->toTimeString());
                        });
                });
            })->select('bookings.*', 'rides.id', 'rides.date', 'rides.time', 'rides.completed_date', 'rides.completed_time')
            ->orderBy(Ride::select('date')
                ->whereColumn('rides.id', 'bookings.ride_id')
                ->limit(1), 'asc')
            ->orderBy(Ride::select('time')
                ->whereColumn('rides.id', 'bookings.ride_id')
                ->limit(1), 'asc')
            ->orderBy('ride_id', 'desc')
            ->paginate(6);

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                $postRidePage = $this->getPostRidePageWithSettingDetail();
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                $postRidePage = $this->getPostRidePageWithSettingDetail();
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }
        $ratings = Rating::all();
        $setting = ReviewSetting::getCached();

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0');
            $notifications = $notifications->where(function ($query) use ($user_id) {
                $query->where('type', '1')->whereHas('ride', function ($query) use ($user_id) {
                    $query->where('added_by', $user_id);
                })
                    ->orWhere(function ($query) use ($user_id) {
                        $query->where('type', '2')->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                    })
                    ->orWhere(function ($query) use ($user_id) {
                        $query->where('type', null)->whereHas('receiver', function ($query) use ($user_id) {
                            $query->where('id', $user_id);
                        });
                    });
            })
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('past_trips', [
            'reviewSetting' => $reviewSetting,
            'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,
            'bookings' => $bookings,
            'rideDetailPage' => $rideDetailPage,
            'tripsPage' => $tripsPage,
            'ratings' => $ratings,
            'setting' => $setting,
            'postRidePage' => $postRidePage
        ]);
    }

    public function CancelledTrips($lang = null)
    {
        $bookings = Booking::join('rides', 'bookings.ride_id', '=', 'rides.id')
            ->where('bookings.user_id', auth()->user()->id)
            ->where('bookings.status', 4)
            ->select('bookings.*', 'rides.seats as ride_seats', 'bookings.seats as booking_seats')
            ->orderBy('rides.date', 'asc')
            ->orderBy('rides.time', 'asc')
            ->paginate(6);

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $rideDetailPage = null;
        $tripsPage = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                $postRidePage = $this->getPostRidePageWithSettingDetail();
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                $postRidePage = $this->getPostRidePageWithSettingDetail();
                $rideDetailPage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }

        $ratings = Rating::all();
        $setting = ReviewSetting::getCached();
        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0');
            $notifications = $notifications->where(function ($query) use ($user_id) {
                $query->where('type', '1')->whereHas('ride', function ($query) use ($user_id) {
                    $query->where('added_by', $user_id);
                })
                    ->orWhere(function ($query) use ($user_id) {
                        $query->where('type', '2')->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                    })
                    ->orWhere(function ($query) use ($user_id) {
                        $query->where('type', null)->whereHas('receiver', function ($query) use ($user_id) {
                            $query->where('id', $user_id);
                        });
                    });
            })
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('cancelled_trips', [
            'reviewSetting' => $reviewSetting,
            'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,
            'bookings' => $bookings,
            'postRidePage' => $postRidePage,
            'rideDetailPage' => $rideDetailPage,
            'tripsPage' => $tripsPage,
            'ratings' => $ratings,
            'setting' => $setting
        ]);
    }
}
