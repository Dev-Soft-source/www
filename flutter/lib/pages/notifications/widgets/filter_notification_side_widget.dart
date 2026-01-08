import 'package:dropdown_button2/dropdown_button2.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/search_ride/widget/luggage_widget.dart';
import 'package:proximaride_app/pages/search_ride/widget/pet_animal_widget.dart';
import 'package:proximaride_app/pages/search_ride/widget/ride_preference_widget.dart';
import 'package:proximaride_app/pages/search_ride/widget/smoking_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget filterNotificationSideWidget({context, controller, double screenWidth = 0.0, double screenHeight = 0.0}){

  var paymentToolTip = "${controller.labelTextDetail['notification_filter_what_label'] ?? "What is this?"}\n";
  for(var index =0; index < controller.paymentOptionList.length; index++){
    paymentToolTip = "$paymentToolTip${controller.paymentOptionList[index]}: ${controller.paymentOptionToolTipList[index]}\n";
  }

  var bookingToolTip = "${controller.labelTextDetail['notification_filter_what_label'] ?? "What is this?"}\n";
  for(var index =0; index < controller.bookingOptionList.length; index++){
    bookingToolTip = "$bookingToolTip${controller.bookingOptionList[index]}: ${controller.bookingOptionToolTipList[index]}\n";
  }

  return Obx((){
    return SafeArea(
      child: Stack(
        children: [
          Container(
            padding: const EdgeInsets.all(15.0),
            child: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  txt24Size(title: "${controller.labelTextDetail['notification_filter_search_filter_label'] ?? "Search filters"}", context: context, textColor: primaryColor, fontFamily: bold),
                  const Divider(),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      txt24Size(title: "${controller.labelTextDetail['notification_filter_payment_label'] ?? "Payment option"}", context: context, textColor: primaryColor),
                      5.widthBox,
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
                        message: paymentToolTip,
                        textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                        showDuration: const Duration(days: 100),
                        waitDuration: Duration.zero,
                        child: const Icon(Icons.question_mark, size: 15,)
                      )
                    ],
                  ),
                  5.heightBox,
                  Container(
                    color: inputColor,
                    child: DropdownButtonFormField2(
                        isExpanded: true,
                        decoration: InputDecoration(
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(5.0),
                            borderSide: const BorderSide(
                              color: primaryColor,
                              style: BorderStyle.solid,
                              width: 1,
                            ),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(5.0),
                            borderSide: const BorderSide(color: primaryColor),
                          ),
                          contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
                          fillColor: inputColor,
                        ),
                        value: controller.paymentMethod.value,
                        items: [
                          DropdownMenuItem(
                            value: "",
                            child:  controller.paymentMethod.value == "" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "${controller.labelTextDetail['notification_filter_all_label'] ?? "All"}", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "${controller.labelTextDetail['notification_filter_all_label'] ?? "All"}", context: context, fontFamily: bold),
                          ),
                          if(controller.paymentOptionList.isNotEmpty)...[
                            for(var i= 0; i< controller.paymentOptionList.length; i++)...[
                              DropdownMenuItem(
                                  value: "${controller.paymentOptionList[i]}",
                                  child: controller.paymentMethod.value == "${controller.paymentOptionList[i]}" ?
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      txt18Size(title: "${controller.paymentOptionLabelList[i]}", context: context, fontFamily: bold),
                                      Icon(Icons.check, color: btnPrimaryColor, size: 20)
                                    ],
                                  ) : txt18Size(title: "${controller.paymentOptionLabelList[i]}", context: context, fontFamily: bold),

                              ),
                            ]
                          ],
                        ],
                        onChanged: (data){
                          controller.paymentMethod.value = data!;
                        },
                      dropdownStyleData: DropdownStyleData(
                        maxHeight: screenHeight * 0.3,
                        width: screenWidth - 80,
                        // padding: EdgeInsets.only(bottom: 100),
                        decoration: BoxDecoration(
                          border: Border.all(width: 2,color: primaryColor),
                          borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(10.0), bottomRight: Radius.circular(10.0)),
                        ),
                      ),

                    ),
                  ),
                  10.heightBox,
                  Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      txt24Size(title: "${controller.labelTextDetail['notification_filter_booking_label'] ?? "Booking type"}", context: context, textColor: primaryColor),
                      5.widthBox,
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
                          message: bookingToolTip,
                          textStyle: const TextStyle(fontSize: 16,color: Colors.white),
                          showDuration: const Duration(days: 100),
                          waitDuration: Duration.zero,
                          child: const Icon(Icons.question_mark, size: 15,)
                      )
                    ],
                  ),
                  5.heightBox,
                  Container(
                    color: inputColor,
                    child: DropdownButtonFormField2(
                      isExpanded: true,
                      decoration: InputDecoration(
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(5.0),
                          borderSide: const BorderSide(
                            color: primaryColor,
                            style: BorderStyle.solid,
                            width: 1,
                          ),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(5.0),
                          borderSide: const BorderSide(color: primaryColor),
                        ),
                        contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0),
                        fillColor: inputColor,
                      ),
                      value: controller.bookingType.value,
                      items: [
                        DropdownMenuItem(
                          value: "",
                          child:  controller.bookingType.value == "" ?
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              txt18Size(title: "${controller.labelTextDetail['notification_filter_all_label'] ?? "All"}", context: context, fontFamily: bold),
                              Icon(Icons.check, color: btnPrimaryColor, size: 20)
                            ],
                          ) : txt18Size(title: "${controller.labelTextDetail['notification_filter_all_label'] ?? "All"}", context: context, fontFamily: bold),
                        ),
                        if(controller.bookingOptionList.isNotEmpty)...[
                          for(var i= 0; i< controller.bookingOptionList.length; i++)...[
                            DropdownMenuItem(
                              value: "${controller.bookingOptionList[i]}",
                              child: controller.bookingType.value == "${controller.bookingOptionList[i]}" ?
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  txt18Size(title: "${controller.bookingOptionLabelList[i]}", context: context, fontFamily: bold),
                                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                                ],
                              ) : txt18Size(title: "${controller.bookingOptionLabelList[i]}", context: context, fontFamily: bold),

                            ),
                          ]
                        ],
                      ],
                      onChanged: (data){
                        controller.bookingType.value = data!;
                      },
                      dropdownStyleData: DropdownStyleData(
                        maxHeight: screenHeight * 0.3,
                        width: screenWidth - 80,
                        // padding: EdgeInsets.only(bottom: 100),
                        decoration: BoxDecoration(
                          border: Border.all(width: 2,color: primaryColor),
                          borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(10.0), bottomRight: Radius.circular(10.0)),
                        ),
                      ),

                    ),
                  ),

                ],
              ),
            ),
          ),
          Align(
            alignment: Alignment.bottomCenter,
            child: Container(
              padding: const EdgeInsets.all(5.0),
              color: Colors.grey.shade200,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: elevatedButtonWidget(
                      textWidget: txt22Size(title: "${controller.labelTextDetail['notification_filter_clear_btn_label'] ?? "Clear"}", context: context, textColor: Colors.white),
                      btnColor: primaryColor,
                      onPressed: () async{
                        Get.back();
                        controller.filter.value = true;
                        controller.actionType.value = "clear";
                        await controller.getSearchNotification();
                      }
                    ),
                  ),
                  5.widthBox,
                  Expanded(
                    child: elevatedButtonWidget(
                        textWidget: txt22Size(title: "${controller.labelTextDetail['notification_filter_apply_btn_label'] ?? "Apply"}", context: context, textColor: Colors.white),
                        onPressed: () async{
                          Get.back();
                          controller.actionType.value = "apply";
                          controller.filter.value = true;
                          await controller.getSearchNotification();
                        }
                    ),
                  )
                ],
              ),
            ),
          )
        ],
      ),
    );
  });
}