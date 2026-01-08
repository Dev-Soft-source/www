<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateSeatOnHold;
use App\Mail\AcceptBookingRequestMail;
use App\Mail\BookingRequestConfirmationMail;
use App\Mail\ArbitrationCancelledMail;
use App\Mail\BookingRequestMail;
use App\Mail\DriverDetailsMail;
use App\Mail\PassengerCancelBookingMail;
use App\Mail\PassengerDetailsMail;
use App\Mail\PaymentInvoiceMail;
use App\Mail\RejectBookingRequestMail;
use App\Models\Booking;
use App\Models\City;
use App\Models\BookingPageSettingDetail;
use App\Models\CancellationHistory;
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
use App\Models\Payout;
use App\Models\PhoneNumber;
use App\Models\PostRidePageSettingDetail;
use App\Models\Ride;
use App\Models\SeatDetail;
use App\Models\SiteSetting;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use App\Models\User;
use App\Models\TripsPageSettingDetail;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Stripe\Stripe;
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
    public function create($lang = null, $id, $rideDetailId)
    {
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
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button', 'login_before_booking_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button', 'login_before_booking_message')->first();
            }
        }

        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();
            // Check if user has suspanded
            if ($user->suspand === '1') {
                return back()->with('message', 'Your account has been suspended by the admin');
            }

            // $rideDetailId = isset($request->rideDetailId) ? $request->rideDetailId : 0;

            $ride = Ride::where('id', $id);
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

            if (!isset($ride) && empty($ride)) {
                $lang = $lang ?? "en";
                return redirect(route('home', ['lang' => $lang]));
            }
            $setting = SiteSetting::first();

            $stateTax = 0;
            if (isset($setting->deduct_tax) && $setting->deduct_tax == "deduct_from_passenger" && $setting->tax_type == "state_wise_tax") {
                $locationBeforeComma = explode(',', $ride->rideDetail[0]->departure);
                $getFromState = City::with('state:id,tax')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ', ['%' . $locationBeforeComma[0] . '%'])->first();
                if (isset($getFromState) && !empty($getFromState)) {
                    $stateTax = $getFromState->state->tax;
                }
            }

            $bookingPage = null;
            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }

                $bookingPage = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->first();

                $ride->luggage = FeaturesSettingDetail::whereFeaturesSettingId($ride->luggage)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->payment_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->payment_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $ride->animal_friendly = FeaturesSettingDetail::whereFeaturesSettingId($ride->animal_friendly)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $ride->booking_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $featureIds = explode('=', $ride->features);
                // Fetch data for each feature ID and concatenate with '='
                $featureNames = collect($featureIds)->map(function ($id) use ($selectedLanguage) {
                    return FeaturesSettingDetail::whereFeaturesSettingId($id)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                })->filter()->implode('='); // Filter out nulls and concatenate with '='
                $ride->features = $featureNames;
            }

            $bookings = Booking::where('ride_id', $id)->where('status', '!=', '3')->where('status', '!=', '4')->get();

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

            $errorMsg = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();

            $seatsBooked = $bookings->sum('seats');
            if ($seatsBooked >= $ride->seats) {
                return redirect()->route('search_ride', ['notificationPage' => $notificationPage, 'successMessage' => $successMessage, 'lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $errorMsg->seat_unavailable_message]);
            }

            $cards = Card::where('user_id', $user_id)->orderBy('id', 'desc')->get();

            Stripe::setApiKey(env('STRIPE_SECRET'));
            // Fetch card details from Stripe
            foreach ($cards as $card) {
                if ($card->stripe_payment_method_id) {
                    $card->paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                }
            }

            $getCoffeeCrBalance = CoffeeWallet::sum('cr_amount');
            $getCoffeeDrBalance = CoffeeWallet::sum('dr_amount');

            $getCrBalance = TopUpBalance::where('user_id', $user->id)->sum('cr_amount');
            $getDrBalance = TopUpBalance::where('user_id', $user->id)->sum('dr_amount');

            return view('booking', ['bookingPage' => $bookingPage, 'rideDetailPage' => $rideDetailPage, 'ride' => $ride, 'cards' => $cards, 'balance' => ($getDrBalance - $getCrBalance), 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'postRidePage' => $postRidePage, 'setting' => $setting, 'coffeeBalance' => ($getCoffeeDrBalance - $getCoffeeCrBalance), 'stateTax' => $stateTax]);
        } else {
            return back()->with(['message' => $successMessage->login_before_booking_message ?? 'You must have to log in to your account before booking']);
        }
    }

    public function seatOnHold(Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('seat_hold_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('seat_hold_message')->first();
            }
        }

        $user = auth()->user();
        $getSeatDetail = SeatDetail::where('id', $request->seat_id)->first();
        if (isset($getSeatDetail) && !empty($getSeatDetail)) {
            if ($getSeatDetail->status == "pending") {
                $getSeatDetail->user_id = $user->id;
                $getSeatDetail->status = 'hold';
                $getSeatDetail->save();

                $jobTime = 10;
                $getRide = Ride::where('id', $getSeatDetail->ride_id)->first();
                if (isset($getRide) && !empty($getRide)) {
                    $ride_time_str = $getRide->date . ' ' . $getRide->time;
                    $ride_timestamp = strtotime($ride_time_str);
                    $current_timestamp = time();

                    if ($ride_timestamp > $current_timestamp) {
                        $time_difference = $ride_timestamp - $current_timestamp;
                        $hours_difference = $time_difference / 3600;

                        $delay_minutes = ($hours_difference <= 1) ? 5 : 10;

                        UpdateSeatOnHold::dispatch($getSeatDetail->id)->delay(now()->addMinutes((int)$delay_minutes));
                    }
                }
                $data['getSeatDetail'] = $getSeatDetail;
                $data['message'] = 'Seat on hold successfully';
                return response()->json($data);
            } else if ($getSeatDetail->status == "booked") {
                $data['message'] = 'Seat booked please select another seat';
                return response()->json($data);
            } else if ($getSeatDetail->status == "hold") {
                if ($getSeatDetail->user_id == $user->id) {
                    $getSeatDetail->user_id = NULL;
                    $getSeatDetail->status = 'pending';
                    $getSeatDetail->save();
                    $data['getSeatDetail'] = $getSeatDetail;
                    $data['message'] = 'Seat on pending successfully';
                    return response()->json($data);
                } else {
                    $data['message'] = $messages->seat_hold_message ?? null;
                    return response()->json($data);
                }
            }
        } else {
            $data['message'] = 'Seat not found';
            return response()->json($data);
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

    public function edit($lang = null, $id)
    {
        $booking = Booking::with(['transaction:id,booking_id,tax_amount,tax_percentage,tax_type,deduct_type'])->where('id', $id)->first();

        if (!isset($booking) && empty($booking)) {
            $lang = $lang ?? "en";
            return redirect(route('home', ['lang' => $lang]));
        }

        $ride = Ride::where('id', $booking->ride_id)->first();
        $setting = SiteSetting::first();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }

        $bookingPage = null;

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button', 'login_before_booking_message')->first();
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }

                $bookingPage = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->first();

                $ride->luggage = FeaturesSettingDetail::whereFeaturesSettingId($ride->luggage)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->payment_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->payment_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $ride->animal_friendly = FeaturesSettingDetail::whereFeaturesSettingId($ride->animal_friendly)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $ride->booking_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $featureIds = explode('=', $ride->features);
                // Fetch data for each feature ID and concatenate with '='
                $featureNames = collect($featureIds)->map(function ($id) use ($selectedLanguage) {
                    return FeaturesSettingDetail::whereFeaturesSettingId($id)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                })->filter()->implode('='); // Filter out nulls and concatenate with '='
                $ride->features = $featureNames;
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $rideDetailPage = RideDetailPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button', 'login_before_booking_message')->first();
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                if ($postRidePage) {
                    // Add features_option1_1 as an additional property
                    $postRidePage->features_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option4 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option5 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option6 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option6)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option7 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option7)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option8 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option9 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option10 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option11 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option12 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option13 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option14 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option15 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->features_option16 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->features_option16)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->payment_methods_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->payment_methods_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->smoking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->smoking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->animals_option3 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->animals_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label1 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                    $postRidePage->cancellation_policy_label2 = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->first();
                }

                $bookingPage = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->first();


                $ride->luggage = FeaturesSettingDetail::whereFeaturesSettingId($ride->luggage)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');

                $ride->payment_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->payment_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $ride->animal_friendly = FeaturesSettingDetail::whereFeaturesSettingId($ride->animal_friendly)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $ride->booking_method = FeaturesSettingDetail::whereFeaturesSettingId($ride->booking_method)
                    ->whereLanguageId($selectedLanguage->id)
                    ->first();

                $featureIds = explode('=', $ride->features);
                // Fetch data for each feature ID and concatenate with '='
                $featureNames = collect($featureIds)->map(function ($id) use ($selectedLanguage) {
                    return FeaturesSettingDetail::whereFeaturesSettingId($id)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                })->filter()->implode('='); // Filter out nulls and concatenate with '='
                $ride->features = $featureNames;
            }
        }

        if (auth()->user()) {
        } else {
            return back()->with(['message' => $successMessage->login_before_booking_message ?? 'You must have to log in to your account before booking']);
        }

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

        $cards = Card::where('user_id', $user_id)->orderBy('id', 'desc')->get();

        Stripe::setApiKey(env('STRIPE_SECRET'));
        // Fetch card details from Stripe
        foreach ($cards as $card) {
            if ($card->stripe_payment_method_id) {
                $card->paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
            }
        }

        $getCoffeeCrBalance = CoffeeWallet::sum('cr_amount');
        $getCoffeeDrBalance = CoffeeWallet::sum('dr_amount');

        $getCrBalance = TopUpBalance::where('user_id', $user_id)->sum('cr_amount');
        $getDrBalance = TopUpBalance::where('user_id', $user_id)->sum('dr_amount');

        return view('edit_booking', ['notificationPage' => $notificationPage, 'rideDetailPage' => $rideDetailPage, 'successMessage' => $successMessage, 'booking' => $booking, 'ride' => $ride, 'cards' => $cards, 'balance' => ($getDrBalance - $getCrBalance), 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'postRidePage' => $postRidePage, 'setting' => $setting, 'coffeeBalance' => ($getCoffeeDrBalance - $getCoffeeCrBalance), 'bookingPage' => $bookingPage]);
    }

    public function cancel($lang = null, $id)
    {
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'features_option1', 'features_option2', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $limitExceed = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_cancellation_limit_exceed')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'features_option1', 'features_option2', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
                $tripsPage = TripsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $limitExceed = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_cancellation_limit_exceed')->first();
            }
        }
        $user_id = auth()->user()->id;
        $setting = SiteSetting::first();
        $monthsAgo = Carbon::now()->subMonths($setting->booking_cancel_duration);

        $cancellationCount = Booking::where('user_id', $user_id)
            ->where('booked_on', '>=', $monthsAgo)
            ->count();
        // if ($cancellationCount >= $setting->booking_cancel_limit) {
        // return redirect()->back()->with(['failure' => $limitExceed->booking_cancellation_limit_exceed ?? "Booking cancellation limit exceeded"]);
        // }


        $booking = Booking::where('id', $id)->first();
        $ride = Ride::where('id', $booking->ride_id)->first();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }

        $sureMessage = $tripsPage->cancel_booking_confirm_message ?? "Are you sure you want to cancel booking?";
        $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
        $bookingDateTime = Carbon::parse($booking->booked_on);
        $hoursDifference = $rideDateTime->diffInHours($bookingDateTime);

        $type = FeaturesSetting::whereId($booking->type)->first();

        if ($type->slug === 'firm') {
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
        return view('cancel_booking', ['notificationPage' => $notificationPage, 'successMessage' => $successMessage, 'booking' => $booking, 'ride' => $ride, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'postRidePage' => $postRidePage, 'setting' => $setting, 'tripsPage' => $tripsPage, 'sureMessage' => $sureMessage]);
    }

    public function bookingRequest($id, Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        $findRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('verified_number_message', 'block_booking_message', 'booking_request_success_message', 'add_your_phone')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('verified_number_message', 'block_booking_message', 'booking_request_success_message', 'add_your_phone')->first();
            }
        }
        $ride = Ride::where('id', $request->id)->first();
        $type = FeaturesSetting::whereId($ride->payment_method)->first();
        $user = User::where('id', auth()->user()->id)->first();
        $phoneNumber = PhoneNumber::where('user_id', $user->id)->first();
        if (is_null($phoneNumber) && $type->slug == 'secured') {
            return redirect()->back()->with(['failure' => $messages->add_your_phone ?? "add phone number"]);
        }

        $phoneVerification = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
        if (!$phoneVerification && $type->slug == 'secured') {

            return redirect()->back()->with(['failure' => $messages->verified_number_message ?? "secured cash message", 'phone' => $phoneNumber]);
        }




        $rideDetailId = isset($request->ride_detail_id) ? $request->ride_detail_id : 0;

        $ride = Ride::where('id', $request->id);
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


        $taxAmt = isset($request->tax_amount) ? $request->tax_amount : 0;



        if ($user->block_booking == '1') {
            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $message->block_booking_message ?? null]);
        }

        // Passenger gatekeeping logic for Pink Ride and Extra Care Ride
        $featuresArray = explode('=', $ride->features);
        $pinkRideSetting = PinkRideSetting::first();
        $folkRideSetting = FolkRideSetting::first();

        // Check if ride has Pink Ride feature (feature ID 1)
        if (in_array('1', $featuresArray)) {
            // For passengers booking Pink Rides, require government ID
            if ($pinkRideSetting && $pinkRideSetting->driver_license === '1') {
                if (empty($user->government_id) && empty($user->driver_license_upload)) {
                    return redirect()->back()->with(['failure' => 'A government-issued photo ID is required to book Pink Rides. Please upload your government ID or driver\'s license in your profile.']);
                }
            }
        }

        // Check if ride has Extra Care feature (feature ID 2)
        if (in_array('2', $featuresArray)) {
            // For passengers booking Extra Care Rides, require government ID
            if ($folkRideSetting && $folkRideSetting->driver_license === '1') {
                if (empty($user->government_id) && empty($user->driver_license_upload)) {
                    return redirect()->back()->with(['failure' => 'A government-issued photo ID is required to book Extra Care Rides. Please upload your government ID or driver\'s license in your profile.']);
                }
            }
        }

        $rules = [
            'agree_terms' => 'accepted|required',
            'seats' => 'required|integer|min:1',
            'driver_message' => 'required'
        ];

        if ($ride->booking_type == "37") {
            $rules['firm_agree_terms'] = 'accepted|required';
        }

        if (in_array('1', $featuresArray)) {
            $rules['pink_ride_agree_terms'] = 'accepted|required';
        }
        if (in_array('2', $featuresArray)) {
            $rules['extra_care_ride_agree_terms'] = 'accepted|required';
        }

        $request->validate($rules);


        $bookings = Booking::where('ride_id', $id)->where('status', '!=', '3')->where('status', '!=', '4')->get();
        $errorMsg = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();

        $seatsBooked = $bookings->sum('seats') + $request->seats;
        if ($seatsBooked > $ride->seats) {
            // return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation,'from' => $ride->rideDetail[0]->departure,'to' => $ride->rideDetail[0]->destination,'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => 'Oops, this seat is no longer available. Looks like another passenger has just booked it. We apologize for the inconvenience. Here are more rides for your route']);

            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $errorMsg->seat_unavailable_message]);
        }

        $price = $request->seats_amount / $request->seats;

        if ($request->online_payment > '0') {
            $validated = $request->validate([
                'payment_method' => 'required',
                'card_id' => $request->payment_method == 'credit_card' && !isset($request->gPayApplePayId) && $request->gPayApplePayId == "" ? 'required' : 'nullable',
                'booking_credit' => 'required|max:25',
            ]);



            if ($request->payment_method == 'paypal') {

                $paypal = new PayPalClient;
                $paypal->setApiCredentials(config('paypal'));
                $token = $paypal->getAccessToken();
                $paypal->setAccessToken($token);

                if ($request->online_payment > '0') {

                    if ($ride->payment_method === $findRidePage->payment_methods_option4) {
                        $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->where('default', '1')->first();

                        if (!$phoneNumber) {
                            $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
                        }

                        if (!$phoneNumber) {
                            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $messages->verified_number_message ?? null]);
                        }
                    }

                    $paypalPay = 0;
                    if ($request->cash_payment > 0) {
                        $paypalPay = $request->input('online_payment') + $taxAmt;
                    } else {
                        $paypalPay = $request->input('online_payment');
                    }

                    $order = $paypal->createOrder([
                        "intent" => "CAPTURE",
                        "purchase_units" => [
                            [
                                "amount" => [
                                    "currency_code" => "USD",
                                    "value" => number_format((float)$paypalPay, 2, '.', '')
                                ]
                            ]
                        ],
                        "application_context" => [
                            "cancel_url" => route('paypal.cancel'),
                            "return_url" => route('paypal.success.booking_request', [
                                'id' => $ride->id,
                                'type' => $request->type,
                                'seats' => $request->seats,
                                'seats_amount' => $request->seats_amount,
                                'booking_credit' => $request->booking_credit,
                                'online_payment' => $request->online_payment,
                                'cash_payment' => $request->cash_payment,
                                'total' => $request->total,
                                'seats_id' => implode(',', $request->seats_id),
                                'coffee_wall' => isset($request->coffee_wall) ? $request->coffee_wall : '0',
                                'transactionTaxSum' => 0,
                                'ride' => $ride,
                                'tax_amount' => isset($request->tax_amount) ? $request->tax_amount : 0,
                                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                                'deduct_tax' => isset($request->deduct_tax) ? $request->deduct_tax : NULL
                            ]),
                        ]
                    ]);
                }

                if (isset($order['id'])) {
                    foreach ($order['links'] as $link) {
                        if ($link['rel'] == 'approve') {
                            return redirect()->away($link['href']);
                        }
                    }
                }

                return redirect()->route('paypal.cancel');
            } elseif ($request->payment_method == 'credit_card') {

                //Booking Method
                $secured_cash = null;
                $secured_cash_code = null;


                $stripId = null;
                try {


                    if (isset($request->gPayApplePayId) && $request->gPayApplePayId != '') {
                        $stripId = $request->gPayApplePayId;
                    } else {
                        // Retrieve the selected card from the database
                        $card = Card::where('id', $request->card_id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

                        // Set your Stripe API key.
                        Stripe::setApiKey(env('STRIPE_SECRET'));

                        // Attach the payment method to the customer
                        $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                        $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                        if ($request->online_payment > '0') {

                            $stripePay = 0;
                            if ($request->cash_payment > 0) {
                                $stripePay = $request->input('online_payment') + $taxAmt;
                            } else {
                                $stripePay = $request->input('online_payment');
                            }

                            // Create a payment intent
                            $paymentIntent = PaymentIntent::create([
                                'amount' => round(($stripePay * 100), 0),
                                'currency' => 'usd',
                                'customer' => $user->stripe_customer_id,
                                'payment_method' => $paymentMethod->id,
                                'off_session' => true,
                                'confirm' => true,
                            ]);

                            $stripId = $paymentIntent->id;
                        }
                    }





                    // Payment successful, handle booking logic here
                    $booking = Booking::create([
                        'user_id' => $user->id,
                        'ride_id' => $id,
                        'seats' => $request->seats,
                        'type' => $request->type,
                        'booked_on' => $currentTime,
                        'booking_credit' => $request->booking_credit,
                        'fare' => $request->seats_amount,
                        'tax_amount' => $taxAmt,
                        'secured_cash' => $secured_cash,
                        'secured_cash_code' => $secured_cash_code,
                        'expires_at' => $expiryTime,
                        'departure' => (string)$ride->rideDetail[0]->departure,
                        'destination' => (string)$ride->rideDetail[0]->destination,
                        'price' => $ride->rideDetail[0]->price,
                        'ride_detail_id' => $ride->rideDetail[0]->id
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

                    if ($request->online_payment > '0') {
                        $onlinePayment = $request->input('online_payment');
                        if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
                            $onlinePayment = $request->input('online_payment') + $request->booking_credit;
                        }

                        if ($request->cash_payment > 0) {
                            $onlinePayment = $onlinePayment;
                        } else {
                            $onlinePayment = $onlinePayment - $taxAmt;
                        }

                        $transaction = Transaction::create([
                            'booking_id' => $booking->id,
                            'type' => '1',
                            'booking_fee' => $request->booking_credit,
                            'price' => $onlinePayment,
                            'stripe_id' => $stripId,
                            'coffee_from_wall' => isset($request->coffee_wall) && $request->coffee_wall == "1" ? true : false,
                            'tax_amount' => $taxAmt,
                            'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                            'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                            'deduct_type' => isset($request->deduct_tax) ? $request->deduct_tax : NULL,
                        ]);

                        if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'cr_amount' => $request->booking_credit,
                            ]);
                        }
                    }
                    Notification::create([
                        'ride_id' => $id,
                        'posted_by' => $user->id,
                        'message' => 'You have a new booking request from ' . $user->first_name . "\n" .
                            'Seats booked: ' . numberToWords($request->seats),
                        'status' => 'request',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $booking->ride_detail_id,
                        'departure' => $booking->departure,
                        'destination' => $booking->destination
                    ]);

                    // Check the ride first message
                    $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
                        $query->where('sender', $ride->added_by)
                            ->where('receiver', $booking->user_id);
                    })->orWhere(function ($query) use ($booking, $ride) {
                        $query->where('sender', $booking->user_id)
                            ->where('receiver', $ride->added_by);
                    })->where('ride_id', $id)->first();
                    if (empty($rideFirstMessage)) {
                        $message1 = Message::create([
                            'ride_id' => $id,
                            'receiver' => $ride->added_by,
                            'sender' => $booking->user_id,
                            'message' => $request->driver_message,
                            'redirect' => '1',
                            'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                        ]);
                    }
                    $message = Message::create([
                        'ride_id' => $id,
                        'receiver' => $ride->added_by,
                        'sender' => $booking->user_id,
                        'message' => $request->driver_message,
                        'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                    ]);

                    $data = ['first_name' => $ride->driver->first_name, 'id' => $booking->id, 'lang' => $selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $price, 'total_price' => $request->seats_amount, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time, 'expires_at' => Carbon::parse($booking->expires_at)->format('H:i')];

                    // Send booking request email
                    if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                        Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
                    }
                    if (isset($user->email_notification) && $user->email_notification == 1) {

                        $data = ['first_name' => $user->first_name];
                        Mail::to($user->email)->queue(new BookingRequestConfirmationMail($data));


                        $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                            // ->where('verified', '1')
                            ->where('default', '1')
                            ->first();

                        $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;

                        // Get the verified phone number for the passenger (user)
                        $passengerPhoneNumber = PhoneNumber::where('user_id', $user->id)
                            // ->where('verified', '1')
                            ->where('default', '1')
                            ->first();

                        $passengerPhoneToUse = $passengerPhoneNumber ? $passengerPhoneNumber->phone : $user->phone;
                        $topUpBalance = TopUpBalance::create([
                            'booking_id' => $booking->id,
                            'user_id' => $user->id,
                            'cr_amount' => $request->cash_payment > 0 ? ($request->booking_credit + $taxAmt) : (isset($request->coffee_wall) && $request->coffee_wall == "1" ? ($request->seats_amount + $taxAmt) : $request->total),
                            'added_date' => date('Y-m-d'),
                        ]);
                        $card = isset($request->card_id) ? Card::find($request->card_id) : null;
                        $data = [
                            'first_name' => $user->first_name,
                            'full_name' => $user->first_name . ' ' . $user->last_name,
                            'amount' => $request->total,
                            'transaction_id' => $topUpBalance->random_id,
                            'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                            'payment_method' => isset($request->gPayApplePayId) && $request->gPayApplePayId != '' ? 'Gay' : 'credit_card',
                            'card_type' => isset($card) && !is_null($card) ? $card->card_type : "",
                            'cardholder_name' => isset($card) && !is_null($card) ? $card->name_on_card : "",
                            'last_four_digits' => isset($card) && !is_null($card) ? $card->card_number : "****",
                            'expiration_date' => isset($card) && !is_null($card) ? $card->exp_month . '/' . $card->exp_year : "",
                            'seats' => $booking->seats,
                            'seats_amount' => $request->seats_amount,
                            'booking_credit' => $booking->booking_credit,
                            'online_payment' => $request->online_payment,
                            'cash_payment' => $request->cash_payment,
                            'total' => $request->total
                        ];
                        Mail::to($user->email)->queue(new PaymentInvoiceMail($data));
                    }


                    $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

                    if (!$phoneNumber) {
                        $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
                    }

                    if (!$phoneNumber) {
                        $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->first();
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

                        // $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                        $departureTime = date('H:i:s', strtotime($ride->time));
                        $depatureDate = date('d F, Y', strtotime($ride->date));
                        $seatWords = numberToWords($booking->seats);
                        // Calculate remaining time
                        $rideDateTime = new DateTime($ride->date . ' ' . $ride->time);
                        $currentDateTime = new DateTime();
                        $timeLeft = $currentDateTime->diff($rideDateTime);
                        // Format remaining time
                        $timeLeftString = '';
                        if ($timeLeft->days > 0) {
                            $timeLeftString .= $timeLeft->days . ' days ';
                        }
                        $timeLeftString .= $timeLeft->h . ' hours and ' . $timeLeft->i . ' minutes';

                        // $message = "" . $title . "\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nPassenger Name: " . $user->first_name . "\nPassenger Phone Number: " . $user->phone . "\nYou have received a booking request. Please visit your ride’s page to approve or decline";
                        // $title = "From ProximaRide: You have a new booking request from (" . $user->first_name . ")";
                        // $depatureDate = date('F d, Y H:i', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                        $message = $title . "\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . "\n" . $user->first_name . ": " . $user->phone . "\nNumber of seats: " . $booking->seats . "\nClick here for accept(" . url("/accept/" . $booking->id) . ")\nClick here for reject(" . url("/reject/" . $booking->id) . ")";
                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                        } catch (\Exception  $e) {
                            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                            // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                        }
                    }

                    return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $messages->booking_request_success_message ?? 'Your request has been successfully sent to the driver']);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    // Handle error
                    return redirect()->back()->with(['error' => $e->getMessage()]);
                }
            }
        }

        //Booking Method
        $secured_cash = null;
        $secured_cash_code = null;

        // Payment successful, handle booking logic here
        $booking = Booking::create([
            'user_id' => $user->id,
            'ride_id' => $ride->id,
            'seats' => $request->seats,
            'type' => $request->type,
            'booked_on' => $currentTime,
            'booking_credit' => $request->booking_credit,
            'fare' => $request->seats_amount,
            'secured_cash' => $secured_cash,
            'tax_amount' => $taxAmt,
            'secured_cash_code' => $secured_cash_code,
            'expires_at' => $expiryTime,
            'departure' => $ride->rideDetail[0]->departure,
            'destination' => $ride->rideDetail[0]->destination,
            'price' => $ride->rideDetail[0]->price,
            'ride_detail_id' => $ride->rideDetail[0]->id

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


        $transcationId = "";

        if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
            $transaction = Transaction::create([
                'booking_id' => $booking->id,
                'type' => '1',
                'booking_fee' => $request->booking_credit,
                'price' => $request->booking_credit,
                'coffee_from_wall' => true,
                'tax_amount' => $taxAmt,
                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                'deduct_type' => isset($request->deduct_tax) ? $request->deduct_tax : NULL,
            ]);

            $transcationId = $transaction->random_id;

            $coffeeWallet = CoffeeWallet::create([
                'booking_id' => $booking->id,
                'ride_id' => $ride->id,
                'user_id' => $booking->user_id,
                'cr_amount' => $request->booking_credit + $taxAmt,
            ]);
        }

        if (isset($request->booked_by_wallet) && $request->booked_by_wallet == "1") {
            $transaction = Transaction::create([
                'booking_id' => $booking->id,
                'type' => '1',
                'booking_fee' => isset($request->coffee_wall) && $request->coffee_wall == "1" ? '0' : $request->booking_credit,
                'price' => $request->cash_payment > 0 ? $request->booking_credit : (isset($request->coffee_wall) && $request->coffee_wall == "1" ? $request->seats_amount : $request->total - $taxAmt),
                'pay_by_account' => true,
                'tax_amount' => $taxAmt,
                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                'deduct_type' => isset($request->deduct_tax) ? $request->deduct_tax : NULL,
            ]);

            $transcationId = $transaction->random_id;

            $topUpBalance = TopUpBalance::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'cr_amount' => $request->cash_payment > 0 ? ($request->booking_credit + $taxAmt) : (isset($request->coffee_wall) && $request->coffee_wall == "1" ? ($request->seats_amount + $taxAmt) : $request->total),
                'added_date' => date('Y-m-d'),
            ]);
        }

        $notification = Notification::create([
            'ride_id' => $ride->id,
            'posted_by' => $user->id,
            // 'message' =>  'You have a new booking request from ' . $user->first_name . '.' . $request->seats . ' Seats booked',
            'message' => 'You have a new booking request from ' . $user->first_name . "\n" .
                'Seats booked: ' . numberToWords($request->seats),
            'status' => 'request',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination
        ]);

        // Check the ride first message
        $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
            $query->where('sender', $ride->added_by)
                ->where('receiver', $booking->user_id);
        })->orWhere(function ($query) use ($booking, $ride) {
            $query->where('sender', $booking->user_id)
                ->where('receiver', $ride->added_by);
        })->where('ride_id', $ride->id)->first();
        if (empty($rideFirstMessage)) {
            $message1 = Message::create([
                'ride_id' => $ride->id,
                'receiver' => $ride->added_by,
                'sender' => $booking->user_id,
                'message' => $request->driver_message,
                'redirect' => '1',
                'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
            ]);
        }
        $message = Message::create([
            'ride_id' => $ride->id,
            'receiver' => $ride->added_by,
            'sender' => $booking->user_id,
            'message' => $request->driver_message,
            'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
        ]);
        // Assuming $user and $fcmToken are defined
        $fcmService = new FCMService();
        $fcm_tokens = FCMToken::where('user_id', $ride->driver->id)->get();
        $body = $notification->message;

        $fcmToken = $ride->driver->mobile_fcm_token;
        if ($fcmToken) {
            $fcmService->sendNotification($fcmToken, $body);
        }

        foreach ($fcm_tokens as $fcm_token) {
            try {
                $fcmService->sendNotification($fcm_token->token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
            }
        }


        if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {
            $data = ['first_name' => $ride->driver->first_name, 'id' => $booking->id, 'lang' => $selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $price, 'total_price' => $request->seats_amount, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
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


            // $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $request->seats_amount, 'booking_credit' => $booking->booking_credit, 'online_payment' => $request->online_payment, 'cash_payment' => $request->cash_payment, 'total' => $request->total];
            $data = [
                'first_name' => $user->first_name,
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'amount' => $request->total,
                'transaction_id' => $transcationId,
                'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                'transaction_type' => 'topup_balance', // Add this special flag
                'seats' => $booking->seats,
                'seats_amount' => $request->seats_amount,
                'booking_credit' => $booking->booking_credit,
                'online_payment' => $request->online_payment,
                'cash_payment' => $request->cash_payment,
                'total' => $request->total
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

            $departureTime = date('H:i:s', strtotime($ride->time));
            $depatureDate = date('d F, Y', strtotime($ride->date));
            $seatWords = numberToWords($booking->seats);
            // Calculate remaining time
            $rideDateTime = new DateTime($ride->date . ' ' . $ride->time);
            $currentDateTime = new DateTime();
            $timeLeft = $currentDateTime->diff($rideDateTime);
            // Format remaining time
            $timeLeftString = '';
            if ($timeLeft->days > 0) {
                $timeLeftString .= $timeLeft->days . ' days ';
            }
            $timeLeftString .= $timeLeft->h . ' hours and ' . $timeLeft->i . ' minutes';

            // $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));

            // $message = "" . $title . "\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nPassenger Name: " . $user->first_name . "\nPassenger Phone Number: " . $user->phone . "\nYou have received a booking request. Please visit your ride’s page to approve or decline";
            // $title = "From ProximaRide: You have a new booking request from (" . $user->first_name . ")";
            // $depatureDate = date('F d, Y H:i', strtotime('' . $ride->date . ' ' . $ride->time . ''));
            $message = $title . "\n" . "From ProximaRide: You have a new booking request from (" . $user->first_name . ")\n"
                . "\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . "at " . $departureTime . "\n" . $user->first_name . ": " . $user->phone . "\nNumber of seats: " . $seatWords . "\nReply \"11\" to approve or \"33\" to decline; you have until (" . $timeLeftString . ")";
            try {
                $res = $twilio->messages->create(
                    $to,
                    [
                        'from' => $from,
                        'body' => $message,
                    ]
                );
            } catch (\Exception  $e) {
                Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
            }
        }
        return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $messages->booking_request_success_message ?? 'Your request has been successfully sent to the driver']);
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
        $message = "Message from ProximaRide. Your verification code is: $verificationCode. This code will expire in 30 minutes.";

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
            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

            // return redirect()->back()->with(['error' => 'Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate']);
        }

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        // return redirect()->route('phone_code', ['lang' => $selectedLanguage->abbreviation]);
        return redirect()->back()->with([
            'lang' => $selectedLanguage->abbreviation,
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
            $ride = Ride::where('id', $id)->first();
            $user = User::where('id', auth()->user()->id)->first();

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


            // Payment successful, handle booking logic here
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
                'departure' => $ride->rideDetail[0]->departure,
                'destination' => $ride->rideDetail[0]->destination,
                'price' => $ride->rideDetail[0]->price,
                'ride_detail_id' => $ride->rideDetail[0]->id
            ]);



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
                $data = ['first_name' => $ride->driver->first_name, 'id' => $booking->id, 'lang' => $selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $price, 'total_price' => $seats_amount, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
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

                // $message = "" . $title . "\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nPassenger Name: " . $user->first_name . "\nPassenger Phone Number: " . $user->phone . "\nYou have received a booking request. Please visit your ride’s page to approve or decline";
                // $title = "From ProximaRide: You have a new booking request from (" . $user->first_name . ")";
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
                    Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                    // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                }
            }

            return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $messages->booking_request_success_message ?? 'Your request has been successfully sent to the driver']);
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
            $ride = Ride::where('id', $booking->ride_id)->first();
            $user = User::where('id', auth()->user()->id)->first();

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
                    'departure' => $ride->rideDetail[0]->departure,
                    'destination' => $ride->rideDetail[0]->destination,
                    'price' => $ride->rideDetail[0]->price,
                    'ride_detail_id' => $ride->rideDetail[0]->id
                ]);

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
                    'message' =>  'Booking request from ' . $user->first_name,
                    'status' => 'request',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $newBooking->ride_detail_id,
                    'departure' => $newBooking->departure,
                    'destination' => $newBooking->destination
                ]);
                if (isset($user->email_notification) && $user->email_notification == 1) {

                    $price = $seats_amount / $seats;
                    $data = ['first_name' => $ride->driver->first_name, 'id' => $newBooking->id, 'lang' => $selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $newBooking->seats, 'booking_price' => $price, 'total_price' => $seats_amount, 'from' => $newBooking->departure, 'to' => $newBooking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
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
                    'departure' => $ride->rideDetail[0]->departure,
                    'destination' => $ride->rideDetail[0]->destination,
                    'price' => $ride->rideDetail[0]->price,
                    'ride_detail_id' => $ride->rideDetail[0]->id
                ]);

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
                    'message' =>  'Booking request from ' . $user->first_name,
                    'status' => 'request',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $newBooking->ride_detail_id,
                    'departure' => $newBooking->departure,
                    'destination' => $newBooking->destination
                ]);
                if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                    $price = $newBooking->fare / $newBooking->seats;
                    $data = ['first_name' => $ride->driver->first_name, 'id' => $newBooking->id, 'lang' => $selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $newBooking->seats, 'booking_price' => $price, 'total_price' => $seats_amount, 'from' => $newBooking->departure, 'to' => $newBooking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
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

                // $message = "" . $title . "\nTrip detail\nOrigin: " . $newBooking->departure . "\nDestination: " . $newBooking->destination . "\nDeparture date: " . $depatureDate . "\nPassenger Name: " . $user->first_name . "\nPassenger Phone Number: " . $user->phone . "\nYou have received a booking request. Please visit your ride’s page to approve or decline";
                // $title = "From ProximaRide: You have a new booking request from (" . $user->first_name . ")";
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
                    Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                    // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                }
            }

            return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $messages->booking_request_success_message ?? 'Your request has been successfully sent to the driver']);
        }

        return redirect()
            ->route('home')
            ->with('message', 'Transaction failed.');
    }

    public function updateBookingRequest($id, Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        $findRidePage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('verified_number_message', 'block_booking_message', 'add_your_phone', 'verified_number_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('verified_number_message', 'block_booking_message', 'add_your_phone', 'verified_number_message')->first();
            }
        }
        $booking = Booking::where('id', $id)->first();
        $rideDetailId = $booking->ride_detail_id;
        $ride = Ride::where('id', $booking->ride_id)->with(['rideDetail' => function ($q) use ($rideDetailId) {
            $q->where('id', $rideDetailId);
        }])->first();

        $type = FeaturesSetting::whereId($ride->payment_method)->first();
        $user = User::where('id', auth()->user()->id)->first();
        $phoneNumber = PhoneNumber::where('user_id', $user->id)->first();
        if (is_null($phoneNumber) && $type->slug == 'secured') {
            return redirect()->back()->with(['failure' => $messages->add_your_phone ?? "add phone "]);
        }
        $phoneVerification = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
        if (!$phoneVerification && $type->slug == 'secured') {
            // dd($messages->verified_number_message);
            return redirect()->back()->with(['failure' => $messages->verified_number_message ?? "verify number", 'phone' => $phoneNumber]);
        }



        $rules = [
            'agree_terms' => 'accepted|required'
        ];

        if ($ride->booking_type == "37") {
            $rules['firm_agree_terms'] = 'accepted|required';
        }

        $featuresArray = explode('=', $ride->features);
        if (in_array('1', $featuresArray)) {
            $rules['pink_ride_agree_terms'] = 'accepted|required';
        }
        if (in_array('2', $featuresArray)) {
            $rules['extra_care_ride_agree_terms'] = 'accepted|required';
        }

        $request->validate($rules);





        $taxAmt = isset($request->tax_amount) ? $request->tax_amount : 0;

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



        if ($user->block_booking == '1') {
            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $message->block_booking_message ?? null]);
        }

        $bookings = Booking::where('ride_id', $booking->ride_id)
            ->where('status', '!=', '3')
            ->where('status', '!=', '4')
            ->whereNotIn('id', [$id])
            ->get();
        $errorMsg = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();
        $seatsBooked = $bookings->sum('seats') + $request->seats;
        if ($seatsBooked > $ride->seats) {
            // return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation,'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => 'Oops, this seat is no longer available. Looks like another passenger has just booked it. We apologize for the inconvenience. Here are more rides for your route']);

            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $errorMsg->seat_unavailable_message]);
        }

        //Booking Method
        $secured_cash = null;
        $secured_cash_code = null;

        $transactions = Transaction::where('booking_id', $booking->id)->get();

        $transactionTotalPrice = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('price');
        $transactionBookingPrice = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('booking_fee');

        $transactionTaxSum = Transaction::where('booking_id', $booking->id)->where('parent_id', '0')->sum('tax_amount');

        $transactionPrice = $transactionTotalPrice + $transactionTaxSum;;
        if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
            $transactionPrice = $transactionTotalPrice - $transactionBookingPrice;
        }

        $total = $request->booking_credit + $request->seats_amount;
        $cash_payment = $total - $request->online_payment;

        if ($booking->status === '0') {
            if ($request->seats > $booking->seats) {

                if ($ride->payment_method == "33") {
                    $payable_amount = ($request->online_payment + $taxAmt) - $transactionPrice;
                } else {

                    $payable_amount = $request->online_payment - $transactionPrice;
                }
                if ($payable_amount > '0') {
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

                        if ($request->online_payment > '0') {
                            $paypalAmt = $payable_amount;
                            $order = $paypal->createOrder([
                                "intent" => "CAPTURE",
                                "purchase_units" => [
                                    [
                                        "amount" => [
                                            "currency_code" => "USD",
                                            "value" => number_format((float)$paypalAmt, 2, '.', '')
                                        ]
                                    ]
                                ],
                                "application_context" => [
                                    "cancel_url" => route('paypal.cancel'),
                                    "return_url" => route('paypal.success.update-booking_request', [
                                        'id' => $booking->id,
                                        'type' => $booking->type,
                                        'seats' => $request->seats,
                                        'seats_amount' => $request->seats_amount,
                                        'booking_credit' => $request->booking_credit,
                                        'online_payment' => $request->online_payment,
                                        'cash_payment' => $cash_payment,
                                        'total' => $total,
                                        'seats_id' => implode(',', $request->seats_id),
                                        'coffee_wall' => isset($request->coffee_wall) ? $request->coffee_wall : '0',
                                        'transactionTaxSum' => $transactionTaxSum,
                                        'ride' => $ride,
                                        'tax_amount' => isset($request->tax_amount) ? $request->tax_amount : 0,
                                        'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                        'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                                        'deduct_tax' => isset($request->deduct_tax) ? $request->deduct_tax : NULL
                                    ]),
                                ]
                            ]);
                        }

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

                                // Retrieve the selected card from the database
                                $card = Card::where('id', $request->card_id)
                                    ->where('user_id', $user->id)
                                    ->firstOrFail();

                                // Set your Stripe API key.
                                Stripe::setApiKey(env('STRIPE_SECRET'));
                                // Attach the payment method to the customer
                                $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                                $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                                if ($payable_amount > '0') {
                                    // Create a payment intent

                                    $stripePay = $payable_amount;

                                    $paymentIntent = PaymentIntent::create([
                                        'amount' => round(($stripePay * 100), 0),
                                        'currency' => 'usd',
                                        'customer' => $user->stripe_customer_id,
                                        'payment_method' => $paymentMethod->id,
                                        'off_session' => true,
                                        'confirm' => true,
                                    ]);

                                    $stripId = $paymentIntent->id;
                                }
                            }



                            // Payment successful, handle booking logic here
                            $newBooking = Booking::create([
                                'user_id' => auth()->user()->id,
                                'ride_id' => $ride->id,
                                'seats' => $request->seats,
                                'type' => $request->type,
                                'booked_on' => $currentTime,
                                'booking_credit' => $request->booking_credit,
                                'fare' => $request->seats_amount,
                                'secured_cash' => $secured_cash,
                                'tax_amount' => $taxAmt,
                                'secured_cash_code' => $secured_cash_code,
                                'expires_at' => $expiryTime,
                                'departure' => $ride->rideDetail[0]->departure,
                                'destination' => $ride->rideDetail[0]->destination,
                                'price' => $ride->rideDetail[0]->price,
                                'ride_detail_id' => $ride->rideDetail[0]->id
                            ]);

                            $ids = $request->seats_id;
                            $getSeatDetails = SeatDetail::whereIn('id', $ids)->get();
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
                            $currentBookingFee = $request->booking_credit - (isset($getBookingFeeSum) && !is_null($getBookingFeeSum) ? $getBookingFeeSum : 0);

                            if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
                                $payable_amount = $payable_amount + $currentBookingFee;
                            }

                            $transactionTaxAmt = $taxAmt - $transactionTaxSum;

                            $currentTransactionAmt = $payable_amount - $transactionTaxAmt;

                            $transaction = Transaction::create([
                                'booking_id' => $newBooking->id,
                                'type' => '1',
                                'booking_fee' => $currentBookingFee,
                                'price' => $currentTransactionAmt,
                                'stripe_id' => $stripId,
                                'coffee_from_wall' => isset($request->coffee_wall) && $request->coffee_wall == "1" ? true : false,
                                'tax_amount' => $transactionTaxAmt,
                                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                                'deduct_type' => isset($request->deduct_tax) ? $request->deduct_tax : NULL,
                            ]);

                            if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
                                $coffeeWallet = CoffeeWallet::create([
                                    'booking_id' => $newBooking->id,
                                    'ride_id' => $ride->id,
                                    'user_id' => $newBooking->user_id,
                                    'cr_amount' => $currentBookingFee,
                                ]);
                            }

                            Notification::create([
                                'ride_id' => $ride->id,
                                'posted_by' => auth()->user()->id,
                                'message' =>  'Booking request from ' . auth()->user()->first_name,
                                'status' => 'request',
                                'notification_type' => 'upcoming',
                                'ride_detail_id' => $newBooking->ride_detail_id,
                                'departure' => $newBooking->departure,
                                'destination' => $newBooking->destination
                            ]);
                            if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                                $data = ['first_name' => $ride->driver->first_name, 'id' => $booking->id, 'lang' => $selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $newBooking->seats, 'booking_price' => $newBooking->booking_credit, 'total_price' => $newBooking->fare, 'from' => $newBooking->departure, 'to' => $newBooking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'expires_at' => Carbon::parse($booking->expires_at)->format('H:i'), 'time' => $ride->time];
                                // Send booking request email
                                Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
                            }


                            return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'Your new request has been successfully sent to the driver']);
                        } catch (\Stripe\Exception\ApiErrorException $e) {
                            // Handle error
                            return redirect()->back()->with(['error' => $e->getMessage()]);
                        }
                    }
                } else {
                    $booking->update([
                        'user_id' => $user->id,
                        'ride_id' => $ride->id,
                        'seats' => $request->seats,
                        'type' => $request->type,
                        'booked_on' => $currentTime,
                        'booking_credit' => $request->booking_credit,
                        'fare' => $request->seats_amount,
                        'secured_cash' => $secured_cash,
                        'tax_amount' => $taxAmt,
                        'secured_cash_code' => $secured_cash_code,
                        'expires_at' => $expiryTime,
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

                    $notification = Notification::create([
                        'ride_id' => $ride->id,
                        'posted_by' => $user->id,
                        'message' =>  'Booking request from ' . $user->first_name,
                        'status' => 'request',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $booking->ride_detail_id,
                        'departure' => $booking->departure,
                        'destination' => $booking->destination
                    ]);

                    // Assuming $user and $fcmToken are defined
                    $fcmService = new FCMService();
                    $fcm_tokens = FCMToken::where('user_id', $ride->driver->id)->get();
                    $body = $notification->message;

                    $fcmToken = $ride->driver->mobile_fcm_token;
                    if ($fcmToken) {
                        $fcmService->sendNotification($fcmToken, $body);
                    }

                    foreach ($fcm_tokens as $fcm_token) {
                        try {
                            $fcmService->sendNotification($fcm_token->token, $body);
                        } catch (\Exception $e) {
                            Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                        }
                    }
                    if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                        $data = ['first_name' => $ride->driver->first_name, 'id' => $booking->id, 'lang' => $selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $request->seats, 'booking_price' => $request->booking_credit, 'total_price' => $request->seats_amount, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'expires_at' => Carbon::parse($booking->expires_at)->format('H:i'), 'time' => $ride->time];
                        // Send booking request email
                        Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
                    }

                    return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'You have successfully booked seat in this ride']);
                }
            } else {
                return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'You did not update your booking in this ride']);
            }
        } elseif ($booking->status === '1') {
            if ($request->seats > $booking->seats) {
                $request->validate([
                    'payment_method' => 'required',
                    'card_id' => $request->payment_method == 'credit_card' && !isset($request->gPayApplePayId) && $request->gPayApplePayId == "" ? 'required' : 'nullable',
                    'booking_credit' => 'required|max:25',
                ]);

                if ($ride->payment_method == "33") {

                    $payable_amount = ($request->online_payment + $taxAmt) - $transactionPrice;
                } else {

                    $payable_amount = $request->online_payment - $transactionPrice;
                }

                if ($payable_amount > '0') {
                    if ($request->payment_method == 'paypal') {
                        $paypal = new PayPalClient;
                        $paypal->setApiCredentials(config('paypal'));
                        $token = $paypal->getAccessToken();
                        $paypal->setAccessToken($token);

                        if ($request->online_payment > '0') {
                            $paypalPay = $payable_amount;
                            $order = $paypal->createOrder([
                                "intent" => "CAPTURE",
                                "purchase_units" => [
                                    [
                                        "amount" => [
                                            "currency_code" => "USD",
                                            "value" => number_format((float)$paypalPay, 2, '.', '')
                                        ]
                                    ]
                                ],
                                "application_context" => [
                                    "cancel_url" => route('paypal.cancel'),
                                    "return_url" => route('paypal.success.update-booking_request', [
                                        'id' => $booking->id,
                                        'type' => $request->type,
                                        'seats' => $request->seats,
                                        'seats_amount' => $request->seats_amount,
                                        'booking_credit' => $request->booking_credit,
                                        'online_payment' => $request->online_payment,
                                        'cash_payment' => $cash_payment,
                                        'total' => $total,
                                        'seats_id' => implode(',', $request->seats_id),
                                        'coffee_wall' => isset($request->coffee_wall) ? $request->coffee_wall : '0',
                                        'transactionTaxSum' => $transactionTaxSum,
                                        'ride' => $ride,
                                        'tax_amount' => isset($request->tax_amount) ? $request->tax_amount : 0,
                                        'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                        'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                                        'deduct_tax' => isset($request->deduct_tax) ? $request->deduct_tax : NULL
                                    ]),
                                ]
                            ]);
                        }

                        if (isset($order['id'])) {
                            foreach ($order['links'] as $link) {
                                if ($link['rel'] == 'approve') {
                                    return redirect()->away($link['href']);
                                }
                            }
                        }

                        return redirect()->route('paypal.cancel');
                    } elseif ($request->payment_method == 'credit_card') {

                        try {

                            $stripId = null;


                            if (isset($request->gPayApplePayId) && $request->gPayApplePayId != '') {
                                $stripId = $request->gPayApplePayId;
                            } else {
                                // Retrieve the selected card from the database
                                $card = Card::where('id', $request->card_id)
                                    ->where('user_id', $user->id)
                                    ->firstOrFail();

                                // Set your Stripe API key.
                                Stripe::setApiKey(env('STRIPE_SECRET'));
                                // Attach the payment method to the customer
                                $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                                $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                                if ($payable_amount > '0') {

                                    $stripePay = $payable_amount;
                                    // Create a payment intent
                                    $paymentIntent = PaymentIntent::create([
                                        'amount' => round(($stripePay * 100), 0),
                                        'currency' => 'usd',
                                        'customer' => $user->stripe_customer_id,
                                        'payment_method' => $paymentMethod->id,
                                        'off_session' => true,
                                        'confirm' => true,
                                    ]);

                                    $stripId = $paymentIntent->id;
                                }
                            }




                            // Payment successful, handle booking logic here
                            $booking_credit = $request->booking_credit - $booking->booking_credit;
                            $seats = $request->seats - $booking->seats;
                            $fare = $request->seats_amount - $booking->fare;

                            $booking = Booking::create([
                                'user_id' => auth()->user()->id,
                                'ride_id' => $ride->id,
                                'seats' => $seats,
                                'booked_on' => $currentTime,
                                'booking_credit' => $booking_credit,
                                'fare' => $fare,
                                'secured_cash' => $secured_cash,
                                'tax_amount' => $taxAmt,
                                'secured_cash_code' => $secured_cash_code,
                                'expires_at' => $expiryTime,
                                'departure' => $ride->rideDetail[0]->departure,
                                'destination' => $ride->rideDetail[0]->destination,
                                'price' => $ride->rideDetail[0]->price,
                                'ride_detail_id' => $ride->rideDetail[0]->id
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
                                $payable_amount = $payable_amount + $booking_credit;
                            }

                            $transactionTaxAmt = $taxAmt - $transactionTaxSum;

                            $currentTransactionAmt = $payable_amount - $transactionTaxAmt;

                            $transaction = Transaction::create([
                                'booking_id' => $booking->id,
                                'type' => '1',
                                'booking_fee' => $booking_credit,
                                'price' => $currentTransactionAmt,
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
                                    'cr_amount' => $booking_credit,
                                ]);
                            }

                            Notification::create([
                                'ride_id' => $ride->id,
                                'posted_by' => auth()->user()->id,
                                'message' =>  'Booking request from ' . $user->first_name,
                                'status' => 'request',
                                'notification_type' => 'upcoming',
                                'ride_detail_id' => $booking->ride_detail_id,
                                'departure' => $booking->departure,
                                'destination' => $booking->destination
                            ]);
                            if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                                $data = ['first_name' => $ride->driver->first_name, 'id' => $booking->id, 'lang' => $selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $booking->booking_credit, 'total_price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time, 'expires_at' => Carbon::parse($booking->expires_at)->format('H:i'),];
                                // Send booking request email
                                Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
                            }

                            return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'Your new request has been successfully sent to the driver']);
                        } catch (\Stripe\Exception\ApiErrorException $e) {
                            // Handle error
                            return redirect()->back()->with(['error' => $e->getMessage()]);
                        }
                    }
                } else {
                    $booking_credit = $request->booking_credit - $booking->booking_credit;
                    $seats = $request->seats - $booking->seats;
                    $fare = $request->seats_amount - $booking->fare;

                    $booking = Booking::create([
                        'user_id' => $user->id,
                        'ride_id' => $ride->id,
                        'seats' => $seats,
                        'booked_on' => $currentTime,
                        'booking_credit' => $booking_credit,
                        'fare' => $fare,
                        'secured_cash' => $secured_cash,
                        'tax_amount' => $taxAmt,
                        'secured_cash_code' => $secured_cash_code,
                        'expires_at' => $expiryTime,
                        'departure' => $ride->rideDetail[0]->departure,
                        'destination' => $ride->rideDetail[0]->destination,
                        'price' => $ride->rideDetail[0]->price,
                        'ride_detail_id' => $ride->rideDetail[0]->id
                    ]);

                    $transactionTaxAmt = $taxAmt - $transactionTaxSum;

                    if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
                        $newTransaction = Transaction::create([
                            'booking_id' => $booking->id,
                            'type' => '1',
                            'price' => $booking_credit,
                            'booking_fee' => $booking_credit,
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
                            'cr_amount' => $booking_credit + $transactionTaxAmt,
                        ]);
                    }

                    $notification = Notification::create([
                        'ride_id' => $ride->id,
                        'posted_by' => $user->id,
                        'message' =>  'Booking request from ' . $user->first_name,
                        'status' => 'request',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $booking->ride_detail_id,
                        'departure' => $booking->departure,
                        'destination' => $booking->destination
                    ]);

                    // Assuming $user and $fcmToken are defined
                    $fcmToken = $ride->driver->mobile_fcm_token;
                    $body = $notification->message;

                    $fcmService = new FCMService();
                    $fcm_tokens = FCMToken::where('user_id', $ride->driver->id)->get();
                    $body = $notification->message;

                    $fcmToken = $ride->driver->mobile_fcm_token;
                    if ($fcmToken) {
                        $fcmService->sendNotification($fcmToken, $body);
                    }

                    foreach ($fcm_tokens as $fcm_token) {
                        try {
                            $fcmService->sendNotification($fcm_token->token, $body);
                        } catch (\Exception $e) {
                            Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                        }
                    }
                    if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                        $data = ['first_name' => $ride->driver->first_name, 'id' => $booking->id, 'lang' => $selectedLanguage->abbreviation, 'email' => $ride->driver->email, 'secured_cash_code' => $secured_cash_code, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'passenger_email' => $user->email, 'phone' => $user->phone, 'seats' => $booking->seats, 'booking_price' => $booking->booking_credit, 'total_price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'expires_at' => Carbon::parse($booking->expires_at)->format('H:i'), 'time' => $ride->time];
                        // Send booking request email
                        Mail::to($ride->driver->email)->queue(new BookingRequestMail($data));
                    }

                    return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'You have successfully booked seat in this ride']);
                }
            } else {
                return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'You did not update your booking in this ride']);
            }
        }

        return redirect()->route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $ride->id]);
    }

    public function AcceptBookingRequest($lang = null, $id, $email)
    {
        if (!auth()->user()) {
            $user = User::where('email', $email)->first();
            $user = auth()->login($user);
        }
        $booking = Booking::with(['ride'])->whereId($id)->first();

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        if ($booking && $booking->status === '0') {
            $existingRecord = Booking::with(['ride'])->where('user_id', $booking->user_id)
                ->where('status', '1')
                ->where('ride_id', $booking->ride_id)
                ->where('id', '!=', $booking->id)
                ->first();

            if ($existingRecord) {
                // Existing record found, update it
                $existingRecord->update([
                    'seats' => DB::raw('seats + ' . $booking->seats), // Adding the seats value
                    'booking_credit' => DB::raw('booking_credit + ' . $booking->booking_credit), // Adding the booking_credit value
                    'fare' => DB::raw('fare + ' . $booking->fare), // Adding the booking_credit value
                ]);
                $existingRecord->refresh();

                $transactions = Transaction::where('booking_id', $booking->id)->get();
                foreach ($transactions as $transaction) {
                    $transaction->update([
                        'booking_id' => $existingRecord->id,
                    ]);
                }

                $coffeeWallets = CoffeeWallet::where('booking_id', $booking->id)->get();
                foreach ($coffeeWallets as $coffeeWallet) {
                    $coffeeWallet->update([
                        'booking_id' => $existingRecord->id,
                    ]);
                }

                $booking->delete();

                $notification = Notification::create([
                    'type' => 2,
                    'ride_id' => $existingRecord->ride_id,
                    'posted_to' => $existingRecord->id,
                    'posted_by' => $existingRecord->ride->added_by,
                    'message' =>  ' Booking request approved by ' . $existingRecord->ride->driver->first_name,
                    'status' => 'completed',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $existingRecord->ride_detail_id,
                    'departure' => $existingRecord->departure,
                    'destination' => $existingRecord->destination
                ]);

                $user = User::whereId($existingRecord->user_id)->first();
                // Assuming $user and $fcmToken are defined
                $fcmToken = $user->mobile_fcm_token;
                $body = $notification->message;

                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $existingRecord->user_id)->get();
                $body = $notification->message;

                $fcmToken = $existingRecord->passenger->mobile_fcm_token;
                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $body);
                }

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                    }
                }


                $bookingPrice = $existingRecord->price * $existingRecord->seats;
                if (isset($existingRecord->passenger->email_notification) && $existingRecord->passenger->email_notification == 1) {
                    $phoneNumber = PhoneNumber::where('user_id', $existingRecord->user_id)
                        // ->where('verified', '1')
                        ->where('default', '1')
                        ->first();
                    $phoneToUse = $phoneNumber ? $phoneNumber->phone : $existingRecord->passenger->phone;

                    $data = ['driver_first_name' => $booking->ride->driver->first_name, 'first_name' => $existingRecord->passenger->first_name, 'last_name' => $existingRecord->passenger->last_name, 'email' => $existingRecord->passenger->email, 'phone' => $phoneToUse, 'from' => $existingRecord?->departure, 'to' => $existingRecord?->destination, 'date' => Carbon::parse($existingRecord?->ride?->date)->format('F d, Y'), 'time' => $existingRecord?->ride?->time, 'seats' => $existingRecord?->seats, 'total_price' => $bookingPrice];
                    // Send booking request email
                    Mail::to($existingRecord->passenger->email)->queue(new AcceptBookingRequestMail($data));
                }

                $phoneNumber = PhoneNumber::where('user_id', $existingRecord->user_id)->where('verified', '1')->where('default', '1')->first();

                if (!$phoneNumber) {
                    $phoneNumber = PhoneNumber::where('user_id', $existingRecord->user_id)->where('verified', '1')->first();
                }

                if ($phoneNumber && env('APP_ENV') != 'local' && isset($existingRecord->driver->sms_notification) && $existingRecord->driver->sms_notification == 1) {
                    // Send the secured cash code via Twilio
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');

                    $twilio = new Client($sid, $token);
                    $to = $phoneNumber->phone;


                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning " . $existingRecord->passenger->first_name . ",";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $existingRecord->passenger->first_name . ",";
                    } else {
                        $title = "Good evening " . $existingRecord->passenger->first_name . ",";
                    }

                    // $depatureDate = date('d F, Y H:i:s', strtotime('' . $existingRecord->ride->date . ' ' . $existingRecord->ride->time . ''));
                    $departureTime = date('H:i:s', strtotime($existingRecord->ride->time));
                    $depatureDate = date('d F, Y', strtotime($existingRecord->ride->date));
                    $seatWords = numberToWords($booking->seats);

                    // $message = "" . $title . "\nThe driver has approved your booking request\nTrip detail\nOrigin: " . $existingRecord->departure . "\nDestination: " . $existingRecord->destination . "\nDeparture date: " . $depatureDate . "\nDriver name: " . $existingRecord->ride->driver->first_name . "\nDriver phone number: " . $existingRecord->ride->driver->phone . "\nVehicle info: " . $existingRecord->ride->make ?? '' . "," . $existingRecord->ride->year ?? '' . "," . $existingRecord->ride->modal ?? '' . "\nVehicle type: " . $existingRecord->ride->car_type . "";

                    $message = $title . "\n" . "From ProximaRide: You have approved " . $existingRecord->passenger->first_name . ". Phone: " . $existingRecord->passenger->phone . "\nRide from " . $existingRecord->departure . " to " . $existingRecord->destination . " on " . $depatureDate . " at " . $departureTime . "\nNumber of seats: " . $seatWords;
                    try {
                        $res = $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $message,
                            ]
                        );
                    } catch (\Exception  $e) {
                        Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                        // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                    }


                    if ($existingRecord->ride->payment_method == "35") {
                        $secured_cash = '1';
                        $secured_cash_code = rand(1000, 9999);

                        $existingRecord->secured_cash = $secured_cash;
                        $existingRecord->secured_cash_code = $secured_cash_code;
                        $existingRecord->save();
                        $to = $phoneNumber->phone;


                        $title = "";
                        $currentHour = date('H');
                        if ($currentHour >= 0 && $currentHour < 12) {
                            $title = "Good morning " . $existingRecord->passenger->first_name . ",";
                        } elseif ($currentHour >= 12 && $currentHour < 17) {
                            $title = "Good afternoon " . $existingRecord->passenger->first_name . ",";
                        } else {
                            $title = "Good evening " . $existingRecord->passenger->first_name . ",";
                        }

                        // $depatureDate = date('d F, Y H:i:s', strtotime('' . $existingRecord->ride->date . ' ' . $existingRecord->ride->time . ''));
                        $departureTime = date('H:i:s', strtotime($existingRecord->ride->time));
                        $depatureDate = date('d F, Y', strtotime($existingRecord->ride->date));
                        $seatWords = numberToWords($booking->seats);

                        // $message = "" . $title . "\nYour secured cash code is: $secured_cash_code\nTrip detail\nOrigin: " . $existingRecord->departure . "\nDestination: " . $existingRecord->destination . "\nDeparture date: " . $depatureDate . "\Driver name: " . $existingRecord->ride->driver->first_name . "\nDriver p    hone number: " . $existingRecord->ride->driver->phone . "\nVehicle info: " . $existingRecord->ride->make ?? '' . "," . $existingRecord->ride->year ?? '' . "," . $existingRecord->ride->modal ?? '' . "\nVehicle type: " . $existingRecord->ride->car_type . "";
                        $message = $title . "\n" . "From ProximaRide: You have approved " . $booking->passenger->first_name . ". Phone: " . $booking->passenger->phone . "\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . " at " . $departureTime . "\nNumber of seats: " . $seatWords;
                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                        } catch (\Exception  $e) {
                            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                        }
                    }
                }

                Notification::create([
                    'ride_id' => $existingRecord->ride_id,
                    'posted_by' => $existingRecord->user_id,
                    'message' =>  'You have approved ' . $existingRecord->passenger->first_name . "\n" .
                        'Seats booked: ' . numberToWords($existingRecord->seats),
                    'status' => 'completed',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $existingRecord->ride_detail_id,
                    'departure' => $existingRecord->departure,
                    'destination' => $existingRecord->destination
                ]);

                $user = auth()->user();
                // Assuming $user and $fcmToken are defined
                $fcmToken = $user->mobile_fcm_token;
                $body = $notification->message;

                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
                $body = $notification->message;

                $fcmToken = $user->mobile_fcm_token;
                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $body);
                }

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                    }
                }

                return redirect()->route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $existingRecord->departure, 'destination' => $existingRecord->destination, 'id' => $existingRecord->ride->id]);
            } else {

                $driverPhoneNumber = PhoneNumber::where('user_id', $booking->ride->added_by)
                    ->where('verified', '1')
                    ->where('default', '1')
                    ->first();

                if (!$driverPhoneNumber) {
                    $driverPhoneNumber = PhoneNumber::where('user_id', $booking->ride->added_by)
                        ->where('verified', '1')
                        ->first();
                }

                if ($driverPhoneNumber && env('APP_ENV') != 'local' && isset($booking->ride->driver->sms_notification) && $booking->ride->driver->sms_notification == 1) {
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');

                    $twilio = new Client($sid, $token);
                    $to = $driverPhoneNumber->phone;

                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning " . $booking->ride->driver->first_name . ",";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $booking->ride->driver->first_name . ",";
                    } else {
                        $title = "Good evening " . $booking->ride->driver->first_name . ",";
                    }

                    $departureTime = date('H:i:s', strtotime($booking->ride->time));
                    $depatureDate = date('d F, Y', strtotime($booking->ride->date));
                    $seatWords = numberToWords($booking->seats);
                    $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->passenger->id)
                        // ->where('verified', '1')
                        ->where('default', '1')
                        ->first();

                    $passengerPhoneToUse = $passengerPhoneNumber ? $passengerPhoneNumber->phone :  $booking->passenger->phone;

                    $message = $title . "\n" . "From ProximaRide: You have approved " . $booking->passenger->first_name . ". Phone: " . $passengerPhoneToUse . "\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . " at " . $departureTime . "\nNumber of seats: " . $seatWords;

                    try {
                        $res = $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $message,
                            ]
                        );
                    } catch (\Exception $e) {
                        Log::info('Cannot send text to driver ' . $to . ' with message: ' . $message . ' because ' . $e->getMessage());
                    }
                }

                if ($booking->ride->payment_method == "35") {
                    $secured_cash = '1';
                    $secured_cash_code = rand(1000, 9999);

                    $booking->secured_cash = $secured_cash;
                    $booking->secured_cash_code = $secured_cash_code;
                    $booking->save();

                       $notificationMessage = "Your Secured-cash payment code is: " . $secured_cash_code;
                        $securedCashNotification = Notification::create([
                            'type' => 2, // Assuming 3 is for secured-cash notifications
                            'ride_id' => $booking->ride_id,
                            'posted_to' => $booking->id,
                            'posted_by' => $booking->ride->added_by,
                            'receiver_id' => $booking->user_id,
                            'message' => $notificationMessage,
                            'status' => 'completed',
                            'notification_type' => 'secured_cash',
                            'ride_detail_id' => $booking->ride_detail_id,
                            'departure' => $booking->departure,
                            'destination' => $booking->destination
                        ]);

                        // Send push notification for secured cash code
                        $fcmService = new FCMService();
                        $fcm_tokens = FCMToken::where('user_id', $booking->user_id)->get();
                        $body = $notificationMessage;

                        $fcmToken = $booking->passenger->mobile_fcm_token;
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

                    $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->where('default', '1')->first();
                    if (!$phoneNumber) {
                        $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->first();
                    }

                    if ($phoneNumber && env('APP_ENV') != 'local' && isset($booking->passenger->sms_notification) && $booking->passenger->sms_notification == 1) {
                        $sid = env('TWILIO_ACCOUNT_SID');
                        $token = env('TWILIO_AUTH_TOKEN');
                        $from = env('TWILIO_PHONE_NUMBER');

                        $twilio = new Client($sid, $token);
                        $to = $phoneNumber->phone;
                        $title = "";
                        $currentHour = date('H');
                        if ($currentHour >= 0 && $currentHour < 12) {
                            $title = "Good morning " . $booking->passenger->first_name . ",";
                        } elseif ($currentHour >= 12 && $currentHour < 17) {
                            $title = "Good afternoon " . $booking->passenger->first_name . ",";
                        } else {
                            $title = "Good evening " . $booking->passenger->first_name . ",";
                        }

                        // $depatureDate = date('d F, Y H:i:s', strtotime('' . $booking->ride->date . ' ' . $booking->ride->time . ''));
                        $departureTime = date('H:i:s', strtotime($booking->ride->time));
                        $departureDate = date('d F, Y', strtotime($booking->ride->date));
                        $driverPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1)$2-$3", $booking->ride->driver->phone);

                        $seatWords = numberToWords($booking->seats);

                        // $message = "" . $title . "\nYour secured cash code is: $secured_cash_code\nTrip detail\nOrigin: " . $booking->ride->rideDetail[0]->departure . "\nDestination: " . $booking->ride->rideDetail[0]->destination . "\nDeparture date: " . $departureDate . "\Driver name: " . $booking->ride->driver->first_name . "\nDriver phone number: " . $booking->ride->driver->phone . "\nVehicle info: " . $booking->ride->make ?? '' . "," . $booking->ride->year ?? '' . "," . $booking->ride->modal ?? '' . "\nVehicle type: " . $booking->ride->car_type . "";
                        $message = $title . "\n" . "From ProximaRide: Your secured-cash payment code is: " . $secured_cash_code . "\n" .
                            "Give this code to your driver ONLY at the time of the ride when you meet with them.\n" .
                            "Driver name is " . $booking->ride->driver->first_name . ", phone " . $driverPhone . "\n" .
                            "Ride from " . $booking->ride->rideDetail[0]->departure . " to " . $booking->ride->rideDetail[0]->destination .
                            " on " . $departureDate . " at " . $departureTime . "\n" .
                            "Number of seats: " . ucfirst($seatWords);

                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                        } catch (\Exception  $e) {
                            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                        }
                    }

                    $driverPhoneNumber = PhoneNumber::where('user_id', $booking->ride->driver->id)
                        ->where('default', '1')
                        ->first();
                    $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $booking->ride->driver->phone;

                    $emailData = [
                        'first_name' => $booking->passenger->first_name,
                        'secured_cash_code' => $secured_cash_code,
                        'driver_first_name' => $booking->ride->driver->first_name,
                        'driver_last_name' => $booking->ride->driver->last_name,
                        'driver_phone' => $driverPhoneToUse,
                        'driver_email' => $booking->ride->driver->email,
                        'departure' => $booking->departure,
                        'destination' => $booking->destination,
                        'date' => Carbon::parse($booking->ride->date)->format('F d, Y'),
                        'time' => $booking->ride->time,
                        'seats' => $booking->seats,
                        'booking_price' => $booking->price * $booking->seats
                    ];

                    Mail::to($booking->passenger->email)->queue(new SecuredCashPaymentCodeMail($emailData));
                }

                $booking->update([
                    'status' => '1',
                    'expires_at' => null,
                ]);

                $notification = Notification::create([
                    'type' => 2,
                    'ride_id' => $booking->ride_id,
                    'posted_to' => $booking->id,
                    'posted_by' => $booking->ride->added_by,
                    'message' =>  ' Booking request approved by ' . $booking->ride->driver->first_name,
                    'status' => 'completed',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);



                $user = User::whereId($booking->user_id)->first();
                // Assuming $user and $fcmToken are defined
                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $booking->user_id)->get();
                $body = $notification->message;

                $fcmToken = $booking->passenger->mobile_fcm_token;
                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $body);
                }

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                    }
                }


                $bookingPrice = $booking->price * $booking->seats;
                $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                    // ->where('verified', '1')
                    ->where('default', '1')
                    ->first();
                $phoneToUse = $phoneNumber ? $phoneNumber->phone : $booking->passenger->phone;
                // if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {

                $data = ['driver_first_name' => $booking->ride->driver->first_name, 'driver_last_name' => $user->last_name, 'first_name' => $booking->passenger->first_name, 'last_name' => $booking->passenger->last_name, 'email' => $booking->passenger->email, 'phone' => $phoneToUse, 'from' => $booking?->departure, 'to' => $booking?->destination, 'date' => Carbon::parse($booking?->ride?->date)->format('F d, Y'), 'time' => $booking?->ride?->time, 'seats' => $booking?->seats, 'total_price' => $bookingPrice];
                // Send booking request email
                if (isset($booking->ride->driver->email_notification) && $booking->ride->driver->email_notification == 1) {
                    Mail::to($booking->ride->driver->email)->queue(new AcceptBookingRequestMail($data));
                }

                $driverPhoneNumber = PhoneNumber::where('user_id', $booking->ride->driver->id)
                    // ->where('verified', '1')
                    ->where('default', '1')
                    ->first();

                $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $booking->ride->driver->phone;

                if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {
                    $data = [
                        'first_name' => $booking->passenger->first_name,
                        'last_name' => $booking->passenger->last_name,
                        'driver_first_name' => $booking->ride->driver->first_name,
                        'driver_last_name' => $booking->ride->driver->last_name,
                        'driver_email' => $booking->ride->driver->email,
                        'driver_phone' => $driverPhoneToUse,
                        'from' => $booking?->departure,
                        'to' => $booking?->destination,
                        'date' => Carbon::parse($booking?->ride?->date)->format('F d, Y'),
                        'time' => $booking?->ride?->time,
                        'seats' => $booking?->seats,
                        'total_price' => $bookingPrice
                    ];
                    Mail::to($booking->passenger->email)->queue(new RideApprovalEmail($data));
                }


                $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->where('default', '1')->first();

                if (!$phoneNumber) {
                    $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->first();
                }

                if ($phoneNumber && env('APP_ENV') != 'local' && isset($booking->passenger->sms_notification) && $booking->passenger->sms_notification == 1) {
                    // Send the secured cash code via Twilio
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');

                    $twilio = new Client($sid, $token);
                    $to = $phoneNumber->phone;

                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning " . $booking->passenger->first_name . ",";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $booking->passenger->first_name . ",";
                    } else {
                        $title = "Good evening " . $booking->passenger->first_name . ",";
                    }

                    // $depatureDate = date('d F, Y H:i:s', strtotime('' . $booking->ride->date . ' ' . $booking->ride->time . ''));
                    $departureTime = date('H:i:s', strtotime($booking->ride->time));
                    $depatureDate = date('d F, Y', strtotime($booking->ride->date));
                    $seatWords = numberToWords($booking->seats);
                    $driverPhoneNumber = PhoneNumber::where('user_id', $booking->ride->driver->id)
                        // ->where('verified', '1')
                        ->where('default', '1')
                        ->first();

                    $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $booking->ride->driver->phone;

                    // $message = "" . $title . "\nThe driver has approved your booking request\nTrip detail\nOrigin: " . $booking->ride->departure . "\nDestination: " . $booking->ride->destination . "\nDeparture date: " . $depatureDate . "\nDriver name: " . $booking->ride->driver->first_name . "\nDriver phone number: " . $booking->ride->driver->phone . "\nVehicle info: " . $booking->ride->make ?? '' . "," . $booking->ride->year ?? '' . "," . $booking->ride->modal ?? '' . "\nVehicle type: " . $booking->ride->car_type . "";
                    $message = $title . "\n" . "From ProximaRide: Your booking request has been approved by " . $booking->ride->driver->first_name .
                        ". Phone " . $driverPhoneToUse .
                        "\nRide from " . $booking->departure .
                        " to " . $booking->destination .
                        " on " . $depatureDate .
                        " at " . $departureTime .
                        "\nNumber of seats: " . $seatWords;


                    try {
                        $res = $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $message,
                            ]
                        );
                    } catch (\Exception  $e) {
                        Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                        // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                    }
                }

                Notification::create([
                    'ride_id' => $booking->ride_id,
                    'posted_by' => $booking->user_id,
                    'message' =>  'You have approved ' . $booking->passenger->first_name . "\n" .
                        'Seats booked: ' . numberToWords($booking->seats),
                    'status' => 'completed',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);

                $user = auth()->user();
                // Assuming $user and $fcmToken are defined
                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
                $body = $notification->message;

                $fcmToken = $user->mobile_fcm_token;
                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $body);
                }

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                    }
                }
                return redirect()->route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id]);
            }
        } else {
            return 'Request expired';
        }
    }

    public function RejectBookingRequest($lang = null, $id, $email)
    {
        if (!auth()->user()) {
            $user = User::where('email', $email)->first();
            $user = auth()->login($user);
        }
        $booking = Booking::with(['passenger', 'ride'])->whereId($id)->first();
        if ($booking && $booking->status === '0') {
            $booking->update([
                'status' => '3',
                'expires_at' => null,
            ]);

            $getSeatDetails = SeatDetail::where('booking_id', $booking->id)->get();
            if (isset($getSeatDetails) && !empty($getSeatDetails)) {
                foreach ($getSeatDetails as $key => $getSeatDetail) {
                    $getSeatDetail->status = 'pending';
                    $getSeatDetail->booking_id = NULL;
                    $getSeatDetail->user_id = NULL;
                    $getSeatDetail->save();
                }
            }

            $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
            foreach ($transactions as $transaction) {
                if ($transaction) {
                    $transactionAmt = 0.0;
                    if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                        $transactionAmt = (float)$transaction->price - (float)$transaction->booking_fee;
                    } else {
                        $transactionAmt = (float)$transaction->price;
                    }

                    $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');
                    if (isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice)) {
                        $transactionAmt = $transactionAmt - $getRefundEntryPrice;
                    }

                    if ($transactionAmt > 0) {
                        $refundId = "";
                        if ($transaction->pay_by_account == 0) {
                            if ($transaction->paypal_id) {
                                $uniqueId = strtotime(date('Y-m-d H:i:s'));
                                $paypal = new PayPalClient;
                                $paypal->setApiCredentials(config('paypal'));
                                $token = $paypal->getAccessToken();
                                $paypal->setAccessToken($token);
                                $response = $paypal->refundCapturedPayment(
                                    $transaction->paypal_id,
                                    'Invoice-' . $uniqueId,
                                    $transactionAmt,
                                    'Refund issued.'
                                );
                                $refundId = isset($response['id']) ? $response['id'] : "";
                            } elseif ($transaction->stripe_id) {
                                // Set your Stripe API key
                                Stripe::setApiKey(env('STRIPE_SECRET'));

                                try {
                                    // Create a refund using the payment intent ID
                                    $refund = Refund::create([
                                        'payment_intent' => $transaction->stripe_id,
                                        'amount' => $transactionAmt * 100, // Refund amount in cents
                                    ]);
                                    $refundId = $refund->id;
                                } catch (\Stripe\Exception\ApiErrorException $e) {
                                    // Handle error
                                    return redirect()->back()->with(['error' => $e->getMessage()]);
                                }
                            }
                        } else {
                            $topUpBalance = TopUpBalance::create([
                                'booking_id' => $transaction->booking_id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transactionAmt,
                                'added_date' => date('Y-m-d'),
                            ]);
                        }

                        if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                            $bookingAmt = $transaction->booking_fee;

                            $getRefundBookingPrice = CoffeeWallet::where('booking_id', $booking->id)->where('ride_id', $booking->ride_id)->where('user_id', $booking->user_id)->sum('dr_amount');
                            if (isset($getRefundBookingPrice) && !is_null($getRefundBookingPrice)) {
                                $bookingAmt = $transaction->booking_fee - $getRefundBookingPrice;
                            }

                            if ($bookingAmt > 0) {
                                $coffeeWallet = CoffeeWallet::create([
                                    'booking_id' => $booking->id,
                                    'ride_id' => $booking->ride_id,
                                    'user_id' => $booking->user_id,
                                    'dr_amount' => $bookingAmt,
                                ]);
                            }
                        }

                        $newTransaction = Transaction::create([
                            'booking_id' => $transaction->booking_id,
                            'ride_id' => $booking->ride_id,
                            'parent_id' => $transaction->id,
                            'type' => '3',
                            'price' => $transactionAmt,
                            'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                            'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                        ]);
                    }
                }
            }
        } else {
            return 'Request expired';
        }

        $notification = Notification::create([
            'type' => 2,
            'ride_id' => $booking->ride_id,
            'posted_to' => $booking->id,
            'posted_by' => $booking->ride->added_by,
            'message' =>  'Booking request declined',
            'status' => 'reject',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination
        ]);

        $user = User::whereId($booking->user_id)->first();
        // Assuming $user and $fcmToken are defined
        $fcmService = new FCMService();
        $fcm_tokens = FCMToken::where('user_id', $booking->user_id)->get();
        $body = $notification->message;

        $fcmToken = $booking->passenger->mobile_fcm_token;
        if ($fcmToken) {
            $fcmService->sendNotification($fcmToken, $body);
        }

        foreach ($fcm_tokens as $fcm_token) {
            try {
                $fcmService->sendNotification($fcm_token->token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
            }
        }

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {

            $data = ['first_name' => $booking->passenger->first_name, 'seats' => $booking->seats, 'price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];

            // Send booking request email
            Mail::to($booking->passenger->email)->queue(new RejectBookingRequestMail($data));
        }


        $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->where('default', '1')->first();

        if (!$phoneNumber) {
            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->first();
        }

        if ($phoneNumber && env('APP_ENV') != 'local' && isset($booking->passenger->sms_notification) && $booking->passenger->sms_notification == 1) {
            // Send the secured cash code via Twilio
            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_PHONE_NUMBER');

            $twilio = new Client($sid, $token);
            $to = $phoneNumber->phone;

            $title = "";
            $currentHour = date('H');
            if ($currentHour >= 0 && $currentHour < 12) {
                $title = "Good morning " . $booking->passenger->first_name . ",";
            } elseif ($currentHour >= 12 && $currentHour < 17) {
                $title = "Good afternoon " . $booking->passenger->first_name . ",";
            } else {
                $title = "Good evening " . $booking->passenger->first_name . ",";
            }

            // $depatureDate = date('d F, Y H:i:s', strtotime('' . $booking->ride->date . ' ' . $booking->ride->time . ''));
            $departureTime = date('H:i:s', strtotime($booking->ride->time));
            $depatureDate = date('d F, Y', strtotime($booking->ride->date));

            // $message = "" . $title . "\nWe are sorry the driver did not approve your booking request\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nDriver name: " . $booking->ride->driver->first_name . "\nDriver phone number: " . $booking->ride->driver->phone . "\n";
            $message = $title . "\n" . "From ProximaRide: We are sorry to inform you that your booking request has been declined by the driver.\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . " at " . $departureTime . "\nAll payments that you have made will be refunded to you immediately";

            try {
                $res = $twilio->messages->create(
                    $to,
                    [
                        'from' => $from,
                        'body' => $message,
                    ]
                );
            } catch (\Exception  $e) {
                Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
            }
        }

        return redirect()->route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id]);
    }

    public function instantBooking($id, Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        $findRidePage = null;
        $messages = null;
        $ride = Ride::where('id', $request->id)->first();
        $type = FeaturesSetting::whereId($ride->payment_method)->first();
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

        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message_end_part', 'book_seat_message', 'block_booking_message', 'verified_number_message', 'add_your_phone')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $findRidePage = FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message_end_part', 'book_seat_message', 'block_booking_message', 'verified_number_message', 'add_your_phone')->first();
            }
        }
        $user = User::where('id', auth()->user()->id)->first();
        $phoneNumber = PhoneNumber::where('user_id', $user->id)->first();
        if (is_null($phoneNumber) && $type->slug == 'secured') {
            return redirect()->back()->with(['failure' => $messages->add_your_phone ?? 'Add your phone number']);
        }
        $phoneVerification = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
        if (!$phoneVerification && $type->slug == 'secured') {
            // dd($messages->verified_number_message);
            return redirect()->back()->with(['failure' => $messages->verified_number_message ?? 'Verify your phone number', 'phone' => $phoneNumber]);
        }






        if ($user->block_booking == '1') {
            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $message->block_booking_message ?? null]);
        }

        // Passenger gatekeeping logic for Pink Ride and Extra Care Ride (instant booking)
        $featuresArray = explode('=', $ride->features);
        $pinkRideSetting = PinkRideSetting::first();
        $folkRideSetting = FolkRideSetting::first();

        // Check if ride has Pink Ride feature (feature ID 1)
        if (in_array('1', $featuresArray)) {
            // For passengers booking Pink Rides, require government ID
            if ($pinkRideSetting && $pinkRideSetting->driver_license === '1') {
                if (empty($user->government_id) && empty($user->driver_license_upload)) {
                    return redirect()->back()->with(['failure' => 'A government-issued photo ID is required to book Pink Rides. Please upload your government ID or driver\'s license in your profile.']);
                }
            }
        }

        // Check if ride has Extra Care feature (feature ID 2)
        if (in_array('2', $featuresArray)) {
            // For passengers booking Extra Care Rides, require government ID
            if ($folkRideSetting && $folkRideSetting->driver_license === '1') {
                if (empty($user->government_id) && empty($user->driver_license_upload)) {
                    return redirect()->back()->with(['failure' => 'A government-issued photo ID is required to book Extra Care Rides. Please upload your government ID or driver\'s license in your profile.']);
                }
            }
        }

        $request->validate([
            'seats' => 'required|integer|min:1',
            'driver_message' => 'required',
        ]);

        $bookings = Booking::where('ride_id', $id)->where('status', '!=', '3')->where('status', '!=', '4')->get();
        $errorMsg = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();

        $seatsBooked = $bookings->sum('seats') + $request->seats;
        if ($seatsBooked > $ride->seats) {
            // return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation,'from' => $ride->rideDetail[0]->departure,'to' => $ride->rideDetail[0]->destination,'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => 'Oops, this seat is no longer available. Looks like another passenger has just booked it. We apologize for the inconvenience. Here are more rides for your route']);

            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $errorMsg->seat_unavailable_message]);
        }

        $taxAmt = isset($request->tax_amount) ? $request->tax_amount : 0;

        $rules = [
            'agree_terms' => 'accepted|required',
        ];

        if ($ride->booking_type == "37") {
            $rules['firm_agree_terms'] = 'accepted|required';
        }

        $featuresArray = explode('=', $ride->features);
        if (in_array('1', $featuresArray)) {
            $rules['pink_ride_agree_terms'] = 'accepted|required';
        }
        if (in_array('2', $featuresArray)) {
            $rules['extra_care_ride_agree_terms'] = 'accepted|required';
        }

        $validated = $request->validate($rules);

        if ($request->online_payment > '0') {

            $rules = [
                'agree_terms' => 'accepted|required',
                'payment_method' => 'required',
                'card_id' => $request->payment_method == 'credit_card' && !isset($request->gPayApplePayId) && $request->gPayApplePayId == "" ? 'required' : 'nullable',
                'booking_credit' => 'required|max:25',
            ];

            if ($ride->booking_type == "37") {
                $rules['firm_agree_terms'] = 'accepted|required';
            }

            $featuresArray = explode('=', $ride->features);
            if (in_array('1', $featuresArray)) {
                $rules['pink_ride_agree_terms'] = 'accepted|required';
            }
            if (in_array('2', $featuresArray)) {
                $rules['extra_care_ride_agree_terms'] = 'accepted|required';
            }

            $validated = $request->validate($rules);


            if ($request->payment_method == 'paypal') {
                if ($ride->payment_method === $findRidePage->payment_methods_option4) {
                    $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->where('default', '1')->first();

                    if (!$phoneNumber) {
                        $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
                    }

                    if (!$phoneNumber) {
                        return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $messages->verified_number_message ?? null]);
                    }
                }

                $paypal = new PayPalClient;
                $paypal->setApiCredentials(config('paypal'));
                $token = $paypal->getAccessToken();
                $paypal->setAccessToken($token);

                if ($request->online_payment > 0) {

                    $paypalPay = 0;
                    if ($request->cash_payment > 0) {
                        $paypalPay = $request->input('online_payment') + $taxAmt;
                    } else {
                        $paypalPay = $request->input('online_payment');
                    }

                    $order = $paypal->createOrder([
                        "intent" => "CAPTURE",
                        "purchase_units" => [
                            [
                                "amount" => [
                                    "currency_code" => "USD",
                                    "value" => number_format((float)$paypalPay, 2, '.', '')
                                ]
                            ]
                        ],
                        "application_context" => [
                            "cancel_url" => route('paypal.cancel'),
                            "return_url" => route('paypal.success', [
                                'id' => $ride->id,
                                'type' => $request->type,
                                'seats' => $request->seats,
                                'seats_amount' => $request->seats_amount,
                                'booking_credit' => $request->booking_credit,
                                'fare' => $request->seats_amount,
                                'online_payment' => $request->online_payment,
                                'cash_payment' => $request->cash_payment,
                                'total' => $request->total,
                                'seats_id' => implode(',', $request->seats_id),
                                'coffee_wall' => isset($request->coffee_wall) ? $request->coffee_wall : '0',
                                'transactionTaxSum' => 0,
                                'ride' => $ride,
                                'tax_amount' => isset($request->tax_amount) ? $request->tax_amount : 0,
                                'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                                'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                                'deduct_tax' => isset($request->deduct_tax) ? $request->deduct_tax : NULL
                            ]),
                        ]
                    ]);
                }

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
                    if ($ride->payment_method == "35" && $ride->booking_method == "31") {
                        $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->where('default', '1')->first();
                        if (!$phoneNumber) {
                            $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
                        }
                        if (!$phoneNumber) {
                            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $messages->verified_number_message ?? null]);
                        }

                        $secured_cash = '1';
                        $secured_cash_code = rand(1000, 9999);

                        if ($phoneNumber && env('APP_ENV') != 'local' && isset($user->sms_notification) && $user->sms_notification == 1) {
                            $sid = env('TWILIO_ACCOUNT_SID');
                            $token = env('TWILIO_AUTH_TOKEN');
                            $from = env('TWILIO_PHONE_NUMBER');

                            $twilio = new Client($sid, $token);
                            $to = $phoneNumber->phone;
                            $title = "";
                            $currentHour = date('H');
                            if ($currentHour >= 0 && $currentHour < 12) {
                                $title = "Good morning " . $user->first_name . ",";
                            } elseif ($currentHour >= 12 && $currentHour < 17) {
                                $title = "Good afternoon " . $user->first_name . ",";
                            } else {
                                $title = "Good evening " . $user->first_name . ",";
                            }

                            // $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                            $driverPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1)$2-$3", $ride->driver->phone);

                            $departureTime = date('H:i:s', strtotime($ride->time));
                            $departureDate = date('d F, Y', strtotime($ride->date));

                            $seatWords = numberToWords($request->seats);

                            // $message = "" . $title . "\nYour secured cash code is: $secured_cash_code\nTrip detail\nOrigin: " . $ride->rideDetail[0]->departure . "\nDestination: " . $ride->rideDetail[0]->destination . "\nDeparture date: " . $depatureDate . "\Driver name: " . $ride->driver->first_name . "\nDriver phone number: " . $ride->driver->phone . "\nVehicle info: " . $ride->make ?? '' . "," . $ride->year ?? '' . "," . $ride->modal ?? '' . "\nVehicle type: " . $ride->car_type . "";
                            $message = $title . "\n" . "From ProximaRide: Your secured-cash payment code is: " . $secured_cash_code . "\n" .
                                "Give this code to your driver ONLY at the time of the ride when you meet with them.\n" .
                                "Driver name is " . $ride->driver->first_name . ", phone " . $driverPhone . "\n" .
                                "Ride from " . $ride->rideDetail[0]->departure . " to " . $ride->rideDetail[0]->destination .
                                " on " . $departureDate . " at " . $departureTime . "\n" .
                                "Number of seats: " . ucfirst($seatWords);

                            try {
                                $res = $twilio->messages->create(
                                    $to,
                                    [
                                        'from' => $from,
                                        'body' => $message,
                                    ]
                                );
                            } catch (\Exception  $e) {
                                Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                            }
                        }
                    } else {
                        $secured_cash = null;
                        $secured_cash_code = null;
                    }

                    if ($request->online_payment > '0') {

                        if (isset($request->gPayApplePayId) && $request->gPayApplePayId != '') {
                            $stripId = $request->gPayApplePayId;
                        } else {

                            Stripe::setApiKey(env('STRIPE_SECRET'));
                            // Retrieve the selected card from the database
                            $card = Card::where('id', $request->card_id)
                                ->where('user_id', $user->id)
                                ->firstOrFail();


                            // Attach the payment method to the customer
                            $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                            $paymentMethod->attach(['customer' => $user->stripe_customer_id]);


                            $stripePay = 0;
                            if ($request->cash_payment > 0) {
                                $stripePay = $request->input('online_payment') + $taxAmt;
                            } else {
                                $stripePay = $request->input('online_payment');
                            }
                            // Create a payment intent
                            $paymentIntent = PaymentIntent::create([
                                'amount' => round(($stripePay * 100), 0),
                                'currency' => 'usd',
                                'customer' => $user->stripe_customer_id,
                                'payment_method' => $paymentMethod->id,
                                'off_session' => true,
                                'confirm' => true,
                            ]);
                            $stripId = $paymentIntent->id;
                        }
                    }

                    // Payment successful, handle booking logic here
                    $booking = Booking::create([
                        'user_id' => $user->id,
                        'ride_id' => $id,
                        'seats' => $request->seats,
                        'type' => $request->type,
                        'booked_on' => Carbon::now(),
                        'status' => '1',
                        'booking_credit' => $request->booking_credit,
                        'fare' => $request->seats_amount,
                        'tax_amount' => $taxAmt,
                        'secured_cash' => $secured_cash,
                        'secured_cash_code' => $secured_cash_code,
                        'departure' => $ride->rideDetail[0]->departure,
                        'destination' => $ride->rideDetail[0]->destination,
                        'price' => $ride->rideDetail[0]->price,
                        'ride_detail_id' => $ride->rideDetail[0]->id
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

                    if ($request->online_payment > '0') {
                        $onlinePayment = $request->input('online_payment');
                        if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
                            $onlinePayment = $request->input('online_payment') + $request->booking_credit;
                        }

                        if ($request->cash_payment > 0) {
                            $onlinePayment = $onlinePayment;
                        } else {
                            $onlinePayment = $onlinePayment - $taxAmt;
                        }

                        $transaction = Transaction::create([
                            'booking_id' => $booking->id,
                            'type' => '1',
                            'booking_fee' => $request->booking_credit,
                            'price' => $onlinePayment,
                            'stripe_id' => $stripId,
                            'coffee_from_wall' => isset($request->coffee_wall) && $request->coffee_wall == "1" ? true : false,
                            'tax_amount' => $taxAmt,
                            'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                            'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                            'deduct_type' => isset($request->deduct_tax) ? $request->deduct_tax : NULL,
                        ]);

                        if (isset($request->coffee_wall) && $request->coffee_wall == "1") {
                            $coffeeWallet = CoffeeWallet::create([
                                'booking_id' => $booking->id,
                                'ride_id' => $ride->id,
                                'user_id' => $booking->user_id,
                                'cr_amount' => $request->booking_credit,
                            ]);
                        }
                    }

                    $notification = Notification::create([
                        'ride_id' => $id,
                        'posted_by' => $user->id,
                        'message' => 'You have a new instant booking from ' . $user->first_name . "\n" .
                            'Seats booked: ' . numberToWords($request->seats),
                        'status' => 'completed',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $booking->ride_detail_id,
                        'departure' => $booking->departure,
                        'destination' => $booking->destination
                    ]);

                    // Check the ride first message
                    $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
                        $query->where('sender', $ride->added_by)
                            ->where('receiver', $booking->user_id);
                    })->orWhere(function ($query) use ($booking, $ride) {
                        $query->where('sender', $booking->user_id)
                            ->where('receiver', $ride->added_by);
                    })->where('ride_id', $id)->first();
                    if (empty($rideFirstMessage)) {
                        $message1 = Message::create([
                            'ride_id' => $id,
                            'receiver' => $ride->added_by,
                            'sender' => $booking->user_id,
                            'message' => $request->driver_message,
                            'redirect' => '1',
                            'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                        ]);
                    }
                    $message = Message::create([
                        'ride_id' => $id,
                        'receiver' => $ride->added_by,
                        'sender' => $booking->user_id,
                        'message' => $request->driver_message,
                        'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                    ]);

                    $fcmService = new FCMService();
                    $fcm_tokens = FCMToken::where('user_id', $ride->driver->id)->get();
                    $body = $notification->message;

                    $fcmToken = $ride->driver->mobile_fcm_token;
                    if ($fcmToken) {
                        $fcmService->sendNotification($fcmToken, $body);
                    }

                    foreach ($fcm_tokens as $fcm_token) {
                        try {
                            $fcmService->sendNotification($fcm_token->token, $body);
                        } catch (\Exception $e) {
                            Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                        }
                    }


                    $notification = Notification::create([
                        'type' => 2,
                        'ride_id' => $id,
                        'posted_to' => $booking->id,
                        'posted_by' => $ride->added_by,
                        'message' => 'Your booking details' . "\n" . 'Seats booked: ' . numberToWords($request->seats),
                        'status' => 'completed',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $booking->ride_detail_id,
                        'departure' => $booking->departure,
                        'destination' => $booking->destination
                    ]);

                    // Check the ride first message
                    $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
                        $query->where('sender', $ride->added_by)
                            ->where('receiver', $booking->user_id);
                    })->orWhere(function ($query) use ($booking, $ride) {
                        $query->where('sender', $booking->user_id)
                            ->where('receiver', $ride->added_by);
                    })->where('ride_id', $id)->first();
                    if (empty($rideFirstMessage)) {
                        $message1 = Message::create([
                            'ride_id' => $id,
                            'receiver' => $ride->added_by,
                            'sender' => $booking->user_id,
                            'message' => $request->driver_message,
                            'redirect' => '1',
                            'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                        ]);
                    }
                    $message = Message::create([
                        'ride_id' => $id,
                        'receiver' => $ride->added_by,
                        'sender' => $booking->user_id,
                        'message' => $request->driver_message,
                        'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                    ]);
                    $fcmService = new FCMService();
                    $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
                    $body = $notification->message;

                    $fcmToken = $user->mobile_fcm_token;
                    if ($fcmToken) {
                        $fcmService->sendNotification($fcmToken, $body);
                    }

                    foreach ($fcm_tokens as $fcm_token) {
                        try {
                            $fcmService->sendNotification($fcm_token->token, $body);
                        } catch (\Exception $e) {
                            Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                        }
                    }


                    $bookingPrice = $booking->price * $booking->seats;

                    // $data = ['first_name' => $ride->driver->first_name, 'passenger_first_name' => $user->first_name,'secured_cash_code' => $secured_cash_code];
                    // Mail::to($ride->driver->email)->queue(new InstantBookingMail($data));
                    if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                        $passengerPhoneNumber = PhoneNumber::where('user_id', $user->id)
                            // ->where('verified', '1')
                            ->where('default', '1')
                            ->first();

                        $passengerPhoneToUse = $passengerPhoneNumber ? $passengerPhoneNumber->phone : $user->phone;
                        $data = ['first_name' => $ride->driver->first_name, 'lang' => $selectedLanguage->abbreviation, 'origin' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'time' => $ride->time, 'seats' => $booking->seats, 'booking_price' => $booking->price, 'total_price' => $bookingPrice, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'email' => $user->email, 'phone' => $passengerPhoneToUse];
                        Mail::to($ride->driver->email)->queue(new PassengerDetailsMail($data));
                    }

                    $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                        ->where('default', '1')
                        ->first();
                    $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;
                    $data = ['first_name' => $user->first_name, 'driver_first_name' => $ride->driver->first_name, 'driver_last_name' => $ride->driver->last_name, 'gender' => $ride->driver->gender, 'email' => $ride->driver->email, 'phone' => $driverPhoneToUse, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
                    Mail::to($user->email)->queue(new DriverDetailsMail($data));

                    // $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $request->seats_amount, 'booking_credit' => $booking->booking_credit, 'online_payment' => $request->online_payment, 'cash_payment' => $request->cash_payment, 'total' => $request->total];
                    $data = [
                        'first_name' => $user->first_name,
                        'full_name' => $user->first_name . ' ' . $user->last_name,
                        'amount' => $request->total,
                        'transaction_id' => $card->random_id ?? 'N/A',
                        'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                        'payment_method' => $request->payment_method,
                        'seats' => $booking->seats,
                        'seats_amount' => $request->seats_amount,
                        'booking_credit' => $booking->booking_credit,
                        'online_payment' => $request->online_payment,
                        'cash_payment' => $request->cash_payment,
                        'total' => $request->total
                    ];
                    Mail::to($user->email)->queue(new PaymentInvoiceMail($data));

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
                            'departure' => $ride->rideDetail[0]->departure,
                            'destination' => $ride->rideDetail[0]->destination,
                            'date' => Carbon::parse($ride->date)->format('F d, Y'),
                            'time' => $ride->time,
                            'seats' => $request->seats,
                            'booking_price' => $ride->rideDetail[0]->price * $request->seats
                        ];

                        Mail::to($user->email)->queue(new SecuredCashPaymentCodeMail($emailData));
                            $notificationMessage = "Your Secured-cash payment code is: " . $secured_cash_code;
                                $securedCashNotification = Notification::create([
                                    'type' => 2,
                                    'ride_id' => $booking->ride_id,
                                    'posted_to' => $booking->id ?? null,
                                    'posted_by' => $booking->ride->added_by,
                                    'receiver_id' => $booking->user_id,
                                    'message' => $notificationMessage,
                                    'status' => 'completed',
                                    'notification_type' => 'secured_cash',
                                    'ride_detail_id' => $ride->rideDetail[0]->id,
                                    'departure' => $ride->rideDetail[0]->departure,
                                    'destination' => $ride->rideDetail[0]->destination
                                ]);

                                // Send push notification
                                $fcmService = new FCMService();
                                $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
                                $body = $notificationMessage;

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

                        $passengerPhoneNumber = PhoneNumber::where('user_id', $user->id)
                            // ->where('verified', '1')
                            ->where('default', '1')
                            ->first();

                        $passengerPhoneToUse = $passengerPhoneNumber ? $passengerPhoneNumber->phone : $user->phone;

                        // $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                        $departureTime = date('H:i:s', strtotime($ride->time));
                        $depatureDate = date('d F, Y', strtotime($ride->date));
                        $seatWords = numberToWords($booking->seats);

                        // $message = "" . $title . "\n" . $user->first_name . " has booked seat in your ride\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nPassenger phone number: " . $user->phone . "";
                        $message = $title . "\n" . "From ProximaRide: You have a new instant booking from (" . $user->first_name . "). Phone " . $passengerPhoneToUse .
                            "\nRide from " . $booking->departure .
                            " to " . $booking->destination .
                            " on " . $depatureDate .
                            " at " . $departureTime .
                            "\nNumber of seats: " . $seatWords;


                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                        } catch (\Exception  $e) {
                            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                            // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                        }
                    }

                    $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                        ->where('verified', '1')
                        ->where('default', '1')
                        ->first();

                    if (!$passengerPhoneNumber) {
                        $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                            ->where('verified', '1')
                            ->first();
                    }

                    if ($passengerPhoneNumber && env('APP_ENV') != 'local' && isset($booking->passenger->sms_notification) && $booking->passenger->sms_notification == 1) {
                        $sid = env('TWILIO_ACCOUNT_SID');
                        $token = env('TWILIO_AUTH_TOKEN');
                        $from = env('TWILIO_PHONE_NUMBER');

                        $twilio = new Client($sid, $token);
                        $to = $passengerPhoneNumber->phone;
                        $title = "";
                        $currentHour = date('H');
                        if ($currentHour >= 0 && $currentHour < 12) {
                            $title = "Good morning " . $booking->passenger->first_name . ",";
                        } elseif ($currentHour >= 12 && $currentHour < 17) {
                            $title = "Good afternoon " . $booking->passenger->first_name . ",";
                        } else {
                            $title = "Good evening " . $booking->passenger->first_name . ",";
                        }

                        $departureTime = date('H:i:s', strtotime($ride->time));
                        $departureDate = date('d F, Y', strtotime($ride->date));
                        $seatWords = numberToWords($booking->seats);

                        $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                            ->where('default', '1')
                            ->first();
                        $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;

                        $message = $title . "\n" . "From ProximaRide: You have just booked on the ride from " . $booking->departure .
                            " to " . $booking->destination .
                            " on " . $departureDate .
                            " at " . $departureTime .
                            ".\nDriver name is (" . $ride->driver->first_name .
                            ") Phone " . $driverPhoneToUse .
                            "\nNumber of seats: " . $seatWords;

                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                        } catch (\Exception $e) {
                            Log::info('Cannot send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
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
                            // foreach ($getBookings as $key => $getBooking) {
                            //     if ($messageContent == "") {
                            //         $messageContent = "" . $getBooking->passenger->first_name . "(" . $getBooking->passenger->phone . ")";
                            //     } else {
                            //         $messageContent .= "\n" . $getBooking->passenger->first_name . "(" . $getBooking->passenger->phone . ")";
                            //     }
                            // }
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

                                // $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                                $departureTime = date('H:i:s', strtotime($ride->time));
                                $departureDate = date('d F, Y', strtotime($ride->date));
                                // Build passenger list
                                $passengerList = "";
                                $counter = 1;

                                foreach ($getBookings as $booking) {
                                    $passengerPhone = $booking->passenger->phone;
                                    // Format phone number if needed - (123)456-7890
                                    $formattedPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1)$2-$3", $passengerPhone);

                                    $seatText = $booking->seats == 1 ? 'seat' : 'seats';

                                    $passengerList .= $counter . "- " . $booking->passenger->first_name .
                                        ". Phone " . $formattedPhone .
                                        ". Booked: " . $booking->seats . " " . $seatText . "\n";
                                    $counter++;
                                }

                                // $message = "" . $title . "\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nHere is your passengers’ list\n" . $messageContent . "";
                                $message = $title . "\n" . "From ProximaRide: Here is your passenger list for your ride from " .
                                    $ride->rideDetail[0]->departure . " to " . $ride->rideDetail[0]->destination .
                                    " on " . $departureDate . " at " . $departureTime . "\n" .
                                    $passengerList . "Drive safe!";

                                try {
                                    $res = $twilio->messages->create(
                                        $to,
                                        [
                                            'from' => $from,
                                            'body' => $message,
                                        ]
                                    );
                                } catch (\Exception  $e) {
                                    Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                                }
                            }
                        }
                    }


                    return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $messages->book_seat_message . ' ' . $request->seats . ' ' . $messages->book_seat_message_end_part]);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    // Handle error
                    return redirect()->back()->with(['error' => $e->getMessage()]);
                }
            }
        } else {

            if ($ride->payment_method == "35" && $ride->booking_method == "31") {
                $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->where('default', '1')->first();
                if (!$phoneNumber) {
                    $phoneNumber = PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
                }
                if (!$phoneNumber) {
                    return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $messages->verified_number_message ?? null]);
                }

                $secured_cash = '1';
                $secured_cash_code = rand(1000, 9999);

                if ($phoneNumber && env('APP_ENV') != 'local' && isset($user->sms_notification) && $user->sms_notification == 1) {
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');

                    $twilio = new Client($sid, $token);
                    $to = $phoneNumber->phone;
                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning " . $user->first_name . "";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $user->first_name . "";
                    } else {
                        $title = "Good evening " . $user->first_name . "";
                    }

                    // $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                    $driverPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1)$2-$3", $ride->driver->phone);

                    $departureTime = date('H:i:s', strtotime($ride->time));
                    $departureDate = date('d F, Y', strtotime($ride->date));

                    $seatWords = numberToWords($request->seats);

                    // $message = "" . $title . "\nYour secured cash code is: $secured_cash_code\nTrip detail\nOrigin: " . $ride->rideDetail[0]->departure . "\nDestination: " . $ride->rideDetail[0]->destination . "\nDeparture date: " . $depatureDate . "\Driver name: " . $ride->driver->first_name . "\nDriver phone number: " . $ride->driver->phone . "\nVehicle info: " . $ride->make ?? '' . "," . $ride->year ?? '' . "," . $ride->modal ?? '' . "\nVehicle type: " . $ride->car_type . "";
                    $message = $title . "\n" . "From ProximaRide: Your secured-cash payment code is: " . $secured_cash_code . "\n" .
                        "Give this code to your driver ONLY at the time of the ride when you meet with them.\n" .
                        "Driver name is " . $ride->driver->first_name . ", phone " . $driverPhone . "\n" .
                        "Ride from " . $ride->rideDetail[0]->departure . " to " . $ride->rideDetail[0]->destination .
                        " on " . $departureDate . " at " . $departureTime . "\n" .
                        "Number of seats: " . ucfirst($seatWords);

                    try {
                        $res = $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $message,
                            ]
                        );
                    } catch (\Exception  $e) {
                        Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                    }
                }
            } else {
                $secured_cash = null;
                $secured_cash_code = null;
            }

            $booking = Booking::create([
                'user_id' => $user->id,
                'ride_id' => $id,
                'seats' => $request->seats,
                'type' => $request->type,
                'booked_on' => Carbon::now(),
                'status' => '1',
                'booking_credit' => $request->booking_credit,
                'fare' => $request->seats_amount,
                'tax_amount' => $taxAmt,
                'secured_cash' => $secured_cash,
                'secured_cash_code' => $secured_cash_code,
                'departure' => $ride->rideDetail[0]->departure,
                'destination' => $ride->rideDetail[0]->destination,
                'price' => $ride->rideDetail[0]->price,
                'ride_detail_id' => $ride->rideDetail[0]->id
            ]);

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
                    'departure' => $ride->rideDetail[0]->departure,
                    'destination' => $ride->rideDetail[0]->destination,
                    'date' => Carbon::parse($ride->date)->format('F d, Y'),
                    'time' => $ride->time,
                    'seats' => $request->seats,
                    'booking_price' => $ride->rideDetail[0]->price * $request->seats
                ];

                Mail::to($user->email)->queue(new SecuredCashPaymentCodeMail($emailData));
                $notificationMessage = "Your Secured-cash payment code is: " . $secured_cash_code;
                $securedCashNotification = Notification::create([
                    'type' => 2,
                    'ride_id' => $booking->ride_id,
                    'posted_to' => $booking->id ?? null,
                    'posted_by' => $booking->ride->added_by,
                    'receiver_id' => $booking->user_id,
                    'message' => $notificationMessage,
                    'status' => 'completed',
                    'notification_type' => 'secured_cash',
                    'ride_detail_id' => $ride->rideDetail[0]->id,
                    'departure' => $ride->rideDetail[0]->departure,
                    'destination' => $ride->rideDetail[0]->destination
                ]);

                // Send push notification
                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
                $body = $notificationMessage;

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
                $transaction = Transaction::create([
                    'booking_id' => $booking->id,
                    'type' => '1',
                    'booking_fee' => $request->booking_credit,
                    'price' => $request->booking_credit,
                    'coffee_from_wall' => true,
                    'tax_amount' => $taxAmt,
                    'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                    'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                    'deduct_type' => isset($request->deduct_tax) ? $request->deduct_tax : NULL,
                ]);

                $coffeeWallet = CoffeeWallet::create([
                    'booking_id' => $booking->id,
                    'ride_id' => $ride->id,
                    'user_id' => $booking->user_id,
                    'cr_amount' => $request->booking_credit + $taxAmt,
                ]);
            }

            if (isset($request->booked_by_wallet) && $request->booked_by_wallet == "1") {
                $transaction = Transaction::create([
                    'booking_id' => $booking->id,
                    'type' => '1',
                    'booking_fee' => isset($request->coffee_wall) && $request->coffee_wall == "1" ? '0' : $request->booking_credit,
                    'price' => $request->cash_payment > 0 ? $request->booking_credit : (isset($request->coffee_wall) && $request->coffee_wall == "1" ? $request->seats_amount : $request->total - $taxAmt),
                    'pay_by_account' => true,
                    'tax_amount' => $taxAmt,
                    'tax_percentage' => isset($request->tax_percentage) ? $request->tax_percentage : 0,
                    'tax_type' => isset($request->tax_type) ? $request->tax_type : NULL,
                    'deduct_type' => isset($request->deduct_tax) ? $request->deduct_tax : NULL,
                ]);

                $topUpBalance = TopUpBalance::create([
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'cr_amount' => $request->cash_payment > 0 ? $request->booking_credit + $taxAmt : (isset($request->coffee_wall) && $request->coffee_wall == "1" ? $request->seats_amount + $taxAmt : $request->total),
                    'added_date' => date('Y-m-d'),
                ]);
            }

            $notification = Notification::create([
                'ride_id' => $id,
                'posted_by' => $user->id,
                'message' => 'You have a new instant booking from ' . $user->first_name . "\n" .
                    'Seats booked: ' . numberToWords($request->seats),
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            $fcmService = new FCMService();
            $fcm_tokens = FCMToken::where('user_id', $ride->driver->id)->get();
            $body = $notification->message;

            $fcmToken = $ride->driver->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }

            $notification = Notification::create([
                'type' => 2,
                'ride_id' => $id,
                'posted_to' => $booking->id,
                'posted_by' => $ride->added_by,
                'message' => 'Your booking details' . "\n" . 'Seats booked: ' . numberToWords($request->seats),
                'status' => 'completed',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);

            // Check the ride first message
            $rideFirstMessage = Message::where(function ($query) use ($booking, $ride) {
                $query->where('sender', $ride->added_by)
                    ->where('receiver', $booking->user_id);
            })->orWhere(function ($query) use ($booking, $ride) {
                $query->where('sender', $booking->user_id)
                    ->where('receiver', $ride->added_by);
            })->where('ride_id', $id)->first();
            if (empty($rideFirstMessage)) {
                $message1 = Message::create([
                    'ride_id' => $id,
                    'receiver' => $ride->added_by,
                    'sender' => $booking->user_id,
                    'message' => $request->driver_message,
                    'redirect' => '1',
                    'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
                ]);
            }
            $message = Message::create([
                'ride_id' => $id,
                'receiver' => $ride->added_by,
                'sender' => $booking->user_id,
                'message' => $request->driver_message,
                'ride_detail_id' => $booking->ride_detail_id != "" ? $booking->ride_detail_id : NULL
            ]);
            $fcmService = new FCMService();
            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
            $body = $notification->message;

            $fcmToken = $user->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }

            $bookingPrice = $booking->price * $booking->seats;

            // $data = ['first_name' => $ride->driver->first_name, 'passenger_first_name' => $user->first_name,'secured_cash_code' => $secured_cash_code];
            // Mail::to($ride->driver->email)->queue(new InstantBookingMail($data));
            if (isset($ride->driver->email_notification) && $ride->driver->email_notification == 1) {

                $passengerPhoneNumber = PhoneNumber::where('user_id', $user->id)
                    // ->where('verified', '1')
                    ->where('default', '1')
                    ->first();

                $passengerPhoneToUse = $passengerPhoneNumber ? $passengerPhoneNumber->phone : $user->phone;
                $data = ['first_name' => $ride->driver->first_name, 'lang' => $selectedLanguage->abbreviation, 'origin' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'time' => $ride->time, 'seats' => $booking->seats, 'booking_price' => $booking->price, 'total_price' => $bookingPrice, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'email' => $user->email, 'phone' => $passengerPhoneToUse];
                Mail::to($ride->driver->email)->queue(new PassengerDetailsMail($data));
            }

            if (isset($user->email_notification) && $user->email_notification == 1) {

                $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                    ->where('default', '1')
                    ->first();
                $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;
                $data = ['first_name' => $user->first_name, 'driver_first_name' => $ride->driver->first_name, 'driver_last_name' => $ride->driver->last_name, 'gender' => $ride->driver->gender, 'email' => $ride->driver->email, 'phone' => $driverPhoneToUse, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y'), 'time' => $ride->time];
                Mail::to($user->email)->queue(new DriverDetailsMail($data));

                $data = ['first_name' => $user->first_name, 'seats' => $booking->seats, 'seats_amount' => $request->seats_amount, 'booking_credit' => $booking->booking_credit, 'online_payment' => $request->online_payment, 'cash_payment' => $request->cash_payment, 'total' => $request->total];
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

                // $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));
                $departureTime = date('H:i:s', strtotime($ride->time));
                $depatureDate = date('d F, Y', strtotime($ride->date));
                $seatWords = numberToWords($booking->seats);

                // $message = "" . $title . "\n" . $user->first_name . " has booked seat in your ride\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nPassenger phone number: " . $user->phone . "";
                $message = $title . "\n" . "From ProximaRide: You have a new instant booking from (" . $user->first_name . "). Phone " . $user->phone .
                    "\nRide from " . $booking->departure .
                    " to " . $booking->destination .
                    " on " . $depatureDate .
                    " at " . $departureTime .
                    "\nNumber of seats: " . $seatWords;


                try {
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                } catch (\Exception  $e) {
                    Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                    // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
                }
            }

            $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                ->where('verified', '1')
                ->where('default', '1')
                ->first();

            if (!$passengerPhoneNumber) {
                $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                    ->where('verified', '1')
                    ->first();
            }

            if ($passengerPhoneNumber && env('APP_ENV') != 'local' && isset($booking->passenger->sms_notification) && $booking->passenger->sms_notification == 1) {
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_PHONE_NUMBER');

                $twilio = new Client($sid, $token);
                $to = $passengerPhoneNumber->phone;

                $title = "";
                $currentHour = date('H');
                if ($currentHour >= 0 && $currentHour < 12) {
                    $title = "Good morning " . $booking->passenger->first_name . ",";
                } elseif ($currentHour >= 12 && $currentHour < 17) {
                    $title = "Good afternoon " . $booking->passenger->first_name . ",";
                } else {
                    $title = "Good evening " . $booking->passenger->first_name . ",";
                }


                $departureTime = date('H:i:s', strtotime($ride->time));
                $departureDate = date('d F, Y', strtotime($ride->date));
                $seatWords = numberToWords($booking->seats);

                $driverPhoneNumber = PhoneNumber::where('user_id', $ride->driver->id)
                    ->where('default', '1')
                    ->first();
                $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $ride->driver->phone;

                $message = $title . "\n" . "From ProximaRide: You have just booked on the ride from " . $booking->departure .
                    " to " . $booking->destination .
                    " on " . $departureDate .
                    " at " . $departureTime .
                    ".\nDriver name is " . $ride->driver->first_name .
                    " phone " . $driverPhoneToUse .
                    "\nNumber of seats: " . $seatWords;

                try {
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                } catch (\Exception $e) {
                    Log::info('Cannot send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
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
                    // foreach ($getBookings as $key => $getBooking) {
                    //     if ($messageContent == "") {
                    //         $messageContent = "" . $getBooking->passenger->first_name . "(" . $getBooking->passenger->phone . ")";
                    //     } else {
                    //         $messageContent .= "\n" . $getBooking->passenger->first_name . "(" . $getBooking->passenger->phone . ")";
                    //     }
                    // }
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


                        $departureTime = date('H:i:s', strtotime($ride->time));
                        $departureDate = date('d F, Y', strtotime($ride->date));

                        // Build passenger list
                        $passengerList = "";
                        $counter = 1;

                        foreach ($getBookings as $booking) {
                            $passengerPhone = $booking->passenger->phone;
                            // Format phone number if needed - (123)456-7890
                            $formattedPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1)$2-$3", $passengerPhone);

                            $seatText = $booking->seats == 1 ? 'seat' : 'seats';

                            $passengerList .= $counter . "- " . $booking->passenger->first_name .
                                ". Phone " . $formattedPhone .
                                ". Booked: " . $booking->seats . " " . $seatText . "\n";
                            $counter++;
                        }

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

                        // $message = "" . $title . "\nTrip detail\nOrigin: " . $booking->ride->departure . "\nDestination: " . $booking->ride->destination . "\nDeparture date: " . $depatureDate . "\nHere is your passengers’ list\n" . $messageContent . "";
                        $message = $title . "\n" . "From ProximaRide: Here is your passenger list for your ride from " .
                            $ride->rideDetail[0]->departure . " to " . $ride->rideDetail[0]->destination .
                            " on " . $departureDate . " at " . $departureTime . "\n" .
                            $passengerList . "Drive safe!";

                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                        } catch (\Exception  $e) {
                            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                        }
                    }
                }
            }
            return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $messages->book_seat_message]);
        }
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
            $ride = Ride::where('id', $id)->first();
            $user = User::where('id', auth()->user()->id)->first();

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
                    return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $ride->rideDetail[0]->departure, 'to' => $ride->rideDetail[0]->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $messages->verified_number_message ?? null]);
                }

                $secured_cash = '1';
                $secured_cash_code = rand(1000, 9999);

                if ($phoneNumber && env('APP_ENV') != 'local' && isset($user->sms_notification) && $user->sms_notification == 1) {
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');

                    $twilio = new Client($sid, $token);
                    $to = $phoneNumber->phone;
                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning " . $user->first_name . "";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $user->first_name . "";
                    } else {
                        $title = "Good evening " . $user->first_name . "";
                    }

                    $depatureDate = date('d F, Y H:i:s', strtotime('' . $ride->date . ' ' . $ride->time . ''));

                    $message = "" . $title . "\nYour secured cash code is: $secured_cash_code\nTrip detail\nOrigin: " . $ride->rideDetail[0]->departure . "\nDestination: " . $ride->rideDetail[0]->destination . "\nDeparture date: " . $depatureDate . "\Driver name: " . $ride->driver->first_name . "\nDriver phone number: " . $ride->driver->phone . "\nVehicle info: " . $ride->make ?? '' . "," . $ride->year ?? '' . "," . $ride->modal ?? '' . "\nVehicle type: " . $ride->car_type . "";

                    try {
                        $res = $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $message,
                            ]
                        );
                    } catch (\Exception  $e) {
                        Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                    }
                }
            } else {
                $secured_cash = null;
                $secured_cash_code = null;
            }


            // Payment successful, handle booking logic here
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
                'departure' => $ride->rideDetail[0]->departure,
                'destination' => $ride->rideDetail[0]->destination,
                'price' => $ride->rideDetail[0]->price,
                'ride_detail_id' => $ride->rideDetail[0]->id
            ]);

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
                    'departure' => $ride->rideDetail[0]->departure,
                    'destination' => $ride->rideDetail[0]->destination,
                    'date' => Carbon::parse($ride->date)->format('F d, Y'),
                    'time' => $ride->time,
                    'seats' => $request->seats,
                    'booking_price' => $ride->rideDetail[0]->price * $request->seats
                ];

                Mail::to($user->email)->queue(new SecuredCashPaymentCodeMail($emailData));
                $notificationMessage = "Your Secured-cash payment code is: " . $secured_cash_code;
                $securedCashNotification = Notification::create([
                    'type' => 2,
                    'ride_id' => $booking->ride_id,
                    'posted_to' => $booking->id ?? null,
                    'posted_by' => $booking->ride->added_by,
                    'receiver_id' => $booking->user_id,
                    'message' => $notificationMessage,
                    'status' => 'completed',
                    'notification_type' => 'secured_cash',
                    'ride_detail_id' => $ride->rideDetail[0]->id,
                    'departure' => $ride->rideDetail[0]->departure,
                    'destination' => $ride->rideDetail[0]->destination
                ]);

                // Send push notification
                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
                $body = $notificationMessage;

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

                $data = ['first_name' => $ride->driver->first_name, 'lang' => $selectedLanguage->abbreviation, 'origin' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'time' => $ride->time, 'seats' => $booking->seats, 'booking_price' => $booking->price, 'total_price' => $bookingPrice, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'email' => $user->email, 'phone' => $user->phone];
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
                    Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

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
                            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                        }
                    }
                }
            }

            return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'You have successfully booked seat in this ride']);
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
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message', 'block_booking_message', 'add_your_phone', 'verified_number_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('book_seat_message', 'block_booking_message', 'add_your_phone', 'verified_number_message')->first();
            }
        }
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
            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $message->block_booking_message ?? null]);
        }

        $bookings = Booking::where('ride_id', $booking->ride_id)
            ->where('status', '!=', '3')
            ->where('status', '!=', '4')
            ->whereNotIn('id', [$id])
            ->get();
        $errorMsg = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->first();

        $seatsBooked = $bookings->sum('seats') + $request->seats;
        if ($seatsBooked > $ride->seats) {
            // return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation,'from' => $booking->departure,'to' => $booking->destination,'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => 'Oops, this seat is no longer available. Looks like another passenger has just booked it. We apologize for the inconvenience. Here are more rides for your route']);

            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($ride->date)->format('F d, Y')])->with(['failure' => $errorMsg->seat_unavailable_message]);
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
            }

            $featuresArray = explode('=', $ride->features);
            if (in_array('1', $featuresArray)) {
                $rules['pink_ride_agree_terms'] = 'accepted|required';
            }
            if (in_array('2', $featuresArray)) {
                $rules['extra_care_ride_agree_terms'] = 'accepted|required';
            }

            $validated = $request->validate($rules);

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
                                    "currency_code" => "USD",
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
                            Stripe::setApiKey(env('STRIPE_SECRET'));

                            $stripePay = $payable_amount;
                            // Attach the payment method to the customer
                            $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                            $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                            // Create a payment intent
                            $paymentIntent = PaymentIntent::create([
                                'amount' => round(($stripePay * 100), 0),
                                'currency' => 'usd',
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

                        return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $message->book_seat_message]);
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

            return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $message->book_seat_message]);
        } elseif ($request->seats <= $booking->seats) {
            return redirect()->route('search_ride', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'You did not update your booking in this ride']);
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
            $ride = Ride::where('id', $booking->ride_id)->first();
            $user = User::where('id', auth()->user()->id)->first();

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

                $data = ['first_name' => $ride->driver->first_name, 'lang' => $selectedLanguage->abbreviation, 'origin' => $booking->departure, 'destination' => $booking->destination, 'date' => $ride->date, 'time' => $ride->time, 'seats' => $booking->seats, 'booking_price' => $Price, 'total_price' => $fare, 'passenger_first_name' => $user->first_name, 'passenger_last_name' => $user->last_name, 'gender' => $user->gender, 'email' => $user->email, 'phone' => $user->phone];
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
                    Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

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
                            Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                        }
                    }
                }
            }

            return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => 'You have successfully booked seat in this ride']);
        }

        return redirect()
            ->route('home')
            ->with('message', 'Transaction failed.');
    }

    public function updateCancelBooking($id, Request $request)
    {
        $user_id = auth()->user()->id;
        $setting = SiteSetting::first();
        $payoutAmt = 0;
        $monthsAgo = Carbon::now()->subMonths($setting->booking_cancel_duration)->setTimezone('UTC');;

        $cancellationCount = CancellationHistory::where('user_id', $user_id)
            ->where('created_at', '>=', $monthsAgo)
            ->where('type', 'driver')

            ->count();


        $booking = Booking::where('id', $id)->first();
        $ride = Ride::where('id', $booking->ride_id)->first();
        $getSetting = SiteSetting::first();

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_booking_message')->first();
            $limitExceed = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_cancellation_limit_exceed')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_booking_message')->first();
            $limitExceed = BookingPageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_cancellation_limit_exceed')->first();
        }
        if ($cancellationCount >= $setting->booking_cancel_limit) {
            return redirect()->back()->with(['failure' => $limitExceed->booking_cancellation_limit_exceed ?? "Booking cancellation limit exceeded"]);
        }
        $customMessages = [
            'max' => 'The :attribute may not be greater than :max characters',
        ];

        $request->validate([
            'booking_credit' => 'required|max:25',
            'message' => 'required'
        ], $customMessages);

        $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
        $bookingDateTime = Carbon::parse($booking->booked_on);
        $hoursDifference = $rideDateTime->diffInHours($bookingDateTime);

        $originalSeats = $booking->seats;
        $updatedSeats = $booking->seats - $request->seats;
        $updatedBookingCredit = $updatedSeats * ($booking->booking_credit / $booking->seats);
        $updatedFare = $updatedSeats * ($booking->fare / $booking->seats);

        $type = FeaturesSetting::whereId($booking->type)->first();
        if ($type->slug === 'firm') {
            $transactions = Transaction::where('booking_id', $booking->id)
                ->where('type', '1')
                ->get();

            $totalPrice = $transactions->sum('price');
            $refundAmount = $request->seats * ($booking->fare / $booking->seats);
            $refundTotalAmount = $request->seats * ($booking->fare / $booking->seats);
            $refundTotalBookingFee = $request->seats * ($booking->booking_credit / $booking->seats);

            // Step 2: Process each transaction for the refund
            foreach ($transactions as $transaction) {

                $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');

                if (isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == ((float)$transaction->price - (float)$transaction->booking_fee)) {
                } else {
                    $transactionAmount = ((float)$transaction->price - (float)$transaction->booking_fee);

                    if ($refundAmount <= 0) {
                        break; // No need to process further if refund is already completed
                    }

                    // Check if the current transaction can cover the remaining refund amount
                    if ($transactionAmount >= $refundAmount) {

                        $newTransaction = Transaction::create([
                            'booking_id' => $transaction->booking_id,
                            'ride_id' => $booking->ride_id,
                            'parent_id' => $transaction->id,
                            'type' => '3',
                            'price' => $refundAmount,
                        ]);
                        $refundAmount = 0; // Refund is completed
                        break;
                    } else {

                        $newTransaction = Transaction::create([
                            'booking_id' => $transaction->booking_id,
                            'ride_id' => $booking->ride_id,
                            'parent_id' => $transaction->id,
                            'type' => '3',
                            'price' => $transactionAmount,
                        ]);

                        $refundAmount -= $transactionAmount; // Reduce the remaining refund amount
                    }
                }
            }
            //Add Payout Data

            $getPayout = Payout::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)->first();
            if (isset($getPayout) && !is_null($getPayout)) {
            } else {
                $getPayout = new Payout();
            }


            if (isset($getSetting->booking_fee_give_to_driver) && $getSetting->booking_fee_give_to_driver == 1) {
                $payoutAmt = $refundTotalAmount + $refundTotalBookingFee;
            } else {
                $payoutAmt = $refundTotalAmount;
            }


            $deduct_tax = $tax_type = "";
            $tax = 0;
            $taxAmt = 0;


            if (isset($getSetting) && !empty($getSetting)) {
                if (isset($getSetting->deduct_tax) && $getSetting->deduct_tax == "deduct_from_driver") {
                    $deduct_tax = $getSetting->deduct_tax;
                    $tax_type = $getSetting->tax_type;
                    if (isset($getSetting->tax_type) && $getSetting->tax_type == "state_wise_tax") {
                        $locationBeforeComma = explode(',', $booking->departure);
                        $getFromState = City::with('state:id,tax')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ', ['%' . $locationBeforeComma[0] . '%'])->first();
                        if (isset($getFromState) && !empty($getFromState)) {
                            $tax = $getFromState->state->tax;
                        }
                    } else {
                        $tax = $getSetting->tax;
                    }

                    $taxAmt = round((($payoutAmt * $tax) / 100), 2);
                    $payoutAmt = $payoutAmt - $taxAmt;
                }
            }
            if (isset($getPayout->amount)) {
                $payoutAmt = $getPayout->amount + $payoutAmt;
            }

            $rideDateTime = Carbon::parse("$ride->completed_date $ride->completed_time");

            $getPayout->ride_id = $booking->ride_id;
            $getPayout->booking_id = $booking->id;
            $getPayout->user_id = $ride->added_by;
            $getPayout->amount = $payoutAmt;
            $getPayout->available_date = $rideDateTime;
            $getPayout->status = "pending";
            $getPayout->tax_amount = $taxAmt;
            $getPayout->tax_percentage = isset($tax) && $tax != 0 ? $tax : 0;
            $getPayout->tax_type = isset($tax_type) && $tax_type != "" ? $tax_type : NULL;
            $getPayout->deduct_type = isset($deduct_tax) && $deduct_tax != "" ? $deduct_tax : NULL;
            $getPayout->save();
        } elseif ($type->slug === 'standard') {

            $transactions = Transaction::where('booking_id', $booking->id)
                ->where('type', '1')
                ->get();

            $totalPrice = $transactions->sum('price');
            $refundAmount = $request->seats * ($booking->fare / $booking->seats);
            $refundTotalAmount = $request->seats * ($booking->fare / $booking->seats);
            $getSeatBookingPrice = $booking->booking_credit / $booking->seats;
            $refundTotalBookingFee = $request->seats * ($booking->booking_credit / $booking->seats);

            if ($hoursDifference > 48) {
                foreach ($transactions as $transaction) {
                    $checkPrice = 0.0;
                    $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');
                    $checkPrice = (float)$transaction->price;

                    if (isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == $checkPrice) {
                    } else {
                        $transactionAmount = (float)$transaction->price;

                        if ($refundAmount <= 0) {
                            break; // No need to process further if refund is already completed
                        }

                        // Check if the current transaction can cover the remaining refund amount

                        $refundId = "";
                        $totalBookingFee = $getSeatBookingPrice * $request->seats;
                        $refundAmount = $refundAmount + $totalBookingFee;
                        if ($transactionAmount >= $refundAmount) {
                            if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                                if ($transaction->booking_fee >= $totalBookingFee) {
                                    $coffeeWallet = CoffeeWallet::create([
                                        'booking_id' => $booking->id,
                                        'ride_id' => $ride->id,
                                        'user_id' => $booking->user_id,
                                        'dr_amount' => $totalBookingFee,
                                    ]);
                                    $refundAmount = $refundAmount - $totalBookingFee;
                                } else {
                                    $coffeeWallet = CoffeeWallet::create([
                                        'booking_id' => $booking->id,
                                        'ride_id' => $booking->ride_id,
                                        'user_id' => $booking->user_id,
                                        'dr_amount' => $transaction->booking_fee,
                                    ]);
                                    $refundAmount = $refundAmount - $transaction->booking_fee;
                                }
                            }

                            if ($transaction->pay_by_account == 0) {
                                if ($transaction->paypal_id) {

                                    try {
                                        $uniqueId = strtotime(date('Y-m-d H:i:s'));
                                        $paypal = new PayPalClient;
                                        $paypal->setApiCredentials(config('paypal'));
                                        $token = $paypal->getAccessToken();
                                        $paypal->setAccessToken($token);
                                        $response = $paypal->refundCapturedPayment(
                                            $transaction->paypal_id,
                                            'Invoice-' . $transaction->paypal_id,
                                            $refundAmount,
                                            'Refund issued.'
                                        );

                                        $refundId = isset($response['id']) ? $response['id'] : "";
                                    } catch (\PayPal\Exception\PayPalConnectionException $e) {
                                        $errorData = json_decode($e->getData(), true);
                                        Log::error("PayPal error: " . $errorData['message']);
                                    }
                                } elseif ($transaction->stripe_id) {
                                    // Set your Stripe API key
                                    Stripe::setApiKey(env('STRIPE_SECRET'));

                                    try {
                                        // Create a refund using the payment intent ID
                                        $refund = Refund::create([
                                            'payment_intent' => $transaction->stripe_id,
                                            'amount' => $refundAmount * 100, // Refund amount in cents
                                        ]);

                                        $refundId = $refund->id;
                                    } catch (\Stripe\Exception\ApiErrorException $e) {
                                    }
                                }
                            } else {
                                $topUpBalance = TopUpBalance::create([
                                    'booking_id' => $transaction->booking_id,
                                    'user_id' => $booking->user_id,
                                    'dr_amount' => $refundAmount,
                                    'added_date' => date('Y-m-d'),
                                ]);
                            }

                            $newTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $refundAmount,
                                'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                            ]);
                            $refundAmount = 0; // Refund is completed
                            break;
                        } else {
                            if ($transaction->pay_by_account == 0) {
                                if ($transaction->paypal_id) {
                                    if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                                        if ($transaction->booking_fee >= $totalBookingFee) {
                                            $coffeeWallet = CoffeeWallet::create([
                                                'booking_id' => $booking->id,
                                                'ride_id' => $ride->id,
                                                'user_id' => $booking->user_id,
                                                'dr_amount' => $totalBookingFee,
                                            ]);
                                            $refundAmount = $refundAmount - $totalBookingFee;
                                        } else {
                                            $coffeeWallet = CoffeeWallet::create([
                                                'booking_id' => $booking->id,
                                                'ride_id' => $booking->ride_id,
                                                'user_id' => $booking->user_id,
                                                'dr_amount' => $transaction->booking_fee,
                                            ]);
                                            $refundAmount = $refundAmount - $transaction->booking_fee;
                                        }
                                    }

                                    try {
                                        $uniqueId = strtotime(date('Y-m-d H:i:s'));
                                        $paypal = new PayPalClient;
                                        $paypal->setApiCredentials(config('paypal'));
                                        $token = $paypal->getAccessToken();
                                        $paypal->setAccessToken($token);
                                        $response = $paypal->refundCapturedPayment(
                                            $transaction->paypal_id,
                                            'Invoice-' . $transaction->paypal_id,
                                            $transactionAmount,
                                            'Refund issued.'
                                        );

                                        $refundId = isset($response['id']) ? $response['id'] : "";
                                    } catch (\PayPal\Exception\PayPalConnectionException $e) {
                                        $errorData = json_decode($e->getData(), true);
                                        Log::error("PayPal error: " . $errorData['message']);
                                    }
                                } elseif ($transaction->stripe_id) {
                                    // Set your Stripe API key
                                    Stripe::setApiKey(env('STRIPE_SECRET'));

                                    try {
                                        // Create a refund using the payment intent ID
                                        $refund = Refund::create([
                                            'payment_intent' => $transaction->stripe_id,
                                            'amount' => $transactionAmount * 100, // Refund amount in cents
                                        ]);

                                        $refundId = $refund->id;
                                    } catch (\Stripe\Exception\ApiErrorException $e) {
                                    }
                                }
                            } else {
                                $topUpBalance = TopUpBalance::create([
                                    'booking_id' => $transaction->booking_id,
                                    'user_id' => $booking->user_id,
                                    'dr_amount' => $transactionAmount,
                                    'added_date' => date('Y-m-d'),
                                ]);
                            }

                            $newTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $transactionAmount,
                                'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                            ]);

                            $refundAmount -= $transactionAmount; // Reduce the remaining refund amount
                        }
                    }
                }
            } elseif ($hoursDifference >= 12 && $hoursDifference <= 48) {

                $passengerAndDriverRefundAmt = $refundAmount * 0.5;
                $passengerAndDriverRefundBookingFee = $refundTotalBookingFee * 0.5;


                foreach ($transactions as $transaction) {

                    $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');

                    if (isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == ((float)$transaction->price - (float)$transaction->booking_fee)) {
                    } else {
                        $transactionAmount = ((float)$transaction->price - (float)$transaction->booking_fee);

                        if ($refundAmount <= 0) {
                            break; // No need to process further if refund is already completed
                        }

                        // Check if the current transaction can cover the remaining refund amount

                        $refundId = "";
                        if ($transactionAmount >= $refundAmount) {

                            if ($transaction->pay_by_account == 0) {
                                if ($transaction->paypal_id) {

                                    try {
                                        $uniqueId = strtotime(date('Y-m-d H:i:s'));
                                        $paypal = new PayPalClient;
                                        $paypal->setApiCredentials(config('paypal'));
                                        $token = $paypal->getAccessToken();
                                        $paypal->setAccessToken($token);
                                        $response = $paypal->refundCapturedPayment(
                                            $transaction->paypal_id,
                                            'Invoice-' . $transaction->paypal_id,
                                            $passengerAndDriverRefundAmt,
                                            'Refund issued.'
                                        );

                                        $refundId = isset($response['id']) ? $response['id'] : "";
                                    } catch (\PayPal\Exception\PayPalConnectionException $e) {
                                        $errorData = json_decode($e->getData(), true);
                                        Log::error("PayPal error: " . $errorData['message']);
                                    }
                                } elseif ($transaction->stripe_id) {
                                    // Set your Stripe API key
                                    Stripe::setApiKey(env('STRIPE_SECRET'));

                                    try {
                                        // Create a refund using the payment intent ID
                                        $refund = Refund::create([
                                            'payment_intent' => $transaction->stripe_id,
                                            'amount' => $passengerAndDriverRefundAmt * 100, // Refund amount in cents
                                        ]);

                                        $refundId = $refund->id;
                                    } catch (\Stripe\Exception\ApiErrorException $e) {
                                    }
                                }
                            } else {
                                $topUpBalance = TopUpBalance::create([
                                    'booking_id' => $transaction->booking_id,
                                    'user_id' => $booking->user_id,
                                    'dr_amount' => $passengerAndDriverRefundAmt,
                                    'added_date' => date('Y-m-d'),
                                ]);
                            }

                            //Passenger Entry
                            $passengerTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $passengerAndDriverRefundAmt,
                                'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                            ]);
                            //Driver Entry
                            $driverTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $passengerAndDriverRefundAmt
                            ]);
                            $refundAmount = 0; // Refund is completed
                            break;
                        } else {

                            $passengerAndDriverRefundAmt = $transactionAmount * 0.5;

                            if ($transaction->pay_by_account == 0) {
                                if ($transaction->paypal_id) {

                                    try {
                                        $uniqueId = strtotime(date('Y-m-d H:i:s'));
                                        $paypal = new PayPalClient;
                                        $paypal->setApiCredentials(config('paypal'));
                                        $token = $paypal->getAccessToken();
                                        $paypal->setAccessToken($token);
                                        $response = $paypal->refundCapturedPayment(
                                            $transaction->paypal_id,
                                            'Invoice-' . $transaction->paypal_id,
                                            $passengerAndDriverRefundAmt,
                                            'Refund issued.'
                                        );

                                        $refundId = isset($response['id']) ? $response['id'] : "";
                                    } catch (\PayPal\Exception\PayPalConnectionException $e) {
                                        $errorData = json_decode($e->getData(), true);
                                        Log::error("PayPal error: " . $errorData['message']);
                                    }
                                } elseif ($transaction->stripe_id) {
                                    // Set your Stripe API key
                                    Stripe::setApiKey(env('STRIPE_SECRET'));

                                    try {
                                        // Create a refund using the payment intent ID
                                        $refund = Refund::create([
                                            'payment_intent' => $transaction->stripe_id,
                                            'amount' => $passengerAndDriverRefundAmt * 100, // Refund amount in cents
                                        ]);

                                        $refundId = $refund->id;
                                    } catch (\Stripe\Exception\ApiErrorException $e) {
                                    }
                                }
                            } else {
                                $topUpBalance = TopUpBalance::create([
                                    'booking_id' => $transaction->booking_id,
                                    'user_id' => $booking->user_id,
                                    'dr_amount' => $passengerAndDriverRefundAmt,
                                    'added_date' => date('Y-m-d'),
                                ]);
                            }

                            //Passenger Transction
                            $passengerTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $passengerAndDriverRefundAmt,
                                'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                                'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                            ]);

                            //Driver Transction
                            $driverTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $passengerAndDriverRefundAmt
                            ]);

                            $refundAmount -= $transactionAmount; // Reduce the remaining refund amount
                        }
                    }
                }

                //Add Payout Data

                $getPayout = Payout::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)->first();
                if (isset($getPayout) && !is_null($getPayout)) {
                } else {
                    $getPayout = new Payout();
                }

                if (isset($getSetting->booking_fee_give_to_driver) && $getSetting->booking_fee_give_to_driver == 1) {
                    $payoutAmt = $passengerAndDriverRefundAmt + $passengerAndDriverRefundBookingFee;
                } else {
                    $payoutAmt = $passengerAndDriverRefundAmt;
                }

                $deduct_tax = $tax_type = "";
                $tax = 0;
                $taxAmt = 0;



                if (isset($getSetting) && !empty($getSetting)) {
                    if (isset($getSetting->deduct_tax) && $getSetting->deduct_tax == "deduct_from_driver") {
                        $deduct_tax = $getSetting->deduct_tax;
                        $tax_type = $getSetting->tax_type;
                        if (isset($getSetting->tax_type) && $getSetting->tax_type == "state_wise_tax") {
                            $locationBeforeComma = explode(',', $booking->departure);
                            $getFromState = City::with('state:id,tax')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ', ['%' . $locationBeforeComma[0] . '%'])->first();
                            if (isset($getFromState) && !empty($getFromState)) {
                                $tax = $getFromState->state->tax;
                            }
                        } else {
                            $tax = $getSetting->tax;
                        }

                        $taxAmt = round((($payoutAmt * $tax) / 100), 2);
                        $payoutAmt = $payoutAmt - $taxAmt;
                    }
                }

                if (isset($getPayout->amount)) {
                    $payoutAmt = $getPayout->amount + $payoutAmt;
                }

                $rideDateTime = Carbon::parse("$ride->completed_date $ride->completed_time");

                $getPayout->ride_id = $booking->ride_id;
                $getPayout->booking_id = $booking->id;
                $getPayout->user_id = $ride->added_by;
                $getPayout->amount = $payoutAmt;
                $getPayout->available_date = $rideDateTime;
                $getPayout->status = "pending";
                $getPayout->tax_amount = $taxAmt;
                $getPayout->tax_percentage = isset($tax) && $tax != 0 ? $tax : 0;
                $getPayout->tax_type = isset($tax_type) && $tax_type != "" ? $tax_type : NULL;
                $getPayout->deduct_type = isset($deduct_tax) && $deduct_tax != "" ? $deduct_tax : NULL;
                $getPayout->save();
            } elseif ($hoursDifference < 12) {
                foreach ($transactions as $transaction) {

                    $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');

                    if (isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && $getRefundEntryPrice == ((float)$transaction->price - (float)$transaction->booking_fee)) {
                    } else {
                        $transactionAmount = ((float)$transaction->price - (float)$transaction->booking_fee);

                        if ($refundAmount <= 0) {
                            break; // No need to process further if refund is already completed
                        }

                        // Check if the current transaction can cover the remaining refund amount
                        if ($transactionAmount >= $refundAmount) {

                            $newTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $refundAmount,
                            ]);
                            $refundAmount = 0; // Refund is completed
                            break;
                        } else {

                            $newTransaction = Transaction::create([
                                'booking_id' => $transaction->booking_id,
                                'ride_id' => $booking->ride_id,
                                'parent_id' => $transaction->id,
                                'type' => '3',
                                'price' => $transactionAmount,
                            ]);

                            $refundAmount -= $transactionAmount; // Reduce the remaining refund amount
                        }
                    }
                }
                //Add Payout Data

                $getPayout = Payout::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)->first();
                if (isset($getPayout) && !is_null($getPayout)) {
                } else {
                    $getPayout = new Payout();
                }

                if (isset($getSetting->booking_fee_give_to_driver) && $getSetting->booking_fee_give_to_driver == 1) {
                    $payoutAmt = $refundTotalAmount + $refundTotalBookingFee;
                } else {
                    $payoutAmt = $refundTotalAmount;
                }



                $deduct_tax = $tax_type = "";
                $tax = 0;
                $taxAmt = 0;

                if (isset($getSetting) && !empty($getSetting)) {
                    if (isset($getSetting->deduct_tax) && $getSetting->deduct_tax == "deduct_from_driver") {
                        $deduct_tax = $getSetting->deduct_tax;
                        $tax_type = $getSetting->tax_type;
                        if (isset($getSetting->tax_type) && $getSetting->tax_type == "state_wise_tax") {
                            $locationBeforeComma = explode(',', $booking->departure);
                            $getFromState = City::with('state:id,tax')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ', ['%' . $locationBeforeComma[0] . '%'])->first();
                            if (isset($getFromState) && !empty($getFromState)) {
                                $tax = $getFromState->state->tax;
                            }
                        } else {
                            $tax = $getSetting->tax;
                        }

                        $taxAmt = round((($payoutAmt * $tax) / 100), 2);
                        $payoutAmt = $payoutAmt - $taxAmt;
                    }
                }

                if (isset($getPayout->amount)) {
                    $payoutAmt = $getPayout->amount + $payoutAmt;
                }

                $rideDateTime = Carbon::parse("$ride->completed_date $ride->completed_time");

                $getPayout->ride_id = $booking->ride_id;
                $getPayout->booking_id = $booking->id;
                $getPayout->user_id = $ride->added_by;
                $getPayout->amount = $payoutAmt;
                $getPayout->available_date = $rideDateTime;
                $getPayout->status = "pending";
                $getPayout->tax_amount = $taxAmt;
                $getPayout->tax_percentage = isset($tax) && $tax != 0 ? $tax : 0;
                $getPayout->tax_type = isset($tax_type) && $tax_type != "" ? $tax_type : NULL;
                $getPayout->deduct_type = isset($deduct_tax) && $deduct_tax != "" ? $deduct_tax : NULL;
                $getPayout->save();
            }
        }

        if ($request->seats < $booking->seats) {
            $booking->update([
                'seats' => $updatedSeats,
                'booking_credit' => $updatedBookingCredit,
                'fare' => $updatedFare,
            ]);
        } elseif ($request->seats == $booking->seats) {
            $booking->update([
                'status' => '4',
            ]);
        }

        $getSeatDetails = SeatDetail::where('booking_id', $booking->id)->get();
        $cancelSeatsCount = $request->seats;
        if (isset($getSeatDetails) && !empty($getSeatDetails)) {
            foreach ($getSeatDetails->take($cancelSeatsCount) as $key => $getSeatDetail) {
                $getSeatDetail->status = 'pending';
                $getSeatDetail->booking_id = NULL;
                $getSeatDetail->user_id = NULL;
                $getSeatDetail->save();
            }
        }

        CancellationHistory::create([
            'ride_id' => $booking->ride_id,
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'type' => 'passenger',
        ]);
        if (isset($booking->ride->driver->email_notification) && $booking->ride->driver->email_notification == 1) {

            $data = ['driver_name' => $booking->ride->driver->first_name, 'passenger_name' => $booking->passenger->first_name, 'seats' => $originalSeats, 'cancelled_searts' => $request->seats, 'price' => $booking->price, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => Carbon::parse($booking->ride->date)->format('F d, Y'), 'time' => $booking->ride->time];
            // Send email to driver
            Mail::to($booking->ride->driver->email)->queue(new PassengerCancelBookingMail($data));
        }

        $notification = Notification::create([
            'ride_id' => $booking->ride_id,
            'posted_by' => $booking->user_id,
            'message' =>  'Booking cancelled',
            'status' => 'cancelled',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination
        ]);

        $fcmService = new FCMService();
        $fcm_tokens = FCMToken::where('user_id', $booking->ride->added_by)->get();
        $body = $notification->message;

        $fcmToken = $booking->ride->driver->mobile_fcm_token;
        if ($fcmToken) {
            $fcmService->sendNotification($fcmToken, $body);
        }

        foreach ($fcm_tokens as $fcm_token) {
            try {
                $fcmService->sendNotification($fcm_token->token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
            }
        }

        $chatMessage = Message::create([
            'ride_id' => $booking->ride->id,
            'receiver' => $booking->ride->added_by,
            'sender' => $booking->user_id,
            'message' => $request->input('message'),
            'ride_detail_id' => $booking->ride_detail_id
        ]);
        $rideDateTime = Carbon::parse($booking->ride->date . ' ' . $booking->ride->time);
        $oneHourBefore = $rideDateTime->copy()->subHour();

        if (Carbon::now()->between($oneHourBefore, $rideDateTime)) {
            // Get updated passenger list (excluding cancelled bookings)
            $getBookings = Booking::with('passenger')
                ->where('ride_id', $booking->ride_id)
                ->whereNotIn('status', ['3', '0', '4']) // Exclude cancelled, pending, rejected
                ->get();

            if ($getBookings->isNotEmpty()) {
                $passengers = [];
                foreach ($getBookings as $bookingItem) {
                    $passengers[] = [
                        'first_name' => $bookingItem->passenger->first_name,
                        'seats' => $bookingItem->seats,
                    ];
                }

                $data = [
                    'driver_name' => $booking->ride->driver->first_name,
                    'from' => $booking->departure,
                    'to' => $booking->destination,
                    'date' => $booking->ride->date,
                    'time' => $booking->ride->time,
                    'passengers' => $passengers,
                ];

                // Send updated passenger list email
                if ($booking->ride->driver->email_notification == 1) {
                    Mail::to($booking->ride->driver->email)
                        ->send(new PassengerListMail($data));
                }

                // Send FCM notification
                $notification = Notification::create([
                    'ride_id' => $booking->ride_id,
                    'posted_by' => $booking->ride->added_by,
                    'message' => 'Your passenger list has been updated',
                    'status' => 'upcoming',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination,
                ]);

                $fcmService = new FCMService();
                $fcmToken = $booking->ride->driver->mobile_fcm_token;
                $body = $notification->message;

                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $body);
                }

                $fcm_tokens = FCMToken::where('user_id', $booking->ride->added_by)->get();

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                    }
                }

                // Send SMS if enabled (same logic as in cron)
                if (env('APP_ENV') != 'local' && isset($booking->ride->driver->sms_notification) && $booking->ride->driver->sms_notification == 1) {
                    $phoneNumber = PhoneNumber::where('user_id', $booking->ride->added_by)
                        ->where('verified', '1')
                        ->orderBy('default', 'desc')
                        ->first();

                    if ($phoneNumber) {
                        $sid = env('TWILIO_ACCOUNT_SID');
                        $token = env('TWILIO_AUTH_TOKEN');
                        $from = env('TWILIO_PHONE_NUMBER');

                        $twilio = new Client($sid, $token);
                        $to = $phoneNumber->phone;

                        // Create greeting based on time of day
                        $currentHour = date('H');
                        if ($currentHour >= 0 && $currentHour < 12) {
                            $title = "Good morning " . $booking->ride->driver->first_name . ",";
                        } elseif ($currentHour >= 12 && $currentHour < 17) {
                            $title = "Good afternoon " . $booking->ride->driver->first_name . ",";
                        } else {
                            $title = "Good evening " . $booking->ride->driver->first_name . ",";
                        }

                        $departureTime = date('H:i:s', strtotime($booking->ride->time));
                        $departureDate = date('d F, Y', strtotime($booking->ride->date));
                        $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->passenger->id)
                            // ->where('verified', '1')
                            ->where('default', '1')
                            ->first();

                        $passengerPhoneToUse = $passengerPhoneNumber ? $passengerPhoneNumber->phone :  $booking->passenger->phone;


                        // Build passenger list
                        $passengerList = "";
                        $counter = 1;
                        foreach ($getBookings as $bookingItem) {
                            // $passengerPhone = $bookingItem->passenger->phone;

                            // $formattedPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1)$2-$3", $passengerPhoneToUse);

                            $seatText = $bookingItem->seats == 1 ? 'seat' : 'seats';

                            $passengerList .= $counter . "- " . $bookingItem->passenger->first_name .
                                ". Phone " . $passengerPhoneToUse .
                                ". Booked: " . $bookingItem->seats . " " . $seatText . "\n";
                            $counter++;
                        }

                        $message = $title . "\n" . "From ProximaRide: Your passenger list has been updated for your ride from " .
                            $booking->departure . " to " . $booking->destination .
                            " on " . $departureDate . " at " . $departureTime . "\n" .
                            $passengerList . "Drive safe!";

                        try {
                            $res = $twilio->messages->create(
                                $to,
                                [
                                    'from' => $from,
                                    'body' => $message,
                                ]
                            );
                            Log::info('SMS sent to ' . $to . ' for updated passenger list on ride ' . $booking->ride_id);
                        } catch (\Exception $e) {
                            Log::error('Cannot send text to ' . $to . ' for ride ' . $booking->ride_id . '. Error: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        $getUser = User::where('id', $booking->user_id)->first();

        broadcast(new MessageSentEvent($booking->ride, $getUser, $chatMessage))->toOthers();

        $phoneNumber = PhoneNumber::where('user_id', $booking->ride->added_by)->where('verified', '1')->where('default', '1')->first();

        if (!$phoneNumber) {
            $phoneNumber = PhoneNumber::where('user_id', $booking->ride->added_by)->where('verified', '1')->first();
        }

        if ($phoneNumber && env('APP_ENV') != 'local' && isset($booking->ride->driver->sms_notification) && $booking->ride->driver->sms_notification == 1) {
            $passengerName = $booking->passenger->first_name;

            // Send the secured cash code via Twilio
            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_PHONE_NUMBER');

            $twilio = new Client($sid, $token);
            $to = $phoneNumber->phone;


            $title = "";
            $currentHour = date('H');
            if ($currentHour >= 0 && $currentHour < 12) {
                $title = "Good morning " . $booking->passenger->first_name . ",";
            } elseif ($currentHour >= 12 && $currentHour < 17) {
                $title = "Good afternoon " . $booking->passenger->first_name . ",";
            } else {
                $title = "Good evening " . $booking->passenger->first_name . ",";
            }

            // $depatureDate = date('d F, Y H:i:s', strtotime('' . $booking->ride->date . ' ' . $booking->ride->time . ''));
            $departureTime = date('H:i:s', strtotime($booking->ride->time));
            $depatureDate = date('d F, Y', strtotime($booking->ride->date));

            // $message = "" . $title . "\nDriver reject your booking request from this ride\nTrip detail\nOrigin: " . $booking->departure . "\nDestination: " . $booking->destination . "\nDeparture date: " . $depatureDate . "\nDriver name: " . $booking->ride->driver->first_name . "\nDriver phone number: " . $booking->ride->driver->phone . "";
            $message = $title . "\n" . "From ProximaRide: Ride from " . $booking->departure . " to " . $booking->destination . " on " . $depatureDate . " at " . $departureTime . "\nYour passenger " . $booking->passenger->first_name . " has cancelled as follows:\nBooked: " . $booking->seats . " seats\nCancelled " . $request->seats . "\nRemaining: " . ($booking->seats - $request->seats) . "\nAmount due to you: $" . $payoutAmt . "* (* See our cancellation policy)\nWe have opened the cancelled seat(s) for other passengers to book";
            try {
                $res = $twilio->messages->create(
                    $to,
                    [
                        'from' => $from,
                        'body' => $message,
                    ]
                );
            } catch (\Exception  $e) {
                Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());

                // return $this->errorResponse('Can not send text to ' . $phoneNumber->phone . ' because unable to create record: Authenticate');
            }
        }

        return redirect()->route('my_trips', ['lang' => $selectedLanguage->abbreviation])->with(['success' => $messages->cancel_booking_message ?? null]);
    }



    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent = PaymentIntent::create([
            'amount' => round(($request->amount * 100), 0),
            'currency' => 'usd',
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
