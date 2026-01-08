import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/pages/trip_detail/TripDetailProvider.dart';
import 'package:proximaride_app/services/service.dart';

class TripDetailController extends GetxController{

  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var tripId = "";
  var tripDetailId = "";
  var type = "";
  var cancelSetting = {};
  var reviewSetting = {};
  var ride = {}.obs;
  var status = "";
  late TextEditingController amountTextEditingController, securedCashTextEditingController;
  var errors = [].obs;
  var firmCancellationPrice = 0.obs;
  var labelTextDetail = {}.obs;
  var hideDriverInfo = false.obs;

@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    tripId = Get.parameters['tripId'] ?? "";
    tripDetailId = Get.parameters['rideDetailId'] ?? "0";
    type = Get.parameters['type'] ?? "";
    status = Get.parameters['status'] ?? "";
    amountTextEditingController = TextEditingController();
    securedCashTextEditingController = TextEditingController();
    await getTripDetail();
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    securedCashTextEditingController.dispose();
  }

  getTripDetail() async{
    try{
      isLoading(true);
      TripDetailProvider().getTripDetail(
        tripId,
        tripDetailId,
        serviceController.token,
        serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['ride'] != null){
            ride.addAll(resp['data']['ride']);
            cancelSetting.addAll(resp['data']['cancelRideSetting']);
            reviewSetting.addAll(resp['data']['reviewSetting']);

            if(ride.isNotEmpty){
              var bookings = List<dynamic>.empty(growable: true);
              bookings.addAll(ride['bookings']);

              var getBooking = bookings.firstWhereOrNull((element) => element['user_id'].toString() == serviceController.loginUserDetail['id'].toString());
              if(getBooking != null){
                hideDriverInfo.value = true;
                print(hideDriverInfo.value);
              }
            }


            if(resp['data'] != null && resp['data']['siteSetting'] != null){
              firmCancellationPrice.value = int.parse(resp['data']['siteSetting']['frim_discount'].toString());
            }
            if(resp['data'] != null && resp['data']['rideDetailPage'] != null){
              labelTextDetail.addAll(resp['data']['rideDetailPage']);
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

  updateBookingStatus(status, bookingId){
    try{
      isOverlayLoading(true);
      TripDetailProvider().updateBookingStatus(
          bookingId,
          status,
          serviceController.token
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['status'] != null && resp['status'] == "Success"){
          var requestBooking = ride['booking_requests'];
          if(requestBooking != null){
            requestBooking.removeWhere((element) => element['id'] == bookingId);
          }
          var tripController = Get.find<MyTripController>();
          var getRideInfo = tripController.upComingRideList.firstWhereOrNull((element) => element['id'] == ride['id']);
          if(getRideInfo != null){
            var getRequestBookings = getRideInfo['booking_requests'];
            getRequestBookings.removeWhere((element) => element['id'] == bookingId);
            tripController.upComingRideList.refresh();
          }

          if(status == "accept"){
            ride['bookings'] = resp['data']['bookings'];
            var bookingList = List<dynamic>.empty(growable: true);
            bookingList.addAll(resp['data']['bookings']);

            int sumSeats = bookingList
                .map((element) => int.tryParse(element['seats'] ?? '') ?? 0)
                .reduce((a, b) => a + b);
            double fare = bookingList
                .map((element) => double.tryParse(element['fare'] ?? '') ?? 0.0)
                .reduce((a, b) => a + b);
            double bookingCredit = bookingList
                .map((element) => double.tryParse(element['booking_credit'] ?? '') ?? 0.0)
                .reduce((a, b) => a + b);

            ride['booked_seats'] = sumSeats.toStringAsFixed(0);
            ride['fare'] = fare.toStringAsFixed(1);
            ride['booking_fee'] = bookingCredit.toStringAsFixed(1);
            ride['total_amount'] = (fare + bookingCredit).toStringAsFixed(1);
            ride.refresh();
            var tripController = Get.find<MyTripController>();
            var getRideInfo = tripController.upComingRideList.firstWhereOrNull((element) => element['id'] == ride['id']);
            if(getRideInfo != null){
              getRideInfo['bookings'] = resp['data']['bookings'];
              tripController.upComingRideList.refresh();
            }
          }else{
            if(ride['total_amount'] > 0){
              ride['booked_seats'] = (int.parse(ride['booked_seats'] != null ? ride['booked_seats'].toString() : '0') - int.parse(resp['data']['booking']['seats'] != null ? resp['data']['booking']['seats'].toString() : "0")).toStringAsFixed(0);
              ride['fare'] = (double.parse(ride['fare'] != null ? ride['fare'].toString() : '0') - double.parse(resp['data']['booking']['fare'] != null ? resp['data']['booking']['fare'].toString() : "0")).toStringAsFixed(1);
              ride['booking_fee'] = (double.parse(ride['booking_fee'] != null ? ride['booking_fee'].toString() : '0') - double.parse(resp['data']['booking']['booking_credit'] != null ? resp['data']['booking']['booking_credit'].toString() : "0")).toStringAsFixed(1);
              ride['total_amount'] = (double.parse(ride['fare'] != null ? ride['fare'].toString() : '0') + double.parse(ride['booking_fee'] ?? '0')).toStringAsFixed(1);
              ride.refresh();
              var tripController = Get.find<MyTripController>();
              var getRideInfo = tripController.upComingRideList.firstWhereOrNull((element) => element['id'] == ride['id']);
              if(getRideInfo != null){
                getRideInfo['booked_seats'] = ride['booked_seats'];
                getRideInfo['booking_fee'] = ride['booking_fee'];
                getRideInfo['total_amount'] = ride['total_amount'];
                tripController.upComingRideList.refresh();
              }
            }else{
              ride['booked_seats'] = (int.parse(ride['booked_seats'] != null ? ride['booked_seats'].toString() : '0') - int.parse(resp['data']['booking']['seats'] != null ? resp['data']['booking']['seats'].toString() : "0")).toStringAsFixed(0);
              ride.refresh();
              var tripController = Get.find<MyTripController>();
              var getRideInfo = tripController.upComingRideList.firstWhereOrNull((element) => element['id'] == ride['id']);
              if(getRideInfo != null){
                getRideInfo['booked_seats'] = ride['booked_seats'];
                tripController.upComingRideList.refresh();
              }
            }

          }
          serviceController.showDialogue(resp['message'].toString());

        }
        ride.refresh();
        isOverlayLoading(false);
      },onError: (error){
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());

      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  enterCode(bookingId){

  if(securedCashTextEditingController.text == ""){
    var err = {
      'title': "payment_code",
      'eList' : ["Please enter payment code"]
    };
    errors.add(err);
    return;
  }
  else{
    errors.clear();
  }

    try{
      isOverlayLoading(true);
      TripDetailProvider().enterCode(
          bookingId,
          securedCashTextEditingController.text,
          serviceController.token
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
          var bookingList = List<dynamic>.empty(growable: true).obs;
          bookingList.addAll(ride['bookings']);
          var currentBooking = bookingList.firstWhereOrNull((element) => element['id'].toString() == bookingId.toString());
          if(currentBooking != null){
            currentBooking['secured_cash_attempt_count'] = resp['data'].toString();
          }
          ride.refresh();
        }else if(resp['status'] != null && resp['status'] == "Success"){
          Get.back();
          Get.back();
          serviceController.showDialogue(resp['message'].toString());
        }

        isOverlayLoading(false);
      },onError: (error){
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());
      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  noShowDriverData(){
    try{
      isOverlayLoading(true);

      var bookingId = 0;

      var passengerList = List<dynamic>.empty(growable: true);
      passengerList.addAll(ride['bookings']);
      if(passengerList.isNotEmpty){
        var bookingData = passengerList.firstWhereOrNull((element) => element['user_id'] == serviceController.loginUserDetail['id']);
        if(bookingData != null){
          bookingId = bookingData['id'];
        }
      }

      TripDetailProvider().noShowDriverData(
          ride['id'],
          'driver',
          bookingId,
          ride['added_by'],
          serviceController.token
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['status'] != null && resp['status'] == "Success"){
          serviceController.showDialogue(resp['message'].toString());
        }
        isOverlayLoading(false);
      },onError: (error){
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());
      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }


  checkRide(type) async{
      if(ride['payment_method_slug'] == "secured"){
        try{
          isOverlayLoading(true);
         await TripDetailProvider().checkPhoneNumber(
            serviceController.langId.value,
              serviceController.token
          ).then((resp) async {
            if(resp['status'] != null && resp['status'] == false){
              Get.toNamed('/my_phone_number');
              serviceController.showDialogue(resp['message'].toString());
            }else{
              if(type == "add"){
                Get.toNamed("/book_seat/${ride['id']}/0/${ride['ride_detail'][0]['id'].toString()}");
              }else{
                Get.toNamed("/book_seat/${ride['id']}/${ride['booked_seats']}/${ride['ride_detail'][0]['id'].toString()}");
              }
            }
            isOverlayLoading(false);
          },onError: (error){
            isOverlayLoading(false);
            serviceController.showDialogue(error.toString());
          });

        }catch (exception) {
          isOverlayLoading(false);
          serviceController.showDialogue(exception.toString());
        }
      }else{
        if(type == "add"){
          Get.toNamed("/book_seat/${ride['id']}/0/${ride['ride_detail'][0]['id'].toString()}");
        }else{
          Get.toNamed("/book_seat/${ride['id']}/${ride['booked_seats']}/${ride['ride_detail'][0]['id'].toString()}");
        }
      }
  }

  cancelRideByDriver() async{
  bool isConfirmed = await serviceController.showConfirmationDialog(labelTextDetail['cancel_ride_confirmation'] ?? "Are you sure you want to cancel this ride", cancelYesBtn: labelTextDetail['cancel_ride_yes_btn'] ?? "Yes, cancel it", cancelNoBtn: labelTextDetail['cancel_ride_no_btn'] ?? "No, take me back" );
    if(isConfirmed == false){
      return;
    }
    try{
      isOverlayLoading(true);
      await TripDetailProvider().cancelMyBooking(
          ride['id'],
          serviceController.token
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'].toString());
        }else if(resp['status'] != null && resp['status'] == "Success"){
          serviceController.navigationIndex.value = 1;
          serviceController.showDialogue(resp['message'].toString(), path: '/navigation', off: 1);
          serviceController.showDialogue(resp['message'].toString());
        }
        isOverlayLoading(false);
      },onError: (error){
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());
      });
    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

}