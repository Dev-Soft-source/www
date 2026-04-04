import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/country_code_finder.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';
import 'package:intl/intl.dart';

class StageController extends GetxController {
  /// Minimum age for Stage One (date of birth).
  static const int minimumProfileAgeYears = 18;

  static DateTime latestBirthDateForMinimumAge(int minYears) {
    final n = DateTime.now();
    return DateTime(n.year - minYears, n.month, n.day);
  }
  var isOverlayLoading = false.obs;
  var isLoading = false.obs;
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;
  final secureStorage = const FlutterSecureStorage();
  var stepNo = "0".obs;

  late TextEditingController firstNameTextEditingController,
      lastNameTextEditingController,
      dobTextEditingController,
      searchTextEditingController,
      postalCodeTextEditingController,
      miniBioTextEditingController;

  var gender = "".obs;
  // Default to Canada for first-time Stage One entry
  var countryId = 38.obs;
  var countryName = "Canada".obs;
  String? countryCode = "";
  var stateId = 0.obs;
  var stateName = "".obs;
  var cityId = 0.obs;
  var cityName = "".obs;
  var countries = List<dynamic>.empty(growable: true).obs;
  var searchCountries = List<dynamic>.empty(growable: true).obs;
  var states = List<dynamic>.empty(growable: true).obs;
  var searchStates = List<dynamic>.empty(growable: true).obs;
  var cities = List<dynamic>.empty(growable: true).obs;
  var searchCities = List<dynamic>.empty(growable: true).obs;

  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;

  /// Controls whether the "Next" button on Stage One is enabled.
  final isStageOneValid = false.obs;

  /// True when DOB is filled but under [minimumProfileAgeYears] (for inline hint).
  final dobFailsMinimumAge = false.obs;

  @override
  void onInit() async {
    super.onInit();
    firstNameTextEditingController = TextEditingController();
    lastNameTextEditingController = TextEditingController();
    dobTextEditingController = TextEditingController();
    searchTextEditingController = TextEditingController();
    postalCodeTextEditingController = TextEditingController();
    miniBioTextEditingController = TextEditingController();

    firstNameTextEditingController.text =
        serviceController.loginUserDetail['first_name'].toString();
    lastNameTextEditingController.text =
        serviceController.loginUserDetail['last_name'].toString();

    stepNo.value = serviceController.loginUserDetail['step'].toString();

    // Re‑validate the form whenever any relevant field changes.
    firstNameTextEditingController.addListener(validateStageOneFields);
    lastNameTextEditingController.addListener(validateStageOneFields);
    dobTextEditingController.addListener(validateStageOneFields);
    postalCodeTextEditingController.addListener(validateStageOneFields);
    miniBioTextEditingController.addListener(validateStageOneFields);

    ever(gender, (_) => validateStageOneFields());
    ever(countryName, (_) => validateStageOneFields());
    ever(stateName, (_) => validateStageOneFields());
    ever(cityName, (_) => validateStageOneFields());

    // Initial validation based on any pre‑filled data.
    validateStageOneFields();

    // Load initial data with error handling
    await loadInitialData();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();
      isLoading(true);

      if (serviceController.languages.isEmpty) {
        await _getLanguages();
      }
      await _getLabelTextDetail();

      errorStateManager.setSuccess();
      isLoading(false);
    } on SocketException {
      logger.error("Network error in loadInitialData: SocketException");
      isLoading(false);
      errorStateManager.setError(
        "No internet connection. Please check your network and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } on TimeoutException {
      logger.error("Timeout error in loadInitialData");
      isLoading(false);
      errorStateManager.setError(
        "Request timed out. Please check your connection and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } catch (error) {
      logger.error("Error in loadInitialData: $error");
      isLoading(false);

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
          "Unable to load page data. Please check your connection and try again.",
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
    firstNameTextEditingController.text = "";
    lastNameTextEditingController.text = "";
    gender.value = "";
    dobTextEditingController.text = "";
    countryName.value = "";
    stateName.value = "";
    cityName.value = "";
    postalCodeTextEditingController.text = "";
    miniBioTextEditingController.text = "";
    firstNameTextEditingController.dispose();
    lastNameTextEditingController.dispose();
    dobTextEditingController.dispose();
    searchTextEditingController.dispose();
    postalCodeTextEditingController.dispose();
    miniBioTextEditingController.dispose();
  }

  /// Parses DOB from the Stage One field (`MMMM dd, y` from the date picker).
  DateTime? _tryParseStageOneDob(String raw) {
    final s = raw.trim();
    if (s.isEmpty) return null;
    try {
      return DateFormat('MMMM dd, y').parseStrict(s);
    } catch (_) {
      return null;
    }
  }

  bool _isDobUnderMinimumAge(DateTime dob) {
    final cutoff = latestBirthDateForMinimumAge(minimumProfileAgeYears);
    return dob.isAfter(cutoff);
  }

  /// Simple client‑side validation for all required Stage One fields.
  /// Keeps `isStageOneValid` in sync so the button can enable/disable reactively.
  void validateStageOneFields() {
    final hasFirstName = firstNameTextEditingController.text.trim().isNotEmpty;
    final hasLastName = lastNameTextEditingController.text.trim().isNotEmpty;
    final hasGender = gender.value.trim().isNotEmpty;
    final dobRaw = dobTextEditingController.text.trim();
    final hasDob = dobRaw.isNotEmpty;
    final parsedDob = hasDob ? _tryParseStageOneDob(dobRaw) : null;
    final dobMeetsAge = parsedDob != null && !_isDobUnderMinimumAge(parsedDob);
    dobFailsMinimumAge.value =
        hasDob && (parsedDob == null || _isDobUnderMinimumAge(parsedDob));
    final hasCountry = countryName.value.trim().isNotEmpty;
    final hasState = stateName.value.trim().isNotEmpty;
    final hasCity = cityName.value.trim().isNotEmpty;
    final hasPostalCode =
        postalCodeTextEditingController.text.trim().isNotEmpty;
    final hasMiniBio = miniBioTextEditingController.text.trim().isNotEmpty;

    isStageOneValid.value = hasFirstName &&
        hasLastName &&
        hasGender &&
        hasDob &&
        dobMeetsAge &&
        hasCountry &&
        hasState &&
        hasCity &&
        hasPostalCode &&
        hasMiniBio;
  }

  updateGenderValue(value) async {
    if (value == "male") {
      gender.value = value;
    } else if (value == "female") {
      gender.value = value;
    } else if (value == "prefer not to say") {
      gender.value = value;
    } else {
      gender.value = "";
    }
  }

  Future<void> _getLanguages() async {
    try {
      final resp = await LoginProvider().getLanguages();

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['languages'] != null) {
          serviceController.languages.clear();
          serviceController.languages.addAll(resp['data']['languages']);

          if (serviceController.langId.value == 0) {
            var getDefaultLanguage = serviceController.languages
                .firstWhereOrNull((element) => element['is_default'] == "1");
            if (getDefaultLanguage != null) {
              serviceController.langId.value =
                  int.parse(getDefaultLanguage['id'].toString());
            }
          }

          var getLanguage = serviceController.languages.firstWhereOrNull(
              (element) => element['id'] == serviceController.langId.value);
          if (getLanguage != null) {
            serviceController.langIcon.value = getLanguage['flag_icon'];
            serviceController.lang.value = getLanguage['abbreviation'];
          }
        }
      } else {
        throw {
          "type": "server",
          "message": resp['message'] ?? "Failed to load languages."
        };
      }
    } on SocketException {
      throw {
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      };
    } on TimeoutException {
      throw {
        "type": "network",
        "message":
            "Request timed out. Please check your connection and try again."
      };
    } catch (error) {
      if (error is Map && error.containsKey('type')) {
        rethrow;
      }
      throw {
        "type": "unknown",
        "message": "Unable to load languages. Please try again."
      };
    }
  }

  Future<void> _getLabelTextDetail() async {
    try {
      final resp = await StageProvider().getLabelTextDetail(
          serviceController.langId.value, step1Page, serviceController.token);

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['step1Page'] != null) {
          labelTextDetail.addAll(resp['data']['step1Page']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }

        var getLanguage = serviceController.languages.firstWhereOrNull(
            (element) => element['id'] == serviceController.langId.value);
        if (getLanguage != null) {
          serviceController.langIcon.value = getLanguage['flag_icon'];
          serviceController.lang.value = getLanguage['abbreviation'];
        }
      } else {
        throw {
          "type": "server",
          "message": resp['message'] ?? "Failed to load page details."
        };
      }
    } on SocketException {
      throw {
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      };
    } on TimeoutException {
      throw {
        "type": "network",
        "message":
            "Request timed out. Please check your connection and try again."
      };
    } catch (error) {
      if (error is Map && error.containsKey('type')) {
        rethrow;
      }
      throw {
        "type": "unknown",
        "message": "Unable to load page details. Please try again."
      };
    }
  }

  setStageOne() async {
    countryCode = CountryCodeFinder.findCode(countryName.toString());

    try {
      final dobTrim = dobTextEditingController.text.trim();
      final dobParsed = dobTrim.isEmpty ? null : _tryParseStageOneDob(dobTrim);
      final dobEmpty = dobTrim.isEmpty;
      final dobInvalidOrUnderage = !dobEmpty &&
          (dobParsed == null || _isDobUnderMinimumAge(dobParsed));

      if (firstNameTextEditingController.text.isEmpty ||
          lastNameTextEditingController.text.isEmpty ||
          gender.value.isEmpty ||
          dobEmpty ||
          dobInvalidOrUnderage ||
          countryName.isEmpty ||
          stateName.isEmpty ||
          cityName.isEmpty ||
          postalCodeTextEditingController.text.isEmpty ||
          miniBioTextEditingController.text.isEmpty) {
        if (firstNameTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute",
              labelTextDetail['first_name_error'] ?? 'First name');
          var err = {
            'title': "first",
            'eList': [message ?? 'First name field is required']
          };
          errors.add(err);
        }

        if (lastNameTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['last_name_error'] ?? 'Last name');
          var err = {
            'title': "last",
            'eList': [message ?? 'Last name field is required']
          };
          errors.add(err);
        }

        if (gender.value.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['gender_error'] ?? 'Gender');
          var err = {
            'title': "gender",
            'eList': [message ?? 'Gender is required']
          };
          errors.add(err);
        }

        if (dobEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['dob_error'] ?? 'Date');
          var err = {
            'title': "dob",
            'eList': [message ?? 'Date is required']
          };
          errors.add(err);
        } else if (dobParsed == null) {
          errors.add({
            'title': "dob",
            'eList': ['Please enter a valid date of birth.']
          });
        } else if (_isDobUnderMinimumAge(dobParsed)) {
          errors.add({
            'title': "dob",
            'eList': [
              'You must be at least $minimumProfileAgeYears years old.'
            ]
          });
        }

        if (countryName.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['country_error'] ?? 'Country name');
          var err = {
            'title': "country",
            'eList': [message ?? 'Country name field is required']
          };
          errors.add(err);
        }

        if (stateName.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['state_error'] ?? 'State/province');
          var err = {
            'title': "state",
            'eList': [message ?? 'State/province is required']
          };
          errors.add(err);
        }

        if (cityName.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['city_error'] ?? 'City');
          var err = {
            'title': "city",
            'eList': [message ?? 'City field is required']
          };
          errors.add(err);
        }

        if (postalCodeTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['zip_code_error'] ?? 'Postal code');
          var err = {
            'title': "zipcode",
            'eList': [message ?? 'Postal code field is required']
          };
          errors.add(err);
        }

        if (miniBioTextEditingController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(
              ":Attribute", labelTextDetail['bio_error'] ?? 'Mini bio');
          var err = {
            'title': "mini",
            'eList': [message ?? 'Mini bio is required']
          };
          errors.add(err);
        }

        return;
      }

      final stateForRequest =
          stateName.value == "Not Applicable" ? "0" : stateId.value.toString();
      final cityForRequest =
          cityName.value == "Not Applicable" ? "0" : cityId.value.toString();

      isOverlayLoading(true);
      StageProvider()
          .setStageOne(
              serviceController.token,
              firstNameTextEditingController.text.trim(),
              lastNameTextEditingController.text,
              gender.value,
              dobTextEditingController.text,
              countryId.value.toString(),
              stateForRequest,
              cityForRequest,
              postalCodeTextEditingController.text,
              miniBioTextEditingController.text)
          .then((resp) async {
        errorList.clear();
        logger.info("Response: $resp");
        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString(),
              type: "error");
        } else if (resp['errors'] != null) {
          if (resp['errors']['first_name'] != null) {
            errorList.addAll(resp['errors']['first_name']);
          }
          if (resp['errors']['last_name'] != null) {
            errorList.addAll(resp['errors']['last_name']);
          }
          if (resp['errors']['zipcode'] != null) {
            var err = {'title': "zipcode", 'eList': resp['errors']['zipcode']};
            errors.add(err);
          }
          if (resp['errors']['about'] != null) {
            var err = {'title': "mini", 'eList': resp['errors']['about']};
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.loginUserDetail['step'] = "2";
          serviceController.loginUserDetail['gender'] =
              resp['data']['gender'].toString();

          serviceController.loginUserDetail['country_code'] =
              countryCode.toString();

          serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));

          stepNo.value = serviceController.loginUserDetail['step'].toString();

          // firstNameTextEditingController.text = "";
          // lastNameTextEditingController.text = "";
          // gender.value = "";
          // dobTextEditingController.text = "";
          // countryName.value = "";
          // stateName.value = "";
          // cityName.value = "";
          // postalCodeTextEditingController.text = "";
          // miniBioTextEditingController.text = "";
          Get.toNamed('/stage_two');
          // serviceController.showDialogue(resp['message'].toString());
          // serviceController.showDialogue(resp['message'].toString(),off: 1,path: '/stage_two');
        }
        isOverlayLoading(false);
      }, onError: (err) {
        isOverlayLoading(false);

        // Parse structured error from provider
        if (err is Map && err.containsKey('message')) {
          serviceController.showDialogue(err['message'], type: "error");
        } else {
          serviceController.showDialogue(
              "Failed to save information. Please try again.",
              type: "error");
        }
      });
    } catch (exception) {
      isOverlayLoading(false);

      // Parse structured error from provider
      if (exception is Map && exception.containsKey('message')) {
        serviceController.showDialogue(exception['message'], type: "error");
      } else if (exception.toString().contains("SocketException") ||
          exception.toString().contains("Network is unreachable")) {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue(
            "Failed to save information. Please try again.",
            type: "error");
      }
    }
  }
}
