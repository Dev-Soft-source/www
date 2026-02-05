import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';

import '../widgets/tool_tip.dart';

class CancelBookingPage extends StatelessWidget {
  const CancelBookingPage({super.key});

  @override
  Widget build(BuildContext context) {
    var controller = Get.find<MyTripController>();

    return Scaffold(
      appBar: AppBar(
        leading: const BackButton(color: Colors.white),
        title: Obx(() => secondAppBarWidget(
            context: context,
            title: controller.pageType == "trip"
                ? "${controller.labelTextTripDetail['cancel_booking_main_heading1'] ?? ""}"
                : "${controller.labelTextTripDetail['cancel_ride_setting'] ?? "Cancel ride"}")),
        backgroundColor: primaryColor,
      ),
      body: Obx(() => Stack(
            children: [
              SingleChildScrollView(
                  child: Container(
                      padding: EdgeInsets.all(
                        getValueForScreenType<double>(
                          context: context,
                          mobile: 15.0,
                          tablet: 15.0,
                        ),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          20.heightBox,
                          circleImageWidget(
                              width: 80.0,
                              height: 80.0,
                              imageType: "local",
                              imagePath: crossImage,
                              context: context),
                          if (controller.pageType == "trip") ...[
                            10.heightBox,
                            txt25SizeCenter(
                                title:
                                "${controller.labelTextTripDetail['cancel_booking_heading'] ?? "Please tell us why you want to cancel the booking"}",
                                context: context),
                          ],
                          10.heightBox,
                          if (controller.errorList.isNotEmpty) ...[
                            ListView.builder(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: controller.errorList.length,
                              itemBuilder: (context, index) {
                                return Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.center,
                                      mainAxisAlignment:
                                          MainAxisAlignment.center,
                                      children: [
                                        const Icon(Icons.circle,
                                            size: 10, color: Colors.red),
                                        10.widthBox,
                                        Expanded(
                                            child: txt14Size(
                                                title:
                                                    "${controller.errorList[index]}",
                                                fontFamily: regular,
                                                textColor: Colors.red,
                                                context: context))
                                      ],
                                    ),
                                    5.heightBox,
                                  ],
                                );
                              },
                            ),
                            10.heightBox,
                          ],
                          if (controller.pageType == "trip") ...[
                            Container(
                              decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(5.0),
                                  border: Border.all(
                                      width: 1, color: Colors.grey.shade200)),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Padding(
                                    padding: const EdgeInsets.all(10.0),
                                    child: Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceBetween,
                                      children: [
                                        Expanded(
                                          child: txt18Size(
                                              context: context,
                                              title: "${controller.labelTextTripDetail['number_of_seat_booked'] ?? "Number of booked seats"}"),
                                        ),
                                        txt14Size(
                                            context: context,
                                            title:
                                                "${controller.cancelRideInfo['seats']}")
                                      ],
                                    ),
                                  ),
                                  const Divider(),
                                  Padding(
                                    padding: const EdgeInsets.all(10.0),
                                    child: Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceEvenly,
                                      children: [
                                        Expanded(
                                            child: txt18Size(
                                                context: context,
                                                title: "${controller.labelTextTripDetail['cancel_seat_label'] ?? "Cancel seats"}")),
                                        SizedBox(
                                          height: 40,
                                          width: 120,
                                          child: fieldsWidget(
                                              textController: controller
                                                  .tripCancelTextEditingController,
                                              fieldType: "number",
                                              readonly: false,
                                              fontSize: 16.0,
                                              fontFamily: regular,
                                              onChanged: (value) {
                                                controller.errors.clear();
                                                RegExp invalidCharPattern = RegExp(r'[^0-9]');
                                                if (invalidCharPattern.hasMatch(value)) {
                                                  controller.tripCancelTextEditingController.text = controller.tripCancelTextEditingController.text.eliminateLast;
                                                } else {
                                                  int enteredSeats = int.tryParse(value) ?? -1;
                                                  int availableSeats = int.parse(controller.cancelRideInfo['seats'].toString());

                                                  if (enteredSeats > availableSeats) {
                                                    controller.tripCancelTextEditingController.text = "";
                                                    var err = {
                                                      'title': "seats",
                                                      'eList' : ['Not this many seats available']
                                                    };
                                                    controller.errors.add(err);
                                                  }
                                                }
                                              }
                                              ),
                                        ),

                                      ],
                                    ),
                                  ),
                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "seats") != null) ...[
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.end,
                                      children: [
                                        toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "seats")),
                                      ],
                                    ),
                                  ],
                                ],
                              ),
                            ),
                            10.heightBox,
                          ],
                          if(controller.pageType == "trip")...[
                            Align(
                              alignment: Alignment.topLeft,
                              child: txt18Size(
                                  context: context,
                                  title: "${controller.labelTextTripDetail['cancel_message_title'] ?? "Message to your driver"}"),
                            ),
                            10.heightBox,
                          ],
                          if(controller.pageType == "ride")...[
                            Align(
                              alignment: Alignment.topLeft,
                              child: txt18Size(
                                  context: context,
                                  title: "${controller.labelTextTripDetail['cancel_ride_label'] ?? "Tell us why"}"),
                            ),
                            10.heightBox,
                          ],
                          textAreaWidget(
                              textController:
                                  controller.reviewTextEditingController,
                              readonly: false,
                              maxLines: 6,
                              fontSize: 16.0,
                              fontFamily: regular,
                              onChanged: (data){
                                if(data != ""){
                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "review") != null) {
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "review"));
                                  }
                                }
                              },
                              placeHolder: controller.pageType == "trip" ?
                              "${controller.labelTextTripDetail['cancel_booking_trip_placeholder'] ?? "Please provide as many details as you want as to why you want to cancel this booking\nYour driver will receive a copy of this message"}" :
                              "${controller.labelTextTripDetail['cancel_ride_placeholder'] ?? "Provide as many details as you want as to why you want to cancel this ride\nYour passengers will receive a copy of this message ProximaRide will investigate each cancellation"}"),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "review") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "review"))
                          ],
                          10.heightBox,
                          if(controller.pageType == "ride")...[
                            Align(
                              alignment: Alignment.topLeft,
                              child: txt18Size(
                                  context: context,
                                  title: "${controller.labelTextTripDetail['tell_passenger_why_label'] ?? "Tell your passenger why"}"),
                            ),
                            10.heightBox,
                            textAreaWidget(
                                textController:
                                controller.tripCancelTextEditingController,
                                readonly: false,
                                maxLines: 6,
                                fontSize: 16.0,
                                fontFamily: regular,
                                onChanged: (data){
                                  if(data != ""){
                                    if(controller.errors.firstWhereOrNull((element) => element['title'] == "reason") != null) {
                                      controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "reason"));
                                    }
                                  }
                                },
                                placeHolder: "${controller.labelTextTripDetail['tell_passenger_why_placeholder'] ?? "Tell us why passenger placeholder"}"),
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "reason") != null) ...[
                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "reason"))
                            ],
                            10.heightBox,
                            Row(
                              mainAxisAlignment: MainAxisAlignment.start,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                SizedBox(
                                  width: 35,
                                  height: 35,
                                  child: checkBoxWidget(
                                    value: controller.confirmRideCheckBox.value,
                                    onChanged: (value){
                                      controller.confirmRideCheckBox.value = value!;
                                      if(controller.errors.firstWhereOrNull((element) => element['title'] == "confirm_check") != null) {
                                        controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "confirm_check"));
                                      }
                                    }
                                  ),
                                ),
                                5.widthBox,
                                Expanded(
                                  child: InkWell(
                                    onTap: () async{
                                      controller.confirmRideCheckBox.value = controller.confirmRideCheckBox.value == true ? false : true;
                                      if(controller.errors.firstWhereOrNull((element) => element['title'] == "confirm_check") != null) {
                                        controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "confirm_check"));
                                      }
                                    },
                                    child: txt16Size(title: controller.labelTextDetail['Confirm_cancel_ride'] ?? "I confirm that i want to cancel this ride", context: context),
                                  )
                                )
                              ],
                            ),
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "confirm_check") != null) ...[
                              toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "confirm_check"))
                            ],
                            10.heightBox,
                          ],
                          SizedBox(
                            height: 50,
                            width: context.screenWidth,
                            child: elevatedButtonWidget(
                                textWidget: txt28Size(
                                    title: "${controller.labelTextTripDetail['booking_cancel_btn_label'] ?? "Cancel ride"}",
                                    context: context,
                                    textColor: Colors.white),
                                onPressed: controller.confirmRideCheckBox.value == false ? null : () async {
                                  await controller
                                      .cancelMyBooking(controller.cancelRideInfo['id']);
                                },
                                context: context),
                          ),
                        ],
                      ))),
              if (controller.isOverlayLoading.value == true) ...[
                overlayWidget(context),
              ]
            ],
          )),
    );
  }
}
