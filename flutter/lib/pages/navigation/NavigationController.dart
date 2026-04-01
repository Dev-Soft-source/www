import 'dart:convert';

import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/pages/chat/ChatController.dart';
import 'package:proximaride_app/pages/messaging_page/MessagingController.dart';
import 'package:proximaride_app/pages/navigation/navigationProvider.dart';
import 'package:proximaride_app/pages/notifications/NotificationProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/notification_service.dart';
import 'package:proximaride_app/services/pusher_service.dart';
import 'package:proximaride_app/services/service.dart';

class NavigationController extends GetxController {
  var currentNavIndex = 0.obs;
  var closedApp = 0.obs;
  final serviceController = Get.find<Service>();
  final PusherService _pusherService = PusherService();

  int _readIntId(dynamic value) {
    if (value is int) return value;
    if (value is String) {
      return int.tryParse(value) ?? 0;
    }
    return 0;
  }

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    logger.info('NavigationController onInit (route: ${Get.currentRoute})');
    if (serviceController.openDeepLinkPage.value == true) {
      Future.delayed(Duration(seconds: 2), () {
        serviceController.openDeepLinkPage.value = false;
        Get.toNamed("/deep_trip_detail");
      });
    }

    await logoutAdminDeActiveAccount();
    // Fetch notification count immediately
    await getNotificationCount();
    requestPermissionAndGetToken();

    // Initialize Pusher and subscribe to chat channel
    await initializePusherAndSubscribeToChat();

    currentNavIndex.value = serviceController.navigationIndex.value;
    if (serviceController.backgroundNotification == "backgroundNotification") {
      await Future.delayed(const Duration(seconds: 1));
      Get.toNamed('/notifications');
    }
  }

  Future<void> initializePusherAndSubscribeToChat() async {
    try {
      logger.info('Initializing Pusher for chat...');

      // Get user ID
      final userId = _readIntId(serviceController.loginUserDetail['id']);

      if (userId == 0) {
        logger.error('Cannot subscribe to chat: User ID is not available');
        return;
      }

      // Subscribe to chat channel
      await _pusherService.subscribeToChatChannel(
        userId,
        (data) => handleNewChatMessage(data),
      );

      logger.info('Successfully subscribed to chat channel for user: $userId');
    } catch (e) {
      logger.error('Error initializing Pusher chat: $e');
    }
  }

  void handleNewChatMessage(dynamic messageData) {
    try {
      final parsedMessageData = json.decode(messageData.toString());

      logger.info("Parsed message data: ${parsedMessageData.toString()}");

      if (parsedMessageData.toString() == "{}" || parsedMessageData == null) {
        return;
      }

      // Extract message data
      final messagePayload = parsedMessageData['message'];
      if (messagePayload == null) {
        logger.warning('Message payload is null');
        return;
      }

      final senderId = messagePayload['sender'];
      final receiverId = messagePayload['receiver'];
      final currentUserId = serviceController.loginUserDetail['id'];

      logger.info(
          'Message - Sender: $senderId, Receiver: $receiverId, Current User: $currentUserId');

      // First, check if MessagingController is registered (chat is open)
      bool isMessageForCurrentChat = false;
      bool isChatOpen = false;

      if (Get.isRegistered<MessagingController>()) {
        try {
          final messagingController = Get.find<MessagingController>();

          logger.info('MessagingController is registered');
          logger.info('Current chatUserId: ${messagingController.chatUserId}');
          logger.info('Current userId: ${messagingController.userId}');

          // Check if the message is for the currently open chat
          // The message is for this chat if:
          // 1. Current user is the receiver and sender is the chatUserId, OR
          // 2. Current user is the sender and receiver is the chatUserId
          if (receiverId.toString() == currentUserId.toString() &&
              senderId.toString() ==
                  messagingController.chatUserId.toString()) {
            // Message is sent TO the current user FROM the chat user
            isMessageForCurrentChat = true;
            isChatOpen = true;
            logger.info('Message is for current chat (user received message)');
          } else if (senderId.toString() == currentUserId.toString() &&
              receiverId.toString() ==
                  messagingController.chatUserId.toString()) {
            // Message is sent FROM the current user TO the chat user
            // This shouldn't happen via Pusher since we sent it, but handle it just in case
            isMessageForCurrentChat = true;
            isChatOpen = true;
            logger.info('Message is for current chat (user sent message)');
          } else {
            logger.info('Message is not for the currently open chat');
          }

          if (isMessageForCurrentChat) {
            logger.info('Adding message to MessagingController messagesList');
            // Call the method in MessagingController to handle the new message
            messagingController.handleNewRealTimeMessage(messagePayload);
          }
        } catch (e) {
          logger.error('Error updating MessagingController: $e');
        }
      } else {
        logger.info(
            'MessagingController not registered yet, message will be loaded when messaging screen opens');
      }

      // Update ChatController chat list
      // Only increment unread count if the chat is NOT currently open
      // OR if the message is NOT for the currently open chat
      if (Get.isRegistered<ChatController>()) {
        try {
          final chatController = Get.find<ChatController>();

          if (chatController.myChats.isNotEmpty) {
            // Find the chat where the sender and receiver IDs match (checking both directions)
            final chatIndex = chatController.myChats.indexWhere((chat) {
              final sender = chat['sender'];
              final receiver = chat['receiver'];
              final chatSenderId =
                  sender is Map ? sender['id']?.toString() : null;
              final chatReceiverId =
                  receiver is Map ? receiver['id']?.toString() : null;
              // Match if: (chat sender = message sender AND chat receiver = message receiver) OR
              //           (chat sender = message receiver AND chat receiver = message sender)
              return (chatSenderId == senderId.toString() &&
                      chatReceiverId == receiverId.toString()) ||
                  (chatSenderId == receiverId.toString() &&
                      chatReceiverId == senderId.toString());
            });
            logger.info("Found chat index: $chatIndex");
            if (chatIndex != -1) {
              chatController.myChats[chatIndex]['message'] =
                  messagePayload['message'];

              // Only increment unread count if:
              // 1. The chat is NOT currently open (MessagingController not registered), OR
              // 2. The message is NOT for the currently open chat
              if (!isChatOpen || !isMessageForCurrentChat) {
                chatController.myChats[chatIndex]['unread_count']++;
                logger.info(
                    'Incrementing unread count for chat (chat is not open or message not for current chat)');
              } else {
                // Chat is open and message is for this chat, so unread should be 0
                chatController.myChats[chatIndex]['unread_count'] = 0;
                logger.info('Chat is open, setting unread count to 0');
              }

              chatController.myChats.refresh();
            }
          }
        } catch (e) {
          logger.error('Error updating ChatController: $e');
        }
      }
    } catch (e) {
      logger.error('Error handling new chat message: $e');
    }
  }

  @override
  void onClose() {
    // TODO: implement onClose
    // Disconnect Pusher when leaving the navigation screen
    _pusherService.disconnect();
    super.onClose();
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
        sound: false,
        alert: true,
        announcement: true,
        badge: true,
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
        logger.warning("FCM token is null or empty");
        return;
      }

      logger.info("FCM Token: $fcmToken");
      NavigationProvider()
          .updateUserFcmToken(serviceController.token, fcmToken)
          .then((resp) async {
        logger.info("Update User FCM Token Response: ${resp.toString()}");
        serviceController.notificationCount.value =
            int.parse(resp['data']['notificationCount'].toString());
        await NotificationService()
            .setBadgeCount(serviceController.notificationCount.value);
      }, onError: (err) {
        logger.error("Failed to update user FCM token: $err");
      });
    } catch (e, stackTrace) {
      final errorMessage = e.toString();
      if (kIsWeb &&
          errorMessage.contains('failed-service-worker-registration')) {
        logger.warning(
            "Skipping web FCM token registration because the Firebase messaging service worker is not available.");
        return;
      }

      logger.error("Failed to get FCM token in NavigationController: $e");
      logger.error(stackTrace.toString());
    }
  }

  Future<void> getNotificationCount() async {
    try {
      await NotificationProvider()
          .getNotifications(
              serviceController.token, "", "", serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null &&
            resp['data'] != null &&
            resp['data']['notifications'] != null) {
          // Filter for unread notifications
          List filteredNotifications =
              resp['data']['notifications'].where((notification) {
            return notification['is_read'] == "0";
          }).toList();

          // Update notification count
          serviceController.notificationCount.value =
              filteredNotifications.length;
          serviceController.notificationCount.refresh();
          await NotificationService()
              .setBadgeCount(serviceController.notificationCount.value);
          logger.info(
              "Notification count loaded: ${serviceController.notificationCount.value}");
        }
      }, onError: (error) {
        // Silent fail - notification count will remain at 0
        logger.info("Failed to load notification count: $error");
      });
    } catch (exception) {
      // Silent fail - notification count will remain at 0
      logger.info("Exception loading notification count: $exception");
    }
  }

  logoutAdminDeActiveAccount() async {
    try {
      await StageProvider()
          .logoutAdminDeActiveAccount(
        serviceController.token,
      )
          .then((resp) async {
        if (resp['data'] != null && resp['data']['status'] == "1") {
          serviceController.secureStorage.deleteAll();
          Get.offAllNamed('/login');
          if (resp['data']['message'] != null) {
            serviceController.showDialogue(resp['data']['message'].toString());
          }
        }
      }, onError: (error) {
        serviceController.showDialogue(error.toString(), type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }
}
