import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/services/logger_service.dart';

Widget appNetworkImage({
  required String imageUrl,
  BoxFit fit = BoxFit.cover,
  double? width,
  double? height,
  Widget? errorChild,
}) {
  Widget fallback() {
    return SizedBox(
      width: width,
      height: height,
      child: Center(
        child: errorChild ??
            const Icon(
              Icons.image_not_supported_outlined,
              color: Colors.grey,
            ),
      ),
    );
  }

  final normalizedUrl = imageUrl.trim();
  if (normalizedUrl.isEmpty) {
    return fallback();
  }

  if (kIsWeb) {
    final parsedUrl = Uri.tryParse(normalizedUrl);
    final host = parsedUrl?.host.toLowerCase() ?? '';
    final shouldForceHtmlImageElement =
        host.contains('googleusercontent.com') ||
        host.contains('googleapis.com');

    return Image.network(
      normalizedUrl,
      width: width,
      height: height,
      fit: fit,
      webHtmlElementStrategy: shouldForceHtmlImageElement
          ? WebHtmlElementStrategy.fallback
          : WebHtmlElementStrategy.prefer,
      loadingBuilder: (context, child, loadingProgress) {
        if (loadingProgress == null) {
          return child;
        }
        return SizedBox(
          width: width,
          height: height,
          child: Center(child: progressCircularWidget(context)),
        );
      },
      errorBuilder: (context, error, stackTrace) {
        logger.warning('Image.network failed for $normalizedUrl -> $error');
        return fallback();
      },
    );
  }

  return CachedNetworkImage(
    imageUrl: normalizedUrl,
    width: width,
    height: height,
    fit: fit,
    placeholder: (context, url) => SizedBox(
      width: width,
      height: height,
      child: Center(child: progressCircularWidget(context)),
    ),
    errorWidget: (context, url, error) {
      logger.error('CachedNetworkImage failed for $url -> $error');
      return fallback();
    },
  );
}
