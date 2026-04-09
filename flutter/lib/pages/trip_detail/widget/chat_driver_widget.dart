import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget chatDriverWidget({context, double screenWidth = 0.0 , driverId, rideId, String heading = "Chat with the driver", String label = "Ask the driver any questions you want, especially if you have extra luggage, kids, or if you need a custom pick-up, or custom drop-off"}) {
  return cardShadowWidget(
      context: context,
      margin: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      widgetChild: Column(
        children: [
          postRideWidget(title: heading, screenWidth: screenWidth, context: context),
          Container(
            color: Colors.white,
              padding: EdgeInsets.all(getValueForScreenType<double>(
                context: context,
                mobile: 8.0,
                tablet: 8.0,
              )),
              child: InkWell(
                onTap: (){
                  Get.toNamed('/messaging_page/$driverId/$rideId/new');
                },
                borderRadius: BorderRadius.circular(14),
                child: Container(
                  width: double.infinity,
                  padding: EdgeInsets.fromLTRB(
                    getValueForScreenType<double>(
                      context: context,
                      mobile: 16.0,
                      tablet: 16.0,
                    ),
                    getValueForScreenType<double>(
                      context: context,
                      mobile: 14.0,
                      tablet: 14.0,
                    ),
                    getValueForScreenType<double>(
                      context: context,
                      mobile: 16.0,
                      tablet: 16.0,
                    ),
                    getValueForScreenType<double>(
                      context: context,
                      mobile: 14.0,
                      tablet: 14.0,
                    ),
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFF25B7B2),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Icon(
                        Icons.chat_bubble_outline,
                        color: Colors.white,
                        size: 22,
                      ),
                      12.widthBox,
                      Expanded(
                        child: txt20Size(
                          title: label,
                          fontFamily: bold,
                          context: context,
                          textColor: Colors.white
                        ),
                      ),
                    ],
                  ),
                ),
              )
          ),
        ],
      )
  );
}
