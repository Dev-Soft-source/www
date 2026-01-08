import 'dart:convert';

import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:flutter_web_auth/flutter_web_auth.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/login/LoginController.dart';
import 'package:proximaride_app/pages/widgets/language_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import 'package:proximaride_app/pages/widgets/other_login_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/tool_tip.dart';
import 'package:signin_with_linkedin/signin_with_linkedin.dart';
import 'package:http/http.dart' as http;
//import 'package:tiktok_login_flutter/tiktok_login_flutter.dart';

class LoginPage extends GetView<LoginController> {
  const LoginPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(LoginController());
    return Scaffold(
        body: Obx((){
      if(controller.isLoading.value == true){
        return Center(child: progressCircularWidget(context));
      }else{
        return Stack(
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
                            title: "${controller.labelTextDetail['continue_label'] ?? "Log in to your account"}",
                            fontFamily: bold,
                            textColor: primaryColor,
                            context: context)),
                    20.heightBox,
                    20.heightBox,
                    txt20Size(
                      title: "${controller.labelTextDetail['email_label'] ?? "E-mail address"}",
                      fontFamily: bold,
                      textColor: textColor,
                      context: context,
                    ),
                    5.heightBox,
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
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
                          focusNode: controller.focusNodes[1.toString()],
                        ),

                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "email") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "email"))
                        ],

                        10.heightBox,
                        txt20Size(
                            title: "${controller.labelTextDetail['password_label'] ?? "Password"}",
                            fontFamily: bold,
                            textColor: textColor,
                            context: context),
                        5.heightBox,
                        TextFormField(
                          controller: controller.passwordTextController,
                          onChanged: (value) {
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "password") != null)
                            {controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "password"));}
                          },
                          decoration: InputDecoration(
                            errorStyle: const TextStyle(color: primaryColor,fontSize: 16),
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
                          focusNode: controller.focusNodes[2.toString()],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "password") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "password"))
                        ],
                      ],
                    ),

                    20.heightBox,
                    SizedBox(
                      width: context.screenWidth,
                      height: 50,
                      child: elevatedButtonWidget(
                          textWidget: txt28Size(
                              title: "${controller.labelTextDetail['submit_button_label'] ?? "Log in"}",
                              fontFamily: regular,
                              textColor: Colors.white,
                              context: context),
                          onPressed: () async {
                            await controller.login();
                          },
                          context: context,
                          btnRadius: 5.0),
                    ),
                    5.heightBox,
                    Align(
                        alignment: Alignment.topRight,
                        child: InkWell(
                            onTap: () {
                              Get.toNamed("/forgot_password");
                            },
                            child: txt18Size(
                                title: "${controller.labelTextDetail['forgot_password_label'] ?? "Forgot your password?"}",
                                fontFamily: regular,
                                textColor: primaryColor,
                                context: context))),
                    10.heightBox,
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(
                            child: SizedBox(
                              height: 2,
                              width: 10,
                              child: Container(
                                color: primaryColor,
                              ),
                            )),
                        10.widthBox,
                        txt22Size(
                            title: "${controller.labelTextDetail['or_label'] ?? "or log in with"}",
                            fontFamily: regular,
                            textColor: textColor,
                            context: context),
                        10.widthBox,
                        Expanded(
                            child: SizedBox(
                              height: 2,
                              width: 10,
                              child: Container(
                                color: primaryColor,
                              ),
                            ))
                      ],
                    ),
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
                                clientSecret: '${dotenv.env['clientSecret']}',
                                redirectUrl: '$url/',
                                scope: ['openid', 'profile', 'email'],
                              );

                              SignInWithLinkedIn.signIn(
                                context,
                                config: linkedInConfig,
                                onGetAuthToken: (data) {
                                  // print('Auth token data: ${data.toJson()}');
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
                        otherLogInWidget(imagePath: instagramImage, context: context, onTap: () async{
                          print("dfdsfdf");

                          var clientId = "1187700026406651";
                          var redirectUri =  "$url/";
                          var clientSecret = "ea9bdba920e7fef56ce58a2fb3036f36";



                          // Step 1: Define the Instagram login URL with OAuth
                          final String authUrl =
                              "https://api.instagram.com/oauth/authorize?client_id=$clientId&redirect_uri=$redirectUri&scope=user_profile&response_type=code";

                          // Step 2: Open the URL for Instagram login
                          final result = await FlutterWebAuth.authenticate(
                            url: authUrl,
                            callbackUrlScheme: "yourapp", // use your redirect URI scheme
                          );

                          // Step 3: Extract the code from the callback URL
                          final Uri responseUri = Uri.parse(result);
                          final String? code = responseUri.queryParameters['code'];

                          if (code != null) {
                            // Step 4: Exchange the authorization code for an access token
                            final response = await http.post(
                              Uri.parse('https://api.instagram.com/oauth/access_token'),
                              body: {
                                'client_id': clientId,
                                'client_secret': clientSecret,
                                'grant_type': 'authorization_code',
                                'redirect_uri': redirectUri,
                                'code': code,
                              },
                            );

                            if (response.statusCode == 200) {
                              // Parse the access token
                              final Map<String, dynamic> data = jsonDecode(response.body);
                              String accessToken = data['access_token'];

                              // Do something with the access token (e.g., fetch user profile)
                              print('Access Token: $accessToken');
                            } else {
                              print('Error during token exchange: ${response.body}');
                            }
                          } else {
                            print('Authorization code not found');
                          }


                        }),
                        //otherLogInWidget(imagePath: snapChatImage, context: context, onTap: (){}),
                        // otherLogInWidget(imagePath: tikTokImage, context: context, onTap: () async{
                        //   var code = await TiktokLoginFlutter.authorize(
                        //       "user.info.basic,user.info.profile");
                        //
                        //   controller.getTikTokUserInfo(code);
                        // }),
                      ],
                    ),
                    15.heightBox,
                    Center(
                      child: RichText(
                          text: TextSpan(children: [
                            textSpan(
                                context: context,
                                textColor: textColor,
                                fontFamily: bold,
                                title: "${controller.labelTextDetail['no_account_label'] ?? "Don't have any account yet?"}",
                                textSize: 22.0),
                            textSpan(
                              context: context,
                              textColor: primaryColor,
                              fontFamily: bold,
                              title: " ${controller.labelTextDetail['signup_link_label'] ?? " Sign up"}",
                              textSize: 22.0,
                              recognizer: TapGestureRecognizer()
                                ..onTap = () {
                                  Get.offAllNamed("/signup");
                                },
                            ),
                            textSpan(
                                context: context,
                                textColor: textColor,
                                fontFamily: bold,
                                title: " ${controller.labelTextDetail['now_label'] ?? " now"}",
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
                            languageBottomSheet(context.screenWidth, controller.serviceController, page: "login");
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
        );
      }
    })
    );
  }
}
