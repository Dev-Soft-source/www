import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_selection_widget.dart';

import '../../widgets/tool_tip.dart';

Widget smokingWidget(
    {context, controller, screenWidth, bool bookingCheck = false,error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['smoking_label'] ?? "Smoking"}", screenWidth: screenWidth, context: context,isRequired: true),
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
                      if (controller.smokingList.isNotEmpty) ...[
                        for (var i = 0; i < controller.smokingList.length; i++) ...[
                          checkBoxSelectionWidget(
                            context: context,
                            title: "${controller.smokingLabelList[i]}",
                            value: controller.smoking.value ==
                                    "${controller.smokingList[i]}"
                                ? true
                                : false,
                            onChanged: bookingCheck == true
                                ? null
                                : (value) async {
                                    controller.smoking.value = value == true
                                        ? "${controller.smokingList[i]}"
                                        : "";
                                  },
                            onTap: bookingCheck == true
                                ? null
                                : (){
                              controller.smoking.value = controller.smoking.value == "${controller.smokingList[i]}" ? "" : "${controller.smokingList[i]}";
                            },
                            isError: controller.errors
                                .where((error) => error == "smoke")
                                .isNotEmpty,
                          ),
                          if (i != controller.smokingList.length - 1) ...[
                            const Divider(color: inputColor),
                          ]
                        ],
                      ],
                      10.heightBox,
                    ],
                  ),
                ),
                if(error.any((error) => error['title'] == "smoke")) ...[
                  toolTip(tip: error.firstWhere((error) => error['title'] == "smoke"))
                ],

              ],
            ),
          ),

        ],
      ));
}
