import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/thank_you/ThankYouController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class ThankYouPage extends GetView<ThankYouController> {
  const ThankYouPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(ThankYouController());
    return PopScope(
        canPop: false,
        onPopInvoked: (confirmed) {
          if (Get.parameters['type'] == "forgot_password") {
            Get.offNamed('/login');
          } else if (Get.parameters['type'] == "instantBooking" ||
              Get.parameters['type'] == "manualBooking") {
            controller.serviceController.navigationIndex.value = 1;
            Get.offNamed('/navigation');
          }if(Get.parameters['type'] == "topUp"){
            controller.serviceController.navigationIndex.value = 0;
            Get.offNamed('/navigation');
          }

          return;
        },
        child: Get.parameters['type'] == "instantBooking" ||
                Get.parameters['type'] == "manualBooking" || Get.parameters['type'] == "topUp"
            ? Scaffold(
                body: Obx(() {
                  if(controller.isLoading.value == true){
                    return Center(child: progressCircularWidget(context));
                  }else{
                    return Container(
                      padding: EdgeInsets.all(getValueForScreenType<double>(
                        context: context,
                        mobile: 15.0,
                        tablet: 15.0,
                      )),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        mainAxisAlignment: MainAxisAlignment.center,
                        mainAxisSize: MainAxisSize.max,
                        children: [
                          Image.asset(
                            verifiedImage,
                            width: 68,
                            height: 68,
                          ),
                          10.heightBox,
                          Get.parameters['type'] == "instantBooking"
                              ? txt25SizeCenter(
                              context: context,
                              title:
                              controller.serviceController.thankYouMessage.value,
                              fontFamily: regular)
                              : Get.parameters['type'] == "manualBooking"
                              ? txt25SizeCenter(
                              context: context,
                              title:
                              controller.serviceController.thankYouMessage.value,
                              fontFamily: regular)
                              : Get.parameters['type'] == "topUp"
                              ? txt25SizeCenter(
                              context: context,
                              title:
                              controller.serviceController.thankYouMessage.value,
                              fontFamily: regular)
                              : const SizedBox(), const SizedBox(),
                          25.heightBox,
                          SizedBox(
                            width: context.screenWidth,
                            height: 50.0,
                            child: elevatedButtonWidget(
                                textWidget: txt22Size(
                                    context: context,
                                    title: Get.parameters['type'] == "forgot_password"
                                        ? "${controller.labelTextDetail['login_btn_label'] ?? "Login"}"
                                        : Get.parameters['type'] ==
                                        "instantBooking" ||
                                        Get.parameters['type'] ==
                                            "manualBooking" || Get.parameters['type'] == "topUp"
                                        ? "${controller.labelTextDetail['done_btn_label'] ?? "Done"}"
                                        : "",
                                    textColor: Colors.white),
                                context: context,
                                onPressed: () {
                                  if (Get.parameters['type'] == "forgot_password") {
                                    Get.offAllNamed('/login');
                                  } else if (Get.parameters['type'] ==
                                      "instantBooking" ||
                                      Get.parameters['type'] == "manualBooking") {
                                    controller.serviceController.navigationIndex.value = 1;
                                    Get.offNamed('/navigation');
                                  }else if(Get.parameters['type'] == "topUp"){
                                    controller.serviceController.navigationIndex.value = 0;
                                    Get.offNamed('/navigation');
                                  }
                                }),
                          )
                        ],
                      ),
                    );
                  }
                }))
            : Get.parameters['type'] == "close_account"
                ? Scaffold(
                    appBar: AppBar(
                      backgroundColor: primaryColor,
                      title: Obx(() => secondAppBarWidget(
                          title: "${controller.labelTextDetail['account_close_heading'] ?? 'Account closed'}", context: context)),
                    ),
                    body: Obx(() {
                      if(controller.isLoading.value == true){
                        return Center(child: progressCircularWidget(context));
                      }else{
                        return Container(
                            padding: EdgeInsets.all(getValueForScreenType<double>(
                              context: context,
                              mobile: 15.0,
                              tablet: 15.0,
                            )),
                            child: Center(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.center,
                                mainAxisAlignment: MainAxisAlignment.center,
                                mainAxisSize: MainAxisSize.max,
                                children: [
                                  Image.asset(
                                    greenTick,
                                    width: 68,
                                    height: 68,
                                  ),
                                  10.heightBox,
                                  txt28SizeAlignCenter(
                                      context: context,
                                      title:
                                      "${controller.labelTextDetail['close_account_message'] ?? "Your account has been closed successfully. We are sorry to see you go, and we wish you all the best"}"),
                                  elevatedButtonWidget(
                                    context: context,
                                    textWidget: txt22Size(
                                        context: context,
                                        textColor: Colors.white,
                                        title: "${controller.labelTextDetail['good_bye_btn_label'] ?? 'Good bye'}",
                                        fontFamily: regular),
                                    onPressed: () {
                                      Get.offAllNamed('/signup');
                                    },
                                  ),
                                ],
                              ),
                            ));
                      }
                    }))
                : Scaffold(
                    appBar: AppBar(
                      backgroundColor: primaryColor,
                      title: Obx(() => secondAppBarWidget(
                          title: Get.parameters['type'] == "forgot_password"
                              ? "${controller.labelTextDetail['rest_password_btn_label'] ?? "Reset password"}"
                              : "",
                          context: context)),
                      leading: null,
                    ),
                    body: Obx(() {
                      if(controller.isLoading.value == true){
                        return Center(child: progressCircularWidget(context));
                      }else{
                        return Container(
                          padding: EdgeInsets.all(getValueForScreenType<double>(
                            context: context,
                            mobile: 15.0,
                            tablet: 15.0,
                          )),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.center,
                            mainAxisAlignment: MainAxisAlignment.center,
                            mainAxisSize: MainAxisSize.max,
                            children: [
                              Image.asset(
                                greenTick,
                                width: 68,
                                height: 68,
                              ),
                              10.heightBox,
                              Get.parameters['type'] == "forgot_password"
                                  ? txt25SizeCenter(
                                  context: context,
                                  title:
                                  "${controller.labelTextDetail['forget_password_message'] ?? "We have just sent you an email with a password reset link. Please follow the instructions in it"}",
                                  fontFamily: regular)
                                  : const SizedBox(),
                              25.heightBox,
                              SizedBox(
                                width: context.screenWidth,
                                height: 50.0,
                                child: elevatedButtonWidget(
                                    textWidget: txt22Size(
                                        context: context,
                                        title: Get.parameters['type'] ==
                                            "forgot_password"
                                            ? "${controller.labelTextDetail['forget_close_btn_label'] ?? "Close"}"
                                            : "",
                                        textColor: Colors.white),
                                    context: context,
                                    onPressed: () {
                                      if (Get.parameters['type'] ==
                                          "forgot_password") {
                                        Get.offAllNamed('/login');
                                      }
                                    }),
                              )
                            ],
                          ),
                        );
                      }
                    })
        )
    );
  }
}
