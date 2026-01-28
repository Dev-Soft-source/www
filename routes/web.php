<?php

use App\Http\Controllers\AccessPortalController;
use App\Http\Controllers\Api\App\NotificationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingCreditController;
use App\Http\Controllers\CancellationPolicyController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\CloseAccountController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\CountryStateCityController;
use App\Http\Controllers\DisputePolicyController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverWalletController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FolkRideController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MyChatsController;
use App\Http\Controllers\MyRideController;
use App\Http\Controllers\MyTripController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\PassengerWalletController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\PayPalWebhookController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\PinkRideController;
use App\Http\Controllers\PostRideAgainController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePassengerPreferencesController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\ProfilePreferencesController;
use App\Http\Controllers\ProfilePhotoGuidelinesController;
use App\Http\Controllers\ProfileReferralController;
use App\Http\Controllers\ProfileVehicleController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RefundPolicyController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\Step1to5Controller;
use App\Http\Controllers\Step2to5Controller;
use App\Http\Controllers\Step3to5Controller;
use App\Http\Controllers\Step4to5Controller;
use App\Http\Controllers\Step5to5Controller;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TermsAndConditionsController;
use App\Http\Controllers\TermsOfUseController;
use App\Http\Controllers\TestEmailController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VerifyDriverController;
use App\Http\Controllers\VerifyStudentController;
use App\Models\Booking;
use Illuminate\Support\Facades\Route;
use Twilio\Rest\Client;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

require __DIR__ . '/auth.php';
require __DIR__ . '/web-routes.php';

Route::get('/run-queue', function () {
    \Artisan::call("queue:listen");
});


 Route::get('/.well-known/assetlinks.json', function () {
     $payload = [
         [
             "relation" => ["delegate_permission/common.handle_all_urls"],
             "target" => [
                 "namespace" => "android_app",
                 "package_name" => "com.devop360.proximaride",
                 "sha256_cert_fingerprints" => [
                     "5EAEF3972B908B855AB851965F46B1F80376FA95CB0C22AFFD956DDEC3CCE9C8"
                 ]
             ]
         ]
     ];

     return response()->json($payload)->header('Access-Control-Allow-Origin', '*');
 });

Route::get('/accept/{bookingId}', [StripeWebhookController::class, 'acceptRedirect']);
Route::get('/reject/{bookingId}', [StripeWebhookController::class, 'rejectRedirect']);

Route::post('/twilio/callback', [StripeWebhookController::class, 'handleTwilloCallback']);

Route::post('/twilio/callback/conservation', [StripeWebhookController::class, 'handleTwilloConservationCallback']);

Route::get('/create-conservation/{id}', function ($id) {
    $sid   = env('TWILIO_ACCOUNT_SID');
    $token = env('TWILIO_AUTH_TOKEN');
    $from  = env('TWILIO_PHONE_NUMBER');

    $twilio = new Client($sid, $token);
    $to     = "+923049446457";

    $booking = Booking::whereId($id)->select("conversation_sid", "participant_sid")->first();

    if (isset($booking->conversation_sid)) {
        try {
            // Verify participant exists
            $participant = $twilio->conversations->v1
                ->conversations($booking->conversation_sid)
                ->participants($booking->participant_sid)
                ->fetch();

            $conversationSid = $booking->conversation_sid;
        } catch (\Twilio\Exceptions\RestException $e) {
            // Participant missing, fallback to creating new conversation
            unset($booking->conversation_sid);
        }
    }

    if (! isset($booking->conversation_sid)) {
        $conversation = $twilio->conversations->v1->conversations->create([
            'friendlyName' => 'Chat with ' . $id,
        ]);

        $conversationSid = $conversation->sid;

        try {

            $participantConversations = $twilio->conversations->v1->participantConversations->read(
                ["address" => $to],
                2
            );


            if (isset($participantConversations) && count($participantConversations) > 0) {
                $participantId = $participantConversations[0]->participantSid;
                $conversationSid = $participantConversations[0]->conversationSid;
            } else {

                $participant = $twilio->conversations->v1->conversations($conversationSid)
                ->participants
                ->create([
                    'messagingBindingAddress'      => $to,
                    'messagingBindingProxyAddress' => $from,
                ]);
                $participantId = $participant->sid;
            }

        } catch (\Twilio\Exceptions\RestException $e) {
            throw $e;
        }

        Booking::whereId($id)->update([
            "conversation_sid" => $conversationSid,
            "participant_sid"  => $participantId,
        ]);
    }

    // Rest of your code to send message
    $randomText = fake()->sentence(2);
    $message    = "{$randomText} for booking accept reply 33 and reject reply 11";

    $response = $twilio->conversations->v1->conversations($conversationSid)
        ->messages
        ->create([
            'author'     => 'system',
            'body'       => $message,
            'attributes' => '{"testAttr": "hello"}',
        ]);

    Log::info("Conversation id: " . $conversationSid);

    return $response;
});

Route::get('/append-new-msg-to-conservation/{id}', function ($id) {
    $sid   = env('TWILIO_ACCOUNT_SID');
    $token = env('TWILIO_AUTH_TOKEN');
    $from  = env('TWILIO_PHONE_NUMBER');

    $twilio = new Client($sid, $token);
    $to     = "+923049446457";

    // $conversation = $twilio->conversations->v1->conversations->create([
    //     'friendlyName' => 'Chat with ' . $id,
    // ]);

    $conversationSid = $id;

    // $twilio->conversations->v1->conversations($conversationSid)
    //     ->participants
    //     ->create([
    //         'messagingBinding.address'      => $to,
    //         'messagingBinding.proxyAddress' => $from,
    //     ]);

    $randomText = fake()->sentence(2);
    $message    = "{$randomText} for booking accept reply 33 and reject reply 11";

    $response = $twilio->conversations->v1->conversations($conversationSid)
        ->messages
        ->create([
            'author'     => 'system',
            'body'       => $message,
            'attributes' => '{"testAttr": "hello"}',
        ]);

    Log::info("Append msg conversation id: " . $conversationSid);

    return $response;
});

Route::get('/create-message/{id}', function ($id) {
    $sid   = env('TWILIO_ACCOUNT_SID');
    $token = env('TWILIO_AUTH_TOKEN');
    $from  = env('TWILIO_PHONE_NUMBER');

    $twilio = new Client($sid, $token);
    $to     = "+15145577856";


    // Rest of your code to send message
    $randomText = fake()->sentence(2);
    $message    = "{$randomText} for booking accept reply 33 and reject reply 11";

    $res = $twilio->messages->create(
        $to,
        [
            'from' => $from,
            'body' => $message,
        ]
    );

    Log::info("Conversation id: " . $res);

    return $res;
});


Route::get('/twilio/callbackv1', [StripeWebhookController::class, 'handleTwilloCallbackv1']);

Route::get('/twilio/sendMessage', [StripeWebhookController::class, 'sendMessage']);

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
Route::post('/webhook/paypal', [PayPalWebhookController::class, 'handleWebhook']);
Route::get('/show-ride', function () {
    return 'true';
})->name('show_ride');

Route::get('/{lang?}', [HomeController::class, 'index'])->name('home')->where('lang', '[a-zA-Z]{2}');
Route::get('{lang?}/coffee-on-the-wall', [HomeController::class, 'coffeeOnWall'])->name('coffee_on_wall');
Route::get('{lang?}/signup', [SignupController::class, 'create'])->middleware('guest')->name('signup');
Route::get('{lang?}/signup/{provider}', [SignupController::class, 'redirectToProvider'])->name('signup.redirectToProvider');
Route::get('{lang?}/signup/{provider}/callback', [SignupController::class, 'handleProviderCallback'])->name('signup.handleProviderCallback');
Route::get('send-email-verify/{email}', [SignupController::class, 'sendEmailVerify'])->middleware('guest')->name('sendEmailVerify');
Route::get('{lang?}/forgot-password', [ForgotPasswordController::class, 'create'])->middleware('guest')->name('forgot.password');
Route::get('{lang?}/reset-password/{token}', [ResetPasswordController::class, 'create'])->middleware('guest')->name('reset.password');
Route::get('email-verify/{token}/{email}', [LoginController::class, 'emailVerify'])->name('emailVerify');
Route::get('email-verified', [LoginController::class, 'emailVerified'])->name('emailVerified');
Route::get('welcome-route/{email}', [LoginController::class, 'welcomeRoute'])->name('welcomeRoute');

// Test routes for email verification (remove in production)
Route::get('test/email-verification', [TestEmailController::class, 'sendTestEmailVerification'])->name('test.emailVerification');
Route::get('test/email-verification/app', [TestEmailController::class, 'sendTestEmailVerification'])->defaults('app', 'true')->name('test.emailVerificationApp');
Route::get('{lang?}/login-with-app', [LoginController::class, 'appLogin'])->middleware('guest')->name('login_with_app');
Route::get('{lang?}/signup-with-referral/{uuid}', [SignupController::class, 'signupWithReferral'])->middleware('guest')->name('signup_with_referral');
Route::get('{lang?}/login', [LoginController::class, 'create'])->middleware('guest')->name('login');
Route::get('{lang?}/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('{lang?}/step1-5', [Step1to5Controller::class, 'create'])->middleware('auth')->name('step1to5');
Route::get('{lang?}/step2-5', [Step2to5Controller::class, 'create'])->middleware('auth')->name('step2to5');
Route::get('{lang?}/step3-5', [Step3to5Controller::class, 'create'])->middleware('auth')->name('step3to5');
Route::get('{lang?}/step4-5', [Step4to5Controller::class, 'create'])->middleware('auth')->name('step4to5');
Route::get('{lang?}/step5-5', [Step5to5Controller::class, 'create'])->middleware('auth')->name('step5to5');
Route::post('{lang?}/step5-5-send-verification', [Step5to5Controller::class, 'sendVerificationCode'])->middleware('auth')->name('step5to5.send_verification');
Route::post('{lang?}/step5-5-send-verification-whatsapp', [Step5to5Controller::class, 'sendVerificationCodeWhatsApp'])->middleware('auth')->name('step5to5.send_verification_whatsapp');
Route::get('{lang?}/students', [StudentController::class, 'index'])->name('students');
Route::get('{lang?}/drivers', [DriverController::class, 'index'])->name('drivers');
Route::get('{lang?}/passenger', [PassengerController::class, 'index'])->name('passengers');
Route::get('{lang?}/search-rides', [RideController::class, 'SearchRide'])->name('search_ride');
Route::get('{lang?}/pink-rides', [PinkRideController::class, 'SearchRide'])->name('pink_ride');
Route::get('{lang?}/Extra-Care-rides', [FolkRideController::class, 'SearchRide'])->name('folk_ride');
Route::get('{lang?}/ride/{departure}/to/{destination}/{id}', [RideController::class, 'RideDetail'])->name('ride_detail');
Route::get('{lang?}/my-ride/{departure}/to/{destination}/{id}', [MyRideController::class, 'MyRideDetail'])->name('my_ride_detail');
Route::get('{lang?}/my-co-passengers/{departure}/to/{destination}/{id}', [RideController::class, 'MyCoPassengers'])->name('my_co_passengers');
Route::get('{lang?}/my-passengers/{departure}/to/{destination}/{id}', [MyRideController::class, 'MyPassengers'])->name('my_passengers');
Route::get('{lang?}/review/{id}', [ReviewController::class, 'reviewIndex'])->name('review.index');
Route::get('{lang?}/review/passenger/{id}', [ReviewController::class, 'passengerReviewIndex'])->name('review_passenger.index');
Route::get('{lang?}/review-left/{id}', [ReviewController::class, 'reviewLeftIndex'])->name('review_left.index');
Route::get('{lang?}/review-left/passenger/{id}', [ReviewController::class, 'passengerLeftReviewIndex'])->name('review_left_passenger.index');
Route::get('{lang?}/review-passenger/{id}', [ReviewController::class, 'ReviewPassenger'])->name('review_passenger');
Route::get('{lang?}/review-driver/{id}', [ReviewController::class, 'ReviewDriver'])->name('review_driver');
Route::get('{lang?}/review-reply/{id}', [ReviewController::class, 'ReviewReply'])->name('review_reply');
Route::get('{lang?}/booking/{id}/{rideDetailId}', [BookingController::class, 'create'])->name('booking');
Route::get('{lang?}/edit-booking/{id}', [BookingController::class, 'edit'])->name('booking.edit');
Route::get('{lang?}/cancel-booking/{id}', [BookingController::class, 'cancel'])->name('booking.cancel');
Route::get('{lang?}/accept-booking-request/{id}/{email}', [BookingController::class, 'AcceptBookingRequest'])->name('accept_booking_request');
Route::get('{lang?}/reject-booking-request/{id}/{email}', [BookingController::class, 'RejectBookingRequest'])->name('reject_booking_request');
Route::get('{lang?}/post-ride', [RideController::class, 'PostRide'])->name('post_ride')->middleware('auth');
Route::get('{lang?}/edit-ride/{id}', [RideController::class, 'EditRide'])->name('edit_ride')->middleware('auth');
Route::put('{lang?}/update-ride/{ride_id}', [RideController::class, 'UpdateRide'])->name('update_ride')->middleware('auth');
Route::get('{lang?}/post-ride/{id}', [RideController::class, 'CopyRide'])->name('copy_ride')->middleware('auth');
Route::get('{lang?}/repost-ride/{id}', [RideController::class, 'RepostRide'])->name('repost_ride')->middleware('auth');
Route::get('{lang?}/post-ride-again', [PostRideAgainController::class, 'CurrentRides'])->name('post_ride_again')->middleware('auth');
Route::get('{lang?}/post-ride-again-completed', [PostRideAgainController::class, 'PastRides'])->name('post_ride_again_completed')->middleware('auth');
Route::get('{lang?}/post-ride-again-cancelled', [PostRideAgainController::class, 'CancelledRides'])->name('post_ride_again_cancelled')->middleware('auth');
Route::get('{lang?}/my-balance', [PassengerWalletController::class, 'getTopUpBalance'])->name('get_top_up_balance')->middleware('auth');
Route::get('{lang?}/top-up-my-balance', [PassengerWalletController::class, 'createTopUpBalance'])->name('create_top_up_balance')->middleware('auth');
Route::get('{lang?}/passenger-wallet-trips', [PassengerWalletController::class, 'index'])->name('passenger_wallet_rides')->middleware('auth');
Route::get('{lang?}/passenger-wallet-rewards', [PassengerWalletController::class, 'reward'])->name('passenger_wallet_rewards')->middleware('auth');
Route::get('{lang?}/driver-wallet-rewards', [DriverWalletController::class, 'reward'])->name('driver_wallet_reward')->middleware('auth');
Route::get('{lang?}/driver-wallet-pending', [DriverWalletController::class, 'pending'])->name('driver_wallet_pending')->middleware('auth');
Route::get('{lang?}/driver-wallet-available', [DriverWalletController::class, 'available'])->name('driver_wallet_available')->middleware('auth');
Route::get('{lang?}/driver-wallet-paid-out', [DriverWalletController::class, 'paid'])->name('driver_wallet_paid')->middleware('auth');
Route::get('{lang?}/ride-fair-details/{id}', [DriverWalletController::class, 'detail'])->name('ride_fair_details')->middleware('auth');
Route::get('{lang?}/my-rides', [MyRideController::class, 'CurrentRides'])->name('my_rides')->middleware('auth');
Route::get('{lang?}/past-rides', [MyRideController::class, 'PastRides'])->name('past_rides')->middleware('auth');
Route::get('{lang?}/cancelled-rides', [MyRideController::class, 'CancelledRides'])->name('cancelled_rides')->middleware('auth');
Route::get('{lang?}/cancel-ride/{id}', [MyRideController::class, 'cancel'])->name('ride.cancel')->middleware('auth');
Route::get('{lang?}/cancel-passenger/{id}', [MyRideController::class, 'cancelPassenger'])->name('passenger.cancel')->middleware('auth');
Route::get('{lang?}/my-trips', [MyTripController::class, 'CurrentTrips'])->name('my_trips')->middleware('auth');
Route::get('{lang?}/past-trips', [MyTripController::class, 'PastTrips'])->name('past_trips')->middleware('auth');
Route::get('{lang?}/cancelled-trips', [MyTripController::class, 'CancelledTrips'])->name('cancelled_trips')->middleware('auth');
Route::get('{lang?}/welcome-on-board', [ProfileController::class, 'index'])->name('profile');
Route::get('{lang?}/profile-info/{id}', [ProfileController::class, 'profileInfo'])->name('profile_info');
Route::get('{lang?}/driver-info/{id}', [ProfileController::class, 'driverInfo'])->name('driver_info');
Route::get('{lang?}/edit-profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::get('{lang?}/profile/photo', [ProfilePhotoController::class, 'index'])->name('profile.photo');
Route::get('{lang?}/profile/driver-preferences', [ProfilePreferencesController::class, 'index'])->name('profile.preferences');
Route::get('{lang?}/profile/passenger-preferences', [ProfilePassengerPreferencesController::class, 'index'])->name('profile.passenger_preferences');
Route::get('{lang?}/profile/vehicles', [ProfileVehicleController::class, 'index'])->name('profile.vehicle');
Route::get('{lang?}/profile/vehicle/create', [ProfileVehicleController::class, 'create'])->middleware('auth')->name('profile.vehicle.create');
Route::get('{lang?}/profile/vehicle/edit/{id}', [ProfileVehicleController::class, 'edit'])->middleware('auth')->name('profile.vehicle.edit');
Route::get('{lang?}/profile/vehicle/delete/{id}', [ProfileVehicleController::class, 'destroy'])->middleware('auth')->name('profile.vehicle.delete');
Route::get('{lang?}/profile/referrals', [ProfileReferralController::class, 'index'])->name('profile.referrals');
Route::get('{lang?}/ratings-left', [RatingController::class, 'RatingsLeft'])->name('ratings.left');
Route::get('{lang?}/ratings-left-to-passenger', [RatingController::class, 'RatingsLeftToPassengers'])->name('ratings.leftToPassengers');
Route::get('{lang?}/ratings-received', [RatingController::class, 'RatingsReceived'])->name('ratings.received');
Route::get('{lang?}/ratings-received-by-passenger', [RatingController::class, 'RatingsReceivedByPassengers'])->name('ratings.receivedByPassengers');
Route::get('{lang?}/all-transactions', [TransactionController::class, 'index'])->name('transactions');
Route::get('{lang?}/all-transactions', [TransactionController::class, 'index'])->name('transactions');
Route::get('{lang?}/user-booking-credits', [BookingCreditController::class, 'index'])->name('booking.credits');
Route::get('{lang?}/payout-options', [PayoutController::class, 'index'])->name('payout');
Route::get('{lang?}/payment-options', [CardController::class, 'index'])->middleware('auth')->name('my_cards');
Route::get('{lang?}/my-cards/add', [CardController::class, 'create'])->name('my_cards.create');
Route::post('/my-cards/session-data', [CardController::class, 'sessionData'])->name('my_cards.sessionData');
// Route::get('{lang?}/my-cards/add/{param?}', [CardController::class, 'create'])->name('my_cards.create');
Route::get('{lang?}/phone', [PhoneController::class, 'index'])->name('phone');
Route::get('{lang?}/add-phone', [PhoneController::class, 'index'])->name('add-phone');
Route::get('{lang?}/phone/code', [PhoneController::class, 'phoneCode'])->name('phone_code');
Route::get('{lang?}/phone/code/step', [PhoneController::class, 'phoneCodeStep'])->name('phone_code_step');
Route::get('{lang?}/email', [EmailController::class, 'index'])->name('email');
Route::get('{lang?}/edit-email', [EmailController::class, 'index'])->name('edit-email');
Route::get('{lang?}/password', [PasswordController::class, 'index'])->name('password');
Route::get('{lang?}/drivers-license', [VerifyDriverController::class, 'index'])->name('driver.verify');
Route::get('{lang?}/student-card', [VerifyStudentController::class, 'index'])->name('student.verify');
Route::get('{lang?}/terms-and-conditions', [TermsAndConditionsController::class, 'index'])->name('terms_conditions');
Route::get('{lang?}/media', [NewsController::class, 'index'])->name('news');
Route::get('{lang?}/news-detail/{id}', [NewsController::class, 'newsDetail'])->name('news_detail');
Route::get('{lang?}/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy_policy');
Route::get('{lang?}/terms-of-use', [TermsOfUseController::class, 'index'])->name('terms_use');
Route::get('{lang?}/proximaride-profile-photo-community-guidelines', [ProfilePhotoGuidelinesController::class, 'index'])->name('profile_photo_guidelines');
Route::get('{lang?}/refund-policy', [RefundPolicyController::class, 'index'])->name('refund_policy');
Route::get('{lang?}/cancellation-policy', [CancellationPolicyController::class, 'index'])->name('cancellation_policy');
Route::get('{lang?}/firm-cancellation-policy', [CancellationPolicyController::class, 'firmCancellation'])->name('firm_cancellation_policy');
Route::get('{lang?}/dispute-policy', [DisputePolicyController::class, 'index'])->name('dispute_policy');
Route::get('{lang?}/contact-us', [ContactUsController::class, 'index'])->name('contact_us');
Route::get('{lang?}/chat/{departure}/to/{destination}/{id}/{passenger}', [ChatsController::class, 'index'])->name('chat');
Route::get('{lang?}/chat-detail/{id}/{passenger}', [ChatsController::class, 'chatDetail'])->name('chat_detail');
Route::get('/chat-messages/{id}/{userId}', [ChatsController::class, 'fetchMessages']);
Route::get('/chat-details/{userId}', [ChatsController::class, 'fetchChats']);
Route::get('{lang?}/my-chats', [MyChatsController::class, 'index'])->name('my_chats')->middleware('auth');
Route::get('{lang?}/old-chats', [MyChatsController::class, 'oldChats'])->name('old_chats')->middleware('auth');
Route::get('/delete-chats', [MyChatsController::class, 'deleteChat'])->name('delete_chats')->middleware('auth');
Route::get('{lang?}/close-account', [CloseAccountController::class, 'index'])->name('close_account');
Route::get('{lang?}/notifications', [NotificationController::class, 'notifications'])->name('notifications')->middleware('auth');
Route::get('read-notification', [NotificationController::class, 'readNotification'])->name('web.read_notifications')->middleware('auth');
Route::post('mark-all-notifications-read', [NotificationController::class, 'markAllAsRead'])->name('web.mark_all_notifications_read')->middleware('auth');
Route::delete('{lang?}/delete/notifications', [NotificationController::class, 'deleteNotification'])->name('delete_notifications')->middleware('auth');

Route::get('/subscription/success', [HomeController::class, 'paypalSuccessResponse'])->name('paypal.subscription.success');
Route::post('{lang?}/coffee-on-the-wall', [HomeController::class, 'coffeeOnWallStore']);
Route::post('seat-on-hold', [BookingController::class, 'seatOnHold'])->name('seat_on_hold');
Route::post('no-show-driver', [BookingController::class, 'noShowDriver'])->name('no_show_driver');
Route::post('revert-no-show-driver', [BookingController::class, 'revertNoShowDriver'])->name('revert_no_show_driver');
Route::post('no-show-passenger', [BookingController::class, 'noShowPassenger'])->name('no_show_passenger');
Route::post('revert-no-show-passenger', [BookingController::class, 'revertNoShowPassenger'])->name('revert_no_show_passenger');
Route::post('store-top-up-balance', [PassengerWalletController::class, 'storeTopUpBalance'])->name('store_top_up_balance')->middleware('auth');
Route::get('send-payout-request', [DriverWalletController::class, 'sendPayoutRequest'])->name('send_payout_request')->middleware('auth');
Route::put('secured-cash-code', [MyRideController::class, 'enterCode'])->name('secured_cash_code');
Route::get('/access-portal/{email}', [AccessPortalController::class, 'index']);
Route::post('/chat-messages', [ChatsController::class, 'sendMessage']);
Route::post('contact-us/store', [ContactUsController::class, 'store'])->name('contact_us.store');
Route::post('review-passenger/store/{id}', [ReviewController::class, 'StoreReviewPassenger'])->name('review_passenger.store');
Route::post('review-driver/store/{id}', [ReviewController::class, 'StoreReviewDriver'])->name('review_driver.store');
Route::post('review-reply/store/{id}', [ReviewController::class, 'ReviewReplyStore'])->name('review_reply.store');
Route::post('get-cities-by-state', [CountryStateCityController::class, 'getCity']);
Route::post('get-cities-distance', [CountryStateCityController::class, 'getCityDistance']);

// New API-based endpoints (uses free external API)
Route::post('get-cities-from-api', [CountryStateCityController::class, 'getCitiesFromApi']);
Route::post('get-states-from-api', [CountryStateCityController::class, 'getStatesFromApi']);
// Hybrid endpoint (tries database first, falls back to API)
Route::post('get-cities-hybrid', [CountryStateCityController::class, 'getCityHybrid']);

Route::post('payout/store', [PayoutController::class, 'store'])->name('payout.store');
Route::post('payout/verifyBank', [PayoutController::class, 'verifyBank'])->name('payout.verifyBank');
Route::post('profile/vehicle/store', [ProfileVehicleController::class, 'store'])->name('profile.vehicle.store');
Route::post('get-states-by-country', [CountryStateCityController::class, 'getState']);
Route::post('post-ride', [RideController::class, 'PostRideStore'])->name('post_ride.store')->middleware('auth');
Route::post('add-new-spots', [RideController::class, 'addNewSpots'])->name('post_ride.add_new_spot')->middleware('auth');
Route::post('delete-spots', [RideController::class, 'deleteSpots'])->name('post_ride.delete_spot')->middleware('auth');
Route::post('instant-booking/{id}', [BookingController::class, 'instantBooking'])->middleware('auth')->name('instant_booking');

Route::post('create-payment-intent', [BookingController::class, 'createPaymentIntent'])->middleware('auth')->name('createPaymentIntent');
Route::post('/create-subscription', [HomeController::class, 'createSubscription']);

Route::post('handleBookingRequest', [BookingController::class, 'handleBookingRequest'])->middleware('auth')->name('handleBookingRequest');

Route::get('/mobile-close-redirect', function () {
    \Illuminate\Support\Facades\Log::info('Mobile close redirect hit', [
        'user_agent' => request()->userAgent(),
    ]);
    return response()->json(['status' => 'ok']);
})->name('mobile_close_redirect');

Route::get('success-transaction/{id}/{type}/{seats}/{seats_amount}/{booking_credit}/{fare}/{online_payment}/{cash_payment}/{total}/{seats_id}/{coffee_wall}/{transactionTaxSum}/{ride}/{tax_amount}/{tax_percentage}/{tax_type}/{deduct_tax}', [BookingController::class, 'successTransaction'])->name('paypal.success');
Route::get('success-transaction-update/{id}/{seats}/{seats_amount}/{booking_credit}/{fare}/{online_payment}/{cash_payment}/{total}/{seats_id}/{coffee_wall}/{transactionTaxSum}/{ride}/{tax_amount}/{tax_percentage}/{tax_type}/{deduct_tax}', [BookingController::class, 'updateSuccessTransaction'])->name('update-paypal.success');
Route::get('success-transaction-booking-request/{id}/{type}/{seats}/{seats_amount}/{booking_credit}/{online_payment}/{cash_payment}/{total}/{seats_id}/{coffee_wall}/{transactionTaxSum}/{ride}/{tax_amount}/{tax_percentage}/{tax_type}/{deduct_tax}', [BookingController::class, 'successTransactionBookingRequest'])->name('paypal.success.booking_request');
Route::get('success-transaction-update-booking-request/{id}/{type}/{seats}/{seats_amount}/{booking_credit}/{online_payment}/{cash_payment}/{total}/{seats_id}/{coffee_wall}/{transactionTaxSum}/{ride}/{tax_amount}/{tax_percentage}/{tax_type}/{deduct_tax}', [BookingController::class, 'updateSuccessTransactionBookingRequest'])->name('paypal.success.update-booking_request');
Route::get('success-transaction-top-up/{dr_amount}', [PassengerWalletController::class, 'successTransaction'])->name('paypal.success.top-up');
Route::get('cancel-transaction', [BookingController::class, 'cancelTransaction'])->name('paypal.cancel');
Route::put('update-instant-booking/{id}', [BookingController::class, 'updateInstantBooking'])->middleware('auth')->name('update_instant_booking');
Route::post('booking-request/{id}', [BookingController::class, 'bookingRequest'])->middleware('auth')->name('booking_request');
Route::put('update-booking-request/{id}', [BookingController::class, 'updateBookingRequest'])->middleware('auth')->name('update_booking_request');
Route::put('update-cancel-booking/{id}', [BookingController::class, 'updateCancelBooking'])->middleware('auth')->name('update_cancel_booking');
Route::post('/cancel-ride/{id}', [MyRideController::class, 'cancelRide'])->name('cancel.ride');
Route::put('update-remove-passenger/{id}', [MyRideController::class, 'updateRemovePassenger'])->middleware('auth')->name('update_remove_passenger');
Route::put('update-cancel-ride/{id}', [MyRideController::class, 'updateCancelRide'])->middleware('auth')->name('update_cancel_ride');
Route::post('step3-5/store/{id}', [Step3to5Controller::class, 'store'])->middleware('auth')->name('step3to5.store');
Route::post('step4-5/store/{id}', [Step4to5Controller::class, 'store'])->middleware('auth')->name('step4to5.store');
Route::post('{lang?}/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('reset-password', [ResetPasswordController::class, 'store'])->middleware('guest')->name('update.password');
Route::post('{lang?}/forgot-password', [ForgotPasswordController::class, 'store'])->middleware('guest');
Route::post('{lang?}/signup', [SignupController::class, 'store'])->middleware('guest')->name('signup.store');
Route::put('close-account/update/{id}', [CloseAccountController::class, 'update'])->name('close_account.update');
Route::put('verify-student/update/{id}', [VerifyStudentController::class, 'update'])->name('student.verify.update');
Route::put('verify-driver/update/{id}', [VerifyDriverController::class, 'update'])->name('driver.verify.update');
Route::post('verify-driver/remove', [VerifyDriverController::class, 'remove'])->name('driver.verify.remove');
Route::put('password/update/{id}', [PasswordController::class, 'update'])->name('password.update');
Route::put('email/update/{userId}', [EmailController::class, 'update'])->name('email.update');
Route::post('phone/store', [PhoneController::class, 'store'])->name('phone.store');
Route::post('{lang?}/phone/store-and-verify', [PhoneController::class, 'storeAndVerify'])->name('phone.store_and_verify');
Route::get('phone/set-default/{id}', [PhoneController::class, 'setDefault'])->name('phone.set_default');
Route::get('phone/destroy/{id}', [PhoneController::class, 'destroy'])->name('phone.destroy');
Route::get('{lang?}/send-verification-code/{id}', [PhoneController::class, 'sendVerificationCode'])->name('send_verification_code');
Route::get('send-verification-code-booking/{id}', [BookingController::class, 'sendVerificationCodeBooking'])->name('send_verification_code_booking');
Route::post('verify-number', [PhoneController::class, 'verifyPhoneNumber'])->name('verify_number');
Route::post('resend-code', [PhoneController::class, 'resendCode'])->name('resend_code');
Route::post('my-cards/store', [CardController::class, 'store'])->name('my_cards.store');
Route::post('my-cards/create-setup-intent', [CardController::class, 'createSetupIntent'])->name('my_cards.create_setup_intent');
Route::get('primary-card/{id}', [CardController::class, 'primary'])->name('my_cards.set_primary');
Route::get('delete-card/{id}', [CardController::class, 'destroy'])->name('my_cards.destroy');
Route::put('booking-credits/update/{id}', [BookingCreditController::class, 'update'])->name('booking.credits.update');
Route::put('profile/vehicle/update/{id}', [ProfileVehicleController::class, 'update'])->name('profile.vehicle.update');
Route::put('profile/preferences/update/{id}', [ProfilePreferencesController::class, 'update'])->name('profile.preferences.update');
Route::put('profile/passenger-preferences/update/{id}', [ProfilePassengerPreferencesController::class, 'update'])->name('profile.passenger_preferences.update');
Route::put('profile/photo/update/{id}', [ProfilePhotoController::class, 'update'])->name('profile.photo.update');
Route::put('profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
Route::put('step5-5/update/{id}', [Step5to5Controller::class, 'update'])->middleware('auth')->name('step5to5.update');
Route::put('step4-5/update/{id}', [Step4to5Controller::class, 'update'])->middleware('auth')->name('step4to5.update');
Route::put('step2-5/update/{id}', [Step2to5Controller::class, 'update'])->middleware('auth')->name('step2to5.update');
Route::put('step1-5/update/{id}', [Step1to5Controller::class, 'update'])->middleware('auth')->name('step1to5.update');
Route::patch('/fcm-token', [HomeController::class, 'updateToken'])->name('fcmToken')->middleware('auth');

Route::get('/admin/{any}', [HomeController::class, 'redirectToAdminDashboard'])
    ->middleware(['admin.auth'])
    ->where('any', '.*');



use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Ride;

Route::get('/sitemap.xml', function () {
    $routes = Route::getRoutes();
    $urls = [];

    $getRides = Ride::with('defaultRideDetail')->where('status','0')->get();


    foreach ($routes as $route) {
        // Filter: named, GET, no required parameters
        if (
            in_array('web', $route->middleware()) &&
            in_array('GET', $route->methods()) &&
            $route->getName()
        ) {
            try {
                $urls[] = url(route($route->getName(), [], false));
            } catch (\Exception $e) {
                // skip broken or unresolvable routes
            }
        }
    }

    return response()->view('sitemap', [
        'urls' => $urls,
        'getRides' => $getRides,
        'lastmod' => Carbon::now()->toAtomString()
    ])->header('Content-Type', 'application/xml');
});


// Route::get('send-notification', function () {
//     $fcmService = new FCMService();
//     $message = 'You got a new notification';
//     $body = $message;

//     $user_id = auth()->user()->id;
//     $fcm_tokens = FCMToken::where('user_id',$user_id)->get();

//     foreach ($fcm_tokens as $fcm_token) {
//         $fcmService->sendNotification($fcm_token->token, $body);
//     }

//     return 'message sent';
// });

// Route::get('create-stripe-acc', function () {
//     \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

//     $customer = \Stripe\Customer::create([
//         'email' => 'info@xelent.io',
//         'name' => 'Xelent solution',
//     ]);

//     Log::info('stripe customer id = ' . $customer->id);

//     $paymentMethod = \Stripe\PaymentMethod::create([
//         'type' => 'card',
//         'card' => [
//             'number' => '4000056655665556',
//             'exp_month' => '12',
//             'exp_year' => '2029',
//             'cvc' => '1234',
//             ],
//         'billing_details' => [
//             'name' => 'Xelent Solutions',
//             ],
//             ]);

//             // Attach the PaymentMethod to the customer
//     \Stripe\PaymentMethod::attach(
//         $paymentMethod->id,
//         ['customer' => $customer->id]
//         );

//         $stripe_payment_method_id = $paymentMethod->id;
//         Log::info('stripe payment method id = ' . $stripe_payment_method_id);

// });
