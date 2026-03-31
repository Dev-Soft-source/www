import 'dart:async';
import 'dart:convert';
import 'dart:io';

import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/driver_license/DriverLicenseProvider.dart';
import 'package:proximaride_app/pages/profile_setting/ProfileSettingController.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class DriverLicenseController extends GetxController {
  var isOverlayLoading = false.obs;
  var isLoading = false.obs;
  final errorStateManager = ErrorStateManager();
  late final ConnectivityService connectivityService;
  final secureStorage = const FlutterSecureStorage();

  final serviceController = Get.find<Service>();

  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;

  var driverLicenseName = "".obs;
  var driverLicensePath = "".obs;
  var oldImagePath = "".obs;

  var driverLicenseNameOriginal = "".obs;
  var driverLicensePathOriginal = "".obs;
  var driverLicensePathOriginalOld = "".obs;
  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;
  var isFormDirty = false.obs;

  @override
  void onInit() async {
    super.onInit();

    if (Get.isRegistered<ProfileSettingController>()) {
      final profileSettingController = Get.find<ProfileSettingController>();
      final title =
          profileSettingController.labelTextDetail['my_driver_license_label'];
      if (title != null && title.toString().trim().isNotEmpty) {
        labelTextDetail['main_heading'] = title;
      }
    }

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    await loadInitialData();
  }

  @override
  void onClose() {
    super.onClose();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      await _getDriverLicense();

      errorStateManager.setSuccess();
      isLoading(false);
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
          "Unable to load driver license settings. Please check your connection and try again.",
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

  Future<void> _getDriverLicense() async {
    await DriverLicenseProvider()
        .getDriverLicense(
            serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['driverSettingPage'] != null) {
          labelTextDetail.addAll(resp['data']['driverSettingPage']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }

        if (resp['data'] != null && resp['data']['messages'] != null) {
          popupTextDetail.addAll(resp['data']['messages']);
        }

        oldImagePath.value = resp['data'] != null &&
                resp['data']['user'] != null &&
                resp['data']['user']['driver_liscense'] != null
            ? resp['data']['user']['driver_liscense']
            : "";
        driverLicensePathOriginalOld.value = resp['data'] != null &&
                resp['data']['user'] != null &&
                resp['data']['user']['driver_license_original_upload'] != null
            ? resp['data']['user']['driver_license_original_upload']
            : "";
      }
    }, onError: (err) {
      throw err; // Propagate to loadInitialData
    });
  }

  updateDriverLicense() async {
    try {
      if (driverLicenseName.value == "") {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute",
            labelTextDetail['driver_license_error'] ?? 'driver license');
        var err = {
          'title': "driver_liscense",
          'eList': [message ?? 'Click on image to upload new license']
        };
        errors.add(err);
        return;
      }

      final file = File(driverLicensePathOriginal.value);
      int sizeInBytes = file.lengthSync();
      double sizeInMb = sizeInBytes / (1024 * 1024);
      if (sizeInMb > 10) {
        var message = validationMessageDetail['file'];
        message = message.replaceAll(":attribute",
            labelTextDetail['driver_license_error'] ?? 'driver license');
        message = message.replaceAll(":max", '10');
        var err = {
          'title': "driver_liscense",
          'eList': [message ?? 'Can not upload image size greater than 10MB']
        };
        errors.add(err);
        return;
      }

      isOverlayLoading(true);
      DriverLicenseProvider()
          .updateDriverLicense(
              driverLicenseName.value,
              driverLicensePath.value,
              driverLicenseNameOriginal.value,
              driverLicensePathOriginal.value,
              serviceController.token,
              serviceController.loginUserDetail['id'])
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString(),
              type: "error");
        } else if (resp['errors'] != null) {
          if (resp['errors']['driver_liscense'] != null) {
            var err = {
              'title': "driver_liscense",
              'eList': resp['errors']['driver_liscense']
            };
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.loginUserDetail['driver_liscense'] =
              resp['data']['user']['driver_liscense'].toString();
          serviceController.loginUserDetail.refresh();
          secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));
          Get.back();
          serviceController.showDialogue(resp['message'].toString(),
              type: "success");
          isFormDirty.value = false;
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        if (err is Map && err.containsKey('type') && err['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(err.toString(), type: "error");
        }
      });
    } catch (exception) {
      isOverlayLoading(false);
      if (exception is Map &&
          exception.containsKey('type') &&
          exception['type'] == 'network') {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(exception.toString(), type: "error");
      }
    }
  }

  void getImage(ImageSource imageSource) async {
    final croppedFile = await serviceController.imageCropper(imageSource);

    if (croppedFile != null) {
      oldImagePath.value = "";
      driverLicensePath.value = croppedFile.path;
      driverLicenseName.value = croppedFile.path.split('/').last;
      driverLicensePathOriginal.value =
          serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      driverLicenseNameOriginal.value =
          serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
      Get.back();
    }
    isFormDirty.value = true;
  }

  removeDriverLicense() async {
    bool isConfirmed = await serviceController.showConfirmationDialog(
        cancelYesBtn: "Yes, remove it!",
        "${popupTextDetail['remove_driver_license_message'] ?? "Are you sure you want to remove the driver license"}");
    if (isConfirmed) {
      try {
        isOverlayLoading(true);
        DriverLicenseProvider()
            .removeDriverLicense(serviceController.token)
            .then((resp) async {
          if (resp['status'] != null && resp['status'] == "Error") {
            serviceController.showDialogue(resp['message'].toString(),
                type: "error");
          } else if (resp['status'] != null && resp['status'] == "Success") {
            serviceController.loginUserDetail['driver_liscense'] = "null";
            serviceController.loginUserDetail.refresh();
            secureStorage.write(
                key: "userInfo",
                value: jsonEncode(serviceController.loginUserDetail));
            serviceController.showDialogue(resp['message'].toString(),
                type: "success");

            oldImagePath.value = "";
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);
          if (err is Map &&
              err.containsKey('type') &&
              err['type'] == 'network') {
            serviceController.showDialogue(
                "No internet connection. Please check your network and try again.",
                type: "error");
          } else {
            serviceController.showDialogue(err.toString(), type: "error");
          }
        });
      } catch (exception) {
        isOverlayLoading(false);
        if (exception is Map &&
            exception.containsKey('type') &&
            exception['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(exception.toString(), type: "error");
        }
      }
    }
  }
}
