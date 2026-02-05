import 'dart:async';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class LocationProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getCountries(token) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$getCountriesList", headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      });

      debugPrint("Base Url: $baseUrl");
      logger.info("Get Countries Response: ${response.body.toString()}");

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
      logger.error("HTTP error in getCountries: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getCountries: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  // Note: getStates and getCities are already implemented in EditProfileProvider
  // which is used by LocationController, so they don't need to be duplicated here
}