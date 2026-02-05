import 'dart:async';
import 'dart:io';

import 'package:get/get.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class SearchRideProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getLabelTextDetail(langId) async {
    try {
      var url = "$baseUrl/$findRidePage";
      if (langId != 0) {
        url = "$url?lang_id=$langId";
      }

      logger.info("Get Label Text Detail URL: $url");

      final response = await getConnect.get(url, headers: {
        'Accept': 'application/json',
      });

      logger
          .info("Get Label Text Detail Response: ${response.body.toString()}");

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
      logger.error("HTTP error in getLabelTextDetail: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getLabelTextDetail: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future checkBooking(rideId, token) async {
    try {
      logger.info("Check Booking - Ride ID: $rideId");

      final response = await getConnect.post(
        "$baseUrl/$checkIsAlreadyBooked",
        {'ride_id': rideId.toString()},
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
          'X-Requested-With': 'XMLHttpRequest',
        },
      );

      logger.info("Check Booking Response: ${response.body.toString()}");

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
      logger.error("HTTP error in checkBooking: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in checkBooking: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future getSearchRide(
      to,
      from,
      keyword,
      date,
      driverName,
      driverAge,
      driverRating,
      driverPhone,
      passengerRating,
      paymentMethod,
      vehicleType,
      features,
      luggage,
      smoking,
      pet,
      pinkRideCheck,
      extraCareCheck,
      pageLimit,
      page,
      token) async {
    try {
      final data = FormData({});
      data.fields.add(MapEntry("to", to));
      data.fields.add(MapEntry("from", from));
      data.fields.add(MapEntry("keyword", keyword));
      data.fields.add(MapEntry("date", date));
      data.fields.add(MapEntry("driver_name", driverName));
      data.fields.add(MapEntry("driver_age", driverAge.toString()));
      data.fields.add(MapEntry("driver_rating", driverRating.toString()));
      data.fields.add(MapEntry("driver_phone", driverPhone.toString()));
      data.fields.add(MapEntry("passenger_rating", passengerRating.toString()));
      data.fields.add(MapEntry("payment_method", paymentMethod.toString()));
      data.fields.add(MapEntry("vehicle_type", vehicleType.toString()));
      data.fields.add(MapEntry("features", features.toString()));
      data.fields.add(MapEntry("luggage", luggage.toString()));
      data.fields.add(MapEntry("smoking", smoking.toString()));
      data.fields.add(MapEntry("pet", pet.toString()));
      data.fields.add(MapEntry("pink_ride", pinkRideCheck == true ? "1" : "0"));
      data.fields
          .add(MapEntry("extra_care", extraCareCheck == true ? "1" : "0"));
      data.fields.add(MapEntry("limit", pageLimit.toString()));
      data.fields.add(MapEntry("page", page.toString()));

      logger.info("Search Ride Data: ${data.fields.toString()}");

      final response = await getConnect.get(
          "$baseUrl/$searchRideDetail?${data.fields.toString()}",
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
          });

      logger.info("Search Ride Response: ${response.body.toString()}");

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
      logger.error("HTTP error in getSearchRide: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getSearchRide: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future getFindRidePreferenceOptions(token, langId) async {
    try {
      final response = await getConnect
          .get("$baseUrl/$findRideFeatureOptions?lang=$langId", headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      });

      logger.info(
          "Get Find Ride Preference Options Response: ${response.body.toString()}");

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
      logger.error("HTTP error in getFindRidePreferenceOptions: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getFindRidePreferenceOptions: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
