import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/profile_setting/ProfileSettingController.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

import 'MyPhoneNumberProvider.dart';

class MyPhoneNumberController extends GetxController {
  late TextEditingController countryCodeTextEditingController,
      phoneNumberTextEditingController;

  var isOverlayLoading = false.obs;
  var isLoading = false.obs;
  final errorStateManager = ErrorStateManager();
  late final ConnectivityService connectivityService;

  final secureStorage = const FlutterSecureStorage();
  final serviceController = Get.find<Service>();
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;

  var verificationCode = "";
  var numbersList = List.empty(growable: true).obs;
  final isNewPhoneFormValid = false.obs;
  // var countryCodes = List.empty(growable: true).obs;

  Timer? timer;
  var secondsRemaining = 5.obs;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  static const List<String> _verifiedDateKeys = [
    'verified_at',
    'verified_on',
    'verified_date',
    'verification_date',
    'verifiedAt',
    'verified_at_formatted',
    'verified_time',
    'verified_timestamp',
    'verified_date_time'
  ];

  static const List<String> _createdDateKeys = [
    'created_at',
    'created_on',
    'created_date',
    'date',
    'added_at',
    'added_on',
    'createdAt',
    'timestamp',
    'created_date_time'
  ];

  @override
  void onInit() async {
    super.onInit();

    if (Get.isRegistered<ProfileSettingController>()) {
      final profileSettingController = Get.find<ProfileSettingController>();
      final title =
          profileSettingController.labelTextDetail['my_phone_number_label'];
      if (title != null && title.toString().trim().isNotEmpty) {
        labelTextDetail['main_heading'] = title;
      }
    }

    countryCodeTextEditingController = TextEditingController();
    phoneNumberTextEditingController = TextEditingController();

    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      connectivityService = Get.put(ConnectivityService());
    }

    if (serviceController.loginUserDetail['country_code'].toString() !=
        "null") {
      countryCodeTextEditingController.text =
          (serviceController.loginUserDetail['country_code']).toString();
    } else {
      countryCodeTextEditingController.text = "+1";
    }
    _setupFormValidationListeners();
    await loadInitialData();
  }

  @override
  void onClose() {
    super.onClose();
    _removeFormValidationListeners();
    timer?.cancel();
    // These controllers are shared across both phone routes and can still be
    // referenced during route transition rebuilds, so avoid disposing them here.
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // Execute init API calls
      await _getPhoneNumbers();

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
          "Unable to load phone numbers. Please check your connection and try again.",
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

  Future<void> _getPhoneNumbers() async {
    await MyPhoneNumberProvider()
        .getPhoneNumbers(
            serviceController.token, serviceController.langId.value)
        .then((resp) {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['phone_numbers'] != null) {
          numbersList.clear();
          numbersList.addAll(resp['data']['phone_numbers']);
          _enforcePrimaryRules();
        }
        if (resp['data'] != null && resp['data']['phoneSettingPage'] != null) {
          logger.info('labelTextDetail: ${resp['data']['phoneSettingPage']}');
          labelTextDetail.addAll(resp['data']['phoneSettingPage']);
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

  void startTimer() {
    timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (secondsRemaining.value == 0) {
        timer.cancel();
      }
      if (secondsRemaining.value > 0) {
        secondsRemaining.value--;
      }
    });
  }

  addNewPhoneNumber() async {
    errors.clear();

    try {
      if (countryCodeTextEditingController.text == "" ||
          phoneNumberTextEditingController.text == "") {
        var err = {
          'title': "phoneNumber",
          'eList': ['Code and phone number field is required']
        };
        errors.add(err);
        return;
      }
      isOverlayLoading(true);
      MyPhoneNumberProvider()
          .addNewPhoneNumber(serviceController.token,
              "${countryCodeTextEditingController.text.replaceAll(' ', '')}${phoneNumberTextEditingController.text.replaceAll(' ', '')}")
          .then((resp) async {
        if (resp['errors'] != null && resp['errors']['phone'] != null) {
          var err = {'title': "phoneNumber", 'eList': resp['errors']['phone']};
          errors.add(err);
        } else if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());
        }
        if (resp['status'] != null && resp['status'] == "Success") {
          numbersList.add(resp['data']['phone_number']);
          numbersList.refresh();
          countryCodeTextEditingController.text = "";
          phoneNumberTextEditingController.text = "";
          _updateFormValidity();
          _enforcePrimaryRules();
          serviceController.showDialogue(resp['message'].toString());
        }
        isOverlayLoading(false);
      }, onError: (error) {
        isOverlayLoading(false);
        if (error is Map &&
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

  sendVerificationCode({phoneId = "0"}) {
    String phoneNumber = "";
    errors.clear();
    if (phoneId == "0") {
      if (countryCodeTextEditingController.text == "" ||
          phoneNumberTextEditingController.text == "") {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(
            ":Attribute", labelTextDetail['phone_error'] ?? 'Code and phone');
        var err = {
          'title': "phoneNumber",
          'eList': [message ?? 'Code and phone number field is required']
        };
        errors.add(err);
        return;
      }
    }

    if (phoneId == "0") {
      phoneNumber = countryCodeTextEditingController.text +
          phoneNumberTextEditingController.text;
    } else {
      var result = numbersList.firstWhereOrNull(
          (number) => number['id'].toString() == phoneId.toString());

      if (result != null) {
        phoneNumber = result['phone'];
      }
    }

    MyPhoneNumberProvider()
        .sendVerificationCode(serviceController.token, phoneNumber, phoneId);
    secondsRemaining.value = 10;
    startTimer();
    Get.toNamed('/phone_number_verification');
  }

  verifyPhoneNumber() async {
    MyPhoneNumberProvider()
        .verifyPhone(serviceController.token, verificationCode)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        var index = -1;
        index = numbersList.indexWhere(
            (number) => number['id'] == resp['data']['phone_number']['id']);

        if (index >= 0) {
          numbersList[index] = resp['data']['phone_number'];
        } else {
          numbersList.add(resp['data']['phone_number']);
        }
        numbersList.refresh();
        _enforcePrimaryRules();

        phoneNumberTextEditingController.clear();
        countryCodeTextEditingController.clear();
        _updateFormValidity();
        Get.back();
        serviceController.showDialogue(resp['message'].toString());
      } else {
        serviceController.showDialogue(resp['message'].toString());
      }
      isLoading.value = false;
    });
  }

  deletePhoneNumber(phoneId, index) async {
    bool isConfirmed = await serviceController.showConfirmationDialog(
        cancelYesBtn: "Yes, delete it!",
        "Are you sure you want to delete this phone number?");

    if (!isConfirmed) {
      return;
    }
    try {
      isLoading.value = true;
      MyPhoneNumberProvider()
          .deletePhoneNumber(serviceController.token, phoneId)
          .then((resp) {
        if (resp['status'] != null && resp['status'] == "Success") {
          numbersList.removeAt(index);
          numbersList.refresh();
          _enforcePrimaryRules();
          serviceController.showDialogue(resp['message'].toString());
        } else {
          serviceController.showDialogue(resp['message'].toString());
        }
        isLoading.value = false;
      }, onError: (error) {
        isLoading.value = false;
        if (error is Map &&
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
      isLoading.value = false;
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

  setAsDefaultNumber(phoneNumberId, index) async {
    try {
      isLoading.value = true;
      MyPhoneNumberProvider()
          .setAsDefaultNumber(serviceController.token, phoneNumberId)
          .then((resp) {
        if (resp['status'] != null && resp['status'] == "Success") {
          // numbersList.where((element) => element['default'] == 1).forEach((element) => element['default'] = 0);
          for (var i = 0; i < numbersList.length; i++) {
            if (_flagValue(numbersList[i]['default']) == "1") {
              numbersList[i]['default'] = "0";
            }
          }
          numbersList[index]['default'] = "1";

          var temp = {};
          temp = numbersList[index];
          numbersList[index] = numbersList[0];
          numbersList[0] = temp;
          numbersList.refresh();
        }
        isLoading.value = false;
      }, onError: (error) {
        isLoading.value = false;
        if (error is Map &&
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
      isLoading.value = false;
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

  void _setupFormValidationListeners() {
    countryCodeTextEditingController.addListener(_updateFormValidity);
    phoneNumberTextEditingController.addListener(_updateFormValidity);
    _updateFormValidity();
  }

  void _removeFormValidationListeners() {
    countryCodeTextEditingController.removeListener(_updateFormValidity);
    phoneNumberTextEditingController.removeListener(_updateFormValidity);
  }

  void _updateFormValidity() {
    final isValid = countryCodeTextEditingController.text.trim().isNotEmpty &&
        phoneNumberTextEditingController.text.trim().isNotEmpty;
    if (isNewPhoneFormValid.value != isValid) {
      isNewPhoneFormValid.value = isValid;
    }
  }

  void _enforcePrimaryRules() {
    if (numbersList.isEmpty) {
      return;
    }

    final primaryIndex = _determinePrimaryIndex();
    if (primaryIndex == null || primaryIndex < 0) {
      return;
    }

    bool changed = false;
    for (var i = 0; i < numbersList.length; i++) {
      final newValue = i == primaryIndex ? "1" : "0";
      if (_flagValue(numbersList[i]['default']) != newValue) {
        numbersList[i]['default'] = newValue;
        changed = true;
      }
    }

    if (primaryIndex != 0) {
      final primary = numbersList.removeAt(primaryIndex);
      numbersList.insert(0, primary);
      changed = true;
    }

    if (changed) {
      numbersList.refresh();
    }
  }

  int? _determinePrimaryIndex() {
    if (numbersList.isEmpty) {
      return null;
    }
    if (numbersList.length == 1) {
      return 0;
    }

    final verifiedIndexes = <int>[];
    for (var i = 0; i < numbersList.length; i++) {
      if (_isVerified(numbersList[i])) {
        verifiedIndexes.add(i);
      }
    }

    if (verifiedIndexes.isNotEmpty) {
      return _pickEarliestIndex(verifiedIndexes, preferVerifiedDate: true);
    }

    final allIndexes = List<int>.generate(numbersList.length, (index) => index);
    return _pickEarliestIndex(allIndexes, preferVerifiedDate: false);
  }

  int _pickEarliestIndex(List<int> indexes,
      {required bool preferVerifiedDate}) {
    indexes.sort((a, b) {
      final mapA = numbersList[a];
      final mapB = numbersList[b];

      final dateA = preferVerifiedDate
          ? (_extractDate(mapA, _verifiedDateKeys) ??
              _extractDate(mapA, _createdDateKeys))
          : _extractDate(mapA, _createdDateKeys);
      final dateB = preferVerifiedDate
          ? (_extractDate(mapB, _verifiedDateKeys) ??
              _extractDate(mapB, _createdDateKeys))
          : _extractDate(mapB, _createdDateKeys);

      if (dateA != null && dateB != null) {
        final cmp = dateA.compareTo(dateB);
        if (cmp != 0) return cmp;
      } else if (dateA != null) {
        return -1;
      } else if (dateB != null) {
        return 1;
      }

      final idA = _extractNumeric(mapA['id']);
      final idB = _extractNumeric(mapB['id']);
      if (idA != null && idB != null && idA != idB) {
        return idA.compareTo(idB);
      }

      return a.compareTo(b);
    });

    return indexes.first;
  }

  bool _isVerified(dynamic number) {
    final value = number['verified'];
    if (value is bool) {
      return value;
    }
    final flag = _flagValue(value);
    return flag == "1";
  }

  DateTime? _extractDate(dynamic data, List<String> keys) {
    if (data is! Map) {
      return null;
    }
    for (final key in keys) {
      if (data.containsKey(key)) {
        final parsed = _parseDateValue(data[key]);
        if (parsed != null) {
          return parsed;
        }
      }
    }
    return null;
  }

  DateTime? _parseDateValue(dynamic value) {
    if (value == null) return null;
    if (value is DateTime) {
      return value;
    }
    if (value is num) {
      final intValue = value.toInt();
      if (intValue > 1000000000000) {
        return DateTime.fromMillisecondsSinceEpoch(intValue);
      } else if (intValue > 1000000000) {
        return DateTime.fromMillisecondsSinceEpoch(intValue * 1000);
      }
    }
    if (value is String) {
      final trimmed = value.trim();
      if (trimmed.isEmpty) return null;
      final parsed = DateTime.tryParse(trimmed);
      if (parsed != null) {
        return parsed;
      }
      final numeric = int.tryParse(trimmed);
      if (numeric != null) {
        if (numeric > 1000000000000) {
          return DateTime.fromMillisecondsSinceEpoch(numeric);
        } else if (numeric > 1000000000) {
          return DateTime.fromMillisecondsSinceEpoch(numeric * 1000);
        }
      }
    }
    return null;
  }

  int? _extractNumeric(dynamic value) {
    if (value == null) return null;
    if (value is int) return value;
    if (value is String) {
      return int.tryParse(value);
    }
    if (value is num) {
      return value.toInt();
    }
    return null;
  }

  String _flagValue(dynamic value) {
    if (value is bool) {
      return value ? "1" : "0";
    }
    if (value == null) {
      return "0";
    }
    return value.toString() == "1" ? "1" : "0";
  }
}
