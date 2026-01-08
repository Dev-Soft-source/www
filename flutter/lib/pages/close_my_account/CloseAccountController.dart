import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/close_my_account/CloseAccountProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

import '../navigation/navigationProvider.dart';

class CloseAccountController extends GetxController {
  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var errorList = List.empty(growable: true).obs;
  final errors = [].obs;
  ScrollController scrollController = ScrollController();
  var removeCarPhoto = false.obs;
  var wouldRecommend = 3.obs;
  var closingAccount = false.obs;
  late TextEditingController txtController1, txtController2;

  var selectedReasons = ['Prefer not to say'].obs;

  var reasonValue = 0.obs;
  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();

    isLoading(true);
    await getLabelTextDetail();
    isLoading(false);
    txtController1 = TextEditingController();
    txtController2 = TextEditingController();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    scrollController.dispose();
    txtController1.dispose();
    txtController2.dispose();
  }


  Future<void> getLabelTextDetail() async {
    try {
      await StageProvider().getLabelTextDetail(serviceController.langId.value, closeMyAccountSetting, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['CloseAccountPage'] != null){
            labelTextDetail.addAll(resp['data']['CloseAccountPage']);
          }

          if(resp['data'] != null && resp['data']['messages'] != null){
            popupTextDetail.addAll(resp['data']['messages']);
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

  removeAccount() async {
    var recommend = '';
    errors.clear();

    if (selectedReasons.isEmpty ||
        closingAccount.value == false ||
        (wouldRecommend.value < 1 || wouldRecommend.value > 3)) {
      if (selectedReasons.isEmpty) {
        var err = {
          'title': "reasons",
          'eList': [popupTextDetail['select_reason'] ?? 'Please select 1 or more reason(s)']
        };
        errors.add(err);
      }

      if (wouldRecommend.value == 1) {
        recommend = 'No';
      } else if (wouldRecommend.value == 2) {
        recommend = 'Yes';
      } else if (wouldRecommend.value == 3) {
        recommend = 'Prefer not to say';
      } else {
        var err = {
          'title': "recommend",
          'eList': [popupTextDetail['select_recommend'] ?? 'Please select if you recommend us']
        };
        errors.add(err);
      }

      if (closingAccount.value == false) {
        var err = {
          'title': "check",
          'eList': [popupTextDetail['check_box'] ?? 'Check the box']
        };
        errors.add(err);
      }
      return;
    }

    bool isConfirmed = await serviceController.showConfirmationDialog("Are you sure you want to close your account?");

    if (isConfirmed) {
      try {
        CloseAccountProvider()
            .closeAccount(serviceController.token, selectedReasons, recommend,
            txtController1.text, txtController2, closingAccount.value)
            .then((resp) async {
          errorList.clear();
          errors.clear();
          if (resp['status'] != null && resp['status'] == "Error") {
            serviceController.showDialogue(resp['message'].toString());

          } else if (resp['errors'] != null) {
            if (resp['errors']['reasons'] != null) {
              var err = {
                'title': "reasons",
                'eList' : resp['errors']['reasons']
              };
              errors.add(err);
            }
            if (resp['errors']['close_account'] != null) {
              var err = {
                'title': "check",
                'eList' : resp['errors']['close_account']
              };
              errors.add(err);
            }
          }

          if (resp['status'] != null) {
            if (resp['status'] == 'Success') {
              serviceController.secureStorage.deleteAll();
              NavigationProvider().removeFcmToken(
                  serviceController.token
              ).then((resp) async {
                isOverlayLoading(false);
                Get.offAllNamed('/thank_you/close_account');
              });
              serviceController.showDialogue(resp['message'].toString());

            }
          }
        }, onError: (error) {
          isOverlayLoading(false);
          serviceController.showDialogue(error.toString());

        });


      } catch (exception) {
        serviceController.showDialogue(exception.toString());

      }
    }

  }
}
