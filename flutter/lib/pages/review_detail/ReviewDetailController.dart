import 'dart:async';
import 'dart:io';

import 'package:get/get.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/review_detail/ReviewDetailProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class ReviewDetailController extends GetxController {
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  late final ConnectivityService connectivityService;

  var isLoading = true.obs;
  var errorList = List.empty(growable: true).obs;
  var id = "";
  var fromToType = Get.parameters['type'] ?? "from";

  var reviewDetail = {}.obs;
  var average = 0.0.obs;
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

    id = Get.parameters['id'] ?? "";
    await loadInitialData();
  }

  @override
  void onClose() {
    super.onClose();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      await _getReviewDetail();

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
          "Unable to load review details. Please check your connection and try again.",
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

  Future<void> _getReviewDetail() async {
    isLoading(true);
    await ReviewDetailProvider()
        .getReviewDetail(
            serviceController.token, id, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['rating'] != null) {
          reviewDetail.addAll(resp['data']['rating']);

          average.value = resp['data']['rating']['average_rating'] != null
              ? double.parse(
                  resp['data']['rating']['average_rating'].toString())
              : 0.0;
        }

        if (resp['data'] != null && resp['data']['reviewDetailPage'] != null) {
          labelTextDetail.addAll(resp['data']['reviewDetailPage']);
        }
      }
      isLoading(false);
    }, onError: (error) {
      isLoading(false);
      throw error; // Propagate to loadInitialData
    });
  }

  // Public method for user-triggered refresh
  Future<void> getReviewDetail() async {
    try {
      isLoading(true);
      ReviewDetailProvider()
          .getReviewDetail(
              serviceController.token, id, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['rating'] != null) {
            reviewDetail.clear();
            reviewDetail.addAll(resp['data']['rating']);

            average.value = resp['data']['rating']['average_rating'] != null
                ? double.parse(
                    resp['data']['rating']['average_rating'].toString())
                : 0.0;
          }

          if (resp['data'] != null &&
              resp['data']['reviewDetailPage'] != null) {
            labelTextDetail.addAll(resp['data']['reviewDetailPage']);
          }
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