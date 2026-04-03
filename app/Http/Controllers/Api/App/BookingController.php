<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\BookingRequestConfirmationMail;
use App\Mail\BookingRequestMail;
use App\Mail\DriverDetailsMail;
use App\Mail\PassengerDetailsMail;
use App\Mail\PaymentInvoiceMail;
use App\Models\Booking;
use App\Models\BookingPageSettingDetail;
use App\Models\Card;
use App\Models\RideDetail;
use App\Models\FeaturesSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\FolkRideSetting;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\City;
use App\Models\SiteSetting;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SeatDetail;
use App\Models\TopUpBalance;
use App\Models\CoffeeWallet;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Stripe\Stripe;
use Twilio\Rest\Client;
use App\Services\BookingRequestRejectService;
use App\Services\SeatHoldService;
use App\Mail\SecuredCashPaymentCodeMail;
use App\Models\FeaturesSettingDetail;
use App\Models\Message;

class BookingController extends Controller
{
    use StatusResponser;

    public function create(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $genderLabel = Step1PageSettingDetail::getByLanguageWithFallback($this->getSelectedLanguageId(), $this->defaultLang?->id);

        $messages = $this->successMessage;

        // Check if user has suspanded
        if ($user->suspand === '1') {
            return $this->apiErrorResponse($messages->acc_suspend_message ?? null, 200);
        }

        $ride = Ride::where('id', $request->id);

        $ride = $ride->with('detail')->with(['driver' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns you want to select
            $query->withTrashed(); // Include soft-deleted users
        }])->with('pendingSeatDetail')
            ->with(['bookings' => function ($query) {
                // Select specific columns from bookings
                $query->select('id', 'ride_id', 'seats', 'user_id', 'type', 'secured_cash_attempt_count', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
                    ->where('status', '<>', 3)
                    ->where('status', '<>', 4)
                    ->withActivePassenger()
                    ->with('transaction_no_coffee_sum')
                    ->with(['passenger' => function ($query) {
                        // Select specific columns from passenger
                        $query->select('id', 'profile_image');
                    }]);
            }]);

        $ride = $ride->first();

        $from_stop_id = $request->input('from_stop_id', 0);
        $to_stop_id = $request->input('to_stop_id', 0);

        if ($ride) {

            $ride = $this->makeDetailOfRide($ride, $from_stop_id, $to_stop_id);

            if ($ride->isCashPayment()) {
                $ride->payment_method_slug = 'cash';
            } elseif ($ride->isSecureCashPayment()) {
                $ride->payment_method_slug = 'secured_cash';
            } else {
                $ride->payment_method_slug = 'online';
            }

            // Calculate seats left
            $bookedSeats = $ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->withActivePassenger()
                ->sum('seats');
            $ride->seats_left = intval($ride->seats) - intval($bookedSeats);

            $rideFeatureOptionGroups = $this->getRideFeatureOptionGroups($this->selectedLanguage?->id, $this->defaultLang?->id);
            $bookingTypeOptions = $this->buildRideFeatureOptionMap($rideFeatureOptionGroups, 'cancellation');
            $bookingTypeOption = $bookingTypeOptions[(int) $ride->booking_type] ?? null;
            if ($ride->booking_type) {
                $ride->booking_type_slug = $bookingTypeOption->slug ?? null;
                $ride->booking_type_tooltip = $bookingTypeOption->tooltip ?? null;
                $ride->booking_type = $bookingTypeNames[$ride->booking_type] ?? null;
            }


            $ride->driver->driven_rides = $ride->driver->rides()
                ->where('status', '!=', 2)
                ->where(function ($query) {
                    $query->whereDate('rides.date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('rides.date', '=', now()->toDateString())
                                ->whereTime('rides.time', '<=', now()->toTimeString());
                        });
                })
                ->get()
                ->flatMap(function ($ride) {
                    return $ride->bookings()->pluck('seats');
                })
                ->sum();

            // Calculate age
            if ($ride->driver->dob) {
                $dob = Carbon::parse($ride->driver->dob);
                $ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $ride->driver->age = null; // Handle case where dob is not set
            }

            if ($ride->driver->gender) {
                $ride->driver->gender = $ride->driver->gender;

                if ($ride->driver->gender === 'male') {
                    $ride->driver->gender_label = $genderLabel->male_option_label;
                } elseif ($ride->driver->gender === 'female') {
                    $ride->driver->gender_label = $genderLabel->female_option_label;
                } elseif ($ride->driver->gender === 'prefer not to say') {
                    $ride->driver->gender_label = $genderLabel->prefer_option_label;
                }
            }

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($ride) {
                return (int) optional($rating->ride)->added_by === (int) $ride->added_by;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $ride->driver->average_rating = $totalAverage;
        }

        $setting = SiteSetting::getCached();
        $stateTax = 0;
        if (isset($setting->deduct_tax) && $setting->deduct_tax == "deduct_from_passenger" && $setting->tax_type == "state_wise_tax") {
            $locationBeforeComma = explode(',', $ride->detail->departure);
            $getFromState = City::with('state:id,tax')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ', ['%' . $locationBeforeComma[0] . '%'])->first();
            if (isset($getFromState) && !empty($getFromState)) {
                $stateTax = $getFromState->state->tax;
            }
        }

        $topBalance = TopUpBalance::where('user_id', $user->id)
            ->selectRaw('SUM(dr_amount) - SUM(cr_amount) as balance')
            ->value('balance');
        $coffeeBalance = CoffeeWallet::selectRaw('SUM(dr_amount) - SUM(cr_amount) as balance')
            ->value('balance');

        $bookingPage = BookingPageSettingDetail::getByLanguageWithFallback($this->getSelectedLanguageId(), $this->defaultLang?->id);

        $data = [
            'ride' => $ride,
            'messages' => $messages,
            'setting' => $setting,
            'bookingPage' => $bookingPage,
            'balance' => $topBalance,
            'coffeeBalance' => $coffeeBalance,
            'stateTax' => $stateTax
        ];
        return $this->successResponse($data, 'Get booking page successfully');
    }

    public function bookingStore(Request $request)
    {
        $id = (int) ($request->input('ride_id') ?: $request->input('id'));

        if ($id <= 0) {
            return $this->apiErrorResponse('Ride not found', 404);
        }

        $ride = Ride::with([
            'rideStops' => fn($query) => $query->orderBy('stop_order'),
            'rideStopSegments',
            'detail'
        ])->where('id', $id)->first();

        if (!$ride) {
            return $this->apiErrorResponse('Ride not found', 404);
        }

        $from_stop_id = $request->input('from_stop_id', 0);
        $to_stop_id = $request->input('to_stop_id', 0);

        $ride = $this->makeDetailOfRide($ride, $from_stop_id, $to_stop_id);

        $errorMsg = $this->successMessage;

        $user = Auth::guard('sanctum')->user();
        $user = User::where('id', $user->id)->with('primaryPhone')->first();

        $passengerPhoneNumber = $user->primaryPhone()?->phone ?? $user->phone;
        $driverPhoneNumber = $ride->driver?->primaryPhone()?->phone ?? $ride->driver?->phone;

        //////////////////////////////////
        // Validation before booking logic

        // Student booking limit for Cash rides: Limit students to 1-2 seats per ride if payment method is Cash
        // Apply limit only for students on Cash rides
        if ($user->isStudent() && $ride->isCashPayment()) {
            if ($request->seats > 2) {
                return $this->apiErrorResponse("Students are limited to booking a maximum of 2 seats per ride for Cash payment rides.", 200);
            }
        }

        // 
        if ($ride->isSecureCashPayment()) {
            $returnUrl = url()->current() . (request()->getQueryString() ? '?' . request()->getQueryString() : '');
            session(['return_url_after_action' => $returnUrl]);
            if (!$user->hasPhone()) {
                return $this->apiErrorResponse($errorMsg->add_your_phone ?? 'Add your phone number', 200);
            }
            if (!$user->hasVerifiedPhone()) {
                return $this->apiErrorResponse($errorMsg->verified_number_message ?? 'Verify your phone number', 200);
            }
        }

        if ($user->isBlockedBooking()) {
            return $this->apiErrorResponse($errorMsg->block_booking_message ?? 'You are blocked from booking rides.', 200);
        }

        $bookings = Booking::where('ride_id', $id)->NotRejected()->get();
        $seatsBooked = $bookings->sum('seats') + $request->seats;
        if ($seatsBooked > $ride->seats) {
            return $this->apiErrorResponse($errorMsg->seat_unavailable_message ?? 'Seats are not available for this ride.', 200);
        }


        $isWalletPayment = in_array((string) $request->input('booked_by_wallet'), ['1', 'true', 'True'], true);

        $rules = [
            'online_payment' => 'nullable|numeric|min:0',
            'payment_method' => $isWalletPayment ? 'nullable' : 'required_with:online_payment|nullable|string|in:paypal,credit_card',
            'paypal_id' => $isWalletPayment ? 'nullable' : 'required_if:payment_method,paypal|nullable|string',
            'card_id' => $isWalletPayment ? 'nullable' : 'required_if:payment_method,credit_card|nullable',
            'paypal_email' => 'nullable|string',
            'paypal_payer_id' => 'nullable|string',
            'g_pay' => 'nullable',
            'booked_by_wallet' => 'nullable',
            'seats' => 'required|integer|min:1',
            'driver_message' => 'required',
            'agree_terms' => 'accepted|required',
        ];

        if ($ride->isFirmCancellation()) {
            $rules['firm_agree_terms'] = 'accepted|required';
            $rules['firm_cancellation_understand'] = 'accepted|required';
        }

        // Passenger gatekeeping logic for Pink Ride and Extra Care Ride
        if ($ride->isPinkRide()) {
            // GENDER VALIDATION: Only female passengers can book Pink Rides
            if ($user->gender !== 'female') {
                return $this->apiErrorResponse($errorMsg->pink_ride_female_only ?? 'Only female passengers can book Pink Rides.', 200);
            }

            $rules['pink_ride_agree_terms'] = 'accepted|required';
        }
        if ($ride->isExtraCareRide()) {
            // For passengers booking Extra Care Rides, require government ID (check all possible ID fields)
            $folkRideSetting = FolkRideSetting::getCached();
            if ($folkRideSetting && $folkRideSetting->requiresDriverLicense()) {
                $hasGovernmentId = !empty($user->government_id) || !empty($user->government_issued_id) || !empty($user->driver_license_upload);
                if (!$hasGovernmentId) {
                    return $this->apiErrorResponse($errorMsg->extra_care_ride_government_id_required ?? 'A government-issued photo ID is required to book Extra Care Rides. Please upload your government ID or driver\'s license in your profile.', 200);
                }
            }

            $rules['extra_care_ride_agree_terms'] = 'accepted|required';
        }

        $request->validate($rules);

        ///////////////////////////////////////////////
        //
        if ($ride->price_minor < 1500) {
            // ProximaLocal: no booking fee on rides under $15 per seat
            $booking_fee = 0;
        } else {
            // Student booking fee waiver: Validate and apply waiver with card expiration check
            $adjustedBookingCredit = $this->validateStudentBookingFee($user, $request->booking_credit);
            $booking_fee = $adjustedBookingCredit;
        }
        $seats_amount = $request->seats_amount;
        $payment_amount = $request->seats_amount;
        if ($ride->isCashPayment()) {
            $payment_amount = $booking_fee;
        }



        $amount = round((float) $request->input('online_payment', 0), 2);
        $isNativePay = in_array((string) $request->input('g_pay'), ['1', 'true', 'True'], true);
        $paymentMethod = (string) $request->input('payment_method', '');

        if ($amount <= 0) {
            $bookingResponse = $this->completeBooking($id, $user->id, $request, null);

            return $this->mergeBookingResponseWithPayment($bookingResponse, [
                'status' => 'not_required',
                'amount' => 0,
                'payment_method' => $ride->isCashPayment() ? 'cash' : null,
                'reference' => null,
                'provider' => 'none',
            ]);
        }

        ///////////////////////////////////////////
        // make a booking
        ///////////////////////////////////////////




        ///////////////////////////////////////////
        // send notifications
        ///////////////////////////////////////////



        ///////////////////////////////////////////
        // process payment
        ///////////////////////////////////////////
        $stripId = null;
        // by wallet
        if ($isWalletPayment) {
            $bookingResponse = $this->completeBooking($id, $user->id, $request, null);

            return $this->mergeBookingResponseWithPayment($bookingResponse, [
                'status' => 'paid',
                'amount' => $amount,
                'payment_method' => 'wallet',
                'reference' => null,
                'provider' => 'wallet',
            ]);
        }

        if ($paymentMethod === 'paypal') {

            $bookingResponse = $this->completeBooking($id, $user->id, $request, (string) $request->input('paypal_id'));

            return $this->mergeBookingResponseWithPayment($bookingResponse, [
                'status' => 'paid',
                'amount' => $amount,
                'payment_method' => 'paypal',
                'reference' => (string) $request->input('paypal_id'),
                'paypal_email' => (string) $request->input('paypal_email'),
                'paypal_payer_id' => (string) $request->input('paypal_payer_id'),
            ]);
        }

        if ($isNativePay) {

            $nativePayDetails = $this->getNativePayDetails((string) $request->input('card_id'));
            $request->merge([
                'card_type' => $nativePayDetails['card_type'] ?? null,
                'last_four_digits' => $nativePayDetails['last_four_digits'] ?? null,
                'expiration_date' => $nativePayDetails['expiration_date'] ?? null,
                'cardholder_name' => $nativePayDetails['cardholder_name'] ?? null,
            ]);

            $bookingResponse = $this->completeBooking($id, $user->id, $request, (string) $request->input('card_id'));

            return $this->mergeBookingResponseWithPayment($bookingResponse, [
                'status' => 'paid',
                'amount' => $amount,
                'payment_method' => 'credit_card',
                'reference' => (string) $request->input('card_id'),
                'provider' => 'stripe_native_pay',
                'card' => $nativePayDetails,
            ]);
        }

        if ($paymentMethod === 'credit_card') {
            try {
                $card = Card::where('id', $request->input('card_id'))
                    ->where('user_id', $user->id)
                    ->firstOrFail();

                if (empty($card->stripe_payment_method_id)) {
                    return $this->apiErrorResponse('Selected card is not linked to Stripe.', 422);
                }

                if (empty($user->stripe_customer_id)) {
                    return $this->apiErrorResponse('Stripe customer profile is missing for this user.', 422);
                }

                Stripe::setApiKey(env('STRIPE_SECRET'));

                $paymentIntent = PaymentIntent::create([
                    'amount' => (int) round($amount * 100),
                    'currency' => 'cad',
                    'customer' => $user->stripe_customer_id,
                    'payment_method' => $card->stripe_payment_method_id,
                    'off_session' => true,
                    'confirm' => true,
                ]);

                $savedCardDetails = [
                    'card_type' => $card->card_type ?: null,
                    'last_four_digits' => $card->card_number ?: null,
                    'expiration_date' => ($card->exp_month && $card->exp_year)
                        ? $card->exp_month . '/' . $card->exp_year
                        : null,
                    'cardholder_name' => $card->name_on_card ?: null,
                ];
                $request->merge($savedCardDetails);

                Log::info('saved card payment');

                $bookingResponse = $this->completeBooking($id, $user->id, $request, $paymentIntent->id);

                return $this->mergeBookingResponseWithPayment($bookingResponse, [
                    'status' => 'paid',
                    'amount' => $amount,
                    'payment_method' => 'credit_card',
                    'reference' => $paymentIntent->id,
                    'card_id' => (string) $request->input('card_id'),
                    'provider' => 'stripe',
                    'card' => $savedCardDetails,
                ]);
            } catch (\Throwable $e) {
                Log::error('Booking payment failed', [
                    'user_id' => $user?->id,
                    'payment_method' => $request->input('payment_method'),
                    'card_id' => $request->input('card_id'),
                    'amount' => $amount,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return $this->apiErrorResponse($e->getMessage(), 422);
            }
        }

        return $this->apiErrorResponse('Unsupported payment method.', 422);
    }

    private function mergeBookingResponseWithPayment(array $bookingResponse, array $paymentData): array
    {
        if (($bookingResponse['status'] ?? null) !== 'Success') {
            return $bookingResponse;
        }

        $data = is_array($bookingResponse['data'] ?? null) ? $bookingResponse['data'] : [];
        $data['payment'] = $paymentData;

        return $this->successResponse(
            $data,
            $bookingResponse['message'] ?? 'Payment processed successfully'
        );
    }


    private function getNativePayDetails(string $paymentIntentId): array
    {
        if ($paymentIntentId === '') {
            return [];
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $paymentMethodId = is_string($paymentIntent->payment_method ?? null)
                ? $paymentIntent->payment_method
                : ($paymentIntent->payment_method->id ?? null);

            if (!$paymentMethodId) {
                return [];
            }

            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

            return [
                'card_type' => $paymentMethod->card->brand ?? null,
                'last_four_digits' => $paymentMethod->card->last4 ?? null,
                'exp_month' => isset($paymentMethod->card->exp_month)
                    ? (string) $paymentMethod->card->exp_month
                    : null,
                'exp_year' => isset($paymentMethod->card->exp_year)
                    ? (string) $paymentMethod->card->exp_year
                    : null,
                'expiration_date' => isset($paymentMethod->card->exp_month, $paymentMethod->card->exp_year)
                    ? $paymentMethod->card->exp_month . '/' . $paymentMethod->card->exp_year
                    : null,
                'wallet_type' => $paymentMethod->card->wallet->type ?? null,
                'payment_method_type' => $paymentMethod->type ?? 'card',
            ];
        } catch (\Throwable $e) {
            Log::warning('Unable to resolve native pay card details', [
                'payment_intent_id' => $paymentIntentId,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function completeBooking($id, $user_id, Request $request, $stripId = null)
    {
        $result = $this->completeBookingUnifiedFlow((int) $id, (int) $user_id, $stripId, $request);

        $data = ['booking' => $result['booking']];
        return $this->successResponse(
            $data,
            $this->successMessage->book_seat_message . ' ' . $request->seats . ' ' . $this->successMessage->book_seat_message_end_part
        );
    }



    public function AcceptBookingRequest(Request $request)
    {
        $booking = Booking::whereId($request->booking_id)->with(['ride', 'passenger'])->first();

        if (!$booking || !$booking->ride) {
            return $this->apiErrorResponse('Booking request not found', 200);
        }

        $user = Auth::guard('sanctum')->user();
        if ($booking->ride->added_by != $user->id) {
            return $this->apiErrorResponse('Booking request not found', 200);
        }

        $message = $this->successMessage;

        if ($booking && $booking->isRequested()) {

            $booking->update([
                'status' => '1',
                'expires_at' => null,
            ]);
            // go to job as background
            $this->notifyBookingRequestApprovedWebFlow($booking, $user, false);

            $genderLabel = Step1PageSettingDetail::getByLanguageWithFallback($this->getSelectedLanguageId(), $this->defaultLang?->id);
            $bookings = Booking::where('ride_id', $booking->ride_id)->where('status', '1')
                ->with(['passenger' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
                }])
                ->get();

            foreach ($bookings as $row) {
                if ($row->passenger->dob) {
                    $dob = Carbon::parse($row->passenger->dob);
                    $row->passenger->age = $dob->diffInYears(Carbon::now());
                } else {
                    $row->passenger->age = null;
                }

                if ($row->passenger->gender && isset($genderLabel)) {
                    if ($row->passenger->gender === 'male') {
                        $row->passenger->gender_label = $genderLabel->male_option_label;
                    } elseif ($row->passenger->gender === 'female') {
                        $row->passenger->gender_label = $genderLabel->female_option_label;
                    } elseif ($row->passenger->gender === 'prefer not to say') {
                        $row->passenger->gender_label = $genderLabel->prefer_option_label;
                    }
                }

                $row->rating = Rating::where('type', '2')->where('ride_id', $row->ride_id)->where('posted_to', $row->id)->first();

                $ratings = Rating::where('status', 1)->where('type', '2')->get();
                $filteredRatings = $ratings->filter(function ($rating) use ($row) {
                    return $rating->booking->user_id === $row->user_id;
                });

                $row->passenger_average_rating = $filteredRatings->avg('average_rating');
            }

            return $this->successResponse(['bookings' => $bookings], strip_tags($message->request_accept_message ?? 'You have accepted the request successfully'));
        }

        return $this->apiErrorResponse(strip_tags($message->request_expired_message ?? 'Request expired'), 200);
    }

    /**
     * Decline a pending booking request: persist refunds/seats via {@see BookingRequestRejectService},
     * then queue passenger notifications ({@see \App\Jobs\NotifyBookingRequestRejectedJob}).
     */
    public function RejectBookingRequest(Request $request)
    {
        $message = $this->successMessage;

        $booking = Booking::with(['ride.driver', 'passenger'])->whereId($request->booking_id)->first();

        if (!$booking || !$booking->ride) {
            return $this->apiErrorResponse('Booking request not found', 200);
        }

        $user = Auth::guard('sanctum')->user();
        if ($booking->ride->added_by != $user->id) {
            return $this->apiErrorResponse('Booking request not found', 200);
        }

        if (!$booking->isRequested()) {
            return $this->apiErrorResponse(strip_tags($message->request_expired_message ?? 'Request expired'), 200);
        }

        $reject = app(BookingRequestRejectService::class)->rejectApi($booking);
        if (!$reject['ok']) {
            return $this->apiErrorResponse($reject['api_error'], 200);
        }

        $this->notifyBookingRequestRejectedWebFlow($booking, $user, 'api');

        $selectedLanguageAbbr = app()->getLocale();
        $messages = null;
        if ($selectedLanguageAbbr) {
            $langRow = Language::where('abbreviation', $selectedLanguageAbbr)->first();
            if ($langRow) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $langRow->id)->select('reject_booking_message', 'general_error_message')->first();
            }
        } else {
            $langRow = Language::where('is_default', 1)->first();
            if ($langRow) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $langRow->id)->select('reject_booking_message', 'general_error_message')->first();
            }
        }

        $bookings = Booking::where('ride_id', $booking->ride_id)->where('status', '1')
            ->with(['passenger' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob');
            }])
            ->get();

        $data = ['booking' => $booking, 'bookings' => $bookings];

        return $this->successResponse($data, $messages?->reject_booking_message ?? null);
    }


    public function seatOnHold(Request $request)
    {
        $messages = $this->successMessage;
        $bookingPage = BookingPageSettingDetail::getByLanguageWithFallback($this->getSelectedLanguageId(), $this->defaultLang?->id);

        $user = Auth::guard('sanctum')->user();
        $seat = SeatDetail::where('id', $request->seat_id)->first();

        $result = app(SeatHoldService::class)->process($seat, $user);

        switch ($result['outcome']) {
            case SeatHoldService::OUTCOME_HELD:
                return $this->successResponse(
                    ['getSeatDetail' => $result['seat']],
                    strip_tags($messages->seat_hold_success_message ?? 'Seat on hold successfully')
                );
            case SeatHoldService::OUTCOME_RELEASED:
                return $this->successResponse(
                    ['getSeatDetail' => $result['seat']],
                    strip_tags($bookingPage->seat_hold_message ?? 'Seat on pending successfully')
                );
            case SeatHoldService::OUTCOME_BOOKED:
                return $this->apiErrorResponse(
                    strip_tags($messages->seat_booked_message ?? 'Seat booked please select another seat'),
                    200
                );
            case SeatHoldService::OUTCOME_HELD_BY_OTHER:
                return $this->apiErrorResponse($messages->seat_hold_message ?? null, 200);
            case SeatHoldService::OUTCOME_NOT_FOUND:
            default:
                return $this->apiErrorResponse(
                    strip_tags($messages->general_error_message ?? 'Seat not found'),
                    200
                );
        }
    }


    public function createPaymentIntent(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'amount' => 'required|integer',
            'currency' => 'required|string',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount, // e.g. 5000 = $50.00
                'currency' => $request->currency,
                'payment_method_types' => ['card'],
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => ['token' => $request->stripeToken]
                ],
                'confirmation_method' => 'automatic',
                'confirm' => true,
            ]);

            return response()->json([
                'paymentIntentId' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function bookingNumberCheck(Request $request)
    {
        $selectedLanguage = app()->getLocale();

        $messages = [];
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('verified_number_message', 'add_your_phone')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('verified_number_message', 'add_your_phone')->first();
            }
        }

        $user = Auth::guard('sanctum')->user();

        $user = User::where('id', $user->id)->first();
        $phoneNumber = PhoneNumber::where('user_id', $user->id)->first();
        if (is_null($phoneNumber)) {
            return response()->json([
                'status' => false,
                'message' => $messages->add_your_phone ?? 'Add your phone number'
            ]);
        }
        $phoneVerification = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
        if (!$phoneVerification) {
            return response()->json([
                'status' => false,
                'message' => $messages->verified_number_message ?? 'Verify your phone number'
            ]);
        }

        return response()->json([
            'status' => true
        ]);
    }

    protected function buildRideFeatureOptionMap($optionGroups, string $groupKey): array
    {
        return collect($optionGroups[$groupKey] ?? [])
            ->mapWithKeys(function ($option) {
                return [(int) ($option->features_setting_id ?? $option->id ?? 0) => $option];
            })
            ->all();
    }
}
