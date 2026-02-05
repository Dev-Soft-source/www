import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/stages/StageController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/step_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';
import '../widgets/date_field_widget.dart';
import '../widgets/drop_down_widget.dart';
import '../widgets/gender_widget.dart';
import '../widgets/prefix_icon_widget.dart';
import '../widgets/tool_tip.dart';

class StageOne extends StatefulWidget {
  const StageOne({super.key});

  @override
  State<StageOne> createState() => _StageOneState();
}

class _StageOneState extends State<StageOne> {
  late final ScrollController _scrollController;
  late final FocusNode _postalCodeFocusNode;
  late final FocusNode _miniBioFocusNode;
  late final StageController controller;

  @override
  void initState() {
    super.initState();

    // Initialize controller
    controller = Get.put(StageController());

    // Initialize controllers and focus nodes
    _scrollController = ScrollController();
    _postalCodeFocusNode = FocusNode();
    _miniBioFocusNode = FocusNode();

    // Add listeners to scroll when fields receive focus
    _postalCodeFocusNode.addListener(_onPostalCodeFocusChange);
    _miniBioFocusNode.addListener(_onMiniBioFocusChange);
  }

  void _onPostalCodeFocusChange() {
    if (_postalCodeFocusNode.hasFocus) {
      Future.delayed(const Duration(milliseconds: 300), () {
        if (_scrollController.hasClients) {
          _scrollController.animateTo(
            _scrollController.position.maxScrollExtent * 0.7,
            duration: const Duration(milliseconds: 300),
            curve: Curves.easeInOut,
          );
        }
      });
    }
  }

  void _onMiniBioFocusChange() {
    if (_miniBioFocusNode.hasFocus) {
      Future.delayed(const Duration(milliseconds: 300), () {
        if (_scrollController.hasClients) {
          _scrollController.animateTo(
            _scrollController.position.maxScrollExtent,
            duration: const Duration(milliseconds: 300),
            curve: Curves.easeInOut,
          );
        }
      });
    }
  }

  @override
  void dispose() {
    // Remove listeners
    _postalCodeFocusNode.removeListener(_onPostalCodeFocusChange);
    _miniBioFocusNode.removeListener(_onMiniBioFocusChange);

    // Dispose controllers and focus nodes
    _scrollController.dispose();
    _postalCodeFocusNode.dispose();
    _miniBioFocusNode.dispose();

    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => stepAppBarWidget(
              context: context,
              serviceController: controller.serviceController,
              langId: controller.serviceController.langId.value,
              langIcon: controller.serviceController.langIcon.value,
              screeWidth: context.screenWidth,
              page: "step1")),
          // leading: const BackButton(color: Colors.white),
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
                      controller: _scrollController,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: [
                          Center(
                              child: txt25Size(
                                  title:
                                      // "${controller.labelTextDetail['main_heading'] ??
                                      "Step 1 of 5 - Personal Information",
                                  context: context)),
                          20.heightBox,
                          txt18Size(
                              title:
                                  "${controller.labelTextDetail['required_label'] ?? '* Indicates required fields'}",
                              context: context,
                              fontFamily: bold,
                              textColor: Colors.red),
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['first_name_label'] ?? "First name"}",
                                  fontFamily: regular,
                                  textColor: textColor,
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
                              textController:
                                  controller.firstNameTextEditingController,
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              placeHolder: "",
                              onChanged: (value) {
                                if (controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] == "first") !=
                                    null) {
                                  controller.errors.remove(controller.errors
                                      .firstWhereOrNull((element) =>
                                          element['title'] == "first"));
                                }
                              }),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "first") !=
                              null) ...[
                            // toolTip(
                            //     tip: controller.errors.firstWhereOrNull(
                            //         (element) => element['title'] == "first"))
                            toolTip(
                                tip: "First name is required", type: 'string')
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['last_name_label'] ?? "Last name"}",
                                  fontFamily: regular,
                                  textColor: textColor,
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
                              textController:
                                  controller.lastNameTextEditingController,
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              placeHolder: "",
                              onChanged: (value) {
                                if (controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] == "last") !=
                                    null) {
                                  controller.errors.remove(controller.errors
                                      .firstWhereOrNull((element) =>
                                          element['title'] == "last"));
                                }
                              }),
                          // if (controller.errors.firstWhereOrNull(
                          //         (element) => element['title'] == "last") !=
                          //     null) ...[
                          //   toolTip(
                          //       tip: controller.errors.firstWhereOrNull(
                          //           (element) => element['title'] == "last"))
                          // ],
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "last") !=
                              null) ...[
                            toolTip(
                                tip: "Last name is required", type: 'string')
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['gender_label'] ?? "Gender"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              txt18Size(
                                  title: '*',
                                  context: context,
                                  fontFamily: bold,
                                  textColor: Colors.red)
                            ],
                          ),
                          5.heightBox,
                          Container(
                            decoration: BoxDecoration(
                                color: inputColor,
                                borderRadius: BorderRadius.circular(5.0)),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                10.heightBox,
                                genderWidget(
                                    context: context,
                                    title:
                                        "${controller.labelTextDetail['male_option_label'] ?? "Male"}",
                                    value: controller.gender.value == "male"
                                        ? true
                                        : false,
                                    onChanged: (value) async {
                                      await controller.updateGenderValue(
                                          value == true ? "male" : "");
                                      if (controller.errors.firstWhereOrNull(
                                              (element) =>
                                                  element['title'] ==
                                                  "gender") !=
                                          null) {
                                        controller.errors.remove(controller
                                            .errors
                                            .firstWhereOrNull((element) =>
                                                element['title'] == "gender"));
                                      }
                                    }),
                                const Divider(),
                                genderWidget(
                                    context: context,
                                    title:
                                        "${controller.labelTextDetail['female_option_label'] ?? "Female"}",
                                    value: controller.gender.value == "female"
                                        ? true
                                        : false,
                                    onChanged: (value) async {
                                      await controller.updateGenderValue(
                                          value == true ? "female" : "");
                                      if (controller.errors.firstWhereOrNull(
                                              (element) =>
                                                  element['title'] ==
                                                  "gender") !=
                                          null) {
                                        controller.errors.remove(controller
                                            .errors
                                            .firstWhereOrNull((element) =>
                                                element['title'] == "gender"));
                                      }
                                    }),
                                const Divider(),
                                genderWidget(
                                    context: context,
                                    title:
                                        "${controller.labelTextDetail['prefer_option_label'] ?? "Prefer not to say"}",
                                    value: controller.gender.value ==
                                            "prefer not to say"
                                        ? true
                                        : false,
                                    onChanged: (value) async {
                                      await controller.updateGenderValue(
                                          value == true
                                              ? "prefer not to say"
                                              : "");
                                      if (controller.errors.firstWhereOrNull(
                                              (element) =>
                                                  element['title'] ==
                                                  "gender") !=
                                          null) {
                                        controller.errors.remove(controller
                                            .errors
                                            .firstWhereOrNull((element) =>
                                                element['title'] == "gender"));
                                      }
                                    }),
                                10.heightBox,
                              ],
                            ),
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "gender") !=
                              null) ...[
                            // toolTip(
                            //     tip: controller.errors.firstWhereOrNull(
                            //         (element) => element['title'] == "gender"))
                            toolTip(tip: "Gender is required", type: 'string')
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['dob_label'] ?? "Date of birth"}",
                                  fontFamily: regular,
                                  textColor: textColor,
                                  context: context),
                              txt18Size(
                                  title: '*',
                                  context: context,
                                  fontFamily: bold,
                                  textColor: Colors.red),
                            ],
                          ),
                          5.heightBox,
                          Row(
                            children: [
                              Expanded(
                                child: dateFieldWidget(
                                  textController:
                                      controller.dobTextEditingController,
                                  fontFamily: regular,
                                  fontSize: 18.0,
                                  onTap: () async {
                                    if (controller.errors.firstWhereOrNull(
                                            (element) =>
                                                element['title'] == "dob") !=
                                        null) {
                                      controller.errors.remove(controller.errors
                                          .firstWhereOrNull((element) =>
                                              element['title'] == "dob"));
                                    }
                                    DateTime? dobDate = await controller
                                        .serviceController
                                        .datePicker(context);
                                    if (dobDate == null) return;
                                    DateFormat dateFormat =
                                        DateFormat('MMMM dd, y');
                                    controller.dobTextEditingController.text =
                                        dateFormat.format(dobDate);
                                  },
                                  prefixIcon: preFixIconWidget(
                                      context: context,
                                      imagePath: calenderImage),
                                  isError: controller.errors
                                      .where((error) => error == "date")
                                      .isNotEmpty,
                                ),
                              ),
                            ],
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "dob") !=
                              null) ...[
                            // toolTip(
                            //     tip: controller.errors.firstWhereOrNull(
                            //         (element) => element['title'] == "dob"))
                            toolTip(
                                tip: "Date of Birth is required",
                                type: 'string')
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['country_label'] ?? "Country"}",
                                  fontFamily: regular,
                                  context: context),
                              txt18Size(
                                  title: '*',
                                  context: context,
                                  fontFamily: bold,
                                  textColor: Colors.red)
                            ],
                          ),
                          6.heightBox,
                          dropDownWidget(
                              title: "Country",
                              screenWidth: context.screenWidth,
                              context: context,
                              onTap: () async {
                                if (controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] == "country") !=
                                    null) {
                                  controller.errors.remove(controller.errors
                                      .firstWhereOrNull((element) =>
                                          element['title'] == "country"));
                                }
                                Get.toNamed("/country/country");
                              },
                              placeHolder: controller.countryName.value == ""
                                  ? ""
                                  : controller.countryName.value),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "country") !=
                              null) ...[
                            // toolTip(
                            //     tip: controller.errors.firstWhereOrNull(
                            //         (element) => element['title'] == "country"))
                            toolTip(tip: "Country is required", type: 'string')
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['state_label'] ?? "Province / State"}",
                                  fontFamily: regular,
                                  context: context),
                              txt18Size(
                                  title: '*',
                                  context: context,
                                  fontFamily: bold,
                                  textColor: Colors.red)
                            ],
                          ),
                          6.heightBox,
                          dropDownWidget(
                              title: "State",
                              screenWidth: context.screenWidth,
                              context: context,
                              onTap: () async {
                                if (controller.countryId.value == 0) {
                                  var err = {
                                    'title': "state",
                                    'eList': ['Please select country first']
                                  };
                                  controller.errors.add(err);
                                } else {
                                  if (controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] == "state") !=
                                      null) {
                                    controller.errors.removeWhere((element) =>
                                        element['title'] == "state");
                                  }
                                  Get.toNamed(
                                      "/state/state/${controller.countryId.value}");
                                }
                              },
                              placeHolder: controller.stateName.value == ""
                                  ? ""
                                  : controller.stateName.value),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "state") !=
                              null) ...[
                            // toolTip(
                            //     tip: controller.errors.firstWhereOrNull(
                            //         (element) => element['title'] == "state"))
                            toolTip(
                                tip: "Province / State is required",
                                type: 'string')
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['city_label'] ?? "City"}",
                                  fontFamily: regular,
                                  context: context),
                              txt18Size(
                                  title: '*',
                                  context: context,
                                  fontFamily: bold,
                                  textColor: Colors.red)
                            ],
                          ),
                          6.heightBox,
                          dropDownWidget(
                              title: "City",
                              screenWidth: context.screenWidth,
                              context: context,
                              onTap: () async {
                                if (controller.stateId.value == 0 &&
                                    controller.stateName.value !=
                                        'Not Applicable') {
                                  var err = {
                                    'title': "city",
                                    'eList': [
                                      'Please select province/state first'
                                    ]
                                  };
                                  controller.errors.add(err);
                                } else {
                                  if (controller.errors.firstWhereOrNull(
                                          (element) =>
                                              element['title'] == "city") !=
                                      null) {
                                    controller.errors.removeWhere((element) =>
                                        element['title'] == "city");
                                  }
                                  Get.toNamed(
                                      "/city/city/${controller.stateId.value}/0/no");
                                }
                              },
                              placeHolder: controller.cityName.value == ""
                                  ? ""
                                  : controller.cityName.value),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "city") !=
                              null) ...[
                            // toolTip(
                            //     tip: controller.errors.firstWhereOrNull(
                            //         (element) => element['title'] == "city"))
                            toolTip(tip: "City is required", type: 'string')
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['zip_code_label'] ?? "Postal code/ Zip code"}",
                                  fontFamily: regular,
                                  textColor: textColor,
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
                              textController:
                                  controller.postalCodeTextEditingController,
                              fieldType: "text",
                              maxLength: 7,
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                              placeHolder: "",
                              focusNode: _postalCodeFocusNode,
                              onChanged: (value) {
                                if (controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] == "zipcode") !=
                                    null) {
                                  controller.errors.removeWhere((element) =>
                                      element['title'] == "zipcode");
                                }
                              }),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "zipcode") !=
                              null) ...[
                            // toolTip(
                            //     tip: controller.errors.firstWhereOrNull(
                            //         (element) => element['title'] == "zipcode"))
                            toolTip(tip: "ZIP Code is required", type: 'string')
                          ],
                          10.heightBox,
                          Row(
                            children: [
                              txt20Size(
                                  title:
                                      "${controller.labelTextDetail['bio_label'] ?? "Mini-bio"}",
                                  fontFamily: regular,
                                  context: context),
                              txt18Size(
                                  title: '*',
                                  context: context,
                                  fontFamily: bold,
                                  textColor: Colors.red)
                            ],
                          ),
                          6.heightBox,
                          textAreaWidget(
                            textController:
                                controller.miniBioTextEditingController,
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            maxLines: 4,
                            focusNode: _miniBioFocusNode,
                            onChanged: (value) {
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "mini") !=
                                  null) {
                                controller.errors.remove(controller.errors
                                    .firstWhereOrNull((element) =>
                                        element['title'] == "mini"));
                              }
                            },
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "mini") !=
                              null) ...[
                            // toolTip(
                            //     tip: controller.errors.firstWhereOrNull(
                            //         (element) => element['title'] == "mini"))
                            toolTip(tip: "Mini Bio is required", type: 'string')
                          ],
                          100.heightBox,
                        ],
                      ),
                    ),
                  ),
                  Align(
                    alignment: Alignment.bottomCenter,
                    child: Container(
                      color: Colors.grey.shade100,
                      padding: EdgeInsets.all(getValueForScreenType<double>(
                        context: context,
                        mobile: 15.0,
                        tablet: 15.0,
                      )),
                      width: context.screenWidth,
                      child: elevatedButtonWidget(
                          enabled: controller.isStageOneValid.value,
                          textWidget: txt22Size(
                              title:
                                  "${controller.labelTextDetail['button_label'] ?? "Next"}",
                              fontFamily: regular,
                              textColor: Colors.white,
                              context: context),
                          onPressed: () async {
                            controller.setStageOne();
                          },
                          context: context,
                          btnRadius: 5.0),
                    ),
                  ),
                  if (controller.isOverlayLoading.value == true) ...[
                    overlayWidget(context)
                  ]
                ],
              ),
            );
          }
        }));
  }
}
