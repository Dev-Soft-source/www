import 'package:flutter/material.dart';
import 'package:flutter_otp_text_field/flutter_otp_text_field.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/stages/StageFiveController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/step_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class StageFive extends StatelessWidget {
  const StageFive({super.key});

  @override
  Widget build(BuildContext context) {
    final StageFiveController controller = Get.isRegistered<StageFiveController>()
        ? Get.find<StageFiveController>()
        : Get.put(StageFiveController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => stepAppBarWidget(
            context: context,
            serviceController: controller.serviceController,
            langId: controller.serviceController.langId.value,
            langIcon: controller.serviceController.langIcon.value,
            screeWidth: context.screenWidth,
            page: "step5")),
      ),
      body: Obx(
        () {
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
                  SingleChildScrollView(
                    child: Column(
                      children: [
                        Container(
                          padding: EdgeInsets.all(getValueForScreenType<double>(
                            context: context,
                            mobile: 20.0,
                            tablet: 20.0,
                          )),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              Center(
                                  child: txt25Size(
                                      title:
                                          "${controller.labelTextDetail['main_heading'] ?? "Step 5 of 5 - Your Phone Number"}",
                                      context: context)),
                              10.heightBox,
                              txt18Size(
                                  title:
                                      "${controller.labelTextDetail['main_label'] ?? 'To be eligible to post "Pink rides" and "Extra-care rides", you must verify your phone number'}",
                                  context: context),
                              // Html(
                              //   data: controller
                              //           .labelTextDetail['main_label'] ??
                              //       'To be eligible to post "Pink rides" and "Extra-care rides", you must verify your phone number',
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
                              // txt16Size(
                              //     title: formatMessage(
                              //       "${controller.labelTextDetail['main_label'] ?? 'To be eligible to post "Pink rides" and "Extra-care rides", you must verify your phone number'}",
                              //     ),
                              //     fontFamily: regular,
                              //     textColor: textColor,
                              //     context: context),
                              20.heightBox,
                              Row(
                                children: [
                                  Expanded(
                                    flex: 5,
                                    child: txt20Size(
                                      title:
                                          "${controller.labelTextDetail['country_code_label'] ?? "Country code"}",
                                      fontFamily: regular,
                                      textColor: textColor,
                                      context: context,
                                    ),
                                  ),
                                  // 5.widthBox,
                                  Expanded(
                                    flex: 9,
                                    child: txt20Size(
                                      title:
                                          "${controller.labelTextDetail['phone_label'] ?? "Phone number"}",
                                      fontFamily: regular,
                                      textColor: textColor,
                                      context: context,
                                    ),
                                  ),
                                ],
                              ),
                              10.heightBox,
                              Row(
                                children: [
                                  Expanded(
                                    flex: 5,
                                    child: TextFormField(
                                      // maxLength: 3,

                                      readOnly:
                                          controller.finishBtn.value == true
                                              ? true
                                              : false,
                                      decoration: InputDecoration(
                                        // isDense: true,
                                        counterText: "",
                                        enabledBorder: OutlineInputBorder(
                                            borderRadius:
                                                BorderRadius.circular(5.0),
                                            borderSide: BorderSide(
                                                color: Colors.grey.shade400,
                                                style: BorderStyle.solid,
                                                width: 1)),
                                        focusedBorder: OutlineInputBorder(
                                            borderRadius:
                                                BorderRadius.circular(5.0),
                                            borderSide: const BorderSide(
                                                color: primaryColor)),
                                        filled: true,
                                        fillColor: inputColor,
                                        // contentPadding: const EdgeInsets.symmetric(
                                        //     vertical: 0.0, horizontal: 8.0),
                                      ),
                                      controller: controller
                                          .countryCodeTextEditingController,

                                      style: const TextStyle(
                                          fontSize: 18, fontFamily: regular),
                                      keyboardType:
                                          TextInputType.visiblePassword,
                                      textInputAction: TextInputAction.done,
                                      onChanged: (value) {
                                        if (value.isNotEmpty &&
                                            (!RegExp(r'^[0-9+]*$')
                                                    .hasMatch(value) ||
                                                (value.indexOf('+') > 0) ||
                                                (value.indexOf('+') !=
                                                    value.lastIndexOf('+')))) {
                                          controller
                                                  .countryCodeTextEditingController
                                                  .text =
                                              value.substring(
                                                  0, value.length - 1);
                                        }
                                        if (controller.errors.firstWhereOrNull(
                                                (element) =>
                                                    element['title'] ==
                                                    "code") !=
                                            null) {
                                          controller.errors.remove(controller
                                              .errors
                                              .firstWhereOrNull((element) =>
                                                  element['title'] == "code"));
                                        }
                                      },
                                    ),
                                  ),
                                  5.widthBox,

                                  Expanded(
                                    flex: 9,
                                    child: TextFormField(
                                      maxLength: 15,
                                      readOnly:
                                          controller.finishBtn.value == true
                                              ? true
                                              : false,
                                      decoration: InputDecoration(
                                        counter: const SizedBox.shrink(),
                                        // isDense: true,
                                        hintText: "(with area code)",
                                        hintStyle:
                                            appPlaceholderTextStyle().copyWith(
                                          fontSize: 18,
                                          fontFamily: regular,
                                        ),
                                        enabledBorder: OutlineInputBorder(
                                            borderRadius:
                                                BorderRadius.circular(5.0),
                                            borderSide: BorderSide(
                                                color: Colors.grey.shade400,
                                                style: BorderStyle.solid,
                                                width: 1)),
                                        focusedBorder: OutlineInputBorder(
                                            borderRadius:
                                                BorderRadius.circular(5.0),
                                            borderSide: const BorderSide(
                                                color: primaryColor)),
                                        filled: true,
                                        fillColor: inputColor,
                                        // contentPadding: const EdgeInsets.symmetric(
                                        //     vertical: 0.0, horizontal: 8.0),
                                      ),
                                      controller: controller
                                          .phoneNumberTextEditingController,
                                      style: const TextStyle(
                                          fontSize: 18, fontFamily: regular),
                                      keyboardType: TextInputType.number,
                                      textInputAction: TextInputAction.done,
                                      onChanged: (value) {
                                        if (value.isNotEmpty &&
                                            !RegExp(r'^[0-9]*$')
                                                .hasMatch(value)) {
                                          // Filter out all non-numeric characters
                                          String filteredValue =
                                              value.replaceAll(
                                                  RegExp(r'[^0-9]'), '');

                                          // Update the text field with only numeric characters
                                          controller
                                              .phoneNumberTextEditingController
                                              .value = TextEditingValue(
                                            text: filteredValue,
                                            selection: TextSelection.collapsed(
                                              offset: filteredValue.length,
                                            ),
                                          );
                                        }
                                        if (controller.errors.firstWhereOrNull(
                                                (element) =>
                                                    element['title'] ==
                                                    "number") !=
                                            null) {
                                          controller.errors.remove(controller
                                              .errors
                                              .firstWhereOrNull((element) =>
                                                  element['title'] ==
                                                  "number"));
                                        }
                                      },
                                    ),
                                  ),
                                  // const Spacer(flex: 2,),
                                ],
                              ),
                              Row(
                                children: [
                                  if (controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] == "code") !=
                                      null) ...[
                                    // toolTip(
                                    //     tip: controller.errors.firstWhereOrNull(
                                    //         (element) => element['title'] == "code"))
                                    toolTip(
                                        tip: "Country Code is required",
                                        type: 'string')
                                  ],
                                  if (controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] == "number") !=
                                      null) ...[
                                    const Spacer(),
                                    // toolTip(
                                    //     tip: controller.errors.firstWhereOrNull(
                                    //         (element) =>
                                    //             element['title'] == "number"))
                                    toolTip(
                                        tip: "Phone Number is required",
                                        type: 'string')
                                  ],
                                ],
                              ),
                              30.heightBox,
                              if (controller.finishBtn.value == true) ...[
                                // txt22Size(
                                //     title:
                                //         "${controller.labelTextDetail['verify_code_label'] ?? 'Please enter the four digit code you received on your phone number'}",
                                //     fontFamily: regular,
                                //     textColor: textColor,
                                //     context: context),
                                // 10.heightBox,
                                Center(
                                  child: txt20Size(
                                      title: "Enter Verification Code",
                                      // "${controller.labelTextDetail['enter_code_label'] ?? 'Enter Code'}",
                                      fontFamily: bold,
                                      textColor: textColor,
                                      context: context),
                                ),
                                20.heightBox,
                                OtpTextField(
                                  filled: true,
                                  fillColor: Colors.black12,
                                  margin: const EdgeInsets.only(right: 20.0),
                                  showFieldAsBox: true,
                                  decoration: const InputDecoration(),
                                  onSubmit: (var verify) {
                                    controller.updateVerificationCodeEntered(
                                        verify.toString());
                                  },
                                ),
                                10.heightBox,
                                Center(
                                  child: txt20Size(
                                      title:
                                          "${controller.labelTextDetail['request_code_label'] ?? 'You can request a new code in'} ${controller.secondsRemaining.value} ${controller.labelTextDetail['second_label'] ?? 'seconds'}",
                                      textColor: textColor,
                                      context: context),
                                ),
                                30.heightBox,
                                Center(
                                  child: SizedBox(
                                    width: context.screenWidth / 2,
                                    height: 50,
                                    child: elevatedButtonWidget(
                                      enabled:
                                          controller.secondsRemaining.value == 0,
                                      textWidget: txt22Size(
                                          title:
                                              "${controller.labelTextDetail['send_button_label'] ?? "Resend code"}",
                                          textColor: controller
                                                      .secondsRemaining.value ==
                                                  0
                                              ? Colors.white
                                              : Colors.black26,
                                          context: context,
                                          fontFamily: regular),
                                      onPressed: () async {
                                        await controller.sendVerificationCode();
                                      },
                                      btnColor:
                                          controller.secondsRemaining.value == 0
                                              ? btnPrimaryColor
                                              : Colors.black12,
                                    ),
                                  ),
                                ),
                              ]
                            ],
                          ),
                        ),
                        100.heightBox,
                      ],
                    ),
                  ),
                  if (controller.isOverlayLoading.value == true) ...[
                    overlayWidget(context)
                  ]
                ],
              ),
            );
          }
        },
      ),
      bottomNavigationBar: AnimatedPadding(
          duration: const Duration(milliseconds: 200),
          curve: Curves.easeOut,
          padding:
              EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
          child: SafeArea(
            top: false,
            child: Obx(() => Container(
                width: context.screenWidth,
                padding: const EdgeInsets.all(15.0),
                color: Colors.grey.shade100,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    // 1) Verify / Finish
                    SizedBox(
                      width: double.infinity,
                      child: elevatedButtonWidget(
                        enabled: controller.finishBtn.value == false
                            ? controller.isPhoneFormValid.value
                            : controller.isVerificationCodeEntered.value,
                        textWidget: txt22Size(
                            title: controller.finishBtn.value == false
                                ? "${controller.labelTextDetail['verify_button_label'] ?? "Verify"}"
                                : "Finish",
                            // : "${controller.labelTextDetail['save_button_label'] ?? "Finish"}",
                            textColor: Colors.white,
                            context: context,
                            fontFamily: regular),
                        onPressed: () async {
                          if (controller.finishBtn.value == false) {
                            await controller.sendVerificationCode();
                          } else {
                            await controller.verifyPhoneNumber();
                          }
                        },
                      ),
                    ),
                    5.heightBox,
                    // 2) Save Unverified (save phone without verification)
                    SizedBox(
                      width: double.infinity,
                      child: elevatedButtonWidget(
                        enabled: controller.isPhoneFormValid.value,
                        textWidget: txt22Size(
                            title: "Save Unverified",
                            textColor: Colors.white,
                            context: context,
                            fontFamily: regular),
                        onPressed: () async {
                          await controller.setStageFive(false);
                        },
                        btnColor: primaryColor,
                      ),
                    ),
                    5.heightBox,
                    // 3) Skip for Now
                    SizedBox(
                      width: double.infinity,
                      child: elevatedButtonWidget(
                        textWidget: txt22Size(
                            title: controller
                                    .labelTextDetail['skip_button_label'] ??
                                "Skip for Now",
                            // "${controller.labelTextDetail['skip_button_label'] ?? "Skip for Now"}",
                            textColor: Colors.white,
                            context: context,
                            fontFamily: bold),
                        onPressed: () async {
                          controller.setStageFive(true);
                        },
                        btnColor: primaryColor,
                      ),
                    ),
                  ],
                ))),
          )),
    );
  }
}


