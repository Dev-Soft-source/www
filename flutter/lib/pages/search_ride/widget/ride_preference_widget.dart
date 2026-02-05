
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/check_box_selection_widget.dart';

Widget ridePreferenceWidget({context, controller, screenWidth}){
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          10.heightBox,
          if(controller.rideFeatureList.isNotEmpty)...[
            for(var i =0; i< controller.rideFeatureList.length; i++)...[
              checkBoxSelectionWidget(
                  context: context,
                  title: "${controller.rideFeatureLabelList[i]}",
                  value: controller.featureList.contains(controller.rideFeatureList[i]) ? true : false,
                  textColor: i == 0 ? Colors.pink : i == 1 ? Colors.green : textColor,
                  onChanged: (value) async{
                    if(value == true){
                      if(controller.featureList.contains(controller.rideFeatureList[i])){
                      }else{
                        controller.featureList.add(controller.rideFeatureList[i]);
                      }
                    }else{
                      controller.featureList.remove(controller.rideFeatureList[i]);
                    }
                  },
                  onTap: (){
                    if(controller.featureList.contains(controller.rideFeatureList[i])){
                      controller.featureList.remove(controller.rideFeatureList[i]);
                    }else{
                      controller.featureList.add(controller.rideFeatureList[i]);
                    }
                  }
                ),
              if(i != controller.rideFeatureList.length - 1)...[
                const Divider(color: inputColor),
              ]
            ]
          ],
          10.heightBox,
        ],
      )
  );
}