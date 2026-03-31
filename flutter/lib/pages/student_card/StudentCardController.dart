import 'dart:async';
import 'dart:convert';
import 'dart:io';

import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/pages/profile_setting/ProfileSettingController.dart';
import 'package:proximaride_app/pages/student_card/StudentCardProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/services/service.dart';

class StudentCardController extends GetxController {
  var isOverlayLoading = false.obs;
  var isLoading = false.obs;
  final errorStateManager = ErrorStateManager();
  late final ConnectivityService connectivityService;

  final serviceController = Get.find<Service>();

  var errorList = List.empty(growable: true).obs;
  var errors = [].obs;

  var studentCardImageName = "".obs;
  var studentCardImagePath = "".obs;
  var oldImagePath = "".obs;
  var studentCardImageNameOriginal = "".obs;
  var studentCardImagePathOriginal = "".obs;
  var studentCardImagePathOriginalOld = "".obs;
  var month = "".obs;
  var day = "".obs;
  var year = "".obs;
  var daysLength = 31.obs;
  var totalYear = 1;
  var startYear = DateTime.now().year;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;
  var popupTextDetail = {}.obs;
  var isFormDirty = false.obs;

  @override
  void onInit() async {
    super.onInit();

    if (Get.isRegistered<ProfileSettingController>()) {
      final profileSettingController = Get.find<ProfileSettingController>();
      final title =
          profileSettingController.labelTextDetail['my_student_card_label'];
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

    await loadInitialData();

    ever<String>(month, (_) => _markDirtyIfLoaded());
    ever<String>(year, (_) => _markDirtyIfLoaded());
  }

  @override
  void onClose() {
    super.onClose();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      await _getStudentCard();

      errorStateManager.setSuccess();
      isFormDirty.value = false;
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
          "Unable to load student card settings. Please check your connection and try again.",
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

  Future<void> _getStudentCard() async {
    await StudentCardProvider()
        .getStudentCard(serviceController.token, serviceController.langId.value)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['user'] != null) {
          oldImagePath.value = resp['data']['user']['student_card'] ?? "";

          studentCardImagePathOriginalOld.value =
              resp['data']['user']['student_card_original_upload'] ?? "";

          if (resp['data']['user']['student_card_exp_date'] != null &&
              resp['data']['user']['student_card_exp_date'] != "") {
            DateTime dateTime =
                DateTime.parse(resp['data']['user']['student_card_exp_date']);

            day.value = dateTime.day.toString();
            month.value = dateTime.month.toString();
            year.value = dateTime.year.toString();

            if (day.value.length == 1) {
              day.value = "0${day.value}";
            }
            if (month.value.length == 1) {
              month.value = "0${month.value}";
            }
          }
        }

        if (resp['data'] != null && resp['data']['studentCardPage'] != null) {
          labelTextDetail.addAll(resp['data']['studentCardPage']);
        }

        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }

        if (resp['data'] != null && resp['data']['messages'] != null) {
          popupTextDetail.addAll(resp['data']['messages']);
        }
      }
    }, onError: (err) {
      throw err; // Propagate to loadInitialData
    });
  }

  updateStudentCard() async {
    errors.clear();
    try {
      if (oldImagePath.value == "" && studentCardImageName.value == "" ||
          month.value == "" ||
          year.value == "") {
        if (oldImagePath.value == "" && studentCardImageName.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute",
              labelTextDetail['photo_error'] ?? 'Student card image');
          var err = {
            'title': "student_card",
            'eList': [message ?? 'Student card image is required']
          };
          errors.add(err);
        }
        if (month.value == "" || year.value == "") {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute",
              labelTextDetail['photo_error'] ?? 'Expiration date');
          var err = {
            'title': "student_card_exp_date",
            'eList': [message ?? 'Expiration date is required']
          };
          errors.add(err);
        }
        return;
      }

      if (studentCardImagePathOriginal.value != "") {
        final file = File(studentCardImagePathOriginal.value);
        int sizeInBytes = file.lengthSync();
        double sizeInMb = sizeInBytes / (1024 * 1024);
        if (sizeInMb > 10) {
          var message = validationMessageDetail['max.file'];
          message = message.replaceAll(":attribute",
              labelTextDetail['photo_error'] ?? 'student card image');
          message = message.replaceAll(":max", '10');
          var err = {
            'title': "student_card",
            'eList': [message ?? 'Can not upload image size greater than 10MB']
          };
          errors.add(err);
          return;
        }
      }

      isOverlayLoading(true);

      if (month.value == "01") {
        day.value = "31";
      }
      if (month.value == "02") {
        day.value = "28";
      }
      if (month.value == "03") {
        day.value = "31";
      }
      if (month.value == "04") {
        day.value = "30";
      }
      if (month.value == "05") {
        day.value = "31";
      }
      if (month.value == "06") {
        day.value = "30";
      }
      if (month.value == "07") {
        day.value = "31";
      }
      if (month.value == "08") {
        day.value = "31";
      }
      if (month.value == "09") {
        day.value = "30";
      }
      if (month.value == "10") {
        day.value = "31";
      }
      if (month.value == "11") {
        day.value = "30";
      }
      if (month.value == "12") {
        day.value = "31";
      }

      String dateString = '${year.value}-${month.value}-${day.value}';

      DateTime dateTime = DateFormat('yyyy-MM-dd').parse(dateString);

      String formattedDate = DateFormat('yyyy-MM-dd').format(dateTime);

      StudentCardProvider()
          .updateStudentCard(
              studentCardImageName.value,
              studentCardImagePath.value,
              studentCardImageNameOriginal.value,
              studentCardImagePathOriginal.value,
              formattedDate,
              serviceController.token,
              serviceController.loginUserDetail['id'])
          .then((resp) async {
        errorList.clear();

        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString(),
              type: "error");
        } else if (resp['errors'] != null) {
          if (resp['errors']['student_card'] != null) {
            serviceController.showDialogue(resp['message'].toString(),
                type: "error");
            var err = {
              'title': "student_card",
              'eList': resp['errors']['student_card']
            };
            errors.add(err);
          }
          if (resp['errors']['student_card_exp_date'] != null) {
            var err = {
              'title': "student_card_exp_date",
              'eList': resp['errors']['student_card_exp_date']
            };
            logger.info(err.toString());
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {
          logger.info(formattedDate.toString());
          serviceController.loginUserDetail['student'] = "1";
          serviceController.loginUserDetail['student_card_exp_date'] =
              formattedDate;
          serviceController.secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));

          Get.back();
          logger.info(resp['message'].toString());
          serviceController.showHtmlDialogue(resp['message'].toString(),
              title: labelTextDetail['student_card_uploaded_successfully'] ??
                  "Student Card Uploaded!");
          isFormDirty.value = false;
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

  void getImage(ImageSource imageSource) async {
    final croppedFile = await serviceController.imageCropper(imageSource);

    if (croppedFile != null) {
      oldImagePath.value = "";
      studentCardImagePath.value = croppedFile.path;
      studentCardImageName.value = croppedFile.path.split('/').last;
      studentCardImagePathOriginal.value =
          serviceController.originalImagePath.value;
      serviceController.originalImagePath.value = "";
      studentCardImageNameOriginal.value =
          serviceController.originalImageName.value;
      serviceController.originalImageName.value = "";
      Get.back();
    }
    _markDirtyIfLoaded();
  }

  void _markDirtyIfLoaded() {
    if (!isLoading.value) {
      isFormDirty.value = true;
    }
  }

  removeStudentCard() async {
    bool isConfirmed = await serviceController.showConfirmationDialog(
        cancelYesBtn: "Yes, remove it!",
        "${popupTextDetail['remove_student_card_message'] ?? "Are you sure you want to remove the student card"}");
    if (isConfirmed) {
      try {
        isOverlayLoading(true);
        StudentCardProvider().removeStudentCard(serviceController.token).then(
            (resp) async {
          if (resp['status'] != null && resp['status'] == "Error") {
            serviceController.showDialogue(resp['message'].toString(),
                type: "error");
          } else if (resp['status'] != null && resp['status'] == "Success") {
            serviceController.loginUserDetail['student'] = "0";
            serviceController.loginUserDetail['student_card_exp_date'] = "";
            await serviceController.secureStorage.write(
                key: "userInfo",
                value: jsonEncode(serviceController.loginUserDetail));
            serviceController.showDialogue(resp['message'].toString(),
                type: "success");

            oldImagePath.value = "";
            studentCardImageName.value = "";
            studentCardImagePath.value = "";
            studentCardImageNameOriginal.value = "";
            studentCardImagePathOriginal.value = "";
            day.value = "";
            month.value = "";
            year.value = "";
            isFormDirty.value = false;
          }
          isOverlayLoading(false);
        }, onError: (err) {
          isOverlayLoading(false);
          if (err is Map &&
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
}
