import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/search_ride/SearchRideController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/date_field_widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/post_ride_again_card_widget.dart';
import 'package:proximaride_app/pages/widgets/prefix_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/tool_tip.dart';

class SearchRidePage extends StatelessWidget {
  const SearchRidePage({super.key});
  @override
  Widget build(BuildContext context) {
    final controller = Get.isRegistered<SearchRideController>()
        ? Get.find<SearchRideController>()
        : Get.put(SearchRideController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() {
          String title = "";

          if (controller.labelTextDetail['main_heading'] != null &&
              controller.labelTextDetail['main_heading']
                  .toString()
                  .trim()
                  .isNotEmpty) {
            title = controller.labelTextDetail['main_heading'].toString();
          }

          return secondAppBarWidget(
            title: title,
            context: context,
          );
        }),
        leading: IconButton(
          onPressed: controller.handleBackNavigation,
          icon: const Icon(Icons.arrow_back, color: Colors.white),
        ),
      ),
      body: SafeArea(
        child: Obx(() {
          if (controller.errorStateManager.isLoading.value) {
            return Center(child: progressCircularWidget(context));
          }

          if (controller.errorStateManager.hasError.value) {
            return ErrorStateWidget(
              message: controller.errorStateManager.errorMessage.value,
              errorType: controller.errorStateManager.errorType.value,
              onRetry: () {
                if (controller.errorStateManager.onRetry.value != null) {
                  controller.errorStateManager.onRetry.value!();
                }
              },
            );
          }

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
                                title:
                                    "${controller.labelTextDetail['card_section_from_label'] ?? "From"}",
                                fontFamily: regular,
                                context: context),
                            txt18Size(
                                title: "*",
                                fontFamily: regular,
                                context: context,
                                textColor: Colors.red),
                          ],
                        ),
                        3.heightBox,
                        fieldsWidget(
                          onTap: () {
                            Get.toNamed("/city/origin/0/0/no");
                          },
                          textController: controller.fromTextEditingController,
                          fieldType: "text",
                          readonly: true,
                          fontFamily: regular,
                          fontSize: 18.0,
                          prefixIcon: preFixIconWidget(
                              context: context, imagePath: fromLocationImage),
                          placeHolder:
                              "${controller.labelTextDetail['search_section_from_placeholder'] ?? "Origin"}",
                          hintTextColor: placeHolderColor,
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
                                title:
                                    "${controller.labelTextDetail['card_section_to_label'] ?? "To"}",
                                fontFamily: regular,
                                context: context),
                            txt18Size(
                                title: "*",
                                fontFamily: regular,
                                context: context,
                                textColor: Colors.red),
                            const Spacer(),
                            InkWell(
                                onTap: () {
                                  controller.swapLocations();
                                },
                                child: Image.asset(locationSwapIcon,
                                    width: 35, height: 35)),
                          ],
                        ),
                        3.heightBox,
                        fieldsWidget(
                          onTap: () {
                            Get.toNamed("/city/destination/0/0/no");
                          },
                          textController: controller.toTextEditingController,
                          fieldType: "text",
                          readonly: true,
                          fontFamily: regular,
                          fontSize: 18.0,
                          prefixIcon: preFixIconWidget(
                              context: context, imagePath: toLocationImage),
                          placeHolder:
                              "${controller.labelTextDetail['search_section_to_placeholder'] ?? "Destination"}",
                          hintTextColor: placeHolderColor,
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
                            title:
                                "${controller.labelTextDetail['search_section_keyword_label'] ?? "Keyword/ Keyphrase (optional)"}",
                            fontFamily: regular,
                            context: context),
                        3.heightBox,
                        fieldsWidget(
                          placeHolder:
                              "${controller.labelTextDetail['search_section_keyword_placeholder'] ?? 'Landmark, metro station, shopping center…etc'}",
                          textController:
                              controller.keywordTextEditingController,
                          fieldType: "text",
                          readonly: false,
                          fontFamily: regular,
                          fontSize: 18.0,
                        ),
                        10.heightBox,
                        txt20Size(
                            title:
                                "${controller.labelTextDetail['search_section_date_placeholder'] ?? "Date (optional)"}",
                            fontFamily: regular,
                            context: context),
                        3.heightBox,
                        dateFieldWidget(
                          textController: controller.dateTextEditingController,
                          fontFamily: regular,
                          fontSize: 18.0,
                          onTap: () async {
                            DateTime? dobDate = await controller
                                .serviceController
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
                            Expanded(
                                child: Row(
                              mainAxisAlignment: MainAxisAlignment.start,
                              crossAxisAlignment: CrossAxisAlignment.start,
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
                                    value: controller.pinkRideCheck.value,
                                    onChanged: (data) {
                                      controller.pinkRideCheck.value = data!;
                                    },
                                  ),
                                ),
                                5.widthBox,
                                InkWell(
                                  onTap: () {
                                    controller.pinkRideCheck.value =
                                        controller.pinkRideCheck.value == true
                                            ? false
                                            : true;
                                  },
                                  child: txt20Size(
                                      title:
                                          "${controller.labelTextDetail['search_section_pink_ride_label'] ?? "Pink rides"}",
                                      fontFamily: bold,
                                      context: context,
                                      textColor: const Color.fromARGB(
                                          255, 180, 20, 9)),
                                ),
                              ],
                            )),
                            Expanded(
                                child: Row(
                              mainAxisAlignment: MainAxisAlignment.start,
                              crossAxisAlignment: CrossAxisAlignment.start,
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
                                    value: controller.extraCareCheck.value,
                                    onChanged: (data) {
                                      controller.extraCareCheck.value = data!;
                                    },
                                  ),
                                ),
                                5.widthBox,
                                InkWell(
                                  onTap: () {
                                    controller.extraCareCheck.value =
                                        controller.extraCareCheck.value == true
                                            ? false
                                            : true;
                                  },
                                  child: txt20Size(
                                      title:
                                          "${controller.labelTextDetail['search_section_extra_care_label'] ?? "Extra care rides"}",
                                      fontFamily: bold,
                                      context: context,
                                      textColor: const Color.fromARGB(
                                          255, 39, 114, 42)),
                                )
                              ],
                            ))
                          ],
                        ),
                        10.heightBox,
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
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
                                value: controller.hideFullRidesCheck.value,
                                onChanged: (data) {
                                  controller.hideFullRidesCheck.value = data!;
                                },
                              ),
                            ),
                            5.widthBox,
                            Expanded(
                              child: InkWell(
                                onTap: () {
                                  controller.hideFullRidesCheck.value =
                                      !controller.hideFullRidesCheck.value;
                                },
                                child: txt20Size(
                                  title:
                                      "${controller.labelTextDetail['hide_full_ride_text'] ?? 'Hide Full Rides'}",
                                  fontFamily: bold,
                                  context: context,
                                ),
                              ),
                            ),
                          ],
                        ),
                        20.heightBox,
                        SizedBox(
                          height: 50,
                          width: context.screenWidth,
                          child: elevatedButtonWidget(
                              textWidget: txt22Size(
                                  title:
                                      "${controller.labelTextDetail['search_section_button_label'] ?? "Search"}",
                                  context: context,
                                  textColor: Colors.white),
                              context: context,
                              onPressed: () async {
                                await controller.getSearchRide(1);
                              }),
                        ),
                        20.heightBox,
                        Container(
                          width: context.screenWidth,
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(10),
                            boxShadow: const [
                              BoxShadow(
                                color: Color(0x14000000),
                                blurRadius: 8,
                                offset: Offset(0, 2),
                              ),
                            ],
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Container(
                                width: context.screenWidth,
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 14, vertical: 12),
                                decoration: BoxDecoration(
                                  color: primaryColor,
                                  borderRadius: const BorderRadius.only(
                                    topLeft: Radius.circular(10),
                                    topRight: Radius.circular(10),
                                  ),
                                ),
                                child: txt22Size(
                                    title:
                                        "${controller.labelTextDetail['search_section_recent_searches'] ?? "Recent searches (${controller.recentSearchList.length > 2 ? 2 : controller.recentSearchList.length})"}",
                                    textColor: Colors.white,
                                    context: context,
                                    fontFamily: regular),
                              ),
                              Padding(
                                padding: const EdgeInsets.all(10),
                                child: ListView.separated(
                                    itemCount:
                                        controller.recentSearchList.length > 2
                                            ? 2
                                            : controller
                                                .recentSearchList.length,
                                    shrinkWrap: true,
                                    physics:
                                        const NeverScrollableScrollPhysics(),
                                    itemBuilder: (context, index) {
                                      return postRideAgainCardWidget(
                                          context: context,
                                          screenWidth: context.screenWidth,
                                          fromText: controller
                                              .recentSearchList[index]['from'],
                                          toText: controller
                                              .recentSearchList[index]['to'],
                                          depatureAt: "",
                                          onTap: () async {
                                            controller.applyRecentSearch(
                                                controller
                                                    .recentSearchList[index]);
                                            await controller.getSearchRide(1);
                                          },
                                          cardBgColor: index % 2 == 0
                                              ? Colors.white
                                              : Colors.grey.shade200,
                                          fromLabel:
                                              "${controller.labelTextDetail['card_section_from_label'] ?? "From"}",
                                          toLabel:
                                              "${controller.labelTextDetail['card_section_to_label'] ?? "To"}");
                                    },
                                    separatorBuilder: (context, index) {
                                      return const SizedBox(height: 8);
                                    }),
                              ),
                            ],
                          ),
                        )
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
      ),
    );
  }
}
