import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/image_url.dart';

Widget profileImageWidget(
    {required controller,
    context,
    String imagePath = "",
    double mobileRadius = 0.0,
    double tabletRadius = 0.0}) {
  final normalizedUrl = normalizeImageUrl(imagePath);

  return CircleAvatar(
    radius: getValueForScreenType<double>(
      context: context,
      mobile: mobileRadius,
      tablet: tabletRadius,
    ),
    child: ClipRRect(
      borderRadius: BorderRadius.circular(50.0),
      child: CachedNetworkImage(
          width: double.infinity,
          height: double.infinity,
          fit: BoxFit.cover,
          imageUrl: normalizedUrl,
          errorWidget: (context, url, error) =>
              controller.serviceController.loginUserDetail['gender'] == 'female'
                  ? Image.asset(defaultFemaleImage,
                      width: double.infinity,
                      height: double.infinity,
                      fit: BoxFit.cover)
                  : Image.asset(defaultMaleImage,
                      width: double.infinity,
                      height: double.infinity,
                      fit: BoxFit.cover)),
    ),
  );
}
