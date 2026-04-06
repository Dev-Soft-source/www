import 'package:flutter/material.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/stages/StageTowController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/step_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:url_launcher/url_launcher.dart';
import '../widgets/image_upload_bottom_sheet.dart';
import '../widgets/image_upload_widget.dart';
import '../widgets/tool_tip.dart';

/// Opens `<a href>` from stage-two HTML: in-app routes (same as signup agreements) when possible,
/// otherwise external browser.
Future<void> launchStageTwoHtmlLink(String? url) async {
  if (url == null || url.isEmpty) return;
  final trimmed = url.trim();
  if (trimmed.startsWith('#')) return;

  final normalizedLink = trimmed.toLowerCase();
  final uri = Uri.tryParse(trimmed);
  final path = (uri?.path ?? normalizedLink).toLowerCase();

  if (path.contains('disclaimer') || normalizedLink.contains('disclaimer')) {
    Get.toNamed('/disclaimer');
    return;
  }
  if (normalizedLink.contains('term_condition') ||
      path.contains('term-condition') ||
      path.contains('terms-and-conditions')) {
    Get.toNamed('/term_condition');
    return;
  }
  if (normalizedLink.contains('term_of_use') ||
      path.contains('term-of-use') ||
      path.contains('terms-of-use')) {
    Get.toNamed('/term_of_use');
    return;
  }
  if (normalizedLink.contains('privacy_policy') ||
      normalizedLink.contains('privacy-policy') ||
      path.contains('privacy_policy') ||
      path.contains('privacy-policy') ||
      path.contains('privacy')) {
    Get.toNamed('/privacy_policy');
    return;
  }
  if (path.contains('profile-photo-guidelines') ||
      path.contains('profile_photo_guidelines') ||
      normalizedLink.contains('profile-photo-guidelines') ||
      normalizedLink.contains('profile_photo_guidelines')) {
    Get.toNamed('/profile_photo_guidelines');
    return;
  }

  String href = trimmed;
  if (href.startsWith('//')) {
    href = 'https:$href';
  } else if (!RegExp(r'^[a-zA-Z][a-zA-Z\d+\-.]*:').hasMatch(href)) {
    if (href.startsWith('/')) {
      logger.error('Unhandled relative HTML href (add in-app route or use full URL): $href');
      return;
    }
    href = 'https://$href';
  }

  final externalUri = Uri.tryParse(href);
  if (externalUri == null) {
    logger.error('Invalid link URL: $url');
    return;
  }

  try {
    final launched = await launchUrl(
      externalUri,
      mode: LaunchMode.externalApplication,
    );
    if (!launched) {
      logger.error('launchUrl returned false for $externalUri');
    }
  } catch (e) {
    logger.error('Error launching URL $externalUri: $e');
  }
}

class StageTwo extends StatelessWidget {
  const StageTwo({super.key});

  @override
  Widget build(BuildContext context) {
    final StageTowController controller = Get.isRegistered<StageTowController>()
        ? Get.find<StageTowController>()
        : Get.put(StageTowController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => stepAppBarWidget(
              context: context,
              serviceController: controller.serviceController,
              langId: controller.serviceController.langId.value,
              langIcon: controller.serviceController.langIcon.value,
              screeWidth: context.screenWidth,
              page: "step2")),
          // leading: safeBackButton(context),
        ),
        body: Obx(() {
          if (controller.errorStateManager.hasError.value) {
            return ErrorStateWidget(
              message: controller.errorStateManager.errorMessage.value,
              errorType: controller.errorStateManager.errorType.value,
              onRetry:
                  controller.errorStateManager.onRetry.value! as VoidCallback,
            );
          } else if (controller.isLoading.value == true) {
            return Center(child: progressCircularWidget(context));
          } else {
            return SafeArea(
              child: Stack(
                children: [
                  Column(
                    children: [
                      Expanded(
                        child: Container(
                          padding: EdgeInsets.all(getValueForScreenType<double>(
                            context: context,
                            mobile: 15.0,
                            tablet: 15.0,
                          )),
                          child: SingleChildScrollView(
                            physics: const AlwaysScrollableScrollPhysics(),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              mainAxisAlignment: MainAxisAlignment.start,
                              children: [
                                Center(
                                    child: txt25Size(
                                        title: "${controller.labelTextDetail['main_heading'] ??
                                            "Step 2 of 5 - Profile Picture"}",

                                        //  }",
                                        context: context)),
                                15.heightBox,
                                Html(
                                  data:
                                      "${controller.labelTextDetail['sub_heading_text'] ?? "If you are signing up as a driver, then please note that to be eligible to post Pink Rides and Extra-Care Rides, you must upload your profile photo"}",
                                  style: {
                                    "body": Style(
                                        padding: HtmlPaddings.zero,
                                        margin: Margins.zero),
                                    'p': Style(
                                      fontSize: FontSize(20),
                                      padding: HtmlPaddings.zero,
                                      margin: Margins.zero,
                                    ),
                                    'div': Style(
                                      fontSize: FontSize(20),
                                      padding: HtmlPaddings.zero,
                                      margin: Margins.zero,
                                    ),
                                    'a': Style(
                                      color: Colors.blue,
                                      textDecoration: TextDecoration.underline,
                                    )
                                  },
                                  onLinkTap: (url, attributes, element) {
                                    launchStageTwoHtmlLink(url);
                                  },
                                ),
                                10.heightBox,
                                imageUploadWidget(
                                    context: context,
                                    onTap: () async {
                                      logger.info("Uploading image");

                                      await imageUploadBottomSheet(
                                          controller, context);
                                      logger.info("Image uploaded");
                                      logger.info("Errors: ${controller.errors}");
                                      if (controller.errors.firstWhereOrNull(
                                              (element) =>
                                                  element['title'] == "image") !=
                                          null) {
                                        controller.errors.remove(controller.errors
                                            .firstWhereOrNull((element) =>
                                                element['title'] == "image"));
                                      }
                                      controller.validateStageTwoFields();
                                    },
                                    title:
                                        "${controller.labelTextDetail['mobile_photo_label'] ?? "Upload profile photo."}",
                                    title1: "",
                                    title2:
                                        "(JPG, PNG, JPEG, and GIF. 10MB max.)",
                                    imageFile:
                                        controller.profileImageName.value == ""
                                            ? null
                                            : controller.profileImagePath.value,
                                    screenWidth: context.screenWidth),
                                if (controller.errors.firstWhereOrNull(
                                        (element) => element['title'] == "image") !=
                                    null) ...[
                                  toolTip(
                                      tip: controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] == "image"))
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
                        child: Row(
                          children: [
                            Expanded(
                              flex: 10,
                              child: elevatedButtonWidget(
                                  textWidget: txt22Size(
                                      title:
                                          "${controller.labelTextDetail['skip_button_label'] ?? "Skip"}",
                                      fontFamily: bold,
                                      textColor: Colors.white,
                                      context: context),
                                  onPressed: () async {
                                    await controller.setStageTwo(true);
                                  },
                                  btnColor: primaryColor,
                                  context: context,
                                  btnRadius: 5.0),
                            ),
                            const Spacer(
                              flex: 1,
                            ),
                            Expanded(
                              flex: 10,
                              child: elevatedButtonWidget(
                                  enabled: controller.isStageTwoValid.value,
                                  textWidget: txt22Size(
                                      title:
                                          "${controller.labelTextDetail['next_button_label'] ?? "Next"}",
                                      fontFamily: regular,
                                      textColor: Colors.white,
                                      context: context),
                                  onPressed: () async {
                                    await controller.setStageTwo(false);
                                  },
                                  context: context,
                                  btnRadius: 5.0),
                            ),
                          ],
                        )),
                    ],
                  ),
                  if (controller.isOverlayLoading.value == true) ...[
                    overlayWidget(context)
                  ]
                ],
              ),
            );
          }
        }));
  }
}



