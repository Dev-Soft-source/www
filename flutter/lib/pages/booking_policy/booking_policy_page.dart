import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/web_page_fallback_widget.dart';
import 'package:webview_flutter/webview_flutter.dart';

/// Resolves API-relative paths against [url] from [const_api.dart].
String resolveBookingPolicyPageUrl(String raw) {
  final t = raw.trim();
  if (t.isEmpty || t == 'null') {
    return '';
  }
  final parsed = Uri.tryParse(t);
  if (parsed != null &&
      parsed.hasScheme &&
      (parsed.scheme == 'http' || parsed.scheme == 'https')) {
    return t;
  }
  final base = url.endsWith('/') ? url.substring(0, url.length - 1) : url;
  if (t.startsWith('/')) {
    return '$base$t';
  }
  return '$base/$t';
}

/// In-app WebView for booking / cancellation policy URLs (same shell as [TermConditionPage]).
class BookingPolicyPage extends StatefulWidget {
  const BookingPolicyPage({super.key});

  @override
  State<BookingPolicyPage> createState() => _BookingPolicyPageState();
}

class _BookingPolicyPageState extends State<BookingPolicyPage> {
  WebViewController? controller;
  late String _pageUrl;
  late String _title;

  @override
  void initState() {
    super.initState();
    final args = Get.arguments;
    String rawUrl = '';
    _title = 'Page';
    if (args is Map) {
      rawUrl = args['url']?.toString() ?? '';
      final t = args['title']?.toString();
      if (t != null && t.trim().isNotEmpty) {
        _title = t.trim();
      }
    } else if (args is String) {
      rawUrl = args;
    }
    _pageUrl = resolveBookingPolicyPageUrl(rawUrl);

    if (kIsWeb || _pageUrl.isEmpty) {
      return;
    }
    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (int progress) {
            controller?.runJavaScriptReturningResult(
                'document.querySelector(".hideheader").style.setProperty("display", "none", "important");'
                'document.querySelector(".hideheader1").style.setProperty("display", "none", "important");'
                'document.querySelector(".hidefooter").style.setProperty("display", "none", "important");'
                'document.querySelector(".hideLanguageIcon").style.setProperty("display", "none", "important");'
                'document.querySelector(".hideTopIcon").style.setProperty("display", "none", "important");');
          },
          onPageStarted: (String url) {},
          onPageFinished: (String url) {},
          onWebResourceError: (WebResourceError error) {},
          onNavigationRequest: (NavigationRequest request) {
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(_pageUrl));
  }

  @override
  Widget build(BuildContext context) {
    if (_pageUrl.isEmpty) {
      return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: secondAppBarWidget(title: _title, context: context),
          leading: safeBackButton(context),
        ),
        body: const SafeArea(
          child: Center(
            child: Padding(
              padding: EdgeInsets.all(24.0),
              child: Text('Invalid or missing link.'),
            ),
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: secondAppBarWidget(title: _title, context: context),
        leading: safeBackButton(context),
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.only(left: 15.0, right: 15.0),
          child: kIsWeb
              ? WebPageFallbackWidget(
                  pageUrl: _pageUrl,
                  title: _title,
                )
              : WebViewWidget(controller: controller!),
        ),
      ),
    );
  }
}
