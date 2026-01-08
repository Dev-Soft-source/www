import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget vehicleInfoWidget({context, String vehicleDetail = "", String vehicleImage = "", String vehicleId = "", String licenseNumber = "",  String carType = "",String rideId = "",
  double screenWidth = 0.0, String type = "", String heading = "Vehicle info", bool hidePhoto = true}){

  return InkWell(
    child: cardShadowWidget(
        context: context,
        widgetChild: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
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
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                       hidePhoto == true ? circleImageWidget(width: 60, height: 60, imageType: "network", imagePath: vehicleImage, borderRadius: 50.0, context: context) : SizedBox(),
                      5.widthBox,
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          txt20Size(title: vehicleDetail, context: context),
                          txt16Size(title: licenseNumber, context: context),
                          txt16Size(title: carType, context: context),
                        ],
                      )
                    ],
                  ),
                )
            ),
          ],
        )
    ),
  );
}