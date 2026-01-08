
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/services/service.dart';

class NotificationService {
  final FlutterLocalNotificationsPlugin notificationsPlugin =
  FlutterLocalNotificationsPlugin();

  final serviceController = Get.find<Service>();

  NotificationService() {
    initNotification();
  }

  Future<void> initNotification() async {
    const AndroidInitializationSettings initializationSettingsAndroid =
    AndroidInitializationSettings('logo_notification');

    final DarwinInitializationSettings initializationSettingsIOS =
    DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
      onDidReceiveLocalNotification:
          (int id, String? title, String? body, String? payload) async {
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
        serviceController.backgroundNotification = "backgroundNotification";
      },
    );
  }

  NotificationDetails notificationDetails() {
    return const NotificationDetails(
      android: AndroidNotificationDetails('channelId', 'channelName'),
      iOS: DarwinNotificationDetails(),
    );
  }

  Future<void> showNotification(
      {int id = 0, String? title, String? body, String? payload}) async {
    print('chat received');
    print(body);

    await notificationsPlugin.show(
      id,
      title,
      body,
      notificationDetails(),
    );
  }
}