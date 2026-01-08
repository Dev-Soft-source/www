import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/notification_add_review/NotificationAddReviewController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/network_cache_image_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/rating_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';

import '../widgets/tool_tip.dart';

class NotificationAddReviewPage extends GetView<NotificationAddReviewController> {
  const NotificationAddReviewPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(NotificationAddReviewController());
    return Scaffold(
      appBar: AppBar(
        leading: const BackButton(color: Colors.white),
        title: secondAppBarWidget(context: context, title: controller.reviewType.toString() == "driver" ? "${controller.labelTextDetail['driver_review_heading'] ?? "Review your driver"}" : "${controller.labelTextDetail['passenger_review_heading'] ?? "Review"}"),
        backgroundColor: primaryColor,
      ),
      body: Obx(() =>
          controller.isLoading.value == true ? Center(child: progressCircularWidget(context)) : Stack(
            children: [
              SingleChildScrollView(
                  child: Container(
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
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          40.heightBox,
                          if(controller.reviewType == "driver")...[
                            Container(
                              width: 80.0,
                              height: 80.0,
                              decoration: BoxDecoration(
                                color: Colors.blueAccent.shade100.withOpacity(0.3),
                                borderRadius: BorderRadius.circular(50.0),
                              ),
                              child: Center(
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(50.0),
                                  child: networkCacheImageWidget(
                                      controller.ride['driver']['profile_image'] ?? ' ',
                                      BoxFit.contain,
                                      70.0,
                                      70.0),
                                ),
                              ),
                            ),
                            txt22SizeCapitalized(title: "${controller.ride['driver']['first_name'] ?? ""} ${controller.ride['driver']['last_name'] ?? ""}", context: context),
                          ]else if(controller.reviewType == "passenger")...[
                            Container(
                              width: 80.0,
                              height: 80.0,
                              decoration: BoxDecoration(
                                color: Colors.blueAccent.shade100.withOpacity(0.3),
                                borderRadius: BorderRadius.circular(50.0),
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(50.0),
                                child: networkCacheImageWidget(
                                    controller.reviewPassengerImage.value ?? ' ',
                                    BoxFit.contain,
                                    40.0,
                                    40.0),
                              ),
                            ),
                            txt22SizeCapitalized(title: controller.reviewPassengerName.value, context: context),
                          ],


                          10.heightBox,
                          textAreaWidget(
                              textController: controller.reviewTextEditingController,
                              readonly: false,
                              maxLines: 6,
                              fontSize: 16.0,
                              fontFamily: regular,
                              placeHolder: controller.reviewType.toString() == "driver" ?
                              "${controller.labelTextDetail['driver_review_placeholder'] ?? "You can write a text review that will be public for our community to see\nYou can include specific feedback, comments or compliments about the driver’s performance"}" :
                              "${controller.labelTextDetail['passenger_review_placeholder'] ?? "You can include specific feedback, comments or compliments about the passenger’s behavior during the ride"}",
                            onChanged: (value){
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "review") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "review"));
                              }
                            }
                          ),
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "review") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "review"))
                          ],
                          20.heightBox,
                          Align(alignment: Alignment.topLeft, child: txt20Size(title: "${controller.labelTextDetail['review_criteria_label'] ?? 'Review criteria'}", context: context)),
                          cardShadowWidget(
                              context: context,
                              widgetChild: Container(
                                padding: const EdgeInsets.all(15.0),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    if(controller.reviewType == "driver")...[
                                      ratingWidget(
                                          context: context,
                                          title: "${controller.labelTextDetail['condition_label'] ?? "Condition of the vehicle"}",
                                          value: controller.vehicleCondition.value,
                                          isSelectable: true,
                                          onRatingUpdate: (value){
                                            controller.vehicleCondition.value = double.parse(value.toString());
                                          }
                                      ),
                                      15.heightBox,
                                    ],
                                    ratingWidget(
                                        context: context,
                                        title: "${controller.labelTextDetail['conscious_passenger_wellness_label'] ?? "Conscious to passenger wellness"}",
                                        value: controller.conscious.value,
                                        onRatingUpdate: (value){
                                          controller.conscious.value = double.parse(value.toString());
                                        }
                                    ),
                                    15.heightBox,
                                    ratingWidget(
                                        context: context,
                                        title: "${controller.labelTextDetail['comfort_label'] ?? "Comfort"}",
                                        value: controller.comfort.value,
                                        onRatingUpdate: (value){
                                          controller.comfort.value = double.parse(value.toString());
                                        }
                                    ),
                                    15.heightBox,
                                    ratingWidget(
                                        context: context,
                                        title: "${controller.labelTextDetail['communication_label'] ?? "Communication"}",
                                        value: controller.communication.value,
                                        onRatingUpdate: (value){
                                          controller.communication.value = double.parse(value.toString());
                                        }
                                    ),
                                    15.heightBox,
                                    ratingWidget(
                                        context: context,
                                        title: "${controller.labelTextDetail['overall_attitude_label'] ?? "Overall attitude"}",
                                        value: controller.attitude.value,
                                        onRatingUpdate: (value){
                                          controller.attitude.value = double.parse(value.toString());
                                        }
                                    ),
                                    15.heightBox,
                                    ratingWidget(
                                        context: context,
                                        title: "${controller.labelTextDetail['personal_hygiene_label'] ?? "Personal hygiene"}",
                                        value: controller.hygiene.value,
                                        onRatingUpdate: (value){
                                          controller.hygiene.value = double.parse(value.toString());
                                        }
                                    ),
                                    15.heightBox,
                                    ratingWidget(
                                        context: context,
                                        title: "${controller.labelTextDetail['respect_and_courtesy_label'] ?? "Respect and courtesy"}",
                                        value: controller.respect.value,
                                        onRatingUpdate: (value){
                                          controller.respect.value = double.parse(value.toString());

                                        }
                                    ),
                                    15.heightBox,
                                    ratingWidget(
                                        context: context,
                                        title: "${controller.labelTextDetail['safety_label'] ?? "Safety"}",
                                        value: controller.safety.value,
                                        onRatingUpdate: (value){
                                          controller.safety.value = double.parse(value.toString());
                                        }
                                    ),
                                    15.heightBox,
                                    ratingWidget(
                                        context: context,
                                        title: "${controller.labelTextDetail['timeliness_label'] ?? "Timeliness"}",
                                        value: controller.timeliness.value,
                                        onRatingUpdate: (value){
                                          controller.timeliness.value = double.parse(value.toString());
                                        }
                                    ),
                                    15.heightBox,
                                  ],
                                ),
                              )
                          ),
                          100.heightBox,
                        ],
                      )
                  )
              ),
              Align(
                alignment: Alignment.bottomCenter,
                child: Container(
                  padding: const EdgeInsets.all(10.0),
                  height: 70,
                  decoration: BoxDecoration(
                      color: Colors.grey.shade200,
                      borderRadius: BorderRadius.circular(5.0)
                  ),
                  child: SizedBox(
                    height: 50,
                    width: context.screenWidth,
                    child: elevatedButtonWidget(
                        textWidget: txt28Size(title: "${controller.labelTextDetail['review_submit_btn_label'] ?? "Submit"}", context: context, textColor: Colors.white),
                        onPressed: () async{

                          controller.vehicleCondition.value = double.parse((controller.vehicleCondition.value).ceil().toString());
                          controller.conscious.value = double.parse((controller.conscious.value).ceil().toString());
                          controller.comfort.value = double.parse((controller.comfort.value).ceil().toString());
                          controller.communication.value = double.parse((controller.communication.value).ceil().toString());
                          controller.attitude.value = double.parse((controller.attitude.value).ceil().toString());
                          controller.hygiene.value = double.parse((controller.hygiene.value).ceil().toString());
                          controller.respect.value = double.parse((controller.respect.value).ceil().toString());
                          controller.safety.value = double.parse((controller.safety.value).ceil().toString());
                          controller.timeliness.value = double.parse((controller.timeliness.value).ceil().toString());

                          if(controller.reviewType == "driver"){
                            await controller.postDriverReview(controller.ride['id']);
                          }else if(controller.reviewType == "passenger"){
                            await controller.storePassengerReview(controller.passengerBookingId.value);
                          }

                        },
                        context: context
                    ),
                  ),
                ),
              ),
              if(controller.isOverlayLoading.value == true)...[
                overlayWidget(context),
              ]
            ],
          )
      ),
    );
  }
}
