import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/PostRideController.dart';
import 'package:proximaride_app/pages/post_ride/widget/anything_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/booking_option_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/cancellation_policy.dart';
import 'package:proximaride_app/pages/post_ride/widget/disclaimer_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/luggage_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/meeting_dropoff_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/pet_animal_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/price_payment_option_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/ride_info_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/ride_preference_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/seat_available_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/smoking_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/vehicle_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../widgets/tool_tip.dart';

class PostRidePage extends GetView<PostRideController> {
  const PostRidePage({super.key});

  @override
  Widget build(BuildContext context) {
    var controller = Get.put(PostRideController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(title: controller.rideType.value == "update" ? "${controller.labelTextDetail['main_heading_update'] ?? "Edit ride"}" : "${controller.labelTextDetail['main_heading'] ?? "Post a ride"}", context: context)),
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
                      mobile: 15.0,
                      tablet: 15.0,
                    )),
                    child: SingleChildScrollView(
                      controller: controller.scrollController,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [

                          if(controller.rideType.value != 'update')...[
                            Align(
                              alignment: Alignment.topRight,
                              child: SizedBox(
                                width: context.screenWidth,
                                height: getValueForScreenType<double>(
                                  context: context,
                                  mobile: 40.0,
                                  tablet: 40.0,
                                ),
                                child: elevatedButtonWidget(
                                  textWidget: txt22Size(
                                      title: "${controller.labelTextDetail['post_arrived_again_label'] ?? "Copy ride details"}",
                                      textColor: Colors.white,
                                      context: context,
                                      fontFamily: regular),
                                  context: context,
                                  onPressed: () {
                                    Get.offNamed("/post_ride_again");
                                  },
                                ),
                              ),
                            ),
                          ],

                          10.heightBox,
                          txt18Size(context: context,textColor: Colors.red,title: '* ${controller.labelTextDetail['indicates_required_field_text'] ?? "Indicates required field"}'),
                          10.heightBox,
                          rideInfoWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth,
                              bookingCheck: controller.bookings.value,
                              error: controller.errors.toList(),
                          ),
                          10.heightBox,
                          meetingDropOffWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth,
                              bookingCheck: controller.bookings.value,
                              error: controller.errors.toList(),
                          ),
                          10.heightBox,
                          seatAvailableWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth,
                              bookingCheck: controller.bookings.value,
                              error: controller.errors.toList()),
                          10.heightBox,
                          Obx( () =>
                            vehicleWidget(
                                context: context,
                                controller: controller,
                                screenWidth: context.screenWidth,
                                screenHeight: context.screenHeight,
                                bookingCheck: controller.bookings.value,
                                error: controller.errors.toList()),
                          ),
                          10.heightBox,
                          smokingWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth,
                              bookingCheck: controller.bookings.value,
                              error: controller.errors.toList()),
                          10.heightBox,
                          petAnimalWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth,
                              bookingCheck: controller.bookings.value,
                              error: controller.errors.toList()),
                          10.heightBox,
                          ridePreferenceWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth,
                              bookingCheck: controller.bookings.value,
                              error: controller.errors.toList()),
                          10.heightBox,
                          bookingOptionWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth,
                              bookingCheck: controller.bookings.value,
                              error: controller.errors.toList()),
                          10.heightBox,
                          luggageWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth,
                              bookingCheck: controller.bookings.value,
                              error: controller.errors.toList()),
                          10.heightBox,
                          pricePaymentOptionWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth,
                              bookingCheck: controller.bookings.value,
                              error: controller.errors.toList()),
                          10.heightBox,
                          cancellationPolicyWidget(
                            context: context,
                            controller: controller,
                            screenWidth: context.screenWidth,
                            error: controller.errors.toList(),
                          ),
                          10.heightBox,
                          anythingWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth,
                              bookingCheck: controller.bookings.value,
                              error: controller.errors.toList()),
                          10.heightBox,
                          disclaimerWidget(
                              context: context,
                              controller: controller,
                              screenWidth: context.screenWidth),
                          10.heightBox,
                          InkWell(
                            onTap: (){
                              if(controller.errors.firstWhereOrNull((element) => element['title'] == "agree_terms") != null)
                              {
                                controller.errors.remove(controller.errors.firstWhereOrNull((element) => element['title'] == "agree_terms"));
                              }
                              controller.disclaimer.value = controller.disclaimer.value == true ? false : true;
                            },
                            child: Container(
                              padding: EdgeInsets.only(left: 10.0, top: 10.0, bottom: 10.0),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(5.0),
                                border: Border.all(
                                    width: 1,
                                    color: controller.disclaimer.value ==
                                        true
                                        ? primaryColor
                                        : controller.errors
                                        .where((error) =>
                                    error == "agree_terms")
                                        .isNotEmpty
                                        ? Colors.red
                                        : Colors.grey.shade200),
                                color: controller.disclaimer.value == true
                                      ? primaryColor.withOpacity(0.1)
                                      : Colors.white
                              ),
                              child: RichText(
                                text: TextSpan(
                                  children: [
                                    textSpan(
                                        title:
                                        "${controller.labelTextDetail['mobile_agree_terms_label'] ?? "I will abide by ProximaRide rules and I have read and agree to ProximaRide "}",
                                        context: context,
                                        fontFamily: bold,
                                        textColor: textColor,
                                        textSize: 16.0),
                                    textSpan(
                                      title: " ${controller.labelTextDetail['mobile_term_of_service_label'] ?? "Terms of services"}",
                                      context: context,
                                      fontFamily: bold,
                                      textColor: primaryColor,
                                      textSize: 16.0,
                                      recognizer: TapGestureRecognizer()
                                        ..onTap = () {
                                          Get.toNamed('/term_condition');
                                        },
                                    ),
                                    textSpan(
                                        title: " ${controller.labelTextDetail['mobile_agree_terms_and_label'] ?? " and "}",
                                        context: context,
                                        fontFamily: bold,
                                        textColor: textColor,
                                        textSize: 16.0),
                                    textSpan(
                                      title: " ${controller.labelTextDetail['mobile_term_of_use_label'] ?? "Term of use"}",
                                      context: context,
                                      fontFamily: bold,
                                      textColor: primaryColor,
                                      textSize: 16.0,
                                      recognizer: TapGestureRecognizer()
                                        ..onTap = () {
                                          Get.toNamed('/term_of_use');
                                        },
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                          10.heightBox,
                          if(controller.errors.firstWhereOrNull((element) => element['title'] == "agree_terms") != null) ...[
                            toolTip(tip: controller.errors.firstWhereOrNull((element) => element['title'] == "agree_terms"))
                          ],

                          90.heightBox,
                        ],
                      ),
                    )),
                Align(
                  alignment: Alignment.bottomCenter,
                  child: Container(
                    padding: const EdgeInsets.all(15.0),
                    color: Colors.grey.shade100,
                    width: context.screenWidth,
                    child: elevatedButtonWidget(
                        textWidget: txt28Size(
                            title: controller.rideType.value == 'update' ? '${controller.labelTextDetail['update_button_label'] ?? "Update ride"}' : "${controller.labelTextDetail['submit_button_label'] ?? "Post ride"}",
                            context: context,
                            textColor: Colors.white),
                        context: context,
                        onPressed: () async {
                          await controller.postRide(context, context.screenHeight);
                        }),
                  ),
                ),
                if (controller.isOverlayLoading.value == true) ...[
                  overlayWidget(context),
                  //overlayWidget(context)
                ]
              ],
            );
          }
        }));
  }
}
