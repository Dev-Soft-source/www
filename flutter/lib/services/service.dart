import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:image_cropper/image_cropper.dart';
import 'package:image_picker/image_picker.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/stages/StageProvider.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:url_launcher/url_launcher.dart';
import '../pages/navigation/navigationProvider.dart';

class Service extends GetxService{


var height = 0.0;
var width = 0.0;

var backgroundNotification = "";
var pendingChatRideId = "";
var pendingChatUserId = "";
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


final loginUserDetail = {
  "id": 0,
  "first_name" : "",
  "last_name": "",
  "gender": "",
  "profile_image" : "",
  "profile_original_image" : "",
  "countryId" : "",
  "email" : "",
  "step" : "0",
  "student" : "0",
  "student_card_exp_date" : "",
  "driver_average_rating" : "",
  "passenger_average_rating" : "",
  "user_average_rating" : "",
  "driver_total_ratings" : "",
  "passenger_total_ratings" : "",
  "user_total_ratings" : "",
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

var logoutLabelTextDetail ={}.obs;

var navigationChatLabel = "Chats".obs;
var navigationMyTripLabel = "My trips".obs;
var navigationMyProfileLabel = "My profile".obs;


var termAndConditionLabel = "Terms and conditions".obs;
var privacyPolicyLabel = "Privacy policy".obs;
var termOfUseLabel = "Terms of use".obs;
var refundPolicyLabel = "Refund policy".obs;
var cancellationPolicyLabel = "Cancellation policy".obs;
var disputePolicyLabel = "Disputes & Policies".obs;
var coffeeOnWallLabel = "Coffee on the Wall".obs;
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
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    await getUserToken();
    await getUserInfo();

  }

  @override
  void onClose() {
   super.onClose();
  }

  getUserInfo() async{

   if(token != ""){
     var data  = jsonDecode(await secureStorage.read(key: "userInfo") ?? "");
     loginUserDetail['id'] = data['id'];
     loginUserDetail['first_name'] = data['first_name'].toString();
     loginUserDetail['last_name'] = data['last_name'].toString();
     loginUserDetail['gender'] = data['gender'].toString();
     loginUserDetail['profile_original_image'] = data['profile_original_image'].toString();
     loginUserDetail['profile_image'] = data['profile_image'].toString();
     loginUserDetail['email'] = data['email'].toString();
     loginUserDetail['step'] = data['step'].toString();
     loginUserDetail['langId'] = data['langId'].toString();
     loginUserDetail['driver_liscense'] = data['driver_liscense'].toString();
     langId.value = int.parse(data['langId'].toString());

     var getLanguage = languages.firstWhereOrNull((element) => element['id'] == langId.value);
     if(getLanguage != null){
       langIcon.value = getLanguage['flag_icon'];
       lang.value = getLanguage['abbreviation'];
     }

   }
  }

  getUserToken() async{
    token = await secureStorage.read(key: "token") ?? "";
  }

  logoutUser() async{

    bool isConfirmed = await showConfirmationDialog("${logoutLabelTextDetail['confirmation_message_heading'] ?? "Are you sure you want to log out?"}");

    if(isConfirmed) {
      try {
        isOverlayLoading.value = true;
        secureStorage.deleteAll();
        NavigationProvider().removeFcmToken(
            token
        ).then((resp) async {
          isOverlayLoading(false);
          Get.offAllNamed('/login');
        }, onError: (err) {
          isOverlayLoading(false);
          showDialogue(err.toString());

        });
      } catch (exception) {
        showDialogue(exception.toString());
      }
    }
  }

  showSnackBar(String title, String message, Color backgroundColor){
    Get.snackbar(
        '',
        message,
        snackPosition: SnackPosition.BOTTOM,
        backgroundColor: backgroundColor,
        colorText: Colors.white,
        duration: const Duration(days: 1000),
        mainButton: TextButton(onPressed: (){Get.back();}, child: const Icon(
        Icons.close,
        color: Colors.white,
      ),)
    );
  }

  showToast(message, position){
    Fluttertoast.showToast(
        msg: message,
        toastLength: Toast.LENGTH_SHORT,
        gravity: position,
        timeInSecForIosWeb: 1,
        backgroundColor: Colors.black.withOpacity(0.7),
        textColor: Colors.white,
        fontSize: 12.0
    );
  }

Future<bool> showConfirmationDialog(message, {String cancelYesBtn = "", String cancelNoBtn = ""}) async {
  return await Get.defaultDialog(
    title: "${logoutLabelTextDetail['main_heading'] ?? 'Confirm'}",
    barrierDismissible: false,
    middleText: message,
    middleTextStyle: const TextStyle(fontSize: 18),
    actions: [
      ElevatedButton(
        onPressed: (){
          Get.back(result: false);
        },
        style: ElevatedButton.styleFrom(
            backgroundColor: primaryColor,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(5)
            )
        ),
        child: cancelNoBtn != "" ? txt14SizeWithOutContext(title: cancelNoBtn, textColor: Colors.white, fontFamily: regular) :  txt14SizeWithOutContext(title: "${logoutLabelTextDetail['confirmation_no_label'] ?? "No"}", textColor: Colors.white, fontFamily: regular),
      ),
      ElevatedButton(
        onPressed: (){
          Get.back(result: true);
        },
        style: ElevatedButton.styleFrom(
            backgroundColor: btnPrimaryColor,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(5)
            )
        ),
        child: cancelYesBtn != "" ? txt14SizeWithOutContext(title: cancelYesBtn, textColor: Colors.white, fontFamily: regular) : txt14SizeWithOutContext(title: "${logoutLabelTextDetail['confirmation_yes_label'] ?? "Yes"}", textColor: Colors.white, fontFamily: regular),
      ),
    ],
  );
}

showDialogue(message, {off = 0, path = "", link = "", title = ""}) async {
  return await Get.defaultDialog(
    title: title,
    titlePadding: title == "" ? EdgeInsets.zero : EdgeInsets.all(5.0),
    middleText: message,
    barrierDismissible: false,
    middleTextStyle: const TextStyle(fontSize: 20),
    actions: [
      if(link != "")...[
        ElevatedButton(
          onPressed: () async{
            final Uri _url = Uri.parse(link);
            if (!await launchUrl(_url)) {
            throw Exception('Could not launch $_url');
            }

          },
          style: ElevatedButton.styleFrom(
              backgroundColor: secondaryColor,
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(5)
              )
          ),
          child: txt16SizeWithOutContext(title: requestVerificationEmailLabel.value, textColor: Colors.white, fontFamily: regular),
        ),
      ],
      ElevatedButton(
        onPressed: (){
          if(off == 0) {
            Get.back();
          }
          else if (off == 1){
            Get.offAllNamed(path);
          }
          // else if (off == 2){
          //   Get.toNamed(path);
          // }
        },
        style: ElevatedButton.styleFrom(
            backgroundColor: btnPrimaryColor,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(5)
            )
        ),
        child: txt16SizeWithOutContext(title: closeBtnLabel.value, textColor: Colors.white, fontFamily: regular),
      ),

    ],
  );
}

showWelcomeDialogue() async {
  return await Get.defaultDialog(
    title: '',
    titlePadding: EdgeInsets.zero,
    middleText: "${welcomeMessage1.isEmpty ? "Hey" : welcomeMessage1.value} ${loginUserDetail['first_name']},  ${welcomeMessage2.isEmpty ? "nice to meet you\nPlease complete your profile, it only takes a couple of minutes" : welcomeMessage2.value}",
    barrierDismissible: false,
    titleStyle: const TextStyle(fontSize: 24,color: primaryColor),
    middleTextStyle: const TextStyle(fontSize: 16),
    actions: [
      ElevatedButton(
        onPressed: (){
          Get.offAllNamed('/stage_one');
        },
        style: ElevatedButton.styleFrom(
            backgroundColor: primaryColor,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(5)
            )
        ),
        child: txt14SizeWithOutContext(title: welcomeButton1.isEmpty ? "Proceed" : welcomeButton1.value, textColor: Colors.white, fontFamily: regular),
      ),
      ElevatedButton(
        onPressed: (){
          try {
            isOverlayLoading.value = true;
            secureStorage.deleteAll();
            NavigationProvider().removeFcmToken(
                token
            ).then((resp) async {
              isOverlayLoading(false);
              Get.offAllNamed('/login');
            }, onError: (err) {
              isOverlayLoading(false);
              showDialogue(err.toString());

            });
          } catch (exception) {
            showDialogue(exception.toString());
          }
        },
        style: ElevatedButton.styleFrom(
            backgroundColor: btnPrimaryColor,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(5)
            )
        ),
        child: txt14SizeWithOutContext(title: welcomeButton2.isEmpty ? "I will do that later" : welcomeButton2.value, textColor: Colors.white, fontFamily: regular),
      )
    ],
    contentPadding: const EdgeInsets.only(bottom: 30.0,left: 10.0,right: 10.0),

  );
}

updateProductLayout(value){
    productLayout.value = value;
    Get.back();
    productLayout.refresh(); 
  }

datePicker(context,{allowPast = true}) async {
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
            dialogBackgroundColor: primaryColor,
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
            dialogBackgroundColor: primaryColor,
          ),
          child: child!,
        );
      });
}

imageCropper(imageSource) async {

    try {
      final pickedFile = await ImagePicker().pickImage(source: imageSource);
      if (pickedFile != null) {

        originalImagePath.value = pickedFile.path;
        originalImageName.value = pickedFile.name;

        final croppedFile = await ImageCropper().cropImage(
          sourcePath: pickedFile.path,


          //aspectRatio: const CropAspectRatio(ratioX: 1.0, ratioY: 1.0),
          compressQuality: 100,
          uiSettings: [
            AndroidUiSettings(
              toolbarTitle: 'Cropper',
              toolbarColor: primaryColor,
              toolbarWidgetColor: Colors.white,
              initAspectRatio: CropAspectRatioPreset.square,
              lockAspectRatio: false,
              aspectRatioPresets: [
                CropAspectRatioPreset.square,
                CropAspectRatioPreset.ratio4x3,
                //CropAspectRatioPresetCustom(),
              ],
            ),
            IOSUiSettings(
              title: 'Cropper',
              aspectRatioPresets: [
                 CropAspectRatioPreset.square,
                CropAspectRatioPreset.ratio4x3,
                // CropAspectRatioPresetCustom(),
              ],
            ),
          ],
        );

        if (croppedFile != null) {

          final file = File(croppedFile.path);
          final fileSizeInBytes = await file.length();  // Size in bytes
          final fileSizeInMB = fileSizeInBytes / (1024 * 1024);  // Size in MB


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

  updateLanguage(lang, page) {
    try {
      langId.value = lang;
      if(token != ""){
        StageProvider()
            .updateLanguageId(
            token,
            langId.value
        ).then((resp) async {
          if (resp['status'] != null && resp['status'] == "Success") {
            if(page == "login"){
              Get.offAllNamed('/login');
            }else if(page == "signup"){
              Get.offAllNamed('/signup');
            }else{
              loginUserDetail['langId'] = langId.value.toString();
              secureStorage.write(
                  key: "userInfo",
                  value: jsonEncode(loginUserDetail));
              if(page == "step1"){
                Get.offAllNamed('/stage_one');
              }else if(page == "step2"){
                Get.offAllNamed('/stage_two');
              }else if(page == "step3"){
                Get.offAllNamed('/stage_three_vehicle');
              }else if(page == "step4"){
                Get.offAllNamed('/stage_four');
              }else{
                Get.offAllNamed('/navigation');
              }
            }

          }
        }, onError: (err) {
          showDialogue(err.toString());
        });
      }else{
        if(page == "login"){
          Get.offAllNamed('/login');
        }else if(page == "signup"){
          Get.offAllNamed('/signup');
        }else{
          loginUserDetail['langId'] = langId.value.toString();
          secureStorage.write(
              key: "userInfo",
              value: jsonEncode(loginUserDetail));
          if(page == "step1"){
            Get.offAllNamed('/stage_one');
          }else if(page == "step2"){
            Get.offAllNamed('/stage_two');
          }else if(page == "step3"){
            Get.offAllNamed('/stage_three_vehicle');
          }else if(page == "step4"){
            Get.offAllNamed('/stage_four');
          }else{
            Get.offAllNamed('/navigation');
          }
        }
      }

    } catch (exception) {
      showDialogue(exception.toString());
    }
  }

}