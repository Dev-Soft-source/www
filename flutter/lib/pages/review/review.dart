import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/review/ReviewController.dart';
import 'package:proximaride_app/pages/widgets/review_card.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';

import '../widgets/textWidget.dart';

class ReviewPage extends GetView<ReviewController> {
  const ReviewPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(ReviewController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx( () =>
            secondAppBarWidget(
                title:
                    Get.parameters['type'] == "user" ?
                    "${controller.serviceController.loginUserDetail['first_name']} ${controller.serviceController.loginUserDetail['last_name']} ${controller.labelTextDetail['review_label'] ?? "reviews"}" :
                    Get.parameters['type'] == "driver" || Get.parameters['type'] == "passenger" ?
                    "${controller.userDetail['first_name'] ?? ""} ${controller.userDetail['last_name'] ?? ""} ${controller.labelTextDetail['review_label'] ?? "reviews"}" :
                    "",
                context: context),
          ),
          leading: const BackButton(color: Colors.white),
        ),
        body: Obx(() {
          if (controller.isLoading.value == true) {
            return Center(child: progressCircularWidget(context));
          } else {
            return SingleChildScrollView(
              controller: controller.scrollController,
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
                  children: [
                    controller.reviews.isNotEmpty ? ListView.separated(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: controller.reviews.length,
                        itemBuilder: (context, index) {
                          return InkWell(
                            onTap: (){
                              Get.toNamed('/review_detail/${controller.reviews[index]['id']}/to/passenger');
                            },
                            child: reviewCard(
                                reviewCardColor: index%2 == 0 ? Colors.white : Colors.grey.shade100,
                                context: context,
                                controller: controller,
                              bottomSheetHeight: (context.screenHeight / 2) + 50,
                                name:
                                    "${controller.reviews[index]['from']['first_name']} ${controller.reviews[index]['id']}",
                                review: controller.reviews[index]['review'],
                                image: controller.reviews[index]['from']
                                    ['profile_image'],
                                addedOn: controller.reviews[index]['added_on'],
                                rating: controller.reviews[index]['average_rating'] != null ? double.parse(controller.reviews[index]['average_rating']) : 0.0,//rating,
                              replied: (controller.reviews[index]['replies'] != null) ? controller.reviews[index]['replies']['reply'] : '',
                              showReply: (controller.reviews[index]['replies'] != null) ? false : true,
                              profileType: controller.profileType,
                              responseLabel:"${controller.labelTextDetail['response_label'] ?? "response"}",
                              repliedLabel: "${controller.labelTextDetail['replied_label'] ?? "replied"}",
                              replyLabel:"${controller.labelTextDetail['reply_label'] ?? "Reply"}",
                              replyHeadingLabel:"${controller.labelTextDetail['reply_heading_label'] ?? "Reply"}",
                              replyPlaceholder: "${controller.labelTextDetail['reply_placeholder'] ?? "Enter your reply"}",
                              replySubmitButtonLabel:"${controller.labelTextDetail['reply_submit_button_label'] ?? "Submit"}",
                              onSubmit: () {
                                controller.addReply(controller.reviews[index]['id'],controller.replyTextController.text);
                                controller.replyTextController.clear();
                                Get.back();
                              },
                              screenHeight: context.screenHeight,
                              screenWidth: context.screenWidth,
                              index: index,
                            ),
                          );
                        },
                        separatorBuilder: (context, index) {
                          return const SizedBox();
                        }) : Center(child:  txt20Size(
                      context: context, title: "${controller.labelTextDetail['no_more_data_label'] ?? "No More data to show"}"),
                    ),
                    10.heightBox,
                    if (controller.noMoreData.value) ...[
                      Center(
                        child: txt20Size(
                            context: context, title: "${controller.labelTextDetail['no_more_data_label'] ?? "No More data to show"}"),
                      ),
                      20.heightBox,
                    ] else ...[
                      if (controller.isScrollLoading.value) ...[
                        Center(
                          child: progressCircularWidget(context),
                        ),
                        20.heightBox,
                      ]
                    ]
                  ],
                ),
              ),
            );
          }
        }));
  }
}