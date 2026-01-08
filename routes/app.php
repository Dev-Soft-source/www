<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\App\AuthController;
use App\Http\Controllers\Api\App\RideController;
use App\Http\Controllers\Api\App\StepController;
use App\Http\Controllers\Api\App\EmailController;
use App\Http\Controllers\Api\App\PhoneController;
use App\Http\Controllers\Api\App\MyRideController;
use App\Http\Controllers\Api\App\MyTripController;
use App\Http\Controllers\Api\App\ReviewController;
use App\Http\Controllers\Api\App\SignupController;
use App\Http\Controllers\Api\App\WalletController;
use App\Http\Controllers\Api\App\ReferralController;
use App\Http\Controllers\Api\App\BookingController;
use App\Http\Controllers\Api\App\MyChatsController;
use App\Http\Controllers\Api\App\ProfileController;
use App\Http\Controllers\Api\App\InfoIconController;
use App\Http\Controllers\Api\App\LanguageController;
use App\Http\Controllers\Api\App\MyWalletController;
use App\Http\Controllers\Api\App\PasswordController;
use App\Http\Controllers\Api\App\ContactUsController;
use App\Http\Controllers\Api\App\BankDetailController;
use App\Http\Controllers\Api\App\CloseAccountController;
use App\Http\Controllers\Api\App\NotificationController;
use App\Http\Controllers\Api\App\ProfilePhotoController;
use App\Http\Controllers\Api\App\VerifyDriverController;
use App\Http\Controllers\Api\App\LogoutSettingController;
use App\Http\Controllers\Api\App\PostRideAgainController;
use App\Http\Controllers\Api\App\PostRideInitController;
use App\Http\Controllers\Api\App\ResetPasswordController;
use App\Http\Controllers\Api\App\VerifyStudentController;
use App\Http\Controllers\Api\App\BookingSettingController;
use App\Http\Controllers\Api\App\ForgotPasswordController;
use App\Http\Controllers\Api\App\LuggageSettingController;
use App\Http\Controllers\Api\App\MyEmailSettingController;
use App\Http\Controllers\Api\App\MyPhoneSettingController;
use App\Http\Controllers\Api\App\PaymentOptionsController;
use App\Http\Controllers\Api\App\PaymentSettingController;
use App\Http\Controllers\Api\App\ProfileSettingController;
use App\Http\Controllers\Api\App\ProfileVehicleController;
use App\Http\Controllers\Api\App\FeaturesSettingController;
use App\Http\Controllers\Api\App\MyDriverSettingController;
use App\Http\Controllers\Api\App\MyReviewSettingController;
use App\Http\Controllers\Api\App\MyWalletSettingController;
use App\Http\Controllers\Api\App\PasswordSettingController;
use App\Http\Controllers\Api\App\CountryStateCityController;
use App\Http\Controllers\Api\App\MyVehicleSettingController;
use App\Http\Controllers\Api\App\MyPassengerSettingController;
use App\Http\Controllers\Api\App\PreferencesSettingController;
use App\Http\Controllers\Api\App\ProfilePageSettingController;
use App\Http\Controllers\Api\App\PayoutOptionSettingController;
use App\Http\Controllers\Api\App\ProfilePhotoSettingController;
use App\Http\Controllers\Api\App\BillingAdressSettingController;
use App\Http\Controllers\Api\App\MyStudentCardSettingController;
use App\Http\Controllers\Api\App\PaymentOptionSettingController;
use App\Http\Controllers\Api\App\CloseMyAccountSettingController;
use App\Http\Controllers\Api\App\EditProfilePageSettingController;
use App\Http\Controllers\Api\App\ContactProximaRideSettingController;

Route::group(['prefix' => 'app/v1'], function () {

    Route::get('languages', [LanguageController::class, 'index'])->name('app.auth.languages');
    Route::get('login-page', [AuthController::class, 'create'])->name('app.auth.login_page');
    Route::get('signup-page', [SignupController::class, 'create'])->name('app.auth.signup_page');
    Route::get('forgot-password-page', [ForgotPasswordController::class, 'create'])->name('app.auth.forgot_password_page');
    Route::get('reset-password-page', [ResetPasswordController::class, 'create'])->name('app.auth.reset_password_page');
    Route::get('post-ride-page', [RideController::class, 'postRideIndex'])->name('app.auth.post_ride_page');
    Route::get('find-ride-page', [RideController::class, 'findRideIndex'])->name('app.auth.find_ride_page');
    Route::get('step1-page', [StepController::class, 'step1Index'])->name('app.auth.step1_page');
    Route::get('step2-page', [StepController::class, 'step2Index'])->name('app.auth.step2_page');
    Route::get('step3-page', [StepController::class, 'step3Index'])->name('app.auth.step3_page');
    Route::get('step4-page', [StepController::class, 'step4Index'])->name('app.auth.step4_page');
    Route::get('chats-page', [MyChatsController::class, 'chatsIndex'])->name('app.auth.chats_page');
    Route::post('delete-chat', [MyChatsController::class, 'deleteChat'])->name('app.auth.delete_chat');
    Route::get('trips-page', [MyTripController::class, 'tripsIndex'])->name('app.auth.trips_page');
    Route::get('thank-you-page', [PreferencesSettingController::class, 'thankyouIndex'])->name('app.auth.thank_you_page');
    Route::post('login', [AuthController::class, 'login'])->name('app.auth.login');
    Route::post('social-login', [AuthController::class, 'redirectToProvider'])->name('app.auth.social_login');
    Route::post('signup', [SignupController::class, 'store'])->name('app.auth.signup');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('app.auth.forgot_password');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('app.auth.reset_password');
    Route::get('ride-detail', [RideController::class, 'RideDetail'])->name('app.auth.ride_detail');
    Route::get('co-passengers', [RideController::class, 'coPassengers'])->name('app.auth.ride_detail');
    Route::get('driver-info', [ProfileController::class, 'driverInfo'])->name('app.auth.driver_info');
    Route::get('countries', [CountryStateCityController::class, 'getCountries'])->name('app.auth.countries');
    Route::get('states', [CountryStateCityController::class, 'getStates'])->name('app.auth.states');
    Route::get('cities', [CountryStateCityController::class, 'getCities'])->name('app.auth.cities');
    Route::post('get-cities-distance', [CountryStateCityController::class, 'getCityDistance'])->name('app.auth.getCityDistance');
    Route::get('preferences-options', [PreferencesSettingController::class, 'preferencesOptions'])->name('app.auth.preferences-options');
    Route::get('post-ride-features-options', [FeaturesSettingController::class, 'postRideFeaturesOptions'])->name('app.auth.post-ride-features-options');
    Route::get('find-ride-features-options', [FeaturesSettingController::class, 'findRideFeaturesOptions'])->name('app.auth.find-ride-features-options');
    Route::get('payment-options', [PaymentSettingController::class, 'index'])->name('app.auth.payment-options');
    Route::get('luggage-options', [LuggageSettingController::class, 'index'])->name('app.auth.luggage-options');
    Route::get('cancellation-options', [PreferencesSettingController::class, 'cancellationOptions'])->name('app.auth.cancellation-options');
    Route::get('booking-options', [BookingSettingController::class, 'index'])->name('app.auth.booking-options');
    Route::get('pink-ride-info-icon', [InfoIconController::class, 'pinkRideInfo'])->name('app.auth.pink-ride-info-icon');
    Route::get('extra-care-ride-info-icon', [InfoIconController::class, 'extraCareRideInfo'])->name('app.auth.extra-care-ride-info-icon');
    Route::post('contact-us/store', [ContactUsController::class, 'store'])->name('app.auth.contact_us.store');


    Route::get('profile-page-setting', [ProfilePageSettingController::class, 'findProfilePageSettingIndex'])->name('app.auth.profile_page_setting');

    Route::get('my-wallet-setting', [MyWalletSettingController::class, 'WalletSettingIndex'])->name('app.auth.my_wallet_setting');
    Route::get('my-email-setting', [MyEmailSettingController::class, 'EmailSettingIndex'])->name('app.auth.my_email_setting');
    Route::get('my-phone-setting', [MyPhoneSettingController::class, 'MyPhonePageSettingIndex'])->name('app.auth.my_phone_setting');
    Route::get('my-driver-setting', [MyDriverSettingController::class, 'DriverPageSettingIndex'])->name('app.auth.my_driver_setting');
    Route::get('password-setting', [PasswordSettingController::class, 'PasswordPageSettingIndex'])->name('app.auth.password_setting');
    Route::get('my-vehicle-setting', [MyVehicleSettingController::class, 'VehicleSettingIndex'])->name('app.auth.my_vehicle_setting');
    Route::get('payment-option-setting', [PaymentOptionSettingController::class, 'PaymentPageSettingIndex'])->name('app.auth.payment_option_setting');
    Route::get('payout-option-setting', [PayoutOptionSettingController::class, 'PayoutPageSettingIndex'])->name('app.auth.payout_option_setting');
    Route::get('my-student-card-setting', [MyStudentCardSettingController::class, 'StudentPageSettingIndex'])->name('app.auth.my_student_card_setting');
    Route::get('profile-photo-setting', [ProfilePhotoSettingController::class, 'ProfilePhotoSettingIndex'])->name('app.auth.profile_photo_setting');
    Route::get('profile-setting', [ProfileSettingController::class, 'ProfilePageSettingIndex'])->name('app.auth.profile_setting');
    Route::get('contact-proximaride-setting', [ContactProximaRideSettingController::class , 'ContactProximaRidePageSettingIndex'])->name('app.auth.contact_proximaride_setting');
    Route::get('logout-setting', [LogoutSettingController::class, 'LogoutPageSettingIndex'])->name('app.auth.logout_setting');
    Route::get('close-my-account-setting', [CloseMyAccountSettingController::class, 'CloseAccountPageSettingIndex'])->name('app.auth.close_my_account_setting');
    Route::get('edit-profile-page-setting', [EditProfilePageSettingController::class, 'EditProfilePageSettingIndex'])->name('app.auth.edit_profile_page_setting');
    Route::get('billing-address-setting', [BillingAdressSettingController::class, 'billingAddressSettingPage'])->name('app.auth.billing_address_setting');
    Route::get('my-review-setting', [MyReviewSettingController::class, 'reviewSettingPage'])->name('app.auth.my_review_setting');
    Route::get('my-passenger-setting', [MyPassengerSettingController::class, 'myPassengerPage'])->name('app.auth.my_passenger_setting');

});

Route::group(['prefix' => 'app/v1', 'middleware' => ['auth:sanctum']], function () {
    Route::get('user', [AuthController::class, 'show'])->name('app.auth.show');
    Route::post('update-user-language', [AuthController::class, 'updateUserLanguage'])->name('app.auth.show');
    Route::get('check-status', [AuthController::class, 'checkStatus'])->name('app.auth.check_status');
    Route::put('step1-5/update', [StepController::class, 'step1to5'])->name('app.auth.step1to5.update');
    Route::post('step2-5/update', [StepController::class, 'step2to5'])->name('app.auth.step2to5.update');
    Route::post('step3-5/update', [StepController::class, 'step3to5'])->name('app.auth.step3to5.update');
    Route::get('search-ride', [RideController::class, 'SearchRide'])->name('app.auth.search_ride');
    Route::get('check-booking', [RideController::class, 'checkBooking'])->name('app.auth.check_booking');
    Route::get('booking', [BookingController::class, 'create'])->name('app.auth.booking');
    Route::get('secured/booking-number-check', [BookingController::class, 'bookingNumberCheck'])->name('app.auth.booking-number-check');
    Route::post('instant-booking', [BookingController::class, 'instantBooking'])->name('app.auth.instant_booking');
    Route::post('seat-on-hold', [BookingController::class, 'seatOnHold'])->name('app.auth.seat_on_hold');
    Route::post('create-payment-intent', [BookingController::class, 'createPaymentIntent'])->name('app.auth.create_payment_intent');
    Route::put('update-instant-booking', [BookingController::class, 'updateInstantBooking'])->name('app.auth.update_instant_booking');
    Route::post('booking-request', [BookingController::class, 'bookingRequest'])->name('app.auth.booking_request');
    Route::put('update-booking-request', [BookingController::class, 'updateBookingRequest'])->name('app.auth.update_booking_request');
    Route::get('accept-booking-request', [BookingController::class, 'AcceptBookingRequest'])->name('app.auth.accept_booking_request');
    Route::get('reject-booking-request', [BookingController::class, 'RejectBookingRequest'])->name('app.auth.reject_booking_request');
    Route::post('no-show', [RideController::class, 'noShow'])->name('app.auth.no_show');
    Route::get('post-ride', [RideController::class, 'PostRide'])->name('app.auth.post_ride');
    Route::get('post-ride-setting', [InfoIconController::class, 'postRideSetting'])->name('app.auth.post-ride-setting');
    Route::get('post-ride-init', [PostRideInitController::class, 'getInitData'])->name('app.auth.post-ride-init');
    Route::get('select-location-setting', [InfoIconController::class, 'selectLocationSetting'])->name('app.auth.select-location-setting');
    Route::post('post-ride', [RideController::class, 'PostRideStore'])->name('app.auth.post_ride.store');
    Route::get('edit-ride', [RideController::class, 'EditRide'])->name('app.auth.edit_ride');
    Route::put('update-ride', [RideController::class, 'UpdateRide'])->name('app.auth.update_ride');
    Route::get('post-ride-again-upcoming', [PostRideAgainController::class, 'CurrentRides'])->name('app.auth.post_ride_again');
    Route::get('post-ride-again-completed', [PostRideAgainController::class, 'PastRides'])->name('app.auth.post_ride_again_completed');
    Route::get('post-ride-again-cancelled', [PostRideAgainController::class, 'CancelledRides'])->name('app.auth.post_ride_again_cancelled');
    Route::get('welcome-on-board', [ProfileController::class, 'index'])->name('app.auth.my_profile');
    Route::get('profile', [ProfileController::class, 'profilePage'])->name('app.auth.profile_page');
    Route::post('review-reply/store', [ReviewController::class, 'ReviewReplyStore'])->name('app.auth.review_reply.store');
    Route::get('all-reviews', [ReviewController::class, 'allReviews'])->name('app.auth.all-reviews');
    Route::get('edit-profile', [ProfileController::class, 'edit'])->name('app.auth.profile.edit');
    Route::put('update-profile', [ProfileController::class, 'update'])->name('app.auth.profile.update');
    Route::get('profile-photo', [ProfilePhotoController::class, 'index'])->name('app.auth.profile.photo');
    Route::put('profile-photo/update', [ProfilePhotoController::class, 'update'])->name('app.auth.profile.photo.update');
    Route::get('my-vehicles', [ProfileVehicleController::class, 'index'])->name('app.auth.my_vehicles');
    Route::post('vehicle/store', [ProfileVehicleController::class, 'store'])->name('app.auth.profile.vehicle.store');
    Route::get('vehicle/edit', [ProfileVehicleController::class, 'edit'])->name('app.auth.profile.vehicle.edit');
    Route::put('vehicle/update', [ProfileVehicleController::class, 'update'])->name('app.auth.profile.vehicle.update');
    Route::delete('vehicle/delete', [ProfileVehicleController::class, 'destroy'])->name('app.auth.profile.vehicle.destroy');
    Route::put('update-password', [PasswordController::class, 'update'])->name('app.auth.password.update');
    Route::get('phone-numbers', [PhoneController::class, 'index'])->name('app.auth.phone_numbers');
    Route::post('phone-number', [PhoneController::class, 'store'])->name('app.auth.phone_number.store');
    Route::post('send-verification-code', [PhoneController::class, 'sendVerificationCode'])->name('app.auth.send_verification_code');
    Route::put('verify-phone-number', [PhoneController::class, 'verifyPhoneNumber'])->name('app.auth.verify_phone_number');
    Route::put('set-default', [PhoneController::class, 'setDefault'])->name('app.auth.set_default');
    Route::delete('phone/delete', [PhoneController::class, 'destroy'])->name('app.auth.phone.destroy');
    Route::put('update-email', [EmailController::class, 'update'])->name('app.auth.email.update');
    Route::get('verify-driver', [VerifyDriverController::class, 'index'])->name('app.auth.driver_verify');
    Route::put('verify-driver/update', [VerifyDriverController::class, 'update'])->name('app.auth.driver_verify.update');
    Route::get('remove-driver-license', [VerifyDriverController::class, 'remove'])->name('app.auth.remove_driver_license');
    Route::get('verify-student', [VerifyStudentController::class, 'index'])->name('app.auth.student_verify');
    Route::put('verify-student/update', [VerifyStudentController::class, 'update'])->name('app.auth.student_verify.update');
    Route::get('remove-student-card', [VerifyStudentController::class, 'remove'])->name('app.auth.remove_student_card');
    Route::get('wallet-passenger-ride', [MyWalletController::class, 'passengerRide'])->name('app.auth.wallet_passenger_ride');
    Route::get('profile-payment-options', [PaymentOptionsController::class, 'index'])->name('app.auth.profile-payment-options');
    Route::post('payment-option/store', [PaymentOptionsController::class, 'store'])->name('app.auth.payment-option.store');
    Route::get('edit-card', [PaymentOptionsController::class, 'edit'])->name('app.auth.edit-card');
    Route::put('update-card', [PaymentOptionsController::class, 'update'])->name('app.auth.update-card');
    Route::delete('delete-card', [PaymentOptionsController::class, 'destroy'])->name('app.auth.delete-card');
    Route::post('payment-option/set-primary', [PaymentOptionsController::class, 'setCardPrimary'])->name('app.auth.payment-option.setPrimary');
    Route::get('reviews-received', [ReviewController::class, 'ReviewsReceived'])->name('app.auth.reviews_received');
    Route::get('reviews-left', [ReviewController::class, 'ReviewsLeft'])->name('app.auth.reviews_left');
    Route::put('close-account', [CloseAccountController::class, 'update'])->name('app.auth.close_account');
    Route::get('review', [ReviewController::class, 'index'])->name('app.auth.review_index');
    Route::get('review-driver', [ReviewController::class, 'ReviewDriver'])->name('app.auth.review_driver');
    Route::post('review-driver/store', [ReviewController::class, 'StoreReviewDriver'])->name('app.auth.review_driver.store');
    Route::get('review-passenger', [ReviewController::class, 'ReviewPassenger'])->name('app.auth.review_passenger');
    Route::post('review-passenger/store', [ReviewController::class, 'StoreReviewPassenger'])->name('app.auth.review_passenger.store');
    Route::get('upcoming-trips', [MyTripController::class, 'CurrentTrips'])->name('app.auth.my_trips');
    Route::get('completed-trips', [MyTripController::class, 'PastTrips'])->name('app.auth.past_trips');
    Route::get('cancelled-trips', [MyTripController::class, 'CancelledTrips'])->name('app.auth.cancelled_trips');
    Route::put('cancel-booking', [MyTripController::class, 'cancelBooking'])->name('app.auth.cancel_booking');
    Route::get('upcoming-rides', [MyRideController::class, 'CurrentRides'])->name('app.auth.my_rides');
    Route::get('completed-rides', [MyRideController::class, 'PastRides'])->name('app.auth.past_rides');
    Route::get('cancelled-rides', [MyRideController::class, 'CancelledRides'])->name('app.auth.cancelled_rides');
    Route::get('my-passengers', [MyRideController::class, 'MyPassengers'])->name('app.auth.my_passengers');
    Route::put('remove-passenger', [MyRideController::class, 'removePassenger'])->name('app.auth.remove_passenger');
    Route::get('passenger-profile', [ProfileController::class, 'passengerInfo'])->name('app.auth.passenger_info');
    Route::put('secured-cash-code', [MyRideController::class, 'enterCode'])->name('app.auth.secured_cash_code');
    Route::put('cancel-ride', [MyRideController::class, 'CancelRide'])->name('app.auth.cancel_ride');
    Route::get('my-chats', [MyChatsController::class, 'index'])->name('app.auth.my_chats');
    Route::get('old-chats', [MyChatsController::class, 'oldChats'])->name('app.auth.old_chats');
    Route::get('chat-detail', [MyChatsController::class, 'chatDetail'])->name('app.auth.chat_detail');
    Route::post('chat/store', [MyChatsController::class, 'store'])->name('app.auth.chat.store');
    Route::get('notifications', [NotificationController::class, 'index'])->name('app.auth.notifications');
    Route::get('read-notification', [NotificationController::class, 'readNotification'])->name('app.auth.read_notifications');
    Route::post('add-token', [NotificationController::class, 'addToken'])->name('app.auth.add_token');
    Route::post('remove-token', [NotificationController::class, 'removeToken'])->name('app.auth.add_token');

    Route::get('delete/notifications', [NotificationController::class, 'deleteAppNotification'])->name('app.auth.delete-notifications');

    Route::post('online-status', [StepController::class, 'online_status'])->name('app.auth.online_status');
    Route::get('bank-detail', [BankDetailController::class, 'getBankDetail'])->name('app.auth.get-bank-detail');
    Route::post('bank-detail/storeUpdateBankDetail', [BankDetailController::class, 'storeUpdateBankDetail'])->name('app.auth.get-bank-detail.storeUpdateBankDetail');
    Route::post('bank-detail/verifyBank', [BankDetailController::class, 'verifyBank'])->name('app.auth.get-bank-detail.verifyBank');

    Route::get('passenger-my-rides', [WalletController::class, 'passengerMyRides'])->name('app.auth.passenger_my_rides');

    Route::get('my-referral-list', [ReferralController::class, 'myReferralList'])->name('app.auth.my_referral_list');

    Route::get('student-reward-points', [WalletController::class, 'studentRewardPoints'])->name('app.auth.student_reward_points');

    Route::post('claim-my-reward', [WalletController::class, 'claimMyReward'])->name('app.auth.claim_my_reward');

    Route::get('driver-paid-out', [WalletController::class, 'driverPaidout'])->name('app.auth.driver_paid_out');

    Route::get('driver-available-balance', [WalletController::class, 'driverAvailableBalance'])->name('app.auth.driver_available_balance');

    Route::get('driver-pending-balance', [WalletController::class, 'driverPendingBalance'])->name('app.auth.driver_pending_balance');

    Route::get('driver-reward-points', [WalletController::class, 'driverRewardPoints'])->name('app.auth.driver_reward_points');

    Route::get('send-payout-request', [WalletController::class, 'sendPayoutRequest'])->name('app.auth.send_payout_request');

    Route::get('get-top-up-balance', [WalletController::class, 'getTopUpBalance'])->name('app.auth.get_top_up_balance');

    Route::post('store-top-up-balance', [WalletController::class, 'storeTopUpBalance'])->name('app.auth.store_top_up_balance');
});
