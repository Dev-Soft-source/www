import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
Widget preFixIconWidget({context, String imagePath = ""}){
  return Container(
    padding: EdgeInsets.all(getValueForScreenType<double>(
      context: context,
      mobile: 12.0,
      tablet: 12.0,
    )),
    height: getValueForScreenType<double>(
      context: context,
      mobile: 24.0,
      tablet: 24.0,
    ),
    width: getValueForScreenType<double>(
      context: context,
      mobile: 24.0,
      tablet: 24.0,
    ),
    child: Image.asset(
      imagePath,
      fit: BoxFit.contain,
    ),
  );
}
