import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride_again/PostRideAgainController.dart';
import 'package:proximaride_app/pages/widgets/post_ride_again_card_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

/// Segmented tab bar styling (light gray track, teal selected pill).
const Color _postRideAgainTabTrackColor = Color(0xFFEBEBEB);
const Color _postRideAgainTabSelectedColor = Color(0xFF00A896);

class PostRideAgainPage extends StatelessWidget {
  const PostRideAgainPage({super.key});

  @override
  Widget build(BuildContext context) {
    final PostRideAgainController controller = Get.isRegistered<PostRideAgainController>()
        ? Get.find<PostRideAgainController>()
        : Get.put(PostRideAgainController());
    return Scaffold(
          appBar: AppBar(
            backgroundColor: primaryColor,
            title: Obx(() => secondAppBarWidget(
                title:
                    "${controller.labelTextDetail['post_ride_again_main_heading'] ?? "Post ride again"}",
                context: context)),
            leading: safeBackButton(
              context,
              authenticatedFallbackRoute: '/post_ride/0/new',
            ),
          ),
          body: SafeArea(
            child: Obx(() {
              if (controller.isLoading.value == true) {
                return Center(child: progressCircularWidget(context));
              } else {
                return Stack(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.symmetric(
                                horizontal: 14, vertical: 12),
                            decoration: BoxDecoration(
                              color: const Color(0xFFEFF6FF),
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(
                                color: const Color(0xFFBFDBFE),
                                width: 1,
                              ),
                            ),
                            child: Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Icon(
                                  Icons.info_outline_rounded,
                                  color: primaryColor,
                                  size: 22,
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: txt16Size(
                                    title:
                                        "Select the ride you want to repost.",
                                    context: context,
                                    fontFamily: regular,
                                    textColor: textColor,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          10.heightBox,
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.all(4),
                            decoration: BoxDecoration(
                              color: _postRideAgainTabTrackColor,
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: TabBar(
                              onTap: (index) async {
                                await controller.getTabIndex(index);
                              },
                              controller: controller.tabController,
                              dividerColor: Colors.transparent,
                              dividerHeight: 0,
                              indicator: BoxDecoration(
                                color: _postRideAgainTabSelectedColor,
                                borderRadius: BorderRadius.circular(8),
                              ),
                              indicatorSize: TabBarIndicatorSize.tab,
                              labelColor: Colors.white,
                              unselectedLabelColor: textColor,
                              labelStyle: const TextStyle(
                                fontFamily: bold,
                                fontSize: 17,
                                fontWeight: FontWeight.w600,
                              ),
                              unselectedLabelStyle: const TextStyle(
                                fontFamily: regular,
                                fontSize: 17,
                                fontWeight: FontWeight.w500,
                              ),
                              splashFactory: NoSplash.splashFactory,
                              overlayColor:
                                  WidgetStateProperty.all(Colors.transparent),
                              // labelPadding: const EdgeInsets.symmetric(
                              //     horizontal: 8, vertical: 0),
                              tabs: [
                                Tab(
                                  text:
                                      "${controller.labelTextDetail['upcoming_label'] ?? "Upcoming"}",
                                ),
                                Tab(
                                  text:
                                      "${controller.labelTextDetail['completed_label'] ?? "Completed"}",
                                ),
                                Tab(
                                  text:
                                      "${controller.labelTextDetail['cancelled_label'] ?? "Cancelled"}",
                                ),
                              ],
                            ),
                          ),
                          10.heightBox,
                          Expanded(
                              child: PageView(
                            controller: controller.pageController,
                            children: [
                              controller.upcomingPostRideList.isNotEmpty
                                  ? SingleChildScrollView(
                                      controller: controller.scrollController,
                                      child: Column(
                                        children: [
                                          ListView.separated(
                                            shrinkWrap: true,
                                            itemCount: controller
                                                .upcomingPostRideList.length,
                                            physics:
                                                const NeverScrollableScrollPhysics(),
                                            itemBuilder: (context, index) {
                                              return postRideAgainCardWidget(
                                                  cardBgColor: index % 2 == 0
                                                      ? Colors.white
                                                      : Colors.grey.shade100,
                                                  context: context,
                                                  screenWidth:
                                                      context.screenWidth,
                                                  fromLabel:
                                                      "${controller.labelTextDetail['from_label'] ?? "From"}",
                                                  toLabel:
                                                      "${controller.labelTextDetail['to_label'] ?? "To"}",
                                                  depatureAt: controller
                                                      .upcomingPostRideList[index]['ride_detail']['date']
                                                      .toString(),
                                                  fromText: controller
                                                      .upcomingPostRideList[index]['ride_detail']['departure']
                                                      .toString(),
                                                  toText: controller
                                                      .upcomingPostRideList[index]['ride_detail']['destination']
                                                      .toString(),
                                                  onTap: () {
                                                    Get.toNamed(
                                                        "/post_ride/${controller.upcomingPostRideList[index]['id']}/new");
                                                  });
                                            },
                                            separatorBuilder: (context, index) {
                                              return const SizedBox();
                                            },
                                          ),
                                          10.heightBox,
                                          if (controller.upcomingEnd.value !=
                                              "") ...[
                                            Center(
                                              child: txt20Size(
                                                  title: controller
                                                      .upcomingEnd.value,
                                                  context: context),
                                            )
                                          ],
                                          if (controller.isScrollUpcomingLoading
                                                  .value ==
                                              true) ...[
                                            Center(
                                                child: progressCircularWidget(
                                                    context)),
                                            10.heightBox,
                                          ]
                                        ],
                                      ),
                                    )
                                  : Center(
                                      child: txt20Size(
                                          context: context,
                                          title:
                                              "${controller.labelTextDetail['upcoming_ride_no_found_message'] ?? "No upcoming ride found"}")),
                              controller.completedPostRideList.isNotEmpty
                                  ? SingleChildScrollView(
                                      controller: controller.scrollController,
                                      child: Column(
                                        children: [
                                          ListView.separated(
                                            shrinkWrap: true,
                                            itemCount: controller
                                                .completedPostRideList.length,
                                            physics:
                                                const NeverScrollableScrollPhysics(),
                                            itemBuilder: (context, index) {
                                              return postRideAgainCardWidget(
                                                  cardBgColor: index % 2 == 0
                                                      ? Colors.white
                                                      : Colors.grey.shade100,
                                                  context: context,
                                                  screenWidth:
                                                      context.screenWidth,
                                                  fromLabel:
                                                      "${controller.labelTextDetail['from_label'] ?? "From"}",
                                                  toLabel:
                                                      "${controller.labelTextDetail['to_label'] ?? "To"}",
                                                  depatureAt: "",
                                                  fromText:
                                                      "${controller.completedPostRideList[index]['ride_detail']['departure']}",
                                                  toText: controller
                                                              .completedPostRideList[
                                                          index]['ride_detail']['destination'],
                                                  onTap: () {
                                                    Get.toNamed(
                                                        "/post_ride/${controller.completedPostRideList[index]['id']}/new");
                                                  });
                                            },
                                            separatorBuilder: (context, index) {
                                              return const SizedBox();
                                            },
                                          ),
                                          10.heightBox,
                                          if (controller.completedEnd.value !=
                                              "") ...[
                                            Center(
                                              child: txt20Size(
                                                  title: controller
                                                      .completedEnd.value,
                                                  context: context),
                                            )
                                          ],
                                          if (controller
                                                  .isScrollCompletedLoading
                                                  .value ==
                                              true) ...[
                                            Center(
                                                child: progressCircularWidget(
                                                    context)),
                                            10.heightBox,
                                          ]
                                        ],
                                      ),
                                    )
                                  : Center(
                                      child: txt20Size(
                                          context: context,
                                          title:
                                              "${controller.labelTextDetail['completed_ride_no_found_message'] ?? "No completed ride found"}")),
                              controller.cancelledPostRideList.isNotEmpty
                                  ? SingleChildScrollView(
                                      controller: controller.scrollController,
                                      child: Column(
                                        children: [
                                          ListView.separated(
                                            shrinkWrap: true,
                                            itemCount: controller
                                                .cancelledPostRideList.length,
                                            physics:
                                                const NeverScrollableScrollPhysics(),
                                            itemBuilder: (context, index) {
                                              return postRideAgainCardWidget(
                                                  cardBgColor: index % 2 == 0
                                                      ? Colors.white
                                                      : Colors.grey.shade100,
                                                  context: context,
                                                  screenWidth:
                                                      context.screenWidth,
                                                  fromLabel:
                                                      "${controller.labelTextDetail['from_label'] ?? "From"}",
                                                  toLabel:
                                                      "${controller.labelTextDetail['to_label'] ?? "To"}",
                                                  depatureAt: "",
                                                  fromText: controller
                                                              .cancelledPostRideList[index]['ride_detail']['departure'],
                                                  toText: controller
                                                              .cancelledPostRideList[index]['ride_detail']['destination'],
                                                  onTap: () {
                                                    Get.toNamed(
                                                        "/post_ride/${controller.cancelledPostRideList[index]['id']}/new");
                                                  });
                                            },
                                            separatorBuilder: (context, index) {
                                              return const SizedBox();
                                            },
                                          ),
                                          10.heightBox,
                                          if (controller.cancelledEnd.value !=
                                              "") ...[
                                            Center(
                                              child: txt20Size(
                                                  title: controller
                                                      .cancelledEnd.value,
                                                  context: context),
                                            )
                                          ],
                                          if (controller
                                                  .isScrollCancelledLoading
                                                  .value ==
                                              true) ...[
                                            Center(
                                                child: progressCircularWidget(
                                                    context)),
                                            10.heightBox,
                                          ]
                                        ],
                                      ),
                                    )
                                  : Center(
                                      child: txt20Size(
                                          context: context,
                                          title:
                                              "${controller.labelTextDetail['cancelled_ride_no_found_message'] ?? "No cancelled ride found"}")),
                            ],
                            onPageChanged: (index) async {
                              await controller.changeTabView(index);
                            },
                          )),
                        ],
                      ),
                    ),
                    if (controller.isOverlayLoading.value == true) ...[
                      overlayWidget(context)
                    ]
                  ],
                );
              }
            }),
          ));
  }
}



