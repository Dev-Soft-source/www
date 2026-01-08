import 'package:flutter/material.dart' hide ModalBottomSheetRoute;
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';

languageBottomSheet(screenWidth, serviceController, {String page = ""}){

  return Get.bottomSheet(
    Container(
      width: screenWidth,
      height: 200,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.only(
          topLeft: Radius.circular(20),
          topRight: Radius.circular(20),
        ),
      ),
      child: Padding(
        padding: EdgeInsets.all(8),
        child:GridView.count(
          crossAxisCount: 5,
          crossAxisSpacing: 0.0,
          mainAxisSpacing: 0.0,
          shrinkWrap: true,
          children: List.generate(serviceController.languages.length, (index) {
            return InkWell(
              child: Ink(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(50.0),
                      child: networkCacheImageWidget(serviceController.languages[index]['flag_icon'], BoxFit.cover, 40.0, 40.0),
                    ),
                    2.heightBox,
                    "${serviceController.languages[index]['name']}".text.size(16
                    ).color(textColor).fontFamily(regular).make()
                  ],
                ),
              ),
              onTap: () async{
                serviceController.updateLanguage(serviceController.languages[index]['id'], page);


                //controller.getImage(ImageSource.camera);
              },
            );
          },),
        ),
      ),
    ),
  );

}
