import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget paymentOptionWidget({context, String payment = "", double screenWidth = 0.0, String toolTipMessage = "", String heading = "Payment method"}){
  return cardShadowWidget(
      context: context,
      widgetChild: Column(
        children: [
          postRideWidget(title: heading, screenWidth: screenWidth, context: context),
          Container(
              padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
                context: context,
                mobile: 15.0,
                tablet: 15.0,
              ),getValueForScreenType<double>(
                context: context,
                mobile: 10.0,
                tablet: 10.0,
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
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  txt18Size(title: payment, context: context),
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
                    message: toolTipMessage,
                    textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                    showDuration: const Duration(days: 100),
                    waitDuration: Duration.zero,
                    child: Image.asset(infoImage,color: Colors.black, width: getValueForScreenType<double>(
                      context: context,
                      mobile: 20.0,
                      tablet: 20.0,
                    ), height: getValueForScreenType<double>(
                      context: context,
                      mobile: 20.0,
                      tablet: 20.0,
                    )),
                  )

                ],
              )
          ),
        ],
      )
  );
}