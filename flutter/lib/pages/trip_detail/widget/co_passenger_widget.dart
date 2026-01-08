import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget coPassengerWidget({context, coPassengerList, String tripId = "", double screenWidth = 0.0, String heading = "Co-passenger"}){
  return InkWell(
    onTap: (){
      if(coPassengerList != null && coPassengerList.isNotEmpty){
        //Get.toNamed("/co_passenger/$tripId");
      }
    },
    child: cardShadowWidget(
        context: context,
        widgetChild: Column(
          crossAxisAlignment: CrossAxisAlignment.center ,
          children: [
            postRideWidget(title: heading, screenWidth: screenWidth, context: context),
            Container(
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
              child: SingleChildScrollView(
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    if(coPassengerList != null && coPassengerList.isNotEmpty)...[
                      for(var i= 0; i<coPassengerList.length; i++ )...[
                        circleImageWidget(width: 36, height: 36, imageType: "network", imagePath: "${coPassengerList[i]['passenger']['profile_image']}", context: context),
                        5.widthBox
                      ]
                    ]
                  ],
                ),
              )
            ),
          ],
        )
    ),
  );
}