import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/signup/RegisterController.dart';
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
    return Scaffold(
        body: Obx((){
          if(controller.isLoading.value == true) {
            return Center(child: progressCircularWidget(context));
          }else{
            return GestureDetector(
              onTap: (){
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
                                  title: "${controller.labelTextDetail['app_main_heading'] ?? "Create a new account with"}",
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
                                    final _linkedInConfig = LinkedInConfig(
                                      clientId: '${dotenv.env['clientId']}',
                                      clientSecret: '${dotenv.env['clientSecret']}',
                                      redirectUrl: '$url/',
                                      scope: ['openid', 'profile', 'email'],
                                    );
                                    SignInWithLinkedIn.signIn(
                                      context,
                                      config: _linkedInConfig,
                                      onGetAuthToken: (data) {
                                        controller.getUserInfo(data.toJson());
                                      },
                                      onSignInError: (error) {
                                      },
                                    );
                                  }),
                              5.widthBox,
                              otherLogInWidget(
                                  imagePath: googleImage,
                                  context: context,
                                  onTap: () async {
                                    await controller.signInWithGoogle();
                                  }),
                              5.widthBox,
                              otherLogInWidget(
                                  imagePath: instagramImage,
                                  context: context,
                                  onTap: () {
                                  }),
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
                          txt20Size(title: "${controller.labelTextDetail['required_label'] ?? "* Indicates required field"}", fontFamily: regular, context: context,textColor: Colors.red),
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['first_name_label'] ?? "First name"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context
                              ),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          fieldsWidget(
                            textController: controller.firstNameTextController,
                            fieldType: "text",
                            readonly: false,
                            fontSize: 16.0,
                            fontFamily: regular,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "first_name") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "first_name"));
                              }
                            },
                            isError: controller.errors.firstWhereOrNull((element) => element['title'] == "first_name") != null,
                            focusNode: controller.focusNodes[1.toString()],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "first_name") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "first_name"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['last_name_label'] ?? "Last name"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          fieldsWidget(
                            textController: controller.lastNameTextController,
                            fieldType: "text",
                            readonly: false,
                            fontSize: 16.0,
                            fontFamily: regular,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "last_name") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "last_name"));
                              }
                            },
                            isError: controller.errors.firstWhereOrNull((element) => element['title'] == "last_name") != null,
                            focusNode: controller.focusNodes[2.toString()],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "last_name") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "last_name"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['email_label'] ?? "E-mail address"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          fieldsWidget(
                            textController: controller.emailTextController,
                            fieldType: "email",
                            readonly: false,
                            fontSize: 16.0,
                            fontFamily: regular,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "email") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "email"));
                              }
                            },
                            isError: controller.errors.firstWhereOrNull((element) => element['title'] == "email") != null,
                            focusNode: controller.focusNodes[3.toString()],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "email") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "email"))
                          ],
                          10.heightBox,
                          Stack(
                            children: [
                              Column(
                                children: [
                                  Row(
                                    children: [
                                      txt20Size(
                                          title: "${controller.labelTextDetail['password_label'] ?? "Password"}",
                                          fontFamily: regular,
                                          textColor: textColor,
                                          context: context),
                                      txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                                      5.widthBox,
                                      InkWell(
                                        onTap: () {
                                          controller.showToolTip.value = !controller.showToolTip.value;
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
                                            mobile: 25.0,
                                            tablet: 25.0,
                                          ),
                                        ),
                                      )
                                    ],
                                  ),
                                  5.heightBox,
                                  TextFormField(
                                    focusNode: controller.focusNodes[4.toString()],
                                    controller: controller.passwordTextController,
                                    onChanged: (value) {
                                      if(controller.errors.firstWhereOrNull((element) => element['title'] == "password") != null)
                                      {
                                        controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "password"));
                                      }
                                    },
                                    maxLines: 1,
                                    decoration: InputDecoration(
                                      hintStyle: const TextStyle(
                                          fontSize: 10,
                                          fontFamily: regular,
                                          color: textColor),

                                      enabledBorder: OutlineInputBorder(
                                          borderRadius: BorderRadius.circular(5.0),
                                          borderSide: BorderSide(
                                              color: controller.errors.firstWhereOrNull((element) => element['title'] == "password") != null
                                                  ? primaryColor : Colors.grey.shade400,
                                              style: BorderStyle.solid,
                                              width: 1)),
                                      focusedBorder: OutlineInputBorder(
                                          borderRadius: BorderRadius.circular(5.0),
                                          borderSide: const BorderSide(color: primaryColor)),
                                      filled: true,

                                      fillColor: inputColor,
                                      contentPadding: const EdgeInsets.symmetric(
                                          vertical: 0.0, horizontal: 8.0),
                                      suffixIcon: IconButton(
                                        icon: Icon(
                                          controller.isPasswordVisible.value
                                              ? Icons.visibility_off
                                              : Icons.visibility,
                                        ),
                                        onPressed: () => controller.isPasswordVisible.value =
                                        !controller.isPasswordVisible.value,
                                      ),
                                    ),
                                    style: const TextStyle(
                                        fontSize: 16, fontFamily: regular, color: textColor),
                                    obscureText: !controller.isPasswordVisible.value,
                                    keyboardType: TextInputType.visiblePassword,
                                    textInputAction: TextInputAction.next,
                                  ),
                                  controller.showToolTip.value ? 45.heightBox : const SizedBox(),
                                ],
                              ),
                              Positioned(
                                top: 30.0,
                                left: 0,
                                right: 0,
                                bottom: 0,
                                child: controller.showToolTip.value ? toolTip(tip: "${controller.labelTextDetail['password_placeholder'] ?? 'The password length should be at least 8 characters and must include one lower case, one uppercase, one number, and one special character'}",type: 'text',position: 1,width: context.screenWidth) : const SizedBox(),
                              )

                            ],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "password") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "password"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['confirm_password_label'] ?? "Confirm password"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          TextFormField(
                            focusNode: controller.focusNodes[5.toString()],
                            controller: controller.confirmPasswordTextController,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "password_confirmation") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "password_confirmation"));
                              }
                            },
                            decoration: InputDecoration(
                              enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(5.0),
                                  borderSide: BorderSide(
                                      color: controller.errors.firstWhereOrNull((element) => element['title'] == "password_confirmation") != null
                                          ? primaryColor : Colors.grey.shade400,
                                      style: BorderStyle.solid,
                                      width: 1)),
                              focusedBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(5.0),
                                  borderSide: const BorderSide(color: primaryColor)),
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
                                onPressed: () =>
                                controller.isConfirmPasswordVisible.value =
                                !controller.isConfirmPasswordVisible.value,
                              ),
                            ),
                            style: const TextStyle(fontSize: 16, fontFamily: regular),
                            obscureText: !controller.isConfirmPasswordVisible.value,
                            keyboardType: TextInputType.visiblePassword,
                            textInputAction: TextInputAction.done,
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "password_confirmation") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "password_confirmation"))
                          ],
                          10.heightBox,
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
                                          title: "${controller.labelTextDetail['app_agree_terms_part1_label'] ?? 'I have read and accepted the '}",
                                          textSize: 18.0),
                                      textSpan(
                                        context: context,
                                        textColor: primaryColor,
                                        fontFamily: regular,
                                        title: " ${controller.labelTextDetail['app_agree_terms_link1_label'] ?? 'Terms and Conditions'},",
                                        textSize: 18.0,
                                        recognizer: TapGestureRecognizer()..onTap = () {
                                          Get.toNamed('/term_condition');
                                        },
                                      ),
                                      textSpan(
                                        context: context,
                                        textColor: primaryColor,
                                        fontFamily: regular,
                                        title: " ${controller.labelTextDetail['app_agree_terms_link2_label'] ?? ' Terms of Use'}",
                                        textSize: 18.0,
                                        recognizer: TapGestureRecognizer()..onTap = () {
                                          Get.toNamed('/term_of_use');
                                        },
                                      ),
                                      textSpan(
                                        context: context,
                                        textColor: Colors.black,
                                        fontFamily: regular,
                                        title: " ${controller.labelTextDetail['app_agree_terms_part2_label'] ?? ' and'}",
                                        textSize: 18.0,
                                        recognizer: TapGestureRecognizer()..onTap = () {},
                                      ),
                                      textSpan(
                                        context: context,
                                        textColor: primaryColor,
                                        fontFamily: regular,
                                        title: " ${controller.labelTextDetail['app_agree_terms_link3_label'] ?? ' Privacy policy'},",
                                        textSize: 18.0,
                                        recognizer: TapGestureRecognizer()..onTap = () { Get.toNamed('/privacy_policy');},
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
                          20.heightBox,
                          SizedBox(
                            width: context.screenWidth,
                            height: 60,
                            child: elevatedButtonWidget(
                                textWidget: txt28Size(
                                    title: "${controller.labelTextDetail['button_label'] ?? "Sign up"}",
                                    fontFamily: regular,
                                    textColor: Colors.white,
                                    context: context),
                                onPressed: controller.switchValue.value == false
                                    ? null
                                    : () async {
                                  await controller.signup(context, context.screenHeight);
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
                                      title: "${controller.labelTextDetail['no_account_label'] ?? 'Already have an account? '}",
                                      textSize: 22.0),
                                  textSpan(
                                    context: context,
                                    textColor: primaryColor,
                                    fontFamily: bold,
                                    title: " ${controller.labelTextDetail['signin_link_label'] ?? 'Log in'}",
                                    textSize: 22.0,
                                    recognizer: TapGestureRecognizer()
                                      ..onTap = () {
                                        Get.offAllNamed("/login");
                                      },
                                  ),
                                  textSpan(
                                      context: context,
                                      textColor: textColor,
                                      fontFamily: bold,
                                      title: " ${controller.labelTextDetail['now_label'] ?? ' now'}",
                                      textSize: 22.0),
                                ])),
                          ),
                          5.heightBox,
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              txt22Size(title: "${controller.labelTextDetail['language_label'] ?? "Language "}:", fontFamily: regular, textColor: textColor, context: context),
                              5.widthBox,
                              InkWell(
                                onTap: (){
                                  languageBottomSheet(context.screenWidth, controller.serviceController, page: "signup");
                                },
                                child: Ink(
                                    padding: const EdgeInsets.all(5.0),
                                    child: ClipRRect(
                                      borderRadius: BorderRadius.circular(50.0),
                                      child: networkCacheImageWidget(controller.serviceController.langIcon.value, BoxFit.cover, 30.0, 30.0),
                                    )
                                ),
                              ),
                              5.widthBox,
                            ],
                          )
                        ],
                      ),
                    ),
                  ),
                  if (controller.showOverly.value == true) ...[overlayWidget(context)]
                ],
              ),
            );
          }
        })
    );
  }
}
