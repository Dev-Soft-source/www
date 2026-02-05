import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
Widget imageEditDeleteWidget({String imagePath = "", context, onTap, imageFile}){
  return InkWell(
    onTap: onTap,
    child: Container(
      padding: const EdgeInsets.all(5.0),
      width: getValueForScreenType<double>(
        context: context,
        mobile: 28.0,
        tablet: 28.0,
      ),
      height: getValueForScreenType<double>(
        context: context,
        mobile: 28.0,
        tablet: 28.0,
      ),
      decoration: BoxDecoration(
          color: primaryColor,
          borderRadius: BorderRadius.circular(5.0)
      ),
      child: Image.asset(
        imagePath,
        width: getValueForScreenType<double>(
          context: context,
          mobile: 15.0,
          tablet: 15.0,
        ),
        height: getValueForScreenType<double>(
          context: context,
          mobile: 16.0,
          tablet: 16.0,
        ),
      ),
    )
  );
}