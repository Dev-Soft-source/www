import 'dart:async';
import 'dart:convert';
import 'dart:ffi';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/main.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/pages/my_phone_number/MyPhoneNumberProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

class StageFourController extends GetxController {
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

  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  late TextEditingController
      countryCodeTextEditingController,
      phoneNumberTextEditingController;

  var verificationCode = "";

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

    countryCodeTextEditingController = TextEditingController();
    phoneNumberTextEditingController = TextEditingController();


    countryCodeTextEditingController.text = "+1";

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
      await StageProvider().getLabelTextDetail(serviceController.langId.value, step4Page, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['step4Page'] != null){
            labelTextDetail.addAll(resp['data']['step4Page']);
          }

          if(resp['data'] != null && resp['data']['messages'] != null){
            popupTextDetail.addAll(resp['data']['messages']);
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



  void startTimer() {
    timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (secondsRemaining.value == 0) {
        timer.cancel();
      }
      if (secondsRemaining.value > 0) {
        secondsRemaining.value--;
      }
    });
  }


  setStageFour(skip) async {
    errors.clear();
    try {

      if(skip == false)
      {
        if (countryCodeTextEditingController.text == "" ||
            phoneNumberTextEditingController.text == "") {
          if(countryCodeTextEditingController.text == "")
            {
              var message = validationMessageDetail['required'];
              message = message.replaceAll(":Attribute", labelTextDetail['country_code_error'] ?? 'Code');
              var err = {
                'title': "code",
                'eList': [message ?? 'Code is required']
              };
              errors.add(err);
            }
          if(phoneNumberTextEditingController.text == ""){
            var message = validationMessageDetail['required'];
            message = message.replaceAll(":Attribute", labelTextDetail['phone_error'] ?? 'Phone number');
            var err = {
              'title': "number",
              'eList': [message ?? 'Number is required']
            };
            errors.add(err);
          }

          return;
        }
      }
      isOverlayLoading(true);
      StageProvider()
          .setStageFour(
              "${countryCodeTextEditingController.text} ${phoneNumberTextEditingController.text}",
              serviceController.token,skip == true ? "1" : "0")
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.loginUserDetail['step'] = "5";
          serviceController.loginUserDetail.refresh();
          serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));
          stepNo.value = serviceController.loginUserDetail['step'].toString();
          countryCodeTextEditingController.text = "";
          phoneNumberTextEditingController.text = "";

          Get.offAllNamed('/navigation');
          if(skip == false)
            {
              // serviceController.showDialogue(resp['message'].toString());
            }
        }
        isOverlayLoading(false);
      }, onError: (error) {
        serviceController.showDialogue(error.toString());
        isOverlayLoading(false);
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
      isOverlayLoading(false);
    }
  }

  sendVerificationCode({phoneId = "0"}){

    String phoneNumber = "";
    errors.clear();
    if(phoneId == "0") {
      if(countryCodeTextEditingController.text == "" || phoneNumberTextEditingController.text == ""){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['phone_error'] ?? 'Code and phone number');
        var err = {
          'title': "number",
          'eList' : [message ?? 'Code and phone number field is required']
        };
        errors.add(err);
        return;
      }
    }
    phoneNumber = countryCodeTextEditingController.text + phoneNumberTextEditingController.text;
    MyPhoneNumberProvider().sendVerificationCode(serviceController.token, phoneNumber, phoneId);
    secondsRemaining.value = 10;
    startTimer();
    finishBtn.value = true;
    //Get.toNamed('/phone_number_verification');
  }

  verifyPhoneNumber() async{

    if(verificationCode == ""){
      serviceController.showDialogue("${popupTextDetail['enter_code_message'] ?? "Please enter code first"}");
      return;
    }

    isOverlayLoading(true);
    MyPhoneNumberProvider().verifyPhone(serviceController.token, verificationCode).then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        serviceController.loginUserDetail['step'] = "5";
        serviceController.loginUserDetail.refresh();
        serviceController.secureStorage.write(
            key: "userInfo",
            value: jsonEncode(serviceController.loginUserDetail));
        stepNo.value = serviceController.loginUserDetail['step'].toString();
        verificationCode = "";

        Get.offAllNamed('/navigation');
        isOverlayLoading(false);
      }
      else{
        serviceController.showDialogue(resp['message'].toString());
        isOverlayLoading(false);
      }
      isLoading.value = false;
    });

  }

}
