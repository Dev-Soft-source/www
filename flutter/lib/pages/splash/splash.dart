import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/splash/SplashController.dart';



class SplashPage extends GetView<SplashController> {
  const SplashPage({super.key});
  @override
  Widget build(BuildContext context) {
    Get.put(SplashController());
    controller.getScreenSize(context);
    return Container(
      color: Colors.white,
      child: Center(
        child: Image.asset(
          proximaRideAppOverlay,
          width: getValueForScreenType<double>(
            context: context,
            mobile: 100.0,
            tablet: 100.0,
          ),
          height: getValueForScreenType<double>(
            context: context,
            mobile: 100.0,
            tablet: 100.0,
          ),
        ),
      )
    );
  }
}

