import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget dataRowWidget({
  context,
  title,
  data,
  onTap,
  Color priceColor = textColor
}) {
  return Container(
      margin: EdgeInsets.only(left: 10.0, right: 10.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.start,
        children: [
          Expanded(
              child: txt20Size(
                  title: '$title', fontFamily: regular, context: context)),
          if (onTap != null) ...[
            InkWell(
              onTap: onTap,
              child: textWithUnderLine(context: context, title: "$data", decorationColor: primaryColor, fontFamily: regular, textSize: 18.0, textColor: primaryColor)
            )
          ] else ...[
            txt18Size(
                title: '$data', fontFamily: bold, context: context, textColor: priceColor),
          ]
        ],
      ));
}
