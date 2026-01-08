import 'package:dropdown_button2/dropdown_button2.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_vehicle/MyVehicleController.dart';
import 'package:proximaride_app/pages/my_vehicle/widget/image_edit_delete_btn_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/image_upload_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/image_upload_widget.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class AddVehiclePage extends StatelessWidget {
  const AddVehiclePage({super.key});

  @override
  Widget build(BuildContext context) {
    var controller = Get.find<MyVehicleController>();
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: secondAppBarWidget(
              title: controller.vehicleId.value == 0
                  ? "${controller.labelTextDetail['add_main_heading'] ?? "Add vehicle"}"
                  : "${controller.labelTextDetail['edit_main_heading'] ?? "Edit vehicle"}",
              context: context),
          leading: const BackButton(
            color: Colors.white,
          ),
        ),
        body: Obx(() {
          if (controller.isLoading.value == true) {
            return Center(child: progressCircularWidget(context));
          } else {
            return Stack(
              children: [
                Container(
                    padding: EdgeInsets.all(getValueForScreenType<double>(
                      context: context,
                      mobile: 15.0,
                      tablet: 15.0,
                    )),
                    child: SingleChildScrollView(
                      controller: controller.scrollController,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          5.heightBox,
                          txt20Size(title: "* ${controller.labelTextDetail['mobile_indicate_field_label'] ?? "* Indicates required field"}", fontFamily: regular, context: context,textColor: Colors.red),
                          5.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['make_label'] ?? "Make"}",
                                  fontFamily: regular,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          fieldsWidget(
                            maxLength: 30,
                            textController:
                                controller.makeTextEditingController,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "make") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "make"));
                              }
                            },
                            fieldType: "text",
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            placeHolder: "${controller.labelTextDetail['make_placeholder'] ?? "Example: Honda, Toyota"}",
                            isError: controller.errors.firstWhereOrNull((element) => element['title'] == "make") != null,
                            focusNode: controller.focusNodes[1.toString()],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "make") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "make"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['model_label'] ?? "Model"}",
                                  fontFamily: regular,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          fieldsWidget(
                            maxLength: 30,
                              textController:
                                  controller.modelTextEditingController,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "model") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "model"));
                              }
                            },
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              placeHolder: "${controller.labelTextDetail['model_placeholder'] ?? "Example: Accord, Corolla"}",
                            isError: controller.errors.firstWhereOrNull((element) => element['title'] == "model") != null,
                            focusNode: controller.focusNodes[2.toString()],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "model") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "model"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['license_plate_number_label'] ?? "License plate number"}",
                                  fontFamily: regular,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          fieldsWidget(
                            maxLength: 30,
                              textController:
                                  controller.licenseNumberTextEditingController,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "liscense_no") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "liscense_no"));
                              }
                            },
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              isError: controller.errors.firstWhereOrNull((element) => element['title'] == "liscense_no") != null,
                            focusNode: controller.focusNodes[3.toString()],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "liscense_no") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "liscense_no"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['color_label'] ?? "Color"}",
                                  fontFamily: regular,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          fieldsWidget(
                            maxLength: 30,
                              textController:
                                  controller.colorTextEditingController,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "color") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "color"));
                              }
                            },
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              isError: controller.errors.firstWhereOrNull((element) => element['title'] == "color") != null,
                            focusNode: controller.focusNodes[4.toString()],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "color") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "color"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['year_label'] ?? "Year"}",
                                  fontFamily: regular,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          fieldsWidget(
                            maxLength: 4,
                              textController:
                                  controller.yearTextEditingController,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "year") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "year"));
                              }
                            },
                              fieldType: "number",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              isError: controller.errors.firstWhereOrNull((element) => element['title'] == "year") != null,
                            focusNode: controller.focusNodes[5.toString()],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "year") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "year"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['vehicle_type_label'] ?? "Vehicle type"}",
                                  fontFamily: regular,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,

                          DropdownButtonFormField2(
                            isExpanded: true,
                            decoration: InputDecoration(
                              enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(5.0),
                                borderSide: BorderSide(
                                  color: controller.errors.firstWhereOrNull((element) => element['title'] == "type") != null ? primaryColor : Colors.grey.shade400,
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
                              maxHeight: context.screenHeight * 0.45,
                              width: context.screenWidth - 30,
                              // padding: EdgeInsets.only(bottom: 100),
                              decoration: BoxDecoration(
                                border: Border.all(width: 2,color: primaryColor),
                                borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(10.0), bottomRight: Radius.circular(10.0)),

                              ),
                            ),
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "type") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "type"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['fuel_label'] ?? "Fuel"}",
                                  fontFamily: regular,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
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
                                        value: controller.fuel.value ==
                                                "Electric car"
                                            ? true
                                            : false,
                                        onChanged: (value) async {
                                          await controller.updateFuelValue(
                                              value == true
                                                  ? "Electric car"
                                                  : "");
                                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type") != null)
                                          {
                                            controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type"));
                                          }
                                        },
                                      isError: controller.errors.firstWhereOrNull((element) => element['title'] == "car_type") != null
                                    ),
                                  ),
                                  5.widthBox,
                                  InkWell(
                                    onTap: () async{
                                      if(controller.fuel.value == "Electric car"){
                                        await controller.updateFuelValue("");
                                      }else{
                                        await controller.updateFuelValue("Electric car");
                                      }
                                      if(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type") != null) {
                                        controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type"));
                                      }
                                    },
                                    child: txt16Size(
                                        title: "${controller.labelTextDetail['electric_checkbox_label'] ?? "Electric"}",
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
                                        value: controller.fuel.value ==
                                                "Hybrid car"
                                            ? true
                                            : false,
                                        onChanged: (value) async {
                                          await controller.updateFuelValue(
                                              value == true
                                                  ? "Hybrid car"
                                                  : "");
                                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type") != null)
                                          {
                                            controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type"));
                                          }
                                        },
                                        isError: controller.errors.firstWhereOrNull((element) => element['title'] == "car_type") != null
                                    ),
                                  ),
                                  5.widthBox,
                                  InkWell(
                                    onTap: () async{
                                      if(controller.fuel.value == "Hybrid car"){
                                        await controller.updateFuelValue("");
                                      }else{
                                        await controller.updateFuelValue("Hybrid car");
                                      }
                                      if(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type") != null) {
                                        controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type"));
                                      }
                                    },
                                    child: txt16Size(
                                        title: "${controller.labelTextDetail['hybrid_checkbox_label'] ?? "Hybrid"}",
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
                                        value: controller.fuel.value ==
                                            "Gas"
                                            ? true
                                            : false,
                                        onChanged: (value) async {
                                          await controller.updateFuelValue(
                                              value == true
                                                  ? "Gas"
                                                  : "");
                                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type") != null)
                                          {
                                            controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type"));
                                          }
                                        },
                                        isError: controller.errors.firstWhereOrNull((element) => element['title'] == "car_type") != null
                                    ),
                                  ),
                                  5.widthBox,
                                  InkWell(
                                    onTap: () async{
                                      if(controller.fuel.value == "Gas"){
                                        await controller.updateFuelValue("");
                                      }else{
                                        await controller.updateFuelValue("Gas");
                                      }
                                      if(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type") != null) {
                                        controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type"));
                                      }
                                    },
                                    child: txt16Size(
                                        title: "${controller.labelTextDetail['gas_checkbox_label'] ?? "Gas"}",
                                        context: context,
                                        fontFamily: regular),
                                  )
                                ],
                              ),

                            ],
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "car_type") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "car_type"))
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title: "${controller.labelTextDetail['set_primary_vehicle_label'] ?? "Set as primary vehicle"}",
                                  fontFamily: regular,
                                  context: context),
                              txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                            ],
                          ),
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
                                      value: controller.setPrimary.value ==
                                          "yes"
                                          ? true
                                          : false,
                                      onChanged: (value) async {
                                        await controller.updateSetPrimaryValue(
                                            value == true
                                                ? "yes"
                                                : "");
                                      },
                                    ),
                                  ),
                                  5.widthBox,
                                  InkWell(
                                    onTap: () async{
                                      if(controller.setPrimary.value == "yes"){
                                        await controller.updateSetPrimaryValue("");
                                      }else{
                                        await controller.updateSetPrimaryValue("yes");
                                      }
                                    },
                                    child: txt16Size(
                                        title: "${controller.labelTextDetail['yes_checkbox_label'] ?? "Yes"}",
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
                                      value: controller.setPrimary.value ==
                                          "no"
                                          ? true
                                          : false,
                                      onChanged: (value) async {
                                        await controller.updateSetPrimaryValue(
                                            value == true
                                                ? "no"
                                                : "");
                                      },
                                    ),
                                  ),
                                  5.widthBox,
                                  InkWell(
                                    onTap: () async{
                                      if(controller.setPrimary.value == "no"){
                                        await controller.updateSetPrimaryValue("");
                                      }else{
                                        await controller.updateSetPrimaryValue("no");
                                      }
                                    },
                                    child: txt16Size(
                                        title: "${controller.labelTextDetail['no_checkbox_label'] ?? "No"}",
                                        context: context,
                                        fontFamily: regular),
                                  )
                                ],
                              ),
                            ],
                          ),
                          10.heightBox,
                          if (controller.oldCarImagePath.value == "") ...[
                            txt20Size(
                                title: "${controller.labelTextDetail['car_photo_label'] ?? "Car photo"}",
                                fontFamily: regular,
                                context: context),
                            10.heightBox,
                            imageUploadWidget(
                                context: context,
                                onTap: () async {
                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "image") != null)
                                  {
                                    controller.errors.removeWhere((element) => element['title'] == "image");
                                  }
                                  await imageUploadBottomSheet(
                                      controller, context);
                                },
                                title: "${controller.labelTextDetail['upload_profile_photo_image_placeholder'] ?? "Car photo."}",
                                title1: "${controller.labelTextDetail['choose_file_image_placeholder'] ?? "Choose file"}",
                                title2: "${controller.labelTextDetail['images_option_placeholder'] ?? "(Only JPG, PNG, JPEG and GIF are allowed. Max. 10MB)"}",
                                imageFile: controller.carImageName.value == ""
                                    ? null
                                    : controller.carImagePath.value,
                                screenWidth: context.screenWidth),
                          ] else ...[
                            txt20Size(
                                title:
                                "${controller.labelTextDetail['image_description_label'] ?? "This is your car. Click on the upload button if you want to change it"}",
                                fontFamily: regular,
                                context: context),
                            10.heightBox,
                            txt20Size(
                                title:
                                "${controller.labelTextDetail['edit_photo_label'] ?? "Click on the vehicle photo in order to edit it"}",
                                fontFamily: regular,
                                context: context),
                            10.heightBox,
                            ClipRRect(
                              borderRadius: BorderRadius.circular(15.0),
                              child: Stack(
                                children: [
                                  InkWell(
                                    onTap: (){
                                      controller.serviceController.showImage.value = controller.oldCarImagePathOriginal.value;
                                      Get.toNamed("/show_image");
                                    },
                                    child: networkCacheImageWidget(
                                        "${controller.oldCarImagePath}",
                                        BoxFit.fill,
                                        context.screenWidth,
                                        320.0),
                                  ),
                                  Positioned(
                                      top: 10,
                                      left: context.width - 70,
                                      child: Row(
                                        mainAxisAlignment:
                                            MainAxisAlignment.spaceBetween,
                                        children: [
                                          imageEditDeleteWidget(
                                              imagePath: editImage,
                                              context: context,
                                              onTap: () async {
                                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "image") != null)
                                                {
                                                  controller.errors.removeWhere((element) => element['title'] == "image");
                                                }
                                                await imageUploadBottomSheet(
                                                    controller, context);
                                              }),
                                        ],
                                      ))
                                ],
                              ),
                            ),
                          ],
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "image") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "image"))
                          ],

                          10.heightBox,
                          if( controller.carImagePath.value != "" || controller.oldCarImagePath.value != "" ) ...[
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
                                    value: controller.removeCarPhoto.value,
                                    onChanged: (value) async {

                                      if(controller.removeCarPhoto.value == false)
                                        {
                                          controller.confirmationRemoveCarPhoto();
                                        }
                                      else{
                                        controller.removeCarPhoto.value = value!;
                                      }
                                    }),
                              ),
                              5.widthBox,
                              InkWell(
                                onTap: (){
                                  if(controller.removeCarPhoto.value == false)
                                  {
                                    controller.confirmationRemoveCarPhoto();
                                  }
                                  else{
                                    controller.removeCarPhoto.value = controller.removeCarPhoto.value == true ? false : true;
                                  }
                                },
                                child: txt16Size(
                                    title: "${controller.labelTextDetail['remove_car_photo_label'] ?? "Remove car photo"}",
                                    fontFamily: bold,
                                    context: context),
                              ),
                            ],
                          ),
                          ],
                          80.heightBox,
                        ],
                      ),
                    )),
                Align(
                  alignment: Alignment.bottomCenter,
                  child: Container(
                    width: context.screenWidth,
                    padding: const EdgeInsets.all(15.0),
                    color: Colors.grey.shade100,
                    child: elevatedButtonWidget(
                      textWidget: txt28Size(
                          title: controller.vehicleId.value == 0
                              ? "${controller.labelTextDetail['add_vehicle_button_text'] ?? "Add vehicle"}"
                              : "${controller.labelTextDetail['update_vehicle_button_text'] ?? "Update vehicle"}",
                          textColor: Colors.white,
                          context: context,
                          fontFamily: regular),
                      onPressed: () async {
                        await controller.addNewVehicle(context, context.screenHeight);
                      },
                    ),
                  ),
                ),
                if (controller.isOverlayLoading.value == true) ...[
                  overlayWidget(context)
                ]
              ],
            );
          }
        }));
  }
}
