import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget pricePaymentOptionWidget(
    {context, controller, screenWidth, bool bookingCheck = false, error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title:
                  "${controller.labelTextDetail['price_payment_heading'] ?? "Price and payment method"}",
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
                    txt20Size(
                        title:
                            "${controller.labelTextDetail['price_per_seat_label'] ?? "Price per seat"}",
                        context: context),
                    txt20Size(
                        title: "*",
                        fontFamily: regular,
                        context: context,
                        textColor: Colors.red),
                  ],
                ),
                3.heightBox,
                if (!controller.hasRoutePriceEntries)
                  fieldsWidget(
                    textController: controller.pricePerSeatTextEditingController,
                    fieldType: "number",
                    prefixIcon: Icon(Icons.monetization_on_rounded,
                        color: textColor, size: 20.0),
                    readonly: bookingCheck,
                    fontFamily: regular,
                    fontSize: 18.0,
                    placeHolder: "\$",
                    onChanged: (value) {
                      if (controller.errors
                          .any((error) => error['title'] == "price")) {
                        controller.errors
                            .removeWhere((error) => error['title'] == "price");
                      }
                    },
                    isError: controller.errors
                        .where((error) => error == "price")
                        .isNotEmpty,
                    focusNode: controller.focusNodes[11.toString()],
                  ),
                if (!controller.hasRoutePriceEntries &&
                    controller.directRouteDistanceHint().isNotEmpty) ...[
                  6.heightBox,
                  txt14Size(
                    title: controller.directRouteDistanceHint(),
                    context: context,
                    textColor: Colors.grey.shade700,
                  ),
                ],
                if (controller.hasRoutePriceEntries) ...[
                  // 8.heightBox,
                  // txt16Size(
                  //   title: "Main price is synced with the direct route price.",
                  //   context: context,
                  //   textColor: Colors.grey.shade700,
                  // ),
                  // 12.heightBox,
                  // Row(
                  //   children: [
                  //     txt20Size(
                  //       title: "Available routes",
                  //       context: context,
                  //     ),
                  //     txt20Size(
                  //       title: "*",
                  //       fontFamily: regular,
                  //       context: context,
                  //       textColor: Colors.red,
                  //     ),
                  //   ],
                  // ),
                  8.heightBox,
                  for (final routeEntry in controller.routePriceEntries) ...[
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        LayoutBuilder(
                          builder: (context, constraints) {
                            final isCompact = constraints.maxWidth < 340;
                            final routeLabel = Row(
                              crossAxisAlignment: CrossAxisAlignment.center,
                              children: [
                                Expanded(
                                  child: txt18Size(
                                    title: controller.shortLocationLabel(
                                        routeEntry['fromLabel'].toString()),
                                    context: context,
                                    fontFamily: routeEntry['isDirect'] == true
                                        ? bold
                                        : regular,
                                  ),
                                ),
                                8.widthBox,
                                Icon(
                                  Icons.arrow_right_alt,
                                  color: routeEntry['isDirect'] == true
                                      ? primaryColor
                                      : Colors.grey.shade700,
                                  size: 22,
                                ),
                                8.widthBox,
                                Expanded(
                                  child: txt18Size(
                                    title: controller.shortLocationLabel(
                                        routeEntry['toLabel'].toString()),
                                    context: context,
                                    fontFamily: routeEntry['isDirect'] == true
                                        ? bold
                                        : regular,
                                  ),
                                ),
                              ],
                            );

                            final priceField = SizedBox(
                              width: isCompact ? double.infinity : 100,
                              child: fieldsWidget(
                                textController: routeEntry['controller'],
                                fieldType: "number",
                                readonly: bookingCheck,
                                fontFamily: regular,
                                fontSize: 18.0,
                                placeHolder: "\$",
                                prefixIcon: Icon(Icons.monetization_on_rounded,
                                    color: textColor, size: 20.0),
                                onChanged: (value) {
                                  controller.handleRoutePriceChanged(
                                      routeEntry, value);
                                },
                              ),
                            );

                            if (isCompact) {
                              return Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  routeLabel,
                                  8.heightBox,
                                  priceField,
                                ],
                              );
                            }

                            return Row(
                              crossAxisAlignment: CrossAxisAlignment.center,
                              children: [
                                Expanded(child: routeLabel),
                                10.widthBox,
                                priceField,
                              ],
                            );
                          },
                        ),
                        4.heightBox,
                        txt14Size(
                          title: controller.routeDistanceHint(routeEntry),
                          context: context,
                          textColor: Colors.grey.shade700,
                        ),
                      ],
                    ),
                    8.heightBox,
                  ],
                ],
                if (controller.errors
                    .any((error) => error['title'] == "price")) ...[
                  toolTip(
                      tip: controller.errors
                          .firstWhere((error) => error['title'] == "price"))
                ],
                10.heightBox,
                Row(
                  children: [
                    txt20Size(
                        title:
                            "${controller.labelTextDetail['payment_methods_label'] ?? "Payment method"}",
                        context: context),
                    txt20Size(
                        title: "*",
                        fontFamily: regular,
                        context: context,
                        textColor: Colors.red),
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
                                    controller.paymentOptionList[i].toString()
                                ? true
                                : false,
                            activeColor: primaryColor,
                            onChanged: bookingCheck == true
                                ? null
                                : (value) {
                                    if (value == true) {
                                      controller.paymentOption.value =
                                          controller.paymentOptionList[i]
                                              .toString();
                                    }
                                    if (controller.errors.any((error) =>
                                        error['title'] == "payment_method")) {
                                      controller.errors.removeWhere((error) =>
                                          error['title'] == "payment_method");
                                    }
                                  },
                            isError: controller.errors
                                .where((error) => error == "payment_method")
                                .isNotEmpty,
                          ),
                        ),
                        10.widthBox,
                        InkWell(
                          onTap: bookingCheck == true
                              ? null
                              : () {
                                  controller.paymentOption.value =
                                      controller.paymentOptionList[i]
                                          .toString();
                                  if (controller.errors.any((error) =>
                                      error['title'] == "payment_method")) {
                                    controller.errors.removeWhere((error) =>
                                        error['title'] == "payment_method");
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
                          textStyle: const TextStyle(
                              fontSize: 20, color: Colors.white),
                          showDuration: const Duration(days: 100),
                          waitDuration: Duration.zero,
                          child: Image.asset(infoImage,
                              color: Colors.black,
                              width: getValueForScreenType<double>(
                                context: context,
                                mobile: 20.0,
                                tablet: 20.0,
                              ),
                              height: getValueForScreenType<double>(
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
                if (controller.errors
                    .any((error) => error['title'] == "payment_method")) ...[
                  toolTip(
                      tip: controller.errors.firstWhere(
                          (error) => error['title'] == "payment_method"))
                ],
              ],
            ),
          ),
        ],
      ));
}
