import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/password/PasswordController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';


class PasswordPage extends GetView<PasswordController> {
  const PasswordPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(PasswordController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "Password"}", context: context)),
          leading: const BackButton(
              color: Colors.white
          ),
        ),
        body: Obx(() {
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
                        txt22Size(title: "${controller.labelTextDetail['mobile_password_description_text'] ?? "You can update your password from here. Passwords must have at least eight characters and contain one uppercase and one lowercase.\nStrong passwords include numbers, letters, and punctuation marks"}",
                            fontFamily: regular, textColor: textColor, context: context),
                        10.heightBox,
                        10.heightBox,
                        txt20Size(title: "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? "* Indicates required field"}", fontFamily: regular, context: context,textColor: Colors.red),
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(title: "${controller.labelTextDetail['current_password_label'] ?? "Current password"}", fontFamily: regular, textColor: textColor, context: context),
                            txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                          ],
                        ),
                        5.heightBox,
                        TextFormField(
                          onChanged: (value) {
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "current_password") != null)
                            {
                              controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "current_password"));
                            }
                          },
                          controller: controller.currentPasswordTextEditingController,
                          decoration: InputDecoration(
                            enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(5.0),
                                borderSide:BorderSide(color: Colors.grey.shade400,
                                    style: BorderStyle.solid, width: 1)
                            ),
                            focusedBorder:OutlineInputBorder(
                                borderRadius: BorderRadius.circular(5.0),
                                borderSide:const BorderSide(color: primaryColor)
                            ),
                            filled: true,
                            fillColor: inputColor,
                            contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
                            suffixIcon: IconButton(
                              icon: Icon(
                                controller.isOldPasswordVisible.value
                                    ? Icons.visibility_off
                                    : Icons.visibility,
                              ),
                              onPressed: () =>
                              controller.isOldPasswordVisible.value = !controller.isOldPasswordVisible.value,
                            ),
                          ),
                          style: const TextStyle(
                              fontSize: 18,
                              fontFamily: regular
                          ),
                          obscureText: !controller.isOldPasswordVisible.value,
                          keyboardType: TextInputType.visiblePassword,
                          textInputAction: TextInputAction.done,
                          focusNode: controller.focusNodes[1.toString()],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "current_password") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "current_password"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(title: "${controller.labelTextDetail['new_password_label'] ?? "New password"}", fontFamily: regular, textColor: textColor, context: context),
                            txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                          ],
                        ),
                        5.heightBox,
                        TextFormField(
                          onChanged: (value) {
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "new_password") != null)
                            {
                              controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "new_password"));
                            }
                          },
                          controller: controller.newPasswordTextEditingController,
                          decoration: InputDecoration(
                            enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(5.0),
                                borderSide:BorderSide(color: Colors.grey.shade400,
                                    style: BorderStyle.solid, width: 1)
                            ),
                            focusedBorder:OutlineInputBorder(
                                borderRadius: BorderRadius.circular(5.0),
                                borderSide:const BorderSide(color: primaryColor)
                            ),
                            filled: true,
                            fillColor: inputColor,
                            contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
                            suffixIcon: IconButton(
                              icon: Icon(
                                controller.isNewPasswordVisible.value
                                    ? Icons.visibility_off
                                    : Icons.visibility,
                              ),
                              onPressed: () =>
                              controller.isNewPasswordVisible.value = !controller.isNewPasswordVisible.value,
                            ),
                          ),
                          style: const TextStyle(
                              fontSize: 18,
                              fontFamily: regular
                          ),
                          obscureText: !controller.isNewPasswordVisible.value,
                          keyboardType: TextInputType.visiblePassword,
                          textInputAction: TextInputAction.done,
                          focusNode: controller.focusNodes[2.toString()],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "new_password") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "new_password"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(title: "${controller.labelTextDetail['confirm_new_password_label'] ?? "Confirm new password"}", fontFamily: regular, textColor: textColor, context: context),
                            txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                          ],
                        ),
                        5.heightBox,
                        TextFormField(
                          onChanged: (value) {
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "confirm_password") != null)
                            {
                              controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "confirm_password"));
                            }
                          },
                          controller: controller.confirmPasswordTextEditingController,
                          decoration: InputDecoration(
                            enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(5.0),
                                borderSide:BorderSide(color: Colors.grey.shade400,
                                    style: BorderStyle.solid, width: 1)
                            ),
                            focusedBorder:OutlineInputBorder(
                                borderRadius: BorderRadius.circular(5.0),
                                borderSide:const BorderSide(color: primaryColor)
                            ),
                            filled: true,
                            fillColor: inputColor,
                            contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
                            suffixIcon: IconButton(
                              icon: Icon(
                                controller.isConfirmPasswordVisible.value
                                    ? Icons.visibility_off
                                    : Icons.visibility,
                              ),
                              onPressed: () =>
                              controller.isConfirmPasswordVisible.value = !controller.isConfirmPasswordVisible.value,
                            ),
                          ),
                          style: const TextStyle(
                              fontSize: 18,
                              fontFamily: regular
                          ),
                          obscureText: !controller.isConfirmPasswordVisible.value,
                          keyboardType: TextInputType.visiblePassword,
                          textInputAction: TextInputAction.done,
                          focusNode: controller.focusNodes[3.toString()],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "confirm_password") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "confirm_password"))
                        ],
                        100.heightBox,

                      ],
                    ),
                  ),
                ),
                Align(
                  alignment: Alignment.bottomCenter,
                  child: Container(
                    color: Colors.grey.shade100,
                    padding: EdgeInsets.all(getValueForScreenType<double>(
                      context: context,
                      mobile: 15.0,
                      tablet: 15.0,
                    ),
                    ),
                    width: context.screenWidth,
                    height: 75,
                    child: elevatedButtonWidget(
                        textWidget: txt28Size(title: "${controller.labelTextDetail['update_button_text'] ?? "Update"}", fontFamily: regular, textColor: Colors.white, context: context),
                        onPressed: () async{
                          await controller.updatePassword();
                        },

                        context: context,
                        btnRadius: 5.0
                    ),
                  ),
                ),
                if(controller.isOverlayLoading.value == true)...[
                  overlayWidget(context)
                ]
              ],
            );
          }
        })
    );
  }
}
