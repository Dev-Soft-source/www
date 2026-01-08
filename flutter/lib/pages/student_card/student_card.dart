import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/student_card/StudentCardController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/drop_down_date_widget.dart';
import 'package:proximaride_app/pages/widgets/image_upload_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/image_upload_widget.dart';
import 'package:proximaride_app/pages/widgets/image_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';


class StudentCardPage extends GetView<StudentCardController> {
  const StudentCardPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(StudentCardController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "Student card"}", context: context)),
          leading: const BackButton(
              color: Colors.white
          ),
        ),
        body: Obx(() {
          if(controller.isLoading.value == true){
            return Center(
              child: progressCircularWidget(context),
            );
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
                        txt22Size(title: "${controller.labelTextDetail['student_card_description_text'] ?? 'To be eligible for our offers to students, your student card must be valid'}",
                            fontFamily: regular, textColor: textColor, context: context),
                        10.heightBox,
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
                         controller.oldImagePath.value == "" ? imageUploadWidget(
                            context: context,
                            onTap: () async{
                              await imageUploadBottomSheet(controller, context);
                              if (controller.errors.any((error) => error['title'] == "student_card")) {
                                controller.errors.removeWhere((error) => error['title'] == "student_card");
                              }
                            },
                            title: "${controller.labelTextDetail['student_card_image_placeholder'] ?? "Student card."}",
                             title1: "${controller.labelTextDetail['choose_file_image_placeholder'] ?? "Choose file"}",
                             title2: "${controller.labelTextDetail['mobile_image_type_placeholder'] ?? "(Only JPG, PNG, JPEG and GIF are allowed. Max. 10MB)"}",
                            imageFile: controller.studentCardImageName.value == "" ? null : controller.studentCardImagePath.value,
                            screenWidth: context.screenWidth
                        ) : imageWidget(
                           context: context,
                           onTap: () async{
                             controller.serviceController.showImage.value = controller.studentCardImagePathOriginalOld.value;
                             Get.toNamed("/show_image");
                           },
                           screenWidth: context.screenWidth,
                           imagePath: controller.oldImagePath.value
                         ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "student_card") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "student_card"))
                        ],
                        if (controller.oldImagePath.value != "" && controller.studentCardImageName.value == "") ...[
                          10.heightBox,
                          Align(
                            alignment: Alignment.topRight,
                            child: elevatedButtonWidget(
                                textWidget: txt22Size(title: "${controller.labelTextDetail['upload_new_image_btn_label'] ?? "Upload new image"}",context: context, textColor: Colors.white),
                                onPressed: () async{
                                  await imageUploadBottomSheet(
                                      controller, context);
                                },
                                context: context,
                                btnColor: primaryColor
                            ),
                          )
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(title: "${controller.labelTextDetail['expiry_date_label'] ?? "Student card expiry date"}", fontFamily: regular, textColor: textColor, context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
                          ],
                        ),
                        5.heightBox,
                        Row(
                          children: [
                            Expanded(
                              child: Container(
                                color: inputColor,
                                child: dropdownMonthWidget(controller: controller,context: context,screenHeight: context.screenHeight, screenWidth: context.screenWidth, monthPlaceholder: "${controller.labelTextDetail['month_placeholder'] ?? "Month"}", type: "student"),
                              ),
                            ),
                            5.widthBox,
                            Expanded(
                              child: Container(
                                color: inputColor,
                                child: dropdownYearWidget(controller: controller,context: context,screenHeight: context.screenHeight, screenWidth: context.screenWidth, yearPlaceholder: "${controller.labelTextDetail['year_placeholder'] ?? "Year"}", type: "student"),
                              ),
                            )
                          ],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "student_card_exp_date") != null) ...[
                          if(controller.month.value == "")...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "student_card_exp_date"))
                          ]else...[
                            Align(alignment: Alignment.centerRight, child: toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "student_card_exp_date")))
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
                        textWidget: txt28Size(title: controller.oldImagePath.value == "" ? "${controller.labelTextDetail['upload_button_text'] ?? "Upload"}" : "${controller.labelTextDetail['update_button_text'] ?? "Update"}"



                            , fontFamily: regular, textColor: Colors.white, context: context),
                        onPressed: () async{
                          await controller.updateStudentCard();
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
