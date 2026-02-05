
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/check_box_selection_widget.dart';
import '../../consts/constFileLink.dart';

Widget luggageOptionWidget({ controller, context, bool bookingCheck = false}){
  return Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      10.heightBox,
      if(controller.luggageList.isNotEmpty)...[
        for(var i =0; i< controller.luggageList.length; i++)...[
          checkBoxSelectionWidget(
              context: context,
              title: "${controller.luggageListLabel[i]}",
              showToolTipBottom: i == 4 ? true : false,
              tooltipMessage: i == 4 ? "${controller.luggageListToolTip[i]}" : "",
              extraChargesToolTip: "${controller.labelTextDetail['luggage_option5_label'] ?? ""}",
              value: controller.luggage.value == "${controller.luggageList[i]}" ? true : false,
              onChanged: bookingCheck == true ? null :  (value) async{
                controller.luggage.value = value == true ? "${controller.luggageList[i]}" : "";
                if (controller.errors.any((error) => error['title'] == "luggage")) {
                  controller.errors.removeWhere((error) => error['title'] == "luggage");
                }
              },
            onTap: bookingCheck == true ? null : () async{
              controller.luggage.value = controller.luggage.value == "${controller.luggageList[i]}"  ? "" : "${controller.luggageList[i]}";
              if (controller.errors.any((error) => error['title'] == "luggage")) {
                controller.errors.removeWhere((error) => error['title'] == "luggage");
              }
            },
            isError: controller.errors
                .where((error) => error == "luggage")
                .isNotEmpty,
          ),
          if(i != controller.luggageList.length - 1)...[
            const Divider(color: inputColor),
          ]
        ]
      ],
      10.heightBox,
    ],
  );
}
