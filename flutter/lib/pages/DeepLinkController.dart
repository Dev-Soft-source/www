import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:app_links/app_links.dart';
import 'package:proximaride_app/pages/login/LoginController.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class DeepLinkController extends GetxController {
  late final AppLinks _appLinks;
  final serviceController = Get.find<Service>();
  bool _isHandlingDeepLink = false;
  String _pendingDeepLinkToken = "";

  bool get isHandlingDeepLink => _isHandlingDeepLink;

  String get pendingDeepLinkToken => _pendingDeepLinkToken;

  bool _isSupportedWebDeepLinkHost(Uri uri) {
    final host = uri.host.toLowerCase();
    return host == '127.0.0.1' ||
        host == 'localhost' ||
        host == 'proximaride.com' ||
        host == 'www.proximaride.com';
  }

  @override
  void onInit() {
    super.onInit();
    _initDeepLinks();
  }

  void _initDeepLinks() async {
    _appLinks = AppLinks();
    final initialUri = await _appLinks.getInitialLink();
    if (initialUri != null) {
      logger.info(
          'DeepLinkController: Received initial URI on app start: $initialUri');
      _handleDeepLink(initialUri);
    }
    logger.info('DeepLinkController: Setting up uriLinkStream listener');
    _appLinks.uriLinkStream.listen(
      (Uri? uri) {
        if (uri != null) {
          logger.info('DeepLinkController: Received URI from stream: $uri');
          _handleDeepLink(uri);
        }
      },
      onError: (err) {
        logger.info('Error receiving deep link: $err');
      },
    );
  }

  void _handleDeepLink(Uri uri) async {
    if (_isHandlingDeepLink) {
      logger.warning(
        'DeepLinkController: ⚠️ SKIPPING - Already handling a deep link: $uri',
      );
      return;
    }

    _isHandlingDeepLink = true;

    logger.info('═══════════════════════════════════════════════════════');
    logger.info('🔗 DEEP LINK HANDLER STARTED');
    logger.info('URI: $uri');
    logger.info('Current route (BEFORE): ${Get.currentRoute}');
    logger.info('═══════════════════════════════════════════════════════');

    try {
      // Handle xelentride://booking deep links
      if (uri.scheme == 'xelentride' && uri.host == 'booking') {
        final bookingId = uri.queryParameters['booking_id'];
        final action = uri.queryParameters['action'];

        if (bookingId != null && action != null) {
          if (action != "accept" && action != "reject") {
          } else {
            serviceController.openDeepLinkPage.value = true;
            serviceController.actionDeep.value = action;
            serviceController.bookingDeepId.value = bookingId.toString();
            Get.offAll(() => SizedBox(child: Text("")));
          }
        }
      }
      // Handle proximaride://email-verified deep links from browser
      else if (uri.scheme == 'proximaride' && uri.host == 'email-verified') {
        logger.info('App-scheme email verification deep link detected');

        final token = uri.queryParameters['token'];
        final status = uri.queryParameters['status'];

        logger.info('Status: $status, Token: $token');

        if (token != null && token.isNotEmpty) {
          LoginController loginController;
          if (Get.isRegistered<LoginController>()) {
            loginController = Get.find<LoginController>();
          } else {
            loginController = Get.put(LoginController());
          }

          await Future.delayed(const Duration(milliseconds: 500));
          await loginController.loginWithToken(token, persistToken: false);
        } else {
          await Future.delayed(const Duration(milliseconds: 300));
          serviceController.showDialogue(
            status == 'success'
                ? "Your email has been verified successfully."
                : "This email has already been verified.",
          );
          Get.offAllNamed('/login');
        }
      }
      // Handle http://127.0.0.1:8000/en/login-with-app
      else if ((uri.scheme == 'https' || uri.scheme == 'http') &&
          _isSupportedWebDeepLinkHost(uri) &&
          uri.path == '/en/login-with-app') {
        logger.info('Login with app deep link detected');

        // Extract query parameters
        final token = uri.queryParameters['token'];
        final email = uri.queryParameters['email'];
        final userId = uri.queryParameters['user_id'];

        _pendingDeepLinkToken = token ?? "";

        logger.info('Token: $token, Email: $email, UserId: $userId');

        // Handle auto-login with token
        if (token != null && token.isNotEmpty) {
          logger.info('Token found, attempting auto-login...');

          // Get or create LoginController
          LoginController loginController;
          if (Get.isRegistered<LoginController>()) {
            loginController = Get.find<LoginController>();
          } else {
            loginController = Get.put(LoginController());
          }

          // Wait a bit for the controller to initialize
          await Future.delayed(const Duration(milliseconds: 500));

          // Perform auto-login with token
          await loginController.loginWithToken(token, persistToken: false);
        } else {
          // No token provided, navigate to login page
          logger.info('No token provided, navigating to login page');
          Get.offAllNamed('/login');
        }
      } // Handle email verification with token
      else if ((uri.scheme == 'https' || uri.scheme == 'http') &&
          _isSupportedWebDeepLinkHost(uri) &&
          uri.path == '/email-verified' &&
          uri.queryParameters['success'] == 'verified' &&
          uri.queryParameters['app'] == 'true' &&
          uri.queryParameters.containsKey('token')) {
        logger.info('Email verification deep link detected with token');

        final token = uri.queryParameters['token'];

        logger.info('Token: $token');

        if (token != null && token.isNotEmpty) {
          logger.info('Token found, attempting auto-login...');

          // Get or create LoginController
          LoginController loginController;
          if (Get.isRegistered<LoginController>()) {
            loginController = Get.find<LoginController>();
          } else {
            loginController = Get.put(LoginController());
          }

          // Wait a bit for the controller to initialize
          await Future.delayed(const Duration(milliseconds: 500));

          // Show welcome dialog
          await loginController.loginWithToken(token, persistToken: false);
        }
      } // Handle email already verified (no token)
      else if ((uri.scheme == 'https' || uri.scheme == 'http') &&
          _isSupportedWebDeepLinkHost(uri) &&
          uri.path == '/email-verified' &&
          uri.queryParameters['app'] == 'true' &&
          !uri.queryParameters.containsKey('token')) {
        logger.info('Email already verified deep link detected');
        await Future.delayed(const Duration(milliseconds: 500));

        // Show "Email already verified" dialog
        // await Future.delayed(const Duration(milliseconds: 500));
        logger.info('→ Showing dialog now...');
        serviceController.showDialogue("This email has already been verified.");

        logger.info('→ Dialog showDialog() call completed');
      } // Handle mobile-close-redirect
      else if ((uri.scheme == 'https' || uri.scheme == 'http') &&
          _isSupportedWebDeepLinkHost(uri) &&
          uri.path == '/mobile-close-redirect') {
        logger.info('Mobile close redirect deep link detected');

        // Check if user is logged in
        final isLoggedIn = serviceController.token.isNotEmpty;

        if (isLoggedIn) {
          logger.info('User is already logged in, staying on current screen');
          // Do nothing - user stays on current screen
        } else {
          logger.info('User is not logged in, navigating to login page');
          Get.offAllNamed('/login');
        }
      } else {
        logger.info('Deep link does not match any known pattern.');
        logger.info('Available patterns:');
        logger.info('1. proximaride://booking?booking_id=X&action=Y');
        logger.info('2. http://127.0.0.1:8000/en/login-with-app');
        logger.info('3. https://13b2407bb966.ngrok-free.app/en/login-with-app');
        logger.info('4. https://proximaride.com/en/login-with-app');
        logger.info('5. http://127.0.0.1:8000/mobile-close-redirect');
        logger.info('6. proximaride://email-verified?status=success&token=...');
      }
    } catch (e, st) {
      logger.error('❌ Deep link handling ERROR: $e');
      logger.error('Stack trace: $st');
    } finally {
      logger.info('═══════════════════════════════════════════════════════');
      logger.info('✅ DEEP LINK HANDLER COMPLETED');
      logger.info('Current route (AFTER): ${Get.currentRoute}');

      logger.info('═══════════════════════════════════════════════════════');

      _isHandlingDeepLink = false;
    }
  }
}
