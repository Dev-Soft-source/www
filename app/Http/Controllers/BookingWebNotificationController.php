<?php

namespace App\Http\Controllers;

use App\Jobs\CompleteBookingNotificationsJob;
use App\Jobs\NotifyBookingRequestApprovedJob;
use App\Jobs\NotifyBookingRequestRejectedJob;
use App\Jobs\NotifyDriverCancelledRidePassengersJob;
use App\Jobs\NotifyDriverPassengerCancelledJob;
use App\Mail\AcceptBookingRequestMail;
use App\Mail\DriverCancelRideMail;
use App\Mail\DriverCancelRideWithReasonMail;
use App\Mail\DriverDetailsMail;
use App\Mail\PassengerCancelBookingMail;
use App\Mail\PassengerDetailsMail;
use App\Mail\PassengerListMail;
use App\Mail\PaymentInvoiceMail;
use App\Mail\RejectBookingRequestMail;
use App\Mail\RideApprovalEmail;
use App\Mail\SecuredCashPaymentCodeMail;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\Ride;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

/**
 * Queued / heavy booking notification side effects (FCM, SMS, Mail::queue, DB notifications).
 *
 * Dispatch helpers are called from {@see Controller}; synchronous implementations run inside:
 * {@see CompleteBookingNotificationsJob}, {@see NotifyBookingRequestApprovedJob},
 * {@see NotifyBookingRequestRejectedJob}, {@see NotifyDriverCancelledRidePassengersJob},
 * {@see NotifyDriverPassengerCancelledJob}.
 */
class BookingWebNotificationController extends Controller
{
    /**
     * Queue driver-facing notifications after a passenger cancels seats on a booking.
     * Runs after the database transaction commits ({@see NotifyDriverPassengerCancelledJob}).
     *
     * @param  int  $bookingId  Booking row affected by the cancellation.
     * @param  int  $actorUserId  User who performed the cancellation (message sender).
     * @param  string  $cancellationMessage  In-app message body to the driver.
     * @param  int  $originalSeats  Passenger seat count before this cancellation.
     * @param  int  $cancelSeats  Seats cancelled in this operation.
     * @param  float  $payoutAmt  Amount shown to the driver in the SMS summary.
     */
    public function dispatchDriverPassengerCancelledNotifications(
        int $bookingId,
        int $actorUserId,
        string $cancellationMessage,
        int $originalSeats,
        int $cancelSeats,
        float $payoutAmt
    ): void {
        NotifyDriverPassengerCancelledJob::dispatch(
            $bookingId,
            $actorUserId,
            $cancellationMessage,
            $originalSeats,
            $cancelSeats,
            $payoutAmt
        )->afterCommit();
    }

    /**
     * Worker entrypoint: email (if enabled), in-app notification, FCM, driver message thread,
     * optional same-day passenger-list refresh, and cancellation summary SMS to the driver.
     *
     * @param  Booking  $booking  Booking row (with passenger) after cancellation persistence.
     * @param  Ride  $ride  Ride the booking belongs to (with driver).
     * @param  User  $actor  Cancelling user (stored as message sender to the driver).
     * @param  string  $cancellationMessage  In-app message body for the driver thread.
     * @param  int  $originalSeats  Seat count before this cancellation (for copy in email/SMS).
     * @param  int  $cancelSeats  Seats removed in this cancellation.
     * @param  float  $payoutAmt  Driver payout amount for SMS wording.
     */
    public function notifyDriverPassengerCancelledWebFlowSync(
        Booking $booking,
        Ride $ride,
        User $actor,
        string $cancellationMessage,
        int $originalSeats,
        int $cancelSeats,
        float $payoutAmt
    ): void {
        $booking->loadMissing(['passenger', 'ride.driver']);

        if (!$booking->passenger || !$ride->driver) {
            return;
        }

        if (isset($ride->driver->email_notification) && (int) $ride->driver->email_notification === 1) {
            $data = [
                'driver_name' => $ride->driver->first_name,
                'passenger_name' => $booking->passenger->first_name,
                'seats' => $originalSeats,
                'cancelled_searts' => $cancelSeats,
                'price' => number_format((float) ($booking->price / 100), 2, '.', ''),
                'from' => $booking->departure,
                'to' => $booking->destination,
                'date' => Carbon::parse($ride->date)->format('F d, Y'),
                'time' => $ride->time,
            ];
            Mail::to($ride->driver->email)->queue(new PassengerCancelBookingMail($data));
        }

        $notification = Notification::create([
            'ride_id' => $booking->ride_id,
            'posted_by' => $booking->user_id,
            'message' => getNotificationMessageText(
                'booking_cancelled',
                $ride->driver,
                [],
                'Booking cancelled'
            ),
            'status' => 'cancelled',
            'notification_type' => 'upcoming',
            'from_stop_id' => $booking->from_stop_id,
            'to_stop_id' => $booking->to_stop_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination,
        ]);

        $this->sendFCM($notification->message, $ride->driver);

        Message::create([
            'ride_id' => $ride->id,
            'receiver' => $ride->added_by,
            'sender' => $actor->id,
            'message' => $cancellationMessage,
        ]);

        $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
        $this->notifyDriverPassengerListUpdatedIfWithinHourWebFlow($booking, $ride, $rideDateTime);

        $driverPhoneNumber = $this->resolveSmsPhoneNumberObject($ride->driver);
        if ($driverPhoneNumber) {
            $sms_message = 'From ProximaRide: Ride from ' .
                $booking->departure . ' to ' . $booking->destination . ' on ' . Carbon::parse($ride->date)->format('F d, Y') . ' at ' . $ride->time .
                "\nYour passenger " . $booking->passenger->first_name . " has cancelled as follows:\nBooked: " . $originalSeats .
                ' seats\nCancelled ' . $cancelSeats . "\nRemaining: " . ($originalSeats - $cancelSeats) .
                "\nAmount due to you: $" . $payoutAmt .
                "* (* See our cancellation policy)\nWe have opened the cancelled seat(s) for other passengers to book";
            $this->sendSmsCode($driverPhoneNumber, $ride->driver, $sms_message);
        }
    }

    /**
     * Format aggregated passenger rows as numbered lines for SMS to the driver.
     *
     * @param  array<int, array{first_name?: string, seats?: int, phone_number?: string}>  $passengers
     * @return string Multiline text; each line is one passenger with phone and seat count.
     */
    protected function buildDriverPassengerListSms(array $passengers): string
    {
        $passengerList = '';
        $counter = 1;
        foreach ($passengers as $passenger) {
            $seatText = ((int) ($passenger['seats'] ?? 0)) === 1 ? 'seat' : 'seats';
            $passengerList .= $counter . '- ' . ($passenger['first_name'] ?? '') .
                '. Phone ' . ($passenger['phone_number'] ?? '') .
                '. Booked: ' . ((int) ($passenger['seats'] ?? 0)) . ' ' . $seatText . "\n";
            $counter++;
        }

        return $passengerList;
    }

    /**
     * If the ride departs within the next hour, notify the driver that the passenger list changed
     * (queued list email, in-app notification, FCM, SMS with formatted lines).
     *
     * @param  Booking  $booking  Context booking (segment stops and labels for copy).
     * @param  Ride  $ride  Ride with driver loaded for delivery channels.
     * @param  Carbon  $rideDateTime  Parsed departure date+time for the ride.
     */
    protected function notifyDriverPassengerListUpdatedIfWithinHourWebFlow(Booking $booking, Ride $ride, Carbon $rideDateTime): void
    {
        $oneHourBefore = $rideDateTime->copy()->subHour();
        if (!Carbon::now()->between($oneHourBefore, $rideDateTime)) {
            return;
        }

        $getBookings = Booking::select('user_id', DB::raw('SUM(seats) as total_seats'))
            ->where('ride_id', $booking->ride_id)
            ->bookedOrCompleted()
            ->groupBy('user_id')
            ->with('passenger')
            ->get();

        if ($getBookings->isEmpty()) {
            return;
        }

        $passengers = [];
        foreach ($getBookings as $bookingItem) {
            $passengers[] = [
                'first_name' => $bookingItem->passenger->first_name,
                'seats' => $bookingItem->total_seats,
                'phone_number' => $bookingItem->passenger->primaryPhone()?->phone ?? $bookingItem->passenger->phone,
            ];
        }

        $data = [
            'driver_name' => $ride->driver->first_name,
            'from' => $booking->departure,
            'to' => $booking->destination,
            'date' => $ride->date,
            'time' => $ride->time,
            'passengers' => $passengers,
        ];

        if (isset($ride->driver->email_notification) && (int) $ride->driver->email_notification === 1) {
            Mail::to($ride->driver->email)->queue(new PassengerListMail($data));
        }

        $notification = Notification::create([
            'ride_id' => $booking->ride_id,
            'posted_by' => $booking->ride->added_by,
            'message' => getNotificationMessageText(
                'passenger_list_updated',
                $ride->driver,
                [],
                'Your passenger list has been updated'
            ),
            'status' => 'upcoming',
            'notification_type' => 'upcoming',
            'from_stop_id' => $booking->from_stop_id,
            'to_stop_id' => $booking->to_stop_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination,
        ]);

        $this->sendFCM($notification->message, $ride->driver);

        $driverPhoneNumber = $this->resolveSmsPhoneNumberObject($ride->driver);
        if ($driverPhoneNumber) {
            $passengerList = $this->buildDriverPassengerListSms($passengers);
            $sms_message = 'From ProximaRide: Your passenger list has been updated for your ride from ' .
                $booking->departure . ' to ' . $booking->destination .
                ' on ' . Carbon::parse($ride->date)->format('F d, Y') . ' at ' . $ride->time . "\n" .
                $passengerList . 'Drive safe!';
            $this->sendSmsCode($driverPhoneNumber, $ride->driver, $sms_message);
        }
    }

    /**
     * Queue post-payment booking notifications (new booking / request to driver and passenger, emails, SMS).
     * Payload matches keys built in {@see Controller::completeBookingUnifiedFlow()}.
     *
     * @param  int  $bookingId  Booking id after seats and transactions are persisted.
     * @param  array<string, mixed>  $payload  transaction_random_id, seats_amount, payment_amount, invoice_form, etc.
     */
    public function dispatchCompleteBookingNotifications(int $bookingId, array $payload): void
    {
        CompleteBookingNotificationsJob::dispatch($bookingId, $payload)->afterCommit();
    }

    /**
     * Worker entrypoint for {@see CompleteBookingNotificationsJob}: secured-cash branch, dual notifications,
     * driver message, optional mails (passenger details, driver details, invoice), and SMS including
     * same-day passenger list when departure is within one hour.
     *
     * @param  Booking  $booking  Persisted booking with passenger and ride relations.
     * @param  array<string, mixed>  $payload  Serialized request/transaction context from the HTTP flow.
     */
    public function completeBookingUnifiedFlowNotificationsSync(Booking $booking, array $payload): void
    {
        $booking->loadMissing(['passenger.primaryPhone', 'ride.driver.primaryPhone']);

        $ride = $booking->ride;
        $user = $booking->passenger;

        if (!$ride || !$user || !$ride->driver) {
            return;
        }

        $transcationId = (string) ($payload['transaction_random_id'] ?? '');
        $seats_amount = (float) ($payload['seats_amount'] ?? 0);
        $payment_amount = (float) ($payload['payment_amount'] ?? 0);
        $bookedByWallet = !empty($payload['booked_by_wallet']);
        $payment_method = (string) ($payload['payment_method'] ?? 'paypal');
        $invoiceForm = is_array($payload['invoice_form'] ?? null) ? $payload['invoice_form'] : [];
        $driver_message = (string) ($payload['driver_message'] ?? '');
        $selectedLangAbbr = (string) ($payload['selected_language_abbr'] ?? 'en');

        $secured_cash_code = $booking->secured_cash_code;
        $rideId = $ride->id;
        $from_stop_id = (int) $booking->from_stop_id;
        $to_stop_id = (int) $booking->to_stop_id;
        $seats_number = (int) $booking->seats;

        $passengerPhoneNumber = $user->primaryPhone()?->phone ?? $user->phone;
        $driverPhoneNumber = $ride->driver->primaryPhone()?->phone ?? $ride->driver->phone;

        $departureTime = date('H:i', strtotime((string) $ride->time));
        $departureDate = date('d F, Y', strtotime((string) $ride->date));

        if ($secured_cash_code && isset($user->email_notification) && (int) $user->email_notification === 1) {
            $emailData = [
                'first_name' => $user->first_name,
                'secured_cash_code' => $secured_cash_code,
                'driver_first_name' => $ride->driver->first_name,
                'driver_last_name' => $ride->driver->last_name,
                'driver_phone' => $driverPhoneNumber,
                'driver_email' => $ride->driver->email,
                'departure' => $ride->departure,
                'destination' => $ride->destination,
                'date' => Carbon::parse($ride->date)->format('F d, Y'),
                'time' => $ride->time,
                'seats' => $seats_number,
                'booking_price' => $seats_amount,
            ];
            Mail::to($user->email)->queue(new SecuredCashPaymentCodeMail($emailData));

            $notificationMessage = 'Your Secured-cash payment code is: ' . $secured_cash_code;
            Notification::create([
                'type' => Notification::TYPE_RIDE_DETAIL,
                'ride_id' => $rideId,
                'from_stop_id' => $from_stop_id,
                'to_stop_id' => $to_stop_id,
                'posted_to' => $booking->id ?? null,
                'posted_by' => $ride->driver->id,
                'receiver_id' => $user->id,
                'message' => $notificationMessage,
                'status' => 'completed',
                'notification_type' => 'secured_cash',
                'departure' => $ride->departure,
                'destination' => $ride->destination,
            ]);
            $this->sendFCM($notificationMessage, $user);
        }

        if ($ride->isInstantBooking()) {
            $notificationStatus = 'completed';
            $notificationSlug = 'instant_booking_new';
            $sms_booking_str = 'instant booking';
        } else {
            $notificationStatus = 'request';
            $notificationSlug = 'booking_request_new';
            $sms_booking_str = 'booking request';
        }

        $notification = Notification::create([
            'ride_id' => $rideId,
            'posted_by' => $user->id,
            'message' => getNotificationMessageText(
                $notificationSlug,
                $ride->driver,
                [
                    'first_name' => $user->first_name,
                    'seats' => numberToWords($seats_number),
                ],
                "You have a new instant booking from {first_name}\nSeats booked: {seats}"
            ),
            'status' => $notificationStatus,
            'notification_type' => 'upcoming',
            'from_stop_id' => $from_stop_id,
            'to_stop_id' => $to_stop_id,
            'departure' => $ride->departure,
            'destination' => $ride->destination,
        ]);
        $this->sendFCM($notification->message, $ride->driver);

        $notification = Notification::create([
            'type' => Notification::TYPE_RIDE_DETAIL,
            'ride_id' => $rideId,
            'posted_to' => $booking->id,
            'posted_by' => $ride->added_by,
            'message' => getNotificationMessageText(
                'booking_details_with_seats',
                $user,
                ['seats' => numberToWords($seats_number)],
                "Your booking details\nSeats booked: {seats}"
            ),
            'status' => $notificationStatus,
            'notification_type' => 'upcoming',
            'from_stop_id' => $from_stop_id,
            'to_stop_id' => $to_stop_id,
            'departure' => $ride->departure,
            'destination' => $ride->destination,
        ]);
        $this->sendFCM($notification->message, $user);

        Message::create([
            'ride_id' => $rideId,
            'receiver' => $ride->added_by,
            'sender' => $user->id,
            'message' => $driver_message,
        ]);

        $bookingPrice = $booking->price * $booking->seats;

        if (isset($ride->driver->email_notification) && (int) $ride->driver->email_notification === 1) {
            $data = [
                'first_name' => $ride->driver->first_name,
                'lang' => $selectedLangAbbr,
                'origin' => $booking->departure,
                'destination' => $booking->destination,
                'date' => $ride->date,
                'time' => $ride->time,
                'seats' => $booking->seats,
                'booking_price' => number_format((float) ($booking->price / 100), 2, '.', ''),
                'total_price' => number_format((float) ($bookingPrice / 100), 2, '.', ''),
                'passenger_first_name' => $user->first_name,
                'passenger_last_name' => $user->last_name,
                'gender' => $user->gender,
                'email' => $user->email,
                'phone' => $passengerPhoneNumber,
            ];
            Mail::to($ride->driver->email)->queue(new PassengerDetailsMail($data));
        }

        if (isset($user->email_notification) && (int) $user->email_notification === 1) {
            $data = [
                'first_name' => $user->first_name,
                'driver_first_name' => $ride->driver->first_name,
                'driver_last_name' => $ride->driver->last_name,
                'gender' => $ride->driver->gender,
                'email' => $ride->driver->email,
                'phone' => $driverPhoneNumber,
                'from' => $booking->departure,
                'to' => $booking->destination,
                'date' => Carbon::parse($ride->date)->format('F d, Y'),
                'time' => $ride->time,
            ];
            Mail::to($user->email)->queue(new DriverDetailsMail($data));

            $invoiceData = [
                'first_name' => $user->first_name,
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'seats' => $booking->seats,
                'seats_amount' => number_format((float) $seats_amount, 2, '.', ''),
                'transaction_id' => $transcationId,
                'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                'transaction_type' => '',
                'card_type' => $invoiceForm['card_type'] ?? '',
                'cardholder_name' => $invoiceForm['cardholder_name'] ?? '',
                'last_four_digits' => $invoiceForm['last_four_digits'] ?? '****',
                'expiration_date' => $invoiceForm['expiration_date'] ?? '',
                'online_payment' => number_format((float) $payment_amount, 2, '.', ''),
            ];

            if ($bookedByWallet) {
                $invoiceData['transaction_type'] = 'topup_balance';
            }

            if ($payment_method == 'paypal') {
                $invoiceData['payment_method'] = 'paypal';
                $invoiceData['paypal_email'] = $invoiceForm['paypal_email'] ?? $user->email ?? 'N/A';
            } elseif ($payment_method === 'google_pay' || $payment_method === 'apple_pay') {
                $invoiceData['payment_method'] = ($invoiceForm['card_id'] ?? '') === 'google_pay' ? 'Google Pay' : 'Apple Pay';
            } else {
                $invoiceData['payment_method'] = 'credit_card';
            }

            Mail::to($user->email)->queue(new PaymentInvoiceMail($invoiceData));
        }

        if ($ride->isSecureCashPayment() && $ride->isInstantBooking()) {
            $sms_message = 'From ProximaRide: Your secured-cash payment code is: ' . $secured_cash_code . "\n" .
                "Give this code to your driver ONLY at the time of the ride when you meet with them.\n" .
                'Driver name is ' . $ride->driver->first_name . ', phone ' . $driverPhoneNumber . "\n" .
                'Ride from ' . $ride->departure . ' to ' . $ride->destination .
                ' on ' . $departureDate . ' at ' . $departureTime . "\n" .
                'Number of seats: ' . ucfirst((string) $booking->seats);

            $this->sendSmsCode($this->resolveSmsPhoneNumberObject($user), $user, $sms_message);
        }

        $sms_message = 'From ProximaRide: You have a new ' . $sms_booking_str . ' from (' . $user->first_name . '). Phone ' . $passengerPhoneNumber .
            "\nRide from " . $booking->departure .
            ' to ' . $booking->destination .
            ' on ' . $departureDate .
            ' at ' . $departureTime .
            "\nNumber of seats: " . ucfirst((string) $booking->seats);
        $this->sendSmsCode($this->resolveSmsPhoneNumberObject($ride->driver), $ride->driver, $sms_message);

        $currentTime = now();
        $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
        if (
            $rideDateTime->isSameDay($currentTime) &&
            $currentTime->diffInSeconds($rideDateTime, false) <= 3600 &&
            $currentTime->lessThanOrEqualTo($rideDateTime)
        ) {
            $bookings = Booking::with(['passenger.primaryPhone'])
                ->where('ride_id', $ride->id)
                ->bookedOrCompleted()
                ->get();

            if ($bookings->isNotEmpty()) {
                $passengerList = $bookings->map(function ($row) {
                    $name = $row->passenger->first_name . ' ' . $row->passenger->last_name;
                    $phone = $row->passenger->primaryPhone->phone ?? 'N/A';

                    return "{$name}, phone: {$phone}";
                })->implode("\n");

                $sms_message = 'From ProximaRide: Here is your passenger list for your ride from '
                    . $ride->departure . ' to ' . $ride->destination
                    . ' on ' . $rideDateTime->format('Y-m-d')
                    . ' at ' . $rideDateTime->format('H:i') . "\n"
                    . $passengerList . "\nDrive safe!";

                $this->sendSmsCode($this->resolveSmsPhoneNumberObject($ride->driver), $ride->driver, $sms_message);
            }
        }
    }

    /**
     * Queue the driver-approved-booking notification pipeline ({@see NotifyBookingRequestApprovedJob}).
     *
     * @param  Booking  $booking  Accepted booking row.
     * @param  User  $driver  Driver who approved (job uses ids only).
     * @param  bool  $statusAlreadyBooked  Passed through to the job when the booking row was already status booked (e.g. merged row).
     */
    public function dispatchBookingRequestApprovedNotifications(Booking $booking, User $driver, bool $statusAlreadyBooked = false): void
    {
        Log::info('user_id WebFlow: ' . $driver->id);
        dispatch(new NotifyBookingRequestApprovedJob($booking->id, $driver->id, $statusAlreadyBooked));
    }

    /**
     * Worker entrypoint when a driver accepts a booking request: SMS to driver, secured-cash handling,
     * passenger + driver notifications and FCM, approval emails, passenger SMS, and driver self-notification.
     *
     * @param  Booking  $booking  Booking being approved (passenger + ride required).
     * @param  User  $driver  Authenticated driver user (may differ from ride’s driver model for FCM on “self” notification).
     * @param  bool  $statusAlreadyBooked  Reserved for callers that skip status mutation; does not change this method’s behaviour today.
     */
    public function notifyBookingRequestApprovedWebFlowSync(Booking $booking, User $driver, bool $statusAlreadyBooked = false): void
    {
        $booking->loadMissing(['passenger.primaryPhone', 'ride.driver.primaryPhone']);
        $ride = $booking->ride;
        $passenger = $booking->passenger;
        $driverUser = $ride?->driver;
        if (!$ride || !$passenger || !$driverUser) {
            return;
        }

        $passengerPhoneStr = $passenger->primaryPhone()?->phone ?? $passenger->phone ?? '';

        $driverForSms = $this->resolveSmsPhoneNumberObject($driverUser);
        if (!$driverForSms || empty($driverForSms->phone ?? null)) {
            $driverForSms = PhoneNumber::where('user_id', $ride->added_by)
                ->where('verified', '1')
                ->first();
        }

        $driverPhoneStr = $driverUser->primaryPhone()?->phone ?? $driverUser->phone ?? '';
        if ($driverPhoneStr === '' && $driverForSms && isset($driverForSms->phone)) {
            $driverPhoneStr = (string) $driverForSms->phone;
        }

        $departureTime = date('H:i:s', strtotime((string) $ride->time));
        $departureDate = date('d F, Y', strtotime((string) $ride->date));
        $seatWords = numberToWords($booking->seats);

        if ($driverForSms && !empty($driverForSms->phone ?? null)) {
            $driverSmsBody = 'From ProximaRide: You have approved ' . $passenger->first_name . '. Phone: ' . $passengerPhoneStr
                . "\nRide from " . $booking->departure . ' to ' . $booking->destination
                . ' on ' . $departureDate . ' at ' . $departureTime
                . "\nNumber of seats: " . $seatWords;
            $this->sendSmsCode($driverForSms, $driverUser, $driverSmsBody);
        }

        if ($ride->isSecureCashPayment()) {
            $securedCash = '1';
            $securedCashCode = rand(1000, 9999);
            $booking->secured_cash = $securedCash;
            $booking->secured_cash_code = $securedCashCode;
            $booking->save();

            $securedCashNotification = Notification::create([
                'type' => 2,
                'ride_id' => $booking->ride_id,
                'posted_to' => $booking->id,
                'posted_by' => $ride->added_by,
                'receiver_id' => $booking->user_id,
                'message' => getNotificationMessageText(
                    'secured_cash_payment_code',
                    $passenger,
                    ['code' => $securedCashCode],
                    'Your Secured-cash payment code is: {code}'
                ),
                'status' => 'completed',
                'notification_type' => 'secured_cash',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination,
            ]);
            $this->sendFCM($securedCashNotification->message, $passenger);

            $securedSms = 'From ProximaRide: Your secured-cash payment code is: ' . $securedCashCode . "\n"
                . "Give this code to your driver ONLY at the time of the ride when you meet with them.\n"
                . 'Driver name is ' . $driverUser->first_name . ', phone ' . $driverPhoneStr . "\n"
                . 'Ride from ' . $booking->departure . ' to ' . $booking->destination
                . ' on ' . $departureDate . ' at ' . $departureTime . "\n"
                . 'Number of seats: ' . $seatWords;
            $this->sendSmsCode($this->resolveSmsPhoneNumberObject($passenger), $passenger, $securedSms);

            if (isset($passenger->email_notification) && (int) $passenger->email_notification === 1) {
                $emailData = [
                    'first_name' => $passenger->first_name,
                    'secured_cash_code' => $securedCashCode,
                    'driver_first_name' => $driverUser->first_name,
                    'driver_last_name' => $driverUser->last_name,
                    'driver_phone' => $driverPhoneStr,
                    'driver_email' => $driverUser->email,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination,
                    'date' => Carbon::parse($ride->date)->format('F d, Y'),
                    'time' => $ride->time,
                    'seats' => $booking->seats,
                    'booking_price' => $booking->price * $booking->seats,
                ];
                Mail::to($passenger->email)->queue(new SecuredCashPaymentCodeMail($emailData));
            }
        }

        $passengerNotification = Notification::create([
            'type' => 2,
            'ride_id' => $booking->ride_id,
            'posted_to' => $booking->id,
            'posted_by' => $ride->added_by,
            'message' => getNotificationMessageText(
                'booking_request_approved_by',
                $passenger,
                ['first_name' => $driverUser->first_name],
                'Booking request approved by {first_name}'
            ),
            'status' => 'completed',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination,
        ]);
        $this->sendFCM($passengerNotification->message, $passenger);

        $bookingPrice = $booking->price * $booking->seats;

        if (isset($driverUser->email_notification) && (int) $driverUser->email_notification === 1) {
            $driverMailPayload = [
                'driver_first_name' => $driverUser->first_name,
                'driver_last_name' => $driverUser->last_name,
                'first_name' => $passenger->first_name,
                'last_name' => $passenger->last_name,
                'email' => $passenger->email,
                'phone' => $passengerPhoneStr,
                'from' => $booking->departure,
                'to' => $booking->destination,
                'date' => Carbon::parse($ride->date)->format('F d, Y'),
                'time' => $ride->time,
                'seats' => $booking->seats,
                'total_price' => $bookingPrice,
            ];
            Mail::to($driverUser->email)->queue(new AcceptBookingRequestMail($driverMailPayload));
        }

        if (isset($passenger->email_notification) && (int) $passenger->email_notification === 1) {
            $passengerMailPayload = [
                'first_name' => $passenger->first_name,
                'last_name' => $passenger->last_name,
                'driver_first_name' => $driverUser->first_name,
                'driver_last_name' => $driverUser->last_name,
                'driver_email' => $driverUser->email,
                'driver_phone' => $driverPhoneStr,
                'from' => $booking->departure,
                'to' => $booking->destination,
                'date' => Carbon::parse($ride->date)->format('F d, Y'),
                'time' => $ride->time,
                'seats' => $booking->seats,
                'total_price' => $bookingPrice,
            ];
            Mail::to($passenger->email)->queue(new RideApprovalEmail($passengerMailPayload));
        }

        $passengerApprovalSms = 'From ProximaRide: Your booking request has been approved by ' . $driverUser->first_name
            . '. Phone ' . $driverPhoneStr
            . "\nRide from " . $booking->departure
            . ' to ' . $booking->destination
            . ' on ' . $departureDate
            . ' at ' . $departureTime
            . "\nNumber of seats: " . $seatWords;
        $this->sendSmsCode($this->resolveSmsPhoneNumberObject($passenger), $passenger, $passengerApprovalSms);

        $driverSelfNotification = Notification::create([
            'ride_id' => $booking->ride_id,
            'posted_by' => $booking->user_id,
            'message' => getNotificationMessageText(
                'booking_approved_you_have_approved',
                $driver,
                ['first_name' => $passenger->first_name, 'seats' => numberToWords($booking->seats)],
                "You have approved {first_name}\nSeats booked: {seats}"
            ),
            'status' => 'completed',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination,
        ]);
        $this->sendFCM($driverSelfNotification->message, $driver);
    }

    /**
     * Queue passenger notifications after a driver rejects a pending booking request ({@see NotifyBookingRequestRejectedJob}).
     *
     * @param  string  $channel  {@see notifyBookingRequestRejectedWebFlowSync()} — {@code web} or {@code api}.
     */
    public function dispatchBookingRequestRejectedNotifications(int $bookingId, int $driverId, string $channel = 'web'): void
    {
        dispatch(new NotifyBookingRequestRejectedJob($bookingId, $driverId, $channel));
    }

    /**
     * Worker entrypoint: in-app notification, FCM to passenger, optional reject email, optional SMS.
     *
     * @param  string  $channel  {@code web} matches legacy web (email/SMS gated by passenger prefs); {@code api} matches legacy API behaviour.
     */
    public function notifyBookingRequestRejectedWebFlowSync(Booking $booking, User $driver, string $channel = 'web'): void
    {
        $booking->loadMissing(['passenger', 'ride.driver']);
        $ride = $booking->ride;
        $passenger = $booking->passenger;
        if (!$ride || !$passenger) {
            return;
        }

        $notification = Notification::create([
            'type' => 2,
            'ride_id' => $booking->ride_id,
            'posted_to' => $booking->id,
            'posted_by' => $ride->added_by,
            'message' => getNotificationMessageText(
                'booking_request_declined',
                $passenger,
                [],
                'Booking request declined'
            ),
            'status' => 'reject',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination,
        ]);

        $this->sendFCM($notification->message, $passenger);

        $sendEmail = $channel === 'api'
            || (isset($passenger->email_notification) && (int) $passenger->email_notification === 1);

        if ($sendEmail && !empty($passenger->email)) {
            $data = [
                'first_name' => $passenger->first_name,
                'seats' => $booking->seats,
                'price' => $booking->fare,
                'from' => $booking->departure,
                'to' => $booking->destination,
                'date' => $ride->date,
                'time' => $ride->time,
            ];
            Mail::to($passenger->email)->queue(new RejectBookingRequestMail($data));
        }

        $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
            ->where('verified', '1')
            ->where('default', '1')
            ->first();
        if (!$phoneNumber) {
            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                ->where('verified', '1')
                ->first();
        }

        if (!$phoneNumber || env('APP_ENV') === 'local') {
            return;
        }

        $allowSms = $channel === 'api'
            || (isset($passenger->sms_notification) && (int) $passenger->sms_notification === 1);
        if (!$allowSms) {
            return;
        }

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');
        $twilio = new Client($sid, $token);
        $to = $phoneNumber->phone;

        if ($channel === 'web') {
            $title = '';
            $currentHour = date('H');
            if ($currentHour >= 0 && $currentHour < 12) {
                $title = 'Good morning ' . $passenger->first_name . ',';
            } elseif ($currentHour >= 12 && $currentHour < 17) {
                $title = 'Good afternoon ' . $passenger->first_name . ',';
            } else {
                $title = 'Good evening ' . $passenger->first_name . ',';
            }
            $departureTime = date('H:i:s', strtotime((string) $ride->time));
            $depatureDate = date('d F, Y', strtotime((string) $ride->date));
            $smsBody = $title . "\n" . 'From ProximaRide: We are sorry to inform you that your booking request has been declined by the driver.' . "\n"
                . 'Ride from ' . $booking->departure . ' to ' . $booking->destination . ' on ' . $depatureDate . ' at ' . $departureTime . "\n"
                . 'All payments that you have made will be refunded to you immediately';
        } else {
            $title = '';
            $currentHour = date('H');
            if ($currentHour >= 0 && $currentHour < 12) {
                $title = 'Good morning ' . $passenger->first_name . '';
            } elseif ($currentHour >= 12 && $currentHour < 17) {
                $title = 'Good afternoon ' . $passenger->first_name . '';
            } else {
                $title = 'Good evening ' . $passenger->first_name . '';
            }
            $depatureDate = date('d F, Y H:i:s', strtotime((string) $ride->date . ' ' . (string) $ride->time));
            $driverUser = $ride->driver;
            $driverPhone = $driverUser->phone ?? '';
            $smsBody = $title . "\nDriver reject your booking request from this ride\nTrip detail\nOrigin: " . $booking->departure
                . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate
                . "\nDriver name: " . ($driverUser->first_name ?? '') . "\nDriver phone number: " . $driverPhone;
        }

        try {
            $twilio->messages->create($to, [
                'from' => $from,
                'body' => $smsBody,
            ]);
        } catch (\Throwable $e) {
            $msgPreview = strlen($smsBody) > 80 ? substr($smsBody, 0, 80) . '...' : $smsBody;
            Log::info('Reject booking SMS failed to ' . $to . '. Message: ' . $msgPreview . ' because ' . $e->getMessage());
        }
    }

    /**
     * Queue passenger notifications after the driver cancels a ride with active bookings ({@see NotifyDriverCancelledRidePassengersJob}).
     *
     * @param  list<int>  $bookingIds
     * @param  string  $channel  {@code web} or {@code api} — see {@see notifyDriverCancelledRidePassengerWebFlowSync()}.
     */
    public function dispatchDriverRideCancelledPassengerNotifications(
        int $rideId,
        int $driverUserId,
        array $bookingIds,
        string $cancellationMessage,
        string $channel = 'web'
    ): void {
        $bookingIds = array_values(array_filter(array_map('intval', $bookingIds)));
        if ($bookingIds === []) {
            return;
        }

        dispatch(new NotifyDriverCancelledRidePassengersJob(
            $rideId,
            $driverUserId,
            $bookingIds,
            $cancellationMessage,
            $channel
        ));
    }

    /**
     * Per-booking: in-app notification, FCM, optional emails, optional SMS (web vs API legacy parity).
     */
    public function notifyDriverCancelledRidePassengerWebFlowSync(
        Booking $booking,
        Ride $ride,
        User $driver,
        string $cancellationMessage,
        string $channel = 'web'
    ): void {
        if ((int) $driver->id !== (int) $ride->added_by) {
            return;
        }

        $booking->loadMissing(['passenger', 'ride.driver']);
        $passenger = $booking->passenger;
        if (!$passenger) {
            return;
        }

        $ride->loadMissing('driver');

        if ($channel === 'web') {
            $notification = Notification::create([
                'type' => 2,
                'ride_id' => $ride->id,
                'posted_to' => $booking->id,
                'posted_by' => $ride->added_by,
                'message' => 'Your ride has been cancelled',
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination,
            ]);
        } else {
            $notification = Notification::create([
                'type' => 2,
                'ride_id' => $ride->id,
                'posted_to' => $booking->id,
                'posted_by' => $ride->added_by,
                'message' => getNotificationMessageText(
                    'your_ride_has_been_cancelled',
                    $passenger,
                    [],
                    'Your ride has been cancelled'
                ),
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination,
            ]);
        }

        $this->sendFCM($notification->message, $passenger);

        if ($channel === 'web') {
            if (isset($passenger->email_notification) && (int) $passenger->email_notification === 1 && !empty($passenger->email)) {
                $driverUser = $ride->driver;
                $data = [
                    'driver_name' => $driverUser->first_name ?? '',
                    'passenger_name' => $passenger->first_name,
                    'from' => $booking->departure,
                    'to' => $booking->destination,
                    'date' => Carbon::parse($ride->date)->format('F d, Y'),
                    'time' => $ride->time,
                    'seats' => $booking->seats,
                    'total_price' => $booking->fare,
                    'cancellation_reason' => $cancellationMessage,
                ];
                Mail::to($passenger->email)->queue(new DriverCancelRideMail($data));
                Mail::to($passenger->email)->queue(new DriverCancelRideWithReasonMail($data));
            }
        } elseif (!empty($passenger->email)) {
            $driverUser = $ride->driver;
            $data = [
                'driver_name' => $driverUser->first_name ?? '',
                'passenger_name' => $passenger->first_name,
                'from' => $booking->departure,
                'to' => $booking->destination,
                'date' => Carbon::parse($ride->date)->format('F d, Y'),
                'time' => $ride->time,
                'seats' => $booking->seats,
                'total_price' => $booking->fare,
            ];
            Mail::to($passenger->email)->queue(new DriverCancelRideMail($data));
        }

        $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
            ->where('verified', '1')
            ->where('default', '1')
            ->first();
        if (!$phoneNumber) {
            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                ->where('verified', '1')
                ->first();
        }

        if (!$phoneNumber || env('APP_ENV') === 'local') {
            return;
        }

        if ($channel === 'web' && (!isset($passenger->sms_notification) || (int) $passenger->sms_notification !== 1)) {
            return;
        }

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');
        $twilio = new Client($sid, $token);
        $to = $phoneNumber->phone;

        if ($channel === 'web') {
            $title = '';
            $currentHour = date('H');
            if ($currentHour >= 0 && $currentHour < 12) {
                $title = 'Good morning ' . $passenger->first_name . ',';
            } elseif ($currentHour >= 12 && $currentHour < 17) {
                $title = 'Good afternoon ' . $passenger->first_name . ',';
            } else {
                $title = 'Good evening ' . $passenger->first_name . ',';
            }
            $departureTime = date('H:i', strtotime((string) $ride->time));
            $departureDate = date('d F, Y', strtotime((string) $ride->date));
            $smsBody = $title . "\n"
                . 'From ProximaRide: we are sorry to inform you that your ride from ' . $booking->departure
                . ' to ' . $booking->destination
                . ' on ' . $departureDate
                . ' at ' . $departureTime . " has been cancelled by the driver.\n"
                . 'All amounts that you have made for this booking will be refunded to you immediately.';
        } else {
            $title = '';
            $currentHour = date('H');
            if ($currentHour >= 0 && $currentHour < 12) {
                $title = 'Good morning ' . $passenger->first_name . '';
            } elseif ($currentHour >= 12 && $currentHour < 17) {
                $title = 'Good afternoon ' . $passenger->first_name . '';
            } else {
                $title = 'Good evening ' . $passenger->first_name . '';
            }
            $depatureDate = date('d F, Y H:i:s', strtotime((string) $ride->date . ' ' . (string) $ride->time));
            $driverUser = $ride->driver;
            $driverPhone = $driverUser->phone ?? '';
            $smsBody = $title . "\nDriver cancelled this ride\nTrip detail\nOrigin: " . $booking->departure
                . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate
                . "\nDriver name: " . ($driverUser->first_name ?? '') . "\nDriver phone number: " . $driverPhone;
        }

        try {
            $twilio->messages->create($to, [
                'from' => $from,
                'body' => $smsBody,
            ]);
        } catch (\Throwable $e) {
            if ($channel === 'web') {
                Log::error("Failed to send SMS to {$to}: " . $e->getMessage());
            } else {
                Log::info('can not send text to ' . $to . ' and message is ' . $smsBody . ' because ' . $e->getMessage());
            }
        }
    }
}
