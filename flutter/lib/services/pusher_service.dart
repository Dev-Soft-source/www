import 'dart:convert';
import 'package:get/get.dart';
import 'package:http/http.dart' as http;
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';
import 'package:pusher_channels_flutter/pusher_channels_flutter.dart';

class PusherService {
  static const String _appKey = '50200df60c6c597e3e7d';
  static const String _cluster = 'ap2';
  // Note: Changed to /api/broadcasting/auth based on your backend setup
  static final String _authEndpoint = '$url/api/broadcasting/auth';

  PusherChannelsFlutter? _pusher;
  bool _isInitialized = false;

  Future<void> initialize() async {
    if (_isInitialized) return;

    _pusher = PusherChannelsFlutter.getInstance();

    final serviceController = Get.find<Service>();

    final headers = <String, String>{
      'Accept': 'application/json',
      'Authorization': 'Bearer ${serviceController.token}',
      'Content-Type': 'application/x-www-form-urlencoded',
    };

    logger.info('headers: ${headers.toString()}');

    await _pusher!.init(
      apiKey: _appKey,
      cluster: _cluster,
      authEndpoint: _authEndpoint,
      authParams: {'headers': headers},
      onConnectionStateChange: (currentState, previousState) {
        logger.info('Pusher Connection: $currentState');
      },
      onError: (message, code, error) {
        logger.error('Pusher Error: $message');
      },
      onAuthorizer: (channelName, socketId, options) async {
        logger.info('=== PUSHER AUTHORIZATION ===');
        logger.info('Channel Name: $channelName');
        logger.info('Socket ID: $socketId');

        // ignore: prefer_typing_uninitialized_variables
        var json;
        try {
          var authUrl = '$url/api/broadcasting/auth';
          logger.info('Auth URL: $authUrl');
          logger.info(
              'Request Body: socket_id=$socketId&channel_name=$channelName');

          var result = await http.post(
            Uri.parse(authUrl),
            headers: headers,
            body: 'socket_id=$socketId&channel_name=$channelName',
          );

          logger.info("Auth Response Status: ${result.statusCode}");
          logger.info("Auth Response Body: ${result.body}");

          if (result.statusCode != 200) {
            logger.error("Auth failed with status ${result.statusCode}");
            return {};
          }

          try {
            json = jsonDecode(result.body);
            logger.info("Parsed Auth Response: $json");
          } catch (e) {
            logger.error("Failed to parse auth response: $e");
            return {};
          }

          return json;
        } catch (e) {
          logger.error("Authorization Error: ${e.toString()}");
          return {};
        }
      },
    );
    await _pusher!.connect();
    _isInitialized = true;
  }

  Future<void> subscribeToChannel(
      String channelName, Function(dynamic) onMessageReceived) async {
    if (!_isInitialized) {
      await initialize();
    }

    final myChannel = await _pusher!.subscribe(
      channelName: channelName,
      onEvent: (event) {
        logger.info(
            "New event data received: ${event.eventName} - ${event.data}");

        onMessageReceived(event.data);
      },
    );

    logger.info("Subscribed to channel: ${myChannel.channelName}");
  }

  // Subscribe to user's chat channel
  Future<void> subscribeToChatChannel(
      int userId, Function(dynamic) onNewMessage) async {
    // IMPORTANT: Private channels MUST start with 'private-' prefix
    // If your backend uses private channels, use: 'private-chat.$userId'
    // If your backend uses public channels, use: 'chat.$userId'
    String channelName = 'chat.$userId';

    logger.info('=== SUBSCRIBING TO CHAT CHANNEL ===');
    logger.info('Channel Name: $channelName');
    logger.info('User ID: $userId');

    await subscribeToChannel(channelName, (data) {
      logger.info('=== NEW CHAT MESSAGE RECEIVED ===');
      logger.info('Message Data: $data');
      onNewMessage(data);
    });
  }

  Future<void> unsubscribeFromChannel(String channelName) async {
    if (_pusher != null) {
      await _pusher!.unsubscribe(channelName: channelName);
      logger.info('Unsubscribed from: $channelName');
    }
  }

  Future<void> disconnect() async {
    if (_pusher == null || !_isInitialized) {
      _isInitialized = false;
      return;
    }

    try {
      await _pusher!.disconnect();
    } catch (e) {
      logger.warning('Pusher disconnect skipped/failed safely: $e');
    } finally {
      _isInitialized = false;
    }
  }
}
