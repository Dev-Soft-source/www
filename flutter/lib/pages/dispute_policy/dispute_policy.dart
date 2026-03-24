import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/web_page_fallback_widget.dart';
import 'package:proximaride_app/services/service.dart';
import 'package:webview_flutter/webview_flutter.dart';

class DisputePolicy extends StatefulWidget {
  const DisputePolicy({super.key});

  @override
  State<DisputePolicy> createState() => _DisputePolicyState();
}

class _DisputePolicyState extends State<DisputePolicy> {
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
          onProgress: (int progress) {
            controller?.runJavaScriptReturningResult(
                'document.querySelector(".hideheader").style.setProperty("display", "none", "important");'
                'document.querySelector(".hideheader1").style.setProperty("display", "none", "important");'
                'document.querySelector(".hidefooter").style.setProperty("display", "none", "important");'
                'document.querySelector(".hideLanguageIcon").style.setProperty("display", "none", "important");'
                'document.querySelector(".hideTopIcon").style.setProperty("display", "none", "important");');
          },
          onPageStarted: (String url) {
            // Future.delayed(const Duration(seconds: 1), (){
            //   controller.runJavaScriptReturningResult(
            //       'document.querySelector(".hideheader").style.setProperty("display", "none", "important");'
            //           'document.querySelector(".hideheader1").style.setProperty("display", "none", "important");'
            //           'document.querySelector(".hidefooter").style.setProperty("display", "none", "important");'
            //           'document.querySelector(".hideLanguageIcon").style.setProperty("display", "none", "important");'
            //           'document.querySelector(".hideTopIcon").style.setProperty("display", "none", "important");'
            //   );
            // });
          },
          onPageFinished: (String url) {},
          onWebResourceError: (WebResourceError error) {},
          onNavigationRequest: (NavigationRequest request) {
            // if (request.url.startsWith('https://www.youtube.com/')) {
            //   return NavigationDecision.prevent;
            // }
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(
          Uri.parse('$url/${serviceController.lang.value}/dispute-policy'));
  }

  @override
  Widget build(BuildContext context) {
    final String pageUrl =
        '$url/${serviceController.lang.value}/dispute-policy';
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: secondAppBarWidget(
            title: serviceController.disputePolicyLabel.value,
            context: context),
        leading: safeBackButton(context),
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.only(left: 15.0, right: 15.0),
          child: kIsWeb
              ? WebPageFallbackWidget(
                  pageUrl: pageUrl,
                  title: serviceController.disputePolicyLabel.value,
                )
              : WebViewWidget(controller: controller!),
        ),
      ),
    );
  }
}

