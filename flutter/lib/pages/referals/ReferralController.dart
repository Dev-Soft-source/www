import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/pages/referals/ReferralProvider.dart';
import 'package:proximaride_app/services/service.dart';

class ReferralController extends GetxController with GetTickerProviderStateMixin{
  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var errors = [].obs;

  var referralLink = "".obs;

  var myReferralList = List<dynamic>.empty(growable: true).obs;
  ScrollController myReferralScrollController = ScrollController();

  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;


  @override
  void onInit() async {
    await getMyReferralData();
    // TODO: implement onInit
    super.onInit();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }


  getMyReferralData() async{
    try{
      isLoading(true);
      await ReferralProvider().getMyReferralData(
          serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['myReferralList'] != null){
            myReferralList.addAll(resp['data']['myReferralList']);
          }
          if(resp['data'] != null && resp['data']['referralLink'] != null){
            referralLink.value = resp['data']['referralLink'].toString();
          }
          if(resp['data'] != null && resp['data']['referralSettingPage'] != null){
            labelTextDetail.addAll(resp['data']['referralSettingPage']);
          }
          if(resp['data'] != null && resp['data']['messages'] != null){
            popupTextDetail.addAll(resp['data']['messages']);
          }
        }
        isLoading(false);
      },onError: (err){
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }
}
