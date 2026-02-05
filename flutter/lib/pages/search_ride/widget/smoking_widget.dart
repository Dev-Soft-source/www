import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/check_box_selection_widget.dart';
Widget smokingWidget({context, controller, screenWidth}){
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          10.heightBox,
          if(controller.smokingList.isNotEmpty)...[
            for(var i =0; i< controller.smokingList.length; i++)...[
              checkBoxSelectionWidget(
                  context: context,
                  title: "${controller.smokingLabelList[i]}",
                  value: controller.smoking.value == "${controller.smokingList[i]}" ? true : false,
                  onChanged: (value) async{
                    controller.smoking.value = value == true ? "${controller.smokingList[i]}" : "";
                  },
                  onTap: (){
                    controller.smoking.value = controller.smoking.value != "${controller.smokingList[i]}" ? "${controller.smokingList[i]}" : "";
                  }
              ),
              if(i != controller.smokingList.length - 1)...[
                const Divider(color: inputColor),
              ]

            ]
          ],
          10.heightBox,
        ],
      )
  );
}