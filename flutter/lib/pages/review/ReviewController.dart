import 'dart:async';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/review/ReviewProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class ReviewController extends GetxController {
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  late final ConnectivityService connectivityService;

  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var isScrollLoading = false.obs;
  var page = 1;
  var pageLimit = 6;
  var noMoreData = false.obs;
  ScrollController scrollController = ScrollController();
  late TextEditingController replyTextController;
  var profileType = "";
  var profileId = Get.parameters['id'].toString();
  var userDetail = {}.obs;
  var labelTextDetail = {}.obs;

  var reviews = List<dynamic>.empty(growable: true).obs;

  @override
  void onInit() async {
    super.onInit();

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    if (profileId == "" || profileId == "0") {
      profileId = serviceController.loginUserDetail['id'].toString();
    }
    profileType = Get.parameters['type']!;
    replyTextController = TextEditingController();

    await loadInitialData();
    paginateReviewList();
  }

  @override
  void onClose() {
    super.onClose();
    replyTextController.dispose();
    scrollController.dispose();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      await _getReviews();

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
          "Unable to load reviews. Please check your connection and try again.",
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

  Future<void> _getReviews() async {
    isLoading(true);
    await ReviewProvider()
        .getAllReviews(profileId, serviceController.token, pageLimit, page,
            profileType, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['ratings'] != null) {
          reviews.addAll(resp['data']['ratings']['data']);
        }
        if (resp['data'] != null && resp['data']['user'] != null) {
          userDetail.addAll(resp['data']['user']);
        }
        if (resp['data'] != null && resp['data']['reviewSettingPage'] != null) {
          labelTextDetail.addAll(resp['data']['reviewSettingPage']);
        }
      }
      isLoading(false);
    }, onError: (err) {
      isLoading(false);
      throw err; // Propagate to loadInitialData
    });
  }

  // Public method for user-triggered refresh
  Future<void> getReviews() async {
    try {
      isLoading(true);
      ReviewProvider()
          .getAllReviews(profileId, serviceController.token, pageLimit, page,
              profileType, serviceController.langId.value)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['ratings'] != null) {
            reviews.clear();
            reviews.addAll(resp['data']['ratings']['data']);
          }
          if (resp['data'] != null && resp['data']['user'] != null) {
            userDetail.addAll(resp['data']['user']);
          }
          if (resp['data'] != null &&
              resp['data']['reviewSettingPage'] != null) {
            labelTextDetail.addAll(resp['data']['reviewSettingPage']);
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

  Future<void> getMoreReviews() async {
    try {
      isScrollLoading(true);
      await ReviewProvider()
          .getAllReviews(profileId, serviceController.token, pageLimit, page,
              profileType, serviceController.langId.value)
          .then((resp) async {
        if (resp['data']['ratings'] != null &&
            resp['data']['ratings']['data'] != null) {
          reviews.addAll(resp['data']['ratings']['data']);
          var temp = resp['data']['ratings']['data'];
          if (temp.length < pageLimit) {
            noMoreData.value = true;
          }
        }

        isScrollLoading(false);
      }, onError: (err) {
        isScrollLoading(false);
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
      isScrollLoading(false);
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

  void paginateReviewList() {
    scrollController.addListener(() async {
      if (scrollController.position.pixels ==
          scrollController.position.maxScrollExtent) {
        page++;
        await getMoreReviews();
      }
    });
  }

  addReply(ratingId, reply) async {
    try {
      isOverlayLoading(true);
      ReviewProvider().addReply(ratingId, reply, serviceController.token).then(
          (resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          reviews.clear();
          page = 1;
          getReviews();
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
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