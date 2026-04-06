import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';



Widget postRideWidget(
    {context,
    String title = "",
    double screenWidth = 0.0,
    bool isRequired = false,
    String infoIcon = "",
    Color bgColor = primaryColor}) {
  return Container(
    padding: EdgeInsets.all(10.0),
    // height: getValueForScreenType<double>(
    //   context: context,
    //   mobile: 40.0,
    //   tablet: 40.0,
    // ),
    width: screenWidth,
    decoration: BoxDecoration(
        borderRadius: const BorderRadius.only(
            topLeft: Radius.circular(8.0), topRight: Radius.circular(8.0)),
        color: bgColor),
    child: Align(
        alignment: Alignment.centerLeft,
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Expanded(
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  Expanded(
                    child: txt22Size(
                      title: title,
                      fontFamily: regular,
                      context: context,
                      textColor: Colors.white,
                    ),
                  ),
                  if (isRequired)
                    txt20Size(
                      title: "*",
                      fontFamily: regular,
                      context: context,
                      textColor: Colors.red,
                    ),
                ],
              ),
            ),
            if(infoIcon != "")...[
              Tooltip(
                margin: EdgeInsets.fromLTRB(
                    getValueForScreenType<double>(
                      context: context,
                      mobile: 15.0,
                      tablet: 15.0,
                    ),
                    getValueForScreenType<double>(
                      context: context,
                      mobile: 0.0,
                      tablet: 0.0,
                    ),
                    getValueForScreenType<double>(
                      context: context,
                      mobile: 15.0,
                      tablet: 15.0,
                    ),
                    getValueForScreenType<double>(
                      context: context,
                      mobile: 0.0,
                      tablet: 0.0,
                    )),
                triggerMode: TooltipTriggerMode.tap,
                message: infoIcon,
                textStyle: const TextStyle(
                  fontSize: 20,
                  color: Colors.white,
                  fontFamily: carlito,
                ),
                showDuration: const Duration(days: 100),
                waitDuration: Duration.zero,
                child: Image.asset(infoImage,color: Colors.white, width: getValueForScreenType<double>(
                  context: context,
                  mobile: 20.0,
                  tablet: 20.0,
                ), height: getValueForScreenType<double>(
                  context: context,
                  mobile: 20.0,
                  tablet: 20.0,
                )),
              )
            ]
          ],
        ))
  );
}
