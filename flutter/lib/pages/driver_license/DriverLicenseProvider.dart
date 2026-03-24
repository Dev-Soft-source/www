import 'dart:async';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class DriverLicenseProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getDriverLicense(token, langId) async {
    try {
      final response = await getConnect
          .get("$baseUrl/$getVerifyDriverLicense?lang_id=$langId", headers: {
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
      logger.error("HTTP error in getDriverLicense: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getDriverLicense: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future updateDriverLicense(imageName, imagePath, imageNameOriginal,
      imagePathOriginal, token, userId) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      data.fields.add(MapEntry("id", userId.toString()));
      if (imageName != "") {
        data.files.add(MapEntry("driver_liscense",
            MultipartFile(File(imagePath), filename: "$imageName")));
        data.files.add(MapEntry(
            "driver_license_original_upload",
            MultipartFile(File(imagePathOriginal),
                filename: "$imageNameOriginal")));
      }
      final response = await getConnect
          .post("$baseUrl/$driverLicenseUpdate?id=$userId", data, headers: {
        'X-Requested-With': 'XMLHttpRequest',
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
      logger.error("HTTP error in updateDriverLicense: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in updateDriverLicense: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future removeDriverLicense(token) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$removeLicense", headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
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
      logger.error("HTTP error in removeDriverLicense: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in removeDriverLicense: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
