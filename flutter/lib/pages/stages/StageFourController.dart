import 'dart:async';
import 'dart:convert';
import 'dart:io';

import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/driver_license/DriverLicenseProvider.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class StageFourController extends GetxController {
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  final secureStorage = const FlutterSecureStorage();

  final isOverlayLoading = false.obs;
  final isLoading = false.obs;
  final errors = [].obs;
  final stepNo = "0".obs;
  final imageType = 0.obs;
  final isLicenseFormValid = false.obs;
  final isLicenseSkipped = false.obs;

  final labelTextDetail = {}.obs;
  final validationMessageDetail = {}.obs;
  final popupTextDetail = {}.obs;
  final step4MainHeading = "Step 4 of 5 - Driver's License".obs;

  final driverLicenseName = "".obs;
  final driverLicensePath = "".obs;
  final driverLicenseNameOriginal = "".obs;
  final driverLicensePathOriginal = "".obs;

  @override
  void onInit() async {
    super.onInit();
    stepNo.value = serviceController.loginUserDetail['step'].toString();
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
      validateLicenseFields(showError: false);

      errorStateManager.setSuccess();
      isLoading(false);
    } on SocketException {
      isLoading(false);
      errorStateManager.setError(
        "No internet connection. Please check your network and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } on TimeoutException {
      isLoading(false);
      errorStateManager.setError(
        "Request timed out. Please check your connection and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } catch (error) {
      isLoading(false);

      if (error is Map &&
          error.containsKey('type') &&
          error.containsKey('message')) {
        errorStateManager.setError(
          error["message"],
          _parseErrorType(error["type"]),
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

  Future<void> _getLanguages() async {
    try {
      final resp = await LoginProvider().getLanguages();

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['languages'] != null) {
          serviceController.languages.clear();
          serviceController.languages.addAll(resp['data']['languages']);

          if (serviceController.langId.value == 0) {
            final getDefaultLanguage = serviceController.languages
                .firstWhereOrNull((element) => element['is_default'] == "1");
            if (getDefaultLanguage != null) {
              serviceController.langId.value =
                  int.parse(getDefaultLanguage['id'].toString());
            }
          }

          final getLanguage = serviceController.languages.firstWhereOrNull(
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
          serviceController.langId.value, step4Page, serviceController.token);

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['step4Page'] != null) {
          labelTextDetail.addAll(resp['data']['step4Page']);
          step4MainHeading.value = labelTextDetail['main_heading'] ??
              "Step 4 of 5 - Driver's License";
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }

        if (resp['data'] != null && resp['data']['messages'] != null) {
          popupTextDetail.addAll(resp['data']['messages']);
        }

        final getLanguage = serviceController.languages.firstWhereOrNull(
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
    final croppedFile = await serviceController.imageCropper(imageSource);
    if (croppedFile == null) {
      return;
    }

    driverLicensePath.value = croppedFile.path;
    driverLicenseName.value = croppedFile.path.split('/').last;
    driverLicensePathOriginal.value = serviceController.originalImagePath.value;
    serviceController.originalImagePath.value = "";
    driverLicenseNameOriginal.value = serviceController.originalImageName.value;
    serviceController.originalImageName.value = "";

    errors.removeWhere((element) => element['title'] == "driver_liscense");
    validateLicenseFields(showError: false);
    Get.back();
  }

  bool validateLicenseFields({bool showError = true}) {
    final hasLicense = driverLicensePathOriginal.value.trim().isNotEmpty;
    isLicenseFormValid.value = hasLicense;

    if (!showError) {
      return hasLicense;
    }

    errors.removeWhere((element) => element['title'] == "driver_liscense");
    if (!hasLicense) {
      var message = validationMessageDetail['required'];
      message = (message ?? ":Attribute is required").replaceAll(
        ":Attribute",
        labelTextDetail['driver_liscense_error'] ?? "Driver's License",
      );
      errors.add({
        'title': "driver_liscense",
        'eList': [message]
      });
    }

    return hasLicense;
  }

  Future<void> submitFinalForm() async {
    errors.clear();

    if (!isLicenseSkipped.value && !validateLicenseFields()) {
      return;
    }

    if (!isLicenseSkipped.value && driverLicensePathOriginal.value.isNotEmpty) {
      final file = File(driverLicensePathOriginal.value);
      final sizeInMb = file.lengthSync() / (1024 * 1024);
      if (sizeInMb > 10) {
        var message = validationMessageDetail['max.file'] ??
            validationMessageDetail['file'];
        message = (message ?? "Can not upload image size greater than :max MB")
            .replaceAll(
                ":attribute",
                labelTextDetail['driver_liscense_error'] ??
                    "driver license")
            .replaceAll(":Attribute",
                labelTextDetail['driver_liscense_error'] ?? "driver license")
            .replaceAll(":max", "10");
        errors.add({
          'title': "driver_liscense",
          'eList': [message]
        });
        return;
      }
    }

    try {
      isOverlayLoading(true);

      dynamic resp;

      if (isLicenseSkipped.value) {
        resp = {'status': 'Success'};
      } else {
        resp = await DriverLicenseProvider().updateDriverLicense(
          driverLicenseName.value,
          driverLicensePath.value,
          driverLicenseNameOriginal.value,
          driverLicensePathOriginal.value,
          serviceController.token,
          serviceController.loginUserDetail['id'],
        );
      }

      if (resp['status'] != null && resp['status'] == "Error") {
        serviceController.showDialogue(resp['message'].toString(),
            type: "error");
      } else if (resp['errors'] != null) {
        final licenseErrors = resp['errors']['driver_liscense'] ??
            resp['errors']['driver_license_original_upload'] ??
            resp['errors']['driver_liscense'];
        if (licenseErrors != null) {
          errors.add({
            'title': "driver_liscense",
            'eList': List<String>.from(licenseErrors)
          });
        }
      } else if (resp['status'] != null && resp['status'] == "Success") {
        if (!isLicenseSkipped.value && resp['data']?['user'] != null) {
          serviceController.loginUserDetail['driver_liscense'] =
              resp['data']['user']['driver_liscense']?.toString() ?? "";
        }
        serviceController.loginUserDetail.refresh();
        await secureStorage.write(
          key: "userInfo",
          value: jsonEncode(serviceController.loginUserDetail),
        );

        stepNo.value = serviceController.loginUserDetail['step'].toString();
        Get.toNamed('/stage_five');
      } else {
        serviceController.showDialogue(
          resp['message']?.toString() ?? "Unable to continue to the next step.",
          type: "error",
        );
      }
    } catch (error) {
      logger.error("Stage four submit failed: $error");
      serviceController.showDialogue(error.toString(), type: "error");
    } finally {
      isOverlayLoading(false);
    }
  }
}
