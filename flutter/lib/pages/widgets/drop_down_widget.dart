import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/color.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../consts/constFileLink.dart';

Widget dropDownWidget({String title = "", double screenWidth = 0.0, context, double height = 50.0, onTap, String placeHolder = ""}){
  return InkWell(
    onTap: onTap,
    child: Container(
        width: screenWidth,
        height: height,
        padding: EdgeInsets.all(10.0),
        decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(5.0),
            border: Border.all(width: 1, color: Colors.grey.shade400),
            color: inputColor
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            txt18Size(title: placeHolder, context: context, textColor: textColor, fontFamily: regular),
            Image.asset(downArrowImage, height: 20,)
          ],
        )
    ),
  );
}