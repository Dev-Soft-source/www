import 'package:flutter/material.dart';
// import 'package:proximaride_app/pages/widgets/language_bottom_sheet.dart';
// import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import '../../consts/constFileLink.dart';

Widget stepAppBarWidget(
    {context, serviceController, langId, langIcon, screeWidth, page}) {
  return Row(
    mainAxisAlignment: MainAxisAlignment.spaceBetween,
    children: [
      Container(
        padding: EdgeInsets.only(
            bottom: getValueForScreenType<double>(
          context: context,
          mobile: 10.0,
          tablet: 10.0,
        )),
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
      IconButton(
        icon: const Icon(
          Icons.close,
          color: Colors.black,
          size: 28.0,
        ),
        onPressed: () async {
          await serviceController.logoutUserFromStages();
        },
      ),
    ],
  );
}
