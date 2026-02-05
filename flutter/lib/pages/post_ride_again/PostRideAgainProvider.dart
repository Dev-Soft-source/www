import 'dart:async';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class PostRideAgainProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getPostRideList(token, type, page, limit, langId) async {
    try {
      var url = baseUrl;
      if (type == "upcoming") {
        url = "$url/$getPostRideUpcomingList";
      } else if (type == "completed") {
        url = "$url/$getPostRideCompletedList";
      } else if (type == "cancelled") {
        url = "$url/$getPostRideCancelledList";
      }
      url = "$url?paginate_limit=$limit&page=$page&lang_id=$langId";

      logger.info("Get Post Ride List URL: $url");

      final response = await getConnect.get(url, headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      });

      logger.info(
          "Get Post Ride List ($type) Response: ${response.body.toString()}");

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
      logger.error("HTTP error in getPostRideList ($type): $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getPostRideList ($type): $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
