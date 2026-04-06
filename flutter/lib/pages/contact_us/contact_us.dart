import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/contact_us/ContactUsController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';

import '../widgets/tool_tip.dart';

class ContactUsPage extends StatelessWidget {
  const ContactUsPage({super.key});

  @override
  Widget build(BuildContext context) {
    final ContactUsController controller = Get.isRegistered<ContactUsController>()
        ? Get.find<ContactUsController>()
        : Get.put(ContactUsController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['main_heading'] ?? "Contact ProximaRide"}",
              context: context)),
          leading: safeBackButton(context),
        ),
        body: SafeArea(
          child: Obx(() {
            if (controller.errorStateManager.isLoading.value) {
              return Center(child: progressCircularWidget(context));
            } else if (controller.errorStateManager.hasError.value) {
              return ErrorStateWidget(
                message: controller.errorStateManager.errorMessage.value,
                errorType: controller.errorStateManager.errorType.value,
                onRetry: () {
                  if (controller.errorStateManager.onRetry.value != null) {
                    controller.errorStateManager.onRetry.value!();
                  }
                },
              );
            } else if (controller.isLoading.value == true) {
              return Center(child: progressCircularWidget(context));
            } else {
              return Column(
                children: [
                  Expanded(
                    child: Container(
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
                          // Red required-fields note: 18px
                          txt18Size(
                              title:
                                  "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? '* Indicates required fields'}",
                              context: context,
                              fontFamily: carlito,
                              textColor: Colors.red),
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['your_full_name_label'] ?? "Your full name"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              // Asterisk slightly smaller: 18px
                              txt18Size(
                                  title: '*',
                                  context: context,
                                  fontFamily: bold,
                                  textColor: Colors.red)
                            ],
                          ),
                          5.heightBox,
                          fieldsWidget(
                            textController:
                                controller.nameTextEditingController,
                            fieldType: "text",
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            placeHolder: "",
                            onChanged: (value) {
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "name") !=
                                  null) {
                                controller.errors.remove(controller.errors
                                    .firstWhereOrNull((element) =>
                                        element['title'] == "name"));
                              }
                            },
                            focusNode: controller.focusNodes[1.toString()],
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "name") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "name"))
                          ],
                          10.heightBox,
                          txt20Size(
                              title:
                                  "${controller.labelTextDetail['your_phone_label'] ?? "Your Phone Number"}",
                              fontFamily: regular,
                              textColor: textColor,
                              context: context),
                          5.heightBox,
                          fieldsWidget(
                            textController:
                                controller.phoneTextEditingController,
                            fieldType: "number",
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            placeHolder: "",
                            onChanged: (value) {
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "phone") !=
                                  null) {
                                controller.errors.remove(controller.errors
                                    .firstWhereOrNull((element) =>
                                        element['title'] == "phone"));
                              }
                            },
                            focusNode: controller.focusNodes[2.toString()],
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "phone") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "phone"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['your_message_label'] ?? "Your message"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              txt18Size(
                                  title: '*',
                                  context: context,
                                  fontFamily: bold,
                                  textColor: Colors.red)
                            ],
                          ),
                          5.heightBox,
                          textAreaWidget(
                            textController:
                                controller.messageTextEditingController,
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            placeHolder: "(Max. 500 characters)",
                            maxLines: 5,
                            onChanged: (value) {
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "message") !=
                                  null) {
                                controller.errors.remove(controller.errors
                                    .firstWhereOrNull((element) =>
                                        element['title'] == "message"));
                              }
                            },
                            focusNode: controller.focusNodes[3.toString()],
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "message") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "message"))
                          ],
                            24.heightBox,
                          ],
                        ),
                      ),
                    ),
                  ),
                  Container(
                    color: Colors.grey.shade100,
                    padding: EdgeInsets.all(getValueForScreenType<double>(
                      context: context,
                      mobile: 15.0,
                      tablet: 15.0,
                    )),
                    width: context.screenWidth,
                    child: elevatedButtonWidget(
                        textWidget: txt22Size(
                            title:
                                "${controller.labelTextDetail['submit_button_text'] ?? "Submit"}",
                            fontFamily: regular,
                            textColor: Colors.white,
                            context: context),
                        onPressed: () async {
                          await controller.storeContactUs();
                        },
                        context: context,
                        btnRadius: 5.0),
                  ),
                  if (controller.isOverlayLoading.value == true) ...[
                    overlayWidget(context)
                  ]
                ],
              );
            }
          }),
        ));
  }
}



