
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import '../../consts/constFileLink.dart';



Widget circleIconWidget({double width = 0.0, double height = 0.0, String imagePath = "",  context}){
  return SizedBox(
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
    child: Center(
      child: networkCacheImageWidget(imagePath, BoxFit.contain, 0.0, 0.0),
    ),
  );
}
