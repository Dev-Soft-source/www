import 'dart:async';
import 'dart:io';
import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class MyReviewsProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getAllReviews(token, paginateLimit, page, reviewType, langId) async {
    try {
      // paginateLimit = 20;
      var url = baseUrl;
      if (reviewType == "received") {
        url = "$url/$reviewsReceived";
      } else {
        url = "$url/$reviewsLeft";
      }
      url = "$url?paginate_limit=$paginateLimit&page=$page&lang_id=$langId";

      final response = await getConnect.get(url, headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      });
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
        logger.info("Get All Reviews Response: ${response.body.toString()}");
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
      logger.error("HTTP error in getAllReviews: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getAllReviews: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future addReply(ratingId, reply, token) async {
    try {
      final data = FormData({});
      data.fields.add(MapEntry("rating_id", ratingId.toString()));
      data.fields.add(MapEntry("reply", reply.toString()));
      final response =
          await getConnect.post("$baseUrl/$reviewReply", data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
      });
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
      logger.error("HTTP error in addReply: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in addReply: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
