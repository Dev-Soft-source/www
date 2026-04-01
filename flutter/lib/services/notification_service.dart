// import 'package:flutter_local_notifications/flutter_local_notifications.dart';
// import 'package:proximaride_app/consts/constFileLink.dart';
// import 'package:get/get.dart';
// import 'package:proximaride_app/services/service.dart';

// class NotificationService {
//   final FlutterLocalNotificationsPlugin notificationsPlugin =
//   FlutterLocalNotificationsPlugin();

//   final serviceController = Get.find<Service>();

//   NotificationService() {
//     initNotification();
//   }

//   Future<void> initNotification() async {
//     const AndroidInitializationSettings initializationSettingsAndroid =
//     AndroidInitializationSettings('logo_notification');

//     final DarwinInitializationSettings initializationSettingsIOS =
//     DarwinInitializationSettings(
//       requestAlertPermission: true,
//       requestBadgePermission: true,
//       requestSoundPermission: true,
//       onDidReceiveLocalNotification:
//           (int id, String? title, String? body, String? payload) async {
//       },
//     );

//     final InitializationSettings initializationSettings =
//     InitializationSettings(
//       android: initializationSettingsAndroid,
//       iOS: initializationSettingsIOS,
//     );

//     await notificationsPlugin.initialize(
//       initializationSettings,
//       onDidReceiveNotificationResponse:
//           (NotificationResponse notificationResponse) async {
//         serviceController.backgroundNotification = "backgroundNotification";
//       },
//     );
//   }

//   NotificationDetails notificationDetails() {
//     return const NotificationDetails(
//       android: AndroidNotificationDetails('channelId', 'channelName'),
//       iOS: DarwinNotificationDetails(),
//     );
//   }

//   Future<void> showNotification(
//       {int id = 0, String? title, String? body, String? payload}) async {
//     logger.info('chat received');
//     logger.info(body);

//     await notificationsPlugin.show(
//       id,
//       title,
//       body,
//       notificationDetails(),
//     );
//   }
// }

import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/services/service.dart';
import 'dart:convert';
import 'dart:developer' as developer;

class NotificationService {
  static const String _badgeCountStorageKey = 'notification_badge_count';
  static const FlutterSecureStorage _storage = FlutterSecureStorage();
  final FlutterLocalNotificationsPlugin notificationsPlugin =
      FlutterLocalNotificationsPlugin();

  Service? get serviceController =>
      Get.isRegistered<Service>() ? Get.find<Service>() : null;

  Future<void> initNotification() async {
    developer.log('Initializing notification service',
        name: 'NotificationService');

    const AndroidInitializationSettings initializationSettingsAndroid =
        AndroidInitializationSettings('logo_notification');

    final DarwinInitializationSettings initializationSettingsIOS =
        DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
      onDidReceiveLocalNotification:
          (int id, String? title, String? body, String? payload) async {
        developer.log('iOS Local notification received: $title',
            name: 'NotificationService');
        _handleNotificationClick(id, title, body, payload);
      },
    );

    final InitializationSettings initializationSettings =
        InitializationSettings(
      android: initializationSettingsAndroid,
      iOS: initializationSettingsIOS,
    );

    await notificationsPlugin.initialize(
      initializationSettings,
      onDidReceiveNotificationResponse:
          (NotificationResponse notificationResponse) async {
        developer.log('Notification clicked: ${notificationResponse.payload}',
            name: 'NotificationService');
        _handleNotificationResponse(notificationResponse);
      },
    );

    // Request permissions
    await _requestPermissions();
    await syncBadgeCountFromStorage();
  }

  Future<void> _requestPermissions() async {
    // Request permissions for Android 13+
    await notificationsPlugin
        .resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>()
        ?.requestNotificationsPermission();

    // Request permissions for iOS
    await notificationsPlugin
        .resolvePlatformSpecificImplementation<
            IOSFlutterLocalNotificationsPlugin>()
        ?.requestPermissions(
          alert: true,
          badge: true,
          sound: true,
        );
  }

  void _handleNotificationResponse(NotificationResponse response) {
    developer.log('Handling notification response',
        name: 'NotificationService');
    developer.log('Response ID: ${response.id}', name: 'NotificationService');
    developer.log('Response payload: ${response.payload}',
        name: 'NotificationService');

    try {
      serviceController?.backgroundNotification = "backgroundNotification";

      // Handle different notification types based on payload
      if (response.payload != null) {
        _performActionBasedOnPayload(response.payload!);
      } else {
        // Default action when no payload
        _performDefaultAction();
      }
    } catch (e) {
      developer.log('Error handling notification response: $e',
          name: 'NotificationService');
    }
  }

  void _handleNotificationClick(
      int id, String? title, String? body, String? payload) {
    developer.log('Notification clicked - ID: $id, Title: $title',
        name: 'NotificationService');

    if (payload != null) {
      _performActionBasedOnPayload(payload);
    } else {
      _performDefaultAction();
    }
  }

  void _performActionBasedOnPayload(String payload) {
    developer.log('Performing action based on payload: $payload',
        name: 'NotificationService');

    try {
      final decodedPayload = jsonDecode(payload);
      if (decodedPayload is Map) {
        _navigateFromNotificationData(
          Map<String, dynamic>.from(decodedPayload),
        );
        return;
      }
    } catch (_) {
      // Keep backwards compatibility with the legacy non-JSON payload format.
    }

    try {
      // Parse payload and perform specific actions
      if (payload.contains('chat')) {
        _navigateToChat(payload);
      } else if (payload.contains('ride')) {
        _navigateToRide(payload);
      } else if (payload.contains('booking')) {
        _navigateToBooking(payload);
      } else {
        _performDefaultAction();
      }
    } catch (e) {
      developer.log('Error parsing payload: $e', name: 'NotificationService');
      _performDefaultAction();
    }
  }

  void _navigateToChat(String payload) {
    developer.log('Navigating to chat', name: 'NotificationService');
    _performDefaultAction();
  }

  void _navigateToRide(String payload) {
    developer.log('Navigating to ride', name: 'NotificationService');
    _performDefaultAction();
  }

  void _navigateToBooking(String payload) {
    developer.log('Navigating to booking', name: 'NotificationService');
    _performDefaultAction();
  }

  void _performDefaultAction() {
    developer.log('Performing default action', name: 'NotificationService');
    Get.toNamed('/notifications');
  }

  void _navigateFromNotificationData(Map<String, dynamic> data) {
    final notificationType = data['notification_type']?.toString();
    final type = data['type']?.toString();
    final rideId = data['ride_id']?.toString();
    final postedBy = data['posted_by']?.toString();
    final postedTo = data['posted_to']?.toString();
    final id = data['id']?.toString();
    final rideDetailId = data['ride_detail_id']?.toString() ?? '0';

    developer.log('Decoded notification payload: $data',
        name: 'NotificationService');

    if (notificationType == 'review' && rideId != null && id != null) {
      final route = type == '1'
          ? '/notification_add_review/passenger/$rideId/${postedTo ?? '0'}/$id/$rideDetailId'
          : '/notification_add_review/driver/$rideId/0/$id/$rideDetailId';
      _navigateSafely(route);
      return;
    }

    if (notificationType == 'chat received') {
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
        _navigateSafely('/navigation');
        return;
      }

      _navigateSafely('/messaging_page/$chatUserId/$chatRideId/new');
      return;
    }

    if (notificationType == 'phone') {
      _navigateSafely('/my_phone_number');
      return;
    }

    if (notificationType == 'profile') {
      _navigateSafely('/profile_setting');
      return;
    }

    if (notificationType != null && rideId != null) {
      final tripType = type == '1' ? 'ride' : 'trip';
      _navigateSafely(
        '/trip_detail/$rideId/$tripType/$notificationType/$rideDetailId',
      );
      return;
    }

    _performDefaultAction();
  }

  void _navigateSafely(String route) {
    try {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        Future.microtask(() {
          if (Get.currentRoute == route) {
            return;
          }
          Get.toNamed(route);
        });
      });
    } catch (_) {
      Future.delayed(const Duration(milliseconds: 120), () {
        if (Get.currentRoute == route) {
          return;
        }
        Get.toNamed(route);
      });
    }
  }

  NotificationDetails notificationDetails({int badgeCount = 0}) {
    return NotificationDetails(
      android: AndroidNotificationDetails(
        'channelId',
        'channelName',
        channelDescription: 'Channel for ProximaRide notifications',
        importance: Importance.high,
        priority: Priority.high,
        showWhen: false,
        channelShowBadge: true,
        number: badgeCount > 0 ? badgeCount : 0,
      ),
      iOS: DarwinNotificationDetails(
        presentAlert: true,
        presentBadge: true,
        presentSound: true,
        badgeNumber: badgeCount,
      ),
    );
  }

  Future<int> getStoredBadgeCount() async {
    if (kIsWeb) {
      return 0;
    }

    final rawValue = await _storage.read(key: _badgeCountStorageKey);
    return int.tryParse(rawValue ?? '') ?? 0;
  }

  Future<void> setBadgeCount(int count) async {
    if (kIsWeb) {
      return;
    }

    final safeCount = count < 0 ? 0 : count;
    await _storage.write(
      key: _badgeCountStorageKey,
      value: safeCount.toString(),
    );

    if (serviceController != null) {
      serviceController!.notificationCount.value = safeCount;
      serviceController!.notificationCount.refresh();
    }

    final iosPlugin = notificationsPlugin.resolvePlatformSpecificImplementation<
        IOSFlutterLocalNotificationsPlugin>();
    await iosPlugin?.requestPermissions(
      alert: true,
      badge: true,
      sound: true,
    );
  }

  Future<int> incrementBadgeCount() async {
    final currentCount = await getStoredBadgeCount();
    final nextCount = currentCount + 1;
    await setBadgeCount(nextCount);
    return nextCount;
  }

  Future<int> decrementBadgeCount() async {
    final currentCount = await getStoredBadgeCount();
    final nextCount = currentCount > 0 ? currentCount - 1 : 0;
    await setBadgeCount(nextCount);
    return nextCount;
  }

  Future<void> clearBadgeCount() async {
    await setBadgeCount(0);
  }

  Future<void> syncBadgeCountFromStorage() async {
    final currentCount = await getStoredBadgeCount();
    await setBadgeCount(currentCount);
  }

  Future<void> showNotification(
      {int id = 0, String? title, String? body, String? payload}) async {
    developer.log('Showing notification', name: 'NotificationService');
    developer.log('Title: $title', name: 'NotificationService');
    developer.log('Body: $body', name: 'NotificationService');
    developer.log('Payload: $payload', name: 'NotificationService');

    try {
      final badgeCount = await incrementBadgeCount();
      await notificationsPlugin.show(
        id,
        title,
        body,
        notificationDetails(badgeCount: badgeCount),
        payload: payload,
      );
      developer.log('Notification shown successfully',
          name: 'NotificationService');
    } catch (e) {
      developer.log('Error showing notification: $e',
          name: 'NotificationService');
    }
  }

  // Method to show notification with specific action
  Future<void> showChatNotification(
      {int id = 0, String? title, String? body, String? chatId}) async {
    developer.log('Showing chat notification', name: 'NotificationService');

    await showNotification(
      id: id,
      title: title,
      body: body,
      payload: 'chat:$chatId',
    );
  }

  Future<void> showRideNotification(
      {int id = 0, String? title, String? body, String? rideId}) async {
    developer.log('Showing ride notification', name: 'NotificationService');

    await showNotification(
      id: id,
      title: title,
      body: body,
      payload: 'ride:$rideId',
    );
  }

  Future<void> showBookingNotification(
      {int id = 0, String? title, String? body, String? bookingId}) async {
    developer.log('Showing booking notification', name: 'NotificationService');

    await showNotification(
      id: id,
      title: title,
      body: body,
      payload: 'booking:$bookingId',
    );
  }

  // Method to cancel notification
  Future<void> cancelNotification(int id) async {
    await notificationsPlugin.cancel(id);
    developer.log('Notification cancelled: $id', name: 'NotificationService');
  }

  // Method to cancel all notifications
  Future<void> cancelAllNotifications() async {
    await notificationsPlugin.cancelAll();
    await clearBadgeCount();
    developer.log('All notifications cancelled', name: 'NotificationService');
  }
}
