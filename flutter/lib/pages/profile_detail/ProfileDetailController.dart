import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/profile_detail/ProfileDetailProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class ProfileDetailController extends GetxController {
  final serviceController = Get.find<Service>();
  late final ConnectivityService connectivityService;
  final errorStateManager = ErrorStateManager();
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
  void onInit() async {
    super.onInit();
    if (profileId == "" || profileId == "0") {
      profileId = serviceController.loginUserDetail['id'].toString();
    }
    replyTextController = TextEditingController();

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    // Load initial data (don't await - let it run asynchronously so loading state shows)
    await loadInitialData();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // NO connectivity check - let the API call proceed
      // Only catch exceptions if they actually occur

      // Execute appropriate profile fetch based on profileType
      if (profileId != "0" && profileType == "driver") {
        await getDriverProfileDetail();
      } else if (profileId != "0" && profileType == "passenger") {
        await getPassengerProfileDetail();
      } else {
        await getMyProfileDetail();
      }

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
          "Unable to load profile details. Please check your connection and try again.",
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

  @override
  void onClose() {
    super.onClose();
    replyTextController.dispose();
  }

  getMyProfileDetail() async {
    await ProfileDetailProvider()
        .getMyProfileDetail(
            pageLimit, serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['user'] != null) {
          userProfile.addAll(resp['data']['user']);
        }

        if (resp['data'] != null && resp['data']['editProfilePage'] != null) {
          labelTextDetail.addAll(resp['data']['editProfilePage']);
        }

        if (resp['data'] != null && resp['data']['ratings'] != null) {
          reviews.addAll(resp['data']['ratings']);

          for (int i = 0; i < reviews.length; i++) {
            var reply = reviews[i]['replies'];
            if (reply != null) {
              replies[reviews[i]['id'].toString()] = reply;
            }
          }
        }
        if (resp['data'] != null && resp['data']['passenger_driven'] != null) {
          passengerDriven.value =
              int.parse(resp['data']['passenger_driven'].toString());
        }
        if (resp['data'] != null && resp['data']['rides_taken'] != null) {
          rideTaken.value = int.parse(resp['data']['rides_taken'].toString());
        }
        if (resp['data'] != null && resp['data']['km_shared'] != null) {
          kmShared.value = int.parse(resp['data']['km_shared'].toString());
        }
        if (resp['data'] != null && resp['data']['total_reviews'] != null) {
          totalReviews.value = resp['data']['total_reviews'];
        }
      }
    }, onError: (err) {
      throw err; // Propagate to loadInitialData
    });
  }

  addReply(ratingId, reply) async {
    try {
      isOverlayLoading(true);
      ProfileDetailProvider()
          .addReply(ratingId, reply, serviceController.token)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          reviews.clear();
          getMyProfileDetail();
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        serviceController.showDialogue(err.toString(), type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  getDriverProfileDetail() async {
    await ProfileDetailProvider()
        .getDriverProfileDetail(
            profileId, pageLimit, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['ride'] != null) {
          ride.addAll(resp['data']['ride']);
          driverTitle.value =
              resp['data']['ride']['driver']['first_name'] + "'s profile";

          passengerDriven.value = int.parse(
              resp['data']['ride']['driver']['passenger_driven'].toString());

          totalReviews(resp['data']['total_reviews']);
        }

        if (resp['data'] != null && resp['data']['editProfilePage'] != null) {
          labelTextDetail.addAll(resp['data']['editProfilePage']);
        }

        if (resp['data'] != null && resp['data']['total_reviews'] != null) {
          totalReviews(resp['data']['total_reviews']);
        }

        if (resp['data'] != null && resp['data']['ratings'] != null) {
          reviews.addAll(resp['data']['ratings']);
        }
      }
    }, onError: (err) {
      throw err; // Propagate to loadInitialData
    });
  }

  getPassengerProfileDetail() async {
    await ProfileDetailProvider()
        .getPassengerProfileDetail(profileId, pageLimit,
            serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['user'] != null) {
          userProfile.addAll(resp['data']['user']);
        }
        if (resp['data'] != null && resp['data']['ratings'] != null) {
          reviews.addAll(resp['data']['ratings']);

          for (int i = 0; i < reviews.length; i++) {
            var reply = reviews[i]['replies'];
            if (reply != null) {
              replies[reviews[i]['id'].toString()] = reply;
            }
          }
        }

        if (resp['data'] != null && resp['data']['editProfilePage'] != null) {
          labelTextDetail.addAll(resp['data']['editProfilePage']);
        }

        if (resp['data'] != null && resp['data']['passenger_driven'] != null) {
          passengerDriven.value =
              int.parse(resp['data']['passenger_driven'].toString());
        }
        if (resp['data'] != null && resp['data']['rides_taken'] != null) {
          rideTaken.value = int.parse(resp['data']['rides_taken'].toString());
        }
        if (resp['data'] != null && resp['data']['km_shared'] != null) {
          kmShared.value = int.parse(resp['data']['km_shared'].toString());
        }
        if (resp['data'] != null && resp['data']['total_reviews'] != null) {
          totalReviews.value = resp['data']['total_reviews'];
        }
      }
    }, onError: (err) {
      throw err; // Propagate to loadInitialData
    });
  }
}
