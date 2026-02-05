import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/email_address/EmailAddressProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class EmailAddressController extends GetxController {
  late TextEditingController currentEmailTextEditingController,
      newEmailTextEditingController,
      confirmEmailTextEditingController;

  final Map<String, FocusNode> focusNodes = {};

  var isOverlayLoading = false.obs;
  var isLoading = false.obs;
  final errorStateManager = ErrorStateManager();
  late final ConnectivityService connectivityService;

  final secureStorage = const FlutterSecureStorage();
  final serviceController = Get.find<Service>();

  var errorList = List.empty(growable: true).obs;
  final errors = [].obs;

  var isOldPasswordVisible = false.obs;
  var isNewPasswordVisible = false.obs;
  var isConfirmPasswordVisible = false.obs;

  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;
  var isFormValid = false.obs;

  @override
  void onInit() async {
    super.onInit();

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    currentEmailTextEditingController = TextEditingController();
    newEmailTextEditingController = TextEditingController();
    confirmEmailTextEditingController = TextEditingController();
    currentEmailTextEditingController.text =
        serviceController.loginUserDetail['email'].toString();

    currentEmailTextEditingController.addListener(evaluateFormValidity);
    newEmailTextEditingController.addListener(evaluateFormValidity);
    confirmEmailTextEditingController.addListener(evaluateFormValidity);
    evaluateFormValidity();

    for (int i = 1; i <= 2; i++) {
      focusNodes[i.toString()] = FocusNode();
      // Attach the onFocusChange listener
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus, trigger validation
          if (i == 1) {
            validateField('email', newEmailTextEditingController.text,
                type: 'email');
          } else if (i == 2) {
            validateField(
                'email_confirmation', confirmEmailTextEditingController.text,
                type: 'check_email');
          }
        }
      });
    }

    await loadInitialData();
  }

  @override
  void onClose() {
    super.onClose();
    currentEmailTextEditingController.removeListener(evaluateFormValidity);
    newEmailTextEditingController.removeListener(evaluateFormValidity);
    confirmEmailTextEditingController.removeListener(evaluateFormValidity);
    currentEmailTextEditingController.dispose();
    newEmailTextEditingController.dispose();
    confirmEmailTextEditingController.dispose();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      await _getLabelTextDetail();

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
          "Unable to load email settings. Please check your connection and try again.",
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

  Future<void> _getLabelTextDetail() async {
    await StageProvider()
        .getLabelTextDetail(serviceController.langId.value, myEmailSetting,
            serviceController.token)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['emailSettingPage'] != null) {
          labelTextDetail.addAll(resp['data']['emailSettingPage']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }

  void validateField(String fieldName, String fieldValue,
      {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    clearFieldError(fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];

      if (fieldName == "email_confirmation") {
        message = message.replaceAll(":Attribute",
            labelTextDetail['confirm_email_error'] ?? "Confirm email");
      } else if (fieldName == "email") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['new_email_error'] ?? "Email");
      }

      errorList.add(message ?? '$fieldName field is required');
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
      return;
    }

    switch (type) {
      case 'check_email':
        if (!isValidEmail(fieldValue)) {
          errorList.add('Please use a valid email address');
        }
        if (newEmailTextEditingController.text !=
            confirmEmailTextEditingController.text) {
          var message = validationMessageDetail['confirmed'];
          message = message.replaceAll(
              ":attribute", labelTextDetail['new_email_error'] ?? 'email');
          errorList.add(message ?? 'Sorry emails do not match');
        }

      case 'email':
        if (!isValidEmail(fieldValue)) {
          errorList.add('Please use a valid email address');
        }
        break;
      case 'numeric':
        if (fieldValue.isNotEmpty && double.tryParse(fieldValue) == null) {
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

  void evaluateFormValidity() {
    final currentEmail = currentEmailTextEditingController.text.trim();
    final newEmail = newEmailTextEditingController.text.trim();
    final confirmEmail = confirmEmailTextEditingController.text.trim();

    final requiredFilled = newEmail.isNotEmpty && confirmEmail.isNotEmpty;
    final validFormat = isValidEmail(newEmail) && isValidEmail(confirmEmail);
    final matches = newEmail == confirmEmail;
    final differsFromCurrent = currentEmail.isEmpty ||
        currentEmail.toLowerCase() != newEmail.toLowerCase();

    isFormValid.value =
        requiredFilled && validFormat && matches && differsFromCurrent;
  }

  void clearFieldError(String fieldName) {
    final previousLength = errors.length;
    errors.removeWhere((element) => element['title'] == fieldName);
    if (errors.length != previousLength) {
      errors.refresh();
    }
  }

  updateEmailAddress() async {
    try {
      errors.clear();
      currentEmailTextEditingController.text =
          serviceController.loginUserDetail['email'].toString();
      if (newEmailTextEditingController.text.isEmpty ||
          confirmEmailTextEditingController.text.isEmpty) {
        if (newEmailTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['new_email_error'] ?? 'New e-mail');
          var err = {
            'title': "email",
            'eList': [message ?? 'New e-mail field is required']
          };
          errors.add(err);
        }

        if (confirmEmailTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute",
              labelTextDetail['confirm_email_error'] ?? 'Confirm new e-mail');
          var err = {
            'title': "email_confirmation",
            'eList': [message ?? 'Confirm new e-mail field is required']
          };
          errors.add(err);
        }

        return;
      }
      errors.clear();
      if (newEmailTextEditingController.text !=
          confirmEmailTextEditingController.text) {
        var message = validationMessageDetail['confirmed'];
        message = message.replaceAll(
            ":attribute", labelTextDetail['confirm_email_error'] ?? 'email');
        var err = {
          'title': "email_confirmation",
          'eList': [message ?? 'Sorry emails do not match']
        };
        errors.add(err);
        return;
      }
      errors.clear();
      if (currentEmailTextEditingController.text ==
          newEmailTextEditingController.text) {
        var message = validationMessageDetail['unique'];
        message = message.replaceAll(
            ":attribute", labelTextDetail['new_email_error'] ?? 'email');
        var err = {
          'title': "email",
          'eList': [message ?? 'Please enter a new e-mail']
        };
        errors.add(err);
        return;
      }

      isOverlayLoading(true);
      EmailAddressProvider()
          .updateEmailAddress(
              currentEmailTextEditingController.text.trim(),
              newEmailTextEditingController.text,
              confirmEmailTextEditingController.text,
              serviceController.token,
              serviceController.loginUserDetail['id'])
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['errors'] != null) {
          if (resp['errors']['old_email'] != null) {
            var err = {
              'title': "old_email",
              'eList': resp['errors']['old_email']
            };
            errors.add(err);
          }
          if (resp['errors']['email'] != null) {
            var err = {'title': "email", 'eList': resp['errors']['email']};
            errors.add(err);
          }
          if (resp['errors']['email_confirmation'] != null) {
            var err = {
              'title': "email_confirmation",
              'eList': resp['errors']['email_confirmation']
            };
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.loginUserDetail['email'] =
              newEmailTextEditingController.text;
          secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));
          serviceController.loginUserDetail.refresh();
          Get.back();
          serviceController.showDialogue("Email updated successfully");
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        if (err is Map && err.containsKey('type') && err['type'] == 'network') {
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
