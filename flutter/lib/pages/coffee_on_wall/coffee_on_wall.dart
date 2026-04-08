import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/web_page_fallback_widget.dart';
import 'package:proximaride_app/services/service.dart';
import 'package:webview_flutter/webview_flutter.dart';

/// Hides site chrome in the embedded WebView. Null-safe: missing nodes are skipped
/// (avoids "Cannot read properties of null (reading 'style')" after PayPal redirects).
const String _kCoffeeWallHideChromeJs = '''
(function(){
  function hide(sel) {
    var el = document.querySelector(sel);
    if (el && el.style) {
      el.style.setProperty('display', 'none', 'important');
    }
  }
  hide('.hideheader');
  hide('.mb-0');
  hide('.hidefooter');
  hide('.hideLanguageIcon');
  hide('.hideTopIcon');
})();
''';

/// Allow same-origin navigation plus payment provider flows (PayPal checkout, Stripe 3DS).
bool _isAllowedCoffeeWallNavigation(Uri requestUrl, Uri appBaseUrl) {
  if (requestUrl.host == appBaseUrl.host) {
    return true;
  }
  final h = requestUrl.host.toLowerCase();
  if (h == 'paypal.com' ||
      h.endsWith('.paypal.com') ||
      h == 'stripe.com' ||
      h.endsWith('.stripe.com')) {
    return true;
  }
  return false;
}

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
            controller?.runJavaScript(_kCoffeeWallHideChromeJs);
          },
          onWebResourceError: (WebResourceError error) {},
          onNavigationRequest: (NavigationRequest request) {
            final appBaseUrl = Uri.parse(
                '$url/${serviceController.lang.value}/coffee-on-the-wall');
            final requestUrl = Uri.parse(request.url);
            if (_isAllowedCoffeeWallNavigation(requestUrl, appBaseUrl)) {
              return NavigationDecision.navigate;
            }
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

