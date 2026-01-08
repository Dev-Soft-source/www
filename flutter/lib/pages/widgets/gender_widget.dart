import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
Widget genderWidget({context, String title = "",  bool value = false, onChanged}){
  return Padding(
    padding: EdgeInsets.fromLTRB(
        getValueForScreenType<double>(
          context: context,
          mobile: 10.0,
          tablet: 10.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 0.0,
          tablet: 0.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 10.0,
          tablet: 10.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 0.0,
          tablet: 0.0,
        )
    ),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        txt16Size(title: title, fontFamily: bold, context: context, textColor: textColor),
        SizedBox(
          width: 25,
          height: 25,
          child: checkBoxWidget(
              value: value,
              activeColor: primaryColor,
              onChanged: onChanged
          ),
        ),
      ],
    ),
  );
}