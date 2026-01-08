import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/post_ride/widget/seat_image_widget.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget seatAvailableWidget({context, controller, screenWidth}){
  return cardShadowWidget(
      context: context,
    widgetChild: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        postRideWidget(title: "${controller.labelTextDetail['seats_available_label'] ?? "Seats available"}", screenWidth: screenWidth, context: context,isRequired: true, infoIcon: "${controller.labelTextDetail['seats_available_info_text'] ?? "Seats available info text"}"),
        Container(
          padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
            context: context,
            mobile: 10.0,
            tablet: 10.0,
          ),getValueForScreenType<double>(
            context: context,
            mobile: 10.0,
            tablet: 10.0,
          ),getValueForScreenType<double>(
            context: context,
            mobile: 10.0,
            tablet: 10.0,
          ),getValueForScreenType<double>(
            context: context,
            mobile: 10.0,
            tablet: 10.0,
          )),
          child: SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.start,
              children: [
                if(controller.ride['seats_left'] != null)...[

                  if(controller.alreadyBookedSeat.value != 0)...[
                    for(var i= 1; i <= controller.currentUserBookedSeat.value; i++)...[
                      seatImageWidget(context: context, onTap: (){}, isActive: true, seatColor: primaryColor),
                      5.widthBox,
                    ]
                  ],

                  for(var i= 1; i <= controller.ride['pending_seat_detail'].length; i++)...[
                    seatImageWidget(context: context, onTap: () async{
                      await controller.seatOnHold(controller.ride['pending_seat_detail'][i-1]['id'], i);
                    }, isActive: controller.ride['pending_seat_detail'][i-1]['status'] != "pending"  ? true : false, seatColor: controller.ride['pending_seat_detail'][i-1]['status'] != "pending"  ? seatOnHoldColor : Colors.white),
                    5.widthBox,
                  ]
                ],
              ],
            ),
          ),
        ),
        10.heightBox,
      ],
    )
  );
}