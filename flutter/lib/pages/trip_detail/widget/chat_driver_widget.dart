import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget chatDriverWidget({context, double screenWidth = 0.0 , driverId, rideId, String heading = "Chat with the driver"}){
  return cardShadowWidget(
      context: context,
      widgetChild: Column(
        children: [
          postRideWidget(title: heading, screenWidth: screenWidth, context: context),
          Container(
            color: btnPrimaryColor,
              padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
                context: context,
                mobile: 15.0,
                tablet: 15.0,
              ),getValueForScreenType<double>(
                context: context,
                mobile: 10.0,
                tablet: 10.0,
              ),getValueForScreenType<double>(
                context: context,
                mobile: 15.0,
                tablet: 15.0,
              ),getValueForScreenType<double>(
                context: context,
                mobile: 10.0,
                tablet: 10.0,
              )),
              child: InkWell(
                onTap: (){
                  Get.toNamed('/messaging_page/$driverId/$rideId/new');
                },
                child: txt16Size(
                  title: "Ask the driver any questions you want, especially if you have extra luggage, kids, or if you need a custom pick-up, or custom drop-off",
                  fontFamily: bold,
                  context: context,
                  textColor: Colors.white
                ),
              )
          ),
        ],
      )
  );
}