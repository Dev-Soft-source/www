import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_vehicle/MyVehicleController.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/logger_service.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';

class MyVehiclePage extends GetView<MyVehicleController> {
  const MyVehiclePage({super.key});
  @override
  Widget build(BuildContext context) {
    Get.put(MyVehicleController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(
            title:
                "${controller.labelTextDetail['main_heading'] ?? "My vehicles"}",
            context: context)),
        leading: const BackButton(color: Colors.white),
      ),
      body: SafeArea(
        child: Obx(() {
          if (controller.errorStateManager.isLoading.value) {
            return Center(child: progressCircularWidget(context));
          } else if (controller.errorStateManager.hasError.value) {
            return ErrorStateWidget(
              errorType: controller.errorStateManager.errorType.value,
              message: controller.errorStateManager.errorMessage.value,
              onRetry: () => controller.errorStateManager.onRetry.value?.call(),
            );
          } else {
            return Stack(
              children: [
                Container(
                  padding: const EdgeInsets.all(15.0),
                  child: SingleChildScrollView(
                    controller: controller.listScrollController,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        for (var i = 0;
                            i < controller.vehicleList.length;
                            i++) ...[
                          InkWell(
                            onTap: () {
                              logger.info(controller.vehicleList[i]['image']);
                            },
                            child: Card(
                              surfaceTintColor: i % 2 == 0
                                  ? Colors.white
                                  : Colors.grey.shade100,
                              color: i % 2 == 0
                                  ? Colors.white
                                  : Colors.grey.shade100,
                              elevation: 1,
                              child: Container(
                                padding: const EdgeInsets.all(8.0),
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.start,
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Container(
                                      width: 66.0,
                                      height: 66.0,
                                      decoration: BoxDecoration(
                                        color: Colors.blueAccent.shade100,
                                        borderRadius:
                                            BorderRadius.circular(50.0),
                                      ),
                                      child: Align(
                                        alignment: Alignment.center,
                                        child: ClipRRect(
                                          borderRadius:
                                              BorderRadius.circular(50.0),
                                          child: networkCacheImageWidget(
                                            "${controller.vehicleList[i]['image']}",
                                            BoxFit.contain,
                                            56.0,
                                            56.0,
                                          ),
                                        ),
                                      ),
                                    ),
                                    5.widthBox,
                                    Expanded(
                                        child: Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceBetween,
                                      mainAxisSize: MainAxisSize.max,
                                      children: [
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              // Vehicle title line: 20px
                                              txt20Size(
                                                  title:
                                                      "${controller.vehicleList[i]['year']} ${controller.vehicleList[i]['make']} ${controller.vehicleList[i]['model']}",
                                                  context: context,
                                                  fontFamily: regular,
                                                  textColor: textColor),
                                              // License number: 20px body
                                              txt20Size(
                                                  title:
                                                      "${controller.vehicleList[i]['liscense_no']}",
                                                  textColor: textColor,
                                                  context: context,
                                                  fontFamily: regular),
                                              // Car type: 20px body
                                              txt20Size(
                                                  title:
                                                      "${controller.vehicleList[i]['car_type']}",
                                                  textColor: textColor,
                                                  context: context,
                                                  fontFamily: regular)
                                            ],
                                          ),
                                        ),
                                        5.widthBox,
                                        Column(
                                          crossAxisAlignment:
                                              CrossAxisAlignment.center,
                                          children: [
                                            SizedBox(
                                              height: 30,
                                              width: 132,
                                              child: elevatedButtonWidget(
                                                  textWidget: txt22Size(
                                                      title:
                                                          "${controller.labelTextDetail['edit_vehicle_button_text'] ?? "Edit vehicle"}",
                                                      context: context,
                                                      fontFamily: regular,
                                                      textColor: Colors.white),
                                                  onPressed: () async {
                                                    await controller
                                                        .getVehicleDetail(
                                                            controller
                                                                    .vehicleList[
                                                                i]['id']);
                                                  },
                                                  context: context),
                                            ),
                                            5.heightBox,
                                            SizedBox(
                                              height: 30,
                                              width: 132,
                                              child: elevatedButtonWidget(
                                                  textWidget: txt22Size(
                                                      title:
                                                          "${controller.labelTextDetail['remove_vehicle_button_text'] ?? "Remove vehicle"}",
                                                      context: context,
                                                      fontFamily: regular,
                                                      textColor: Colors.white),
                                                  btnColor: Colors.red,
                                                  onPressed: () async {
                                                    await controller
                                                        .removeVehicle(controller
                                                                .vehicleList[i]
                                                            ['id']);
                                                  },
                                                  context: context),
                                            )
                                          ],
                                        )
                                      ],
                                    ))
                                  ],
                                ),
                              ),
                            ),
                          )
                        ],
                        80.heightBox,
                      ],
                    ),
                  ),
                ),
                Align(
                  alignment: Alignment.bottomCenter,
                  child: Container(
                    color: Colors.grey.shade100,
                    padding: const EdgeInsets.all(15.0),
                    child: SizedBox(
                      width: context.screenWidth,
                      height: 50,
                      child: elevatedButtonWidget(
                        textWidget: txt22Size(
                            title: controller.vehicleList.isEmpty
                                ? "${controller.labelTextDetail['add_vehicle_button_text'] ?? "Add Vehicle"}"
                                : "Add a New Vehicle",
                            textColor: Colors.white,
                            context: context,
                            fontFamily: regular),
                        onPressed: () async {
                          controller.oldCarImagePath.value = "";
                          await controller.getVehicleDetail(0);
                        },
                      ),
                    ),
                  ),
                ),
                if (controller.vehicleList.isEmpty) ...[
                  Center(
                      child: txt20Size(
                          context: context,
                          fontFamily: bold,
                          title:
                              "${controller.labelTextDetail['no_vehicle_message'] ?? "You don't have any vehicles on your profile yet"}")),
                ],
                if (controller.isOverlayLoading.value == true) ...[
                  overlayWidget(context),
                ]
              ],
            );
          }
        }),
      ),
    );
  }
}
