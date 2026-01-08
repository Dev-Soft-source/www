import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/location/LocationController.dart';
import 'package:proximaride_app/pages/widgets/drop_down_item_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/overlay_widget.dart';

class CityPage extends GetView<LocationController> {
  const CityPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(LocationController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(title: controller.isCity.value == "city" ?
          "${controller.labelTextDetail['select_city_label'] ?? "Select city"}" : controller.isCity.value == "origin" ?
          "${controller.labelTextDetail['select_origin_label'] ?? "Select origin"}" : controller.isCity.value == "destination" ?
          "${controller.labelTextDetail['select_destination_label'] ?? "Select destination"}" : "",
              context: context
          )),
          leading: const BackButton(color: Colors.white),
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
                      mobile: 10.0,
                      tablet: 10.0,
                    )),
                    child: SingleChildScrollView(
                        child: Column(
                          children: [
                            fieldsWidget(
                                textController:
                                controller.searchTextEditingController,
                                autoFocus: true,
                                fieldType: "text",
                                fontFamily: regular,
                                fontSize: 18.0,
                                readonly: false,
                                placeHolder: controller.isCity.value == "city" ?
                                "${controller.labelTextDetail['search_city_label'] ?? "Search city here"}" : controller.isCity.value == "origin" ?
                                "${controller.labelTextDetail['search_origin_label'] ?? "Search origin here"}" : controller.isCity.value == "destination" ?
                                "${controller.labelTextDetail['search_destination_label'] ?? "Search destination here"}" : "",
                                suffix: const Icon(
                                  Icons.search,
                                  color: textColor,
                                ),
                                onChanged: (value) async {
                                  if(controller.isCity.value == "city"){
                                    await controller.filterCities(value);
                                  }else{
                                    await controller.searchCitiesData(value);
                                  }

                                }),
                            10.heightBox,
                            controller.searchCities.isNotEmpty
                                ? ListView.separated(
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                itemCount: controller.searchCities.length,
                                itemBuilder: (context, index) {
                                  return dropDownItemWidget(
                                      context: context,
                                      onTap: () async{

                                        if(controller.isCity.value == "city"){
                                          controller.tempController.cityId.value =
                                          controller.searchCities[index]
                                          ['id'];
                                          controller
                                              .tempController.cityName.value =
                                          "${controller.searchCities[index]['name']}, ${controller.searchCities[index]['state']['abrv']}, ${controller.searchCities[index]['state']['country']['name']}";
                                        }else if(controller.isCity.value == "origin"){
                                          if(controller.spot.value == "yes"){
                                            controller
                                                .tempController.fromSpotControllers[controller.spotIndex.value].text =
                                            "${controller.searchCities[index]['name']}, ${controller.searchCities[index]['state']['abrv']}, ${controller.searchCities[index]['state']['country']['name']}";
                                          }else{
                                            controller
                                                .tempController.fromTextEditingController.text =
                                            "${controller.searchCities[index]['name']}, ${controller.searchCities[index]['state']['abrv']}, ${controller.searchCities[index]['state']['country']['name']}";
                                          }
                                        }else if(controller.isCity.value == "destination"){

                                            if(controller.spot.value == "yes"){
                                            controller
                                                .tempController.toSpotControllers[controller.spotIndex.value].text =
                                            "${controller.searchCities[index]['name']}, ${controller.searchCities[index]['state']['abrv']}, ${controller.searchCities[index]['state']['country']['name']}";
                                            }else{
                                              controller
                                                  .tempController.toTextEditingController.text =
                                              "${controller.searchCities[index]['name']}, ${controller.searchCities[index]['state']['abrv']}, ${controller.searchCities[index]['state']['country']['name']}";
                                            }
                                        }

                                        if(controller.isPostRideControllerRegistered){
                                          if(controller.spot.value == "yes"){
                                            if(controller.isCity.value == "origin"){
                                            }else if(controller.isCity.value == "destination"){

                                            }
                                          }else{
                                            if(controller.isCity.value == "origin"){
                                              if (controller.tempController.errors.any((error) => error['title'] == "from")) {
                                                controller.tempController.errors.removeWhere((error) => error['title'] == "from");
                                              }
                                            }else if(controller.isCity.value == "destination"){
                                              if (controller.tempController.errors.any((error) => error['title'] == "to")) {
                                                controller.tempController.errors.removeWhere((error) => error['title'] == "to");
                                              }
                                            }
                                            controller.tempController.getCitiesDistance();
                                          }

                                        }

                                        Get.back();
                                      },
                                      name:
                                      "${controller.searchCities[index]['name']}, ${controller.searchCities[index]['state']['abrv']}, ${controller.searchCities[index]['state']['country']['name']}",
                                      isSelected: controller.cityId.value ==
                                          controller.searchCities[index]
                                          ['id']
                                          ? true
                                          : false);
                                },
                                separatorBuilder: (context, index) {
                                  return const Divider();
                                })
                                : Center(
                                child: txt20Size(
                                    title:  controller.isCity.value == "city" ?
                                    "${controller.labelTextDetail['no_city_label'] ?? "No cities found!"}" : controller.isCity.value == "origin" ?
                                    "${controller.labelTextDetail['no_origin_label'] ?? "No origin found!"}" : controller.isCity.value == "destination" ?
                                    "${controller.labelTextDetail['no_destination_label'] ?? "No destination found!"}" : "",
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
