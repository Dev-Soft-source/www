import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';

Widget profileImageWidget({context, String imagePath = "", double mobileRadius = 0.0, double tabletRadius = 0.0}){
  return CircleAvatar(
    backgroundImage: NetworkImage(
        imagePath),
    radius: getValueForScreenType<double>(
      context: context,
      mobile: mobileRadius,
      tablet: tabletRadius,
    ),
  );
}