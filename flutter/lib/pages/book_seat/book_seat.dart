
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/book_seat/BookSeatController.dart';
import 'package:proximaride_app/pages/book_seat/widget/cancellation_policy_widget.dart';
import 'package:proximaride_app/pages/book_seat/widget/online_payment_widget.dart';
import 'package:proximaride_app/pages/book_seat/widget/pricing_widget.dart';
import 'package:proximaride_app/pages/book_seat/widget/seat_available_widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';

import '../widgets/button_Widget.dart';
import '../widgets/tool_tip.dart';


class BookSeatPage extends GetView<BookSeatController> {
  const BookSeatPage({super.key});

  @override

  Widget build(BuildContext context) {
    Get.put(BookSeatController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(title:"${controller.labelTextDetail['main_heading'] ?? "Book your seat(s)"}", context: context)),
        leading: const BackButton(
            color: Colors.white
        ),
      ),


      body: Obx(() {
        if(controller.isLoading.value == true){
          return Center(child: progressCircularWidget(context));
        }else{
          return Stack(
            children: [
              Container(
                padding: const EdgeInsets.all(15.0),
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      txt18Size(context: context,textColor: Colors.red,title: '* ${controller.labelTextDetail['required_fields'] ?? "Indicates required field"}'),
                      10.heightBox,
                      seatAvailableWidget(context: context, controller: controller, screenWidth: context.screenWidth),
                      if(controller.errors.firstWhereOrNull((element) => element['title'] == "seats") != null) ...[
                        toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "seats"))
                      ],
                      // if(controller.ride['booking_type_slug'] == 'firm')...[
                      //   10.heightBox,
                      //   cancellationPolicyWidget(context: context,controller: controller,screenWidth: context.screenWidth),
                      //   if(controller.errors.firstWhereOrNull((element) => element['title'] == "policy") != null) ...[
                      //     toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "policy"))
                      //   ],
                      // ],
                      10.heightBox,
                      pricingWidget(context: context, controller: controller, screenWidth: context.screenWidth),
                      10.heightBox,

                      txt20Size(context: context, title: "${controller.labelTextDetail['message_to_driver_label'] ?? "Message to driver"}"),
                      3.heightBox,
                      textAreaWidget(
                        textController: controller.messageDriverTextEditingController,
                        readonly: false,
                        fontSize: 18.0,
                        fontFamily: regular,
                        placeHolder: "${controller.labelTextDetail['message_driver_placeholder'] ?? "Tell the driver why you're traveling, introduce yourself, or just say hi\nDrivers are more likely to accept passengers who introduce themselves"}",
                        maxLines: 4,
                        onChanged: (value){
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "message") != null)
                          {
                            controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "message"));
                          }
                        },
                      ),
                      if(controller.errors.firstWhereOrNull((element) => element['title'] == "message") != null) ...[
                        toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "message"))
                      ],
                      10.heightBox,
                      txt16Size(
                          title:
                          "${controller.labelTextDetail['booking_disclaimer_on_time'] ?? "I will show up at least ten minutes before the time of the ride. If I am late, the driver has the right to leave without me and I will not be refunded"}",
                          fontFamily: bold,
                          context: context),
                      3.heightBox,
                      txt16Size(
                          title:
                          "${controller.labelTextDetail['booking_disclaimer_pink_ride'] ?? "I know that Pink Rides are exclusive to ProximaRide female members. If I am booking on a Pink Ride, I will not be accompanied by male members who are above 12 years of age, nor will I send a male member in my place. If I do, the driver will not take me or them, and I will not be refunded"}",
                          fontFamily: bold,
                          context: context),
                      3.heightBox,
                      txt16Size(
                          title:
                          "${controller.labelTextDetail['booking_disclaimer_extra_care_ride'] ?? "I know that Extra-Care Rides are exclusive to members with highest review score. If I am booking on an Extra-Care Ride, I will adhere to its standards"}",
                          fontFamily: bold,
                          context: context),
                      3.heightBox,
                      Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          SizedBox(
                            width: 35,
                            height: 35,
                            child: checkBoxWidget(
                              value: controller.agreeTerms.value,
                              onChanged: (value){
                                controller.agreeTerms.value = value!;
                                if(controller.agreeTerms.value == true){
                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "agree_terms"));
                                }else{
                                  var err = {
                                    'title': "agree_terms",
                                    'eList': ['Please select agree terms']
                                  };
                                  controller.errors.add(err);
                                }
                              },
                              isError: controller.errors.isNotEmpty && controller.errors.firstWhereOrNull((element) => element['title'] == "agree_terms") != null ? true : false,
                            ),
                          ),
                          3.widthBox,
                          Expanded(
                            child: InkWell(
                              onTap: () async{
                                controller.agreeTerms.value = controller.agreeTerms.value == true ? false : true;
                                if(controller.agreeTerms.value == true){
                                  controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "agree_terms"));
                                }else{
                                  var err = {
                                    'title': "agree_terms",
                                    'eList': ['Please select agree terms']
                                  };
                                  controller.errors.add(err);
                                }
                              },
                              child: txt16Size(
                                    title:
                                    "${controller.labelTextDetail['booking_term_agree_text'] ?? "I agree to these rules, and I have read, and agree to ProximaRide's terms and conditions. I also confirm that I am at least 18 years of age"}",
                                    fontFamily: bold,
                                    context: context),
                            ),
                          ),

                        ],
                      ),
                      if(controller.policyTypeId.value == "37")...[
                        3.heightBox,
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            SizedBox(
                              width: 35,
                              height: 35,
                              child: checkBoxWidget(
                                value: controller.firmAgreeTerms.value,
                                onChanged: (value){
                                  controller.firmAgreeTerms.value = value!;
                                  if(controller.firmAgreeTerms.value == true){
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "firm_agree_terms"));
                                  }else{
                                    var err = {
                                      'title': "firm_agree_terms",
                                      'eList': ['Please select agree terms']
                                    };
                                    controller.errors.add(err);
                                  }
                                },
                                isError: controller.errors.isNotEmpty && controller.errors.firstWhereOrNull((element) => element['title'] == "firm_agree_terms") !=null ? true : false,
                              ),
                            ),
                            3.widthBox,
                            Expanded(
                              child: InkWell(
                                onTap: () async{
                                  controller.firmAgreeTerms.value = controller.firmAgreeTerms.value == true ? false : true;
                                  if(controller.firmAgreeTerms.value == true){
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "firm_agree_terms"));
                                  }else{
                                    var err = {
                                      'title': "firm_agree_terms",
                                      'eList': ['Please select agree terms']
                                    };
                                    controller.errors.add(err);
                                  }
                                },
                                child: txt16Size(
                                    title: controller.firmDisclaimer.value != "" ? controller.firmDisclaimer.value : "I know that this ride has the Firm cancellation policy which entitles me to a 10% discount of the booking price, and it is not refundable; regardless of the cancellation time",
                                    fontFamily: bold,
                                    context: context),
                              ),
                            ),

                          ],
                        ),
                      ],
                      if(controller.showPinkCheckBox.value == true)...[
                        3.heightBox,
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            SizedBox(
                              width: 35,
                              height: 35,
                              child: checkBoxWidget(
                                value: controller.pinkAgreeTerms.value,
                                onChanged: (value){
                                  controller.pinkAgreeTerms.value = value!;
                                  if(controller.pinkAgreeTerms.value == true){
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "pink_agree_terms"));
                                  }else{
                                    var err = {
                                      'title': "pink_agree_terms",
                                      'eList': ['Please select agree terms']
                                    };
                                    controller.errors.add(err);
                                  }
                                },
                                isError: controller.errors.isNotEmpty && controller.errors.firstWhereOrNull((element) => element['title'] == "pink_agree_terms") !=null ? true : false,
                              ),
                            ),
                            3.widthBox,
                            Expanded(
                              child: InkWell(
                                onTap: (){
                                  controller.pinkAgreeTerms.value = controller.pinkAgreeTerms.value == true ? false : true;
                                  if(controller.pinkAgreeTerms.value == true){
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "pink_agree_terms"));
                                  }else{
                                    var err = {
                                      'title': "pink_agree_terms",
                                      'eList': ['Please select agree terms']
                                    };
                                    controller.errors.add(err);
                                  }
                                },
                                child: txt16Size(
                                    title: controller.pinkDisclaimer.value != "" ? controller.pinkDisclaimer.value : "Pink ride disclaimer",
                                    fontFamily: bold,
                                    context: context),
                              ),
                            ),

                          ],
                        ),
                      ],
                      if(controller.showExtraCareCheckBox.value == true)...[
                        3.heightBox,
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            SizedBox(
                              width: 35,
                              height: 35,
                              child: checkBoxWidget(
                                value: controller.extraCareAgreeTerms.value,
                                onChanged: (value){
                                  controller.extraCareAgreeTerms.value = value!;
                                  if(controller.extraCareAgreeTerms.value == true){
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "extra_agree_terms"));
                                  }else{
                                    var err = {
                                      'title': "pink_agree_terms",
                                      'eList': ['Please select agree terms']
                                    };
                                    controller.errors.add(err);
                                  }
                                },
                                isError: controller.errors.isNotEmpty && controller.errors.firstWhereOrNull((element) => element['title'] == "extra_agree_terms") !=null ? true : false,
                              ),
                            ),
                            3.widthBox,
                            Expanded(
                              child: InkWell(
                                onTap: (){
                                  controller.extraCareAgreeTerms.value = controller.extraCareAgreeTerms.value == true ? false : true;
                                  if(controller.extraCareAgreeTerms.value == true){
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "extra_agree_terms"));
                                  }else{
                                    var err = {
                                      'title': "pink_agree_terms",
                                      'eList': ['Please select agree terms']
                                    };
                                    controller.errors.add(err);
                                  }
                                },
                                child: txt16Size(
                                    title: controller.extraCareDisclaimer.value != "" ? controller.extraCareDisclaimer.value : "Extra ride disclaimer",
                                    fontFamily: bold,
                                    context: context),
                              ),
                            ),

                          ],
                        ),
                      ],
                      10.heightBox,
                      if((controller.ride['payment_method_slug'] == "cash" && (double.parse(controller.ride['ride_detail'][0]['price'].toString()) <= 15)) || controller.bookedByWallet.value == true)...[
                        Align(
                          alignment: Alignment.bottomCenter,
                          child: SizedBox(
                            width: context.screenWidth,
                            child: elevatedButtonWidget(
                              textWidget: txt28Size(
                                  title: "${controller.labelTextDetail['book_seat_button_label'] ?? "Book seat(s)"}",
                                  fontFamily: regular,
                                  textColor: Colors.white,
                                  context: context),
                              onPressed: controller.agreeTerms.value != true && controller.firmAgreeTerms.value != true ? null : () async {
                                await controller.bookingRidePaymentType(paymentType: "cash");
                              },
                              context: context,
                              btnRadius: 5.0,
                            ),
                          ),
                        ),
                      ]else if(controller.ride['payment_method_slug'] == "cash" && controller.coffeeFromWall.value == true)...[
                        Align(
                          alignment: Alignment.bottomCenter,
                          child: SizedBox(
                            width: context.screenWidth,
                            child: elevatedButtonWidget(
                              textWidget: txt28Size(
                                  title: "${controller.labelTextDetail['book_seat_button_label'] ?? "Book seat(s)"}",
                                  fontFamily: regular,
                                  textColor: Colors.white,
                                  context: context),
                              onPressed: controller.agreeTerms.value != true && controller.firmAgreeTerms.value != true && controller.pinkAgreeTerms.value != true && controller.extraCareAgreeTerms.value != true ? null : () async {
                                await controller.bookingRidePaymentType(paymentType: "cash");
                              },
                              context: context,
                              btnRadius: 5.0,
                            ),
                          ),
                        ),
                      ]else...[
                      onlinePaymentWidget(context: context, screenWidth: context.screenWidth, controller: controller),
                      ],
                      20.heightBox,

                    ],
                  ),
                ),
              ),
              if(controller.isOverlayLoading.value == true)...[
                overlayWidget(context)
              ]
            ],
          );
        }
      })
    );
  }
}

