import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/date_field_widget.dart';
import 'package:proximaride_app/pages/widgets/prefix_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget addMoreSpotRideWidget(
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
                  "${controller.labelTextDetail['stop_along_the_way_label'] ?? "Add More Spots(Optinal)"}",
              screenWidth: screenWidth,
              context: context),
          Container(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: ListView.builder(
                itemCount: controller.spotsCount.value,
                shrinkWrap: true,
                physics: NeverScrollableScrollPhysics(),
                itemBuilder: (context, index) {
                  return Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          txt20Size(
                              title:
                                  "Stop",
                              fontFamily: regular,
                              context: context),
                          index == 0
                              ? SizedBox()
                              : elevatedButtonWidget(
                                  textWidget: txt14Size(
                                      title:
                                          '${controller.labelTextDetail['delete_spot_button_label'] ?? "Delete spot"}',
                                      context: context,
                                      textColor: Colors.white),
                                  context: context,
                                  btnColor: Colors.red,
                                  onPressed: () async {
                                    await controller.removeNewSpot(index);
                                  }),
                        ],
                      ),
                      3.heightBox,
                      fieldsWidget(
                        onTap: () {
                          Get.toNamed("/city/origin/0/$index/yes");
                        },
                        textController: controller.fromSpotControllers[index],
                        fieldType: "text",
                        readonly: true,
                        fontFamily: regular,
                        fontSize: 18.0,
                        prefixIcon: preFixIconWidget(
                            context: context, imagePath: fromLocationImage),
                        placeHolder: "Stop",
                        hintTextColor: textColor,
                        onChanged: (value) {},
                      ),
                      Obx(() {
                        if (controller.fromSpotControllers[index].text == "" &&
                            controller.showErrorSpot.value == true) {
                          return toolTip(
                              tip: "Please add stop", type: "normal1");
                        } else {
                          return SizedBox();
                        }
                      }),
                      10.heightBox,
                      txt20Size(
                          title:
                              "Pickup/off location",
                          context: context),
                      3.heightBox,
                      fieldsWidget(
                        textController: controller.pickupSpotControllers[index],
                        fieldType: "text",
                        readonly: false,
                        fontFamily: regular,
                        fontSize: 18.0,
                        placeHolder:
                            "${controller.labelTextDetail['pickup_off_placeholder'] ?? "Describe the pickup/off point"}",
                        onChanged: (value) {},
                      ),
                      Obx(() {
                        if (controller.pickupSpotControllers[index].text == "" &&
                            controller.showErrorSpot.value == true) {
                          return toolTip(
                              tip: "Please add pickup/off location",
                              type: "normal1");
                        } else {
                          return SizedBox();
                        }
                      }),
                      10.heightBox,
                      txt20Size(
                          title:
                              "${controller.labelTextDetail['date_time_label'] ?? "Date & time"}",
                          context: context),
                      3.heightBox,
                      Row(
                        children: [
                          Expanded(
                            child: dateFieldWidget(
                              textController: controller.dateSpotControllers[index],
                              fontFamily: regular,
                              fontSize: 16.0,
                              onTap: bookingCheck == true
                                  ? null
                                  : () async {
                                      DateTime? pickedDate = await controller
                                          .serviceController
                                          .datePicker(context, allowPast: false);
                                      if (pickedDate == null) return;

                                      controller.dateSpotControllers[index].text =
                                          DateFormat("MMMM dd, yyyy")
                                              .format(pickedDate);
                                    },
                              prefixIcon: preFixIconWidget(
                                  context: context, imagePath: calenderImage),
                              isError: false,
                            ),
                          ),
                          5.widthBox,
                          txt20Size(
                              title:
                                  "${controller.labelTextDetail['at_label'] ?? "at"}",
                              context: context,
                              fontFamily: regular),
                          5.widthBox,
                          Expanded(
                            child: dateFieldWidget(
                              textController: controller.timeSpotControllers[index],
                              fontFamily: regular,
                              fontSize: 16.0,
                              onTap: bookingCheck == true
                                  ? null
                                  : () async {
                                      if (controller
                                          .dateSpotControllers[index].text
                                          .isEmpty) {
                                        controller.serviceController.showDialogue(
                                            "${controller.popupTextDetail['past_date_message'] ?? 'Please select date first'}");
                                        return;
                                      }

                                      TimeOfDay? pickedTime = await controller
                                          .serviceController
                                          .timePicker(context);
                                      if (pickedTime == null) return;

                                      final now = DateTime.now();
                                      final selectedDate = DateFormat("MMMM dd, yyyy")
                                          .parse(controller.dateSpotControllers[index].text);
                                      final currentDateOnly =
                                          DateTime(now.year, now.month, now.day);

                                      if (selectedDate.isAtSameMomentAs(currentDateOnly) &&
                                          (pickedTime.hour < now.hour ||
                                              (pickedTime.hour == now.hour &&
                                                  pickedTime.minute < now.minute))) {
                                        controller.serviceController.showDialogue(
                                            "${controller.popupTextDetail['past_time_message'] ?? 'Can not pick a time in the past'}");
                                        controller.timeSpotControllers[index].text =
                                            "";
                                        return;
                                      }

                                      final selectedDateTime = DateTime(
                                        now.year,
                                        now.month,
                                        now.day,
                                        pickedTime.hour,
                                        pickedTime.minute,
                                      );

                                      controller.timeSpotControllers[index].text =
                                          DateFormat('HH:mm')
                                              .format(selectedDateTime);
                                    },
                              prefixIcon: preFixIconWidget(
                                  context: context, imagePath: clockImage),
                              isError: false,
                            ),
                          ),
                        ],
                      ),
                      Obx(() {
                        if ((controller.dateSpotControllers[index].text == "" ||
                                controller.timeSpotControllers[index].text == "") &&
                            controller.showErrorSpot.value == true) {
                          return toolTip(
                              tip: "Please add date and time", type: "normal1");
                        } else {
                          return SizedBox();
                        }
                      }),
                      10.heightBox,
                    ],
                  );
                }),
          ),
        ],
      ));
}
