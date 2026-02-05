import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
Widget seatImageWidget({context, onTap, bool isActive = false, bool isError = false, Color seatColor = Colors.white}){
  return InkWell(
    splashColor: Colors.transparent,
    borderRadius: BorderRadius.circular(50.0),
    onTap: onTap,
    child: Ink(
      padding: EdgeInsets.all(getValueForScreenType<double>(
        context: context,
        mobile: 10.0,
        tablet: 10.0,
      )),
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(50.0),
          border: Border.all(width: 2, color: isError ? Colors.red : Colors.grey.shade400),
          color: seatColor
      ),
      child: Image.asset(isActive == true ? seatIconFilledImage : seatImage, width: getValueForScreenType<double>(
        context: context,
        mobile: 17.0,
        tablet: 17.0,
      ), height: getValueForScreenType<double>(
        context: context,
        mobile: 19.0,
        tablet: 19.0,
      )),
    ),
  );
}