import 'dart:async';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class TripDetailProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getTripDetail(
      rideId, rideDetailId, token, langId, from, to, fromCityId, toCityId) async {
    try {
      final queryParams = <String, String>{
        'id': rideId.toString(),
        'lang_id': langId.toString(),
        'ride_detail_id': rideDetailId.toString(),
        'from': from.toString(),
        'to': to.toString(),
        'from_city_id': fromCityId.toString(),
        'to_city_id': toCityId.toString(),
      };
      final response = await getConnect.get(
          "$baseUrl/$rideDetail?${Uri(queryParameters: queryParams).query}",
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          });

      logger.info("Get Trip Detail Response:${queryParams} ${response.body.toString()}");

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

  Future checkPhoneNumber(langId, token) async {
    try {
      final response = await getConnect
          .get("$baseUrl/$userNumberCheck?langId=$langId", headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      });

      logger.info("Check Phone Number Response: ${response.body.toString()}");

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
      logger.error("HTTP error in checkPhoneNumber: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in checkPhoneNumber: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future cancelMyBooking(
    bookingId,
    token,
  ) async {
    try {
      var url = "";
      var data = {};
      url = "$baseUrl/$cancelRide?id=$bookingId";
      data = {
        '_method': "PUT",
      };

      final response = await getConnect.post(url, data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      });

      logger.info("Cancel My Booking Response: ${response.body.toString()}");

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
      logger.error("HTTP error in cancelMyBooking: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in cancelMyBooking: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
