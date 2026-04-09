import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/co_passenger_widget.dart';

Widget myCoPassengerWidget({context, coPassengerList, controller, String tripId = "", double screenWidth = 0.0, String type = "trip",
  String tripCoPassengerHeading = "My co-passenger(s)", String rideCoPassengerHeading = "My passengers", String review = "Review"}){
  final ageLabel =
      "${controller.labelTextDetail['passenger_age_label'] ?? "Age"}";
  return InkWell(
    onTap: (){
      if(type == "trip"){
        if(coPassengerList != null && coPassengerList.isNotEmpty){
          Get.toNamed("/co_passenger/$tripId");
        }
      }

      if(type == "ride"){
        if(coPassengerList != null && coPassengerList.isNotEmpty){
          Get.toNamed("/my_passenger/$tripId");
        }
      }
    },
    child: cardShadowWidget(
      margin: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
        context: context,
        widgetChild: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            postRideWidget(title: type == "trip" ? tripCoPassengerHeading : rideCoPassengerHeading, screenWidth: screenWidth, context: context),
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
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.start,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if(coPassengerList != null && coPassengerList.isNotEmpty)...[
                      for(var i= 0; i<coPassengerList.length; i++ )...[
                        5.heightBox,
                        SingleChildScrollView(
                          scrollDirection: Axis.horizontal,
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              circleImageWidget(
                                  width: 36,
                                  height: 36,
                                  imageType: "network",
                                  imagePath: coPassengerProfileImageUrl(
                                      coPassengerList[i]),
                                  context: context),
                              10.widthBox,
                              txt20Size(
                                  title: myCoPassengerSummaryLine(
                                    coPassengerList[i],
                                    ageLabel: ageLabel,
                                    review: review,
                                    noReviewsLabel:
                                        "${controller.labelTextDetail['no_reviews_label'] ?? "No review yet"}",
                                  ),
                                  context: context,
                                  fontFamily: bold)
                            ],
                          ),
                        ),
                        5.heightBox,
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
