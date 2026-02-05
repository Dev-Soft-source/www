
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/check_box_selection_widget.dart';
Widget petAnimalWidget({context, controller, screenWidth}){
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          10.heightBox,
          if(controller.petList.isNotEmpty)...[
            for(var i =0; i< controller.petList.length; i++)...[
              checkBoxSelectionWidget(
                  context: context,
                  title: "${controller.petLabelList[i]}",
                  value: controller.pet.value == "${controller.petList[i]}" ? true : false,
                  onChanged: (value) async{
                    controller.pet.value = value == true ? "${controller.petList[i]}" : "";
                  },
                  onTap: (){
                    controller.pet.value =  controller.pet.value == "${controller.petList[i]}" ? "" : "${controller.petList[i]}";
                  }
              ),
              if(i != controller.petList.length - 1)...[
                const Divider(color: inputColor),
              ]
            ]
          ],
          10.heightBox,
        ],
      )
  );
}