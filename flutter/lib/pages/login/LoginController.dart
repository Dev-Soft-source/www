import 'dart:convert';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:flutter_facebook_auth/flutter_facebook_auth.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/services/service.dart';

class LoginController extends GetxController {
  late TextEditingController emailTextController, passwordTextController;

  final Map<String, FocusNode> focusNodes = {};
  final formKey = GlobalKey<FormState>();

  var showOverly = false.obs;
  var isLoading = false.obs;
  var isPasswordVisible = false.obs;

  final secureStorage = const FlutterSecureStorage();
  final serviceController = Get.find<Service>();
  var showHideText = true.obs;
  var errorList = List.empty(growable: true).obs;
  final errors = [].obs;
  var regionId = "".obs;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;
  var popupTextDetail = {}.obs;


  @override
  void onInit() async {
    // TODO: implement onInit
    super.onInit();
    isLoading(true);
    await getLanguages();
    await getLabelTextDetail();
    isLoading(false);
    emailTextController = TextEditingController();
    passwordTextController = TextEditingController();


    for (int i = 1; i <= 2; i++) {
      focusNodes[i.toString()] = FocusNode();
      // Attach the onFocusChange listener
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus, trigger validation
          if (i == 1) {
            validateField('email', emailTextController.text, type: 'email');
          } else if (i == 2) {
            validateField('password', passwordTextController.text,);
          }
        }
      });
    }
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }

  Future<void> getLanguages() async {
    try {
      await LoginProvider().getLanguages().then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          
          if(resp['data']!= null && resp['data']['languages']!= null){
            serviceController.languages.clear();
            serviceController.languages.addAll(resp['data']['languages']);

            if(serviceController.langId.value == 0){
              var getDefaultLanguage = serviceController.languages.firstWhereOrNull((element) => element['is_default'] == "1");
              if(getDefaultLanguage != null){
                serviceController.langId.value = int.parse(getDefaultLanguage['id'].toString());
              }
            }


            var getLanguage = serviceController.languages.firstWhereOrNull((element) => element['id'] == serviceController.langId.value);
            if(getLanguage != null){
              serviceController.langIcon.value = getLanguage['flag_icon'];
              serviceController.lang.value = getLanguage['abbreviation'];
            }

          }

        }
      }, onError: (error) {
        serviceController.showDialogue(error.toString());
        isLoading(false);
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
      isLoading(false);
    }
  }

  Future<void> getLabelTextDetail() async {
    try {
      await LoginProvider().getLabelTextDetail(serviceController.langId.value, loginPage).then((resp) async {
        if (resp['status']!= null && resp['status'] == "Success") {
          if(resp['data'] != null && resp['data']['loginPage'] != null){
            labelTextDetail.addAll(resp['data']['loginPage']);
            serviceController.requestVerificationEmailLabel.value = labelTextDetail['new_verification_email_btn_label'] ?? "Request new verification email";
          }
          if(resp['data'] != null && resp['data']['validationMessages'] != null){
            validationMessageDetail.addAll(resp['data']['validationMessages']);
          }
          if(resp['data'] != null && resp['data']['messages'] != null){
            serviceController.welcomeMessage1.value = resp['data']['messages']['hey_message'].toString();
            serviceController.welcomeMessage2.value = resp['data']['messages']['complete_profile_message'].toString();
            serviceController.welcomeButton1.value = resp['data']['messages']['proceed_button'].toString();
            serviceController.welcomeButton2.value = resp['data']['messages']['do_later_button'].toString();
          }
        }
      }, onError: (error) {
        serviceController.showDialogue(error.toString());
        isLoading(false);
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
      isLoading(false);
    }
  }

  void validateField(String fieldName, String fieldValue, {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      if(fieldName == "email"){
        message = message.replaceAll(":Attribute", labelTextDetail['email_error'] ?? "Email");
      }else{
        message = message.replaceAll(":Attribute", labelTextDetail['password_error'] ?? "Password");
      }

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
          var message = validationMessageDetail['email'];
          errorList.add(message ?? 'Please use a valid email address, like: example@email.com');
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
        if (fieldValue.isNotEmpty && !RegExp(r'^\d{2}:\d{2}$').hasMatch(fieldValue)) {
          errorList.add('$fieldName must be in the format HH:MM');
        }
        break;
      case 'max_words':
        if (fieldValue.isNotEmpty && fieldValue.split(' ').length > wordsLimit) {
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
    final RegExp emailRegExp = RegExp(r'^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$');
    return emailRegExp.hasMatch(email);
  }

  login() async {
    try {
      if (emailTextController.text.isEmpty ||
          passwordTextController.text.isEmpty) {
        if (emailTextController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['email_error'] ?? "Email");
          var err = {
            'title': "email",
            'eList': [message]
          };
          errors.add(err);
        }

        if (passwordTextController.text.isEmpty) {
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['password_error'] ?? "Password");
          var err = {
            'title': "password",
            'eList': [message]
          };
          errors.add(err);
        }

        return;
      }
      showOverly(true);
      LoginProvider()
          .loginUser(
              emailTextController.text.trim(), passwordTextController.text, serviceController.langId.value)
          .then((resp) async {
        errorList.clear();
        errors.clear();

        if (resp['status'] != null && resp['status'] == "Error") {
          if(resp['data'] == null){
            serviceController.showDialogue(resp['message']);
          }else{
            serviceController.showDialogue(resp['message'], link: resp['data']['url']);
          }

        } else if (resp['errors'] != null) {
          if (resp['errors']['email'] != null) {
            var err = {'title': "email", 'eList': resp['errors']['email']};
            errors.add(err);
          }
          if (resp['errors']['password'] != null) {
            var err = {
              'title': "password",
              'eList': resp['errors']['password']
            };
            errors.add(err);
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {

          serviceController.languages.clear();
          if(resp['data'] != null && resp['data']['languages'] != null){
            serviceController.languages.addAll(resp['data']['languages']);
          }


          serviceController.loginUserDetail.clear();
          secureStorage.deleteAll();

          secureStorage.write(key: "token", value: resp['data']['token']);
          serviceController.token = resp['data']['token'];
          serviceController.loginUserDetail['id'] =
              int.parse(resp['data']['id'].toString());
          serviceController.loginUserDetail['first_name'] =
              resp['data']['first_name'].toString();
          serviceController.loginUserDetail['last_name'] =
              resp['data']['last_name'].toString();
          serviceController.loginUserDetail['gender'] =
              resp['data']['gender'].toString();
          serviceController.loginUserDetail['profile_image'] =
              resp['data']['profile_image'].toString();
          serviceController.loginUserDetail['profile_original_image'] =
              resp['data']['profile_original_image'].toString();
          serviceController.loginUserDetail['email'] =
              resp['data']['email'].toString();
          serviceController.loginUserDetail['step'] =
              resp['data']['step'].toString();
          serviceController.loginUserDetail['student'] =
              resp['data']['student'].toString();
          serviceController.loginUserDetail['langId'] =
              resp['data']['langId'].toString();
          serviceController.loginUserDetail['driver_liscense'] = resp['data']['driver_liscense'].toString();
          serviceController.langId.value = int.parse(resp['data']['langId'].toString());

          serviceController.loginUserDetail['student_card_exp_date'] = resp['data']['student_card_exp_date'].toString();
          serviceController.loginUserDetail['driver_average_rating'] = resp['data']['driver_average_rating'] != null ? resp['data']['driver_average_rating'].toString() : "0.0";
          serviceController.loginUserDetail['passenger_average_rating'] = resp['data']['passenger_average_rating'] != null ? resp['data']['passenger_average_rating'].toString() : "0.0";
          serviceController.loginUserDetail['user_average_rating'] = resp['data']['user_average_rating'] != null ? resp['data']['user_average_rating'].toString() : "0.0";
          serviceController.loginUserDetail['driver_total_ratings'] = resp['data']['driver_total_ratings'] != null ? resp['data']['driver_total_ratings'].toString() : "0";
          serviceController.loginUserDetail['passenger_total_ratings'] = resp['data']['passenger_total_ratings'] != null ? resp['data']['passenger_total_ratings'].toString() : "0";
          serviceController.loginUserDetail['user_total_ratings'] = resp['data']['user_total_ratings'] != null ? resp['data']['user_total_ratings'].toString() : "0";
          secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));

          var getLanguage = serviceController.languages.firstWhereOrNull((element) => element['id'] == serviceController.langId.value);
          if(getLanguage != null){
            serviceController.langIcon.value = getLanguage['flag_icon'];
            serviceController.lang.value = getLanguage['abbreviation'];
          }


          if (serviceController.loginUserDetail['step'] == "1") {
            serviceController.showWelcomeDialogue();
          } else if (serviceController.loginUserDetail['step'] == "2") {
            Get.offAllNamed('/stage_two');
          } else if (serviceController.loginUserDetail['step'] == "3") {
            Get.offAllNamed('/stage_three_vehicle');
          } else if (serviceController.loginUserDetail['step'] == "4") {
            Get.offAllNamed('/stage_four');
          } else if (serviceController.loginUserDetail['step'] == "5") {
            serviceController.navigationIndex.value = 0;
            Get.offAllNamed('/navigation');
          }
          else{
            serviceController.navigationIndex.value = 0;
            Get.offAllNamed('/navigation');
          }

        } else if (resp['status'] != null && resp['status'] == "Duplicate") {
          Get.toNamed('/contact_us');
          // serviceController.showDialogue(resp['message']);

          // serviceController.showDialogue(resp['message'], off: 2, path: '/contact_us');
        }
        else if (resp['status'] != null && resp['status'] == "noShow") {
          serviceController.showDialogue(resp['message']);
        }
        showOverly(false);
      }, onError: (err) {
        showOverly(false);
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }

  Future signInWithGoogle() async {
    // Trigger the authentication flow
    final GoogleSignInAccount? googleUser = await GoogleSignIn(scopes: ["profile", "email"]).signIn();
    // Obtain the auth details from the request
    final GoogleSignInAuthentication? googleAuth =
        await googleUser?.authentication;



    // Create a new credential
    final credential = GoogleAuthProvider.credential(
      accessToken: googleAuth?.accessToken,
      idToken: googleAuth?.idToken,
    );

    // Once signed in, return the UserCredential
    var userCredential =
        await FirebaseAuth.instance.signInWithCredential(credential);
    var email = userCredential.user?.email;
    var name = userCredential.user?.displayName;
    var photoUrl = userCredential.user?.photoURL;
    var uId = userCredential.user?.uid;
    await socialLogin('google', email, name, photoUrl, uId);
  }

  Future signInWithFacebook() async {
    // Trigger the sign-in flow
    final LoginResult loginResult = await FacebookAuth.instance.login();

    // Create a credential from the access token
    final OAuthCredential facebookAuthCredential =
        FacebookAuthProvider.credential(loginResult.accessToken!.tokenString);

    // Once signed in, return the UserCredential
    var userCredential =
        await FirebaseAuth.instance.signInWithCredential(facebookAuthCredential);
    User? user = userCredential.user;
    var email = user?.email;
    var name = user?.displayName;
    var photoUrl = user?.photoURL;
    var uId = user?.uid;
    await socialLogin('facebook', email, name, photoUrl, uId);
  }

  getUserInfo(data) {
    try {
      showOverly(true);
      LoginProvider().linkedInUserInfo(data['access_token']).then((resp) async {
        var email = resp['email'];
        var name = resp['name'];
        var photoUrl = resp['picture'];
        var uId = resp['sub'];
        await socialLogin('linkedIn', email, name, photoUrl, uId);
      }, onError: (err) {
        showOverly(false);
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
  }

  getTikTokUserInfo(code) async {
    try {
      showOverly(true);
      LoginProvider().tikTokUserAccessToken(code).then((resp) async {
        showOverly(false);
        // LoginProvider().tikTokUserInfo(
        //     code
        // ).then((resp) async {
        //   print(resp);
        //   showOverly(false);
        // },onError: (err){;
        // showOverly(false);
        // });
      }, onError: (err) {
        showOverly(false);
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());

    }
  }

  socialLogin(type, email, name, photoUrl, typeId) async {
    try {
      showOverly(true);
      LoginProvider().socialLogin(type, email, name, photoUrl, typeId, serviceController.langId.value).then(
          (resp) async {
        errorList.clear();
        errors.clear();

        if (resp['status'] != null && resp['status'] == "Error") {
          serviceController.showDialogue(resp['message'].toString());

        } else if (resp['errors'] != null) {
          if (resp['errors']['type'] != null) {
            errorList.addAll(resp['errors']['type']);
            errors.add('type');
          }
          if (resp['errors']['type_id'] != null) {
            errorList.addAll(resp['errors']['type_id']);
            errors.add('type_id');
          }
          if (resp['errors']['user_name'] != null) {
            errorList.addAll(resp['errors']['user_name']);
            errors.add('user_name');
          }
          if (resp['errors']['email'] != null) {
            errorList.addAll(resp['errors']['email']);
            errors.add('email');
          }
          if (resp['errors']['photourl'] != null) {
            errorList.addAll(resp['errors']['photourl']);
            errors.add('photourl');
          }
        } else if (resp['status'] != null && resp['status'] == "Success") {


          serviceController.languages.clear();
          if(resp['data'] != null && resp['data']['languages'] != null){
            serviceController.languages.addAll(resp['data']['languages']);
          }

          secureStorage.write(key: "token", value: resp['data']['token']);
          serviceController.token = resp['data']['token'];
          serviceController.loginUserDetail['id'] =
              int.parse(resp['data']['id'].toString());
          serviceController.loginUserDetail['first_name'] =
              resp['data']['first_name'].toString();
          serviceController.loginUserDetail['last_name'] =
              resp['data']['last_name'].toString();
          serviceController.loginUserDetail['gender'] =
              resp['data']['gender'].toString();
          serviceController.loginUserDetail['profile_image'] =
              resp['data']['profile_image'].toString();
          serviceController.loginUserDetail['profile_original_image'] =
              resp['data']['profile_original_image'].toString();
          serviceController.loginUserDetail['email'] =
              resp['data']['email'].toString();
          serviceController.loginUserDetail['step'] =
              resp['data']['step'] == null ? "1" : resp['data']['step'].toString();
          serviceController.loginUserDetail['langId'] =
              resp['data']['langId'].toString();
          serviceController.langId.value = int.parse(resp['data']['langId'].toString());
          serviceController.loginUserDetail['driver_liscense'] = resp['data']['driver_liscense'].toString();
          serviceController.loginUserDetail['driver_average_rating'] = resp['data']['driver_average_rating'] != null ? resp['data']['driver_average_rating'].toString() : "0.0";
          serviceController.loginUserDetail['passenger_average_rating'] = resp['data']['passenger_average_rating'] != null ? resp['data']['passenger_average_rating'].toString() : "0.0";
          serviceController.loginUserDetail['user_average_rating'] = resp['data']['user_average_rating'] != null ? resp['data']['user_average_rating'].toString() : "0.0";
          serviceController.loginUserDetail['driver_total_ratings'] = resp['data']['driver_total_ratings'] != null ? resp['data']['driver_total_ratings'].toString() : "0";
          serviceController.loginUserDetail['passenger_total_ratings'] = resp['data']['passenger_total_ratings'] != null ? resp['data']['passenger_total_ratings'].toString() : "0";
          serviceController.loginUserDetail['user_total_ratings'] = resp['data']['user_total_ratings'] != null ? resp['data']['user_total_ratings'].toString() : "0";

          secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));

          var getLanguage = serviceController.languages.firstWhereOrNull((element) => element['id'] == serviceController.langId.value);
          if(getLanguage != null){
            serviceController.langIcon.value = getLanguage['flag_icon'];
            serviceController.lang.value = getLanguage['abbreviation'];
          }

          if (serviceController.loginUserDetail['step'] == "0") {
            Get.toNamed('/login');
          } else if (serviceController.loginUserDetail['step'] == "1") {
            Get.offAllNamed('/stage_one');
          } else if (serviceController.loginUserDetail['step'] == "2") {
            Get.offAllNamed('/stage_two');
          } else if (serviceController.loginUserDetail['step'] == "3") {
            Get.offAllNamed('/stage_three_vehicle');
          } else if (serviceController.loginUserDetail['step'] == "4") {
            Get.offAllNamed('/stage_four');
          } else if (serviceController.loginUserDetail['step'] == "5") {
            serviceController.navigationIndex.value = 0;
            Get.offAllNamed('/navigation');
          }
          else{
            serviceController.navigationIndex.value = 0;
            Get.offAllNamed('/navigation');
          }
          // Get.offAllNamed('/navigation');
        }
        showOverly(false);
      }, onError: (err) {
        showOverly(false);
        serviceController.showDialogue(err.toString());
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString());
    }
  }
}
