<?php

use App\Http\Controllers\Api\Admin\{
    ArticleController,
    AuthController,
    BankController,
    DriverRewardPointController,
    StudentRewardPointController,
    BankSettingController,
    LanguageController,
    UserController,
    DriverVerificationController,
    StudentVerificationController,
    RideController,
    BookingController,
    ReviewController,
    TransactionController,
    WithdrawalRequestController,
    CoffeeWalletController,
    BookingCreditController,
    BookingPageSettingController,
    ReferralPageSettingController,
    CancellationPolicyPageSettingController,
    CancelRideSettingController,
    RegistrationRewardSettingController,
    ChatsPageSettingController,
    CityController,
    ClosedAccountMessageController,
    ContactUsPageSettingController,
    CountryController,
    DisputePolicyPageSettingController,
    DriverPageSettingController,
    FeaturesSettingController,
    FindRidePageSettingController,
    FolkRideSettingController,
    ForgotPasswordPageSettingController,
    HomePageSettingController,
    LoginPageSettingController,
    LuggageOptionsSettingController,
    MediaController,
    MessageController,
    MobileFindRideSettingController,
    MobileForgotPasswordSettingController,
    MobileLoginSettingController,
    MobilePostRideSettingController,
    MobileResetPasswordSettingController,
    MobileSignupSettingController,
    PagesController,
    PassengerController,
    PassengerPageSettingController,
    PaymentMethodsSettingController,
    PinkRideSettingController,
    PostRidePageSettingController,
    PreferencesSettingController,
    PrivacyPolicyPageSettingController,
    ReferralSystemSettingController,
    RefundPolicyPageSettingController,
    ResetPasswordPageSettingController,
    ReviewSettingController,
    RideDetailPageSettingController,
    SignupPageSettingController,
    SiteSettingController,
    StateController,
    Step1PageSettingController,
    Step2PageSettingController,
    Step3PageSettingController,
    Step4PageSettingController,
    Step5PageSettingController,
    StudentPageSettingController,
    TermsAndConditionPageSettingController,
    TermsOfUsePageSettingController,
    TripsPageSettingController,
    VerifyBanksController,
    ProfilePageSettingController,
    VideoController,PasswordSettingController,MyWalletSettingController,MyVehicleSettingController,MyEmailSettingController,MyPhoneSettingController,
    MyDriverSettingController,
    LogoutSettingController,ContactUsSettingController,
    CloseAccountSettingController,PayoutOptionSettingController,PaymentOptionSettingController,
    ProfilePhotoSettingController,EditProfilePageSettingController,MyStudentCardSettingController,MyReviewSettingController,
    SuccessMessagesSettingController,ErrorController,BillingAdressSettingController, ClaimRewardsController, CoffeeWallPageSettingController, ExtraCareFaqController, FirmCancellationPolicyPageSettingController, ProfileSettingController,
    SecuredCashBookingController,
    VerifyPhonesController,MyPassengerSettingController,
    NoShowController,
    PackageController,
    PinkRideFaqController,
    SelectLocationPageSettingController
};
use App\Http\Resources\Admin\AdminResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin'], function () {
    Route::get('/user', function (Request $request) {
        $admin = $request->user('admin');
        if (!$admin) {
            return response()->json(['data' => null, 'status' => 'Error', 'message' => 'Admin not authenticated'], 401);
        }
        $user = new AdminResource($admin);
        return response()->json(['data' => $user, 'status' => 'Success']);
    });
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/update-mobile-login-page-setting', [MobileLoginSettingController::class, 'update']);
    Route::get('/get-mobile-login-page-setting', [MobileLoginSettingController::class, 'show']);
    Route::post('/update-mobile-signup-page-setting', [MobileSignupSettingController::class, 'update']);
    Route::get('/get-mobile-signup-page-setting', [MobileSignupSettingController::class, 'show']);
    Route::post('/update-mobile-forgot-password-page-setting', [MobileForgotPasswordSettingController::class, 'update']);
    Route::get('/get-mobile-forgot-password-page-setting', [MobileForgotPasswordSettingController::class, 'show']);
    Route::post('/update-mobile-reset-password-page-setting', [MobileResetPasswordSettingController::class, 'update']);
    Route::get('/get-mobile-reset-password-page-setting', [MobileResetPasswordSettingController::class, 'show']);
    Route::post('/update-mobile-post-ride-page-setting', [MobilePostRideSettingController::class, 'update']);
    Route::get('/get-mobile-post-ride-page-setting', [MobilePostRideSettingController::class, 'show']);
    Route::post('/update-mobile-find-ride-page-setting', [MobileFindRideSettingController::class, 'update']);
    Route::get('/get-mobile-find-ride-page-setting', [MobileFindRideSettingController::class, 'show']);
    Route::post('/update-success-messages-setting', [SuccessMessagesSettingController::class, 'update']);
    Route::get('/get-success-messages-setting', [SuccessMessagesSettingController::class, 'show']);
    Route::post('/update-home-page-setting', [HomePageSettingController::class, 'update']);
    Route::get('/get-home-page-setting', [HomePageSettingController::class, 'show']);
    Route::post('/upload-home-page-setting-excel', [HomePageSettingController::class, 'uploadExcel']);
    Route::get('/download-home-page-setting-template', [HomePageSettingController::class, 'downloadTemplate']);
    Route::post('/update-student-page-setting', [StudentPageSettingController::class, 'update']);
    Route::get('/get-student-page-setting', [StudentPageSettingController::class, 'show']);
    Route::post('/upload-student-page-setting-excel', [StudentPageSettingController::class, 'uploadExcel']);
    Route::get('/download-student-page-setting-template', [StudentPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-driver-page-setting', [DriverPageSettingController::class, 'update']);
    Route::get('/get-driver-page-setting', [DriverPageSettingController::class, 'show']);
    Route::post('/upload-driver-page-setting-excel', [DriverPageSettingController::class, 'uploadExcel']);
    Route::get('/download-driver-page-setting-template', [DriverPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-passenger-page-setting', [PassengerPageSettingController::class, 'update']);
    Route::get('/get-passenger-page-setting', [PassengerPageSettingController::class, 'show']);
    Route::post('/upload-passenger-page-setting-excel', [PassengerPageSettingController::class, 'uploadExcel']);
    Route::get('/download-passenger-page-setting-template', [PassengerPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-find-ride-page-setting', [FindRidePageSettingController::class, 'update']);
    Route::get('/get-find-ride-page-setting', [FindRidePageSettingController::class, 'show']);
    Route::post('/upload-find-ride-page-setting-excel', [FindRidePageSettingController::class, 'uploadExcel']);
    Route::get('/download-find-ride-page-setting-template', [FindRidePageSettingController::class, 'downloadTemplate']);
    Route::post('/update-ride-detail-page-setting', [RideDetailPageSettingController::class, 'update']);
    Route::get('/get-ride-detail-page-setting', [RideDetailPageSettingController::class, 'show']);
    Route::post('/upload-ride-detail-page-setting-excel', [RideDetailPageSettingController::class, 'uploadExcel']);
    Route::get('/download-ride-detail-page-setting-template', [RideDetailPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-booking-page-setting', [BookingPageSettingController::class, 'update']);
    Route::get('/get-booking-page-setting', [BookingPageSettingController::class, 'show']);
    Route::post('/upload-booking-page-setting-excel', [BookingPageSettingController::class, 'uploadExcel']);
    Route::get('/download-booking-page-setting-template', [BookingPageSettingController::class, 'downloadTemplate']);

    Route::post('/update-referral-page-setting', [ReferralPageSettingController::class, 'update']);
    Route::get('/get-referral-page-setting', [ReferralPageSettingController::class, 'show']);
    Route::post('/upload-referral-page-setting-excel', [ReferralPageSettingController::class, 'uploadExcel']);
    Route::get('/download-referral-page-setting-template', [ReferralPageSettingController::class, 'downloadTemplate']);

    Route::post('/update-post-ride-page-setting', [PostRidePageSettingController::class, 'update']);
    Route::get('/get-post-ride-page-setting', [PostRidePageSettingController::class, 'show']);
    Route::post('/upload-post-ride-page-setting-excel', [PostRidePageSettingController::class, 'uploadExcel']);
    Route::get('/download-post-ride-page-setting-template', [PostRidePageSettingController::class, 'downloadTemplate']);
    Route::post('/update-features-setting', [FeaturesSettingController::class, 'update']);
    Route::get('/get-features-setting', [FeaturesSettingController::class, 'show']);
    Route::post('/upload-features-setting-excel', [FeaturesSettingController::class, 'uploadExcel']);
    Route::get('/download-features-setting-template', [FeaturesSettingController::class, 'downloadTemplate']);
    Route::post('/update-preferences-setting', [PreferencesSettingController::class, 'update']);
    Route::post('/update-payment-methods-setting', [PaymentMethodsSettingController::class, 'update']);
    Route::post('/upload-payment-methods-setting-excel', [PaymentMethodsSettingController::class, 'uploadExcel']);
    Route::get('/download-payment-methods-setting-template', [PaymentMethodsSettingController::class, 'downloadTemplate']);
    Route::post('/update-luggage-options-setting', [LuggageOptionsSettingController::class, 'update']);
    Route::post('/upload-luggage-options-setting-excel', [LuggageOptionsSettingController::class, 'uploadExcel']);
    Route::get('/download-luggage-options-setting-template', [LuggageOptionsSettingController::class, 'downloadTemplate']);
    Route::post('/update-login-page-setting', [LoginPageSettingController::class, 'update']);
    Route::get('/get-login-page-setting', [LoginPageSettingController::class, 'show']);
    Route::post('/upload-login-page-setting-excel', [LoginPageSettingController::class, 'uploadExcel']);
    Route::get('/download-login-page-setting-template', [LoginPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-forgot-password-page-setting', [ForgotPasswordPageSettingController::class, 'update']);
    Route::get('/get-forgot-password-page-setting', [ForgotPasswordPageSettingController::class, 'show']);
    Route::post('/upload-forgot-password-page-setting-excel', [ForgotPasswordPageSettingController::class, 'uploadExcel']);
    Route::get('/download-forgot-password-page-setting-template', [ForgotPasswordPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-reset-password-page-setting', [ResetPasswordPageSettingController::class, 'update']);
    Route::get('/get-reset-password-page-setting', [ResetPasswordPageSettingController::class, 'show']);
    Route::post('/upload-reset-password-page-setting-excel', [ResetPasswordPageSettingController::class, 'uploadExcel']);
    Route::get('/download-reset-password-page-setting-template', [ResetPasswordPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-signup-page-setting', [SignupPageSettingController::class, 'update']);
    Route::get('/get-signup-page-setting', [SignupPageSettingController::class, 'show']);
    Route::post('/upload-signup-page-setting-excel', [SignupPageSettingController::class, 'uploadExcel']);
    Route::get('/download-signup-page-setting-template', [SignupPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-step1-page-setting', [Step1PageSettingController::class, 'update']);
    Route::get('/get-step1-page-setting', [Step1PageSettingController::class, 'show']);
    Route::post('/upload-step1-page-setting-excel', [Step1PageSettingController::class, 'uploadExcel']);
    Route::get('/download-step1-page-setting-template', [Step1PageSettingController::class, 'downloadTemplate']);
    Route::post('/update-step2-page-setting', [Step2PageSettingController::class, 'update']);
    Route::get('/get-step2-page-setting', [Step2PageSettingController::class, 'show']);
    Route::post('/upload-step2-page-setting-excel', [Step2PageSettingController::class, 'uploadExcel']);
    Route::get('/download-step2-page-setting-template', [Step2PageSettingController::class, 'downloadTemplate']);
    Route::post('/update-step3-page-setting', [Step3PageSettingController::class, 'update']);
    Route::get('/get-step3-page-setting', [Step3PageSettingController::class, 'show']);
    Route::post('/upload-step3-page-setting-excel', [Step3PageSettingController::class, 'uploadExcel']);
    Route::get('/download-step3-page-setting-template', [Step3PageSettingController::class, 'downloadTemplate']);
    Route::get('/get-step2-page-setting', [Step2PageSettingController::class, 'show']);
    Route::post('/update-step4-page-setting', [Step4PageSettingController::class, 'update']);
    Route::get('/get-step4-page-setting', [Step4PageSettingController::class, 'show']);
    Route::post('/update-step5-page-setting', [Step5PageSettingController::class, 'update']);
    Route::get('/get-step5-page-setting', [Step5PageSettingController::class, 'show']);
    Route::post('/update-contact-us-page-setting', [ContactUsPageSettingController::class, 'update']);
    Route::get('/get-contact-us-page-setting', [ContactUsPageSettingController::class, 'show']);
    Route::post('/update-terms-and-condition-page-setting', [TermsAndConditionPageSettingController::class, 'update']);
    Route::get('/get-terms-and-condition-page-setting', [TermsAndConditionPageSettingController::class, 'show']);
    Route::post('/upload-terms-and-condition-page-setting-excel', [TermsAndConditionPageSettingController::class, 'uploadExcel']);
    Route::get('/download-terms-and-condition-page-setting-template', [TermsAndConditionPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-privacy-policy-page-setting', [PrivacyPolicyPageSettingController::class, 'update']);
    Route::get('/get-privacy-policy-page-setting', [PrivacyPolicyPageSettingController::class, 'show']);
    Route::post('/upload-privacy-policy-page-setting-excel', [PrivacyPolicyPageSettingController::class, 'uploadExcel']);
    Route::get('/download-privacy-policy-page-setting-template', [PrivacyPolicyPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-terms-of-use-page-setting', [TermsOfUsePageSettingController::class, 'update']);
    Route::get('/get-terms-of-use-page-setting', [TermsOfUsePageSettingController::class, 'show']);
    Route::post('/upload-terms-of-use-page-setting-excel', [TermsOfUsePageSettingController::class, 'uploadExcel']);
    Route::get('/download-terms-of-use-page-setting-template', [TermsOfUsePageSettingController::class, 'downloadTemplate']);
    Route::post('/update-refund-policy-page-setting', [RefundPolicyPageSettingController::class, 'update']);
    Route::get('/get-refund-policy-page-setting', [RefundPolicyPageSettingController::class, 'show']);
    Route::post('/upload-refund-policy-page-setting-excel', [RefundPolicyPageSettingController::class, 'uploadExcel']);
    Route::get('/download-refund-policy-page-setting-template', [RefundPolicyPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-cancellation-page-setting', [CancellationPolicyPageSettingController::class, 'update']);
    Route::get('/get-cancellation-page-setting', [CancellationPolicyPageSettingController::class, 'show']);
    Route::post('/upload-cancellation-page-setting-excel', [CancellationPolicyPageSettingController::class, 'uploadExcel']);
    Route::get('/download-cancellation-page-setting-template', [CancellationPolicyPageSettingController::class, 'downloadTemplate']);

    Route::get('/get-firm-cancellation-page-setting', [FirmCancellationPolicyPageSettingController::class, 'show']);
    Route::post('/update-firm-cancellation-page-setting', [FirmCancellationPolicyPageSettingController::class, 'update']);
    Route::post('/upload-firm-cancellation-page-setting-excel', [FirmCancellationPolicyPageSettingController::class, 'uploadExcel']);
    Route::get('/download-firm-cancellation-page-setting-template', [FirmCancellationPolicyPageSettingController::class, 'downloadTemplate']);

    Route::post('/update-dispute-page-setting', [DisputePolicyPageSettingController::class, 'update']);
    Route::get('/get-dispute-page-setting', [DisputePolicyPageSettingController::class, 'show']);
    Route::post('/upload-dispute-page-setting-excel', [DisputePolicyPageSettingController::class, 'uploadExcel']);
    Route::get('/download-dispute-page-setting-template', [DisputePolicyPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-coffee-wall-page-setting', [CoffeeWallPageSettingController::class, 'update']);
    Route::get('/get-coffee-wall-page-setting', [CoffeeWallPageSettingController::class, 'show']);
    Route::post('/upload-coffee-wall-page-setting-excel', [CoffeeWallPageSettingController::class, 'uploadExcel']);
    Route::get('/download-coffee-wall-page-setting-template', [CoffeeWallPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-chats-page-setting', [ChatsPageSettingController::class, 'update']);
    Route::get('/get-chats-page-setting', [ChatsPageSettingController::class, 'show']);
    Route::post('/upload-chats-page-setting-excel', [ChatsPageSettingController::class, 'uploadExcel']);
    Route::get('/download-chats-page-setting-template', [ChatsPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-select-location-page-setting', [SelectLocationPageSettingController::class, 'update']);
    Route::get('/get-select-location-page-setting', [SelectLocationPageSettingController::class, 'show']);
    Route::post('/upload-select-location-page-setting-excel', [SelectLocationPageSettingController::class, 'uploadExcel']);
    Route::get('/download-select-location-page-setting-template', [SelectLocationPageSettingController::class, 'downloadTemplate']);
    Route::post('/update-trips-page-setting', [TripsPageSettingController::class, 'update']);
    Route::get('/get-trips-page-setting', [TripsPageSettingController::class, 'show']);
    Route::post('/upload-trips-page-setting-excel', [TripsPageSettingController::class, 'uploadExcel']);
    Route::get('/download-trips-page-setting-template', [TripsPageSettingController::class, 'downloadTemplate']);
    Route::apiResource('languages', LanguageController::class);
    Route::apiResource('countries', CountryController::class);
    Route::get('/countries/{country}/states', [CountryController::class, 'getStates']);
    Route::apiResource('states', StateController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('packages', PackageController::class);
    Route::apiResource('banks', BankController::class);
    Route::apiResource('extra-care-faqs', ExtraCareFaqController::class);
    Route::apiResource('pink-ride-faqs', PinkRideFaqController::class);
    Route::apiResource('driver-reward-points', DriverRewardPointController::class);
    Route::apiResource('student-reward-points', StudentRewardPointController::class);
    Route::apiResource('bank-settings', BankSettingController::class);
    Route::apiResource('review-settings', ReviewSettingController::class);
    Route::apiResource('pink-ride-settings', PinkRideSettingController::class);
    Route::apiResource('folk-ride-settings', FolkRideSettingController::class);
    Route::apiResource('cancel-ride-settings', CancelRideSettingController::class);
    Route::apiResource('registration-reward-settings', RegistrationRewardSettingController::class);
    Route::apiResource('referral-system-settings', ReferralSystemSettingController::class);
    Route::apiResource('pages', PagesController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('messages', MessageController::class);
    Route::apiResource('closed-account-messages', ClosedAccountMessageController::class);
    Route::put('suspand-user/{id}', [UserController::class, 'suspandUser']);
    Route::put('unsuspand-user/{id}', [UserController::class, 'unSuspandUser']);
    Route::apiResource('passengers', PassengerController::class);
    Route::get('driver-verification', [DriverVerificationController::class, 'index']);
    Route::put('approve-driver/{id}', [DriverVerificationController::class, 'approveDriver']);
    Route::put('reject-driver/{id}', [DriverVerificationController::class, 'rejectDriver']);
    Route::get('student-verification', [StudentVerificationController::class, 'index']);
    Route::put('update-student/{id}/{student}', [StudentVerificationController::class, 'updateStudent']);
    Route::put('update-charge-booking/{id}/{charge_booking}', [StudentVerificationController::class, 'updateChargeBooking']);
    Route::put('update-email-verified/{id}/{email_verified}', [StudentVerificationController::class, 'updateEmailVerified']);
    Route::put('update-phone-verified/{id}/{phone_verified}', [StudentVerificationController::class, 'updatePhoneVerified']);
    Route::put('update-government_id/{id}/{government_id}', [StudentVerificationController::class, 'updateGovernmentId']);
    Route::put('update-pink_ride/{id}', [StudentVerificationController::class, 'updatePinkRideStatus']);
    Route::put('update-folks_ride/{id}', [StudentVerificationController::class, 'updateFolksRideStatus']);
    Route::get('no-shows', [NoShowController::class, 'index']);
    Route::get('no-shows-count/{id}', [NoShowController::class, 'count']);
    Route::get('cancellation-count/{id}', [NoShowController::class, 'cancellationCount']);
    Route::put('update-status/{id}', [NoShowController::class, 'updateStatus']);
    Route::put('undo-update-status/{id}', [NoShowController::class, 'undoUpdateStatus']);
    Route::get('rides', [RideController::class, 'index']);
    Route::get('ride/{id}', [RideController::class, 'show']);
    Route::put('cancel-ride/{id}', [RideController::class, 'cancelRide']);
    Route::put('remove-ride/{id}', [RideController::class, 'removeRide']);
    Route::put('suspand-ride/{id}', [RideController::class, 'suspandRide']);
    Route::put('unsuspand-ride/{id}', [RideController::class, 'unSuspandRide']);
    Route::apiResource('bookings', BookingController::class);
    Route::get('secured-cash-bookings', [SecuredCashBookingController::class, 'index']);
    Route::put('secured-cash-code/{id}', [SecuredCashBookingController::class, 'enterCode']);
    Route::post('verify-password', [SecuredCashBookingController::class, 'verifyPassword']);
    Route::post('verify-password', [WithdrawalRequestController::class, 'verifyPassword']);
    Route::get('ratings', [ReviewController::class, 'index']);
    Route::get('rating/{id}', [ReviewController::class, 'show']);
    Route::post('rating/update-display-check', [ReviewController::class, 'updateDisplyCheckbox']);
    Route::post('ratings/unsuspend/{id}', [ReviewController::class, 'unSuspend']);
    Route::post('ratings/delete/{id}', [ReviewController::class, 'destroy']);
    Route::apiResource('transactions', TransactionController::class);
    Route::get('claim-rewards', [ClaimRewardsController::class, 'index']);
    Route::put('claim-reward-approve/{id}', [ClaimRewardsController::class, 'approveRequest']);
    Route::get('verify-phones', [VerifyPhonesController::class, 'index']);
    Route::put('verify-phone-request/{id}', [VerifyPhonesController::class, 'verifyRequest']);
    Route::get('verify-banks', [VerifyBanksController::class, 'index']);
    Route::put('verify-bank-request/{id}', [VerifyBanksController::class, 'verifyRequest']);
    Route::get('withdrawal-requests', [WithdrawalRequestController::class, 'index']);
    Route::put('accept-withdrawal-request/{id}', [WithdrawalRequestController::class, 'acceptRequest']);
    Route::put('reject-withdrawal-request/{id}', [WithdrawalRequestController::class, 'rejectRequest']);
    Route::get('coffee-wallet', [CoffeeWalletController::class, 'index']);
    Route::get('total-amount', [CoffeeWalletController::class, 'totalAmount']);
    Route::apiResource('booking-credits', BookingCreditController::class);
    Route::apiResource('articles', ArticleController::class);
    Route::apiResource('videos', VideoController::class);
    Route::apiResource('site-settings', SiteSettingController::class);
    Route::group(['prefix' => 'media'], function () {
        Route::post('/process', [MediaController::class, 'process']);
        Route::post('/revert', [MediaController::class, 'revert']);
        Route::post('image_again_upload', [MediaController::class, 'uploadImage']);
    });
    Route::post('/update-profile-page-setting', [ProfilePageSettingController::class, 'update']);
    Route::get('/get-profile-page-setting', [ProfilePageSettingController::class, 'show']);
    Route::post('/upload-profile-page-setting-excel', [ProfilePageSettingController::class, 'uploadExcel']);
    Route::get('/download-profile-page-setting-template', [ProfilePageSettingController::class, 'downloadTemplate']);
    Route::post('/update-profile-photo-setting', [ProfilePhotoSettingController::class, 'update']);
    Route::get('/get-profile-photo-setting', [ProfilePhotoSettingController::class, 'show']);
    Route::post('/upload-profile-photo-setting-excel', [ProfilePhotoSettingController::class, 'uploadExcel']);
    Route::get('/download-profile-photo-setting-template', [ProfilePhotoSettingController::class, 'downloadTemplate']);
    Route::post('/update-profile-setting', [ProfileSettingController::class, 'update']);
    Route::get('/get-profile-setting', [ProfileSettingController::class, 'show']);
    Route::post('/upload-profile-setting-excel', [ProfileSettingController::class, 'uploadExcel']);
    Route::get('/download-profile-setting-template', [ProfileSettingController::class, 'downloadTemplate']);
    Route::post('/update-close-account-setting', [CloseAccountSettingController::class, 'update']);
    Route::get('/get-close-account-setting', [CloseAccountSettingController::class, 'show']);
    Route::post('/upload-close-account-setting-excel', [CloseAccountSettingController::class, 'uploadExcel']);
    Route::get('/download-close-account-setting-template', [CloseAccountSettingController::class, 'downloadTemplate']);
    Route::post('/update-contact-proxima-setting', [ContactUsSettingController::class, 'update']);
    Route::get('/get-contact-proxima-setting', [ContactUsSettingController::class, 'show']);
    Route::post('/upload-contact-proxima-setting-excel', [ContactUsSettingController::class, 'uploadExcel']);
    Route::get('/download-contact-proxima-setting-template', [ContactUsSettingController::class, 'downloadTemplate']);
    Route::post('/update-logout-setting', [LogoutSettingController::class, 'update']);
    Route::get('/get-logout-setting', [LogoutSettingController::class, 'show']);
    Route::post('/upload-logout-setting-excel', [LogoutSettingController::class, 'uploadExcel']);
    Route::get('/download-logout-setting-template', [LogoutSettingController::class, 'downloadTemplate']);
    Route::post('/update-my-driver-setting', [MyDriverSettingController::class, 'update']);
    Route::get('/get-my-driver-setting', [MyDriverSettingController::class, 'show']);
    Route::post('/upload-my-driver-setting-excel', [MyDriverSettingController::class, 'uploadExcel']);
    Route::get('/download-my-driver-setting-template', [MyDriverSettingController::class, 'downloadTemplate']);
    Route::post('/update-my-email-setting', [MyEmailSettingController::class, 'update']);
    Route::get('/get-my-email-setting', [MyEmailSettingController::class, 'show']);
    Route::post('/upload-my-email-setting-excel', [MyEmailSettingController::class, 'uploadExcel']);
    Route::get('/download-my-email-setting-template', [MyEmailSettingController::class, 'downloadTemplate']);
    Route::post('/update-my-phone-setting', [MyPhoneSettingController::class, 'update']);
    Route::get('/get-my-phone-setting', [MyPhoneSettingController::class, 'show']);
    Route::post('/upload-my-phone-setting-excel', [MyPhoneSettingController::class, 'uploadExcel']);
    Route::get('/download-my-phone-setting-template', [MyPhoneSettingController::class, 'downloadTemplate']);
    Route::post('/update-my-vehicle-setting', [MyVehicleSettingController::class, 'update']);
    Route::get('/get-my-vehicle-setting', [MyVehicleSettingController::class, 'show']);
    Route::post('/upload-my-vehicle-setting-excel', [MyVehicleSettingController::class, 'uploadExcel']);
    Route::get('/download-my-vehicle-setting-template', [MyVehicleSettingController::class, 'downloadTemplate']);
    Route::post('/update-my-wallet-setting', [MyWalletSettingController::class, 'update']);
    Route::get('/get-my-wallet-setting', [MyWalletSettingController::class, 'show']);
    Route::post('/upload-my-wallet-setting-excel', [MyWalletSettingController::class, 'uploadExcel']);
    Route::get('/download-my-wallet-setting-template', [MyWalletSettingController::class, 'downloadTemplate']);
    Route::post('/update-password-setting', [PasswordSettingController::class, 'update']);
    Route::get('/get-password-setting', [PasswordSettingController::class, 'show']);
    Route::post('/upload-password-setting-excel', [PasswordSettingController::class, 'uploadExcel']);
    Route::get('/download-password-setting-template', [PasswordSettingController::class, 'downloadTemplate']);
    Route::post('/update-payment-setting', [PaymentOptionSettingController::class, 'update']);
    Route::get('/get-payment-setting', [PaymentOptionSettingController::class, 'show']);
    Route::post('/upload-payment-option-setting-excel', [PaymentOptionSettingController::class, 'uploadExcel']);
    Route::get('/download-payment-option-setting-template', [PaymentOptionSettingController::class, 'downloadTemplate']);
    Route::post('/update-payout-setting', [PayoutOptionSettingController::class, 'update']);
    Route::get('/get-payout-setting', [PayoutOptionSettingController::class, 'show']);
    Route::post('/upload-payout-option-setting-excel', [PayoutOptionSettingController::class, 'uploadExcel']);
    Route::get('/download-payout-option-setting-template', [PayoutOptionSettingController::class, 'downloadTemplate']);
    Route::post('/update-edit-profile-setting', [EditProfilePageSettingController::class, 'update']);
    Route::get('/get-edit-profile-setting', [EditProfilePageSettingController::class, 'show']);
    Route::post('/upload-edit-profile-page-setting-excel', [EditProfilePageSettingController::class, 'uploadExcel']);
    Route::get('/download-edit-profile-page-setting-template', [EditProfilePageSettingController::class, 'downloadTemplate']);
    Route::post('/update-my-student-setting', [MyStudentCardSettingController::class, 'update']);
    Route::get('/get-my-student-setting', [MyStudentCardSettingController::class, 'show']);
    Route::post('/upload-my-studentcard-setting-excel', [MyStudentCardSettingController::class, 'uploadExcel']);
    Route::get('/download-my-studentcard-setting-template', [MyStudentCardSettingController::class, 'downloadTemplate']);
    Route::post('/update-my-passenger-setting', [MyPassengerSettingController::class, 'update']);
    Route::get('/get-my-passenger-setting', [MyPassengerSettingController::class, 'show']);
    Route::post('/upload-my-passenger-setting-excel', [MyPassengerSettingController::class, 'uploadExcel']);
    Route::get('/download-my-passenger-setting-template', [MyPassengerSettingController::class, 'downloadTemplate']);
    Route::group(['prefix' => 'errors'], function () {
        Route::post('/', [ErrorController::class, 'update']);
        Route::get('/', [ErrorController::class, 'index']);
    });
    Route::post('/update-my-review-setting', [MyReviewSettingController::class, 'update']);
    Route::get('/get-my-review-setting', [MyReviewSettingController::class, 'show']);
    Route::post('/upload-review-setting-excel', [MyReviewSettingController::class, 'uploadExcel']);
    Route::get('/download-review-setting-template', [MyReviewSettingController::class, 'downloadTemplate']);
    Route::post('/update-billing-address-setting', [BillingAdressSettingController::class, 'update']);
    Route::get('/get-billing-address-setting', [BillingAdressSettingController::class, 'show']);
    Route::post('/upload-billing-address-setting-excel', [BillingAdressSettingController::class, 'uploadExcel']);
    Route::get('/download-billing-address-setting-template', [BillingAdressSettingController::class, 'downloadTemplate']);
});
