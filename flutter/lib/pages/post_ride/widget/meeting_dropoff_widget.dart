import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget meetingDropOffWidget({
  context,
  controller,
  screenWidth,
  bool bookingCheck = false,
  error,
}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['meeting_drop_off_description_label'] ?? "Meeting and drop-off description"}",
              screenWidth: screenWidth,
              context: context,
              isRequired: true
          ),
          Padding(
            padding: const EdgeInsets.all(10.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    textAreaWidget(
                      textController:
                          controller.dropOffDescriptionTextEditingController,
                      readonly: bookingCheck,
                      fontSize: 18.0,
                      fontFamily: regular,
                      placeHolder:
                      "${controller.labelTextDetail['meeting_drop_off_description_placeholder'] ?? "Describe the place where you will meet your passengers, and the place where you will drop them of. Please elaborate, and be as accurate as possible"}",
                      maxLines: 5,
                      onChanged: (value) {
                        if (controller.errors.any((error) => error['title'] == "details")) {
                          controller.errors.removeWhere((error) => error['title'] == "details");
                        }
                      },
                      isError: controller.errors
                          .where((error) => error == "details")
                          .isNotEmpty,
                      focusNode: controller.focusNodes[5.toString()],
                    ),
                  ],
                ),
                if(controller.errors.any((error) => error['title'] == "details")) ...[
                  toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "details"))
                ],
              ],
            ),
          ),
        ],
      ));
}
