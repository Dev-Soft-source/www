import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/review_detail/ReviewDetailController.dart';
import 'package:proximaride_app/pages/widgets/average_rating_stars_widget.dart';
import '../../consts/constFileLink.dart';
import '../widgets/card_shadow_widget.dart';
import '../widgets/network_cache_image_widget.dart';
import '../widgets/progress_circular_widget.dart';
import '../widgets/rating_widget.dart';
import '../widgets/second_appbar_widget.dart';
import '../widgets/textWidget.dart';
import '../widgets/text_area_widget.dart';

class ReviewDetail extends GetView<ReviewDetailController> {
  const ReviewDetail({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(ReviewDetailController());
    return DefaultTabController(
      length: 2,
      child: Scaffold(
          appBar: AppBar(
            leading: const BackButton(color: Colors.white),
            title: Obx(() => secondAppBarWidget(context: context, title: "${controller.labelTextDetail['main_heading'] ?? "Review"}")),
            backgroundColor: primaryColor,
          ),
          body: Obx(() {
            if (controller.isLoading.value == true) {
              return Center(child: progressCircularWidget(context));
            } else {
              print(controller.fromToType);
              return Container(
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
                  child: SingleChildScrollView(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        20.heightBox,
                        Container(
                          width: 66.0,
                          height: 66.0,
                          decoration: BoxDecoration(
                            color: Colors.blueAccent.shade100.withOpacity(0.3),
                            borderRadius: BorderRadius.circular(50.0),
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(50.0),
                            child: networkCacheImageWidget(controller.reviewDetail[controller.fromToType]['profile_image'], BoxFit.contain, 75.0, 75.0),
                          ),
                        ),
                        txt25SizeCapitalize(
                            title: "${controller.reviewDetail[controller.fromToType]['first_name'] ?? " "} ${controller.reviewDetail[controller.fromToType]['last_name'] ?? " "}",
                            context: context),
                        10.heightBox,
                        // if (controller.errorList.isNotEmpty) ...[
                        //   ListView.builder(
                        //     shrinkWrap: true,
                        //     physics: const NeverScrollableScrollPhysics(),
                        //     itemCount: controller.errorList.length,
                        //     itemBuilder: (context, index) {
                        //       return Column(
                        //         crossAxisAlignment: CrossAxisAlignment.start,
                        //         children: [
                        //           Row(
                        //             crossAxisAlignment:
                        //                 CrossAxisAlignment.center,
                        //             mainAxisAlignment: MainAxisAlignment.center,
                        //             children: [
                        //               const Icon(Icons.circle,
                        //                   size: 10, color: Colors.red),
                        //               10.widthBox,
                        //               Expanded(
                        //                   child: txt12Size(
                        //                       title:
                        //                           "${controller.errorList[index]}",
                        //                       fontFamily: regular,
                        //                       textColor: Colors.red,
                        //                       context: context))
                        //             ],
                        //           ),
                        //           5.heightBox,
                        //         ],
                        //       );
                        //     },
                        //   ),
                        //   10.heightBox,
                        // ],
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 15),
                          child: textAreaWidget(
                              readonly: true,
                              maxLines: 6,
                              fontSize: 16.0,
                              fontFamily: bold,
                              placeHolder:
                                  "${controller.reviewDetail['review'] ?? " "}"),
                        ),
                        20.heightBox,
                        Align(
                            alignment: Alignment.topLeft,
                            child: txt20Size(
                                title: "${controller.labelTextDetail['review_criteria_label'] ?? 'Review criteria'}", context: context)),
                        cardShadowWidget(
                            context: context,
                            widgetChild: Container(
                              padding: const EdgeInsets.all(15.0),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [

                                  // if(Get.parameters['reviewType'] == "driver")...[
                                    ratingWidget(
                                        context: context,
                                        title: "${controller.labelTextDetail['condition_label'] ?? "Condition of the vehicle"}",
                                        isSelectable: false,
                                        value : double.parse(controller.reviewDetail['vehicle_condition']?.toString() ?? "0"),
                                        onRatingUpdate: (value){
                                        }
                                    ),
                                    15.heightBox,
                                  // ],
                                  ratingWidget(
                                    context: context,
                                    title: "${controller.labelTextDetail['conscious_passenger_wellness_label'] ?? "Conscious to passenger wellness"}",
                                    value: double.parse(controller.reviewDetail['conscious']?.toString() ?? "0"),
                                    isSelectable: false,
                                    onRatingUpdate: (value) {},
                                  ),
                                  15.heightBox,
                                  ratingWidget(
                                    context: context,
                                    title: "${controller.labelTextDetail['comfort_label'] ?? "Comfort"}",
                                    value: double.parse(controller.reviewDetail['comfort'] ?? '0'),
                                    isSelectable: false,
                                    onRatingUpdate: (value) {},
                                  ),
                                  15.heightBox,
                                  ratingWidget(
                                    context: context,
                                    title: "${controller.labelTextDetail['communication_label'] ?? "Communication"}",
                                    value: double.parse(controller.reviewDetail['communication'] ?? '0'),
                                    isSelectable: false,
                                    onRatingUpdate: (value) {},
                                  ),
                                  15.heightBox,
                                  ratingWidget(
                                    context: context,
                                    title: "${controller.labelTextDetail['overall_attitude_label'] ?? "Overall attitude"}",
                                    value: double.parse(controller.reviewDetail['attitude'] ?? '0'),
                                    isSelectable: false,
                                    onRatingUpdate: (value) {},
                                  ),
                                  15.heightBox,
                                  ratingWidget(
                                    context: context,
                                    title: "${controller.labelTextDetail['personal_hygiene_label'] ?? "Personal hygiene"}",
                                    value: double.parse(controller.reviewDetail['hygiene'] ?? '0'),
                                    isSelectable: false,
                                    onRatingUpdate: (value) {},
                                  ),
                                  15.heightBox,
                                  ratingWidget(
                                    context: context,
                                    title: "${controller.labelTextDetail['respect_and_courtesy_label'] ?? "Respect and courtesy"}",
                                    value: double.parse(controller.reviewDetail['respect'] ?? '0'),
                                    isSelectable: false,
                                    onRatingUpdate: (value) {},
                                  ),
                                  15.heightBox,
                                  ratingWidget(
                                    context: context,
                                    title: "${controller.labelTextDetail['safety_label'] ?? "Safety"}",
                                    value: double.parse(controller.reviewDetail['safety'] ?? '0'),
                                    isSelectable: false,
                                    onRatingUpdate: (value) {},
                                  ),
                                  15.heightBox,
                                  ratingWidget(
                                    context: context,
                                    title: "${controller.labelTextDetail['timeliness_label'] ?? "Timeliness"}",
                                    value: double.parse(controller.reviewDetail['timeliness'] ?? '0'),
                                    isSelectable: false,
                                    onRatingUpdate: (value) {},
                                  ),
                                  15.heightBox,
                                ],
                              ),
                            )),
                        20.heightBox,
                        cardShadowWidget(
                          context: context,
                          widgetChild: Container(
                            padding: const EdgeInsets.all(15.0),
                            child: Row(
                              children: [
                                txt18Size(title: '${controller.labelTextDetail['average_label'] ?? 'Average'} (${controller.average.value.toStringAsFixed(1)})',fontFamily: regular,context: context,textColor: Colors.black),
                                const Spacer(),

                                averageStarWidget(
                                  fixValue: controller.average.value <= 10 ? controller.average.value.toInt() : 0,
                                  decimalValue: controller.average.value <= 10 ? (controller.average.value - controller.average.value.toInt()) : 0.0,
                                  screenHeight: context.screenHeight,
                                  screenWidth: context.screenWidth,
                                  emptyStars: ((5 - controller.average.value).toInt())
                                ),
                              ],
                            ),
                          ),
                        ),
                        30.heightBox,
                      ],
                    ),
                  ));
            }
          })),
    );
  }
}
