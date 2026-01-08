import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/pages/my_trips/MyTripProvider.dart';
import 'package:proximaride_app/pages/notifications/NotificationController.dart';
import 'package:proximaride_app/pages/trip_detail/TripDetailProvider.dart';
import 'package:proximaride_app/services/service.dart';

class NotificationAddReviewController extends GetxController{


  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var ride = {}.obs;
  var cancelRideInfo = {}.obs;
  late TextEditingController reviewTextEditingController, tripCancelTextEditingController;
  var vehicleCondition = 0.0.obs;
  var conscious = 0.0.obs;
  var comfort = 0.0.obs;
  var communication = 0.0.obs;
  var attitude = 0.0.obs;
  var hygiene = 0.0.obs;
  var respect = 0.0.obs;
  var safety = 0.0.obs;
  var timeliness = 0.0.obs;
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  var removePassengerType = "0".obs;
  var reviewPassengerImage = "".obs;
  var reviewPassengerName = "".obs;
  var passengerBookingId = "".obs;

  var reviewType = "";
  var rideId = "";
  var rideDetailId = "";
  var notificationId = "";
  var pageType = "";

  var labelTextDetail = {}.obs;



@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    reviewTextEditingController = TextEditingController();
    tripCancelTextEditingController = TextEditingController();

    reviewType = Get.parameters['reviewType'].toString();
    rideId = Get.parameters['rideId'].toString();
    rideDetailId = Get.parameters['rideId'] ?? "0";
    passengerBookingId.value = Get.parameters['rideDetailId'].toString();
    notificationId = Get.parameters['notificationId'].toString();
    await getTripDetail();

  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    reviewTextEditingController.dispose();
    tripCancelTextEditingController.dispose();
  }

  getTripDetail() async{
    try{
      isLoading(true);
      TripDetailProvider().getTripDetail(
          rideId,
          rideDetailId,
          serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['ride'] != null){
            ride.addAll(resp['data']['ride']);
            if(passengerBookingId.value  != '0'){
              var bookingList = List.empty(growable: true);
              bookingList.addAll(ride['bookings']);
              var getBooking = bookingList.firstWhereOrNull((element) => element['id'] == int.parse(passengerBookingId.value.toString()));
              if(getBooking != null){
                reviewPassengerImage.value = getBooking['passenger']['profile_image'];
                reviewPassengerName.value = "${getBooking['passenger']['first_name']}";
              }
              if(resp['data'] != null && resp['data']['tripsPage'] != null){
                labelTextDetail.addAll(resp['data']['tripsPage']);
              }

            }
          }
        }
        isLoading(false);
      },onError: (error){
        isLoading(false);
        serviceController.showDialogue(error.toString());
      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());

    }
  }



  postDriverReview(rideId) async{
  errors.clear();

    if(reviewTextEditingController.text == ""){
      var err = {
        'title': "review",
        'eList' : ['Please enter review']
      };
      errors.add(err);
      return;
    }

    try{
      isOverlayLoading(true);
      await MyTripProvider().addReview(
        rideId,
        reviewTextEditingController.text.trim(),
        vehicleCondition.value,
        conscious.value,
        comfort.value,
        communication.value,
        attitude.value,
        hygiene.value,
        respect.value,
        safety.value,
        timeliness.value,
        serviceController.token
      ).then((resp) async {
        errorList.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['review'] != null){
            errorList.addAll(resp['errors']['review']);
          }
        }else if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data']['rating'] != null){

            bool isMyTripControllerRegistered = Get.isRegistered<MyTripController>();
            if(isMyTripControllerRegistered == true){
              var tempController = Get.find<MyTripController>();
              for(var completeTrip in tempController.completedTripList){
                if(completeTrip['ride_id'] == rideId){
                  completeTrip['rating'] = resp['data']['rating'];
                }
              }
              tempController.completedTripList.refresh();
            }
          }

          if(notificationId != '0'){
            bool isNotificationControllerRegistered = Get.isRegistered<NotificationController>();
            if(isNotificationControllerRegistered == true){
              var tempController = Get.find<NotificationController>();
              tempController.notificationsList.removeWhere((element) => element['id'] == int.parse(notificationId.toString()));
              tempController.notificationsList.refresh();
              await tempController.readNotification(notificationId);
            }
          }

          Get.back();
          serviceController.showDialogue(resp['message'].toString());
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


  storePassengerReview(bookingId) async{
  errors.clear();
  if(reviewTextEditingController.text == ""){
      var err = {
        'title': "review",
        'eList' : ['Please enter review']
      };
      errors.add(err);
      return;
    }
    try{
      isOverlayLoading(true);

      await MyTripProvider().storePassengerReview(
          passengerBookingId.value,
          reviewTextEditingController.text.trim(),
          conscious.value,
          comfort.value,
          communication.value,
          attitude.value,
          hygiene.value,
          respect.value,
          safety.value,
          timeliness.value,
          serviceController.token
      ).then((resp) async {
        errorList.clear();
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['errors'] != null){
          if(resp['errors']['review'] != null){
            errorList.addAll(resp['errors']['review']);
          }
        }else if(resp['status'] != null && resp['status'] == "Success"){

          // if(resp['data']['rating'] != null){
          //   for(var rideInfo in cancelRideInfo['bookings']){
          //     if(rideInfo['id'] == int.parse(bookingId.toString())){
          //       rideInfo['rating'] = resp['data']['rating'];
          //     }
          //   }
          //   cancelRideInfo.refresh();

          bool isMyTripControllerRegistered = Get.isRegistered<MyTripController>();
          if(isMyTripControllerRegistered == true){
            var tempController = Get.find<MyTripController>();
            var rideData = tempController.completedRideList.firstWhereOrNull((element) => element['id'] == rideId);
            if(rideData != null){
              for(var ride in rideData['bookings']){
                if(ride['id'] == int.parse(passengerBookingId.value.toString())){
                  ride['rating'] = resp['data']['rating'];
                }
              }
            }
            tempController.completedRideList.refresh();
          }


          if(notificationId != '0'){
            bool isNotificationControllerRegistered = Get.isRegistered<NotificationController>();
            if(isNotificationControllerRegistered == true){
              var tempController = Get.find<NotificationController>();
              tempController.notificationsList.removeWhere((element) => element['id'] == int.parse(notificationId.toString()));
              tempController.notificationsList.refresh();
              await tempController.readNotification(notificationId);
            }
          }
          //completedRideList.refresh();
          Get.back();
          serviceController.showDialogue(resp['message'].toString());
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


}