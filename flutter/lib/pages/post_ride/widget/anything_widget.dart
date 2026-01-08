import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget anythingWidget(
    {context, controller, screenWidth, bool bookingCheck = false,error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['anything_to_add_label'] ?? "Anything to add?"}",
              screenWidth: screenWidth,
              context: context,
              isRequired: false
          ),
          Container(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                textAreaWidget(
                  textController: controller.anythingTextEditingController,
                  readonly: bookingCheck,
                  fontSize: 16.0,
                  fontFamily: regular,
                  placeHolder: "${controller.labelTextDetail['anything_to_add_placeholder'] ?? "What else do you want to tell your passengers?"}",
                  maxLines: 5,
                  onChanged: (value) {
                    if (controller.errors.any((error) => error['title'] == "notes")) {
                      controller.errors.removeWhere((error) => error['title'] == "notes");
                    }
                  },
                  isError: controller.errors
                      .where((error) => error == "notes")
                      .isNotEmpty,
                  focusNode: controller.focusNodes[12.toString()],
                ),
                if(controller.errors.any((error) => error['title'] == "notes")) ...[
                  toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "notes"))
                ],
              ],
            ),
          ),
        ],
      ));
}
