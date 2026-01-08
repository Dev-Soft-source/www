import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_vehicle/MyVehicleProvider.dart';
import 'package:proximaride_app/services/service.dart';

class MyVehicleController extends GetxController{


  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
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
  ScrollController scrollController = ScrollController();
  var page = 1;
  var removeCarPhoto = false.obs;

  var scrollField = false;

  late TextEditingController makeTextEditingController, modelTextEditingController, licenseNumberTextEditingController, colorTextEditingController,
      yearTextEditingController;

  final Map<String, FocusNode> focusNodes = {};

  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  @override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    await getVehicleList();
    // paginateVehicleList();
    makeTextEditingController = TextEditingController();
    modelTextEditingController = TextEditingController();
    licenseNumberTextEditingController = TextEditingController();
    colorTextEditingController = TextEditingController();
    yearTextEditingController= TextEditingController();

    for (int i = 1; i <= 5; i++) {
      focusNodes[i.toString()] = FocusNode();
      // Attach the onFocusChange listener
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus, trigger validation
          if (i == 1) {
            validateField('make', makeTextEditingController.text,);
          } else if (i == 2) {
            validateField('model', modelTextEditingController.text,);
          } else if (i == 3) {
            validateField('liscense_no', licenseNumberTextEditingController.text,);
          } else if (i == 4) {
            validateField('color', colorTextEditingController.text,);
          } else if (i == 5) {
            validateField('year', yearTextEditingController.text, type: 'numeric');
          }
        }
      });
    }
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    scrollController.dispose();
    makeTextEditingController.dispose();
    modelTextEditingController.dispose();
    licenseNumberTextEditingController.dispose();
    colorTextEditingController.dispose();
    yearTextEditingController.dispose();
  }

  void validateField(String fieldName, String fieldValue, {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      if(fieldName == "make"){
        message = message.replaceAll(":Attribute", labelTextDetail['make_error'] ?? "Make");
      }else if(fieldName == "model"){
        message = message.replaceAll(":Attribute", labelTextDetail['model_error'] ?? "Model");
      }else if(fieldName == "liscense_no"){
        message = message.replaceAll(":Attribute", labelTextDetail['license_error'] ?? "License no");
      }else if(fieldName == "color"){
        message = message.replaceAll(":Attribute", labelTextDetail['color_error'] ?? "Color");
      }else if(fieldName == "year"){
        message = message.replaceAll(":Attribute", labelTextDetail['year_error'] ?? "Year");
      }

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
          message.replaceAll(":attribute", labelTextDetail['year_error'] ?? "Year");
          errorList.add(message ?? '$fieldName must be a number');
        }
        break;
      case 'date':
        if (fieldValue.isNotEmpty && DateTime.tryParse(fieldValue) == null) {
          errorList.add('$fieldName must be a valid date');
        }
        break;
      case 'time':
        if (fieldValue.isNotEmpty && !RegExp(r'^\d{2}:\d{2}$').hasMatch(fieldValue)) {
          errorList.add('$fieldName must be in the format HH:MM');
        }
        break;
      case 'max_words':
        if (fieldValue.isNotEmpty && fieldValue.split(' ').length > wordsLimit) {
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


  getVehicleList() async{
    try{
      isLoading(true);
      MyVehicleProvider().getVehicleList(
        page,
        serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['vehicles'] != null){
            vehicleList.addAll(resp['data']['vehicles']);
          }
          if(resp['data'] != null && resp['data']['vehicleSettingPage'] != null){
            labelTextDetail.addAll(resp['data']['vehicleSettingPage']);
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

          if(resp['data'] != null && resp['data']['messages'] != null){
            popupTextDetail.addAll(resp['data']['messages']);
          }




        }
        isLoading(false);
      },onError: (err){
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });
    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  void paginateVehicleList(){
    scrollController.addListener(() async {
      if(scrollController.position.pixels == scrollController.position.maxScrollExtent){
        page++;
        await getMoreVehicleList();
      }
    });
  }

  getMoreVehicleList() async{
    try {
      isOverlayLoading(true);
      await MyVehicleProvider().getVehicleList(
          page,
          serviceController.token,
        serviceController.langId.value
      ).then((resp) async{
        if(resp['data']['vehicles'] != null && resp['data']['vehicles'].isNotEmpty){

        }else{
          isOverlayLoading(false);
        }
        if(resp['data']['vehicles'] != null && resp['data']['vehicles'] != null){
          vehicleList.addAll(resp['data']['vehicles']);
        }
        isOverlayLoading(false);
      } ,onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  addNewVehicle(context, screenHeight) async{
    try{

      scrollField = false;
      if(carImagePathOriginal.value != ""){
        final file = File(carImagePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10){
          var message = validationMessageDetail['max'];
          message = message.replaceAll(":attribute", labelTextDetail['photo_error'] ?? 'car image');
          message = message.replaceAll(":max", '10');
          var err = {
            'title': "image",
            'eList' : [message ?? 'Can not upload image size greater than 10MB']
          };
          errors.add(err);
          return;
        }
      }


      isOverlayLoading(true);
      MyVehicleProvider().addNewVehicle(
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
          primary: setPrimary.value == "yes" ? "1" : "0"
      ).then((resp) async {

        errorList.clear();
        errors.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['make'] != null){
            var err = {
              'title': "make",
              'eList' : resp['errors']['make']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, 1, screenHeight);
              scrollField = true;
            }
          }if(resp['errors']['model'] != null){
            var err = {
              'title': "model",
              'eList' : resp['errors']['model']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, 2, screenHeight);
              scrollField = true;
            }
          }if(resp['errors']['type'] != null ){
            var err = {
              'title': "type",
              'eList' : resp['errors']['type']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, 6, screenHeight);
              scrollField = true;
            }
          }if(resp['errors']['liscense_no'] != null){
            var err = {
              'title': "liscense_no",
              'eList' : resp['errors']['liscense_no']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, 3, screenHeight);
              scrollField = true;
            }
          }if(resp['errors']['color'] != null){
            var err = {
              'title': "color",
              'eList' : resp['errors']['color']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, 4, screenHeight);
              scrollField = true;
            }
          }if(resp['errors']['year'] != null){
            var err = {
              'title': "year",
              'eList' : resp['errors']['year']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, 5, screenHeight);
              scrollField = true;
            }
          }if(resp['errors']['car_type'] != null){
            var err = {
              'title': "car_type",
              'eList' : resp['errors']['car_type']
            };
            errors.add(err);
            if(scrollField == false){
              scrollError(context, 7, screenHeight);
              scrollField = true;
            }
          }if(resp['errors']['image'] != null){
            var err = {
              'title': "image",
              'eList' : resp['errors']['image']
            };
            errors.add(err);
          }
        }else if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data']['vehicle'] != null) {
            if (vehicleId.value != 0) {
              vehicleList.removeWhere((element) =>
              element['id'] == vehicleId.value);
            }
            if (resp['data']['vehicle']['primary_vehicle'] == "1") {
              vehicleList.add(vehicleList[0]);
              vehicleList[0] = resp['data']['vehicle'];
              setPrimary.value = "no";
            }
            else {
              vehicleList.add(resp['data']['vehicle']);
            }
          }
          vehicleList.refresh();
          Get.back();
          serviceController.showDialogue(resp['message'].toString());
        }
        isOverlayLoading(false);
      },onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });
    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  void getImage(ImageSource imageSource) async{


    final croppedFile = await serviceController.imageCropper(imageSource);

    if (croppedFile != null) {
      oldCarImagePath.value = "";
      carImagePath.value = croppedFile.path;
      carImageName.value = croppedFile.path
          .split('/')
          .last;
      carImagePathOriginal.value = serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      carImageNameOriginal.value = serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
      Get.back();
    }
  }

  removeVehicle(deleteVehicleId) async{
    bool isConfirmed = await serviceController.showConfirmationDialog("${popupTextDetail['delete_vehicle_message'] ?? "Are you sure you want to delete this vehicle"}");
    if(isConfirmed){
      try{
        isOverlayLoading(true);
        MyVehicleProvider().removeVehicle(
            deleteVehicleId,
            serviceController.token
        ).then((resp) async {
          if(resp['status'] != null && resp['status'] == "Error"){
            serviceController.showDialogue(resp['message'].toString());
          }else if(resp['status'] != null && resp['status'] == "Success"){
            vehicleList.removeWhere((element) => element['id'] == deleteVehicleId);
            vehicleList.refresh();
            serviceController.showDialogue(resp['message'].toString());
          }
          isOverlayLoading(false);
        },onError: (err){
          isOverlayLoading(false);
          serviceController.showDialogue(err.toString());
        });

      }catch (exception) {
        isOverlayLoading(false);
        serviceController.showDialogue(exception.toString());
      }
    }
  }

  updateFuelValue (value) async{
    if(value == "Electric car"){
      fuel.value = value;
    }else if(value == "Hybrid car"){
      fuel.value = value;
    }else if(value == "Gas"){
      fuel.value = value;
    }
    else{
      fuel.value = "";
    }
  }

  updateSetPrimaryValue (value) async{
    if(value == "yes"){
      setPrimary.value = value;
    }else if(value == "no"){
      setPrimary.value = value;
    }
    else{
      setPrimary.value = "";
    }
  }

  getVehicleDetail(id) async{
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
    if(id != 0){
      await getVehicleInfo();
    }else{
      Get.toNamed('/add_vehicle');
    }
  }

  getVehicleInfo() async{
    try{
      isOverlayLoading(true);
      MyVehicleProvider().getVehicleInfo(
          vehicleId.value,
          serviceController.token
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success" && resp['data'] != null && resp['data']['vehicle'] != null){
          makeTextEditingController.text = resp['data']['vehicle']['make'];
          modelTextEditingController.text = resp['data']['vehicle']['model'];
          licenseNumberTextEditingController.text = resp['data']['vehicle']['liscense_no'];
          colorTextEditingController.text = resp['data']['vehicle']['color'];
          yearTextEditingController.text = resp['data']['vehicle']['year'];
          fuel.value = resp['data']['vehicle']['car_type'];
          vehicleType.value = resp['data']['vehicle']['type'];
          oldCarImagePath.value = resp['data']['vehicle']['image'] ?? "";
          oldCarImagePathOriginal.value = resp['data']['vehicle']['original_image'] ?? "";
          setPrimary.value = resp['data']['vehicle']['primary_vehicle'] == "1" ? "yes" : "no";
          isOverlayLoading(false);
          Get.toNamed('/add_vehicle');
        }
        isOverlayLoading(false);
      },onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  confirmationRemoveCarPhoto() async{
    bool isConfirmed = await serviceController.showConfirmationDialog("${labelTextDetail['delete_photo_message'] ?? "Are you sure you want to delete this car photo"}");

    if(isConfirmed){
      removeCarPhoto.value = true;
    }
  }

  scrollError(context, position ,screenHeight){

    position = position * 50.0;
    if (MediaQuery.of(context).viewInsets.bottom > 0) {
      // Keyboard is visible, adjust the scroll to avoid the keyboard
      position -= 50.0; // Adjust as per your requirement
    }

    // Scroll to the calculated position with some margin
    scrollController.animateTo(
      position - screenHeight / 4, // This adjusts the position dynamically
      duration: Duration(milliseconds: 300),
      curve: Curves.easeInOut,
    );
  }

}