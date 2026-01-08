import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_phone_number/MyPhoneNumberController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:flutter_otp_text_field/flutter_otp_text_field.dart';

class PhoneNumberVerificationPage extends GetView<MyPhoneNumberController> {
  const PhoneNumberVerificationPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(MyPhoneNumberController());
    return Obx(() => Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: secondAppBarWidget(
              title: "${controller.labelTextDetail['verify_phone_number_heading'] ?? "Verify my phone number"}", context: context),
          leading: const BackButton(color: Colors.white),
        ),
        body: Stack(children: [
          Container(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: SingleChildScrollView(
              child: Column(
                children: [
                  Container(
                    padding: EdgeInsets.all(
                      getValueForScreenType<double>(
                        context: context,
                        mobile: 15.0,
                        tablet: 15.0,
                      ),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        txt22Size(
                            title:
                            "${controller.labelTextDetail['otp_code_description'] ?? 'Please enter the four digit code you received on your phone number +1 *** -*** -****'}",
                            fontFamily: regular,
                            textColor: textColor,
                            context: context),
                        20.heightBox,
                        Center(
                          child: txt24Size(
                              title: "${controller.labelTextDetail['enter_code_label'] ?? 'Enter Code'}",
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
                            controller.verificationCode = verify.toString();
                          },
                        ),
                        130.heightBox,
                        SizedBox(
                          width: context.screenWidth,
                          height: 50,
                          child: elevatedButtonWidget(
                            textWidget: txt22Size(
                                title: "${controller.labelTextDetail['verify_phone_number_label'] ?? "Verify phone number"}",
                                textColor: Colors.white,
                                context: context,
                                fontFamily: regular),
                            onPressed: () {
                              controller.isLoading.value = true;
                              controller.verifyPhoneNumber();
                            },
                          ),
                        ),
                        10.heightBox,
                        Center(
                          child: txt14Size(
                              title:
                                  '${controller.labelTextDetail['request_code_text'] ?? "You can request a new code in"} ${controller.secondsRemaining.value} ${controller.labelTextDetail['second_text'] ?? "seconds"}',
                              textColor: textColor,
                              context: context),
                        ),
                        30.heightBox,
                        Center(
                          child: SizedBox(
                            width: context.screenWidth / 2,
                            height: 50,
                            child: elevatedButtonWidget(
                              textWidget: txt22Size(
                                  title: "${controller.labelTextDetail['resend_code_btn_label'] ?? "Resend code"}",
                                  textColor:
                                      controller.secondsRemaining.value == 0
                                          ? Colors.white
                                          : Colors.black26,
                                  context: context,
                                  fontFamily: regular),
                              onPressed: () {
                                controller.secondsRemaining.value = 60;
                                controller.startTimer();
                                controller.sendVerificationCode();
                              },
                              btnColor: controller.secondsRemaining.value == 0
                                  ? primaryColor
                                  : Colors.black12,
                            ),
                          ),
                        ),
                        if (controller.errorList.isNotEmpty) ...[
                          ListView.builder(
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            itemCount: controller.errorList.length,
                            itemBuilder: (context, index) {
                              return Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.center,
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      const Icon(Icons.circle,
                                          size: 10, color: Colors.red),
                                      10.widthBox,
                                      Expanded(
                                          child: txt14Size(
                                              title:
                                                  "${controller.errorList[index]}",
                                              fontFamily: regular,
                                              textColor: Colors.red,
                                              context: context))
                                    ],
                                  ),
                                  5.heightBox,
                                ],
                              );
                            },
                          ),
                        ],
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
          if (controller.isLoading.value == true) ...[overlayWidget(context)]
        ])));
  }
}
