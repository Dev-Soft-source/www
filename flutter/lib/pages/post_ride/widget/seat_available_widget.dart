import 'package:flutter/material.dart';
import 'package:flutter/widgets.dart';
import 'package:proximaride_app/pages/post_ride/widget/seat_image_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/seat_number_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget seatAvailableWidget(
    {context, controller, screenWidth, bool bookingCheck = false, error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['seats_label'] ?? "Number of seats available"}",
              screenWidth: screenWidth,
              context: context),
          Container(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      seatImageWidget(
                        context: context,
                        onTap: bookingCheck == true
                            ? null
                            : () {
                                controller.seatAvailable.value = 1;
                                if (controller.errors.any((error) => error['title'] == "seats")) {
                                  controller.errors.removeWhere((error) => error['title'] == "seats");
                                }
                              },
                        isActive:
                            controller.seatAvailable.value >= 1 ? true : false,
                        isError: controller.errors
                            .where((error) => error == "seats")
                            .isNotEmpty,
                      ),
                      5.widthBox,
                      seatImageWidget(
                        context: context,
                        onTap: () {
                          controller.seatAvailable.value = 2;
                          if (controller.errors.any((error) => error['title'] == "seats")) {
                            controller.errors.removeWhere((error) => error['title'] == "seats");
                          }
                        },
                        isActive:
                            controller.seatAvailable.value >= 2 ? true : false,
                        isError: controller.errors
                            .where((error) => error == "seats")
                            .isNotEmpty,
                      ),
                      5.widthBox,
                      seatImageWidget(
                          context: context,
                          onTap: () {
                            controller.seatAvailable.value = 3;
                            if (controller.errors.any((error) => error['title'] == "seats")) {
                              controller.errors.removeWhere((error) => error['title'] == "seats");
                            }
                          },
                          isActive:
                              controller.seatAvailable.value >= 3 ? true : false),
                      5.widthBox,
                      seatImageWidget(
                          context: context,
                          onTap: () {
                            controller.seatAvailable.value = 4;
                            if (controller.errors.any((error) => error['title'] == "seats")) {
                              controller.errors.removeWhere((error) => error['title'] == "seats");
                            }
                          },
                          isActive:
                              controller.seatAvailable.value >= 4 ? true : false),
                      5.widthBox,
                      seatImageWidget(
                          context: context,
                          onTap: () {
                            controller.seatAvailable.value = 5;
                            if (controller.errors.any((error) => error['title'] == "seats")) {
                              controller.errors.removeWhere((error) => error['title'] == "seats");
                            }
                          },
                          isActive:
                              controller.seatAvailable.value >= 5 ? true : false),
                      5.widthBox,
                      seatImageWidget(
                          context: context,
                          onTap: () {
                            controller.seatAvailable.value = 6;
                            if (controller.errors.any((error) => error['title'] == "seats")) {
                              controller.errors.removeWhere((error) => error['title'] == "seats");
                            }
                          },
                          isActive:
                              controller.seatAvailable.value >= 6 ? true : false),
                      5.widthBox,
                      seatImageWidget(
                          context: context,
                          onTap: () {
                            controller.seatAvailable.value = 7;
                            if (controller.errors.any((error) => error['title'] == "seats")) {
                              controller.errors.removeWhere((error) => error['title'] == "seats");
                            }
                          },
                          isActive:
                              controller.seatAvailable.value >= 7 ? true : false),
                    ],
                  ),
                ),
                if(error.any((error) => error['title'] == "seats")) ...[
                  toolTip(tip: error.firstWhere((error) => error['title'] == "seats"))
                ],
                10.heightBox,
                Row(
                  children: [
                    txt20Size(
                        title: "${controller.labelTextDetail['seats_middle_label'] ?? "Number of seats in middle"}",
                        fontFamily: regular,
                        context: context),
                    txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                  ],
                ),
                3.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    seatNumberWidget(
                      context: context,
                      onTap: bookingCheck == true
                          ? null
                          : () {
                              controller.seatMiddle.value = 2;
                              if (controller.errors.any((error) => error['title'] == "middle_seats")) {
                                controller.errors.removeWhere((error) => error['title'] == "middle_seats");
                              }
                            },
                      title: "2 ${controller.labelTextDetail['seats_text'] ?? "seats"}",
                      isActive: controller.seatMiddle.value == 2 ? true : false,
                      isError: controller.errors
                          .where((error) => error == "middle_seats")
                          .isNotEmpty,
                    ),
                    10.widthBox,
                    seatNumberWidget(
                      context: context,
                      onTap: bookingCheck == true
                          ? null
                          : () {
                              controller.seatMiddle.value = 3;
                              if (controller.errors.any((error) => error['title'] == "middle_seats")) {
                                controller.errors.removeWhere((error) => error['title'] == "middle_seats");
                              }
                            },
                      title: "3 ${controller.labelTextDetail['seats_text'] ?? "seats"}",
                      isActive: controller.seatMiddle.value == 3 ? true : false,
                      isError: controller.errors
                          .where((error) => error == "middle_seats")
                          .isNotEmpty,
                    ),
                  ],
                ),
                if(error.any((error) => error['title'] == "middle_seats")) ...[
                  toolTip(tip: error.firstWhere((error) => error['title'] == "middle_seats"))
                ],
                10.heightBox,
                Row(
                  children: [
                    txt20Size(
                        title: "${controller.labelTextDetail['seats_back_label'] ?? "Number of seats at back"}",
                        fontFamily: regular,
                        context: context),
                    txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                  ],
                ),
                3.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    seatNumberWidget(
                      context: context,
                      onTap: bookingCheck == true
                          ? null
                          : () {
                              controller.seatBack.value = 2;
                              if (controller.errors.any((error) => error['title'] == "back_seats")) {
                                controller.errors.removeWhere((error) => error['title'] == "back_seats");
                              }
                            },
                      title: "2 ${controller.labelTextDetail['seats_text'] ?? "seats"}",
                      isActive: controller.seatBack.value == 2 ? true : false,
                      isError: controller.errors
                          .where((error) => error == "back_seats")
                          .isNotEmpty,
                    ),
                    10.widthBox,
                    seatNumberWidget(
                      context: context,
                      onTap: bookingCheck == true
                          ? null
                          : () {
                              controller.seatBack.value = 3;
                              if (controller.errors.any((error) => error['title'] == "back_seats")) {
                                controller.errors.removeWhere((error) => error['title'] == "back_seats");
                              }
                            },
                      title: "3 ${controller.labelTextDetail['seats_text'] ?? "seats"}",
                      isActive: controller.seatBack.value == 3 ? true : false,
                      isError: controller.errors
                          .where((error) => error == "back_seats")
                          .isNotEmpty,
                    ),
                  ],
                ),
                if(error.any((error) => error['title'] == "back_seats")) ...[
                  toolTip(tip: error.firstWhere((error) => error['title'] == "back_seats"))
                ],
              ],
            ),
          ),
        ],
      ));
}
