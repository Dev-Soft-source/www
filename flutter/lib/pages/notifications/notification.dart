import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/notifications/widgets/filter_notification_side_widget.dart';
import 'package:proximaride_app/pages/notifications/widgets/userCard.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:side_sheet/side_sheet.dart';
import 'NotificationController.dart';

class NotificationPage extends GetView<NotificationController> {
  const NotificationPage({super.key});


  @override
  Widget build(BuildContext context) {
    Get.put(NotificationController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title: "${controller.labelTextDetail['notification_page_main_heading'] ?? 'Notifications'}",
              context: context)),
          leading: const BackButton(color: Colors.white),
        ),
        body: Obx(() {
          if (controller.isLoading.value == true) {
            return Center(child: progressCircularWidget(context));
          } else {
            return controller.notificationsList.isEmpty && controller.filter.value == false ?  Center(
                child:
                Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Image.asset(noNotifications),
                    txt16Size(title: "${controller.labelTextDetail['notification_page_no_messages_label'] ?? "You have no notifications yet"}", context: context),
                  ],
                )
            ) : Stack(
              children: [
                SingleChildScrollView(
                  child: Column(

                    children: [
                      10.heightBox,
                      Padding(
                        padding: EdgeInsets.only(right: 15.0),
                        child: Align(
                          alignment: Alignment.topRight,
                          child: SizedBox(
                            height: getValueForScreenType<double>(
                              context: context,
                              mobile: 36.0,
                              tablet: 26.0,
                            ),
                            child: ElevatedButton.icon(
                                onPressed: () {
                                  SideSheet.right(
                                      body: filterNotificationSideWidget(
                                        context: context,
                                        controller: controller,
                                        screenWidth: context.screenWidth,
                                        screenHeight: context.screenHeight,
                                      ),
                                      context: context,
                                      width: context.screenWidth - 50);
                                },
                                style: ElevatedButton.styleFrom(
                                    backgroundColor: btnPrimaryColor,
                                    shape: RoundedRectangleBorder(
                                        borderRadius:
                                        BorderRadius.circular(5))),
                                icon: Image.asset(filterImage,
                                    height: getValueForScreenType<double>(
                                      context: context,
                                      mobile: 15.0,
                                      tablet: 15.0,
                                    ),
                                    width: getValueForScreenType<double>(
                                      context: context,
                                      mobile: 15.0,
                                      tablet: 15.0,
                                    )),
                                label: txt22Size(
                                    title: "${controller.labelTextDetail['notification_filter_btn_label'] ?? "Search filters"}",
                                    context: context,
                                    textColor: Colors.white)),
                          ),
                        ),
                      ),
                      10.heightBox,
                      Container(
                        padding: EdgeInsets.only(
                          left: getValueForScreenType<double>(
                            context: context,
                            mobile: 10.0,
                            tablet: 10.0,
                          ),
                          right: getValueForScreenType<double>(
                            context: context,
                            mobile: 10.0,
                            tablet: 10.0,
                          ),
                        ),
                        child: ListView.separated(
                            physics: const NeverScrollableScrollPhysics(),
                            shrinkWrap: true,
                            itemCount: controller.notificationsList.length,
                            itemBuilder: (context, index) {
                                return Dismissible(
                                    direction: DismissDirection.startToEnd,
                                    key: UniqueKey(),
                                    confirmDismiss: (DismissDirection direction) async{
                                      bool dataReturn = false;
                                      if (direction == DismissDirection.startToEnd) {
                                        controller.readNotification(controller.notificationsList[index]['id']);
                                        dataReturn = true;
                                      }
                                      return dataReturn;
                                    },
                                    child: userCard(
                                        context: context,
                                        bgColor: index % 2 == 0 ? Colors.white : Colors.grey.shade300,
                                        image: controller.notificationsList[index]['from']['profile_image'],
                                        name: "${controller.notificationsList[index]['from']['first_name']} ${controller.notificationsList[index]['from']['last_name']}" ,
                                        controller: controller,
                                        notification: "${controller.notificationsList[index]['message']}",
                                        date: controller.notificationsList[index]['added_on'],
                                        time: controller.notificationsList[index]['added_on'],
                                        userType: controller.notificationsList[index]['type'] == "1" ? 'Passenger' : 'Driver' ,
                                        onTap: (){
                                          if(controller.notificationsList[index]['notification_type'] == "review"){
                                            if(controller.notificationsList[index]['type'] == "1"){
                                              Get.toNamed('/notification_add_review/passenger/${controller.notificationsList[index]['ride_id']}/${controller.notificationsList[index]['posted_to']}/${controller.notificationsList[index]['id']}/${controller.notificationsList[index]['ride_detail_id']}');
                                            }else{
                                              Get.toNamed('/notification_add_review/driver/${controller.notificationsList[index]['ride_id']}/0/${controller.notificationsList[index]['id']}/${controller.notificationsList[index]['ride_detail_id']}');
                                            }


                                          }else if(controller.notificationsList[index]['notification_type'] == "chat"){
                                            var rideId = 0;
                                            if(controller.notificationsList[index]['ride_id'] != null){
                                              rideId = int.parse(controller.notificationsList[index]['ride_id'].toString());
                                            }
                                            Get.toNamed('/messaging_page/${controller.notificationsList[index]['posted_by']}/$rideId/new');
                                          }else if(controller.notificationsList[index]['notification_type'] == "phone"){
                                            Get.toNamed('/my_phone_number');
                                          }else if(controller.notificationsList[index]['notification_type'] == "christmas"){
                                          }else if(controller.notificationsList[index]['notification_type'] == "birthday"){
                                          }else if(controller.notificationsList[index]['notification_type'] == "password"){
                                          }else if(controller.notificationsList[index]['notification_type'] == "welcome"){
                                          }else if(controller.notificationsList[index]['notification_type'] == "student_card"){
                                            Get.toNamed('/student_card');
                                          }else{
                                            var type = controller.notificationsList[index]['type'] == "1" ? "ride" : "trip";
                                            Get.toNamed('/trip_detail/${controller.notificationsList[index]['ride_id']}/$type/${controller.notificationsList[index]['notification_type']}/${controller.notificationsList[index]['ride_detail_id']}');
                                          }
                                        },
                                        onLongPress: () async{
                                          bool isConfirmed = await controller.serviceController.showConfirmationDialog(controller.labelTextDetail['notification_confirm_message'] ?? "Are you sure you want to delete this notification");
                                          if(isConfirmed == false){
                                          }else{
                                           await controller.deleteNotification(controller.notificationsList[index]['id']);
                                          }
                                        }

                                    )
                                );

                            },
                            separatorBuilder: (context, index) {
                              return const SizedBox();
                            }),
                      ),
                    ],
                  ),
                ),
                if(controller.isOverlayLoading.value == true)...[
                  overlayWidget(context),
                ],
                 if(controller.notificationsList.isEmpty && controller.filter.value == true && controller.isOverlayLoading.value == false)...[
                   Center(
                       child:
                       Column(
                         mainAxisAlignment: MainAxisAlignment.center,
                         children: [
                           Image.asset(noNotifications),
                           txt16Size(title: "${controller.labelTextDetail['notification_page_no_messages_label'] ?? "You have no notifications"}", context: context),
                         ],
                       )
                   )
                 ]

              ],
            );
          }
        }));
  }
}



