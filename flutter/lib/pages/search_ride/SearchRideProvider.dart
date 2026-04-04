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
      final url =
          "$baseUrl/$checkIsAlreadyBooked?ride_id=${Uri.encodeQueryComponent(rideId.toString())}";

      final response = await getConnect.get(
        url,
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
      toCityId,
      fromCityId,
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
      final queryParams = <String, String>{
        "to": to.toString(),
        "from": from.toString(),
        "to_city_id": toCityId.toString(),
        "from_city_id": fromCityId.toString(),
        "keyword": keyword.toString(),
        "date": date.toString(),
        "driver_name": driverName.toString(),
        "driver_age": driverAge.toString(),
        "driver_rating": driverRating.toString(),
        "driver_phone": driverPhone.toString(),
        "passenger_rating": passengerRating.toString(),
        "payment_method": paymentMethod.toString(),
        "vehicle_type": vehicleType.toString(),
        "features": features.toString(),
        "luggage": luggage.toString(),
        "smoking": smoking.toString(),
        "pet": pet.toString(),
        "pink_ride": pinkRideCheck == true ? "1" : "0",
        "extra_care": extraCareCheck == true ? "1" : "0",
        "limit": pageLimit.toString(),
        "page": page.toString(),
      };

      final encodedQuery = Uri(queryParameters: queryParams).query;
      final url = "$baseUrl/$searchRideDetail?$encodedQuery";

      logger.info("Search Ride Data: ${queryParams.toString()}");
      logger.info("Search Ride URL: $url");

      final response = await getConnect.get(
          url,
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

  /// Initial search screen: find-ride labels + first search page + filter lists (one request).
  Future getSearchRideBootstrap(
    to,
    from,
    toCityId,
    fromCityId,
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
    token,
    langId,
  ) async {
    try {
      final queryParams = <String, String>{
        "to": to.toString(),
        "from": from.toString(),
        "to_city_id": toCityId.toString(),
        "from_city_id": fromCityId.toString(),
        "keyword": keyword.toString(),
        "date": date.toString(),
        "driver_name": driverName.toString(),
        "driver_age": driverAge.toString(),
        "driver_rating": driverRating.toString(),
        "driver_phone": driverPhone.toString(),
        "passenger_rating": passengerRating.toString(),
        "payment_method": paymentMethod.toString(),
        "vehicle_type": vehicleType.toString(),
        "features": features.toString(),
        "luggage": luggage.toString(),
        "smoking": smoking.toString(),
        "pet": pet.toString(),
        "pink_ride": pinkRideCheck == true ? "1" : "0",
        "extra_care": extraCareCheck == true ? "1" : "0",
        "limit": pageLimit.toString(),
        "page": page.toString(),
      };
      if (langId != 0) {
        queryParams["lang_id"] = langId.toString();
      }

      final encodedQuery = Uri(queryParameters: queryParams).query;
      final url = "$baseUrl/$searchRideBootstrap?$encodedQuery";

      logger.info("Search ride bootstrap URL: $url");

      final response = await getConnect.get(
        url,
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
          'X-Requested-With': 'XMLHttpRequest',
        },
      );

      logger.info(
          "Search ride bootstrap response: ${response.body.toString()}");

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
      logger.error("HTTP error in getSearchRideBootstrap: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getSearchRideBootstrap: $exception");
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
