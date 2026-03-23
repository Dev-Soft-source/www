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
  });

  @override
  Widget build(BuildContext context) {
    final Color resolvedTextColor =
        textColor ?? Theme.of(context).textTheme.bodyMedium?.color ?? Colors.black;

    return Html(
      data: data,
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
      onLinkTap: openLinksExternally
          ? (link, attributes, element) async {
              if (link == null || link.trim().isEmpty) {
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
            }
          : null,
    );
  }
}
