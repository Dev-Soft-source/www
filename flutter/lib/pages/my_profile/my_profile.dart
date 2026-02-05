import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_profile/MyProfileController.dart';
import 'package:proximaride_app/pages/my_profile/widget/link_widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/main_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class MyProfilePage extends GetView<MyProfileController> {
  const MyProfilePage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(MyProfileController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => mainAppBarWidget(
              context,
              controller.serviceController.langId.value,
              controller.serviceController.langIcon.value,
              context.screenWidth,
              controller.serviceController)),
        ),
        body: SafeArea(
          child: Obx(() {
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
              return Stack(
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
                                  title:
                                      "${controller.labelTextDetail['main_heading'] ?? "Welcome to Your ProximaRide Profile"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context)),
                          10.heightBox,
                          Obx(() {
                            return linkWidget(
                                controller: controller,
                                imagePath:
                                    "${controller.serviceController.loginUserDetail['profile_image']}",
                                title:
                                    "${controller.serviceController.loginUserDetail['first_name']} ${controller.serviceController.loginUserDetail['last_name']}",
                                context: context,
                                index: 0,
                                onTap: () {
                                  Get.toNamed("/profile_detail/user/0/0");
                                },
                                textColor: primaryColor);
                          }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: profileSettingImage,
                              title:
                                  "${controller.labelTextDetail['profile_setting_label'] ?? "Profile settings"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/profile_setting");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: myWalletImage,
                              title:
                                  "${controller.labelTextDetail['my_wallet_label'] ?? "My wallet"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/my_wallet");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: paymentOptionImage,
                              title:
                                  "${controller.labelTextDetail['payment_options_label'] ?? "Payment Methods"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/payment_options");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: payoutAccountIcon,
                              title:
                                  "${controller.labelTextDetail['payout_options_label'] ?? "Payout options"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/payout_account");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: myReviewsImage,
                              title:
                                  "${controller.labelTextDetail['my_reviews_label'] ?? "My reviews"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/my_reviews");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: termAndConditionImage,
                              title:
                                  "${controller.labelTextDetail['terms_condition_label'] ?? "Terms and conditions"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/term_condition");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: privacyPolicyImage,
                              title:
                                  "${controller.labelTextDetail['privacy_policy_label'] ?? "Privacy policy"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/privacy_policy");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: termOfUseIcon,
                              title:
                                  "${controller.labelTextDetail['terms_of_use_label'] ?? "Terms of use"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/term_of_use");
                              }),
                          10.heightBox,
                          // linkWidget(
                          //     imagePath: refundPolicyIcon,
                          //     title: "${controller.labelTextDetail['refund_policy_label'] ?? "Refund policy"}",
                          //     context: context,
                          //     index: 1,
                          //     onTap: (){
                          //       Get.toNamed("/refund_policy");
                          //     }
                          // ),
                          // 10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: cancellationPolicyIcon,
                              title:
                                  "${controller.labelTextDetail['cancellation_policy_label'] ?? "Cancellation policy"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/cancellation_policy");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: disputePolicyIcon,
                              title:
                                  "${controller.labelTextDetail['dispute_policy_label'] ?? "Dispute policy"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/dispute_policy");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: contactUsImage,
                              title:
                                  "${controller.labelTextDetail['contact_proximaride_label'] ?? "Contact ProximaRide"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/contact_us");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: coffeeIcon,
                              title:
                                  "${controller.labelTextDetail['coffee_on_wall_label'] ?? "Coffee on the Wall"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/coffee_on_wall");
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: logOutImage,
                              title:
                                  "${controller.labelTextDetail['logout_label'] ?? "Log out"}",
                              context: context,
                              index: 1,
                              onTap: () async {
                                await controller.serviceController.logoutUser();
                              }),
                          10.heightBox,
                          linkWidget(
                              controller: controller,
                              imagePath: closeAccountImage,
                              title:
                                  "${controller.labelTextDetail['colse_your_contact_label'] ?? "Close your account"}",
                              context: context,
                              index: 1,
                              onTap: () {
                                Get.toNamed("/close_my_account");
                              }),
                          10.heightBox,
                          // linkWidget(
                          //     imagePath: cashImage,
                          //     title: "Paypal",
                          //     context: context,
                          //     index: 1,
                          //     onTap: (){
                          //       Get.toNamed("/paypal");
                          //     }
                          // ),
                        ],
                      ),
                    ),
                  ),
                  if (controller.serviceController.isOverlayLoading.value ==
                      true) ...[
                    overlayWidget(context),
                  ]
                ],
              );
            }
          }),
        ));
  }
}
