import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/profile_detail/widget/user_widget/line_widget.dart';
import 'package:proximaride_app/pages/profile_detail/widget/user_widget/tiles_widget.dart';

Widget userPassengerDrivenWidget({context, String passengerDriven = "", String rideTaken = "", String kmShared = "" , String passengerDrivenLabel = "Passenger driven",
  String ridesTakenLabel = "Rides taken", String kmSharedLabel = "KM shared"}){
  return Container(
    padding: EdgeInsets.all(getValueForScreenType<double>(
      context: context,
      mobile: 15.0,
      tablet: 15.0,
    )),
    color: primaryColor.withOpacity(0.15),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        tileWidget(
            imagePath: passengerDrivenImage,
            title: passengerDrivenLabel,
            totalNumber: passengerDriven,
            textColor: textColor,
            context: context),
        5.widthBox,
        lineWidget(),
        tileWidget(
            imagePath: rideTakenImage,
            title: ridesTakenLabel,
            totalNumber: rideTaken,
            textColor: textColor,
            context: context),
        5.widthBox,
        lineWidget(),
        tileWidget(
            imagePath: kmSharedImage,
            title: kmSharedLabel,
            totalNumber: kmShared,
            textColor: textColor,
            context: context),
      ],
    ),
  );
}