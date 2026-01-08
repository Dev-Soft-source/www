import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

class StageController extends GetxController {
  var isOverlayLoading = false.obs;
  var isLoading = false.obs;
  final serviceController = Get.find<Service>();
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  final secureStorage = const FlutterSecureStorage();
  var stepNo = "0".obs;

  late TextEditingController firstNameTextEditingController,
      lastNameTextEditingController,
      dobTextEditingController,
      searchTextEditingController,
      postalCodeTextEditingController,
      miniBioTextEditingController;

  var gender = "".obs;
  var countryId = 39.obs;
  var countryName = "Canada".obs;
  var stateId = 0.obs;
  var stateName = "".obs;
  var cityId = 0.obs;
  var cityName = "".obs;
  var countries = List<dynamic>.empty(growable: true).obs;
  var searchCountries = List<dynamic>.empty(growable: true).obs;
  var states = List<dynamic>.empty(growable: true).obs;
  var searchStates = List<dynamic>.empty(growable: true).obs;
  var cities = List<dynamic>.empty(growable: true).obs;
  var searchCities = List<dynamic>.empty(growable: true).obs;

  var labelTextDetail = {}.obs;
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

    firstNameTextEditingController = TextEditingController();
    lastNameTextEditingController = TextEditingController();
    dobTextEditingController = TextEditingController();
    searchTextEditingController = TextEditingController();
    postalCodeTextEditingController = TextEditingController();
    miniBioTextEditingController = TextEditingController();


    firstNameTextEditingController.text = serviceController.loginUserDetail['first_name'].toString();
    lastNameTextEditingController.text = serviceController.loginUserDetail['last_name'].toString();

    stepNo.value = serviceController.loginUserDetail['step'].toString();
  }

  @override
  void onClose() {
    // TODO: implement onClose

    super.onClose();
    // firstNameTextEditingController.dispose();
    // lastNameTextEditingController.dispose();
    // dobTextEditingController.dispose();
    // searchTextEditingController.dispose();
  }

  updateGenderValue(value) async {
    if (value == "male") {
      gender.value = value;
    } else if (value == "female") {
      gender.value = value;
    } else if (value == "prefer not to say") {
      gender.value = value;
    } else {
      gender.value = "";
    }
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
      await StageProvider().getLabelTextDetail(serviceController.langId.value, step1Page, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['step1Page'] != null){
            labelTextDetail.addAll(resp['data']['step1Page']);
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


  setStageOne() async {
    try {
      if (firstNameTextEditingController.text.isEmpty ||
          lastNameTextEditingController.text.isEmpty ||
          gender.value.isEmpty ||
          dobTextEditingController.text.isEmpty ||
          countryName.isEmpty ||
          stateName.isEmpty ||
          cityName.isEmpty ||
          postalCodeTextEditingController.text.isEmpty ||
          miniBioTextEditingController.text.isEmpty
      ) {
        if (firstNameTextEditingController.text.isEmpty) {

          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['first_name_error'] ?? 'First name');
          var err = {
            'title': "first",
            'eList': [message ?? 'First name field is required']
          };
          errors.add(err);
        }

        if (lastNameTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['last_name_error'] ?? 'Last name');
          var err = {
            'title': "last",
            'eList': [message ?? 'Last name field is required']
          };
          errors.add(err);
        }

        if (gender.value.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['gender_error'] ?? 'Gender');
          var err = {
            'title': "gender",
            'eList': [message ?? 'Gender is required']
          };
          errors.add(err);
        }

        if (dobTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['dob_error'] ?? 'Date');
          var err = {
            'title': "dob",
            'eList': [message ?? 'Date is required']
          };
          errors.add(err);
        }

        if (countryName.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['country_error'] ?? 'Country name');
          var err = {
            'title': "country",
            'eList': [message ?? 'Country name field is required']
          };
          errors.add(err);
        }

        if (stateName.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['state_error'] ?? 'State/province');
          var err = {
            'title': "state",
            'eList': [message ?? 'State/province is required']
          };
          errors.add(err);
        }

        if (cityName.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['city_error'] ?? 'City');
          var err = {
            'title': "city",
            'eList': [message ?? 'City field is required']
          };
          errors.add(err);
        }

        if (postalCodeTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['zip_code_error'] ?? 'Postal code');
          var err = {
            'title': "zipcode",
            'eList': [message ?? 'Postal code field is required']
          };
          errors.add(err);
        }

        if(miniBioTextEditingController.text.isEmpty){
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['bio_error'] ?? 'Mini bio');
          var err = {
            'title': "mini",
            'eList' : [message ?? 'Mini bio is required']
          };
          errors.add(err);
        }

        return;
      }

      isOverlayLoading(true);
      StageProvider()
          .setStageOne(
        serviceController.token,
        firstNameTextEditingController.text.trim(),
        lastNameTextEditingController.text,
        gender.value,
        dobTextEditingController.text,
        countryId.value.toString(),
        stateId.value.toString(),
        cityId.value.toString(),
        postalCodeTextEditingController.text,
        miniBioTextEditingController.text
      )
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['errors'] != null) {
          if (resp['errors']['first_name'] != null) {
            errorList.addAll(resp['errors']['first_name']);
          }
          if (resp['errors']['last_name'] != null) {
            errorList.addAll(resp['errors']['last_name']);
          }
          if(resp['errors']['zipcode'] != null) {
            var err = {
              'title': "zipcode",
              'eList' : resp['errors']['zipcode']
            };
            errors.add(err);
          }
          if(resp['errors']['about'] != null) {
            var err = {
              'title': "mini",
              'eList' : resp['errors']['about']
            };
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.loginUserDetail['step'] = "2";
          serviceController.loginUserDetail['gender'] = resp['data']['gender'].toString();

          serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));

          stepNo.value = serviceController.loginUserDetail['step'].toString();

          firstNameTextEditingController.text = "";
          lastNameTextEditingController.text = "";
          gender.value = "";
          dobTextEditingController.text = "";
          countryName.value = "";
          stateName.value = "";
          cityName.value = "";
          postalCodeTextEditingController.text = "";
          miniBioTextEditingController.text = "";
          Get.offAllNamed('/stage_two');
          // serviceController.showDialogue(resp['message'].toString());
          // serviceController.showDialogue(resp['message'].toString(),off: 1,path: '/stage_two');
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
