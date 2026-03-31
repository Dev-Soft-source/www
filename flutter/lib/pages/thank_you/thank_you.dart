import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/thank_you/ThankYouController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class ThankYouPage extends StatelessWidget {
  const ThankYouPage({super.key});

  @override
  Widget build(BuildContext context) {
    final ThankYouController controller = Get.isRegistered<ThankYouController>()
        ? Get.find<ThankYouController>()
        : Get.put(ThankYouController());
    return PopScope(
        canPop: false,
        onPopInvoked: (confirmed) {
          if (Get.parameters['type'] == "forgot_password") {
            Get.offNamed('/login');
          } else if (Get.parameters['type'] == "instantBooking" ||
              Get.parameters['type'] == "manualBooking") {
            controller.serviceController.navigationIndex.value = 1;
            Get.offNamed('/navigation');
          }
          if (Get.parameters['type'] == "topUp") {
            controller.serviceController.navigationIndex.value = 0;
            Get.offNamed('/navigation');
          }

          return;
        },
        child: Get.parameters['type'] == "instantBooking" ||
                Get.parameters['type'] == "manualBooking" ||
                Get.parameters['type'] == "topUp"
            ? Scaffold(body: Obx(() {
                if (controller.errorStateManager.isLoading.value) {
                  return Center(child: progressCircularWidget(context));
                } else if (controller.errorStateManager.hasError.value) {
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
                            ? Center(
                                child: txt20Size(
                                    context: context,
                                    title: controller.serviceController
                                        .thankYouMessage.value,
                                    fontFamily: regular))
                            : Get.parameters['type'] == "manualBooking"
                                ? Center(
                                    child: txt20Size(
                                        context: context,
                                        title: controller.serviceController
                                            .thankYouMessage.value,
                                        fontFamily: regular))
                                : Get.parameters['type'] == "topUp"
                                    ? Center(
                                        child: txt20Size(
                                            context: context,
                                            title: controller.serviceController
                                                .thankYouMessage.value,
                                            fontFamily: regular))
                                    : const SizedBox(),
                        const SizedBox(),
                        25.heightBox,
                        SizedBox(
                          width: context.screenWidth,
                          height: 50.0,
                          child: elevatedButtonWidget(
                              textWidget: txt22Size(
                                  context: context,
                                  title: Get.parameters['type'] ==
                                          "forgot_password"
                                      ? "${controller.labelTextDetail['login_btn_label'] ?? "Login"}"
                                      : Get.parameters['type'] ==
                                                  "instantBooking" ||
                                              Get.parameters['type'] ==
                                                  "manualBooking" ||
                                              Get.parameters['type'] == "topUp"
                                          ? "${controller.labelTextDetail['done_btn_label'] ?? "Done"}"
                                          : "",
                                  textColor: Colors.white),
                              context: context,
                              onPressed: () {
                                if (Get.parameters['type'] ==
                                    "forgot_password") {
                                  Get.offAllNamed('/login');
                                } else if (Get.parameters['type'] ==
                                        "instantBooking" ||
                                    Get.parameters['type'] == "manualBooking") {
                                  controller.serviceController.navigationIndex
                                      .value = 1;
                                  Get.offNamed('/navigation');
                                } else if (Get.parameters['type'] == "topUp") {
                                  controller.serviceController.navigationIndex
                                      .value = 0;
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
                          title:
                              "${controller.labelTextDetail['account_close_heading'] ?? 'Account Closed'}",
                          context: context)),
                    ),
                    body: Obx(() {
                      if (controller.errorStateManager.isLoading.value) {
                        return Center(child: progressCircularWidget(context));
                      } else if (controller.errorStateManager.hasError.value) {
                        return ErrorStateWidget(
                          message:
                              controller.errorStateManager.errorMessage.value,
                          errorType:
                              controller.errorStateManager.errorType.value,
                          onRetry: () {
                            if (controller.errorStateManager.onRetry.value !=
                                null) {
                              controller.errorStateManager.onRetry.value!();
                            }
                          },
                        );
                      } else if (controller.isLoading.value == true) {
                        return Center(child: progressCircularWidget(context));
                      } else {
                        return Container(
                            padding:
                                EdgeInsets.all(getValueForScreenType<double>(
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
                                  Center(
                                      child: txt20Size(
                                          context: context,
                                          title:
                                              "${controller.labelTextDetail['close_account_message'] ?? "Your account is now closed. We're sad to see you go and wish you all the best. Safe travels — and see you on the road!"}",
                                          fontFamily: regular)),
                                  10.heightBox,
                                  elevatedButtonWidget(
                                    context: context,
                                    textWidget: txt22Size(
                                        context: context,
                                        textColor: Colors.white,
                                        title:
                                            "${controller.labelTextDetail['good_bye_btn_label'] ?? 'Goodbye from ProximaRide'}",
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
                              ? "${controller.labelTextDetail['rest_password_btn_label'] ?? "Reset Password Email Sent"}"
                              : "",
                          context: context)),
                      leading: null,
                    ),
                    body: Obx(() {
                      if (controller.errorStateManager.isLoading.value) {
                        return Center(child: progressCircularWidget(context));
                      } else if (controller.errorStateManager.hasError.value) {
                        return ErrorStateWidget(
                          message:
                              controller.errorStateManager.errorMessage.value,
                          errorType:
                              controller.errorStateManager.errorType.value,
                          onRetry: () {
                            if (controller.errorStateManager.onRetry.value !=
                                null) {
                              controller.errorStateManager.onRetry.value!();
                            }
                          },
                        );
                      } else if (controller.isLoading.value == true) {
                        return Center(child: progressCircularWidget(context));
                      } else {
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
                                  ? Center(
                                      child: txt20Size(
                                          context: context,
                                          title:
                                              "${controller.labelTextDetail['forget_password_message'] ?? "We have just sent you an email with a password reset link. Please follow the instructions in it"}",
                                          fontFamily: regular))
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
                    })));
  }
}


