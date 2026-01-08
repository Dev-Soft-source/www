import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/pages/navigation/navigationProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';
class NavigationController extends GetxController{


  var currentNavIndex = 0.obs;
  var closedApp = 0.obs;
  final serviceController = Get.find<Service>();

@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    if(serviceController.openDeepLinkPage.value == true){
      Future.delayed(Duration(seconds: 2), () {
        serviceController.openDeepLinkPage.value = false;
        Get.toNamed("/deep_trip_detail");
      });

    }

    await logoutAdminDeActiveAccount();
    requestPermissionAndGetToken();
    currentNavIndex.value = serviceController.navigationIndex.value;
    if(serviceController.backgroundNotification == "chatNotification" )
    {
      // Navigate to specific chat from notification
      await Future.delayed(const Duration(seconds: 1));
      final rideId = serviceController.pendingChatRideId;
      final userId = serviceController.pendingChatUserId;
      // Clear the pending notification data
      serviceController.backgroundNotification = "";
      serviceController.pendingChatRideId = "";
      serviceController.pendingChatUserId = "";
      // Navigate to messaging page with the chat details
      Get.toNamed('/messaging_page/$userId/$rideId/new');
    }
    else if(serviceController.backgroundNotification == "backgroundNotification" )
    {
      await Future.delayed(const Duration(seconds: 1));
      serviceController.backgroundNotification = "";
      Get.toNamed('/notifications');
    }
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }

  Future<void> requestPermissionAndGetToken() async {
    final notificationSettings = await FirebaseMessaging.instance.requestPermission(provisional: true, sound: false,alert: true,announcement: true, badge: true, );

    if (notificationSettings.authorizationStatus == AuthorizationStatus.authorized) {
      final fcmToken = await FirebaseMessaging.instance.getToken();
      NavigationProvider().updateUserFcmToken(
            serviceController.token,
            fcmToken
        ).then((resp) async {
          print(resp);
          serviceController.notificationCount.value = int.parse(resp['data']['notificationCount'].toString());
        },onError: (err){
          // serviceController.showDialogue(err.toString());
        });
    } else {
    }
  }

  logoutAdminDeActiveAccount() async{
    try {

      await StageProvider().logoutAdminDeActiveAccount(
        serviceController.token,
      ).then((resp) async {
        if (resp['data'] != null && resp['data']['status'] == "1") {
          serviceController.secureStorage.deleteAll();
          Get.offAllNamed('/login');
          if(resp['data']['message'] != null){
            serviceController.showDialogue(resp['data']['message'].toString());
          }

        }
      }, onError: (error) {
        serviceController.showDialogue(error.toString());
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }
}