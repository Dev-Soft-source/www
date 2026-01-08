import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/pages/post_ride/widget/seat_number_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/radio_button_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';

class RemovePassengerPage extends StatelessWidget {
  const RemovePassengerPage({super.key});

  @override
  Widget build(BuildContext context) {
    var controller = Get.find<MyTripController>();

    return Scaffold(
      appBar: AppBar(
        leading: const BackButton(color: Colors.white),
        //title: Obx(() => secondAppBarWidget(context: context, title:"${controller.labelTextTripDetail['remove_passenger_heading'] ?? "Remove this passenger"}")),
        title: secondAppBarWidget(context: context, title:""),
        backgroundColor: primaryColor,
      ),
      body: Obx(() =>
          Stack(
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
                          Align(alignment: Alignment.topLeft, child: txt16Size(context: context, title: "${controller.labelTextTripDetail['remove_passenger_text'] ?? "Remove passenger"}", fontFamily: bold)),
                          10.heightBox,
                          circleImageWidget(
                              width: 80.0,
                              height: 80.0,
                              imageType: "local",
                              imagePath: crossImage,
                              context: context
                          ),
                          10.heightBox,
                          if(controller.errorList.isNotEmpty)...[
                            ListView.builder(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: controller.errorList.length,
                              itemBuilder: (context, index){
                                return Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      crossAxisAlignment: CrossAxisAlignment.center,
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      children: [
                                        const Icon(Icons.circle, size: 10, color: Colors.red),
                                        10.widthBox,
                                        Expanded(child: txt14Size(title: "${controller.errorList[index]}", fontFamily: regular, textColor: Colors.red, context: context))
                                      ],
                                    ),
                                    5.heightBox,
                                  ],
                                );
                              },
                            ),
                            10.heightBox,
                          ],
                          Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              SizedBox(
                                height: 25,
                                width: 25,
                                child: radioButtonWidget(
                                  value: "0",
                                  groupValue: controller.removePassengerType.value,
                                  onChanged: (value) async{
                                    controller.removePassengerType.value = value!;
                                  }
                                ),
                              ),
                              Expanded(child: txt16Size(context: context, title: "${controller.labelTextTripDetail['remove_from_this_ride_message'] ?? "Remove passenger from this ride"}", fontFamily: bold))
                            ],
                          ),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              SizedBox(
                                height: 25,
                                width: 25,
                                child: radioButtonWidget(
                                  value: "1",
                                  groupValue: controller.removePassengerType.value,
                                  onChanged: (value) async{
                                    controller.removePassengerType.value = value!;
                                  }
                                ),
                              ),
                              Expanded(child: txt16Size(context: context, title: "${controller.labelTextTripDetail['remove_passenger_and_block_message'] ?? "Remove this passenger from this and prevent him from booking on any of my ride in future"}", fontFamily: bold))
                            ],
                          ),
                          if(controller.removePassengerType.value == "1")...[
                            10.heightBox,
                            Row(
                              mainAxisAlignment: MainAxisAlignment.start,
                              children: [
                                seatNumberWidget(
                                  context: context,
                                  onTap: () {
                                    controller.removePassenger.value = "temporarily";
                                  },
                                  title: "${controller.labelTextTripDetail['block_temporarily_label'] ?? "Block temporarily"}",
                                  isActive: controller.removePassenger.value == "temporarily" ? true : false,
                                  isError: controller.errors
                                      .where((error) => error == "remove_type")
                                      .isNotEmpty,
                                ),
                                10.widthBox,
                                seatNumberWidget(
                                  context: context,
                                  onTap:  () {
                                    controller.removePassenger.value = "permanently";
                                  },
                                  title: "${controller.labelTextTripDetail['block_permanently_label'] ?? "Block permanently"}",
                                  isActive: controller.removePassenger.value == "permanently" ? true : false,
                                  isError: controller.errors
                                      .where((error) => error == "remove_type")
                                      .isNotEmpty,
                                ),
                              ],
                            ),
                            // if(error.any((error) => error['title'] == "middle_seats")) ...[
                            //   toolTip(tip: error.firstWhere((error) => error['title'] == "middle_seats"))
                            // ],
                          ],
                          if(controller.removePassenger.value == "temporarily")...[
                            10.heightBox,
                            fieldsWidget(
                              textController: controller.blockDaysTextEditingController,
                              fieldType: "number",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              placeHolder: "${controller.labelTextTripDetail['remove_day_placeholder'] ?? "Select days"}",
                              hintTextColor: textColor,
                              onChanged: (value) {
                              },
                            ),
                          ],
                          10.heightBox,
                          Align(
                            alignment: Alignment.topLeft,
                            child: txt20Size(
                                title: "${controller.labelTextTripDetail['driver_remove_reason_label'] ?? "Tell us why"}",
                                fontFamily: regular,
                                context: context),
                          ),
                          5.heightBox,
                          textAreaWidget(
                              textController: controller.reviewTextEditingController,
                              readonly: false,
                              maxLines: 6,
                              fontSize: 16.0,
                              fontFamily: regular,
                              placeHolder: "${controller.labelTextTripDetail['driver_remove_reason_placeholder'] ?? "Please tell us why you want to remove this passenger from your ride\nYour passenger will not receive a copy of this message"}"
                          ),
                          10.heightBox,
                          Align(
                            alignment: Alignment.topLeft,
                            child: txt20Size(
                                title: "${controller.labelTextTripDetail['passenger_remove_reason_label'] ?? "Tell your passenger why"}",
                                fontFamily: regular,
                                context: context),
                          ),
                          5.heightBox,
                          textAreaWidget(
                              textController: controller.tripCancelTextEditingController,
                              readonly: false,
                              maxLines: 6,
                              fontSize: 16.0,
                              fontFamily: regular,
                              placeHolder: "${controller.labelTextTripDetail['passenger_remove_reason_placeholder'] ?? "Please tell the passenger why you are removing them from this ride"}"
                          ),
                          100.heightBox,
                        ],
                      )
                  )
              ),
              Align(
                alignment: Alignment.bottomCenter,
                child: Container(
                  padding: const EdgeInsets.all(10.0),
                  height: 70,
                  decoration: BoxDecoration(
                      color: Colors.grey.shade200,
                      borderRadius: BorderRadius.circular(5.0)
                  ),
                  child: SizedBox(
                    height: 70,
                    width: context.screenWidth,
                    child: elevatedButtonWidget(
                        textWidget: txt28Size(title: "${controller.labelTextTripDetail['passenger_cancel_ride_btn_label'] ?? "Cancel ride"}", context: context, textColor: Colors.white),
                        onPressed: controller.removePassengerType.value != "" &&
                            controller.reviewTextEditingController.text != "" &&
                            controller.tripCancelTextEditingController.text != "" &&
                            ((controller.removePassengerType.value == "1" && controller.removePassenger.value != "") || controller.removePassengerType.value == "0") &&
                            ((controller.removePassenger.value == "temporarily" && controller.blockDaysTextEditingController.text != "") || controller.removePassenger.value == "permanently" || controller.removePassenger.value == "") ?
                            () async{
                          await controller.removePassengerFromRide(Get.parameters['rideId'] ?? "0");
                        }: null,
                        context: context
                    ),
                  ),
                ),
              ),
              if(controller.isOverlayLoading.value == true)...[
                overlayWidget(context),
              ]
            ],
          )
      ),
    );
  }
}
