import 'package:flutter/material.dart';
// import 'package:flutter_html/flutter_html.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/password/PasswordController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import '../widgets/tool_tip.dart';

class PasswordPage extends GetView<PasswordController> {
  const PasswordPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(PasswordController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['main_heading'] ?? "Password"}",
              context: context)),
          leading: const BackButton(color: Colors.white),
        ),
        body: SafeArea(
          child: Obx(() {
            if (controller.errorStateManager.isLoading.value) {
              return Center(child: progressCircularWidget(context));
            } else if (controller.errorStateManager.hasError.value) {
              return ErrorStateWidget(
                errorType: controller.errorStateManager.errorType.value,
                message: controller.errorStateManager.errorMessage.value,
                onRetry: () =>
                    controller.errorStateManager.onRetry.value?.call(),
              );
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
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: [
                          txt20Size(
                              title:
                                  "${controller.labelTextDetail['mobile_password_description_text'] ?? "You can update your password from here. Passwords must have at least eight characters and contain one uppercase and one lowercase.\nStrong passwords include numbers, letters, and punctuation marks"}",
                              fontFamily: regular,
                              textColor: textColor,
                              context: context),
                          // Html(
                          //   data: controller.labelTextDetail[
                          //           'mobile_password_description_text'] ??
                          //       "You can update your password from here. Passwords must have at least eight characters and contain one uppercase and one lowercase.\nStrong passwords include numbers, letters, and punctuation marks",
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
                          // txt22Size(
                          //     title: formatMessage(
                          //       "${controller.labelTextDetail['mobile_password_description_text'] ?? "You can update your password from here. Passwords must have at least eight characters and contain one uppercase and one lowercase.\nStrong passwords include numbers, letters, and punctuation marks"}",
                          //     ),
                          //     fontFamily: regular,
                          //     textColor: textColor,
                          //     context: context),
                          10.heightBox,
                          10.heightBox,
                          // Red required-fields note: 18px
                          txt18Size(
                              title:
                                  "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? "* Indicates required fields"}",
                              fontFamily: regular,
                              context: context,
                              textColor: Colors.red),
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['current_password_label'] ?? "Current password"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              txt20Size(
                                  title: "*",
                                  fontFamily: regular,
                                  context: context,
                                  textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          TextFormField(
                            onChanged: (value) {
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] ==
                                          "current_password") !=
                                  null) {
                                controller.errors.remove(controller.errors
                                    .firstWhereOrNull((element) =>
                                        element['title'] ==
                                        "current_password"));
                              }
                            },
                            controller:
                                controller.currentPasswordTextEditingController,
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
                              suffixIcon: IconButton(
                                icon: Icon(
                                  controller.isOldPasswordVisible.value
                                      ? Icons.visibility_off
                                      : Icons.visibility,
                                ),
                                onPressed: () =>
                                    controller.isOldPasswordVisible.value =
                                        !controller.isOldPasswordVisible.value,
                              ),
                            ),
                            style: const TextStyle(
                                fontSize: 18, fontFamily: regular),
                            obscureText: !controller.isOldPasswordVisible.value,
                            keyboardType: TextInputType.visiblePassword,
                            textInputAction: TextInputAction.done,
                            focusNode: controller.focusNodes[1.toString()],
                          ),
                          if (controller.errors.firstWhereOrNull((element) =>
                                  element['title'] == "current_password") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) =>
                                        element['title'] == "current_password"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['new_password_label'] ?? "New password"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              txt20Size(
                                  title: "*",
                                  fontFamily: regular,
                                  context: context,
                                  textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          TextFormField(
                            onChanged: (value) {
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "new_password") !=
                                  null) {
                                controller.errors.remove(controller.errors
                                    .firstWhereOrNull((element) =>
                                        element['title'] == "new_password"));
                              }
                            },
                            controller:
                                controller.newPasswordTextEditingController,
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
                              suffixIcon: IconButton(
                                icon: Icon(
                                  controller.isNewPasswordVisible.value
                                      ? Icons.visibility_off
                                      : Icons.visibility,
                                ),
                                onPressed: () =>
                                    controller.isNewPasswordVisible.value =
                                        !controller.isNewPasswordVisible.value,
                              ),
                            ),
                            style: const TextStyle(
                                fontSize: 18, fontFamily: regular),
                            obscureText: !controller.isNewPasswordVisible.value,
                            keyboardType: TextInputType.visiblePassword,
                            textInputAction: TextInputAction.done,
                            focusNode: controller.focusNodes[2.toString()],
                          ),
                          if (controller.errors.firstWhereOrNull((element) =>
                                  element['title'] == "new_password") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) =>
                                        element['title'] == "new_password"))
                          ],
                          10.heightBox,

                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['confirm_new_password_label'] ?? "Confirm new password"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              txt20Size(
                                  title: "*",
                                  fontFamily: regular,
                                  context: context,
                                  textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          TextFormField(
                            onChanged: (value) {
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] ==
                                          "confirm_password") !=
                                  null) {
                                controller.errors.remove(controller.errors
                                    .firstWhereOrNull((element) =>
                                        element['title'] ==
                                        "confirm_password"));
                              }
                            },
                            controller:
                                controller.confirmPasswordTextEditingController,
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
                              suffixIcon: IconButton(
                                icon: Icon(
                                  controller.isConfirmPasswordVisible.value
                                      ? Icons.visibility_off
                                      : Icons.visibility,
                                ),
                                onPressed: () => controller
                                        .isConfirmPasswordVisible.value =
                                    !controller.isConfirmPasswordVisible.value,
                              ),
                            ),
                            style: const TextStyle(
                                fontSize: 18, fontFamily: regular),
                            obscureText:
                                !controller.isConfirmPasswordVisible.value,
                            keyboardType: TextInputType.visiblePassword,
                            textInputAction: TextInputAction.done,
                            focusNode: controller.focusNodes[3.toString()],
                          ),
                          if (controller.errors.firstWhereOrNull((element) =>
                                  element['title'] == "confirm_password") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) =>
                                        element['title'] == "confirm_password"))
                          ],
                          ValueListenableBuilder<TextEditingValue>(
                            valueListenable:
                                controller.newPasswordTextEditingController,
                            builder: (context, value, _) {
                              if (value.text.isEmpty) {
                                return const SizedBox.shrink();
                              }

                              final checklist =
                                  controller.getPasswordChecklist();
                              final completed = checklist.length;
                              final strengthColor =
                                  _getStrengthColor(completed);

                              return Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const SizedBox(height: 10),
                                  Container(
                                    padding: const EdgeInsets.all(16.0),
                                    decoration: BoxDecoration(
                                      color: Colors.grey.shade50,
                                      borderRadius: BorderRadius.circular(8.0),
                                      border: Border.all(
                                          color: Colors.grey.shade300),
                                    ),
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        // Section title: 20px
                                        txt20Size(
                                          title: "Password Requirements",
                                          fontFamily: bold,
                                          textColor: textColor,
                                          context: context,
                                        ),
                                        const SizedBox(height: 12),
                                        Container(
                                          padding: const EdgeInsets.all(12.0),
                                          decoration: BoxDecoration(
                                            color: Colors.white,
                                            borderRadius:
                                                BorderRadius.circular(6.0),
                                            border: Border.all(
                                                color: Colors.grey.shade200),
                                          ),
                                          child: Column(
                                            children: [
                                              Row(
                                                children: [
                                                  // Label: 20px
                                                  txt20Size(
                                                    title: "Overall Progress",
                                                    fontFamily: regular,
                                                    textColor: textColor,
                                                    context: context,
                                                  ),
                                                  const Spacer(),
                                                  // Percentage as secondary text: 18px
                                                  txt18Size(
                                                    title:
                                                        "${(completed * 20).toInt()}%",
                                                    fontFamily: bold,
                                                    textColor: completed == 5
                                                        ? Colors.green
                                                        : primaryColor,
                                                    context: context,
                                                  ),
                                                ],
                                              ),
                                              const SizedBox(height: 8),
                                              LinearProgressIndicator(
                                                value: completed / 5,
                                                backgroundColor:
                                                    Colors.grey.shade300,
                                                valueColor:
                                                    AlwaysStoppedAnimation<
                                                        Color>(
                                                  completed == 5
                                                      ? Colors.green
                                                      : primaryColor,
                                                ),
                                                minHeight: 6.0,
                                              ),
                                              const SizedBox(height: 16),
                                              _buildRequirementRow(
                                                context,
                                                "At least 8 characters",
                                                checklist.contains("length"),
                                                Icons.text_fields,
                                              ),
                                              const SizedBox(height: 8),
                                              _buildRequirementRow(
                                                context,
                                                "One lowercase letter",
                                                checklist.contains("small"),
                                                Icons.text_format,
                                              ),
                                              const SizedBox(height: 8),
                                              _buildRequirementRow(
                                                context,
                                                "One uppercase letter",
                                                checklist.contains("capital"),
                                                Icons.format_size,
                                              ),
                                              const SizedBox(height: 8),
                                              _buildRequirementRow(
                                                context,
                                                "One number",
                                                checklist.contains("number"),
                                                Icons.numbers,
                                              ),
                                              const SizedBox(height: 8),
                                              _buildRequirementRow(
                                                context,
                                                "One special character",
                                                checklist.contains("special"),
                                                Icons.star,
                                              ),
                                            ],
                                          ),
                                        ),
                                        const SizedBox(height: 12),
                                        Container(
                                          padding: const EdgeInsets.symmetric(
                                              horizontal: 12.0, vertical: 8.0),
                                          decoration: BoxDecoration(
                                            color:
                                                strengthColor.withOpacity(0.1),
                                            borderRadius:
                                                BorderRadius.circular(20.0),
                                            border: Border.all(
                                              color: strengthColor,
                                              width: 1.0,
                                            ),
                                          ),
                                          child: Row(
                                            mainAxisSize: MainAxisSize.min,
                                            children: [
                                              Icon(
                                                _getStrengthIcon(completed),
                                                size: 16.0,
                                                color: strengthColor,
                                              ),
                                              const SizedBox(width: 6),
                                              // Strength label as secondary text: 18px
                                              txt18Size(
                                                title:
                                                    _getStrengthText(completed),
                                                fontFamily: bold,
                                                textColor: strengthColor,
                                                context: context,
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              );
                            },
                          ),
                          10.heightBox,
                          100.heightBox,
                        ],
                      ),
                    ),
                  ),
                  Align(
                    alignment: Alignment.bottomCenter,
                    child: Container(
                      color: Colors.grey.shade100,
                      padding: EdgeInsets.all(
                        getValueForScreenType<double>(
                          context: context,
                          mobile: 15.0,
                          tablet: 15.0,
                        ),
                      ),
                      width: context.screenWidth,
                      height: 75,
                      child: elevatedButtonWidget(
                          enabled: controller.isFormValid.value,
                          textWidget: txt22Size(
                              title:
                                  "${controller.labelTextDetail['update_button_text'] ?? "Update"}",
                              fontFamily: regular,
                              textColor: Colors.white,
                              context: context),
                          onPressed: () async {
                            await controller.updatePassword();
                          },
                          context: context,
                          btnRadius: 5.0),
                    ),
                  ),
                  if (controller.isOverlayLoading.value == true) ...[
                    overlayWidget(context)
                  ]
                ],
              );
            }
          }),
        ));
  }
}

Widget _buildRequirementRow(
    BuildContext context, String requirement, bool isCompleted, IconData icon) {
  return Row(
    children: [
      Container(
        width: 20.0,
        height: 20.0,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: isCompleted ? Colors.green : Colors.grey.shade300,
        ),
        child: Icon(
          isCompleted ? Icons.check : icon,
          size: 12.0,
          color: isCompleted ? Colors.white : Colors.grey.shade600,
        ),
      ),
      const SizedBox(width: 12),
      Expanded(
        child: txt20Size(
          title: requirement,
          fontFamily: regular,
          textColor: isCompleted ? Colors.green.shade700 : textColor,
          context: context,
        ),
      ),
      Container(
        width: 40.0,
        height: 4.0,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(2.0),
          color: isCompleted ? Colors.green : Colors.grey.shade300,
        ),
      ),
    ],
  );
}

Color _getStrengthColor(int completedRequirements) {
  switch (completedRequirements) {
    case 0:
    case 1:
      return Colors.red;
    case 2:
    case 3:
      return Colors.orange;
    case 4:
      return Colors.blue;
    case 5:
      return Colors.green;
    default:
      return Colors.grey;
  }
}

IconData _getStrengthIcon(int completedRequirements) {
  switch (completedRequirements) {
    case 0:
    case 1:
      return Icons.security;
    case 2:
    case 3:
      return Icons.shield;
    case 4:
      return Icons.verified_user;
    case 5:
      return Icons.security;
    default:
      return Icons.help;
  }
}

String _getStrengthText(int completedRequirements) {
  switch (completedRequirements) {
    case 0:
    case 1:
      return "Very Weak";
    case 2:
      return "Weak";
    case 3:
      return "Fair";
    case 4:
      return "Good";
    case 5:
      return "Strong";
    default:
      return "Unknown";
  }
}
