import 'dart:async';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class ProfilePhotoProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future profilePhotoUpdate(imageName, imagePath, imageNameOriginal,
      imagePathOriginal, token, userId) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      data.files.add(MapEntry(
          "image", MultipartFile(File(imagePath), filename: "$imageName")));
      data.files.add(MapEntry(
          "profile_original_image",
          MultipartFile(File(imagePathOriginal),
              filename: "$imageNameOriginal")));

      final response = await getConnect.post(
          "$baseUrl/$updateProfilePhoto?id=$userId", data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest'
      });
      logger.info("Profile Photo Update Response: ${response.body.toString()}");
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
