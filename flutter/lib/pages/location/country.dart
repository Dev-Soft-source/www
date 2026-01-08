import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/location/LocationController.dart';
import 'package:proximaride_app/pages/widgets/drop_down_item_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/overlay_widget.dart';


class CountryPage extends GetView<LocationController> {
  const CountryPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(LocationController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['select_country_label'] ?? "Select country"}", context: context)),
        leading: const BackButton(
            color: Colors.white
        ),
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
                    mobile: 10.0,
                    tablet: 10.0,
                  )
                  ),
                  child: SingleChildScrollView(
                      child: Column(
                        children: [
                          fieldsWidget(
                              textController: controller.searchTextEditingController,
                              fieldType: "text",
                              autoFocus:true,
                              fontFamily: regular,
                              fontSize: 18.0,
                              readonly: false,
                              placeHolder: "${controller.labelTextDetail['search_country_label'] ?? "Search country here"}",
                              suffix: const Icon(Icons.search, color: textColor,),
                              onChanged: (value) async{
                                await controller.filterCountries(value);
                              }
                          ),
                          10.heightBox,
                          controller.searchCountries.isNotEmpty ?
                          ListView.separated(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: controller.searchCountries.length,
                              itemBuilder: (context, index){
                                return dropDownItemWidget(
                                    context: context,
                                    onTap: () async{

                                      controller.countryId.value = controller.searchCountries[index]['id'];
                                      controller.countryName.value = controller.searchCountries[index]['name'];
                                      controller.states.clear();
                                      controller.cities.clear();
                                      controller.stateId.value = 0;
                                      controller.stateName.value = "";
                                      controller.cityId.value = 0;
                                      controller.cityName.value = "";

                                      controller.tempController.countryId.value = controller.searchCountries[index]['id'];
                                      controller.tempController.countryName.value = controller.searchCountries[index]['name'];
                                      controller.tempController.states.clear();
                                      controller.tempController.cities.clear();
                                      controller.tempController.stateId.value = 0;
                                      controller.tempController.stateName.value = "";
                                      controller.tempController.cityId.value = 0;
                                      controller.tempController.cityName.value = "";
                                      Get.back();
                                    },
                                    name: "${controller.searchCountries[index]['name']}",
                                    isSelected: controller.countryId.value == controller.searchCountries[index]['id'] ? true : false
                                );
                              },
                              separatorBuilder: (context, index){
                                return const Divider();
                              }
                          ) :
                          Center(child: txt20Size(title: "${controller.labelTextDetail['no_country_label'] ?? "No countries found!"}", context: context, fontFamily: regular))
                        ],
                      )
                  )
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

