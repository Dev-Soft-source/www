import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/contact_us/ContactUsProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

class ContactUsController extends GetxController{


  late TextEditingController
  nameTextEditingController,
  emailTextEditingController,
  messageTextEditingController,
  phoneTextEditingController;

  final Map<String, FocusNode> focusNodes = {};

  var isOverlayLoading = false.obs;
  var isLoading = false.obs;

  final serviceController = Get.find<Service>();

  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;
  var isLoggedInWithEmail = false.obs;


@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();

    isLoading(true);
    await getLabelTextDetail();
    isLoading(false);

    nameTextEditingController = TextEditingController();
    emailTextEditingController = TextEditingController();
    messageTextEditingController = TextEditingController();
    phoneTextEditingController = TextEditingController();

    // Pre-fill email for logged-in users
    if (serviceController.loginUserDetail['email'] != null &&
        serviceController.loginUserDetail['email'].toString().isNotEmpty) {
      emailTextEditingController.text = serviceController.loginUserDetail['email'].toString();
      isLoggedInWithEmail.value = true;
    }

    for (int i = 1; i <= 9; i++) {
      focusNodes[i.toString()] = FocusNode();
      // Attach the onFocusChange listener
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus, trigger validation
          if (i == 1) {
            validateField('name', nameTextEditingController.text);
          } else if (i == 2) {
            validateField('email', emailTextEditingController.text,type: 'email');
          } else if (i == 4) {
            validateField('message', messageTextEditingController.text);
          }
        }
      });
    }



}

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    // nameTextEditingController.dispose();
    // emailTextEditingController.dispose();
    // messageTextEditingController.dispose();
    // phoneTextEditingController.dispose();
  }

  Future<void> getLabelTextDetail() async {
    try {
      await StageProvider().getLabelTextDetail(serviceController.langId.value, contactUsSetting, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['contactPage'] != null){
            labelTextDetail.addAll(resp['data']['contactPage']);
          }

          if(resp['data'] != null && resp['data']['validationMessages'] != null){
            validationMessageDetail.addAll(resp['data']['validationMessages']);
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

  void validateField(String fieldName, String fieldValue, {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      message = message.replaceAll(":Attribute", fieldName);
      errorList.add(message);
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
      return;
    }

    switch (type) {
      case 'email':
        if (!isValidEmail(fieldValue)) {
          var message = validationMessageDetail['email'];
          errorList.add(message ?? 'Please use a valid email address, like: example@email.com');
        }
        break;
      case 'numeric':
        if (fieldValue.isNotEmpty && double.tryParse(fieldValue) == null) {
          var message = validationMessageDetail['email'];
          errorList.add('$fieldName must be a number');
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
  bool isValidEmail(String email) {
    // Regular expression for email validation
    final RegExp emailRegExp = RegExp(r'^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$');
    return emailRegExp.hasMatch(email);
  }


  storeContactUs() async{
    try{
      if(nameTextEditingController.text.isEmpty ||
          emailTextEditingController.text.isEmpty ||
          phoneTextEditingController.text.isEmpty ||
          messageTextEditingController.text.isEmpty){
        if(nameTextEditingController.text.isEmpty){
          var err = {
            'title': "name",
            'eList' : ['Name field is required']
          };
          errors.add(err);
        }
        if(emailTextEditingController.text.isEmpty){
          var err = {
            'title': "email",
            'eList' : ['E-mail field is required']
          };
          errors.add(err);
        }

        if(messageTextEditingController.text.isEmpty){
          var err = {
            'title': "message",
            'eList' : ['Message field is required']
          };
          errors.add(err);
        }
        return;
      }

      isOverlayLoading(true);
      ContactUsProvider().storeContactUs(
          nameTextEditingController.text.trim(),
          emailTextEditingController.text,
          phoneTextEditingController.text,
          messageTextEditingController.text,
          serviceController.token
        ).then((resp) async {
        errorList.clear();

        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['name'] != null ){
            errorList.addAll(resp['errors']['name']);
          }if(resp['errors']['email'] != null ){
            errorList.addAll(resp['errors']['email']);
          }if(resp['errors']['message'] != null ){
            errorList.addAll(resp['errors']['message']);
          }
        }
        else if(resp['status'] != null && resp['status'] == "Success"){
          Get.back();
          serviceController.showDialogue(resp['message'].toString());
        }
        isOverlayLoading(false);
      },onError: (error){
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());

      });
    
    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }
}