import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/my_profile/MyProfileController.dart';
import 'package:proximaride_app/pages/payment_options/PaymentOptionsProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class PaymentOptionController extends GetxController {
  final serviceController = Get.find<Service>();
  late final ConnectivityService connectivityService;
  final errorStateManager = ErrorStateManager();
  var errorList = List.empty(growable: true).obs;
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var isScrollLoading = false.obs;
  var page = 1;
  var pageLimit = 6;
  var noMoreData = false.obs;
  ScrollController scrollController = ScrollController();
  late TextEditingController cardNameController,
      cardNumberController,
      cvvCodeController,
      addressController;

  var addEditType = "";

  var totalYear = 70;
  var startYear = DateTime.now().year;
  var makePrimaryCard = false.obs;
  var editCardId = 0;
  var primaryCardActive = 0.obs;

  var labelTextDetail = {}.obs;
  var popupTextDetail = {}.obs;

  var cards = List<dynamic>.empty(growable: true).obs;

  /// From API (`stripeConfig`): e.g. country CA, currency cad — Stripe account region.
  var stripeConfig = <String, dynamic>{}.obs;

  @override
  void onInit() {
    super.onInit();

    if (Get.isRegistered<MyProfileController>()) {
      final myProfileController = Get.find<MyProfileController>();
      final title = myProfileController.labelTextDetail['payment_options_label'];
      if (title != null && title.toString().trim().isNotEmpty) {
        labelTextDetail['main_heading'] = title;
      }
    }

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    cardNameController = TextEditingController();
    cardNumberController = TextEditingController();
    cvvCodeController = TextEditingController();
    addressController = TextEditingController();

    // Load initial data
    loadInitialData();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // NO connectivity check - let the API call proceed
      // Only catch exceptions if they actually occur

      // Execute init API calls
      await getCards();

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
          "Unable to load payment options. Please check your connection and try again.",
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
    scrollController.dispose();
    cardNameController.dispose();
    cardNumberController.dispose();
    cvvCodeController.dispose();
    addressController.dispose();
  }

  void getType() {
    addEditType = Get.parameters['type'] ?? "";
  }

  getCards() async {
    await PaymentOptionsProvider()
        .getCards(
            1, 10, serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null &&
            resp['data']['cards'] != null &&
            resp['data']['cards']['data'] != null) {
          cards.addAll(resp['data']['cards']['data']);
        }
        if (resp['data'] != null && resp['data']['paymentOptionPage'] != null) {
          labelTextDetail.addAll(resp['data']['paymentOptionPage']);
        }
        if (resp['data'] != null && resp['data']['messages'] != null) {
          popupTextDetail.addAll(resp['data']['messages']);
        }
        if (resp['data'] != null && resp['data']['stripeConfig'] != null) {
          stripeConfig.value = Map<String, dynamic>.from(
            resp['data']['stripeConfig'] as Map,
          );
        }
      }
    }, onError: (err) {
      throw err; // Propagate to loadInitialData
    });
  }

  deleteCard(id) async {
    bool isConfirmed = await serviceController.showConfirmationDialog(
        cancelYesBtn: "Yes, remove it!",
        cancelNoBtn: "No, take me back!",
        "${popupTextDetail['delete_card_message'] ?? "Are you sure you want to delete this card"}");

    if (isConfirmed) {
      try {
        isOverlayLoading(true);
        PaymentOptionsProvider().deleteCard(serviceController.token, id).then(
            (resp) async {
          errorList.clear();
          if (resp['status'] != null && resp['status'] == "Error") {
            serviceController.showDialogue(resp['message'].toString());
          } else if (resp['status'] != null && resp['status'] == "Success") {
            cards.removeWhere((element) => element['id'] == id);
            cards.refresh();
            serviceController.showDialogue(resp['message'].toString());
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);
          // Parse structured error from provider
          String errorMessage = "Unable to delete card. Please try again.";
          if (err is Map && err.containsKey('message')) {
            errorMessage = err['message'];
          }
          serviceController.showDialogue(errorMessage, type: "error");
        });
      } catch (exception) {
        isOverlayLoading(false);
        serviceController.showDialogue(
            "Unable to delete card. Please try again.",
            type: "error");
      }
    }
  }

  setPrimaryCard(cardId, index) async {
    try {
      PaymentOptionsProvider()
          .setPrimaryCard(serviceController.token, cardId)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          cards[index]['primary_card'] = "1";
          if (cards.length > 1) {
            var obj = cards[index];
            cards[0]['primary_card'] = "0";
            cards[index] = cards[0];
            cards[0] = obj;
          }
          cards.refresh();
          serviceController.showDialogue(resp['message'].toString());
        }
        isLoading(false);
      }, onError: (err) {
        isLoading(false);
        // Parse structured error from provider
        String errorMessage = "Unable to set primary card. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      serviceController.showDialogue(
          "Unable to set primary card. Please try again.",
          type: "error");
    }
  }
}
