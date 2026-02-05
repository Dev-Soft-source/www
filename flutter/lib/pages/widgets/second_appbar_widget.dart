
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';



Widget secondAppBarWidget({String title = "", context}){
  return Container(
    padding: EdgeInsets.only(bottom: getValueForScreenType<double>(
      context: context,
      mobile: 10.0,
      tablet: 10.0,
    )),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Expanded(child: txt25Size(title: title, fontFamily: regular, textColor: Colors.white, context: context)),
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
      ],
    ),
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