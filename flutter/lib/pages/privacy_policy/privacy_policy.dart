import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/services/service.dart';
import 'package:webview_flutter/webview_flutter.dart';

class PrivacyPolicyPage extends StatefulWidget {
  const PrivacyPolicyPage({Key? key}) : super(key: key);

  @override
  State<PrivacyPolicyPage> createState() => _PrivacyPolicyPageState();
}

class _PrivacyPolicyPageState extends State<PrivacyPolicyPage> {
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
            //       'document.querySelector(".header").style.setProperty("display", "none", "important");'
            //       'document.querySelector(".mobileHideFooter").style.setProperty("display", "none", "important");'
            //       'document.querySelector(".hideFooter").style.setProperty("display", "none", "important");');
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
      ..loadRequest(Uri.parse('$url/${serviceController.lang.value}/privacy-policy'));
  }

  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: secondAppBarWidget(
            title:
            serviceController.privacyPolicyLabel.value,
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
