import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/student_card/StudentCardController.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/drop_down_date_widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/image_upload_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/image_upload_widget.dart';
import 'package:proximaride_app/pages/widgets/image_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class StudentCardPage extends StatelessWidget {
  const StudentCardPage({super.key});

  @override
  Widget build(BuildContext context) {
    final StudentCardController controller = Get.isRegistered<StudentCardController>()
        ? Get.find<StudentCardController>()
        : Get.put(StudentCardController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['main_heading'] ?? "Student card"}",
              context: context)),
          leading: safeBackButton(context),
        ),
        body: SafeArea(
          child: Obx(() {
            if (controller.errorStateManager.isLoading.value) {
              return Center(
                child: progressCircularWidget(context),
              );
            }

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

            if (controller.isLoading.value == true) {
              return Center(
                child: progressCircularWidget(context),
              );
            } else {
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
                          // Red required-fields note: 18px
                          txt18Size(
                              title:
                                  "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? '* Indicates required fields'}",
                              context: context,
                              fontFamily: bold,
                              textColor: Colors.red),
                          10.heightBox,
                          // Description/body text: 20px
                          txt20Size(
                              title:
                                  "${controller.labelTextDetail['student_card_description_text'] ?? 'To be eligible for our offers to students, your student card must be valid'}",
                              fontFamily: regular,
                              textColor: textColor,
                              context: context),
                          20.heightBox,
                          // if(controller.errorList.isNotEmpty)...[
                          //   ListView.builder(
                          //     shrinkWrap: true,
                          //     physics: const NeverScrollableScrollPhysics(),
                          //     itemCount: controller.errorList.length,
                          //     itemBuilder: (context, index){
                          //       return Column(
                          //         crossAxisAlignment: CrossAxisAlignment.start,
                          //         children: [
                          //           Row(
                          //             crossAxisAlignment: CrossAxisAlignment.center,
                          //             mainAxisAlignment: MainAxisAlignment.center,
                          //             children: [
                          //               const Icon(Icons.circle, size: 10, color: Colors.red),
                          //               10.widthBox,
                          //               Expanded(child: txt14Size(title: "${controller.errorList[index]}", fontFamily: regular, textColor: Colors.red, context: context))
                          //             ],
                          //           ),
                          //           10.heightBox,
                          //         ],
                          //       );
                          //     },
                          //   ),
                          // ],
                          controller.oldImagePath.value == ""
                              ? imageUploadWidget(
                                  context: context,
                                  onTap: () async {
                                    await imageUploadBottomSheet(
                                        controller, context);
                                    if (controller.errors.any((error) =>
                                        error['title'] == "student_card")) {
                                      controller.errors.removeWhere((error) =>
                                          error['title'] == "student_card");
                                    }
                                  },
                                  title:
                                      "${controller.labelTextDetail['student_card_image_placeholder'] ?? "Student card."}",
                                  title1: "",
                                  title2:
                                      "(JPG, PNG, JPEG, and GIF. 10MB max.)",
                                  imageFile: controller
                                              .studentCardImageName.value ==
                                          ""
                                      ? null
                                      : controller.studentCardImagePath.value,
                                  screenWidth: context.screenWidth)
                              : Stack(
                                  children: [
                                    imageWidget(
                                      context: context,
                                      onTap: () async {
                                        logger.info(controller
                                            .studentCardImagePathOriginalOld
                                            .value);
                                        logger.info(
                                            controller.oldImagePath.value);
                                        controller.serviceController.showImage
                                                .value =
                                            controller
                                                .studentCardImagePathOriginalOld
                                                .value;
                                        Get.toNamed("/show_image");
                                      },
                                      screenWidth: context.screenWidth,
                                      imagePath: controller.oldImagePath.value,
                                    ),
                                    Positioned(
                                      top: 8,
                                      right: 8,
                                      child: GestureDetector(
                                        onTap: () {
                                          controller.removeStudentCard();
                                        },
                                        child: Container(
                                          decoration: const BoxDecoration(
                                              color:
                                                  primaryColor, // Background color to make the icon more visible
                                              shape: BoxShape.rectangle,
                                              borderRadius: BorderRadius.all(
                                                  Radius.circular(5.0))),
                                          padding: const EdgeInsets.all(4.0),
                                          child: const Icon(
                                            Icons.delete,
                                            color: Colors.white,
                                          ),
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                          if (controller.errors.firstWhereOrNull((element) =>
                                  element['title'] == "student_card") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) =>
                                        element['title'] == "student_card"))
                          ],
                          if (controller.oldImagePath.value != "" &&
                              controller.studentCardImageName.value == "") ...[
                            10.heightBox,
                            Align(
                              alignment: Alignment.center,
                              child: elevatedButtonWidget(
                                  textWidget: txt22Size(
                                      title:
                                          "${controller.labelTextDetail['upload_new_image_btn_label'] ?? "Upload new image"}",
                                      context: context,
                                      textColor: Colors.white),
                                  onPressed: () async {
                                    await imageUploadBottomSheet(
                                        controller, context);
                                  },
                                  context: context,
                                  btnColor: primaryColor),
                            )
                          ],
                          20.heightBox,
                          Row(
                            children: [
                              // Title: 20px
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['expiry_date_label'] ?? "Student card expiry date"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              // Red asterisk slightly smaller: 18px
                              txt18Size(
                                  title: '*',
                                  context: context,
                                  fontFamily: bold,
                                  textColor: Colors.red)
                            ],
                          ),
                          5.heightBox,
                          Row(
                            children: [
                              Expanded(
                                child: Container(
                                  color: inputColor,
                                  child: dropdownMonthWidget(
                                      controller: controller,
                                      context: context,
                                      screenHeight: context.screenHeight,
                                      screenWidth: context.screenWidth,
                                      monthPlaceholder:
                                          "${controller.labelTextDetail['month_placeholder'] ?? "Month"}",
                                      type: "student"),
                                ),
                              ),
                              5.widthBox,
                              Expanded(
                                child: Container(
                                  color: inputColor,
                                  child: dropdownYearWidget(
                                      controller: controller,
                                      context: context,
                                      screenHeight: context.screenHeight,
                                      screenWidth: context.screenWidth,
                                      yearPlaceholder:
                                          "${controller.labelTextDetail['year_placeholder'] ?? "Year"}",
                                      type: "student",
                                      yearsAhead: 4),
                                ),
                              )
                            ],
                          ),
                          if (controller.errors.firstWhereOrNull((element) =>
                                  element['title'] ==
                                  "student_card_exp_date") !=
                              null) ...[
                            if (controller.month.value == "") ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] ==
                                          "student_card_exp_date"))
                            ] else ...[
                              Align(
                                  alignment: Alignment.centerRight,
                                  child: toolTip(
                                      tip: controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] ==
                                              "student_card_exp_date")))
                            ],
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
                      height: 75,
                      child: elevatedButtonWidget(
                          enabled: controller.isFormDirty.value,
                          textWidget: txt22Size(
                              title: controller.oldImagePath.value == ""
                                  ? "${controller.labelTextDetail['upload_button_text'] ?? "Upload"}"
                                  : "${controller.labelTextDetail['update_button_text'] ?? "Update"}",
                              fontFamily: regular,
                              textColor: Colors.white,
                              context: context),
                          onPressed: () async {
                            await controller.updateStudentCard();
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
          }),
        ));
  }
}



