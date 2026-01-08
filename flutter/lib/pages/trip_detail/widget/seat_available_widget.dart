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
        postRideWidget(title: "Seats available", screenWidth: screenWidth, context: context),
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
                  for(var i= 1; i <= int.parse(controller.ride['seats_left'].toString()); i++)...[
                    seatImageWidget(context: context, onTap: null, isActive: false),
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