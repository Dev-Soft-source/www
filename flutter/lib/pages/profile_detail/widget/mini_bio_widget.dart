import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget miniBioWidget({context, String miniBio = ""}){
  return Container(
      padding: EdgeInsets.only(
          left: getValueForScreenType<double>(
            context: context,
            mobile: 15.0,
            tablet: 15.0,
          )),
      child: txt18Size(
          title: miniBio,
          fontFamily: regular,
          textColor: textColor,
          context: context
      )
  );
}