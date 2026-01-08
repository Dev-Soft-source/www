import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/profile_detail/widget/profile_image_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget userInfoWidget({context, String imagePath = "", String userName = "", String editProfileLabel = "Edit profile"}){
  return Row(
    mainAxisAlignment: MainAxisAlignment.spaceBetween,
    crossAxisAlignment: CrossAxisAlignment.center,
    children: [
      Expanded(
        child: Row(
          mainAxisAlignment: MainAxisAlignment.start,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            profileImageWidget(context: context, imagePath: imagePath, mobileRadius: 48.0, tabletRadius: 48.0),
            10.widthBox,
            Expanded(
              child: txt24SizeCapitalize(
                  title: userName,
                  fontFamily: bold,
                  textColor: primaryColor,
                  context: context
              ),
            )
          ],
        ),
      ),
      elevatedButtonWidget(
        textWidget: txt16Size(
            title: editProfileLabel,
            textColor: Colors.white,
            fontFamily: regular,
            context: context
        ),
        onPressed: () {
          Get.toNamed('/edit_profile');
        },
        context: context,
      ),
    ],
  );
}