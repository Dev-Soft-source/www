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
  RxBool isVehicleSkipped = false.obs;

  /// Controls whether the vehicle step "Next/Add Vehicle" button is enabled.
  final isVehicleFormValid = false.obs;

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
          'title': "license_no",
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
    isVehicleSkipped.value = false;
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

  var imageType = 0.obs;
  var labelTextDetail = {}.obs;
  var step3MainHeading = "".obs;
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

    // Initial validation in case data is pre‑filled.
    validateVehicleFormFields();

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
          _populateVehicleTypes(
            details: labelTextDetail,
            vehicleTypeOptions: resp['data']['vehicleTypeOptions'],
          );
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

  void _populateVehicleTypes({
    required Map<dynamic, dynamic> details,
    dynamic vehicleTypeOptions,
  }) {
    vehicleTypeList.clear();
    vehicleTypeLabelList.clear();

    final normalizedVehicleTypeOptions = vehicleTypeOptions is List
        ? vehicleTypeOptions
        : vehicleTypeOptions is Map
            ? vehicleTypeOptions.values.toList()
            : vehicleTypeOptions is Iterable
                ? vehicleTypeOptions.toList()
                : const [];

    if (normalizedVehicleTypeOptions.isNotEmpty) {
      final seenValues = <String>{};

      for (final option in normalizedVehicleTypeOptions) {
        if (option is! Map) {
          continue;
        }

        final value = option['features_setting_id']?.toString() ??
            option['id']?.toString() ??
            "";
        final label = option['name']?.toString() ??
            option['label']?.toString() ??
            option['slug']?.toString() ??
            "";

        if (value.isEmpty || label.isEmpty || seenValues.contains(value)) {
          continue;
        }

        seenValues.add(value);
        vehicleTypeList.add(value);
        vehicleTypeLabelList.add(label);
      }

      if (vehicleType.value.isNotEmpty &&
          !vehicleTypeList.contains(vehicleType.value)) {
        vehicleType.value = "";
      }
      return;
    }

    final options = [
      {
        'label': details['vehicle_type_convertible_text'] ?? "Convertable",
        'value': details['vehicle_type_convertible_value'],
      },
      {
        'label': details['vehicle_type_coupe_text'] ?? "Coupe",
        'value': details['vehicle_type_coupe_value'],
      },
      {
        'label': details['vehicle_type_hatchback_text'] ?? "Hatchback",
        'value': details['vehicle_type_hatchback_value'],
      },
      {
        'label': details['vehicle_type_minivan_text'] ?? "Minivan",
        'value': details['vehicle_type_minivan_value'],
      },
      {
        'label': details['vehicle_type_sedan_text'] ?? "Sedan",
        'value': details['vehicle_type_sedan_value'],
      },
      {
        'label': details['vehicle_type_station_wagon_text'] ?? "Station wagon",
        'value': details['vehicle_type_station_wagon_value'],
      },
      {
        'label': details['vehicle_type_suv_text'] ?? "SUV",
        'value': details['vehicle_type_suv_value'],
      },
      {
        'label': details['vehicle_type_truck_text'] ?? "Truck",
        'value': details['vehicle_type_truck_value'],
      },
      {
        'label': details['vehicle_type_van_text'] ?? "Van",
        'value': details['vehicle_type_van_value'],
      },
    ];

    final seenValues = <String>{};
    for (final option in options) {
      final value = option['value']?.toString() ?? "";
      if (value.isEmpty || seenValues.contains(value)) {
        continue;
      }

      seenValues.add(value);
      vehicleTypeLabelList.add(option['label']?.toString() ?? "");
      vehicleTypeList.add(value);
    }

    if (vehicleType.value.isNotEmpty &&
        !vehicleTypeList.contains(vehicleType.value)) {
      vehicleType.value = "";
    }
  }

  void getImage(ImageSource imageSource) async {
    final croppedFile = await serviceController.imageCropper(imageSource);
    if (croppedFile != null) {
      carImagePath.value = croppedFile.path;
      carImageName.value = croppedFile.path.split('/').last;
      carImagePathOriginal.value = serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      carImageNameOriginal.value = serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
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

  setStageThree(skip, skipVehicle) {
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

            Get.toNamed('/stage_five');
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
            'title': "license_no",
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
        '',
        '',
        '',
        '',
        skipVehicle == true ? "0" : "1",
        skipVehicle == true ? "1" : "0",
        "1",
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
