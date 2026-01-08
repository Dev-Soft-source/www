import 'dart:convert';
import 'dart:io';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/profile_photo/ProfilePhotoProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

class ProfilePhotoController extends GetxController{

  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var profileImagePath = "".obs;
  var profileImageName = "".obs;
  var profileImagePathOld = "".obs;
  var profileImagePathOriginal = "".obs;
  var profileImageNameOriginal = "".obs;
  var profileImagePathOriginalOld = "".obs;
  var errorList = List.empty(growable: true).obs;
  final secureStorage = const FlutterSecureStorage();
  var errors = [].obs;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();

    isLoading(true);
    await getLabelTextDetail();
    isLoading(false);

    profileImagePathOld.value = serviceController.loginUserDetail['profile_image'].toString();
    profileImagePathOriginalOld.value = serviceController.loginUserDetail['profile_original_image'].toString();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    serviceController.loginUserDetail.refresh();
  }

  Future<void> getLabelTextDetail() async {
    try {
      await StageProvider().getLabelTextDetail(serviceController.langId.value, profilePhotoSetting, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['profilePhotoPage'] != null){
            labelTextDetail.addAll(resp['data']['profilePhotoPage']);
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

  uploadUserPhoto() async{
    if(profileImageName.value == ""){
      var message = validationMessageDetail['required'];
      message = message.replaceAll(":Attribute", labelTextDetail['photo_error'] ?? 'Image');
      var err = {
        'title': "image",
        'eList' : [message ?? 'Image is required']
      };
      errors.add(err);
      return;
    }


    final file = File(profileImagePathOriginal.value);
    int sizeInBytes = file.lengthSync();
    double sizeInMb = sizeInBytes / (1024 * 1024);
    if (sizeInMb > 10){
      var message = validationMessageDetail['max'];
      message = message.replaceAll(":attribute", labelTextDetail['photo_error'] ?? 'image');
      message = message.replaceAll(":max", '10');
      var err = {
        'title': "image",
        'eList' : [message ?? 'Can not upload image size greater than 10MB']
      };
      errors.add(err);
      return;
    }

    try{
      errors.clear();

      isOverlayLoading(true);
      ProfilePhotoProvider().profilePhotoUpdate(
          profileImageName.value,
          profileImagePath.value,
          profileImageNameOriginal.value,
          profileImagePathOriginal.value,
          serviceController.token,
          serviceController.loginUserDetail['id']
      ).then((resp) async {
        errorList.clear();

        // if(resp['message'] != null)
        //   {
        //     var err = {
        //       'title': "image",
        //       'eList' : [resp['message'].toString()]
        //     };
        //     errors.add(err);
        //   }

        if(resp['status'] != null && resp['status'] == "Error"){
          var err = {
            'title': "image",
            'eList' : [resp['message'].toString()]
          };
          errors.add(err);
        }else if(resp != null && resp['errors'] != null){
          if(resp['errors']['image'] != null ){

            errorList.addAll(resp['errors']['image']);
            var err = {
              'title': "image",
              'eList' : resp['errors']['image']
            };
            errors.add(err);

          }
        }else if(resp['status'] != null && resp['status'] == "Success"){
          serviceController.loginUserDetail['profile_image'] = resp['data']['user']['profile_image'];
          serviceController.loginUserDetail['profile_original_image'] = resp['data']['user']['profile_original_image'];
          serviceController.loginUserDetail.refresh();
          secureStorage.write(key: "userInfo", value: jsonEncode(serviceController.loginUserDetail));
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
      serviceController.showDialogue(exception.toString());    }
  }

  void getImage(ImageSource imageSource) async {
    final croppedFile = await serviceController.imageCropper(imageSource);

    if (croppedFile != null) {
      profileImagePath.value = croppedFile.path;
      profileImageName.value = croppedFile.path
          .split('/')
          .last;
      profileImagePathOriginal.value = serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      profileImageNameOriginal.value = serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
      Get.back();
    }
  }

}


