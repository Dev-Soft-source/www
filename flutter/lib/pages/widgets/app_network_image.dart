import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/const_api.dart';
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
  final lowerNormalizedUrl = normalizedUrl.toLowerCase();
  if (normalizedUrl.isEmpty ||
      lowerNormalizedUrl == 'null' ||
      lowerNormalizedUrl == 'undefined') {
    return fallback();
  }
  final absoluteUrlMatches = RegExp(r'https?://').allMatches(normalizedUrl).toList();
  final embeddedAbsoluteUrlIndex =
      absoluteUrlMatches.length > 1 ? absoluteUrlMatches.last.start : -1;
  final extractedUrl = embeddedAbsoluteUrlIndex > 0
      ? normalizedUrl.substring(embeddedAbsoluteUrlIndex)
      : normalizedUrl;
  final appBaseUri = Uri.tryParse(url);
  final parsedExtractedUrl = Uri.tryParse(extractedUrl);
  final isAbsoluteHttpUrl = parsedExtractedUrl != null &&
      (parsedExtractedUrl.scheme == 'http' || parsedExtractedUrl.scheme == 'https');
  final isProtocolRelative = extractedUrl.startsWith('//');
  var resolvedUrl = extractedUrl;

  if (isProtocolRelative) {
    final baseScheme = appBaseUri?.scheme == 'https' ? 'https' : 'http';
    resolvedUrl = '$baseScheme:$extractedUrl';
  } else if (!isAbsoluteHttpUrl) {
    resolvedUrl = appBaseUri?.resolve(extractedUrl).toString() ?? extractedUrl;
  }

  final parsedResolvedUrl = Uri.tryParse(resolvedUrl);
  if (parsedResolvedUrl != null && !kIsWeb) {
    final host = parsedResolvedUrl.host.toLowerCase();
    if (host == '127.0.0.1' || host == 'localhost') {
      if (defaultTargetPlatform == TargetPlatform.android) {
        final emulatorUri = parsedResolvedUrl.replace(host: '10.0.2.2');
        resolvedUrl = emulatorUri.toString();
      }
    }
  }

  if (resolvedUrl.isEmpty) {
    return fallback();
  }

  if (kIsWeb) {
    final parsedUrl = Uri.tryParse(resolvedUrl);
    final host = parsedUrl?.host.toLowerCase() ?? '';
    final shouldForceHtmlImageElement =
        host.contains('googleusercontent.com') ||
        host.contains('googleapis.com');

    return Image.network(
      resolvedUrl,
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
        logger.warning('Image.network failed for $resolvedUrl -> $error');
        return fallback();
      },
    );
  }

  return CachedNetworkImage(
    imageUrl: resolvedUrl,
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
