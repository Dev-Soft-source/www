import 'dart:async';
import 'dart:io';

import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/my_passenger/MyPassengerProvider.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class MyPassengerController extends GetxController {
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  late final ConnectivityService connectivityService;

  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var tripId = "";

  var myPassengers = List<dynamic>.empty(growable: true).obs;
  var labelTextDetail = {}.obs;

  @override
  void onInit() async {
    super.onInit();

    if (Get.isRegistered<MyTripController>()) {
      final tripController = Get.find<MyTripController>();
      final heading =
          tripController.labelTextDetail['ride_co_passenger_heading'];
      if (heading != null && heading.toString().trim().isNotEmpty) {
        labelTextDetail['main_heading'] = heading;
      }
    }

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    tripId = Get.parameters['rideId'] ?? "";
    await loadInitialData();
  }

  @override
  void onClose() {
    super.onClose();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      await _getMyPassengers();

      errorStateManager.setSuccess();
    } on SocketException {
      logger.error("Network error in loadInitialData: SocketException");
      errorStateManager.setError(
        "No internet connection. Please check your network and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } on TimeoutException {
      logger.error("Timeout error in loadInitialData");
      errorStateManager.setError(
        "Request timed out. Please check your connection and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } catch (error) {
      logger.error("Error in loadInitialData: $error");

      if (error is Map &&
          error.containsKey('type') &&
          error.containsKey('message')) {
        errorStateManager.setError(
          error["message"],
          _parseErrorType(error["type"]),
          loadInitialData,
        );
      } else if (error.toString().contains("SocketException") ||
          error.toString().contains("Network is unreachable") ||
          error.toString().contains("Connection refused")) {
        errorStateManager.setError(
          "No internet connection. Please check your network and try again.",
          ErrorType.network,
          loadInitialData,
        );
      } else {
        errorStateManager.setError(
          "Unable to load passengers. Please check your connection and try again.",
          ErrorType.unknown,
          loadInitialData,
        );
      }
    }
  }

  ErrorType _parseErrorType(String type) {
    switch (type) {
      case "network":
        return ErrorType.network;
      case "server":
        return ErrorType.server;
      default:
        return ErrorType.unknown;
    }
  }

  Future<void> _getMyPassengers() async {
    isLoading(true);
    await MyPassengerProvider()
        .getMyPassengers(
            tripId, serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['bookings'] != null) {
          myPassengers.addAll(resp['data']['bookings']);
        }
        if (resp['data'] != null && resp['data']['myPassengerPage'] != null) {
          labelTextDetail.addAll(resp['data']['myPassengerPage']);
        }
      }
      isLoading(false);
    }, onError: (err) {
      isLoading(false);
      throw err; // Propagate to loadInitialData
    });
  }

  // Public method for user-triggered refresh
  Future<void> getMyPassengers() async {
    try {
      isLoading(true);
      MyPassengerProvider()
          .getMyPassengers(
              tripId, serviceController.token, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['bookings'] != null) {
            myPassengers.clear();
            myPassengers.addAll(resp['data']['bookings']);
          }
          if (resp['data'] != null && resp['data']['myPassengerPage'] != null) {
            labelTextDetail.addAll(resp['data']['myPassengerPage']);
          }
        }
        isLoading(false);
      }, onError: (err) {
        isLoading(false);
        if (err is Map &&
            err.containsKey('type') &&
            err.containsKey('message')) {
          serviceController.showDialogue(err['message'], type: "error");
        } else if (err is Map &&
            err.containsKey('type') &&
            err['type'] == 'network') {
          serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error");
        } else {
          serviceController.showDialogue(err.toString(), type: "error");
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

  noShowDriverData(bookingId, rideId, userId) {
    try {
      isOverlayLoading(true);

      MyPassengerProvider()
          .noShowDriverData(
              rideId, 'passenger', bookingId, userId, serviceController.token)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.showDialogue(resp['message'].toString());
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
}
