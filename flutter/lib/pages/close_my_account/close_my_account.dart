import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import '../widgets/button_Widget.dart';
import '../widgets/overlay_widget.dart';
import '../widgets/textWidget.dart';
import '../widgets/tool_tip.dart';
import 'CloseAccountController.dart';

class CloseMyAccount extends StatelessWidget {
  const CloseMyAccount({super.key});

  @override
  Widget build(BuildContext context) {
    final CloseAccountController controller = Get.isRegistered<CloseAccountController>()
        ? Get.find<CloseAccountController>()
        : Get.put(CloseAccountController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['main_heading'] ?? 'Close my account'}",
              context: context)),
          leading: safeBackButton(context),
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
                          // if (controller.errorList.isNotEmpty) ...[
                          //   ListView.builder(
                          //     shrinkWrap: true,
                          //     physics: const NeverScrollableScrollPhysics(),
                          //     itemCount: controller.errorList.length,
                          //     itemBuilder: (context, index) {
                          //       return Column(
                          //         crossAxisAlignment: CrossAxisAlignment.start,
                          //         children: [
                          //           Row(
                          //             crossAxisAlignment:
                          //             CrossAxisAlignment.center,
                          //             mainAxisAlignment:
                          //             MainAxisAlignment.center,
                          //             children: [
                          //               const Icon(Icons.circle,
                          //                   size: 10, color: Colors.red),
                          //               10.widthBox,
                          //               Expanded(
                          //                   child: txt14Size(
                          //                       title:
                          //                       "${controller.errorList[index]}",
                          //                       fontFamily: regular,
                          //                       textColor: Colors.red,
                          //                       context: context))
                          //             ],
                          //           ),
                          //           5.heightBox,
                          //         ],
                          //       );
                          //     },
                          //   ),
                          //   10.heightBox,
                          // ],
                          Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                                color: Colors.red.withOpacity(0.15)),
                            child: Row(
                              children: [
                                const Icon(
                                  Icons.warning_amber,
                                  color: Colors.red,
                                ),
                                10.widthBox,
                                Expanded(
                                  child: txt18Size(
                                      title:
                                          "${controller.labelTextDetail['warning_text'] ?? 'We’ll be sad to see you go — closing your account can’t be undone. Please confirm only if you’re sure.'}",
                                      context: context,
                                      fontFamily: bold,
                                      textColor: Colors.red),
                                ),
                              ],
                            ),
                          ),
                          10.heightBox,
                          // Red required-fields note: 18px
                          txt18Size(
                              title:
                                  "${controller.labelTextDetail['mobile_indicate_required_field_label'] ?? '* Indicates required fields'}",
                              context: context,
                              fontFamily: bold,
                              textColor: Colors.red),
                          20.heightBox,
                          // Heading with primary text 20px, no extra asterisk
                          RichText(
                            text: TextSpan(children: [
                              textSpan(
                                  context: context,
                                  textColor: primaryColor,
                                  fontFamily: bold,
                                  title:
                                      "${controller.labelTextDetail['apply_reason_label'] ?? 'You are closing your account'} ${controller.labelTextDetail['reason_label'] ?? '(select all the reasons that apply)'}",
                                  textSize: 20.0),
                            ]),
                          ),
                          10.heightBox,
                          // txt22Size(
                          //   title: 'Reason(s) you are closing your accounts',
                          //   textColor: primaryColor,
                          //   context: context,
                          //   fontFamily: regular,
                          // ),
                          // 5.heightBox,
                          Container(
                            decoration: BoxDecoration(
                              border: Border.all(width: 1, color: Colors.grey),
                              borderRadius:
                                  const BorderRadius.all(Radius.circular(10)),
                            ),
                            child: Column(
                              children: [
                                10.heightBox,
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.selectedReasons.contains(
                                        "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}"),
                                    side: BorderSide(
                                        color: controller.errors
                                                .where((error) =>
                                                    error == "reasons")
                                                .isNotEmpty
                                            ? Colors.red
                                            : Colors.black,
                                        width: 2),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) {
                                      if (value!) {
                                        // Clear all other selections and add only "Prefer not to say"
                                        controller.selectedReasons.clear();
                                        controller.selectedReasons.add(
                                            "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}");
                                      } else {
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}");
                                      }
                                    },
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}",
                                    context: context,
                                    textColor: Colors.black,
                                  ),
                                ),
                                const Divider(),
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.selectedReasons.contains(
                                        "${controller.labelTextDetail['customer_service_checkbox_label'] ?? 'I do not like the phone/email customer service'}"),
                                    side: BorderSide(
                                        color: controller.errors
                                                .where((error) =>
                                                    error == "reasons")
                                                .isNotEmpty
                                            ? Colors.red
                                            : Colors.black,
                                        width: 2),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) {
                                      if (value!) {
                                        // Remove "Prefer not to say" if it exists
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}");
                                        controller.selectedReasons.add(
                                            "${controller.labelTextDetail['customer_service_checkbox_label'] ?? 'I do not like the phone/email customer service'}");
                                      } else {
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['customer_service_checkbox_label'] ?? 'I do not like the phone/email customer service'}");
                                      }
                                    },
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['customer_service_checkbox_label'] ?? 'I do not like the phone/email customer service'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                const Divider(),
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.selectedReasons.contains(
                                        "${controller.labelTextDetail['technical_issue_checkbox_label'] ?? 'Technical issues with the website/app'}"),
                                    side: BorderSide(
                                        color: controller.errors
                                                .where((error) =>
                                                    error == "reasons")
                                                .isNotEmpty
                                            ? Colors.red
                                            : Colors.black,
                                        width: 2),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) {
                                      if (value!) {
                                        // Remove "Prefer not to say" if it exists
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}");
                                        controller.selectedReasons.add(
                                            "${controller.labelTextDetail['technical_issue_checkbox_label'] ?? 'Technical issues with the website/app'}");
                                      } else {
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['technical_issue_checkbox_label'] ?? 'Technical issues with the website/app'}");
                                      }
                                    },
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['technical_issue_checkbox_label'] ?? 'Technical issues with the website/app'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                const Divider(),
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.selectedReasons.contains(
                                        "${controller.labelTextDetail['difficulties_making_receiving_payments_label'] ?? 'Difficulties making/receiving payments'}"),
                                    side: BorderSide(
                                        color: controller.errors
                                                .where((error) =>
                                                    error == "reasons")
                                                .isNotEmpty
                                            ? Colors.red
                                            : Colors.black,
                                        width: 2),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) {
                                      if (value!) {
                                        // Remove "Prefer not to say" if it exists
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}");
                                        controller.selectedReasons.add(
                                            "${controller.labelTextDetail['difficulties_making_receiving_payments_label'] ?? 'Difficulties making/receiving payments'}");
                                      } else {
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['difficulties_making_receiving_payments_label'] ?? 'Difficulties making/receiving payments'}");
                                      }
                                    },
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['difficulties_making_receiving_payments_label'] ?? 'Difficulties making/receiving payments'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                const Divider(),
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.selectedReasons.contains(
                                        "${controller.labelTextDetail['dont_use_checkbox_label'] ?? 'I don’t use ride-sharing anymore'}"),
                                    side: BorderSide(
                                        color: controller.errors
                                                .where((error) =>
                                                    error == "reasons")
                                                .isNotEmpty
                                            ? Colors.red
                                            : Colors.black,
                                        width: 2),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) {
                                      if (value!) {
                                        // Remove "Prefer not to say" if it exists
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}");
                                        controller.selectedReasons.add(
                                            "${controller.labelTextDetail['dont_use_checkbox_label'] ?? 'I don\'t use ride-sharing anymore'}");
                                      } else {
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['dont_use_checkbox_label'] ?? 'I don\'t use ride-sharing anymore'}");
                                      }
                                    },
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['dont_use_checkbox_label'] ?? 'I don’t use ridesharing anymore'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                const Divider(),
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.selectedReasons.contains(
                                        "${controller.labelTextDetail['another_account_checkbox_label'] ?? 'I have another account that I’ll be using'}"),
                                    side: BorderSide(
                                        color: controller.errors
                                                .where((error) =>
                                                    error == "reasons")
                                                .isNotEmpty
                                            ? Colors.red
                                            : Colors.black,
                                        width: 2),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) {
                                      if (value!) {
                                        // Remove "Prefer not to say" if it exists
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}");
                                        controller.selectedReasons.add(
                                            "${controller.labelTextDetail['another_account_checkbox_label'] ?? 'I have another account that I\'ll be using'}");
                                      } else {
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['another_account_checkbox_label'] ?? 'I have another account that I\'ll be using'}");
                                      }
                                    },
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['another_account_checkbox_label'] ?? 'I have another account that I’ll be using'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                const Divider(),
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.selectedReasons.contains(
                                        "${controller.labelTextDetail['did_not_get_booking_checkbox_label'] ?? 'I did not get bookings on the rides I posted'}"),
                                    side: BorderSide(
                                        color: controller.errors
                                                .where((error) =>
                                                    error == "reasons")
                                                .isNotEmpty
                                            ? Colors.red
                                            : Colors.black,
                                        width: 2),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) {
                                      if (value!) {
                                        // Remove "Prefer not to say" if it exists
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}");
                                        controller.selectedReasons.add(
                                            "${controller.labelTextDetail['did_not_get_booking_checkbox_label'] ?? 'I did not get bookings on the rides I posted'}");
                                      } else {
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['did_not_get_booking_checkbox_label'] ?? 'I did not get bookings on the rides I posted'}");
                                      }
                                    },
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['did_not_get_booking_checkbox_label'] ?? 'I did not get bookings on the rides I posted'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                const Divider(),
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.selectedReasons.contains(
                                        "${controller.labelTextDetail['did_not_find_ride_checkbox_label'] ?? 'I did not find rides to my destination'}"),
                                    side: BorderSide(
                                        color: controller.errors
                                                .where((error) =>
                                                    error == "reasons")
                                                .isNotEmpty
                                            ? Colors.red
                                            : Colors.black,
                                        width: 2),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) {
                                      if (value!) {
                                        // Remove "Prefer not to say" if it exists
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}");
                                        controller.selectedReasons.add(
                                            "${controller.labelTextDetail['did_not_find_ride_checkbox_label'] ?? 'I did not find rides to my destination'}");
                                      } else {
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['did_not_find_ride_checkbox_label'] ?? 'I did not find rides to my destination'}");
                                      }
                                    },
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['did_not_find_ride_checkbox_label'] ?? 'I did not find rides to my destination'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                const Divider(),
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.selectedReasons.contains(
                                        "${controller.labelTextDetail['other_checkbox_label'] ?? 'Others'}"),
                                    side: BorderSide(
                                        color: controller.errors
                                                .where((error) =>
                                                    error == "reasons")
                                                .isNotEmpty
                                            ? Colors.red
                                            : Colors.black,
                                        width: 2),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) {
                                      if (value!) {
                                        // Remove "Prefer not to say" if it exists
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['not_say_checkbox_label'] ?? 'Prefer not to say'}");
                                        controller.selectedReasons.add(
                                            "${controller.labelTextDetail['other_checkbox_label'] ?? 'Others'}");
                                      } else {
                                        controller.selectedReasons.remove(
                                            "${controller.labelTextDetail['other_checkbox_label'] ?? 'Others'}");
                                      }
                                    },
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['other_checkbox_label'] ?? 'Others'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                10.heightBox,
                              ],
                            ),
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "reasons") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "reasons"))
                          ],
                          10.heightBox,
                          // txt22Size(
                          //     title:
                          //         'Would you recommend ProximaRide to your friends?',
                          //     textColor: primaryColor,
                          //     fontFamily: regular,
                          //     context: context),

                          20.heightBox,
                          // Recommendation heading: 20px, asterisk 18px
                          RichText(
                            text: TextSpan(children: [
                              textSpan(
                                  context: context,
                                  textColor: primaryColor,
                                  fontFamily: regular,
                                  title:
                                      "${controller.labelTextDetail['recommend_heading'] ?? 'Would you recommend ProximaRide to your friends?'}",
                                  textSize: 20.0),
                              textSpan(
                                  context: context,
                                  textColor: Colors.red,
                                  fontFamily: bold,
                                  title: '*',
                                  textSize: 18.0),
                            ]),
                          ),
                          5.heightBox,
                          Container(
                            decoration: BoxDecoration(
                              border: Border.all(width: 1, color: Colors.grey),
                              borderRadius:
                                  const BorderRadius.all(Radius.circular(10)),
                            ),
                            child: Column(
                              children: [
                                10.heightBox,
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.wouldRecommend.value ==
                                        2, // Set value based on index
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) => (controller
                                        .wouldRecommend
                                        .value = (value! ? 2 : 0)),
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['yes_checkbox_label'] ?? 'Yes'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                const Divider(),
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.wouldRecommend.value ==
                                        1, // Set value based on index
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) => (controller
                                        .wouldRecommend
                                        .value = (value! ? 1 : 0)),
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['no_checkbox_label'] ?? 'No'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                const Divider(),
                                ListTile(
                                  dense: true,
                                  visualDensity: const VisualDensity(
                                      horizontal: -4, vertical: -2),
                                  minVerticalPadding: 8,
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16),
                                  horizontalTitleGap: 12,
                                  minLeadingWidth: 0,
                                  trailing: Checkbox(
                                    value: controller.wouldRecommend.value ==
                                        3, // Set value based on index
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4.0),
                                    ),
                                    activeColor: primaryColor,
                                    onChanged: (value) => (controller
                                        .wouldRecommend
                                        .value = (value! ? 3 : 0)),
                                  ),
                                  title: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['prefer_not_checkbox_label'] ?? 'Prefer not to say'}",
                                    context: context,
                                    textColor: Colors.black,
                                    fontFamily: regular,
                                  ),
                                ),
                                10.heightBox
                              ],
                            ),
                          ),
                          if (controller.errors.firstWhereOrNull((element) =>
                                  element['title'] == "recommend") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) =>
                                        element['title'] == "recommend"))
                          ],

                          // RichText(
                          //   text: TextSpan(children: [
                          //     textSpan(
                          //         context: context,
                          //         textColor: primaryColor,
                          //         fontFamily: regular,
                          //         title:
                          //             "${controller.labelTextDetail['why_closing_account_label'] ?? 'Tell us, in your own words, why you are closing your account'}",
                          //         textSize: 22.0),
                          //     textSpan(
                          //         context: context,
                          //         textColor: Colors.red,
                          //         fontFamily: bold,
                          //         title: '*',
                          //         textSize: 22.0),
                          //   ]),
                          // ),
                          // 5.heightBox,
                          // txt18Size(
                          // title:
                          //     "${controller.labelTextDetail['why_closing_account_label'] ?? 'Tell us, in your own words, why you are closing your account'}",
                          // context: context,
                          // fontFamily: regular,
                          // textColor: Colors.black),
                          20.heightBox,
                          txt20Size(
                            title:
                                "${controller.labelTextDetail['why_closing_account_label'] ?? 'Tell us, in your own words, why you are closing your account'}",
                            context: context,
                            fontFamily: regular,
                            textColor: primaryColor,
                          ),
                          5.heightBox,
                          TextField(
                            maxLines: 3,
                            controller: controller.txtController2,
                            decoration: InputDecoration(
                              // hintStyle: TextStyle(color: Colors.grey),
                              // hintText:
                              //     "${controller.labelTextDetail['why_closing_account_placeholder'] ?? "In your own words, please tell us why you’d like to close your account. This is optional, but your feedback would be greatly appreciated."}",
                              border: OutlineInputBorder(
                                borderRadius:
                                    BorderRadius.all(Radius.circular(7)),
                              ),
                            ),
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "why") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "why"))
                          ],

                          // txt18Size(
                          //     title:
                          //         "${controller.labelTextDetail['improve_label'] ?? 'Please tell us, how we can improve'}",
                          //     context: context,
                          //     fontFamily: regular,
                          //     textColor: Colors.black),
                          20.heightBox,
                          txt20Size(
                            title:
                                "${controller.labelTextDetail['improve_label'] ?? 'Please tell us, how we can improve'}",
                            context: context,
                            fontFamily: regular,
                            textColor: primaryColor,
                          ),
                          5.heightBox,
                          TextField(
                            maxLines: 3,
                            controller: controller.txtController1,
                            decoration: InputDecoration(
                              // hintStyle: TextStyle(color: Colors.grey),
                              // hintText:
                              //     "${controller.labelTextDetail['improve_placeholder'] ?? "We’d love to hear how we can improve. Sharing is optional, but your input would mean a lot."}",
                              border: OutlineInputBorder(
                                borderRadius:
                                    BorderRadius.all(Radius.circular(7)),
                              ),
                            ),
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "improve") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "improve"))
                          ],

                          20.heightBox,
                          Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              SizedBox(
                                height: 25,
                                width: 25,
                                child: checkBoxWidget(
                                    value: controller.closingAccount.value,
                                    isError: controller.errors
                                        .where((error) =>
                                            error is Map &&
                                            error['title'] == "check")
                                        .isNotEmpty,
                                    onChanged: (value) {
                                      controller.closingAccount.value =
                                          value ?? false;
                                      if (controller.closingAccount.value) {
                                        controller.errors.removeWhere((error) =>
                                            error is Map &&
                                            error['title'] == "check");
                                      }
                                    }),
                              ),
                              10.widthBox,
                              Row(
                                children: [
                                  InkWell(
                                    onTap: () {
                                      controller.closingAccount.value =
                                          !controller.closingAccount.value;
                                      if (controller.closingAccount.value) {
                                        controller.errors.removeWhere((error) =>
                                            error is Map &&
                                            error['title'] == "check");
                                      }
                                    },
                                    child: txt20Size(
                                        title:
                                            "${controller.labelTextDetail['close_my_account_checkbox'] ?? 'Close my account'}",
                                        context: context,
                                        textColor: Colors.black,
                                        fontFamily: regular),
                                  ),
                                  txt18Size(
                                      title: '*',
                                      context: context,
                                      fontFamily: bold,
                                      textColor: Colors.red)
                                ],
                              ),
                            ],
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "check") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "check"))
                          ],
                          20.heightBox,
                          SizedBox(
                            width: context.screenWidth,
                            height: 50,
                            child: elevatedButtonWidget(
                              enabled: controller.closingAccount.value,
                              textWidget: txt22Size(
                                  title:
                                      "${controller.labelTextDetail['close_account_button_text'] ?? "Close my account"}",
                                  textColor: Colors.white,
                                  context: context,
                                  fontFamily: regular),
                              onPressed: () async {
                                controller.removeAccount();
                              },
                              btnColor: Colors.red,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  // Align(
                  //   alignment: Alignment.bottomCenter,
                  //   child: Container(
                  //     color: Colors.grey.shade100,
                  //     padding: const EdgeInsets.all(15.0),
                  //     width: context.screenWidth,
                  //     child: elevatedButtonWidget(
                  //       textWidget: txt28Size(
                  //           title:
                  //               "${controller.labelTextDetail['close_account_button_text'] ?? "Close my account"}",
                  //           textColor: Colors.white,
                  //           context: context,
                  //           fontFamily: regular),
                  //       onPressed: () async {
                  //         controller.removeAccount();
                  //       },
                  //       btnColor: Colors.red,
                  //     ),
                  //   ),
                  // ),
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



