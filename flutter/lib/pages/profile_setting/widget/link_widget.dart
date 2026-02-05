import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget linkWidget(
    {required controller,
    String imagePath = "",
    String title = "",
    context,
    int index = 0,
    onTap,
    Color textColor = textColor}) {
  return InkWell(
    onTap: onTap,
    child: Ink(
        padding: EdgeInsets.all(getValueForScreenType<double>(
          context: context,
          mobile: 8.0,
          tablet: 8.0,
        )),
        decoration: BoxDecoration(
            border: Border.all(
                color: primaryColor, width: 1, style: BorderStyle.solid),
            borderRadius: BorderRadius.circular(10.0)),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.start,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                index == 0
                    ? CircleAvatar(
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(50.0),
                          child: CachedNetworkImage(
                            imageUrl: imagePath,
                            width: double.infinity,
                            height: double.infinity,
                            fit: BoxFit.cover,
                            errorWidget: (context, url, error) => controller
                                        .serviceController
                                        .loginUserDetail['gender'] ==
                                    'female'
                                ? Image.asset(defaultFemaleImage,
                                    width: double.infinity,
                                    height: double.infinity,
                                    fit: BoxFit.cover)
                                : Image.asset(defaultMaleImage,
                                    width: double.infinity,
                                    height: double.infinity,
                                    fit: BoxFit.cover),
                          ),
                        ),
                        radius: getValueForScreenType<double>(
                          context: context,
                          mobile: 18.0,
                          tablet: 18.0,
                        ),
                      )
                    : Image.asset(imagePath,
                        height: getValueForScreenType<double>(
                          context: context,
                          mobile: 35.0,
                          tablet: 35.0,
                        )),
                10.widthBox,
                txt20Size(
                    title: title,
                    fontFamily: bold,
                    textColor: textColor,
                    context: context),
              ],
            ),
            Image.asset(rightArrowImage,
                height: getValueForScreenType<double>(
                  context: context,
                  mobile: 15.0,
                  tablet: 15.0,
                ),
                width: getValueForScreenType<double>(
                  context: context,
                  mobile: 15.0,
                  tablet: 15.0,
                ))
          ],
        )),
  );
}
