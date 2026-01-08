import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:flutter/material.dart';

Widget overlayWidget(context){
  return Container(
      color: Colors.black.withOpacity(0.6),
      child: Center(
        child: Image.asset(
          proximaRideAppOverlay,
          width: getValueForScreenType<double>(
            context: context,
            mobile: 80.0,
            tablet: 80.0,
          ),
          height: getValueForScreenType<double>(
            context: context,
            mobile: 80.0,
            tablet: 80.0,
          ),
        ),
      )
  );
}