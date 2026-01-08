import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/email_address/EmailAddressController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class UpdateEmailAddressPage extends StatelessWidget {
  const UpdateEmailAddressPage({super.key});

  @override
  Widget build(BuildContext context) {
    var controller = Get.find<EmailAddressController>();
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "My e-mail address"}", context: context)),
          leading: const BackButton(color: Colors.white),
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
                        txt20Size(
                            title: "${controller.labelTextDetail['current_email_label'] ?? "Current e-mail"}",
                            fontFamily: regular,
                            textColor: textColor,
                            context: context),
                        5.heightBox,
                        fieldsWidget(
                          textController:
                          controller.currentEmailTextEditingController,
                          fieldType: "email",
                          readonly: true,
                          fontFamily: regular,
                          fontSize: 18.0,
                        ),
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['new_email_label'] ?? "New e-mail"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                          textController:
                          controller.newEmailTextEditingController,
                          onChanged: (value) {
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "email") != null)
                            {
                              controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "email"));
                            }
                          },
                          fieldType: "email",
                          readonly: false,
                          fontFamily: regular,
                          fontSize: 18.0,
                          placeHolder: "",
                          focusNode: controller.focusNodes[1.toString()],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "email") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "email"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['confirm_email_label'] ?? "Confirm new e-mail"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                          textController:
                          controller.confirmEmailTextEditingController,
                          onChanged: (value) {
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "email_confirmation") != null)
                            {
                              controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "email_confirmation"));
                            }
                          },
                          fieldType: "email",
                          readonly: false,
                          fontFamily: regular,
                          fontSize: 18.0,
                          placeHolder: "",
                          focusNode: controller.focusNodes[2.toString()],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "email_confirmation") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "email_confirmation"))
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
                    )),
                    width: context.screenWidth,
                    // height: 75,
                    child: elevatedButtonWidget(
                        textWidget: txt28Size(
                            title: "${controller.labelTextDetail['save_btn_label'] ?? "Save"}",
                            fontFamily: regular,
                            textColor: Colors.white,
                            context: context),
                        onPressed: () async {
                          controller.currentEmailTextEditingController.text = "";
                          await controller.updateEmailAddress();
                        },
                        context: context,
                        btnRadius: 5.0),
                  ),
                ),
                if (controller.isOverlayLoading.value == true) ...[
                  overlayWidget(context)
                ]
              ],
            );
          }
        }));
  }
}
