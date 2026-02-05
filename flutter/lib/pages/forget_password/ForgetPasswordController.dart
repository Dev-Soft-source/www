import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/forget_password/ForgetPasswordProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class ForgetPasswordController extends GetxController {
  late TextEditingController emailTextEditingController;

  var isOverlayLoading = false.obs;
  var isLoading = false.obs;

  final serviceController = Get.find<Service>();
  late final ConnectivityService connectivityService;
  final errorStateManager = ErrorStateManager();

  var errors = [].obs;

  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  @override
  void onInit() async {
    super.onInit();
    emailTextEditingController = TextEditingController();

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

      // Execute init API call
      await getLabelTextDetail();

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
      } else {
        errorStateManager.setError(
          "Unable to load app data. Please check your connection and try again.",
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
    // TODO: implement onClose
    super.onClose();
    emailTextEditingController.dispose();
  }

  Future<void> getLabelTextDetail() async {
    await StageProvider()
        .getLabelTextDetail(serviceController.langId.value,
            forgotPasswordSetting, serviceController.token)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null &&
            resp['data']['forgotPasswordPage'] != null) {
          labelTextDetail.addAll(resp['data']['forgotPasswordPage']);
        }
        if (resp['data'] != null && resp['data']['messages'] != null) {
          validationMessageDetail.addAll(resp['data']['messages']);
        }
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }

  forgetPassword() async {
    try {
      errors.clear();
      validateEmail();
      if (errors.isNotEmpty) {
        return;
      }

      // Check connectivity before making API call
      if (!connectivityService.isConnected) {
        serviceController.showDialogue(
          "No internet connection. Please check your network and try again.",
          type: "error",
        );
        return;
      }

      isOverlayLoading(true);
      ForgetPasswordProvider()
          .forgetPassword(
        emailTextEditingController.text,
      )
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Error") {
          var err = {
            'title': "email",
            'eList': [resp['message'].toString()]
          };
          errors.add(err);
        } else if (resp['errors'] != null) {
          if (resp['errors']['email'] != null) {
            var err = {'title': "email", 'eList': resp['errors']['email']};
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          Get.offAllNamed('/thank_you/forgot_password');
        }
        isOverlayLoading(false);
      }, onError: (error) {
        isOverlayLoading(false);
        // Handle structured errors from provider
        if (error is Map && error.containsKey('type')) {
          final errorType = error['type'];
          final errorMessage = error['message'] ?? 'An error occurred';

          if (errorType != 'network') {
            serviceController.showDialogue(errorMessage, type: "error");
          } else {
            serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error",
            );
          }
        } else {
          serviceController.showDialogue(error.toString(), type: "error");
        }
      });
    } catch (exception) {
      isOverlayLoading(false);
      logger.error("Forgot password exception: $exception");
      serviceController.showDialogue(
        "An unexpected error occurred. Please try again.",
        type: "error",
      );
    }
  }

  void validateEmail() {
    String email = emailTextEditingController.text.trim();

    if (email.isEmpty) {
      _addError("email", ['Email is required'], 3);
    } else if (!isValidEmail(email)) {
      _addError("email", ['Please enter a valid email address'], 3);
    }
  }

  bool isValidEmail(String email) {
    // Regular expression for email validation
    final RegExp emailRegExp =
        RegExp(r'^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$');
    return emailRegExp.hasMatch(email);
  }

  void _addError(String title, List<String> errorList, int scrollPosition) {
    var err = {'title': title, 'eList': errorList};
    errors.add(err);

    // if (scrollField == false) {
    //   scrollError(context, scrollPosition, screenHeight);
    //   scrollField = true;
    // }
  }
}
