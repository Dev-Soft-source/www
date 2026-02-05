
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:flutter/material.dart';

Widget progressCircularWidget(context, {double width = 200.0, double height = 200.0}){
  return Container(
      child: Image.asset(
        proximaRideApp,
        width: getValueForScreenType<double>(
          context: context,
          mobile: width,
          tablet: width,
        ),
        height: getValueForScreenType<double>(
          context: context,
          mobile: height,
          tablet: height,
        ),
      )
  );
}