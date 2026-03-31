import 'dart:async';
import 'dart:io';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class MyProfileController extends GetxController {
  var showOverly = false.obs;
  var isLoading = false.obs;
  final serviceController = Get.find<Service>();
  late final ConnectivityService connectivityService;
  final errorStateManager = ErrorStateManager();
  var labelTextDetail = {}.obs;

  @override
  void onInit() async {
    super.onInit();

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    // Load initial data
    await loadInitialData();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // NO connectivity check - let the API call proceed
      // Only catch exceptions if they actually occur

      // Execute init API calls
      await getLabelTextDetail();

      errorStateManager.setSuccess();
      isLoading(false);
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
          "Unable to load profile data. Please check your connection and try again.",
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
  }

  Future<void> getLabelTextDetail() async {
    logger.info(
        "My Profile Label Text Detail: ${serviceController.langId.value}");
    logger.info("My Profile Label Text Detail: ${serviceController.token}");
    logger.info("My Profile Label Text Detail: ${profilePageSetting}");
    await StageProvider()
        .getLabelTextDetail(serviceController.langId.value, profilePageSetting,
            serviceController.token)
        .then((resp) async {
      logger.info("My Profile Label Text Detail: $resp");
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['myProfilePage'] != null) {
          labelTextDetail.addAll(resp['data']['myProfilePage']);
        }
        if (resp['data'] != null && resp['data']['logoutPage'] != null) {
          serviceController.logoutLabelTextDetail.clear();
          serviceController.logoutLabelTextDetail
              .addAll(resp['data']['logoutPage']);
        }

        serviceController.termAndConditionLabel.value = resp['data']
                ['termsAndConditionHeading'] ??
            serviceController.termAndConditionLabel.value;
        serviceController.privacyPolicyLabel.value = resp['data']
                ['privacyPolicyHeading'] ??
            serviceController.privacyPolicyLabel.value;
        serviceController.termOfUseLabel.value = resp['data']
                ['termsofuseHeading'] ??
            serviceController.termOfUseLabel.value;
        serviceController.refundPolicyLabel.value = resp['data']
                ['refundPolicyHeading'] ??
            serviceController.refundPolicyLabel.value;
        serviceController.cancellationPolicyLabel.value = resp['data']
                ['cancellationPolicyHeading'] ??
            serviceController.cancellationPolicyLabel.value;
        serviceController.disputePolicyLabel.value = resp['data']
                ['disputePolicyHeading'] ??
            serviceController.disputePolicyLabel.value;
        serviceController.coffeeOnWallLabel.value = resp['data']
                ['coffeeOnWallHeading'] ??
            serviceController.coffeeOnWallLabel.value;
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }
}
