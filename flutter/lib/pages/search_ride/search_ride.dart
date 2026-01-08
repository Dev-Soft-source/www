import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/search_ride/SearchRideController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/date_field_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/post_ride_again_card_widget.dart';
import 'package:proximaride_app/pages/widgets/prefix_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class SearchRidePage extends GetView<SearchRideController> {
  const SearchRidePage({super.key});
  @override
  Widget build(BuildContext context) {
    Get.put(SearchRideController());
    return


      Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(context: context, title: "${controller.labelTextDetail['main_heading'] ?? "Search ride"}")),
        leading: const BackButton(color: Colors.white),
      ),
      body: Obx(() {
        if (controller.isLoading.value == true) {
          return Center(child: progressCircularWidget(context));
        } else {
          return Stack(
            children: [
              SingleChildScrollView(
                controller: controller.scrollController,
                child: Container(
                  padding: EdgeInsets.all(getValueForScreenType<double>(
                    context: context,
                    mobile: 15.0,
                    tablet: 15.0,
                  )),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          txt20Size(
                              title: "${controller.labelTextDetail['card_section_from_label'] ?? "From"}", fontFamily: regular, context: context),
                          txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                        ],
                      ),
                      3.heightBox,
                      fieldsWidget(
                        onTap: (){
                          Get.toNamed("/city/origin/0/0/no");
                        },
                        textController: controller.fromTextEditingController,
                        fieldType: "text",
                        readonly: true,
                        fontFamily: regular,
                        fontSize: 18.0,
                        prefixIcon: preFixIconWidget(
                            context: context, imagePath: fromLocationImage),
                        placeHolder: "${controller.labelTextDetail['search_section_from_placeholder'] ?? "Origin"}",
                        hintTextColor: textColor,
                        onChanged: (value) {
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "from") !=
                              null) {
                            controller.errors.remove(controller.errors
                                .firstWhereOrNull(
                                    (element) => element['title'] == "from"));
                          }
                        },
                      ),
                      if (controller.errors.firstWhereOrNull(
                              (element) => element['title'] == "from") !=
                          null) ...[
                        toolTip(
                            tip: controller.errors.firstWhereOrNull(
                                (element) => element['title'] == "from"))
                      ],
                      10.heightBox,
                      Row(
                        children: [
                          txt20Size(
                              title: "${controller.labelTextDetail['card_section_to_label'] ?? "To"}", fontFamily: regular, context: context),
                          txt20Size(title: "*", fontFamily: regular, context: context,textColor: Colors.red),
                          const Spacer(),
                          InkWell(
                            onTap: (){
                              String temp = controller.fromTextEditingController.text;
                              controller.fromTextEditingController.text = controller.toTextEditingController.text;
                              controller.toTextEditingController.text = temp;
                            },
                            child: Image.asset(locationSwapIcon, width: 35, height: 35)),
                        ],
                      ),
                      3.heightBox,
                      fieldsWidget(
                        onTap: (){
                          Get.toNamed("/city/destination/0/0/no");
                        },
                        textController: controller.toTextEditingController,
                        fieldType: "text",
                        readonly: true,
                        fontFamily: regular,
                        fontSize: 18.0,
                        prefixIcon: preFixIconWidget(
                            context: context, imagePath: toLocationImage),
                        placeHolder: "${controller.labelTextDetail['search_section_to_placeholder'] ?? "Destination"}",
                        hintTextColor: textColor,
                        onChanged: (value) {
                          if (controller.errors.firstWhereOrNull(
                                  (element) => element['title'] == "to") !=
                              null) {
                            controller.errors.remove(controller.errors
                                .firstWhereOrNull(
                                    (element) => element['title'] == "to"));
                          }
                        },
                      ),
                      if (controller.errors.firstWhereOrNull(
                              (element) => element['title'] == "to") !=
                          null) ...[
                        toolTip(
                            tip: controller.errors.firstWhereOrNull(
                                (element) => element['title'] == "to"))
                      ],
                      10.heightBox,
                      txt20Size(
                          title: "${controller.labelTextDetail['search_section_keyword_label'] ?? "Keyword/ Keyphrase (optional)"}",
                          fontFamily: regular,
                          context: context),
                      3.heightBox,
                      fieldsWidget(
                        placeHolder:
                        "${controller.labelTextDetail['search_section_keyword_placeholder'] ?? 'Landmark, metro station, shopping center…etc'}",
                        textController: controller.keywordTextEditingController,
                        fieldType: "text",
                        readonly: false,
                        fontFamily: regular,
                        fontSize: 18.0,
                      ),
                      10.heightBox,
                      txt20Size(
                          title: "${controller.labelTextDetail['search_section_date_placeholder'] ?? "Date (optional)"}",
                          fontFamily: regular,
                          context: context),
                      3.heightBox,
                      dateFieldWidget(
                        textController: controller.dateTextEditingController,
                        fontFamily: regular,
                        fontSize: 18.0,
                        onTap: () async {
                          DateTime? dobDate = await controller.serviceController
                              .datePicker(context);
                          if (dobDate == null) return;
                          DateFormat dateFormat = DateFormat.yMMMMd();
                          controller.dateTextEditingController.text =
                              dateFormat.format(dobDate);
                        },
                        prefixIcon: preFixIconWidget(
                            context: context, imagePath: calenderImage),
                      ),
                      10.heightBox,
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Expanded(child: Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              SizedBox(
                                width: 25,
                                height: 25,
                                child: checkBoxWidget(value: controller.pinkRideCheck.value,
                                  onChanged: (data){
                                    controller.pinkRideCheck.value = data!;
                                  },

                                ),
                              ),
                              Expanded(
                                child: InkWell(
                                  onTap: (){
                                    controller.pinkRideCheck.value = controller.pinkRideCheck.value == true ? false : true;
                                  },
                                  child: txt20Size(
                                      title: "${controller.labelTextDetail['search_section_pink_ride_label'] ?? "Pink rides"}",
                                      fontFamily: regular,
                                      context: context
                                  ),
                                ),
                              ),
                            ],
                          )),
                          Expanded(child: Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              SizedBox(
                                width: 25,
                                height: 25,
                                child: checkBoxWidget(value: controller.extraCareCheck.value,
                                  onChanged: (data){
                                    controller.extraCareCheck.value = data!;
                                  },
                                ),
                              ),
                              Expanded(child: InkWell(
                                onTap: (){
                                  controller.extraCareCheck.value = controller.extraCareCheck.value == true ? false : true;
                                },
                                child: txt20Size(
                                    title: "${controller.labelTextDetail['search_section_extra_care_label'] ?? "Extra care rides"}",
                                    fontFamily: regular,
                                    context: context
                                ),
                              ))
                            ],
                          ))
                        ],
                      ),
                      20.heightBox,
                      SizedBox(
                        height: 50,
                        width: context.screenWidth,
                        child: elevatedButtonWidget(
                            textWidget: txt28Size(
                                title: "${controller.labelTextDetail['search_section_button_label'] ?? "Search"}",
                                context: context,
                                textColor: Colors.white),
                            context: context,
                            onPressed: () async {
                              await controller.getSearchRide(1);
                            }),
                      ),
                      20.heightBox,
                      SizedBox(
                        width: context.screenWidth,
                        child: elevatedButtonWidget(
                          textWidget: txt22Size(
                              title: "${controller.labelTextDetail['search_section_recent_searches'] ?? "Recent searches (${controller.recentSearchList.isNotEmpty ? controller.recentSearchList.length : 0})"}",
                              textColor: Colors.white,
                              context: context,
                              fontFamily: regular),
                          context: context,
                          onPressed: () {
                          },
                        ),
                      ),
                      10.heightBox,
                      ListView.separated(
                          itemCount: controller.recentSearchList.length,
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemBuilder: (context, index) {
                            return postRideAgainCardWidget(
                                context: context,
                                screenWidth: context.screenWidth,
                                fromText: controller.recentSearchList[index]
                                    ['from'],
                                toText: controller.recentSearchList[index]
                                    ['to'],
                                onTap: () async {
                                  controller.fromTextEditingController.text =
                                      controller.recentSearchList[index]
                                          ['from'];
                                  controller.toTextEditingController.text =
                                      controller.recentSearchList[index]['to'];

                                  await controller.getSearchRide(1);
                                },
                              cardBgColor: index % 2 == 0 ? Colors.white : Colors.grey.shade200,
                              fromLabel: "${controller.labelTextDetail['card_section_from_label'] ?? "From"}",
                              toLabel: "${controller.labelTextDetail['card_section_to_label'] ?? "To"}"
                            );
                          },
                          separatorBuilder: (context, index) {
                            return const SizedBox();
                          })
                    ],
                  ),
                ),
              ),
              if (controller.isOverlayLoading.value == true) ...[
                overlayWidget(context)
              ]
            ],
          );
        }
      }),
    );
  }
}
