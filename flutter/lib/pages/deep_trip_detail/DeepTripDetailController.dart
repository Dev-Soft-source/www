import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/deep_trip_detail/DeepTripDetailProvider.dart';
import 'package:proximaride_app/services/service.dart';

class DeepTripDetailController extends GetxController {
  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var tripId = "";
  var tripDetailId = "";
  var ride = {}.obs;
  var status = "";
  late TextEditingController amountTextEditingController,
      securedCashTextEditingController;
  var errors = [].obs;
  var firmCancellationPrice = 0.obs;
  var labelTextDetail = {}.obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    amountTextEditingController = TextEditingController();
    securedCashTextEditingController = TextEditingController();
    var bookingId = serviceController.bookingDeepId.value;
    var bookingStatus = serviceController.actionDeep.value;
    await updateBookingStatus(bookingStatus, bookingId);
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
    securedCashTextEditingController.dispose();
  }

  getTripDetail(rideId, rideDetailId) async {
    try {
      isOverlayLoading(true);
      DeepTripDetailProvider()
          .getTripDetail(rideId, rideDetailId, serviceController.token,
              serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['ride'] != null) {
            ride.addAll(resp['data']['ride']);
            if (resp['data'] != null && resp['data']['siteSetting'] != null) {
              firmCancellationPrice.value = int.parse(
                  resp['data']['siteSetting']['frim_discount'].toString());
            }
            if (resp['data'] != null &&
                resp['data']['rideDetailPage'] != null) {
              labelTextDetail.addAll(resp['data']['rideDetailPage']);
            }
          }
        }
        isOverlayLoading(false);
      }, onError: (error) {
        isOverlayLoading(false);
        if (error is Map &&
            error.containsKey('type') &&
            error.containsKey('message')) {
          serviceController.showDialogue(error['message'], type: "error");
        } else if (error is Map &&
            error.containsKey('type') &&
            error['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(error.toString(), type: "error");
        }
      });
    } catch (exception) {
      isOverlayLoading(false);
      if (exception is Map &&
          exception.containsKey('type') &&
          exception.containsKey('message')) {
        serviceController.showDialogue(exception['message'], type: "error");
      } else if (exception is Map &&
          exception.containsKey('type') &&
          exception['type'] == 'network') {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(exception.toString(), type: "error");
      }
    }
  }

  updateBookingStatus(status, bookingId) {
    try {
      isLoading(true);
      DeepTripDetailProvider()
          .updateBookingStatus(bookingId, status, serviceController.token)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['status'] != null && resp['status'] == "Success") {
          var rideId = resp['data']['bookings'][0]['ride_id'].toString();
          var rideDetailId =
              resp['data']['bookings'][0]['ride_detail_id'].toString();
          await getTripDetail(rideId, rideDetailId);
          serviceController.showDialogue(resp['message'].toString());
        }
        isLoading(false);
      }, onError: (error) {
        isLoading(false);
        if (error is Map &&
            error.containsKey('type') &&
            error.containsKey('message')) {
          serviceController.showDialogue(error['message'], type: "error");
        } else if (error is Map &&
            error.containsKey('type') &&
            error['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(error.toString(), type: "error");
        }
      });
    } catch (exception) {
      isLoading(false);
      if (exception is Map &&
          exception.containsKey('type') &&
          exception.containsKey('message')) {
        serviceController.showDialogue(exception['message'], type: "error");
      } else if (exception is Map &&
          exception.containsKey('type') &&
          exception['type'] == 'network') {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(exception.toString(), type: "error");
      }
    }
  }
}