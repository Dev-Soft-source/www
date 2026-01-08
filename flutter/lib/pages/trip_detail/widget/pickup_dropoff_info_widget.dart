import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget pickupDropoffInfoWidget({context, String pickup = "",String dropoff = "",String description = "", double screenWidth = 0.0,
  String pickUpHeading = "Pick up & drop off info",  String pickupLabel = "Pick up",  String dropOffLabel = "Drop off", String descriptionLabel = "Description"}){
  return cardShadowWidget(
      context: context,
      widgetChild: Column(
        children: [
          postRideWidget(title: pickUpHeading, screenWidth: screenWidth, context: context),
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
              child: Container(
                decoration: BoxDecoration(
                    border: Border.all(color: Colors.grey.shade300),
                    borderRadius: BorderRadius.all(Radius.circular(10))
                ),
                child: Column(
                  children: [
                     Padding(
                       padding: const EdgeInsets.all(10.0),
                       child: Row(
                         crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            txt18Size(title: '$pickupLabel: ', context: context),
                            Expanded(
                                child: txt18Size(title: pickup, context: context)),
                          ],
                        ),
                     ),
                    const Divider(),
                    Padding(
                      padding: const EdgeInsets.all(10.0),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          txt18Size(title: '$dropOffLabel: ', context: context),
                          Expanded(child: txt18Size(title: dropoff, context: context)),
                        ],
                      ),
                    ),
                    const Divider(),
                    Padding(
                      padding: const EdgeInsets.all(10.0),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          txt18Size(title: '$descriptionLabel: ', context: context),
                          Expanded(child: txt18Size(title: description, context: context)),
                        ],
                      ),
                    ),
                  ],
                ),
              )
          ),
        ],
      )
  );
}