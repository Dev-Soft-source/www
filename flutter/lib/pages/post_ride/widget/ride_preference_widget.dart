import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_selection_widget.dart';

import '../../widgets/tool_tip.dart';

Widget ridePreferenceWidget(
    {context, controller, screenWidth, bool bookingCheck = false, error}) {

  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['preferences_label'] ?? "Ride preference"}",
              screenWidth: screenWidth,
              context: context,
              isRequired: true
          ),
          Padding(
            padding: const EdgeInsets.all(15.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(8.0),
                      border: Border.all(width: 1, color: inputColor)),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      10.heightBox,
                      if (controller.rideFeatureList.isNotEmpty) ...[
                        for (var i = 0;
                            i < controller.rideFeatureList.length;
                            i++) ...[
                          checkBoxSelectionWidget(
                            context: context,
                            title: "${controller.rideFeatureLabelList[i]}",
                            value: controller.featureList
                                    .contains(controller.rideFeatureList[i])
                                ? true
                                : false,
                            textColor: i == 0
                                ? Colors.pink
                                : i == 1
                                    ? Colors.green
                                    : textColor,
                            infoText: i == 0
                                ? controller.pinkRideToolTipText.value
                                : i == 1
                                    ? controller.extraCareRideToolTipText.value
                                    : "",
                            onChanged: bookingCheck == true
                                ? null
                                : i == 0 && controller.pinkRideReadOnly.value == true
                                ? null
                                : i == 1 && controller.extraCareRideReadOnly.value == true
                                ? null
                                : (value) async {
                                    if (controller.errors.any((error) => error['title'] == "features")) {
                                      controller.errors.removeWhere((error) => error['title'] == "features");
                                    }
                                    if (value == true) {
                                      if (controller.featureList.contains(
                                          controller.rideFeatureList[i].toString())) {
                                      } else {
                                        controller.featureList
                                            .add(controller.rideFeatureList[i]);
                                      }
                                    } else {
                                      controller.featureList
                                          .remove(controller.rideFeatureList[i]);

                                    }
                                  },
                            onTap: bookingCheck == true
                                ? null
                                : i == 0 && controller.pinkRideReadOnly.value == true
                                ? null
                                : i == 1 && controller.extraCareRideReadOnly.value == true
                                ? null
                                : (){
                              if (controller.errors.any((error) => error['title'] == "features")) {
                                controller.errors.removeWhere((error) => error['title'] == "features");
                              }
                              if (controller.featureList.contains(
                                  controller.rideFeatureList[i].toString())) {
                                controller.featureList
                                    .remove(controller.rideFeatureList[i]);
                              } else {
                                controller.featureList
                                    .add(controller.rideFeatureList[i]);
                              }
                            },
                            isError: controller.errors
                                .where((error) => error == "features")
                                .isNotEmpty,
                          ),
                          if (i != controller.rideFeatureList.length - 1) ...[
                            const Divider(color: inputColor),
                          ]
                        ]
                      ],
                      10.heightBox,
                    ],
                  ),
                ),
                if(error.any((error) => error['title'] == "features")) ...[
                  toolTip(tip: error.firstWhere((error) => error['title'] == "features"))
                ],
              ],
            ),
          ),
        ],
      ));
}
