import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
Widget headerWidget({context, String name = "", double screenWidth = 0.0, String age = "",  String image = "", String gender = "", onTap, controller}){
  return Container(
    height: getValueForScreenType<double>(
      context: context,
      mobile: 70.0,
      tablet: 70.0,
    ),
    width: screenWidth,
    decoration: const BoxDecoration(
        borderRadius: BorderRadius.only(
            topLeft: Radius.circular(8.0),
            topRight: Radius.circular(8.0)
        ),
        color: primaryColor
    ),
    child: Padding(
      padding: EdgeInsets.only(
        left: getValueForScreenType<double>(
          context: context,
          mobile: 15.0,
          tablet: 15.0,
        ),
        right: getValueForScreenType<double>(
          context: context,
          mobile: 15.0,
          tablet: 15.0,
        )
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.start,
            children: [
              Padding(padding: const EdgeInsets.only(top: 5),
              child: circleImageWidget(width: 30.0, height: 30.0, imagePath: image, imageType: "network", context: context, borderRadius: 100.0, bgColor: Colors.white)),
              5.widthBox,
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  txt22SizeCapitalized(context: context, title: name, textColor: Colors.white),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    children: [
                      txt14Size(context: context, title: "${controller.labelTextDetail['age'] ?? "Age"}: $age", textColor: Colors.white),
                      20.widthBox,
                      txt14Size(context: context, title: "${controller.labelTextDetail['gender'] ?? "Gender"}: $gender", textColor: Colors.white),
                    ],
                  )
                ],
              )
            ],
          ),
          InkWell(
            onTap: onTap,
            child: textWithUnderLine(
              title: "${controller.labelTextDetail['review_profile_label'] ?? "View profile"}",
              fontFamily: regular,
              textColor: Colors.white,
              decorationColor: Colors.white,
              textSize: 18.0,
              context: context
            ),
          ),
        ],
      )
    ),
  );
}