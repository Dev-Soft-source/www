import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/stages/StageController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
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

class StageOne extends GetView<StageController> {
  const StageOne({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(StageController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => stepAppBarWidget(
              context: context, serviceController: controller.serviceController, langId: controller.serviceController.langId.value, langIcon: controller.serviceController.langIcon.value, screeWidth: context.screenWidth, page: "step1")),
          // leading: const BackButton(color: Colors.white),
        ),
        body: Obx(() {
          if(controller.isLoading.value == true){
            return Center(child: progressCircularWidget(context));
          }else{
            return Stack(
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
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        Align(
                          alignment: Alignment.topRight,
                          child: elevatedButtonWidget(
                              textWidget: txt16Size(
                                  title: "${controller.labelTextDetail['logout_button_label'] ?? "Logout"}",
                                  fontFamily: regular,
                                  textColor: Colors.white,
                                  context: context),
                              onPressed: () async {
                                await controller.serviceController.logoutUser();
                              },
                              context: context,
                              btnColor: primaryColor,
                              btnRadius: 5.0),
                        ),
                        Center(child: txt25Size(title: "${controller.labelTextDetail['main_heading'] ?? "Step 1 of 4 - Personal Information"}", context: context)),
                        5.heightBox,
                        txt20Size(title: "${controller.labelTextDetail['required_label'] ?? '* Indicates required field'}",context: context,fontFamily: bold,textColor: Colors.red),
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['first_name_label'] ?? "First name"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                            textController: controller.firstNameTextEditingController,
                            fieldType: "text",
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            placeHolder: "",
                            onChanged: (value){
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "first") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "first"));
                              }
                            }
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "first") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "first"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['last_name_label'] ?? "Last name"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                            textController: controller.lastNameTextEditingController,
                            fieldType: "text",
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            placeHolder: "",
                            onChanged: (value){
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "last") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "last"));
                              }
                            }
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "last") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "last"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['gender_label'] ?? "Gender"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
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
                                  title: "${controller.labelTextDetail['male_option_label'] ?? "Male"}",
                                  value: controller.gender.value == "male"
                                      ? true
                                      : false,
                                  onChanged: (value) async {
                                    await controller.updateGenderValue(
                                        value == true ? "male" : "");
                                    if(controller.errors.firstWhereOrNull((element) => element['title'] == "gender") != null)
                                    {
                                      controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "gender"));
                                    }

                                  }),
                              const Divider(),
                              genderWidget(
                                  context: context,
                                  title: "${controller.labelTextDetail['female_option_label'] ?? "Female"}",
                                  value: controller.gender.value == "female"
                                      ? true
                                      : false,
                                  onChanged: (value) async {
                                    await controller.updateGenderValue(
                                        value == true ? "female" : "");
                                    if(controller.errors.firstWhereOrNull((element) => element['title'] == "gender") != null)
                                    {
                                      controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "gender"));
                                    }
                                  }),
                              const Divider(),
                              genderWidget(
                                  context: context,
                                  title: "${controller.labelTextDetail['prefer_option_label'] ?? "Prefer not to say"}",
                                  value: controller.gender.value ==
                                      "prefer not to say"
                                      ? true
                                      : false,
                                  onChanged: (value) async {
                                    await controller.updateGenderValue(
                                        value == true
                                            ? "prefer not to say"
                                            : "");
                                    if(controller.errors.firstWhereOrNull((element) => element['title'] == "gender") != null)
                                    {
                                      controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "gender"));
                                    }
                                  }),
                              10.heightBox,
                            ],
                          ),
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "gender") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "gender"))
                        ],

                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['dob_label'] ?? "Date of birth"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red),
                          ],
                        ),
                        5.heightBox,
                        Row(
                          children: [
                            Expanded(
                              child: dateFieldWidget(
                                textController: controller.dobTextEditingController,
                                fontFamily: regular,
                                fontSize: 16.0,
                                onTap: () async {
                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "dob") != null)
                                  {
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "dob"));
                                  }
                                  DateTime? dobDate = await controller.serviceController
                                      .datePicker(context);
                                  if (dobDate == null) return;
                                  DateFormat dateFormat = DateFormat('MMMM dd, y');
                                  controller.dobTextEditingController.text =
                                      dateFormat.format(dobDate);

                                },
                                prefixIcon: preFixIconWidget(
                                    context: context, imagePath: calenderImage),
                                isError: controller.errors
                                    .where((error) => error == "date")
                                    .isNotEmpty,
                              ),
                            ),
                          ],
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "dob") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "dob"))
                        ],




                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['country_label'] ?? "Country"}",
                                fontFamily: regular,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
                          ],
                        ),
                        6.heightBox,
                        dropDownWidget(
                            title: "Country",
                            screenWidth: context.screenWidth,
                            context: context,
                            onTap: () async {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "country") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "country"));
                              }
                              Get.toNamed("/country/country");
                            },
                            placeHolder: controller.countryName.value == ""
                                ? ""
                                : controller.countryName.value),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "country") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "country"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['state_label'] ?? "Province / State"}",
                                fontFamily: regular,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
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
                                  'eList' : ['Please select country first']
                                };
                                controller.errors.add(err);
                              } else {
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "state") != null)
                                {
                                  controller.errors.removeWhere((element) => element['title'] == "state");
                                }
                                Get.toNamed("/state/state/${controller.countryId.value}");
                              }
                            },
                            placeHolder: controller.stateName.value == ""
                                ? ""
                                : controller.stateName.value),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "state") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "state"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['city_label'] ?? "City"}",
                                fontFamily: regular,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
                          ],
                        ),
                        6.heightBox,
                        dropDownWidget(
                            title: "City",
                            screenWidth: context.screenWidth,
                            context: context,
                            onTap: () async {
                              if (controller.stateId.value == 0) {
                                var err = {
                                  'title': "city",
                                  'eList' : ['Please select province/state first']
                                };
                                controller.errors.add(err);
                              } else {
                                if(controller.errors.firstWhereOrNull((element) => element['title'] == "city") != null)
                                {
                                  controller.errors.removeWhere((element) => element['title'] == "city");
                                }
                                Get.toNamed("/city/city/${controller.stateId.value}/0/no");
                              }
                            },
                            placeHolder: controller.cityName.value == ""
                                ? ""
                                : controller.cityName.value),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "city") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "city"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['zip_code_label'] ?? "Postal code/ Zip code"}",
                                fontFamily: regular,
                                textColor: textColor,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
                          ],
                        ),
                        5.heightBox,
                        fieldsWidget(
                            textController: controller.postalCodeTextEditingController,
                            fieldType: "text",
                            maxLength: 7,
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            placeHolder: "",
                            onChanged: (value){
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "zipcode") != null)
                              {
                                controller.errors.removeWhere((element) => element['title'] == "zipcode");
                              }
                            }
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "zipcode") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "zipcode"))
                        ],
                        10.heightBox,
                        Row(
                          children: [
                            txt20Size(
                                title: "${controller.labelTextDetail['bio_label'] ?? "Mini bio"}",
                                fontFamily: regular,
                                context: context),
                            txt20Size(title: '*',context: context,fontFamily: bold,textColor: Colors.red)
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
                          onChanged: (value) {
                            if(controller.errors.firstWhereOrNull((element) => element['title'] == "mini") != null)
                            {
                              controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "mini"));
                            }
                          },
                        ),
                        if(controller.errors.firstWhereOrNull((element) => element['title'] == "mini") != null) ...[
                          toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "mini"))
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
                        textWidget: txt28Size(
                            title: "${controller.labelTextDetail['button_label'] ?? "Next"}",
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
            );
          }
        })
    );
  }
}
