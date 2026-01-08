import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/stages/StageTowController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/step_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../widgets/image_upload_bottom_sheet.dart';
import '../widgets/image_upload_widget.dart';
import '../widgets/tool_tip.dart';


class StageTwo extends GetView<StageTowController> {
  const StageTwo({super.key});



  @override
  Widget build(BuildContext context) {
    Get.put(StageTowController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => stepAppBarWidget(
              context: context, serviceController: controller.serviceController, langId: controller.serviceController.langId.value, langIcon: controller.serviceController.langIcon.value, screeWidth: context.screenWidth, page: "step2")),
          // leading: const BackButton(color: Colors.white),
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
                        Align(
                          alignment: Alignment.topRight,
                          child: elevatedButtonWidget(
                              textWidget: txt16Size(
                                  title: "${controller.labelTextDetail['logout_button_label'] ?? "Logout"}",
                                  fontFamily: regular,
                                  textColor: Colors.white,
                                  context: context),
                              onPressed: () async {
                                await controller.serviceController.logoutUser();
                              },
                              context: context,
                              btnColor: primaryColor,
                              btnRadius: 5.0),
                        ),
                        10.heightBox,
                        Center(child: txt25Size(title: "${controller.labelTextDetail['main_heading'] ?? "Step 2 of 4 - Profile picture"}", context: context)),
                        15.heightBox,
                        txt16Size(title: "${controller.labelTextDetail['sub_heading_text'] ?? "If you are signing up as a driver, then please note that to be eligible to post Pink Rides and Extra-Care Rides, you must upload your profile photo"}", context: context),
                        10.heightBox,
                        imageUploadWidget(
                            context: context,
                            onTap: () async{
                              await imageUploadBottomSheet(controller, context);
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "image") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "image"));
                              }
                            },
                            title: "${controller.labelTextDetail['mobile_photo_label'] ?? "Upload profile photo."}",
                            title1: "${controller.labelTextDetail['mobile_choose_file_label'] ?? "Upload profile photo."}",
                            title2: "${controller.labelTextDetail['photo_label'] ?? "Upload profile photo."}",
                            imageFile: controller.profileImageName.value == "" ? null : controller.profileImagePath.value,
                            screenWidth: context.screenWidth
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "image") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "image"))
                        ],
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
                      child: Row(children: [
                        Expanded(
                          flex: 10,
                          child: elevatedButtonWidget(
                              textWidget: txt28Size(
                                  title: "${controller.labelTextDetail['skip_button_label'] ?? "Skip"}",
                                  fontFamily: regular,
                                  textColor: Colors.white,
                                  context: context),
                              onPressed: () async {
                                await controller.setStageTwo(true);
                              },
                              btnColor: primaryColor,
                              context: context,
                              btnRadius: 5.0),
                        ),
                        const Spacer(flex: 1,),
                        Expanded(
                          flex: 10,
                          child: elevatedButtonWidget(
                              textWidget: txt28Size(
                                  title: "${controller.labelTextDetail['next_button_label'] ?? "Next"}",
                                  fontFamily: regular,
                                  textColor: Colors.white,
                                  context: context),
                              onPressed: () async {
                                await controller.setStageTwo(false);
                              },
                              context: context,
                              btnRadius: 5.0),
                        ),

                      ],)
                  ),
                ),
                if (controller.isOverlayLoading.value == true) ...[
                  overlayWidget(context)
                ]
              ],
            );
          }
        })

    );
  }
}
