import 'package:dropdown_button2/dropdown_button2.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/image_upload_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/image_upload_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';

import '../../widgets/tool_tip.dart';

Widget vehicleWidget(
    {context, controller, screenWidth, bool bookingCheck = false, error, screenHeight}) {
  return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8.0),
          border: Border.all(width: 1, color: inputColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          postRideWidget(
              title: "${controller.labelTextDetail['vehicle_label'] ?? "Vehicle"}", screenWidth: screenWidth, context: context,isRequired: true),
          Container(
            padding: EdgeInsets.all(getValueForScreenType<double>(
              context: context,
              mobile: 10.0,
              tablet: 10.0,
            )),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: [
                          SizedBox(
                            width: getValueForScreenType<double>(
                              context: context,
                              mobile: 25.0,
                              tablet: 25.0,
                            ),
                            height: getValueForScreenType<double>(
                              context: context,
                              mobile: 25.0,
                              tablet: 25.0,
                            ),
                            child: checkBoxWidget(
                                value: controller.skipNow.value,
                                onChanged: bookingCheck == true
                                    ? null
                                    : (value) {
                                  controller.skipNow.value = value!;
                                  controller.addNewVehicle.value = false;
                                  controller.alreadyAdded.value = false;
                                  controller.modelTextEditingController
                                      .clear();
                                  controller.makeTextEditingController
                                      .clear();
                                  controller
                                      .licenseNumberTextEditingController
                                      .clear();
                                  controller.colorTextEditingController
                                      .clear();
                                  controller.yearTextEditingController
                                      .clear();
                                  controller.vehicleType.value = "";
                                  controller.carImageName.value = "";
                                  controller.vehicleId.value = "";
                                }),
                          ),
                          5.widthBox,
                          InkWell(
                            onTap: bookingCheck == true ? null : (){
                              controller.skipNow.value = controller.skipNow.value == true ? false : true;
                              controller.addNewVehicle.value = false;
                              controller.alreadyAdded.value = false;
                              controller.modelTextEditingController
                                  .clear();
                              controller.makeTextEditingController
                                  .clear();
                              controller
                                  .licenseNumberTextEditingController
                                  .clear();
                              controller.colorTextEditingController
                                  .clear();
                              controller.yearTextEditingController
                                  .clear();
                              controller.vehicleType.value = "";
                              controller.carImageName.value = "";
                              controller.vehicleId.value = "";
                            },
                            child: txt16Size(
                                title: "${controller.labelTextDetail['skip_label'] ?? "Skip for now"}",
                                context: context,
                                fontFamily: bold,
                                textColor: textColor
                            ),
                          )
                        ],
                      ),
                      5.widthBox,
                      Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: [
                          SizedBox(
                            width: getValueForScreenType<double>(
                              context: context,
                              mobile: 25.0,
                              tablet: 25.0,
                            ),
                            height: getValueForScreenType<double>(
                              context: context,
                              mobile: 25.0,
                              tablet: 25.0,
                            ),
                            child: checkBoxWidget(
                                value: controller.addNewVehicle.value,
                                onChanged: bookingCheck == true
                                    ? null
                                    : (value) {
                                  controller.addNewVehicle.value = value!;
                                  controller.skipNow.value = false;
                                  controller.alreadyAdded.value = false;
                                  // controller.vehicleId.value = "";
                                }),
                          ),
                          5.widthBox,
                          InkWell(
                            onTap: bookingCheck == true ? null : (){
                              controller.addNewVehicle.value = controller.addNewVehicle.value == true ? false : true;
                              controller.skipNow.value = false;
                              controller.alreadyAdded.value = false;
                            },
                            child: txt16Size(
                                title: "${controller.labelTextDetail['add_vehicle_label'] ?? "Add new vehicle"}",
                                context: context,
                                fontFamily: bold),
                          ),
                        ],
                      ),
                      if(controller.vehicleList.length != 0 ) ...[
                        5.widthBox,
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          children: [
                            SizedBox(
                              width: getValueForScreenType<double>(
                                context: context,
                                mobile: 25.0,
                                tablet: 25.0,
                              ),
                              height: getValueForScreenType<double>(
                                context: context,
                                mobile: 25.0,
                                tablet: 25.0,
                              ),
                              child: checkBoxWidget(
                                  value: controller.alreadyAdded.value,
                                  onChanged: bookingCheck == true
                                      ? null
                                      : (value) {
                                    controller.alreadyAdded.value = value!;
                                    controller.addNewVehicle.value = false;
                                    controller.skipNow.value = false;
                                    controller.modelTextEditingController
                                        .clear();
                                    controller.makeTextEditingController
                                        .clear();
                                    controller
                                        .licenseNumberTextEditingController
                                        .clear();
                                    controller.colorTextEditingController
                                        .clear();
                                    controller.yearTextEditingController
                                        .clear();
                                    controller.vehicleType.value = "";
                                    controller.carImageName.value = "";
                                    controller.vehicleId.value = "";
                                  }),
                            ),
                            5.widthBox,
                            InkWell(
                              onTap: bookingCheck == true ? null : (){
                                controller.alreadyAdded.value = controller.alreadyAdded.value == true ? false : true;
                                controller.addNewVehicle.value = false;
                                controller.skipNow.value = false;
                                controller.modelTextEditingController
                                    .clear();
                                controller.makeTextEditingController
                                    .clear();
                                controller
                                    .licenseNumberTextEditingController
                                    .clear();
                                controller.colorTextEditingController
                                    .clear();
                                controller.yearTextEditingController
                                    .clear();
                                controller.vehicleType.value = "";
                                controller.carImageName.value = "";
                                controller.vehicleId.value = "";
                              },
                              child: txt16Size(
                                  title: "${controller.labelTextDetail['existing_label'] ?? "Existing"}",
                                  context: context,
                                  fontFamily: bold,
                                  textColor: textColor),
                            )
                          ],
                        ),
                      ],

                    ],
                  ),
                ),
                if (controller.addNewVehicle.value == true) ...[
                  10.heightBox,
                  txt20Size(
                      title: "${controller.labelTextDetail['make_label'] ?? "Make"}", fontFamily: regular, context: context),
                  3.heightBox,
                  fieldsWidget(
                    textController: controller.makeTextEditingController,
                    fieldType: "text",
                    readonly: bookingCheck,
                    fontFamily: regular,
                    fontSize: 16.0,
                    placeHolder: "${controller.labelTextDetail['make_placeholder'] ?? "Example: Honda"}",
                    onChanged: (value) {
                      if (controller.errors.any((error) => error['title'] == "make")) {
                        controller.errors.removeWhere((error) => error['title'] == "make");
                      }
                    },
                    isError: controller.errors
                        .where((error) => error == "make")
                        .isNotEmpty,
                    focusNode: controller.focusNodes[6.toString()],
                  ),
                  if(controller.errors.any((error) => error['title'] == "make")) ...[
                    toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "make"))
                  ],
                  10.heightBox,
                  txt20Size(
                      title: "${controller.labelTextDetail['model_label'] ?? "Model"}", fontFamily: regular, context: context),
                  3.heightBox,
                  fieldsWidget(
                    textController: controller.modelTextEditingController,
                    fieldType: "text",
                    readonly: bookingCheck,
                    fontFamily: regular,
                    fontSize: 16.0,
                    placeHolder: "${controller.labelTextDetail['model_placeholder'] ?? "Example: Accord"}",
                    onChanged: (value) {
                      if (controller.errors.any((error) => error['title'] == "model")) {
                        controller.errors.removeWhere((error) => error['title'] == "model");
                      }
                    },
                    isError: controller.errors
                        .where((error) => error == "model")
                        .isNotEmpty,
                    focusNode: controller.focusNodes[7.toString()],
                  ),
                  if(controller.errors.any((error) => error['title'] == "model")) ...[
                    toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "model"))
                  ],
                  10.heightBox,
                  txt20Size(
                      title: "${controller.labelTextDetail['liscense_label'] ?? "License plate number"}",
                      fontFamily: regular,
                      context: context),
                  5.heightBox,
                  fieldsWidget(
                    textController:
                    controller.licenseNumberTextEditingController,
                    fieldType: "text",
                    readonly: bookingCheck,
                    fontFamily: regular,
                    fontSize: 16.0,
                    onChanged: (value) {
                      if (controller.errors.any((error) => error['title'] == "license_no")) {
                        controller.errors.removeWhere((error) => error['title'] == "license_no");
                      }
                    },
                    isError: controller.errors
                        .where((error) => error == "license_no")
                        .isNotEmpty,
                    focusNode: controller.focusNodes[8.toString()],
                  ),
                  if(controller.errors.any((error) => error['title'] == "license_no")) ...[
                    toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "license_no"))
                  ],
                  10.heightBox,
                  txt20Size(
                      title: "${controller.labelTextDetail['color_label'] ?? "Color"}", fontFamily: regular, context: context),
                  5.heightBox,
                  fieldsWidget(
                    textController: controller.colorTextEditingController,
                    fieldType: "text",
                    readonly: bookingCheck,
                    fontFamily: regular,
                    fontSize: 16.0,
                    onChanged: (value) {
                      if (controller.errors.any((error) => error['title'] == "color")) {
                        controller.errors.removeWhere((error) => error['title'] == "color");
                      }
                    },
                    isError: controller.errors
                        .where((error) => error == "color")
                        .isNotEmpty,
                    focusNode: controller.focusNodes[9.toString()],
                  ),
                  if(controller.errors.any((error) => error['title'] == "color")) ...[
                    toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "color"))
                  ],
                  10.heightBox,
                  txt20Size(
                      title: "${controller.labelTextDetail['year_label'] ?? "Year"}", fontFamily: regular, context: context),
                  5.heightBox,
                  fieldsWidget(
                    textController: controller.yearTextEditingController,
                    fieldType: "number",
                    readonly: bookingCheck,
                    fontFamily: regular,
                    fontSize: 16.0,
                    onChanged: (value) {
                      if (controller.errors.any((error) => error['title'] == "year")) {
                        controller.errors.removeWhere((error) => error['title'] == "year");
                      }
                    },
                    isError: controller.errors
                        .where((error) => error == "year")
                        .isNotEmpty,
                    focusNode: controller.focusNodes[10.toString()],
                  ),
                  if(controller.errors.any((error) => error['title'] == "year")) ...[
                    toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "year"))
                  ],
                  10.heightBox,
                  txt20Size(
                      title: "${controller.labelTextDetail['type_label'] ?? "Vehicle type"}",
                      fontFamily: regular,
                      context: context),
                  5.heightBox,


                  Container(
                    color: inputColor,
                    child:  DropdownButtonFormField2(
                      isExpanded: true,
                      decoration: InputDecoration(
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(5.0),
                          borderSide: BorderSide(
                            //color: controller.errors.firstWhereOrNull((element) => element['title'] == "type") != null ? primaryColor : Colors.grey.shade400,
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
                              txt18Size(title: "${controller.labelTextDetail['vehicle_type_placeholder'] ?? "Select vehicle type"}", context: context, fontFamily: bold),
                              Icon(Icons.check, color: btnPrimaryColor, size: 20)
                            ],
                          ) : txt18Size(title: "${controller.labelTextDetail['vehicle_type_placeholder'] ?? "Select vehicle type"}", context: context, fontFamily: bold),
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

                  if(error.any((error) => error['title'] == "vehicle_type")) ...[
                    toolTip(tip: error.firstWhere((error) => error['title'] == "vehicle_type"))
                  ],
                  10.heightBox,
                  txt20Size(
                      title: "${controller.labelTextDetail['car_type_label'] ?? "Fuel"}", fontFamily: regular, context: context),
                  5.heightBox,
                  Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    children: [

                      Row(
                        children: [
                          SizedBox(
                            width: getValueForScreenType<double>(
                              context: context,
                              mobile: 25.0,
                              tablet: 25.0,
                            ),
                            height: getValueForScreenType<double>(
                              context: context,
                              mobile: 25.0,
                              tablet: 25.0,
                            ),
                            child: checkBoxWidget(
                                value: controller.fuel.value == "Electric car"
                                    ? true
                                    : false,
                                isError: controller.errors
                                    .where((error) => error == "car_type")
                                    .isNotEmpty,
                                onChanged: bookingCheck == true
                                    ? null
                                    : (value) async {
                                  controller.fuel.value =
                                  value == true ? "Electric car" : "";
                                }),
                          ),
                          5.widthBox,
                          InkWell(
                            onTap: bookingCheck == true ? null : (){
                              controller.fuel.value = controller.fuel.value == "Electric car" ? "" : "Electric car";
                            },
                            child: txt16Size(
                                title: "${controller.labelTextDetail['electric_car_label'] ?? "Electric"}",
                                context: context,
                                fontFamily: regular),
                          )
                        ],
                      ),
                      20.widthBox,
                      Row(
                        children: [
                          SizedBox(
                            width: getValueForScreenType<double>(
                              context: context,
                              mobile: 25.0,
                              tablet: 25.0,
                            ),
                            height: getValueForScreenType<double>(
                              context: context,
                              mobile: 25.0,
                              tablet: 25.0,
                            ),
                            child: checkBoxWidget(
                                value: controller.fuel.value == "Hybrid car"
                                    ? true
                                    : false,
                                isError: controller.errors
                                    .where((error) => error == "car_type")
                                    .isNotEmpty,
                                onChanged: bookingCheck == true
                                    ? null
                                    : (value) async {
                                  controller.fuel.value =
                                  value == true ? "Hybrid car" : "";
                                }),
                          ),
                          5.widthBox,
                          InkWell(
                            onTap: bookingCheck == true ? null : (){
                              controller.fuel.value = controller.fuel.value == "Hybrid car" ? "" : "Hybrid car";
                            },
                            child: txt16Size(
                                title: "${controller.labelTextDetail['hybrid_car_label'] ?? "Hybrid"}",
                                context: context,
                                fontFamily: regular),
                          )
                        ],
                      ),
                      20.widthBox,
                      Row(
                        children: [
                          SizedBox(
                            width: getValueForScreenType<double>(
                              context: context,
                              mobile: 25.0,
                              tablet: 25.0,
                            ),
                            height: getValueForScreenType<double>(
                              context: context,
                              mobile: 25.0,
                              tablet: 25.0,
                            ),
                            child: checkBoxWidget(
                                value: controller.fuel.value == "Gas"
                                    ? true
                                    : false,
                                isError: controller.errors
                                    .where((error) => error == "car_type")
                                    .isNotEmpty,
                                onChanged: bookingCheck == true
                                    ? null
                                    : (value) async {
                                  controller.fuel.value =
                                  value == true ? "Gas" : "";
                                }),
                          ),
                          5.widthBox,
                          InkWell(
                            onTap: bookingCheck == true ? null : (){
                              controller.fuel.value = controller.fuel.value == "Gas" ? "" : "Gas";
                            },
                            child: txt16Size(
                                title: "${controller.labelTextDetail['gas_car_label'] ?? "Gas"}",
                                context: context,
                                fontFamily: regular),
                          )
                        ],
                      ),
                    ],
                  ),
                  if(error.any((error) => error['title'] == "car_type")) ...[
                    toolTip(tip: error.firstWhere((error) => error['title'] == "car_type"))
                  ],
                  10.heightBox,
                  txt20Size(
                      title: "${controller.labelTextDetail['car_photo_label'] ?? "Car photo"}",
                      fontFamily: regular,
                      context: context),
                  10.heightBox,
                  imageUploadWidget(
                      context: context,
                      onTap: bookingCheck == true
                          ? null
                          : () async {
                        await imageUploadBottomSheet(controller, context);
                      },
                      title: "${controller.labelTextDetail['car_photo_label'] ?? "Car photo"}",
                      imageFile: controller.carImageName.value == ""
                          ? null
                          : controller.carImagePath.value,
                      screenWidth: screenWidth),
                ]
                else ...[
                  if(controller.alreadyAdded.value == true)...[
                    10.heightBox,
                    Container(
                      color: inputColor,
                      child: DropdownButtonFormField(
                          isExpanded: true,
                          elevation: 2,
                          decoration: InputDecoration(
                              enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(5.0),
                                  borderSide: BorderSide(
                                      color: Colors.grey.shade400,
                                      style: BorderStyle.solid,
                                      width: 1)),
                              focusedBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(5.0),
                                  borderSide:
                                  const BorderSide(color: primaryColor)),
                              contentPadding: const EdgeInsets.symmetric(
                                  vertical: 0.0, horizontal: 8.0),
                              fillColor: inputColor),
                          value: controller.vehicleId.value,
                          items: [
                              DropdownMenuItem(
                                value: "",
                                child: txt18Size(
                                    title: "${controller.labelTextDetail['select_vehicle'] ?? "Select vehicle"}",
                                    context: context,
                                    fontFamily: bold),
                              ),
                            if (controller.vehicleList.isNotEmpty) ...[
                              for (var i = 0;
                              i < controller.vehicleList.length;
                              i++) ...[
                                DropdownMenuItem(
                                    value: controller.vehicleList[i]['id']
                                        .toString(),
                                    child: Container(
                                      child: txt18Size(
                                          title:
                                          "${controller.vehicleList[i]['year']} ${controller.vehicleList[i]['make']} ${controller.vehicleList[i]['model']}",
                                          context: context,
                                          fontFamily: bold),
                                    )),
                              ]
                            ]
                          ],
                          onChanged: bookingCheck == true
                              ? null
                              : (data) {
                            controller.vehicleId.value = data!;
                          }),
                    ),
                    if(controller.errors.any((error) => error['title'] == "vehicle_id")) ...[
                      toolTip(tip: controller.errors.firstWhere((error) => error['title'] == "vehicle_id"))
                    ],
                  ],
                ],
              ],
            ),
          ),
        ],
      ));
}
