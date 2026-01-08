import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/contact_us/ContactUsController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';

import '../widgets/tool_tip.dart';

class ContactUsPage extends GetView<ContactUsController> {
  const ContactUsPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(ContactUsController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title: "${controller.labelTextDetail['main_heading'] ?? "Contact ProximaRide"}", context: context)),
          leading: const BackButton(color: Colors.white),
        ),
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

                        txt20Size(title: "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? '* Indicates required field'}",context: context,fontFamily: bold,textColor: Colors.red),
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['your_full_name_label'] ?? "Your full name"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)

                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                          textController: controller.nameTextEditingController,
                          fieldType: "text",
                          readonly: false,
                          fontFamily: regular,
                          fontSize: 18.0,
                          placeHolder: "",
                          onChanged: (value){
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "name") != null)
                            {
                              controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "name"));
                            }
                          },
                          focusNode: controller.focusNodes[1.toString()],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "name") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "name"))
                        ],
                        if (!controller.isLoggedInWithEmail.value) ...[
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['your_email_address_label'] ?? "Your e-mail address"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
                            ],
                          ),
                          5.heightBox,
                          fieldsWidget(
                            textController: controller.emailTextEditingController,
                            fieldType: "email",
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            placeHolder: "",
                            onChanged: (value){
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "email") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "email"));
                              }
                            },
                            focusNode: controller.focusNodes[2.toString()],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "email") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "email"))
                          ],
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['your_phone_label'] ?? "Your phone"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                          textController: controller.phoneTextEditingController,
                          fieldType: "number",
                          readonly: false,
                          fontFamily: regular,
                          fontSize: 18.0,
                          placeHolder: "",
                          onChanged: (value){
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "phone") != null)
                            {
                              controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "phone"));
                            }
                          },
                          focusNode: controller.focusNodes[3.toString()],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "phone") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "phone"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['your_message_label'] ?? "Your message"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
                          ],
                        ),
                        5.heightBox,
                        textAreaWidget(
                          textController: controller.messageTextEditingController,
                          readonly: false,
                          fontFamily: regular,
                          fontSize: 18.0,
                          placeHolder: "",
                          maxLines: 5,
                          onChanged: (value){
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "message") != null)
                            {
                              controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "message"));
                            }
                          },
                          focusNode: controller.focusNodes[4.toString()],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "message") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "message"))
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
                    child: elevatedButtonWidget(
                        textWidget: txt28Size(
                            title: "${controller.labelTextDetail['submit_button_text'] ?? "Submit"}",
                            fontFamily: regular,
                            textColor: Colors.white,
                            context: context),
                        onPressed: () async {
                          await controller.storeContactUs();
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
