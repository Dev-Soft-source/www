
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/profile_photo/ProfilePhotoController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/image_upload_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/image_upload_widget.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';


class ProfilePhotoPage extends GetView<ProfilePhotoController> {
  const ProfilePhotoPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(ProfilePhotoController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "Profile photo"}", context: context)),
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
                  )
                  ),
                  child: Stack(
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          txt20Size(title: "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? "* Indicates required field"}", fontFamily: regular, context: context,textColor: Colors.red),
                          Row(
                            children: [
                              txt20Size(context: context, title: "${controller.labelTextDetail['main_heading'] ?? "Profile photo"}"),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                              10.widthBox,
                              Tooltip(
                                margin: EdgeInsets.fromLTRB(
                                    getValueForScreenType<double>(
                                      context: context,
                                      mobile: 15.0,
                                      tablet: 15.0,
                                    ),
                                    getValueForScreenType<double>(
                                      context: context,
                                      mobile: 0.0,
                                      tablet: 0.0,
                                    ),
                                    getValueForScreenType<double>(
                                      context: context,
                                      mobile: 15.0,
                                      tablet: 15.0,
                                    ),
                                    getValueForScreenType<double>(
                                      context: context,
                                      mobile: 0.0,
                                      tablet: 0.0,
                                    )),
                                triggerMode: TooltipTriggerMode.tap,
                                message: "${controller.labelTextDetail['mobile_upload_photo_tooltip'] ?? 'Upload photo'}",
                                textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                                showDuration: const Duration(days: 100),
                                waitDuration: Duration.zero,
                                child: Image.asset(infoImage,color: Colors.black, width: getValueForScreenType<double>(
                                  context: context,
                                  mobile: 20.0,
                                  tablet: 20.0,
                                ), height: getValueForScreenType<double>(
                                  context: context,
                                  mobile: 20.0,
                                  tablet: 20.0,
                                )),
                              )
                            ],
                          ),
                          15.heightBox,
                          txt16Size(title: "${controller.labelTextDetail['sub_heading_text'] ?? "If you are signing up as a driver, then please note that to be eligible to post Pink Rides and Extra-Care Rides, you must upload your profile photo"}", context: context),
                          10.heightBox,
                          if (controller.profileImagePathOriginalOld.value != "" &&
                              controller.profileImageName.value == "") ...[
                            InkWell(
                              onTap: (){
                                controller.serviceController.showImage.value = controller.profileImagePathOriginalOld.value;
                                Get.toNamed("/show_image");
                              },
                              child: Container(
                                padding: const EdgeInsets.all(1.0),
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(15.0),
                                  child: networkCacheImageWidget(
                                      controller.profileImagePathOld.value,
                                      BoxFit.fill,
                                      context.screenWidth,
                                      320.0),
                                ),
                              ),
                            ),
                          ]else...[
                            imageUploadWidget(
                                context: context,
                                onTap: () async{
                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "image") != null)
                                  {
                                    controller.errors.removeWhere((element) => element['title'] == "image");
                                  }
                                  await imageUploadBottomSheet(controller, context);
                                },
                                title: "${controller.labelTextDetail['upload_profile_photo_placeholder'] ?? "Upload profile photo."}",
                                title1: "${controller.labelTextDetail['choose_file_placeholder'] ?? "Choose file"}",
                                title2: "${controller.labelTextDetail['images_option_placeholder'] ?? "(Only JPG, PNG, JPEG and GIF are allowed. Max. 10MB)"}",
                                imageFile: controller.profileImageName.value == "" ? null : controller.profileImagePath.value,
                                screenWidth: context.screenWidth
                            ),
                          ],
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "image") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "image"))
                          ],
                          if (controller.profileImagePathOld.value != "" && controller.profileImagePath.value == "") ...[
                            10.heightBox,
                            Align(
                              alignment: Alignment.topRight,
                              child: elevatedButtonWidget(
                                  textWidget: txt22Size(title: "${controller.labelTextDetail['mobile_upload_new_image_button_text'] ?? "Upload new image"}",context: context, textColor: Colors.white),
                                  onPressed: () async{
                                    await imageUploadBottomSheet(
                                        controller, context);
                                  },
                                  context: context,
                                  btnColor: primaryColor
                              ),
                            )
                          ],
                        ],
                      ),
                    ],
                  )
              ),
              Align(
                alignment: Alignment.bottomCenter,
                child: Container(
                  padding: const EdgeInsets.all(15.0),
                  color: Colors.grey.shade100,
                  child: SizedBox(
                    width: context.screenWidth,
                    height: 50,
                    child: elevatedButtonWidget(
                      textWidget: txt28Size(title: "${controller.labelTextDetail['save_button_text'] ?? "Save"}", textColor: Colors.white, context: context, fontFamily: regular),
                      onPressed: () async{
                        await controller.uploadUserPhoto();
                      },
                    ),
                  ),
                ),
              ),
              if(controller.isOverlayLoading.value == true)...[
                overlayWidget(context),
              ]
            ],
          );
        }
      })
    );
  }
}

