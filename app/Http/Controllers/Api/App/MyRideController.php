<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\CancelPassengerAdminMail;
use App\Mail\CancelPassengerMail;
use App\Mail\DriverCancelRideMail;
use App\Mail\SecuredCashDriverMail;
use App\Mail\SecuredCashPassengerMail;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\CancellationHistory;
use App\Models\CancelRideSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\TripsPageSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Models\Rating;
use App\Models\ReviewSetting;
use App\Models\Ride;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\PhoneNumber;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\TopUpBalance;
use App\Models\BookingPageSettingDetail;
use App\Models\MyPassengerSettingDetail;
use App\Models\SeatDetail;
use App\Models\CoffeeWallet;
use App\Models\FeaturesSettingDetail;
use App\Models\Message;
use App\Models\Notification;
use App\Services\DriverRideCancellationService;
use App\Services\FCMService;
use App\Services\PassengerRemovalService;
use App\Jobs\NotifyPassengerRemovedJob;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;
use Twilio\Rest\Client;

class MyRideController extends Controller
{
    use StatusResponser;

    protected function buildRideFeatureAssetMaps($optionGroups, string $groupKey): array
    {
        $options = collect($optionGroups[$groupKey] ?? []);

        return [
            'images' => $options
                ->mapWithKeys(function ($option) {
                    $icon = $option->icon ?? null;

                    return [(int) ($option->features_setting_id ?? $option->id ?? 0) => $icon ? asset('home_page_icons/' . $icon) : null];
                })
                ->all(),
            'tooltips' => $options
                ->mapWithKeys(function ($option) {
                    return [(int) ($option->features_setting_id ?? $option->id ?? 0) => $option->tooltip ?? null];
                })
                ->all(),
        ];
    }

    protected function buildRideFeatureNameMap($optionGroups, string $groupKey): array
    {
        return collect($optionGroups[$groupKey] ?? [])
            ->mapWithKeys(function ($option) {
                return [(int) ($option->features_setting_id ?? $option->id ?? 0) => $option->name ?? null];
            })
            ->all();
    }

    protected function buildRideFeatureResponseMap($optionGroups, string $groupKey): array
    {
        return collect($optionGroups[$groupKey] ?? [])
            ->mapWithKeys(function ($option) {
                $featureId = (int) ($option->features_setting_id ?? $option->id ?? 0);
                $icon = $option->icon ?? null;

                return [$featureId => [
                    'id' => $featureId,
                    'title' => $option->name ?? null,
                    'image' => $icon ? asset('home_page_icons/' . $icon) : null,
                    'tooltip' => $option->tooltip ?? null,
                ]];
            })
            ->all();
    }

    protected function getApiGenderLabel(?Language $language)
    {
        if (!$language) {
            return null;
        }

        return Step1PageSettingDetail::where('language_id', $language->id)
            ->select('male_option_label', 'female_option_label', 'prefer_option_label')
            ->first();
    }

    public function CurrentRides(Request $request, $kind = 'upcoming')
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $selectedLanguage = $this->resolveApiLanguage();
        $defaultLanguage = $this->defaultLang;
        $genderLabel = $this->getApiGenderLabel($selectedLanguage);
        $rideFeatureOptionGroups = $this->getRideFeatureOptionGroups($selectedLanguage?->id, $defaultLanguage?->id);
        $bookingMethodAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'booking_method');
        $paymentMethodAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'payment_method');
        $smokingAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'smoking_allowed');
        $petsAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'pets_allowed');
        $luggageAssets = $this->buildRideFeatureAssetMaps($rideFeatureOptionGroups, 'luggage_size');

        $bookingMethodImages = $bookingMethodAssets['images'];
        $bookingMethodTooltips = $bookingMethodAssets['tooltips'];
        $paymentMethodImages = $paymentMethodAssets['images'];
        $paymentMethodTooltips = $paymentMethodAssets['tooltips'];
        $smokeImages = $smokingAssets['images'];
        $smokeTooltips = $smokingAssets['tooltips'];
        $petsImages = $petsAssets['images'];
        $petsTooltips = $petsAssets['tooltips'];
        $luggageImages = $luggageAssets['images'];
        $luggageTooltips = $luggageAssets['tooltips'];
        $bookingMethodNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'booking_method');
        $paymentMethodNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'payment_method');
        $smokingNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'smoking_allowed');
        $petsNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'pets_allowed');
        $luggageNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'luggage_size');
        $bookingTypeNames = $this->buildRideFeatureNameMap($rideFeatureOptionGroups, 'cancellation');
        $featureResponseMap = $this->buildRideFeatureResponseMap($rideFeatureOptionGroups, 'features');

        $query = Ride::where('added_by', $user_id);

        switch ($kind) {
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

        $rides = $query->whereHas('driver', function ($query) {
            $query->active(); // Exclude soft-deleted drivers
        })
            ->with('rideDetail')
            ->with([
                'vehicle',
                'driver' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
                },
                'bookings' => function ($query) {
                    $query->where('status', '<>', 0)
                        ->where('status', '<>', 3)
                        ->where('status', '<>', 4)
                        ->with(['passenger' => function ($query) {
                            $query->select('id', 'first_name', 'last_name', 'profile_image', 'gender', 'dob'); // Include names for review-passenger and completed-ride UI
                        }]);
                }
            ])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($request->paginate_limit);





        foreach ($rides as $ride) {

        

            $displayPrice = $ride->price_minor ?? number_format($ride->detail->price / 100, 2, '.', '');
            $ride->price = $displayPrice;

            // Calculate seats left
            $bookedSeats = $ride->bookings()
                ->where('status', '<>', 3)
                ->where('status', '<>', 4)
                ->withActivePassenger()
                ->sum('seats');
            $ride->seats_left = intval($ride->seats) - intval($bookedSeats);

            $ride->booking_method_image = $bookingMethodImages[$ride->booking_method] ?? null;
            $ride->booking_method_tooltip = $bookingMethodTooltips[$ride->booking_method] ?? null;
            $ride->payment_method_image = $paymentMethodImages[$ride->payment_method] ?? null;
            $ride->payment_method_tooltip = $paymentMethodTooltips[$ride->payment_method] ?? null;
            $ride->smoke_image = $smokeImages[$ride->smoke] ?? null;
            $ride->smoke_tooltip = $smokeTooltips[$ride->smoke] ?? null;
            $ride->animal_friendly_image = $petsImages[$ride->animal_friendly] ?? null;
            $ride->animal_friendly_tooltip = $petsTooltips[$ride->animal_friendly] ?? null;
            $ride->luggage_image = $luggageImages[$ride->luggage] ?? null;
            $ride->luggage_tooltip = $luggageTooltips[$ride->luggage] ?? null;

            $ride->booked_seats = $bookedSeats;
            $ride->booking_fee = round($ride->bookings->sum('booking_credit'), 1);
            $ride->fare = round($ride->bookings->sum('fare'), 1);
            $ride->total_amount = $ride->booking_fee + $ride->fare;

            // Initialize a temporary array for the features
            $features = [];

            $rideFeatures = collect($ride->features)
                ->when(is_string($ride->features), fn($c) => collect(explode('=', $ride->features)))
                ->filter()
                ->values()
                ->all();
            foreach ($rideFeatures as $feature) {
                if (isset($featureResponseMap[$feature])) {
                    $features[] = $featureResponseMap[$feature];
                }
            }

            // Assign the features array to the ride's features attribute
            $ride->features = $features;

            // Calculate age
            if ($ride->driver->dob) {
                $dob = Carbon::parse($ride->driver->dob);
                $ride->driver->age = $dob->diffInYears(Carbon::now());
            } else {
                $ride->driver->age = null; // Handle case where dob is not set
            }

            if ($ride->driver->gender) {
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
            $filteredRatings = $ratings->filter(function ($rating) use ($user) {
                return optional($rating->ride)->added_by === $user->id;
            });

            $totalAverage = $filteredRatings->avg('average_rating');
            $ride->driver->average_rating = $totalAverage;

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

            foreach ($ride->bookings as $booking) {
                if ($booking->passenger->gender) {
                    if ($booking->passenger->gender === 'male') {
                        $booking->passenger->gender_label = $genderLabel->male_option_label;
                    } elseif ($booking->passenger->gender === 'female') {
                        $booking->passenger->gender_label = $genderLabel->female_option_label;
                    } elseif ($booking->passenger->gender === 'prefer not to say') {
                        $booking->passenger->gender_label = $genderLabel->prefer_option_label;
                    }
                }
            }
        }

        // Separate bookings based on status
        $rides->getCollection()->transform(function ($ride) {
            $ride->booking_requests = $ride->bookings()->where('status', 0)
                ->with(['passenger' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'profile_image', 'gender', 'dob');
                }])->get();
            return $ride;
        });

        Log::info('Processing rides payload', [
            'kind' => $kind,
            'total' => method_exists($rides, 'total') ? $rides->total() : null,
            'count' => $rides->count(),
            'ride_ids' => $rides->getCollection()->pluck('id')->all(),
        ]);
        
        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $data = ['rides' => $rides, 'rideDetailPage' => $rideDetailPage, 'tripsPage' => $tripsPage];
        return $this->successResponse($data, 'Get my ' . $kind . ' rides');
    }

    public function PastRides(Request $request)
    {

        return $this->CurrentRides($request, 'completed');
    }

    public function CancelledRides(Request $request)
    {

        return $this->CurrentRides($request, 'cancelled');
    }

    public function MyPassengers(Request $request)
    {

        $selectedLanguage = app()->getLocale() ?? 'en';
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message')->first();

        $ride = Ride::where('id', $request->id)->first();
        if ($ride) {
            $bookings = Booking::where('ride_id', $ride->id)
                ->whereNotIn('status', [0, 3, 4])
                ->withActivePassenger()
                ->with(['passenger' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'dob', 'profile_image');
                }])
                ->with(['ride' => function ($query) {
                    $query->select('id', 'date', 'time');
                }])->get();

            if ($request->lang_id && $request->lang_id != 0) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
                }
            }

            foreach ($bookings as $booking) {

                // Calculate age
                if ($booking->passenger->dob) {
                    $dob = Carbon::parse($booking->passenger->dob);
                    $booking->passenger->age = $dob->diffInYears(Carbon::now());
                } else {
                    $booking->passenger->age = null; // Handle case where dob is not set
                }

                if ($booking->passenger->gender) {
                    if ($booking->passenger->gender === 'male') {
                        $booking->passenger->gender_label = $genderLabel->male_option_label;
                    } elseif ($booking->passenger->gender === 'female') {
                        $booking->passenger->gender_label = $genderLabel->female_option_label;
                    } elseif ($booking->passenger->gender === 'prefer not to say') {
                        $booking->passenger->gender_label = $genderLabel->prefer_option_label;
                    }
                }
            }

            $cancelRideSetting = CancelRideSetting::select('id', 'driver_cancel_hours')->first();

            $myPassengerPage = null;
            if ($request->lang_id && $request->lang_id != 0) {
                $myPassengerPage = MyPassengerSettingDetail::where('language_id', $request->lang_id)->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $myPassengerPage = MyPassengerSettingDetail::where('language_id', $selectedLanguage->id)->first();
                }
            }

            $data = ['bookings' => $bookings, 'cancelRideSetting' => $cancelRideSetting, 'myPassengerPage' => $myPassengerPage];
            return $this->successResponse($data, 'Success');
        }

        return $this->apiErrorResponse($message->general_error_message ?? "Ride not found", 404);
    }

    public function removePassenger(Request $request)
    {
        $booking = Booking::with('passenger')->where('id', $request->booking_id)->first();

        $message = $this->successMessage;

        if ($booking) {
            $user = Auth::guard('sanctum')->user();
            $getSetting = SiteSetting::getCached();

            // Driver-side cancellation limit (mirrors web updateRemovePassenger)
            $monthsAgo = Carbon::now()->subMonths($getSetting->booking_cancel_duration)->setTimezone('UTC');
            $cancellationCount = CancellationHistory::where('user_id', $user->id)
                ->where('created_at', '>=', $monthsAgo)
                ->where('type', 'driver')
                ->count();

            if ($cancellationCount >= $getSetting->booking_cancel_limit) {
                $bookingPage = BookingPageSettingDetail::getByLanguageWithFallback(
                    $this->selectedLanguage->id,
                    $this->defaultLang->id
                );

                return $this->apiErrorResponse(
                    $bookingPage->booking_cancellation_limit_exceed ?? "Booking cancellation limit exceeded",
                    404
                );
            }

            $ride = Ride::with('driver')->where('id', $booking->ride_id)->first();

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
                return $this->apiErrorResponse($result['error'], 200);
            }

            $booking = $result['booking'];
            $ride = $result['ride'];

            NotifyPassengerRemovedJob::dispatch(
                $booking->id,
                $ride->added_by,
                (string) $request->admin_message,
                (string) $request->passenger_message
            );

            return $this->successResponse([], strip_tags($message->removed_passenger_message ?? 'Removed successfully'));
        }



        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? "Booking not found"), 404);
    }

    public function enterCode(Request $request)
    {
        $booking = Booking::where('id', $request->booking_id)->first();

        $siteSetting = SiteSetting::getCached();

        $message = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('incorrect_code_message', 'general_error_message', 'too_many_secured_cash_attempt_message', 'secured_cash_success_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('incorrect_code_message', 'general_error_message', 'too_many_secured_cash_attempt_message', 'secured_cash_success_message')->first();
            }
        }

        if ($booking) {
            $request->validate([
                'code' => 'required|max:4',
            ]);

            $messageData = "";

            if ($request->code === $booking->secured_cash_code) {
                $booking->update([
                    'secured_cash' => null,
                    'secured_cash_code' => null,
                ]);

                $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
                foreach ($transactions as $transaction) {
                    if ($transaction) {
                        $refundId = "";
                        if ($transaction->pay_by_account == 0) {
                            if ($transaction->paypal_id) {
                                $paypal = new PayPalClient;
                                $paypal->setApiCredentials(config('paypal'));
                                $token = $paypal->getAccessToken();
                                $paypal->setAccessToken($token);
                                $response = $paypal->refundCapturedPayment(
                                    $transaction->paypal_id,
                                    'USD',
                                    $transaction->price - $transaction->booking_fee,
                                    'Invoice-' . $transaction->paypal_id
                                );
                                $refundId = isset($response['id']) ? $response['id'] : "";
                            } elseif ($transaction->stripe_id) {
                                // Set your Stripe API key
                                Stripe::setApiKey(env('STRIPE_SECRET'));

                                try {
                                    // Create a refund using the payment intent ID
                                    $refund = Refund::create([
                                        'payment_intent' => $transaction->stripe_id,
                                        'amount' => ($transaction->price - $transaction->booking_fee) * 100, // Refund amount in cents
                                    ]);
                                    $refundId = $refund->id;
                                } catch (\Stripe\Exception\ApiErrorException $e) {
                                    // Handle error
                                    return $this->apiErrorResponse($e->getMessage(), 200);
                                }
                            }
                        } else {
                            $topUpBalance = TopUpBalance::create([
                                'booking_id' => $transaction->booking_id,
                                'user_id' => $booking->user_id,
                                'dr_amount' => $transaction->price - $transaction->booking_fee,
                                'added_date' => date('Y-m-d'),
                            ]);
                        }

                        $newTransaction = Transaction::create([
                            'booking_id' => $transaction->booking_id,
                            'ride_id' => $booking->ride_id,
                            'parent_id' => $transaction->id,
                            'type' => '3',
                            'price' => $transaction->price,
                            'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                            'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL
                        ]);
                    }
                }

                $notification = Notification::create([
                    'ride_id' => $booking->ride_id,
                    'posted_by' => $booking->user_id,
                    'message' => getNotificationMessageText(
                        'secured_cash_payment_code_successful',
                        $booking->ride->driver,
                        [],
                        'Secured-cash payment code successful'
                    ),
                    'status' => 'upcoming',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);

                $fcmToken = $booking->ride->driver->mobile_fcm_token;
                $body = $notification->message;
                $fcmService = new FCMService();

                if ($fcmToken) {
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }

                $notification = Notification::create([
                    'type' => 2,
                    'ride_id' => $booking->ride_id,
                    'posted_to' => $booking->id,
                    'posted_by' => $booking->ride->added_by,
                    'message' => getNotificationMessageText(
                        'secured_cash_payment_code_successful',
                        $booking->passenger,
                        [],
                        'Secured-cash payment code successful'
                    ),
                    'status' => 'upcoming',
                    'notification_type' => 'upcoming',
                    'ride_detail_id' => $booking->ride_detail_id,
                    'departure' => $booking->departure,
                    'destination' => $booking->destination
                ]);

                $fcmToken = $booking->passenger->mobile_fcm_token;
                $body = $notification->message;
                $fcmService = new FCMService();

                if ($fcmToken) {
                    // Send the booking notification
                    $fcmService->sendNotification($fcmToken, $body);
                }

                $driverPhoneNumber = PhoneNumber::where('user_id', $booking->ride->driver->id)
                    ->where('default', '1')
                    ->first();
                $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $booking->ride->driver->phone;

                $passengerPhoneNumber = PhoneNumber::where('user_id', $booking->passenger->id)
                    ->where('default', '1')
                    ->first();
                $passengerPhoneToUse = $passengerPhoneNumber ? $passengerPhoneNumber->phone : $booking->passenger->phone;

                $passengerData = [
                    'passenger_first_name' => $booking->passenger->first_name,
                    'seats_booked' => $booking->seats,
                    'booking_price' => $booking->price,
                    'from' => $booking->departure,
                    'to' => $booking->destination,
                    'on' => $booking->ride->date,
                    'at' => $booking->ride->time,
                    'driver_first_name' => $booking->ride->driver->first_name,
                    'driver_phone' => $driverPhoneToUse,
                    'passenger_email' => $booking->ride->driver->email,
                ];
                if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {
                    Mail::to($booking->passenger->email)->queue(new SecuredCashPassengerMail($passengerData));
                }

                // Send email to driver
                $driverData = [
                    'driver_first_name' => $booking->ride->driver->first_name,
                    'seats_booked' => $booking->seats,
                    'booking_price' => $booking->price,
                    'from' => $booking->departure,
                    'to' => $booking->destination,
                    'on' => $booking->ride->date,
                    'at' => $booking->ride->time,
                    'passenger_first_name' => $booking->passenger->first_name,
                    'passenger_phone' => $passengerPhoneToUse,
                    'passenger_email' => $booking->passenger->email,
                ];
                if (isset($booking->ride->driver->email_notification) && $booking->ride->driver->email_notification == 1) {
                    Mail::to($booking->ride->driver->email)->queue(new SecuredCashDriverMail($driverData));
                }

                return $this->successResponse('', strip_tags($message->secured_cash_success_message ?? "Code submitted and the booking price has been released back to the passenger. Now, get your payment in cash from them"));
            } else {
                if ($booking->secured_cash_attempt_count < $siteSetting->secured_cash_attempt) {
                    $count = isset($booking->secured_cash_attempt_count) ? $booking->secured_cash_attempt_count : 0;
                    $count = $count + 1;
                    $booking->secured_cash_attempt_count = $count;
                    $booking->save();
                    $messageData = strip_tags($message->incorrect_code_message);
                } else {
                    $messageData = strip_tags($message->too_many_secured_cash_attempt_message);
                }
            }



            return $this->apiErrorResponse($messageData, 200, $booking->secured_cash_attempt_count);
        }
        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? "Booking not found"), 404);
    }

    /**
     * Driver cancels ride: persistence in {@see DriverRideCancellationService};
     * passenger notifications via queued {@see NotifyDriverCancelledRidePassengersJob}.
     */
    public function CancelRide(Request $request)
    {
        $ride = Ride::where('id', $request->id)->first();
        $messages = $this->successMessage;

        if (!$ride) {
            return $this->apiErrorResponse(strip_tags($messages->general_error_message ?? 'Ride not found'), 404);
        }

        $authUser = Auth::guard('sanctum')->user();
        if (!$authUser) {
            return $this->apiErrorResponse(strip_tags($messages->general_error_message ?? 'Unauthorized'), 401);
        }
        if ((int) $ride->added_by !== (int) $authUser->id) {
            return $this->apiErrorResponse(strip_tags($messages->general_error_message ?? 'Unauthorized'), 403);
        }

        $cancellation = app(DriverRideCancellationService::class);

        if ($cancellation->countBookedSeatsExcludingInactive($ride) === 0) {
            $cancellation->markRideCancelledEmpty($ride, false);

            return $this->successResponse(['ride' => $ride->fresh()], strip_tags($messages->ride_cancel_message));
        }

        $request->validate([
            'message' => 'required',
            'reason' => 'required',
        ]);

        $bookings = $cancellation->bookingsForApiCancel($ride);
        $result = $cancellation->cancelByDriverApi($ride, $bookings);

        if (!$result['ok']) {
            return $this->apiErrorResponse($result['error'], 200);
        }

        $this->dispatchDriverRideCancelledPassengerWebFlow(
            $ride->fresh(),
            $authUser,
            $result['booking_ids'],
            (string) $request->message,
            'api'
        );

        return $this->successResponse(['ride' => $ride->fresh()], strip_tags($messages->ride_cancel_message));
    }
}
