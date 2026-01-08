import 'package:get/get.dart';
import 'package:proximaride_app/pages/add_card/AddCardController.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatController.dart';
import 'package:proximaride_app/pages/close_my_account/CloseAccountController.dart';
import 'package:proximaride_app/pages/co_passenger/CoPassengerController.dart';
import 'package:proximaride_app/pages/contact_us/ContactUsController.dart';
import 'package:proximaride_app/pages/deep_trip_detail/DeepTripDetailController.dart';
import 'package:proximaride_app/pages/driver_license/DriverLicenseController.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileController.dart';
import 'package:proximaride_app/pages/email_address/EmailAddressController.dart';
import 'package:proximaride_app/pages/forget_password/ForgetPasswordController.dart';
import 'package:proximaride_app/pages/location/LocationController.dart';
import 'package:proximaride_app/pages/login/LoginController.dart';
import 'package:proximaride_app/pages/messaging_page/MessagingController.dart';
import 'package:proximaride_app/pages/my_passenger/MyPassengerController.dart';
import 'package:proximaride_app/pages/my_phone_number/MyPhoneNumberController.dart';
import 'package:proximaride_app/pages/my_reviews/MyReviewsController.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/pages/my_vehicle/MyVehicleController.dart';
import 'package:proximaride_app/pages/my_wallet/MyWalletController.dart';
import 'package:proximaride_app/pages/navigation/NavigationController.dart';
import 'package:proximaride_app/pages/notification_add_review/NotificationAddReviewController.dart';
import 'package:proximaride_app/pages/notifications/NotificationController.dart';
import 'package:proximaride_app/pages/old_messages/OldMessagesController.dart';
import 'package:proximaride_app/pages/password/PasswordController.dart';
import 'package:proximaride_app/pages/payment_options/PaymentOptionsController.dart';
import 'package:proximaride_app/pages/post_ride/PostRideController.dart';
import 'package:proximaride_app/pages/post_ride_again/PostRideAgainController.dart';
import 'package:proximaride_app/pages/profile_detail/ProfileDetailController.dart';
import 'package:proximaride_app/pages/profile_photo/ProfilePhotoController.dart';
import 'package:proximaride_app/pages/profile_setting/ProfileSettingController.dart';
import 'package:proximaride_app/pages/review/ReviewController.dart';
import 'package:proximaride_app/pages/review_detail/ReviewDetailController.dart';
import 'package:proximaride_app/pages/search_ride/SearchRideController.dart';
import 'package:proximaride_app/pages/signup/RegisterController.dart';
import 'package:proximaride_app/pages/splash/SplashController.dart';
import 'package:proximaride_app/pages/stages/StageController.dart';
import 'package:proximaride_app/pages/stages/StageFourController.dart';
import 'package:proximaride_app/pages/stages/StageThreeController.dart';
import 'package:proximaride_app/pages/stages/StageTowController.dart';
import 'package:proximaride_app/pages/student_card/StudentCardController.dart';
import 'package:proximaride_app/pages/thank_you/ThankYouController.dart';
import 'package:proximaride_app/pages/trip_detail/TripDetailController.dart';

import 'pages/chat/ChatController.dart';


class BindingController implements Bindings{
  @override
  void dependencies(){
    Get.lazyPut<SplashController>(() => SplashController());
    Get.lazyPut<RegisterController>(() => RegisterController());
    Get.lazyPut<LoginController>(() => LoginController());
    Get.lazyPut<NavigationController>(() => NavigationController());
    Get.lazyPut<ProfileDetailController>(() => ProfileDetailController());
    Get.lazyPut<ReviewController>(() => ReviewController());
    Get.lazyPut<EditProfileController>(() => EditProfileController());
    Get.lazyPut<ProfilePhotoController>(() => ProfilePhotoController());
    Get.lazyPut<MyVehicleController>(() => MyVehicleController());
    Get.lazyPut<PasswordController>(() => PasswordController());
    Get.lazyPut<MyPhoneNumberController>(() => MyPhoneNumberController());
    Get.lazyPut<EmailAddressController>(() => EmailAddressController());
    Get.lazyPut<DriverLicenseController>(() => DriverLicenseController());
    Get.lazyPut<StudentCardController>(() => StudentCardController());
    Get.lazyPut<MyTripController>(() => MyTripController());
    Get.lazyPut<PostRideController>(() => PostRideController());
    Get.lazyPut<MyWalletController>(() => MyWalletController());
    Get.lazyPut<PaymentOptionController>(() => PaymentOptionController());
    Get.lazyPut<MyReviewsController>(() => MyReviewsController());
    Get.lazyPut<CloseAccountController>(() => CloseAccountController());
    Get.lazyPut<SearchRideController>(() => SearchRideController());
    Get.lazyPut<PostRideAgainController>(() => PostRideAgainController());
    Get.lazyPut<TripDetailController>(() => TripDetailController());
    Get.lazyPut<CoPassengerController>(() => CoPassengerController());
    Get.lazyPut<ChatController>(() => ChatController());
    Get.lazyPut<MessagingController>(() => MessagingController());
    Get.lazyPut<OldMessagesController>(() => OldMessagesController());
    Get.lazyPut<BookSeatController>(() => BookSeatController());
    Get.lazyPut<NotificationController>(() => NotificationController());
    Get.lazyPut<ReviewDetailController>(() => ReviewDetailController());
    Get.lazyPut<MyPassengerController>(() => MyPassengerController());
    Get.lazyPut<ContactUsController>(() => ContactUsController());
    Get.lazyPut<ForgetPasswordController>(() => ForgetPasswordController());
    Get.lazyPut<AddCardController>(() => AddCardController());
    Get.lazyPut<StageController>(() => StageController());
    Get.lazyPut<StageTowController>(() => StageTowController());
    Get.lazyPut<StageThreeController>(() => StageThreeController());
    Get.lazyPut<StageFourController>(() => StageFourController());
    Get.lazyPut<LocationController>(() => LocationController());
    Get.lazyPut<NotificationAddReviewController>(() => NotificationAddReviewController());
    Get.lazyPut<ThankYouController>(() => ThankYouController());
    Get.lazyPut<ProfileSettingController>(() => ProfileSettingController());
    Get.lazyPut<DeepTripDetailController>(() => DeepTripDetailController());

  }
}