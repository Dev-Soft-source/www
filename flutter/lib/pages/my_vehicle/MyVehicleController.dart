import 'dart:async';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/my_vehicle/MyVehicleProvider.dart';
import 'package:proximaride_app/pages/profile_setting/ProfileSettingController.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class MyVehicleController extends GetxController {
  final serviceController = Get.find<Service>();
  late ErrorStateManager errorStateManager;
  late ConnectivityService connectivityService;

  var isOverlayLoading = false.obs;
  var vehicleType = "".obs;
  var fuel = "".obs;
  var setPrimary = "no".obs;
  var carImageName = "".obs;
  var carImagePath = "".obs;
  var oldCarImagePath = "".obs;

  var carImageNameOriginal = "".obs;
  var carImagePathOriginal = "".obs;
  var oldCarImagePathOriginal = "".obs;

  var vehicleTypeList = [].obs;
  var vehicleTypeLabelList = [].obs;

  var vehicleList = List.empty(growable: true).obs;
  var errorList = List.empty(growable: true).obs;
  final errors = [].obs;
  var vehicleId = 0.obs;
  final ScrollController listScrollController = ScrollController();
  final ScrollController formScrollController = ScrollController();
  var page = 1;
  var removeCarPhoto = false.obs;

  var scrollField = false;

  late TextEditingController makeTextEditingController,
      modelTextEditingController,
      licenseNumberTextEditingController,
      colorTextEditingController,
      yearTextEditingController;

  final Map<String, FocusNode> focusNodes = {};

  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  bool get isPrimarySelectionLocked =>
      vehicleId.value == 0 && vehicleList.isEmpty;

  @override
  void onInit() {
    super.onInit();

    if (Get.isRegistered<ProfileSettingController>()) {
      final profileSettingController = Get.find<ProfileSettingController>();
      final title = profileSettingController.labelTextDetail['my_vehicles_label'];
      if (title != null && title.toString().trim().isNotEmpty) {
        labelTextDetail['main_heading'] = title;
      }
    }

    // Initialize ErrorStateManager
    errorStateManager = Get.put(ErrorStateManager());

    // Initialize ConnectivityService
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    loadInitialData();
    // paginateVehicleList(); // Call this here or after manual setup? Original called it.
    paginateVehicleList();

    makeTextEditingController = TextEditingController();
    modelTextEditingController = TextEditingController();
    licenseNumberTextEditingController = TextEditingController();
    colorTextEditingController = TextEditingController();
    yearTextEditingController = TextEditingController();

    for (int i = 1; i <= 5; i++) {
      focusNodes[i.toString()] = FocusNode();
      // Attach the onFocusChange listener
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus, trigger validation
          if (i == 1) {
            validateField(
              'make',
              makeTextEditingController.text,
            );
          } else if (i == 2) {
            validateField(
              'model',
              modelTextEditingController.text,
            );
          } else if (i == 3) {
            validateField(
              'license_no',
              licenseNumberTextEditingController.text,
            );
          } else if (i == 4) {
            validateField(
              'color',
              colorTextEditingController.text,
            );
          } else if (i == 5) {
            validateField('year', yearTextEditingController.text,
                type: 'numeric');
          }
        }
      });
    }
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // NO connectivity check here - relying on API exception handling (ErrorStateManager)

      await getVehicleList();

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
    if (type == "network") return ErrorType.network;
    if (type == "server") return ErrorType.server;
    return ErrorType.unknown;
  }

  @override
  void onClose() {
    super.onClose();
    listScrollController.dispose();
    formScrollController.dispose();
    makeTextEditingController.dispose();
    modelTextEditingController.dispose();
    licenseNumberTextEditingController.dispose();
    colorTextEditingController.dispose();
    yearTextEditingController.dispose();
  }

  void validateField(String fieldName, String fieldValue,
      {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      if (fieldName == "make") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['make_error'] ?? "Make");
      } else if (fieldName == "model") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['model_error'] ?? "Model");
      } else if (fieldName == "license_no") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['license_error'] ?? "License no");
      } else if (fieldName == "color") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['color_error'] ?? "Color");
      } else if (fieldName == "year") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['year_error'] ?? "Year");
      }
      // Provide default message if null
      message ??= "$fieldName is required";

      errorList.add(message);
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
      return;
    }
    switch (type) {
      case 'numeric':
        if (fieldValue.isNotEmpty && double.tryParse(fieldValue) == null) {
          var message = validationMessageDetail['numeric'];
          if (message != null) {
            message = message.replaceAll(
                ":attribute", labelTextDetail['year_error'] ?? "Year");
          }
          errorList.add(message ?? '$fieldName must be a number');
        }
        break;
      case 'date':
        if (fieldValue.isNotEmpty && DateTime.tryParse(fieldValue) == null) {
          errorList.add('$fieldName must be a valid date');
        }
        break;
      case 'time':
        if (fieldValue.isNotEmpty &&
            !RegExp(r'^\d{2}:\d{2}$').hasMatch(fieldValue)) {
          errorList.add('$fieldName must be in the format HH:MM');
        }
        break;
      case 'max_words':
        if (fieldValue.isNotEmpty &&
            fieldValue.split(' ').length > wordsLimit) {
          errorList.add('$fieldName must have at most $wordsLimit words');
        }
        break;
      default:
        break;
    }

    if (errorList.isNotEmpty) {
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
    }
    update();
  }

  Future<void> getVehicleList() async {
    // Removed local isLoading
    await MyVehicleProvider()
        .getVehicleList(
            page, serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['vehicles'] != null) {
          vehicleList.addAll(resp['data']['vehicles']);
        }
        if (resp['data'] != null &&
            resp['data']['vehicleSettingPage'] != null) {
          labelTextDetail.addAll(resp['data']['vehicleSettingPage']);
          _populateVehicleTypes(
            details: labelTextDetail,
            vehicleTypeOptions: resp['data']['vehicleTypeOptions'],
          );
        }
        if (resp['data'] != null && resp['data']['siteText'] != null) {
          labelTextDetail.addAll(resp['data']['siteText']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }

        if (resp['data'] != null && resp['data']['messages'] != null) {
          popupTextDetail.addAll(resp['data']['messages']);
        }
      }
    }, onError: (err) {
      throw err; // Propagate to loadInitialData
    });
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

// logger.info('normalizedVehicleTypeOptions ${normalizedVehicleTypeOptions}');
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

  String getVehicleCardTypeLabel(dynamic vehicle) {
    if (vehicle is! Map) {
      return "";
    }

    final String rawCarType =
        vehicle['power_type']?.toString().trim().toLowerCase() ?? "";
    // if (rawCarType.isNotEmpty) {
    //   switch (rawCarType) {
    //     case 'electric':
    //       return labelTextDetail['electric_checkbox_label']?.toString() ??
    //           "Electric";
    //     case 'hybrid':
    //       return labelTextDetail['hybrid_checkbox_label']?.toString() ??
    //           "Hybrid";
    //     case 'gas':
    //       return labelTextDetail['gas_checkbox_label']?.toString() ?? "Gas";
    //   }
    // }

    // final String vehicleTypeId = vehicle['type']?.toString().trim() ?? "";
    // if (vehicleTypeId.isNotEmpty) {
    //   final int index = vehicleTypeList.indexOf(vehicleTypeId);
    //   if (index >= 0 && index < vehicleTypeLabelList.length) {
    //     return vehicleTypeLabelList[index].toString();
    //   }
    // }

    return rawCarType.isNotEmpty ? rawCarType : "";
  }

  bool isPrimaryVehicle(dynamic vehicle) {
    if (vehicle is! Map) {
      return false;
    }
    return vehicle['primary_vehicle']?.toString() == "1";
  }

  void upsertVehicleInList(dynamic rawVehicle) {
    if (rawVehicle is! Map) {
      return;
    }

    final updatedVehicle = Map<String, dynamic>.from(rawVehicle);
    final updatedVehicleId = updatedVehicle['id'];
    final updatedIsPrimary = isPrimaryVehicle(updatedVehicle);

    if (updatedIsPrimary) {
      for (var i = 0; i < vehicleList.length; i++) {
        final item = vehicleList[i];
        if (item is Map) {
          final normalized = Map<String, dynamic>.from(item);
          normalized['primary_vehicle'] =
              normalized['id'] == updatedVehicleId ? "1" : "0";
          vehicleList[i] = normalized;
        }
      }
    }

    final existingIndex =
        vehicleList.indexWhere((element) => element['id'] == updatedVehicleId);

    if (existingIndex != -1) {
      vehicleList.removeAt(existingIndex);
    }

    if (updatedIsPrimary) {
      vehicleList.insert(0, updatedVehicle);
      setPrimary.value = "no";
      return;
    }

    if (existingIndex != -1 && existingIndex <= vehicleList.length) {
      vehicleList.insert(existingIndex, updatedVehicle);
    } else {
      vehicleList.add(updatedVehicle);
    }
  }

  void paginateVehicleList() {
    listScrollController.addListener(() async {
      if (!listScrollController.hasClients || isOverlayLoading.value) {
        return;
      }

      final position = listScrollController.position;
      if (position.pixels >= position.maxScrollExtent) {
        page++;
        await getMoreVehicleList();
      }
    });
  }

  getMoreVehicleList() async {
    try {
      isOverlayLoading(true);
      await MyVehicleProvider()
          .getVehicleList(
              page, serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['data']['vehicles'] != null &&
            resp['data']['vehicles'].isNotEmpty) {
        } else {
          isOverlayLoading(false);
        }
        if (resp['data']['vehicles'] != null &&
            resp['data']['vehicles'] != null) {
          vehicleList.addAll(resp['data']['vehicles']);
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        _handleActionError(err);
      });
    } catch (exception) {
      isOverlayLoading(false);
      _handleActionError(exception);
    }
  }

  addNewVehicle(context, screenHeight) async {
    try {
      scrollField = false;
      if (carImagePathOriginal.value != "") {
        final file = File(carImagePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10) {
          var message = validationMessageDetail['max'];
          message = message.replaceAll(
              ":attribute", labelTextDetail['photo_error'] ?? 'car image');
          message = message.replaceAll(":max", '10');
          var err = {
            'title': "image",
            'eList': [message ?? 'Can not upload image size greater than 10MB']
          };
          errors.add(err);
          return;
        }
      }

      if (isPrimarySelectionLocked) {
        setPrimary.value = "yes";
      }

      // Connectivity check
      if (!connectivityService.isConnected) {
        serviceController.showDialogue(
          "No internet connection. Please check your network and try again.",
          type: "error",
        );
        return;
      }

      isOverlayLoading(true);
      final bool isFirstVehicleCreation = isPrimarySelectionLocked;
      final String primaryFlag = isFirstVehicleCreation
          ? "1"
          : (setPrimary.value == "yes" ? "1" : "0");
      final resp = await MyVehicleProvider().addNewVehicle(
          makeTextEditingController.text.trim(),
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
          vehicleId.value,
          serviceController.token,
          removeCarPhoto.value ? 1 : 0,
          primary: primaryFlag);
      errorList.clear();
      errors.clear();
      if (resp['status'] != null && resp['status'] == "Error") {
        serviceController.showDialogue(resp['message'].toString(),
            type: "error");
      } else if (resp['errors'] != null) {
        if (resp['errors']['make'] != null) {
          var err = {'title': "make", 'eList': resp['errors']['make']};
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 1, screenHeight);
            scrollField = true;
          }
        }
        if (resp['errors']['model'] != null) {
          var err = {'title': "model", 'eList': resp['errors']['model']};
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 2, screenHeight);
            scrollField = true;
          }
        }
        if (resp['errors']['type'] != null) {
          var err = {'title': "type", 'eList': resp['errors']['type']};
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 6, screenHeight);
            scrollField = true;
          }
        }
        if (resp['errors']['license_no'] != null) {
          var err = {
            'title': "license_no",
            'eList': resp['errors']['license_no']
          };
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 3, screenHeight);
            scrollField = true;
          }
        }
        if (resp['errors']['color'] != null) {
          var err = {'title': "color", 'eList': resp['errors']['color']};
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 4, screenHeight);
            scrollField = true;
          }
        }
        if (resp['errors']['year'] != null) {
          var err = {'title': "year", 'eList': resp['errors']['year']};
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 5, screenHeight);
            scrollField = true;
          }
        }
        if (resp['errors']['power_type'] != null) {
          var err = {'title': "power_type", 'eList': resp['errors']['power_type']};
          errors.add(err);
          if (scrollField == false) {
            scrollError(context, 7, screenHeight);
            scrollField = true;
          }
        }
        if (resp['errors']['image'] != null) {
          var err = {'title': "image", 'eList': resp['errors']['image']};
          errors.add(err);
        }
      } else if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data']['vehicle'] != null) {
          upsertVehicleInList(resp['data']['vehicle']);
        }
        vehicleList.refresh();
        Get.back();
        serviceController.showDialogue(resp['message'].toString(),
            type: "success");
      }
      isOverlayLoading(false);
    } catch (exception) {
      isOverlayLoading(false);
      _handleActionError(exception);
    }
  }

  void _handleActionError(dynamic error) {
    String message = error.toString();
    if (error is Map && error.containsKey('message')) {
      message = error['message'];
    }
    serviceController.showDialogue(message, type: "error");
  }

  void getImage(ImageSource imageSource) async {
    final croppedFile = await serviceController.imageCropper(imageSource);

    if (croppedFile != null) {
      oldCarImagePath.value = "";
      carImagePath.value = croppedFile.path;
      carImageName.value = croppedFile.path.split('/').last;
      carImagePathOriginal.value = serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      carImageNameOriginal.value = serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
      Get.back();
    }
  }

  removeVehicle(deleteVehicleId) async {
    // Connectivity check
    if (!connectivityService.isConnected) {
      serviceController.showDialogue(
        "No internet connection. Please check your network and try again.",
        type: "error",
      );
      return;
    }

    bool isConfirmed = await serviceController.showConfirmationDialog(
        cancelYesBtn: '${labelTextDetail['btn_delete_it_text'] ?? 'Yes, delete it!'}',
        "${popupTextDetail['delete_vehicle_message'] ?? "Are you sure you want to delete this vehicle"}",
        cancelNoBtn:
            '${labelTextDetail['btn_take_me_back_text'] ?? "No, take me back!"}');
    if (isConfirmed) {
      try {
        isOverlayLoading(true);
        MyVehicleProvider()
            .removeVehicle(deleteVehicleId, serviceController.token)
            .then((resp) async {
          if (resp['status'] != null && resp['status'] == "Error") {
            serviceController.showDialogue(resp['message'].toString(),
                type: "error");
          } else if (resp['status'] != null && resp['status'] == "Success") {
            vehicleList
                .removeWhere((element) => element['id'] == deleteVehicleId);
            vehicleList.refresh();
            serviceController.showDialogue(resp['message'].toString(),
                type: "success");
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);
          _handleActionError(err);
        });
      } catch (exception) {
        isOverlayLoading(false);
        _handleActionError(exception);
      }
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

  updateSetPrimaryValue(value) async {
    if (value == "yes") {
      setPrimary.value = value;
    } else if (value == "no") {
      setPrimary.value = value;
    } else {
      setPrimary.value = "";
    }
  }

  getVehicleDetail(id) async {
    makeTextEditingController.text = "";
    modelTextEditingController.text = "";
    licenseNumberTextEditingController.text = "";
    colorTextEditingController.text = "";
    yearTextEditingController.text = "";
    fuel.value = "Gas";
    vehicleType.value = "";
    carImageName.value = "";
    carImagePath.value = "";
    vehicleId.value = id;
    if (id != 0) {
      await getVehicleInfo();
    } else {
      setPrimary.value = isPrimarySelectionLocked ? "yes" : "no";
      Get.toNamed('/add_vehicle');
    }
  }

  getVehicleInfo() async {
    // Connectivity check
    if (!connectivityService.isConnected) {
      serviceController.showDialogue(
        "No internet connection. Please check your network and try again.",
        type: "error",
      );
      return;
    }

    try {
      isOverlayLoading(true);
      MyVehicleProvider()
          .getVehicleInfo(vehicleId.value, serviceController.token)
          .then((resp) async {
        if (isClosed) {
          return;
        }

        if (resp['status'] != null &&
            resp['status'] == "Success" &&
            resp['data'] != null &&
            resp['data']['vehicle'] != null) {
          final vehicle =
              Map<String, dynamic>.from(resp['data']['vehicle'] as Map);
          makeTextEditingController.text = vehicle['make']?.toString() ?? "";
          modelTextEditingController.text = vehicle['model']?.toString() ?? "";
          licenseNumberTextEditingController.text =
              vehicle['license_no']?.toString() ?? "";
          colorTextEditingController.text = vehicle['color']?.toString() ?? "";
          yearTextEditingController.text = vehicle['year']?.toString() ?? "";
          fuel.value = vehicle['power_type']?.toString() ?? "";
          vehicleType.value = vehicle['type']?.toString() ?? "";
          oldCarImagePath.value = vehicle['image']?.toString() ?? "";
          oldCarImagePathOriginal.value =
              vehicle['original_image']?.toString().isNotEmpty == true
                  ? vehicle['original_image'].toString()
                  : oldCarImagePath.value;
          setPrimary.value =
              vehicle['primary_vehicle']?.toString() == "1" ? "yes" : "no";
          isOverlayLoading(false);
          if (!isClosed) {
            Get.toNamed('/add_vehicle');
          }
        }
        if (!isClosed) {
          isOverlayLoading(false);
        }
      }, onError: (err) {
        if (!isClosed) {
          isOverlayLoading(false);
          _handleActionError(err);
        }
      });
    } catch (exception) {
      if (!isClosed) {
        isOverlayLoading(false);
        _handleActionError(exception);
      }
    }
  }

  confirmationRemoveCarPhoto() async {
    bool isConfirmed = await serviceController.showConfirmationDialog(
        cancelYesBtn: "Yes, remove it!",
        "${labelTextDetail['delete_photo_message'] ?? "Are you sure you want to delete this car photo"}");

    if (isConfirmed) {
      removeCarPhoto.value = true;
    }
  }

  scrollError(context, position, screenHeight) {
    position = position * 50.0;
    if (MediaQuery.of(context).viewInsets.bottom > 0) {
      // Keyboard is visible, adjust the scroll to avoid the keyboard
      position -= 50.0; // Adjust as per your requirement
    }

    // Scroll to the calculated position with some margin
    formScrollController.animateTo(
      position - screenHeight / 4, // This adjusts the position dynamically
      duration: Duration(milliseconds: 300),
      curve: Curves.easeInOut,
    );
  }
}
