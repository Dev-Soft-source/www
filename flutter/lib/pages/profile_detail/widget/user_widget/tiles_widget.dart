import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget tileWidget({String imagePath ="", String title ="", String totalNumber = "", context, Color textColor = textColor}){
  return Expanded(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Image.asset(
            imagePath,
            height: getValueForScreenType<double>(
              context: context,
              mobile: 25.0,
              tablet: 25.0,
            ),
          ),
          2.heightBox,
          txt24Size(title: totalNumber, textColor: textColor, fontFamily: bold, context: context),
          2.heightBox,
          txt18SizeCenter(title: title, textColor: textColor, fontFamily: bold, context: context)
        ],
      )
  );
}