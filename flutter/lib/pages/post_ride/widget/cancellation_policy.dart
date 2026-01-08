import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/check_box_selection_widget.dart';
import '../../widgets/tool_tip.dart';

Widget cancellationPolicyWidget({context, controller, screenWidth, error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['cancellation_policy_label'] ?? "Cancellation policy"}",
              screenWidth: screenWidth,
              context: context,
              isRequired: true,
          ),
          Container(
            padding: const EdgeInsets.fromLTRB(15, 15, 15, 15),
            decoration: BoxDecoration(
                color: Colors.white, borderRadius: BorderRadius.circular(5.0)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                for(var i= 0; i < controller.cancellationOptionList.length; i++)...[
                  Container(
                    padding: const EdgeInsets.all(5),
                    decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(5.0),
                        border: Border.all(width: 1, color: inputColor)),
                    child: checkBoxSelectionWidget(
                      context: context,
                      title: "${controller.cancellationOptionLabelList[i]} ${i == 1 ? "(${controller.labelTextDetail['cancellation_policy_discount_label'] ?? "discount"} ${controller.firmCancellationPrice.value}%)" : ""}",
                      showToolTipBottom: true,
                      infoText: "${controller.cancellationOptionToolTipList[i]}",
                      value:
                      controller.bookingType.value == controller.cancellationOptionList[i].toString() ? true : false,
                      onChanged: (value) async {
                        controller.bookingType.value = value == true ? controller.cancellationOptionList[i].toString() : "";
                      },
                      onTap: (){
                        controller.bookingType.value = controller.bookingType.value == controller.cancellationOptionList[i].toString() ? "" : controller.cancellationOptionList[i].toString();
                      },
                      isError: controller.errors
                          .where((error) => error == "booking_type")
                          .isNotEmpty,
                    ),
                  ),
                  10.heightBox,
                ],
              ],
            ),
          ),

          if(error.any((error) => error['title'] == "booking_type")) ...[
            Padding(padding: const EdgeInsets.fromLTRB(15, 0, 15, 15), child: toolTip(tip: error.firstWhere((error) => error['title'] == "booking_type")),)
          ],
        ],
      ));
}
