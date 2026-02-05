import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/contact_us/ContactUsProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/service.dart';
import 'package:proximaride_app/services/logger_service.dart';

class ContactUsController extends GetxController {
  late TextEditingController nameTextEditingController,
      messageTextEditingController,
      phoneTextEditingController;

  final Map<String, FocusNode> focusNodes = {};

  var isOverlayLoading = false.obs;
  var isLoading = false.obs;

  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();

  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  @override
  void onInit() async {
    super.onInit();

    nameTextEditingController = TextEditingController();
    messageTextEditingController = TextEditingController();
    phoneTextEditingController = TextEditingController();

    for (int i = 1; i <= 9; i++) {
      focusNodes[i.toString()] = FocusNode();
      // Attach the onFocusChange listener
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus, trigger validation
          if (i == 1) {
            validateField('name', nameTextEditingController.text);
          } else if (i == 3) {
            validateField('message', messageTextEditingController.text);
          }
        }
      });
    }

    await loadInitialData();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // NO connectivity check - let the API call proceed
      // Only catch exceptions if they actually occur
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
          "Unable to load contact form. Please check your connection and try again.",
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
    // nameTextEditingController.dispose();
    // emailTextEditingController.dispose();
    // messageTextEditingController.dispose();
    // phoneTextEditingController.dispose();
  }

  Future<void> getLabelTextDetail() async {
    try {
      await StageProvider()
          .getLabelTextDetail(serviceController.langId.value, contactUsSetting,
              serviceController.token)
          .then((resp) async {
        if (resp['status'] != null && resp['status'] == "Success") {
          if (resp['data'] != null && resp['data']['contactPage'] != null) {
            labelTextDetail.addAll(resp['data']['contactPage']);
          }

          if (resp['data'] != null &&
              resp['data']['validationMessages'] != null) {
            validationMessageDetail.addAll(resp['data']['validationMessages']);
          }
        }
      }, onError: (error) {
        throw error; // Propagate to loadInitialData
      });
    } catch (e) {
      // Ensure any error is propagated to loadInitialData
      rethrow;
    }
  }

  void validateField(String fieldName, String fieldValue,
      {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      message = message.replaceAll(":Attribute", fieldName);
      errorList.add(message);
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
      return;
    }

    switch (type) {
      case 'email':
        if (!isValidEmail(fieldValue)) {
          errorList.add('Please use a valid email address');
        }
        break;
      case 'numeric':
        if (fieldValue.isNotEmpty && double.tryParse(fieldValue) == null) {
          // var message = validationMessageDetail['email'];
          errorList.add('$fieldName must be a number');
        }
        break;
      case 'date':
        if (fieldValue.isNotEmpty && DateTime.tryParse(fieldValue) == null) {
          errorList.add('$fieldName must be a valid date');
        }
        break;
      case 'time':
        if (fieldValue.isNotEmpty &&
            !RegExp(r'^\d{2}:\d{2}$').hasMatch(fieldValue)) {
          errorList.add('$fieldName must be in the format HH:MM');
        }
        break;
      case 'max_words':
        if (fieldValue.isNotEmpty &&
            fieldValue.split(' ').length > wordsLimit) {
          errorList.add('$fieldName must have at most $wordsLimit words');
        }
        break;
      default:
        break;
    }

    if (errorList.isNotEmpty) {
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
    }
    update();
  }

  bool isValidEmail(String email) {
    // Regular expression for email validation
    final RegExp emailRegExp =
        RegExp(r'^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$');
    return emailRegExp.hasMatch(email);
  }

  storeContactUs() async {
    try {
      if (nameTextEditingController.text.isEmpty ||
          messageTextEditingController.text.isEmpty) {
        if (nameTextEditingController.text.isEmpty) {
          var err = {
            'title': "name",
            'eList': ['Name field is required']
          };
          errors.add(err);
        }

        if (messageTextEditingController.text.isEmpty) {
          var err = {
            'title': "message",
            'eList': ['Message field is required']
          };
          errors.add(err);
        }
        return;
      }

      // Log form submission attempt
      logger.info(
          '📧 Contact Us - Preparing to submit form: ${nameTextEditingController.text.trim()}, ${serviceController.loginUserDetail['email']}, ${phoneTextEditingController.text}, ${messageTextEditingController.text}');

      isOverlayLoading(true);
      ContactUsProvider()
          .storeContactUs(
              nameTextEditingController.text.trim(),
              serviceController.loginUserDetail['email'].toString(),
              phoneTextEditingController.text,
              messageTextEditingController.text,
              serviceController.token)
          .then((resp) async {
        logger.info('📧 Contact Us - Form submitted successfully: $resp');
        errorList.clear();

        if (resp['status'] != null && resp['status'] == "Error") {
          logger.error('❌ Contact Us - Submission failed', resp['message']);
          serviceController.showDialogue(resp['message'].toString(),
              type: "error");
        } else if (resp['errors'] != null) {
          logger.warning('⚠️ Contact Us - Validation errors', resp['errors']);
          if (resp['errors']['name'] != null) {
            errorList.addAll(resp['errors']['name']);
          }
          if (resp['errors']['email'] != null) {
            errorList.addAll(resp['errors']['email']);
          }
          if (resp['errors']['message'] != null) {
            errorList.addAll(resp['errors']['message']);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          logger.info('✅ Contact Us - Successfully submitted', resp['message']);
          Get.back();
          serviceController.showDialogue(resp['message'].toString(),
              type: "success");
        }
        isOverlayLoading(false);
      }, onError: (error) {
        logger.error('❌ Contact Us - API call failed', error);
        isOverlayLoading(false);
        // Parse structured error from provider
        String errorMessage = "Unable to submit form. Please try again.";
        if (error is Map && error.containsKey('message')) {
          errorMessage = error['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      logger.error('❌ Contact Us - Exception occurred', exception);
      isOverlayLoading(false);
      serviceController.showDialogue("Unable to submit form. Please try again.",
          type: "error");
    }
  }
}
