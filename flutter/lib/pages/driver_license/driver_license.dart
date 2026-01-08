import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/driver_license/DriverLicenseController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/image_upload_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/image_upload_widget.dart';
import 'package:proximaride_app/pages/widgets/image_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class DriverLicensePage extends GetView<DriverLicenseController> {
  const DriverLicensePage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(DriverLicenseController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "Driver's license"}", context: context)),
        leading: const BackButton(color: Colors.white),
      ),
      body: Obx(() {
        if (controller.isLoading.value == true) {
          return Center(child: progressCircularWidget(context));
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
                      txt20Size(title: "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? '* Indicates required field'}",context: context,fontFamily: bold,textColor: Colors.red),
                      txt22Size(
                          title:
                          "${controller.labelTextDetail['driver_license_description_text'] ?? 'To be eligible to post "Pink rides" and "Extra-care rides", you must upload your driver\'s license'}",
                          fontFamily: regular,
                          textColor: textColor,
                          context: context),
                      10.heightBox,
                      Row(
                        children: [
                          txt20Size(
                              title: "${controller.labelTextDetail['driver_license_label'] ?? "Your driver license"}",
                              fontFamily: regular,
                              textColor: textColor,
                              context: context),
                          txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
                        ],
                      ),
                      5.heightBox,
                      if (controller.oldImagePath.value != "")
                        Stack(
                          children: [
                            imageWidget(
                              context: context,
                              onTap: () async {

                                controller.serviceController.showImage.value = controller.driverLicensePathOriginalOld.value;
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
                                  controller.removeDriverLicense();
                                },
                                child: Container(
                                  decoration: const BoxDecoration(
                                    color: primaryColor, // Background color to make the icon more visible
                                    shape: BoxShape.rectangle,
                                    borderRadius: BorderRadius.all(Radius.circular(5.0))
                                  ),
                                  padding: const EdgeInsets.all(4.0),
                                  child: const Icon(
                                    Icons.delete,
                                    color: Colors.white,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        )
                      else
                        imageUploadWidget(
                          context: context,
                          onTap: () async {
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "driver_liscense") != null)
                            {
                              controller.errors.removeWhere((element) => element['title'] == "driver_liscense");
                            }
                            await imageUploadBottomSheet(
                                controller, context);
                          },
                          title: "${controller.labelTextDetail['mobile_driver_license_image_placeholder'] ?? "Driver license."}",
                          title1: "${controller.labelTextDetail['mobile_choose_file_image_placeholder'] ?? "Choose file"}",
                          title2: "${controller.labelTextDetail['mobile_image_type_placeholder'] ?? "(Only JPG, PNG, JPEG and GIF are allowed. Max. 10MB)"}",
                          imageFile: controller.driverLicenseName.value == ""
                              ? null
                              : controller.driverLicensePath.value,
                          screenWidth: context.screenWidth,
                        ),
                      if(controller.errors.firstWhereOrNull((element) => element['title'] == "driver_liscense") != null) ...[
                        toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "driver_liscense"))
                      ],
                      if (controller.oldImagePath.value != "" && controller.driverLicenseName.value == "") ...[
                        10.heightBox,
                        Align(
                          alignment: Alignment.topRight,
                          child: elevatedButtonWidget(
                              textWidget: txt22Size(title: "${controller.labelTextDetail['upload_new_image_btn_label'] ?? "Upload new image"}",context: context, textColor: Colors.white),
                              onPressed: () async{
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "driver_liscense") != null)
                                {
                                  controller.errors.removeWhere((element) => element['title'] == "driver_liscense");
                                }
                                await imageUploadBottomSheet(controller, context);
                              },
                              context: context,
                              btnColor: primaryColor
                          ),
                        )
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
                        title: controller.oldImagePath.value == ""
                            ? "${controller.labelTextDetail['upload_button_text'] ?? "Upload"}"
                            : "${controller.labelTextDetail['update_button_text'] ?? "Update"}",
                        fontFamily: regular,
                        textColor: Colors.white,
                        context: context),
                    onPressed: () async {
                      await controller.updateDriverLicense();
                    },
                    context: context,
                    btnRadius: 5.0,
                  ),
                ),
              ),
              if (controller.isOverlayLoading.value == true) ...[
                overlayWidget(context)
              ]
            ],
          );
        }
      }),
    );
  }
}
