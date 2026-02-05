import 'dart:async';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class MyVehicleProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getVehicleList(page, token, langId) async {
    try {
      final response = await getConnect
          .get("$baseUrl/$myVehicleList?lang_id=$langId", headers: {
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
        return Future.error({
          'type': 'server',
          'message': response.statusText ?? 'Server error occurred'
        });
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
      if (exception is SocketException ||
          exception.toString().contains("SocketException")) {
        return Future.error({
          'type': 'network',
          'message':
              'No internet connection. Please check your network and try again.'
        });
      }
      return Future.error({'type': 'unknown', 'message': exception.toString()});
    }
  }

  Future addNewVehicle(
      make,
      model,
      licenseNumber,
      color,
      year,
      vehicleType,
      fuel,
      imagePath,
      imageName,
      imagePathOriginal,
      imageNameOriginal,
      vehicleId,
      token,
      removeImage,
      {primary = "0"}) async {
    try {
      var url = "";
      if (vehicleId == 0) {
        url = "$baseUrl/$vehicleStore";
      } else {
        url = "$baseUrl/$vehicleUpdate?id=$vehicleId";
      }

      final data = FormData({});
      if (vehicleId != 0) {
        data.fields.add(const MapEntry("_method", "PUT"));
      }
      data.fields.add(MapEntry("primary_vehicle", primary));
      data.fields.add(MapEntry("make", make));
      data.fields.add(MapEntry("model", model));
      data.fields.add(MapEntry("type", vehicleType));
      data.fields.add(MapEntry("liscense_no", licenseNumber));
      data.fields.add(MapEntry("color", color.toString()));
      data.fields.add(MapEntry("year", year));
      data.fields.add(MapEntry("car_type", fuel.toString()));
      data.fields.add(MapEntry('remove_image', removeImage.toString()));
      if (imageName != "") {
        data.files.add(MapEntry(
            "image", MultipartFile(File(imagePath), filename: "$imageName")));
        data.files.add(MapEntry(
            "original_image",
            MultipartFile(File(imagePathOriginal),
                filename: "$imageNameOriginal")));
      }
      final response = await getConnect.post(url, data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
      });

      logger.info("Add New Vehicle Response: ${response.body.toString()}");

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
        return Future.error({
          'type': 'server',
          'message': response.statusText ?? 'Server error occurred'
        });
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
      if (exception is SocketException ||
          exception.toString().contains("SocketException")) {
        return Future.error({
          'type': 'network',
          'message':
              'No internet connection. Please check your network and try again.'
        });
      }
      return Future.error({'type': 'unknown', 'message': exception.toString()});
    }
  }

  Future getVehicleInfo(vehicleId, token) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$editVehicle?id=$vehicleId", headers: {
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
        return Future.error({
          'type': 'server',
          'message': response.statusText ?? 'Server error occurred'
        });
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
      if (exception is SocketException ||
          exception.toString().contains("SocketException")) {
        return Future.error({
          'type': 'network',
          'message':
              'No internet connection. Please check your network and try again.'
        });
      }
      return Future.error({'type': 'unknown', 'message': exception.toString()});
    }
  }

  Future removeVehicle(vehicleId, token) async {
    try {
      final response = await getConnect
          .delete("$baseUrl/$deleteVehicle?id=$vehicleId", headers: {
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
        return Future.error({
          'type': 'server',
          'message': response.statusText ?? 'Server error occurred'
        });
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
      if (exception is SocketException ||
          exception.toString().contains("SocketException")) {
        return Future.error({
          'type': 'network',
          'message':
              'No internet connection. Please check your network and try again.'
        });
      }
      return Future.error({'type': 'unknown', 'message': exception.toString()});
    }
  }
}
