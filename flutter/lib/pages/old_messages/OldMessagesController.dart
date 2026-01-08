
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/old_messages/OldMessagesProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

class OldMessagesController extends GetxController{


  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var oldMessagesList = List<dynamic>.empty(growable: true).obs;
  var chatId = "";
  var userId;
  var labelTextDetail = {}.obs;




  @override
  void onInit() async{
    userId = serviceController.loginUserDetail['id'];
    super.onInit();
    isLoading(true);
    await getOldMessages();
    await getLabelTextDetail();
    isLoading(false);
  }
  @override
  void onClose() {
    super.onClose();
  }


  Future<void> getOldMessages() async {
    try {
      await OldMessagesProvider().getOldChats(
        serviceController.token,
      )
          .then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data']!= null && resp['data']['chats']!= null){
            oldMessagesList.addAll(resp['data']['chats']);
          }
        }
      }, onError: (err) {
      serviceController.showDialogue(err.toString());
      isLoading(false);
      });

    } catch (exception) {
      serviceController.showDialogue(exception.toString());

      isLoading(false);
    }
  }

  Future<void> getLabelTextDetail() async {
    try {
      await StageProvider().getLabelTextDetail(serviceController.langId.value, chatPage, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['chatsPage'] != null){
            labelTextDetail.addAll(resp['data']['chatsPage']);
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



}