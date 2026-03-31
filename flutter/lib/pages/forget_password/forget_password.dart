import 'package:flutter/material.dart';
// import 'package:flutter_html/flutter_html.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/forget_password/ForgetPasswordController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class ForgetPasswordPage extends StatelessWidget {
  const ForgetPasswordPage({super.key});

  @override
  Widget build(BuildContext context) {
    final ForgetPasswordController controller = Get.isRegistered<ForgetPasswordController>()
        ? Get.find<ForgetPasswordController>()
        : Get.put(ForgetPasswordController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['main_heading'] ?? "Forgot password?"}",
              context: context)),
          leading: safeBackButton(context),
        ),
        body: Obx(() => controller.errorStateManager.isLoading.value
            ? Center(child: progressCircularWidget(context))
            : controller.errorStateManager.hasError.value
                ? ErrorStateWidget(
                    message: controller.errorStateManager.errorMessage.value,
                    errorType: controller.errorStateManager.errorType.value,
                    onRetry: () {
                      if (controller.errorStateManager.onRetry.value != null) {
                        controller.errorStateManager.onRetry.value!();
                      }
                    },
                  )
                : controller.isLoading.value == true
                    ? Center(child: progressCircularWidget(context))
                    : SafeArea(
                        child: Stack(
                          children: [
                            Container(
                              padding:
                                  EdgeInsets.all(getValueForScreenType<double>(
                                context: context,
                                mobile: 15.0,
                                tablet: 15.0,
                              )),
                              child: SingleChildScrollView(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  mainAxisAlignment: MainAxisAlignment.start,
                                  children: [
                                    txt20Size(
                                        title:
                                            "${controller.labelTextDetail['main_label'] ?? "Please enter the e-mail you used to sign up"}",
                                        fontFamily: regular,
                                        textColor: textColor,
                                        context: context),
                                    // Html(
                                    //   data: controller.labelTextDetail['main_label'] ??
                                    //       "Please enter the e-mail you used to sign up",
                                    //   style: {
                                    //     "body": Style(
                                    //         padding: HtmlPaddings.zero,
                                    //         margin: Margins.zero),
                                    //     'p': Style(
                                    //       fontSize: FontSize(20),
                                    //       padding: HtmlPaddings.zero,
                                    //       margin: Margins.zero,
                                    //     ),
                                    //     'div': Style(
                                    //       fontSize: FontSize(20),
                                    //       padding: HtmlPaddings.zero,
                                    //       margin: Margins.zero,
                                    //     )
                                    //   },
                                    // ),
                                    // txt18Size(
                                    //     title:
                                    //         "${controller.labelTextDetail['main_label'] ?? "Please enter the e-mail you used to sign up"}",
                                    //     fontFamily: regular,
                                    //     textColor: textColor,
                                    //     context: context),
                                    5.heightBox,
                                    fieldsWidget(
                                        textController: controller
                                            .emailTextEditingController,
                                        fieldType: "email",
                                        readonly: false,
                                        fontFamily: regular,
                                        fontSize: 18.0,
                                        placeHolder: "",
                                        onChanged: (value) {
                                          if (controller.errors
                                                  .firstWhereOrNull((element) =>
                                                      element['title'] ==
                                                      "email") !=
                                              null) {
                                            controller.errors.remove(controller
                                                .errors
                                                .firstWhereOrNull((element) =>
                                                    element['title'] ==
                                                    "email"));
                                          }
                                        }),
                                    if (controller.errors.firstWhereOrNull(
                                            (element) =>
                                                element['title'] == "email") !=
                                        null) ...[
                                      toolTip(
                                          tip: controller.errors
                                              .firstWhereOrNull((element) =>
                                                  element['title'] == "email"))
                                    ],
                                  ],
                                ),
                              ),
                            ),
                            Align(
                              alignment: Alignment.bottomCenter,
                              child: Container(
                                padding: EdgeInsets.all(
                                    getValueForScreenType<double>(
                                  context: context,
                                  mobile: 15.0,
                                  tablet: 15.0,
                                )),
                                width: context.screenWidth,
                                height: 75,
                                child: elevatedButtonWidget(
                                    textWidget: txt22Size(
                                        title:
                                            "${controller.labelTextDetail['button_label'] ?? "Reset password"}",
                                        fontFamily: regular,
                                        textColor: Colors.white,
                                        context: context),
                                    onPressed: () async {
                                      await controller.forgetPassword();
                                    },
                                    context: context,
                                    btnRadius: 5.0),
                              ),
                            ),
                            if (controller.isOverlayLoading.value == true) ...[
                              overlayWidget(context)
                            ]
                          ],
                        ),
                      )),
      );
  }
}



