import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
Widget otherLogInWidget({String imagePath = "", context, onTap}){
  return InkWell(
    onTap: onTap,
    child: Container(
      width: getValueForScreenType<double>(
        context: context,
        mobile: 48.0,
        tablet: 48.0,
      ),
      height: getValueForScreenType<double>(
        context: context,
        mobile: 48.0,
        tablet: 48.0,
      ),
      decoration: BoxDecoration(
          border: Border.all(width: 1, color: Colors.grey, style: BorderStyle.solid),
          borderRadius: BorderRadius.circular(5.0)
      ),
      child: Center(
          child: Image.asset(
            imagePath,
            height: getValueForScreenType<double>(
              context: context,
              mobile: 30.0,
              tablet: 30.0,
            ),
            width: getValueForScreenType<double>(
              context: context,
              mobile: 30.0,
              tablet: 30.0,
            ),
          )
      ),
    ),
  );
}