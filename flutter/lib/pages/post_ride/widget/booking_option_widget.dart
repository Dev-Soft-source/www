import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget bookingOptionWidget(
    {context, controller, screenWidth, bool bookingCheck = false,error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['booking_label'] ?? "Booking method"}",
              screenWidth: screenWidth,
              context: context,
              isRequired: true,
          ),
          Container(
            padding: const EdgeInsets.fromLTRB(15, 15, 15, 0),
            decoration: BoxDecoration(
                color: Colors.white, borderRadius: BorderRadius.circular(5.0)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (controller.bookingOptionList.isNotEmpty) ...[
                  for (var i = 0;
                      i < controller.bookingOptionList.length;
                      i++) ...[
                    InkWell(
                      onTap: bookingCheck == true
                          ? null
                          : () {
                              if (controller.errors.any((error) => error['title'] == "booking_method")) {
                                controller.errors.removeWhere((error) => error['title'] == "booking_method");
                              }
                              controller.bookingOption.value =
                                  controller.bookingOptionList[i];
                            },
                      child: Container(
                        padding: EdgeInsets.all(getValueForScreenType<double>(
                          context: context,
                          mobile: 10.0,
                          tablet: 10.0,
                        )),
                        decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(10.0),
                            border: Border.all(
                                width: 1,
                                color: controller.bookingOption.value ==
                                        controller.bookingOptionList[i]
                                    ? primaryColor
                                    : controller.errors
                                            .where((error) =>
                                                error == "booking_method")
                                            .isNotEmpty
                                        ? Colors.red
                                        : Colors.grey.shade200),
                            color: controller.bookingOption.value ==
                                    controller.bookingOptionList[i]
                                ? primaryColor.withOpacity(0.1)
                                : Colors.white),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          children: [
                            Image.asset(
                                i == 0
                                    ? instantBookingImage
                                    : manualBookingImage,
                                width: getValueForScreenType<double>(
                                  context: context,
                                  mobile: 45.0,
                                  tablet: 45.0,
                                ),
                                height: getValueForScreenType<double>(
                                  context: context,
                                  mobile: 45.0,
                                  tablet: 45.0,
                                )),
                            5.widthBox,
                            txt22Size(
                                title: "${controller.bookingOptionLabelList[i]}",
                                context: context,
                                fontFamily: regular,
                                textColor: controller.bookingOption.value ==
                                        controller.bookingOptionList[i]
                                    ? primaryColor
                                    : textColor),
                            const SizedBox(width: 10),
                            Tooltip(
                              margin: EdgeInsets.fromLTRB(
                                  getValueForScreenType<double>(
                                    context: context,
                                    mobile: 15.0,
                                    tablet: 15.0,
                                  ),
                                  getValueForScreenType<double>(
                                    context: context,
                                    mobile: 0.0,
                                    tablet: 0.0,
                                  ),
                                  getValueForScreenType<double>(
                                    context: context,
                                    mobile: 15.0,
                                    tablet: 15.0,
                                  ),
                                  getValueForScreenType<double>(
                                    context: context,
                                    mobile: 0.0,
                                    tablet: 0.0,
                                  )),
                              triggerMode: TooltipTriggerMode.tap,
                              message: controller.bookingOptionToolTipList[i].toString(),
                              textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                              showDuration: const Duration(days: 100),
                              waitDuration: Duration.zero,
                              child: Image.asset(infoImage,color: Colors.black, width: getValueForScreenType<double>(
                                context: context,
                                mobile: 20.0,
                                tablet: 20.0,
                              ), height: getValueForScreenType<double>(
                                context: context,
                                mobile: 20.0,
                                tablet: 20.0,
                              )),
                            )
                          ],
                        ),
                      ),
                    ),
                    if (i != controller.petList.length - 1) ...[
                      10.heightBox,
                    ]
                  ]
                ],
              ],
            ),
          ),
          if(error.any((error) => error['title'] == "booking_method")) ...[
            Padding(padding: const EdgeInsets.fromLTRB(15, 0, 15, 15), child: toolTip(tip: error.firstWhere((error) => error['title'] == "booking_method")),)
          ],
        ],
      ));
}
