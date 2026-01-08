import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/services/service.dart';

import 'MyPhoneNumberProvider.dart';

class MyPhoneNumberController extends GetxController {
  late TextEditingController countryCodeTextEditingController,
      phoneNumberTextEditingController;

  var isOverlayLoading = false.obs;
  var isLoading = false.obs;

  final secureStorage = const FlutterSecureStorage();
  final serviceController = Get.find<Service>();
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;

  var verificationCode = "";
  var numbersList = List.empty(growable: true).obs;
  // var countryCodes = List.empty(growable: true).obs;

  Timer? timer;
  var secondsRemaining = 5.obs;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    countryCodeTextEditingController = TextEditingController();
    phoneNumberTextEditingController = TextEditingController();
    countryCodeTextEditingController.text = "+1";
    getPhoneNumbers();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    countryCodeTextEditingController.dispose();
    phoneNumberTextEditingController.dispose();
  }

  getPhoneNumbers() async {
    try{
      isLoading.value = true;
      await MyPhoneNumberProvider().getPhoneNumbers(serviceController.token, serviceController.langId.value).then((resp) {
        if(resp['status'] != null && resp['status'] == "Success")
          {
            if(resp['data'] != null && resp['data']['phone_numbers'] != null)
              {
                numbersList.addAll(resp['data']['phone_numbers']);

                for(int i=0; i<numbersList.length; i++)
                  {
                    if(numbersList[i]['default'] == "1")
                      {
                        var temp = numbersList[i];
                        numbersList[i] = numbersList[0];
                        numbersList[0] = temp;
                      }
                  }
              }
            if(resp['data'] != null && resp['data']['phoneSettingPage'] != null){
              labelTextDetail.addAll(resp['data']['phoneSettingPage']);
            }

            if(resp['data'] != null && resp['data']['validationMessages'] != null){
              validationMessageDetail.addAll(resp['data']['validationMessages']);
            }
          }
        isLoading.value = false;
      },onError: (error){
        serviceController.showDialogue(error.toString());
        isLoading.value = false;
      });
    }catch(exception){
      serviceController.showDialogue(exception.toString());
      isLoading.value = false;
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

  addNewPhoneNumber() async {

    errors.clear();

    try{

      if(countryCodeTextEditingController.text == "" || phoneNumberTextEditingController.text == ""){

        var err = {
          'title': "phoneNumber",
          'eList' : ['Code and phone number field is required']
        };
        errors.add(err);
        return;
      }
      isOverlayLoading(true);
      MyPhoneNumberProvider()
          .addNewPhoneNumber(serviceController.token,
          "${countryCodeTextEditingController.text.replaceAll(' ', '')}${phoneNumberTextEditingController.text.replaceAll(' ', '')}")
          .then((resp) async {
            if(resp['errors'] != null && resp['errors']['phone'] != null){
              var err = {
                'title': "phoneNumber",
                'eList' : resp['errors']['phone']
              };
              errors.add(err);
            }else if(resp['status'] != null && resp['status'] == "Error"){
              serviceController.showDialogue(resp['message'].toString());
            }
            if(resp['status'] != null && resp['status'] == "Success")
            {
              numbersList.add(resp['data']['phone_number']);
              numbersList.refresh();
              countryCodeTextEditingController.text = "";
              phoneNumberTextEditingController.text = "";
              serviceController.showDialogue(resp['message'].toString());
            }
        isOverlayLoading(false);
      },
      onError: (error){
        serviceController.showDialogue(error.toString());
            isOverlayLoading(false);
      }
      );
    }catch(exception){
      serviceController.showDialogue(exception.toString());
      isOverlayLoading(false);
    }

  }

  sendVerificationCode({phoneId = "0"}) async {

    String phoneNumber = "";
    errors.clear();
    if(phoneId == "0") {
      if(countryCodeTextEditingController.text == "" || phoneNumberTextEditingController.text == ""){
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['phone_error'] ?? 'Code and phone');
        var err = {
          'title': "phoneNumber",
          'eList' : [message ?? 'Code and phone number field is required']
        };
        errors.add(err);
        return;
      }
    }

    if(phoneId == "0") {
      phoneNumber = countryCodeTextEditingController.text+phoneNumberTextEditingController.text;
    } else {
      var result = numbersList.firstWhereOrNull((number) => number['id'].toString() == phoneId.toString());

      if(result != null)
        {
          phoneNumber = result['phone'];
        }
    }

    try {
      isOverlayLoading(true);
      await MyPhoneNumberProvider().sendVerificationCode(serviceController.token, phoneNumber, phoneId).then((resp) {
        isOverlayLoading(false);
        if(resp['status'] != null && resp['status'] == "Success") {
          secondsRemaining.value = 10;
          startTimer();
          Get.toNamed('/phone_number_verification');
        } else if(resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message']?.toString() ?? 'Failed to send verification code');
        } else if(resp['errors'] != null) {
          serviceController.showDialogue(resp['errors'].toString());
        }
      }, onError: (error) {
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());
      });
    } catch(exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  verifyPhoneNumber() async{
    MyPhoneNumberProvider().verifyPhone(serviceController.token, verificationCode).then((resp) async {
      if(resp['status'] != null && resp['status'] == "Success")
        {
          // Clear and refresh the entire list from server to get accurate verification status
          numbersList.clear();
          await getPhoneNumbers();

          phoneNumberTextEditingController.clear();
          countryCodeTextEditingController.clear();
          Get.back();
          serviceController.showDialogue(resp['message'].toString());
        }
      else{
        serviceController.showDialogue(resp['message'].toString());
      }
      isLoading.value = false;
    });

  }

  deletePhoneNumber(phoneId,index) async{
    try{
      isLoading.value = true;
      MyPhoneNumberProvider().deletePhoneNumber(serviceController.token, phoneId).then((resp) {
        if(resp['status'] != null && resp['status'] == "Success")
        {
          numbersList.removeAt(index);
          numbersList.refresh();
          serviceController.showDialogue(resp['message'].toString());
        }
        else{
          serviceController.showDialogue(resp['message'].toString());
        }
        isLoading.value = false;
      },
      onError: (error){
        serviceController.showDialogue(error.toString());
        isLoading.value = false;
      });


    }catch(exception){
      serviceController.showDialogue(exception.toString());
      isLoading.value = false;
    }

  }

  setAsDefaultNumber(phoneNumberId,index) async{
    try {
      isLoading.value = true;
      MyPhoneNumberProvider().setAsDefaultNumber(serviceController.token, phoneNumberId).then((resp) {
        if(resp['status'] != null && resp['status'] == "Success"){

          // numbersList.where((element) => element['default'] == 1).forEach((element) => element['default'] = 0);
          for(var i=0; i<numbersList.length;i++)
            {
              if(numbersList[i]['default'] == "1")
                {
                  numbersList[i]['default'] = "0";
                }
            }
          numbersList[index]['default'] = 1;

          var temp = {};
          temp = numbersList[index];
          numbersList[index] = numbersList[0];
          numbersList[0] = temp;



        }
        isLoading.value = false;

      },
      onError: (error){
        serviceController.showDialogue(error.toString());
        isLoading.value = false;

      });
    }catch(exception){
      serviceController.showDialogue(exception.toString());
      isLoading.value = false;
    }
  }

}
