import 'dart:async';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/chat/ChatController.dart';
import 'package:proximaride_app/pages/messaging_page/MessagingProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class MessagingController extends GetxController {
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  late final ConnectivityService connectivityService;
  final ScrollController scrollController = ScrollController();

  var isLoading = true.obs;
  dynamic userId;
  String rideId = "";
  String type = "";

  var chatUserInfo = {}.obs;
  var messagesList = List<dynamic>.empty(growable: true).obs;
  late TextEditingController typedMessageController;
  var messageLength = 0.obs;

  var isOverlayLoading = false.obs;

  var chatUserId = "";

  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;
  Timer? _messageRefreshTimer;
  String _routeSignature = "";
  var isSendingMessage = false.obs;

  @override
  void onInit() async {
    super.onInit();

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    userId = serviceController.loginUserDetail['id'];
    typedMessageController = TextEditingController();
    _syncRouteParams();
    _startMessageRefresh();
    await loadInitialData();
  }

  @override
  void onClose() {
    super.onClose();
    _messageRefreshTimer?.cancel();
    scrollController.dispose();
    typedMessageController.dispose();
  }

  void ensureRouteState() {
    final previousSignature = _routeSignature;
    _syncRouteParams();

    if (previousSignature == _routeSignature) {
      return;
    }

    Future.microtask(loadInitialData);
  }

  void _syncRouteParams() {
    chatUserId = Get.parameters['userId'] ?? "";
    final rideIdParam = Get.parameters['rideId'] ?? "";
    rideId = (rideIdParam.isNotEmpty && rideIdParam != "0") ? rideIdParam : "";
    type = Get.parameters['type'] ?? "";
    userId = serviceController.loginUserDetail['id'];
    _routeSignature = "$chatUserId|$rideId|$type";

    logger.info('Chat User ID: $chatUserId');
    logger.info('Ride ID: $rideIdParam');
    logger.info('Type: $type');
    logger.info('User ID: $userId');
  }

  void _startMessageRefresh() {
    _messageRefreshTimer?.cancel();
    _messageRefreshTimer = Timer.periodic(const Duration(seconds: 4), (_) {
      refreshMessagesSilently();
    });
  }

  void scrollToBottom({bool animated = true}) {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!scrollController.hasClients) {
        return;
      }

      final target = scrollController.position.maxScrollExtent;

      if (animated) {
        scrollController.animateTo(
          target,
          duration: const Duration(milliseconds: 250),
          curve: Curves.easeOut,
        );
      } else {
        scrollController.jumpTo(target);
      }
    });
  }

  void _applyMessagesPayload(
    Map resp, {
    bool clearMessages = false,
    bool clearUser = false,
  }) {
    final data = resp['data'];

    if (resp['status'] == null ||
        data == null ||
        data is! Map ||
        data['messages'] == null) {
      return;
    }

    _syncChatUnreadCounts();

    if (data['user'] is Map) {
      if (clearUser) {
        chatUserInfo.clear();
      }
      chatUserInfo.addAll(Map<String, dynamic>.from(data['user']));
    }

    if (data['messages'] is List) {
      final pendingMessages = messagesList
          .where((message) => message is Map && message['delivery_status'] == 'pending')
          .map((message) => Map<String, dynamic>.from(message))
          .toList();

      final incomingMessages = List<dynamic>.from(data['messages']);
      for (final message in incomingMessages) {
        if (message is Map) {
          message['delivery_status'] = 'sent';
        }
      }

      for (final pendingMessage in pendingMessages) {
        final alreadyExists = incomingMessages.any((message) {
          if (message is! Map) {
            return false;
          }

          return message['message']?.toString().trim() ==
                  pendingMessage['message']?.toString().trim() &&
              message['sender'] != null &&
              message['sender'].toString().contains(userId.toString());
        });

        if (!alreadyExists) {
          incomingMessages.add(pendingMessage);
        }
      }

      incomingMessages.sort((a, b) {
        final aTime = DateTime.tryParse(a['created_at']?.toString() ?? '');
        final bTime = DateTime.tryParse(b['created_at']?.toString() ?? '');

        if (aTime == null || bTime == null) {
          return 0;
        }

        return aTime.compareTo(bTime);
      });

      if (clearMessages) {
        messagesList.clear();
      }
      messagesList.assignAll(incomingMessages);
      scrollToBottom(animated: false);
    }

    if (data['chatsPage'] is Map) {
      labelTextDetail.assignAll(Map<String, dynamic>.from(data['chatsPage']));
    }

    if (data['messageSetting'] is Map) {
      popupTextDetail
          .assignAll(Map<String, dynamic>.from(data['messageSetting']));
    }
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      await _getMessages();

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
          "Unable to load messages. Please check your connection and try again.",
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

  void _syncChatUnreadCounts() {
    if (!Get.isRegistered<ChatController>()) {
      return;
    }

    final chatController = Get.find<ChatController>();
    final currentUserId =
        serviceController.loginUserDetail['id']?.toString() ?? '';

    for (int i = 0; i < chatController.myChats.length; i++) {
      final chat = chatController.myChats[i];
      final senderId = chat['sender'] is Map
          ? chat['sender']['id']?.toString() ?? ''
          : '';
      final receiverId = chat['receiver'] is Map
          ? chat['receiver']['id']?.toString() ?? ''
          : '';

      if (senderId.isEmpty || receiverId.isEmpty) {
        continue;
      }

      if (senderId == currentUserId) {
        if (receiverId == userId.toString()) {
          chatController.myChats[i]['unread_count'] = 0;
        }
      } else if (receiverId == userId.toString()) {
        chatController.myChats[i]['unread_count'] = 0;
      }
    }

    chatController.myChats.refresh();
  }

  Future<void> _getMessages() async {
    isLoading(true);
    logger.info('Getting messages...');
    logger.info('Token: ${serviceController.token}');
    logger.info('Chat User ID: $chatUserId');
    logger.info('Ride ID: $rideId');
    logger.info('Type: $type');
    await MessagingProvider()
        .getMessages(serviceController.token, chatUserId, rideId, type)
        .then((resp) async {
      // Defensive checks: resp can be null when app resumes from background
      if (resp == null || resp is! Map) {
        logger.error('Messages response is null or not a Map: $resp');
        return;
      }
      logger.info('Messages: ${resp.toString()}');
      final data = resp['data'];
      if (resp['status'] != null &&
          data != null &&
          data is Map &&
          data['messages'] != null) {
        _applyMessagesPayload(resp, clearMessages: true, clearUser: true);
      }
      isLoading(false);
    }, onError: (error) {
      logger.error('Error getting messages: $error');
      isLoading(false);
      throw error; // Propagate to loadInitialData
    });
  }

  // Public method for user-triggered refresh
  Future<void> getMessages() async {
    try {
      isLoading(true);
      logger.info('Getting messages...');
      logger.info('Token: ${serviceController.token}');
      logger.info('Chat User ID: $chatUserId');
      logger.info('Ride ID: $rideId');
      logger.info('Type: $type');
      await MessagingProvider()
          .getMessages(serviceController.token, chatUserId, rideId, type)
          .then((resp) async {
        // Defensive checks: resp can be null when app resumes from background
        if (resp == null || resp is! Map) {
          logger.error('Messages response is null or not a Map: $resp');
          return;
        }
        logger.info('Messages: ${resp.toString()}');
        final data = resp['data'];
        if (resp['status'] != null &&
            data != null &&
            data is Map &&
            data['messages'] != null) {
          _applyMessagesPayload(resp, clearMessages: true, clearUser: true);
        }
      }, onError: (error) {
        logger.error('Error getting messages: $error');
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
      isLoading(false);
    } catch (exception) {
      logger.error('Error getting messages: $exception');
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
      isLoading(false);
    }
  }

  Future<void> refreshMessagesSilently() async {
    if (chatUserId.isEmpty) {
      return;
    }

    try {
      final resp = await MessagingProvider()
          .getMessages(serviceController.token, chatUserId, rideId, type);

      if (resp is Map) {
        _applyMessagesPayload(resp, clearMessages: true, clearUser: true);
      }
    } catch (error) {
      logger.warning('Silent message refresh skipped: $error');
    }
  }

  Future sendMessage() async {
    if (typedMessageController.text == "") {
      return;
    }

    final messageText = typedMessageController.text.trim();
    if (messageText.isEmpty || isSendingMessage.value) {
      return;
    }

    if (isURL(messageText)) {
      typedMessageController.text = "";
      logger.error('URL is not allowed: $messageText');
      serviceController.showDialogue(
          "${popupTextDetail['url_not_allowed_message'] ?? "URLs are not allowed"}");
      return;
    } else if (isEmail(messageText)) {
      typedMessageController.text = "";
      logger.error('Email is not allowed: $messageText');
      serviceController.showDialogue(
          "${popupTextDetail['email_not_allowed_message'] ?? "Emails are not allowed"}");
      return;
    } else if (isPhoneNumber(messageText)) {
      typedMessageController.text = "";
      logger.error('Phone number is not allowed: $messageText');
      serviceController.showDialogue(
          "${popupTextDetail['phone_number_not_allowed_message'] ?? "Phone numbers are not allowed"}");
      return;
    }

    // Validate and get rideId - use from parameter, or get from existing messages, or default to "0"
    String validRideId = "0";

    // Check if rideId from parameters is valid (not empty, not "0", not null)
    if (rideId.trim().isNotEmpty && rideId != "0") {
      validRideId = rideId;
      logger.info('Using rideId from parameters: $validRideId');
    } else {
      // Try to get ride_id from existing messages
      if (messagesList.isNotEmpty) {
        for (var message in messagesList) {
          if (message['ride_id'] != null &&
              message['ride_id'].toString().trim().isNotEmpty &&
              message['ride_id'].toString() != "0") {
            validRideId = message['ride_id'].toString();
            // Store it for future messages
            rideId = validRideId;
            logger.info('Using rideId from existing messages: $validRideId');
            break;
          }
        }
      }

      if (validRideId == "0") {
        logger.warning(
            'No valid rideId found, using 0 as default. rideId will be sent as 0.');
      }
    }

    final optimisticMessage = _buildOptimisticMessage(messageText, validRideId);
    final optimisticMessageId = optimisticMessage['id'].toString();

    messagesList.add(optimisticMessage);
    messagesList.refresh();
    typedMessageController.clear();
    scrollToBottom();

    try {
      isSendingMessage(true);
      MessagingProvider()
          .sendNewMessage(serviceController.token, validRideId,
              chatUserId.toString(), messageText)
          .then((resp) async {
        if (resp['data'] == null && resp['message'] != null) {
          logger.info('Message: ${resp['message']}');
          _removeMessageById(optimisticMessageId);
          typedMessageController.text = messageText;
          serviceController.showDialogue(resp['message']);
        } else if (resp["data"] != null) {
          final serverMessage = Map<String, dynamic>.from(resp["data"]);
          serverMessage['delivery_status'] = 'sent';
          _replaceMessageById(optimisticMessageId, serverMessage);
          scrollToBottom();
          Future.delayed(const Duration(milliseconds: 600), () {
            refreshMessagesSilently();
          });
          bool isRegistered = Get.isRegistered<ChatController>();
          if (isRegistered == true) {
            var chatController = Get.find<ChatController>();
            await chatController.getChats();
          } else {}
        }

        isSendingMessage(false);
      }, onError: (error) {
        logger.error('Error sending message: $error');
        _removeMessageById(optimisticMessageId);
        typedMessageController.text = messageText;
        isSendingMessage(false);
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
      logger.error('Error sending message: $exception');
      _removeMessageById(optimisticMessageId);
      typedMessageController.text = messageText;
      isSendingMessage(false);
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

  bool isURL(String text) {
    final urlRegex = RegExp(
      r'(https?:\/\/[^\s]+)',
      caseSensitive: false,
    );
    return urlRegex.hasMatch(text);
  }

  bool isEmail(String text) {
    final emailRegex = RegExp(
      r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}',
    );
    return emailRegex.hasMatch(text);
  }

  bool isPhoneNumber(String text) {
    final phoneRegex = RegExp(
      r'(\+?\d{1,4}[\s-]?)?(\(?\d{3,4}\)?[\s-]?)?[\d\s-]{7,10}$',
    );
    return phoneRegex.hasMatch(text);
  }

  /// Handles new real-time message received via Pusher
  /// This method is called from NavigationController when a new message is received
  void handleNewRealTimeMessage(Map<String, dynamic> messagePayload) {
    try {
      logger.info(
          '=== HANDLING NEW REAL-TIME MESSAGE IN MESSAGING CONTROLLER ===');
      logger.info('Message payload: $messagePayload');

      // Check if message already exists in the list (to avoid duplicates)
      // If message has an 'id' field, use it to check for duplicates
      if (messagePayload['id'] != null) {
        final messageId = messagePayload['id'];
        final existingMessageIndex = messagesList.indexWhere(
          (msg) =>
              msg['id'] != null && msg['id'].toString() == messageId.toString(),
        );

        if (existingMessageIndex != -1) {
          logger.info('Message already exists in list, skipping duplicate');
          return;
        }
      }

      // Transform the message to match the expected structure
      // The UI expects sender and receiver to be objects with 'id' fields,
      // but Pusher might send them as integers or strings
      Map<String, dynamic> transformedMessage =
          Map<String, dynamic>.from(messagePayload);
      transformedMessage['delivery_status'] = 'sent';

      // Transform sender if it's an integer or string to an object with 'id'
      if (messagePayload['sender'] == null) {
        logger.error('Message payload missing sender field, skipping message');
        return;
      }

      if (messagePayload['sender'] is int ||
          messagePayload['sender'] is String) {
        transformedMessage['sender'] = {'id': messagePayload['sender']};
        logger.info(
            'Transformed sender from ${messagePayload['sender'].runtimeType} to object: ${transformedMessage['sender']}');
      } else if (messagePayload['sender'] is Map) {
        // Already an object, keep it as is
        transformedMessage['sender'] = messagePayload['sender'];
      } else {
        logger.error(
            'Unexpected sender type: ${messagePayload['sender'].runtimeType}, skipping message');
        return;
      }

      // Transform receiver if it's an integer or string to an object with 'id'
      if (messagePayload['receiver'] == null) {
        logger
            .error('Message payload missing receiver field, skipping message');
        return;
      }

      if (messagePayload['receiver'] is int ||
          messagePayload['receiver'] is String) {
        transformedMessage['receiver'] = {'id': messagePayload['receiver']};
        logger.info(
            'Transformed receiver from ${messagePayload['receiver'].runtimeType} to object: ${transformedMessage['receiver']}');
      } else if (messagePayload['receiver'] is Map) {
        // Already an object, keep it as is
        transformedMessage['receiver'] = messagePayload['receiver'];
      } else {
        logger.error(
            'Unexpected receiver type: ${messagePayload['receiver'].runtimeType}, skipping message');
        return;
      }

      // Add the transformed message to the list
      messagesList.add(transformedMessage);

      // Refresh the list to trigger UI update
      messagesList.refresh();
      scrollToBottom();

      logger.info(
          'Message added to messagesList. Total messages: ${messagesList.length}');

      // Also update the ChatController to mark as read if needed
      // (This is already handled in NavigationController, but we can also ensure it here)
      bool isRegistered = Get.isRegistered<ChatController>();
      if (isRegistered) {
        try {
          var chatController = Get.find<ChatController>();
          // The chat list is already updated in NavigationController
          // But we can ensure unread count is 0 since we're viewing the chat
          for (int i = 0; i < chatController.myChats.length; i++) {
            logger.info('ChatController myChats: ${chatController.myChats[i]}');
            if (chatController.myChats[i] != null) {
              final senderId = chatController.myChats[i]['sender'] is Map
                  ? chatController.myChats[i]['sender']['id']?.toString() ?? ''
                  : '';
              final receiverId = chatController.myChats[i]['receiver'] is Map
                  ? chatController.myChats[i]['receiver']['id']?.toString() ??
                      ''
                  : '';

              if (senderId.isEmpty || receiverId.isEmpty) {
                continue;
              }

              if (senderId == userId.toString()) {
                if (receiverId == chatUserId.toString()) {
                  chatController.myChats[i]['unread_count'] = 0;
                }
              } else {
                if (receiverId == chatUserId.toString()) {
                  chatController.myChats[i]['unread_count'] = 0;
                }
              }
            }
          }
          chatController.myChats.refresh();
        } catch (e) {
          logger.error('Error updating ChatController unread count: $e');
        }
      }
    } catch (e) {
      logger.error('Error handling new real-time message: $e');
    }
  }

  Map<String, dynamic> _buildOptimisticMessage(
      String messageText, String validRideId) {
    final tempId = 'temp_${DateTime.now().microsecondsSinceEpoch}';

    return {
      'id': tempId,
      'ride_id': validRideId,
      'ride_detail_id': null,
      'message': messageText,
      'sender': {
        'id': userId,
        'first_name': serviceController.loginUserDetail['first_name'] ?? '',
        'last_name': serviceController.loginUserDetail['last_name'] ?? '',
        'profile_image': serviceController.loginUserDetail['profile_image'] ?? '',
      },
      'receiver': {
        'id': chatUserId,
      },
      'redirect': '0',
      'created_at': DateTime.now().toIso8601String(),
      'delivery_status': 'pending',
    };
  }

  void _replaceMessageById(String messageId, Map<String, dynamic> newMessage) {
    final index = messagesList.indexWhere(
      (msg) => msg['id'] != null && msg['id'].toString() == messageId,
    );

    if (index == -1) {
      messagesList.add(newMessage);
    } else {
      messagesList[index] = newMessage;
    }

    messagesList.refresh();
  }

  void _removeMessageById(String messageId) {
    messagesList.removeWhere(
      (msg) => msg['id'] != null && msg['id'].toString() == messageId,
    );
    messagesList.refresh();
  }
}
