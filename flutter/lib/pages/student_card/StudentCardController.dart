
import 'dart:convert';
import 'dart:io';

import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/student_card/StudentCardProvider.dart';
import 'package:proximaride_app/services/service.dart';


class StudentCardController extends GetxController{

  var isOverlayLoading = false.obs;
  var isLoading = false.obs;

  final serviceController = Get.find<Service>();

  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;

  var studentCardImageName = "".obs;
  var studentCardImagePath = "".obs;
  var oldImagePath = "".obs;
  var studentCardImageNameOriginal = "".obs;
  var studentCardImagePathOriginal = "".obs;
  var studentCardImagePathOriginalOld = "".obs;
  var month = "".obs;
  var day = "".obs;
  var year = "".obs;
  var daysLength = 31.obs;
  var totalYear = 1;
  var startYear = DateTime.now().year;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    await getStudentCard();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }



  getStudentCard() async{
    try{
      isLoading(true);
      StudentCardProvider().getStudentCard(
          serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['user'] != null){
            oldImagePath.value = resp['data']['user']['student_card'] ?? "";

            studentCardImagePathOriginalOld.value = resp['data']['user']['student_card_original_upload'] ?? "";

            if(resp['data']['user']['student_card_exp_date'] != null && resp['data']['user']['student_card_exp_date'] != ""){
              DateTime dateTime = DateTime.parse(resp['data']['user']['student_card_exp_date']);

              day.value = dateTime.day.toString();
              month.value = dateTime.month.toString();
              year.value = dateTime.year.toString();

              if(day.value.length == 1)
                {
                  day.value = "0${day.value}";
                }
              if(month.value.length == 1)
              {
                  month.value = "0${month.value}";
              }
            }
          }

          if(resp['data'] != null && resp['data']['studentCardPage'] != null){
            labelTextDetail.addAll(resp['data']['studentCardPage']);
          }

          if(resp['data'] != null && resp['data']['validationMessages'] != null){
            validationMessageDetail.addAll(resp['data']['validationMessages']);
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


  updateStudentCard() async{
  errors.clear();
    try{
      if(oldImagePath.value == "" && studentCardImageName.value == "" || month.value == "" || year.value == "") {
        if (oldImagePath.value == "" && studentCardImageName.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['photo_error'] ?? 'Student card image');
          var err = {
            'title': "student_card",
            'eList': [message ?? 'Student card image is required']
          };
          errors.add(err);
        }
        if (month.value == "" || year.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['photo_error'] ?? 'Expiration date');
          var err = {
            'title': "student_card_exp_date",
            'eList': [message ?? 'Expiration date is required']
          };
          errors.add(err);
        }
        return;
      }

      if(studentCardImagePathOriginal.value != ""){
        final file = File(studentCardImagePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10){
          var message = validationMessageDetail['max.file'];
          message = message.replaceAll(":attribute", labelTextDetail['photo_error'] ?? 'student card image');
          message = message.replaceAll(":max", '10');
          var err = {
            'title': "student_card",
            'eList' : [message ?? 'Can not upload image size greater than 10MB']
          };
          errors.add(err);
          return;
        }
      }

      isOverlayLoading(true);

      if(month.value == "01")
        {
          day.value = "31";
        }
      if(month.value == "02")
      {
        day.value = "28";
      }
      if(month.value == "03")
      {
        day.value = "31";
      }
      if(month.value == "04")
      {
        day.value = "30";
      }
      if(month.value == "05")
      {
        day.value = "31";
      }
      if(month.value == "06")
      {
        day.value = "30";
      }
      if(month.value == "07")
      {
        day.value = "31";
      }
      if(month.value == "08")
      {
        day.value = "31";
      }
      if(month.value == "09")
      {
        day.value = "30";
      }
      if(month.value == "10")
      {
        day.value = "31";
      }
      if(month.value == "11")
      {
        day.value = "30";
      }
      if(month.value == "12")
      {
        day.value = "31";
      }

      String dateString = '${year.value}-${month.value}-${day.value}';

      DateTime dateTime = DateFormat('yyyy-MM-dd').parse(dateString);

      String formattedDate = DateFormat('yyyy-MM-dd').format(dateTime);



      StudentCardProvider().updateStudentCard(
          studentCardImageName.value,
          studentCardImagePath.value,
          studentCardImageNameOriginal.value,
          studentCardImagePathOriginal.value,
          formattedDate,
          serviceController.token,
          serviceController.loginUserDetail['id']
        ).then((resp) async {
        errorList.clear();

        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){

          if(resp['errors']['student_card'] != null ){
            serviceController.showDialogue(resp['message'].toString());
            var err = {
              'title': "student_card",
              'eList' : resp['errors']['student_card']
            };
            errors.add(err);
          }if(resp['errors']['student_card_exp_date'] != null ){
            var err = {
              'title': "student_card_exp_date",
              'eList' : resp['errors']['student_card_exp_date']
            };
            print(err);
            errors.add(err);
          }
        }else if(resp['status'] != null && resp['status'] == "Success"){
          print(formattedDate);
          serviceController.loginUserDetail['student'] = "1";
          serviceController.loginUserDetail['student_card_exp_date'] = formattedDate;
          serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));

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
      oldImagePath.value = "";
      studentCardImagePath.value = croppedFile.path;
      studentCardImageName.value = croppedFile.path
          .split('/')
          .last;
      studentCardImagePathOriginal.value = serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      studentCardImageNameOriginal.value = serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
      Get.back();
    }

  }
}