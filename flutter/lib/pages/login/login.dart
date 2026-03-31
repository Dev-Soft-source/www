// import 'package:flutter/gestures.dart';
// import 'package:flutter/material.dart';
// import 'package:flutter_dotenv/flutter_dotenv.dart';
// // import 'package:flutter_web_auth/flutter_web_auth.dart';
// import 'package:proximaride_app/consts/constFileLink.dart';
// import 'package:proximaride_app/consts/const_api.dart';
// import 'package:proximaride_app/pages/login/LoginController.dart';
// import 'package:proximaride_app/pages/widgets/language_bottom_sheet.dart';
// import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
// import 'package:proximaride_app/pages/widgets/other_login_widget.dart';
// import 'package:proximaride_app/pages/widgets/button_Widget.dart';
// import 'package:proximaride_app/pages/widgets/fields_widget.dart';
// import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
// import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
// import 'package:proximaride_app/pages/widgets/textWidget.dart';
// import 'package:proximaride_app/pages/widgets/tool_tip.dart';
// import 'package:signin_with_linkedin/signin_with_linkedin.dart';
// //import 'package:tiktok_login_flutter/tiktok_login_flutter.dart';

// class LoginPage extends GetView<LoginController> {
//   const LoginPage({super.key});

//   @override
//   Widget build(BuildContext context) {
//     Get.put(LoginController());
//     return Scaffold(body: Obx(() {
//       if (controller.isLoading.value == true) {
//         return Center(child: progressCircularWidget(context));
//       } else {
//         return Stack(
//           children: [
//             Container(
//               padding: EdgeInsets.all(getValueForScreenType<double>(
//                 context: context,
//                 mobile: 15.0,
//                 tablet: 15.0,
//               )),
//               child: SingleChildScrollView(
//                 child: Column(
//                   crossAxisAlignment: CrossAxisAlignment.start,
//                   mainAxisAlignment: MainAxisAlignment.start,
//                   children: [
//                     65.heightBox,
//                     Center(
//                       child: Image.asset(
//                         logoImage,
//                         width: getValueForScreenType<double>(
//                           context: context,
//                           mobile: 164.0,
//                           tablet: 164.0,
//                         ),
//                         height: getValueForScreenType<double>(
//                           context: context,
//                           mobile: 106.0,
//                           tablet: 106.0,
//                         ),
//                       ),
//                     ),
//                     20.heightBox,
//                     Center(
//                         child: txt25Size(
//                             title:
//                                 "${controller.labelTextDetail['continue_label'] ?? "Log in to your account"}",
//                             fontFamily: bold,
//                             textColor: primaryColor,
//                             context: context)),
//                     20.heightBox,
//                     20.heightBox,
//                     txt20Size(
//                       title:
//                           "${controller.labelTextDetail['email_label'] ?? "E-mail address"}",
//                       fontFamily: bold,
//                       textColor: textColor,
//                       context: context,
//                     ),
//                     5.heightBox,
//                     // Column(
//                     //   crossAxisAlignment: CrossAxisAlignment.start,
//                     //   children: [
//                     //     fieldsWidget(
//                     //       textController: controller.emailTextController,
//                     //       fieldType: "email",
//                     //       readonly: false,
//                     //       fontSize: 16.0,
//                     //       fontFamily: regular,
//                     //       onChanged: (value) {
//                     //         if (controller.errors.firstWhereOrNull(
//                     //                 (element) => element['title'] == "email") !=
//                     //             null) {
//                     //           controller.errors.remove(controller.errors
//                     //               .firstWhereOrNull((element) =>
//                     //                   element['title'] == "email"));
//                     //         }
//                     //       },
//                     //       isError: controller.errors.firstWhereOrNull(
//                     //               (element) => element['title'] == "email") !=
//                     //           null,
//                     //       focusNode: controller.focusNodes[1.toString()],
//                     //     ),
//                     //     if (controller.errors.firstWhereOrNull(
//                     //             (element) => element['title'] == "email") !=
//                     //         null) ...[
//                     //       toolTip(
//                     //           tip: controller.errors.firstWhereOrNull(
//                     //               (element) => element['title'] == "email"))
//                     //     ],
//                     //     10.heightBox,
//                     //     txt20Size(
//                     //         title:
//                     //             "${controller.labelTextDetail['password_label'] ?? "Password"}",
//                     //         fontFamily: bold,
//                     //         textColor: textColor,
//                     //         context: context),
//                     //     5.heightBox,
//                     //     TextFormField(
//                     //       controller: controller.passwordTextController,
//                     //       onChanged: (value) {
//                     //         if (controller.errors.firstWhereOrNull((element) =>
//                     //                 element['title'] == "password") !=
//                     //             null) {
//                     //           controller.errors.remove(controller.errors
//                     //               .firstWhereOrNull((element) =>
//                     //                   element['title'] == "password"));
//                     //         }
//                     //       },
//                     //       decoration: InputDecoration(
//                     //         errorStyle: const TextStyle(
//                     //             color: primaryColor, fontSize: 16),
//                     //         enabledBorder: OutlineInputBorder(
//                     //             borderRadius: BorderRadius.circular(5.0),
//                     //             borderSide: BorderSide(
//                     //                 color: controller.errors.firstWhereOrNull(
//                     //                             (element) =>
//                     //                                 element['title'] ==
//                     //                                 "password") !=
//                     //                         null
//                     //                     ? primaryColor
//                     //                     : Colors.grey.shade400,
//                     //                 style: BorderStyle.solid,
//                     //                 width: 1)),
//                     //         focusedBorder: OutlineInputBorder(
//                     //             borderRadius: BorderRadius.circular(5.0),
//                     //             borderSide:
//                     //                 const BorderSide(color: primaryColor)),
//                     //         filled: true,
//                     //         fillColor: inputColor,
//                     //         contentPadding: const EdgeInsets.symmetric(
//                     //             vertical: 0.0, horizontal: 8.0),
//                     //         suffixIcon: IconButton(
//                     //           icon: Icon(
//                     //             controller.isPasswordVisible.value
//                     //                 ? Icons.visibility_off
//                     //                 : Icons.visibility,
//                     //           ),
//                     //           onPressed: () => controller.isPasswordVisible
//                     //               .value = !controller.isPasswordVisible.value,
//                     //         ),
//                     //       ),
//                     //       style: const TextStyle(
//                     //           fontSize: 16,
//                     //           fontFamily: regular,
//                     //           color: textColor),
//                     //       obscureText: !controller.isPasswordVisible.value,
//                     //       keyboardType: TextInputType.visiblePassword,
//                     //       focusNode: controller.focusNodes[2.toString()],
//                     //     ),
//                     //     if (controller.errors.firstWhereOrNull(
//                     //             (element) => element['title'] == "password") !=
//                     //         null) ...[
//                     //       toolTip(
//                     //           tip: controller.errors.firstWhereOrNull(
//                     //               (element) => element['title'] == "password"))
//                     //     ],
//                     //   ],
//                     // ),
//                     Column(
//                       crossAxisAlignment: CrossAxisAlignment.start,
//                       children: [
//                         // Email Field
//                         fieldsWidget(
//                           textController: controller.emailTextController,
//                           fieldType: "email",
//                           readonly: false,
//                           fontSize: 16.0,
//                           fontFamily: regular,
//                           onChanged: (value) {
//                             // Clear email error when user starts typing
//                             controller.errors.removeWhere(
//                                 (element) => element['title'] == "email");

//                             // Only validate if field is not empty (to avoid showing errors while typing)
//                             if (value.isNotEmpty) {
//                               controller.validateEmail();
//                             }
//                           },
//                           // isError: controller.errors.firstWhereOrNull(
//                           //         (element) => element['title'] == "email") !=
//                           //     null,
//                           // focusNode: controller.focusNodes[1.toString()],
//                         ),

//                         if (controller.errors.firstWhereOrNull(
//                                 (element) => element['title'] == "email") !=
//                             null) ...[
//                           toolTip(
//                               tip: controller.errors.firstWhereOrNull(
//                                   (element) => element['title'] == "email"))
//                         ],

//                         10.heightBox,

//                         // Password Label
//                         txt20Size(
//                             title:
//                                 "${controller.labelTextDetail['password_label'] ?? "Password"}",
//                             fontFamily: bold,
//                             textColor: textColor,
//                             context: context),

//                         5.heightBox,

//                         // Password Field
//                         // TextFormField(
//                         //   controller: controller.passwordTextController,
//                         //   onChanged: (value) {
//                         //     // Clear password error when user starts typing
//                         //     controller.errors.removeWhere(
//                         //         (element) => element['title'] == "password");

//                         //     // Only validate if field is not empty (to avoid showing errors while typing)
//                         //     if (value.isNotEmpty) {
//                         //       controller.validatePassword();
//                         //     }
//                         //   },
//                         //   validator: (value) {
//                         //     // Form validation when submitted
//                         //     controller.validatePassword();
//                         //     return null;
//                         //   },
//                         //   decoration: InputDecoration(
//                         //     errorStyle: const TextStyle(
//                         //         color: primaryColor, fontSize: 16),
//                         //     enabledBorder: OutlineInputBorder(
//                         //         borderRadius: BorderRadius.circular(5.0),
//                         //         borderSide: BorderSide(
//                         //             color: controller.errors.firstWhereOrNull(
//                         //                         (element) =>
//                         //                             element['title'] ==
//                         //                             "password") !=
//                         //                     null
//                         //                 ? primaryColor
//                         //                 : Colors.grey.shade400,
//                         //             style: BorderStyle.solid,
//                         //             width: 1)),
//                         //     focusedBorder: OutlineInputBorder(
//                         //         borderRadius: BorderRadius.circular(5.0),
//                         //         borderSide:
//                         //             const BorderSide(color: primaryColor)),
//                         //     filled: true,
//                         //     fillColor: inputColor,
//                         //     contentPadding: const EdgeInsets.symmetric(
//                         //         vertical: 0.0, horizontal: 8.0),
//                         //     suffixIcon: IconButton(
//                         //       icon: Icon(
//                         //         controller.isPasswordVisible.value
//                         //             ? Icons.visibility_off
//                         //             : Icons.visibility,
//                         //       ),
//                         //       onPressed: () => controller.isPasswordVisible
//                         //           .value = !controller.isPasswordVisible.value,
//                         //     ),
//                         //   ),
//                         //   style: const TextStyle(
//                         //       fontSize: 16,
//                         //       fontFamily: regular,
//                         //       color: textColor),
//                         //   obscureText: !controller.isPasswordVisible.value,
//                         //   keyboardType: TextInputType.visiblePassword,
//                         //   // focusNode: controller.focusNodes[2.toString()],
//                         // ),

//                         // // Password Error Display
//                         // // if (controller.errors.firstWhereOrNull(
//                         // //         (element) => element['title'] == "password") !=
//                         // //     null) ...[
//                         // //   toolTipPassword(
//                         // //       tip: controller.errors.firstWhereOrNull(
//                         // //           (element) => element['title'] == "password"))
//                         // // ],
//                         // if (controller.errors.firstWhereOrNull(
//                         //         (e) => e['title'] == "password") !=
//                         //     null)
//                         //   toolTipPassword(
//                         //     context,
//                         //     controller
//                         //         .getPasswordChecklist(), // create this method
//                         //     'password',
//                         //   ),

//                         TextFormField(
//                           controller: controller.passwordTextController,
//                           onChanged: (value) {
//                             // Clear password error when user starts typing
//                             controller.errors.removeWhere(
//                                 (element) => element['title'] == "password");

//                             // Trigger validation update for real-time progress bars
//                             controller.validatePassword();
//                           },
//                           validator: (value) {
//                             // Form validation when submitted
//                             controller.validatePassword();
//                             return null;
//                           },
//                           decoration: InputDecoration(
//                             errorStyle: const TextStyle(
//                                 color: primaryColor, fontSize: 16),
//                             enabledBorder: OutlineInputBorder(
//                                 borderRadius: BorderRadius.circular(5.0),
//                                 borderSide: BorderSide(
//                                     color: controller.errors.firstWhereOrNull(
//                                                 (element) =>
//                                                     element['title'] ==
//                                                     "password") !=
//                                             null
//                                         ? primaryColor
//                                         : Colors.grey.shade400,
//                                     style: BorderStyle.solid,
//                                     width: 1)),
//                             focusedBorder: OutlineInputBorder(
//                                 borderRadius: BorderRadius.circular(5.0),
//                                 borderSide:
//                                     const BorderSide(color: primaryColor)),
//                             filled: true,
//                             fillColor: inputColor,
//                             contentPadding: const EdgeInsets.symmetric(
//                                 vertical: 0.0, horizontal: 8.0),
//                             suffixIcon: IconButton(
//                               icon: Icon(
//                                 controller.isPasswordVisible.value
//                                     ? Icons.visibility_off
//                                     : Icons.visibility,
//                               ),
//                               onPressed: () => controller.isPasswordVisible
//                                   .value = !controller.isPasswordVisible.value,
//                             ),
//                           ),
//                           style: const TextStyle(
//                               fontSize: 16,
//                               fontFamily: regular,
//                               color: textColor),
//                           obscureText: !controller.isPasswordVisible.value,
//                           keyboardType: TextInputType.visiblePassword,
//                         ),

//                       ],
//                     ),
//                     if (controller.passwordTextController.text.isNotEmpty) ...[
//   10.heightBox,
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
//         txt18Size(
//           title: "Password Requirements",
//           fontFamily: bold,
//           textColor: textColor,
//           context: context,
//         ),
//         12.heightBox,

//         // Progress bars container
//         Container(
//           padding: const EdgeInsets.all(12.0),
//           decoration: BoxDecoration(
//             color: Colors.white,
//             borderRadius: BorderRadius.circular(6.0),
//             border: Border.all(color: Colors.grey.shade200),
//           ),
//           child: Column(
//             children: [
//               // Overall progress bar
//               Row(
//                 children: [
//                   txt16Size(
//                     title: "Overall Progress",
//                     fontFamily: regular,
//                     textColor: textColor,
//                     context: context,
//                   ),
//                   const Spacer(),
//                   txt14Size(
//                     title: "${(controller.getPasswordChecklist().length * 20).toInt()}%",
//                     fontFamily: bold,
//                     textColor: controller.getPasswordChecklist().length == 5
//                         ? Colors.green
//                         : primaryColor,
//                     context: context,
//                   ),
//                 ],
//               ),
//               8.heightBox,
//               LinearProgressIndicator(
//                 value: controller.getPasswordChecklist().length / 5,
//                 backgroundColor: Colors.grey.shade300,
//                 valueColor: AlwaysStoppedAnimation<Color>(
//                   controller.getPasswordChecklist().length == 5
//                       ? Colors.green
//                       : primaryColor,
//                 ),
//                 minHeight: 6.0,
//               ),
//               16.heightBox,

//               // Individual requirement bars
//               _buildRequirementRow(
//                 context,
//                 "At least 8 characters",
//                 controller.getPasswordChecklist().contains("length"),
//                 Icons.text_fields,
//               ),
//               8.heightBox,
//               _buildRequirementRow(
//                 context,
//                 "One lowercase letter",
//                 controller.getPasswordChecklist().contains("small"),
//                 Icons.text_format,
//               ),
//               8.heightBox,
//               _buildRequirementRow(
//                 context,
//                 "One uppercase letter",
//                 controller.getPasswordChecklist().contains("capital"),
//                 Icons.format_size,
//               ),
//               8.heightBox,
//               _buildRequirementRow(
//                 context,
//                 "One number",
//                 controller.getPasswordChecklist().contains("number"),
//                 Icons.numbers,
//               ),
//               8.heightBox,
//               _buildRequirementRow(
//                 context,
//                 "One special character",
//                 controller.getPasswordChecklist().contains("special"),
//                 Icons.star,
//               ),
//             ],
//           ),
//         ),

//         // Security strength indicator
//         12.heightBox,
//         Container(
//           padding: const EdgeInsets.symmetric(horizontal: 12.0, vertical: 8.0),
//           decoration: BoxDecoration(
//             color: _getStrengthColor(controller.getPasswordChecklist().length).withOpacity(0.1),
//             borderRadius: BorderRadius.circular(20.0),
//             border: Border.all(
//               color: _getStrengthColor(controller.getPasswordChecklist().length),
//               width: 1.0,
//             ),
//           ),
//           child: Row(
//             mainAxisSize: MainAxisSize.min,
//             children: [
//               Icon(
//                 _getStrengthIcon(controller.getPasswordChecklist().length),
//                 size: 16.0,
//                 color: _getStrengthColor(controller.getPasswordChecklist().length),
//               ),
//               6.widthBox,
//               txt14Size(
//                 title: _getStrengthText(controller.getPasswordChecklist().length),
//                 fontFamily: bold,
//                 textColor: _getStrengthColor(controller.getPasswordChecklist().length),
//                 context: context,
//               ),
//             ],
//           ),
//         ),
//       ],
//     ),
//   ),
// ],

// // Helper method to build requirement rows - Add this as a separate method
// Widget _buildRequirementRow(BuildContext context, String requirement, bool isCompleted, IconData icon) {
//   return Row(
//     children: [
//       Container(
//         width: 20.0,
//         height: 20.0,
//         decoration: BoxDecoration(
//           shape: BoxShape.circle,
//           color: isCompleted ? Colors.green : Colors.grey.shade300,
//         ),
//         child: Icon(
//           isCompleted ? Icons.check : icon,
//           size: 12.0,
//           color: isCompleted ? Colors.white : Colors.grey.shade600,
//         ),
//       ),
//       12.widthBox,
//       Expanded(
//         child: txt14Size(
//           title: requirement,
//           fontFamily: regular,
//           textColor: isCompleted ? Colors.green.shade700 : textColor,
//           context: context,
//         ),
//       ),
//       // Mini progress bar for each requirement
//       Container(
//         width: 40.0,
//         height: 4.0,
//         decoration: BoxDecoration(
//           borderRadius: BorderRadius.circular(2.0),
//           color: isCompleted ? Colors.green : Colors.grey.shade300,
//         ),
//       ),
//     ],
//   );
// }

// // Helper methods for security strength - Add these as separate methods
// Color _getStrengthColor(int completedRequirements) {
//   switch (completedRequirements) {
//     case 0:
//     case 1:
//       return Colors.red;
//     case 2:
//     case 3:
//       return Colors.orange;
//     case 4:
//       return Colors.blue;
//     case 5:
//       return Colors.green;
//     default:
//       return Colors.grey;
//   }
// }

// IconData _getStrengthIcon(int completedRequirements) {
//   switch (completedRequirements) {
//     case 0:
//     case 1:
//       return Icons.security;
//     case 2:
//     case 3:
//       return Icons.shield;
//     case 4:
//       return Icons.verified_user;
//     case 5:
//       return Icons.security;
//     default:
//       return Icons.help;
//   }
// }

// String _getStrengthText(int completedRequirements) {
//   switch (completedRequirements) {
//     case 0:
//     case 1:
//       return "Very Weak";
//     case 2:
//       return "Weak";
//     case 3:
//       return "Fair";
//     case 4:
//       return "Good";
//     case 5:
//       return "Strong";
//     default:
//       return "Unknown";
//   }
// }
//                     20.heightBox,
//                     SizedBox(
//                       width: context.screenWidth,
//                       height: 50,
//                       child: elevatedButtonWidget(
//                           textWidget: txt28Size(
//                               title:
//                                   "${controller.labelTextDetail['submit_button_label'] ?? "Log in"}",
//                               fontFamily: regular,
//                               textColor: Colors.white,
//                               context: context),
//                           onPressed: () async {
//                             await controller.login();
//                           },
//                           context: context,
//                           btnRadius: 5.0),
//                     ),
//                     5.heightBox,
//                     Align(
//                         alignment: Alignment.topRight,
//                         child: InkWell(
//                             onTap: () {
//                               Get.toNamed("/forgot_password");
//                             },
//                             child: txt18Size(
//                                 title:
//                                     "${controller.labelTextDetail['forgot_password_label'] ?? "Forgot your password?"}",
//                                 fontFamily: regular,
//                                 textColor: primaryColor,
//                                 context: context))),
//                     10.heightBox,
//                     Row(
//                       mainAxisAlignment: MainAxisAlignment.spaceBetween,
//                       children: [
//                         Expanded(
//                             child: SizedBox(
//                           height: 2,
//                           width: 10,
//                           child: Container(
//                             color: primaryColor,
//                           ),
//                         )),
//                         10.widthBox,
//                         txt22Size(
//                             title:
//                                 "${controller.labelTextDetail['or_label'] ?? "or log in with"}",
//                             fontFamily: regular,
//                             textColor: textColor,
//                             context: context),
//                         10.widthBox,
//                         Expanded(
//                             child: SizedBox(
//                           height: 2,
//                           width: 10,
//                           child: Container(
//                             color: primaryColor,
//                           ),
//                         ))
//                       ],
//                     ),
//                     10.heightBox,
//                     Row(
//                       mainAxisAlignment: MainAxisAlignment.center,
//                       children: [
//                         otherLogInWidget(
//                             imagePath: facebookImage,
//                             context: context,
//                             onTap: () async {
//                               await controller.signInWithFacebook();
//                             }),
//                         5.widthBox,
//                         otherLogInWidget(
//                             imagePath: linkedInImage,
//                             context: context,
//                             onTap: () async {
//                               final linkedInConfig = LinkedInConfig(
//                                 clientId: '${dotenv.env['clientId']}',
//                                 clientSecret: '${dotenv.env['clientSecret']}',
//                                 redirectUrl: '$url/',
//                                 scope: ['openid', 'profile', 'email'],
//                               );

//                               SignInWithLinkedIn.signIn(
//                                 context,
//                                 config: linkedInConfig,
//                                 onGetAuthToken: (data) {
//                                   // logger.info('Auth token data: ${data.toJson()}');
//                                   controller.getUserInfo(data.toJson());
//                                 },
//                                 onSignInError: (error) {},
//                               );
//                             }),
//                         5.widthBox,
//                         otherLogInWidget(
//                             imagePath: googleImage,
//                             context: context,
//                             onTap: () async {
//                               await controller.signInWithGoogle();
//                             }),
//                         5.widthBox,
//                         otherLogInWidget(
//                             imagePath: instagramImage,
//                             context: context,
//                             onTap: () async {
//                               logger.info("dfdsfdf");

//                               var clientId = "1187700026406651";
//                               var redirectUri = "$url/";
//                               var clientSecret =
//                                   "ea9bdba920e7fef56ce58a2fb3036f36";

//                               // Step 1: Define the Instagram login URL with OAuth
//                               final String authUrl =
//                                   "https://api.instagram.com/oauth/authorize?client_id=$clientId&redirect_uri=$redirectUri&scope=user_profile&response_type=code";

//                               // Step 2: Open the URL for Instagram login
//                               // final result = await FlutterWebAuth.authenticate(
//                               //   url: authUrl,
//                               //   callbackUrlScheme: "yourapp", // use your redirect URI scheme
//                               // );

//                               // Step 3: Extract the code from the callback URL
//                               // final Uri responseUri = Uri.parse(result);
//                               // final String? code = responseUri.queryParameters['code'];

//                               // if (code != null) {
//                               //   // Step 4: Exchange the authorization code for an access token
//                               //   final response = await http.post(
//                               //     Uri.parse('https://api.instagram.com/oauth/access_token'),
//                               //     body: {
//                               //       'client_id': clientId,
//                               //       'client_secret': clientSecret,
//                               //       'grant_type': 'authorization_code',
//                               //       'redirect_uri': redirectUri,
//                               //       'code': code,
//                               //     },
//                               //   );

//                               //   if (response.statusCode == 200) {
//                               //     // Parse the access token
//                               //     final Map<String, dynamic> data = jsonDecode(response.body);
//                               //     String accessToken = data['access_token'];

//                               //     // Do something with the access token (e.g., fetch user profile)
//                               //     logger.info('Access Token: $accessToken');
//                               //   } else {
//                               //     logger.info('Error during token exchange: ${response.body}');
//                               //   }
//                               // } else {
//                               //   logger.info('Authorization code not found');
//                               // }
//                             }),
//                         //otherLogInWidget(imagePath: snapChatImage, context: context, onTap: (){}),
//                         // otherLogInWidget(imagePath: tikTokImage, context: context, onTap: () async{
//                         //   var code = await TiktokLoginFlutter.authorize(
//                         //       "user.info.basic,user.info.profile");
//                         //
//                         //   controller.getTikTokUserInfo(code);
//                         // }),
//                       ],
//                     ),
//                     15.heightBox,
//                     Center(
//                       child: RichText(
//                           text: TextSpan(children: [
//                         textSpan(
//                             context: context,
//                             textColor: textColor,
//                             fontFamily: bold,
//                             title:
//                                 "${controller.labelTextDetail['no_account_label'] ?? "Don't have any account yet?"}",
//                             textSize: 22.0),
//                         textSpan(
//                           context: context,
//                           textColor: primaryColor,
//                           fontFamily: bold,
//                           title:
//                               " ${controller.labelTextDetail['signup_link_label'] ?? " Sign up"}",
//                           textSize: 22.0,
//                           recognizer: TapGestureRecognizer()
//                             ..onTap = () {
//                               Get.offAllNamed("/signup");
//                             },
//                         ),
//                         textSpan(
//                             context: context,
//                             textColor: textColor,
//                             fontFamily: bold,
//                             title:
//                                 " ${controller.labelTextDetail['now_label'] ?? " now"}",
//                             textSize: 22.0),
//                       ])),
//                     ),
//                     5.heightBox,
//                     Row(
//                       mainAxisAlignment: MainAxisAlignment.center,
//                       children: [
//                         txt22Size(
//                             title:
//                                 "${controller.labelTextDetail['language_label'] ?? "Language "}:",
//                             fontFamily: regular,
//                             textColor: textColor,
//                             context: context),
//                         5.widthBox,
//                         InkWell(
//                           onTap: () {
//                             languageBottomSheet(context.screenWidth,
//                                 controller.serviceController,
//                                 page: "login");
//                           },
//                           child: Ink(
//                               padding: const EdgeInsets.all(5.0),
//                               child: ClipRRect(
//                                 borderRadius: BorderRadius.circular(50.0),
//                                 child: networkCacheImageWidget(
//                                     controller.serviceController.langIcon.value,
//                                     BoxFit.cover,
//                                     30.0,
//                                     30.0),
//                               )),
//                         ),
//                         5.widthBox,
//                       ],
//                     )
//                   ],
//                 ),
//               ),
//             ),
//             if (controller.showOverly.value == true) ...[overlayWidget(context)]
//           ],
//         );
//       }
//     }));
//   }
// }
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

class LoginPage extends GetView<LoginController> {
  const LoginPage({super.key});

  @override
  Widget build(BuildContext context) {
    if (!Get.isRegistered<LoginController>()) {
      Get.put(LoginController());
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
                            // if (controller
                            //     .passwordTextController.text.isEmpty) ...[
                            //   toolTip(tip: "Password is required"),
                            // ],
                            // if (controller.errors.firstWhereOrNull(
                            //         (e) => e['title'] == "password") !=
                            //     null)
                            //   toolTipEmptyPassword(context)
                            // COMMENTED OUT - no password validation on login screen
                            // if (controller.passwordTextController.text
                            //         .trim()
                            //         .isEmpty &&
                            //     controller.errors
                            //         .any((e) => e['title'] == "password")) ...[
                            //   toolTipEmptyPassword(context),
                            // ]
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
                                      '$url/en/signup/linkedin-openid/callback',
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

  // Helper method to build requirement rows - COMMENTED OUT (not needed for login)
  // Widget _buildRequirementRow(BuildContext context, String requirement,
  //     bool isCompleted, IconData icon) {
  //   return Row(
  //     children: [
  //       Container(
  //         width: 20.0,
  //         height: 20.0,
  //         decoration: BoxDecoration(
  //           shape: BoxShape.circle,
  //           color: isCompleted ? Colors.green : Colors.grey.shade300,
  //         ),
  //         child: Icon(
  //           isCompleted ? Icons.check : icon,
  //           size: 12.0,
  //           color: isCompleted ? Colors.white : Colors.grey.shade600,
  //         ),
  //       ),
  //       const SizedBox(width: 12),
  //       Expanded(
  //         child: txt16Size(
  //           title: requirement,
  //           fontFamily: regular,
  //           textColor: isCompleted ? Colors.green.shade700 : textColor,
  //           context: context,
  //         ),
  //       ),
  //       // Mini progress bar for each requirement
  //       Container(
  //         width: 40.0,
  //         height: 4.0,
  //         decoration: BoxDecoration(
  //           borderRadius: BorderRadius.circular(2.0),
  //           color: isCompleted ? Colors.green : Colors.grey.shade300,
  //         ),
  //       ),
  //     ],
  //   );
  // }

  // Helper methods for security strength - COMMENTED OUT (not needed for login)
  // Color _getStrengthColor(int completedRequirements) {
  //   switch (completedRequirements) {
  //     case 0:
  //     case 1:
  //       return Colors.red;
  //     case 2:
  //     case 3:
  //       return Colors.orange;
  //     case 4:
  //       return Colors.blue;
  //     case 5:
  //       return Colors.green;
  //     default:
  //       return Colors.grey;
  //   }
  // }

  // IconData _getStrengthIcon(int completedRequirements) {
  //   switch (completedRequirements) {
  //     case 0:
  //     case 1:
  //       return Icons.security;
  //     case 2:
  //     case 3:
  //       return Icons.shield;
  //     case 4:
  //       return Icons.verified_user;
  //     case 5:
  //       return Icons.security;
  //     default:
  //       return Icons.help;
  //   }
  // }

  // String _getStrengthText(int completedRequirements) {
  //   switch (completedRequirements) {
  //     case 0:
  //     case 1:
  //       return "Very Weak";
  //     case 2:
  //       return "Weak";
  //     case 3:
  //       return "Fair";
  //     case 4:
  //       return "Good";
  //     case 5:
  //       return "Strong";
  //     default:
  //       return "Unknown";
  //   }
  // }
}
