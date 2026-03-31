import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/profile_photo/ProfilePhotoController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/app_html_text.dart';
import 'package:proximaride_app/pages/widgets/image_upload_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/image_upload_widget.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/logger_service.dart';

import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import '../widgets/tool_tip.dart';

class ProfilePhotoPage extends StatelessWidget {
  const ProfilePhotoPage({super.key});

  @override
  Widget build(BuildContext context) {
    final ProfilePhotoController controller = Get.isRegistered<ProfilePhotoController>()
        ? Get.find<ProfilePhotoController>()
        : Get.put(ProfilePhotoController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['main_heading'] ?? "Profile photo"}",
              context: context)),
          leading: safeBackButton(context),
        ),
        body: SafeArea(
          child: Obx(() {
            if (controller.errorStateManager.isLoading.value) {
              return Center(child: progressCircularWidget(context));
            } else if (controller.errorStateManager.hasError.value) {
              return ErrorStateWidget(
                errorType: controller.errorStateManager.errorType.value,
                message: controller.errorStateManager.errorMessage.value,
                onRetry: () =>
                    controller.errorStateManager.onRetry.value?.call(),
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
                        physics: const AlwaysScrollableScrollPhysics(),
                        padding: const EdgeInsets.only(bottom: 90.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                              // txt20Size(
                              //     title:
                              //         "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? "* Indicates required fields"}",
                              //     fontFamily: regular,
                              //     context: context,
                              //     textColor: Colors.red),
                              // 15.heightBox,
                              // Row(
                              //   children: [
                              //     txt20Size(
                              //         context: context,
                              //         title:
                              //             "${controller.labelTextDetail['main_heading'] ?? "Profile photo"}"),
                              //     txt20Size(
                              //         title: "*",
                              //         fontFamily: regular,
                              //         context: context,
                              //         textColor: Colors.red),
                              //     10.widthBox,
                              //     Tooltip(
                              //       margin: EdgeInsets.fromLTRB(
                              //           getValueForScreenType<double>(
                              //             context: context,
                              //             mobile: 15.0,
                              //             tablet: 15.0,
                              //           ),
                              //           getValueForScreenType<double>(
                              //             context: context,
                              //             mobile: 0.0,
                              //             tablet: 0.0,
                              //           ),
                              //           getValueForScreenType<double>(
                              //             context: context,
                              //             mobile: 15.0,
                              //             tablet: 15.0,
                              //           ),
                              //           getValueForScreenType<double>(
                              //             context: context,
                              //             mobile: 0.0,
                              //             tablet: 0.0,
                              //           )),
                              //       triggerMode: TooltipTriggerMode.tap,
                              //       message:
                              //           "${controller.labelTextDetail['mobile_upload_photo_tooltip'] ?? 'Upload photo'}",
                              //       textStyle: const TextStyle(
                              //           fontSize: 16, color: Colors.white),
                              //       showDuration: const Duration(days: 100),
                              //       waitDuration: Duration.zero,
                              //       child: Image.asset(infoImage,
                              //           color: Colors.black,
                              //           width: getValueForScreenType<double>(
                              //             context: context,
                              //             mobile: 20.0,
                              //             tablet: 20.0,
                              //           ),
                              //           height: getValueForScreenType<double>(
                              //             context: context,
                              //             mobile: 20.0,
                              //             tablet: 20.0,
                              //           )),
                              //     )
                              //   ],
                              // ),
                              // 15.heightBox,
                              // Main explanatory text: 20px body
                              AppHtmlText(
                                data:
                                    "${controller.labelTextDetail['sub_heading_text'] ?? "If you are signing up as a driver, then please note that to be eligible to post Pink Rides and Extra-Care Rides, you must upload your profile photo"}",
                                fontSize: 20,
                                fontFamily: regular,
                              ),

                              15.heightBox,
                              Row(
                                children: [
                                  // Base text: 20px
                                  txt20Size(title: "See ", context: context),
                                  // Linked part as "rich" text: 18px
                                  InkWell(
                                    onTap: () {
                                      Get.toNamed("/profile_photo_guidelines");
                                    },
                                    child: txt18Size(
                                      title:
                                          "ProximaRide Profile Photo Guidelines",
                                      context: context,
                                      textColor: primaryColor,
                                    ),
                                  ),
                                ],
                              ),

                              10.heightBox,
                              if (controller
                                          .profileImagePathOriginalOld.value !=
                                      "" &&
                                  controller.profileImageName.value == "") ...[
                                InkWell(
                                  onTap: () {
                                    controller
                                            .serviceController.showImage.value =
                                        controller
                                            .profileImagePathOriginalOld.value;

                                    logger.info(controller
                                        .profileImagePathOriginalOld.value);
                                    logger.info(
                                        controller.profileImagePathOld.value);
                                    Get.toNamed("/show_image");
                                  },
                                  child: Container(
                                    padding: const EdgeInsets.all(1.0),
                                    decoration: BoxDecoration(
                                        color: Colors.grey,
                                        borderRadius:
                                            BorderRadius.circular(15.0)),
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
                              ] else ...[
                                imageUploadWidget(
                                    context: context,
                                    onTap: () async {
                                      if (controller.errors.firstWhereOrNull(
                                              (element) =>
                                                  element['title'] ==
                                                  "image") !=
                                          null) {
                                        controller.errors.removeWhere(
                                            (element) =>
                                                element['title'] == "image");
                                      }
                                      await imageUploadBottomSheet(
                                          controller, context);
                                    },
                                    title:
                                        "${controller.labelTextDetail['upload_profile_photo_placeholder'] ?? "Upload profile photo."}",
                                    title1:
                                        "${controller.labelTextDetail['choose_file_placeholder'] ?? "Choose file"}",
                                    title2:
                                        "${controller.labelTextDetail['images_option_placeholder'] ?? "(Only JPG, PNG, JPEG and GIF are allowed. Max. 10MB)"}",
                                    imageFile:
                                        controller.profileImageName.value == ""
                                            ? null
                                            : controller.profileImagePath.value,
                                    screenWidth: context.screenWidth),
                              ],
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "image") !=
                                  null) ...[
                                toolTip(
                                    tip: controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] == "image"))
                              ],
                              if (controller.profileImagePathOld.value != "" &&
                                  controller.profileImagePath.value == "") ...[
                                10.heightBox,
                                Align(
                                  alignment: Alignment.center,
                                  child: elevatedButtonWidget(
                                      textWidget: txt22Size(
                                          title:
                                              "${controller.labelTextDetail['mobile_upload_new_image_button_text'] ?? "Upload new image"}",
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
                          ],
                        ),
                      )),
                  Align(
                    alignment: Alignment.bottomCenter,
                    child: Container(
                      padding: const EdgeInsets.all(15.0),
                      color: Colors.grey.shade100,
                      child: SizedBox(
                        width: context.screenWidth,
                        height: 50,
                        child: elevatedButtonWidget(
                          enabled: controller.profileImageName.value.isNotEmpty,
                          textWidget: txt22Size(
                              title:
                                  "${controller.labelTextDetail['save_button_text'] ?? "Save"}",
                              textColor: Colors.white,
                              context: context,
                              fontFamily: regular),
                          onPressed: () async {
                            await controller.uploadUserPhoto();
                          },
                        ),
                      ),
                    ),
                  ),
                  if (controller.isOverlayLoading.value == true) ...[
                    overlayWidget(context),
                  ]
                ],
              );
            }
          }),
        ));
  }
}



