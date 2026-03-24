import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/app_network_image.dart';

Widget networkCacheImageWidget(imagePath, boxType, imageWidth, imageHeight){
  return appNetworkImage(
    imageUrl: imagePath ?? "",
    width: imageWidth == 0.0 ? null : imageWidth,
    height: imageHeight == 0.0 ? null : imageHeight,
    fit: boxType,
    errorChild: const Icon(Icons.error),
  );
}
