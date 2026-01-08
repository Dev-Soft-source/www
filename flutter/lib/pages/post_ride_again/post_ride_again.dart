import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride_again/PostRideAgainController.dart';
import 'package:proximaride_app/pages/widgets/post_ride_again_card_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';



class PostRideAgainPage extends GetView<PostRideAgainController> {
  const PostRideAgainPage({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(PostRideAgainController());
    return PopScope(
      canPop: false,
      onPopInvoked: (confirmed) {
        Get.offNamed('/post_ride/0/new');
        return;
      },
      child: Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['post_ride_again_main_heading'] ?? "Post ride again"}", context: context)),
          leading: const BackButton(
              color: Colors.white,
          ),
        ),

        body: Obx(() {
          if(controller.isLoading.value == true){
            return Center(child: progressCircularWidget(context));
          }else{
            return Stack(
              children: [
                Container(
                  padding: const EdgeInsets.all(10.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      TabBar(
                        onTap: (index) async{
                          await controller.getTabIndex(index);
                        },
                        indicatorColor: primaryColor,
                        indicatorSize: TabBarIndicatorSize.tab,
                        controller: controller.tabController,
                        //isScrollable: true,
                        tabs: [
                          Tab(child: txt20Size(title: "${controller.labelTextDetail['upcoming_label'] ?? "Upcoming"}", context: context)),
                          Tab(child: txt20Size(title: "${controller.labelTextDetail['completed_label'] ?? "Completed"}", context: context)),
                          Tab(child: txt20Size(title: "${controller.labelTextDetail['cancelled_label'] ?? "Cancelled"}", context: context)),
                        ],
                      ),
                      10.heightBox,
                      Expanded(
                          child: PageView(
                            controller: controller.pageController,
                            children: [
                              controller.upcomingPostRideList.isNotEmpty ? SingleChildScrollView(
                                controller: controller.scrollController,
                                child: Column(
                                  children: [
                                    ListView.separated(
                                      shrinkWrap: true,
                                      itemCount: controller.upcomingPostRideList.length,
                                      physics: const NeverScrollableScrollPhysics(),
                                      itemBuilder: (context, index){
                                        return postRideAgainCardWidget(
                                          cardBgColor: index%2 == 0 ? Colors.white : Colors.grey.shade100,
                                          context: context,
                                          screenWidth: context.screenWidth,
                                          fromLabel: "${controller.labelTextDetail['from_label'] ?? "From"}",
                                          toLabel: "${controller.labelTextDetail['to_label'] ?? "To"}",
                                          fromText: controller.upcomingPostRideList[index]['ride_detail'][0]['departure'].toString(),
                                          toText: controller.upcomingPostRideList[index]['ride_detail'][0]['destination'].toString(),
                                          onTap: (){
                                            Get.offNamed("/post_ride/${controller.upcomingPostRideList[index]['id']}/new");
                                          }
                                        );
                                      },
                                      separatorBuilder: (context, index){
                                        return const SizedBox();
                                      },
                                    ),
                                    10.heightBox,
                                    if(controller.upcomingEnd.value != "")...[
                                      Center(
                                        child: txt16Size(title: controller.upcomingEnd.value, context: context),
                                      )
                                    ],
                                    if(controller.isScrollUpcomingLoading.value == true)...[
                                      Center(child: progressCircularWidget(context)),
                                      10.heightBox,
                                    ]
                                  ],
                                ),
                              ) : Center(child: txt20Size(context: context, title: "${controller.labelTextDetail['upcoming_ride_no_found_message'] ?? "No upcoming ride found"}")),
                              controller.completedPostRideList.isNotEmpty ? SingleChildScrollView(
                                controller: controller.scrollController,
                                child: Column(
                                  children: [
                                    ListView.separated(
                                      shrinkWrap: true,
                                      itemCount: controller.completedPostRideList.length,
                                      physics: const NeverScrollableScrollPhysics(),
                                      itemBuilder: (context, index){
                                        return postRideAgainCardWidget(
                                            cardBgColor: index%2 == 0 ? Colors.white : Colors.grey.shade100,
                                            context: context,
                                            screenWidth: context.screenWidth,
                                            fromLabel: "${controller.labelTextDetail['from_label'] ?? "From"}",
                                            toLabel: "${controller.labelTextDetail['to_label'] ?? "To"}",
                                            fromText: "${controller.completedPostRideList[index]['ride_detail'][0]['departure']}",
                                            toText: controller.completedPostRideList[index]['ride_detail'][0]['destination'],
                                            onTap: (){
                                              Get.offNamed("/post_ride/${controller.completedPostRideList[index]['id']}/new");
                                            }
                                        );
                                      },
                                      separatorBuilder: (context, index){
                                        return const SizedBox();
                                      },
                                    ),
                                    10.heightBox,
                                    if(controller.completedEnd.value != "")...[
                                      Center(
                                        child: txt16Size(title: controller.completedEnd.value, context: context),
                                      )
                                    ],
                                    if(controller.isScrollCompletedLoading.value == true)...[
                                      Center(child: progressCircularWidget(context)),
                                      10.heightBox,
                                    ]
                                  ],
                                ),
                              ) : Center(child: txt20Size(context: context, title: "${controller.labelTextDetail['completed_ride_no_found_message'] ?? "No completed ride found"}")),
                              controller.cancelledPostRideList.isNotEmpty ? SingleChildScrollView(
                                controller: controller.scrollController,
                                child: Column(
                                  children: [
                                    ListView.separated(
                                      shrinkWrap: true,
                                      itemCount: controller.cancelledPostRideList.length,
                                      physics: const NeverScrollableScrollPhysics(),
                                      itemBuilder: (context, index){
                                        return postRideAgainCardWidget(
                                            cardBgColor: index%2 == 0 ? Colors.white : Colors.grey.shade100,
                                            context: context,
                                            screenWidth: context.screenWidth,
                                            fromLabel: "${controller.labelTextDetail['from_label'] ?? "From"}",
                                            toLabel: "${controller.labelTextDetail['to_label'] ?? "To"}",
                                            fromText: controller.cancelledPostRideList[index]['ride_detail'][0]['departure'],
                                            toText: controller.cancelledPostRideList[index]['ride_detail'][0]['destination'],
                                            onTap: (){
                                              Get.offNamed("/post_ride/${controller.cancelledPostRideList[index]['id']}/new");
                                            }
                                        );
                                      },
                                      separatorBuilder: (context, index){
                                        return const SizedBox();
                                      },
                                    ),
                                    10.heightBox,
                                    if(controller.cancelledEnd.value != "")...[
                                      Center(
                                        child: txt16Size(title: controller.cancelledEnd.value, context: context),
                                      )
                                    ],
                                    if(controller.isScrollCancelledLoading.value == true)...[
                                      Center(child: progressCircularWidget(context)),
                                      10.heightBox,
                                    ]
                                  ],
                                ),
                              ): Center(child: txt20Size(context: context, title: "${controller.labelTextDetail['cancelled_ride_no_found_message'] ?? "No cancelled ride found"}")),
                            ],
                            onPageChanged: (index) async{
                              await controller.changeTabView(index);
                            },
                          )
                      ),
                    ],
                  ),
                ),
                if(controller.isOverlayLoading.value == true)...[
                  overlayWidget(context)
                ]
              ],
            );
          }
        })
      ),
    );
  }
}

