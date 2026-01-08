import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';

Widget comingSoonWidget({context}) {
  return Container(
    padding: EdgeInsets.all(
        getValueForScreenType<double>(
          context: context,
          mobile: 20.0,
          tablet: 20.0,
        )
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.center,
      mainAxisAlignment: MainAxisAlignment.center,
      children: [

        Image.asset(comingSoon),
        30.heightBox,
        txt44Size(context: context, fontFamily: regular, title: 'COMING SOON'),
        20.heightBox,
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Expanded(
              child: txt18Size(
                  context: context,
                  fontFamily: bold,
                  title:
                      "We're hard at work behind the scenes developing an innovative product. Great things take time, and we can't wait to share it with you soon!"),
            ),
          ],
        ),
        30.heightBox,
        elevatedButtonWidget(
            context: context,
            btnRadius: 0,
            btnColor: Colors.white,
            textWidget: txt22Size(
                context: context,
                title: 'Go to back',
                fontFamily: regular,
                textColor: primaryColor),
            onPressed: () {
              Get.back();
            }),
      ],
    ),
  );
}
