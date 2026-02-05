import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';

Widget otherLogInWidget({String imagePath = "", context, onTap}) {
  return InkWell(
    onTap: onTap,
    child: Container(
      width: getValueForScreenType<double>(
        context: context,
        mobile: 40.0,
        tablet: 40.0,
      ),
      height: getValueForScreenType<double>(
        context: context,
        mobile: 40.0,
        tablet: 40.0,
      ),
      decoration: BoxDecoration(
          border: Border.all(
              width: 1, color: Colors.grey, style: BorderStyle.solid),
          borderRadius: BorderRadius.circular(5.0)),
      child: Center(
          child: Image.asset(
        imagePath,
        height: getValueForScreenType<double>(
          context: context,
          mobile: 25.0,
          tablet: 25.0,
        ),
        width: getValueForScreenType<double>(
          context: context,
          mobile: 25.0,
          tablet: 25.0,
        ),
      )),
    ),
  );
}
