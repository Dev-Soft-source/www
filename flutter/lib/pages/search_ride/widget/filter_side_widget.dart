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

Widget filterSideWidget({context, controller, double screenWidth = 0.0, double screenHeight = 0.0}){

  var paymentToolTip = "${controller.labelTextDetail['filter_what_label'] ?? "What is this?"}\n";
  for(var index =0; index < controller.paymentOptionList.length; index++){
    paymentToolTip = "$paymentToolTip${controller.paymentOptionList[index]}: ${controller.paymentOptionToolTipList[index]}\n";
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
                  txt24Size(title: "${controller.labelTextDetail['filter_section_heading'] ?? "Search filters"}", context: context, textColor: primaryColor, fontFamily: bold),
                  const Divider(),
                  txt24Size(title: "${controller.labelTextDetail['filter1_driver_heading'] ?? "Driver"}", context: context, textColor: primaryColor),
                  10.heightBox,
                  txt20Size(title: "${controller.labelTextDetail['driver_age_label'] ?? "Driver age"}", context: context),
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
                        value: controller.driverAge.value,
                        items: [
                          DropdownMenuItem(
                            value: "",
                            child: controller.driverAge.value == "" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "${controller.labelTextDetail['driver_age_placeholder'] ?? "All"}", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) :  txt18Size(title: "${controller.labelTextDetail['driver_age_placeholder'] ?? "All"}", context: context, fontFamily: bold),
                          ),
                          DropdownMenuItem(
                              value: "20",
                              child: controller.driverAge.value == "20" ?
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  txt18Size(title: "+20", context: context, fontFamily: bold),
                                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                                ],
                              ) : txt18Size(title: "+20", context: context, fontFamily: bold),
                          ),
                          DropdownMenuItem(
                            value: "30",
                            child: controller.driverAge.value == "30" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "+30", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "+30", context: context, fontFamily: bold),
                          ),
                          DropdownMenuItem(
                            value: "40",
                            child: controller.driverAge.value == "40" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "+40", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "+40", context: context, fontFamily: bold),
                          ),
                          DropdownMenuItem(
                            value: "50",
                            child: controller.driverAge.value == "50" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "+50", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "+50", context: context, fontFamily: bold),
                          ),
                          DropdownMenuItem(
                            value: "60",
                            child: controller.driverAge.value == "60" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "+60", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "+60", context: context, fontFamily: bold),
                          ),
                        ],
                        onChanged: (data){
                          controller.driverAge.value = data!;
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
                  txt20Size(title: "${controller.labelTextDetail['driver_rating_label'] ?? "Driver rating"}", context: context),
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
                        value: controller.driverRating.value,
                        items: [
                          DropdownMenuItem(
                            value: "",
                            child:  controller.driverRating.value == "" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "${controller.labelTextDetail['driver_rating_placeholder'] ?? "All"}", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "${controller.labelTextDetail['driver_rating_placeholder'] ?? "All"}", context: context, fontFamily: bold),
                          ),
                          DropdownMenuItem(
                              value: "4.5",
                              child: controller.driverRating.value == "4.5" ?
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  txt18Size(title: "5", context: context, fontFamily: bold),
                                  Icon(Icons.check, color: btnPrimaryColor, size: 20)
                                ],
                              ) : txt18Size(title: "5", context: context, fontFamily: bold),
                          ),
                          DropdownMenuItem(
                            value: "4",
                            child: controller.driverRating.value == "4" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "4", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "4 ${controller.labelTextDetail['search_and_above_label'] ?? "and above"}", context: context, fontFamily: bold),
                          ),
                          DropdownMenuItem(
                            value: "3",
                            child: controller.driverRating.value == "3" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "3", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "3 ${controller.labelTextDetail['search_and_above_label'] ?? "and above"}", context: context, fontFamily: bold),
                          ),
                          DropdownMenuItem(
                            value: "2",
                            child: controller.driverRating.value == "2" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "2", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "2 ${controller.labelTextDetail['search_and_above_label'] ?? "and above"}", context: context, fontFamily: bold),
                          ),
                          DropdownMenuItem(
                            value: "1",
                            child: controller.driverRating.value == "1" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: "1", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "1 ${controller.labelTextDetail['search_and_above_label'] ?? "and above"}", context: context, fontFamily: bold),
                          ),
                        ],
                        onChanged: (data){
                          controller.driverRating.value = data!;
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
                    children: [
                      SizedBox(
                        width: 25,
                        height: 25,
                        child: checkBoxWidget(
                            value: controller.driverPhone.value == "1" ? true: false,
                            onChanged: (value){
                              controller.driverPhone.value = value == true ? "1" : "0";
                            }
                        ),
                      ),
                      5.widthBox,
                      InkWell(onTap: (){
                        controller.driverPhone.value = controller.driverPhone.value == "1" ? "0" : "1";
                      }, child: txt16Size(title: "${controller.labelTextDetail['driver_phone_access_label'] ?? "Access to driver's phone number"}", context: context)),
                    ],
                  ),
                  10.heightBox,
                  txt20Size(title: "${controller.labelTextDetail['driver_know_label'] ?? "Driver you know"}", context: context),
                  5.heightBox,
                  fieldsWidget(textController: controller.driverNameEditingController, fieldType: "text", fontSize: 18.0, fontFamily: regular, placeHolder: "${controller.labelTextDetail['driver_know_placeholder'] ?? "Enter"}", readonly: false, hintTextColor: textColor),
                  10.heightBox,
                  txt24Size(title: "${controller.labelTextDetail['filter2_passengers_heading'] ?? "Passenger travelling in same car"}", context: context, textColor: primaryColor),
                  10.heightBox,
                  txt20Size(title: "${controller.labelTextDetail['passengers_rating_label'] ?? "Passenger rating"}", context: context),
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
                        value: controller.passengerRating.value,
                        items: [
                          DropdownMenuItem(
                            value: "",
                            child: controller.passengerRating.value == "" ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(child: txt18Size(title: "${controller.labelTextDetail['search_filter_all_label'] ?? "All"}", context: context, fontFamily: bold)),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "${controller.labelTextDetail['search_filter_all_label'] ?? "All"}", context: context, fontFamily: bold),
                          ),
                          if(controller.passengerRatingList.isNotEmpty)...[
                            for(var i = 0; i < controller.passengerRatingList.length; i++)...[
                              DropdownMenuItem(
                                  value: "${controller.passengerRatingList[i]}",
                                  child: controller.passengerRating.value == "${controller.passengerRatingList[i]}" ?
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Expanded(child: txt18Size(title: "${controller.passengerRatingLabelList[i]}", context: context, fontFamily: bold),),
                                      Icon(Icons.check, color: btnPrimaryColor, size: 20)
                                    ],
                                  ) : txt18Size(title: "${controller.passengerRatingLabelList[i]}", context: context, fontFamily: bold),
                              ),
                            ]
                          ],

                        ],
                        onChanged: (data){
                          controller.passengerRating.value = data!;
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
                      txt24Size(title: "${controller.labelTextDetail['filter3_payment_methods_heading'] ?? "Payment option"}", context: context, textColor: primaryColor),
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
                                txt18Size(title: "${controller.labelTextDetail['search_filter_all_label'] ?? "All"}", context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: "${controller.labelTextDetail['search_filter_all_label'] ?? "All"}", context: context, fontFamily: bold),
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
                  txt24Size(title: "${controller.labelTextDetail['filter4_vehicle_heading'] ?? "Vehicle"}", context: context, textColor: primaryColor),
                  5.heightBox,
                  Container(
                    color: inputColor,
                    child: DropdownButtonFormField2(
                      isExpanded: true,
                      decoration: InputDecoration(
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(5.0),
                          borderSide: BorderSide(
                            color: Colors.grey.shade400,
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
                      value: controller.vehicleType.value,
                      items: [
                        DropdownMenuItem(
                          value: "",
                          child: controller.vehicleType.value == "" ?
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              txt18Size(title: "${controller.labelTextDetail['search_filter_select_vehicle_label'] ?? "Select vehicle type"}", context: context, fontFamily: bold),
                              Icon(Icons.check, color: btnPrimaryColor, size: 20)
                            ],
                          ) : txt18Size(title: "${controller.labelTextDetail['search_filter_select_vehicle_label'] ?? "Select vehicle type"}", context: context, fontFamily: bold),
                        ),
                        for(var i = 0; i < controller.vehicleTypeList.length; i++)...[
                          DropdownMenuItem(
                            value: controller.vehicleTypeList[i].toString(),
                            child: controller.vehicleType.value == controller.vehicleTypeList[i].toString() ?
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                txt18Size(title: controller.vehicleTypeLabelList[i], context: context, fontFamily: bold),
                                Icon(Icons.check, color: btnPrimaryColor, size: 20)
                              ],
                            ) : txt18Size(title: controller.vehicleTypeLabelList[i], context: context, fontFamily: bold),
                          ),
                        ],
                      ],
                      onChanged: (data) {
                        controller.vehicleType.value = data!;
                        if (controller.errors.firstWhereOrNull((element) => element['title'] == "type") != null) {
                          controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "type"));
                        }
                      },
                      alignment: AlignmentDirectional.topCenter,
                      dropdownStyleData: DropdownStyleData(
                        //maxHeight: context.screenHeight * 0.45,
                        //width: context.screenWidth - 30,
                        // padding: EdgeInsets.only(bottom: 100),
                        decoration: BoxDecoration(
                          border: Border.all(width: 2,color: primaryColor),
                          borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(10.0), bottomRight: Radius.circular(10.0)),

                        ),
                      ),
                    ),
                  ),
                  10.heightBox,
                  txt24Size(title: "${controller.labelTextDetail['ride_preferences_label'] ?? "Ride preference"}", context: context, textColor: primaryColor),
                  5.heightBox,
                  ridePreferenceWidget(context: context, controller: controller, screenWidth: screenWidth),
                  10.heightBox,
                  txt24Size(title: "${controller.labelTextDetail['luggage_label'] ?? "Luggage (I want rides that accept the following luggage options)"}", context: context, textColor: primaryColor),
                  5.heightBox,
                  luggageWidget(context: context, controller: controller, screenWidth: screenWidth),
                  10.heightBox,
                  txt24Size(title: "${controller.labelTextDetail['smoking_label'] ?? "Smoking"}", context: context, textColor: primaryColor),
                  5.heightBox,
                  smokingWidget(context: context, controller: controller, screenWidth: screenWidth),
                  10.heightBox,
                  txt24Size(title: "${controller.labelTextDetail['pets_allowed_label'] ?? "Pets allowed"}", context: context, textColor: primaryColor),
                  5.heightBox,
                  petAnimalWidget(context: context, controller: controller, screenWidth: screenWidth),
                  50.heightBox,

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
                      textWidget: txt22Size(title: "${controller.labelTextDetail['clear_button_label'] ?? "Clear"}", context: context, textColor: Colors.white),
                      btnColor: primaryColor,
                      onPressed: () async{
                        Get.back();
                        controller.clearFilter();
                        controller.actionType.value = "clear";
                        await controller.getSearchRide(1);
                      }
                    ),
                  ),
                  5.widthBox,
                  Expanded(
                    child: elevatedButtonWidget(
                        textWidget: txt22Size(title: "${controller.labelTextDetail['apply_button_label'] ?? "Apply"}", context: context, textColor: Colors.white),
                        onPressed: () async{
                          Get.back();
                          controller.actionType.value = "apply";
                          controller.filter.value = true;
                          await controller.getSearchRide(1);
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