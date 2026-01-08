import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class ReviewPassengerPage extends StatelessWidget {
  const ReviewPassengerPage({super.key});

  @override
  Widget build(BuildContext context) {
    var controller = Get.find<MyTripController>();

    return Scaffold(
      appBar: AppBar(
        leading: const BackButton(color: Colors.white),
        title: Obx(() => secondAppBarWidget(context: context, title: "${controller.labelTextTripDetail['review_passengers_heading'] ?? "Review passengers"}")),
        backgroundColor: primaryColor,
      ),
      body: Obx(() =>
          Stack(
            children: [
              Container(
                padding: const EdgeInsets.all(15.0),
                child: ListView.separated(
                  itemCount: controller.cancelRideInfo['bookings'].length,
                  itemBuilder: (context, index){

                    return Container(
                      padding: const EdgeInsets.all(15.0),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(5.0),
                        color: Colors.grey.shade200
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              circleImageWidget(
                                width: 60.0,
                                height: 60.0,
                                imagePath: controller.cancelRideInfo['bookings'] != null &&
                                    controller.cancelRideInfo['bookings'][index]['passenger'] != null ?
                                    controller.cancelRideInfo['bookings'][index]['passenger']['profile_image'] ?? "" : "",
                                imageType: "network",
                                context: context,
                                borderRadius: 5.0
                              ),
                              10.widthBox,
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  txt22SizeCapitalized(context: context, title: "${controller.cancelRideInfo['bookings'] != null &&
                                      controller.cancelRideInfo['bookings'][index]['passenger'] != null ?
                                  controller.cancelRideInfo['bookings'][index]['passenger']['first_name'] : ""} ${controller.cancelRideInfo['bookings'] != null &&
                                      controller.cancelRideInfo['bookings'][index]['passenger'] != null ?
                                  controller.cancelRideInfo['bookings'][index]['passenger']['last_name'] : ""}"),
                                  10.heightBox,
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.start,
                                    children: [
                                      Image.asset(reviewsImage, width: 12),
                                      5.widthBox,
                                      txt16Size(context: context, title: "${controller.cancelRideInfo['bookings'] != null && controller.cancelRideInfo['bookings'][index]['passenger_average_rating'] != null ?
                                      controller.cancelRideInfo['bookings'][index]['passenger_average_rating'].toStringAsFixed(1) ?? "" : ""}"),
                                    ],
                                  )
                                ],
                              ),
                            ],
                          ),
                          if(controller.cancelRideInfo['bookings'][index]['rating'] != null)...[
                            5.widthBox,
                            Expanded(child: InkWell(
                              onTap: (){
                                Get.toNamed('/review_detail/${controller.cancelRideInfo['bookings'][index]['rating']['id']}/from/passenger');
                              },
                              child: textWithUnderLine(
                                  title: "${controller.labelTextTripDetail['review_passengers_i_review_label'] ??"I reviewed"}",
                                  context: context,
                                  fontFamily: bold,
                                  textColor: primaryColor,
                                  decorationColor: primaryColor,
                                  textSize: 16.0
                              ),
                            )),
                          ]else...[
                            5.widthBox,
                            Expanded(child: elevatedButtonWidget(
                                textWidget: txt18Size(context: context, title: "${controller.labelTextTripDetail['review_passengers_review_label'] ??"Review"}", textColor: Colors.white),
                                context: context,
                                onPressed: () async{

                                  await controller.addPassengerReview(
                                      controller.cancelRideInfo['id'],
                                      "passenger",
                                      controller.cancelRideInfo['bookings'] != null &&
                                          controller.cancelRideInfo['bookings'][index]['passenger'] != null ?
                                      controller.cancelRideInfo['bookings'][index]['passenger']['profile_image'] : "",
                                      "${controller.cancelRideInfo['bookings'] != null &&
                                          controller.cancelRideInfo['bookings'][index]['passenger'] != null ?
                                      controller.cancelRideInfo['bookings'][index]['passenger']['first_name'] : ""} ${controller.cancelRideInfo['bookings'] != null &&
                                          controller.cancelRideInfo['bookings'][index]['passenger'] != null ?
                                      controller.cancelRideInfo['bookings'][index]['passenger']['last_name'] : ""}",
                                      controller.cancelRideInfo['bookings'] != null ?
                                      controller.cancelRideInfo['bookings'][index]['id'].toString() : ""
                                  );
                                }
                            ))
                          ],

                        ],
                      ),
                    );
                  },
                  separatorBuilder: (context, index){
                    return 10.heightBox;
                  },
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
