import 'dart:async';
import 'dart:convert';
import 'dart:io';

import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/profile_photo/ProfilePhotoProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class ProfilePhotoController extends GetxController {
  final serviceController = Get.find<Service>();
  late ErrorStateManager errorStateManager;
  late ConnectivityService connectivityService;

  var isOverlayLoading = false.obs;
  var profileImagePath = "".obs;
  var profileImageName = "".obs;
  var profileImagePathOld = "".obs;
  var profileImagePathOriginal = "".obs;
  var profileImageNameOriginal = "".obs;
  var profileImagePathOriginalOld = "".obs;
  var errorList = List.empty(growable: true).obs;
  final secureStorage = const FlutterSecureStorage();
  var errors = [].obs;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  @override
  void onInit() {
    super.onInit();

    // Initialize ErrorStateManager
    errorStateManager = Get.put(ErrorStateManager());

    // Initialize ConnectivityService
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    profileImagePathOld.value =
        serviceController.loginUserDetail['profile_image'].toString();
    profileImagePathOriginalOld.value =
        serviceController.loginUserDetail['profile_original_image'].toString();

    loadInitialData();
  }

  @override
  void onClose() {
    super.onClose();
    serviceController.loginUserDetail.refresh();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // NO connectivity check here - relying on API exception handling

      await getLabelTextDetail();

      errorStateManager.setSuccess();
    } on SocketException {
      logger.error("Network error in loadInitialData: SocketException");
      errorStateManager.setError(
        "No internet connection. Please check your network and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } on TimeoutException {
      logger.error("Timeout error in loadInitialData");
      errorStateManager.setError(
        "Request timed out. Please check your connection and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } catch (error) {
      logger.error("Error in loadInitialData: $error");

      if (error is Map &&
          error.containsKey('type') &&
          error.containsKey('message')) {
        errorStateManager.setError(
          error["message"],
          _parseErrorType(error["type"]),
          loadInitialData,
        );
      } else if (error.toString().contains("SocketException") ||
          error.toString().contains("Network is unreachable") ||
          error.toString().contains("Connection refused")) {
        errorStateManager.setError(
          "No internet connection. Please check your network and try again.",
          ErrorType.network,
          loadInitialData,
        );
      } else {
        errorStateManager.setError(
          "Unable to load data. Please check your connection and try again.",
          ErrorType.unknown,
          loadInitialData,
        );
      }
    }
  }

  ErrorType _parseErrorType(String type) {
    switch (type) {
      case "network":
        return ErrorType.network;
      case "server":
        return ErrorType.server;
      default:
        return ErrorType.unknown;
    }
  }

  Future<void> getLabelTextDetail() async {
    await StageProvider()
        .getLabelTextDetail(serviceController.langId.value, profilePhotoSetting,
            serviceController.token)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['profilePhotoPage'] != null) {
          labelTextDetail.addAll(resp['data']['profilePhotoPage']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }

  uploadUserPhoto() async {
    if (profileImageName.value == "") {
      var message = validationMessageDetail['required'];
      message = message.replaceAll(
          ":Attribute", labelTextDetail['photo_error'] ?? 'Image');
      var err = {
        'title': "image",
        'eList': [message ?? 'Image is required']
      };
      errors.add(err);
      return;
    }

    final file = File(profileImagePathOriginal.value);
    int sizeInBytes = file.lengthSync();
    double sizeInMb = sizeInBytes / (1024 * 1024);
    if (sizeInMb > 10) {
      var message = validationMessageDetail['max'];
      message = message.replaceAll(
          ":attribute", labelTextDetail['photo_error'] ?? 'image');
      message = message.replaceAll(":max", '10');
      var err = {
        'title': "image",
        'eList': [message ?? 'Can not upload image size greater than 10MB']
      };
      errors.add(err);
      return;
    }

    // Connectivity check for user action
    if (!connectivityService.isConnected) {
      serviceController.showDialogue(
        "No internet connection. Please check your network and try again.",
        type: "error",
      );
      return;
    }

    try {
      errors.clear();

      isOverlayLoading(true);
      ProfilePhotoProvider()
          .profilePhotoUpdate(
              profileImageName.value,
              profileImagePath.value,
              profileImageNameOriginal.value,
              profileImagePathOriginal.value,
              serviceController.token,
              serviceController.loginUserDetail['id'])
          .then((resp) async {
        errorList.clear();

        if (resp['status'] != null && resp['status'] == "Error") {
          var err = {
            'title': "image",
            'eList': [resp['message'].toString()]
          };
          errors.add(err);
        } else if (resp != null && resp['errors'] != null) {
          if (resp['errors']['image'] != null) {
            errorList.addAll(resp['errors']['image']);
            var err = {'title': "image", 'eList': resp['errors']['image']};
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.loginUserDetail['profile_image'] =
              resp['data']['user']['profile_image'];
          serviceController.loginUserDetail['profile_original_image'] =
              resp['data']['user']['profile_original_image'];
          serviceController.loginUserDetail.refresh();
          secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));
          Get.back();
          serviceController.showDialogue(
              "Upload successful! Your profile photo is now live.",
              type: "success");
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        String message = err.toString();
        if (err is Map && err.containsKey('message')) {
          message = err['message'];
        }
        serviceController.showDialogue(message, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      String message = exception.toString();
      if (exception is Map && exception.containsKey('message')) {
        message = exception['message'];
      }
      serviceController.showDialogue(message, type: "error");
    }
  }

  void getImage(ImageSource imageSource) async {
    final croppedFile = await serviceController.imageCropper(imageSource);

    if (croppedFile != null) {
      profileImagePath.value = croppedFile.path;
      profileImageName.value = croppedFile.path.split('/').last;
      profileImagePathOriginal.value =
          serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      profileImageNameOriginal.value =
          serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
      Get.back();
    }
  }
}
