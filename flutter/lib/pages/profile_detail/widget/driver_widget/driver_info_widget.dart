import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/profile_detail/widget/profile_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget driverInfoWidget(
    {required controller,
    context,
    String imagePath = "",
    String driverName = "",
    String driven = "",
    String gender = "",
    String date = "",
    String passengerDrivenLabel = "Passenger driven",
    String joinedLabel = "Joined"}) {
  DateTime parsedDate = DateTime.parse(date);
  DateFormat outputFormat = DateFormat('MMMM d, yyyy');
  String joinDate = outputFormat.format(parsedDate);

  return Row(
    mainAxisAlignment: MainAxisAlignment.start,
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      profileImageWidget(
          controller: controller,
          context: context,
          imagePath: imagePath,
          mobileRadius: 35.0,
          tabletRadius: 35.0),
      5.widthBox,
      Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Main name/title: 25px
          txt25Size(
              title: driverName, context: context, textColor: primaryColor),
          // Supporting line: 20px
          txt20Size(
              title: "$passengerDrivenLabel: $driven", context: context),
          3.heightBox,
          // Meta info: 20px
          txt20Size(
              context: context,
              fontFamily: bold,
              title: "$gender - $joinedLabel: $joinDate")
        ],
      )
    ],
  );
}
