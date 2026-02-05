import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
Widget preFixIconWidget({context, String imagePath = ""}){
  return Container( padding: EdgeInsets.all(getValueForScreenType<double>(
    context: context,
    mobile: 15.0,
    tablet: 15.0,
  )), height: getValueForScreenType<double>(
    context: context,
    mobile: 10.0,
    tablet: 10.0,
  ), width: getValueForScreenType<double>(
    context: context,
    mobile: 10.0,
    tablet: 10.0,
  ), child: Image.asset(imagePath));
}