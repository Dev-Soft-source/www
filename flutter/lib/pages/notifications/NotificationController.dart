import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/post_ride/PostRideProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';

import 'NotificationProvider.dart';

class NotificationController extends GetxController {
  final serviceController = Get.find<Service>();
  var notificationsList = List<dynamic>.empty(growable: true).obs;
  var isLoading = true.obs;
  var isOverlayLoading = false.obs;
  var paymentOptionList = [].obs;
  var paymentOptionToolTipList = [].obs;
  var paymentOptionLabelList =[].obs;
  var bookingOptionList = [].obs;
  var bookingOptionToolTipList = [].obs;
  var bookingOptionLabelList = [].obs;
  var paymentMethod = "".obs;
  var bookingType = "".obs;
  var actionType = "".obs;
  var filter = false.obs;

  var labelTextDetail = {}.obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    // requestPermissionAndGetToken();
    isLoading(true);
    await getNotifications();
    await getLabelTextDetail();
    await getPaymentOptions();
    await getBookingOption();
    isLoading(false);
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }

  // Future<void> requestPermissionAndGetToken() async {
  //   final notificationSettings = await FirebaseMessaging.instance.requestPermission(provisional: true);
  //
  //   if (notificationSettings.authorizationStatus == AuthorizationStatus.authorized) {
  //     // Notification permission granted.
  //     final fcmToken = await FirebaseMessaging.instance.getToken();
  //     print("FCM Token: $fcmToken");
  //     // You can now store or send this token to your application server.
  //   } else {
  //     // Notification permission NOT granted.
  //     print("Notification permission NOT granted");
  //     // You might want to handle this case and request permission again.
  //   }
  //
  //   final fcmToken = await FirebaseMessaging.instance.getToken();
  //
  //
  // }

  Future<void> getNotifications({int type = 0}) async {
    try {

      if(type == 1){
        isOverlayLoading(true);
      }
      notificationsList.clear();
      await NotificationProvider()
          .getNotifications(
        serviceController.token,
          bookingType.value,
          paymentMethod.value,
        serviceController.langId.value
      )
          .then((resp) async {
        if (resp['status'] != null &&
            resp['data'] != null &&
            resp['data']['notifications'] != null) {
          List filteredNotifications = resp['data']['notifications'].where((notification) {
            return notification['is_read'] == "0";
          }).toList();

          notificationsList.addAll(filteredNotifications);
          print(notificationsList.length);

        }
        if(type == 1){
          isOverlayLoading(false);
        }
      }, onError: (error) {
        serviceController.showDialogue(error.toString());
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
      type == 1 ? isOverlayLoading(false) : isLoading(false);
    }
  }

  getSearchNotification() async{
    if(actionType.value == "clear"){
      bookingType.value = "";
      paymentMethod.value = "";
    }

    await getNotifications(type: 1);
  }


  Future<void> readNotification(notificationId) async {
    try{
      await NotificationProvider().readNotification(serviceController.token, notificationId).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          serviceController.notificationCount.value = serviceController.notificationCount.value - 1;
          serviceController.notificationCount.refresh();
        }
      },onError: (error){
        serviceController.showDialogue(error.toString());
      });
    }catch(exception){
      serviceController.showDialogue(exception.toString());

    }

  }

  Future<void> deleteNotification(notificationId) async {
    try{
      isOverlayLoading(true);
      await NotificationProvider().deleteNotification(serviceController.token, notificationId).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          serviceController.notificationCount.value = serviceController.notificationCount.value - 1;
          notificationsList.removeWhere((element) => element['id'].toString() == notificationId.toString());
          serviceController.notificationCount.refresh();
          notificationsList.refresh();
          isOverlayLoading(false);
        }
      },onError: (error){
        serviceController.showDialogue(error.toString());
        isOverlayLoading(false);
      });
    }catch(exception){
      serviceController.showDialogue(exception.toString());
      isOverlayLoading(false);
    }

  }

  getPaymentOptions() async{
    try{
      await PostRideProvider().getPaymentOptions(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['paymentOptions'] != null){
            paymentOptionList.addAll(resp['data']['paymentOptions']);
          }
          if(resp['data'] != null && resp['data']['paymentTooltips'] != null){
            paymentOptionToolTipList.addAll(resp['data']['paymentTooltips']);
          }
          if(resp['data'] != null && resp['data']['paymentLabels'] != null){
            paymentOptionLabelList.addAll(resp['data']['paymentLabels']);
          }
        }
      },onError: (error){
        isLoading(false);
        serviceController.showDialogue(error.toString());      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());    }
  }

  getBookingOption() async{
    try{
      await PostRideProvider().getBookingOption(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['bookingOptions'] != null){
            bookingOptionList.addAll(resp['data']['bookingOptions']);
          }
          if(resp['data'] != null && resp['data']['bookingTooltips'] != null){
            bookingOptionToolTipList.addAll(resp['data']['bookingTooltips']);
          }

          if(resp['data'] != null && resp['data']['bookingLabels'] != null){
            bookingOptionLabelList.addAll(resp['data']['bookingLabels']);
          }
        }
      },onError: (error){
        isLoading(false);
        serviceController.showDialogue(error.toString());
      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());    }
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
