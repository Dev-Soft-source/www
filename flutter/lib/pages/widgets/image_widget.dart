
import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import '../../consts/constFileLink.dart';


Widget imageWidget({context, onTap, double screenWidth = 0.0, String imagePath = ""}){
  return InkWell(
    onTap: onTap,
    child: DottedBorder(
      color: primaryColor,
      borderType: BorderType.RRect,
      dashPattern: const [4,6],
      radius: const Radius.circular(12),
      // padding: const EdgeInsets.all(6),
      child: Container(
        padding: const EdgeInsets.fromLTRB(0.0, 0.0, 0.0, 0.0),
        height: screenWidth,
        width: screenWidth,
        child:  ClipRRect(
          borderRadius: BorderRadius.circular(5.0),
          child: networkCacheImageWidget(imagePath, BoxFit.fill, 0.0, 0.0)
        ),
      ),
    ),
  );
}