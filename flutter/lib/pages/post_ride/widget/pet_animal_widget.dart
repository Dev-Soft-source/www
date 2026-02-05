import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_selection_widget.dart';

import '../../widgets/tool_tip.dart';

Widget petAnimalWidget(
    {context, controller, screenWidth, bool bookingCheck = false,error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['animals_label'] ?? "Animal & pets friendly"}",
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
                      if (controller.petList.isNotEmpty) ...[
                        for (var i = 0; i < controller.petList.length; i++) ...[
                          checkBoxSelectionWidget(
                            context: context,
                            title: "${controller.petLabelList[i]}",
                            value:
                                controller.pet.value == "${controller.petList[i]}"
                                    ? true
                                    : false,
                            onChanged: bookingCheck == true
                                ? null
                                : (value) async {
                                    controller.pet.value = value == true
                                        ? "${controller.petList[i]}"
                                        : "";
                                  },
                            onTap: bookingCheck == true
                                ? null
                                : (){
                              controller.pet.value = controller.pet.value == "${controller.petList[i]}" ? "" : "${controller.petList[i]}";
                            },
                            isError: controller.errors
                                .where((error) => error == "animal_friendly")
                                .isNotEmpty,
                          ),
                          if (i != controller.petList.length - 1) ...[
                            const Divider(color: inputColor),
                          ]
                        ]
                      ],
                      10.heightBox,
                    ],
                  ),
                ),
                if(error.any((error) => error['title'] == "animal_friendly")) ...[
                  toolTip(tip: error.firstWhere((error) => error['title'] == "animal_friendly"))
                ],
              ],
            ),
          ),
        ],
      ));
}
