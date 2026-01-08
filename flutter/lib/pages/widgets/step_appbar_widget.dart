import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/language_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import '../../consts/constFileLink.dart';

Widget stepAppBarWidget({context, serviceController, langId, langIcon, screeWidth, page}){
  return Container(
    padding: EdgeInsets.only(bottom: getValueForScreenType<double>(
      context: context,
      mobile: 10.0,
      tablet: 10.0,
    )),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Image.asset(
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
        InkWell(
          onTap: (){
            languageBottomSheet(screeWidth, serviceController, page: page);
          },
          child: Ink(
            padding: const EdgeInsets.all(5.0),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(50.0),
              child: networkCacheImageWidget(langIcon, BoxFit.cover, 30.0, 30.0),
            ),
          ),
        )
      ],
    ),
  );
}