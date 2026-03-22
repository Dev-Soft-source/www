import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:flutter_facebook_auth/flutter_facebook_auth.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/error_state_manager.dart';
import 'package:proximaride_app/helpers/percentage_to_pipe.dart';
import 'package:proximaride_app/pages/login/LoginProvider.dart';
import 'package:proximaride_app/services/connectivity_service.dart';
import 'package:proximaride_app/services/logger_service.dart';
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
  late final ConnectivityService connectivityService;
  final errorStateManager = ErrorStateManager();
  var showHideText = true.obs;
  var errorList = List.empty(growable: true).obs;
  final errors = [].obs;
  var regionId = "".obs;
  var labelTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;
  var popupTextDetail = {}.obs;

  @override
  void onInit() async {
    super.onInit();
    // Initialize connectivity service
    try {
      connectivityService = Get.find<ConnectivityService>();
    } catch (e) {
      // If not found, put it
      connectivityService = Get.put(ConnectivityService());
    }

    // Initialize text controllers
    emailTextController = TextEditingController();
    passwordTextController = TextEditingController();

    // Setup focus node listeners
    for (int i = 1; i <= 2; i++) {
      focusNodes[i.toString()] = FocusNode();
      focusNodes[i.toString()]?.addListener(() {
        if (!focusNodes[i.toString()]!.hasFocus) {
          // Field has lost focus.
          // We intentionally do NOT validate email here anymore so that
          // email errors only appear when the user taps the "Log in" button.
        }
      });
    }

    // Load initial data
    await loadInitialData();
  }

  Future<void> loadInitialData() async {
    try {
      errorStateManager.setLoading();

      // NO connectivity check - let the API call proceed
      // Only catch exceptions if they actually occur

      // Execute all init API calls
      await getLanguages();
      await getLabelTextDetail();

      errorStateManager.setSuccess();
    } on SocketException {
      // Handle socket exceptions (network errors) with user-friendly message
      logger.error("Network error in loadInitialData: SocketException");
      errorStateManager.setError(
        "No internet connection. Please check your network and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } on TimeoutException {
      // Handle timeout errors
      logger.error("Timeout error in loadInitialData");
      errorStateManager.setError(
        "Request timed out. Please check your connection and try again.",
        ErrorType.network,
        loadInitialData,
      );
    } catch (error) {
      logger.error("Error in loadInitialData: $error");

      // Check if error is a structured error from provider
      if (error is Map &&
          error.containsKey('type') &&
          error.containsKey('message')) {
        errorStateManager.setError(
          error["message"],
          _parseErrorType(error["type"]),
          loadInitialData,
        );
      } else {
        // Fallback for unknown errors with user-friendly message
        errorStateManager.setError(
          "Unable to load app data. Please check your connection and try again.",
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
  }

  Future<void> getLanguages() async {
    await LoginProvider().getLanguages().then((resp) async {
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
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }

  Future<void> getLabelTextDetail() async {
    await LoginProvider()
        .getLabelTextDetail(serviceController.langId.value, loginPage)
        .then((resp) async {
      if (resp['status'] != null && resp['status'] == "Success") {
        if (resp['data'] != null && resp['data']['loginPage'] != null) {
          labelTextDetail.addAll(resp['data']['loginPage']);
          serviceController.requestVerificationEmailLabel.value =
              labelTextDetail['new_verification_email_btn_label'] ??
                  "Request new verification email";
        }
        if (resp['data'] != null &&
            resp['data']['validationMessages'] != null) {
          validationMessageDetail.addAll(resp['data']['validationMessages']);
        }
        if (resp['data'] != null && resp['data']['messages'] != null) {
          serviceController.welcomeMessage1.value =
              resp['data']['messages']['hey_message'].toString();
          serviceController.welcomeMessage2.value =
              resp['data']['messages']['complete_profile_message'].toString();
          serviceController.welcomeButton1.value =
              resp['data']['messages']['proceed_button'].toString();
          serviceController.welcomeButton2.value =
              resp['data']['messages']['do_later_button'].toString();
        }
      }
    }, onError: (error) {
      throw error; // Propagate to loadInitialData
    });
  }

  void validateField(String fieldName, String fieldValue,
      {String type = 'string', bool isRequired = true, int wordsLimit = 50}) {
    errors.removeWhere((element) => element['title'] == fieldName);
    List<String> errorList = [];

    if (isRequired && fieldValue.isEmpty) {
      var message = validationMessageDetail['required'];
      if (fieldName == "email") {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['email_error'] ?? "Email");
      } else {
        message = message.replaceAll(
            ":Attribute", labelTextDetail['password_error'] ?? "Password");
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
        RegExp(r'^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$');
    return emailRegExp.hasMatch(email);
  }
  // Add these methods to your controller

  void validateEmailOnChange() {
    String email = emailTextController.text.trim();

    // Only validate format if email is not empty
    if (email.isNotEmpty && !isValidEmail(email)) {
      _addError("email", ['Please enter a valid email address'], 3);
    }
  }

  void validatePasswordOnChange() {
    String password = passwordTextController.text;
    List<String> passwordErrors = [];

    // Only validate if password is not empty
    if (password.isNotEmpty) {
      if (password.length < 8) {
        passwordErrors.add('Password must be at least 8 characters long');
      }
      if (!RegExp(r'[a-z]').hasMatch(password)) {
        passwordErrors
            .add('Password must contain at least one lowercase letter');
      }
      if (!RegExp(r'[A-Z]').hasMatch(password)) {
        passwordErrors
            .add('Password must contain at least one uppercase letter');
      }
      if (!RegExp(r'[0-9]').hasMatch(password)) {
        passwordErrors.add('Password must contain at least one number');
      }
      if (!RegExp(r'[!@#$%^&*(),.?":{}|<>]').hasMatch(password)) {
        passwordErrors
            .add('Password must contain at least one special character');
      }
    }

    if (passwordErrors.isNotEmpty) {
      _addError("password", passwordErrors, 4);
    }
  }

  login() async {
    try {
      // if (emailTextController.text.isEmpty ||
      //     passwordTextController.text.isEmpty) {
      //   if (emailTextController.text.isEmpty) {
      //     var message = validationMessageDetail['required'];
      //     message = message.replaceAll(
      //         ":Attribute", labelTextDetail['email_error'] ?? "Email");
      //     var err = {
      //       'title': "email",
      //       'eList': [message]
      //     };
      //     errors.add(err);
      //   }

      //   if (passwordTextController.text.isEmpty) {
      //     var message = validationMessageDetail['required'];
      //     message = message.replaceAll(
      //         ":Attribute", labelTextDetail['password_error'] ?? "Password");
      //     var err = {
      //       'title': "password",
      //       'eList': [message]
      //     };
      //     errors.add(err);
      //   }

      //   return;
      // }

      // errors.clear(); // Clear previous errors

      // Validate email and password
      
      validateEmail();
      validatePassword(); // Commented out - no password validation on login screen

      // Check only if password is empty (basic check for login)
      if (passwordTextController.text.trim().isEmpty) {
        var message =
            validationMessageDetail['required'] ?? ":Attribute is required";
        message = message.replaceAll(
            ":Attribute", labelTextDetail['password_error'] ?? "Password");
        errors.add({
          'title': 'password',
          'eList': [message]
        });
      }

      // If there are validation errors, don't proceed
      if (errors.isNotEmpty) {
        return;
      }

      // Check connectivity before making API call
      if (!connectivityService.isConnected) {
        serviceController.showDialogue(
          "No internet connection. Please check your network and try again.",
          type: "error",
        );
        return;
      }

      showOverly(true);
      LoginProvider()
          .loginUser(emailTextController.text.trim(),
              passwordTextController.text, serviceController.langId.value)
          .then((resp) async {
        errorList.clear();
        errors.clear();

        logger.info("Login Response: ${resp.toString()}");

        if (resp['status'] != null && resp['status'] == "Error") {
          if (resp['data'] == null) {
            serviceController.showDialogue(resp['message'], type: "error");
          } else {
            if (resp['data']['verify_email'] == true) {
              serviceController.showDialogue(resp['message'],
                  title: "Email Not Verified", link: resp['data']['url']);
            } else {
              serviceController.showDialogue(resp['message'],
                  link: resp['data']['url']);
            }
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
          if (resp['data'] != null && resp['data']['languages'] != null) {
            serviceController.languages.addAll(resp['data']['languages']);
          }

          serviceController.loginUserDetail.clear();
          await secureStorage.deleteAll();

          final rawToken = resp['data']['token']?.toString() ?? "";
          if (rawToken.isNotEmpty) {
            final normalizedToken = convertPercentToPipe(rawToken);
            await secureStorage.write(key: "token", value: normalizedToken);
            serviceController.token = normalizedToken;
          }

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
          serviceController.loginUserDetail['driver'] =
              resp['data']['driver'].toString();
          serviceController.loginUserDetail['langId'] =
              resp['data']['langId'].toString();
          serviceController.loginUserDetail['driver_liscense'] =
              resp['data']['driver_liscense'].toString();
          serviceController.langId.value =
              int.parse(resp['data']['langId'].toString());

          serviceController.loginUserDetail['student_card_exp_date'] =
              resp['data']['student_card_exp_date'].toString();
          serviceController.loginUserDetail['driver_average_rating'] =
              resp['data']['driver_average_rating'] != null
                  ? resp['data']['driver_average_rating'].toString()
                  : "0.0";
          serviceController.loginUserDetail['passenger_average_rating'] =
              resp['data']['passenger_average_rating'] != null
                  ? resp['data']['passenger_average_rating'].toString()
                  : "0.0";
          serviceController.loginUserDetail['user_average_rating'] =
              resp['data']['user_average_rating'] != null
                  ? resp['data']['user_average_rating'].toString()
                  : "0.0";
          serviceController.loginUserDetail['driver_total_ratings'] =
              resp['data']['driver_total_ratings'] != null
                  ? resp['data']['driver_total_ratings'].toString()
                  : "0";
          serviceController.loginUserDetail['passenger_total_ratings'] =
              resp['data']['passenger_total_ratings'] != null
                  ? resp['data']['passenger_total_ratings'].toString()
                  : "0";
          serviceController.loginUserDetail['user_total_ratings'] =
              resp['data']['user_total_ratings'] != null
                  ? resp['data']['user_total_ratings'].toString()
                  : "0";
          secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));

          var getLanguage = serviceController.languages.firstWhereOrNull(
              (element) => element['id'] == serviceController.langId.value);
          if (getLanguage != null) {
            serviceController.langIcon.value = getLanguage['flag_icon'];
            serviceController.lang.value = getLanguage['abbreviation'];
          }

          if (serviceController.loginUserDetail['step'] == "1") {
            serviceController.showWelcomeDialogue(resp['data']['token']);
          } else if (serviceController.loginUserDetail['step'] == "2") {
            Get.offAllNamed('/stage_two');
          } else if (serviceController.loginUserDetail['step'] == "3") {
            Get.offAllNamed('/stage_three_vehicle');
          } else if (serviceController.loginUserDetail['step'] == "4") {
            Get.offAllNamed('/stage_four_vehicle');
          } else if (serviceController.loginUserDetail['step'] == "5") {
            serviceController.navigationIndex.value = 0;
            Get.offAllNamed('/navigation');
          } else {
            serviceController.navigationIndex.value = 0;
            Get.offAllNamed('/navigation');
          }
        } else if (resp['status'] != null && resp['status'] == "Duplicate") {
          Get.toNamed('/contact_us');
          // serviceController.showDialogue(resp['message']);

          // serviceController.showDialogue(resp['message'], off: 2, path: '/contact_us');
        } else if (resp['status'] != null && resp['status'] == "noShow") {
          serviceController.showDialogue(resp['message'], type: "warning");
        }
        showOverly(false);
      }, onError: (err) {
        showOverly(false);
        // Handle structured errors from provider
        if (err is Map && err.containsKey('type')) {
          final errorType = err['type'];
          final errorMessage = err['message'] ?? 'An error occurred';

          // Only show dialog for non-network errors
          // Network errors are handled by connectivity check above
          if (errorType != 'network') {
            serviceController.showDialogue(errorMessage, type: "error");
          } else {
            serviceController.showDialogue(
              "No internet connection. Please check your network and try again.",
              type: "error",
            );
          }
        } else {
          // Fallback for non-structured errors
          serviceController.showDialogue(err.toString(), type: "error");
        }
      });
    } catch (exception) {
      showOverly(false);
      logger.error("Login exception: $exception");
      serviceController.showDialogue(
        "An unexpected error occurred. Please try again.",
        type: "error",
      );
    }
  }

  void validateEmail() {
    String email = emailTextController.text.trim();

    if (email.isEmpty) {
      _addError("email", ['Email is required'], 3);
    } else if (!isValidEmail(email)) {
      _addError("email", ['Enter a valid email e.g test@example.com'], 3);
    }
  }

  void validatePassword() {
    String password = passwordTextController.text;
    List<String> passwordErrors = [];

    if (password.isEmpty) {
      passwordErrors.add('Password is required');
    } else {
      if (password.length < 8) {
        passwordErrors.add('Password must be at least 8 characters long');
      }
      if (!RegExp(r'[a-z]').hasMatch(password)) {
        passwordErrors
            .add('Password must contain at least one lowercase letter');
      }
      if (!RegExp(r'[A-Z]').hasMatch(password)) {
        passwordErrors
            .add('Password must contain at least one uppercase letter');
      }
      if (!RegExp(r'[0-9]').hasMatch(password)) {
        passwordErrors.add('Password must contain at least one number');
      }
      if (!RegExp(r'[!@#$%^&*(),.?":{}|<>]').hasMatch(password)) {
        passwordErrors
            .add('Password must contain at least one special character');
      }
    }

    if (passwordErrors.isNotEmpty) {
      _addError("password", passwordErrors, 4);
    }
  }

  // COMMENTED OUT - No password validation on login screen (this is for signup)
  // validatePassword() {
  //   errors.removeWhere((element) => element['title'] == "password");

  //   String password = passwordTextController.text;
  //   List<String> errorMessages = [];

  //   if (password.isEmpty) {
  //     var message =
  //         validationMessageDetail['required'] ?? ":Attribute is required";
  //     message = message.replaceAll(
  //         ":Attribute", labelTextDetail['password_error'] ?? "Password");
  //     errorMessages.add(message);
  //   } else {
  //     // Check all password requirements
  //     if (password.length < 8) {
  //       errorMessages.add("Password must be at least 8 characters long");
  //     }
  //     if (!password.contains(RegExp(r'[a-z]'))) {
  //       errorMessages
  //           .add("Password must contain at least one lowercase letter");
  //     }
  //     if (!password.contains(RegExp(r'[A-Z]'))) {
  //       errorMessages
  //           .add("Password must contain at least one uppercase letter");
  //     }
  //     if (!password.contains(RegExp(r'[0-9]'))) {
  //       errorMessages.add("Password must contain at least one number");
  //     }
  //     if (!password.contains(RegExp(r'[!@#$%^&*(),.?":{}|<>]'))) {
  //       errorMessages
  //           .add("Password must contain at least one special character");
  //     }
  //   }

  //   if (errorMessages.isNotEmpty) {
  //     var err = {'title': "password", 'eList': errorMessages};
  //     errors.add(err);
  //   }
  // }

  void _addError(String title, List<String> errorList, int scrollPosition) {
    var err = {'title': title, 'eList': errorList};
    errors.add(err);

    // if (scrollField == false) {
    //   scrollError(context, scrollPosition, screenHeight);
    //   scrollField = true;
    // }
  }

  // List<String> getPasswordChecklist() {
  //   String password = passwordTextController.text;
  //   List<String> checklist = [];

  //   if (password.length >= 8) checklist.add("length");
  //   if (RegExp(r'[a-z]').hasMatch(password)) checklist.add("small");
  //   if (RegExp(r'[A-Z]').hasMatch(password)) checklist.add("capital");
  //   if (RegExp(r'[0-9]').hasMatch(password)) checklist.add("number");
  //   if (RegExp(r'[!@#$%^&*(),.?":{}|<>]').hasMatch(password))
  //     checklist.add("special");

  //   return checklist;
  // }

  // COMMENTED OUT - Not needed on login screen (this is for signup)
  // List<String> getPasswordChecklist() {
  //   List<String> checklist = [];
  //   String password = passwordTextController.text;

  //   if (password.length >= 8) {
  //     checklist.add("length");
  //   }

  //   if (password.contains(RegExp(r'[a-z]'))) {
  //     checklist.add("small");
  //   }
  //   if (password.contains(RegExp(r'[A-Z]'))) {
  //     checklist.add("capital");
  //   }
  //   if (password.contains(RegExp(r'[0-9]'))) {
  //     checklist.add("number");
  //   }
  //   if (password.contains(RegExp(r'[!@#$%^&*(),.?":{}|<>]'))) {
  //     checklist.add("special");
  //   }

  //   return checklist;
  // }

  Future signInWithGoogle() async {
    try {
      // Trigger the authentication flow
      final GoogleSignInAccount? googleUser =
          await GoogleSignIn(scopes: ["profile", "email"]).signIn();

      logger.info('Google user: $googleUser');
      // Obtain the auth details from the request
      final GoogleSignInAuthentication? googleAuth =
          await googleUser?.authentication;

      logger.info('Google auth: $googleAuth');

      // Create a new credential
      final credential = GoogleAuthProvider.credential(
        accessToken: googleAuth?.accessToken,
        idToken: googleAuth?.idToken,
      );

      logger.info('Credential: $credential');

      // Once signed in, return the UserCredential
      var userCredential =
          await FirebaseAuth.instance.signInWithCredential(credential);
      logger.info('User credential: $userCredential');
      var email = userCredential.user?.email;
      var name = userCredential.user?.displayName;
      var photoUrl = userCredential.user?.photoURL;
      var uId = userCredential.user?.uid;
      await socialLogin('google', email, name, photoUrl, uId);
      logger.info('Social login: $email, $name, $photoUrl, $uId');
    } on FirebaseAuthException catch (e) {
      logger.error("Google login error: ${e.message}");
      serviceController.showDialogue(e.message ?? "Google login error",
          type: "error");
    } catch (e) {
      logger.error("Google login error: $e");
      serviceController.showDialogue("Something went wrong", type: "error");
    }
  }

  Future signInWithFacebook() async {
    try {
      // Trigger the sign-in flow
      final LoginResult loginResult = await FacebookAuth.instance.login();
      logger.info('Facebook login result: $loginResult');

      // Create a credential from the access token
      final OAuthCredential facebookAuthCredential =
          FacebookAuthProvider.credential(loginResult.accessToken!.tokenString);

      logger.info('Facebook auth credential: $facebookAuthCredential');

      // Once signed in, return the UserCredential
      var userCredential = await FirebaseAuth.instance
          .signInWithCredential(facebookAuthCredential);
      logger.info('Facebook user credential: $userCredential');
      User? user = userCredential.user;
      var email = user?.email;
      var name = user?.displayName;
      var photoUrl = user?.photoURL;
      var uId = user?.uid;
      logger.info('Facebook user: $email, $name, $photoUrl, $uId');
      await socialLogin('facebook', email, name, photoUrl, uId);
    } on FirebaseAuthException catch (e) {
      logger.error("Facebook login error: ${e.message}");
      serviceController.showDialogue(e.message ?? "Facebook login error",
          type: "error");
    } catch (e) {
      logger.error("Facebook login error: $e");
      serviceController.showDialogue("Something went wrong", type: "error");
    }
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
        if (err is Map && err.containsKey('type')) {
          serviceController.showDialogue(
              err['type'] == 'network'
                  ? "No internet connection. Please check your network and try again."
                  : err['message'],
              type: "error");
        } else {
          serviceController.showDialogue(err.toString(), type: "error");
        }
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
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
        //   logger.info(resp);
        //   showOverly(false);
        // },onError: (err){;
        // showOverly(false);
        // });
      }, onError: (err) {
        showOverly(false);
        if (err is Map && err.containsKey('type')) {
          serviceController.showDialogue(
              err['type'] == 'network'
                  ? "No internet connection. Please check your network and try again."
                  : err['message'],
              type: "error");
        } else {
          serviceController.showDialogue(err.toString(), type: "error");
        }
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }

  Future<void> loginWithToken(
    String token, {
    bool persistToken = true,
  }) async {
    try {
      showOverly(true);
      logger.info('Login with token: $token');

      final resp = await LoginProvider()
          .loginWithToken(token, serviceController.langId.value);

      logger.info('Login with token response: $resp');

      if (resp['status'] != null && resp['status'] == "Success") {
        // Clear previous data
        serviceController.loginUserDetail.clear();
        await secureStorage.deleteAll();

        token = convertPercentToPipe(token);
        logger.info("Token after conversion: $token");

        // Store token only when allowed (skip for deep-link prelogin to keep user on dialog)
        if (persistToken) {
          await secureStorage.write(key: "token", value: token);
          serviceController.token = token;
        } else {
          logger.info(
              'loginWithToken: persistToken=false -> deferring token save until user action');
        }

        // Store languages if available
        serviceController.languages.clear();
        if (resp['data'] != null && resp['data']['languages'] != null) {
          serviceController.languages.addAll(resp['data']['languages']);
        }

        // Store user details
        final data = resp['data']['user'];
        serviceController.loginUserDetail['id'] =
            int.parse(data['id'].toString());
        serviceController.loginUserDetail['first_name'] =
            data['first_name'].toString();
        serviceController.loginUserDetail['last_name'] =
            data['last_name'].toString();
        serviceController.loginUserDetail['gender'] = data['gender'].toString();
        serviceController.loginUserDetail['profile_image'] =
            data['profile_image'].toString();
        serviceController.loginUserDetail['profile_original_image'] =
            data['profile_original_image'].toString();
        serviceController.loginUserDetail['email'] = data['email'].toString();
        serviceController.loginUserDetail['step'] =
            data['step']?.toString() ?? "5";
        serviceController.loginUserDetail['student'] =
            data['student']?.toString() ?? "0";
        serviceController.loginUserDetail['driver'] =
            data['driver']?.toString() ?? "0";
        serviceController.loginUserDetail['langId'] =
            data['lang_id'].toString();
        serviceController.loginUserDetail['driver_liscense'] =
            data['driver_liscense']?.toString() ?? "0";

        serviceController.langId.value = int.parse(data['lang_id'].toString());

        serviceController.loginUserDetail['student_card_exp_date'] =
            data['student_card_exp_date']?.toString() ?? "";
        serviceController.loginUserDetail['driver_average_rating'] =
            data['driver_average_rating']?.toString() ?? "0.0";
        serviceController.loginUserDetail['passenger_average_rating'] =
            data['passenger_average_rating']?.toString() ?? "0.0";
        serviceController.loginUserDetail['user_average_rating'] =
            data['user_average_rating']?.toString() ?? "0.0";
        serviceController.loginUserDetail['driver_total_ratings'] =
            data['driver_total_ratings']?.toString() ?? "0";
        serviceController.loginUserDetail['passenger_total_ratings'] =
            data['passenger_total_ratings']?.toString() ?? "0";
        serviceController.loginUserDetail['user_total_ratings'] =
            data['user_total_ratings']?.toString() ?? "0";

        logger.info("Login User Detail: ${serviceController.loginUserDetail}");

        // Save to secure storage
        await secureStorage.write(
          key: "userInfo",
          value: jsonEncode(serviceController.loginUserDetail),
        );

        // Update language settings
        var getLanguage = serviceController.languages.firstWhereOrNull(
          (element) => element['id'] == serviceController.langId.value,
        );
        if (getLanguage != null) {
          serviceController.langIcon.value = getLanguage['flag_icon'];
          serviceController.lang.value = getLanguage['abbreviation'];
        }

        showOverly(false);

        // Navigate based on user step
        final step = serviceController.loginUserDetail['step'];
        if (step == "1") {
          // If token wasn't persisted yet (deep link), the dialog proceed button will store it
          serviceController.showWelcomeDialogue(token);
        } else if (step == "2") {
          Get.offAllNamed('/stage_two');
        } else if (step == "3") {
          Get.offAllNamed('/stage_three_vehicle');
        } else if (step == "4") {
          Get.offAllNamed('/stage_four_vehicle');
        } else if (step == "5") {
          serviceController.navigationIndex.value = 0;
          Get.offAllNamed('/navigation');
        } else {
          serviceController.navigationIndex.value = 0;
          Get.offAllNamed('/navigation');
        }

        logger.info('Successfully logged in with token');
      } else if (resp['status'] != null && resp['status'] == "Error") {
        showOverly(false);
        serviceController
            .showDialogue(resp['message'] ?? "Invalid token or token expired");
        Get.offAllNamed('/login');
      } else {
        showOverly(false);
        serviceController.showDialogue("Failed to login with token");
        Get.offAllNamed('/login');
      }
    } catch (exception) {
      showOverly(false);
      logger.info('Error logging in with token: $exception');
      if (exception is Map &&
          exception.containsKey('type') &&
          exception['type'] == 'network') {
        serviceController.showDialogue(
            "No internet connection. Please check your network and try again.",
            type: "error");
      } else {
        serviceController.showDialogue("An error occurred during auto-login",
            type: "error");
      }
      Get.offAllNamed('/login');
    }
  }

  socialLogin(type, email, name, photoUrl, typeId) async {
    try {
      showOverly(true);
      logger.info('Social login: $type, $email, $name, $photoUrl, $typeId');
      LoginProvider()
          .socialLogin(type, email, name, photoUrl, typeId,
              serviceController.langId.value)
          .then((resp) async {
        logger.info('Social login response: $resp');
        errorList.clear();
        errors.clear();

        logger.info('Social login errors: $errors');

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
          if (resp['data'] != null && resp['data']['languages'] != null) {
            serviceController.languages.addAll(resp['data']['languages']);
          }

          await secureStorage.deleteAll();
          final rawToken = resp['data']['token']?.toString() ?? "";
          if (rawToken.isNotEmpty) {
            final normalizedToken = convertPercentToPipe(rawToken);
            await secureStorage.write(key: "token", value: normalizedToken);
            serviceController.token = normalizedToken;
          }
          serviceController.loginUserDetail.clear();
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
              resp['data']['step'] == null
                  ? "1"
                  : resp['data']['step'].toString();
          serviceController.loginUserDetail['driver'] =
              resp['data']['driver'].toString();
          logger.info("Driver: ${resp['data']['driver']}");
          serviceController.loginUserDetail['langId'] =
              resp['data']['langId'].toString();
          serviceController.langId.value =
              int.parse(resp['data']['langId'].toString());
          serviceController.loginUserDetail['driver_liscense'] =
              resp['data']['driver_liscense'].toString();
          serviceController.loginUserDetail['driver_average_rating'] =
              resp['data']['driver_average_rating'] != null
                  ? resp['data']['driver_average_rating'].toString()
                  : "0.0";
          serviceController.loginUserDetail['passenger_average_rating'] =
              resp['data']['passenger_average_rating'] != null
                  ? resp['data']['passenger_average_rating'].toString()
                  : "0.0";
          serviceController.loginUserDetail['user_average_rating'] =
              resp['data']['user_average_rating'] != null
                  ? resp['data']['user_average_rating'].toString()
                  : "0.0";
          serviceController.loginUserDetail['driver_total_ratings'] =
              resp['data']['driver_total_ratings'] != null
                  ? resp['data']['driver_total_ratings'].toString()
                  : "0";
          serviceController.loginUserDetail['passenger_total_ratings'] =
              resp['data']['passenger_total_ratings'] != null
                  ? resp['data']['passenger_total_ratings'].toString()
                  : "0";
          serviceController.loginUserDetail['user_total_ratings'] =
              resp['data']['user_total_ratings'] != null
                  ? resp['data']['user_total_ratings'].toString()
                  : "0";

          secureStorage.write(
              key: "userInfo",
              value: jsonEncode(serviceController.loginUserDetail));

          var getLanguage = serviceController.languages.firstWhereOrNull(
              (element) => element['id'] == serviceController.langId.value);
          if (getLanguage != null) {
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
            Get.offAllNamed('/stage_four_vehicle');
          } else if (serviceController.loginUserDetail['step'] == "5") {
            serviceController.navigationIndex.value = 0;
            Get.offAllNamed('/navigation');
          } else {
            serviceController.navigationIndex.value = 0;
            Get.offAllNamed('/navigation');
          }
          // Get.offAllNamed('/navigation');
        }
        showOverly(false);
      }, onError: (err) {
        showOverly(false);
        if (err is Map && err.containsKey('type')) {
          serviceController.showDialogue(
              err['type'] == 'network'
                  ? "No internet connection. Please check your network and try again."
                  : err['message'],
              type: "error");
        } else {
          serviceController.showDialogue(err.toString(), type: "error");
        }
      });
    } catch (exception) {
      serviceController.showDialogue(exception.toString(), type: "error");
    }
  }
}
