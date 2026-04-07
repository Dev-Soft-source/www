<?php

namespace App\Http\Controllers;

use App\Mail\AcceptBookingRequestMail;
use App\Mail\BookingRequestConfirmationMail;
use App\Mail\ArbitrationCancelledMail;
use App\Mail\BookingRequestMail;
use App\Mail\DriverDetailsMail;
use App\Mail\PassengerCancelBookingMail;
use App\Mail\PassengerDetailsMail;
use App\Mail\PaymentInvoiceMail;
use App\Models\Booking;
use App\Models\City;
use App\Models\BookingPageSettingDetail;
use App\Models\CancellationHistory;
use App\Models\BillingAddressSettingDetail;
use App\Models\Card;
use App\Models\CoffeeWallet;
use App\Models\Message;
use App\Models\FCMToken;
use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\FolkRideSetting;
use App\Models\PinkRideSetting;
use App\Models\Language;
use App\Models\NoShowHistory;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\PostRidePageSettingDetail;
use App\Models\Ride;
use App\Models\RideDetail;
use App\Models\SeatDetail;
use App\Models\SiteSetting;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use App\Models\User;
use App\Models\TripsPageSettingDetail;
use App\Services\BookingCancellationService;
use App\Services\BookingRequestRejectService;
use App\Services\SeatHoldService;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Customer;
use Twilio\Rest\Client;
use App\Events\MessageSentEvent;
use App\Mail\PassengerListMail;
use App\Mail\RideApprovalEmail;
use App\Mail\SecuredCashPaymentCodeMail;
use App\Models\ChatsPageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use DateTime;

class BookingController extends Controller
{


    /**
     * Make a Booking of a Ride
     * @id: Ride's id
     * @routeId: Ride's route id, it is only available on rides with multi stops
     * 
     */
    public function create($lang = null, $id, $from_stop_id = null, $to_stop_id = null)
    {
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();

        // Check if user has suspanded
        if ($user->isSuspended()) {
            return back()->with('message', $this->successMessage['admin_block_account_message'] ?? 'Your account has been suspended by the admin');
        }

        $ride = Ride::with(['rideDetail', 'rideStops'])->where('id', $id)->first();
        $ride = $this->makeDetailOfRide($ride, $from_stop_id, $to_stop_id);

        $bookingPage = BookingPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $paymentSettingDetail = BillingAddressSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $cards = Card::where('user_id', $user_id)->orderBy('id', 'desc')->get();

        Stripe::setApiKey(config('stripe.secret'));
        // Fetch card details from Stripe
        foreach ($cards as $card) {
            if ($card->stripe_payment_method_id) {
                $card->paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
            }
        }

        $topBalance = (float) (
            TopUpBalance::where('user_id', $user->id)
            ->selectRaw('COALESCE(SUM(dr_amount), 0) - COALESCE(SUM(cr_amount), 0) as balance')
            ->value('balance') ?? 0
        );
        $coffeeBalance = (float) (
            CoffeeWallet::selectRaw('COALESCE(SUM(dr_amount), 0) - COALESCE(SUM(cr_amount), 0) as balance')
            ->value('balance') ?? 0
        );

        $setting = SiteSetting::getCached();
        $settingTaxPercentage = 0;
        if (isset($setting->deduct_tax) && $setting->deduct_tax == "deduct_from_passenger") {
            if ($setting->tax_type == "state_wise_tax") {
                $getFromState = City::with('state:id,tax')->where('status', '1')->where('id',  $ride->city_id)->first();
                if (isset($getFromState) && !empty($getFromState)) {
                    $settingTaxPercentage = $getFromState->state->tax;
                }
            } else {
                $settingTaxPercentage = $setting->tax;
            }
        }
        $settingFirmDiscount = $setting->frim_discount ?? 0;
        $settingBookingFee = $setting->booking_price;

        // user's status
        $isStudentFeeWaived = $user->isBookingFeeCurrentlyWaived();
        $isChargeBooking = $user->hasBookingChargeFlag();

        return view(
            'booking',
            [
                'user' => $user,
                'bookingPage' => $bookingPage,
                'rideDetailPage' => $rideDetailPage,
                'ride' => $ride,
                'cards' => $cards,
                'paymentSettingDetail' => $paymentSettingDetail,
                'topUpBalance' => $topBalance,
                'setting' => $setting,
                'settingFirmDiscount' => $settingFirmDiscount,
                'settingBookingFee' => $settingBookingFee,
                'settingTaxPercentage' => $settingTaxPercentage,
                'coffeeBalance' => $coffeeBalance,

                'isChargeBooking' => $isChargeBooking,
                'isStudentFeeWaived' => $isStudentFeeWaived,

                'from_stop_id' => $from_stop_id,
                'to_stop_id' => $to_stop_id,
            ]
        );
    }

    /**
     * Make a book
     * param: @id: a ride's id
     */
    public function bookingStore($id, Request $request)
    {
        $ride = Ride::with([
            'rideStops' => fn($query) => $query->orderBy('stop_order'),
            'rideStopSegments',
            'detail'
        ])->where('id', $id)->first();

        $from_stop_id = $request->input('from_stop_id', 0);
        $to_stop_id = $request->input('to_stop_id', 0);

        $ride = $this->makeDetailOfRide($ride, $from_stop_id, $to_stop_id);

        $errorMsg = $this->successMessage;

        $user = auth()->user();
        $user = User::where('id', $user->id)->with('primaryPhone')->first();

        $passengerPhoneNumber = $user->primaryPhone()?->phone ?? $user->phone;
        $driverPhoneNumber = $ride->driver?->primaryPhone()?->phone ?? $ride->driver?->phone;

        //////////////////////////////////
        // Validation before booking logic

        // Student booking limit for Cash rides: Limit students to 1-2 seats per ride if payment method is Cash
        // Apply limit only for students on Cash rides
        if ($user->isStudent() && $ride->isCashPayment()) {
            if ($request->seats > 2) {
                return redirect()->back()->with(['failure' => 'Students are limited to booking a maximum of 2 seats per ride for Cash payment rides.'])->withInput();
            }
        }

        // 
        if ($ride->isSecureCashPayment()) {
            $returnUrl = url()->current() . (request()->getQueryString() ? '?' . request()->getQueryString() : '');
            session(['return_url_after_action' => $returnUrl]);
            if (!$user->hasPhone()) {
                return redirect()
                    ->route('add-phone', ['lang' => $this->selectedLanguage->abbreviation])
                    ->with([
                        'error' => $errorMsg->add_your_phone ?? 'Add your phone number'
                    ]);
            }
            if (!$user->hasVerifiedPhone()) {
                return redirect()
                    ->route('phone', ['lang' => $this->selectedLanguage->abbreviation])
                    ->with([
                        'error' => $errorMsg->verified_number_message ?? 'Verify your phone number',
                        'phone' => $passengerPhoneNumber,
                    ]);
            }
        }

        if ($user->isBlockedBooking()) {
            return redirect()->back()->with(['failure' => $message->block_booking_message ?? null]);
        }

        $bookings = Booking::where('ride_id', $id)->NotRejected()->get();
        $seatsBooked = $bookings->sum('seats') + $request->seats;
        if ($seatsBooked > $ride->seats) {
            return redirect()->back()->with(['failure' => $errorMsg->seat_unavailable_message]);
        }

        $rules = [
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
                return redirect()->back()->with(['failure' => 'Only female passengers can book Pink Rides.']);
            }

            $rules['pink_ride_agree_terms'] = 'accepted|required';
        }
        if ($ride->isExtraCareRide()) {
            // For passengers booking Extra Care Rides, require government ID (check all possible ID fields)
            $folkRideSetting = FolkRideSetting::getCached();
            if ($folkRideSetting && $folkRideSetting->requiresDriverLicense()) {
                $hasGovernmentId = !empty($user->government_id) || !empty($user->government_issued_id) || !empty($user->driver_license_upload);
                if (!$hasGovernmentId) {
                    return redirect()->back()->with(['failure' => 'A government-issued photo ID is required to book Extra Care Rides. Please upload your government ID or driver\'s license in your profile.']);
                }
            }

            $rules['extra_care_ride_agree_terms'] = 'accepted|required';
        }

        if (!((int) $request->input('booked_by_wallet'))) {
            $rules = array_merge($rules, [
                'card_id' => 'required',
            ]);
        }
        // validate the request with all the conditional rules
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

        $bookedByWallet = (int) $request->input('booked_by_wallet');



        ///////////////////////////////////////////////////////////////////////////
        // process of payment by payment method (paypal, stripe, cash, secure cash)
        ///////////////////////////////////////////////////////////////////////////
        $stripId = null;

        if (!$bookedByWallet) {

            $payment_method = $request->input('card_id', 'paypal');

            if ($payment_method === 'paypal') {

                if (!$user->hasVerifiedPhone()) {
                    return redirect()->back()->with(['failure' => $errorMsg->verified_number_message ?? 'Verify your phone number']);
                }
                // Process PayPal payment
                $paypal = new PayPalClient;
                $paypal->setApiCredentials(config('paypal'));
                $token = $paypal->getAccessToken();
                $paypal->setAccessToken($token);

                $paypalEmail = null;

                if (is_numeric($request->card_id)) {
                    $paypalEmail = Card::where('id', $request->card_id)
                        ->where('user_id', $user->id)
                        ->where('payment_method_type', 'paypal')
                        ->value('paypal_email');
                }

                $order = $paypal->createOrder([
                    "intent" => "CAPTURE",
                    "purchase_units" => [
                        [
                            "amount" => [
                                "currency_code" => "CAD",
                                "value" => number_format((float)$payment_amount, 2, '.', '')
                            ]
                        ]
                    ],
                    "application_context" => [
                        "cancel_url" => route('paypal.cancel'),
                        "return_url" => route('paypal.success.booking', [
                            'id' => $id,
                            'user_id' => $user->id,
                        ] + array_filter([
                            'paypal_email' => $paypalEmail,
                        ]) + $request->except('_token')),
                    ]
                ]);

                if (isset($order['id'])) {
                    foreach ($order['links'] as $link) {
                        if ($link['rel'] == 'approve') {
                            return redirect()->away($link['href']);
                        }
                    }
                }

                return redirect()->route('paypal.cancel');
            } else {
                /**
                 * Map card_id (paypal / credit_card / google_pay / apple_pay / saved card id)
                 * to the legacy payment_method field so existing PayPal/Stripe flows keep working.
                 */
                // Helper to redirect back with a generic error
                $genericErrorResponse = function () use ($errorMsg) {
                    return redirect()->back()
                        ->withInput()
                        ->with(['failure' => $errorMsg->general_error_message ?? 'Payment could not be completed. Please try again.']);
                };

                // Helper: charge via Stripe PaymentIntent using a Stripe payment method id (card / Google Pay / Apple Pay)
                $chargeWithStripePaymentMethod = function (string $stripePaymentMethodId, float $amount) use ($user, $genericErrorResponse) {
                    if (!$user->stripe_customer_id) {
                        return $genericErrorResponse();
                    }

                    Stripe::setApiKey(config('stripe.secret'));

                    try {
                        $paymentIntent = PaymentIntent::create([
                            'amount'        => round(($amount * 100), 0),
                            'currency'      => 'CAD',
                            'customer'      => $user->stripe_customer_id,
                            'payment_method' => $stripePaymentMethodId,
                            'off_session'   => true,
                            'confirm'       => true,
                        ]);

                        return $paymentIntent;
                    } catch (\Stripe\Exception\CardException $e) {
                        // Card declined or similar error
                        return redirect()->back()
                            ->withInput()
                            ->with(['failure' => $e->getMessage()]);
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        // General Stripe API error
                        return redirect()->back()
                            ->withInput()
                            ->with(['failure' => $e->getMessage()]);
                    } catch (\Throwable $e) {
                        return $genericErrorResponse();
                    }
                };


                $stripeCardDetails = [];

                try {
                    if (isset($request->gPayApplePayId) && $request->gPayApplePayId != '') {
                        // gPayApplePayId is already a PaymentIntent ID from the frontend (for Google Pay / Apple Pay)
                        $stripId = $request->gPayApplePayId;
                    } elseif ($request->card_id === 'credit_card') {
                        // New credit card entered by user
                        if (!$user->stripe_customer_id) {
                            Stripe::setApiKey(config('stripe.secret'));
                            $customer = Customer::create([
                                'email' => $user->email,
                                'name' => $user->first_name,
                            ]);
                            $user->update(['stripe_customer_id' => $customer->id]);
                        }

                        Stripe::setApiKey(config('stripe.secret'));
                        $stripeToken = $request->stripeToken;

                        if (!$stripeToken) {
                            return redirect()->back()
                                ->withInput()
                                ->withErrors(['card_element' => 'Card details are required.']);
                        }

                        if (str_starts_with($stripeToken, 'tok_')) {
                            $paymentMethod = PaymentMethod::create([
                                'type' => 'card',
                                'card' => [
                                    'token' => $stripeToken,
                                ],
                            ]);
                        } elseif (str_starts_with($stripeToken, 'pm_')) {
                            $paymentMethod = PaymentMethod::retrieve($stripeToken);
                        } else {
                            return redirect()->back()
                                ->withInput()
                                ->with(['failure' => $errorMsg->general_error_message ?? 'Payment method not found. Please try again.']);
                        }

                        $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                        $stripeCardDetails = [
                            'card_type' => $paymentMethod->card->brand ?? '',
                            'cardholder_name' => $paymentMethod->billing_details->name ?? '',
                            'last_four_digits' => $paymentMethod->card->last4 ?? '****',
                            'expiration_date' => isset($paymentMethod->card->exp_month, $paymentMethod->card->exp_year)
                                ? $paymentMethod->card->exp_month . '/' . $paymentMethod->card->exp_year
                                : '',
                        ];

                        if ($payment_amount > 0) {
                            $paymentIntentOrResponse = $chargeWithStripePaymentMethod($paymentMethod->id, $payment_amount);
                            if ($paymentIntentOrResponse instanceof \Illuminate\Http\RedirectResponse) {
                                return $paymentIntentOrResponse;
                            }

                            /** @var \Stripe\PaymentIntent $paymentIntentOrResponse */
                            if ($paymentIntentOrResponse->status !== 'succeeded') {
                                return $genericErrorResponse();
                            }

                            $stripId = $paymentIntentOrResponse->id;
                        }
                    } elseif ($request->card_id === 'google_pay' || $request->card_id === 'apple_pay') {
                        // Use the user's primary saved Google Pay / Apple Pay card
                        $typeMap = [
                            'google_pay'  => 'google_pay',
                            'apple_pay'   => 'apple_pay',
                        ];

                        $mappedType = $typeMap[$request->card_id] ?? null;
                        if (!$mappedType) {
                            return $genericErrorResponse();
                        }

                        $card = Card::where('user_id', $user->id)
                            ->where('payment_method_type', $mappedType)
                            ->where('primary_card', 1)
                            ->first();

                        $stripeCardDetails = [
                            'card_type' => $card?->card_type ?? '',
                            'cardholder_name' => $card?->name_on_card ?? '',
                            'last_four_digits' => $card?->card_number ?? '****',
                            'expiration_date' => isset($card?->exp_month, $card?->exp_year)
                                ? $card->exp_month . '/' . $card->exp_year
                                : '',
                        ];

                        if (!$card || !$card->stripe_payment_method_id) {
                            return redirect()->back()
                                ->withInput()
                                ->with(['failure' => $errorMsg->general_error_message ?? 'Payment method not found. Please add a card first.']);
                        }

                        if ($payment_amount > 0) {
                            $paymentIntentOrResponse = $chargeWithStripePaymentMethod($card->stripe_payment_method_id, $payment_amount);
                            if ($paymentIntentOrResponse instanceof \Illuminate\Http\RedirectResponse) {
                                return $paymentIntentOrResponse;
                            }

                            /** @var \Stripe\PaymentIntent $paymentIntentOrResponse */
                            if ($paymentIntentOrResponse->status !== 'succeeded') {
                                return $genericErrorResponse();
                            }

                            $stripId = $paymentIntentOrResponse->id;
                        }
                    } else {
                        // card_id is expected to be an ID of a saved card in the cards table
                        if (!is_numeric($request->card_id)) {
                            return redirect()->back()
                                ->withInput()
                                ->with(['failure' => $errorMsg->general_error_message ?? 'Selected payment option is not available. Please choose another one.']);
                        }

                        $card = Card::where('id', $request->card_id)
                            ->where('user_id', $user->id)
                            ->first();

                        if (!$card) {
                            return redirect()->back()
                                ->withInput()
                                ->with(['failure' => $errorMsg->general_error_message ?? 'Selected payment option is not available. Please choose another one.']);
                        }

                        if (!$card->stripe_payment_method_id) {
                            return redirect()->back()
                                ->withInput()
                                ->with(['failure' => $errorMsg->general_error_message ?? 'Saved payment method is not properly configured.']);
                        }

                        // Attach the payment method to the customer
                        Stripe::setApiKey(config('stripe.secret'));
                        $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                        $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                        $stripeCardDetails = [
                            'card_type' => $card->card_type ?? '',
                            'cardholder_name' => $card->name_on_card ?? '',
                            'last_four_digits' => $card->card_number ?? '****',
                            'expiration_date' => isset($card->exp_month, $card->exp_year)
                                ? $card->exp_month . '/' . $card->exp_year
                                : '',
                        ];

                        if ($payment_amount > 0) {
                            $paymentIntentOrResponse = $chargeWithStripePaymentMethod($card->stripe_payment_method_id, $payment_amount);
                            if ($paymentIntentOrResponse instanceof \Illuminate\Http\RedirectResponse) {
                                return $paymentIntentOrResponse;
                            }

                            /** @var \Stripe\PaymentIntent $paymentIntentOrResponse */
                            if ($paymentIntentOrResponse->status !== 'succeeded') {
                                return $genericErrorResponse();
                            }

                            $stripId = $paymentIntentOrResponse->id;
                        }
                    }
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    // Handle Stripe API error
                    return redirect()->back()->with(['error' => 'Payment processing failed: ' . $e->getMessage()]);
                }
            }
        }

        // Merge Stripe card details into the request so that it can be used in the booking completion logic and transaction record creation
        if (!empty($stripeCardDetails)) {
            $request->merge($stripeCardDetails);
        }

        // $id is ride id
        return $this->completeBooking($id, $user->id, $request, $stripId);
    }

    public function paypalSuccess(Request $request, $id, $user_id)
    {
        if ($request->filled('token')) {
            $paypal = new PayPalClient;
            $paypal->setApiCredentials(config('paypal'));
            $token = $paypal->getAccessToken();
            $paypal->setAccessToken($token);

            $orderDetails = $paypal->showOrderDetails($request->get('token'));
            $paypalEmail = data_get($orderDetails, 'payer.email_address');

            if ($paypalEmail) {
                $request->merge(['paypal_email' => $paypalEmail]);
            }
        }

        return $this->completeBooking($id, $user_id, $request, null);
    }

    public function completeBooking($id, $user_id, Request $request, $stripId = null, $isWeb = true)
    {
        $this->completeBookingUnifiedFlow((int) $id, (int) $user_id, $stripId, $request);

        return redirect()
            ->route('my_trips', ['lang' => $this->selectedLanguage->abbreviation])
            ->with(['success' => $this->successMessage->book_seat_message]);
    }

    /**
     * to cancel a booking
     */
    public function updateCancelBooking($id, Request $request)
    {
        $request->validate([
            'booking_credit' => 'required|max:25',
            'message' => 'required'
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->back()->with(['failure' => 'Unauthorized']);
        }

        $getSetting = SiteSetting::getCached();

        $cancellationCount = $user->recentPassengerCancellationCount($getSetting->booking_cancel_duration);

        if ($cancellationCount >= $getSetting->booking_cancel_limit) {
            $bookingPage = BookingPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
            return redirect()->back()->with(['failure' => $bookingPage->booking_cancellation_limit_exceed ?? "Booking cancellation limit exceeded"]);
        }

        $booking = Booking::with(['ride.driver', 'passenger'])->where('id', $id)->first();
        if (!$booking || !$booking->ride) {
            return redirect()->back()->with(['failure' => 'Booking not found']);
        }

        $originalSeats = (int) $booking->seats;
        $cancelSeats = (int) $request->seats;
        if ($cancelSeats <= 0 || $cancelSeats > $booking->seats) {
            return redirect()->back()->with(['failure' => 'Invalid number of seats to cancel.']);
        }

        $ride = $booking->ride;

        $cancellationService = app(BookingCancellationService::class);
        $result = $cancellationService->cancelPassengerBookingWebFlow($booking, $ride, $cancelSeats, $user->id, $getSetting);
        $booking = $result['booking'];
        $payoutAmt = $result['payoutAmt'];
        $originalSeats = $result['originalSeats'];

        $this->notifyDriverPassengerCancelledWebFlow(
            $booking,
            $ride,
            $user,
            (string) $request->input('message'),
            (int) $originalSeats,
            (int) $cancelSeats,
            (float) $payoutAmt
        );

        $messages = $this->successMessage;

        return redirect()->route('my_trips', ['lang' => $this->selectedLanguage->abbreviation])->with(['success' => $messages->cancel_booking_message ?? null]);
    }















    /**
     * Log Twilio SMS failure and add a hint when Twilio rejects the From/To combination
     * (e.g. sending from US number to unsupported destination).
     */
    protected function logTwilioSmsFailure(string $to, string $message, \Throwable $e, string $context = ''): void
    {
        $msgPreview = strlen($message) > 80 ? substr($message, 0, 80) . '...' : $message;
        Log::info('SMS failed to ' . $to . ($context ? " ({$context})" : '') . '. Message: ' . $msgPreview . ' because ' . $e->getMessage());
        if (str_contains($e->getMessage(), "current combination of 'To'") || str_contains($e->getMessage(), "combination of 'To'")) {
            Log::info('Twilio From/To hint: Enable the destination country in Twilio Console (Phone Numbers → Manage → Active Numbers → your number → Geographic permissions), or use a sending number that supports the recipient region.');
        }
    }



    protected function normalizeBookingPaymentAmounts(Request $request, Ride $ride): array
    {
        $bookingCredit = round((float) $request->input('booking_credit', 0), 2);
        $seatsAmount = round((float) $request->input('seats_amount', 0), 2);
        $taxAmount = round((float) $request->input('tax_amount', 0), 2);
        $total = round((float) $request->input('total', 0), 2);
        $coffeeWall = (string) $request->input('coffee_wall') === '1';
        $walletCovered = (string) $request->input('booked_by_wallet') === '1';
        $isCashRide = $ride->isCashPayment();

        $baseOnlinePayment = $isCashRide ? $bookingCredit : $total;
        if ($coffeeWall) {
            $baseOnlinePayment = $isCashRide
                ? 0.0
                : round(max(0, $total - $bookingCredit), 2);
        }

        $onlinePayment = $walletCovered
            ? 0.0
            : round((float) $request->input('online_payment', $baseOnlinePayment), 2);

        if (!$walletCovered) {
            $onlinePayment = $baseOnlinePayment;
        }

        return [
            'booking_credit' => $bookingCredit,
            'seats_amount' => $seatsAmount,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'online_payment' => $onlinePayment,
            'cash_payment' => $isCashRide ? $seatsAmount : 0.0,
            'wallet_charge' => $walletCovered ? $baseOnlinePayment : 0.0,
            'coffee_wall' => $coffeeWall,
            'booked_by_wallet' => $walletCovered,
            'is_cash_ride' => $isCashRide,
        ];
    }

    protected function loadRideForBooking(int $rideId, ?int $rideDetailId = null, $fromStopId = null, $toStopId = null): Ride
    {
        $ride = Ride::with([
            'rideStops' => fn($query) => $query->orderBy('stop_order'),
            'rideStopSegments',
            'detail'
        ])->findOrFail($rideId);

        $ride = $this->makeDetailOfRide($ride, $fromStopId, $toStopId);

        return $ride;
    }

    protected function resolveBookingRouteData(Ride $ride, $fromStopId = null, $toStopId = null): array
    {
        $ride->loadMissing([
            'rideStops' => fn($query) => $query->orderBy('stop_order'),
            'rideStopSegments',
            'detail'
        ]);

        $ride = $this->makeDetailOfRide($ride, $fromStopId, $toStopId);

        $resolvedFromStopId = (int) ($ride->matched_from_stop_id ?? $ride->rideStops->first()?->id ?? 0);
        $resolvedToStopId = (int) ($ride->matched_to_stop_id ?? $ride->rideStops->last()?->id ?? 0);

        $fromStop = $ride->rideStops->firstWhere('id', $resolvedFromStopId);
        $toStop = $ride->rideStops->firstWhere('id', $resolvedToStopId);

        $departure = (string) ($fromStop?->label ?? $ride->detail?->departure ?? $ride->departure ?? '');
        $destination = (string) ($toStop?->label ?? $ride->detail?->destination ?? $ride->destination ?? '');
        $price = (string) ((int) ($ride->matched_segment_price_minor ?? $ride->detail?->price ?? 0));

        // $matchedRideDetail = $ride->rideDetail->first(function ($detail) use ($departure, $destination) {
        //     return strcasecmp(trim((string) ($detail->departure ?? '')), trim($departure)) === 0
        //         && strcasecmp(trim((string) ($detail->destination ?? '')), trim($destination)) === 0;
        // });

        return [
            'from_stop_id' => $resolvedFromStopId ?: null,
            'to_stop_id' => $resolvedToStopId ?: null,
            'departure' => $departure,
            'destination' => $destination,
            'price' => $price,
            'ride_detail_id' => $ride->detail?->id,
        ];
    }

    protected function syncBookingRouteData(Booking $booking, Ride $ride, $fromStopId = null, $toStopId = null): Booking
    {
        $booking->update($this->resolveBookingRouteData($ride, $fromStopId, $toStopId));

        return $booking->refresh();
    }

    protected function findExistingBookingForSegment(int $rideId, int $userId, $fromStopId = null, $toStopId = null): ?Booking
    {
        return Booking::where('ride_id', $rideId)
            ->where('user_id', $userId)
            ->whereIn('status', [Booking::STATUS_REQUESTED, Booking::STATUS_BOOKED])
            ->where('from_stop_id', $fromStopId)
            ->where('to_stop_id', $toStopId)
            ->latest('id')
            ->first();
    }

    /**
     * it is not needed
     */
    public function edit($lang = null, $id) {}


    public function seatOnHold(Request $request)
    {
        $messages = $this->successMessage;
        $bookingPage = BookingPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $user = auth()->user();
        $seat = SeatDetail::where('id', $request->seat_id)->first();

        $result = app(SeatHoldService::class)->process($seat, $user);

        switch ($result['outcome']) {
            case SeatHoldService::OUTCOME_HELD:
                return response()->json([
                    'getSeatDetail' => $result['seat'],
                    'message' => 'Seat on hold successfully',
                ]);
            case SeatHoldService::OUTCOME_RELEASED:
                return response()->json([
                    'getSeatDetail' => $result['seat'],
                    'message' => $bookingPage->seat_hold_message ?? 'Your selected seat(s) will be held for 10 minutes. If the booking isn\'t completed within that time, the seat(s) will be released and made available to others.',
                ]);
            case SeatHoldService::OUTCOME_BOOKED:
                return response()->json(['message' => 'Seat booked please select another seat']);
            case SeatHoldService::OUTCOME_HELD_BY_OTHER:
                return response()->json(['message' => $messages->seat_hold_message ?? null]);
            case SeatHoldService::OUTCOME_NOT_FOUND:
            default:
                return response()->json(['message' => 'Seat not found']);
        }
    }

    public function noShowDriver(Request $request)
    {

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('arbitration_success_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('arbitration_success_message')->first();
            }
        }
        $booking = Booking::where('id', $request->booking_id)->first();

        $exist = NoShowHistory::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)
            ->where('user_id', $booking->ride->added_by)->where('type', 'driver')->first();

        if ($exist) {
            $data['message'] = 'Your response has already been submitted';
            return response()->json($data);
        }

        $response = NoShowHistory::create([
            'ride_id' => $booking->ride_id,
            'booking_id' => $booking->id,
            'user_id' => $booking->ride->added_by,
            'type' => 'driver',
        ]);

        // Revoke Extra Care eligibility when driver receives a no-show
        User::where('id', $booking->ride->added_by)->whereIn('folks_ride', ['1', ''])->update(['folks_ride' => '0']);

        $data['message'] = $successMessage->arbitration_success_message;
        return response()->json($data);
    }

    public function revertNoShowDriver(Request $request)
    {
        $booking = Booking::where('id', $request->booking_id)->first();

        $exist = NoShowHistory::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)
            ->where('user_id', $booking->ride->added_by)->where('type', 'driver')->first();
        if ($exist) {
            $exist->delete();
            $data['message'] = 'No show reverted successfully';
            $data = ['first_name' => $booking->ride->driver->first_name, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];
            Mail::to($booking->ride->driver->email)->queue(new ArbitrationCancelledMail($data));
            return response()->json($data);
        }
        $data['message'] = 'Something went wrong!';
        return response()->json($data);
    }

    public function noShowPassenger(Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('arbitration_success_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('arbitration_success_message')->first();
            }
        }
        $booking = Booking::where('id', $request->booking_id)->first();

        $exist = NoShowHistory::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)
            ->where('user_id', $booking->user_id)->where('type', 'passenger')->first();

        if ($exist) {
            $data['message'] = 'Your response has already been submitted';
            return response()->json($data);
        }

        $response = NoShowHistory::create([
            'ride_id' => $booking->ride_id,
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'type' => 'passenger',
        ]);

        $data['message'] =  $successMessage->arbitration_success_message;
        return response()->json($data);
    }

    public function revertNoShowPassenger(Request $request)
    {
        $booking = Booking::where('id', $request->booking_id)->first();

        $exist = NoShowHistory::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)
            ->where('user_id', $booking->user_id)->where('type', 'passenger')->first();
        if ($exist) {
            $exist->delete();
            $data['message'] = 'Your response has already been submitted';
            $data = ['first_name' => $booking->passenger->first_name, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];
            Mail::to($booking->passenger->email)->queue(new ArbitrationCancelledMail($data));
            return response()->json($data);
        }

        $data['message'] = 'Error occured';
        return response()->json($data);
    }


    /**
     * cancel booking by passenger
     * params: booking id
     */
    public function cancel($lang = null, $id)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login', ['lang' => $lang])->with('error', __('Please log in to cancel your booking.'));
        }


        $setting = SiteSetting::getCached();

        $booking = Booking::where('id', $id)->with('ride')->first();
        $ride = $booking->ride;

        $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
        $bookingDateTime = Carbon::parse($booking->booked_on);
        $hoursDifference = $rideDateTime->diffInHours($bookingDateTime);


        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $sureMessage = $tripsPage->cancel_booking_confirm_message ?? "Are you sure you want to cancel booking?";

        if ($ride->isFirmCancellation()) {
            $sureMessage = $tripsPage->cancel_booking_confirm_firm_message ?? "Are you sure you want to cancel booking?";
        } else {
            if ($hoursDifference > 48) {
                $sureMessage = $tripsPage->cancel_booking_confirm_48_hour_message ?? "Are you sure you want to cancel booking?";
            } else if ($hoursDifference >= 12 && $hoursDifference <= 48) {
                $sureMessage = $tripsPage->cancel_booking_confirm_12_to_48_hour_message ?? "Are you sure you want to cancel booking?";
            } else if ($hoursDifference < 12) {
                $sureMessage = $tripsPage->cancel_booking_confirm_less_12_hour_message ?? "Are you sure you want to cancel booking?";
            }
        }

        return view('cancel_booking', [
            'booking' => $booking,
            'ride' => $ride,
            'setting' => $setting,
            'tripsPage' => $tripsPage,
            'sureMessage' => $sureMessage
        ]);
    }





    public function sendVerificationCodeBooking($id)
    {
        $phoneNumber = PhoneNumber::find($id);

        $existingRecord = DB::table('phone_verifications')
            ->where('phone_number_id', $phoneNumber->id)
            ->first();

        if ($existingRecord) {
            $existingRecord = DB::table('phone_verifications')
                ->where('phone_number_id', $phoneNumber->id)
                ->delete();
        }

        $verificationCode = rand(1000, 9999);

        // Save verification code and its expiration time (30 minutes) to the database
        DB::table('phone_verifications')->insert([
            'phone_number_id' => $phoneNumber->id,
            'verification_code' => $verificationCode,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        // Send the verification code via Twilio
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        $twilio = new Client($sid, $token);
        $to = $phoneNumber->phone;
        $message = "ProximaRide: Your verification code is: $verificationCode. This code will expire in 30 minutes.";

        try {
            if (env('APP_ENV') != 'local') {
                $res = $twilio->messages->create(
                    $to,
                    [
                        'from' => $from,
                        'body' => $message,
                    ]
                );
            }
        } catch (\Exception  $e) {
            $this->logTwilioSmsFailure($to, $message, $e);

            // return redirect()->back()->with(['error' => 'Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate']);
        }

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        // return redirect()->route('phone_code', ['lang' => $this->selectedLanguage->abbreviation]);
        return redirect()->back()->with([
            'lang' => $this->selectedLanguage->abbreviation,
            'phone_code' => 'Language changed successfully!' // or any success message
        ]);
    }

    public function successTransactionBookingRequest($id, $type, $seats, $seats_amount, $booking_credit, $online_payment, $cash_payment, $total, $seats_id, $coffee_wall, $transactionTaxSum, $ride, $tax_amount, $tax_percentage, $tax_type, $deduct_tax, Request $request)
    {

        $taxAmt = $tax_amount;
        $paypal = new PayPalClient;
        $paypal->setApiCredentials(config('paypal'));
        $token = $paypal->getAccessToken();
        $paypal->setAccessToken($token);

        $result = $paypal->capturePaymentOrder($request->get('token'));

        if ($result['status'] == 'COMPLETED') {
            $ride = $this->loadRideForBooking(
                $id,
                $request->input('from_stop_id'),
                $request->input('to_stop_id')
            );
            $user = User::where('id', auth()->user()->id)->first();
            $bookingRouteData = $this->resolveBookingRouteData(
                $ride,
                $request->input('from_stop_id'),
                $request->input('to_stop_id')
            );

            // Student booking fee waiver: Validate and apply waiver with card expiration check
            $booking_credit = $this->validateStudentBookingFee($user, $booking_credit);

            // Calculate expiry time based on ride date and time
            $currentTime = now();
            $Time = now();
            $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
            $difference = $rideDateTime->diffInHours($currentTime);

            if ($difference > 48) {
                $expiryTime = $Time->addHours(12);
            } elseif ($difference >= 24 && $difference <= 48) {
                $expiryTime = $Time->addHours(6);
            } elseif ($difference >= 6 && $difference < 24) {
                $expiryTime = $Time->addHours(2);
            } else {
                $expiryTime = $Time->addMinutes(30);
            }

            $message = $this->successMessage;

            //Booking Method
            $secured_cash = null;
            $secured_cash_code = null;


            // Payment successful, handle booking logic here.
            // If the user already has a booking for this ride, REUSE it and ADD new values to old ones
            // (PayPal flow does booking creation here, so this is where we must apply the "add to old" behavior).
            $existingBooking = $this->findExistingBookingForSegment(
                $id,
                $user->id,
                $bookingRouteData['from_stop_id'],
                $bookingRouteData['to_stop_id']
            );

            if ($existingBooking) {
                $booking = $existingBooking;
                $booking->update([
                    'seats' => (int) ($booking->seats ?? 0) + (int) $seats,
                    'booking_credit' => (float) ($booking->booking_credit ?? 0) + (float) $booking_credit,
                    'fare' => (float) ($booking->fare ?? 0) + (float) $seats_amount,
                    'tax_amount' => (float) ($booking->tax_amount ?? 0) + (float) $taxAmt,

                    // Keep booking metadata fresh for this new request batch
                    'booked_on' => $currentTime,
                    'expires_at' => $expiryTime,
                    'type' => $type,
                    'ride_detail_id' => $ride->detail->id,
                    'departure' => $ride->detail->departure,
                    'destination' => $ride->detail->destination,
                    'price' => $ride->detail->price,
                ]);
                $booking = $this->syncBookingRouteData(
                    $booking,
                    $ride,
                    $bookingRouteData['from_stop_id'],
                    $bookingRouteData['to_stop_id']
                );
            } else {
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'ride_id' => $id,
                    'seats' => $seats,
                    'type' => $type,
                    'booked_on' => $currentTime,
                    'booking_credit' => $booking_credit,
                    'fare' => $seats_amount,
                    'secured_cash' => $secured_cash,
                    'tax_amount' => $taxAmt,
                    'secured_cash_code' => $secured_cash_code,
                    'expires_at' => $expiryTime,
                    'departure' => $ride->detail->departure,
                    'destination' => $ride->detail->destination,
                    'price' => $ride->detail->price,
                    'ride_detail_id' => $ride->detail->id
                ]);
                $booking = $this->syncBookingRouteData(
                    $booking,
                    $ride,
                    $bookingRouteData['from_stop_id'],
                    $bookingRouteData['to_stop_id']
                );
            }



            $seats_id_array = explode(',', $seats_id);
            $getSeatDetails = SeatDetail::whereIn('id', $seats_id_array)->get();
            if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                foreach ($getSeatDetails as $key => $getSeatDetail) {
                    $getSeatDetail->status = 'booked';
                    $getSeatDetail->booking_id = $booking->id;
                    $getSeatDetail->user_id = $booking->user_id;
                    $getSeatDetail->save();
                }
            }

            $captureId = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            $onlinePayment = $online_payment;
            if (isset($coffee_wall) && $coffee_wall == "1") {
                $onlinePayment = $online_payment + $booking_credit;
            }

            if ($request->cash_payment > 0) {
                $onlinePayment = $onlinePayment;
            } else {
                $onlinePayment = $onlinePayment - $taxAmt;
            }

            $transaction = Transaction::create([
                'booking_id' => $booking->id,
                'type' => '1',
                'booking_fee' => $booking_credit,
                'price' => $onlinePayment,
                'paypal_id' => $captureId,
                'coffee_from_wall' => isset($coffee_wall) && $coffee_wall == "1" ? true : false,
                'tax_amount' => $taxAmt,
                'tax_percentage' => $tax_percentage,
                'tax_type' => $tax_type,
                'deduct_type' => $deduct_tax,
            ]);

            if (isset($coffee_wall) && $coffee_wall == "1") {
                $coffeeWallet = CoffeeWallet::create([
                    'booking_id' => $booking->id,
                    'ride_id' => $ride->id,
                    'user_id' => $booking->user_id,
                    'cr_amount' => $booking_credit,
                ]);
            }

            Notification::create([
                'ride_id' => $id,
                'posted_by' => $user->id,
                'message' =>  $seats . ' seats needed',
                'status' => 'request',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);
            if (isset($user->email_notification) && $user->email_notification == 1) {

                $price = $seats_amount / $seats;
                $data = ['first_name' => $ride->driver->first_name, 'id' => $booking->id, 'lang' => $this->selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $price, 'total_price' => $seats_amount, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
                // Send booking request email
                Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
            }




            if (isset($user->email_notification) && $user->email_notification == 1) {
                $data = ['first_name' => $user->first_name];
                Mail::to($user->email)->queue(new BookingRequestConfirmationMail($data));


                // $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $seats_amount, 'booking_credit' => $booking->booking_credit, 'online_payment' => $online_payment, 'cash_payment' => $cash_payment, 'total' => $total];
                $data = [
                    'first_name' => $user->first_name,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'amount' => $total,
                    'transaction_id' => $card->random_id ?? 'N/A',
                    'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                    'payment_method' => 'paypal',
                    'paypal_email' => $user->paypal_email ?? 'N/A',
                    'seats' => $booking->seats,
                    'seats_amount' => $seats_amount,
                    'booking_credit' => $booking->booking_credit,
                    'online_payment' => $online_payment,
                    'cash_payment' => $cash_payment,
                    'total' => $total
                ];
                Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
            }

            $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

            if (!$phoneNumber) {
                $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
            }

            if ($phoneNumber && env('APP_ENV') != 'local' && isset($ride->driver->sms_notification) && $ride->driver->sms_notification == 1) {
                // Send the secured cash code via Twilio
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_PHONE_NUMBER');

                $twilio = new Client($sid, $token);
                $to = $phoneNumber->phone;

                $title = "";
                $currentHour = date('H');
                if ($currentHour >= 0 && $currentHour < 12) {
                    $title = "Good morning " . $ride->driver->first_name . ",";
                } elseif ($currentHour >= 12 && $currentHour < 17) {
                    $title = "Good afternoon " . $ride->driver->first_name . ",";
                } else {
                    $title = "Good evening " . $ride->driver->first_name . ",";
                }

                $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));

                $depatureDate = date('F d, Y H:i', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                $message = $title . "\n" . "From ProximaRide: You have a new booking request from (" . $user->first_name . ")\n"
                    . "\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . "\n" . $user->first_name . ": " . $user->phone . "\nNumber of seats: " . $booking->seats . "\nClick here for accept(" . url("/accept/" . $booking->id) . ")\nClick here for reject(" . url("/reject/" . $booking->id) . ")";

                try {
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                } catch (\Exception  $e) {
                    $this->logTwilioSmsFailure($to, $message, $e);

                    // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                }
            }

            return redirect()->route('my_trips', ['lang' => $this->selectedLanguage->abbreviation])->with(['success' => $messages->booking_request_success_message ?? 'Your request has been successfully sent to the driver']);
        }

        return redirect()
            ->route('home')
            ->with('message', 'Transaction failed.');
    }

    public function updateSuccessTransactionBookingRequest($id, $type, $seats, $seats_amount, $booking_credit, $online_payment, $cash_payment, $total, $seats_id, $coffee_wall, $transactionTaxSum, $ride, $tax_amount, $tax_percentage, $tax_type, $deduct_tax, Request $request)
    {

        $taxAmt = $tax_amount;
        $paypal = new PayPalClient;
        $paypal->setApiCredentials(config('paypal'));
        $token = $paypal->getAccessToken();
        $paypal->setAccessToken($token);

        $result = $paypal->capturePaymentOrder($request->get('token'));

        if ($result['status'] == 'COMPLETED') {
            $booking = Booking::where('id', $id)->first();
            $ride = $this->loadRideForBooking(
                $booking->ride_id,
                $booking->from_stop_id,
                $booking->to_stop_id
            );
            $user = User::where('id', auth()->user()->id)->first();
            $bookingRouteData = $this->resolveBookingRouteData(
                $ride,
                $booking->from_stop_id,
                $booking->to_stop_id
            );

            // Student booking fee waiver: Validate and apply waiver with card expiration check
            $booking_credit = $this->validateStudentBookingFee($user, $booking_credit);

            // Calculate expiry time based on ride date and time
            $currentTime = now();
            $Time = now();
            $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
            $difference = $rideDateTime->diffInHours($currentTime);

            if ($difference > 48) {
                $expiryTime = $Time->addHours(12);
            } elseif ($difference >= 24 && $difference <= 48) {
                $expiryTime = $Time->addHours(6);
            } elseif ($difference >= 6 && $difference < 24) {
                $expiryTime = $Time->addHours(2);
            } else {
                $expiryTime = $Time->addMinutes(30);
            }

            $selectedLanguage = session('selectedLanguage');
            $messages = null;
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
                if ($selectedLanguage) {
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_request_success_message')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_request_success_message')->first();
                }
            }

            //Booking Method
            $secured_cash = null;
            $secured_cash_code = null;

            $transactions = Transaction::where('booking_id', $booking->id)->get();

            $captureId = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            if ($booking->status === '0') {
                // Payment successful, handle booking logic here
                $newBooking = Booking::create([
                    'user_id' => $user->id,
                    'ride_id' => $ride->id,
                    'seats' => $seats,
                    'type' => $type,
                    'booked_on' => $currentTime,
                    'booking_credit' => $booking_credit,
                    'fare' => $seats_amount,
                    'secured_cash' => $secured_cash,
                    'tax_amount' => $taxAmt,
                    'secured_cash_code' => $secured_cash_code,
                    'expires_at' => $expiryTime,
                    'departure' => $ride->detail->departure,
                    'destination' => $ride->detail->destination,
                    'price' => $ride->detail->price,
                    'ride_detail_id' => $ride->detail->id
                ]);
                $newBooking = $this->syncBookingRouteData(
                    $newBooking,
                    $ride,
                    $booking->from_stop_id,
                    $booking->to_stop_id
                );

                $seats_id_array = explode(',', $seats_id);
                $getSeatDetails = SeatDetail::whereIn('id', $seats_id_array)->get();
                if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                    foreach ($getSeatDetails as $key => $getSeatDetail) {
                        $getSeatDetail->status = 'booked';
                        $getSeatDetail->booking_id = $newBooking->id;
                        $getSeatDetail->user_id = $newBooking->user_id;
                        $getSeatDetail->save();
                    }
                }

                foreach ($transactions as $transaction) {
                    $transaction->update([
                        'booking_id' => $newBooking->id,
                    ]);
                }

                $booking->delete();

                $getBookingFeeSum = Transaction::where('booking_id', $newBooking->id)->sum('booking_fee');
                $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);

                $transactionTotalPrice = Transaction::where('booking_id', $newBooking->id)->where('parent_id', '0')->sum('price');
                $transactionBookingPrice = Transaction::where('booking_id', $newBooking->id)->where('parent_id', '0')->sum('booking_fee');

                $transactionPrice = $transactionTotalPrice;
                if (isset($coffee_wall) && $coffee_wall == "1") {
                    $transactionPrice = $transactionTotalPrice - $transactionBookingPrice;
                }

                if ($ride->payment_method == "33") {

                    $payable_amount = ($request->online_payment + $taxAmt) - $transactionPrice;
                } else {

                    $payable_amount = $request->online_payment - $transactionPrice;
                }


                $onlinePayment = $payable_amount;
                if (isset($coffee_wall) && $coffee_wall == "1") {
                    $onlinePayment = $payable_amount + $currentBookingFee;
                }

                $transactionTaxAmt = $taxAmt - $transactionTaxSum;

                $currentTransactionAmt = $onlinePayment - $transactionTaxAmt;

                $transaction = Transaction::create([
                    'booking_id' => $newBooking->id,
                    'type' => '1',
                    'booking_fee' => $currentBookingFee,
                    'price' => $currentTransactionAmt,
                    'paypal_id' => $captureId,
                    'coffee_from_wall' => isset($coffee_wall) && $coffee_wall == "1" ? true : false,
                    'tax_amount' => $transactionTaxAmt,
                    'tax_percentage' => $tax_percentage,
                    'tax_type' => $tax_type,
                    'deduct_type' => $deduct_tax,
                ]);

                if (isset($coffee_wall) && $coffee_wall == "1") {
                    $coffeeWallet = CoffeeWallet::create([
                        'booking_id' => $newBooking->id,
                        'ride_id' => $ride->id,
                        'user_id' => $newBooking->user_id,
                        'cr_amount' => $currentBookingFee,
                    ]);
                }

                Notification::create([
                    'ride_id' => $ride->id,
                    'posted_by' => $user->id,
                    'message' => getNotificationMessageText(
                        'booking_request_from_name',
                        $ride->driver,
                        ['first_name' => $user->first_name],
                        'Booking request from {first_name}'
                    ),
                    'status' => 'request',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $newBooking->ride_detail_id,
                    'departure' => $newBooking->departure,
                    'destination' => $newBooking->destination
                ]);
                if (isset($user->email_notification) && $user->email_notification == 1) {

                    $price = $seats_amount / $seats;
                    $data = ['first_name' => $ride->driver->first_name, 'id' => $newBooking->id, 'lang' => $this->selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $newBooking->seats, 'booking_price' => $price, 'total_price' => $seats_amount, 'from' => $newBooking->departure, 'to' => $newBooking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
                    // Send booking request email
                    Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
                }


                if (isset($user->email_notification) && $user->email_notification == 1) {
                    $data = ['first_name' => $user->first_name];
                    Mail::to($user->email)->queue(new BookingRequestConfirmationMail($data));


                    $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                        ->where('default', '1')
                        ->first();

                    $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;


                    $data = ['first_name' => $user->first_name, 'seats' => $newBooking->seats, 'seats_amount' => $seats_amount, 'booking_credit' => $newBooking->booking_credit, 'online_payment' => $online_payment, 'cash_payment' => $cash_payment, 'total' => $total];
                    Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
                }
            } elseif ($booking->status === '1') {
                $booking_credit = $booking_credit - $booking->booking_credit;
                $seats = $seats - $booking->seats;
                $seats_amount = $seats_amount - $booking->fare;
                $newBooking = Booking::create([
                    'user_id' => $user->id,
                    'ride_id' => $ride->id,
                    'seats' => $seats,
                    'type' => $type,
                    'booked_on' => $currentTime,
                    'booking_credit' => $booking_credit,
                    'fare' => $seats_amount,
                    'secured_cash' => $secured_cash,
                    'secured_cash_code' => $secured_cash_code,
                    'expires_at' => $expiryTime,
                    'departure' => $ride->detail->departure,
                    'destination' => $ride->detail->destination,
                    'price' => $ride->detail->price,
                    'ride_detail_id' => $ride->detail->id
                ]);
                $newBooking = $this->syncBookingRouteData(
                    $newBooking,
                    $ride,
                    $booking->from_stop_id,
                    $booking->to_stop_id
                );

                $transactionTotalPrice = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('price');
                $transactionBookingPrice = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('booking_fee');

                $transactionPrice = $transactionTotalPrice;
                if (isset($coffee_wall) && $coffee_wall == "1") {
                    $transactionPrice = $transactionTotalPrice - $transactionBookingPrice;
                }

                $payable_amount = $online_payment - $transactionPrice;

                $onlinePayment = $payable_amount;
                if (isset($coffee_wall) && $coffee_wall == "1") {
                    $onlinePayment = $payable_amount + $booking_credit;
                }

                $transaction = Transaction::create([
                    'booking_id' => $newBooking->id,
                    'type' => '1',
                    'booking_fee' => $booking_credit,
                    'price' => $onlinePayment,
                    'paypal_id' => $captureId,
                    'coffee_from_wall' => isset($coffee_wall) && $coffee_wall == "1" ? true : false
                ]);

                if (isset($coffee_wall) && $coffee_wall == "1") {
                    $coffeeWallet = CoffeeWallet::create([
                        'booking_id' => $newBooking->id,
                        'ride_id' => $ride->id,
                        'user_id' => $newBooking->user_id,
                        'cr_amount' => $booking_credit,
                    ]);
                }

                Notification::create([
                    'ride_id' => $ride->id,
                    'posted_by' => $user->id,
                    'message' => getNotificationMessageText(
                        'booking_request_from_name',
                        $ride->driver,
                        ['first_name' => $user->first_name],
                        'Booking request from {first_name}'
                    ),
                    'status' => 'request',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $newBooking->ride_detail_id,
                    'departure' => $newBooking->departure,
                    'destination' => $newBooking->destination
                ]);
                if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                    $price = $newBooking->fare / $newBooking->seats;
                    $data = ['first_name' => $ride->driver->first_name, 'id' => $newBooking->id, 'lang' => $this->selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $newBooking->seats, 'booking_price' => $price, 'total_price' => $seats_amount, 'from' => $newBooking->departure, 'to' => $newBooking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
                    // Send booking request email
                    Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
                }


                if (isset($user->email_notification) && $user->email_notification == 1) {
                    $data = ['first_name' => $user->first_name];
                    Mail::to($user->email)->queue(new BookingRequestConfirmationMail($data));


                    $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                        ->where('default', '1')
                        ->first();

                    $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;


                    $data = ['first_name' => $user->first_name, 'seats' => $newBooking->seats, 'seats_amount' => $seats_amount, 'booking_credit' => $newBooking->booking_credit, 'online_payment' => $online_payment, 'cash_payment' => $cash_payment, 'total' => $total];
                    Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
                }
            }

            $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

            if (!$phoneNumber) {
                $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
            }

            if ($phoneNumber && env('APP_ENV') != 'local' && isset($ride->driver->sms_notification) && $ride->driver->sms_notification == 1) {
                // Send the secured cash code via Twilio
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_PHONE_NUMBER');

                $twilio = new Client($sid, $token);
                $to = $phoneNumber->phone;

                $title = "";
                $currentHour = date('H');
                if ($currentHour >= 0 && $currentHour < 12) {
                    $title = "Good morning " . $ride->driver->first_name . ",";
                } elseif ($currentHour >= 12 && $currentHour < 17) {
                    $title = "Good afternoon " . $ride->driver->first_name . ",";
                } else {
                    $title = "Good evening " . $ride->driver->first_name . ",";
                }

                $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));

                $depatureDate = date('F d, Y H:i', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                $message = $title . "\n"
                    . "From ProximaRide: You have a new booking request from (" . $user->first_name . ")\n"
                    . "\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . "\n" . $user->first_name . ": " . $user->phone . "\nNumber of seats: " . $booking->seats . "\nClick here for accept(" . url("/accept/" . $booking->id) . ")\nClick here for reject(" . url("/reject/" . $booking->id) . ")";

                try {
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                } catch (\Exception  $e) {
                    $this->logTwilioSmsFailure($to, $message, $e);

                    // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                }
            }

            return redirect()->route('my_trips', ['lang' => $this->selectedLanguage->abbreviation])->with(['success' => $messages->booking_request_success_message ?? 'Your request has been successfully sent to the driver']);
        }

        return redirect()
            ->route('home')
            ->with('message', 'Transaction failed.');
    }

    /**
     * Approve request booking
     * run job to send notification
     */
    public function AcceptBookingRequest($lang = null, $id, $email)
    {

        $user = auth()->user();
        $booking = Booking::with(['passenger.primaryPhone', 'ride.driver.primaryPhone', 'ride'])
            ->whereId($id)
            ->first();

        if ($booking && $booking->isRequested()) {
            
            $booking->update([
                'status' => '1',
                'expires_at' => null,
            ]);
            // go to job as background
            $this->notifyBookingRequestApprovedWebFlow($booking, $user);

            return redirect()->route('my_ride_detail', ['lang' => $this->selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id])
                ->with('approve_success_message', "You've successfully approved the booking request. You can view the passenger's details by visiting the ride page. Please remember to follow all road safety rules and adhere to ProximaRide's community guidelines. Wishing you a smooth and safe ride!");
        } else {
            return redirect()->route('my_rides', ['lang' => app()->getLocale()])
                ->with('error', __('Request expired'));
        }
    }

    /**
     * Decline a pending booking request: persist refunds/seats via {@see BookingRequestRejectService},
     * then queue passenger notifications ({@see NotifyBookingRequestRejectedJob}).
     */
    public function RejectBookingRequest($lang = null, $id, $email)
    {
        if (!auth()->user()) {
            $loginUser = User::where('email', $email)->first();
            if ($loginUser) {
                auth()->login($loginUser);
            }
        }

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('my_rides', ['lang' => app()->getLocale()])
                ->with('error', __('Request expired'));
        }

        $booking = Booking::with(['passenger', 'ride'])->whereId($id)->first();

        if (!$booking || !$booking->isRequested()) {
            return redirect()->route('my_rides', ['lang' => app()->getLocale()])
                ->with('error', __('Request expired'));
        }

        $reject = app(BookingRequestRejectService::class)->rejectWeb($booking);
        if (!$reject['ok']) {
            return redirect()->back()->with(['error' => $reject['stripe_error']]);
        }

        $this->notifyBookingRequestRejectedWebFlow($booking, $user, 'web');

        return redirect()->route('my_ride_detail', ['lang' => app()->getLocale(), 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id])
            ->with('decline_success_message', 'You have declined the booking request. The seats are now available for other passengers to book.');
    }



    public function successTransaction($id, $type, $seats, $seats_amount, $booking_credit, $fare, $online_payment, $cash_payment, $total, $seats_id, $coffee_wall, $transactionTaxSum, $ride, $tax_amount, $tax_percentage, $tax_type, $deduct_tax, Request $request)
    {

        $taxAmt = $tax_amount;
        $paypal = new PayPalClient;
        $paypal->setApiCredentials(config('paypal'));
        $token = $paypal->getAccessToken();
        $paypal->setAccessToken($token);

        $result = $paypal->capturePaymentOrder($request->get('token'));

        if ($result['status'] == 'COMPLETED') {
            $ride = $this->loadRideForBooking(
                $id,
                $request->input('from_stop_id'),
                $request->input('to_stop_id')
            );
            $user = User::where('id', auth()->user()->id)->first();
            $bookingRouteData = $this->resolveBookingRouteData(
                $ride,
                $request->input('from_stop_id'),
                $request->input('to_stop_id')
            );

            // Student booking fee waiver: Validate and apply waiver with card expiration check
            $booking_credit = $this->validateStudentBookingFee($user, $booking_credit);

            $selectedLanguage = session('selectedLanguage');
            $findRidePage = null;
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
                if ($selectedLanguage) {
                    $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                }
            }

            if ($ride->payment_method == "35" && $ride->booking_method == "31") {
                $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->where('default', '1')->first();
                if (!$phoneNumber) {
                    $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
                }
                if (!$phoneNumber) {
                    return redirect()->route('search_ride', ['lang' => $this->selectedLanguage->abbreviation, 'from' => $ride->detail->departure, 'to' => $ride->detail->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $messages->verified_number_message ?? null]);
                }

                $secured_cash = '1';
                $secured_cash_code = rand(1000, 9999);

                $this->sendSmsCode($phoneNumber, $user, $secured_cash_code);
                // $this->sendSecuredCashPaymentCodeSms($phoneNumber, $user, $ride, $secured_cash_code, (int) $request->seats);
            } else {
                $secured_cash = null;
                $secured_cash_code = null;
            }

            // Payment successful, handle booking logic here.
            // Reuse the active booking for the same ride segment if one already exists.
            $existingBooking = $this->findExistingBookingForSegment(
                $id,
                $user->id,
                $bookingRouteData['from_stop_id'],
                $bookingRouteData['to_stop_id']
            );

            if ($existingBooking) {
                $booking = $existingBooking;
                $booking->update([
                    'seats' => (int) ($booking->seats ?? 0) + (int) $seats,
                    'booking_credit' => (float) ($booking->booking_credit ?? 0) + (float) $booking_credit,
                    'fare' => (float) ($booking->fare ?? 0) + (float) $fare,
                    'tax_amount' => (float) ($booking->tax_amount ?? 0) + (float) $taxAmt,
                    'type' => $type,
                    'status' => Booking::STATUS_BOOKED,
                    'booked_on' => Carbon::now(),
                    'secured_cash' => $secured_cash,
                    'secured_cash_code' => $secured_cash_code,
                    'departure' => $ride->detail->departure,
                    'destination' => $ride->detail->destination,
                    'price' => $ride->detail->price,
                    'ride_detail_id' => $ride->detail->id,
                ]);
            } else {
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'ride_id' => $id,
                    'seats' => $seats,
                    'type' => $type,
                    'booked_on' => Carbon::now(),
                    'status' => '1',
                    'booking_credit' => $booking_credit,
                    'fare' => $fare,
                    'tax_amount' => $taxAmt,
                    'secured_cash' => $secured_cash,
                    'secured_cash_code' => $secured_cash_code,
                    'departure' => $ride->detail->departure,
                    'destination' => $ride->detail->destination,
                    'price' => $ride->detail->price,
                    'ride_detail_id' => $ride->detail->id
                ]);
            }
            $booking = $this->syncBookingRouteData(
                $booking,
                $ride,
                $bookingRouteData['from_stop_id'],
                $bookingRouteData['to_stop_id']
            );

            if ($secured_cash_code && isset($user->email_notification) && $user->email_notification == 1) {
                $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                    ->where('default', '1')
                    ->first();
                $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;

                $emailData = [
                    'first_name' => $user->first_name,
                    'secured_cash_code' => $secured_cash_code,
                    'driver_first_name' => $ride->driver->first_name,
                    'driver_last_name' => $ride->driver->last_name,
                    'driver_phone' => $driverPhoneToUse,
                    'driver_email' => $ride->driver->email,
                    'departure' => $ride->detail->departure,
                    'destination' => $ride->detail->destination,
                    'date' => Carbon::parse($ride->date)->format('F d, Y'),
                    'time' => $ride->time,
                    'seats' => $request->seats,
                    'booking_price' => $ride->detail->price * $request->seats
                ];

                Mail::to($user->email)->queue(new SecuredCashPaymentCodeMail($emailData));
                $notificationMessage = "Your Secured-cash payment code is: " . $secured_cash_code;
                $securedCashNotification = Notification::create([
                    'type' => 2,
                    'ride_id' => $booking->ride_id,
                    'posted_to' => $booking->id ?? null,
                    'posted_by' => $booking->ride->added_by,
                    'receiver_id' => $booking->user_id,
                    'message' => getNotificationMessageText(
                        'secured_cash_payment_code',
                        $booking->passenger,
                        ['code' => $secured_cash_code],
                        'Your Secured-cash payment code is: {code}'
                    ),
                    'status' => 'completed',
                    'notification_type' => 'secured_cash',
                    'ride_detail_id' => $ride->detail->id,
                    'departure' => $ride->detail->departure,
                    'destination' => $ride->detail->destination
                ]);

                // Send push notification
                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
                $body = $securedCashNotification->message;

                $fcmToken = $user->mobile_fcm_token;
                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $body);
                }

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: $fcm_token->token, Error: " . $e->getMessage());
                    }
                }
            }

            $seats_id_array = explode(',', $seats_id);
            $getSeatDetails = SeatDetail::whereIn('id', $seats_id_array)->get();
            if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                foreach ($getSeatDetails as $key => $getSeatDetail) {
                    $getSeatDetail->status = 'booked';
                    $getSeatDetail->booking_id = $booking->id;
                    $getSeatDetail->user_id = $booking->user_id;
                    $getSeatDetail->save();
                }
            }

            $captureId = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            $onlinePayment = $online_payment;
            if (isset($coffee_wall) && $coffee_wall == "1") {
                $onlinePayment = $online_payment + $booking_credit;
            }


            if ($request->cash_payment > 0) {
                $onlinePayment = $onlinePayment;
            } else {
                $onlinePayment = $onlinePayment - $taxAmt;
            }

            $transaction = Transaction::create([
                'booking_id' => $booking->id,
                'type' => '1',
                'booking_fee' => $booking_credit,
                'price' => $onlinePayment,
                'paypal_id' => $captureId,
                'coffee_from_wall' => isset($coffee_wall) && $coffee_wall == "1" ? true : false,
                'tax_amount' => $taxAmt,
                'tax_percentage' => $tax_percentage,
                'tax_type' => $tax_type,
                'deduct_type' => $deduct_tax,
            ]);

            if (isset($coffee_wall) && $coffee_wall == "1") {
                $coffeeWallet = CoffeeWallet::create([
                    'booking_id' => $booking->id,
                    'ride_id' => $ride->id,
                    'user_id' => $booking->user_id,
                    'cr_amount' => $booking_credit,
                ]);
            }

            Notification::create([
                'ride_id' => $id,
                'posted_by' => $user->id,
                'message' =>  $seats . ' seats booked',
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            Notification::create([
                'type' => 2,
                'ride_id' => $id,
                'posted_to' => $booking->id,
                'posted_by' => $ride->added_by,
                'message' =>  $seats . ' booked successfully',
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            $bookingPrice = $booking->price * $booking->seats;

            // $data = ['first_name' => $ride->driver->first_name, 'passenger_first_name' => $user->first_name,'secured_cash_code' => $secured_cash_code];
            // Mail::to($ride->driver->email)->queue(new InstantBookingMail($data));
            if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                $data = ['first_name' => $ride->driver->first_name, 'lang' => $this->selectedLanguage->abbreviation, 'origin' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'time' => $ride->time, 'seats' => $booking->seats, 'booking_price' => $booking->price, 'total_price' => $bookingPrice, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'email' => $user->email, 'phone' => $user->phone];
                Mail::to($ride->driver->email)->queue(new PassengerDetailsMail($data));
            }
            if (isset($user->email_notification) && $user->email_notification == 1) {

                $data = ['first_name' => $user->first_name, 'driver_first_name' => $ride->driver->first_name, 'driver_last_name' => $ride->driver->last_name, 'gender' => $ride->driver->gender, 'email' => $ride->driver->email, 'phone' => $ride->driver->phone, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
                Mail::to($user->email)->queue(new DriverDetailsMail($data));

                $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $seats_amount, 'booking_credit' => $booking_credit, 'online_payment' => $online_payment, 'cash_payment' => $cash_payment, 'total' => $total];
                Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
            }
            $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

            if (!$phoneNumber) {
                $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
            }

            if ($phoneNumber && env('APP_ENV') != 'local' && isset($ride->driver->sms_notification) && $ride->driver->sms_notification == 1) {
                // Send the secured cash code via Twilio
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_PHONE_NUMBER');

                $twilio = new Client($sid, $token);
                $to = $phoneNumber->phone;

                $title = "";
                $currentHour = date('H');
                if ($currentHour >= 0 && $currentHour < 12) {
                    $title = "Good morning " . $ride->driver->first_name . ",";
                } elseif ($currentHour >= 12 && $currentHour < 17) {
                    $title = "Good afternoon " . $ride->driver->first_name . ",";
                } else {
                    $title = "Good evening " . $ride->driver->first_name . ",";
                }

                $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));

                $message = "" . $title . "\n" . $user->first_name . " has booked seat in your ride\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nPassenger phone number: " . $user->phone . "\n";

                try {
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                } catch (\Exception  $e) {
                    $this->logTwilioSmsFailure($to, $message, $e);

                    // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                }
            }

            $ride_time = strtotime($ride->time);
            $current_time = time();
            $current_date = date('Y-m-d');
            $time_left = $ride_time - $current_time;
            if ($current_date == date('Y-m-d', strtotime($ride->data)) && $time_left <= 3600) {
                $getBookings = Booking::with('passenger')
                    ->where('ride_id', $ride->id)
                    ->where('status', '!=', '3')
                    ->where('status', '!=', '0')
                    ->where('status', '!=', '4')
                    ->get();
                $messageContent = "";
                if (isset($getBookings) && count($getBookings) > 0) {
                    foreach ($getBookings as $key => $getBooking) {
                        if ($messageContent == "") {
                            $messageContent = "" . $getBooking->passenger->first_name . "(" . $getBooking->passenger->phone . ")";
                        } else {
                            $messageContent .= "\n" . $getBooking->passenger->first_name . "(" . $getBooking->passenger->phone . ")";
                        }
                    }
                    $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

                    if (!$phoneNumber) {
                        $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
                    }

                    if ($phoneNumber && env('APP_ENV') != 'local' && isset($ride->driver->sms_notification) && $ride->driver->sms_notification == 1) {
                        $sid = env('TWILIO_ACCOUNT_SID');
                        $token = env('TWILIO_AUTH_TOKEN');
                        $from = env('TWILIO_PHONE_NUMBER');

                        $twilio = new Client($sid, $token);
                        $to = $phoneNumber->phone;

                        $title = "";
                        $currentHour = date('H');
                        if ($currentHour >= 0 && $currentHour < 12) {
                            $title = "Good morning " . $ride->driver->first_name . ",";
                        } elseif ($currentHour >= 12 && $currentHour < 17) {
                            $title = "Good afternoon " . $ride->driver->first_name . ",";
                        } else {
                            $title = "Good evening " . $ride->driver->first_name . ",";
                        }

                        $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));

                        $message = "" . $title . "\nTrip detail\nOrigin: " . $booking->ride->departure . "\nDestination: " . $booking->ride->destination . "\nDeparture date: " . $depatureDate . "\nHere is your passengers’ list\n" . $messageContent . "";

                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                        } catch (\Exception  $e) {
                            $this->logTwilioSmsFailure($to, $message, $e);
                        }
                    }
                }
            }

            return redirect()->route('my_trips', ['lang' => $this->selectedLanguage->abbreviation])->with(['success' => 'You have successfully booked seat in this ride']);
        }

        return redirect()
            ->route('home')
            ->with('message', 'Transaction failed.');
    }

    public function cancelTransaction(Request $request)
    {
        return redirect()
            ->route('home')
            ->with('message', 'You have canceled the transaction.');
    }

    public function updateInstantBooking($id, Request $request)
    {

        //return $request;
        $booking = Booking::where('id', $id)->first();
        $ride = Ride::where('id', $booking->ride_id)->first();
        $user = User::where('id', auth()->user()->id)->first();

        $message = null;
        $taxAmt = isset($request->tax_amount) ? $request->tax_amount : 0;

        $message = $this->successMessage;
        $type = FeaturesSetting::whereId($ride->payment_method)->first();
        $phoneNumber = PhoneNumber::where('user_id', $user->id)->first();
        if (is_null($phoneNumber) && $type->slug == 'secured') {
            return redirect()->back()->with(['failure' => $messages->add_your_phone ?? null]);
        }
        $phoneVerification = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
        if (!$phoneVerification && $type->slug == 'secured') {
            // dd($messages->verified_number_message);
            return redirect()->back()->with(['failure' => $messages->verified_number_message ?? null, 'phone' => $phoneNumber]);
        }
        if ($user->block_booking == '1') {
            return redirect()->route('search_ride', ['lang' => $this->selectedLanguage->abbreviation, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $message->block_booking_message ?? null]);
        }

        $bookings = Booking::where('ride_id', $booking->ride_id)
            ->where('status', '!=', '3')
            ->where('status', '!=', '4')
            ->whereNotIn('id', [$id])
            ->get();
        $errorMsg = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();

        $seatsBooked = $bookings->sum('seats') + $request->seats;
        if ($seatsBooked > $ride->seats) {
            // return redirect()->route('search_ride', ['lang' => $this->selectedLanguage->abbreviation,'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => 'Oops, this seat is no longer available. Looks like another passenger has just booked it. We apologize for the inconvenience. Here are more rides for your route']);

            return redirect()->route('search_ride', ['lang' => $this->selectedLanguage->abbreviation, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $errorMsg->seat_unavailable_message]);
        }

        $transactionTotalPrice = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('price');
        $transactionBookingPrice = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('booking_fee');

        $transactionTaxSum = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('tax_amount');

        $transactionPrice = $transactionTotalPrice + $transactionTaxSum;
        if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
            $transactionPrice = $transactionTotalPrice - $transactionBookingPrice;
        }

        if ($request->seats > $booking->seats) {

            if ($ride->payment_method == "33") {

                $payable_amount = ($request->online_payment + $taxAmt) - $transactionPrice;
            } else {

                $payable_amount = $request->online_payment - $transactionPrice;
            }


            $rules = [
                'agree_terms' => 'accepted|required'
            ];

            if ($ride->booking_type == "37") {
                $rules['firm_agree_terms'] = 'accepted|required';
                $rules['firm_cancellation_understand'] = 'accepted|required';
            }

            $featuresArray = explode('=', $ride->features);
            if (in_array('1', $featuresArray)) {
                $rules['pink_ride_agree_terms'] = 'accepted|required';
            }
            if (in_array('2', $featuresArray)) {
                $rules['extra_care_ride_agree_terms'] = 'accepted|required';
            }

            $validated = $request->validate($rules);

            // Student booking limit for Cash rides: Limit students to 1-2 seats per ride if payment method is Cash
            $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
            if ($postRidePage) {
                // Check if user is a student (student == 1 for verified, student == 2 for pending)
                $isStudent = ($user->student == '1' || $user->student == '2');

                // Check if payment method is Cash (payment_methods_option1 is Cash)
                $isCashPayment = ($ride->payment_method == $postRidePage->payment_methods_option1);

                // Apply limit only for students on Cash rides
                if ($isStudent && $isCashPayment) {
                    if ($request->seats > 2) {
                        return redirect()->back()->with(['failure' => 'Students are limited to booking a maximum of 2 seats per ride for Cash payment rides.'])->withInput();
                    }
                }
            }

            // ProximaLocal: no booking fee on rides under $15 per seat
            $pricePerSeat = (float) ($ride->detail->price ?? 0);
            if ($pricePerSeat < 15) {
                $request->merge(['booking_credit' => '0']);
            }

            // Student booking fee waiver: Validate and apply waiver with card expiration check
            $adjustedBookingCredit = $this->validateStudentBookingFee($user, $request->booking_credit);
            $request->merge(['booking_credit' => $adjustedBookingCredit]);

            if ($payable_amount > 0) {
                $request->validate([
                    'payment_method' => 'required',
                    'card_id' => $request->payment_method == 'credit_card' && !isset($request->gPayApplePayId) && $request->gPayApplePayId == "" ? 'required' : 'nullable',
                    'booking_credit' => 'required|max:25',
                ]);

                if ($request->payment_method == 'paypal') {
                    $paypal = new PayPalClient;
                    $paypal->setApiCredentials(config('paypal'));
                    $token = $paypal->getAccessToken();
                    $paypal->setAccessToken($token);

                    $total = $request->booking_credit * $request->seats_amount;
                    $cash_payment = $total - $request->online_payment;

                    $paypalPay = $payable_amount;

                    $order = $paypal->createOrder([
                        "intent" => "CAPTURE",
                        "purchase_units" => [
                            [
                                "amount" => [
                                    "currency_code" => "CAD",
                                    "value" => number_format((float)$paypalPay, 2, '.', '')
                                ]
                            ]
                        ],
                        "application_context" => [
                            "cancel_url" => route('paypal.cancel'),
                            "return_url" => route('update-paypal.success', [
                                'id' => $booking->id,
                                'seats' => $request->seats,
                                'seats_amount' => $request->seats_amount,
                                'booking_credit' => $request->booking_credit,
                                'fare' => $request->seats_amount,
                                'online_payment' => $payable_amount,
                                'cash_payment' => $cash_payment,
                                'total' => $total,
                                'seats_id' => implode(',', $request->seats_id),
                                'coffee_wall' => isset($request->coffee_wall) ? $request->coffee_wall : '0',
                                'transactionTaxSum' => $transactionTaxSum,
                                'ride' => $ride,
                                'tax_amount' => isset($request->tax_amount) ? $request->tax_amount : 0,
                                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                                'deduct_tax' => isset($request->deduct_tax) ? $request->deduct_tax : NULL,
                                'from_stop_id' => $bookingRouteData['from_stop_id'],
                                'to_stop_id' => $bookingRouteData['to_stop_id'],

                            ]),
                        ]
                    ]);

                    if (isset($order['id'])) {
                        foreach ($order['links'] as $link) {
                            if ($link['rel'] == 'approve') {
                                return redirect()->away($link['href']);
                            }
                        }
                    }

                    return redirect()->route('paypal.cancel');
                } elseif ($request->payment_method == 'credit_card') {

                    $stripId = null;
                    try {
                        if (isset($request->gPayApplePayId) && $request->gPayApplePayId != '') {
                            $stripId = $request->gPayApplePayId;
                        } else {
                            $card = Card::where('id', $request->card_id)
                                ->where('user_id', $user->id)
                                ->firstOrFail();

                            // Set your Stripe API key.
                            Stripe::setApiKey(config('stripe.secret'));

                            $stripePay = $payable_amount;
                            // Attach the payment method to the customer
                            $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                            $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                            // Create a payment intent
                            $paymentIntent = PaymentIntent::create([
                                'amount' => round(($stripePay * 100), 0),
                                'currency' => 'cad',
                                'customer' => $user->stripe_customer_id,
                                'payment_method' => $paymentMethod->id,
                                'off_session' => true,
                                'confirm' => true,
                            ]);

                            $stripId = $paymentIntent->id;
                        }



                        $booking->update([
                            'seats' => $request->seats,
                            'booking_credit' => $request->booking_credit,
                            'fare' => $request->seats_amount,
                            'tax_amount' => $taxAmt,
                        ]);

                        $ids = $request->seats_id;
                        $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
                        if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                            foreach ($getSeatDetails as $key => $getSeatDetail) {
                                $getSeatDetail->status = 'booked';
                                $getSeatDetail->booking_id = $booking->id;
                                $getSeatDetail->user_id = $booking->user_id;
                                $getSeatDetail->save();
                            }
                        }

                        $getBookingFeeSum = Transaction::where('booking_id', $booking->id)->sum('booking_fee');
                        $currentBookingFee = $request->booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);

                        if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
                            $payable_amount = $payable_amount + $currentBookingFee;
                        }

                        $transactionTaxAmt = $taxAmt - $transactionTaxSum;

                        $currentTransactionAmt = $payable_amount - $transactionTaxAmt;

                        $newTransaction = Transaction::create([
                            'booking_id' => $booking->id,
                            'type' => '1',
                            'price' => $currentTransactionAmt,
                            'booking_fee' => $currentBookingFee,
                            'stripe_id' => $stripId,
                            'coffee_from_wall' => isset($request->coffee_wall) && $request->coffee_wall == "1" ? true : false,
                            'tax_amount' => $transactionTaxAmt,
                            'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                            'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                            'deduct_type' => isset($request->deduct_tax) ? $request->deduct_tax : NULL,
                        ]);

                        if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'cr_amount' => $currentBookingFee,
                            ]);
                        }

                        Notification::create([
                            'ride_id' => $ride->id,
                            'posted_by' => auth()->user()->id,
                            'message' =>   'Instant booking details - ' . auth()->user()->first_name,
                            'status' => 'completed',
                            'notification_type' => 'upcoming',
                            'ride_detail_id' => $booking->ride_detail_id,
                            'departure' => $booking->departure,
                            'destination' => $booking->destination
                        ]);

                        Notification::create([
                            'type' => 2,
                            'ride_id' => $ride->id,
                            'posted_to' => $booking->id,
                            'posted_by' => $ride->added_by,
                            'message' =>  'Your booking details',
                            'status' => 'completed',
                            'notification_type' => 'upcoming',
                            'ride_detail_id' => $booking->ride_detail_id,
                            'departure' => $booking->departure,
                            'destination' => $booking->destination
                        ]);

                        return redirect()->route('my_trips', ['lang' => $this->selectedLanguage->abbreviation])->with(['success' => $message->book_seat_message]);
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        // Handle error
                        return redirect()->back()->with(['error' => $e->getMessage()]);
                    }
                }
            }
            $booking->update([
                'seats' => $request->seats,
                'booking_credit' => $request->booking_credit,
                'fare' => $request->seats_amount,
                'tax_amount' => $taxAmt
            ]);

            $ids = $request->seats_id;
            $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
            if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                foreach ($getSeatDetails as $key => $getSeatDetail) {
                    $getSeatDetail->status = 'booked';
                    $getSeatDetail->booking_id = $booking->id;
                    $getSeatDetail->user_id = $booking->user_id;
                    $getSeatDetail->save();
                }
            }

            if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
                $getBookingFeeSum = Transaction::where('booking_id', $booking->id)->sum('booking_fee');
                $currentBookingFee = $request->booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);


                $transactionTaxAmt = $taxAmt - $transactionTaxSum;

                $newTransaction = Transaction::create([
                    'booking_id' => $booking->id,
                    'type' => '1',
                    'price' => $currentBookingFee,
                    'booking_fee' => $currentBookingFee,
                    'coffee_from_wall' => isset($request->coffee_wall) && $request->coffee_wall == "1" ? true : false,
                    'tax_amount' => $transactionTaxAmt,
                    'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                    'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                    'deduct_type' => isset($request->deduct_tax) ? $request->deduct_tax : NULL,
                ]);

                $coffeeWallet = CoffeeWallet::create([
                    'booking_id' => $booking->id,
                    'ride_id' => $ride->id,
                    'user_id' => $booking->user_id,
                    'cr_amount' => $currentBookingFee + $transactionTaxAmt,
                ]);
            }


            Notification::create([
                'ride_id' => $ride->id,
                'posted_by' => auth()->user()->id,
                'message' =>   'Instant booking details - ' . auth()->user()->first_name,
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            Notification::create([
                'type' => 2,
                'ride_id' => $ride->id,
                'posted_to' => $booking->id,
                'posted_by' => $ride->added_by,
                'message' =>  'Your booking details',
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            return redirect()->route('my_trips', ['lang' => $this->selectedLanguage->abbreviation])->with(['success' => $message->book_seat_message]);
        } elseif ($request->seats <= $booking->seats) {
            return redirect()->route('search_ride', ['lang' => $this->selectedLanguage->abbreviation])->with(['success' => $messages->booking_not_update_message ?? 'You did not update your booking in this ride']);
        }
    }

    public function updateSuccessTransaction($id, $seats, $seats_amount, $booking_credit, $fare, $online_payment, $cash_payment, $total, $seats_id, $coffee_wall, $transactionTaxSum, $ride, $tax_amount, $tax_percentage, $tax_type, $deduct_tax,  Request $request)
    {


        $taxAmt = $tax_amount;
        $paypal = new PayPalClient;
        $paypal->setApiCredentials(config('paypal'));
        $token = $paypal->getAccessToken();
        $paypal->setAccessToken($token);

        $result = $paypal->capturePaymentOrder($request->get('token'));

        if ($result['status'] == 'COMPLETED') {
            $booking = Booking::where('id', $id)->first();
            $ride = $this->loadRideForBooking(
                $booking->ride_id,
                $booking->from_stop_id,
                $booking->to_stop_id
            );
            $user = User::where('id', auth()->user()->id)->first();
            $bookingRouteData = $this->resolveBookingRouteData(
                $ride,
                $booking->from_stop_id,
                $booking->to_stop_id
            );

            $selectedLanguage = session('selectedLanguage');
            $findRidePage = null;
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
                if ($selectedLanguage) {
                    $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                }
            }

            // Payment successful, handle booking logic here
            $booking->update([
                'seats' => $seats,
                'booking_credit' => $booking_credit,
                'fare' => $fare,
                'tax_amount' => $taxAmt
            ]);
            $booking = $this->syncBookingRouteData(
                $booking,
                $ride,
                $bookingRouteData['from_stop_id'],
                $bookingRouteData['to_stop_id']
            );

            $seats_id_array = explode(',', $seats_id);
            $getSeatDetails = SeatDetail::whereIn('id', $seats_id_array)->get();
            if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                foreach ($getSeatDetails as $key => $getSeatDetail) {
                    $getSeatDetail->status = 'booked';
                    $getSeatDetail->booking_id = $booking->id;
                    $getSeatDetail->user_id = $booking->user_id;
                    $getSeatDetail->save();
                }
            }

            $captureId = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            $getBookingFeeSum = Transaction::where('booking_id', $booking->id)->sum('booking_fee');
            $currentBookingFee = $booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);

            $onlinePayment = $online_payment;
            if (isset($coffee_wall) && $coffee_wall == "1") {
                $onlinePayment = $online_payment + $currentBookingFee;
            }

            $transactionTaxAmt = $taxAmt - $transactionTaxSum;

            $currentTransactionAmt = $onlinePayment - $transactionTaxAmt;

            $newTransaction = Transaction::create([
                'booking_id' => $booking->id,
                'type' => '1',
                'booking_fee' => $currentBookingFee,
                'price' => $currentTransactionAmt,
                'paypal_id' => $captureId,
                'coffee_from_wall' => isset($coffee_wall) && $coffee_wall == "1" ? true : false,
                'tax_amount' => $transactionTaxAmt,
                'tax_percentage' => $tax_percentage,
                'tax_type' => $tax_type,
                'deduct_type' => $deduct_tax,
            ]);

            if (isset($coffee_wall) && $coffee_wall == "1") {
                $coffeeWallet = CoffeeWallet::create([
                    'booking_id' => $booking->id,
                    'ride_id' => $ride->id,
                    'user_id' => $booking->user_id,
                    'cr_amount' => $currentBookingFee,
                ]);
            }

            Notification::create([
                'ride_id' => $ride->id,
                'posted_by' => $user->id,
                'message' =>   'Instant booking details - ' . $user->first_name,
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            Notification::create([
                'type' => 2,
                'ride_id' => $ride->id,
                'posted_to' => $booking->id,
                'posted_by' => $ride->added_by,
                'message' =>  $seats . ' booked successfully',
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            $Price = $fare / $booking->seats;

            // $data = ['first_name' => $ride->driver->first_name, 'passenger_first_name' => $user->first_name,'secured_cash_code' => $secured_cash_code];
            // Mail::to($ride->driver->email)->queue(new InstantBookingMail($data));
            if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                $data = ['first_name' => $ride->driver->first_name, 'lang' => $this->selectedLanguage->abbreviation, 'origin' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'time' => $ride->time, 'seats' => $booking->seats, 'booking_price' => $Price, 'total_price' => $fare, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'email' => $user->email, 'phone' => $user->phone];
                Mail::to($ride->driver->email)->queue(new PassengerDetailsMail($data));
            }

            if (isset($user->email_notification) && $user->email_notification == 1) {

                $data = ['first_name' => $user->first_name, 'driver_first_name' => $ride->driver->first_name, 'driver_last_name' => $ride->driver->last_name, 'gender' => $ride->driver->gender, 'email' => $ride->driver->email, 'phone' => $ride->driver->phone, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
                Mail::to($user->email)->queue(new DriverDetailsMail($data));

                $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $seats_amount, 'booking_credit' => $booking_credit, 'online_payment' => $online_payment, 'cash_payment' => $cash_payment, 'total' => $total];
                Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
            }
            $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

            if (!$phoneNumber) {
                $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
            }

            if ($phoneNumber && env('APP_ENV') != 'local' && isset($ride->driver->sms_notification) && $ride->driver->sms_notification == 1) {
                // Send the secured cash code via Twilio
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_PHONE_NUMBER');

                $twilio = new Client($sid, $token);
                $to = $phoneNumber->phone;

                $title = "";
                $currentHour = date('H');
                if ($currentHour >= 0 && $currentHour < 12) {
                    $title = "Good morning " . $ride->driver->first_name . ",";
                } elseif ($currentHour >= 12 && $currentHour < 17) {
                    $title = "Good afternoon " . $ride->driver->first_name . ",";
                } else {
                    $title = "Good evening " . $ride->driver->first_name . ",";
                }

                $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));

                $message = "" . $title . "\n" . $user->first_name . " has booked seat in your ride\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nPassenger phone number: " . $user->phone . "\n";


                try {
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                } catch (\Exception  $e) {
                    $this->logTwilioSmsFailure($to, $message, $e);

                    // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                }
            }

            $ride_time = strtotime($ride->time);
            $current_time = time();
            $current_date = date('Y-m-d');
            $time_left = $ride_time - $current_time;
            if ($current_date == date('Y-m-d', strtotime($ride->data)) && $time_left <= 3600) {
                $getBookings = Booking::with('passenger')
                    ->where('ride_id', $ride->id)
                    ->where('status', '!=', '3')
                    ->where('status', '!=', '0')
                    ->where('status', '!=', '4')
                    ->get();
                $messageContent = "";
                if (isset($getBookings) && count($getBookings) > 0) {
                    foreach ($getBookings as $key => $getBooking) {
                        if ($messageContent == "") {
                            $messageContent = "" . $getBooking->passenger->first_name . "(" . $getBooking->passenger->phone . ")";
                        } else {
                            $messageContent .= "\n" . $getBooking->passenger->first_name . "(" . $getBooking->passenger->phone . ")";
                        }
                    }
                    $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

                    if (!$phoneNumber) {
                        $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
                    }

                    if ($phoneNumber && env('APP_ENV') != 'local' && isset($ride->driver->sms_notification) && $ride->driver->sms_notification == 1) {
                        $sid = env('TWILIO_ACCOUNT_SID');
                        $token = env('TWILIO_AUTH_TOKEN');
                        $from = env('TWILIO_PHONE_NUMBER');

                        $twilio = new Client($sid, $token);
                        $to = $phoneNumber->phone;

                        $title = "";
                        $currentHour = date('H');
                        if ($currentHour >= 0 && $currentHour < 12) {
                            $title = "Good morning " . $ride->driver->first_name . ",";
                        } elseif ($currentHour >= 12 && $currentHour < 17) {
                            $title = "Good afternoon " . $ride->driver->first_name . ",";
                        } else {
                            $title = "Good evening " . $ride->driver->first_name . ",";
                        }

                        $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));

                        $message = "" . $title . "\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nHere is your passengers’ list\n" . $messageContent . "";

                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                        } catch (\Exception  $e) {
                            $this->logTwilioSmsFailure($to, $message, $e);
                        }
                    }
                }
            }

            return redirect()->route('my_trips', ['lang' => $this->selectedLanguage->abbreviation])->with(['success' => 'You have successfully booked seat in this ride']);
        }

        return redirect()
            ->route('home')
            ->with('message', 'Transaction failed.');
    }



    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(config('stripe.secret'));
        $paymentIntent = PaymentIntent::create([
            'amount' => round((float) $request->amount * 100, 0),
            'currency' => 'cad',
            'payment_method_types' => ['card'],
        ]);
        return response()->json([
            'clientSecret' => $paymentIntent->client_secret
        ]);
    }

    public function handleBookingRequest(Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        $findRidePage = null;
        $messages = null;
        $ride = Ride::where('id', $request->id);
        $rideDetailId = isset($request->ride_detail_id) ? $request->ride_detail_id : 0;
        if ($rideDetailId != 0) {
            $ride = $ride->with(['rideDetail' => function ($q) use ($rideDetailId) {
                $q->where('id', $rideDetailId);
            }]);
        } else {
            $ride = $ride->with(['rideDetail' => function ($q) {
                $q->where('default_ride', '1');
            }]);
        }
        $ride = $ride->first();


        $type = FeaturesSetting::whereId($ride->payment_method)->first();

        $validated = $request->validate([
            'seats' => 'required|integer|min:1',
            'agree_terms' => 'accepted|required',
            'firm_agree_terms' => 'accepted|required_if:booking_type,37',
        ]);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message_end_part', 'book_seat_message', 'block_booking_message', 'verified_number_message', 'add_your_phone')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message_end_part', 'book_seat_message', 'block_booking_message', 'verified_number_message', 'add_your_phone')->first();
            }
        }
        $user = User::where('id', auth()->user()->id)->first();
        $phoneNumber = PhoneNumber::where('user_id', $user->id)->first();
        $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();
        $selectedLanguage = Language::find($request->input('language_id'));
        if (is_null($phoneNumber) && $type->slug == 'secured') {
            return response()->json(['error' => $messages->add_your_phone ?? 'Please add your phone number.'], 400);
        }

        $phoneVerification = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
        if (!$phoneVerification && $type->slug == 'secured') {
            return response()->json(['error' => $messages->verified_number_message ?? 'Please verify your phone number.'], 400);
        }
        if ($user->block_booking == '1') {
            return response()->json(['error' => $message->block_booking_message ?? 'You are blocked from booking.'], 400);
        }


        $bookings = Booking::where('ride_id', $request->id)->where('status', '!=', '3')->where('status', '!=', '4')->get();
        $seatsBooked = $bookings->sum('seats') + $request->seats;
        if ($seatsBooked > $ride->seats) {
            return response()->json(['error' => $errorMsg->seat_unavailable_message ?? 'Oops, this seat is no longer available.'], 400);
        }
        return response()->json(['success' => true, 'message' => 'Validation passed. Ready for payment.'], 200);
    }
}
