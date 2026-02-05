import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/profile_detail/widget/profile_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget passengerInfoWidget(
    {required controller,
    context,
    String imagePath = "",
    String passengerName = "",
    String gender = "",
    String date = "",
    String age = "",
    String yearOldLabel = "years old",
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
              title: passengerName, context: context, textColor: primaryColor),
          3.heightBox,
          // Meta lines: 20px
          txt20Size(
              context: context,
              fontFamily: bold,
              title: "$joinedLabel: $joinDate"),
          3.heightBox,
          txt20Size(title: "$gender, $age $yearOldLabel", context: context),
        ],
      )
    ],
  );
}
