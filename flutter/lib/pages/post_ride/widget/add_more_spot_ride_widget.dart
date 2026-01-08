import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/prefix_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/date_field_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget addMoreSpotRideWidget(
    {context, controller, screenWidth, bool bookingCheck = false,error}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['add_more_from_to'] ?? "Add more spots"}", screenWidth: screenWidth, context: context),
          Container(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: ListView.builder(
                itemCount: controller.spotsCount.value,
                shrinkWrap: true,
                physics: NeverScrollableScrollPhysics(),
                itemBuilder: (context, index){
                  return Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          txt20Size(title: "${controller.labelTextDetail['from_label'] ?? "From"}", fontFamily: regular, context: context),
                          index == 0 ? SizedBox() : elevatedButtonWidget(
                              textWidget: txt14Size(
                                  title: '${controller.labelTextDetail['delete_spot_button_label'] ?? "Delete spot"}',
                                  context: context,
                                  textColor: Colors.white),
                              context: context,
                              btnColor: Colors.red,
                              onPressed: () async {
                                await controller.removeNewSpot(index);
                              }
                          ),
                        ],
                      ),
                      3.heightBox,
                      fieldsWidget(
                        onTap: (){
                          Get.toNamed("/city/origin/0/$index/yes");
                        },
                        textController: controller.fromSpotControllers[index],
                        fieldType: "text",
                        readonly: true,
                        fontFamily: regular,
                        fontSize: 16.0,
                        prefixIcon: preFixIconWidget(
                            context: context, imagePath: fromLocationImage),
                        placeHolder: "${controller.labelTextDetail['from_placeholder'] ?? "Origin"}",
                        hintTextColor: textColor,
                        onChanged: (value) {

                        },
                      ),
                      Obx((){
                        if(controller.fromSpotControllers[index].text == "" && controller.showErrorSpot.value == true){
                          return toolTip(tip: "Please add first origin", type: "normal1");
                        }else{
                          return SizedBox();
                        }
                      }),
                      10.heightBox,
                      txt20Size(title: "${controller.labelTextDetail['to_label'] ?? "To"}", fontFamily: regular, context: context),
                      3.heightBox,
                      fieldsWidget(
                        textController: controller.toSpotControllers[index],
                        onTap: (){
                          Get.toNamed("/city/destination/0/$index/yes");
                        },
                        fieldType: "text",
                        readonly: true,
                        fontFamily: regular,
                        fontSize: 16.0,
                        prefixIcon: preFixIconWidget(
                            context: context, imagePath: toLocationImage),
                        placeHolder: "${controller.labelTextDetail['to_placeholder'] ?? "Destination"}",
                        onChanged: (value) {
                        },
                      ),
                      Obx((){
                        if(controller.toSpotControllers[index].text == "" && controller.showErrorSpot.value == true){
                        return toolTip(tip: "Please add first destination", type: "normal1");
                        }else{
                          return SizedBox();
                        }
                      }),
                      10.heightBox,
                      txt20Size(title: "${controller.labelTextDetail['price_per_seat_label'] ?? "Price"}", context: context),
                      3.heightBox,
                      fieldsWidget(
                        textController: controller.priceSpotControllers[index],
                        fieldType: "number",
                        readonly: false,
                        fontFamily: regular,
                        fontSize: 16.0,
                        placeHolder: "\$",
                        onChanged: (value) {

                        },
                      ),
                      Obx((){
                        if(controller.priceSpotControllers[index].text == "" && controller.showErrorSpot.value == true){
                          return toolTip(tip: "Please add first price", type: "normal1");
                        }else{
                          return SizedBox();
                        }
                      }),
                      10.heightBox,
                    ],
                  );
                }
            ),
          ),
        ],
      ));
}
