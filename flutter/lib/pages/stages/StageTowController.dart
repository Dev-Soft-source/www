import 'dart:convert';
import 'dart:io';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

class StageTowController extends GetxController {
  var isOverlayLoading = false.obs;
  var isLoading = false.obs;
  final serviceController = Get.find<Service>();
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  final secureStorage = const FlutterSecureStorage();
  var stepNo = "0".obs;



  var profileImagePath = "".obs;
  var profileImageName = "".obs;
  var profileImagePathOriginal = "".obs;
  var profileImageNameOriginal = "".obs;

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
      await StageProvider().getLabelTextDetail(serviceController.langId.value, step2Page, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['step2Page'] != null){
            labelTextDetail.addAll(resp['data']['step2Page']);
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
      if (stepNo.value == "2") {
        profileImagePath.value = croppedFile.path;
        profileImageName.value = croppedFile.path
            .split('/')
            .last;
        profileImagePathOriginal.value = serviceController.originalImagePath.value;
        serviceController.originalImagePath.value = "";
        profileImageNameOriginal.value = serviceController.originalImageName.value;
        serviceController.originalImageName.value = "";
      }
      Get.back();
    }
  }


  setStageTwo(skip) async {
    errors.clear();

    if(profileImageName.value == ""){
      skip = true;
    }
    if (skip == false) {
      if (profileImageName.value == "") {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute", labelTextDetail['photo_error'] ?? 'Image');
        var err = {
          'title': "image",
          'eList': [message ?? 'Image is required']
        };
        errors.add(err);
        return;
      }

      if(profileImagePathOriginal.value != ""){
        final file = File(profileImagePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10){
          var message = validationMessageDetail['max.file'];
          message = message.replaceAll(":attribute", labelTextDetail['photo_error'] ?? 'image');
          message = message.replaceAll(":max", '10');
          var err = {
            'title': "image",
            'eList' : [message ?? 'Can not upload image size greater than 10MB']
          };
          errors.add(err);
          return;
        }
      }

    }
    try {
      isOverlayLoading(true);
      StageProvider()
          .setStageTwo(
        profileImageName.value,
        profileImagePath.value,
        profileImageNameOriginal.value,
        profileImagePathOriginal.value,
        serviceController.token,
        skip == true ? "1" : "0",
      )
          .then((resp) async {
        errorList.clear();
        // if (resp['message'] != null) {
        //   serviceController.showDialogue("${resp['message']}1");
        // }

        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue("${resp['message']}1");
        } else if (resp != null && resp['errors'] != null) {
          serviceController.showDialogue("${resp['message']}");
          if (resp['errors']['image'] != null) {
            var err = {
              'title': "image",
              'eList' : resp['errors']['image']
            };
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.loginUserDetail['profile_image'] =
              resp['data']['profile_image'];
          serviceController.loginUserDetail['profile_original_image'] =
              resp['data']['profile_original_image'].toString();
          serviceController.loginUserDetail['step'] = "3";
          serviceController.loginUserDetail.refresh();
          serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));
          stepNo.value = serviceController.loginUserDetail['step'].toString();

          profileImageName.value = "";
          profileImagePath.value = "";
          profileImageNameOriginal.value = "";
          profileImagePathOriginal.value = "";

          Get.offAllNamed('/stage_three_vehicle');
          if(skip == false)
            {
              // serviceController.showDialogue(resp['message'].toString());
            }

        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);

serviceController.showDialogue("${err}3");

      });
    } catch (exception) {
      isOverlayLoading(false);

        serviceController.showDialogue(exception.toString());

    }
  }

}
