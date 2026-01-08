
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_passenger/MyPassengerController.dart';
import 'package:proximaride_app/pages/my_passenger/widget/header_widget.dart';
import 'package:proximaride_app/pages/my_passenger/widget/seat_booked_row_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';


class MyPassengerPage extends GetView<MyPassengerController> {
  const MyPassengerPage({super.key});

  @override

  Widget build(BuildContext context) {
    Get.put(MyPassengerController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "My passengers"}", context: context)),
          leading: const BackButton(
              color: Colors.white
          ),
        ),

        body: Obx(() {
          if(controller.isLoading.value == true){
            return Center(child: progressCircularWidget(context));
          }else{

            return Container(
              padding: const EdgeInsets.all(15.0),
              child: ListView.separated(
                itemCount: controller.myPassengers.length,
                itemBuilder: (context, index){
                  var dateTimeString = "${controller.myPassengers[index]['ride']['date']} ${controller.myPassengers[index]['ride']['time']}";
                  DateFormat dateFormat = DateFormat("yyyy-MM-dd HH:mm:ss");
                  DateTime dateTime = dateFormat.parse(dateTimeString);
                  DateTime currentDateTime = DateTime.now();

                  return cardShadowWidget(
                    context: context,
                    widgetChild: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        headerWidget(
                          context: context,
                          name: "${controller.myPassengers[index]['passenger'] != null ? controller.myPassengers[index]['passenger']['first_name'] : ""}",
                          screenWidth: context.screenWidth,
                          age: "${controller.myPassengers[index]['passenger'] != null ? controller.myPassengers[index]['passenger']['age'] : ""}",
                          gender: "${controller.myPassengers[index]['passenger'] != null ? controller.myPassengers[index]['passenger']['gender_label'] : ""}",
                          image: "${controller.myPassengers[index]['passenger'] != null ? controller.myPassengers[index]['passenger']['profile_image'] : ""}",
                          controller: controller,
                          onTap: (){
                            Get.toNamed('/profile_detail/passenger/${controller.myPassengers[index]['passenger'] != null ? controller.myPassengers[index]['passenger']['id'] : "0"}/0');
                          }
                        ),
                        10.heightBox,
                        seatBookedRowWidget(title: "${controller.labelTextDetail['seat_booked_label'] ?? "Seat booked"}", value: "${controller.myPassengers[index]['seats']}", context: context),
                        const Divider(),
                        seatBookedRowWidget(title: "${controller.labelTextDetail['my_fare_label'] ?? "My fare"}", value: "${controller.myPassengers[index]['fare'] ?? 0}", context: context),
                        const Divider(),
                        seatBookedRowWidget(title: "${controller.labelTextDetail['booking_fee_label'] ?? "Booking fee"}", value: double.parse(controller.myPassengers[index]['booking_credit'].toString()).toStringAsFixed(1), context: context),
                        const Divider(),
                        seatBookedRowWidget(title: "${controller.labelTextDetail['total_amount_label'] ?? "Total amount"}", value: (double.parse(controller.myPassengers[index]['booking_credit'].toString()) + double.parse(controller.myPassengers[index]['fare'] != null ? controller.myPassengers[index]['fare'].toString(): '0')).toStringAsFixed(1), context: context),
                        const Divider(),
                        10.heightBox,
                        Padding(
                          padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
                            context: context,
                            mobile: 15.0,
                            tablet: 15.0,
                          ),getValueForScreenType<double>(
                            context: context,
                            mobile: 0.0,
                            tablet: 0.0,
                          ),getValueForScreenType<double>(
                            context: context,
                            mobile: 15.0,
                            tablet: 15.0,
                          ),getValueForScreenType<double>(
                            context: context,
                            mobile: 0.0,
                            tablet: 0.0,
                          )),
                          child: SizedBox(
                            height: 50.0,
                            width: context.screenWidth,
                            child: elevatedButtonWidget(
                              context: context,
                              textWidget: txt22Size(context: context, title: "${controller.labelTextDetail['chat_passenger_btn_label'] ?? "Chat with passenger"}", textColor: Colors.white),
                              onPressed: (){
                                Get.toNamed('/messaging_page/${controller.myPassengers[index]['user_id']}/${controller.myPassengers[index]['ride_id']}/new');
                              }
                            ),
                          ),
                        ),
                        if(dateTime.isAfter(currentDateTime))...[
                          10.heightBox,
                          Padding(
                            padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
                              context: context,
                              mobile: 15.0,
                              tablet: 15.0,
                            ),getValueForScreenType<double>(
                              context: context,
                              mobile: 0.0,
                              tablet: 0.0,
                            ),getValueForScreenType<double>(
                              context: context,
                              mobile: 15.0,
                              tablet: 15.0,
                            ),getValueForScreenType<double>(
                              context: context,
                              mobile: 0.0,
                              tablet: 0.0,
                            )),
                            child: SizedBox(
                              height: 50.0,
                              width: context.screenWidth,
                              child: elevatedButtonWidget(
                                  context: context,
                                  textWidget: txt22Size(context: context, title: "${controller.labelTextDetail['remove_ride_btn_label'] ?? "Remove from this ride"}", textColor: Colors.white),
                                  onPressed: (){
                                    Get.toNamed("/remove_passenger/${controller.myPassengers[index]['id']}");
                                  },
                                  btnColor: Colors.red
                              ),
                            ),
                          ),
                        ]else...[
                          10.heightBox,
                          Padding(
                            padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
                              context: context,
                              mobile: 15.0,
                              tablet: 15.0,
                            ),getValueForScreenType<double>(
                              context: context,
                              mobile: 0.0,
                              tablet: 0.0,
                            ),getValueForScreenType<double>(
                              context: context,
                              mobile: 15.0,
                              tablet: 15.0,
                            ),getValueForScreenType<double>(
                              context: context,
                              mobile: 0.0,
                              tablet: 0.0,
                            )),
                            child: SizedBox(
                              height: 50.0,
                              width: context.screenWidth,
                              child: elevatedButtonWidget(
                                  context: context,
                                  textWidget: txt22Size(context: context, title: "${controller.labelTextDetail['no_show_passenger_label'] ?? "No show passenger"}", textColor: Colors.white),
                                  onPressed: () async{
                                    await controller.noShowDriverData('${controller.myPassengers[index]['id']}', '${controller.myPassengers[index]['ride']['id']}', '${controller.myPassengers[index]['user_id']}');
                                  },
                                  btnColor: Colors.red
                              ),
                            ),
                          ),
                        ],

                        10.heightBox,

                      ],
                    )
                  );
                },
                separatorBuilder: (context, index){
                  return const SizedBox();
                },
              )
            );
          }
        })
    );
  }
}

