import 'package:flutter/material.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:url_launcher/url_launcher.dart';

class AppHtmlText extends StatelessWidget {
  final String data;
  final double fontSize;
  final String? fontFamily;
  final FontWeight? fontWeight;
  final Color? textColor;
  final Color? linkColor;
  final TextAlign? textAlign;
  final double lineHeight;
  final bool openLinksExternally;
  final Future<void> Function(String link)? onLinkTapCallback;

  const AppHtmlText({
    super.key,
    required this.data,
    this.fontSize = 16,
    this.fontFamily,
    this.fontWeight,
    this.textColor,
    this.linkColor,
    this.textAlign,
    this.lineHeight = 1.35,
    this.openLinksExternally = true,
    this.onLinkTapCallback,
  });

  @override
  Widget build(BuildContext context) {
    final String normalizedData = _decodeHtmlEntities(data);
    final Color resolvedTextColor =
        textColor ?? Theme.of(context).textTheme.bodyMedium?.color ?? Colors.black;

    return Html(
      data: normalizedData,
      style: {
        "html": Style(
          margin: Margins.zero,
          padding: HtmlPaddings.zero,
        ),
        "body": Style(
          margin: Margins.zero,
          padding: HtmlPaddings.zero,
          fontSize: FontSize(fontSize),
          fontFamily: fontFamily,
          fontWeight: fontWeight,
          color: resolvedTextColor,
          lineHeight: LineHeight(lineHeight),
          textAlign: textAlign,
        ),
        "p": Style(
          margin: Margins.zero,
          padding: HtmlPaddings.zero,
          fontSize: FontSize(fontSize),
          fontFamily: fontFamily,
          fontWeight: fontWeight,
          color: resolvedTextColor,
          lineHeight: LineHeight(lineHeight),
          textAlign: textAlign,
        ),
        "div": Style(
          margin: Margins.zero,
          padding: HtmlPaddings.zero,
          fontSize: FontSize(fontSize),
          fontFamily: fontFamily,
          fontWeight: fontWeight,
          color: resolvedTextColor,
          lineHeight: LineHeight(lineHeight),
          textAlign: textAlign,
        ),
        "span": Style(
          color: resolvedTextColor,
          fontSize: FontSize(fontSize),
          fontFamily: fontFamily,
          fontWeight: fontWeight,
          lineHeight: LineHeight(lineHeight),
        ),
        "strong": Style(
          fontWeight: FontWeight.bold,
          fontFamily: fontFamily,
          color: resolvedTextColor,
        ),
        "a": Style(
          color: linkColor ?? resolvedTextColor,
          textDecoration: TextDecoration.underline,
          fontWeight: FontWeight.w700,
        ),
      },
      onLinkTap: (link, attributes, element) async {
        if (link == null || link.trim().isEmpty) {
          return;
        }

        if (onLinkTapCallback != null) {
          await onLinkTapCallback!(link);
          return;
        }

        if (!openLinksExternally) {
          return;
        }

        final Uri? parsedLink = Uri.tryParse(link);
        final Uri targetUri = parsedLink != null && parsedLink.hasScheme
            ? parsedLink
            : Uri.parse(url).resolve(link);

        if (await canLaunchUrl(targetUri)) {
          await launchUrl(
            targetUri,
            mode: LaunchMode.externalApplication,
          );
        }
      },
    );
  }

  String _decodeHtmlEntities(String value) {
    return value
        .replaceAll('&lt;', '<')
        .replaceAll('&gt;', '>')
        .replaceAll('&quot;', '"')
        .replaceAll('&#39;', "'")
        .replaceAll('&apos;', "'")
        .replaceAll('&amp;', '&')
        .replaceAllMapped(RegExp(r'&#(\d+);'), (match) {
      final int? codePoint = int.tryParse(match.group(1) ?? '');
      return codePoint == null ? match.group(0)! : String.fromCharCode(codePoint);
    });
  }
}
