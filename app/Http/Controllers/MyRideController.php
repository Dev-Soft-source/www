<?php

namespace App\Http\Controllers;

use App\Models\Ride;
use App\Models\User;
use App\Models\Admin;
use App\Models\Rating;
use App\Models\Booking;
use Twilio\Rest\Client;
use App\Models\Language;
use App\Models\PhoneNumber;
use App\Models\SiteSetting;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ReviewSetting;
use App\Mail\CancelPassengerMail;
use App\Models\CancelRideSetting;
use App\Mail\DriverCancelRideMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\FeaturesSettingDetail;
use App\Mail\CancelPassengerAdminMail;
use App\Mail\DriverCancelRideWithReasonMail;
use App\Models\BookingPageSettingDetail;
use App\Models\CancellationHistory;
use App\Models\ChatsPageSettingDetail;
use App\Models\CoffeeWallet;
use App\Models\FCMToken;
use App\Models\TripsPageSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\Message;
use App\Models\PostRidePageSettingDetail;
use App\Models\MyPassengerSettingDetail;
use App\Models\MyReviewSettingDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\Step1PageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Models\SeatDetail;
use Carbon\Carbon;
use App\Models\SuccessMessagesSettingDetail;
use App\Services\DriverRideCancellationService;
use App\Services\FCMService;
use App\Services\PassengerRemovalService;
use App\Services\SecuredCashEnterCodeService;
use App\Jobs\NotifyPassengerRemovedJob;
use Illuminate\Support\Facades\View;

class MyRideController extends Controller
{
    public function MyRides($lang = null)
    {
        $user = auth()->user();
        $user_id = $user->id;

        if ($user->step === '1') {
            return redirect()->route('step1to5', ['lang' => $this->selectedLanguage->abbreviation]);
        } elseif ($user->step === '2') {
            return redirect()->route('step2to5', ['lang' => $this->selectedLanguage->abbreviation]);
        } elseif ($user->step === '3') {
            return redirect()->route('step3to5', ['lang' => $this->selectedLanguage->abbreviation]);
        } elseif ($user->step === '4') {
            return redirect()->route('step4to5', ['lang' => $this->selectedLanguage->abbreviation]);
        }

        // Check if user has posted any rides (as a driver)
        $hasPostedRides = Ride::where('added_by', $user_id)->exists();

        // If user hasn't posted rides, redirect to "As a Passenger" (my_trips)
        if (!$hasPostedRides) {
            return redirect()->route('my_trips', ['lang' => $this->selectedLanguage->abbreviation]);
        }


        // Get tab filter from query parameter (default to 'upcoming')
        $tab = request()->query('tab', 'upcoming');

        // Build query based on tab
        $query = Ride::where('added_by', $user_id)->with(['detail', 'rideStops', 'rideStopSegments']);

        switch ($tab) {
            case 'upcoming':
                // include past rides even if they are not marked as completed, as long as their departure time has passed
                $query->notCancelled()
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereDate('completed_date', '>', now()->toDateString())
                                ->orWhere(function ($query) {
                                    $query->whereDate('completed_date', '=', now()->toDateString())
                                        ->whereTime('completed_time', '>=', now()->toTimeString());
                                });
                        });
                    });
                break;
            case 'completed':
                $query->notCancelled()
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereDate('completed_date', '<', now()->toDateString())
                                ->orWhere(function ($query) {
                                    $query->whereDate('completed_date', '=', now()->toDateString())
                                        ->whereTime('completed_time', '<', now()->toTimeString());
                                });
                        });
                    });
                break;
            case 'cancelled':
                $query->cancelled();
                break;
            default:
                break;
        }

        $rides = $query->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->paginate(6);

        foreach ($rides as $ride) {
            $ride = $this->getRideDetail($ride);
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

        return view('my_rides', [
            'rides' => $rides,
            'activeTab' => $tab,
            'reviewSetting' => $reviewSetting,
            'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,
            'rideDetailPage' => $rideDetailPage,
            'tripsPage' => $tripsPage
        ]);
    }

    protected function MyRideDetail(Request $request, $lang = null)
    {
        $siteSetting = SiteSetting::getCached();

        $ride = Ride::where('id', $request->id)
            ->with([
                'rideDetail' => function ($q) {
                    $q->orderBy('id');
                },
                'rideStops' => function ($q) {
                    $q->orderBy('stop_order');
                },
                'rideStopSegments',
                'vehicle',
                'bookings.passenger',
            ])
            ->first();

        if (!isset($ride) && empty($ride)) {
            $lang = $lang ?? "en";
            return redirect(route('home', ['lang' => $lang]));
        }

        $setting = ReviewSetting::getCached();
        $cancelSetting = CancelRideSetting::getCached();



        $findRidePage = FindRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback(
            $this->selectedLanguage->id,
            $this->defaultLang->id
        );
        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback(
            $this->selectedLanguage->id,
            $this->defaultLang->id
        );
        $myPassengerPage = MyPassengerSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $messages = $this->successMessage;

        $ride = $this->getRideDetail($ride);

        // $ratings = Rating::all();
        $ride_cancelled = false;
        $completed_date_time = Carbon::parse($ride->completed_date . ' ' . $ride->completed_time);
        if (($completed_date_time < Carbon::now() || $ride->status == '2' || $ride->status == '3')) {
            $ride_cancelled = true;
        }

        $firm_cancellation_discount = SiteSetting::value('frim_discount');
        View::share([
            'rideDetailPage' => $rideDetailPage,
            'firm_cancellation_discount' => $firm_cancellation_discount,
        ]);

        return view('my_ride_detail', [
            'ride' => $ride,
            'siteSetting' => $siteSetting,
            'ride_cancelled' => $ride_cancelled,
            'setting' => $setting,
            // 'ratings' => $ratings,
            'myPassengerPage' => $myPassengerPage,
            'findRidePage' => $findRidePage,
            'cancelSetting' => $cancelSetting,
            'tripsPage' => $tripsPage,
            'messages' => $messages,
        ]);
    }

    protected function resolveMyRideStopIndices($orderedStops, $from, $to): array
    {
        $from = trim((string) $from);
        $to = trim((string) $to);
        $fromIndex = null;
        $toIndex = null;

        foreach ($orderedStops as $idx => $stop) {
            if ($fromIndex === null && $this->myRideStopLabelMatches((string) ($stop->label ?? ''), $from)) {
                $fromIndex = $idx;
            }

            if ($fromIndex !== null && $idx > $fromIndex && $this->myRideStopLabelMatches((string) ($stop->label ?? ''), $to)) {
                $toIndex = $idx;
                break;
            }
        }

        return [$fromIndex, $toIndex];
    }

    protected function myRideStopLabelMatches(string $stopLabel, ?string $searchLabel): bool
    {
        $searchLabel = trim((string) $searchLabel);
        if ($searchLabel === '') {
            return false;
        }

        return strcasecmp(trim($stopLabel), $searchLabel) === 0
            || stripos($stopLabel, $searchLabel) !== false
            || stripos($searchLabel, $stopLabel) !== false;
    }

    protected function resolveMyRideDetailForStops(Ride $ride, ?string $fromLabel, ?string $toLabel)
    {
        $rideDetails = $ride->rideDetail->sortBy('id')->values();

        $matched = $rideDetails->first(function ($detail) use ($fromLabel, $toLabel) {
            return $this->myRideStopLabelMatches((string) ($detail->departure ?? ''), (string) $fromLabel)
                && $this->myRideStopLabelMatches((string) ($detail->destination ?? ''), (string) $toLabel);
        });

        if ($matched) {
            return $matched;
        }

        return $rideDetails->firstWhere('default_ride', 1) ?: $rideDetails->first();
    }

    /**
     * Secured-cash release: persistence in {@see SecuredCashEnterCodeService};
     * notifications via queued {@see NotifySecuredCashCodeSuccessJob}.
     */
    public function enterCode(Request $request)
    {
        $booking = Booking::where('id', $request->booking_id)->first();

        $siteSetting = SiteSetting::getCached();

        if ($booking) {
            $request->validate([
                'code' => 'required|max:4',
            ], [
                'code.required' => 'The code is required',
                'code.max' => 'The code must be less than 4 characters',
            ]);

            if ($request->code === $booking->secured_cash_code) {
                $service = app(SecuredCashEnterCodeService::class);
                $service->applySuccessfulCode($booking, false);

                return redirect()->route('my_ride_detail', ['lang' => app()->getLocale(), 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id])->with(['success' => $this->successMessage->secured_cash_success_message ?? "Code submitted and the booking price has been released back to the passenger. Now, get your payment in cash from them"]);
            }

            $messageData = '';
            if ($booking->secured_cash_attempt_count < $siteSetting->secured_cash_attempt) {
                $count = isset($booking->secured_cash_attempt_count) ? $booking->secured_cash_attempt_count : 0;
                $count = $count + 1;
                $booking->secured_cash_attempt_count = $count;
                $booking->save();
                $messageData = $this->successMessage->incorrect_code_message;
            } else {
                $messageData = $this->successMessage->too_many_secured_cash_attempt_message;
            }

            return redirect()->route('my_ride_detail', ['lang' => app()->getLocale(), 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id])->with(['message' => $messageData, 'secured_cash_attempt_count' => $booking->secured_cash_attempt_count]);
        }

        return $this->successMessage->general_error_message ?? 'Booking not found';
    }

    public function MyPassengers(Request $request, $lang = null, $ride_id)
    {

        $ride = Ride::where('id', $ride_id)->with(['rideDetail', 'bookings'])->first();

        /**
         * todo
         * booking has ride_id, from_stop_id and to_stop_id, 
         * so we need to filter the bookings based on the from and to stop id and the ride details to get the correct passengers for the ride detail that is being viewed
         */

        $setting = ReviewSetting::getCached();
        $cancelSetting = CancelRideSetting::getCached();

        $myPassengerPage = MyPassengerSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $genderLabel = Step1PageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $messages = $this->successMessage;

        $ratings = Rating::all();

        return view('my_passengers', [
            'ride' => $ride,
            'setting' => $setting,
            'cancelSetting' => $cancelSetting,
            'ratings' => $ratings,
            'myPassengerPage' => $myPassengerPage,
            'messages' => $messages
        ]);
    }

    public function cancel($lang = null, $id)
    {
        $ride = Ride::where('id', $id)->first();
        $setting = SiteSetting::getCached();

        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('cancel_ride', [
            'ride' => $ride,
            'setting' => $setting,
            'rideDetailPage' => $rideDetailPage,
            'tripsPage' => $tripsPage
        ]);
    }

    /**
     * Driver cancels ride: limits + persistence in {@see DriverRideCancellationService};
     * passenger FCM/email/SMS via queued {@see NotifyDriverCancelledRidePassengersJob}.
     */
    public function updateCancelRide($id, Request $request)
    {
        $request->validate([
            'message' => 'required',
            'reason' => 'required',
        ]);

        $ride = Ride::where('id', $id)->first();
        if (!$ride) {
            return redirect()->back()->with(['failure' => __('Ride not found')]);
        }

        $user_id = auth()->user()->id;
        $setting = SiteSetting::getCached();
        $monthsAgo = Carbon::now()->subMonths($setting->booking_cancel_duration)->setTimezone('UTC');

        $cancellationCount = CancellationHistory::where('user_id', $user_id)
            ->where('created_at', '>=', $monthsAgo)
            ->where('type', 'driver')
            ->count();

        $messages = $this->successMessage;
        $limitExceed = BookingPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        if ($cancellationCount >= $setting->ride_cancel_limit) {
            return redirect()->back()->with(['failure' => $limitExceed->ride_cancellation_limit_exceed ?? 'Ride cancellation limit exceeded']);
        }

        $cancellation = app(DriverRideCancellationService::class);
        $bookings = $cancellation->bookingsForWebCancel((int) $id);

        if ($bookings->isEmpty()) {
            $cancellation->markRideCancelledEmpty($ride, true);
        } else {
            $bookingIds = $cancellation->cancelByDriverWeb($ride, $bookings);
            $this->dispatchDriverRideCancelledPassengerWebFlow(
                $ride->fresh(),
                auth()->user(),
                $bookingIds,
                (string) $request->message,
                'web'
            );
        }

        return redirect()->route('home', ['lang' => app()->getLocale()])
            ->with(['success' => $messages->ride_cancel_message]);
    }

    public function cancelRide($id, Request $request)
    {

        // Find the ride by ID
        $ride = Ride::find($id);

        if (!$ride) {
            return response()->json(['success' => false, 'message' => 'Ride not found.'], 404);
        }

        // Check if there are any active bookings for the ride
        $bookedSeats = $ride->bookings()
            ->where('status', '<>', 3) // Exclude canceled bookings
            ->where('status', '<>', 4) // Exclude completed bookings
            ->withActivePassenger()
            ->sum('seats');


        // If there are no active bookings, cancel the ride
        if ($bookedSeats == 0) {
            // Update the ride status to 'cancelled'
            $ride->update(['status' => 'cancelled']);

            // Add cancellation history
            CancellationHistory::create([
                'ride_id' => $ride->id,
                'user_id' => $ride->added_by,
                'type' => 'driver',
            ]);

            // Revoke Extra Care eligibility when driver cancels ride
            User::where('id', $ride->added_by)->whereIn('folks_ride', ['1', ''])->update(['folks_ride' => '0']);

            // Notify passengers (if any)
            $bookings = Booking::where('ride_id', $id)->get();
            foreach ($bookings as $booking) {
                // Create detailed in-app notification message
                $departureDate = Carbon::parse($ride->date)->format('F d, Y');
                $departureTime = $ride->time;

                $notificationMessage = "Good morning " . $booking->passenger->first_name . ",\n" .
                    "We are sorry to inform you that the driver has cancelled their ride. They will send you a separate message explaining their reasons.\n\n" .
                    "While we pride ProximaRide on being a RELIABLE platform, we never know what life can throw at us. We assure you that we conduct a thorough investigation on each cancellation and, in case of frequent cancellations, we block the driver's account.\n\n" .
                    "Seats booked: " . numberToWords($booking->seats) . "\n" .
                    "Total booking price: $" . number_format($booking->fare, 2) . "\n" .
                    "Ride from " . $booking->departure . " to " . $booking->destination .
                    " on " . $departureDate . " at " . $departureTime . "\n\n" .
                    "All amounts that you have paid for this booking will be refunded to you immediately.\n\n" .
                    "Do not be discouraged; go ahead and search for other trips on ProximaRide.\n\n" .
                    "Please accept our sincere apologies for the inconvenience and have a safe ride,\n" .
                    "ProximaRide Team";

                // Create notification
                $notification = Notification::create([
                    'type' => 2, // Passenger notification
                    'ride_id' => $ride->id,
                    'posted_to' => $booking->id,
                    'posted_by' => $ride->added_by,
                    'message' => $notificationMessage,
                    'status' => 'cancelled',
                    'notification_type' => 'ride_cancellation',
                    'departure' => $booking->departure,
                    'destination' => $booking->destination,
                    'ride_detail_id' => $booking->ride_detail_id,
                ]);

                // Send FCM notification
                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $booking->user_id)->get();
                $notificationTitle = 'Your ride has been cancelled';
                $notificationBody = 'The driver has cancelled their ride';

                $fcmToken = $booking->passenger->mobile_fcm_token;
                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $notificationBody);
                }

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification(
                            $fcm_token->token,
                            $notificationBody,
                            $notificationTitle,
                            [
                                'notification_type' => 'ride_cancelled',
                                'ride_id' => $ride->id,
                                'notification_id' => $notification->id,
                                'open_message' => true
                            ]
                        );
                    } catch (\Exception $e) {
                        \Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                    }
                }

                // Send email notification to passengers
                if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {
                    $data = [
                        'driver_name' => $ride->driver->first_name,
                        'passenger_name' => $booking->passenger->first_name,
                        'from' => $booking->departure,
                        'to' => $booking->destination,
                        'date' => $departureDate,
                        'time' => $departureTime,
                        'seats' => $booking->seats,
                        'total_price' => $booking->fare,
                    ];
                    Mail::to($booking->passenger->email)->queue(new DriverCancelRideMail($data));
                }

                // Send SMS notification to passengers (if enabled)
                $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                    ->where('verified', '1')
                    ->where('default', '1')
                    ->first();

                if ($phoneNumber && env('APP_ENV') != 'local') {
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');

                    $twilio = new Client($sid, $token);
                    $to = $phoneNumber->phone;

                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning " . $booking->passenger->first_name;
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $booking->passenger->first_name;
                    } else {
                        $title = "Good evening " . $booking->passenger->first_name;
                    }

                    // $departureDateTime = date('d F, Y H:i:s', strtotime($ride->date . ' ' . $ride->time));
                    $departureDate = date('F d, Y', strtotime($ride->date));
                    $departureTime = date('H:i', strtotime($ride->time));

                    // $smsMessage = "{$title}\nWe regret to inform you that your ride from {$booking->departure} to {$booking->destination} on {$departureDateTime} has been cancelled by the driver.\n\nAll payments will be refunded immediately.\n\nWe apologize for the inconvenience and encourage you to search for alternative rides.\n\nProximaRide Team";
                    $smsMessage = "From ProximaRide: we are sorry to inform you that your ride from {$booking->departure} to {$booking->destination} on {$departureDate} at {$departureTime} has been cancelled by the driver.\n\nAll amounts that you have made for this booking will be refunded to you immediately.";

                    try {
                        $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $smsMessage,
                            ]
                        );
                    } catch (\Exception $e) {
                        \Log::error("Failed to send SMS to {$to}: " . $e->getMessage());
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Ride canceled successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Cannot cancel ride with booked seats.']);
        }
    }
    public function cancelPassenger($lang = null, $id)
    {
        $booking = Booking::where('id', $id)->first();
        $ride = Ride::where('id', $booking->ride_id)->first();
        $setting = SiteSetting::getCached();


        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $messages = $this->successMessage;

        return view('cancel_passenger', [
            'booking' => $booking,
            'messages' => $messages,
            'ride' => $ride,

            'postRidePage' => $postRidePage,
            'setting' => $setting,
            'tripsPage' => $tripsPage
        ]);
    }

    public function updateRemovePassenger($id, Request $request)
    {

        $booking = Booking::where('id', $id)->first();
        $ride = Ride::where('id', $booking->ride_id)->first();
        $user_id = auth()->user()->id;
        $setting = SiteSetting::getCached();
        $monthsAgo = Carbon::now()->subMonths($setting->booking_cancel_duration)->setTimezone('UTC');;

        $cancellationCount = CancellationHistory::where('user_id', $user_id)
            ->where('created_at', '>=', $monthsAgo)
            ->where('type', 'driver')
            ->count();

        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $limitExceed = BookingPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);


        if ($cancellationCount >= $setting->booking_cancel_limit) {
            return redirect()->back()->with(['failure' => $limitExceed->booking_cancellation_limit_exceed ?? "Booking cancellation limit exceeded"]);
        }
        $removed_permanently = $request->filled('removed_permanently') ? (int) $request->removed_permanently : 0;
        $remove_type = $request->filled('remove_type') ? $request->remove_type : null;

        $request->validate([
            'admin_message' => 'required',
            'passenger_message' => 'required',
            'remove_type' => $removed_permanently === 1 ? 'required' : 'nullable',
            'block_day' => $remove_type === "temporarily" ? 'required' : 'nullable',
        ]);

        $blockDay = null;
        if ($removed_permanently === 1 && $remove_type === 'temporarily') {
            $blockDay = (int) $request->block_day;
        }

        $service = app(PassengerRemovalService::class);
        $result = $service->remove($ride, $booking, $removed_permanently, $remove_type, $blockDay);

        if (!$result['ok']) {
            return redirect()->back()->with(['failure' => $result['error']]);
        }

        $booking = $result['booking'];
        $ride = $result['ride'];

        NotifyPassengerRemovedJob::dispatch(
            $booking->id,
            $ride->added_by,
            (string) $request->admin_message,
            (string) $request->passenger_message
        );


        return redirect()->route('my_ride_detail', [
            'departure' => $booking->departure,
            'destination' => $booking->destination,
            'id' => $ride->id
        ])->with(['success' => "The passenger has been removed from your ride"]);
    }
}
