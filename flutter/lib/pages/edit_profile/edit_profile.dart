import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/edit_profile/EditProfileController.dart';
import 'package:proximaride_app/pages/widgets/gender_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/date_field_widget.dart';
import 'package:proximaride_app/pages/widgets/drop_down_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/image_upload_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/image_upload_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';
import '../widgets/network_cache_image_widget.dart';
import '../widgets/tool_tip.dart';

class EditProfilePage extends GetView<EditProfileController> {
  const EditProfilePage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(EditProfileController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['main_heading'] ?? "Edit profile"}", context: context)),
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
                          txt22Size(
                              title:
                              "${controller.labelTextDetail['edit_profile_text'] ?? 'To be eligible for the "Pink rides" and "Extra-care rides", you must complete all fields below'}",
                              fontFamily: regular,
                              textColor: textColor,
                              context: context),
                          10.heightBox,
                          txt20Size(
                              title: "${controller.labelTextDetail['first_name_label'] ?? "First name"}",
                              fontFamily: regular,
                              context: context),
                          6.heightBox,
                          fieldsWidget(
                            textController:
                                controller.firstNameTextEditingController,
                            fieldType: "text",
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            onChanged: (value) {
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "first") !=
                                  null) {
                                controller.errors.remove(controller.errors
                                    .firstWhereOrNull((element) =>
                                        element['title'] == "first"));
                              }
                            },
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "first") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "first"))
                          ],
                          10.heightBox,
                          txt20Size(
                              title: "${controller.labelTextDetail['last_name_label'] ?? "Last name"}",
                              fontFamily: regular,
                              context: context),
                          6.heightBox,
                          fieldsWidget(
                            textController:
                                controller.lastNameTextEditingController,
                            fieldType: "text",
                            readonly: false,
                            fontFamily: regular,
                            fontSize: 18.0,
                            onChanged: (value) {
                              if (controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "last") !=
                                  null) {
                                controller.errors.remove(controller.errors
                                    .firstWhereOrNull((element) =>
                                        element['title'] == "last"));
                              }
                            },
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "last") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "last"))
                          ],
                          10.heightBox,
                          txt20Size(
                              title: "${controller.labelTextDetail['gender_label'] ?? "Gender"}",
                              fontFamily: regular,
                              context: context),
                          6.heightBox,
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
                                    title: "${controller.labelTextDetail['male_label'] ?? "Male"}",
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
                                    title: "${controller.labelTextDetail['female_label'] ?? "Female"}",
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
                                    title: "${controller.labelTextDetail['prefer_no_to_say_label'] ?? "Prefer not to say"}",
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
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "gender"))
                          ],
                          10.heightBox,
                          txt20Size(
                              title: "${controller.labelTextDetail['dob_label'] ?? "Date of birth"}",
                              fontFamily: regular,
                              context: context),
                          6.heightBox,
                          Stack(
                            children: [
                              dateFieldWidget(
                                  textController:
                                  controller.dobTextEditingController,
                                  fontFamily: regular,
                                  fontSize: 18.0,
                                  onTap: () async {
                                    DateTime? dobDate = await controller
                                        .serviceController
                                        .datePicker(context);
                                    if (dobDate == null) return;
                                    DateFormat dateFormat = DateFormat.yMMMMd();
                                    controller.dobTextEditingController.text =
                                        dateFormat.format(dobDate);
                                      if(controller.errors.firstWhereOrNull((element) => element['title'] == "dob") != null)
                                      {
                                        controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "dob"));
                                      }
                                  }),
                            ],
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "dob") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "dob"))
                          ],
                          10.heightBox,
                          txt20Size(
                              title: "${controller.labelTextDetail['country_label'] ?? "Country"}",
                              fontFamily: regular,
                              context: context),
                          6.heightBox,
                          dropDownWidget(
                              title: "${controller.labelTextDetail['country_label'] ?? "Country"}",
                              screenWidth: context.screenWidth,
                              context: context,
                              onTap: () async {
                                Get.toNamed("/country/country");
                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "country") != null)
                                  {
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "country"));
                                  }
                              },
                              placeHolder: controller.countryName.value == ""
                                  ? "${controller.labelTextDetail['country_placeholder'] ?? "Please select country"}"
                                  : controller.countryName.value),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "country") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "country"))
                          ],
                          10.heightBox,
                          txt20Size(
                              title: "${controller.labelTextDetail['state_label'] ?? "Province / State"}",
                              fontFamily: regular,
                              context: context),
                          6.heightBox,
                          dropDownWidget(
                              title: "${controller.labelTextDetail['state_label'] ?? "State"}",
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
                                  Get.toNamed(
                                      "/state/state/${controller.countryId.value}");
                                }
                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "state") != null)
                                  {
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "state"));
                                  }
                              },
                              placeHolder: controller.stateName.value == ""
                                  ? "${controller.labelTextDetail['state_placeholder'] ?? "Please select state"}"
                                  : controller.stateName.value),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "state") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "state"))
                          ],
                          10.heightBox,
                          txt20Size(
                              title: "${controller.labelTextDetail['city_label'] ?? "City"}",
                              fontFamily: regular,
                              context: context),
                          6.heightBox,
                          dropDownWidget(
                              title: "${controller.labelTextDetail['city_label'] ?? "City"}",
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
                                  // await controller.getCities();
                                  Get.toNamed(
                                      "/city/city/${controller.stateId.value}/0/no");
                                }
                                  if(controller.errors.firstWhereOrNull((element) => element['title'] == "city") != null)
                                  {
                                    controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "city"));
                                  }
                              },
                              placeHolder: controller.cityName.value == ""
                                  ? "${controller.labelTextDetail['city_placeholder'] ?? "Please select city"}"
                                  : controller.cityName.value),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "city") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "city"))
                          ],
                          10.heightBox,
                          txt20Size(
                              title:
                              "${controller.labelTextDetail['address_label'] ?? "Street number, street name, apartment number"}",
                              fontFamily: regular,
                              context: context),
                          6.heightBox,
                          fieldsWidget(
                              textController:
                                  controller.addressTextEditingController,
                              fieldType: "text",
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "address") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "address"));
                              }
                            },
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "address") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "address"))
                          ],
                          10.heightBox,
                          txt20Size(
                              title: "${controller.labelTextDetail['zip_label'] ?? "Postal code / Zip code"}",
                              fontFamily: regular,
                              context: context),
                          6.heightBox,
                          fieldsWidget(
                              textController:
                                  controller.postalCodeTextEditingController,
                              fieldType: "text",
                              maxLength: 7,
                              readonly: false,
                              fontFamily: regular,
                              fontSize: 18.0,
                            onChanged: (value) {
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "postal") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "postal"));
                              }
                            },
                          ),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "postal") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "postal"))
                          ],
                          10.heightBox,
                          txt20Size(
                              title: "${controller.labelTextDetail['mini_bio_placeholder'] ?? "Mini bio"}",
                              fontFamily: regular,
                              context: context),
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
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "mini") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                    (element) => element['title'] == "mini"))
                          ],
                          30.heightBox,
                          txt20Size(
                              title: "${controller.labelTextDetail['govt_id_label'] ?? "A government-issued photo ID"}",
                              fontFamily: regular,
                              context: context),
                          5.heightBox,
                          txt16Size(
                              title: "${controller.labelTextDetail['govt_id_text'] ?? "If you intend to post or use our Pink Rides or Extra-Care Rides, you must upload this ID. It can be your driver's license, health card, passport, PR card, Indian Status card"}",
                              fontFamily: regular,
                              context: context),
                          5.heightBox,
                          Stack(children: [
                            imageUploadWidget(
                              context: context,
                              onTap: () async {
                                await imageUploadBottomSheet(
                                    controller, context);
                              },
                              screenWidth: context.screenWidth,
                              title1: "${controller.labelTextDetail['choose_file_placeholder'] ?? "Choose file"}",
                              title2: "${controller.labelTextDetail['image_option_placeholder'] ?? "(Only JPG, PNG, JPEG and GIF are allowed. Max. 10MB)"}",
                              title: "${controller.labelTextDetail['image_placeholder'] ?? "Upload government-issued ID."}",
                              imageFile:
                                  controller.governmentImageName.value == ""
                                      ? null
                                      : controller.governmentImagePath.value,
                            ),
                            if (controller.oldGovernmentIssuedId.value != "" &&
                                controller.governmentImageName.value == "") ...[
                              InkWell(
                                onTap: (){
                                  controller.serviceController.showImage.value = controller.governmentImagePathOriginalOld.value;
                                  Get.toNamed("/show_image");
                                },
                                child: Container(
                                  padding: const EdgeInsets.all(1.0),
                                  child: ClipRRect(
                                    borderRadius: BorderRadius.circular(15.0),
                                    child: networkCacheImageWidget(
                                        "${controller.oldGovernmentIssuedId}",
                                        BoxFit.fill,
                                        context.screenWidth,
                                        320.0),
                                  ),
                                ),
                              ),
                            ],
                          ]),
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "government_issued_id") !=
                              null) ...[
                            toolTip(
                                tip: controller.errors.firstWhereOrNull(
                                        (element) => element['title'] == "government_issued_id"))
                          ],
                          if (controller.oldGovernmentIssuedId.value != "" && controller.governmentImageName.value == "") ...[
                            10.heightBox,
                            Align(
                              alignment: Alignment.topRight,
                              child: elevatedButtonWidget(
                                textWidget: txt22Size(title: "${controller.labelTextDetail['new_image_button_text'] ?? "Upload new image"}",context: context, textColor: Colors.white),
                                onPressed: () async{
                                  await imageUploadBottomSheet(
                                  controller, context);
                                },
                                context: context,
                                btnColor: primaryColor
                              ),
                            )
                          ],
                          20.heightBox,
                          SizedBox(
                            width: context.screenWidth,
                            height: 50,
                            child: elevatedButtonWidget(
                              textWidget: txt28Size(
                                  title: "${controller.labelTextDetail['save_button_text'] ?? "Save"}",
                                  textColor: Colors.white,
                                  context: context,
                                  fontFamily: regular),
                              onPressed: () async {
                                await controller.editUserProfile(context, context.screenHeight);
                              },
                            ),
                          ),
                          // 10.heightBox,
                        ],
                      ),
                    )),
                if (controller.isOverlayLoading.value == true) ...[
                  overlayWidget(context)
                ]
              ],
            );
          }
        }));
  }
}
