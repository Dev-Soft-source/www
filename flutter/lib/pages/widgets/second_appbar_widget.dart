
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/service.dart';
import '../../consts/constFileLink.dart';



Widget secondAppBarWidget({String title = "", context}){
  return SizedBox(
    height: kToolbarHeight,
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Expanded(
          child: Align(
            alignment: Alignment.centerLeft,
            child: txt25Size(
              title: title,
              fontFamily: regular,
              textColor: Colors.white,
              context: context,
            ),
          ),
        ),
        Align(
          alignment: Alignment.center,
          child: Image.asset(
            headerLogoImage,
            width: getValueForScreenType<double>(
              context: context,
              mobile: 50.0,
              tablet: 50.0,
            ),
            height: getValueForScreenType<double>(
              context: context,
              mobile: 50.0,
              tablet: 50.0,
            ),
          ),
        ),
      ],
    ),
  );
}

Widget safeBackButton(
  BuildContext context, {
  Color color = Colors.white,
  String? authenticatedFallbackRoute,
  String unauthenticatedFallbackRoute = '/login',
}) {
  return IconButton(
    icon: Icon(Icons.arrow_back, color: color),
    onPressed: () {
      if (Get.key.currentState?.canPop() ?? false) {
        Get.back();
        return;
      }

      if (Navigator.of(context).canPop()) {
        Navigator.of(context).pop();
        return;
      }

      final hasService = Get.isRegistered<Service>();
      final isAuthenticated = hasService && Get.find<Service>().token.isNotEmpty;
      final fallbackRoute =
          isAuthenticated ? (authenticatedFallbackRoute ?? '/navigation') : unauthenticatedFallbackRoute;

      if (Get.currentRoute != fallbackRoute) {
        Get.offNamed(fallbackRoute);
      }
    },
  );
}


Widget iconGrid({ String imagePath = "", onTap, context }){
  return InkWell(
    onTap: onTap,
    child: Ink(
      padding: const EdgeInsets.all(5.0),
      decoration: BoxDecoration(
          border: Border.all(color: Colors.white, style: BorderStyle.solid),
          borderRadius: BorderRadius.circular(5.0)
      ),
      child: Image.asset(imagePath, width: getValueForScreenType<double>(
        context: context,
        mobile: 25.0,
        tablet: 25.0,
      ), height: getValueForScreenType<double>(
        context: context,
        mobile: 25.0,
        tablet: 25.0,
      ),),
    ),
  );
}
