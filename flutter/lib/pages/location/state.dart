import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/location/LocationController.dart';
import 'package:proximaride_app/pages/widgets/drop_down_item_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../widgets/overlay_widget.dart';


class StatePage extends GetView<LocationController> {
  const StatePage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(LocationController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['select_state_label'] ?? "Select state"}", context: context)),
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
                              autoFocus: true,
                              fontFamily: regular,
                              fontSize: 18.0,
                              readonly: false,
                              placeHolder: "${controller.labelTextDetail['search_state_label'] ?? "Search state here"}",
                              suffix: const Icon(Icons.search, color: textColor,),
                              onChanged: (value) async{
                                await controller.filterStates(value);
                              }
                          ),
                          10.heightBox,
                          controller.searchStates.isNotEmpty ?
                          ListView.separated(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: controller.searchStates.length,
                              itemBuilder: (context, index){
                                return dropDownItemWidget(
                                    context: context,
                                    onTap: (){
                                      // controller.stateId.value = controller.searchStates[index]['id'];
                                      // controller.stateName.value = controller.searchStates[index]['name'];
                                      // controller.cities.clear();
                                      // controller.cityId.value = 0;
                                      // controller.cityName.value = "";


                                      controller.tempController.stateId.value = controller.searchStates[index]['id'];
                                      controller.tempController.stateName.value = controller.searchStates[index]['name'];
                                      controller.tempController.cities.clear();
                                      controller.tempController.cityId.value = 0;
                                      controller.tempController.cityName.value = "";
                                      Get.back();
                                    },
                                    name: "${controller.searchStates[index]['name']}",
                                    isSelected: controller.stateId.value == controller.searchStates[index]['id'] ? true : false

                                );
                              },
                              separatorBuilder: (context, index){
                                return const Divider();
                              }
                          ) :
                          Center(child: txt20Size(title: "${controller.labelTextDetail['no_state_label'] ?? "No state found!"}", context: context, fontFamily: regular))
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

