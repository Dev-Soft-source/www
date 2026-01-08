import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget rideFeatureWidget({context, featureList, rideDetail, double screenWidth = 0.0, String heading = "Ride features"}){
  return cardShadowWidget(
      context: context,
      widgetChild: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
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
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if(featureList.isNotEmpty)...[
                    for(var i =0; i < featureList.length; i++)...[
                      Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          circleIconWidget(width: 25, height: 25, imagePath: featureList[i]['image'], context: context),
                          10.widthBox,
                          Expanded(child: txt18Size(title: featureList[i]['title'], context: context)),
                          if(featureList[i]['title'] == 'Pink rides' || featureList[i]['title'] == 'Extra-care rides')...[
                            5.widthBox,
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
                              message: featureList[i]['title'] == 'Pink rides' ? '${featureList[i]['tooltip']}'
                                  : '${featureList[i]['tooltip']}',
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

                          ]
                        ],
                      ),
                      5.heightBox,
                    ]
                  ],
                  if(rideDetail['smoke'] != null)...[
                    Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        circleIconWidget(width: 25, height: 25, imagePath: rideDetail['smoke_image'] ?? "", context: context),
                        10.widthBox,
                        Expanded(child: txt18Size(title: rideDetail['smoke'], context: context))
                      ],
                    ),
                    5.heightBox,
                  ],
                  if(rideDetail['luggage'] != null)...[
                    Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        circleIconWidget(width: 25, height: 25, imagePath: rideDetail['luggage_image'] ?? "", context: context),
                        10.widthBox,
                        Expanded(
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              txt18Size(title: rideDetail['luggage'], context: context),
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
                                message: "${rideDetail['luggage_tooltip']}",
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
                              ),
                            ],
                          )
                        ),
                      ],
                    ),
                    5.heightBox,
                  ],
                  if(rideDetail['animal_friendly'] != null)...[
                    Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        circleIconWidget(width: 25, height: 25, imagePath: rideDetail['animal_friendly_image'] ?? "", context: context),
                        10.widthBox,
                        Expanded(child: txt18Size(title: rideDetail['animal_friendly'], context: context))
                      ],
                    ),
                    5.heightBox,
                  ],
                ],
              )
          ),
        ],
      )
  );
}