import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/pages/post_ride/widget/add_more_spot_ride_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/prefix_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/date_field_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget rideInfoWidget(
    {context, controller, screenWidth, bool bookingCheck = false,error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['ride_info_heading'] ?? "Ride Info"}", screenWidth: screenWidth, context: context),
          Container(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    txt20Size(title: "${controller.labelTextDetail['from_label'] ?? "From"}", fontFamily: regular, context: context),
                    txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                  ],
                ),
                // txt20Required(title: 'From', fontFamily: bold,context: context),
                3.heightBox,
                fieldsWidget(
                  onTap: (){
                    Get.toNamed("/city/origin/0/0/no");
                  },
                  textController: controller.fromTextEditingController,
                  fieldType: "text",
                  readonly: true,
                  fontFamily: regular,
                  fontSize: 16.0,
                  prefixIcon: preFixIconWidget(
                      context: context, imagePath: fromLocationImage),
                  placeHolder: "${controller.labelTextDetail['from_placeholder'] ?? "Origin"}",
                  hintTextColor: textColor,
                  onChanged: (value) {
                    if (controller.errors.any((error) => error['title'] == "from")) {
                      controller.errors.removeWhere((error) => error['title'] == "from");
                    }

                  },
                  isError: controller.errors
                      .where((error) => error == "from")
                      .isNotEmpty,
                  focusNode: controller.focusNodes[1.toString()],
                ),
                if(controller.errors.any((error) => error['title'] == "from")) ...[
                  toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "from"))
                ],
                10.heightBox,
                Row(
                  children: [
                    txt20Size(title: "${controller.labelTextDetail['to_label'] ?? "To"}", fontFamily: regular, context: context),
                    txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                  ],
                ),
                3.heightBox,
                fieldsWidget(
                  textController: controller.toTextEditingController,
                  onTap: (){
                    Get.toNamed("/city/destination/0/0/no");
                  },
                  fieldType: "text",
                  readonly: true,
                  fontFamily: regular,
                  fontSize: 16.0,
                  prefixIcon: preFixIconWidget(
                      context: context, imagePath: toLocationImage),
                  placeHolder: "${controller.labelTextDetail['to_placeholder'] ?? "Destination"}",
                  onChanged: (value) {
                    if (controller.errors.any((error) => error['title'] == "to")) {
                      controller.errors.removeWhere((error) => error['title'] == "to");
                    }
                  },
                  isError: controller.errors
                      .where((error) => error == "to")
                      .isNotEmpty,
                  focusNode: controller.focusNodes[2.toString()],
                ),
                if(controller.errors.any((error) => error['title'] == "to")) ...[
                  toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "to"))
                ],
                10.heightBox,
                Obx( () =>
                  addMoreSpotRideWidget(
                    context: context,
                    controller: controller,
                    screenWidth: screenWidth,
                    bookingCheck: controller.bookings.value,
                    error: controller.errors.toList(),
                  ),
                ),
                5.heightBox,
                Align(
                  alignment: Alignment.centerRight,
                  child: elevatedButtonWidget(
                      textWidget: txt18Size(
                          title: '${controller.labelTextDetail['add_spot_button_label'] ?? "Add spot"}',
                          context: context,
                          textColor: Colors.white),
                      context: context,
                      onPressed: () async {
                        await controller.addNewSpot();
                      }),
                ),
                10.heightBox,
                Row(
                  children: [
                    txt20Size(
                        title: "${controller.labelTextDetail['pick_up_label'] ?? "Pick-up location"}",
                        fontFamily: regular,
                        context: context),
                    txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                  ],
                ),
                3.heightBox,
                textAreaWidget(
                  textController:
                      controller.pickUpLocationTextEditingController,
                  readonly: bookingCheck,
                  fontSize: 16.0,
                  fontFamily: regular,
                  placeHolder:
                      "${controller.labelTextDetail['pick_up_placeholder'] ?? "Please describe the meeting point: landmark, address, intersection, metro station…. etc"}",
                  maxLines: 2,
                  onChanged: (value) {
                    if (controller.errors.any((error) => error['title'] == "pickup")) {
                      controller.errors.removeWhere((error) => error['title'] == "pickup");
                    }
                  },
                  isError: controller.errors
                      .where((error) => error == "pickup")
                      .isNotEmpty,
                  focusNode: controller.focusNodes[3.toString()],
                ),
                if(controller.errors.any((error) => error['title'] == "pickup")) ...[
                  toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "pickup"))
                ],
                10.heightBox,
                Row(
                  children: [
                    txt20Size(
                        title: "${controller.labelTextDetail['drop_off_label'] ?? "Drop-off location"}",
                        fontFamily: regular,
                        context: context),
                    txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                  ],
                ),
                3.heightBox,
                textAreaWidget(
                  textController:
                      controller.dropOffLocationTextEditingController,
                  readonly: bookingCheck,
                  fontSize: 16.0,
                  fontFamily: regular,
                  placeHolder:
                      "${controller.labelTextDetail['drop_off_placeholder'] ?? "Please describe the arrival point: landmark, address, intersection, metro station…. etc"}",
                  maxLines: 2,
                  onChanged: (value) {
                    if (controller.errors.any((error) => error['title'] == "dropoff")) {
                      controller.errors.removeWhere((error) => error['title'] == "dropoff");
                    }
                  },
                  isError: controller.errors
                      .where((error) => error == "dropoff")
                      .isNotEmpty,
                  focusNode: controller.focusNodes[4.toString()],
                ),
                if(controller.errors.any((error) => error['title'] == "dropoff")) ...[
                  toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "dropoff"))
                ],
                10.heightBox,
                Row(
                  children: [
                    txt20Size(
                        title: "${controller.labelTextDetail['date_time_label'] ?? "Date & time"}",
                        fontFamily: regular,
                        context: context),
                    txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                  ],
                ),

                3.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: dateFieldWidget(
                        textController: controller.dateTextEditingController,
                        fontFamily: regular,
                        fontSize: 16.0,
                        onTap: () async {
                          DateTime? dobDate = await controller.serviceController
                              .datePicker(context,allowPast: false);
                          if (dobDate == null) return;
                          DateFormat dateFormat = DateFormat.yMMMMd();
                          controller.dateTextEditingController.text =
                              dateFormat.format(dobDate);
                          if (controller.errors.any((error) => error['title'] == "date")) {
                            controller.errors.removeWhere((error) => error['title'] == "date");
                          }
                        },
                        prefixIcon: preFixIconWidget(
                            context: context, imagePath: calenderImage),
                        isError: controller.errors
                            .where((error) => error == "date")
                            .isNotEmpty,
                      ),
                    ),
                    5.widthBox,
                    txt20Size(
                        title: "${controller.labelTextDetail['at_label'] ?? "at"}", context: context, fontFamily: regular),
                    5.widthBox,
                    Expanded(
                      child: dateFieldWidget(
                        textController: controller.timeTextEditingController,
                        fontFamily: regular,
                        fontSize: 16.0,
                        onTap: () async {
                          TimeOfDay? picked = await controller.serviceController
                              .timePicker(context);
                          if (picked == null) return;

                          final nowTime = TimeOfDay.now();
                          DateTime now = DateTime.now();

                          if(controller.dateTextEditingController.text.isEmpty){
                            controller.serviceController.showDialogue("${controller.popupTextDetail['past_date_message'] ?? 'Please select date first'}");
                            return;
                          }

                          DateFormat dateFormat = DateFormat("MMMM dd, yyyy");
                          DateTime dateFromString = dateFormat.parse(controller.dateTextEditingController.text);


                          DateTime currentDateOnly = DateTime(now.year, now.month, now.day);

                          if (dateFromString.isAtSameMomentAs(currentDateOnly) && picked.hour < nowTime.hour) {
                            controller.serviceController.showDialogue("${controller.popupTextDetail['past_time_message'] ?? 'Can not pick a time in the past'}");
                            controller.timeTextEditingController.text = "";
                            return;
                          }
                          
                          final DateTime dateTime = DateTime(now.year,
                              now.month, now.day, picked.hour, picked.minute);
                          final DateFormat formatter = DateFormat('HH:mm');
                          controller.timeTextEditingController.text =
                              formatter.format(dateTime);

                          if (controller.errors.any((error) => error['title'] == "time")) {
                            controller.errors.removeWhere((error) => error['title'] == "time");
                          }
                        },
                        prefixIcon: preFixIconWidget(
                            context: context, imagePath: clockImage),
                        isError: controller.errors
                            .where((error) => error == "time")
                            .isNotEmpty,
                      ),
                    ),
                  ],
                ),
                if(error.any((error) => error['title'] == "date") || error.any((error) => error['date'] == "time")) ...[
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                    if(error.any((error) => error['title'] == "date")) ...[
                      toolTip(tip: error.firstWhere((error) => error['title'] == "date"))
                    ],
                    if(error.any((error) => error['title'] == "time")) ...[
                      toolTip(tip: error.firstWhere((error) => error['title'] == "time"))
                    ],
                  ],)
                ],
                10.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    SizedBox(
                      width: getValueForScreenType<double>(
                        context: context,
                        mobile: 15.0,
                        tablet: 15.0,
                      ),
                      height: getValueForScreenType<double>(
                        context: context,
                        mobile: 15.0,
                        tablet: 15.0,
                      ),
                      child: checkBoxWidget(
                          value: controller.recurring.value,
                          onChanged: bookingCheck == true
                              ? null
                              : (value) {
                                  controller.recurring.value = value!;
                                }),
                    ),
                    10.widthBox,
                    InkWell(
                      onTap: bookingCheck == true ? null : (){
                        controller.recurring.value = controller.recurring.value == true ? false : true;
                      },
                      child: txt16Size(
                          title: "${controller.labelTextDetail['recurring_label'] ?? "Recurring trip"}",
                          context: context,
                          fontFamily: bold),
                    ),
                  ],
                ),
                if (controller.recurring.value == true) ...[
                  10.heightBox,
                  txt20Size(
                      title: "${controller.labelTextDetail['recurring_type_label'] ?? "Recurring type"}",
                      fontFamily: regular,
                      context: context),
                  3.heightBox,
                  Container(
                    color: inputColor,
                    child: DropdownButtonFormField(
                        isExpanded: true,
                        elevation: 2,
                        decoration: InputDecoration(
                            enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(5.0),
                                borderSide: BorderSide(
                                    color: controller.errors
                                            .where((error) =>
                                                error == "recurring_type")
                                            .isNotEmpty
                                        ? Colors.red
                                        : Colors.grey.shade400,
                                    style: BorderStyle.solid,
                                    width: 1)),
                            focusedBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(5.0),
                                borderSide:
                                    const BorderSide(color: primaryColor)),
                            contentPadding: const EdgeInsets.symmetric(
                                vertical: 0.0, horizontal: 8.0),
                            fillColor: inputColor),
                        value: controller.recurringType.value,
                        items: [
                          DropdownMenuItem(
                            value: "",
                            child: txt16Size(
                                title: "${controller.labelTextDetail['recurring_type_select_placeholder'] ?? "Select"}",
                                context: context,
                                fontFamily: bold,
                                textColor: textColor),
                          ),
                          DropdownMenuItem(
                              value: "Daily",
                              child: Container(
                                child: txt16Size(
                                    title: "${controller.labelTextDetail['recurring_type_daily_label'] ?? "Daily"}",
                                    context: context,
                                    fontFamily: bold,
                                    textColor: textColor),
                              )),
                          DropdownMenuItem(
                            value: "Weekly",
                            child: txt16Size(
                                title: "${controller.labelTextDetail['recurring_type_weekly_label'] ?? "Weekly"}",
                                context: context,
                                fontFamily: bold,
                                textColor: textColor),
                          ),
                        ],
                        onChanged: (data) {
                          controller.recurringType.value = data!;
                        }),
                  ),
                  txt20Size(
                      title: "${controller.labelTextDetail['recurring_trips_label'] ?? "Recurring trips"}",
                      fontFamily: regular,
                      context: context),
                  3.heightBox,
                  fieldsWidget(
                    textController:
                        controller.recurringTripsTextEditingController,
                    fieldType: "number",
                    readonly: false,
                    fontFamily: regular,
                    fontSize: 16.0,
                    placeHolder: "${controller.labelTextDetail['recurring_trips_placeholder'] ?? "Enter a number, example: 10"}",
                    isError: controller.errors
                        .where((error) => error == "recurring_trips")
                        .isNotEmpty,
                  ),
                ]
              ],
            ),
          ),
        ],
      ));
}
