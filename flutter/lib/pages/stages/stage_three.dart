import 'package:dropdown_button2/dropdown_button2.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/stages/StageThreeController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/image_upload_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/image_upload_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/step_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../widgets/tool_tip.dart';

class StageThree extends StatelessWidget {
  const StageThree({super.key});

  @override
  Widget build(BuildContext context) {
    final StageThreeController controller = Get.isRegistered<StageThreeController>()
        ? Get.find<StageThreeController>()
        : Get.put(StageThreeController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          leading: IconButton(
            icon: const Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () {
              Navigator.of(context).maybePop();
            },
          ),
          title: Obx(
            () => stepAppBarWidget(
              context: context,
              serviceController: controller.serviceController,
              langId: controller.serviceController.langId.value,
              langIcon: controller.serviceController.langIcon.value,
              screeWidth: context.screenWidth,
              page: "step3",
            ),
          ),
        ),
        body: Obx(() {
          if (controller.errorStateManager.hasError.value) {
            return ErrorStateWidget(
              message: controller.errorStateManager.errorMessage.value,
              errorType: controller.errorStateManager.errorType.value,
              onRetry:
                  controller.errorStateManager.onRetry.value! as VoidCallback,
            );
          } else if (controller.isLoading.value == true) {
            return Center(child: progressCircularWidget(context));
          } else {
            return SafeArea(
              child: Stack(
                children: [
                  Container(
                      padding: EdgeInsets.all(getValueForScreenType<double>(
                        context: context,
                        mobile: 15.0,
                        tablet: 15.0,
                      )),
                      child: SingleChildScrollView(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                              Center(
                                  child: txt25Size(
                                      title: controller.step3MainHeading.value,
                                      textColor: primaryColor,
                                      context: context)),
                              5.heightBox,
                              // txt16Size(
                              //     title:
                              //         "${controller.labelTextDetail['sub_heading'] ?? "Please provide your vehicle details"}",
                              //     context: context,
                              //     textColor: Colors.red),
                              // 5.heightBox,
                              Html(
                                data: controller
                                        .labelTextDetail['main_label'] ??
                                    "If you are signing up as a driver, please note that to be eligible to post Pink Rides and Extra-Care Rides, you must state your vehicle details on every ride",
                                style: {
                                  "body": Style(
                                      padding: HtmlPaddings.zero,
                                      margin: Margins.zero),
                                  'p': Style(
                                    fontSize: FontSize(18),
                                    fontFamily: carlito,
                                    padding: HtmlPaddings.zero,
                                    margin: Margins.zero,
                                    textAlign: TextAlign.justify
                                  ),
                                  'div': Style(
                                    fontSize: FontSize(20),
                                    padding: HtmlPaddings.zero,
                                    margin: Margins.zero,
                                  )
                                },
                              ),
                              // txt18Size(
                              //     title:
                              //         "${controller.labelTextDetail['main_label'] ?? "If you are signing up as a driver, please note that to be eligible to post Pink Rides and Extra-Care Rides, you must state your vehicle details on every ride"}",
                              //     context: context),
                              15.heightBox,
                              Html(
                                data: controller
                                        .labelTextDetail['sub_main_label'] ??
                                    "If you intend to use ProximaRide as a passenger only, then this point is not applicable to you. You may 'Skip' it",
                                style: {
                                  "body": Style(
                                      padding: HtmlPaddings.zero,
                                      margin: Margins.zero),
                                  'p': Style(
                                    fontSize: FontSize(18),
                                    fontFamily: carlito,
                                    padding: HtmlPaddings.zero,
                                    margin: Margins.zero,
                                    textAlign: TextAlign.justify
                                  ),
                                  'div': Style(
                                    fontSize: FontSize(20),
                                    padding: HtmlPaddings.zero,
                                    margin: Margins.zero,
                                  )
                                },
                              ),
                              // 10.heightBox,
                              // txt16Size(
                              //     title:
                              //         "${controller.labelTextDetail['auto_primary_vehicle_message'] ?? "The first vehicle you add will automatically become your primary vehicle."}",
                              //     context: context,
                              //     textColor: Colors.green,
                              //     fontFamily: regular),
                              // txt18Size(
                              //     title:
                              //         "${controller.labelTextDetail['sub_main_label'] ?? "If you intend to use ProximaRide as a passenger only, then this point is not applicable to you. You may 'Skip' it"}",
                              //     context: context),
                              15.heightBox,
                              txt18Size(
                                  title:
                                      "${controller.labelTextDetail['required_label'] ?? "* Indicates required fields"}",
                                  context: context,
                                  fontFamily: carlito,
                                  textColor: Colors.red),
                              5.heightBox,

                              // Container(
                              //   padding: EdgeInsets.all(5.0),
                              //   width: context.screenWidth,
                              //   decoration: BoxDecoration(
                              //       border: Border.all(
                              //           color: primaryColor,
                              //           style: BorderStyle.solid),
                              //       color: primaryColor),
                              //   child: txt18Size(
                              //       title:
                              //           "${controller.labelTextDetail['vehicle_section_heading'] ?? "Step 1 of 2 - Your vehicle information"}",
                              //       context: context,
                              //       textColor: Colors.white),
                              // ),

                              5.heightBox,
                              Row(
                                children: [
                                  txt20Size(
                                      title:
                                          "${controller.labelTextDetail['make_label'] ?? "Make"}",
                                      fontFamily: regular,
                                      context: context),
                                  txt18Size(
                                      title: '*',
                                      context: context,
                                      fontFamily: bold,
                                      textColor: Colors.red)
                                ],
                              ),
                              5.heightBox,
                              fieldsWidget(
                                maxLength: 30,
                                textController:
                                    controller.makeTextEditingController,
                                onChanged: (value) {
                                  if (controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] == "make") !=
                                      null) {
                                    controller.errors.remove(controller.errors
                                        .firstWhereOrNull((element) =>
                                            element['title'] == "make"));
                                  }
                                },
                                fieldType: "text",
                                readonly: false,
                                fontFamily: regular,
                                fontSize: 18.0,
                                placeHolder:
                                    "${controller.labelTextDetail['make_placeholder'] ?? "Example: Honda, Toyota"}",
                                isError: controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] == "make") !=
                                    null,
                              ),
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "make") !=
                                  null) ...[
                                // toolTip(
                                //     tip: controller.errors.firstWhereOrNull(
                                //         (element) => element['title'] == "make"))
                                toolTip(tip: "Make is required", type: 'string')
                              ],
                              10.heightBox,
                              Row(
                                children: [
                                  txt20Size(
                                      title:
                                          "${controller.labelTextDetail['model_label'] ?? "Model"}",
                                      fontFamily: regular,
                                      context: context),
                                  txt18Size(
                                      title: '*',
                                      context: context,
                                      fontFamily: bold,
                                      textColor: Colors.red)
                                ],
                              ),
                              5.heightBox,
                              fieldsWidget(
                                maxLength: 30,
                                textController:
                                    controller.modelTextEditingController,
                                onChanged: (value) {
                                  if (controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] == "model") !=
                                      null) {
                                    controller.errors.remove(controller.errors
                                        .firstWhereOrNull((element) =>
                                            element['title'] == "model"));
                                  }
                                },
                                fieldType: "text",
                                readonly: false,
                                fontFamily: regular,
                                fontSize: 18.0,
                                placeHolder:
                                    "${controller.labelTextDetail['model_placeholder'] ?? "Example: Accord, Corolla"}",
                                isError: controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] == "model") !=
                                    null,
                              ),
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "model") !=
                                  null) ...[
                                // toolTip(
                                //     tip: controller.errors.firstWhereOrNull(
                                //         (element) => element['title'] == "model"))
                                toolTip(
                                    tip: "Model is required", type: 'string')
                              ],
                              10.heightBox,
                              Row(
                                children: [
                                  txt20Size(
                                      title:
                                          "${controller.labelTextDetail['license_label'] ?? "License Plate Number"}",
                                      fontFamily: regular,
                                      context: context),
                                  txt18Size(
                                      title: '*',
                                      context: context,
                                      fontFamily: bold,
                                      textColor: Colors.red)
                                ],
                              ),
                              5.heightBox,
                              fieldsWidget(
                                maxLength: 10,
                                textController: controller
                                    .licenseNumberTextEditingController,
                                inputFormatters: [
                                  FilteringTextInputFormatter.allow(
                                      RegExp(r'[a-zA-Z0-9 ]')),
                                ],
                                onChanged: (value) {
                                  if (controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] ==
                                              "license_no") !=
                                      null) {
                                    controller.errors.remove(controller.errors
                                        .firstWhereOrNull((element) =>
                                            element['title'] == "license_no"));
                                  }
                                },
                                fieldType: "text",
                                readonly: false,
                                fontFamily: regular,
                                fontSize: 18.0,
                                isError: controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] ==
                                            "license_no") !=
                                    null,
                              ),
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "license_no") !=
                                  null) ...[
                                // toolTip(
                                //     tip: controller.errors.firstWhereOrNull(
                                //         (element) =>
                                //             element['title'] == "license_no"))
                                toolTip(
                                    tip: "License Plate Number is required",
                                    type: 'string')
                              ],
                              10.heightBox,
                              Row(
                                children: [
                                  txt20Size(
                                      title:
                                          "${controller.labelTextDetail['color_label'] ?? "Color"}",
                                      fontFamily: regular,
                                      context: context),
                                  txt18Size(
                                      title: '*',
                                      context: context,
                                      fontFamily: bold,
                                      textColor: Colors.red)
                                ],
                              ),
                              5.heightBox,
                              fieldsWidget(
                                maxLength: 30,
                                textController:
                                    controller.colorTextEditingController,
                                onChanged: (value) {
                                  if (controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] == "color") !=
                                      null) {
                                    controller.errors.remove(controller.errors
                                        .firstWhereOrNull((element) =>
                                            element['title'] == "color"));
                                  }
                                },
                                fieldType: "text",
                                readonly: false,
                                fontFamily: regular,
                                fontSize: 18.0,
                                isError: controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] == "color") !=
                                    null,
                              ),
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "color") !=
                                  null) ...[
                                // toolTip(
                                //     tip: controller.errors.firstWhereOrNull(
                                //         (element) => element['title'] == "color"))
                                toolTip(
                                    tip: "Color is required", type: 'string')
                              ],
                              10.heightBox,
                              Row(
                                children: [
                                  txt20Size(
                                      title:
                                          "${controller.labelTextDetail['year_label'] ?? "Year"}",
                                      fontFamily: regular,
                                      context: context),
                                  txt18Size(
                                      title: '*',
                                      context: context,
                                      fontFamily: bold,
                                      textColor: Colors.red)
                                ],
                              ),
                              5.heightBox,
                              fieldsWidget(
                                maxLength: 4,
                                textController:
                                    controller.yearTextEditingController,
                                onChanged: (value) {
                                  if (controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] == "year") !=
                                      null) {
                                    controller.errors.remove(controller.errors
                                        .firstWhereOrNull((element) =>
                                            element['title'] == "year"));
                                  }
                                },
                                fieldType: "number",
                                readonly: false,
                                fontFamily: regular,
                                fontSize: 18.0,
                                isError: controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] == "year") !=
                                    null,
                              ),
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "year") !=
                                  null) ...[
                                // toolTip(
                                //     tip: controller.errors.firstWhereOrNull(
                                //         (element) => element['title'] == "year"))
                                toolTip(tip: "Year is required", type: 'string')
                              ],
                              10.heightBox,
                              Row(
                                children: [
                                  txt20Size(
                                      title:
                                          "${controller.labelTextDetail['vehicle_type_label'] ?? "Vehicle type"}",
                                      fontFamily: regular,
                                      context: context),
                                  txt18Size(
                                      title: '*',
                                      context: context,
                                      fontFamily: bold,
                                      textColor: Colors.red)
                                ],
                              ),
                              5.heightBox,
                              DropdownButtonFormField2(
                                isExpanded: true,
                                decoration: InputDecoration(
                                  enabledBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(5.0),
                                    borderSide: BorderSide(
                                      color: controller.errors.firstWhereOrNull(
                                                  (element) =>
                                                      element['title'] ==
                                                      "type") !=
                                              null
                                          ? primaryColor
                                          : Colors.grey.shade400,
                                      style: BorderStyle.solid,
                                      width: 1,
                                    ),
                                  ),
                                  focusedBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(5.0),
                                    borderSide:
                                        const BorderSide(color: primaryColor),
                                  ),
                                  contentPadding: const EdgeInsets.symmetric(
                                      vertical: 0.0, horizontal: 8.0),
                                  fillColor: inputColor,
                                ),
                                value: controller.vehicleType.value,
                                items: [
                                  DropdownMenuItem(
                                    value: "",
                                    child: controller.vehicleType.value == ""
                                        ? Row(
                                            mainAxisAlignment:
                                                MainAxisAlignment.spaceBetween,
                                            children: [
                                              txt18Size(
                                                  title:
                                                      "${controller.labelTextDetail['vehicle_type_placeholder'] ?? "Select vehicle type"}",
                                                  context: context,
                                                  fontFamily: bold),
                                              Icon(Icons.check,
                                                  color: btnPrimaryColor,
                                                  size: 20)
                                            ],
                                          )
                                        : txt18Size(
                                            title:
                                                "${controller.labelTextDetail['vehicle_type_placeholder'] ?? "Select vehicle type"}",
                                            context: context,
                                            fontFamily: bold),
                                  ),
                                  for (var i = 0;
                                      i < controller.vehicleTypeList.length;
                                      i++) ...[
                                    DropdownMenuItem(
                                      value: controller.vehicleTypeList[i]
                                          .toString(),
                                      child: controller.vehicleType.value ==
                                              controller.vehicleTypeList[i]
                                                  .toString()
                                          ? Row(
                                              mainAxisAlignment:
                                                  MainAxisAlignment
                                                      .spaceBetween,
                                              children: [
                                                txt18Size(
                                                    title: controller
                                                        .vehicleTypeLabelList[i],
                                                    context: context,
                                                    fontFamily: bold),
                                                Icon(Icons.check,
                                                    color: btnPrimaryColor,
                                                    size: 20)
                                              ],
                                            )
                                          : txt18Size(
                                              title: controller
                                                  .vehicleTypeLabelList[i],
                                              context: context,
                                              fontFamily: bold),
                                    ),
                                  ],
                                ],
                                onChanged: (data) {
                                  controller.vehicleType.value = data!;
                                  if (controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] == "type") !=
                                      null) {
                                    controller.errors.remove(controller.errors
                                        .firstWhereOrNull((element) =>
                                            element['title'] == "type"));
                                  }
                                },
                                alignment: AlignmentDirectional.topCenter,
                                dropdownStyleData: DropdownStyleData(
                                  maxHeight: context.screenHeight * 0.45,
                                  width: context.screenWidth - 30,
                                  // padding: EdgeInsets.only(bottom: 100),
                                  decoration: BoxDecoration(
                                    border: Border.all(
                                        width: 2, color: primaryColor),
                                    borderRadius: const BorderRadius.only(
                                        bottomLeft: Radius.circular(10.0),
                                        bottomRight: Radius.circular(10.0)),
                                  ),
                                ),
                              ),
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "type") !=
                                  null) ...[
                                // toolTip(
                                //     tip: controller.errors.firstWhereOrNull(
                                //         (element) => element['title'] == "type"))
                                toolTip(tip: "Type is required", type: 'string')
                              ],
                              10.heightBox,
                              Row(
                                children: [
                                  txt20Size(
                                      title:
                                          "${controller.labelTextDetail['fuel_label'] ?? "Fuel"}",
                                      fontFamily: regular,
                                      context: context),
                                  txt18Size(
                                      title: '*',
                                      context: context,
                                      fontFamily: bold,
                                      textColor: Colors.red)
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
                                              if (controller.errors
                                                      .firstWhereOrNull(
                                                          (element) =>
                                                              element[
                                                                  'title'] ==
                                                              "car_type") !=
                                                  null) {
                                                controller.errors.remove(
                                                    controller.errors
                                                        .firstWhereOrNull(
                                                            (element) =>
                                                                element[
                                                                    'title'] ==
                                                                "car_type"));
                                              }
                                            },
                                            isError: controller.errors
                                                    .firstWhereOrNull(
                                                        (element) =>
                                                            element['title'] ==
                                                            "car_type") !=
                                                null),
                                      ),
                                      5.widthBox,
                                      InkWell(
                                        onTap: () async {
                                          if (controller.fuel.value ==
                                              "Electric car") {
                                            await controller
                                                .updateFuelValue("");
                                          } else {
                                            await controller.updateFuelValue(
                                                "Electric car");
                                          }
                                          if (controller.errors
                                                  .firstWhereOrNull((element) =>
                                                      element['title'] ==
                                                      "car_type") !=
                                              null) {
                                            controller.errors.remove(controller
                                                .errors
                                                .firstWhereOrNull((element) =>
                                                    element['title'] ==
                                                    "car_type"));
                                          }
                                        },
                                        child: txt20Size(
                                            title:
                                                "${controller.labelTextDetail['electric_option_label'] ?? "Electric car"}",
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
                                              if (controller.errors
                                                      .firstWhereOrNull(
                                                          (element) =>
                                                              element[
                                                                  'title'] ==
                                                              "car_type") !=
                                                  null) {
                                                controller.errors.remove(
                                                    controller.errors
                                                        .firstWhereOrNull(
                                                            (element) =>
                                                                element[
                                                                    'title'] ==
                                                                "car_type"));
                                              }
                                            },
                                            isError: controller.errors
                                                    .firstWhereOrNull(
                                                        (element) =>
                                                            element['title'] ==
                                                            "car_type") !=
                                                null),
                                      ),
                                      5.widthBox,
                                      InkWell(
                                        onTap: () async {
                                          if (controller.fuel.value ==
                                              "Hybrid car") {
                                            await controller
                                                .updateFuelValue("");
                                          } else {
                                            await controller
                                                .updateFuelValue("Hybrid car");
                                          }
                                          if (controller.errors
                                                  .firstWhereOrNull((element) =>
                                                      element['title'] ==
                                                      "car_type") !=
                                              null) {
                                            controller.errors.remove(controller
                                                .errors
                                                .firstWhereOrNull((element) =>
                                                    element['title'] ==
                                                    "car_type"));
                                          }
                                        },
                                        child: txt20Size(
                                            title:
                                                "${controller.labelTextDetail['hybrid_option_label'] ?? "Hybrid car"}",
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
                                            value:
                                                controller.fuel.value == "Gas"
                                                    ? true
                                                    : false,
                                            onChanged: (value) async {
                                              await controller.updateFuelValue(
                                                  value == true ? "Gas" : "");
                                              if (controller.errors
                                                      .firstWhereOrNull(
                                                          (element) =>
                                                              element[
                                                                  'title'] ==
                                                              "car_type") !=
                                                  null) {
                                                controller.errors.remove(
                                                    controller.errors
                                                        .firstWhereOrNull(
                                                            (element) =>
                                                                element[
                                                                    'title'] ==
                                                                "car_type"));
                                              }
                                            },
                                            isError: controller.errors
                                                    .firstWhereOrNull(
                                                        (element) =>
                                                            element['title'] ==
                                                            "car_type") !=
                                                null),
                                      ),
                                      5.widthBox,
                                      InkWell(
                                        onTap: () async {
                                          if (controller.fuel.value == "Gas") {
                                            await controller
                                                .updateFuelValue("");
                                          } else {
                                            await controller
                                                .updateFuelValue("Gas");
                                          }
                                          if (controller.errors
                                                  .firstWhereOrNull((element) =>
                                                      element['title'] ==
                                                      "car_type") !=
                                              null) {
                                            controller.errors.remove(controller
                                                .errors
                                                .firstWhereOrNull((element) =>
                                                    element['title'] ==
                                                    "car_type"));
                                          }
                                        },
                                        child: txt20Size(
                                            title:
                                                "${controller.labelTextDetail['gas_option_label'] ?? "Gas"}",
                                            context: context,
                                            fontFamily: regular),
                                      )
                                    ],
                                  )
                                ],
                              ),
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "car_type") !=
                                  null) ...[
                                // toolTip(
                                //     tip: controller.errors.firstWhereOrNull(
                                //         (element) =>
                                //             element['title'] == "car_type"))
                                toolTip(
                                    tip: "Power Source is required",
                                    type: 'string')
                              ],
                              30.heightBox,
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['photo_label'] ?? "Vehicle Photo"}",
                                  fontFamily: regular,
                                  context: context),
                              10.heightBox,
                              imageUploadWidget(
                                  context: context,
                                  onTap: () async {
                                    controller.imageType.value = 1;
                                    await imageUploadBottomSheet(
                                        controller, context);
                                  },
                                  title:
                                      "${controller.labelTextDetail['photo_label'] ?? "Car photo"}.",
                                  title1: "",
                                  title2:
                                      "(JPG, PNG, JPEG, and GIF. 10MB max.)",
                                  imageFile: controller.carImageName.value == ""
                                      ? null
                                      : controller.carImagePath.value,
                                  screenWidth: context.screenWidth),
                              15.heightBox,

                              // Vehicle Step Buttons
                              20.heightBox,
                              Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                crossAxisAlignment: CrossAxisAlignment.center,
                                children: [
                                  // Expanded(
                                  //     child: elevatedButtonWidget(
                                  //   textWidget: txt28Size(
                                  //       title:
                                  //           "${controller.labelTextDetail['skip_vehicle_info'] ?? "Skip Vehicle"}",
                                  //       textColor: Colors.white,
                                  //       context: context,
                                  //       fontFamily: regular),
                                  //   onPressed: () async {
                                  //     // controller.switchToDriverStep();
                                  //     await controller.setStageThree(
                                  //       false, true, false,
                                  //       // isVehicleStep: true
                                  //     );
                                  //   },
                                  //   btnColor: primaryColor,
                                  // )),
                                  // 2.widthBox,
                                  // Expanded(
                                  //     child: elevatedButtonWidget(
                                  //   textWidget: txt28Size(
                                  //       title:
                                  //           "${controller.labelTextDetail['next_button_label'] ?? "Add Vehicle"}",
                                  //       textColor: Colors.white,
                                  //       context: context,
                                  //       fontFamily: regular),
                                  //   onPressed: () async {
                                  //     await controller.setStageThree(
                                  //       false, false, false,
                                  //       // isVehicleStep: true
                                  //     );
                                  //   },
                                  // )),

                                  // Add Vehicle Button
                                ],
                              ),
                              Row(
                                children: [
                                  Expanded(
                                    child: elevatedButtonWidget(
                                      enabled:
                                          controller.isVehicleFormValid.value,
                                      textWidget: txt22Size(
                                          title:
                                              "${controller.labelTextDetail['next_button_label'] ?? "Add Vehicle"}",
                                          textColor: Colors.white,
                                          context: context,
                                          fontFamily: regular),
                                      onPressed: () async {
                                        // Validate vehicle fields first
                                        if (controller
                                            .validateVehicleFields()) {
                                          controller.isVehicleSkipped.value =
                                              false;
                                          Get.toNamed('/stage_four');
                                        }
                                      },
                                    ),
                                  ),
                                ],
                              ),
                              10.heightBox,
                              Row(
                                children: [
                                  Expanded(
                                      child: elevatedButtonWidget(
                                    textWidget: txt22Size(
                                        title:
                                            "${controller.labelTextDetail['skip_vehicle_info'] ?? "Skip Vehicle"}",
                                        textColor: Colors.white,
                                        context: context,
                                        fontFamily: bold),
                                    onPressed: () async {
                                      controller.isVehicleSkipped.value = true;
                                      Get.toNamed('/stage_four');
                                    },
                                    btnColor: primaryColor,
                                  )),
                                ],
                              )
                          ],
                        ),
                      )

                      // SingleChildScrollView(
                      //   child: Column(
                      //     crossAxisAlignment: CrossAxisAlignment.start,
                      //     children: [
                      //       Align(
                      //         alignment: Alignment.topRight,
                      //         child: elevatedButtonWidget(
                      //             textWidget: txt16Size(
                      //                 title:
                      //                     "${controller.labelTextDetail['logout_button_label'] ?? "Logout"}",
                      //                 fontFamily: regular,
                      //                 textColor: Colors.white,
                      //                 context: context),
                      //             onPressed: () async {
                      //               await controller.serviceController
                      //                   .logoutUser();
                      //             },
                      //             context: context,
                      //             btnColor: primaryColor,
                      //             btnRadius: 5.0),
                      //       ),
                      //       10.heightBox,

                      //       Center(
                      //           child: txt25Size(
                      //               title:
                      //                   "${controller.labelTextDetail['main_heading'] ?? "Step 3 of 4 - Vehicle Information"}",
                      //               context: context)),
                      //       5.heightBox,
                      //       txt16Size(
                      //           title:
                      //               "${controller.labelTextDetail['sub_heading'] ?? "Don't forget your driver's license below"}",
                      //           context: context,
                      //           textColor: Colors.red),
                      //       5.heightBox,
                      //       txt16Size(
                      //           title:
                      //               "${controller.labelTextDetail['main_label'] ?? "If you are signing up as a driver, please note that to be eligible to post Pink Rides and Extra-Care Rides, you must state your vehicle details on every ride, and must upload a valid driver's license"}",
                      //           context: context),
                      //       5.heightBox,
                      //       txt16Size(
                      //           title:
                      //               "${controller.labelTextDetail['sub_main_label'] ?? "If you intend to use ProximaRide as a passenger only, then this point is not applicable to you. You may 'Skip' it"}",
                      //           context: context),
                      //       5.heightBox,
                      //       txt16Size(
                      //           title:
                      //               "* ${controller.labelTextDetail['required_label'] ?? ""}",
                      //           context: context,
                      //           textColor: Colors.red),
                      //       10.heightBox,
                      //       Container(
                      //         padding: EdgeInsets.all(5.0),
                      //         width: context.screenWidth,
                      //         decoration: BoxDecoration(
                      //             border: Border.all(
                      //                 color: primaryColor,
                      //                 style: BorderStyle.solid),
                      //             color: primaryColor),
                      //         child: txt18Size(
                      //             title:
                      //                 "${controller.labelTextDetail['vehicle_section_heading'] ?? "Step 1 of 2 - Your vehicle information"}",
                      //             context: context,
                      //             textColor: Colors.white),
                      //       ),
                      //       5.heightBox,
                      //       Row(
                      //         children: [
                      //           txt20Size(
                      //               title:
                      //                   "${controller.labelTextDetail['make_label'] ?? "Make"}",
                      //               fontFamily: regular,
                      //               context: context),
                      //           txt20Size(
                      //               title: '*',
                      //               context: context,
                      //               fontFamily: bold,
                      //               textColor: Colors.red)
                      //         ],
                      //       ),
                      //       5.heightBox,
                      //       fieldsWidget(
                      //         maxLength: 30,
                      //         textController:
                      //             controller.makeTextEditingController,
                      //         onChanged: (value) {
                      //           if (controller.errors.firstWhereOrNull(
                      //                   (element) =>
                      //                       element['title'] == "make") !=
                      //               null) {
                      //             controller.errors.remove(controller.errors
                      //                 .firstWhereOrNull((element) =>
                      //                     element['title'] == "make"));
                      //           }
                      //         },
                      //         fieldType: "text",
                      //         readonly: false,
                      //         fontFamily: regular,
                      //         fontSize: 18.0,
                      //         placeHolder:
                      //             "${controller.labelTextDetail['make_placeholder'] ?? "Example: Honda, Toyota"}",
                      //         isError: controller.errors.firstWhereOrNull(
                      //                 (element) => element['title'] == "make") !=
                      //             null,
                      //       ),
                      //       if (controller.errors.firstWhereOrNull(
                      //               (element) => element['title'] == "make") !=
                      //           null) ...[
                      //         toolTip(
                      //             tip: controller.errors.firstWhereOrNull(
                      //                 (element) => element['title'] == "make"))
                      //       ],
                      //       10.heightBox,
                      //       Row(
                      //         children: [
                      //           txt20Size(
                      //               title:
                      //                   "${controller.labelTextDetail['model_label'] ?? "Model"}",
                      //               fontFamily: regular,
                      //               context: context),
                      //           txt20Size(
                      //               title: '*',
                      //               context: context,
                      //               fontFamily: bold,
                      //               textColor: Colors.red)
                      //         ],
                      //       ),
                      //       5.heightBox,
                      //       fieldsWidget(
                      //         maxLength: 30,
                      //         textController:
                      //             controller.modelTextEditingController,
                      //         onChanged: (value) {
                      //           if (controller.errors.firstWhereOrNull(
                      //                   (element) =>
                      //                       element['title'] == "model") !=
                      //               null) {
                      //             controller.errors.remove(controller.errors
                      //                 .firstWhereOrNull((element) =>
                      //                     element['title'] == "model"));
                      //           }
                      //         },
                      //         fieldType: "text",
                      //         readonly: false,
                      //         fontFamily: regular,
                      //         fontSize: 18.0,
                      //         placeHolder:
                      //             "${controller.labelTextDetail['model_placeholder'] ?? "Example: Accord, Corolla"}",
                      //         isError: controller.errors.firstWhereOrNull(
                      //                 (element) => element['title'] == "model") !=
                      //             null,
                      //       ),
                      //       if (controller.errors.firstWhereOrNull(
                      //               (element) => element['title'] == "model") !=
                      //           null) ...[
                      //         toolTip(
                      //             tip: controller.errors.firstWhereOrNull(
                      //                 (element) => element['title'] == "model"))
                      //       ],
                      //       10.heightBox,
                      //       Row(
                      //         children: [
                      //           txt20Size(
                      //               title:
                      //                   "${controller.labelTextDetail['license_label'] ?? "License plate number"}",
                      //               fontFamily: regular,
                      //               context: context),
                      //           txt20Size(
                      //               title: '*',
                      //               context: context,
                      //               fontFamily: bold,
                      //               textColor: Colors.red)
                      //         ],
                      //       ),
                      //       5.heightBox,
                      //       fieldsWidget(
                      //         maxLength: 30,
                      //         textController:
                      //             controller.licenseNumberTextEditingController,
                      //         onChanged: (value) {
                      //           if (controller.errors.firstWhereOrNull(
                      //                   (element) =>
                      //                       element['title'] == "license_no") !=
                      //               null) {
                      //             controller.errors.remove(controller.errors
                      //                 .firstWhereOrNull((element) =>
                      //                     element['title'] == "license_no"));
                      //           }
                      //         },
                      //         fieldType: "text",
                      //         readonly: false,
                      //         fontFamily: regular,
                      //         fontSize: 18.0,
                      //         isError: controller.errors.firstWhereOrNull(
                      //                 (element) =>
                      //                     element['title'] == "license_no") !=
                      //             null,
                      //       ),
                      //       if (controller.errors.firstWhereOrNull((element) =>
                      //               element['title'] == "license_no") !=
                      //           null) ...[
                      //         toolTip(
                      //             tip: controller.errors.firstWhereOrNull(
                      //                 (element) =>
                      //                     element['title'] == "license_no"))
                      //       ],
                      //       10.heightBox,
                      //       Row(
                      //         children: [
                      //           txt20Size(
                      //               title:
                      //                   "${controller.labelTextDetail['color_label'] ?? "Color"}",
                      //               fontFamily: regular,
                      //               context: context),
                      //           txt20Size(
                      //               title: '*',
                      //               context: context,
                      //               fontFamily: bold,
                      //               textColor: Colors.red)
                      //         ],
                      //       ),
                      //       5.heightBox,
                      //       fieldsWidget(
                      //         maxLength: 30,
                      //         textController:
                      //             controller.colorTextEditingController,
                      //         onChanged: (value) {
                      //           if (controller.errors.firstWhereOrNull(
                      //                   (element) =>
                      //                       element['title'] == "color") !=
                      //               null) {
                      //             controller.errors.remove(controller.errors
                      //                 .firstWhereOrNull((element) =>
                      //                     element['title'] == "color"));
                      //           }
                      //         },
                      //         fieldType: "text",
                      //         readonly: false,
                      //         fontFamily: regular,
                      //         fontSize: 18.0,
                      //         isError: controller.errors.firstWhereOrNull(
                      //                 (element) => element['title'] == "color") !=
                      //             null,
                      //       ),
                      //       if (controller.errors.firstWhereOrNull(
                      //               (element) => element['title'] == "color") !=
                      //           null) ...[
                      //         toolTip(
                      //             tip: controller.errors.firstWhereOrNull(
                      //                 (element) => element['title'] == "color"))
                      //       ],
                      //       10.heightBox,
                      //       Row(
                      //         children: [
                      //           txt20Size(
                      //               title:
                      //                   "${controller.labelTextDetail['year_label'] ?? "Year"}",
                      //               fontFamily: regular,
                      //               context: context),
                      //           txt20Size(
                      //               title: '*',
                      //               context: context,
                      //               fontFamily: bold,
                      //               textColor: Colors.red)
                      //         ],
                      //       ),
                      //       5.heightBox,
                      //       fieldsWidget(
                      //         maxLength: 4,
                      //         textController:
                      //             controller.yearTextEditingController,
                      //         onChanged: (value) {
                      //           if (controller.errors.firstWhereOrNull(
                      //                   (element) =>
                      //                       element['title'] == "year") !=
                      //               null) {
                      //             controller.errors.remove(controller.errors
                      //                 .firstWhereOrNull((element) =>
                      //                     element['title'] == "year"));
                      //           }
                      //         },
                      //         fieldType: "number",
                      //         readonly: false,
                      //         fontFamily: regular,
                      //         fontSize: 18.0,
                      //         isError: controller.errors.firstWhereOrNull(
                      //                 (element) => element['title'] == "year") !=
                      //             null,
                      //       ),
                      //       if (controller.errors.firstWhereOrNull(
                      //               (element) => element['title'] == "year") !=
                      //           null) ...[
                      //         toolTip(
                      //             tip: controller.errors.firstWhereOrNull(
                      //                 (element) => element['title'] == "year"))
                      //       ],
                      //       10.heightBox,
                      //       Row(
                      //         children: [
                      //           txt20Size(
                      //               title:
                      //                   "${controller.labelTextDetail['vehicle_type_label'] ?? "Vehicle type"}",
                      //               fontFamily: regular,
                      //               context: context),
                      //           txt20Size(
                      //               title: '*',
                      //               context: context,
                      //               fontFamily: bold,
                      //               textColor: Colors.red)
                      //         ],
                      //       ),
                      //       5.heightBox,
                      //       DropdownButtonFormField2(
                      //         isExpanded: true,
                      //         decoration: InputDecoration(
                      //           enabledBorder: OutlineInputBorder(
                      //             borderRadius: BorderRadius.circular(5.0),
                      //             borderSide: BorderSide(
                      //               color: controller.errors.firstWhereOrNull(
                      //                           (element) =>
                      //                               element['title'] == "type") !=
                      //                       null
                      //                   ? primaryColor
                      //                   : Colors.grey.shade400,
                      //               style: BorderStyle.solid,
                      //               width: 1,
                      //             ),
                      //           ),
                      //           focusedBorder: OutlineInputBorder(
                      //             borderRadius: BorderRadius.circular(5.0),
                      //             borderSide:
                      //                 const BorderSide(color: primaryColor),
                      //           ),
                      //           contentPadding: const EdgeInsets.symmetric(
                      //               vertical: 0.0, horizontal: 8.0),
                      //           fillColor: inputColor,
                      //         ),
                      //         value: controller.vehicleType.value,
                      //         items: [
                      //           DropdownMenuItem(
                      //             value: "",
                      //             child: controller.vehicleType.value == ""
                      //                 ? Row(
                      //                     mainAxisAlignment:
                      //                         MainAxisAlignment.spaceBetween,
                      //                     children: [
                      //                       txt18Size(
                      //                           title:
                      //                               "${controller.labelTextDetail['vehicle_type_placeholder'] ?? "Select vehicle type"}",
                      //                           context: context,
                      //                           fontFamily: bold),
                      //                       Icon(Icons.check,
                      //                           color: btnPrimaryColor, size: 20)
                      //                     ],
                      //                   )
                      //                 : txt18Size(
                      //                     title:
                      //                         "${controller.labelTextDetail['vehicle_type_placeholder'] ?? "Select vehicle type"}",
                      //                     context: context,
                      //                     fontFamily: bold),
                      //           ),
                      //           for (var i = 0;
                      //               i < controller.vehicleTypeList.length;
                      //               i++) ...[
                      //             DropdownMenuItem(
                      //               value:
                      //                   controller.vehicleTypeList[i].toString(),
                      //               child: controller.vehicleType.value ==
                      //                       controller.vehicleTypeList[i]
                      //                           .toString()
                      //                   ? Row(
                      //                       mainAxisAlignment:
                      //                           MainAxisAlignment.spaceBetween,
                      //                       children: [
                      //                         txt18Size(
                      //                             title: controller
                      //                                 .vehicleTypeLabelList[i],
                      //                             context: context,
                      //                             fontFamily: bold),
                      //                         Icon(Icons.check,
                      //                             color: btnPrimaryColor,
                      //                             size: 20)
                      //                       ],
                      //                     )
                      //                   : txt18Size(
                      //                       title: controller
                      //                           .vehicleTypeLabelList[i],
                      //                       context: context,
                      //                       fontFamily: bold),
                      //             ),
                      //           ],
                      //         ],
                      //         onChanged: (data) {
                      //           controller.vehicleType.value = data!;
                      //           if (controller.errors.firstWhereOrNull(
                      //                   (element) =>
                      //                       element['title'] == "type") !=
                      //               null) {
                      //             controller.errors.remove(controller.errors
                      //                 .firstWhereOrNull((element) =>
                      //                     element['title'] == "type"));
                      //           }
                      //         },
                      //         alignment: AlignmentDirectional.topCenter,
                      //         dropdownStyleData: DropdownStyleData(
                      //           maxHeight: context.screenHeight * 0.45,
                      //           width: context.screenWidth - 30,
                      //           // padding: EdgeInsets.only(bottom: 100),
                      //           decoration: BoxDecoration(
                      //             border:
                      //                 Border.all(width: 2, color: primaryColor),
                      //             borderRadius: const BorderRadius.only(
                      //                 bottomLeft: Radius.circular(10.0),
                      //                 bottomRight: Radius.circular(10.0)),
                      //           ),
                      //         ),
                      //       ),
                      //       if (controller.errors.firstWhereOrNull(
                      //               (element) => element['title'] == "type") !=
                      //           null) ...[
                      //         toolTip(
                      //             tip: controller.errors.firstWhereOrNull(
                      //                 (element) => element['title'] == "type"))
                      //       ],
                      //       10.heightBox,
                      //       Row(
                      //         children: [
                      //           txt20Size(
                      //               title:
                      //                   "${controller.labelTextDetail['fuel_label'] ?? "Fuel"}",
                      //               fontFamily: regular,
                      //               context: context),
                      //           txt20Size(
                      //               title: '*',
                      //               context: context,
                      //               fontFamily: bold,
                      //               textColor: Colors.red)
                      //         ],
                      //       ),
                      //       5.heightBox,
                      //       Row(
                      //         mainAxisAlignment: MainAxisAlignment.start,
                      //         children: [
                      //           Row(
                      //             children: [
                      //               SizedBox(
                      //                 width: getValueForScreenType<double>(
                      //                   context: context,
                      //                   mobile: 25.0,
                      //                   tablet: 25.0,
                      //                 ),
                      //                 height: getValueForScreenType<double>(
                      //                   context: context,
                      //                   mobile: 25.0,
                      //                   tablet: 25.0,
                      //                 ),
                      //                 child: checkBoxWidget(
                      //                     value: controller.fuel.value ==
                      //                             "Electric car"
                      //                         ? true
                      //                         : false,
                      //                     onChanged: (value) async {
                      //                       await controller.updateFuelValue(
                      //                           value == true
                      //                               ? "Electric car"
                      //                               : "");
                      //                       if (controller.errors
                      //                               .firstWhereOrNull((element) =>
                      //                                   element['title'] ==
                      //                                   "car_type") !=
                      //                           null) {
                      //                         controller.errors.remove(controller
                      //                             .errors
                      //                             .firstWhereOrNull((element) =>
                      //                                 element['title'] ==
                      //                                 "car_type"));
                      //                       }
                      //                     },
                      //                     isError: controller.errors
                      //                             .firstWhereOrNull((element) =>
                      //                                 element['title'] ==
                      //                                 "car_type") !=
                      //                         null),
                      //               ),
                      //               5.widthBox,
                      //               InkWell(
                      //                 onTap: () async {
                      //                   if (controller.fuel.value ==
                      //                       "Electric car") {
                      //                     await controller.updateFuelValue("");
                      //                   } else {
                      //                     await controller
                      //                         .updateFuelValue("Electric car");
                      //                   }
                      //                   if (controller.errors.firstWhereOrNull(
                      //                           (element) =>
                      //                               element['title'] ==
                      //                               "car_type") !=
                      //                       null) {
                      //                     controller.errors.remove(controller
                      //                         .errors
                      //                         .firstWhereOrNull((element) =>
                      //                             element['title'] ==
                      //                             "car_type"));
                      //                   }
                      //                 },
                      //                 child: txt16Size(
                      //                     title:
                      //                         "${controller.labelTextDetail['electric_option_label'] ?? "Electric car"}",
                      //                     context: context,
                      //                     fontFamily: regular),
                      //               )
                      //             ],
                      //           ),
                      //           20.widthBox,
                      //           Row(
                      //             children: [
                      //               SizedBox(
                      //                 width: getValueForScreenType<double>(
                      //                   context: context,
                      //                   mobile: 25.0,
                      //                   tablet: 25.0,
                      //                 ),
                      //                 height: getValueForScreenType<double>(
                      //                   context: context,
                      //                   mobile: 25.0,
                      //                   tablet: 25.0,
                      //                 ),
                      //                 child: checkBoxWidget(
                      //                     value: controller.fuel.value ==
                      //                             "Hybrid car"
                      //                         ? true
                      //                         : false,
                      //                     onChanged: (value) async {
                      //                       await controller.updateFuelValue(
                      //                           value == true
                      //                               ? "Hybrid car"
                      //                               : "");
                      //                       if (controller.errors
                      //                               .firstWhereOrNull((element) =>
                      //                                   element['title'] ==
                      //                                   "car_type") !=
                      //                           null) {
                      //                         controller.errors.remove(controller
                      //                             .errors
                      //                             .firstWhereOrNull((element) =>
                      //                                 element['title'] ==
                      //                                 "car_type"));
                      //                       }
                      //                     },
                      //                     isError: controller.errors
                      //                             .firstWhereOrNull((element) =>
                      //                                 element['title'] ==
                      //                                 "car_type") !=
                      //                         null),
                      //               ),
                      //               5.widthBox,
                      //               InkWell(
                      //                 onTap: () async {
                      //                   if (controller.fuel.value ==
                      //                       "Hybrid car") {
                      //                     await controller.updateFuelValue("");
                      //                   } else {
                      //                     await controller
                      //                         .updateFuelValue("Hybrid car");
                      //                   }
                      //                   if (controller.errors.firstWhereOrNull(
                      //                           (element) =>
                      //                               element['title'] ==
                      //                               "car_type") !=
                      //                       null) {
                      //                     controller.errors.remove(controller
                      //                         .errors
                      //                         .firstWhereOrNull((element) =>
                      //                             element['title'] ==
                      //                             "car_type"));
                      //                   }
                      //                 },
                      //                 child: txt16Size(
                      //                     title:
                      //                         "${controller.labelTextDetail['hybrid_option_label'] ?? "Hybrid car"}",
                      //                     context: context,
                      //                     fontFamily: regular),
                      //               )
                      //             ],
                      //           ),
                      //           20.widthBox,
                      //           Row(
                      //             children: [
                      //               SizedBox(
                      //                 width: getValueForScreenType<double>(
                      //                   context: context,
                      //                   mobile: 25.0,
                      //                   tablet: 25.0,
                      //                 ),
                      //                 height: getValueForScreenType<double>(
                      //                   context: context,
                      //                   mobile: 25.0,
                      //                   tablet: 25.0,
                      //                 ),
                      //                 child: checkBoxWidget(
                      //                     value: controller.fuel.value == "Gas"
                      //                         ? true
                      //                         : false,
                      //                     onChanged: (value) async {
                      //                       await controller.updateFuelValue(
                      //                           value == true ? "Gas" : "");
                      //                       if (controller.errors
                      //                               .firstWhereOrNull((element) =>
                      //                                   element['title'] ==
                      //                                   "car_type") !=
                      //                           null) {
                      //                         controller.errors.remove(controller
                      //                             .errors
                      //                             .firstWhereOrNull((element) =>
                      //                                 element['title'] ==
                      //                                 "car_type"));
                      //                       }
                      //                     },
                      //                     isError: controller.errors
                      //                             .firstWhereOrNull((element) =>
                      //                                 element['title'] ==
                      //                                 "car_type") !=
                      //                         null),
                      //               ),
                      //               5.widthBox,
                      //               InkWell(
                      //                 onTap: () async {
                      //                   if (controller.fuel.value == "Gas") {
                      //                     await controller.updateFuelValue("");
                      //                   } else {
                      //                     await controller.updateFuelValue("Gas");
                      //                   }
                      //                   if (controller.errors.firstWhereOrNull(
                      //                           (element) =>
                      //                               element['title'] ==
                      //                               "car_type") !=
                      //                       null) {
                      //                     controller.errors.remove(controller
                      //                         .errors
                      //                         .firstWhereOrNull((element) =>
                      //                             element['title'] ==
                      //                             "car_type"));
                      //                   }
                      //                 },
                      //                 child: txt16Size(
                      //                     title:
                      //                         "${controller.labelTextDetail['gas_option_label'] ?? "Gas"}",
                      //                     context: context,
                      //                     fontFamily: regular),
                      //               )
                      //             ],
                      //           )
                      //         ],
                      //       ),
                      //       if (controller.errors.firstWhereOrNull((element) =>
                      //               element['title'] == "car_type") !=
                      //           null) ...[
                      //         toolTip(
                      //             tip: controller.errors.firstWhereOrNull(
                      //                 (element) =>
                      //                     element['title'] == "car_type"))
                      //       ],
                      //       10.heightBox,
                      //       txt20Size(
                      //           title:
                      //               "${controller.labelTextDetail['photo_label'] ?? "Car photo"}",
                      //           fontFamily: regular,
                      //           context: context),
                      //       10.heightBox,
                      //       imageUploadWidget(
                      //           context: context,
                      //           onTap: () async {
                      //             controller.imageType.value = 1;
                      //             await imageUploadBottomSheet(
                      //                 controller, context);
                      //           },
                      //           title:
                      //               "${controller.labelTextDetail['photo_label'] ?? "Car photo"}.",
                      //           title1:
                      //               "${controller.labelTextDetail['mobile_photo_choose_file_label'] ?? "Choose file"}",
                      //           title2:
                      //               "${controller.labelTextDetail['photo_detail_label'] ?? "(Only JPG, PNG, JPEG and GIF are allowed. Max. 10MB)"}",
                      //           imageFile: controller.carImageName.value == ""
                      //               ? null
                      //               : controller.carImagePath.value,
                      //           screenWidth: context.screenWidth),
                      //       15.heightBox,
                      //       // Container(
                      //       //   padding: EdgeInsets.all(5.0),
                      //       //   width: context.screenWidth,
                      //       //   decoration: BoxDecoration(
                      //       //       border: Border.all(
                      //       //           color: primaryColor,
                      //       //           style: BorderStyle.solid),
                      //       //       color: primaryColor),
                      //       //   child: txt18Size(
                      //       //       title:
                      //       //           "${controller.labelTextDetail['liecense_section_heading'] ?? "Step 2 of 2 - Your driver's license"}",
                      //       //       context: context,
                      //       //       textColor: Colors.white),
                      //       // ),
                      //       // 10.heightBox,
                      //       // txt20Size(
                      //       //     title:
                      //       //         "${controller.labelTextDetail['driver_license_label'] ?? "Driver license"}",
                      //       //     fontFamily: regular,
                      //       //     context: context),
                      //       // 5.heightBox,
                      //       // txt18Size(
                      //       //     title:
                      //       //         "${controller.labelTextDetail['driver_license_sub_label'] ?? 'To be eligible to post "Pink rides" and "Extra-care rides", you must upload your driver\'s license'}",
                      //       //     fontFamily: regular,
                      //       //     textColor: textColor,
                      //       //     context: context),
                      //       // 10.heightBox,
                      //       // imageUploadWidget(
                      //       //     context: context,
                      //       //     onTap: () async {
                      //       //       controller.imageType.value = 2;
                      //       //       await imageUploadBottomSheet(
                      //       //           controller, context);
                      //       //     },
                      //       //     title:
                      //       //         "${controller.labelTextDetail['driver_license_label'] ?? "Driver license"}.",
                      //       //     title1:
                      //       //         "${controller.labelTextDetail['mobile_driver_choose_file_label'] ?? "Choose file"}",
                      //       //     title2:
                      //       //         "${controller.labelTextDetail['photo_detail_label'] ?? "(Only JPG, PNG, JPEG and GIF are allowed. Max. 10MB)"}",
                      //       //     imageFile:
                      //       //         controller.driverLicenseName.value == ""
                      //       //             ? null
                      //       //             : controller.driverLicensePath.value,
                      //       //     screenWidth: context.screenWidth),
                      //       // if (controller.errors.firstWhereOrNull((element) =>
                      //       //         element['title'] == "driver_license") !=
                      //       //     null) ...[
                      //       //   toolTip(
                      //       //       tip: controller.errors.firstWhereOrNull(
                      //       //           (element) =>
                      //       //               element['title'] == "driver_license"))
                      //       // ],
                      //       // 20.heightBox,
                      //       Row(
                      //         mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      //         crossAxisAlignment: CrossAxisAlignment.center,
                      //         children: [
                      //           Expanded(
                      //               child: elevatedButtonWidget(
                      //             textWidget: txt28Size(
                      //                 title:
                      //                     "${controller.labelTextDetail['skip_vehicle_info'] ?? "Skip Vehicle"}",
                      //                 textColor: Colors.white,
                      //                 context: context,
                      //                 fontFamily: regular),
                      //             onPressed: () async {
                      //               await controller.setStageThree(
                      //                   false, true, false);
                      //               // controller.addVehicleStageThree(true);
                      //             },
                      //             btnColor: primaryColor,
                      //           )),
                      //           2.widthBox,
                      //           // Expanded(
                      //           //     child: elevatedButtonWidget(
                      //           //   textWidget: txt28Size(
                      //           //       title:
                      //           //           "${controller.labelTextDetail['skip_license'] ?? "license"}",
                      //           //       textColor: Colors.white,
                      //           //       context: context,
                      //           //       fontFamily: regular),
                      //           //   onPressed: () async {
                      //           //     await controller.setStageThree(
                      //           //         false, false, true);
                      //           //   },
                      //           // )),
                      //         ],
                      //       ),
                      //       10.heightBox,
                      //       Row(
                      //         mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      //         crossAxisAlignment: CrossAxisAlignment.center,
                      //         children: [
                      //           // Expanded(
                      //           //     child: elevatedButtonWidget(
                      //           //   textWidget: txt28Size(
                      //           //       title:
                      //           //           "${controller.labelTextDetail['skip_button_label'] ?? "Skip"}",
                      //           //       textColor: Colors.white,
                      //           //       context: context,
                      //           //       fontFamily: regular),
                      //           //   onPressed: () async {
                      //           //     await controller.setStageThree(
                      //           //         true, false, false);
                      //           //     // controller.addVehicleStageThree(true);
                      //           //   },
                      //           //   btnColor: primaryColor,
                      //           // )),
                      //           2.widthBox,
                      //           Expanded(
                      //               child: elevatedButtonWidget(
                      //             textWidget: txt28Size(
                      //                 title:
                      //                     "${controller.labelTextDetail['next_button_label'] ?? "Add vehicle"}",
                      //                 textColor: Colors.white,
                      //                 context: context,
                      //                 fontFamily: regular),
                      //             onPressed: () async {
                      //               await controller.setStageThree(
                      //                   false, false, false);
                      //             },
                      //           )),
                      //         ],
                      //       )
                      //     ],
                      //   ),
                      // )

                      ),
                  if (controller.isOverlayLoading.value == true) ...[
                    overlayWidget(context)
                  ]
                ],
              ),
            );
          }
        }),
      );
  }
}


