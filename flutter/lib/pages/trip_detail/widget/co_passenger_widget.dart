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
        margin: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
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
                        circleImageWidget(
                            width: 36,
                            height: 36,
                            imageType: "network",
                            imagePath: coPassengerProfileImageUrl(coPassengerList[i]),
                            context: context),
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

String coPassengerProfileImageUrl(dynamic booking) {
  if (booking is! Map) {
    return '';
  }
  final passenger = booking['passenger'];
  if (passenger is! Map) {
    return '';
  }
  final url = passenger['profile_image'];
  if (url == null) {
    return '';
  }
  return url.toString();
}

/// One-line summary when [booking] may have a null `passenger` (API shape).
String myCoPassengerSummaryLine(
  dynamic booking, {
  required String ageLabel,
  required String review,
  required String noReviewsLabel,
}) {
  if (booking is! Map) {
    return '';
  }
  final passenger = booking['passenger'];
  final Map<String, dynamic> pm = passenger is Map
      ? Map<String, dynamic>.from(passenger)
      : <String, dynamic>{};
  final firstName = (pm['first_name'] ?? '').toString();
  final age = (pm['age'] ?? '').toString();
  final genderLabel = (pm['gender_label'] ?? '').toString();
  final avg = booking['passenger_average_rating'];
  final reviewPart = (avg == null || avg.toString().isEmpty)
      ? noReviewsLabel
      : '$review: $avg';
  return '$firstName | $ageLabel: $age | $genderLabel | $reviewPart';
}