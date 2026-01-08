import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/balance_card.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/driver_available_widget.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/driver_paid_out_widget.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/driver_pending_widget.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/my_balance_widget.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/my_ride_widget.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/table_row_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';
import '../widgets/button_Widget.dart';
import '../widgets/second_appbar_widget.dart';
import 'MyWalletController.dart';

class MyWallet extends GetView<MyWalletController> {
  const MyWallet({super.key});

  @override
  Widget build(BuildContext context) {
    Get.put(MyWalletController());
    return Scaffold(
      appBar: AppBar(
        leading: const BackButton(color: Colors.white),
        title: Obx(() => secondAppBarWidget(context: context, title: "${controller.labelTextDetail['main_heading'] ?? "My wallet"}")),
        backgroundColor: primaryColor,
      ),
      body: Obx(() =>
        controller.isLoading.value == true ?
        Center(child: progressCircularWidget(context)) :
        Stack(
          children: [
            Container(
              padding: const EdgeInsets.all(15.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  balanceCard(context: context, balance: (controller.balance.value).toStringAsFixed(0), width: context.width, balanceLabel: "${controller.labelTextDetail['card_heading'] ?? "Your balance"}"),
                  10.heightBox,
                  Expanded(
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
                              fontSize: 22,
                            ),
                            unselectedLabelStyle: const TextStyle(
                                fontFamily: regular,
                                fontSize: 22
                            ),
                            labelPadding: const EdgeInsets.all(5.0),
                            tabs:[
                              Text("${controller.labelTextDetail['passenger_heading'] ?? "I'm a passenger"}", textAlign: TextAlign.center),
                              Text("${controller.labelTextDetail['driver_heading'] ?? "I'm a driver"}", textAlign: TextAlign.center),
                            ],
                          ),
                          10.heightBox,
                          Expanded(
                              child: PageView(
                                controller: controller.pageController,
                                children: [
                                  Column(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.all(10.0),
                                        decoration: BoxDecoration(
                                            color: Colors.grey.shade200,
                                            borderRadius: BorderRadius.circular(5.0)
                                        ),
                                        child: TabBar(
                                          controller: controller.passengerTabController,
                                          onTap: (index){
                                            controller.passengerPageController.animateToPage(index, duration: const Duration(milliseconds: 1), curve: Curves.linear);
                                          },
                                          indicatorColor: btnPrimaryColor,
                                          indicatorSize: TabBarIndicatorSize.tab,
                                          dividerColor: Colors.transparent,
                                          labelColor: Colors.white,
                                          unselectedLabelColor: textColor,
                                          labelStyle: const TextStyle(
                                              fontFamily: regular,
                                              fontSize: 20
                                          ),
                                          unselectedLabelStyle: const TextStyle(
                                              fontFamily: regular,
                                              fontSize: 20
                                          ),
                                          indicator: BoxDecoration(
                                              borderRadius: BorderRadius.circular(5), // Creates border
                                              color: btnPrimaryColor
                                          ),
                                          labelPadding: const EdgeInsets.all(5.0),
                                          tabs:[
                                            Text("${controller.labelTextDetail['passenger_my_ride_heading'] ?? "My ride"}", textAlign: TextAlign.center,),
                                            Text("${controller.labelTextDetail['balance_heading'] ?? "My balance"}", textAlign: TextAlign.center),
                                            Text("${controller.labelTextDetail['passenger_my_reward_heading'] ?? "My reward"}", textAlign: TextAlign.center,)
                                          ],
                                        ),
                                      ),
                                      10.heightBox,
                                      Expanded(
                                          child: PageView(
                                            controller: controller.passengerPageController,
                                            children: [
                                              controller.passengerRideList.isNotEmpty ? SingleChildScrollView(
                                                controller: controller.passengerRideScrollController,
                                                physics: const AlwaysScrollableScrollPhysics(),
                                                child: Column(
                                                  children: [
                                                    ListView.separated(
                                                        itemCount: controller.passengerRideList.length, //controller.passengerRideList.length,
                                                        shrinkWrap: true,
                                                        physics: const NeverScrollableScrollPhysics(),
                                                        itemBuilder: (context, index){
                                                          return myRideWidget(context: context, myRide:controller.passengerRideList[index], controller: controller, changeColor: index % 2 == 0 ? true : false);
                                                        },
                                                        separatorBuilder: (context, index){
                                                          return 10.heightBox;
                                                        }
                                                    ),
                                                    if(controller.passengerRideNoMoreData.value == true)...[
                                                      30.heightBox,
                                                      Center(child: txt20Size(title: "${controller.labelTextDetail['no_more_data_message'] ?? "No more data"}", context: context)),
                                                      30.heightBox,
                                                    ],
                                                    if(controller.passengerRideLoadMore.value == true)...[
                                                      30.heightBox,
                                                      Center(child: progressCircularWidget(context)),
                                                      30.heightBox,
                                                    ]
                                                  ],
                                                ),
                                              ) : Center(child: txt20Size(title: "${controller.labelTextDetail['no_my_ride_message'] ?? "No my ride found"}", context: context)),
                                              Stack(
                                                children: [
                                                  controller.myBalanceList.isNotEmpty ? SingleChildScrollView(
                                                    controller: controller.myBalanceScrollController,
                                                    physics: const AlwaysScrollableScrollPhysics(),
                                                    child: Column(
                                                      children: [
                                                        ListView.separated(
                                                            itemCount: controller.myBalanceList.length, //controller.passengerRideList.length,
                                                            shrinkWrap: true,
                                                            physics: const NeverScrollableScrollPhysics(),
                                                            itemBuilder: (context, index){
                                                              return myBalanceWidget(context: context, data: controller.myBalanceList[index], controller: controller, changeColor: index % 2 == 0 ? true : false);
                                                            },
                                                            separatorBuilder: (context, index){
                                                              return 10.heightBox;
                                                            }
                                                        ),
                                                        if(controller.myBalanceNoMoreData.value == true)...[
                                                          30.heightBox,
                                                          Center(child: txt20Size(title: "${controller.labelTextDetail['no_more_data_message'] ?? "No more data"}", context: context)),
                                                          30.heightBox,
                                                        ],
                                                        if(controller.myBalanceLoadMore.value == true)...[
                                                          30.heightBox,
                                                          Center(child: progressCircularWidget(context)),
                                                          30.heightBox,
                                                        ],
                                                        80.heightBox,
                                                      ],
                                                    ),
                                                  ) : Center(child: txt20Size(title: "${controller.labelTextDetail['no_balance_found_message'] ?? "No balance found"}", context: context)),

                                                  Align(
                                                    alignment: Alignment.bottomCenter,
                                                    child: Container(
                                                      width: context.screenWidth,
                                                      color: Colors.grey.shade100,
                                                      height: 50,
                                                      child: elevatedButtonWidget(
                                                        textWidget: txt28Size(title: "${controller.labelTextDetail['balance_buy_more_button_text'] ?? "Buy more top up balance"}", textColor: Colors.white, context: context, fontFamily: regular),
                                                        onPressed: () async{
                                                          Get.toNamed('/top_up_balance');
                                                        },
                                                      ),
                                                    ),
                                                  ),
                                                ],
                                              ),
                                              controller.passengerRewardList.isNotEmpty ? Stack( children: [
                                                SingleChildScrollView(
                                                  controller: controller.passengerRewardScrollController,
                                                  physics: const AlwaysScrollableScrollPhysics(),
                                                  child: Column(
                                                    crossAxisAlignment: CrossAxisAlignment.start,
                                                    children: [
                                                      txt18Size(
                                                          title:
                                                          "${controller.labelTextDetail['passenger_my_reward_description'] ?? 'You have'} ${controller.passengerRewardPoints.value} ${controller.labelTextDetail['passenger_my_reward_description1'] ?? 'point as student'}",
                                                          context: context,
                                                          fontFamily: bold
                                                      ),
                                                      10.heightBox,
                                                      Table(
                                                        border: TableBorder.all(
                                                            color: Colors.grey.shade400,
                                                            style: BorderStyle.solid,
                                                            width: 1
                                                        ),

                                                        children: [
                                                          TableRow( children: [
                                                            Column(children:[txt20Size(context: context, title: "${controller.labelTextDetail['passenger_my_reward_points_table_label'] ?? "Points"}")]),
                                                            Column(children:[txt20Size(context: context, title: "${controller.labelTextDetail['passenger_my_reward_reward_table_label'] ?? "Rewards"}")]),
                                                          ]),
                                                          for(var index = 0; index < controller.passengerRewardList.length; index++)...[
                                                            tableRowWidget(context: context, cell1: "${controller.passengerRewardList[index]['reward_point_setting']['point']}", cell2: "${controller.passengerRewardList[index]['reward_name']}"),
                                                          ]
                                                        ],
                                                      ),
                                                    ],
                                                  ),
                                                ),
                                                Align(
                                                  alignment: Alignment.bottomCenter,
                                                  child: Container(
                                                    width: context.screenWidth,
                                                    color: Colors.grey.shade100,
                                                    height: 50,
                                                    child: elevatedButtonWidget(
                                                      textWidget: txt28Size(title: "${controller.labelTextDetail['claim_my_reward_button_text'] ?? "Claim my reward"}", textColor: Colors.white, context: context, fontFamily: regular),
                                                      onPressed: () async{
                                                        await controller.claimMyReward('student');
                                                      },
                                                    ),
                                                  ),
                                                ),
                                              ]) : Center(child: txt20Size(title: "${controller.labelTextDetail['no_reward_found_message'] ?? "No student reward found"}", context: context)),
                                              
                                            ],
                                            onPageChanged: (index) async{
                                              controller.passengerTabController.index = index;
                                              await controller.updatePassengerPageValue(index);
                                            },
                                          )
                                      )
                                    ],
                                  ),
                                  Column(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.all(10.0),
                                        decoration: BoxDecoration(
                                            color: Colors.grey.shade200,
                                            borderRadius: BorderRadius.circular(5.0)
                                        ),
                                        child: TabBar(
                                          controller: controller.driverTabController,
                                          onTap: (index){
                                            controller.driverPageController.animateToPage(index, duration: const Duration(milliseconds: 1), curve: Curves.linear);
                                          },
                                          indicatorColor: btnPrimaryColor,
                                          indicatorSize: TabBarIndicatorSize.tab,
                                          dividerColor: Colors.transparent,
                                          labelColor: Colors.white,
                                          unselectedLabelColor: textColor,
                                          labelStyle: const TextStyle(
                                              fontFamily: regular,
                                              fontSize: 20
                                          ),
                                          unselectedLabelStyle: const TextStyle(
                                              fontFamily: regular,
                                              fontSize: 20
                                          ),
                                          indicator: BoxDecoration(
                                              borderRadius: BorderRadius.circular(5), // Creates border
                                              color: btnPrimaryColor
                                          ),
                                          labelPadding: const EdgeInsets.all(5.0),
                                          tabs:[
                                            Text("${controller.labelTextDetail['driver_paid_out_heading'] ?? "Paid out"}", textAlign: TextAlign.center,),
                                            Text("${controller.labelTextDetail['driver_availabe_heading'] ?? "Available"}", textAlign: TextAlign.center,),
                                            Text("${controller.labelTextDetail['driver_pending_heading'] ?? "Pending"}", textAlign: TextAlign.center,),
                                            Text("${controller.labelTextDetail['driver_reward_heading'] ?? "Reward"}", textAlign: TextAlign.center,)
                                          ],
                                        ),
                                      ),
                                      10.heightBox,
                                      Expanded(
                                          child: PageView(
                                            controller: controller.driverPageController,
                                            children: [
                                              controller.driverPaidOutList.isNotEmpty ? SingleChildScrollView(
                                                controller: controller.driverPaidOutScrollController,
                                                physics: const AlwaysScrollableScrollPhysics(),
                                                child: Column(
                                                  children: [
                                                    ListView.separated(
                                                        itemCount: controller.driverPaidOutList.length, //controller.passengerRideList.length,
                                                        shrinkWrap: true,
                                                        physics: const NeverScrollableScrollPhysics(),
                                                        itemBuilder: (context, index){
                                                          return driverPaidOutWidget(context: context, data: controller.driverPaidOutList[index], controller: controller, changeColor: index % 2 == 0 ? true : false);
                                                        },
                                                        separatorBuilder: (context, index){
                                                          return 10.heightBox;
                                                        }
                                                    ),
                                                    if(controller.driverPaidOutNoMoreData.value == true)...[
                                                      30.heightBox,
                                                      Center(child: txt20Size(title: "${controller.labelTextDetail['no_more_data_message'] ?? "No more data"}", context: context)),
                                                      30.heightBox,
                                                    ],
                                                    if(controller.driverPaidOutLoadMore.value == true)...[
                                                      30.heightBox,
                                                      Center(child: progressCircularWidget(context)),
                                                      30.heightBox,
                                                    ]
                                                  ],
                                                ),
                                              ) : Center(child: txt20Size(title: "${controller.labelTextDetail['no_paid_out_message'] ?? "No paid out found"}", context: context)),
                                              Stack(
                                                children: [
                                                  controller.driverAvailableList.isNotEmpty ? SingleChildScrollView(
                                                    controller: controller.driverAvailableScrollController,
                                                    physics: const AlwaysScrollableScrollPhysics(),
                                                    child: Column(
                                                      children: [
                                                        ListView.separated(
                                                            itemCount: controller.driverAvailableList.length,
                                                            shrinkWrap: true,
                                                            physics: const NeverScrollableScrollPhysics(),
                                                            itemBuilder: (context, index){
                                                              return driverAvailableWidget(context: context, data: controller.driverAvailableList[index], controller: controller, changeColor: index % 2 == 0 ? true : false);
                                                            },
                                                            separatorBuilder: (context, index){
                                                              return 10.heightBox;
                                                            }
                                                        ),
                                                        if(controller.driverAvailableNoMoreData.value == true)...[
                                                          30.heightBox,
                                                          Center(child: txt20Size(title: "${controller.labelTextDetail['no_more_data_message'] ?? "No more data"}", context: context)),
                                                          30.heightBox,
                                                        ],
                                                        if(controller.driverAvailableLoadMore.value == true)...[
                                                          30.heightBox,
                                                          Center(child: progressCircularWidget(context)),
                                                          30.heightBox,
                                                        ],
                                                        controller.driverAvailableList.isNotEmpty ? 80.heightBox : 5.heightBox,
                                                      ],

                                                    ),
                                                  ) : Center(child: txt20Size(title: "${controller.labelTextDetail['no_balance_found_message'] ?? "No balance available found"}", context: context)),
                                                  controller.driverAvailableList.isNotEmpty ? Align(
                                                    alignment: Alignment.bottomCenter,
                                                    child: Container(
                                                      width: context.screenWidth,
                                                      color: Colors.grey.shade100,
                                                      height: 50,
                                                      child: elevatedButtonWidget(
                                                          textWidget: txt28Size(title: "${controller.labelTextDetail['request_transfer_label'] ?? "Request for transfer"}", textColor: Colors.white, context: context, fontFamily: regular),
                                                          onPressed: () async{
                                                            await controller.sendPayoutRequest();
                                                          }
                                                      ),
                                                    ),
                                                  ) : const SizedBox(),

                                                ],
                                              ),
                                              controller.driverPendingList.isNotEmpty ? SingleChildScrollView(
                                                controller: controller.driverPendingScrollController,
                                                physics: const AlwaysScrollableScrollPhysics(),
                                                child: Column(
                                                  children: [
                                                    ListView.separated(
                                                        itemCount: controller.driverPendingList.length,
                                                        shrinkWrap: true,
                                                        physics: const NeverScrollableScrollPhysics(),
                                                        itemBuilder: (context, index){
                                                          return driverPendingWidget(context: context, data: controller.driverPendingList[index], controller: controller, changeColor: index % 2 == 0 ? true : false);
                                                        },
                                                        separatorBuilder: (context, index){
                                                          return 10.heightBox;
                                                        }
                                                    ),
                                                    if(controller.driverPendingNoMoreData.value == true)...[
                                                      30.heightBox,
                                                      Center(child: txt20Size(title: "${controller.labelTextDetail['no_more_data_message'] ?? "No more data"}", context: context)),
                                                      30.heightBox,
                                                    ],
                                                    if(controller.driverPendingLoadMore.value == true)...[
                                                      30.heightBox,
                                                      Center(child: progressCircularWidget(context)),
                                                      30.heightBox,
                                                    ]
                                                  ],
                                                ),
                                              ) : Center(child: txt20Size(title: "${controller.labelTextDetail['no_pending_found_message'] ?? "No pending found"}", context: context)),
                                              controller.driverRewardList.isNotEmpty ? Stack(
                                                children: [
                                                  SingleChildScrollView(
                                                    controller: controller.driverRewardScrollController,
                                                    physics: const AlwaysScrollableScrollPhysics(),
                                                    child: Column(
                                                      crossAxisAlignment: CrossAxisAlignment.start,
                                                      children: [
                                                        txt18Size(title: "${controller.labelTextDetail['driver_reward_description'] ?? 'You have'} ${controller.driverRewardPoints.value} ${controller.labelTextDetail['driver_reward_description1'] ?? 'points as driver'}",context: context, fontFamily: bold, textColor:  Colors.black),
                                                        10.heightBox,
                                                        Table(
                                                          border: TableBorder.all(
                                                              color: Colors.grey.shade400,
                                                              style: BorderStyle.solid,
                                                              width: 1
                                                          ),

                                                          children: [
                                                            TableRow( children: [
                                                              Column(children:[txt20Size(context: context, title: "${controller.labelTextDetail['driver_reward_points_table_label'] ?? "Points"}")]),
                                                              Column(children:[txt20Size(context: context, title: "${controller.labelTextDetail['driver_reward_reward_table_label'] ?? "Rewards"}")]),
                                                            ]),
                                                            for(var index = 0; index < controller.driverRewardList.length; index++)...[
                                                              tableRowWidget(context: context, cell1: "${controller.driverRewardList[index]['reward_point_setting']['point']}", cell2: "${controller.driverRewardList[index]['reward_name']}"),
                                                            ]
                                                          ],
                                                        ),
                                                      ],
                                                    ),
                                                  ),
                                                  Align(
                                                    alignment: Alignment.bottomCenter,
                                                    child: Container(
                                                      width: context.screenWidth,
                                                      color: Colors.grey.shade100,
                                                      height: 50,
                                                      child: elevatedButtonWidget(
                                                        textWidget: txt28Size(title: "${controller.labelTextDetail['claim_my_reward_button_text'] ?? "Claim my reward"}", textColor: Colors.white, context: context, fontFamily: regular),
                                                        onPressed: () async{
                                                          await controller.claimMyReward('driver');
                                                        },
                                                      ),
                                                    ),
                                                  ),
                                                ],
                                              ) : Center(child: txt20Size(title: "${controller.labelTextDetail['no_driver_found_message'] ?? "No driver reward found"}", context: context)),
                                            ],
                                            onPageChanged: (index) async{
                                              controller.driverTabController.index = index;
                                              await controller.updateDriverPageValue(index);
                                            },
                                          )
                                      )
                                    ],
                                  ),
                                ],
                                onPageChanged: (index) async{
                                  controller.tabController.index = index;
                                  await controller.updatePageIndexValue(index);
                                },
                              )
                          )
                        ],
                      )
                  ),
                ],
              ),
            ),
            if(controller.isOverlayLoading.value == true)...[
              overlayWidget(context),
            ]
          ],
        )
      )
    );
  }
}

