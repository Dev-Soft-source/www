import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget vehicleInfoWidget({context, String vehicleImage = "", String vehicleDetail = "", String licenseNumber = "", String type = ""}){


  return Container(
      padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
        context: context,
        mobile: 15.0,
        tablet: 15.0,
      ),getValueForScreenType<double>(
        context: context,
        mobile: 0.0,
        tablet: 0.0,
      ),getValueForScreenType<double>(
        context: context,
        mobile: 15.0,
        tablet: 15.0,
      ),getValueForScreenType<double>(
        context: context,
        mobile: 10.0,
        tablet: 10.0,
      )),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.start,
        children: [
          circleImageWidget(width: 60, height: 60, imageType: "network", imagePath: vehicleImage, borderRadius: 5.0, context: context),
          5.widthBox,
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              txt18Size(title: vehicleDetail, context: context),
              txt14Size(title: licenseNumber, context: context),
              txt14Size(title: type, context: context),
            ],
          )
        ],
      )
  );
}