import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

Widget pickupDropoffInfoWidget(
    {context,
    String pickup = "",
    String dropoff = "",
    String description = "",
    double screenWidth = 0.0,
    String pickUpHeading = "Pick-up & Drop-off Info",
    String pickupLabel = "Pick up",
    String dropOffLabel = "Drop off",
    String descriptionLabel = "Description"}) {
  Widget infoRow({
    required String label,
    required String value,
    bool showDivider = true,
  }) {
    return Container(
      constraints: const BoxConstraints(minHeight: 54),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      decoration: BoxDecoration(
        border: showDivider
            ? Border(
                bottom: BorderSide(color: Colors.grey.shade300),
              )
            : null,
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          txt20Size(title: '$label: ', context: context),
          Expanded(
            child: Align(
              alignment: Alignment.centerLeft,
              child: txt20Size(title: value, context: context),
            ),
          ),
        ],
      ),
    );
  }

  return cardShadowWidget(
      context: context,
      widgetChild: Column(
        children: [
          postRideWidget(
              title: pickUpHeading, screenWidth: screenWidth, context: context),
          Container(
              padding: EdgeInsets.fromLTRB(
                  getValueForScreenType<double>(
                    context: context,
                    mobile: 10.0,
                    tablet: 10.0,
                  ),
                  getValueForScreenType<double>(
                    context: context,
                    mobile: 10.0,
                    tablet: 10.0,
                  ),
                  getValueForScreenType<double>(
                    context: context,
                    mobile: 10.0,
                    tablet: 10.0,
                  ),
                  getValueForScreenType<double>(
                    context: context,
                    mobile: 10.0,
                    tablet: 10.0,
                  )),
              child: Container(
                decoration: BoxDecoration(
                    border: Border.all(color: Colors.grey.shade300),
                    borderRadius: BorderRadius.all(Radius.circular(10))),
                child: Column(
                  children: [
                    infoRow(label: pickupLabel, value: pickup),
                    infoRow(label: dropOffLabel, value: dropoff),
                    infoRow(
                      label: descriptionLabel,
                      value: description,
                      showDivider: false,
                    ),
                  ],
                ),
              )),
        ],
      ));
}
