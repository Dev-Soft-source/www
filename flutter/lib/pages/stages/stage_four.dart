import 'package:flutter/material.dart';
import 'package:flutter_otp_text_field/flutter_otp_text_field.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/stages/StageFourController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/step_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class StageFour extends GetView<StageFourController> {
  const StageFour({super.key});


  @override
  Widget build(BuildContext context) {
    Get.put(StageFourController());
    return  Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => stepAppBarWidget(
              context: context, serviceController: controller.serviceController, langId: controller.serviceController.langId.value, langIcon: controller.serviceController.langIcon.value, screeWidth: context.screenWidth, page: "step4")),
        ),
        body:  Obx(() {
          if(controller.isLoading.value == true){
            return Center(child: progressCircularWidget(context));
          }else{
            return Stack(
              children: [
                SingleChildScrollView(
                  child: Container(
                    padding: EdgeInsets.all(getValueForScreenType<double>(
                      context: context,
                      mobile: 20.0,
                      tablet: 20.0,
                    )),
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
                        5.heightBox,
                        Center(child: txt25Size(title: "${controller.labelTextDetail['main_heading'] ?? "Step 4 of 4 - Contact Information"}", context: context)),
                        5.heightBox,
                        txt22Size(
                            title:
                            "${controller.labelTextDetail['main_label'] ?? 'To be eligible to post "Pink rides" and "Extra-care rides", you must verify your phone number'}",
                            fontFamily: regular,
                            textColor: textColor,
                            context: context),
                        20.heightBox,
                        Row(
                          children: [
                            Expanded(
                              flex: 5,
                              child: txt20Size(
                                title: "${controller.labelTextDetail['country_code_label'] ?? "Country code"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context,
                              ),
                            ),
                            5.widthBox,
                            Expanded(
                              flex: 9,
                              child: txt20Size(
                                title: "${controller.labelTextDetail['phone_label'] ?? "Phone number"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context,
                              ),
                            ),
                            const Spacer(flex: 2,),
                          ],
                        ),
                        Row(
                          children: [
                            Expanded(
                              flex: 5,
                              child: TextFormField(
                                maxLength: 3,
                                readOnly: controller.finishBtn.value == true ? true : false,
                                decoration: InputDecoration(
                                  counterText: "",
                                  enabledBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(5.0),
                                      borderSide: BorderSide(
                                          color: Colors.grey.shade400,
                                          style: BorderStyle.solid,
                                          width: 1)),
                                  focusedBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(5.0),
                                      borderSide:
                                      const BorderSide(color: primaryColor)),
                                  filled: true,
                                  fillColor: inputColor,
                                  contentPadding: const EdgeInsets.symmetric(
                                      vertical: 0.0, horizontal: 8.0),
                                ),
                                controller: controller.countryCodeTextEditingController,
                                style: const TextStyle(
                                    fontSize: 18, fontFamily: regular),
                                keyboardType: TextInputType.visiblePassword,
                                textInputAction: TextInputAction.done,
                                onChanged: (value){
                                  if (value.isNotEmpty &&
                                      (!RegExp(r'^[0-9+]*$').hasMatch(value) ||
                                          (value.indexOf('+') > 0) ||
                                          (value.indexOf('+') != value.lastIndexOf('+')))) {
                                    controller.countryCodeTextEditingController.text = value.substring(0, value.length - 1);
                                  }
                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "code") != null)
                                  {
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "code"));
                                  }
                                },
                              ),
                            ),
                            5.widthBox,
                            Expanded(
                              flex: 9,
                              child: TextFormField(
                                maxLength: 14,
                                readOnly: controller.finishBtn.value == true ? true : false,
                                decoration: InputDecoration(
                                  enabledBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(5.0),
                                      borderSide: BorderSide(
                                          color: Colors.grey.shade400,
                                          style: BorderStyle.solid,
                                          width: 1)),
                                  focusedBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(5.0),
                                      borderSide:
                                      const BorderSide(color: primaryColor)),
                                  filled: true,
                                  fillColor: inputColor,
                                  contentPadding: const EdgeInsets.symmetric(
                                      vertical: 0.0, horizontal: 8.0),
                                ),
                                controller: controller.phoneNumberTextEditingController,
                                style: const TextStyle(
                                    fontSize: 18, fontFamily: regular),
                                keyboardType: TextInputType.visiblePassword,
                                textInputAction: TextInputAction.done,
                                onChanged: (value){
                                  if (value.isNotEmpty && !RegExp(r'^[0-9]*$').hasMatch(value)) {
                                    controller.phoneNumberTextEditingController.text = value.substring(0, value.length - 1);
                                  }
                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "number") != null)
                                  {
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "number"));
                                  }
                                },
                              ),
                            ),
                            // const Spacer(flex: 2,),
                          ],
                        ),
                        Row(
                          children: [
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "code") != null) ...[
                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "code"))
                            ],
                            if( controller.errors.firstWhereOrNull((element) => element['title'] == "number") != null) ...[
                              const Spacer(),
                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "number"))
                            ],
                          ],
                        ),
                        10.heightBox,

                        if(controller.finishBtn.value == true)...[
                          txt22Size(
                              title:
                              "${controller.labelTextDetail['verify_code_label'] ?? 'Please enter the four digit code you received on your phone number'}",
                              fontFamily: regular,
                              textColor: textColor,
                              context: context),
                          10.heightBox,
                          Center(
                            child: txt24Size(
                                title: "${controller.labelTextDetail['enter_code_label'] ?? 'Enter Code'}",
                                fontFamily: bold,
                                textColor: textColor,
                                context: context),
                          ),
                          10.heightBox,
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
                          10.heightBox,
                          Center(
                            child: txt14Size(
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
                                textWidget: txt22Size(
                                    title: "${controller.labelTextDetail['send_button_label'] ?? "Resend code"}",
                                    textColor:
                                    controller.secondsRemaining.value == 0
                                        ? Colors.white
                                        : Colors.black26,
                                    context: context,
                                    fontFamily: regular),
                                onPressed: () {
                                  controller.secondsRemaining.value = 60;
                                  controller.sendVerificationCode();
                                },
                                btnColor: controller.secondsRemaining.value == 0
                                    ? btnPrimaryColor
                                    : Colors.black12,
                              ),
                            ),
                          ),
                        ]

                      ],
                    ),
                  ),
                ),
                Align(
                  alignment: Alignment.bottomCenter,
                  child: Container(
                    width: context.screenWidth,
                    padding: const EdgeInsets.all(15.0),
                    color: Colors.grey.shade100,
                    child: Row(children: [
                      Expanded(
                        flex: 10,
                        child: elevatedButtonWidget(
                        textWidget: txt28Size(
                            title: "${controller.labelTextDetail['skip_button_label'] ?? "Skip"}",
                            textColor: Colors.white,
                            context: context,
                            fontFamily: regular),
                        onPressed: () async {
                          controller.setStageFour(true);
                        },
                        btnColor: primaryColor,
                      ),),
                      const Spacer(flex: 1,),
                      Expanded(
                        flex: 10,
                        child: elevatedButtonWidget(
                          textWidget: txt28Size(
                              title: controller.finishBtn.value ==  false ? "${controller.labelTextDetail['verify_button_label'] ?? "Verify"}" : "${controller.labelTextDetail['save_button_label'] ?? "Finish"}",
                              textColor: Colors.white,
                              context: context,
                              fontFamily: regular),
                          onPressed: () async {
                            controller.finishBtn.value ==  false ? controller.sendVerificationCode() :
                            controller.verifyPhoneNumber();
                          },
                        ),)

                    ],)
                  ),
                ),
                if (controller.isOverlayLoading.value == true) ...[overlayWidget(context)]
              ],
            );
          }
        })
    );
  }
}
