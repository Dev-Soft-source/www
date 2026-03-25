import 'dart:async';
import 'dart:io';
import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class MyTripProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Map<String, dynamic>? _asMap(dynamic value) {
    if (value is Map<String, dynamic>) {
      return value;
    }
    if (value is Map) {
      return Map<String, dynamic>.from(value);
    }
    return null;
  }

  Future getAllTrips(page, token, type, pageLimit, langId) async {
    try {
      var url = baseUrl;
      if (type == "upcoming") {
        url = "$url/$upComingTrips";
      } else if (type == "completed") {
        url = "$url/$completedTrips";
      } else {
        url = "$url/$cancelledTrips";
      }
      url = "$url?page=$page&lang_id=$langId&paginate_limit=$pageLimit";
      final response = await getConnect.get(url, headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': 'Bearer $token',
      });

      logger.info("Get All Trips Response: ${response.body}");

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
      logger.error("HTTP error in getAllTrips: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getAllTrips: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future getAllRides(page, token, type, pageLimit, langId) async {
    try {
      var url = baseUrl;
      if (type == "upcoming") {
        url = "$url/$upComingRides";
      } else if (type == "completed") {
        url = "$url/$completedRides";
      } else {
        url = "$url/$cancelledRides";
      }
      url = "$url?page=$page&lang_id=$langId&pageaginate_limit=$pageLimit";
      final response = await getConnect.get(url, headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': 'Bearer $token',
      });

      logger.info("Get All Rides URL: $url");
      logger.info("Get All Rides Response: ${response.body}");
      final responseBody = _asMap(response.body);
      final responseData = _asMap(responseBody?['data']);
      final rides = _asMap(responseData?['rides']);
      logger.info("Get All Rides Total: ${rides?['total'] ?? 0}");

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
      logger.error("HTTP error in getAllRides: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getAllRides: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future addReview(
      rideId,
      reviewTextEditingController,
      vehicleCondition,
      conscious,
      comfort,
      communication,
      attitude,
      hygiene,
      respect,
      safety,
      timeliness,
      token) async {
    try {
      final data = {
        'review': reviewTextEditingController,
        'vehicle_condition': vehicleCondition,
        'conscious': conscious,
        'comfort': comfort,
        'communication': communication,
        'attitude': attitude,
        'hygiene': hygiene,
        'respect': respect,
        'safety': safety,
        'timeliness': timeliness,
      };
      final response = await getConnect
          .post("$baseUrl/$addDriverReview?ride_id=$rideId", data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
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
      logger.error("HTTP error in addReview: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in addReview: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future cancelMyBooking(bookingId, reviewTextEditingController,
      tripCancelTextEditingController, token, pageType) async {
    try {
      var url = "";
      var data = {};

      if (pageType == "trip") {
        url = "$baseUrl/$cancelMyBookingPost?booking_id=$bookingId";
        data = {
          '_method': "PUT",
          'message': reviewTextEditingController,
          'cancel_seats': tripCancelTextEditingController,
        };
      }

      if (pageType == "ride") {
        url = "$baseUrl/$cancelRide?id=$bookingId";
        data = {
          '_method': "PUT",
          'message': reviewTextEditingController,
          'reason': tripCancelTextEditingController
        };
      }

      final response = await getConnect.post(url, data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      });

      logger.info("Cancel My Booking Response: ${response.body}");

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

  Future storePassengerReview(
      bookingId,
      reviewTextEditingController,
      conscious,
      comfort,
      communication,
      attitude,
      hygiene,
      respect,
      safety,
      timeliness,
      token) async {
    try {
      final data = {
        'review': reviewTextEditingController,
        'conscious': conscious,
        'comfort': comfort,
        'communication': communication,
        'attitude': attitude,
        'hygiene': hygiene,
        'respect': respect,
        'safety': safety,
        'timeliness': timeliness,
      };

      final response = await getConnect.post(
          "$baseUrl/$storePassengerReviewed?booking_id=$bookingId", data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
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
      logger.error("HTTP error in storePassengerReview: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in storePassengerReview: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future removePassengerFromRide(
      bookingId,
      removePassengerType,
      reviewTextEditingController,
      tripCancelTextEditingController,
      blockDaysTextEditingController,
      removeType,
      token) async {
    try {
      var data = {
        '_method': "PUT",
        'removed_permanently': removePassengerType,
        'admin_message': reviewTextEditingController,
        'block_day': blockDaysTextEditingController,
        'remove_type': removeType,
        'passenger_message': tripCancelTextEditingController,
      };

      final response = await getConnect.post(
          "$baseUrl/$removePassenger?booking_id=$bookingId", data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          });
      logger.info("Remove Passenger Response: ${response.body}");
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
      logger.error("HTTP error in removePassengerFromRide: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in removePassengerFromRide: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
