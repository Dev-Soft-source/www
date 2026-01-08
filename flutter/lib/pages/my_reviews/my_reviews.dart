import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/my_reviews/MyReviewsController.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import '../../consts/constFileLink.dart';
import '../widgets/progress_circular_widget.dart';
import '../widgets/review_card.dart';
import '../widgets/second_appbar_widget.dart';
import '../widgets/textWidget.dart';

class MyReviews extends GetView<MyReviewsController> {
  const MyReviews({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(MyReviewsController());
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          leading: const BackButton(color: Colors.white),
          title: Obx(() => secondAppBarWidget(context: context, title: "${controller.labelTextDetail['main_heading'] ?? "My reviews"}")),
          backgroundColor: primaryColor,
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
                    mobile: 15.0,
                    tablet: 15.0,
                  )),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      TabBar(
                        controller: controller.tabController,
                        onTap: (index){
                          controller.pageController.animateToPage(index, duration: const Duration(milliseconds: 1), curve: Curves.linear);
                        },
                        indicatorColor: primaryColor,
                        indicatorSize: TabBarIndicatorSize.label,
                        labelColor: primaryColor,
                        unselectedLabelColor: textColor,
                        labelStyle: const TextStyle(
                            fontFamily: regular,
                            fontSize: 22
                        ),
                        unselectedLabelStyle: const TextStyle(
                            fontFamily: regular,
                            fontSize: 22
                        ),
                        labelPadding: const EdgeInsets.all(5.0),
                        tabs:[
                          Text("${controller.labelTextDetail['review_received_label'] ?? "Reviews I received"}"),
                          Text("${controller.labelTextDetail['review_left_label'] ?? "Reviews I left"}")
                        ],
                      ),
                      Expanded(
                        child: PageView(
                          controller: controller.pageController,
                          children: [
                            controller.reviewsReceived.isNotEmpty ?
                            SingleChildScrollView(
                              controller: controller.receivedScrollController,
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  ListView.separated(
                                    itemCount: controller.reviewsReceived.length,
                                    shrinkWrap: true,
                                    physics: const NeverScrollableScrollPhysics(),
                                    itemBuilder: (context, index){
                                      return InkWell(
                                        onTap: () {
                                          Get.toNamed('/review_detail/${controller.reviewsReceived[index]['id']}/from/driver');
                                        },
                                        child: reviewCard(
                                          reviewCardColor: index%2 == 0 ? Colors.white : Colors.grey.shade100,
                                          context: context,
                                          controller: controller,
                                          bottomSheetHeight: (context.screenHeight / 2) + 50,
                                          name:
                                          "${controller.reviewsReceived[index]['from']['first_name']} ${controller.reviewsReceived[index]['from']['last_name']}",
                                          review: controller.reviewsReceived[index]
                                          ['review'],
                                          image: "${controller.reviewsReceived[index]['from']['profile_image']}",
                                          addedOn: controller.reviewsReceived[index]['added_on'],
                                          rating: controller.reviewsReceived[index]['average_rating'] != null ? double.parse(controller.reviewsReceived[index]['average_rating']) : 0,
                                          replied: (controller.reviewsReceived[index]['replies'] != null) ? controller.reviewsReceived[index]['replies']['reply'] : '' ,
                                          showReply: (controller.reviewsReceived[index]['replies'] != null) ? false : true,
                                          responseLabel:"${controller.labelTextDetail['response_label'] ?? "response"}",
                                          repliedLabel: "${controller.labelTextDetail['replied_label'] ?? "replied"}",
                                          replyLabel:"${controller.labelTextDetail['reply_label'] ?? "Reply"}",
                                          replyHeadingLabel:"${controller.labelTextDetail['reply_heading_label'] ?? "Reply"}",
                                          replyPlaceholder: "${controller.labelTextDetail['reply_placeholder'] ?? "Enter your reply"}",
                                          replySubmitButtonLabel:"${controller.labelTextDetail['reply_submit_button_label'] ?? "Submit"}",
                                          onSubmit: (){
                                            controller.addReply(controller.reviewsReceived[index]['id'],controller.replyTextController.text);
                                            controller.replyTextController.clear();
                                            Get.back();
                                          },
                                          screenHeight: context.screenHeight,
                                          screenWidth: context.screenWidth,
                                          index: index,
                                        ),
                                      );
                                    },
                                    separatorBuilder: (context, index){
                                      return const SizedBox();
                                    },
                                  ),
                                  if(controller.receivedNoMoreData.value == true)...[
                                    30.heightBox,
                                    Center(child: txt20Size(title: "${controller.labelTextDetail['no_more_data_label'] ?? "No more data"}", context: context)),
                                    30.heightBox,
                                  ],
                                  if(controller.receivedLoadMore.value == true)...[
                                    30.heightBox,
                                    Center(child: progressCircularWidget(context)),
                                    30.heightBox,
                                  ]
                                ],
                              ),
                            ) : Center(
                                child:
                                Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Image.asset(noReviews),
                                    txt16Size(title: "${controller.labelTextDetail['no_received_message'] ?? "You have not received any reviews yet"}", context: context),
                                  ],

                                )),
                            controller.reviewsLeft.isNotEmpty ?
                            SingleChildScrollView(
                              controller: controller.leftScrollController,
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  ListView.separated(
                                    itemCount: controller.reviewsLeft.length,
                                    shrinkWrap: true,
                                    physics: const NeverScrollableScrollPhysics(),
                                    itemBuilder: (context, index){
                                      return InkWell(
                                        onTap: () {
                                          Get.toNamed('/review_detail/${controller.reviewsLeft[index]['id']}/to/passenger');
                                        },
                                        child: reviewCard(
                                            reviewCardColor: index%2 == 0 ? Colors.white : Colors.grey.shade100,
                                          context: context,
                                          controller: controller,
                                          bottomSheetHeight: (context.screenHeight / 2) + 50,
                                          name: "${controller.reviewsLeft[index]['to']['first_name']} ${controller.reviewsLeft[index]['to']['last_name']}",
                                          review: controller.reviewsLeft[index]
                                          ['review'],
                                            image: "${controller.reviewsLeft[index]['to']['profile_image']}",
                                          addedOn: controller.reviewsLeft[index]
                                          ['added_on'],
                                            rating: controller.reviewsLeft[index]['average_rating'] != null ? double.parse(controller.reviewsLeft[index]['average_rating']) : 0,
                                          replied: (controller.reviewsLeft[index]['replies'] != null) ? controller.reviewsLeft[index]['replies']['reply'] : '' ,
                                          showReply: (controller.reviewsLeft[index]['replies'] != null) ? false : true,
                                          responseLabel:"${controller.labelTextDetail['response_label'] ?? "response"}",
                                          repliedLabel: "${controller.labelTextDetail['replied_label'] ?? "replied"}",
                                          replyLabel:"${controller.labelTextDetail['reply_label'] ?? "Reply"}",
                                            replyHeadingLabel:"${controller.labelTextDetail['reply_heading_label'] ?? "Reply"}",
                                          replyPlaceholder: "${controller.labelTextDetail['reply_placeholder'] ?? "Enter your reply"}",
                                          replySubmitButtonLabel:"${controller.labelTextDetail['reply_submit_button_label'] ?? "Submit"}",
                                          onSubmit: (){
                                            controller.addReply(controller.reviewsLeft[index]['id'],controller.replyTextController.text);
                                            controller.replyTextController.clear();
                                            Get.back();
                                          },
                                          type: 2,
                                          screenWidth: context.screenWidth,
                                          screenHeight: context.screenHeight,
                                          index: index
                                        ),
                                      );
                                    },
                                    separatorBuilder: (context, index){
                                      return const SizedBox();
                                    },
                                  ),
                                  if(controller.leftNoMoreData.value == true)...[
                                    30.heightBox,
                                    Center(child: txt20Size(title: "${controller.labelTextDetail['no_more_data_label'] ?? "No more data"}", context: context)),
                                    30.heightBox,
                                  ],
                                  if(controller.leftLoadMore.value == true)...[
                                    30.heightBox,
                                    Center(child: progressCircularWidget(context)),
                                    30.heightBox,
                                  ]
                                ],
                              ),
                            ) : Center(
                                child:
                                Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Image.asset(noReviews),
                                    txt16Size(title: "${controller.labelTextDetail['no_left_message'] ?? "You have not left any reviews yet"}", context: context),
                                  ],
                                )),
                          ],
                          onPageChanged: (index) async{
                            controller.tabController.index = index;
                            await controller.updateReviewPageValue(index);
                          },
                        )
                      )
                    ],
                  ),
                ),
                if(controller.isOverlayLoading.value == true)...[
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

