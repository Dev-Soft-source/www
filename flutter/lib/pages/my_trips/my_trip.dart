import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/pages/my_trips/widget/ride_card_widget.dart';
import 'package:proximaride_app/pages/my_trips/widget/trip_card_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/main_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class MyTripsPage extends StatelessWidget {
  const MyTripsPage({super.key});

  Widget _emptyState(BuildContext context, String imagePath, String title) {
    return Center(
      child: LayoutBuilder(
        builder: (context, constraints) {
          final imageHeight = (constraints.maxHeight * 0.72).clamp(120.0, 320.0);

          return SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            child: ConstrainedBox(
              constraints: BoxConstraints(minHeight: constraints.maxHeight),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Image.asset(imagePath, height: imageHeight),
                  txt16Size(title: title, context: context),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final MyTripController controller = Get.isRegistered<MyTripController>()
        ? Get.find<MyTripController>()
        : Get.put(MyTripController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => mainAppBarWidget(
            context,
            controller.serviceController.langId.value,
            controller.serviceController.langIcon.value,
            context.screenWidth,
            controller.serviceController)),
      ),
      body: SafeArea(
        child: Obx(() {
          if (controller.errorStateManager.isLoading.value) {
            return Center(child: progressCircularWidget(context));
          } else if (controller.errorStateManager.hasError.value) {
            return ErrorStateWidget(
              message: controller.errorStateManager.errorMessage.value,
              errorType: controller.errorStateManager.errorType.value,
              onRetry: () {
                if (controller.errorStateManager.onRetry.value != null) {
                  controller.errorStateManager.onRetry.value!();
                }
              },
            );
          } else if (controller.isLoading.value == true) {
            return Center(child: progressCircularWidget(context));
          } else {
            return RefreshIndicator(
              onRefresh: () async {
                await controller.updateMyTrips();
              },
              child: Stack(
                children: [
                  Container(
                    padding: EdgeInsets.all(getValueForScreenType<double>(
                      context: context,
                      mobile: 15.0,
                      tablet: 15.0,
                    )),
                    child: Column(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10.0),
                          decoration: BoxDecoration(
                              color: Colors.grey.shade200,
                              borderRadius: BorderRadius.circular(5.0)),
                          child: TabBar(
                            controller: controller.tabController,
                            onTap: (index) async {
                              await controller.updatePageIndexValue(index);
                              // controller.pageController.animateToPage(index, duration: const Duration(milliseconds: 1), curve: Curves.linear);
                              // logger.info("value ${controller.pageController.initialPage}");
                              if (index == 0) {
                                controller.tripTabController.index = 0;
                              } else {
                                controller.rideTabController.index = 0;
                              }
                            },
                            indicatorColor: primaryColor,
                            indicatorSize: TabBarIndicatorSize.tab,
                            dividerColor: Colors.transparent,
                            labelColor: Colors.white,
                            unselectedLabelColor: textColor,
                            // Top-level tab titles: treat as main section titles -> 20px
                            labelStyle: const TextStyle(
                                fontFamily: regular, fontSize: 20),
                            unselectedLabelStyle: const TextStyle(
                                fontFamily: regular, fontSize: 20),
                            indicator: BoxDecoration(
                                borderRadius:
                                    BorderRadius.circular(5), // Creates border
                                color: btnPrimaryColor),
                            labelPadding: const EdgeInsets.all(5.0),
                            tabs: [
                              Text(
                                  "${controller.labelTextTripDetail['passenger_trips_heading'] ?? "Passenger trips"}"),
                              Text(
                                  "${controller.labelTextTripDetail['driver_rides_heading'] ?? "Driver rides"}")
                            ],
                          ),
                        ),
                        10.heightBox,
                        Expanded(
                            child: TabBarView(
                          controller: controller.tabController,
                          children: [
                            Column(
                              children: [
                                TabBar(
                                    controller: controller.tripTabController,
                                    onTap: (index) async {
                                      controller.tripPageController
                                          .animateToPage(index,
                                              duration: const Duration(
                                                  milliseconds: 2),
                                              curve: Curves.linear);
                                    },
                                    indicatorColor: primaryColor,
                                    indicatorSize: TabBarIndicatorSize.label,
                                    labelColor: primaryColor,
                                    unselectedLabelColor: textColor,
                                    labelStyle: const TextStyle(
                                        fontFamily: regular, fontSize: 20),
                                    unselectedLabelStyle: const TextStyle(
                                        fontFamily: regular, fontSize: 20),
                                    labelPadding: const EdgeInsets.all(5.0),
                                    tabs: [
                                      Text(
                                          "${controller.labelTextTripDetail['upcoming_label'] ?? "Upcoming"}"),
                                      Text(
                                          "${controller.labelTextTripDetail['completed_label'] ?? "Completed"}"),
                                      Text(
                                          "${controller.labelTextTripDetail['cancelled_label'] ?? "Cancelled"}"),
                                    ]),
                                Expanded(
                                    child: PageView(
                                  controller: controller.tripPageController,
                                  children: [
                                    controller.upComingTripList.isNotEmpty
                                        ? SingleChildScrollView(
                                            controller: controller
                                                .upComingTripScrollController,
                                            physics:
                                                const AlwaysScrollableScrollPhysics(),
                                            child: Column(
                                              children: [
                                                ListView.separated(
                                                    itemCount: controller
                                                        .upComingTripList
                                                        .length,
                                                    shrinkWrap: true,
                                                    physics:
                                                        const NeverScrollableScrollPhysics(),
                                                    itemBuilder:
                                                        (context, index) {
                                                      return tripCardWidget(
                                                          context: context,
                                                          controller:
                                                              controller,
                                                          tripDetail:
                                                              controller.upComingTripList[index]
                                                                  ['ride'],
                                                          bookSeat: controller
                                                              .upComingTripList[index]
                                                                  ['seats']
                                                              .toString(),
                                                          rating: null,
                                                          bookingDeparture: controller
                                                                  .upComingTripList[index]
                                                              ['departure'],
                                                          bookingDestination: controller
                                                                  .upComingTripList[index]
                                                              ['destination'],
                                                          bookingPrice:
                                                              controller.upComingTripList[index]
                                                                  ['price'],
                                                          tripStatus:
                                                              "upcoming",
                                                          cardBgColor: index % 2 != 0
                                                              ? Colors.grey.shade100
                                                              : Colors.white,
                                                          onTapTripCard: () async {
                                                            await controller
                                                                .getTripDetail(
                                                                    controller.upComingTripList[
                                                                            index]
                                                                        ['id']);
                                                          });
                                                    },
                                                    separatorBuilder:
                                                        (context, index) {
                                                      return const SizedBox();
                                                    }),
                                                if (controller
                                                        .upComingTripNoMoreData
                                                        .value ==
                                                    true) ...[
                                                  30.heightBox,
                                                  Center(
                                                      child: txt20Size(
                                                          title:
                                                              "${controller.labelTextTripDetail['no_more_data_message'] ?? "No more data"}",
                                                          context: context)),
                                                  30.heightBox,
                                                ] else ...[
                                                  30.heightBox,
                                                  if (controller
                                                          .upComingTripLoadMore
                                                          .value ==
                                                      true) ...[
                                                    progressCircularWidget(
                                                        context,
                                                        width: 50.0,
                                                        height: 50.0),
                                                  ] else ...[
                                                    controller.upComingTripTotal
                                                                .value >
                                                            5
                                                        ? elevatedButtonWidget(
                                                            textWidget: txt18Size(
                                                                title:
                                                                    "${controller.labelTextTripDetail['load_more_trips_label'] ?? "Load more trips"}",
                                                                context:
                                                                    context,
                                                                fontFamily:
                                                                    regular,
                                                                textColor:
                                                                    Colors
                                                                        .white),
                                                            context: context,
                                                            onPressed:
                                                                () async {
                                                              await controller
                                                                  .loadMoreTrips(
                                                                      'upcoming');
                                                            })
                                                        : SizedBox(),
                                                  ],
                                                  30.heightBox,
                                                ],
                                              ],
                                            ),
                                          )
                                        : _emptyState(
                                            context,
                                            noUpcomingTrips,
                                            "${controller.labelTextTripDetail['no_upcoming_trips_label'] ?? "You have no upcoming trips"}",
                                          ),
                                    controller.completedTripList.isNotEmpty
                                        ? SingleChildScrollView(
                                            controller: controller
                                                .completedTripScrollController,
                                            physics:
                                                const AlwaysScrollableScrollPhysics(),
                                            child: Column(
                                              children: [
                                                ListView.separated(
                                                    itemCount: controller
                                                        .completedTripList
                                                        .length,
                                                    shrinkWrap: true,
                                                    physics:
                                                        const NeverScrollableScrollPhysics(),
                                                    itemBuilder:
                                                        (context, index) {
                                                      return tripCardWidget(
                                                        context: context,
                                                        controller: controller,
                                                        tripDetail: controller
                                                                .completedTripList[
                                                            index]['ride'],
                                                        bookSeat: controller
                                                            .completedTripList[
                                                                index]['seats']
                                                            .toString(),
                                                        rating: controller
                                                                .completedTripList[
                                                            index]['rating'],
                                                        bookingDeparture: controller
                                                                .completedTripList[
                                                            index]['departure'],
                                                        bookingDestination:
                                                            controller.completedTripList[
                                                                    index]
                                                                ['destination'],
                                                        bookingPrice: controller
                                                                .completedTripList[
                                                            index]['price'],
                                                        tripStatus: "completed",
                                                        cardBgColor:
                                                            index % 2 != 0
                                                                ? Colors.grey
                                                                    .shade100
                                                                : Colors.white,
                                                        onTapTripCard:
                                                            () async {
                                                          final query = Uri(
                                                                  queryParameters: {
                                                                'from': (controller.completedTripList[index]['departure'] ??
                                                                        '')
                                                                    .toString(),
                                                                'to': (controller.completedTripList[index]['destination'] ??
                                                                        '')
                                                                    .toString(),
                                                                'price': (controller.completedTripList[index]['price'] ??
                                                                        '')
                                                                    .toString(),
                                                              })
                                                              .query;
                                                          Get.toNamed(
                                                              '/trip_detail/${controller.completedTripList[index]['ride']['id']}/trip/completed/${controller.completedTripList[index]['ride_detail_id']}?$query');
                                                        },
                                                        leaveReviewDays:
                                                            int.parse(controller
                                                                    .tripSettings[
                                                                'leave_review_days']),
                                                      );
                                                    },
                                                    separatorBuilder:
                                                        (context, index) {
                                                      return const SizedBox();
                                                    }),
                                                if (controller
                                                        .completedTripNoMoreData
                                                        .value ==
                                                    true) ...[
                                                  30.heightBox,
                                                  Center(
                                                      child: txt20Size(
                                                          title:
                                                              "${controller.labelTextTripDetail['no_more_data_message'] ?? "No more data"}",
                                                          context: context)),
                                                  30.heightBox,
                                                ] else ...[
                                                  30.heightBox,
                                                  if (controller
                                                          .completedTripLoadMore
                                                          .value ==
                                                      true) ...[
                                                    progressCircularWidget(
                                                        context,
                                                        width: 50.0,
                                                        height: 50.0),
                                                  ] else ...[
                                                    controller.completedTripTotal
                                                                .value >
                                                            5
                                                        ? elevatedButtonWidget(
                                                            textWidget: txt18Size(
                                                                title:
                                                                    "${controller.labelTextTripDetail['load_more_trips_label'] ?? "Load more trips"}",
                                                                context:
                                                                    context,
                                                                fontFamily:
                                                                    regular,
                                                                textColor:
                                                                    Colors
                                                                        .white),
                                                            context: context,
                                                            onPressed:
                                                                () async {
                                                              await controller
                                                                  .loadMoreTrips(
                                                                      'completed');
                                                            })
                                                        : SizedBox(),
                                                  ],
                                                  30.heightBox,
                                                ],
                                              ],
                                            ),
                                          )
                                        : _emptyState(
                                            context,
                                            noCompletedTrips,
                                            "${controller.labelTextTripDetail['no_completed_trips_label'] ?? "You have no completed trips"}",
                                          ),
                                    controller.cancelledTripList.isNotEmpty
                                        ? SingleChildScrollView(
                                            controller: controller
                                                .cancelledTripScrollController,
                                            physics:
                                                const AlwaysScrollableScrollPhysics(),
                                            child: Column(
                                              children: [
                                                ListView.separated(
                                                    itemCount: controller
                                                        .cancelledTripList
                                                        .length,
                                                    shrinkWrap: true,
                                                    physics:
                                                        const NeverScrollableScrollPhysics(),
                                                    itemBuilder:
                                                        (context, index) {
                                                      return tripCardWidget(
                                                          context: context,
                                                          controller:
                                                              controller,
                                                          tripDetail:
                                                              controller.cancelledTripList[index]
                                                                  ['ride'],
                                                          bookSeat: controller
                                                              .cancelledTripList[index]
                                                                  ['seats']
                                                              .toString(),
                                                          rating: null,
                                                          bookingDeparture: controller
                                                                  .cancelledTripList[index]
                                                              ['departure'],
                                                          bookingDestination: controller
                                                                  .cancelledTripList[index]
                                                              ['destination'],
                                                          bookingPrice:
                                                              controller.cancelledTripList[index]
                                                                  ['price'],
                                                          tripStatus:
                                                              "cancelled",
                                                          cardBgColor: index % 2 != 0
                                                              ? Colors.grey.shade100
                                                              : Colors.white,
                                                          onTapTripCard: () async {
                                                            final query = Uri(
                                                                    queryParameters: {
                                                                  'from': (controller.cancelledTripList[index]['departure'] ??
                                                                          '')
                                                                      .toString(),
                                                                  'to': (controller.cancelledTripList[index]['destination'] ??
                                                                          '')
                                                                      .toString(),
                                                                  'price': (controller.cancelledTripList[index]['price'] ??
                                                                          '')
                                                                      .toString(),
                                                                })
                                                                .query;
                                                            Get.toNamed(
                                                                '/trip_detail/${controller.cancelledTripList[index]['ride']['id']}/trip/cancelled/${controller.cancelledTripList[index]['ride_detail_id']}?$query');
                                                          });
                                                    },
                                                    separatorBuilder:
                                                        (context, index) {
                                                      return const SizedBox();
                                                    }),
                                                if (controller
                                                        .cancelledTripNoMoreData
                                                        .value ==
                                                    true) ...[
                                                  30.heightBox,
                                                  Center(
                                                      child: txt20Size(
                                                          title:
                                                              "${controller.labelTextTripDetail['no_more_data_message'] ?? "No more data"}",
                                                          context: context)),
                                                  30.heightBox,
                                                ] else ...[
                                                  30.heightBox,
                                                  if (controller
                                                          .cancelledTripLoadMore
                                                          .value ==
                                                      true) ...[
                                                    progressCircularWidget(
                                                        context,
                                                        width: 50.0,
                                                        height: 50.0),
                                                  ] else ...[
                                                    controller.cancelledTripTotal
                                                                .value >
                                                            5
                                                        ? elevatedButtonWidget(
                                                            textWidget: txt18Size(
                                                                title:
                                                                    "${controller.labelTextTripDetail['load_more_trips_label'] ?? "Load more trips"}",
                                                                context:
                                                                    context,
                                                                fontFamily:
                                                                    regular,
                                                                textColor:
                                                                    Colors
                                                                        .white),
                                                            context: context,
                                                            onPressed:
                                                                () async {
                                                              await controller
                                                                  .loadMoreTrips(
                                                                      'cancelled');
                                                            })
                                                        : SizedBox(),
                                                  ],
                                                  30.heightBox,
                                                ],
                                              ],
                                            ),
                                          )
                                        : _emptyState(
                                            context,
                                            noCancelledTrips,
                                            "${controller.labelTextTripDetail['no_cancelled_trips_label'] ?? "You have no cancelled trips"}",
                                          ),
                                  ],
                                  onPageChanged: (index) async {
                                    controller.tripTabController.index = index;
                                    await controller.updateTripPageValue(index);
                                  },
                                ))
                              ],
                            ),
                            Column(
                              children: [
                                TabBar(
                                    controller: controller.rideTabController,
                                    onTap: (index) async {
                                      controller.ridePageController
                                          .animateToPage(index,
                                              duration: const Duration(
                                                  milliseconds: 2),
                                              curve: Curves.linear);
                                    },
                                    indicatorColor: primaryColor,
                                    indicatorSize: TabBarIndicatorSize.label,
                                    labelColor: primaryColor,
                                    unselectedLabelColor: textColor,
                                    labelStyle: const TextStyle(
                                        fontFamily: regular, fontSize: 20),
                                    unselectedLabelStyle: const TextStyle(
                                        fontFamily: regular, fontSize: 20),
                                    labelPadding: const EdgeInsets.all(5.0),
                                    tabs: [
                                      Text(
                                          "${controller.labelTextTripDetail['upcoming_label'] ?? "Upcoming"}"),
                                      Text(
                                          "${controller.labelTextTripDetail['completed_label'] ?? "Completed"}"),
                                      Text(
                                          "${controller.labelTextTripDetail['cancelled_label'] ?? "Cancelled"}"),
                                    ]),
                                Expanded(
                                    child: PageView(
                                  controller: controller.ridePageController,
                                  children: [
                                    controller.upComingRideList.isNotEmpty
                                        ? SingleChildScrollView(
                                            controller: controller
                                                .upComingRideScrollController,
                                            physics:
                                                const AlwaysScrollableScrollPhysics(),
                                            child: Column(
                                              children: [
                                                ListView.separated(
                                                    itemCount: controller
                                                        .upComingRideList
                                                        .length,
                                                    shrinkWrap: true,
                                                    physics:
                                                        const NeverScrollableScrollPhysics(),
                                                    itemBuilder:
                                                        (context, index) {
                                                      return rideCardWidget(
                                                          context: context,
                                                          controller:
                                                              controller,
                                                          tripDetail: controller
                                                                  .upComingRideList[
                                                              index],
                                                          tripStatus:
                                                              "upcoming",
                                                          cardBgColor:
                                                              index % 2 != 0
                                                                  ? Colors.grey
                                                                      .shade100
                                                                  : Colors
                                                                      .white,
                                                          onTapRideCard:
                                                              () async {
                                                            await controller
                                                                .getRideDetail(
                                                                    controller.upComingRideList[
                                                                            index]
                                                                        ['id']);
                                                          },
                                                          onTapReviewPassenger:
                                                              () {
                                                            Get.toNamed(
                                                                "/my_passenger/${controller.upComingRideList[index]['id']}");
                                                          });
                                                    },
                                                    separatorBuilder:
                                                        (context, index) {
                                                      return const SizedBox();
                                                    }),
                                                if (controller
                                                        .upComingRideNoMoreData
                                                        .value ==
                                                    true) ...[
                                                  30.heightBox,
                                                  Center(
                                                      child: txt20Size(
                                                          title:
                                                              "${controller.labelTextTripDetail['no_more_data_message'] ?? "No more data"}",
                                                          context: context)),
                                                  30.heightBox,
                                                ] else ...[
                                                  30.heightBox,
                                                  if (controller
                                                          .upComingRideLoadMore
                                                          .value ==
                                                      true) ...[
                                                    progressCircularWidget(
                                                        context,
                                                        width: 50.0,
                                                        height: 50.0),
                                                  ] else ...[
                                                    controller.upComingRideTotal
                                                                .value >
                                                            5
                                                        ? elevatedButtonWidget(
                                                            textWidget: txt18Size(
                                                                title:
                                                                    "${controller.labelTextTripDetail['load_more_rides_label'] ?? "Load more rides"}",
                                                                context:
                                                                    context,
                                                                fontFamily:
                                                                    regular,
                                                                textColor:
                                                                    Colors
                                                                        .white),
                                                            context: context,
                                                            onPressed:
                                                                () async {
                                                              await controller
                                                                  .loadMoreRides(
                                                                      'upcoming');
                                                            })
                                                        : SizedBox(),
                                                  ],
                                                  30.heightBox,
                                                ],
                                              ],
                                            ),
                                          )
                                        : _emptyState(
                                            context,
                                            noUpcomingRides,
                                            "${controller.labelTextTripDetail['no_upcoming_rides_label'] ?? "You have no upcoming rides"}",
                                          ),
                                    controller.completedRideList.isNotEmpty
                                        ? SingleChildScrollView(
                                            controller: controller
                                                .completedRideScrollController,
                                            physics:
                                                const AlwaysScrollableScrollPhysics(),
                                            child: Column(
                                              children: [
                                                ListView.separated(
                                                    itemCount: controller
                                                        .completedRideList
                                                        .length,
                                                    shrinkWrap: true,
                                                    physics:
                                                        const NeverScrollableScrollPhysics(),
                                                    itemBuilder:
                                                        (context, index) {
                                                      return rideCardWidget(
                                                          context: context,
                                                          controller:
                                                              controller,
                                                          tripDetail: controller
                                                                  .completedRideList[
                                                              index],
                                                          tripStatus:
                                                              "completed",
                                                          cardBgColor:
                                                              index % 2 != 0
                                                                  ? Colors.grey
                                                                      .shade100
                                                                  : Colors
                                                                      .white,
                                                          onTapRideCard: () {
                                                            Get.toNamed(
                                                                '/trip_detail/${controller.completedRideList[index]['id']}/ride/completed/0');
                                                          },
                                                          onTapReviewPassenger:
                                                              () async {
                                                            await controller
                                                                .getCompletedRideData(
                                                                    controller.completedRideList[
                                                                            index]
                                                                        ['id']);
                                                          });
                                                    },
                                                    separatorBuilder:
                                                        (context, index) {
                                                      return const SizedBox();
                                                    }),
                                                if (controller
                                                        .completedRideNoMoreData
                                                        .value ==
                                                    true) ...[
                                                  30.heightBox,
                                                  Center(
                                                      child: txt20Size(
                                                          title:
                                                              "${controller.labelTextTripDetail['no_more_data_message'] ?? "No more data"}",
                                                          context: context)),
                                                  30.heightBox,
                                                ] else ...[
                                                  30.heightBox,
                                                  if (controller
                                                          .completedRideLoadMore
                                                          .value ==
                                                      true) ...[
                                                    progressCircularWidget(
                                                        context,
                                                        width: 50.0,
                                                        height: 50.0),
                                                  ] else ...[
                                                    controller.completedRideTotal
                                                                .value >
                                                            5
                                                        ? elevatedButtonWidget(
                                                            textWidget: txt18Size(
                                                                title:
                                                                    "${controller.labelTextTripDetail['load_more_rides_label'] ?? "Load more rides"}",
                                                                context:
                                                                    context,
                                                                fontFamily:
                                                                    regular,
                                                                textColor:
                                                                    Colors
                                                                        .white),
                                                            context: context,
                                                            onPressed:
                                                                () async {
                                                              await controller
                                                                  .loadMoreRides(
                                                                      'completed');
                                                            })
                                                        : SizedBox(),
                                                  ],
                                                  30.heightBox,
                                                ],
                                              ],
                                            ),
                                          )
                                        : _emptyState(
                                            context,
                                            noCompletedRides,
                                            "${controller.labelTextTripDetail['no_completed_rides_label'] ?? "You have no completed rides"}",
                                          ),
                                    controller.cancelledRideList.isNotEmpty
                                        ? SingleChildScrollView(
                                            controller: controller
                                                .cancelledRideScrollController,
                                            physics:
                                                const AlwaysScrollableScrollPhysics(),
                                            child: Column(
                                              children: [
                                                ListView.separated(
                                                    itemCount: controller
                                                        .cancelledRideList
                                                        .length,
                                                    shrinkWrap: true,
                                                    physics:
                                                        const NeverScrollableScrollPhysics(),
                                                    itemBuilder:
                                                        (context, index) {
                                                      return rideCardWidget(
                                                          context: context,
                                                          controller:
                                                              controller,
                                                          tripDetail: controller
                                                                  .cancelledRideList[
                                                              index],
                                                          tripStatus:
                                                              "cancelled",
                                                          cardBgColor:
                                                              index % 2 != 0
                                                                  ? Colors.grey
                                                                      .shade100
                                                                  : Colors
                                                                      .white,
                                                          onTapRideCard: () {
                                                            Get.toNamed(
                                                                '/trip_detail/${controller.cancelledRideList[index]['id']}/ride/cancelled/0');
                                                          });
                                                    },
                                                    separatorBuilder:
                                                        (context, index) {
                                                      return const SizedBox();
                                                    }),
                                                if (controller
                                                        .cancelledRideNoMoreData
                                                        .value ==
                                                    true) ...[
                                                  30.heightBox,
                                                  Center(
                                                      child: txt20Size(
                                                          title:
                                                              "${controller.labelTextTripDetail['no_more_data_message'] ?? "No more data"}",
                                                          context: context)),
                                                  30.heightBox,
                                                ] else ...[
                                                  30.heightBox,
                                                  if (controller
                                                          .cancelledRideLoadMore
                                                          .value ==
                                                      true) ...[
                                                    progressCircularWidget(
                                                        context,
                                                        width: 50.0,
                                                        height: 50.0),
                                                  ] else ...[
                                                    controller.cancelledRideTotal
                                                                .value >
                                                            5
                                                        ? elevatedButtonWidget(
                                                            textWidget: txt18Size(
                                                                title:
                                                                    "${controller.labelTextTripDetail['load_more_rides_label'] ?? "Load more rides"}",
                                                                context:
                                                                    context,
                                                                fontFamily:
                                                                    regular,
                                                                textColor:
                                                                    Colors
                                                                        .white),
                                                            context: context,
                                                            onPressed:
                                                                () async {
                                                              await controller
                                                                  .loadMoreRides(
                                                                      'cancelled');
                                                            })
                                                        : SizedBox(),
                                                  ],
                                                  30.heightBox,
                                                ],
                                              ],
                                            ),
                                          )
                                        : _emptyState(
                                            context,
                                            noCancelledRides,
                                            "${controller.labelTextTripDetail['no_cancelled_rides_label'] ?? "You have no cancelled rides"}",
                                          ),
                                  ],
                                  onPageChanged: (index) async {
                                    controller.rideTabController.index = index;
                                    await controller.updateRidePageValue(index);
                                  },
                                ))
                              ],
                            )
                          ],
                          // onPageChanged: (index) async{
                          //   controller.tabController.index = index;
                          //   await controller.updatePageIndexValue(index);
                          // },
                        ))
                      ],
                    ),
                  ),
                  if (controller.isOverlayLoading.value == true) ...[
                    overlayWidget(context)
                  ]
                ],
              ),
            );
          }
        }),
      ),
    );
  }
}


