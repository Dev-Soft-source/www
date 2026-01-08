import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/check_box_widget.dart';
Widget cancellationPolicyWidget({context, controller, screenWidth}) {
  return cardShadowWidget(
      context: context,
      widgetChild: Column(
        children: [
          postRideWidget(title: "${controller.labelTextDetail['cancellation_policy_label'] ?? "Cancellation policy"}", screenWidth: screenWidth, context: context,isRequired: true),

          Container(
            padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
              context: context,
              mobile: 0.0,
              tablet: 0.0,
            ),getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            ),getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            ),getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: Column(
              children: [
                for(var i =0; i < controller.cancellationOptionList.length; i++)...[
                  Row(
                    children: [
                      checkBoxWidget(
                        value: controller.policyTypeId.value  == controller.cancellationOptionList[i].toString() ? true : false,
                        onChanged:controller.cancellationDisable.value == false ? (value) {
                          if (value == true) {
                            controller.policyTypeId.value = controller.cancellationOptionList[i].toString();
                            if(i == 0){
                              controller.policyType.value = 'standard';
                            }else if(i == 1){
                              controller.policyType.value = 'firm';
                            }
                          }
                        } : null,
                      ),
                      Expanded(child: txt20Size(context: context,title: '${controller.cancellationOptionLabelList[i]} ${ i == 1 ? '(${controller.labelTextDetail['cancellation_policy_discount_label'] ?? "Discount"} ${controller.setting['frim_discount'].toString()}%)' : ''}',fontFamily: regular)),
                      10.widthBox,
                      Tooltip(
                        margin: EdgeInsets.fromLTRB(
                            getValueForScreenType<double>(
                              context: context,
                              mobile: 15.0,
                              tablet: 15.0,
                            ),
                            getValueForScreenType<double>(
                              context: context,
                              mobile: 0.0,
                              tablet: 0.0,
                            ),
                            getValueForScreenType<double>(
                              context: context,
                              mobile: 15.0,
                              tablet: 15.0,
                            ),
                            getValueForScreenType<double>(
                              context: context,
                              mobile: 0.0,
                              tablet: 0.0,
                            )),
                        triggerMode: TooltipTriggerMode.tap,
                        message: '${controller.cancellationOptionToolTipList[i]}',
                        textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                        showDuration: const Duration(days: 100),
                        waitDuration: Duration.zero,
                        child: Image.asset(infoImage,color: Colors.black, width: getValueForScreenType<double>(
                          context: context,
                          mobile: 20.0,
                          tablet: 20.0,
                        ), height: getValueForScreenType<double>(
                          context: context,
                          mobile: 20.0,
                          tablet: 20.0,
                        )),
                      )
                    ],
                  ),
                ],

              ],
            ),
          ),

        ],
      )
  );
}