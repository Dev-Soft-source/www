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

import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/services/service.dart';
import 'dart:developer' as developer;

class NotificationService {
  final FlutterLocalNotificationsPlugin notificationsPlugin =
      FlutterLocalNotificationsPlugin();

  final serviceController = Get.find<Service>();

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
      serviceController.backgroundNotification = "backgroundNotification";

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
    // Example: Extract chat ID from payload
    // Get.toNamed('/chat', arguments: {'chatId': extractChatId(payload)});
    Get.toNamed('/chat');
  }

  void _navigateToRide(String payload) {
    developer.log('Navigating to ride', name: 'NotificationService');
    // Example: Extract ride ID from payload
    // Get.toNamed('/ride', arguments: {'rideId': extractRideId(payload)});
    Get.toNamed('/ride');
  }

  void _navigateToBooking(String payload) {
    developer.log('Navigating to booking', name: 'NotificationService');
    // Example: Extract booking ID from payload
    // Get.toNamed('/booking', arguments: {'bookingId': extractBookingId(payload)});
    Get.toNamed('/booking');
  }

  void _performDefaultAction() {
    developer.log('Performing default action', name: 'NotificationService');
    // Navigate to home or main screen
    Get.toNamed('/home');
  }

  NotificationDetails notificationDetails() {
    return const NotificationDetails(
      android: AndroidNotificationDetails(
        'channelId',
        'channelName',
        channelDescription: 'Channel for ProximaRide notifications',
        importance: Importance.high,
        priority: Priority.high,
        showWhen: false,
      ),
      iOS: DarwinNotificationDetails(
        presentAlert: true,
        presentBadge: true,
        presentSound: true,
      ),
    );
  }

  Future<void> showNotification(
      {int id = 0, String? title, String? body, String? payload}) async {
    developer.log('Showing notification', name: 'NotificationService');
    developer.log('Title: $title', name: 'NotificationService');
    developer.log('Body: $body', name: 'NotificationService');
    developer.log('Payload: $payload', name: 'NotificationService');

    try {
      await notificationsPlugin.show(
        id,
        title,
        body,
        notificationDetails(),
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
    developer.log('All notifications cancelled', name: 'NotificationService');
  }
}
