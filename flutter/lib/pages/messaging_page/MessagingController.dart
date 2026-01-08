
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/pages/chat/ChatController.dart';
import 'package:proximaride_app/pages/messaging_page/MessagingProvider.dart';
import 'package:proximaride_app/services/service.dart';

class MessagingController extends GetxController {
  final serviceController = Get.find<Service>();

  var isLoading = true.obs;
  var userId;
  var rideId;
  var type;

  var chatUserInfo = {}.obs;
  var messagesList = List<dynamic>.empty(growable: true).obs;
  late TextEditingController typedMessageController;
  var messageLength = 0.obs;

  var isOverlayLoading = false.obs;

  var chatUserId = "";

  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    chatUserId = Get.parameters['userId'] ?? "";
    rideId = Get.parameters['rideId'] ?? "";
    type = Get.parameters['type'] ?? "";
    userId = serviceController.loginUserDetail['id'];

    typedMessageController = TextEditingController();
    await getMessages();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    typedMessageController.dispose();
  }

  Future<void> getMessages() async {
    try {
      isLoading(true);
      await MessagingProvider()
          .getMessages(serviceController.token, chatUserId,rideId, type)
          .then((resp) async {
        if (resp['status'] != null && resp['data'] != null && resp['data']['messages'] != null) {

          bool isRegistered = Get.isRegistered<ChatController>();

          if(isRegistered)
            {
              var chatController = Get.find<ChatController>();

              for(int i=0;i<chatController.myChats.length;i++)
                {
                  if(chatController.myChats[i]['sender']['id'].toString() == serviceController.loginUserDetail['id'].toString())
                  {
                    if(chatController.myChats[i]['receiver']['id'].toString() == userId.toString())
                    {
                      chatController.myChats[i]['unread_count'] = 0;
                    }
                  }
                  else{
                    if(chatController.myChats[i]['receiver']['id'].toString() == userId.toString())
                    {
                      chatController.myChats[i]['unread_count'] = 0;
                    }
                  }
                }
              chatController.myChats.refresh();

            }
          chatUserInfo.addAll(resp['data']['user']);
          messagesList.addAll(resp['data']['messages']);

          if(resp['data'] != null && resp['data']['chatsPage'] != null){
            labelTextDetail.addAll(resp['data']['chatsPage']);
          }
          if(resp['data'] != null && resp['data']['messageSetting'] != null){
            popupTextDetail.addAll(resp['data']['messageSetting']);
          }


        }
      }, onError: (error) {
        serviceController.showDialogue(error.toString());

      });
      isLoading(false);
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
      isLoading(false);
    }
  }

  Future sendMessage() async{

    if(typedMessageController.text == "")
    {
      return;
    }

    final messageText = typedMessageController.text.trim();
    if (isURL(messageText)) {
      typedMessageController.text = "";
      serviceController.showDialogue("${popupTextDetail['url_not_allowed_message'] ?? "URLs are not allowed"}");
      return;
    } else if (isEmail(messageText)) {
      typedMessageController.text = "";
      serviceController.showDialogue("${popupTextDetail['email_not_allowed_message'] ?? "Emails are not allowed"}");
      return;
    } else if (isPhoneNumber(messageText)) {
      typedMessageController.text = "";
      serviceController.showDialogue("${popupTextDetail['phone_number_not_allowed_message'] ?? "Phone numbers are not allowed"}");
      return;
    }

    try{
      isOverlayLoading(true);
      MessagingProvider().sendNewMessage(serviceController.token, rideId.toString(), chatUserId.toString(), typedMessageController.text).then((resp) async{
        if(resp['data'] == null && resp['message'] != null){
          serviceController.showDialogue(resp['message']);
        }else if(resp["data"] != null){
          messagesList.add(resp["data"]);
          messagesList.refresh();
          typedMessageController.clear();
          bool isRegistered = Get.isRegistered<ChatController>();
          if(isRegistered == true){
            var chatController = Get.find<ChatController>();
            await chatController.getChats();
          }
          else{
          }
        }

        isOverlayLoading(false);

      }, onError: (error){
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());
      });
    }catch(exception){
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  bool isURL(String text) {
    final urlRegex = RegExp(
      r'(https?:\/\/[^\s]+)',
      caseSensitive: false,
    );
    return urlRegex.hasMatch(text);
  }

  bool isEmail(String text) {
    final emailRegex = RegExp(
      r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}',
    );
    return emailRegex.hasMatch(text);
  }

  bool isPhoneNumber(String text) {
    final phoneRegex = RegExp(
      r'(\+?\d{1,4}[\s-]?)?(\(?\d{3,4}\)?[\s-]?)?[\d\s-]{7,10}$',
    );
    return phoneRegex.hasMatch(text);
  }

}
