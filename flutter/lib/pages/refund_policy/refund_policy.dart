import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/services/service.dart';
import 'package:webview_flutter/webview_flutter.dart';

class RefundPolicy extends StatefulWidget {
  const RefundPolicy({super.key});

  @override
  State<RefundPolicy> createState() => _RefundPolicyState();
}

class _RefundPolicyState extends State<RefundPolicy> {
  late final WebViewController controller;
  final serviceController = Get.find<Service>();
  @override

  void initState() {
    super.initState();
    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (int progress) {
            controller.runJavaScriptReturningResult(
                'document.querySelectorAll(".hideheader").forEach(el => el.style.setProperty("display", "none", "important"));'
                    'document.querySelectorAll(".hideheader1").forEach(el => el.style.setProperty("display", "none", "important"));'
                    'document.querySelectorAll(".hidefooter").forEach(el => el.style.setProperty("display", "none", "important"));'
                    'document.querySelectorAll(".hideLanguageIcon").forEach(el => el.style.setProperty("display", "none", "important"));'
                    'document.querySelectorAll(".hideTopIcon").forEach(el => el.style.setProperty("display", "none", "important"));'
            );
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
          onPageFinished: (String url) {


          },
          onWebResourceError: (WebResourceError error) {},
          onNavigationRequest: (NavigationRequest request) {
            // if (request.url.startsWith('https://www.youtube.com/')) {
            //   return NavigationDecision.prevent;
            // }
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse('$url/${serviceController.lang.value}/refund-policy'));
  }

  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: secondAppBarWidget(
            title:
            serviceController.refundPolicyLabel.value,
            context: context),
        leading: const BackButton(color: Colors.white),
      ),
      body: Padding(
        padding: const EdgeInsets.only(left: 15.0, right: 15.0),
        child: WebViewWidget(controller: controller),
      ),
    );
  }
}
