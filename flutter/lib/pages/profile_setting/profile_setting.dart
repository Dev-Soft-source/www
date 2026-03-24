import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/profile_setting/ProfileSettingController.dart';
import 'package:proximaride_app/pages/profile_setting/widget/link_widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';

class ProfileSettingPage extends GetView<ProfileSettingController> {
  const ProfileSettingPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(ProfileSettingController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['main_heading'] ?? "Profile settings"}",
              context: context)),
          leading: IconButton(
            icon: const Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () {
              if (Get.key.currentState?.canPop() ?? false) {
                Get.back();
              } else if (Navigator.of(context).canPop()) {
                Navigator.of(context).pop();
              } else {
                Get.offNamed('/navigation');
              }
            },
          ),
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
              return Container(
                padding: EdgeInsets.all(getValueForScreenType<double>(
                  context: context,
                  mobile: 15.0,
                  tablet: 15.0,
                )),
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Obx(() {
                        return linkWidget(
                            controller: controller,
                            imagePath:
                                "${controller.serviceController.loginUserDetail['profile_image']}",
                            title:
                                "${controller.labelTextDetail['profile_photo_label'] ?? "Profile photo"}",
                            context: context,
                            index: 0,
                            onTap: () {
                              Get.toNamed("/profile_photo");
                            },
                            textColor: textColor);
                      }),
                      10.heightBox,
                      linkWidget(
                          controller: controller,
                          imagePath: myVehicleImage,
                          title:
                              "${controller.labelTextDetail['my_vehicles_label'] ?? "My vehicles"}",
                          context: context,
                          index: 1,
                          onTap: () {
                            Get.toNamed("/my_vehicle");
                          }),
                      10.heightBox,
                      linkWidget(
                          controller: controller,
                          imagePath: passwordImage,
                          title:
                              "${controller.labelTextDetail['password_label'] ?? "Password"}",
                          context: context,
                          index: 1,
                          onTap: () {
                            Get.toNamed("/password");
                          }),
                      10.heightBox,
                      linkWidget(
                          controller: controller,
                          imagePath: phoneImg,
                          title:
                              "${controller.labelTextDetail['my_phone_number_label'] ?? "My phone number"}",
                          context: context,
                          index: 1,
                          onTap: () {
                            Get.toNamed("/my_phone_number");
                          }),
                      10.heightBox,
                      linkWidget(
                          controller: controller,
                          imagePath: myEmailImage,
                          title:
                              "${controller.labelTextDetail['my_email_address_label'] ?? "My e-mail address"}",
                          context: context,
                          index: 1,
                          onTap: () {
                            Get.toNamed("/email_address");
                          }),
                      10.heightBox,
                      linkWidget(
                          controller: controller,
                          imagePath: myDriverImage,
                          title:
                              "${controller.labelTextDetail['my_driver_license_label'] ?? "My driver's license"}",
                          context: context,
                          index: 1,
                          onTap: () {
                            Get.toNamed("/driver_license");
                          }),
                      10.heightBox,
                      linkWidget(
                          controller: controller,
                          imagePath: studentCardImage,
                          title:
                              "${controller.labelTextDetail['my_student_card_label'] ?? "My student card"}",
                          context: context,
                          index: 1,
                          onTap: () {
                            Get.toNamed("/student_card");
                          }),
                      10.heightBox,
                      linkWidget(
                          controller: controller,
                          imagePath: referralsImage,
                          title:
                              "${controller.labelTextDetail['referrals_label'] ?? "Referrals"}",
                          context: context,
                          index: 1,
                          onTap: () {
                            Get.toNamed("/referral");
                          }),
                    ],
                  ),
                ),
              );
            }
          }),
        ));
  }
}
