import 'dart:async';
import 'package:flutter/cupertino.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/services/service.dart';

import '../../MainProvider.dart';

class SplashController extends GetxController{

  final serviceController = Get.find<Service>();
  final secureStorage = const FlutterSecureStorage();

  @override
  void onInit() async{
    super.onInit();


    Future.delayed(const Duration(seconds: 2), () async{
      var token = await secureStorage.read(key: "token") ?? "";
      if(token != ""){

        if(serviceController.token != "")
        {
          MainProvider().updateStatus(serviceController.token,"1");
        }

        if(serviceController.loginUserDetail['step'].toString() == '1') {
          Get.offAllNamed('/stage_one');
          return;
        }
        else if(serviceController.loginUserDetail['step'].toString() == '2'){
          Get.offAllNamed('/stage_two');
          return;
        }
        else if(serviceController.loginUserDetail['step'].toString() == '3'){
          Get.offAllNamed('/stage_three_vehicle');
          return;
        }
        else if(serviceController.loginUserDetail['step'].toString() == '4'){
          Get.offAllNamed('/stage_four');
          return;
        }
        else{
          Get.offNamed('/navigation');
        }

      }
      else{
        Get.offNamed('/login');
      }
    });
  }

  void dispose(){
    super.dispose();
  }

  getScreenSize(BuildContext context){
    serviceController.height = context.height;
    serviceController.width = context.width;
  }
}
