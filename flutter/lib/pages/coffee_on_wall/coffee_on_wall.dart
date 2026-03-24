import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/web_page_fallback_widget.dart';
import 'package:proximaride_app/services/service.dart';
import 'package:webview_flutter/webview_flutter.dart';

class CoffeeOnWall extends StatefulWidget {
  const CoffeeOnWall({super.key});

  @override
  State<CoffeeOnWall> createState() => _CoffeeOnWallState();
}

class _CoffeeOnWallState extends State<CoffeeOnWall> {
  WebViewController? controller;
  final serviceController = Get.find<Service>();
  @override
  void initState() {
    super.initState();
    if (kIsWeb) {
      return;
    }
    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (int progress) {},
          onPageStarted: (String url) {},
          onPageFinished: (String url) {
            controller?.runJavaScriptReturningResult(
                'document.querySelector(".hideheader").style.setProperty("display", "none", "important");'
                'document.querySelector(".mb-0").style.setProperty("display", "none", "important");'
                'document.querySelector(".hidefooter").style.setProperty("display", "none", "important");'
                'document.querySelector(".hideLanguageIcon").style.setProperty("display", "none", "important");'
                'document.querySelector(".hideTopIcon").style.setProperty("display", "none", "important");');
          },
          onWebResourceError: (WebResourceError error) {},
          onNavigationRequest: (NavigationRequest request) {
            // Allow navigation only to the same domain to preserve data
            final initialUrl = Uri.parse(
                '$url/${serviceController.lang.value}/coffee-on-the-wall');
            final requestUrl = Uri.parse(request.url);

            // Check if the navigation is to the same host/domain
            if (requestUrl.host == initialUrl.host) {
              return NavigationDecision.navigate;
            }

            // Prevent external navigation to preserve app state
            return NavigationDecision.prevent;
          },
        ),
      )
      ..loadRequest(
          Uri.parse('$url/${serviceController.lang.value}/coffee-on-the-wall'));
  }

  @override
  Widget build(BuildContext context) {
    final String pageUrl =
        '$url/${serviceController.lang.value}/coffee-on-the-wall';
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: secondAppBarWidget(
            title: serviceController.coffeeOnWallLabel.value, context: context),
        leading: safeBackButton(context),
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.only(left: 15.0, right: 15.0),
          child: kIsWeb
              ? WebPageFallbackWidget(
                  pageUrl: pageUrl,
                  title: serviceController.coffeeOnWallLabel.value,
                )
              : WebViewWidget(controller: controller!),
        ),
      ),
    );
  }
}

