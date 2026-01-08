import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileProvider.dart';
import 'package:proximaride_app/services/service.dart';

import '../profile_detail/ProfileDetailController.dart';

class EditProfileController extends GetxController {
  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var gender = "".obs;
  var type = 0.obs;
  var countryId = 0.obs;
  var countryName = "".obs;
  var stateId = 0.obs;
  var stateName = "".obs;
  var cityId = 0.obs;
  var cityName = "".obs;
  var governmentImageName = "".obs;
  var governmentImagePath = "".obs;
  var governmentImageNameOriginal = "".obs;
  var governmentImagePathOriginal = "".obs;
  var governmentImagePathOriginalOld = "".obs;
  var countries = List<dynamic>.empty(growable: true).obs;
  var searchCountries = List<dynamic>.empty(growable: true).obs;
  var states = List<dynamic>.empty(growable: true).obs;
  var searchStates = List<dynamic>.empty(growable: true).obs;
  var cities = List<dynamic>.empty(growable: true).obs;
  var searchCities = List<dynamic>.empty(growable: true).obs;
  var oldGovernmentIssuedId = "".obs;
  var errorList = List<dynamic>.empty(growable: true).obs;
  var errors = [].obs;
  var scrollField = false;
  ScrollController scrollController = ScrollController();

  var labelTextDetail = {}.obs;

  var validationMessageDetail = {}.obs;

  late TextEditingController firstNameTextEditingController,
      lastNameTextEditingController,
      dobTextEditingController,
      addressTextEditingController,
      postalCodeTextEditingController,
      miniBioTextEditingController,
      searchTextEditingController;

  var reviews = List<dynamic>.empty(growable: true).obs;
  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    firstNameTextEditingController = TextEditingController();
    lastNameTextEditingController = TextEditingController();
    dobTextEditingController = TextEditingController();
    addressTextEditingController = TextEditingController();
    postalCodeTextEditingController = TextEditingController();
    miniBioTextEditingController = TextEditingController();
    searchTextEditingController = TextEditingController();
    await getUserDetail();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    firstNameTextEditingController.dispose();
    lastNameTextEditingController.dispose();
    dobTextEditingController.dispose();
    addressTextEditingController.dispose();
    postalCodeTextEditingController.dispose();
    miniBioTextEditingController.dispose();
    searchTextEditingController.dispose();
  }

  getUserDetail() async {
    try {
      isLoading(true);
      EditProfileProvider().getUserDetail(serviceController.token, serviceController.langId.value).then(
          (resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['user'] != null) {
            firstNameTextEditingController.text =
                resp['data']['user']['first_name'];
            lastNameTextEditingController.text =
                resp['data']['user']['last_name'];
            addressTextEditingController.text = resp['data']['user']['address'] ?? "";
            postalCodeTextEditingController.text =
                resp['data']['user']['zipcode'];
            miniBioTextEditingController.text = resp['data']['user']['about'];
            oldGovernmentIssuedId.value =
                resp['data']['user']['government_issued_id'] ?? "";

            governmentImagePathOriginalOld.value = resp['data']['user']['government_issued_original_id'] ?? "";

            gender.value = resp['data']['user']['gender'];
            type.value = int.parse(resp['data']['user']['type'] == ""
                ? 0.toString()
                : resp['data']['user']['type'].toString());
            if (resp['data']['user']['dob'] != null) {
              dobTextEditingController.text = resp['data']['user']['dob'];
            }
            countryId.value = resp['data']['country'] != null
                ? resp['data']['country']['id']
                : 0;
            countryName.value = resp['data']['country'] != null
                ? resp['data']['country']['name']
                : "";
            stateId.value =
                resp['data']['state'] != null ? resp['data']['state']['id'] : 0;
            stateName.value = resp['data']['state'] != null
                ? resp['data']['state']['name']
                : '';
            cityId.value =
                resp['data']['city'] != null ? resp['data']['city']['id'] : 0;
            cityName.value = resp['data']['city'] != null
                ? resp['data']['city']['name']
                : '';
          }

          if(resp['data'] != null && resp['data']['editProfilePage'] != null){
            labelTextDetail.addAll(resp['data']['editProfilePage']);
          }

          if(resp['data'] != null && resp['data']['validationMessages'] != null){
            validationMessageDetail.addAll(resp['data']['validationMessages']);
          }
        }
        isLoading(false);
      }, onError: (err) {
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
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

  getCountries() async {
    try {
      if (countries.isNotEmpty) {
        Get.toNamed("/country");
        return;
      } else {
        isOverlayLoading(true);
        EditProfileProvider().getCountries(serviceController.token).then(
            (resp) async {
          if (resp['status'] != null && resp['status'] == "Success") {
            if (resp['data'] != null && resp['data']['countries'] != null) {
              countries.addAll(resp['data']['countries']);
              searchCountries.addAll(countries);
              isOverlayLoading(false);
              Get.toNamed("/country");
            }
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);
          serviceController.showDialogue(err.toString());
        });
      }
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
  }

  getStates() async {
    try {
      if (states.isNotEmpty) {
        Get.toNamed("/state/${countryId.value}");
        return;
      } else {
        isOverlayLoading(true);
        EditProfileProvider()
            .getStates(countryId.value, serviceController.token)
            .then((resp) async {
          if (resp['status'] != null && resp['status'] == "Success") {
            if (resp['data'] != null && resp['data']['states'] != null) {
              states.addAll(resp['data']['states']);
              searchStates.addAll(states);
              isOverlayLoading(false);
              Get.toNamed("/state/${countryId.value}");
            }
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);
          serviceController.showDialogue(err.toString());
        });
      }
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
  }

  getCities() async {
    try {
      if (cities.isNotEmpty) {
        Get.toNamed("/city/city/${stateId.value}/0/no");
        return;
      } else {
        isOverlayLoading(true);
        EditProfileProvider()
            .getCities(stateId.value, "", serviceController.token)
            .then((resp) async {
          if (resp['status'] != null && resp['status'] == "Success") {
            if (resp['data'] != null && resp['data']['cities'] != null) {
              cities.addAll(resp['data']['cities']);
              searchCities.addAll(cities);
              isOverlayLoading(false);
              Get.toNamed("/city/city/${stateId.value}/0/no");
            }
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);
          serviceController.showDialogue(err.toString());
        });
      }
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
  }

  filterCountries(value) {
    searchCountries.clear();
    if (value == "" || value == null) {
      searchCountries.addAll(countries);
    } else {
      searchCountries.addAll(countries
          .where((item) =>
              item['name'].toLowerCase().contains(value.toLowerCase()))
          .toList());
    }
  }

  filterStates(value) {
    searchStates.clear();
    if (value == "" || value == null) {
      searchStates.addAll(states);
    } else {
      searchStates.addAll(states
          .where((item) =>
              item['name'].toLowerCase().contains(value.toLowerCase()))
          .toList());
    }
  }

  filterCities(value) {
    searchCities.clear();
    if (value == "" || value == null) {
      searchCities.addAll(cities);
    } else {
      searchCities.addAll(cities
          .where((item) =>
              item['name'].toLowerCase().contains(value.toLowerCase()))
          .toList());
    }
  }

  void getImage(ImageSource imageSource) async {



    final croppedFile = await serviceController.imageCropper(imageSource, );

    if (croppedFile != null) {
      governmentImagePath.value = croppedFile.path;
      governmentImageName.value = croppedFile.path.split('/').last;
      governmentImagePathOriginal.value = serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      governmentImageNameOriginal.value = serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
      Get.back();
    }
  }

  editUserProfile(context, screenHeight) async {
    scrollField = false;
    if (firstNameTextEditingController.text.isEmpty ||
        lastNameTextEditingController.text.isEmpty ||
        gender.value == "" ||
        dobTextEditingController.text.isEmpty ||
        countryName.value == "" ||
        stateName.value == "" ||
        cityName.value == "" ||
        postalCodeTextEditingController.text.isEmpty ||
        miniBioTextEditingController.text.isEmpty) {

      if(firstNameTextEditingController.text.isEmpty){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['first_name_error'] ?? 'First name');
        var err = {
          'title': "first",
          'eList' : [message ?? 'First name field is required']
        };
        errors.add(err);
        if(scrollField == false){
          scrollError(context, 1, screenHeight);
          scrollField = true;
        }
      }
      if(lastNameTextEditingController.text.isEmpty){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['last_name_error'] ?? 'Last name');
        var err = {
          'title': "last",
          'eList' : [message ?? 'Last name field is required']
        };
        errors.add(err);
        if(scrollField == false){
          scrollError(context, 2, screenHeight);
          scrollField = true;
        }
      }
      if(gender.value == ""){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['gender_error'] ?? 'Gender');
        var err = {
          'title': "gender",
          'eList' : [message ?? 'Gender is required']
        };
        errors.add(err);
        if(scrollField == false){
          scrollError(context, 4, screenHeight);
          scrollField = true;
        }
      }

      if(dobTextEditingController.text.isEmpty){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['dob_error'] ?? 'Date of birth ');
        var err = {
          'title': "dob",
          'eList' : [message ?? 'Date of birth is required']
        };
        errors.add(err);
        if(scrollField == false){
          scrollError(context, 5, screenHeight);
          scrollField = true;
        }
      }

      if(countryName.value == ""){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['country_error'] ?? 'Country');
        var err = {
          'title': "country",
          'eList' : [message ?? 'Country is required']
        };
        errors.add(err);
        if(scrollField == false){
          scrollError(context, 6, screenHeight);
          scrollField = true;
        }
      }

      if(stateName.value == ""){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['state_error'] ?? 'State');
        var err = {
          'title': "state",
          'eList' : [message ?? 'State is required']
        };
        errors.add(err);
        if(scrollField == false){
          scrollError(context, 7, screenHeight);
          scrollField = true;
        }
      }
      if(cityName.value == ""){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['city_error'] ?? 'City');
        var err = {
          'title': "city",
          'eList' : [message ?? 'City is required']
        };
        errors.add(err);
        if(scrollField == false){
          scrollError(context, 8, screenHeight);
          scrollField = true;
        }
      }

      if(postalCodeTextEditingController.text.isEmpty){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['zip_code_error'] ?? 'Postal code');
        var err = {
          'title': "postal",
          'eList' : [message ?? 'Postal code field is required']
        };
        errors.add(err);
        if(scrollField == false){
          scrollError(context, 9, screenHeight);
          scrollField = true;
        }
      }

      if(miniBioTextEditingController.text.isEmpty){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['bio_error'] ?? 'Mini bio');
        var err = {
          'title': "mini",
          'eList' : [message ?? 'Mini bio is required']
        };
        errors.add(err);
        if(scrollField == false){
          scrollError(context, 11, screenHeight);
          scrollField = true;
        }
      }
      if(errors.isNotEmpty){
        return;
      }

    }


    if(governmentImagePathOriginal.value != ""){
      final file = File(governmentImagePathOriginal.value);
      int sizeInBytes = file.lengthSync();
      double sizeInMb = sizeInBytes / (1024 * 1024);
      if (sizeInMb > 10){
        var message = validationMessageDetail['max.file'];
        message = message.replaceAll(":attribute", labelTextDetail['government_issued_error'] ?? 'government issue id');
        message = message.replaceAll(":max", '10');
        var err = {
          'title': "government_issued_id",
          'eList' : [message ?? 'Can not upload image size greater than 10MB']
        };
        errors.add(err);
        return;
      }
    }





    try {
      isOverlayLoading(true);
      EditProfileProvider()
          .editUserProfile(
              firstNameTextEditingController.text.trim(),
              lastNameTextEditingController.text,
              dobTextEditingController.text,
              addressTextEditingController.text,
              postalCodeTextEditingController.text,
              miniBioTextEditingController.text,
              gender.value,
              countryId.value,
              stateId.value,
              cityId.value,
              governmentImagePath.value,
              governmentImageName.value,
              governmentImagePathOriginal.value,
              governmentImageNameOriginal.value,
              type.value.toString(),
              serviceController.token,
              serviceController.loginUserDetail['id'],
      serviceController.langId.value,
          oldGovernmentIssuedId.value)
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
          if (resp['errors']['email'] != null) {
            errorList.addAll(resp['errors']['email']);
          }
          if (resp['errors']['dob'] != null) {
            errorList.addAll(resp['errors']['dob']);
          }
          if (resp['errors']['type'] != null) {
            errorList.addAll(resp['errors']['type']);
          }
          if (resp['errors']['gender'] != null) {
            errorList.addAll(resp['errors']['gender']);
          }
          if (resp['errors']['country'] != null) {
            errorList.addAll(resp['errors']['country']);
          }
          if (resp['errors']['state'] != null) {
            errorList.addAll(resp['errors']['state']);
          }
          if (resp['errors']['city'] != null) {
            errorList.addAll(resp['errors']['city']);
          }
          if (resp['errors']['bio'] != null) {
            errorList.addAll(resp['errors']['bio']);
          }
          if (resp['errors']['type'] != null) {
            errorList.addAll(resp['errors']['type']);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          final cont = Get.find<ProfileDetailController>();
          cont.userProfile.addAll(resp['data']['user']);
          serviceController.loginUserDetail['first_name'] = resp['data']['user']['first_name'];
          serviceController.loginUserDetail['last_name'] = resp['data']['user']['last_name'];
          serviceController.loginUserDetail['gender'] = resp['data']['user']['gender'];
          serviceController.loginUserDetail['langId'] = resp['data']['user']['lang_id'];
          serviceController.langId.value = int.parse(resp['data']['user']['lang_id'].toString());
          var getLanguage = serviceController.languages.firstWhereOrNull((element) => element['id'] == serviceController.langId.value);
          if(getLanguage != null){
            serviceController.langIcon.value = getLanguage['flag_icon'];
            serviceController.lang.value = getLanguage['abbreviation'];
          }

          serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));

          print(serviceController.loginUserDetail);

          Get.back();
          serviceController.showDialogue(resp['message'].toString());

          // Get.offNamed("/profile_detail/user/0/0");
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

  scrollError(context, position ,screenHeight){

    position = position * 100.0;
    if (MediaQuery.of(context).viewInsets.bottom > 0) {
      // Keyboard is visible, adjust the scroll to avoid the keyboard
      position -= 100.0; // Adjust as per your requirement
    }

    // Scroll to the calculated position with some margin
    scrollController.animateTo(
      position - screenHeight / 4, // This adjusts the position dynamically
      duration: Duration(milliseconds: 300),
      curve: Curves.easeInOut,
    );
  }

}
