import 'dart:async';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class DeepTripDetailProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getTripDetail(rideId, rideDetailId, token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$rideDetail?id=$rideId&lang_id=$langId&ride_detail_id=$rideDetailId",
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          });

      logger.info("Get Trip Detail Response: ${response.body.toString()}");

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
      logger.error("HTTP error in getTripDetail: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getTripDetail: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future updateBookingStatus(bookingId, status, token) async {
    try {
      var url = "";
      if (status == "accept") {
        url = "$baseUrl/$acceptBookingRequest?booking_id=$bookingId";
      } else {
        url = "$baseUrl/$rejectBookingRequest?booking_id=$bookingId";
      }

      logger.info("Update Booking Status URL: $url");

      final response = await getConnect.get(url, headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      });

      logger
          .info("Update Booking Status Response: ${response.body.toString()}");

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
      logger.error("HTTP error in updateBookingStatus: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in updateBookingStatus: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future enterCode(bookingId, amount, token) async {
    try {
      var data = {'_method': 'PUT', 'code': amount};

      logger.info("Enter Code - Booking ID: $bookingId, Code: $amount");

      final response = await getConnect.post(
          "$baseUrl/$securedCashCode?booking_id=$bookingId", data,
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          });

      logger.info("Enter Code Response: ${response.body.toString()}");

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
      logger.error("HTTP error in enterCode: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in enterCode: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future noShowDriverData(rideId, type, bookingId, userId, token) async {
    try {
      var data = {
        'ride_id': rideId,
        'type': type,
        'booking_id': bookingId,
        'user_id': userId,
      };

      logger.info("No Show Driver Data: ${data.toString()}");

      final response =
          await getConnect.post("$baseUrl/$noShow", data, headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      });

      logger.info("No Show Driver Data Response: ${response.body.toString()}");

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
      logger.error("HTTP error in noShowDriverData: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in noShowDriverData: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}