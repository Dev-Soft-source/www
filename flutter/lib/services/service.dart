import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:image_cropper/image_cropper.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/format_message.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:url_launcher/url_launcher.dart';
import '../pages/navigation/navigationProvider.dart';

class Service extends GetxService {
  var height = 0.0;
  var width = 0.0;

  var backgroundNotification = "";
  final languageId = '2'.obs;
  final lang = "en".obs;
  final currency = 'Rs'.obs;

  final categoryName = "".obs;
  final categorySlug = "".obs;
  final bannerImage = "".obs;

  final loginUserId = 0.obs;

  final storeSellerId = "0".obs;

  final productLayout = "grid".obs;

  var unreadMessage = false.obs;

  var navigationIndex = 0.obs;

  var originalImagePath = "".obs;
  var originalImageName = "".obs;
  var showImage = "".obs;

  var verifyEmail = "";
  var langId = 0.obs;
  var langIcon = "".obs;
  var openDeepLinkPage = false.obs;
  var bookingDeepId = "".obs;
  var actionDeep = "".obs;

  var isLoading = false.obs;

  final loginUserDetail = {
    "id": 0,
    "first_name": "",
    "last_name": "",
    "gender": "",
    "profile_image": "",
    "profile_original_image": "",
    "countryId": "",
    "country_code": "",
    "email": "",
    "step": "0",
    "student": "0",
    "driver": "0",
    "student_card_exp_date": "",
    "driver_average_rating": "",
    "passenger_average_rating": "",
    "user_average_rating": "",
    "driver_total_ratings": "",
    "passenger_total_ratings": "",
    "user_total_ratings": "",
    "langId": "",
    "driver_liscense": ""
  }.obs;

  String token = "";
  final secureStorage = const FlutterSecureStorage();

  final addCartProductCount = 0.obs;

  final checkOutCount = 0.obs;

  var myCart = List<dynamic>.empty(growable: true).obs;
  var buyItNow = List<dynamic>.empty(growable: true).obs;
  var bankDetails = List<dynamic>.empty(growable: true).obs;
  var installmentPlanDetails = List<dynamic>.empty(growable: true).obs;
  var languages = List<dynamic>.empty(growable: true).obs;

  var orderTotalAmt = 0.0.obs;
  var orderSubTotalAmt = 0.0.obs;
  var totalShippping = 0.0.obs;
  var totalShipppingCost = 0.0.obs;
  var checkOutType = "".obs;

  var logoutLabelTextDetail = {}.obs;

  var navigationChatLabel = "Chats".obs;
  var navigationMyTripLabel = "My trips".obs;
  var navigationMyProfileLabel = "My profile".obs;

  var termAndConditionLabel = "Terms and conditions".obs;
  var privacyPolicyLabel = "Privacy policy".obs;
  var termOfUseLabel = "Terms of use".obs;
  var refundPolicyLabel = "Refund policy".obs;
  var cancellationPolicyLabel = "Cancellation policy".obs;
  var disputePolicyLabel = "Disputes & Policies".obs;
  var coffeeOnWallLabel = "Coffee on the wall".obs;
  var closeBtnLabel = "Close".obs;
  var requestVerificationEmailLabel = "".obs;

  var welcomeMessage1 = "".obs;
  var welcomeMessage2 = "".obs;
  var welcomeButton1 = "".obs;
  var welcomeButton2 = "".obs;
  var imagePreviewLabel = "".obs;
  var notificationCount = 0.obs;
  var thankYouMessage = "".obs;

  var installmentTotalAmount = [];
  var installmentIds = [];

  var creditBalanceFormId = 0;
  var isOverlayLoading = false.obs;

  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    await getUserToken();
    await getUserInfo();
  }

  getUserInfo() async {
    if (token != "") {
      final rawUserInfo = await secureStorage.read(key: "userInfo") ?? "";
      if (rawUserInfo.trim().isEmpty) {
        logger.warning(
            "Stored token exists but userInfo is empty. Clearing stale session.");
        await _clearLocalSessionAndNavigateToLogin();
        return;
      }

      dynamic data;
      try {
        data = jsonDecode(rawUserInfo);
      } catch (error) {
        logger.error("Failed to decode stored userInfo: $error");
        await _clearLocalSessionAndNavigateToLogin();
        return;
      }

      if (data is! Map) {
        logger.warning(
            "Stored userInfo is not a JSON object. Clearing stale session.");
        await _clearLocalSessionAndNavigateToLogin();
        return;
      }

      loginUserDetail['id'] = data['id'];
      loginUserDetail['first_name'] = data['first_name'].toString();
      loginUserDetail['last_name'] = data['last_name'].toString();
      loginUserDetail['gender'] = data['gender'].toString();
      loginUserDetail['profile_original_image'] =
          data['profile_original_image'].toString();
      loginUserDetail['country_code'] = data['country_code'].toString();
      loginUserDetail['profile_image'] = data['profile_image'].toString();
      loginUserDetail['email'] = data['email'].toString();
      loginUserDetail['step'] = data['step'].toString();
      loginUserDetail['langId'] = data['langId'].toString();
      loginUserDetail['driver_liscense'] = data['driver_liscense'].toString();
      langId.value = int.parse(data['langId'].toString());

      var getLanguage = languages
          .firstWhereOrNull((element) => element['id'] == langId.value);
      if (getLanguage != null) {
        langIcon.value = getLanguage['flag_icon'];
        lang.value = getLanguage['abbreviation'];
      }
    }
  }

  getUserToken() async {
    token = await secureStorage.read(key: "token") ?? "";
  }

  Future<void> _clearLocalSessionAndNavigateToLogin() async {
    token = "";
    await secureStorage.deleteAll();
    loginUserDetail.assignAll({
      "id": 0,
      "first_name": "",
      "last_name": "",
      "gender": "",
      "profile_image": "",
      "profile_original_image": "",
      "countryId": "",
      "country_code": "",
      "email": "",
      "step": "0",
      "student": "0",
      "driver": "0",
      "student_card_exp_date": "",
      "driver_average_rating": "",
      "passenger_average_rating": "",
      "user_average_rating": "",
      "driver_total_ratings": "",
      "passenger_total_ratings": "",
      "user_total_ratings": "",
      "langId": "",
      "driver_liscense": ""
    });
    languages.clear();
    langId.value = 0;
    langIcon.value = "";
    isOverlayLoading(false);
    Get.offAllNamed('/login');
  }

  logoutUser() async {
    bool isConfirmed = await showConfirmationDialog(
        "${logoutLabelTextDetail['confirmation_message_heading'] ?? "Are you sure you want to log out?"}");

    if (isConfirmed) {
      try {
        isOverlayLoading.value = true;
        final currentToken = token;
        if (currentToken.isNotEmpty) {
          try {
            await NavigationProvider().removeFcmToken(currentToken);
          } catch (err) {
            logger.warning("removeFcmToken failed during logout: $err");
          }
        }
        await _clearLocalSessionAndNavigateToLogin();
      } catch (exception) {
        isOverlayLoading(false);
        showDialogue(exception.toString(), type: "error");
      }
    }
  }

  logoutUserFromStages() async {
    bool isConfirmed = await showConfirmationDialog(
        "Your current progress will be securely saved as you exit. You will be able to pick up right where you left off the next time you log in.",
        title: "Save Profile and Log Out?",
        cancelYesBtn: "Save & Log Out",
        cancelNoBtn: "Go back");

    if (isConfirmed) {
      try {
        isOverlayLoading.value = true;
        final currentToken = token;
        if (currentToken.isNotEmpty) {
          try {
            await NavigationProvider().removeFcmToken(currentToken);
          } catch (err) {
            logger.warning(
                "removeFcmToken failed during staged logout: $err");
          }
        }
        await _clearLocalSessionAndNavigateToLogin();
      } catch (exception) {
        isOverlayLoading(false);
        showDialogue(exception.toString(), type: "error");
      }
    }
  }

  showSnackBar(String title, String message, Color backgroundColor) {
    Get.snackbar('', message,
        snackPosition: SnackPosition.BOTTOM,
        backgroundColor: backgroundColor,
        colorText: Colors.white,
        duration: const Duration(days: 1000),
        mainButton: TextButton(
          onPressed: () {
            Get.back();
          },
          child: const Icon(
            Icons.close,
            color: Colors.white,
          ),
        ));
  }

  showToast(message, position, {String type = "info"}) {
    // Centralized color management for toasts
    Color toastColor;

    switch (type.toLowerCase()) {
      case "success":
        toastColor = successColor;
        break;
      case "error":
        toastColor = errorColor;
        break;
      case "warning":
        toastColor = warningColor;
        break;
      case "info":
      default:
        toastColor = Colors.black.withOpacity(0.7);
        break;
    }

    Fluttertoast.showToast(
        msg: message,
        toastLength: Toast.LENGTH_SHORT,
        gravity: position,
        timeInSecForIosWeb: 1,
        backgroundColor: toastColor,
        textColor: Colors.white,
        fontSize: 12.0);
  }

  Future<bool> showConfirmationDialog(message,
      {String cancelYesBtn = "",
      String cancelNoBtn = "",
      String title = "Confirm!"}) async {
    return await Get.dialog(
      Dialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        elevation: 8,
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Icon
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: primaryColor.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.help_outline_rounded,
                  color: primaryColor,
                  size: 48,
                ),
              ),
              const SizedBox(height: 20),
              // Title
              Text(
                title,
                style: const TextStyle(
                  fontSize: 26,
                  fontWeight: FontWeight.bold,
                  color: textColor,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 12),
              // Message
              Text(
                message,
                style: TextStyle(
                  fontSize: 20,
                  color: textColor.withOpacity(0.7),
                  height: 1.5,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 28),
              // Buttons
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () {
                        Get.back(result: false);
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.grey[100],
                        foregroundColor: textColor,
                        elevation: 0,
                        minimumSize: const Size.fromHeight(buttonHeight),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(5),
                        ),
                      ),
                      child: cancelNoBtn != ""
                          ? txt20SizeWithOutContext(
                              title: cancelNoBtn,
                              textColor: textColor,
                              fontFamily: buttonFontFamily)
                          : txt20SizeWithOutContext(
                              title:
                                  "${logoutLabelTextDetail['confirmation_no_label'] ?? "No"}",
                              textColor: textColor,
                              fontFamily: buttonFontFamily),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () {
                        Get.back(result: true);
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: btnPrimaryColor,
                        foregroundColor: Colors.white,
                        elevation: 0,
                        minimumSize: const Size.fromHeight(buttonHeight),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(5),
                        ),
                      ),
                      child: cancelYesBtn != ""
                          ? txt20SizeWithOutContext(
                              title: cancelYesBtn,
                              textColor: Colors.white,
                              fontFamily: buttonFontFamily)
                          : txt20SizeWithOutContext(
                              title:
                                  "${logoutLabelTextDetail['confirmation_yes_label'] ?? "Yes"}",
                              textColor: Colors.white,
                              fontFamily: buttonFontFamily),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
      barrierDismissible: false,
    ).then((value) => value ?? false);
  }

  showHtmlDialogue(String html,
      {String title = "", int off = 0, String path = ""}) async {
    return await Get.dialog(
      Dialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        elevation: 8,
        child: Container(
          constraints: BoxConstraints(
            maxHeight: Get.height * 0.75,
            maxWidth: Get.width * 0.9,
          ),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Header
              if (title != "")
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: primaryColor.withOpacity(0.05),
                    borderRadius: const BorderRadius.only(
                      topLeft: Radius.circular(20),
                      topRight: Radius.circular(20),
                    ),
                  ),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: primaryColor.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Icon(
                          Icons.info_outline_rounded,
                          color: primaryColor,
                          size: 24,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          title,
                          style: const TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            color: textColor,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              // Content
              Flexible(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(20),
                  child: Html(
                    data: html,
                    style: {
                      "body": Style(
                        padding: HtmlPaddings.zero,
                        margin: Margins.zero,
                      ),
                      'p': Style(
                        fontSize: FontSize(20),
                        padding: HtmlPaddings.symmetric(vertical: 8),
                        margin: Margins.zero,
                        lineHeight: const LineHeight(1.6),
                      ),
                      'div': Style(
                        fontSize: FontSize(20),
                        padding: HtmlPaddings.symmetric(vertical: 8),
                        margin: Margins.zero,
                        lineHeight: const LineHeight(1.6),
                      ),
                      'h1': Style(
                        fontSize: FontSize(28),
                        fontWeight: FontWeight.bold,
                        padding: HtmlPaddings.only(bottom: 12),
                      ),
                      'h2': Style(
                        fontSize: FontSize(24),
                        fontWeight: FontWeight.bold,
                        padding: HtmlPaddings.only(bottom: 10),
                      ),
                      'h3': Style(
                        fontSize: FontSize(22),
                        fontWeight: FontWeight.bold,
                        padding: HtmlPaddings.only(bottom: 8),
                      ),
                    },
                  ),
                ),
              ),
              // Button
              Container(
                padding: const EdgeInsets.fromLTRB(20, 12, 20, 20),
                child: SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () {
                      if (off == 0) {
                        Get.back();
                      } else if (off == 1) {
                        Get.offAllNamed(path);
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: btnPrimaryColor,
                      foregroundColor: Colors.white,
                      elevation: 0,
                      minimumSize: const Size.fromHeight(buttonHeight),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(5),
                      ),
                    ),
                    child: txt20SizeWithOutContext(
                        title: closeBtnLabel.value,
                        textColor: Colors.white,
                        fontFamily: buttonFontFamily),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
      barrierDismissible: false,
    );
  }

  showDialogue(message,
      {off = 0, path = "", link = "", title = "", String type = "info"}) async {
    // Centralized icon and color management based on type
    IconData dialogIcon;
    Color dialogColor;

    switch (type.toLowerCase()) {
      case "success":
        dialogIcon = Icons.check_circle_outline_rounded;
        dialogColor = successColor;
        break;
      case "error":
        dialogIcon = Icons.error_outline_rounded;
        dialogColor = errorColor;
        break;
      case "warning":
        dialogIcon = Icons.warning_amber_rounded;
        dialogColor = warningColor;
        break;
      case "info":
      default:
        dialogIcon = title == ""
            ? Icons.info_outline_rounded
            : Icons.notifications_outlined;
        dialogColor = title == "" ? infoColor : secondaryColor;
        break;
    }

    return await Get.dialog(
      Dialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        elevation: 8,
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Icon - dynamically colored based on type
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: dialogColor.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  dialogIcon,
                  color: dialogColor,
                  size: 48,
                ),
              ),
              const SizedBox(height: 20),
              // Title
              if (title != "")
                Column(
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 26,
                        fontWeight: FontWeight.bold,
                        color: textColor,
                      ),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 12),
                  ],
                ),
              // Message
              Text(
                message,
                style: TextStyle(
                  fontSize: 20,
                  color: textColor.withOpacity(0.7),
                  height: 1.5,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 28),
              // Actions
              if (link != "")
                Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: () async {
                          final Uri url = Uri.parse(link);
                          if (!await launchUrl(url)) {
                            throw Exception('Could not launch $url');
                          }
                        },
                        icon: const Icon(Icons.email_outlined, size: 20),
                        label: txt16SizeWithOutContext(
                          title: requestVerificationEmailLabel.value,
                          textColor: Colors.white,
                          fontFamily: regular,
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: secondaryColor,
                          foregroundColor: Colors.white,
                          elevation: 0,
                          minimumSize: const Size.fromHeight(buttonHeight),
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(5),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 10),
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton(
                        onPressed: () {
                          if (off == 0) {
                            Get.back();
                          } else if (off == 1) {
                            Get.offAllNamed(path);
                          }
                        },
                        style: OutlinedButton.styleFrom(
                          foregroundColor: textColor,
                          side: BorderSide(color: Colors.grey[300]!),
                          minimumSize: const Size.fromHeight(buttonHeight),
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(5),
                          ),
                        ),
                        child: txt20SizeWithOutContext(
                          title: closeBtnLabel.value,
                          textColor: textColor,
                          fontFamily: buttonFontFamily,
                        ),
                      ),
                    ),
                  ],
                )
              else
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () {
                      if (off == 0) {
                        Get.back();
                      } else if (off == 1) {
                        Get.offAllNamed(path);
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: btnPrimaryColor,
                      foregroundColor: Colors.white,
                      elevation: 0,
                      minimumSize: const Size.fromHeight(buttonHeight),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(5),
                      ),
                    ),
                    child: txt20SizeWithOutContext(
                      title: closeBtnLabel.value,
                      textColor: Colors.white,
                      fontFamily: buttonFontFamily,
                    ),
                  ),
                ),
            ],
          ),
        ),
      ),
      barrierDismissible: false,
    );
  }

  showWelcomeDialogue(String dialogToken) async {
    final serviceController = Get.find<Service>();
    return await Get.defaultDialog(
      title: '',
      titlePadding: EdgeInsets.zero,
      middleText: formatMessage(
          "${welcomeMessage1.isEmpty ? "Hey" : welcomeMessage1.value} ${loginUserDetail['first_name']},  ${welcomeMessage2.isEmpty ? "nice to meet you\nPlease complete your profile, it only takes a couple of minutes" : welcomeMessage2.value}"),
      barrierDismissible: false,
      titleStyle: const TextStyle(fontSize: 24, color: primaryColor),
      middleTextStyle: const TextStyle(fontSize: 20),
      actions: [
        ElevatedButton(
          onPressed: () async {
            await secureStorage.write(key: "token", value: dialogToken);
            serviceController.token = dialogToken;
            Get.offAllNamed('/stage_one');
          },
          style: ElevatedButton.styleFrom(
              backgroundColor: primaryColor,
              minimumSize: const Size.fromHeight(buttonHeight),
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(5))),
          child: txt20SizeWithOutContext(
              title: welcomeButton1.isEmpty ? "Proceed" : welcomeButton1.value,
              textColor: Colors.white,
              fontFamily: buttonFontFamily),
        ),
        // ElevatedButton(
        //   onPressed: (){
        //     try {
        //       isOverlayLoading.value = true;
        //       secureStorage.deleteAll();
        //       NavigationProvider().removeFcmToken(
        //           token
        //       ).then((resp) async {
        //         isOverlayLoading(false);
        //         Get.offAllNamed('/login');
        //       }, onError: (err) {
        //         isOverlayLoading(false);
        //         showDialogue(err.toString());

        //       });
        //     } catch (exception) {
        //       showDialogue(exception.toString());
        //     }
        //   },
        //   style: ElevatedButton.styleFrom(
        //       backgroundColor: btnPrimaryColor,
        //       shape: RoundedRectangleBorder(
        //           borderRadius: BorderRadius.circular(5)
        //       )
        //   ),
        //   child: txt14SizeWithOutContext(title: welcomeButton2.isEmpty ? "I will do that later" : welcomeButton2.value, textColor: Colors.white, fontFamily: regular),
        // )
      ],
      contentPadding:
          const EdgeInsets.only(bottom: 30.0, left: 10.0, right: 10.0),
    );
  }

  updateProductLayout(value) {
    productLayout.value = value;
    Get.back();
    productLayout.refresh();
  }

  datePicker(context, {allowPast = true}) async {
    final now = DateTime.now();
    return showDatePicker(
        context: context,
        initialDate: now,
        initialEntryMode: DatePickerEntryMode.calendarOnly,
        firstDate: allowPast ? DateTime(1900) : now,
        lastDate: DateTime(2100),
        builder: (context, child) {
          return Theme(
            data: ThemeData.dark().copyWith(
              colorScheme: const ColorScheme.dark(
                primary: primaryColor,
                onPrimary: Colors.white,
                surface: Colors.white,
                onSurface: Colors.black,
              ),
              dialogTheme: DialogThemeData(backgroundColor: primaryColor),
            ),
            child: child!,
          );
        });
  }

  timePicker(context) async {
    final now = TimeOfDay.now();
    return showTimePicker(
        context: context,
        initialTime: now,
        builder: (context, child) {
          return Theme(
            data: ThemeData.dark().copyWith(
              colorScheme: const ColorScheme.dark(
                primary: primaryColor,
                onPrimary: Colors.white,
                surface: Colors.white,
                onSurface: Colors.black,
              ),
              dialogTheme: DialogThemeData(backgroundColor: primaryColor),
            ),
            child: child!,
          );
        });
  }

  imageCropper(imageSource) async {
    try {
      final pickedFile =
          await ImagePicker().pickImage(source: imageSource, imageQuality: 20);
      if (pickedFile != null) {
        originalImagePath.value = pickedFile.path;
        originalImageName.value = pickedFile.name;

        final croppedFile = await ImageCropper().cropImage(
          sourcePath: pickedFile.path,

          //aspectRatio: const CropAspectRatio(ratioX: 1.0, ratioY: 1.0),
          compressQuality: 50,
          uiSettings: [
            AndroidUiSettings(
              toolbarTitle: 'Cropper',
              toolbarColor: primaryColor,
              toolbarWidgetColor: Colors.white,
              initAspectRatio: CropAspectRatioPreset.original,
              lockAspectRatio: false,
              hideBottomControls: false,
              cropStyle: CropStyle.rectangle,
              aspectRatioPresets: [
                CropAspectRatioPreset.original,
                CropAspectRatioPreset.ratio16x9,
                CropAspectRatioPreset.ratio4x3,
                CropAspectRatioPreset.square,
              ],
            ),
            IOSUiSettings(
              title: 'Cropper',
              aspectRatioLockEnabled: false,
              rotateButtonsHidden: false,
              resetButtonHidden: false,
              aspectRatioPresets: [
                CropAspectRatioPreset.original,
                CropAspectRatioPreset.ratio16x9,
                CropAspectRatioPreset.ratio4x3,
                CropAspectRatioPreset.square,
              ],
            ),
          ],
        );

        if (croppedFile != null) {
          // You can calculate size if needed in future:
          // final file = File(croppedFile.path);
          // final fileSizeInBytes = await file.length();
          // final fileSizeInMB = fileSizeInBytes / (1024 * 1024);

          return croppedFile;
        } else {
          return;
        }
      } else {
        return;
      }
    } catch (exception) {
      showDialogue(exception.toString());

      return;
    }
  }

  void syncSelectedLanguage(dynamic selectedLanguage) {
    if (selectedLanguage == null) return;

    langIcon.value = selectedLanguage['flag_icon']?.toString() ?? "";
    lang.value = selectedLanguage['abbreviation']?.toString() ?? lang.value;
    loginUserDetail['langId'] = langId.value.toString();
  }

  Future<void> persistUserLanguage() async {
    await secureStorage.write(key: "userInfo", value: jsonEncode(loginUserDetail));
  }

  updateLanguage(lang, page) async {
    if (isLoading.value) return;
    isLoading.value = true;
    try {
      langId.value = lang;
      final selectedLanguage = languages.firstWhereOrNull(
        (element) => element['id'] == langId.value,
      );
      syncSelectedLanguage(selectedLanguage);

      if (token != "") {
        StageProvider().updateLanguageId(token, langId.value).then(
            (resp) async {
          if (resp['status'] != null && resp['status'] == "Success") {
            if (page == "login") {
              await Get.offAllNamed('/login');
            } else if (page == "signup") {
              await Get.offAllNamed('/signup');
            } else {
              await persistUserLanguage();
              if (page == "step1") {
                await Get.offAllNamed('/stage_one');
              } else if (page == "step2") {
                await Get.offAllNamed('/stage_two');
              } else if (page == "step3") {
                await Get.offAllNamed('/stage_three');
              } else if (page == "step4") {
                await Get.offAllNamed('/stage_four');
              } else {
                await Get.offAllNamed('/navigation');
              }
            }
          }
        }, onError: (err) {
          showDialogue(err.toString());
        });
      } else {
        if (page == "login") {
          Get.offAllNamed('/login');
        } else if (page == "signup") {
          Get.offAllNamed('/signup');
        } else {
          await persistUserLanguage();
          if (page == "step1") {
            Get.offAllNamed('/stage_one');
          } else if (page == "step2") {
            Get.offAllNamed('/stage_two');
          } else if (page == "step3") {
            Get.offAllNamed('/stage_three');
          } else if (page == "step4") {
            Get.offAllNamed('/stage_four');
          } else {
            Get.offAllNamed('/navigation');
          }
        }
      }
    } catch (exception) {
      showDialogue(exception.toString());
    } finally {
      await Future.delayed(const Duration(seconds: 2));
      isLoading.value = false;
    }
  }
}
