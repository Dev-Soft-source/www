import 'package:flutter/material.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/stages/StageFourController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/image_upload_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/image_upload_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/step_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class StageFour extends StatelessWidget {
  const StageFour({super.key});

  @override
  Widget build(BuildContext context) {
    final StageFourController controller = Get.isRegistered<StageFourController>()
        ? Get.find<StageFourController>()
        : Get.put(StageFourController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        leading: IconButton(
            icon: const Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () {
              Navigator.of(context).maybePop();
            },
          ),
        title: Obx(() => stepAppBarWidget(
            context: context,
            serviceController: controller.serviceController,
            langId: controller.serviceController.langId.value,
            langIcon: controller.serviceController.langIcon.value,
            screeWidth: context.screenWidth,
            page: "step4")),
      ),
      body: Obx(() {
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
        } else if (controller.isLoading.value == true) {
          return Center(child: progressCircularWidget(context));
        } else {
          return SafeArea(
            child: Stack(
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
                      children: [
                        Center(
                          child: txt25Size(
                            title: controller.step4MainHeading.value,
                            textColor: primaryColor,
                            context: context,
                          ),
                        ),
                        10.heightBox,
                        Html(
                          data:
                              "<p>To post Pink Rides or Extra-Care Rides, you must upload your <strong>valid driver&rsquo;s license</strong>. This helps us verify that all drivers meet our community standards.</p><p>&nbsp;</p><p>If you&rsquo;re planning to use ProximaRide as a&nbsp;<strong>passenger only</strong>, this step does not apply to you. You may simply click &ldquo;Skip&rdquo; below</p>",
                          style: {
                            "body": Style(
                                padding: HtmlPaddings.zero,
                                margin: Margins.zero),
                            'p': Style(
                              fontSize: FontSize(18),
                              fontFamily: carlito,
                              padding: HtmlPaddings.zero,
                              margin: Margins.zero,
                              textAlign: TextAlign.justify
                            ),
                            'div': Style(
                              fontSize: FontSize(20),
                              padding: HtmlPaddings.zero,
                              margin: Margins.zero,
                            )
                          },
                        ),
                        30.heightBox,
                        imageUploadWidget(
                          context: context,
                          onTap: () async {
                            controller.errors.removeWhere(
                                (element) => element['title'] == "driver_liscense");
                            controller.imageType.value = 2;
                            await imageUploadBottomSheet(controller, context);
                          },
                          title: "Upload Driver's License",
                          title1: "",
                          title2: "(JPG, PNG, JPEG, and GIF. 10MB max.)",
                          imageFile: controller.driverLicenseName.value == ""
                              ? null
                              : controller.driverLicensePath.value,
                          screenWidth: context.screenWidth,
                          isError: controller.errors.firstWhereOrNull(
                                  (element) =>
                                      element['title'] == "driver_liscense") !=
                              null,
                        ),
                        if (controller.errors.firstWhereOrNull((element) =>
                                element['title'] == "driver_liscense") !=
                            null) ...[
                          toolTip(
                              tip: "Driver's License is required",
                              type: 'string')
                        ],
                        20.heightBox,
                        Row(
                          children: [
                            Expanded(
                              child: elevatedButtonWidget(
                                enabled: controller.isLicenseFormValid.value,
                                textWidget: txt22Size(
                                    title:
                                        "${controller.labelTextDetail['save_button_label'] ?? "Save & Continue"}",
                                    textColor: Colors.white,
                                    context: context,
                                    fontFamily: regular),
                                onPressed: () async {
                                  if (controller.validateLicenseFields()) {
                                    controller.isLicenseSkipped.value = false;
                                    await controller.submitFinalForm();
                                  }
                                },
                              ),
                            ),
                          ],
                        ),
                        10.heightBox,
                        Row(
                          children: [
                            Expanded(
                              child: elevatedButtonWidget(
                                textWidget: txt22Size(
                                    title:
                                        "${controller.labelTextDetail['skip_button_label'] ?? "Skip"}",
                                    textColor: Colors.white,
                                    context: context,
                                    fontFamily: bold),
                                onPressed: () async {
                                  controller.isLicenseSkipped.value = true;
                                  await controller.submitFinalForm();
                                },
                                btnColor: primaryColor,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
                if (controller.isOverlayLoading.value == true) ...[
                  overlayWidget(context)
                ]
              ],
            ),
          );
        }
      }),
    );
  }
}


