import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class StageTowController extends GetxController {
  var isOverlayLoading = false.obs;
  var isLoading = false.obs;
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  final secureStorage = const FlutterSecureStorage();
  var stepNo = "0".obs;

  var profileImagePath = "".obs;
  var profileImageName = "".obs;
  var profileImagePathOriginal = "".obs;
  var profileImageNameOriginal = "".obs;

  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  /// Controls whether the "Next" button on Stage Two is enabled.
  final isStageTwoValid = false.obs;

  @override
  void onInit() async {
    super.onInit();

    stepNo.value = serviceController.loginUserDetail['step'].toString();

    // Re-validate whenever the selected profile image changes.
    ever(profileImageName, (_) => validateStageTwoFields());

    // Initial validation in case an image is already present.
    validateStageTwoFields();

    // Load initial data with error handling
    await loadInitialData();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();
      isLoading(true);

      if (serviceController.languages.isEmpty) {
        await _getLanguages();
      }
      await _getLabelTextDetail();

      errorStateManager.setSuccess();
      isLoading(false);
    } on SocketException {
      logger.error("Network error in loadInitialData: SocketException");
      isLoading(false);
      errorStateManager.setError(
        "No internet connection. Please check your network and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } on TimeoutException {
      logger.error("Timeout error in loadInitialData");
      isLoading(false);
      errorStateManager.setError(
        "Request timed out. Please check your connection and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } catch (error) {
      logger.error("Error in loadInitialData: $error");
      isLoading(false);

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
          "Unable to load page data. Please check your connection and try again.",
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

  @override
  void onClose() {
    super.onClose();
    profileImageName.value = "";
    profileImagePath.value = "";
    profileImageNameOriginal.value = "";
    profileImagePathOriginal.value = "";
  }

  /// Simple client-side validation for Stage Two.
  /// The Next button is enabled only when a profile photo has been selected.
  void validateStageTwoFields() {
    logger.info(profileImageName.value);
    logger.info(profileImageName.value.trim().isNotEmpty.toString());
    isStageTwoValid.value = profileImageName.value.trim().isNotEmpty;
  }

  Future<void> _getLanguages() async {
    try {
      final resp = await LoginProvider().getLanguages();

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['languages'] != null) {
          serviceController.languages.clear();
          serviceController.languages.addAll(resp['data']['languages']);

          if (serviceController.langId.value == 0) {
            var getDefaultLanguage = serviceController.languages
                .firstWhereOrNull((element) => element['is_default'] == "1");
            if (getDefaultLanguage != null) {
              serviceController.langId.value =
                  int.parse(getDefaultLanguage['id'].toString());
            }
          }

          var getLanguage = serviceController.languages.firstWhereOrNull(
              (element) => element['id'] == serviceController.langId.value);
          if (getLanguage != null) {
            serviceController.langIcon.value = getLanguage['flag_icon'];
            serviceController.lang.value = getLanguage['abbreviation'];
          }
        }
      } else {
        throw {
          "type": "server",
          "message": resp['message'] ?? "Failed to load languages."
        };
      }
    } on SocketException {
      throw {
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      };
    } on TimeoutException {
      throw {
        "type": "network",
        "message":
            "Request timed out. Please check your connection and try again."
      };
    } catch (error) {
      if (error is Map && error.containsKey('type')) {
        rethrow;
      }
      throw {
        "type": "unknown",
        "message": "Unable to load languages. Please try again."
      };
    }
  }

  Future<void> _getLabelTextDetail() async {
    try {
      final resp = await StageProvider().getLabelTextDetail(
          serviceController.langId.value, step2Page, serviceController.token);

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['step2Page'] != null) {
          labelTextDetail.addAll(resp['data']['step2Page']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }

        var getLanguage = serviceController.languages.firstWhereOrNull(
            (element) => element['id'] == serviceController.langId.value);
        if (getLanguage != null) {
          serviceController.langIcon.value = getLanguage['flag_icon'];
          serviceController.lang.value = getLanguage['abbreviation'];
        }
      } else {
        throw {
          "type": "server",
          "message": resp['message'] ?? "Failed to load page details."
        };
      }
    } on SocketException {
      throw {
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      };
    } on TimeoutException {
      throw {
        "type": "network",
        "message":
            "Request timed out. Please check your connection and try again."
      };
    } catch (error) {
      if (error is Map && error.containsKey('type')) {
        rethrow;
      }
      throw {
        "type": "unknown",
        "message": "Unable to load page details. Please try again."
      };
    }
  }

  void getImage(ImageSource imageSource) async {
    logger.info(imageSource.toString());
    final croppedFile = await serviceController.imageCropper(imageSource);

    // Previously this only updated the image when `stepNo == "2"`.
    // After skipping Stage 2 and moving to Stage 3, `stepNo` becomes "3",
    // so when you pop back to this screen the selected image was never
    // stored, and the widget kept receiving `null`.
    //
    // We always want to update the profile image when the user selects one
    // on this screen, regardless of the current backend step value.
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
    } else {
      logger.warning("Cropped file is null");
    }
  }

  setStageTwo(skip) async {
    errors.clear();

    if (profileImageName.value == "") {
      skip = true;
    }
    if (skip == false) {
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

      if (profileImagePathOriginal.value != "") {
        final file = File(profileImagePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10) {
          var message = validationMessageDetail['max.file'];
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
      }
    }
    try {
      isOverlayLoading(true);
      StageProvider()
          .setStageTwo(
        profileImageName.value,
        profileImagePath.value,
        profileImageNameOriginal.value,
        profileImagePathOriginal.value,
        serviceController.token,
        skip == true ? "1" : "0",
      )
          .then((resp) async {
        errorList.clear();
        // if (resp['message'] != null) {
        //   serviceController.showDialogue("${resp['message']}1");
        // }

        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue("${resp['message']}1", type: "error");
        } else if (resp != null && resp['errors'] != null) {
          serviceController.showDialogue("${resp['message']}", type: "error");
          if (resp['errors']['image'] != null) {
            var err = {'title': "image", 'eList': resp['errors']['image']};
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.loginUserDetail['profile_image'] =
              resp['data']['profile_image'];
          serviceController.loginUserDetail['profile_original_image'] =
              resp['data']['profile_original_image'].toString();
          serviceController.loginUserDetail['step'] = "3";
          serviceController.loginUserDetail.refresh();
          serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));
          stepNo.value = serviceController.loginUserDetail['step'].toString();

          // profileImageName.value = "";
          // profileImagePath.value = "";
          // profileImageNameOriginal.value = "";
          // profileImagePathOriginal.value = "";

          Get.toNamed('/stage_three_vehicle');
          if (skip == false) {
            // serviceController.showDialogue(resp['message'].toString());
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);

        // Parse structured error from provider
        if (err is Map && err.containsKey('message')) {
          serviceController.showDialogue(err['message'], type: "error");
        } else {
          serviceController.showDialogue(
              "Failed to save information. Please try again.",
              type: "error");
        }
      });
    } catch (exception) {
      isOverlayLoading(false);

      // Parse structured error from provider
      if (exception is Map && exception.containsKey('message')) {
        serviceController.showDialogue(exception['message'], type: "error");
      } else if (exception.toString().contains("SocketException") ||
          exception.toString().contains("Network is unreachable")) {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(
            "Failed to save information. Please try again.",
            type: "error");
      }
    }
  }
}
