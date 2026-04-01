import 'dart:async';
import 'dart:io';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/post_ride/PostRideProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

import 'NotificationProvider.dart';

class NotificationController extends GetxController {
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  late final ConnectivityService connectivityService;

  var notificationsList = List<dynamic>.empty(growable: true).obs;
  var isLoading = true.obs;
  var isOverlayLoading = false.obs;
  var paymentOptionList = [].obs;
  var paymentOptionToolTipList = [].obs;
  var paymentOptionLabelList = [].obs;
  var bookingOptionList = [].obs;
  var bookingOptionToolTipList = [].obs;
  var bookingOptionLabelList = [].obs;
  var paymentMethod = "".obs;
  var bookingType = "".obs;
  var actionType = "".obs;
  var filter = false.obs;
  // Controls whether the info banner on the notifications page is expanded.
  var isInfoExpanded = false.obs;
  // final serviceController = Get.find<Service>();

  var labelTextDetail = {}.obs;

  @override
  void onInit() async {
    super.onInit();

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    requestPermissionAndGetToken();
    await loadInitialData();
  }

  @override
  void onClose() {
    super.onClose();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // Load all initial data
      await _getNotifications();
      await _getLabelTextDetail();
      await _getPaymentOptions();
      await _getBookingOption();

      errorStateManager.setSuccess();
      isLoading(false);
    } on SocketException {
      logger.error("Network error in loadInitialData: SocketException");
      errorStateManager.setError(
        "No internet connection. Please check your network and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } on TimeoutException {
      logger.error("Timeout error in loadInitialData");
      errorStateManager.setError(
        "Request timed out. Please check your connection and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } catch (error) {
      logger.error("Error in loadInitialData: $error");

      if (error is Map &&
          error.containsKey('type') &&
          error.containsKey('message')) {
        errorStateManager.setError(
          error["message"],
          _parseErrorType(error["type"]),
          loadInitialData,
        );
      } else if (error.toString().contains("SocketException") ||
          error.toString().contains("Network is unreachable") ||
          error.toString().contains("Connection refused")) {
        errorStateManager.setError(
          "No internet connection. Please check your network and try again.",
          ErrorType.network,
          loadInitialData,
        );
      } else {
        errorStateManager.setError(
          "Unable to load notifications. Please check your connection and try again.",
          ErrorType.unknown,
          loadInitialData,
        );
      }
    }
  }

  ErrorType _parseErrorType(String type) {
    switch (type) {
      case "network":
        return ErrorType.network;
      case "server":
        return ErrorType.server;
      default:
        return ErrorType.unknown;
    }
  }

  Future<bool> _canUseFirebaseMessaging() async {
    if (!kIsWeb) {
      return true;
    }

    try {
      final isSupported = await FirebaseMessaging.instance.isSupported();
      if (!isSupported) {
        logger.warning(
            "Skipping FCM token fetch on web because Firebase Messaging is not supported in this browser/environment.");
      }
      return isSupported;
    } catch (e) {
      logger.warning("Unable to verify Firebase Messaging support on web: $e");
      return false;
    }
  }

  Future<void> requestPermissionAndGetToken() async {
    try {
      if (!await _canUseFirebaseMessaging()) {
        return;
      }

      final notificationSettings =
          await FirebaseMessaging.instance.requestPermission(
        provisional: true,
      );

      final isPermissionGranted =
          notificationSettings.authorizationStatus ==
                  AuthorizationStatus.authorized ||
              notificationSettings.authorizationStatus ==
                  AuthorizationStatus.provisional;

      if (!isPermissionGranted) {
        logger.info(
            "Notification permission not granted: ${notificationSettings.authorizationStatus}");
        return;
      }

      final fcmToken = await FirebaseMessaging.instance.getToken();
      if (fcmToken == null || fcmToken.isEmpty) {
        logger.warning("FCM token is null or empty in NotificationController");
        return;
      }

      logger.info("FCM Token: $fcmToken");
    } catch (e, stackTrace) {
      final errorMessage = e.toString();
      if (kIsWeb &&
          errorMessage.contains('failed-service-worker-registration')) {
        logger.warning(
            "Skipping web FCM token registration because the Firebase messaging service worker is not available.");
        return;
      }

      logger.error("Failed to get FCM token in NotificationController: $e");
      logger.error(stackTrace.toString());
    }
  }

  // Private method for initial load (no dialog on error, throws to loadInitialData)
  Future<void> _getNotifications() async {
    notificationsList.clear();
    logger.info("Booking Type: ${bookingType.value}");
    logger.info("Payment Method: ${paymentMethod.value}");
    logger.info("Language ID: ${serviceController.langId.value}");

    await NotificationProvider()
        .getNotifications(serviceController.token, bookingType.value,
            paymentMethod.value, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null &&
          resp['data'] != null &&
          resp['data']['notifications'] != null) {
        // Show all notifications (both read and unread)
        List allNotifications = resp['data']['notifications'];
        notificationsList.addAll(allNotifications);

        // Count only unread notifications for the badge
        int unreadCount = allNotifications.where((notification) {
          return notification['is_read'] == "0";
        }).length;

        // Keep global unread notifications count in sync
        serviceController.notificationCount.value = unreadCount;
        serviceController.notificationCount.refresh();
        logger.info("Notifications Length: ${notificationsList.length}");
        logger.info("Unread Notifications Count: $unreadCount");
        Get.log("The notification list is $notificationsList");
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }

  // Public method for filtering/refreshing (shows dialog on error)
  Future<void> getNotifications({int type = 0}) async {
    try {
      if (type == 1) {
        isOverlayLoading(true);
      }
      notificationsList.clear();
      logger.info("Booking Type: ${bookingType.value}");
      logger.info("Payment Method: ${paymentMethod.value}");
      logger.info("Language ID: ${serviceController.langId.value}");
      await NotificationProvider()
          .getNotifications(serviceController.token, bookingType.value,
              paymentMethod.value, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null &&
            resp['data'] != null &&
            resp['data']['notifications'] != null) {
          // Show all notifications (both read and unread)
          List allNotifications = resp['data']['notifications'];
          notificationsList.addAll(allNotifications);

          // Count only unread notifications for the badge
          int unreadCount = allNotifications.where((notification) {
            return notification['is_read'] == "0";
          }).length;

          // Keep global unread notifications count in sync
          serviceController.notificationCount.value = unreadCount;
          serviceController.notificationCount.refresh();
          logger.info("Notifications Length: ${notificationsList.length}");
          logger.info("Unread Notifications Count: $unreadCount");
          Get.log("The notification list is $notificationsList");
        }
        if (type == 1) {
          isOverlayLoading(false);
        }
      }, onError: (error) {
        if (error is Map &&
            error.containsKey('type') &&
            error.containsKey('message')) {
          serviceController.showDialogue(error['message'], type: "error");
        } else if (error is Map &&
            error.containsKey('type') &&
            error['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(error.toString(), type: "error");
        }
        if (type == 1) {
          isOverlayLoading(false);
        }
      });
    } catch (exception) {
      if (exception is Map &&
          exception.containsKey('type') &&
          exception.containsKey('message')) {
        serviceController.showDialogue(exception['message'], type: "error");
      } else if (exception is Map &&
          exception.containsKey('type') &&
          exception['type'] == 'network') {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(exception.toString(), type: "error");
      }
      type == 1 ? isOverlayLoading(false) : isLoading(false);
    }
  }

  getSearchNotification() async {
    if (actionType.value == "clear") {
      bookingType.value = "";
      paymentMethod.value = "";
    }

    await getNotifications(type: 1);
  }

  Future<void> readNotification(notificationId, {bool showError = true}) async {
    try {
      await NotificationProvider()
          .readNotification(serviceController.token, notificationId)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.notificationCount.value =
              serviceController.notificationCount.value - 1;
          serviceController.notificationCount.refresh();
        }
      }, onError: (error) {
        if (!showError) {
          logger.warning(
              "Failed to mark notification as read for id=$notificationId: $error");
          return;
        }

        if (error is Map &&
            error.containsKey('type') &&
            error.containsKey('message')) {
          serviceController.showDialogue(error['message'], type: "error");
        } else if (error is Map &&
            error.containsKey('type') &&
            error['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(error.toString(), type: "error");
        }
      });
    } catch (exception) {
      if (!showError) {
        logger.warning(
            "Failed to mark notification as read for id=$notificationId: $exception");
        return;
      }

      if (exception is Map &&
          exception.containsKey('type') &&
          exception.containsKey('message')) {
        serviceController.showDialogue(exception['message'], type: "error");
      } else if (exception is Map &&
          exception.containsKey('type') &&
          exception['type'] == 'network') {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(exception.toString(), type: "error");
      }
    }
  }

  Future<void> deleteNotification(notificationId) async {
    try {
      isOverlayLoading(true);
      await NotificationProvider()
          .deleteNotification(serviceController.token, notificationId)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.notificationCount.value =
              serviceController.notificationCount.value - 1;
          notificationsList.removeWhere((element) =>
              element['id'].toString() == notificationId.toString());
          serviceController.notificationCount.refresh();
          notificationsList.refresh();
          isOverlayLoading(false);
        }
      }, onError: (error) {
        if (error is Map &&
            error.containsKey('type') &&
            error.containsKey('message')) {
          serviceController.showDialogue(error['message'], type: "error");
        } else if (error is Map &&
            error.containsKey('type') &&
            error['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(error.toString(), type: "error");
        }
        isOverlayLoading(false);
      });
    } catch (exception) {
      if (exception is Map &&
          exception.containsKey('type') &&
          exception.containsKey('message')) {
        serviceController.showDialogue(exception['message'], type: "error");
      } else if (exception is Map &&
          exception.containsKey('type') &&
          exception['type'] == 'network') {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(exception.toString(), type: "error");
      }
      isOverlayLoading(false);
    }
  }

  Future<void> _getPaymentOptions() async {
    await PostRideProvider()
        .getPaymentOptions(
            serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['paymentOptions'] != null) {
          paymentOptionList.addAll(resp['data']['paymentOptions']);
        }
        if (resp['data'] != null && resp['data']['paymentTooltips'] != null) {
          paymentOptionToolTipList.addAll(resp['data']['paymentTooltips']);
        }
        if (resp['data'] != null && resp['data']['paymentLabels'] != null) {
          paymentOptionLabelList.addAll(resp['data']['paymentLabels']);
        }
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }

  Future<void> _getBookingOption() async {
    await PostRideProvider()
        .getBookingOption(
            serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['bookingOptions'] != null) {
          bookingOptionList.addAll(resp['data']['bookingOptions']);
        }
        if (resp['data'] != null && resp['data']['bookingTooltips'] != null) {
          bookingOptionToolTipList.addAll(resp['data']['bookingTooltips']);
        }

        if (resp['data'] != null && resp['data']['bookingLabels'] != null) {
          bookingOptionLabelList.addAll(resp['data']['bookingLabels']);
        }
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }

  Future<void> _getLabelTextDetail() async {
    await StageProvider()
        .getLabelTextDetail(
            serviceController.langId.value, chatPage, serviceController.token)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['chatsPage'] != null) {
          labelTextDetail.addAll(resp['data']['chatsPage']);
        }

        var getLanguage = serviceController.languages.firstWhereOrNull(
            (element) => element['id'] == serviceController.langId.value);
        if (getLanguage != null) {
          serviceController.langIcon.value = getLanguage['flag_icon'];
          serviceController.lang.value = getLanguage['abbreviation'];
        }
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }
}
