import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/luggage_option_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget luggageWidget(
    {context, controller, screenWidth, bool bookingCheck = false,error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['luggage_label'] ?? "Luggage"}", screenWidth: screenWidth, context: context,isRequired: true),
          Padding(
              padding: EdgeInsets.all(getValueForScreenType<double>(
                context: context,
                mobile: 10.0,
                tablet: 10.0,
              )),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(8.0),
                        border: Border.all(width: 1, color: inputColor)),
                    child: luggageOptionWidget(
                        controller: controller,
                        context: context,
                        bookingCheck: bookingCheck),
                  ),
                  if(error.any((error) => error['title'] == "luggage")) ...[
                    toolTip(tip: error.firstWhere((error) => error['title'] == "luggage"))
                  ],
                  10.heightBox,
                  Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    children: [
                      SizedBox(
                        width: getValueForScreenType<double>(
                          context: context,
                          mobile: 25.0,
                          tablet: 25.0,
                        ),
                        height: getValueForScreenType<double>(
                          context: context,
                          mobile: 25.0,
                          tablet: 25.0,
                        ),
                        child: checkBoxWidget(
                            value: controller.acceptMoreLuggage.value == "1"
                                ? true
                                : false,
                            activeColor: primaryColor,
                            onChanged: bookingCheck == true
                                ? null
                                : (value) {
                                    controller.acceptMoreLuggage.value =
                                        value == true ? "1" : "";
                                  }),
                      ),
                      5.widthBox,
                      Expanded(
                        child: InkWell(
                          onTap: (){
                            controller.acceptMoreLuggage.value = controller.acceptMoreLuggage.value == "1" ? "" : "1";
                          },
                          child: txt16Size(
                              title: "${controller.labelTextDetail['luggage_checkbox_label1'] ?? "I accept more luggage for extra charge"}",
                              fontFamily: bold,
                              context: context,
                              textColor: textColor),
                        ),
                      ),
                      5.widthBox,
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
                        message: "${controller.labelTextDetail['luggage_checkbox_label1_tooltip'] ?? 'Must be agreed upon with the driver BEFORE booking'}",
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
                  ),// I accept more luggage
                  // 10.heightBox,
                  // Row(
                  //   mainAxisAlignment: MainAxisAlignment.start,
                  //   crossAxisAlignment: CrossAxisAlignment.start,
                  //   mainAxisSize: MainAxisSize.min,
                  //   children: [
                  //     SizedBox(
                  //       width: getValueForScreenType<double>(
                  //         context: context,
                  //         mobile: 25.0,
                  //         tablet: 25.0,
                  //       ),
                  //       height: getValueForScreenType<double>(
                  //         context: context,
                  //         mobile: 25.0,
                  //         tablet: 25.0,
                  //       ),
                  //       child: checkBoxWidget(
                  //           value: controller.openCustomized.value == "1"
                  //               ? true
                  //               : false,
                  //           activeColor: primaryColor,
                  //           onChanged: bookingCheck == true
                  //               ? null
                  //               : (value) {
                  //                   controller.openCustomized.value =
                  //                       value == true ? "1" : "";
                  //                 }),
                  //     ),
                  //     5.widthBox,
                  //     Expanded(
                  //         child: txt16Size(
                  //             title:
                  //             "${controller.labelTextDetail['luggage_checkbox_label2'] ?? "Open to customized pick-up & drop-off for extra charge"}",
                  //             fontFamily: bold,
                  //             context: context)),
                  //     Tooltip(
                  //       margin: EdgeInsets.fromLTRB(
                  //           getValueForScreenType<double>(
                  //             context: context,
                  //             mobile: 15.0,
                  //             tablet: 15.0,
                  //           ),
                  //           getValueForScreenType<double>(
                  //             context: context,
                  //             mobile: 0.0,
                  //             tablet: 0.0,
                  //           ),
                  //           getValueForScreenType<double>(
                  //             context: context,
                  //             mobile: 15.0,
                  //             tablet: 15.0,
                  //           ),
                  //           getValueForScreenType<double>(
                  //             context: context,
                  //             mobile: 0.0,
                  //             tablet: 0.0,
                  //           )),
                  //       triggerMode: TooltipTriggerMode.tap,
                  //       message: "${controller.labelTextDetail['luggage_checkbox_label2_tooltip'] ?? 'Must be agreed upon with the driver BEFORE booking'}",
                  //       textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                  //       showDuration: const Duration(days: 100),
                  //       waitDuration: Duration.zero,
                  //       child: Image.asset(infoImage,color: Colors.black, width: getValueForScreenType<double>(
                  //         context: context,
                  //         mobile: 20.0,
                  //         tablet: 20.0,
                  //       ), height: getValueForScreenType<double>(
                  //         context: context,
                  //         mobile: 20.0,
                  //         tablet: 20.0,
                  //       )),
                  //     )
                  //
                  //   ],
                  // ) //Open to customized pick ups & drop offs
                ],
              )),
        ],
      ));
}
