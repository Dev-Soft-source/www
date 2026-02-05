import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
Widget seatBookedRowWidget({context, String title = "", String value = ""}){
  return Container(
    padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
      context: context,
      mobile: 15.0,
      tablet: 15.0,
    ),getValueForScreenType<double>(
      context: context,
      mobile: 0.0,
      tablet: 0.0,
    ),getValueForScreenType<double>(
      context: context,
      mobile: 15.0,
      tablet: 15.0,
    ),getValueForScreenType<double>(
      context: context,
      mobile: 0.0,
      tablet: 0.0,
    )),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        txt18Size(title: title, context: context),
        txt18Size(title: value, context: context)
      ],
    ),
  );
}