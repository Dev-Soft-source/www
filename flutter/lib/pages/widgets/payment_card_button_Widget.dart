
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';



Widget paymentCardButtonWidget({ String btn1Title = "", String btn2Title = "", onPressed1, onPressed2, Color btnColor = primaryColor, context, double screenWidth = 0.0}){
  return Container(
      padding: EdgeInsets.all(15.0),
      height: 120.0,
      width: screenWidth,
      color: Colors.grey.shade200,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            height: 40,
            width: screenWidth,
            child: elevatedButtonWidget(
                textWidget: txt22Size(title : btn1Title, context: context, textColor: Colors.white),
                onPressed: onPressed1,
                context: context,
                btnColor: primaryColor
            ),
          ),
          10.heightBox,
          SizedBox(
            height: 40,
            width: screenWidth,
            child: elevatedButtonWidget(
                textWidget: txt22Size(title : btn2Title, context: context, textColor: Colors.white),
                onPressed: onPressed2,
                context: context,
                btnColor: btnPrimaryColor
            ),
          ),
        ],
      )
  );
}
