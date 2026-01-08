import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:app_links/app_links.dart';
import 'package:proximaride_app/services/service.dart';

class DeepLinkController extends GetxController {
  late final AppLinks _appLinks;
  final serviceController = Get.find<Service>();

  @override
  void onInit() {
    super.onInit();
    _initDeepLinks();
  }

  void _initDeepLinks() async {
    _appLinks = AppLinks();
    _appLinks.uriLinkStream.listen((Uri? uri) {
      if (uri != null) {
        _handleDeepLink(uri);
      }
    }, onError: (err) {
      print('Error receiving deep link: $err');
    });
  }

  void _handleDeepLink(Uri uri) {
    print('Received deep link: $uri');
    if (uri.scheme == 'xelentride' && uri.host == 'booking') {
      final bookingId = uri.queryParameters['booking_id'];
      final action = uri.queryParameters['action'];

      if (bookingId != null && action != null) {
        if(action != "accept" && action != "reject"){

        }else{
          serviceController.openDeepLinkPage.value = true;
          serviceController.actionDeep.value = action;
          serviceController.bookingDeepId.value = bookingId.toString();
          Get.offAll(() => Container(child: Text("")));
        }
      }
    } else {
      print('Deep link does not match app link pattern.');
    }
  }
}