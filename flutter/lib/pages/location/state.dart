import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/location/LocationController.dart';
import 'package:proximaride_app/pages/widgets/drop_down_item_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import '../widgets/overlay_widget.dart';

class StatePage extends StatelessWidget {
  const StatePage({super.key});

  @override
  Widget build(BuildContext context) {
    final LocationController controller = Get.isRegistered<LocationController>()
        ? Get.find<LocationController>()
        : Get.put(LocationController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['select_state_label'] ?? "Select state"}",
              context: context)),
          leading: safeBackButton(context),
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
            return Stack(
              children: [
                Container(
                    padding: EdgeInsets.all(getValueForScreenType<double>(
                      context: context,
                      mobile: 10.0,
                      tablet: 10.0,
                    )),
                    child: SingleChildScrollView(
                        child: Column(
                      children: [
                        fieldsWidget(
                            textController:
                                controller.searchTextEditingController,
                            fieldType: "text",
                            autoFocus: true,
                            fontFamily: regular,
                            fontSize: 18.0,
                            readonly: false,
                            placeHolder:
                                "${controller.labelTextDetail['search_state_label'] ?? "Enter Province / State"}",
                            suffix: const Icon(
                              Icons.search,
                              color: textColor,
                            ),
                            onChanged: (value) async {
                              await controller.filterStates(value);
                            }),
                        10.heightBox,
                        controller.searchStates.isNotEmpty
                            ? ListView.separated(
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                itemCount: controller.searchStates.length,
                                // Commented out "Not Applicable" functionality
                                // itemCount: controller.searchStates.length + (controller.searchTextEditingController.text.isEmpty ? 1 : 0),
                                itemBuilder: (context, index) {
                                  // Commented out "Not Applicable" functionality
                                  // final showNotApplicable = controller.searchTextEditingController.text.isEmpty;
                                  // final isNotApplicable = showNotApplicable && index == 0;
                                  // final item = isNotApplicable
                                  //     ? {'id': null, 'name': 'Not Applicable'}
                                  //     : controller.searchStates[showNotApplicable ? index - 1 : index];

                                  final item = controller.searchStates[index];

                                  return dropDownItemWidget(
                                      context: context,
                                      onTap: () {
                                        // Commented out "Not Applicable" functionality
                                        // if (isNotApplicable) {
                                        //   controller.tempController.stateId.value = 0;
                                        //   controller.tempController.stateName.value = "Not Applicable";
                                        //   controller.tempController.cities.clear();
                                        //   controller.tempController.cityId.value = 0;
                                        //   controller.tempController.cityName.value = "Not Applicable";
                                        // } else {
                                        controller.tempController.stateId
                                            .value = item['id'];
                                        controller.tempController.stateName
                                            .value = item['name'];
                                        controller.tempController.cities
                                            .clear();
                                        controller.tempController.cityId.value =
                                            0;
                                        controller
                                            .tempController.cityName.value = "";
                                        // }
                                        Get.back();
                                      },
                                      name: "${item['name']}",
                                      isSelected: controller
                                              .tempController.stateId.value ==
                                          item['id']);
                                  // Commented out "Not Applicable" functionality
                                  // isSelected: isNotApplicable
                                  //     ? controller.tempController.stateName.value == "Not Applicable"
                                  //     : controller.tempController.stateId.value == item['id']);
                                },
                                separatorBuilder: (context, index) {
                                  return const Divider();
                                })
                            : Center(
                                child: txt20Size(
                                    title:
                                        "${controller.labelTextDetail['no_state_label'] ?? "No states found!"}",
                                    context: context,
                                    fontFamily: regular))
                      ],
                    ))),
                if (controller.isOverlayLoading.value == true) ...[
                  overlayWidget(context)
                ]
              ],
            );
          }
        }));
  }
}



