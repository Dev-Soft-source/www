import 'dart:async';
import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/color.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/consts/font.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/helpers/format_message.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/pages/my_phone_number/MyPhoneNumberProvider.dart';
import 'package:proximaride_app/pages/navigation/NavigationController.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class StageFiveController extends GetxController {
  final serviceController = Get.find<Service>();
  final errorStateManager = ErrorStateManager();
  final secureStorage = const FlutterSecureStorage();

  final isOverlayLoading = false.obs;
  final isLoading = false.obs;
  final errorList = List.empty(growable: true).obs;
  final errors = [].obs;
  final stepNo = "0".obs;
  final finishBtn = false.obs;
  final secondsRemaining = 5.obs;

  final isPhoneFormValid = false.obs;
  final isVerificationCodeEntered = false.obs;

  final labelTextDetail = {}.obs;
  final popupTextDetail = {}.obs;
  final validationMessageDetail = {}.obs;

  late TextEditingController countryCodeTextEditingController;
  late TextEditingController phoneNumberTextEditingController;

  Timer? timer;
  String verificationCode = "";

  @override
  void onInit() async {
    super.onInit();

    countryCodeTextEditingController = TextEditingController();
    phoneNumberTextEditingController = TextEditingController();

    logger.info(
        "Country Code: ${serviceController.loginUserDetail['country_code']}");

    if (serviceController.loginUserDetail['country_code'].toString() !=
        "null") {
      countryCodeTextEditingController.text =
          serviceController.loginUserDetail['country_code'].toString();
    } else {
      countryCodeTextEditingController.text = "+1";
    }

    stepNo.value = serviceController.loginUserDetail['step'].toString();

    countryCodeTextEditingController.addListener(validatePhoneFormFields);
    phoneNumberTextEditingController.addListener(validatePhoneFormFields);
    validatePhoneFormFields();

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
    timer?.cancel();
    countryCodeTextEditingController.dispose();
    phoneNumberTextEditingController.dispose();
    super.onClose();
  }

  void validatePhoneFormFields() {
    final hasCode = countryCodeTextEditingController.text.trim().isNotEmpty;
    final hasNumber = phoneNumberTextEditingController.text.trim().isNotEmpty;
    isPhoneFormValid.value = hasCode && hasNumber;
  }

  void updateVerificationCodeEntered(String code) {
    verificationCode = code;
    isVerificationCodeEntered.value = verificationCode.trim().isNotEmpty;
  }

  Future<void> _getLanguages() async {
    try {
      final resp = await LoginProvider().getLanguages();

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['languages'] != null) {
          serviceController.languages.clear();
          serviceController.languages.addAll(resp['data']['languages']);

          if (serviceController.langId.value == 0) {
            final getDefaultLanguage = serviceController.languages
                .firstWhereOrNull((element) => element['is_default'] == "1");
            if (getDefaultLanguage != null) {
              serviceController.langId.value =
                  int.parse(getDefaultLanguage['id'].toString());
            }
          }

          final getLanguage = serviceController.languages.firstWhereOrNull(
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
          serviceController.langId.value, step5Page, serviceController.token);

      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['step5Page'] != null) {
          labelTextDetail.addAll(resp['data']['step5Page']);
        }

        if (resp['data'] != null && resp['data']['messages'] != null) {
          popupTextDetail.addAll(resp['data']['messages']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }

        final getLanguage = serviceController.languages.firstWhereOrNull(
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

  void startTimer() {
    timer?.cancel();
    timer = Timer.periodic(const Duration(seconds: 1), (currentTimer) {
      if (secondsRemaining.value == 0) {
        currentTimer.cancel();
      } else {
        secondsRemaining.value--;
      }
    });
  }

  Future<void> setStageFive(bool skip) async {
    errors.clear();
    try {
      logger.info("setStageFive called with skip= $skip");
      logger.info(
          "countryCodeTextEditingController.text= ${countryCodeTextEditingController.text}");
      logger.info(
          "phoneNumberTextEditingController.text= ${phoneNumberTextEditingController.text}");

      if (!skip) {
        if (countryCodeTextEditingController.text.isEmpty ||
            phoneNumberTextEditingController.text.isEmpty) {
          if (countryCodeTextEditingController.text.isEmpty) {
            var message = validationMessageDetail['required'];
            message = message.replaceAll(
                ":Attribute", labelTextDetail['country_code_error'] ?? 'Code');
            errors.add({
              'title': "code",
              'eList': [message ?? 'Code is required']
            });
          }
          if (phoneNumberTextEditingController.text.isEmpty) {
            var message = validationMessageDetail['required'];
            message = message.replaceAll(
                ":Attribute", labelTextDetail['phone_error'] ?? 'Phone number');
            errors.add({
              'title': "number",
              'eList': [message ?? 'Number is required']
            });
          }
          return;
        }
      }

      isOverlayLoading(true);
      StageProvider()
          .setStageFour(
              "${countryCodeTextEditingController.text} ${phoneNumberTextEditingController.text}",
              serviceController.token,
              skip ? "1" : "0")
          .then((resp) async {
        logger.info("setStageFive response: $resp");

        if (resp['status'] != null && resp['status'] == "Success") {
          serviceController.loginUserDetail['step'] = "5";
          serviceController.loginUserDetail.refresh();
          serviceController.secureStorage.write(
            key: "userInfo",
            value: jsonEncode(serviceController.loginUserDetail),
          );
          stepNo.value = serviceController.loginUserDetail['step'].toString();
          countryCodeTextEditingController.text = "";
          phoneNumberTextEditingController.text = "";

          if (skip) {
            await Get.defaultDialog(
              title: "Important!",
              titlePadding: const EdgeInsets.symmetric(vertical: 12),
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
              radius: 10,
              barrierDismissible: false,
              titleStyle: const TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
              ),
              middleText: formatMessage(
                  "Please keep in mind that you are not permitted to use Pink Rides and Extra-Care Rides until you verify your phone number."),
              middleTextStyle: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.normal,
              ),
              actions: [
                ElevatedButton(
                  onPressed: () {
                    Get.back();
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: btnPrimaryColor,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(5),
                    ),
                  ),
                  child: txt16SizeWithOutContext(
                    title: "Noted",
                    textColor: Colors.white,
                    fontFamily: regular,
                  ),
                ),
              ],
            );
          } else {
            await Get.defaultDialog(
              title: "Important!",
              titlePadding: const EdgeInsets.symmetric(vertical: 12),
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
              radius: 10,
              barrierDismissible: false,
              titleStyle: const TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
              ),
              middleText: formatMessage(
                  "Your phone number is saved Unverified. Keep in mind that you are not permitted to use Pink Rides and Extra-Care Rides until you verify it."),
              middleTextStyle: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.normal,
              ),
              actions: [
                ElevatedButton(
                  onPressed: () {
                    Get.back();
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: btnPrimaryColor,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(5),
                    ),
                  ),
                  child: txt16SizeWithOutContext(
                    title: "Noted",
                    textColor: Colors.white,
                    fontFamily: regular,
                  ),
                ),
              ],
            );
          }

          await Get.defaultDialog(
            title: "Welcome to ProximaRide!",
            titlePadding: const EdgeInsets.symmetric(vertical: 12),
            contentPadding:
                const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
            radius: 10,
            barrierDismissible: false,
            titleStyle: const TextStyle(
              fontSize: 22,
              fontWeight: FontWeight.bold,
            ),
            middleText: "Your profile is all set. Let's get started.",
            middleTextStyle: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.normal,
            ),
            actions: [
              ElevatedButton(
                onPressed: () {
                  Get.back();
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: btnPrimaryColor,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(5),
                  ),
                ),
                child: txt16SizeWithOutContext(
                  title: "OK",
                  textColor: Colors.white,
                  fontFamily: regular,
                ),
              ),
            ],
          );

          Get.put(NavigationController());
          Get.offAllNamed('/navigation');
        } else if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString(),
              type: "error");
        }
        isOverlayLoading(false);
      }, onError: (error) {
        serviceController.showDialogue(error.toString(), type: "error");
        isOverlayLoading(false);
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
      isOverlayLoading(false);
    }
  }

  void sendVerificationCode({String phoneId = "0"}) {
    errors.clear();
    if (phoneId == "0") {
      if (countryCodeTextEditingController.text == "" ||
          phoneNumberTextEditingController.text == "") {
        var message = validationMessageDetail['required'];
        message = message.replaceAll(":Attribute",
            labelTextDetail['phone_error'] ?? 'Code and phone number');
        errors.add({
          'title': "number",
          'eList': [message ?? 'Code and phone number field is required']
        });
        return;
      }
    }

    final phoneNumber = countryCodeTextEditingController.text +
        phoneNumberTextEditingController.text;
    serviceController.showDialogue('Verification code sent to your phone.',
        type: "info");
    MyPhoneNumberProvider()
        .sendVerificationCode(serviceController.token, phoneNumber, phoneId);
    secondsRemaining.value = 10;
    startTimer();
    finishBtn.value = true;
  }

  Future<void> verifyPhoneNumber() async {
    if (verificationCode == "") {
      serviceController.showDialogue(
          "${popupTextDetail['enter_code_message'] ?? "Please enter code first"}",
          type: "warning");
      return;
    }

    isOverlayLoading(true);
    try {
      final resp = await MyPhoneNumberProvider()
          .verifyPhone(serviceController.token, verificationCode);
      if (resp['status'] != null && resp['status'] == "Success") {
        await _setPrimaryPhoneFromResponse(resp['data']);
        serviceController.loginUserDetail['step'] = "5";
        serviceController.loginUserDetail.refresh();
        serviceController.secureStorage.write(
            key: "userInfo",
            value: jsonEncode(serviceController.loginUserDetail));
        stepNo.value = serviceController.loginUserDetail['step'].toString();
        verificationCode = "";
        await Get.defaultDialog(
          title: "Welcome!",
          titlePadding: const EdgeInsets.symmetric(vertical: 12),
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
          radius: 10,
          barrierDismissible: false,
          titleStyle: const TextStyle(
            fontSize: 22,
            fontWeight: FontWeight.bold,
          ),
          middleText: "Your profile is all set. Welcome to ProximaRide!",
          middleTextStyle: const TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.normal,
          ),
          actions: [
            ElevatedButton(
              onPressed: () {
                Get.back();
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: btnPrimaryColor,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(5),
                ),
              ),
              child: txt16SizeWithOutContext(
                title: "Close",
                textColor: Colors.white,
                fontFamily: regular,
              ),
            ),
          ],
        );
        Get.put(NavigationController());
        Get.offAllNamed('/navigation');
      } else {
        serviceController.showDialogue(resp['message'].toString(),
            type: "error");
      }
    } catch (error) {
      serviceController.showDialogue(error.toString(), type: "error");
    } finally {
      isOverlayLoading(false);
      isLoading.value = false;
    }
  }

  Future<void> _setPrimaryPhoneFromResponse(dynamic data) async {
    try {
      final phoneId = data?['phone_number']?['id']?.toString() ??
          data?['phone_number_id']?.toString();
      if (phoneId == null || phoneId.isEmpty) {
        return;
      }
      await MyPhoneNumberProvider()
          .setAsDefaultNumber(serviceController.token, phoneId);
    } catch (error) {
      logger.warning(
          'Failed to auto-set primary phone during stage five: $error');
    }
  }
}
