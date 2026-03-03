import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/helpers/image_url.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';

Widget networkCacheImageWidget(imagePath, boxType, imageWidth, imageHeight) {
  final normalizedUrl = normalizeImageUrl(imagePath?.toString());

  if (imageWidth == 0.0 && imageHeight == 0.0) {
    return CachedNetworkImage(
      imageUrl: normalizedUrl,
      key: UniqueKey(),
      fit: boxType,
      placeholder: (context, url) => Center(child: progressCircularWidget(context)),
      errorWidget: (context, url, error) => const Icon(Icons.error),
    );
  } else {
    return CachedNetworkImage(
      imageUrl: normalizedUrl,
      key: UniqueKey(),
      height: imageHeight,
      width: imageWidth,
      fit: boxType,
      placeholder: (context, url) => Center(child: progressCircularWidget(context)),
      errorWidget: (context, url, error) => const Icon(Icons.error),
    );
  }
}
