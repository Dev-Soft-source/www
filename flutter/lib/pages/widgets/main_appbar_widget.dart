
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/language_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';



Widget mainAppBarWidget(context1, langId, langIcon, screeWidth, serviceController){
  return Container(
    padding: const EdgeInsets.only(bottom: 10.0),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Image.asset(
          headerLogoImage,
          width: getValueForScreenType<double>(
            context: context1,
            mobile: 50.0,
            tablet: 50.0,
          ),
          height: getValueForScreenType<double>(
            context: context1,
            mobile: 50.0,
            tablet: 50.0,
          ),
        ),
        Row(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [

            iconGrid(imagePath: langIcon, onTap: (){
              languageBottomSheet(screeWidth, serviceController);
            }, context: context1, index: 0),
            5.widthBox,
            InkWell(
              onTap: (){
                Get.toNamed("/notifications");
              },
              child: Stack(
                children: [
                  Image.asset(notificationImage, width: getValueForScreenType<double>(
                    context: context1,
                    mobile: 35.0,
                    tablet: 35.0,
                  ), height: getValueForScreenType<double>(
                    context: context1,
                    mobile: 35.0,
                    tablet: 35.0,
                  )),
                  Positioned(
                      right: 0,
                      child: Container(
                        padding: EdgeInsets.all(2.0),
                        width: 20,
                        height: 20,
                        decoration: BoxDecoration(
                            color: Colors.red,
                            borderRadius: BorderRadius.circular(50)
                        ),
                        child: txt12SizeAlignCenter(title: serviceController.notificationCount.value.toString(), context: context1, textColor: Colors.white),
                      )
                  )
                ],
              ),
            ),
            5.widthBox,
            iconGrid(imagePath: headerSearchImage, onTap: (){
              Get.toNamed("/search_ride");
            }, context: context1),
            5.widthBox,
            iconGrid(imagePath: headerPostImage, onTap: (){
              Get.toNamed("/post_ride/0/new");
            }, context: context1),
          ],
        )
      ],
    ),
  );
}


Widget iconGrid({ String imagePath = "", int index = 1, onTap, context}){
  return InkWell(
    onTap: onTap,
    child: Ink(
      padding: const EdgeInsets.all(3.0),
      // decoration: BoxDecoration(
      //     border: Border.all(color: Colors.white, style: BorderStyle.solid),
      //     borderRadius: BorderRadius.circular(5.0)
      // ),
      child: index == 0 ? ClipRRect(
        borderRadius: BorderRadius.circular(50.0),
        child: networkCacheImageWidget(imagePath, BoxFit.cover, 35.0, 35.0),
      ) : Image.asset(imagePath, width: getValueForScreenType<double>(
        context: context,
        mobile: 35.0,
        tablet: 35.0,
      ), height: getValueForScreenType<double>(
        context: context,
        mobile: 35.0,
        tablet: 35.0,
      ),),
    ),
  );
}