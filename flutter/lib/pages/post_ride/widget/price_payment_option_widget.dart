import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget pricePaymentOptionWidget(
    {context, controller, screenWidth, bool bookingCheck = false,error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['price_payment_heading'] ?? "Price and payment method"}",
              screenWidth: screenWidth,
              context: context),
          Container(
            padding: const EdgeInsets.all(10.0),
            decoration: BoxDecoration(
                color: Colors.white, borderRadius: BorderRadius.circular(5.0)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    txt20Size(title: "${controller.labelTextDetail['price_per_seat_label'] ?? "Price per seat"}", context: context),
                    txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                  ],
                ),
                3.heightBox,
                fieldsWidget(
                  textController: controller.pricePerSeatTextEditingController,
                  fieldType: "number",
                  readonly: false,
                  fontFamily: regular,
                  fontSize: 16.0,
                  placeHolder: "\$",
                  onChanged: (value) {
                    if (controller.errors.any((error) => error['title'] == "price")) {
                      controller.errors.removeWhere((error) => error['title'] == "price");
                    }
                  },
                  isError: controller.errors
                      .where((error) => error == "price")
                      .isNotEmpty,
                  focusNode: controller.focusNodes[11.toString()],
                ),
                if(controller.errors.any((error) => error['title'] == "price")) ...[
                  toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "price"))
                ],
                10.heightBox,
                Row(
                  children: [
                    txt20Size(title: "${controller.labelTextDetail['payment_methods_label'] ?? "Payment method"}", context: context),
                    txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                  ],
                ),
                10.heightBox,
                if (controller.paymentOptionList.isNotEmpty) ...[
                  for (var i = 0;
                      i < controller.paymentOptionList.length;
                      i++) ...[
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
                            value: controller.paymentOption.value ==
                                    controller.paymentOptionList[i]
                                ? true
                                : false,
                            activeColor: primaryColor,
                            onChanged: bookingCheck == true
                                ? null
                                : (value) {
                                    controller.paymentOption.value =
                                        value == true
                                            ? controller.paymentOptionList[i]
                                            : "";
                                    if (controller.errors.any((error) => error['title'] == "payment_method")) {
                                      controller.errors.removeWhere((error) => error['title'] == "payment_method");
                                    }
                                  },
                            isError: controller.errors
                                .where((error) => error == "payment_method")
                                .isNotEmpty,
                          ),
                        ),
                        10.widthBox,
                        InkWell(
                          onTap: bookingCheck == true ? null : (){
                            controller.paymentOption.value = controller.paymentOption.value == controller.paymentOptionList[i] ? "" : controller.paymentOptionList[i];
                            if (controller.errors.any((error) => error['title'] == "payment_method")) {
                              controller.errors.removeWhere((error) => error['title'] == "payment_method");
                            }
                          },
                          child: txt20Size(
                              title: controller.paymentOptionLabelList[i],
                              fontFamily: regular,
                              context: context),
                        ),
                        10.widthBox,
                        // Image.asset(
                        //     i == 0
                        //         ? cashImage
                        //         : i == 1
                        //             ? onlineImage
                        //             : securedCashImage,
                        //     width: 24,
                        //     height: 24),
                        // 10.widthBox,
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
                            message: controller.paymentOptionToolTipList[i],
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
                    if (i != controller.paymentOptionList.length - 1) ...[
                      10.heightBox,
                    ]
                  ]
                ],
                if(controller.errors.any((error) => error['title'] == "payment_method")) ...[
                  toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "payment_method"))
                ],
              ],
            ),
          ),
        ],
      ));
}
