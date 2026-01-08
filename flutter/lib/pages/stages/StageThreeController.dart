import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

class StageThreeController extends GetxController {
  var isOverlayLoading = false.obs;
  var isLoading = false.obs;
  final serviceController = Get.find<Service>();
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  final secureStorage = const FlutterSecureStorage();
  var stepNo = "0".obs;
  var finishBtn = false.obs;
  Timer? timer;
  var secondsRemaining = 5.obs;

  late TextEditingController
      makeTextEditingController,
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
  var vehicleTypeList = [].obs;
  var vehicleTypeLabelList = [].obs;
  var validationMessageDetail = {}.obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();

    isLoading(true);
    if(serviceController.languages.isEmpty){
      await getLanguages();
    }
    await getLabelTextDetail();
    isLoading(false);
    makeTextEditingController = TextEditingController();
    modelTextEditingController = TextEditingController();
    licenseNumberTextEditingController = TextEditingController();
    colorTextEditingController = TextEditingController();
    yearTextEditingController = TextEditingController();


    stepNo.value = serviceController.loginUserDetail['step'].toString();
  }

  @override
  void onClose() {
    // TODO: implement onClose

    super.onClose();

  }



  Future<void> getLanguages() async {
    try {

      await LoginProvider().getLanguages().then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data']!= null && resp['data']['languages']!= null){
            serviceController.languages.clear();
            serviceController.languages.addAll(resp['data']['languages']);

            if(serviceController.langId.value == 0){
              var getDefaultLanguage = serviceController.languages.firstWhereOrNull((element) => element['is_default'] == "1");
              if(getDefaultLanguage != null){
                serviceController.langId.value = int.parse(getDefaultLanguage['id'].toString());
              }
            }


            var getLanguage = serviceController.languages.firstWhereOrNull((element) => element['id'] == serviceController.langId.value);
            if(getLanguage != null){
              serviceController.langIcon.value = getLanguage['flag_icon'];
              serviceController.lang.value = getLanguage['abbreviation'];
            }

          }

        }

      }, onError: (error) {
        serviceController.showDialogue(error.toString());
        isLoading(false);
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
      isLoading(false);
    }
  }

  Future<void> getLabelTextDetail() async {
    try {
      await StageProvider().getLabelTextDetail(serviceController.langId.value, step3Page, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['step3Page'] != null){
            labelTextDetail.addAll(resp['data']['step3Page']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_convertible_text'] ?? "Convertable");
            vehicleTypeList.add(labelTextDetail['vehicle_type_convertible_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_coupe_text'] ?? "Coupe");
            vehicleTypeList.add(labelTextDetail['vehicle_type_coupe_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_hatchback_text'] ?? "Hatchback");
            vehicleTypeList.add(labelTextDetail['vehicle_type_hatchback_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_minivan_text'] ?? "Minivan");
            vehicleTypeList.add(labelTextDetail['vehicle_type_minivan_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_sedan_text'] ?? "Sedan");
            vehicleTypeList.add(labelTextDetail['vehicle_type_sedan_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_station_wagon_text'] ?? "Station wagon");
            vehicleTypeList.add(labelTextDetail['vehicle_type_station_wagon_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_suv_text'] ?? "SUV");
            vehicleTypeList.add(labelTextDetail['vehicle_type_suv_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_truck_text'] ?? "Truck");
            vehicleTypeList.add(labelTextDetail['vehicle_type_truck_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_van_text'] ?? "Van");
            vehicleTypeList.add(labelTextDetail['vehicle_type_van_value']);
          }

          if(resp['data'] != null && resp['data']['validationMessages'] != null){
            validationMessageDetail.addAll(resp['data']['validationMessages']);
          }

          var getLanguage = serviceController.languages.firstWhereOrNull((element) => element['id'] == serviceController.langId.value);
          if(getLanguage != null){
            serviceController.langIcon.value = getLanguage['flag_icon'];
            serviceController.lang.value = getLanguage['abbreviation'];
          }
        }
      }, onError: (error) {
        serviceController.showDialogue(error.toString());
        isLoading(false);
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
      isLoading(false);
    }
  }



  void getImage(ImageSource imageSource) async {
    final croppedFile = await serviceController.imageCropper(imageSource);
    if (croppedFile != null) {
       if (stepNo.value == "3") {
        if (imageType.value == 1) {
          carImagePath.value = croppedFile.path;
          carImageName.value = croppedFile.path
              .split('/')
              .last;
          carImagePathOriginal.value = serviceController.originalImagePath.value;
          serviceController.originalImagePath.value = "";
          carImageNameOriginal.value = serviceController.originalImageName.value;
          serviceController.originalImageName.value = "";
        } else if (imageType.value == 2) {
          driverLicensePath.value = croppedFile.path;
          driverLicenseName.value = croppedFile.path
              .split('/')
              .last;
          driverLicensePathOriginal.value = serviceController.originalImagePath.value;
          serviceController.originalImagePath.value = "";
          driverLicenseNameOriginal.value = serviceController.originalImageName.value;
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
            .setStageThree('', '', '', '', '', '', '', '', '', '', '','', '', '', '','','',
            serviceController.token, "1")
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

            Get.offAllNamed('/stage_four');
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);

          serviceController.showDialogue(err.toString());


        });
      } catch (exception) {
        isOverlayLoading(false);
        serviceController.showDialogue(exception.toString());
      }
      return;
    }


    errors.clear();

    if(skipVehicle == false){
      if (makeTextEditingController.text.isEmpty ||
          modelTextEditingController.text.isEmpty ||
          licenseNumberTextEditingController.text.isEmpty ||
          colorTextEditingController.text.isEmpty ||
          yearTextEditingController.text.isEmpty ||
          vehicleType.value == "" ||
          fuel.value == "") {
        if (makeTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['make_error'] ?? 'Make');
          var err = {
            'title': "make",
            'eList': [message ?? 'Make field is required']
          };
          errors.add(err);
        }
        if (modelTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['model_error'] ?? 'Model');
          var err = {
            'title': "model",
            'eList': [message ?? 'Model field is required']
          };
          errors.add(err);
        }
        if (vehicleType.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['vehicle_type_error'] ?? 'Car');
          var err = {
            'title': "type",
            'eList': [message ?? 'Car type is required']
          };
          errors.add(err);
        }
        if (licenseNumberTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['license_error'] ?? 'License number');
          var err = {
            'title': "liscense_no",
            'eList': [message ?? 'License number field is required']
          };
          errors.add(err);
        }
        if (colorTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['color_error'] ?? 'Color');
          var err = {
            'title': "color",
            'eList': [message ?? 'Color field is required']
          };
          errors.add(err);
        }
        if (yearTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['year_error'] ?? 'Year');
          var err = {
            'title': "year",
            'eList': [message ?? 'Year field is required']
          };
          errors.add(err);
        }
        if (fuel.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['fuel_error'] ?? 'Fuel type ');
          var err = {
            'title': "car_type",
            'eList': [message ?? 'Fuel type is required']
          };
          errors.add(err);
        }

        if(carImagePathOriginal.value != ""){
          final file = File(carImagePathOriginal.value);
          int sizeInBytes = file.lengthSync();
          double sizeInMb = sizeInBytes / (1024 * 1024);
          if (sizeInMb > 10){
            var message = validationMessageDetail['max.file'];
            message = message.replaceAll(":attribute", labelTextDetail['photo_error'] ?? 'car image');
            message = message.replaceAll(":max", '10');
            serviceController.showDialogue(message ?? 'Can not upload image size greater than 10MB');
          }
        }
        return;
      }
    }

    if(skipLicense == false){

      if (driverLicensePathOriginal.value.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['driver_license_error'] ?? 'Driver license');
        var err = {
          'title': "driver_license",
          'eList': [message ?? 'Driver license field is required']
        };
        errors.add(err);
      }

      if(driverLicensePathOriginal.value != ""){
        final file = File(driverLicensePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10){
          var message = validationMessageDetail['max.file'];
          message = message.replaceAll(":attribute", labelTextDetail['driver_license_error'] ?? 'driver license image');
          message = message.replaceAll(":max", '10');
          serviceController.showDialogue(message ?? 'Can not upload image size greater than 10MB');
          return;
        }
      }
    }
    try {
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

          makeTextEditingController.text = "";
          modelTextEditingController.text = "";
          licenseNumberTextEditingController.text  = "";
          colorTextEditingController.text  = "";
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

          Get.offAllNamed('/stage_four');
          // serviceController.showDialogue(resp['message'].toString());
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      isOverlayLoading(false);
        serviceController.showDialogue(exception.toString());
    }
  }


}
