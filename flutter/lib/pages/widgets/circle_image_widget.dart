import 'package:flutter/material.dart';
import 'package:proximaride_app/helpers/image_url.dart';
import '../../consts/constFileLink.dart';

Widget circleImageWidget(
    {double width = 0.0,
    double height = 0.0,
    String imageType = "",
    String imagePath = "",
    double borderRadius = 50.0,
    context,
    bgColor = primaryColor}) {
  final normalizedUrl = normalizeImageUrl(imagePath);

  return Container(
    height: getValueForScreenType<double>(
      context: context,
      mobile: height,
      tablet: height,
    ),
    width: getValueForScreenType<double>(
      context: context,
      mobile: width,
      tablet: width,
    ),
    decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(borderRadius),
        color: bgColor.withOpacity(0.3),
        image: imageType != "local"
            ? DecorationImage(
                image: NetworkImage(normalizedUrl),
              )
            : DecorationImage(
                image: AssetImage(imagePath),
              )),
  );
}
