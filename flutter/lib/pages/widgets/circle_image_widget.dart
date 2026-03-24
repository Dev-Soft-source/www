import 'package:flutter/material.dart';
import '../../consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/app_network_image.dart';

Widget circleImageWidget(
    {double width = 0.0,
    double height = 0.0,
    String imageType = "",
    String imagePath = "",
    double borderRadius = 50.0,
    context,
    bgColor = primaryColor}) {
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
    ),
    child: ClipRRect(
      borderRadius: BorderRadius.circular(borderRadius),
      child: imageType != "local"
          ? appNetworkImage(
              imageUrl: imagePath,
              width: double.infinity,
              height: double.infinity,
              fit: BoxFit.cover,
              errorChild: Icon(
                Icons.image_outlined,
                color: bgColor,
              ),
            )
          : Image.asset(
              imagePath,
              width: double.infinity,
              height: double.infinity,
              fit: BoxFit.cover,
            ),
    ),
  );
}
