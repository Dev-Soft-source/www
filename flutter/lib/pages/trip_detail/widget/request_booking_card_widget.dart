import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget requestBookingCardWidget({context, booking, onPressedAccept, onPressedReject, String ageLabel = "Age",
  String reviewLabel = "Review", String seatRequestLabel = "seat requested", String acceptBtnLabel = "Accept", String rejectBtnLabel = "Reject"}){
  return Container(
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
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: Row(
                mainAxisAlignment: MainAxisAlignment.start,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  circleImageWidget(width: 60.0, height: 60.0, imageType: "network", imagePath: booking['passenger'] != null ? booking['passenger']['profile_image'] : "", context: context),
                  5.widthBox,
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      txt22SizeCapitalized(title: "${booking['passenger']['first_name']}", context: context),
                      5.heightBox,
                      txt16Size(context: context, title: "$ageLabel: ${booking['passenger']['age']}  ${booking['passenger']['gender_label']} $reviewLabel: ${booking['passenger']['average_rating'] != null ? booking['passenger']['average_rating'].ceil() : "0" }", fontFamily: bold),
                      5.heightBox,
                      txt16Size(context: context, title: "${booking['seats']} $seatRequestLabel", textColor: primaryColor),
                    ],
                  )
                ],
              ),
            ),

          ],
        ),
        10.heightBox,
        Row(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            Expanded(
                child: elevatedButtonWidget(
                    textWidget: txt22Size(context: context, title: rejectBtnLabel, textColor: Colors.white),
                    context: context,
                    onPressed: onPressedReject,
                  btnColor: primaryColor
                )
            ),
            5.widthBox,
            Expanded(
                child: elevatedButtonWidget(
                    textWidget: txt22Size(context: context, title: acceptBtnLabel, textColor: Colors.white),
                    context: context,
                    onPressed: onPressedAccept
                )
            )
          ],
        )
      ],
    ),
  );
}