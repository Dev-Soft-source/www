import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget linkWidget({String imagePath ="", String title ="", context, int index = 0, onTap, Color textColor = textColor}){
  return InkWell(
    onTap: onTap,
    child: Ink(
        padding: EdgeInsets.all(getValueForScreenType<double>(
          context: context,
          mobile: 8.0,
          tablet: 8.0,
        )),
        decoration: BoxDecoration(
            border: Border.all(color: primaryColor, width: 1, style: BorderStyle.solid),
            borderRadius: BorderRadius.circular(10.0)
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.start,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                index == 0 ? CircleAvatar(
                  backgroundImage: NetworkImage(imagePath),
                  radius: getValueForScreenType<double>(
                    context: context,
                    mobile: 25.0,
                    tablet: 25.0,
                  ),
                ) : Image.asset(
                  imagePath,
                  height: getValueForScreenType<double>(
                    context: context,
                    mobile: 26.0,
                    tablet: 26.0,
                  ),
                  width: getValueForScreenType<double>(
                    context: context,
                    mobile: 26.0,
                    tablet: 26.0,
                  ),
                ),
                10.widthBox,
                index == 0 ?
                txt22SizeCapitalized(title: title, fontFamily: bold, textColor: textColor, context: context) :
                txt20Size(title: title, fontFamily: bold, textColor: textColor, context: context),
              ],
            ),
            Image.asset(
              rightArrowImage,
              height: getValueForScreenType<double>(
                context: context,
                mobile: 15.0,
                tablet: 15.0,
              ),
              width: getValueForScreenType<double>(
                context: context,
                mobile: 15.0,
                tablet: 15.0,
              )
            )
          ],
        )
    ),
  );
}