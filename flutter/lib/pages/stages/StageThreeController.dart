import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class StageThreeController extends GetxController {
  var isOverlayLoading = false.obs;
  var isLoading = false.obs;
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  final secureStorage = const FlutterSecureStorage();
  var stepNo = "0".obs;
  var finishBtn = false.obs;
  Timer? timer;
  var secondsRemaining = 5.obs;
  var currentStep = 1.obs;

  RxBool isVehicleSkipped = false.obs;
  RxBool isLicenseSkipped = false.obs;

  /// Controls whether the vehicle step "Next/Add Vehicle" button is enabled.
  final isVehicleFormValid = false.obs;

  /// Controls whether the license step "Next/Add" button is enabled.
  final isLicenseFormValid = false.obs;

  void switchToDriverStep() {
    currentStep.value = 2;
  }

  void switchToVehicleStep() {
    currentStep.value = 1;
  }

  bool validateVehicleFields() {
    errors.clear();

    if (makeTextEditingController.text.isEmpty ||
        modelTextEditingController.text.isEmpty ||
        licenseNumberTextEditingController.text.isEmpty ||
        colorTextEditingController.text.isEmpty ||
        yearTextEditingController.text.isEmpty ||
        vehicleType.value == "" ||
        fuel.value == "") {
      // Add all validation errors as before
      if (makeTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['make_error'] ?? 'Make');
        var err = {
          'title': "make",
          'eList': [message ?? 'Make field is required']
        };
        errors.add(err);
      }
      if (modelTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['model_error'] ?? 'Model');
        var err = {
          'title': "model",
          'eList': [message ?? 'Model field is required']
        };
        errors.add(err);
      }
      if (vehicleType.value == "") {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['vehicle_type_error'] ?? 'Car');
        var err = {
          'title': "type",
          'eList': [message ?? 'Car type is required']
        };
        errors.add(err);
      }
      if (licenseNumberTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['license_error'] ?? 'License number');
        var err = {
          'title': "liscense_no",
          'eList': [message ?? 'License number field is required']
        };
        errors.add(err);
      }
      if (colorTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['color_error'] ?? 'Color');
        var err = {
          'title': "color",
          'eList': [message ?? 'Color field is required']
        };
        errors.add(err);
      }
      if (yearTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['year_error'] ?? 'Year');
        var err = {
          'title': "year",
          'eList': [message ?? 'Year field is required']
        };
        errors.add(err);
      }
      if (fuel.value == "") {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['fuel_error'] ?? 'Fuel type ');
        var err = {
          'title': "car_type",
          'eList': [message ?? 'Fuel type is required']
        };
        errors.add(err);
      }

      if (carImagePathOriginal.value != "") {
        final file = File(carImagePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10) {
          var message = validationMessageDetail['max.file'];
          message = message.replaceAll(
              ":attribute", labelTextDetail['photo_error'] ?? 'car image');
          message = message.replaceAll(":max", '10');
          serviceController.showDialogue(
              message ?? 'Can not upload image size greater than 10MB');
        }
      }

      return false;
    }

    // Validate car image size if provided
    if (carImagePathOriginal.value != "") {
      final file = File(carImagePathOriginal.value);
      int sizeInBytes = file.lengthSync();
      double sizeInMb = sizeInBytes / (1024 * 1024);
      if (sizeInMb > 10) {
        var message = validationMessageDetail['max.file'];
        message = message.replaceAll(
            ":attribute", labelTextDetail['photo_error'] ?? 'car image');
        message = message.replaceAll(":max", '10');
        serviceController.showDialogue(
            message ?? 'Can not upload image size greater than 10MB');
        return false;
      }
    }

    return true;
  }

  bool validateLicenseFields() {
    errors.clear();

    if (driverLicensePathOriginal.value.isEmpty) {
      var message = validationMessageDetail['required'];
      message = message.replaceAll(":Attribute",
          labelTextDetail['driver_license_error'] ?? 'Driver license');
      var err = {
        'title': "driver_license",
        'eList': [message ?? 'Driver license field is required']
      };
      errors.add(err);
      return false;
    }

    // Validate license image size
    if (driverLicensePathOriginal.value != "") {
      final file = File(driverLicensePathOriginal.value);
      int sizeInBytes = file.lengthSync();
      double sizeInMb = sizeInBytes / (1024 * 1024);
      if (sizeInMb > 10) {
        var message = validationMessageDetail['max.file'];
        message = message.replaceAll(":attribute",
            labelTextDetail['driver_license_error'] ?? 'driver license image');
        message = message.replaceAll(":max", '10');
        serviceController.showDialogue(
            message ?? 'Can not upload image size greater than 10MB');
        return false;
      }
    }

    return true;
  }

  submitFinalForm() async {
    logger.debug(
        "StageThreeController.submitFinalForm called with carImageName=${carImageName.value}, carImageNameOriginal=${carImageNameOriginal.value}, carImagePath=${carImagePath.value}, carImagePathOriginal=${carImagePathOriginal.value}, driverLicenseName=${driverLicenseName.value}, driverLicenseNameOriginal=${driverLicenseNameOriginal.value}, driverLicensePath=${driverLicensePath.value}, driverLicensePathOriginal=${driverLicensePathOriginal.value}, isVehicleSkipped=${isVehicleSkipped.value}, isLicenseSkipped=${isLicenseSkipped.value}");
    try {
      isOverlayLoading(true);
      logger
          .debug("StageThreeController.submitFinalForm overlay loading shown");

      StageProvider()
          .setStageThree(
        isVehicleSkipped.value ? '' : makeTextEditingController.text,
        isVehicleSkipped.value ? '' : modelTextEditingController.text,
        isVehicleSkipped.value ? '' : licenseNumberTextEditingController.text,
        isVehicleSkipped.value ? '' : colorTextEditingController.text,
        isVehicleSkipped.value ? '' : yearTextEditingController.text,
        isVehicleSkipped.value ? '' : vehicleType.value,
        isVehicleSkipped.value ? '' : fuel.value,
        isVehicleSkipped.value ? '' : carImagePath.value,
        isVehicleSkipped.value ? '' : carImageName.value,
        isVehicleSkipped.value ? '' : carImagePathOriginal.value,
        isVehicleSkipped.value ? '' : carImageNameOriginal.value,
        isLicenseSkipped.value ? '' : driverLicensePath.value,
        isLicenseSkipped.value ? '' : driverLicenseName.value,
        isLicenseSkipped.value ? '' : driverLicensePathOriginal.value,
        isLicenseSkipped.value ? '' : driverLicenseNameOriginal.value,
        isVehicleSkipped.value ? "0" : "1",
        isVehicleSkipped.value ? "1" : "0",
        isLicenseSkipped.value ? "1" : "0",
        serviceController.token,
        "0",
      )
          .then((resp) async {
        logger.debug(
            "StageThreeController.submitFinalForm received response: ${resp.toString()}");
        if (resp['status'] != null && resp['status'] == "Error") {
          logger.warning(
              "StageThreeController.submitFinalForm backend returned error status with message=${resp['message']}");
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp != null && resp['errors'] != null) {
          logger.warning(
              "StageThreeController.submitFinalForm backend returned validation errors: ${resp['errors']}");
          if (resp['errors']['image'] != null) {
            errorList.addAll(resp['errors']['image']);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          logger.info(
              "StageThreeController.submitFinalForm success, updating user step to 4");
          serviceController.loginUserDetail['step'] = "4";
          serviceController.loginUserDetail.refresh();
          serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));
          stepNo.value = serviceController.loginUserDetail['step'].toString();

          Get.toNamed('/stage_four');
        } else {
          logger.warning(
              "StageThreeController.submitFinalForm received unexpected response format: ${resp.toString()}");
        }
        isOverlayLoading(false);
        logger.debug(
            "StageThreeController.submitFinalForm overlay loading hidden after response");
      }, onError: (err) {
        isOverlayLoading(false);
        logger.error(
            "StageThreeController.submitFinalForm encountered onError: $err");
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      logger.error(
          "StageThreeController.submitFinalForm threw exception: $exception");
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

// 7. Add method to clear form data
  void clearFormData() {
    makeTextEditingController.text = "";
    modelTextEditingController.text = "";
    licenseNumberTextEditingController.text = "";
    colorTextEditingController.text = "";
    yearTextEditingController.text = "";
    vehicleType.value = "";
    fuel.value = "";
    carImagePath.value = "";
    carImageName.value = "";
    carImagePathOriginal.value = "";
    carImageNameOriginal.value = "";
    driverLicensePath.value = "";
    driverLicenseName.value = "";
    driverLicensePathOriginal.value = "";
    driverLicenseNameOriginal.value = "";
    isVehicleSkipped.value = false;
    isLicenseSkipped.value = false;
  }

  late TextEditingController makeTextEditingController,
      modelTextEditingController,
      licenseNumberTextEditingController,
      colorTextEditingController,
      yearTextEditingController;

  var vehicleType = "".obs;
  var fuel = "".obs;
  var carImageName = "".obs;
  var carImagePath = "".obs;
  var carImageNameOriginal = "".obs;
  var carImagePathOriginal = "".obs;

  var vehicleList = List.empty(growable: true).obs;
  var isVehicleSet = false.obs;

  var driverLicenseName = "".obs;
  var driverLicensePath = "".obs;
  var driverLicenseNameOriginal = "".obs;
  var driverLicensePathOriginal = "".obs;
  var imageType = 0.obs;
  var labelTextDetail = {}.obs;
  var step3MainHeading = "".obs;
  var step4MainHeading = "".obs;
  var vehicleTypeList = [].obs;
  var vehicleTypeLabelList = [].obs;
  var validationMessageDetail = {}.obs;

  @override
  void onInit() async {
    super.onInit();
    makeTextEditingController = TextEditingController();
    modelTextEditingController = TextEditingController();
    licenseNumberTextEditingController = TextEditingController();
    colorTextEditingController = TextEditingController();
    yearTextEditingController = TextEditingController();

    stepNo.value = serviceController.loginUserDetail['step'].toString();

    // Re‑validate vehicle form when any relevant field changes.
    makeTextEditingController.addListener(validateVehicleFormFields);
    modelTextEditingController.addListener(validateVehicleFormFields);
    licenseNumberTextEditingController.addListener(validateVehicleFormFields);
    colorTextEditingController.addListener(validateVehicleFormFields);
    yearTextEditingController.addListener(validateVehicleFormFields);
    ever(vehicleType, (_) => validateVehicleFormFields());
    ever(fuel, (_) => validateVehicleFormFields());

    // Re‑validate license form when the selected license image changes.
    ever(driverLicensePathOriginal, (_) => validateLicenseFormFields());

    // Initial validation in case data is pre‑filled.
    validateVehicleFormFields();
    validateLicenseFormFields();

    fuel.value = "Gas";

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
    clearFormData();
    makeTextEditingController.dispose();
    modelTextEditingController.dispose();
    licenseNumberTextEditingController.dispose();
    colorTextEditingController.dispose();
    yearTextEditingController.dispose();
  }

  /// Lightweight client‑side validation for vehicle fields to drive button state.
  /// This does not add errors or show dialogs; it only toggles `isVehicleFormValid`.
  void validateVehicleFormFields() {
    final hasMake = makeTextEditingController.text.trim().isNotEmpty;
    final hasModel = modelTextEditingController.text.trim().isNotEmpty;
    final hasLicenseNo =
        licenseNumberTextEditingController.text.trim().isNotEmpty;
    final hasColor = colorTextEditingController.text.trim().isNotEmpty;
    final hasYear = yearTextEditingController.text.trim().isNotEmpty;
    final hasVehicleType = vehicleType.value.trim().isNotEmpty;
    final hasFuel = fuel.value.trim().isNotEmpty;

    isVehicleFormValid.value = hasMake &&
        hasModel &&
        hasLicenseNo &&
        hasColor &&
        hasYear &&
        hasVehicleType &&
        hasFuel;
  }

  /// Lightweight client‑side validation for license fields to drive button state.
  /// This does not add errors or show dialogs; it only toggles `isLicenseFormValid`.
  void validateLicenseFormFields() {
    isLicenseFormValid.value =
        driverLicensePathOriginal.value.trim().isNotEmpty;
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
          serviceController.langId.value, step3Page, serviceController.token);

      logger.info(
          "Get Label Text Detail Response for Step 3: ${resp.toString()}");

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['step3Page'] != null) {
          logger.info("Step 3 Page: ${resp['data']['step3Page'].toString()}");
          labelTextDetail.addAll(resp['data']['step3Page'] ?? {});
          step3MainHeading.value = labelTextDetail['main_heading'] ??
              "Step 3 of 5 - Vehicle Information";
          step4MainHeading.value = "Step 4 of 5 - Your Driver's License";

          vehicleTypeLabelList.add(
              labelTextDetail['vehicle_type_convertible_text'] ??
                  "Convertable");
          vehicleTypeList
              .add(labelTextDetail['vehicle_type_convertible_value']);
          vehicleTypeLabelList
              .add(labelTextDetail['vehicle_type_coupe_text'] ?? "Coupe");
          vehicleTypeList.add(labelTextDetail['vehicle_type_coupe_value']);
          vehicleTypeLabelList.add(
              labelTextDetail['vehicle_type_hatchback_text'] ?? "Hatchback");
          vehicleTypeList.add(labelTextDetail['vehicle_type_hatchback_value']);
          vehicleTypeLabelList
              .add(labelTextDetail['vehicle_type_minivan_text'] ?? "Minivan");
          vehicleTypeList.add(labelTextDetail['vehicle_type_minivan_value']);
          vehicleTypeLabelList
              .add(labelTextDetail['vehicle_type_sedan_text'] ?? "Sedan");
          vehicleTypeList.add(labelTextDetail['vehicle_type_sedan_value']);
          vehicleTypeLabelList.add(
              labelTextDetail['vehicle_type_station_wagon_text'] ??
                  "Station wagon");
          vehicleTypeList
              .add(labelTextDetail['vehicle_type_station_wagon_value']);
          vehicleTypeLabelList
              .add(labelTextDetail['vehicle_type_suv_text'] ?? "SUV");
          vehicleTypeList.add(labelTextDetail['vehicle_type_suv_value']);
          vehicleTypeLabelList
              .add(labelTextDetail['vehicle_type_truck_text'] ?? "Truck");
          vehicleTypeList.add(labelTextDetail['vehicle_type_truck_value']);
          vehicleTypeLabelList
              .add(labelTextDetail['vehicle_type_van_text'] ?? "Van");
          vehicleTypeList.add(labelTextDetail['vehicle_type_van_value']);
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
    final croppedFile = await serviceController.imageCropper(imageSource);
    if (croppedFile != null) {
      if (stepNo.value == "3") {
        if (imageType.value == 1) {
          carImagePath.value = croppedFile.path;
          carImageName.value = croppedFile.path.split('/').last;
          carImagePathOriginal.value =
              serviceController.originalImagePath.value;
          serviceController.originalImagePath.value = "";
          carImageNameOriginal.value =
              serviceController.originalImageName.value;
          serviceController.originalImageName.value = "";
        } else if (imageType.value == 2) {
          driverLicensePath.value = croppedFile.path;
          driverLicenseName.value = croppedFile.path.split('/').last;
          driverLicensePathOriginal.value =
              serviceController.originalImagePath.value;
          serviceController.originalImagePath.value = "";
          driverLicenseNameOriginal.value =
              serviceController.originalImageName.value;
          serviceController.originalImageName.value = "";
        }
      }
      Get.back();
    }
  }

  updateFuelValue(value) async {
    if (value == "Electric car") {
      fuel.value = value;
    } else if (value == "Hybrid car") {
      fuel.value = value;
    } else if (value == "Gas") {
      fuel.value = value;
    } else {
      fuel.value = "";
    }
  }

  setStageThree(skip, skipVehicle, skipLicense) {
    if (skip == true) {
      try {
        StageProvider()
            .setStageThree('', '', '', '', '', '', '', '', '', '', '', '', '',
                '', '', '0', '1', '1', serviceController.token, "1")
            .then((resp) async {
          if (resp['status'] != null && resp['status'] == "Error") {
            serviceController.showDialogue(resp['message'].toString());
          } else if (resp['status'] != null && resp['status'] == "Success") {
            serviceController.loginUserDetail['step'] = "4";
            serviceController.loginUserDetail.refresh();
            serviceController.secureStorage.write(
                key: "userInfo",
                value: jsonEncode(serviceController.loginUserDetail));
            stepNo.value = serviceController.loginUserDetail['step'].toString();

            Get.toNamed('/stage_four');
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);

          serviceController.showDialogue(err.toString(), type: "error");
        });
      } catch (exception) {
        isOverlayLoading(false);
        serviceController.showDialogue(exception.toString(), type: "error");
      }
      return;
    }

    errors.clear();

    if (skipVehicle == false) {
      if (makeTextEditingController.text.isEmpty ||
          modelTextEditingController.text.isEmpty ||
          licenseNumberTextEditingController.text.isEmpty ||
          colorTextEditingController.text.isEmpty ||
          yearTextEditingController.text.isEmpty ||
          vehicleType.value == "" ||
          fuel.value == "") {
        if (makeTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['make_error'] ?? 'Make');
          var err = {
            'title': "make",
            'eList': [message ?? 'Make field is required']
          };
          errors.add(err);
        }
        if (modelTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['model_error'] ?? 'Model');
          var err = {
            'title': "model",
            'eList': [message ?? 'Model field is required']
          };
          errors.add(err);
        }
        if (vehicleType.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['vehicle_type_error'] ?? 'Car');
          var err = {
            'title': "type",
            'eList': [message ?? 'Car type is required']
          };
          errors.add(err);
        }
        if (licenseNumberTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute",
              labelTextDetail['license_error'] ?? 'License number');
          var err = {
            'title': "liscense_no",
            'eList': [message ?? 'License number field is required']
          };
          errors.add(err);
        }
        if (colorTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['color_error'] ?? 'Color');
          var err = {
            'title': "color",
            'eList': [message ?? 'Color field is required']
          };
          errors.add(err);
        }
        if (yearTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['year_error'] ?? 'Year');
          var err = {
            'title': "year",
            'eList': [message ?? 'Year field is required']
          };
          errors.add(err);
        }
        if (fuel.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['fuel_error'] ?? 'Fuel type ');
          var err = {
            'title': "car_type",
            'eList': [message ?? 'Fuel type is required']
          };
          errors.add(err);
        }

        if (carImagePathOriginal.value != "") {
          final file = File(carImagePathOriginal.value);
          int sizeInBytes = file.lengthSync();
          double sizeInMb = sizeInBytes / (1024 * 1024);
          if (sizeInMb > 10) {
            var message = validationMessageDetail['max.file'];
            message = message.replaceAll(
                ":attribute", labelTextDetail['photo_error'] ?? 'car image');
            message = message.replaceAll(":max", '10');
            serviceController.showDialogue(
                message ?? 'Can not upload image size greater than 10MB');
          }
        }
        return;
      }
    }

    if (skipLicense == false) {
      if (driverLicensePathOriginal.value.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute",
            labelTextDetail['driver_license_error'] ?? 'Driver license');
        var err = {
          'title': "driver_license",
          'eList': [message ?? 'Driver license field is required']
        };
        errors.add(err);
      }

      if (driverLicensePathOriginal.value != "") {
        final file = File(driverLicensePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10) {
          var message = validationMessageDetail['max.file'];
          message = message.replaceAll(
              ":attribute",
              labelTextDetail['driver_license_error'] ??
                  'driver license image');
          message = message.replaceAll(":max", '10');
          serviceController.showDialogue(
              message ?? 'Can not upload image size greater than 10MB');
          return;
        }
      }
    }
    try {
      logger.info(
          "The value is $carImageName $carImageNameOriginal $carImagePath $carImagePathOriginal");
      isOverlayLoading(true);
      StageProvider()
          .setStageThree(
        makeTextEditingController.text,
        modelTextEditingController.text,
        licenseNumberTextEditingController.text,
        colorTextEditingController.text,
        yearTextEditingController.text,
        vehicleType.value,
        fuel.value,
        carImagePath.value,
        carImageName.value,
        carImagePathOriginal.value,
        carImageNameOriginal.value,
        driverLicensePath.value,
        driverLicenseName.value,
        driverLicensePathOriginal.value,
        driverLicenseNameOriginal.value,
        skipVehicle == true ? "0" : "1",
        skipVehicle == true ? "1" : "0",
        skipLicense == true ? "1" : "0",
        serviceController.token,
        "0",
      )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp != null && resp['errors'] != null) {
          if (resp['errors']['image'] != null) {
            errorList.addAll(resp['errors']['image']);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.loginUserDetail['step'] = "4";
          serviceController.loginUserDetail.refresh();
          serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));
          stepNo.value = serviceController.loginUserDetail['step'].toString();

          // makeTextEditingController.text = "";
          // modelTextEditingController.text = "";
          // licenseNumberTextEditingController.text = "";
          // colorTextEditingController.text = "";
          // yearTextEditingController.text = "";
          // vehicleType.value = "";
          // fuel.value = "";
          // carImagePath.value = "";
          // carImageName.value = "";
          // carImagePathOriginal.value = "";
          // carImageNameOriginal.value = "";
          // driverLicensePath.value = "";
          // driverLicenseName.value = "";
          // driverLicensePathOriginal.value = "";
          // driverLicenseNameOriginal.value = "";

          Get.toNamed('/');
          // serviceController.showDialogue(resp['message'].toString());
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }
}
