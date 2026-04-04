import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/app_network_image.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget linkWidget(
    {required controller,
    String imagePath = "",
    String title = "",
    context,
    int index = 0,
    onTap,
    Color textColor = textColor,
    Color? backgroundColor,
    Color? iconColor}) {
  final borderColor =
      backgroundColor ?? primaryColor;
  return InkWell(
    onTap: onTap,
    child: Ink(
        padding: EdgeInsets.all(getValueForScreenType<double>(
          context: context,
          mobile: 8.0,
          tablet: 8.0,
        )),
        decoration: BoxDecoration(
            color: backgroundColor,
            border: Border.all(
                color: borderColor, width: 1, style: BorderStyle.solid),
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
                        radius: getValueForScreenType<double>(
                          context: context,
                          mobile: 25.0,
                          tablet: 25.0,
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(50.0),
                          child: appNetworkImage(
                            imageUrl: imagePath,
                            width: double.infinity,
                            height: double.infinity,
                            fit: BoxFit.cover,
                            errorChild: controller
                                        .serviceController
                                        .loginUserDetail['gender'] ==
                                    'female'
                                ? Image.asset(
                                    defaultFemaleImage,
                                    width: double.infinity,
                                    height: double.infinity,
                                    fit: BoxFit.cover,
                                  )
                                : Image.asset(
                                    defaultMaleImage,
                                    width: double.infinity,
                                    height: double.infinity,
                                    fit: BoxFit.cover,
                                  ),
                          ),
                        ),
                      )
                    : iconColor != null
                        ? ColorFiltered(
                            colorFilter: ColorFilter.mode(
                                iconColor, BlendMode.srcIn),
                            child: Image.asset(
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
                          )
                        : Image.asset(
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
                index == 0
                    ? txt22SizeCapitalized(
                        title: title,
                        fontFamily: bold,
                        textColor: textColor,
                        context: context)
                    : txt20Size(
                        title: title,
                        fontFamily: bold,
                        textColor: textColor,
                        context: context),
              ],
            ),
            iconColor != null
                ? ColorFiltered(
                    colorFilter:
                        ColorFilter.mode(iconColor, BlendMode.srcIn),
                    child: Image.asset(rightArrowImage,
                        height: getValueForScreenType<double>(
                          context: context,
                          mobile: 15.0,
                          tablet: 15.0,
                        ),
                        width: getValueForScreenType<double>(
                          context: context,
                          mobile: 15.0,
                          tablet: 15.0,
                        )),
                  )
                : Image.asset(rightArrowImage,
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
