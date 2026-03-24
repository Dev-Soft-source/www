import 'dart:async';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class StageProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 1200));

  Future getLabelTextDetail(langId, stageUrl, token) async {
    try {
      var url = "$baseUrl/$stageUrl";
      if (langId != 0) {
        url = "$url?lang_id=$langId";
      }
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
    } catch (exception) {
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future setStageOne(
    token,
    firstName,
    lastName,
    gender,
    dob,
    country,
    String? state,
    String? city,
    postalCode,
    mini,
  ) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      data.fields.add(MapEntry("first_name", firstName));
      data.fields.add(MapEntry("last_name", lastName));
      data.fields.add(MapEntry("gender", gender));
      data.fields.add(MapEntry("dob", dob));
      data.fields.add(MapEntry("country", country));
      data.fields.add(MapEntry("state", state ?? "null"));
      data.fields.add(MapEntry("city", city ?? "null"));
      data.fields.add(MapEntry("zipcode", postalCode));
      data.fields.add(MapEntry('about', mini));

      logger.info("StageOne Data: ${data.fields.toString()}");
      logger.info("StageOne Data: ${data.files.toString()}");
      logger.info("StageOne URL: $baseUrl/$step1");
      logger.info("StageOne Token: $token");
      logger.info(
          "StageOne Headers: {'Authorization': 'Bearer $token', 'X-Requested-With': 'XMLHttpRequest'}");

      final response = await getConnect.post("$baseUrl/$step1", data, headers: {
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
    } catch (exception) {
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future setStageTwo(imageName, imagePath, imageNameOriginal, imagePathOriginal,
      token, skip) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "POST"));
      if (skip == "0") {
        data.files.add(MapEntry(
            "image", MultipartFile(File(imagePath), filename: "$imageName")));
        data.files.add(MapEntry("profile_original_image",
            MultipartFile(File(imagePathOriginal), filename: "$imageNameOriginal")));
      }
      data.fields.add(MapEntry("skip", skip));

      final response = await getConnect.post("$baseUrl/$step2", data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest'
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
    } catch (exception) {
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future setStageThree(
      make,
      model,
      licenseNumber,
      color,
      year,
      vehicleType,
      fuel,
      carImagePath,
      carImageName,
      carImagePathOriginal,
      carImageNameOriginal,
      licenseImagePath,
      licenseImageName,
      licenseImagePathOriginal,
      licenseImageNameOriginal,
      primaryVehicle,
      skipVehicle,
      skipLicense,
      token,
      skip) async {
    logger.debug(
        "StageProvider.setStageThree called with skip=$skip, skipVehicle=$skipVehicle, skipLicense=$skipLicense, make=$make, model=$model, vehicleType=$vehicleType, licenseNumber=$licenseNumber, color=$color, year=$year, fuel=$fuel, carImageName=$carImageName, carImageNameOriginal=$carImageNameOriginal, carImagePath=$carImagePath, carImagePathOriginal=$carImagePathOriginal, licenseImageName=$licenseImageName, licenseImageNameOriginal=$licenseImageNameOriginal, licenseImagePath=$licenseImagePath, licenseImagePathOriginal=$licenseImagePathOriginal");
    try {
      var url = "";
      url = "$baseUrl/$step3";
      logger.debug("StageProvider.setStageThree url resolved to $url");

      final data = FormData({});
      data.fields.add(const MapEntry("_method", "POST"));

      if (skip == "0" && skipVehicle == "0") {
        logger.debug(
            "StageProvider.setStageThree adding vehicle details because skip=0 and skipVehicle=0");
        data.fields.add(MapEntry("make", make));
        data.fields.add(MapEntry("model", model));
        data.fields.add(MapEntry("type", vehicleType));
        data.fields.add(MapEntry("license_no", licenseNumber));
        data.fields.add(MapEntry("color", color.toString()));
        data.fields.add(MapEntry("year", year));
        data.fields.add(MapEntry("car_type", fuel.toString()));
        data.fields.add(MapEntry("primary_vehicle", primaryVehicle));
        if (carImageName != "") {
          logger.debug(
              "StageProvider.setStageThree attaching car image files carImageName=$carImageName, carImageNameOriginal=$carImageNameOriginal");
          data.files.add(MapEntry("image",
              MultipartFile(File(carImagePath), filename: "$carImageName")));
          data.files.add(MapEntry(
              "original_image",
              MultipartFile(File(carImagePathOriginal),
                  filename: "$carImageNameOriginal")));
        }
      }

      if (skip == "0" && skipLicense == "0") {
        logger.debug(
            "StageProvider.setStageThree adding license details because skip=0 and skipLicense=0");
        if (licenseImageName != "") {
          logger.debug(
              "StageProvider.setStageThree attaching license image files licenseImageName=$licenseImageName, licenseImageNameOriginal=$licenseImageNameOriginal");
          data.files.add(MapEntry(
              "driver_liscense",
              MultipartFile(File(licenseImagePath),
                  filename: "$licenseImageName")));
          data.files.add(MapEntry(
              "driver_license_original_upload",
              MultipartFile(File(licenseImagePathOriginal),
                  filename: "$licenseImageNameOriginal")));
        }
      }

      data.fields.add(MapEntry("skip", skip));
      data.fields.add(MapEntry("skip_vehicle", skipVehicle));
      data.fields.add(MapEntry("skip_license", skipLicense));

      logger.debug(
          "StageProvider.setStageThree sending POST request with prepared FormData");
      final response = await getConnect.post(url, data, headers: {
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
        logger.warning(
            "StageProvider.setStageThree request failed with status=${response.status.code}, message=${response.statusText}");
        if (response.status.code == 422) {
          logger.warning(
              "StageProvider.setStageThree received validation errors: ${response.body}");
          return response.body;
        }
        return Future.error(response.statusText as Object);
      } else {
        logger.debug(
            "StageProvider.setStageThree request succeeded: ${response.body}");
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
    } catch (exception) {
      logger.error(
          "StageProvider.setStageThree encountered an exception: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future setStageFour(number, token, skip) async {
    try {
      var url = "$baseUrl/$savePhoneNumber";
      final data = FormData({});
      if (skip == "0") {
        data.fields.add(MapEntry("phone", number));
      } else if (skip == "1") {
        data.fields.add(const MapEntry("phone", ""));
      }
      data.fields.add(const MapEntry("step", '5'));
      data.fields.add(MapEntry("skip", skip));

      final response = await getConnect.post(url, data, headers: {
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
    } catch (exception) {
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future setStageFourLicense(
    imageName,
    imagePath,
    imageNameOriginal,
    imagePathOriginal,
    token,
    skip,
  ) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "POST"));

      if (skip == "0") {
        data.files.add(MapEntry(
            "driver_liscense",
            MultipartFile(File(imagePath), filename: "$imageName")));
        data.files.add(MapEntry(
            "driver_license_original_upload",
            MultipartFile(File(imagePathOriginal),
                filename: "$imageNameOriginal")));
      }

      data.fields.add(const MapEntry("skip", "0"));
      data.fields.add(const MapEntry("skip_vehicle", "1"));
      data.fields.add(MapEntry("skip_license", skip));

      final response = await getConnect.post("$baseUrl/$step3", data, headers: {
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
    } catch (exception) {
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future updateLanguageId(token, langId) async {
    try {
      final data = FormData({});
      data.fields.add(MapEntry("lang_id", langId.toString()));

      final response =
          await getConnect.post("$baseUrl/$updateUserLanguage", data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
      });
      logger.info("Update Language ID Response: ${response.body.toString()}");
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
    } catch (exception) {
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future logoutAdminDeActiveAccount(token) async {
    try {
      var url = "$baseUrl/$logoutUserAccount";
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
    } catch (exception) {
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
