import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget driverInfoWidget({context, String driverName = "", String driverRating = "", String driverImage = "", String rideId = "",  double screenWidth = 0.0, String pageType ="0", String heading = "Driver info", bool hidePhoto = true}){
  return InkWell(
    onTap: (){

      Get.toNamed('/profile_detail/driver/$rideId/$pageType');
    },
    child: cardShadowWidget(
        context: context,
        widgetChild: Column(
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
                    hidePhoto == true ? circleImageWidget(width: 40, height: 40, imageType: "network", imagePath: driverImage, context: context) : SizedBox(),
                    5.widthBox,
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        txt20SizeCapitalize(title: driverName, context: context),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          children: [
                            Image.asset(reviewsImage, width: 14, height: 14),
                            txt16Size(title: driverRating, context: context, fontFamily: bold)
                          ],
                        )
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