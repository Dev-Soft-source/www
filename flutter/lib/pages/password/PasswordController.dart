import 'dart:async';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/password/PasswordProvider.dart';
import 'package:proximaride_app/pages/profile_setting/ProfileSettingController.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class PasswordController extends GetxController {
  late TextEditingController currentPasswordTextEditingController,
      newPasswordTextEditingController,
      confirmPasswordTextEditingController;

  final Map<String, FocusNode> focusNodes = {};

  var isOverlayLoading = false.obs;

  final secureStorage = const FlutterSecureStorage();
  final serviceController = Get.find<Service>();
  late ErrorStateManager errorStateManager;
  late ConnectivityService connectivityService;

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

    if (Get.isRegistered<ProfileSettingController>()) {
      final profileSettingController = Get.find<ProfileSettingController>();
      final title = profileSettingController.labelTextDetail['password_label'];
      if (title != null && title.toString().trim().isNotEmpty) {
        labelTextDetail['main_heading'] = title;
      }
    }

    // Initialize ErrorStateManager
    errorStateManager = Get.put(ErrorStateManager());

    // Initialize ConnectivityService
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    loadInitialData();

    currentPasswordTextEditingController = TextEditingController();
    newPasswordTextEditingController = TextEditingController();
    confirmPasswordTextEditingController = TextEditingController();

    currentPasswordTextEditingController.addListener(evaluateFormValidity);
    newPasswordTextEditingController.addListener(evaluateFormValidity);
    confirmPasswordTextEditingController.addListener(evaluateFormValidity);
    evaluateFormValidity();

    for (int i = 1; i <= 3; i++) {
      focusNodes[i.toString()] = FocusNode();
      // Attach the onFocusChange listener
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus, trigger validation
          if (i == 1) {
            validateField('Current password', 'current_password',
                currentPasswordTextEditingController.text);
          } else if (i == 2) {
            validateField('New password', 'new_password',
                newPasswordTextEditingController.text,
                type: 'password');
          } else if (i == 3) {
            validateField('Confirm password', 'confirm_password',
                confirmPasswordTextEditingController.text,
                type: 'confirmPassword');
          }
        }
      });
    }
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

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
          "Unable to load data. Please check your connection and try again.",
          ErrorType.unknown,
          loadInitialData,
        );
      }
    }
  }

  ErrorType _parseErrorType(String type) {
    if (type == "network") return ErrorType.network;
    if (type == "server") return ErrorType.server;
    return ErrorType.unknown;
  }

  @override
  void onClose() {
    super.onClose();
    currentPasswordTextEditingController.removeListener(evaluateFormValidity);
    newPasswordTextEditingController.removeListener(evaluateFormValidity);
    confirmPasswordTextEditingController.removeListener(evaluateFormValidity);
    currentPasswordTextEditingController.dispose();
    newPasswordTextEditingController.dispose();
    confirmPasswordTextEditingController.dispose();
  }

  Future<void> getLabelTextDetail() async {
    await StageProvider()
        .getLabelTextDetail(serviceController.langId.value, passwordSetting,
            serviceController.token)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null &&
            resp['data']['passwordSettingPage'] != null) {
          labelTextDetail.addAll(resp['data']['passwordSettingPage']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }
      }
    }, onError: (error) {
      throw error;
    });
  }

  void validateField(String fieldData, String fieldName, String fieldValue,
      {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      if (fieldName == "current_password") {
        message = message.replaceAll(":Attribute",
            labelTextDetail['current_password_error'] ?? fieldData);
      } else if (fieldName == "new_password") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['new_password_error'] ?? fieldData);
      } else if (fieldName == "confirm_password") {
        message = message.replaceAll(":Attribute",
            labelTextDetail['confirm_new_password_error'] ?? fieldData);
      }
      errorList.add(message ?? '$fieldData field is required');
      errors.add({
        'title': fieldName,
        'eList': errorList,
      });
      return;
    }

    switch (type) {
      case 'password':
        if (checkPassword()) {
          var message = validationMessageDetail['regex'];
          message = message.replaceAll(":attribute",
              labelTextDetail['confirm_new_password_error'] ?? 'password');
          errorList.add(message ??
              'The password format is invalid; The password length should be at least 8 characters and must include one lower case, one uppercase, one number, and one special character');
        }
        break;

      case 'confirmPassword':
        if (newPasswordTextEditingController.text !=
            confirmPasswordTextEditingController.text) {
          var message = validationMessageDetail['confirmed'];
          message = message.replaceAll(":attribute",
              labelTextDetail['confirm_new_password_error'] ?? 'password');
          errorList
              .add(message ?? 'Password and confirm password does not match');
        }
        break;

      case 'email':
        if (!isValidEmail(fieldValue)) {
          errorList.add('$fieldName must be a valid format');
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

  checkPassword() {
    var pass = newPasswordTextEditingController.text;
    bool small = false, capital = false, special = false, number = false;

    for (int i = 0; i < pass.length; i++) {
      if (RegExp(r'[a-z]').hasMatch(pass[i])) {
        small = true;
      }
      if (RegExp(r'[A-Z]').hasMatch(pass[i])) {
        capital = true;
      }
      if (RegExp(r'[0-9]').hasMatch(pass[i])) {
        number = true;
      }
      if (RegExp(r'[^a-zA-Z0-9]').hasMatch(pass[i])) {
        special = true;
      }
    }

    if (pass.length >= 8 && small && capital && number && special) {
      return false;
    }
    return true;
  }

  void evaluateFormValidity() {
    final current = currentPasswordTextEditingController.text.trim();
    final newPassword = newPasswordTextEditingController.text;
    final confirmPassword = confirmPasswordTextEditingController.text;

    final allFieldsFilled = current.isNotEmpty &&
        newPassword.isNotEmpty &&
        confirmPassword.isNotEmpty;
    final passwordsMatch =
        newPassword == confirmPassword && newPassword.isNotEmpty;
    final passwordIsStrong = !checkPassword();

    isFormValid.value = allFieldsFilled && passwordsMatch && passwordIsStrong;
  }

  List<String> getPasswordChecklist() {
    final password = newPasswordTextEditingController.text;
    final checklist = <String>[];

    if (password.length >= 8) checklist.add("length");
    if (RegExp(r'[a-z]').hasMatch(password)) checklist.add("small");
    if (RegExp(r'[A-Z]').hasMatch(password)) checklist.add("capital");
    if (RegExp(r'[0-9]').hasMatch(password)) checklist.add("number");
    if (RegExp(r'[!@#$%^&*(),.?\":{}|<>]').hasMatch(password)) {
      checklist.add("special");
    }

    return checklist;
  }

  updatePassword() async {
    // Connectivity check
    if (!connectivityService.isConnected) {
      serviceController.showDialogue(
        "No internet connection. Please check your network and try again.",
        type: "error",
      );
      return;
    }

    try {
      errors.clear();
      if (currentPasswordTextEditingController.text.isEmpty ||
          newPasswordTextEditingController.text.isEmpty ||
          confirmPasswordTextEditingController.text.isEmpty) {
        if (currentPasswordTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute",
              labelTextDetail['current_password_error'] ?? 'Current password');
          var err = {
            'title': "current_password",
            'eList': [message ?? 'Current password field is required']
          };
          errors.add(err);
        }

        if (newPasswordTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute",
              labelTextDetail['new_password_error'] ?? 'New password');
          var err = {
            'title': "new_password",
            'eList': [message ?? 'New password field is required']
          };
          errors.add(err);
        }

        if (confirmPasswordTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute",
              labelTextDetail['confirm_new_password_error'] ??
                  'Confirm new password');
          var err = {
            'title': "confirm_password",
            'eList': [message ?? 'Confirm new password field is required']
          };
          errors.add(err);
        }
        return;
      }
      isOverlayLoading(true);
      PasswordProvider()
          .updatePassword(
              currentPasswordTextEditingController.text.trim(),
              newPasswordTextEditingController.text,
              confirmPasswordTextEditingController.text,
              serviceController.token,
              serviceController.loginUserDetail['id'])
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString(),
              type: "error");
        } else if (resp['errors'] != null) {
          if (resp['errors']['current_password'] != null) {
            var err = {
              'title': "current_password",
              'eList': resp['errors']['current_password']
            };
            errors.add(err);
          }
          if (resp['errors']['new_password'] != null) {
            var err = {
              'title': "new_password",
              'eList': resp['errors']['new_password']
            };
            errors.add(err);
          }
          if (resp['errors']['confirm_password'] != null) {
            var err = {
              'title': "confirm_password",
              'eList': resp['errors']['confirm_password']
            };
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          Get.back();
          serviceController.showDialogue(resp['message'].toString(),
              type: "success");
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        String message = err.toString();
        if (err is Map && err.containsKey('message')) {
          message = err['message'];
        }
        serviceController.showDialogue(message, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      String message = exception.toString();
      if (exception is Map && exception.containsKey('message')) {
        message = exception['message'];
      }
      serviceController.showDialogue(message, type: "error");
    }
  }
}
