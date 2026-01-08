import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';

Widget seatNumberWidget(
    {context,
    onTap,
    bool isActive = false,
    String title = "",
    bool isError = false}) {
  return Expanded(
      child: InkWell(
    borderRadius: BorderRadius.circular(5.0),
    onTap: onTap,
    child: Ink(
        padding: EdgeInsets.all(getValueForScreenType<double>(
          context: context,
          mobile: 15.0,
          tablet: 15.0,
        )),
        decoration: BoxDecoration(
          border: Border.all(width: 1, color: isError ? Colors.red : Colors.grey.shade400),
          borderRadius: BorderRadius.circular(5.0),
          color: isActive == true ? primaryColor : Colors.white,
        ),
        child: Center(
          child: txt18Size(
              title: title,
              context: context,
              textColor: isActive == true ? Colors.white : textColor,
              fontFamily: regular),
        )),
  ));
}
