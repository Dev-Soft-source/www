import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:url_launcher/url_launcher.dart';

class WebPageFallbackWidget extends StatelessWidget {
  final String pageUrl;
  final String title;

  const WebPageFallbackWidget({
    super.key,
    required this.pageUrl,
    required this.title,
  });

  @override
  Widget build(BuildContext context) {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            txt20Size(
              title: "$title is available in your browser.",
              context: context,
              fontFamily: regular,
              textColor: textColor,
            ),
            16.heightBox,
            SelectableText(
              pageUrl,
              textAlign: TextAlign.center,
              style: TextStyle(
                color: primaryColor,
                fontFamily: regular,
                fontSize: 16,
              ),
            ),
            20.heightBox,
            SizedBox(
              width: 220,
              child: elevatedButtonWidget(
                textWidget: txt22Size(
                  title: "Open page",
                  context: context,
                  textColor: Colors.white,
                  fontFamily: regular,
                ),
                onPressed: () async {
                  final uri = Uri.parse(pageUrl);
                  await launchUrl(uri, mode: LaunchMode.platformDefault);
                },
                context: context,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
