import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/app_network_image.dart';

Widget profileImageWidget(
    {required controller,
    context,
    String imagePath = "",
    double mobileRadius = 0.0,
    double tabletRadius = 0.0}) {
  return CircleAvatar(
    radius: getValueForScreenType<double>(
      context: context,
      mobile: mobileRadius,
      tablet: tabletRadius,
    ),
    child: ClipRRect(
      borderRadius: BorderRadius.circular(50.0),
      child: appNetworkImage(
        imageUrl: imagePath,
        width: double.infinity,
        height: double.infinity,
        fit: BoxFit.cover,
        errorChild:
            controller.serviceController.loginUserDetail['gender'] == 'female'
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
  );
}
