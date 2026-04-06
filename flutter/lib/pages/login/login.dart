
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:get/get.dart';
// import 'package:flutter_web_auth/flutter_web_auth.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/login/LoginController.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/language_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import 'package:proximaride_app/pages/widgets/other_login_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/tool_tip.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:signin_with_linkedin/signin_with_linkedin.dart';
//import 'package:tiktok_login_flutter/tiktok_login_flutter.dart';

class LoginPage extends StatelessWidget {
  const LoginPage({super.key});

  @override
  Widget build(BuildContext context) {
    final LoginController controller;
    if (Get.isRegistered<LoginController>()) {
      final existingController = Get.find<LoginController>();
      if (existingController.loadedLangId !=
          existingController.serviceController.langId.value) {
        Get.delete<LoginController>(force: true);
        controller = Get.put(LoginController());
      } else {
        controller = existingController;
      }
    } else {
      controller = Get.put(LoginController());
    }
    return Scaffold(
      body: Obx(() {
        // Show loading state
        if (controller.errorStateManager.isLoading.value) {
          return Center(child: progressCircularWidget(context));
        }

        // Show error state
        if (controller.errorStateManager.hasError.value) {
          return ErrorStateWidget(
            message: controller.errorStateManager.errorMessage.value,
            errorType: controller.errorStateManager.errorType.value,
            onRetry: () {
              if (controller.errorStateManager.onRetry.value != null) {
                controller.errorStateManager.onRetry.value!();
              }
            },
          );
        }

        // Show normal content when loading is complete and no error
        if (controller.isLoading.value == true) {
          return Center(child: progressCircularWidget(context));
        } else {
          // Obx must observe `errors` directly; nested reads alone can miss
          // RxList updates on web so field tooltips never rebuild after submit.
          controller.errors.length;
          return SafeArea(
            child: Stack(
              children: [
                Container(
                  padding: EdgeInsets.all(getValueForScreenType<double>(
                    context: context,
                    mobile: 15.0,
                    tablet: 15.0,
                  )),
                  child: SingleChildScrollView(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        const SizedBox(height: 65),
                        Center(
                          child: Image.asset(
                            logoImage,
                            width: getValueForScreenType<double>(
                              context: context,
                              mobile: 164.0,
                              tablet: 164.0,
                            ),
                            height: getValueForScreenType<double>(
                              context: context,
                              mobile: 106.0,
                              tablet: 106.0,
                            ),
                          ),
                        ),
                        const SizedBox(height: 20),
                        Center(
                          child: txt25Size(
                            title:
                                controller.labelTextDetail['continue_label'] ??
                                    "Log in to your account",
                            fontFamily: bold,
                            textColor: primaryColor,
                            context: context,
                          ),
                        ),
                        const SizedBox(height: 40),
                        txt20Size(
                          title: controller.labelTextDetail['email_label'] ??
                              "E-mail address",
                          fontFamily: bold,
                          textColor: textColor,
                          context: context,
                        ),
                        const SizedBox(height: 5),

                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Email Field
                            fieldsWidget(
                              textController: controller.emailTextController,
                              fieldType: "email",
                              readonly: false,
                              fontSize: 18.0,
                              fontFamily: regular,
                              focusNode: controller.focusNodes['1'],
                              onChanged: (value) {
                                // Email error behavior for Login:
                                // - Do NOT validate on every keystroke (errors appear only on "Log in" tap).
                                // - If an email error is already shown, clear it once the user corrects it.
                                final existingError = controller.errors
                                    .firstWhereOrNull(
                                        (e) => e['title'] == "email");

                                if (existingError != null) {
                                  final trimmed = value.trim();

                                  // If the email is now valid OR the user cleared the field,
                                  // remove the existing error. Fresh validation will only run
                                  // when the user taps the "Log in" button via controller.login().
                                  if (trimmed.isEmpty ||
                                      controller.isValidEmail(trimmed)) {
                                    controller.errors.remove(existingError);
                                  }
                                }
                              },
                            ),

                            if (controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "email") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                      (element) => element['title'] == "email"))
                            ],

                            const SizedBox(height: 10),

                            // Password Label
                            txt20Size(
                              title: controller
                                      .labelTextDetail['password_label'] ??
                                  "Password",
                              fontFamily: bold,
                              textColor: textColor,
                              context: context,
                            ),

                            const SizedBox(height: 5),

                            // Password Field
                            TextFormField(
                              controller: controller.passwordTextController,
                              focusNode: controller.focusNodes['2'],
                              onChanged: (value) {
                                // Clear password error when user starts typing
                                controller.errors.removeWhere((element) =>
                                    element['title'] == "password");

                                // Trigger validation update for real-time progress bars
                                // controller.validatePassword(); // Commented out - no validation needed on login
                              },
                              // onChanged: (value) {
                              //   // Remove previous error
                              //   controller.errors.removeWhere(
                              //       (element) => element['title'] == "password");

                              //   if (value.isEmpty) {
                              //     controller.errors.add({
                              //       'title': 'password',
                              //       'eList': ['Password is required']
                              //     });
                              //   }

                              //   controller.validatePassword();
                              // },
                              validator: (value) {
                                // Form validation when submitted
                                // controller.validatePassword(); // Commented out - no validation needed on login
                                return null;
                              },
                              // validator: (value) {
                              //   if (value == null || value.trim().isEmpty) {
                              //     controller.errors.removeWhere((element) =>
                              //         element['title'] == "password");
                              //     controller.errors.add({
                              //       'title': 'password',
                              //       'eList': ['Password is required']
                              //     });
                              //   } else {
                              //     controller.errors.removeWhere((element) =>
                              //         element['title'] == "password");
                              //   }

                              //   controller.validatePassword();
                              //   return null;
                              // },

                              decoration: InputDecoration(
                                hintStyle: appPlaceholderTextStyle().copyWith(
                                  fontSize: 18,
                                ),
                                // hintText: "Password",
                                errorStyle: const TextStyle(
                                    color: primaryColor, fontSize: 16),
                                enabledBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(5.0),
                                    borderSide: BorderSide(
                                        color: controller.errors
                                                    .firstWhereOrNull(
                                                        (element) =>
                                                            element['title'] ==
                                                            "password") !=
                                                null
                                            ? primaryColor
                                            : Colors.grey.shade400,
                                        style: BorderStyle.solid,
                                        width: 1)),
                                focusedBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(5.0),
                                    borderSide:
                                        const BorderSide(color: primaryColor)),
                                filled: true,
                                fillColor: inputColor,
                                contentPadding: const EdgeInsets.symmetric(
                                    vertical: 16.0, horizontal: 12.0),
                                suffixIcon: IconButton(
                                  icon: Icon(
                                    controller.isPasswordVisible.value
                                        ? Icons.visibility_off
                                        : Icons.visibility,
                                  ),
                                  onPressed: () =>
                                      controller.isPasswordVisible.value =
                                          !controller.isPasswordVisible.value,
                                ),
                              ),
                              style: const TextStyle(
                                  fontSize: 18,
                                  fontFamily: regular,
                                  color: textColor),
                              obscureText: !controller.isPasswordVisible.value,
                              keyboardType: TextInputType.visiblePassword,
                            ),
                            if (controller.errors.firstWhereOrNull(
                                    (element) =>
                                        element['title'] == "password") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "password"))
                            ],
                          ],
                        ),

                        // Password Requirements Section - COMMENTED OUT (not needed for login)
                        // if (controller
                        //     .passwordTextController.text.isNotEmpty) ...[
                        //   const SizedBox(height: 10),
                        //   Container(
                        //     padding: const EdgeInsets.all(16.0),
                        //     decoration: BoxDecoration(
                        //       color: Colors.grey.shade50,
                        //       borderRadius: BorderRadius.circular(8.0),
                        //       border: Border.all(color: Colors.grey.shade300),
                        //     ),
                        //     child: Column(
                        //       crossAxisAlignment: CrossAxisAlignment.start,
                        //       children: [
                        //         txt20Size(
                        //           title: "Password Requirements",
                        //           fontFamily: bold,
                        //           textColor: textColor,
                        //           context: context,
                        //         ),
                        //         const SizedBox(height: 12),

                        //         // Progress bars container
                        //         Container(
                        //           padding: const EdgeInsets.all(12.0),
                        //           decoration: BoxDecoration(
                        //             color: Colors.white,
                        //             borderRadius: BorderRadius.circular(6.0),
                        //             border:
                        //                 Border.all(color: Colors.grey.shade200),
                        //           ),
                        //           child: Column(
                        //             children: [
                        //               // Overall progress bar
                        //               Row(
                        //                 children: [
                        //                   txt18Size(
                        //                     title: "Overall Progress",
                        //                     fontFamily: regular,
                        //                     textColor: textColor,
                        //                     context: context,
                        //                   ),
                        //                   const Spacer(),
                        //                   txt16Size(
                        //                     title:
                        //                         "${(controller.getPasswordChecklist().length * 20).toInt()}%",
                        //                     fontFamily: bold,
                        //                     textColor: controller
                        //                                 .getPasswordChecklist()
                        //                                 .length ==
                        //                             5
                        //                         ? Colors.green
                        //                         : primaryColor,
                        //                     context: context,
                        //                   ),
                        //                 ],
                        //               ),
                        //               const SizedBox(height: 8),
                        //               LinearProgressIndicator(
                        //                 value: controller
                        //                         .getPasswordChecklist()
                        //                         .length /
                        //                     5,
                        //                 backgroundColor: Colors.grey.shade300,
                        //                 valueColor:
                        //                     AlwaysStoppedAnimation<Color>(
                        //                   controller
                        //                               .getPasswordChecklist()
                        //                               .length ==
                        //                           5
                        //                       ? Colors.green
                        //                       : primaryColor,
                        //                 ),
                        //                 minHeight: 6.0,
                        //               ),
                        //               const SizedBox(height: 16),

                        //               // Individual requirement bars
                        //               _buildRequirementRow(
                        //                 context,
                        //                 "At least 8 characters",
                        //                 controller
                        //                     .getPasswordChecklist()
                        //                     .contains("length"),
                        //                 Icons.text_fields,
                        //               ),
                        //               const SizedBox(height: 8),
                        //               _buildRequirementRow(
                        //                 context,
                        //                 "One lowercase letter",
                        //                 controller
                        //                     .getPasswordChecklist()
                        //                     .contains("small"),
                        //                 Icons.text_format,
                        //               ),
                        //               const SizedBox(height: 8),
                        //               _buildRequirementRow(
                        //                 context,
                        //                 "One uppercase letter",
                        //                 controller
                        //                     .getPasswordChecklist()
                        //                     .contains("capital"),
                        //                 Icons.format_size,
                        //               ),
                        //               const SizedBox(height: 8),
                        //               _buildRequirementRow(
                        //                 context,
                        //                 "One number",
                        //                 controller
                        //                     .getPasswordChecklist()
                        //                     .contains("number"),
                        //                 Icons.numbers,
                        //               ),
                        //               const SizedBox(height: 8),
                        //               _buildRequirementRow(
                        //                 context,
                        //                 "One special character",
                        //                 controller
                        //                     .getPasswordChecklist()
                        //                     .contains("special"),
                        //                 Icons.star,
                        //               ),
                        //             ],
                        //           ),
                        //         ),

                        //         // Security strength indicator
                        //         const SizedBox(height: 12),
                        //         Container(
                        //           padding: const EdgeInsets.symmetric(
                        //               horizontal: 12.0, vertical: 8.0),
                        //           decoration: BoxDecoration(
                        //             color: _getStrengthColor(controller
                        //                     .getPasswordChecklist()
                        //                     .length)
                        //                 .withOpacity(0.1),
                        //             borderRadius: BorderRadius.circular(20.0),
                        //             border: Border.all(
                        //               color: _getStrengthColor(controller
                        //                   .getPasswordChecklist()
                        //                   .length),
                        //               width: 1.0,
                        //             ),
                        //           ),
                        //           child: Row(
                        //             mainAxisSize: MainAxisSize.min,
                        //             children: [
                        //               Icon(
                        //                 _getStrengthIcon(controller
                        //                     .getPasswordChecklist()
                        //                     .length),
                        //                 size: 16.0,
                        //                 color: _getStrengthColor(controller
                        //                     .getPasswordChecklist()
                        //                     .length),
                        //               ),
                        //               const SizedBox(width: 6),
                        //               txt16Size(
                        //                 title: _getStrengthText(controller
                        //                     .getPasswordChecklist()
                        //                     .length),
                        //                 fontFamily: bold,
                        //                 textColor: _getStrengthColor(controller
                        //                     .getPasswordChecklist()
                        //                     .length),
                        //                 context: context,
                        //               ),
                        //             ],
                        //           ),
                        //         ),
                        //       ],
                        //     ),
                        //   ),
                        // ],

                        const SizedBox(height: 20),
                        SizedBox(
                          width: MediaQuery.of(context).size.width,
                          height: 50,
                          child: elevatedButtonWidget(
                            textWidget: txt22Size(
                              title: controller
                                      .labelTextDetail['submit_button_label'] ??
                                  "Log in",
                              fontFamily: regular,
                              textColor: Colors.white,
                              context: context,
                            ),
                            onPressed: () async {
                              await controller.login();
                            },
                            context: context,
                            btnRadius: 5.0,
                          ),
                        ),
                        const SizedBox(height: 5),
                        Align(
                          alignment: Alignment.topRight,
                          child: InkWell(
                            onTap: () {
                              Get.toNamed("/forgot_password");
                            },
                            child: txt20Size(
                              title: controller.labelTextDetail[
                                      'forgot_password_label'] ??
                                  "Forgot your password?",
                              fontFamily: regular,
                              textColor: primaryColor,
                              context: context,
                            ),
                          ),
                        ),
                        const SizedBox(height: 10),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Container(
                                height: 2,
                                color: primaryColor,
                              ),
                            ),
                            const SizedBox(width: 10),
                            txt20Size(
                              title: controller.labelTextDetail['or_label'] ??
                                  "or log in with",
                              fontFamily: regular,
                              textColor: textColor,
                              context: context,
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: Container(
                                height: 2,
                                color: primaryColor,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 10),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            otherLogInWidget(
                              imagePath: facebookImage,
                              context: context,
                              onTap: () async {
                                logger.info('Facebook login clicked');
                                await controller.signInWithFacebook();
                              },
                            ),
                            const SizedBox(width: 5),
                            otherLogInWidget(
                              imagePath: linkedInImage,
                              context: context,
                              onTap: () async {
                                logger.info('LinkedIn login clicked');
                                logger.info(
                                    'clientId: ${dotenv.env['clientId']}');
                                logger.info(
                                    'clientSecret: ${dotenv.env['clientSecret']}');
                                logger.info('redirectUrl: $url/');
                                logger.info(
                                    'scope: ${['openid', 'profile', 'email']}');
                                final linkedInConfig = LinkedInConfig(
                                  clientId: '${dotenv.env['clientId']}',
                                  clientSecret: '${dotenv.env['clientSecret']}',
                                  redirectUrl:
                                      '$url/en/signup/linkedin/callback',
                                  scope: ['openid', 'profile', 'email'],
                                );

                                SignInWithLinkedIn.signIn(
                                  context,
                                  config: linkedInConfig,
                                  onGetAuthToken: (data) {
                                    controller.getUserInfo(data.toJson());
                                  },
                                  onSignInError: (error) {},
                                );
                              },
                            ),
                            const SizedBox(width: 5),
                            otherLogInWidget(
                              imagePath: googleImage,
                              context: context,
                              onTap: () async {
                                await controller.signInWithGoogle();
                              },
                            ),
                            // const SizedBox(width: 5),
                            // otherLogInWidget(
                            //   imagePath: instagramImage,
                            //   context: context,
                            //   onTap: () async {
                            //     logger.info("Instagram login clicked");
                            //     // Instagram login implementation would go here
                            //   },
                            // ),
                          ],
                        ),
                        const SizedBox(height: 15),
                        Divider(
                          color: primaryColor,
                          thickness: 2,
                        ),
                        const SizedBox(height: 10),
                        Center(
                          child: RichText(
                            text: TextSpan(
                              children: [
                                textSpan(
                                  context: context,
                                  textColor: textColor,
                                  fontFamily: bold,
                                  title: controller.labelTextDetail[
                                          'no_account_label'] ??
                                      "Don't have any account yet?",
                                  // title: 'Don’t have an account yet?',
                                  textSize: 20.0,
                                ),
                                textSpan(
                                  context: context,
                                  textColor: primaryColor,
                                  fontFamily: bold,
                                  title:
                                      " ${controller.labelTextDetail['signup_link_label'] ?? "Sign up"}",
                                  // title: " Sign up",
                                  textSize: 20.0,
                                  recognizer: TapGestureRecognizer()
                                    ..onTap = () {
                                      Get.toNamed("/signup");
                                    },
                                ),
                                textSpan(
                                  context: context,
                                  textColor: textColor,
                                  fontFamily: bold,
                                  title:
                                      " ${controller.labelTextDetail['now_label'] ?? 'now'}",
                                  textSize: 20.0,
                                ),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(height: 10),
                        // Row(
                        //   mainAxisAlignment: MainAxisAlignment.center,
                        //   children: [
                        //     // txt22Size(
                        //     //   title:
                        //     //       controller.labelTextDetail['language_label'] ??
                        //     //           "Language :",
                        //     //   fontFamily: regular,
                        //     //   textColor: textColor,
                        //     //   context: context,
                        //     // ),
                        //     // const SizedBox(width: 5),
                        //     InkWell(
                        //       onTap: () {
                        //         languageBottomSheet(
                        //           MediaQuery.of(context).size.width,
                        //           controller.serviceController,
                        //           page: "login",
                        //         );
                        //       },
                        //       child: Ink(
                        //         padding: const EdgeInsets.all(5.0),
                        //         child: ClipRRect(
                        //           borderRadius: BorderRadius.circular(50.0),
                        //           child: networkCacheImageWidget(
                        //             controller.serviceController.langIcon.value,
                        //             BoxFit.cover,
                        //             30.0,
                        //             30.0,
                        //           ),
                        //         ),
                        //       ),
                        //     ),
                        //   ],
                        // ),
                        Align(
                          alignment: Alignment.center,
                          child: InkWell(
                            onTap: () {
                              languageBottomSheet(
                                MediaQuery.of(context).size.width,
                                controller.serviceController,
                                page: "login",
                              );
                            },
                            child: Ink(
                              padding: const EdgeInsets.all(5.0),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(50.0),
                                child: networkCacheImageWidget(
                                  controller.serviceController.langIcon.value,
                                  BoxFit.cover,
                                  30.0,
                                  30.0,
                                ),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                if (controller.showOverly.value == true) ...[
                  overlayWidget(context)
                ],
              ],
            ),
          );
        }
      }),
    );
  }

 
}
