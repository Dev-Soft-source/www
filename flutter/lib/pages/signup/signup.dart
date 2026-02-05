import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/signup/RegisterController.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/language_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import 'package:proximaride_app/pages/widgets/other_login_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:signin_with_linkedin/signin_with_linkedin.dart';
//import 'package:tiktok_login_flutter/tiktok_login_flutter.dart';
import '../widgets/tool_tip.dart';

class SignupPage extends GetView<RegisterController> {
  const SignupPage({super.key});
  @override
  Widget build(BuildContext context) {
    Get.put(RegisterController());
    return Scaffold(body: Obx(() {
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
        return SafeArea(
          child: GestureDetector(
            onTap: () {
              controller.showToolTip.value = false;
            },
            child: Stack(
              children: [
                Container(
                  padding: EdgeInsets.all(getValueForScreenType<double>(
                    context: context,
                    mobile: 15.0,
                    tablet: 15.0,
                  )),
                  child: SingleChildScrollView(
                    controller: controller.scrollController,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        65.heightBox,
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
                        20.heightBox,
                        Center(
                            child: txt25Size(
                                title:
                                    "${controller.labelTextDetail['app_main_heading'] ?? "Create a new account with"}",
                                fontFamily: bold,
                                textColor: primaryColor,
                                context: context)),
                        10.heightBox,
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            otherLogInWidget(
                                imagePath: facebookImage,
                                context: context,
                                onTap: () async {
                                  await controller.signInWithFacebook();
                                }),
                            5.widthBox,
                            otherLogInWidget(
                                imagePath: linkedInImage,
                                context: context,
                                onTap: () async {
                                  final linkedInConfig = LinkedInConfig(
                                    clientId: '${dotenv.env['clientId']}',
                                    clientSecret:
                                        '${dotenv.env['clientSecret']}',
                                    redirectUrl: '$url/',
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
                                }),
                            5.widthBox,
                            otherLogInWidget(
                                imagePath: googleImage,
                                context: context,
                                onTap: () async {
                                  await controller.signInWithGoogle();
                                }),
                            // 5.widthBox,
                            // otherLogInWidget(
                            //     imagePath: instagramImage,
                            //     context: context,
                            //     onTap: () {}),
                            // otherLogInWidget(
                            //     imagePath: snapChatImage,
                            //     context: context,
                            //     onTap: () {}),
                            // otherLogInWidget(
                            //     imagePath: tikTokImage,
                            //     context: context,
                            //     onTap: () async{
                            //       var code = await TiktokLoginFlutter.authorize(
                            //           "user.info.basic,user.info.profile");
                            //     }),
                          ],
                        ),
                        30.heightBox,
                        Center(
                            child: txt20Size(
                                title:
                                    "${controller.labelTextDetail['or_label'] ?? "Or sign up with e-mail (all fields are required)"}",
                                fontFamily: bold,
                                textColor: textColor,
                                context: context)),
                        30.heightBox,
                        txt18Size(
                            title:
                                "* ${controller.labelTextDetail['required_label'] ?? "* Indicates required fields"}",
                            // "* Indicates required field",
                            fontFamily: regular,
                            context: context,
                            textColor: Colors.red),
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title:
                                    "${controller.labelTextDetail['first_name_label'] ?? "First name"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(
                                title: "*",
                                fontFamily: regular,
                                context: context,
                                textColor: Colors.red),
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                          textController: controller.firstNameTextController,
                          fieldType: "text",
                          readonly: false,
                          fontSize: 16.0,
                          fontFamily: regular,
                          focusNode: controller.focusNodes[1.toString()],
                          onChanged: (value) {
                            if (controller.errors.firstWhereOrNull((element) =>
                                    element['title'] == "first_name") !=
                                null) {
                              controller.errors.remove(controller.errors
                                  .firstWhereOrNull((element) =>
                                      element['title'] == "first_name"));
                            }
                            // if (value.isNotEmpty) {
                            //   controller.validateFirstName();
                            // }
                          },
                          // isError: controller.errors.firstWhereOrNull((element) =>
                          //         element['title'] == "first_name") !=
                          //     null,
                          // focusNode: controller.focusNodes[1.toString()],
                        ),
                        if (controller.errors.firstWhereOrNull((element) =>
                                element['title'] == "first_name") !=
                            null) ...[
                          toolTip(
                              tip: controller.errors.firstWhereOrNull(
                                  (element) =>
                                      element['title'] == "first_name"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title:
                                    "${controller.labelTextDetail['last_name_label'] ?? "Last name"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(
                                title: "*",
                                fontFamily: regular,
                                context: context,
                                textColor: Colors.red),
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                          textController: controller.lastNameTextController,
                          fieldType: "text",
                          readonly: false,
                          fontSize: 16.0,
                          fontFamily: regular,
                          focusNode: controller.focusNodes[2.toString()],
                          onChanged: (value) {
                            if (controller.errors.firstWhereOrNull((element) =>
                                    element['title'] == "last_name") !=
                                null) {
                              controller.errors.remove(controller.errors
                                  .firstWhereOrNull((element) =>
                                      element['title'] == "last_name"));
                            }
                            // if (value.isNotEmpty) {
                            //   controller.validateLastName();
                            // }
                          },
                          // isError: controller.errors.firstWhereOrNull(
                          //         (element) => element['title'] == "last_name") !=
                          //     null,
                          // focusNode: controller.focusNodes[2.toString()],
                        ),
                        if (controller.errors.firstWhereOrNull(
                                (element) => element['title'] == "last_name") !=
                            null) ...[
                          toolTip(
                              tip: controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "last_name"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title:
                                    "${controller.labelTextDetail['email_label'] ?? "E-mail address"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(
                                title: "*",
                                fontFamily: regular,
                                context: context,
                                textColor: Colors.red),
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                          textController: controller.emailTextController,
                          fieldType: "email",
                          readonly: false,
                          fontSize: 16.0,
                          fontFamily: regular,
                          focusNode: controller.focusNodes[3.toString()],
                          onChanged: (value) {
                            // Only clear the email error once the user has actually corrected it.
                            // We do NOT trigger validation here to avoid showing errors while typing;
                            // full validation happens when the user taps "Sign up".
                            final existingError = controller.errors
                                .firstWhereOrNull(
                                    (element) => element['title'] == "email");

                            if (existingError != null) {
                              final trimmed = value.trim();

                              // If the email is now valid OR the user has cleared the field,
                              // remove the existing error and let the next Sign Up press
                              // re‑validate as needed.
                              if (trimmed.isEmpty ||
                                  controller.isValidEmail(trimmed)) {
                                controller.errors.remove(existingError);
                              }
                            }
                          },
                          // isError: controller.errors.firstWhereOrNull(
                          //         (element) => element['title'] == "email") !=
                          //     null,
                          // focusNode: controller.focusNodes[3.toString()],
                        ),
                        if (controller.errors.firstWhereOrNull(
                                (element) => element['title'] == "email") !=
                            null) ...[
                          toolTip(
                              tip: controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "email"))
                        ],
                        10.heightBox,
                        Stack(
                          children: [
                            Column(
                              children: [
                                Row(
                                  children: [
                                    txt20Size(
                                        title:
                                            "${controller.labelTextDetail['password_label'] ?? "Password"}",
                                        fontFamily: regular,
                                        textColor: textColor,
                                        context: context),
                                    txt20Size(
                                        title: "*",
                                        fontFamily: regular,
                                        context: context,
                                        textColor: Colors.red),
                                    5.widthBox,
                                    InkWell(
                                      onTap: () {
                                        controller.showToolTip.value =
                                            !controller.showToolTip.value;
                                      },
                                      child: Image.asset(
                                        infoImage,
                                        color: Colors.black,
                                        width: getValueForScreenType<double>(
                                          context: context,
                                          mobile: 25.0,
                                          tablet: 25.0,
                                        ),
                                        height: getValueForScreenType<double>(
                                          context: context,
                                          mobile: 50.0,
                                          tablet: 50.0,
                                        ),
                                      ),
                                    )
                                  ],
                                ),
                                5.heightBox,
                                // TextFormField(
                                //   // focusNode: controller.focusNodes[4.toString()],
                                //   controller: controller.passwordTextController,
                                //   onChanged: (value) {
                                //     if (controller.errors.firstWhereOrNull(
                                //             (element) =>
                                //                 element['title'] == "password") !=
                                //         null) {
                                //       controller.errors.remove(controller.errors
                                //           .firstWhereOrNull((element) =>
                                //               element['title'] == "password"));
                                //     }
                                //     if (value.isNotEmpty) {
                                //       controller.validatePassword();
                                //     }
                                //   },
                                //   validator: (value) {
                                //     // Form validation when submitted
                                //     controller.validatePassword();
                                //     return null;
                                //   },
                                //   maxLines: 1,
                                //   decoration: InputDecoration(
                                //     hintStyle: const TextStyle(
                                //         fontSize: 10,
                                //         fontFamily: regular,
                                //         color: textColor),
                                //     enabledBorder: OutlineInputBorder(
                                //         borderRadius: BorderRadius.circular(5.0),
                                //         borderSide: BorderSide(
                                //             color: controller.errors
                                //                         .firstWhereOrNull(
                                //                             (element) =>
                                //                                 element[
                                //                                     'title'] ==
                                //                                 "password") !=
                                //                     null
                                //                 ? primaryColor
                                //                 : Colors.grey.shade400,
                                //             style: BorderStyle.solid,
                                //             width: 1)),
                                //     focusedBorder: OutlineInputBorder(
                                //         borderRadius: BorderRadius.circular(5.0),
                                //         borderSide: const BorderSide(
                                //             color: primaryColor)),
                                //     filled: true,
                                //     fillColor: inputColor,
                                //     contentPadding: const EdgeInsets.symmetric(
                                //         vertical: 0.0, horizontal: 8.0),
                                //     suffixIcon: IconButton(
                                //       icon: Icon(
                                //         controller.isPasswordVisible.value
                                //             ? Icons.visibility_off
                                //             : Icons.visibility,
                                //       ),
                                //       onPressed: () =>
                                //           controller.isPasswordVisible.value =
                                //               !controller.isPasswordVisible.value,
                                //     ),
                                //   ),
                                //   style: const TextStyle(
                                //       fontSize: 16,
                                //       fontFamily: regular,
                                //       color: textColor),
                                //   obscureText:
                                //       !controller.isPasswordVisible.value,
                                //   keyboardType: TextInputType.visiblePassword,
                                //   textInputAction: TextInputAction.next,
                                // ),
                                // controller.showToolTip.value
                                //     ? 45.heightBox
                                //     : const SizedBox(),
                                TextFormField(
                                  focusNode:
                                      controller.focusNodes[4.toString()],
                                  controller: controller.passwordTextController,
                                  onChanged: (value) {
                                    // Clear password error when user starts typing
                                    controller.errors.removeWhere((element) =>
                                        element['title'] == "password");

                                    // Trigger validation update for real-time progress bars
                                    controller.validatePassword();
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
                                    controller.validatePassword();
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
                                    hintMaxLines: 10,
                                    hintStyle: const TextStyle(fontSize: 18),
                                    // hintText: "Select Password",
                                    // hintText:
                                    //     "${controller.labelTextDetail['password_placeholder'] ?? 'Your password must be at least 8 characters long, including a lowercase letter, an uppercase letter, a number, and a special character'}",
                                    errorStyle: const TextStyle(
                                        color: primaryColor, fontSize: 16),
                                    enabledBorder: OutlineInputBorder(
                                        borderRadius:
                                            BorderRadius.circular(5.0),
                                        borderSide: BorderSide(
                                            color: controller.errors
                                                        .firstWhereOrNull(
                                                            (element) =>
                                                                element[
                                                                    'title'] ==
                                                                "password") !=
                                                    null
                                                ? primaryColor
                                                : Colors.grey.shade400,
                                            style: BorderStyle.solid,
                                            width: 1)),
                                    focusedBorder: OutlineInputBorder(
                                        borderRadius:
                                            BorderRadius.circular(5.0),
                                        borderSide: const BorderSide(
                                            color: primaryColor)),
                                    filled: true,
                                    fillColor: inputColor,
                                    contentPadding: const EdgeInsets.symmetric(
                                        vertical: 8.0, horizontal: 8.0),
                                    suffixIcon: IconButton(
                                      icon: Icon(
                                        controller.isPasswordVisible.value
                                            ? Icons.visibility_off
                                            : Icons.visibility,
                                      ),
                                      onPressed: () => controller
                                              .isPasswordVisible.value =
                                          !controller.isPasswordVisible.value,
                                    ),
                                  ),
                                  style: const TextStyle(
                                      fontSize: 16,
                                      fontFamily: regular,
                                      color: textColor),
                                  obscureText:
                                      !controller.isPasswordVisible.value,
                                  keyboardType: TextInputType.visiblePassword,
                                ),
                                // Show password errors - handle both empty and validation errors
                                if (controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] == "password") !=
                                    null) ...[
                                  // Show empty password tooltip for empty password
                                  if (controller.passwordTextController.text
                                      .trim()
                                      .isEmpty) ...[
                                    toolTipEmptyPassword(context),
                                  ] else ...[
                                    // Show regular tooltip for other password errors (including backend errors)
                                    toolTip(
                                        tip: controller.errors.firstWhereOrNull(
                                            (element) =>
                                                element['title'] == "password"))
                                  ]
                                ]
                              ],
                            ),
                            Positioned(
                              top: 30.0,
                              left: 0,
                              right: 0,
                              bottom: 0,
                              child: controller.showToolTip.value
                                  ? toolTip(
                                      tip:
                                          "${controller.labelTextDetail['password_placeholder'] ?? 'The password length should be at least 8 characters and must include one lower case, one uppercase, one number, and one special character'}",
                                      type: 'text',
                                      position: 1,
                                      width: context.screenWidth)
                                  : const SizedBox(),
                            )
                          ],
                        ),
                        // if (controller.errors.firstWhereOrNull(
                        //         (element) => element['title'] == "password") !=
                        //     null) ...[
                        //   toolTip(
                        //       tip: controller.errors.firstWhereOrNull(
                        //           (element) => element['title'] == "password"))
                        // ],
                        // if (controller.errors.firstWhereOrNull(
                        //         (e) => e['title'] == "password") !=
                        //     null)
                        //   toolTipPassword(
                        //     context,
                        //     controller
                        //         .getPasswordChecklist(), // create this method
                        //     'password',
                        //   ),

                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title:
                                    "${controller.labelTextDetail['confirm_password_label'] ?? "Confirm password"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(
                                title: "*",
                                fontFamily: regular,
                                context: context,
                                textColor: Colors.red),
                          ],
                        ),

                        5.heightBox,

                        TextFormField(
                          focusNode: controller.focusNodes[5.toString()],
                          controller: controller.confirmPasswordTextController,
                          onChanged: (value) {
                            // Only clear the confirm password error when the user has actually fixed it.
                            // No validation is triggered here; full validation happens on "Sign up".
                            final existingError = controller.errors
                                .firstWhereOrNull((element) =>
                                    element['title'] ==
                                    "password_confirmation");

                            if (existingError != null) {
                              final trimmed = value.trim();
                              final password =
                                  controller.passwordTextController.text;

                              final isNowValid =
                                  trimmed.isNotEmpty && trimmed == password;

                              // If the user has cleared the field OR it now matches the password,
                              // remove the existing error and let Sign Up re‑validate if needed.
                              if (trimmed.isEmpty || isNowValid) {
                                controller.errors.remove(existingError);
                              }
                            }
                          },
                          validator: (value) {
                            // Form validation when submitted
                            controller.validateConfirmPassword();
                            return null;
                          },
                          decoration: InputDecoration(
                            hintStyle: const TextStyle(fontSize: 18),
                            // hintText: "Confirm Password",
                            // hintText:
                            //     "${controller.labelTextDetail['confirm_password_placeholder'] ?? 'Confirm your password'}",
                            enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(5.0),
                                borderSide: BorderSide(
                                    color: controller.errors.firstWhereOrNull(
                                                (element) =>
                                                    element['title'] ==
                                                    "password_confirmation") !=
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
                                vertical: 0.0, horizontal: 8.0),
                            suffixIcon: IconButton(
                              icon: Icon(
                                controller.isConfirmPasswordVisible.value
                                    ? Icons.visibility_off
                                    : Icons.visibility,
                              ),
                              onPressed: () => controller
                                      .isConfirmPasswordVisible.value =
                                  !controller.isConfirmPasswordVisible.value,
                            ),
                          ),
                          style: const TextStyle(
                              fontSize: 16, fontFamily: regular),
                          obscureText:
                              !controller.isConfirmPasswordVisible.value,
                          keyboardType: TextInputType.visiblePassword,
                          textInputAction: TextInputAction.done,
                        ),
                        if (controller.errors.firstWhereOrNull((element) =>
                                element['title'] == "password_confirmation") !=
                            null) ...[
                          toolTip(
                              tip: controller.errors.firstWhereOrNull(
                                  (element) =>
                                      element['title'] ==
                                      "password_confirmation"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title:
                                    "${controller.labelTextDetail['referral_code_label'] ?? "Referral Code"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            5.widthBox,
                            txt16Size(
                                title: "(Optional)",
                                fontFamily: regular,
                                textColor: Colors.grey,
                                context: context),
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                          textController: controller.referralCodeTextController,
                          fieldType: "text",
                          readonly: false,
                          fontSize: 16.0,
                          fontFamily: regular,
                          onChanged: (value) {
                            // Clear any referral code errors when user types
                            if (controller.errors.firstWhereOrNull((element) =>
                                    element['title'] == "referral_code") !=
                                null) {
                              controller.errors.remove(controller.errors
                                  .firstWhereOrNull((element) =>
                                      element['title'] == "referral_code"));
                            }
                          },
                        ),
                        if (controller.errors.firstWhereOrNull((element) =>
                                element['title'] == "referral_code") !=
                            null) ...[
                          toolTip(
                              tip: controller.errors.firstWhereOrNull(
                                  (element) =>
                                      element['title'] == "referral_code"))
                        ],
                        10.heightBox,
                        if (controller.isPasswordFocused.value) ...[
                          const SizedBox(height: 10),
                          Container(
                            padding: const EdgeInsets.all(16.0),
                            decoration: BoxDecoration(
                              color: Colors.grey.shade50,
                              borderRadius: BorderRadius.circular(8.0),
                              border: Border.all(color: Colors.grey.shade300),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                txt20Size(
                                  title: "Password Requirements",
                                  fontFamily: bold,
                                  textColor: textColor,
                                  context: context,
                                ),
                                const SizedBox(height: 12),

                                // Progress bars container
                                Container(
                                  padding: const EdgeInsets.all(12.0),
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(6.0),
                                    border:
                                        Border.all(color: Colors.grey.shade200),
                                  ),
                                  child: Column(
                                    children: [
                                      // Overall progress bar
                                      Row(
                                        children: [
                                          txt18Size(
                                            title: "Overall Progress",
                                            fontFamily: regular,
                                            textColor: textColor,
                                            context: context,
                                          ),
                                          const Spacer(),
                                          txt16Size(
                                            title:
                                                "${(controller.getPasswordChecklist().length * 20).toInt()}%",
                                            fontFamily: bold,
                                            textColor: controller
                                                        .getPasswordChecklist()
                                                        .length ==
                                                    5
                                                ? Colors.green
                                                : primaryColor,
                                            context: context,
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 8),
                                      LinearProgressIndicator(
                                        value: controller
                                                .getPasswordChecklist()
                                                .length /
                                            5,
                                        backgroundColor: Colors.grey.shade300,
                                        valueColor:
                                            AlwaysStoppedAnimation<Color>(
                                          controller
                                                      .getPasswordChecklist()
                                                      .length ==
                                                  5
                                              ? Colors.green
                                              : primaryColor,
                                        ),
                                        minHeight: 6.0,
                                      ),
                                      const SizedBox(height: 16),

                                      // Individual requirement bars
                                      _buildRequirementRow(
                                        context,
                                        "At least 8 characters",
                                        controller
                                            .getPasswordChecklist()
                                            .contains("length"),
                                        Icons.text_fields,
                                      ),
                                      const SizedBox(height: 8),
                                      _buildRequirementRow(
                                        context,
                                        "One lowercase letter",
                                        controller
                                            .getPasswordChecklist()
                                            .contains("small"),
                                        Icons.text_format,
                                      ),
                                      const SizedBox(height: 8),
                                      _buildRequirementRow(
                                        context,
                                        "One uppercase letter",
                                        controller
                                            .getPasswordChecklist()
                                            .contains("capital"),
                                        Icons.format_size,
                                      ),
                                      const SizedBox(height: 8),
                                      _buildRequirementRow(
                                        context,
                                        "One number",
                                        controller
                                            .getPasswordChecklist()
                                            .contains("number"),
                                        Icons.numbers,
                                      ),
                                      const SizedBox(height: 8),
                                      _buildRequirementRow(
                                        context,
                                        "One special character",
                                        controller
                                            .getPasswordChecklist()
                                            .contains("special"),
                                        Icons.star,
                                      ),
                                    ],
                                  ),
                                ),

                                // Security strength indicator
                                const SizedBox(height: 12),
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 12.0, vertical: 8.0),
                                  decoration: BoxDecoration(
                                    color: _getStrengthColor(controller
                                            .getPasswordChecklist()
                                            .length)
                                        .withOpacity(0.1),
                                    borderRadius: BorderRadius.circular(20.0),
                                    border: Border.all(
                                      color: _getStrengthColor(controller
                                          .getPasswordChecklist()
                                          .length),
                                      width: 1.0,
                                    ),
                                  ),
                                  child: Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Icon(
                                        _getStrengthIcon(controller
                                            .getPasswordChecklist()
                                            .length),
                                        size: 16.0,
                                        color: _getStrengthColor(controller
                                            .getPasswordChecklist()
                                            .length),
                                      ),
                                      const SizedBox(width: 6),
                                      txt16Size(
                                        title: _getStrengthText(controller
                                            .getPasswordChecklist()
                                            .length),
                                        fontFamily: bold,
                                        textColor: _getStrengthColor(controller
                                            .getPasswordChecklist()
                                            .length),
                                        context: context,
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                        15.heightBox,
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Switch(
                              value: controller.switchValue.value,
                              onChanged: (value) {
                                controller.switchValue.value = value;
                              },
                              activeColor: Colors.white,
                              activeTrackColor: primaryColor,
                            ),
                            Expanded(
                              child: RichText(
                                  text: TextSpan(children: [
                                textSpan(
                                    context: context,
                                    textColor: textColor,
                                    fontFamily: regular,
                                    title:
                                        "${controller.labelTextDetail['app_agree_terms_part1_label'] ?? 'I have read and accepted the '}",
                                    textSize: 18.0),
                                textSpan(
                                  context: context,
                                  textColor: primaryColor,
                                  fontFamily: regular,
                                  title:
                                      " ${controller.labelTextDetail['app_agree_terms_link1_label'] ?? 'Terms and Conditions'},",
                                  textSize: 18.0,
                                  recognizer: TapGestureRecognizer()
                                    ..onTap = () {
                                      Get.toNamed('/term_condition');
                                    },
                                ),
                                textSpan(
                                  context: context,
                                  textColor: primaryColor,
                                  fontFamily: regular,
                                  title:
                                      " ${controller.labelTextDetail['app_agree_terms_link2_label'] ?? ' Terms of Use'}",
                                  textSize: 18.0,
                                  recognizer: TapGestureRecognizer()
                                    ..onTap = () {
                                      Get.toNamed('/term_of_use');
                                    },
                                ),
                                textSpan(
                                  context: context,
                                  textColor: Colors.black,
                                  fontFamily: regular,
                                  title:
                                      " ${controller.labelTextDetail['app_agree_terms_part2_label'] ?? ' and'}",
                                  textSize: 18.0,
                                  recognizer: TapGestureRecognizer()
                                    ..onTap = () {},
                                ),
                                textSpan(
                                  context: context,
                                  textColor: primaryColor,
                                  fontFamily: regular,
                                  title:
                                      " ${controller.labelTextDetail['app_agree_terms_link3_label'] ?? ' Privacy policy'},",
                                  textSize: 18.0,
                                  recognizer: TapGestureRecognizer()
                                    ..onTap = () {
                                      Get.toNamed('/privacy_policy');
                                    },
                                ),
                                textSpan(
                                    context: context,
                                    textColor: textColor,
                                    fontFamily: bold,
                                    title:
                                        " ${controller.labelTextDetail['app_agree_terms_part3_label'] ?? '  and I confirm that I am at least 18 years old'}",
                                    textSize: 18.0),
                              ])),
                            )
                          ],
                        ),
                        15.heightBox,
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Switch(
                              value: controller.rideshareDisclaimerValue.value,
                              onChanged: (value) {
                                controller.rideshareDisclaimerValue.value =
                                    value;
                              },
                              activeColor: Colors.white,
                              activeTrackColor: primaryColor,
                            ),
                            Expanded(
                              child: txt20Size(
                                  title:
                                      "Ride sharing cannot be used as a business dummy text, will be changed after you have provided",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                            )
                          ],
                        ),
                        if (controller.errors.firstWhereOrNull((element) =>
                                element['title'] == "rideshare_disclaimer") !=
                            null) ...[
                          toolTip(
                              tip: controller.errors.firstWhereOrNull(
                                  (element) =>
                                      element['title'] ==
                                      "rideshare_disclaimer"))
                        ],
                        20.heightBox,
                        SizedBox(
                          width: context.screenWidth,
                          height: 50,
                          child: elevatedButtonWidget(
                              textWidget: txt22Size(
                                  title:
                                      "${controller.labelTextDetail['button_label'] ?? "Sign up"}",
                                  fontFamily: regular,
                                  textColor: Colors.white,
                                  context: context),
                              onPressed:
                                  controller.switchValue.value == false ||
                                          controller.rideshareDisclaimerValue
                                                  .value ==
                                              false
                                      ? null
                                      : () async {
                                          await controller.signup(
                                              context, context.screenHeight);
                                        },
                              context: context,
                              btnRadius: 5.0),
                        ),
                        15.heightBox,
                        Center(
                          child: RichText(
                              text: TextSpan(children: [
                            textSpan(
                                context: context,
                                textColor: textColor,
                                fontFamily: bold,
                                title:
                                    "${controller.labelTextDetail['no_account_label'] ?? 'Already have an account? '}",
                                textSize: 18.0),
                            textSpan(
                              context: context,
                              textColor: primaryColor,
                              fontFamily: bold,
                              title:
                                  " ${controller.labelTextDetail['signin_link_label'] ?? 'Log in'}",
                              textSize: 18.0,
                              recognizer: TapGestureRecognizer()
                                ..onTap = () {
                                  Get.offAllNamed("/login");
                                },
                            ),
                            textSpan(
                                context: context,
                                textColor: textColor,
                                fontFamily: bold,
                                title:
                                    " ${controller.labelTextDetail['now_label'] ?? ' now'}",
                                textSize: 18.0),
                          ])),
                        ),
                        5.heightBox,
                        Align(
                          alignment: Alignment.center,
                          child: InkWell(
                            onTap: () {
                              languageBottomSheet(context.screenWidth,
                                  controller.serviceController,
                                  page: "signup");
                            },
                            child: Ink(
                                padding: const EdgeInsets.all(5.0),
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(50.0),
                                  child: networkCacheImageWidget(
                                      controller
                                          .serviceController.langIcon.value,
                                      BoxFit.cover,
                                      30.0,
                                      30.0),
                                )),
                          ),
                        ),
                        // Row(
                        //   mainAxisAlignment: MainAxisAlignment.center,
                        //   children: [
                        //     txt22Size(
                        //         title:
                        //             "${controller.labelTextDetail['language_label'] ?? "Language "}:",
                        //         fontFamily: regular,
                        //         textColor: textColor,
                        //         context: context),
                        //     5.widthBox,
                        //     InkWell(
                        //       onTap: () {
                        //         languageBottomSheet(context.screenWidth,
                        //             controller.serviceController,
                        //             page: "signup");
                        //       },
                        //       child: Ink(
                        //           padding: const EdgeInsets.all(5.0),
                        //           child: ClipRRect(
                        //             borderRadius: BorderRadius.circular(50.0),
                        //             child: networkCacheImageWidget(
                        //                 controller
                        //                     .serviceController.langIcon.value,
                        //                 BoxFit.cover,
                        //                 30.0,
                        //                 30.0),
                        //           )),
                        //     ),
                        //     5.widthBox,
                        //   ],
                        // )
                      ],
                    ),
                  ),
                ),
                if (controller.showOverly.value == true) ...[
                  overlayWidget(context)
                ]
              ],
            ),
          ),
        );
      }
    }));
  }

  // Helper method to build requirement rows
  Widget _buildRequirementRow(BuildContext context, String requirement,
      bool isCompleted, IconData icon) {
    return Row(
      children: [
        Container(
          width: 20.0,
          height: 20.0,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: isCompleted ? Colors.green : Colors.grey.shade300,
          ),
          child: Icon(
            isCompleted ? Icons.check : icon,
            size: 12.0,
            color: isCompleted ? Colors.white : Colors.grey.shade600,
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: txt14Size(
            title: requirement,
            fontFamily: regular,
            textColor: isCompleted ? Colors.green.shade700 : textColor,
            context: context,
          ),
        ),
        // Mini progress bar for each requirement
        Container(
          width: 40.0,
          height: 4.0,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(2.0),
            color: isCompleted ? Colors.green : Colors.grey.shade300,
          ),
        ),
      ],
    );
  }

  // Helper methods for security strength
  Color _getStrengthColor(int completedRequirements) {
    switch (completedRequirements) {
      case 0:
      case 1:
        return Colors.red;
      case 2:
      case 3:
        return Colors.orange;
      case 4:
        return Colors.blue;
      case 5:
        return Colors.green;
      default:
        return Colors.grey;
    }
  }

  IconData _getStrengthIcon(int completedRequirements) {
    switch (completedRequirements) {
      case 0:
      case 1:
        return Icons.security;
      case 2:
      case 3:
        return Icons.shield;
      case 4:
        return Icons.verified_user;
      case 5:
        return Icons.security;
      default:
        return Icons.help;
    }
  }

  String _getStrengthText(int completedRequirements) {
    switch (completedRequirements) {
      case 0:
      case 1:
        return "Very Weak";
      case 2:
        return "Weak";
      case 3:
        return "Fair";
      case 4:
        return "Good";
      case 5:
        return "Strong";
      default:
        return "Unknown";
    }
  }
}
