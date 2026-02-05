import 'dart:async';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class MessagingProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getMessages(token, id, rideId, type) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$messages?user_id=$id&ride_id=$rideId&type=$type",
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          });

      logger.info("Get Messages Response: ${response.body.toString()}");

      if (response.status.hasError) {
        if (response.status.connectionError) {
          return Future.error({
            "type": "network",
            "message":
                "No internet connection. Please check your network and try again."
          });
        }
        if (response.status.code == 500) {
          return response.body;
        } else if (response.status.code == 422) {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      } else {
        return response.body;
      }
    } on SocketException {
      return Future.error({
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      });
    } on TimeoutException {
      return Future.error({
        "type": "network",
        "message": "Request timed out. Please try again."
      });
    } on GetHttpException catch (e) {
      logger.error("HTTP error in getMessages: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getMessages: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future sendNewMessage(token, rideId, receiverId, msg) async {
    try {
      var url = "$baseUrl/$sendMessage";
      final data = FormData({});

      logger.info("Ride Id: $rideId");
      logger.info("Receiver Id: $receiverId");
      logger.info("Message: $msg");

      data.fields.add(MapEntry("ride_id", rideId));
      data.fields.add(MapEntry("receiver_id", receiverId));
      data.fields.add(MapEntry("message", msg));

      final response = await getConnect.post(url, data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
      });

      logger.info("Send New Message Response: ${response.body.toString()}");

      if (response.status.hasError) {
        if (response.status.connectionError) {
          return Future.error({
            "type": "network",
            "message":
                "No internet connection. Please check your network and try again."
          });
        }
        if (response.status.code == 422) {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      } else {
        return response.body;
      }
    } on SocketException {
      return Future.error({
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      });
    } on TimeoutException {
      return Future.error({
        "type": "network",
        "message": "Request timed out. Please try again."
      });
    } on GetHttpException catch (e) {
      logger.error("HTTP error in sendNewMessage: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in sendNewMessage: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}