import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/my_profile/MyProfileController.dart';
import 'package:proximaride_app/pages/payout_account/PayoutAccountProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class PayoutAccountController extends GetxController
    with GetTickerProviderStateMixin {
  late TextEditingController bankTitleTextEditingController,
      accountNumberTextEditingController,
      branchNumberTextEditingController,
      institutionNumberTextEditingController,
      userVerifyAmountTextEditingController,
      paypalEmailTextEditingController,
      interacEmailTextEditingController,
      interacEmailConfirmTextEditingController;

  final Map<String, FocusNode> focusNodes = {};
  var banks = List<dynamic>.empty(growable: true).obs;

  var isOverlayLoading = false.obs;
  var isLoading = false.obs;

  final secureStorage = const FlutterSecureStorage();
  final serviceController = Get.find<Service>();
  late final ConnectivityService connectivityService;
  final errorStateManager = ErrorStateManager();

  var errorList = List.empty(growable: true).obs;
  final errors = [].obs;

  var setDefault = "".obs;
  /// Last `set_default` from server; used when saving without toggling a tab checkbox.
  var loadedSetDefault = "".obs;
  var mainPageIndex = 0.obs;

  var bankBtnText = 0.obs;
  var paypalBtnText = 0.obs;
  var interacBtnText = 0.obs;
  var readOnly = false.obs;
  var bankStatus = "".obs;
  var isBankFormValid = false.obs;
  var isPaypalFormValid = false.obs;
  var isInteracFormValid = false.obs;
  var isBankVerifyValid = false.obs;
  var isPaypalEditMode = false.obs;
  var interacEmailReadOnly = false.obs;
  var interacAutodepositChecked = false.obs;
  late TabController tabController;
  late PageController pageController;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  @override
  void onInit() {
    super.onInit();

    if (Get.isRegistered<MyProfileController>()) {
      final myProfileController = Get.find<MyProfileController>();
      final title = myProfileController.labelTextDetail['payout_options_label'];
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

    bankTitleTextEditingController = TextEditingController();
    accountNumberTextEditingController = TextEditingController();
    userVerifyAmountTextEditingController = TextEditingController();
    paypalEmailTextEditingController = TextEditingController();
    interacEmailTextEditingController = TextEditingController();
    interacEmailConfirmTextEditingController = TextEditingController();
    branchNumberTextEditingController = TextEditingController();
    institutionNumberTextEditingController = TextEditingController();
    tabController = TabController(length: 3, vsync: this);
    pageController = PageController(initialPage: mainPageIndex.value);

    for (final key in [
      'institution_number',
      'branch_number',
      'account_holder_name',
      'account_holder_number',
      'interac_email',
      'interac_email_confirm',
      'paypal_email',
      'user_verify_amount',
    ]) {
      focusNodes[key] = FocusNode();
      focusNodes[key]!.addListener(() {
        if (!focusNodes[key]!.hasFocus) {
          if (key == 'institution_number') {
            validateField('Institution number', 'institution_number',
                institutionNumberTextEditingController.text,
                type: 'numeric');
            validateBankFormFields();
          } else if (key == 'branch_number') {
            validateField('Transit number', 'branch_number',
                branchNumberTextEditingController.text,
                type: 'numeric');
            validateBankFormFields();
          } else if (key == 'account_holder_name') {
            validateField('Account holder name', 'account_holder_name',
                bankTitleTextEditingController.text);
            validateBankFormFields();
          } else if (key == 'account_holder_number') {
            validateField('Account number', 'account_holder_number',
                accountNumberTextEditingController.text,
                type: 'numeric');
            validateBankFormFields();
          } else if (key == 'interac_email') {
            validateField('Interac email', 'interac_email',
                interacEmailTextEditingController.text,
                type: 'email');
            validateInteracFormFields();
          } else if (key == 'interac_email_confirm') {
            validateField('Confirm Interac email', 'interac_email_confirm',
                interacEmailConfirmTextEditingController.text,
                type: 'email');
            validateInteracFormFields();
          } else if (key == 'paypal_email') {
            validateField('Paypal email', 'paypal_email',
                paypalEmailTextEditingController.text,
                type: 'email');
            validatePaypalFormFields();
          } else if (key == 'user_verify_amount') {
            validateField('User verify amount', 'user_verify_amount',
                userVerifyAmountTextEditingController.text,
                type: 'numeric');
            validateBankVerifyField();
          }
        }
      });
    }

    loadInitialData();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // NO connectivity check - let the API call proceed
      // Only catch exceptions if they actually occur

      // Execute init API calls
      await getBanks();

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
          "Unable to load payout account details. Please check your connection and try again.",
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

    bankTitleTextEditingController.dispose();
    accountNumberTextEditingController.dispose();
    userVerifyAmountTextEditingController.dispose();
    paypalEmailTextEditingController.dispose();
    interacEmailTextEditingController.dispose();
    interacEmailConfirmTextEditingController.dispose();
    branchNumberTextEditingController.dispose();
    institutionNumberTextEditingController.dispose();
    for (final n in focusNodes.values) {
      n.dispose();
    }
  }

  void validateField(String fieldData, String fieldName, String fieldValue,
      {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      if (fieldName == "bank_name") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['bank_title_error'] ?? fieldData);
      } else if (fieldName == "acc_no") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['acc_no_error'] ?? fieldData);
      } else if (fieldName == "branch") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['branch_error'] ?? fieldData);
      } else if (fieldName == "address") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['address_error'] ?? fieldData);
      } else if (fieldName == "user_verify_amount") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['verify_amount_error'] ?? fieldData);
      } else if (fieldName == "paypal_email") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['paypal_email_error'] ?? fieldData);
      } else if (fieldName == "branch_address") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['branch_address'] ?? fieldData);
      } else if (fieldName == "interac_email") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['interac_email_label'] ?? fieldData);
      } else if (fieldName == "interac_email_confirm") {
        message = message.replaceAll(":Attribute",
            labelTextDetail['interac_email_confirm_label'] ?? fieldData);
      }
      errorList.add(message ?? '$fieldData field is required');
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
          var message = validationMessageDetail['required'];
          if (fieldName == "user_verify_amount") {
            message = message.replaceAll(":attribute",
                labelTextDetail['verify_amount_error'] ?? fieldData);
          } else if (fieldName == "acc_no") {
            message = message.replaceAll(
                ":attribute", labelTextDetail['acc_no_error'] ?? fieldData);
          }

          errorList.add(message ?? '$fieldName must be a number');
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

  void validateBankFormFields() {
    final acc = accountNumberTextEditingController.text;
    final transit = branchNumberTextEditingController.text;
    final inst = institutionNumberTextEditingController.text;
    final digitsOk = acc.isNotEmpty &&
        acc.length >= 7 &&
        acc.length <= 12 &&
        RegExp(r'^\d+$').hasMatch(acc) &&
        transit.length == 5 &&
        RegExp(r'^\d{5}$').hasMatch(transit) &&
        inst.length == 3 &&
        RegExp(r'^\d{3}$').hasMatch(inst);
    isBankFormValid.value = bankTitleTextEditingController.text.isNotEmpty &&
        digitsOk;
  }

  void validateInteracFormFields() {
    final email = interacEmailTextEditingController.text.trim();
    final confirm = interacEmailConfirmTextEditingController.text.trim();
    final emailOk = email.isNotEmpty && isValidEmail(email);
    final match = emailOk && confirm.isNotEmpty && email == confirm;
    isInteracFormValid.value =
        match && interacAutodepositChecked.value;
  }

  void validatePaypalFormFields() {
    // Paypal button should be enabled when email is filled; "set default" is optional
    isPaypalFormValid.value = paypalEmailTextEditingController.text.isNotEmpty;
  }

  void validateBankVerifyField() {
    isBankVerifyValid.value =
        userVerifyAmountTextEditingController.text.isNotEmpty;
  }

  void togglePaypalEditMode() {
    isPaypalEditMode.value = !isPaypalEditMode.value;
  }

  getBanks() async {
    await PayoutAccountProvider()
        .getBanks(serviceController.token, serviceController.langId.value)
        .then((resp) async {
      logger.info("Banks: ${resp.toString()}");
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['banks'] != null) {
          banks.addAll(resp['data']['banks']);
        }

        if (resp['data'] != null && resp['data']['payoutOptionPage'] != null) {
          logger.info(
              "Payout Option Page: ${resp['data']['payoutOptionPage'].toString()}");
          labelTextDetail.addAll(resp['data']['payoutOptionPage']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }

        if (resp['data'] != null && resp['data']['userBankDetail'] != null) {
          final ubd = resp['data']['userBankDetail'] as Map;

          bankTitleTextEditingController.text =
              (ubd['bank_title'] ?? "").toString();
          accountNumberTextEditingController.text =
              (ubd['acc_no'] ?? "").toString();
          branchNumberTextEditingController.text =
              (ubd['branch_number'] ?? "").toString();
          institutionNumberTextEditingController.text =
              (ubd['institution_number'] ?? "").toString();
          setDefault.value = (ubd['set_default'] ?? "").toString();
          loadedSetDefault.value = setDefault.value;
          paypalEmailTextEditingController.text =
              (ubd['paypal_email'] ?? "").toString();

          final interacSaved = (ubd['interac_email'] ?? '').toString().trim();
          interacEmailTextEditingController.text = interacSaved;
          interacEmailReadOnly.value = interacSaved.isNotEmpty;
          interacBtnText.value = interacSaved.isNotEmpty ? 1 : 0;

          bankBtnText.value =
              bankTitleTextEditingController.text.isNotEmpty &&
                      branchNumberTextEditingController.text.isNotEmpty &&
                      institutionNumberTextEditingController.text.isNotEmpty &&
                      accountNumberTextEditingController.text.isNotEmpty
                  ? 1
                  : 0;

          paypalBtnText.value =
              ubd['paypal_email'] != null &&
                      ubd['paypal_email'].toString().trim().isNotEmpty
                  ? 1
                  : 0;

          bankStatus.value = (ubd['status'] ?? "pending").toString();

          isPaypalEditMode.value = ubd['paypal_email'] == null ||
              ubd['paypal_email'].toString().trim().isEmpty;
        }

        // Initialize form validity based on any pre-filled data
        validateBankFormFields();
        validateInteracFormFields();
        validatePaypalFormFields();
        validateBankVerifyField();
      }
    }, onError: (err) {
      throw err; // Propagate to loadInitialData
    });
  }

  String _setDefaultForApi(String fallbackMethod) {
    if (setDefault.value.isNotEmpty) return setDefault.value;
    if (loadedSetDefault.value.isNotEmpty) return loadedSetDefault.value;
    return fallbackMethod;
  }

  updateBankDetail() async {
    try {
      errors.clear();

      if (institutionNumberTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute",
            labelTextDetail['institution_number_error'] ??
                'Institution number');
        var err = {
          'title': "institution_number",
          'eList': [message ?? 'Institution number field is required']
        };
        errors.add(err);
      } else if (institutionNumberTextEditingController.text.length != 3) {
        var err = {
          'title': "institution_number",
          'eList': ['Institution number must be exactly 3 digits']
        };
        errors.add(err);
      }

      if (branchNumberTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute",
            labelTextDetail['branch_number_error'] ?? 'Transit number');
        var err = {
          'title': "branch_number",
          'eList': [message ?? 'Transit number is required']
        };
        errors.add(err);
      } else if (branchNumberTextEditingController.text.length != 5) {
        var err = {
          'title': "branch_number",
          'eList': ['Transit number must be exactly 5 digits']
        };
        errors.add(err);
      }

      if (bankTitleTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['bank_title_error'] ?? 'Bank title');
        var err = {
          'title': "account_holder_name",
          'eList': [message ?? 'Account holder name is required']
        };
        errors.add(err);
      }

      if (accountNumberTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['acc_no_error'] ?? 'Account number');
        var err = {
          'title': "account_holder_number",
          'eList': [message ?? 'Account number is required']
        };
        errors.add(err);
      } else {
        final acc = accountNumberTextEditingController.text;
        if (!RegExp(r'^\d{7,12}$').hasMatch(acc)) {
          errors.add({
            'title': "account_holder_number",
            'eList': ['Account number must be 7–12 digits']
          });
        }
      }
      if (errors.isNotEmpty) {
        return;
      }
      isOverlayLoading(true);
      PayoutAccountProvider()
          .updateBankDetail(
              bankTitleTextEditingController.text,
              accountNumberTextEditingController.text,
              branchNumberTextEditingController.text,
              institutionNumberTextEditingController.text,
              _setDefaultForApi('bank'),
              serviceController.token)
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['errors'] != null) {
          if (resp['errors']['account_holder_name'] != null) {
            var err = {
              'title': "account_holder_name",
              'eList': resp['errors']['account_holder_name']
            };
            errors.add(err);
          }
          if (resp['errors']['account_holder_number'] != null) {
            var err = {
              'title': "account_holder_number",
              'eList': resp['errors']['account_holder_number']
            };
            errors.add(err);
          }
          if (resp['errors']['branch_number'] != null) {
            var err = {
              'title': "branch_number",
              'eList': resp['errors']['branch_number']
            };
            errors.add(err);
          }
          if (resp['errors']['institution_number'] != null) {
            var err = {
              'title': "institution_number",
              'eList': resp['errors']['institution_number']
            };
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          bankBtnText.value = 1;
          final bd = resp['data']?['bankDetail'];
          if (bd != null && bd['set_default'] != null) {
            loadedSetDefault.value = bd['set_default'].toString();
          }
          serviceController.showDialogue(resp['message'].toString());
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        // Parse structured error from provider
        String errorMessage =
            "Unable to update bank details. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(
          "Unable to update bank details. Please try again.",
          type: "error");
    }
  }

  updateInteracDetail() async {
    try {
      errors.clear();
      final email = interacEmailTextEditingController.text.trim();
      final confirm = interacEmailConfirmTextEditingController.text.trim();
      if (email.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute",
            labelTextDetail['interac_email_label'] ?? 'Email');
        errors.add({
          'title': "interac_email",
          'eList': [message ?? 'Email is required']
        });
      } else if (!isValidEmail(email)) {
        errors.add({
          'title': "interac_email",
          'eList': ['Please use a valid email address']
        });
      }
      if (confirm.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute",
            labelTextDetail['interac_email_confirm_label'] ?? 'Confirm email');
        errors.add({
          'title': "interac_email_confirm",
          'eList': [message ?? 'Please confirm your email']
        });
      } else if (email != confirm) {
        errors.add({
          'title': "interac_email_confirm",
          'eList': ['Interac emails must match']
        });
      }
      if (!interacAutodepositChecked.value) {
        errors.add({
          'title': "interac_autodeposit",
          'eList': [
            labelTextDetail['interac_autodeposit_label'] ??
                'Please confirm Autodeposit is enabled for this email.'
          ]
        });
      }
      if (errors.isNotEmpty) {
        return;
      }
      isOverlayLoading(true);
      PayoutAccountProvider()
          .updateInteracDetail(
            email,
            confirm,
            interacAutodepositChecked.value,
            _setDefaultForApi('interac'),
            serviceController.token,
          )
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['errors'] != null) {
          final e = resp['errors'] as Map;
          if (e['interac_email'] != null) {
            errors.add({
              'title': "interac_email",
              'eList': e['interac_email'] is List
                  ? List<String>.from(e['interac_email'])
                  : [e['interac_email'].toString()]
            });
          }
          if (e['interac_email_confirm'] != null) {
            errors.add({
              'title': "interac_email_confirm",
              'eList': e['interac_email_confirm'] is List
                  ? List<String>.from(e['interac_email_confirm'])
                  : [e['interac_email_confirm'].toString()]
            });
          }
          if (e['interac_autodeposit'] != null) {
            errors.add({
              'title': "interac_autodeposit",
              'eList': e['interac_autodeposit'] is List
                  ? List<String>.from(e['interac_autodeposit'])
                  : [e['interac_autodeposit'].toString()]
            });
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          interacBtnText.value = 1;
          interacEmailReadOnly.value = true;
          interacEmailConfirmTextEditingController.clear();
          interacAutodepositChecked.value = false;
          final bd = resp['data']?['bankDetail'];
          if (bd != null && bd['set_default'] != null) {
            loadedSetDefault.value = bd['set_default'].toString();
          }
          serviceController.showDialogue(resp['message'].toString());
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        String errorMessage =
            "Unable to update Interac details. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(
          "Unable to update Interac details. Please try again.",
          type: "error");
    }
  }

  updatePageIndexValue(index) async {
    mainPageIndex.value = index;
  }

  updatePaypalDetail() async {
    try {
      errors.clear();
      if (paypalEmailTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute",
            labelTextDetail['paypal_email_error'] ?? 'Paypal email');
        var err = {
          'title': "paypal_email",
          'eList': [message ?? 'Paypal email field is required']
        };
        errors.add(err);
      }
      if (errors.isNotEmpty) {
        return;
      }
      isOverlayLoading(true);
      PayoutAccountProvider()
          .updatePaypalDetail(
              paypalEmailTextEditingController.text,
              _setDefaultForApi('paypal'),
              serviceController.token,
              serviceController.loginUserDetail['id'])
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['errors'] != null) {
          if (resp['errors']['paypal_email'] != null) {
            var err = {
              'title': "paypal_email",
              'eList': resp['errors']['paypal_email']
            };
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          paypalBtnText.value = 1; // Set to update mode
          isPaypalEditMode.value = false; // Exit edit mode
          final bd = resp['data']?['bankDetail'];
          if (bd != null && bd['set_default'] != null) {
            loadedSetDefault.value = bd['set_default'].toString();
          }

          // Show dialog and navigate back after user closes it
          await serviceController.showDialogue(resp['message'].toString());

          // Navigate back 2 screens
          Get.back(); // First back
          Get.back(); // Second back
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        // Parse structured error from provider
        String errorMessage =
            "Unable to update PayPal details. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(
          "Unable to update PayPal details. Please try again.",
          type: "error");
    }
  }

  verifyBank() async {
    try {
      errors.clear();
      if (userVerifyAmountTextEditingController.text.isEmpty) {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['verify_amount_error'] ?? 'Amount');
        var err = {
          'title': "user_verify_amount",
          'eList': [message ?? 'Amount field is required']
        };
        errors.add(err);
      }
      if (errors.isNotEmpty) {
        return;
      }
      isOverlayLoading(true);
      PayoutAccountProvider()
          .verifyBank(userVerifyAmountTextEditingController.text,
              serviceController.token, serviceController.loginUserDetail['id'])
          .then((resp) async {
        errorList.clear();
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        } else if (resp['errors'] != null) {
          if (resp['errors']['user_verify_amount'] != null) {
            var err = {
              'title': "user_verify_amount",
              'eList': resp['errors']['user_verify_amount']
            };
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.showDialogue(resp['message'].toString());

          if (resp['data'] != null && resp['data']['bankDetail'] != null) {
            bankStatus.value =
                resp['data']['bankDetail']['status'] ?? "pending";
          }
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);
        // Parse structured error from provider
        String errorMessage = "Unable to verify bank. Please try again.";
        if (err is Map && err.containsKey('message')) {
          errorMessage = err['message'];
        }
        serviceController.showDialogue(errorMessage, type: "error");
      });
    } catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue("Unable to verify bank. Please try again.",
          type: "error");
    }
  }
}
