import 'dart:async';

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:flutter_stripe/flutter_stripe.dart';
import 'package:proximaride_app/MainProvider.dart';
import 'package:proximaride_app/firebase_options.dart';
import 'package:proximaride_app/pages/DeepLinkController.dart';
import 'package:proximaride_app/pages/booking_check_page.dart';
import 'package:proximaride_app/pages/cancellation_policy/cancellation_policy.dart';
import 'package:proximaride_app/pages/coffee_on_wall/coffee_on_wall.dart';
import 'package:proximaride_app/pages/contact_us/contact_us.dart';
import 'package:proximaride_app/pages/deep_trip_detail/deep_trip_detail.dart';
import 'package:proximaride_app/pages/dispute_policy/dispute_policy.dart';
import 'package:proximaride_app/pages/email_address/email_address.dart';
import 'package:proximaride_app/pages/forget_password/forget_password.dart';
import 'package:proximaride_app/pages/image_show.dart';
import 'package:proximaride_app/pages/location/city.dart';
import 'package:proximaride_app/pages/my_passenger/my_passenger.dart';
import 'package:proximaride_app/pages/my_profile/my_profile.dart';
import 'package:proximaride_app/pages/my_trips/add_review.dart';
import 'package:proximaride_app/pages/book_seat/book_add_card.dart';
import 'package:proximaride_app/pages/book_seat/book_cards.dart';
import 'package:proximaride_app/pages/book_seat/book_seat.dart';
import 'package:proximaride_app/pages/close_my_account/close_my_account.dart';
import 'package:proximaride_app/pages/co_passenger/co_passenger.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/driver_license/driver_license.dart';
import 'package:proximaride_app/pages/location/country.dart';
import 'package:proximaride_app/pages/edit_profile/edit_profile.dart';
import 'package:proximaride_app/pages/location/state.dart';
import 'package:proximaride_app/pages/email_address/update_email_address.dart';
import 'package:proximaride_app/pages/login/login.dart';
import 'package:proximaride_app/pages/messaging_page/MessagingController.dart';
import 'package:proximaride_app/pages/messaging_page/messaging_page.dart';
import 'package:proximaride_app/pages/my_phone_number/my_phone_number.dart';
import 'package:proximaride_app/pages/my_phone_number/phone_number_verification.dart';
import 'package:proximaride_app/pages/my_reviews/my_reviews.dart';
import 'package:proximaride_app/pages/my_trips/cancel_booking.dart';
import 'package:proximaride_app/pages/my_trips/remove_passenger.dart';
import 'package:proximaride_app/pages/my_trips/review_passenger.dart';
import 'package:proximaride_app/pages/my_vehicle/add_vehicle.dart';
import 'package:proximaride_app/pages/my_vehicle/my_vehicle.dart';
import 'package:proximaride_app/pages/my_wallet/balance_book_cards.dart';
import 'package:proximaride_app/pages/my_wallet/my_wallet.dart';
import 'package:proximaride_app/pages/my_wallet/ride_fair_details.dart';
import 'package:proximaride_app/pages/my_wallet/top_up_my_balance.dart';
import 'package:proximaride_app/pages/navigation/navigation.dart';
import 'package:proximaride_app/pages/notification_add_review/notification_add_review.dart';
import 'package:proximaride_app/pages/notifications/notification.dart';
import 'package:proximaride_app/pages/old_messages/old_messages.dart';
import 'package:proximaride_app/pages/password/password.dart';
import 'package:proximaride_app/pages/add_card/add_card.dart';
import 'package:proximaride_app/pages/payment_options/payment_options.dart';
import 'package:proximaride_app/pages/payout_account/payout_account.dart';
import 'package:proximaride_app/pages/post_ride/post_ride.dart';
import 'package:proximaride_app/pages/post_ride_again/post_ride_again.dart';
import 'package:proximaride_app/pages/privacy_policy/privacy_policy.dart';
import 'package:proximaride_app/pages/profile_detail/profile_detail.dart';
import 'package:proximaride_app/pages/profile_photo/profile_photo.dart';
import 'package:proximaride_app/pages/profile_photo_guidelines/profile_photo_guidelines.dart';
import 'package:proximaride_app/pages/profile_setting/profile_setting.dart';
import 'package:proximaride_app/pages/referals/referals.dart';
import 'package:proximaride_app/pages/refund_policy/refund_policy.dart';
import 'package:proximaride_app/pages/review/review.dart';
import 'package:proximaride_app/pages/review_detail/review_detail.dart';
import 'package:proximaride_app/pages/search_ride/search_ride.dart';
import 'package:proximaride_app/pages/search_ride/search_ride_result.dart';
import 'package:proximaride_app/pages/signup/signup.dart';
import 'package:proximaride_app/pages/splash/show_ride.dart';
import 'package:proximaride_app/pages/splash/splash.dart';
import 'package:proximaride_app/pages/stages/stage_four_vehicle.dart';
import 'package:proximaride_app/pages/stages/stage_four.dart';
import 'package:proximaride_app/pages/stages/stage_one.dart';
import 'package:proximaride_app/pages/stages/stage_three_vehicle.dart';
import 'package:proximaride_app/pages/stages/stage_two.dart';
import 'package:proximaride_app/pages/student_card/student_card.dart';
import 'package:proximaride_app/pages/term_condition/term_condition.dart';
import 'package:proximaride_app/pages/terms_of_use/terms_of_use.dart';
import 'package:proximaride_app/pages/thank_you/thank_you.dart';
import 'package:proximaride_app/pages/trip_detail/trip_detail.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/notification_service.dart';
import 'package:proximaride_app/services/service.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
//import 'package:tiktok_login_flutter/tiktok_login_flutter.dart';
import 'consts/constFileLink.dart';

Future<void> _initializeStripe({String? publishableKey}) async {
  if (kIsWeb) {
    logger.info('Skipping Stripe initialization on web.');
    return;
  }

  if (publishableKey != null && publishableKey.isNotEmpty) {
    Stripe.publishableKey = publishableKey;
  }
  Stripe.merchantIdentifier = 'merchant.flutter.stripe.test';
  Stripe.urlScheme = 'flutterstripe';
  await Stripe.instance.applySettings();
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  logger.intialize();

  await _initializeStripe(
    publishableKey:
        'pk_test_51PQ40hHySwupjfTMAKFhcggJHnPhCgsnASCOyIFfNixqiReRCXa4v1w3Zds3OuOzADlGg2Uk0xbLbLU9CvSyrBSH000NbZbLzR',
  );

  await Firebase.initializeApp(options: DefaultFirebaseOptions.currentPlatform);
  FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);

  await instalData();

  // WidgetsBinding.instance.addObserver(
  //     LifecycleEventHandler(resumeCallBack: () async => setState(() {
  //     }))
  // );
  Get.put(DeepLinkController());
  runApp(const MyApp());
}

Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();

  logger.info("[BG] Notification received:");
  logger.info("Notification: ");
  // logger.info(message.notification?.toMap().toString() ?? 'null');
  logger.info("Data: ");
  logger.info(message.data.toString());

  if (message.notification != null) {
    logger.info("Hello1");
    logger.info("Broadcast notification received in BACKGROUND: ");
    logger.info("Title: ${message.notification!.title ?? ""}");
    logger.info("Body: ${message.notification!.body ?? ""}");
    logger.info("Data: ${message.data}");
    NotificationService().showNotification(
      title: message.notification!.title,
      body: message.notification!.body,
    );
  }
}

instalData() async {
  await dotenv.load(fileName: "assets/.env");
  await initializeApiConfig();
  logger.info("API environment: $apiEnvironment");
  logger.info("Resolved app URL: $url");
  logger.info("Resolved API URL: $baseUrl");
  await initService();

  NotificationService().initNotification();

  FirebaseMessaging.instance.onTokenRefresh.listen((fcmToken) {
    if (kDebugMode) {
      logger.info(fcmToken);
    }
  }).onError((err) {});

  FirebaseMessaging.onMessage;

  FirebaseMessaging.onMessage.listen((RemoteMessage message) {
    logger.info("[FG] Notification received:");
    logger.info("Notification: ");
    // logger.info(message.notification?.toMap().toString() ?? 'null');
    logger.info("Data: ");
    logger.info(message.data.toString());
    if (message.notification != null) {
      logger.info("Hello222${message.data}");
      logger.info("Broadcast notification received: ");
      logger.info("Title: ${message.notification!.title ?? ""}");
      logger.info("Body: ${message.notification!.body ?? ""}");
      logger.info("Data: ${message.data}");
      if (message.data['type'] == 'chat') {
        // logger.info('New message received');

        // if (Get.isRegistered<MessagingController>()) {
        //   var msgController = Get.find<MessagingController>();
        //   msgController.messagesList.clear();
        //   msgController.getMessages();
        // }
      }
      NotificationService().showNotification(
        title: message.notification!.title,
        body: message.notification!.body,
      );
    }
  });

  await _initializeStripe(
    publishableKey: dotenv.env['STRIPE_KEY'],
  );

  //await TiktokLoginFlutter.initializeTiktokLogin("sbawj9a1vuvtt3arxd");

  // SystemChrome.setPreferredOrientations([
  //   DeviceOrientation.portraitUp,
  //   DeviceOrientation.portraitDown
  // ]);
}

Future<void> initService() async {
  logger.info("initService called");
  await Get.putAsync<Service>(() async => Service());
}

class MyApp extends StatefulWidget {
  const MyApp({super.key});

  @override
  State<MyApp> createState() => MyAppState();
}

class MyAppState extends State<MyApp> {
  final serviceController = Get.find<Service>();
  LifecycleEventHandler? lifecycleEventHandler;
  List<String> registeredRoutes = [
    '/',
    '/show-ride',
    '/login',
    '/signup',
    '/navigation',
    '/my_profile',
    '/profile_detail/:type/:id/:pageType',
    '/review/:type/:id',
    '/edit_profile',
    '/country/:country',
    '/state/:state/:countryId',
    '/city/:city/:stateId/:index/:spot',
    '/profile_setting',
    '/profile_photo',
    '/my_vehicle',
    '/add_vehicle',
    '/password',
    '/my_phone_number',
    '/phone_number_verification',
    '/email_address',
    '/update_email_address',
    '/driver_license',
    '/student_card',
    '/my_wallet',
    '/ride_fair_detail/:rideId/:type',
    '/top_up_balance',
    '/payment_options',
    '/my_reviews',
    '/review_detail/:id/:type/:reviewType',
    '/close_my_account',
    '/post_ride/:id/:type',
    '/add_card/:type',
    '/search_ride',
    '/post_ride_again',
    '/search_ride_result',
    '/trip_detail/:tripId/:type/:status/:rideDetailId',
    '/co_passenger/:tripId',
    '/book_seat/:tripId/:bookedSeat/:rideDetailId',
    '/notifications',
    '/book_cards',
    '/book_add_cards',
    '/add_review',
    '/messaging_page/:userId/:rideId/:type',
    '/cancel_booking/:pageType',
    '/my_passenger/:rideId',
    '/remove_passenger/:rideId',
    '/review_passenger/:rideId',
    '/term_condition',
    '/term_of_use',
    '/contact_us',
    '/thank_you/:type',
    '/forgot_password',
    '/privacy_policy',
    '/refund_policy',
    '/cancellation_policy',
    '/dispute_policy',
    '/coffee_on_wall',
    '/referral',
    '/old_messages',
    '/stage_one',
    '/stage_two',
    '/stage_three_vehicle',
    '/stage_four_vehicle',
    '/stage_four',
    '/balance_book_cards',
    '/payout_account',
    '/show_image',
    '/booking_check',
    '/notification_add_review/:reviewType/:rideId/:bookingId/:notificationId/:rideDetailId',
    '/deep_trip_detail',
  ];

  @override
  void initState() {
    super.initState();
    handleInitialNotification();

    logger.info("initState called");
    logger.info(
        "Initial platform route: ${WidgetsBinding.instance.platformDispatcher.defaultRouteName}");

    // Initialize the lifecycle flutter  handler and add it as an observer
    lifecycleEventHandler = LifecycleEventHandler(
      resumeCallBack: () async => setState(() {
        if (kDebugMode) {
          logger.info('resume call back obs');
        }
      }),
      closeCallBack: () async {
        if (kDebugMode) {
          logger.info('close call back obs');
        }
        // Place your API call here to handle app termination
        // await serviceController.someAPIOnAppTermination();
      },
    );

    WidgetsBinding.instance.addObserver(
      lifecycleEventHandler!,
    ); // Modify this line

    // WidgetsBinding.instance.addObserver(
    //     LifecycleEventHandler(resumeCallBack: () async => setState(() {
    //       logger.info('resume call back obs');
    //     }),
    //         closeCallBack: () async => setState(() {
    //           logger.info('close call back obs');
    //         }))
    // );
  }

  @override
  void dispose() {
    if (lifecycleEventHandler != null) {
      WidgetsBinding.instance.removeObserver(lifecycleEventHandler!);
    }
    super.dispose();
  }

  void handleInitialNotification() async {
    RemoteMessage? initialMessage =
        await FirebaseMessaging.instance.getInitialMessage();
    logger.info("[TERM] Notification received (getInitialMessage):");
    if (initialMessage != null) {
      logger.info("Notification: ");
      logger.info((initialMessage.notification ?? 'null').toString());
      logger.info("Data: ");
      logger.info(initialMessage.data.toString());
      // App launched from terminated state due to a notification
      _handleNotificationNavigation(initialMessage);
    } else {
      // Handle background and foreground notification taps
      FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
        logger.info("[BG TAP] Notification received (onMessageOpenedApp):");
        logger.info("Notification: ");
        // logger.info(message.notification?.toMap().toString() ?? 'null');
        logger.info("Data: ");
        logger.info(message.data.toString());
        _handleNotificationNavigation(message);
      });
    }
  }

  void _handleNotificationNavigation(RemoteMessage message) {
    final data = message.data;
    final notificationType = data['notification_type'];
    final type = data['type'];
    final rideId = data['ride_id'];
    final postedBy = data['posted_by'];
    final postedTo = data['posted_to'];
    final id = data['id'];
    final rideDetailId = data['ride_detail_id'];

    logger.info('Notification tap data:');
    logger.info(data.toString());
    logger.info('notificationType: ${notificationType ?? 'null'}');
    logger.info('type: ${type ?? 'null'}');
    logger.info('rideId: ${rideId ?? 'null'}');
    logger.info('postedBy: ${postedBy ?? 'null'}');
    logger.info('postedTo: ${postedTo ?? 'null'}');
    logger.info('id: ${id ?? 'null'}');
    logger.info('rideDetailId: ${rideDetailId ?? 'null'}');

    if (notificationType == 'review') {
      if (type == '1') {
        var route =
            '/notification_add_review/passenger/$rideId/$postedTo/$id/$rideDetailId';
        logger.info('Navigating to: $route');
        Get.toNamed(route);
      } else {
        var route =
            '/notification_add_review/driver/$rideId/0/$id/$rideDetailId';
        logger.info('Navigating to: $route');
        Get.toNamed(route);
      }
    } else if (notificationType == 'chat received') {
      logger.info(" chat received data from notification: ${data.toString()}");
      // Normalize user (other participant) and ride id from payload with robust fallbacks
      final candidateUserId =
          postedBy ?? data['sender'] ?? data['sender_id'] ?? data['user_id'];
      final candidateRideId = rideId ?? data['rideId'] ?? data['ride'] ?? '0';

      final chatUserId = (candidateUserId != null &&
              candidateUserId.toString().isNotEmpty &&
              candidateUserId.toString() != 'null')
          ? candidateUserId.toString()
          : '';
      final chatRideId = (candidateRideId != null &&
              candidateRideId.toString().isNotEmpty &&
              candidateRideId.toString() != 'null')
          ? candidateRideId.toString()
          : '0';

      if (chatUserId.isEmpty) {
        // If we don't know who to chat with, fall back to chat list
        logger.info(
          'Missing chat user id in notification; navigating to /navigation (chat tab)',
        );
        Get.toNamed('/navigation');
        return;
      }

      final route = '/messaging_page/$chatUserId/$chatRideId/new';
      logger.info('Navigating to: $route');
      _navigateSafely(route);
    } else if (notificationType == 'phone') {
      logger.info('Navigating to: /my_phone_number');
      Get.toNamed('/my_phone_number');
    } else if (notificationType != null && rideId != null) {
      var tripType = type == '1' ? 'ride' : 'trip';
      var route =
          '/trip_detail/$rideId/$tripType/$notificationType/$rideDetailId';
      logger.info('Navigating to: $route');
      Get.toNamed(route);
    } else {
      // Fallback: go to profile or welcome screen
      logger.info('Navigating to: /profile_setting');
      Get.toNamed('/profile_setting');
    }
  }

  // Defer navigation until after a frame to avoid issues when resuming from background
  void _navigateSafely(String route) {
    try {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        Future.microtask(() {
          // If already on route, skip
          if (Get.currentRoute == route) return;
          Get.toNamed(route);
        });
      });
    } catch (e) {
      // Fallback with slight delay
      Future.delayed(const Duration(milliseconds: 120), () {
        if (Get.currentRoute == route) return;
        Get.toNamed(route);
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    logger.info("base url: $baseUrl");
    return GetMaterialApp(
      debugShowCheckedModeBanner: false,
      title: appName,
      theme: ThemeData(
        scaffoldBackgroundColor: Colors.white,
        appBarTheme: const AppBarTheme(backgroundColor: Colors.white),
        fontFamily: regular,
        inputDecorationTheme: InputDecorationTheme(
          hintStyle: appPlaceholderTextStyle(),
        ),
      ),
      initialRoute: '/',
      defaultTransition: Transition.leftToRightWithFade,
      routingCallback: (routing) {
        final currentRoute = routing?.current ?? '';
        final previousRoute = routing?.previous ?? '';
        logger.info(
            'routingCallback -> current: $currentRoute, previous: $previousRoute');

        if (!registeredRoutes.contains(currentRoute) &&
            (currentRoute.contains('/email-verified') ||
                currentRoute.contains('?app=true'))) {
          logger.warning(
              '🚫 BLOCKING unregistered deep link route: $currentRoute');
          Future.microtask(() {
            Get.offAllNamed(routing?.previous ?? '/login');
          });
          return;
        }
      },
      getPages: [
        GetPage(name: '/', page: () => const SplashPage()),
        GetPage(name: '/show-ride', page: () => const ShowRidePage()),
        GetPage(name: '/login', page: () => const LoginPage()),
        GetPage(name: '/signup', page: () => const SignupPage()),
        GetPage(name: '/navigation', page: () => const NavigationPage()),
        GetPage(name: '/my_profile', page: () => const MyProfilePage()),
        GetPage(
          name: '/profile_detail/:type/:id/:pageType',
          page: () => const ProfileDetailPage(),
        ),
        GetPage(name: '/review/:type/:id', page: () => const ReviewPage()),
        GetPage(name: '/edit_profile', page: () => const EditProfilePage()),

        GetPage(name: '/country/:country', page: () => const CountryPage()),
        GetPage(
          name: '/state/:state/:countryId',
          page: () => const StatePage(),
        ),
        GetPage(
          name: '/city/:city/:stateId/:index/:spot',
          page: () => const CityPage(),
        ),

        GetPage(
          name: '/profile_setting',
          page: () => const ProfileSettingPage(),
        ),
        GetPage(name: '/profile_photo', page: () => const ProfilePhotoPage()),
        GetPage(name: '/my_vehicle', page: () => const MyVehiclePage()),
        GetPage(name: '/add_vehicle', page: () => const AddVehiclePage()),
        GetPage(name: '/password', page: () => const PasswordPage()),
        GetPage(
          name: '/my_phone_number',
          page: () => const MyPhoneNumberPage(),
        ),
        GetPage(
          name: '/phone_number_verification',
          page: () => const PhoneNumberVerificationPage(),
        ),
        GetPage(name: '/email_address', page: () => const EmailAddressPage()),
        GetPage(
          name: '/update_email_address',
          page: () => const UpdateEmailAddressPage(),
        ),
        GetPage(name: '/driver_license', page: () => const DriverLicensePage()),
        GetPage(name: '/student_card', page: () => const StudentCardPage()),
        GetPage(name: '/my_wallet', page: () => const MyWallet()),
        GetPage(
          name: '/ride_fair_detail/:rideId/:type',
          page: () => const RideFairDetail(),
        ),
        GetPage(name: '/top_up_balance', page: () => const TopUpMyBalance()),
        GetPage(name: '/payment_options', page: () => const PaymentOptions()),
        GetPage(name: '/my_reviews', page: () => const MyReviews()),
        GetPage(
          name: '/review_detail/:id/:type/:reviewType',
          page: () => const ReviewDetail(),
        ),
        GetPage(name: '/close_my_account', page: () => const CloseMyAccount()),
        GetPage(name: '/post_ride/:id/:type', page: () => const PostRidePage()),
        GetPage(name: '/add_card/:type', page: () => const AddCard()),
        GetPage(name: '/search_ride', page: () => const SearchRidePage()),
        GetPage(
          name: '/post_ride_again',
          page: () => const PostRideAgainPage(),
        ),
        GetPage(
          name: '/search_ride_result',
          page: () => const SearchRideResultPage(),
        ),
        GetPage(
          name: '/trip_detail/:tripId/:type/:status/:rideDetailId',
          page: () => const TripDetailPage(),
        ),
        GetPage(
          name: '/co_passenger/:tripId',
          page: () => const CoPassengerPage(),
        ),
        GetPage(
          name: '/book_seat/:tripId/:bookedSeat/:rideDetailId',
          page: () => const BookSeatPage(),
        ),
        GetPage(name: '/notifications', page: () => const NotificationPage()),
        GetPage(name: '/book_cards', page: () => const BookCardsPage()),
        GetPage(name: '/book_add_cards', page: () => const BookAddCardPage()),
        // GetPage(name: '/messaging_page/:Id', page: () => const MessagingPage()),
        GetPage(name: '/add_review', page: () => const AddReviewPage()),
        GetPage(
          name: '/messaging_page/:userId/:rideId/:type',
          page: () => const MessagingPage(),
          binding: BindingsBuilder(() {
            if (!Get.isRegistered<MessagingController>()) {
              Get.lazyPut<MessagingController>(() => MessagingController());
            }
          }),
        ),
        GetPage(
          name: '/cancel_booking/:pageType',
          page: () => const CancelBookingPage(),
        ),
        GetPage(
          name: '/my_passenger/:rideId',
          page: () => const MyPassengerPage(),
        ),
        GetPage(
          name: '/remove_passenger/:rideId',
          page: () => const RemovePassengerPage(),
        ),
        GetPage(
          name: '/review_passenger/:rideId',
          page: () => const ReviewPassengerPage(),
        ),
        GetPage(name: '/term_condition', page: () => const TermConditionPage()),
        GetPage(name: '/term_of_use', page: () => const TermsOfUsePage()),
        GetPage(name: '/contact_us', page: () => const ContactUsPage()),
        GetPage(name: '/thank_you/:type', page: () => const ThankYouPage()),
        GetPage(
          name: '/forgot_password',
          page: () => const ForgetPasswordPage(),
        ),
        GetPage(name: '/privacy_policy', page: () => const PrivacyPolicyPage()),
        GetPage(
            name: '/profile_photo_guidelines',
            page: () => const ProfilePhotoGuidelines()),
        GetPage(name: '/refund_policy', page: () => const RefundPolicy()),
        GetPage(
          name: '/cancellation_policy',
          page: () => const CancellationPolicy(),
        ),
        GetPage(name: '/dispute_policy', page: () => const DisputePolicy()),
        GetPage(name: '/coffee_on_wall', page: () => const CoffeeOnWall()),

        GetPage(name: '/referral', page: () => const Referral()),
        GetPage(name: '/old_messages', page: () => const OldMessages()),
        GetPage(name: '/stage_one', page: () => const StageOne()),
        GetPage(name: '/stage_two', page: () => const StageTwo()),
        GetPage(
          name: '/stage_three_vehicle',
          page: () => const StageThreeVehicle(),
        ),
        GetPage(
          name: '/stage_four_vehicle',
          page: () => const StageFourVehicle(),
        ),
        GetPage(name: '/stage_four', page: () => const StageFour()),
        GetPage(
          name: '/balance_book_cards',
          page: () => const BalanceBookCardsPage(),
        ),
        GetPage(name: '/payout_account', page: () => const PayoutAccountPage()),
        GetPage(name: '/show_image', page: () => const ImageShow()),
        GetPage(name: '/booking_check', page: () => const BookingCheckPage()),
        GetPage(
          name:
              '/notification_add_review/:reviewType/:rideId/:bookingId/:notificationId/:rideDetailId',
          page: () => const NotificationAddReviewPage(),
        ),
        GetPage(
          name: '/deep_trip_detail',
          page: () => const DeepTripDetailPage(),
        ),
      ],
    );
  }
}

class LifecycleEventHandler extends WidgetsBindingObserver {
  final AsyncCallback? resumeCallBack;
  final AsyncCallback? suspendingCallBack;
  final AsyncCallback? closeCallBack;
  final serviceController = Get.find<Service>();

  LifecycleEventHandler({
    this.resumeCallBack,
    this.suspendingCallBack,
    this.closeCallBack,
  });

  @override
  Future<void> didChangeAppLifecycleState(AppLifecycleState state) async {
    if (kDebugMode) {
      logger.info("state changed ${state.name}");
    }
    switch (state) {
      case AppLifecycleState.resumed:
        logger.info('📱 APP RESUMED');
        logger.info('  Current route: ${Get.currentRoute}');
        logger.info('  Token exists: ${serviceController.token.isNotEmpty}');
        if (serviceController.token != "") {
          if (kDebugMode) {
            logger.info('main provider called (user is online)');
          }
          MainProvider().updateStatus(serviceController.token, "1");
        }

        break;
      case AppLifecycleState.inactive:
        break;

      case AppLifecycleState.paused:
        if (serviceController.token != "") {
          if (kDebugMode) {
            logger.info('main provider called (user is offline)');
          }
          MainProvider().updateStatus(serviceController.token, "0");
        }
        break;

      case AppLifecycleState.detached:
        if (closeCallBack != null) {
          await closeCallBack!();
          if (kDebugMode) {
            logger.info('App is being detached and will be terminated');
          }
        }
        break;

      case AppLifecycleState.hidden:
        if (suspendingCallBack != null) {
          await suspendingCallBack!();
        }
        break;
    }
  }
}
