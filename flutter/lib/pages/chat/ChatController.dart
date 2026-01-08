
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';
import 'ChatProvider.dart';


class ChatController extends GetxController{


  final serviceController = Get.find<Service>();
  var isLoading = true.obs;
  var isOverlayLoading = false.obs;
  var myChats = List<dynamic>.empty(growable: true).obs;
  var chatId = "";
  var userId;
  var labelTextDetail = {}.obs;


  @override
  void onInit() async{
    // TODO: implement onInit
    userId = serviceController.loginUserDetail['id'];
    super.onInit();
    isLoading(true);
    await getChats();
    await getLabelTextDetail();
    isLoading(false);

  }
  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }


  void getSelectedChatId(){
    chatId = Get.parameters['Id'] ?? "";
  }

  Future<void> getChats() async {
    try {
      myChats.clear();
      await ChatProvider().getChats(
        serviceController.token,
      )
          .then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data']!= null && resp['data']['chats']!= null){
            myChats.addAll(resp['data']['chats']);
          }

          if(resp['data']!= null && resp['data']['languages']!= null){
            serviceController.languages.clear();
            serviceController.languages.addAll(resp['data']['languages']);

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
      await StageProvider().getLabelTextDetail(serviceController.langId.value, chatPage, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['chatsPage'] != null){
            labelTextDetail.addAll(resp['data']['chatsPage']);

            serviceController.imagePreviewLabel.value = labelTextDetail['image_preview'] ?? 'Image Preview';
          }

          serviceController.navigationChatLabel.value = labelTextDetail['navigation_chat_label'] ?? "Chats";
          serviceController.navigationMyTripLabel.value = labelTextDetail['navigation_my_trip_label'] ?? "My trips";
          serviceController.navigationMyProfileLabel.value = labelTextDetail['navigation_my_profile_label'] ?? "My profile";

          if(resp['data'] != null && resp['data']['messages'] != null){
            serviceController.closeBtnLabel.value = resp['data']['messages']['popup_close_btn_text'].toString();
          }


          var getLanguage = serviceController.languages.firstWhereOrNull((element) => element['id'] == serviceController.langId.value);
          if(getLanguage != null){
            serviceController.langIcon.value = getLanguage['flag_icon'];
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

  deleteChat(chatObj) async{
    bool isConfirmed = await serviceController.showConfirmationDialog(labelTextDetail['delete_messages_label'] ?? "Are you sure you want to delete this chat?");
    if(isConfirmed == false){
      return;
    }
    try {
      isOverlayLoading(true);
      await ChatProvider().deleteChat(serviceController.langId.value, chatObj, serviceController.token).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          myChats.removeWhere((element) => element['receiver'] == chatObj['receiver'] && element['sender'] == chatObj['sender']);
          myChats.refresh();
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



}