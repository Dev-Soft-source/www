
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/pages/profile_detail/ProfileDetailProvider.dart';
import 'package:proximaride_app/services/service.dart';

class ProfileDetailController extends GetxController{
  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var showReply = false.obs;
  var passengerDriven = 0.obs;
  var rideTaken = 0.obs;
  var kmShared = 0.obs;
  var totalReviews = 0.obs;
  late TextEditingController replyTextController;
  var reviews = List<dynamic>.empty(growable: true).obs;
  var profileType = Get.parameters['type'];
  var profileId = Get.parameters['id'];
  var pageType = Get.parameters['pageType'];
  var pageLimit = 3;
  Map<String, dynamic> replies = {};
  var userProfile = {}.obs;
  var driverTitle = "Driver Info".obs;

  var labelTextDetail = {}.obs;

  var ride = {}.obs;
@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    if(profileId == "" || profileId == "0" ){
      profileId = serviceController.loginUserDetail['id'].toString();
    }
    replyTextController = TextEditingController();

    if(profileId != "0" && profileType == "driver"){
      await getDriverProfileDetail();
    }else if(profileId != "0" && profileType == "passenger"){
      await getPassengerProfileDetail();
    }else{
      await getMyProfileDetail();
    }


  }
  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    replyTextController.dispose();
  }


  getMyProfileDetail() async{
    try{
      isLoading(true);
      ProfileDetailProvider().getMyProfileDetail(
          pageLimit,
          serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
         if(resp['status'] != null && resp['status'] == "Success"){
           if(resp['data'] != null && resp['data']['user'] != null){
             userProfile.addAll(resp['data']['user']);
           }

           if(resp['data'] != null && resp['data']['editProfilePage'] != null){
             labelTextDetail.addAll(resp['data']['editProfilePage']);
           }

           if(resp['data'] != null && resp['data']['ratings'] != null) {
             reviews.addAll(resp['data']['ratings']);

             for (int i = 0; i < reviews.length; i++) {
               var reply = reviews[i]['replies'];
               if (reply != null) {
                 replies[reviews[i]['id'].toString()] = reply;
               }
             }
           }
           if(resp['data'] != null && resp['data']['passenger_driven'] != null){
             passengerDriven.value = int.parse(resp['data']['passenger_driven'].toString());
           }
           if(resp['data'] != null && resp['data']['rides_taken'] != null){
             rideTaken.value = int.parse(resp['data']['rides_taken'].toString());
           }
           if(resp['data'] != null && resp['data']['km_shared'] != null){
             kmShared.value = int.parse(resp['data']['km_shared'].toString());
           }
           if(resp['data'] != null && resp['data']['total_reviews'] != null){
             totalReviews.value = resp['data']['total_reviews'];
           }
         }
         isLoading(false);
      },onError: (err){
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  addReply(ratingId,reply) async{
    try{
      isOverlayLoading(true);
      ProfileDetailProvider().addReply(
        ratingId,reply,serviceController.token
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          reviews.clear();
          getMyProfileDetail();
        }
        isOverlayLoading(false);
      },onError: (err){
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString());
      });
    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  getDriverProfileDetail() async{
    try{
      isLoading(true);
      ProfileDetailProvider().getDriverProfileDetail(
          profileId,
          pageLimit,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){

          if(resp['data'] != null && resp['data']['ride'] != null){
            ride.addAll(resp['data']['ride']);
            driverTitle.value = resp['data']['ride']['driver']['first_name'] + "'s profile";

            passengerDriven.value = int.parse(resp['data']['ride']['driver']['passenger_driven'].toString());

            totalReviews(resp['data']['total_reviews']);
          }

          if(resp['data'] != null && resp['data']['editProfilePage'] != null){
            labelTextDetail.addAll(resp['data']['editProfilePage']);
          }

          if(resp['data'] != null && resp['data']['total_reviews'] != null){
            totalReviews(resp['data']['total_reviews']);
          }

          if(resp['data'] != null && resp['data']['ratings'] != null) {
            reviews.addAll(resp['data']['ratings']);
          }
        }
        isLoading(false);
      },onError: (err){
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  getPassengerProfileDetail() async{
    try{
      isLoading(true);
      ProfileDetailProvider().getPassengerProfileDetail(
          profileId,
          pageLimit,
          serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['user'] != null){
            userProfile.addAll(resp['data']['user']);
          }
          if(resp['data'] != null && resp['data']['ratings'] != null) {
            reviews.addAll(resp['data']['ratings']);

            for (int i = 0; i < reviews.length; i++) {
              var reply = reviews[i]['replies'];
              if (reply != null) {
                replies[reviews[i]['id'].toString()] = reply;
              }
            }
          }

          if(resp['data'] != null && resp['data']['editProfilePage'] != null){
            labelTextDetail.addAll(resp['data']['editProfilePage']);
          }

          if(resp['data'] != null && resp['data']['passenger_driven'] != null){
            passengerDriven.value = int.parse(resp['data']['passenger_driven'].toString());
          }
          if(resp['data'] != null && resp['data']['rides_taken'] != null){
            rideTaken.value = int.parse(resp['data']['rides_taken'].toString());
          }
          if(resp['data'] != null && resp['data']['km_shared'] != null){
            kmShared.value = int.parse(resp['data']['km_shared'].toString());
          }
          if(resp['data'] != null && resp['data']['total_reviews'] != null){
            totalReviews.value = resp['data']['total_reviews'];
          }
        }
        isLoading(false);
      },onError: (err){
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }


}