import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
// import 'package:proximaride_app/pages/my_profile/my_profile.dart';
// import 'package:proximaride_app/pages/notifications/widgets/filter_notification_side_widget.dart';
import 'package:proximaride_app/pages/notifications/widgets/userCard.dart';
import 'package:proximaride_app/pages/widgets/error_state_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/logger_service.dart';
// import 'package:side_sheet/side_sheet.dart';
import 'NotificationController.dart';

class NotificationPage extends StatelessWidget {
  const NotificationPage({super.key});

  @override
  Widget build(BuildContext context) {
    final NotificationController controller = Get.isRegistered<NotificationController>()
        ? Get.find<NotificationController>()
        : Get.put(NotificationController());
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title:
                  "${controller.labelTextDetail['notification_page_main_heading'] ?? 'Notifications'}",
              context: context)),
          leading: safeBackButton(context),
        ),
        body: SafeArea(
          child: Obx(() {
            if (controller.errorStateManager.isLoading.value) {
              return Center(child: progressCircularWidget(context));
            }

            if (controller.errorStateManager.hasError.value) {
              return ErrorStateWidget(
                message: controller.errorStateManager.errorMessage.value,
                errorType: controller.errorStateManager.errorType.value,
                onRetry: () {
                  if (controller.errorStateManager.onRetry.value != null) {
                    controller.errorStateManager.onRetry.value!();
                  }
                },
              );
            }

            if (controller.isLoading.value == true) {
              return Center(child: progressCircularWidget(context));
            } else {
              return controller.notificationsList.isEmpty &&
                      controller.filter.value == false
                  ? SafeArea(
                      child: Center(
                          child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Image.asset(noNotifications),
                          20.heightBox,
                          txt20Size(
                              title:
                                  "${controller.labelTextDetail['notification_page_no_messages_label'] ?? "You have no notifications yet"}",
                              context: context),
                        ],
                      )),
                    )
                  : Stack(
                      children: [
                        SingleChildScrollView(
                          child: Column(
                            children: [
                              10.heightBox,
                              // Padding(
                              //   padding:
                              //       const EdgeInsets.symmetric(horizontal: 15.0),
                              //   child: Align(
                              //     alignment: Alignment.topRight,
                              //     child: GestureDetector(
                              //       onTap: () {
                              //         SideSheet.right(
                              //           body: filterNotificationSideWidget(
                              //             context: context,
                              //             controller: controller,
                              //             screenWidth: context.screenWidth,
                              //             screenHeight: context.screenHeight,
                              //           ),
                              //           context: context,
                              //           width: context.screenWidth - 50,
                              //         );
                              //       },
                              //       child: Container(
                              //         height: 50,
                              //         padding: const EdgeInsets.symmetric(
                              //             horizontal: 16.0),
                              //         decoration: BoxDecoration(
                              //           color: btnPrimaryColor,
                              //           borderRadius: BorderRadius.circular(5.0),
                              //           boxShadow: [
                              //             BoxShadow(
                              //               color:
                              //                   btnPrimaryColor.withOpacity(0.2),
                              //               blurRadius: 5,
                              //               offset: const Offset(0, 2),
                              //             ),
                              //           ],
                              //         ),
                              //         child: Row(
                              //           crossAxisAlignment:
                              //               CrossAxisAlignment.center,
                              //           children: [
                              //             Image.asset(
                              //               filterImage,
                              //               height: getValueForScreenType<double>(
                              //                 context: context,
                              //                 mobile: 20.0,
                              //                 tablet: 22.0,
                              //               ),
                              //               width: getValueForScreenType<double>(
                              //                 context: context,
                              //                 mobile: 20.0,
                              //                 tablet: 22.0,
                              //               ),
                              //               color: Colors.white,
                              //             ),
                              //             const SizedBox(width: 8),
                              //             Expanded(
                              //               child: Text(
                              //                 controller.labelTextDetail[
                              //                         'notification_filter_btn_label'] ??
                              //                     "Search filters",
                              //                 style: TextStyle(
                              //                   fontSize:
                              //                       getValueForScreenType<double>(
                              //                     context: context,
                              //                     mobile: 18.0,
                              //                     tablet: 20.0,
                              //                   ),
                              //                   fontWeight: FontWeight.w600,
                              //                   color: Colors.white,
                              //                 ),
                              //                 softWrap: true,
                              //                 overflow: TextOverflow.visible,
                              //                 maxLines: 2,
                              //               ),
                              //             ),
                              //           ],
                              //         ),
                              //       ),
                              //     ),
                              //   ),
                              // ),

                              GestureDetector(
                                onTap: () {
                                  // Toggle expand / collapse of the info banner.
                                  controller.isInfoExpanded.toggle();
                                },
                                child: Container(
                                  width: double.infinity,
                                  margin: const EdgeInsets.symmetric(
                                      horizontal: 16, vertical: 12),
                                  padding: const EdgeInsets.all(16),
                                  decoration: BoxDecoration(
                                    color: btnPrimaryColor,
                                    borderRadius: BorderRadius.circular(8),
                                    border: Border.all(
                                        color: const Color(0xFFB0EACD)),
                                  ),
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      // Top row with title
                                      Row(
                                        mainAxisAlignment:
                                            MainAxisAlignment.spaceBetween,
                                        children: [
                                          Expanded(
                                            child: Text(
                                              'Stay connected – your chats live here',
                                              style: const TextStyle(
                                                fontSize: 22,
                                                fontWeight: FontWeight.w600,
                                                color: Colors.white,
                                              ),
                                            ),
                                          ),
                                          Icon(
                                            controller.isInfoExpanded.value
                                                ? Icons.keyboard_arrow_up
                                                : Icons.keyboard_arrow_down,
                                            color: Colors.white,
                                          ),
                                        ],
                                      ),
                                      AnimatedCrossFade(
                                        duration:
                                            const Duration(milliseconds: 250),
                                        crossFadeState:
                                            controller.isInfoExpanded.value
                                                ? CrossFadeState.showSecond
                                                : CrossFadeState.showFirst,
                                        firstChild: const SizedBox.shrink(),
                                        secondChild: Column(
                                          crossAxisAlignment:
                                              CrossAxisAlignment.start,
                                          children: [
                                            const SizedBox(height: 12),
                                            // Notification types
                                            Row(
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
                                                const Padding(
                                                  padding:
                                                      EdgeInsets.only(top: 4),
                                                  child: Icon(
                                                    Icons.directions_car,
                                                    size: 20,
                                                    color: Colors.white,
                                                  ),
                                                ),
                                                const SizedBox(width: 8),
                                                Expanded(
                                                  child: Text(
                                                    'If the message is about a ride, tapping it will take you straight to that ride\'s details page.',
                                                    style: const TextStyle(
                                                        fontSize: 20,
                                                        color: Colors.white),
                                                  ),
                                                ),
                                              ],
                                            ),
                                            const SizedBox(height: 10),
                                            Row(
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
                                                const Padding(
                                                  padding:
                                                      EdgeInsets.only(top: 4),
                                                  child: Icon(
                                                    Icons.chat_bubble_outline,
                                                    size: 20,
                                                    color: Colors.white,
                                                  ),
                                                ),
                                                const SizedBox(width: 8),
                                                Expanded(
                                                  child: Text(
                                                    'If it\'s from another member, you\'ll be directed to your Inbox.',
                                                    style: const TextStyle(
                                                        fontSize: 20,
                                                        color: Colors.white),
                                                  ),
                                                ),
                                              ],
                                            ),
                                            const SizedBox(height: 10),
                                            Row(
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
                                                const Padding(
                                                  padding:
                                                      EdgeInsets.only(top: 4),
                                                  child: Icon(
                                                    Icons
                                                        .notifications_active_outlined,
                                                    size: 20,
                                                    color: Colors.white,
                                                  ),
                                                ),
                                                const SizedBox(width: 8),
                                                Expanded(
                                                  child: Text(
                                                    'If it\'s a general update from ProximaRide, it will open right here for you to read.',
                                                    style: const TextStyle(
                                                        fontSize: 20,
                                                        color: Colors.white),
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),

                              // elevatedButtonWidget(
                              //   textWidget: txt22Size(
                              //     title: "Go to Welcome Screen",
                              //     context: context,
                              //     textColor: Colors.white,
                              //   ),
                              //   onPressed: () {
                              //     Get.to(WelcomeScreen(
                              //       firstName: "Suheer",
                              //     ));
                              //   },
                              //   context: context,
                              // ),

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
                                    physics:
                                        const NeverScrollableScrollPhysics(),
                                    shrinkWrap: true,
                                    itemCount:
                                        controller.notificationsList.length,
                                    itemBuilder: (context, index) {
                                      return userCard(
                                        context: context,
                                        isRead:
                                            controller.notificationsList[index]
                                                ['is_read'],
                                        notificationType:
                                            controller.notificationsList[index]
                                                ['notification_type'],
                                        bgColor: index % 2 == 0
                                            ? Colors.white
                                            : Colors.grey.shade300,
                                        image: (controller.notificationsList[index]
                                                            ['from']
                                                            ['first_name']
                                                        ?.toString()
                                                        .toLowerCase()
                                                        .trim() ==
                                                    controller
                                                        .serviceController
                                                        .loginUserDetail[
                                                            'first_name']
                                                        ?.toString()
                                                        .toLowerCase()
                                                        .trim() &&
                                                controller
                                                        .notificationsList[index]
                                                            ['from']
                                                            ['last_name']
                                                        ?.toString()
                                                        .toLowerCase()
                                                        .trim() ==
                                                    controller.serviceController.loginUserDetail['last_name']
                                                        ?.toString()
                                                        .toLowerCase()
                                                        .trim())
                                            ? "assets/icons/logo.png"
                                            : controller.notificationsList[index]
                                                ['from']['profile_image'],
                                        //  controller
                                        //         .notificationsList[index]
                                        //     ['from']['profile_image'],
                                        // name:
                                        //     "${controller.notificationsList[index]['from']['first_name']} ${controller.notificationsList[index]['from']['last_name']}",

                                        name: (controller
                                                        .notificationsList[index]
                                                            ['from']
                                                            ['first_name']
                                                        ?.toString()
                                                        .toLowerCase()
                                                        .trim() ==
                                                    controller
                                                        .serviceController
                                                        .loginUserDetail[
                                                            'first_name']
                                                        ?.toString()
                                                        .toLowerCase()
                                                        .trim() &&
                                                controller
                                                        .notificationsList[index]
                                                            ['from']
                                                            ['last_name']
                                                        ?.toString()
                                                        .toLowerCase()
                                                        .trim() ==
                                                    controller
                                                        .serviceController
                                                        .loginUserDetail[
                                                            'last_name']
                                                        ?.toString()
                                                        .toLowerCase()
                                                        .trim())
                                            ? "ProximaRide"
                                            : "${controller.notificationsList[index]['from']['first_name']}",
                                        controller: controller,
                                        notification:
                                            "${controller.notificationsList[index]['message']}",
                                        date:
                                            controller.notificationsList[index]
                                                ['added_on'],
                                        time:
                                            controller.notificationsList[index]
                                                ['added_on'],
                                        // userType:
                                        //     controller.notificationsList[
                                        //                 index]['type'] ==
                                        //             "1"
                                        //         ? 'Passenger'
                                        //         : 'Driver',
                                        // onTap: () {
                                        //   if (controller.notificationsList[
                                        //               index]
                                        //           ['notification_type'] ==
                                        //       "review") {
                                        //     if (controller
                                        //                 .notificationsList[
                                        //             index]['type'] ==
                                        //         "1") {
                                        //       Get.toNamed(
                                        //           '/notification_add_review/passenger/${controller.notificationsList[index]['ride_id']}/${controller.notificationsList[index]['posted_to']}/${controller.notificationsList[index]['id']}/${controller.notificationsList[index]['ride_detail_id']}');
                                        //     } else {
                                        //       Get.toNamed(
                                        //           '/notification_add_review/driver/${controller.notificationsList[index]['ride_id']}/0/${controller.notificationsList[index]['id']}/${controller.notificationsList[index]['ride_detail_id']}');
                                        //     }
                                        //   } else if (controller
                                        //                   .notificationsList[
                                        //               index]
                                        //           ['notification_type'] ==
                                        //       "chat") {
                                        //     var rideId = 0;
                                        //     if (controller
                                        //                 .notificationsList[
                                        //             index]['ride_id'] !=
                                        //         null) {
                                        //       rideId = int.parse(controller
                                        //           .notificationsList[index]
                                        //               ['ride_id']
                                        //           .toString());
                                        //     }
                                        //     Get.toNamed(
                                        //         '/messaging_page/${controller.notificationsList[index]['posted_by']}/$rideId/new');
                                        //   } else if (controller
                                        //                   .notificationsList[
                                        //               index]
                                        //           ['notification_type'] ==
                                        //       "phone") {
                                        //     Get.toNamed('/my_phone_number');
                                        //   } else if (controller
                                        //                   .notificationsList[
                                        //               index]
                                        //           ['notification_type'] ==
                                        //       "christmas") {
                                        //   } else if (controller
                                        //                   .notificationsList[
                                        //               index]
                                        //           ['notification_type'] ==
                                        //       "birthday") {
                                        //   } else if (controller
                                        //                   .notificationsList[
                                        //               index]
                                        //           ['notification_type'] ==
                                        //       "password") {
                                        //   } else if (controller
                                        //                   .notificationsList[
                                        //               index]
                                        //           ['notification_type'] ==
                                        //       "welcome") {
                                        //   } else if (controller
                                        //                   .notificationsList[
                                        //               index]
                                        //           ['notification_type'] ==
                                        //       "student_card") {
                                        //     Get.toNamed('/student_card');
                                        //   }
                                        //    else {
                                        //     var type =
                                        //         controller.notificationsList[
                                        //                         index]
                                        //                     ['type'] ==
                                        //                 "1"
                                        //             ? "ride"
                                        //             : "trip";
                                        //     Get.toNamed(
                                        //         '/trip_detail/${controller.notificationsList[index]['ride_id']}/$type/${controller.notificationsList[index]['notification_type']}/${controller.notificationsList[index]['ride_detail_id']}');
                                        //   }
                                        // },
                                        onLongPress: () async {
                                          logger.info(controller
                                              .notificationsList[index]
                                              .toString());
                                          bool isConfirmed = await controller
                                              .serviceController
                                              .showConfirmationDialog(
                                                  controller.labelTextDetail[
                                                          'notification_confirm_message'] ??
                                                      "Are you sure you want to delete this notification",
                                                  cancelNoBtn:
                                                      "No, take me back!",
                                                  cancelYesBtn:
                                                      'Yes, delete it!');
                                          if (isConfirmed == false) {
                                          } else {
                                            await controller.deleteNotification(
                                                controller.notificationsList[
                                                    index]['id']);
                                          }
                                        },
                                        onTap: () {
                                                                                    // Mark notification as read when tapped
                                          controller.readNotification(
                                            controller.notificationsList[index]
                                                ['id'],
                                            showError: false,
                                          );

                                          // Update the notification's is_read status locally
                                          controller.notificationsList[index]
                                              ['is_read'] = "1";
                                          controller.notificationsList
                                              .refresh();

                                          if (controller.notificationsList[index]
                                                  ['notification_type'] ==
                                              "review") {
                                            logger.info(
                                                ">>> ENTERING REVIEW CONDITION <<<");
                                            if (controller.notificationsList[
                                                    index]['type'] ==
                                                "1") {
                                              logger.info(
                                                  "Review type is 1 - navigating to passenger review");
                                              var route =
                                                  '/notification_add_review/passenger/${controller.notificationsList[index]['ride_id']}/${controller.notificationsList[index]['posted_to']}/${controller.notificationsList[index]['id']}/${controller.notificationsList[index]['ride_detail_id']}';
                                              logger.info(
                                                  "Navigation route: $route");
                                              Get.toNamed(route);
                                              logger
                                                  .info("Navigation completed");
                                            } else {
                                              logger.info(
                                                  "Review type is NOT 1 - navigating to driver review");
                                              var route =
                                                  '/notification_add_review/driver/${controller.notificationsList[index]['ride_id']}/0/${controller.notificationsList[index]['id']}/${controller.notificationsList[index]['ride_detail_id']}';
                                              logger.info(
                                                  "Navigation route: $route");
                                              Get.toNamed(route);
                                              logger
                                                  .info("Navigation completed");
                                            }
                                          } else if (controller
                                                      .notificationsList[index]
                                                  ['notification_type'] ==
                                              "chat") {
                                            logger.info(
                                                ">>> ENTERING CHAT CONDITION <<<");
                                            var rideId = 0;
                                            if (controller.notificationsList[
                                                    index]['ride_id'] !=
                                                null) {
                                              rideId = int.parse(controller
                                                  .notificationsList[index]
                                                      ['ride_id']
                                                  .toString());
                                              logger.info(
                                                  "Ride ID parsed: $rideId");
                                            } else {
                                              logger.info(
                                                  "Ride ID is null, using 0");
                                            }
                                            var route =
                                                '/messaging_page/${controller.notificationsList[index]['posted_by']}/$rideId/new';
                                            logger.info(
                                                "Chat navigation route: $route");
                                            Get.toNamed(route);
                                            logger.info(
                                                "Chat navigation completed");
                                          } else if (controller
                                                      .notificationsList[index]
                                                  ['notification_type'] ==
                                              "phone") {
                                            logger.info(
                                                ">>> ENTERING PHONE CONDITION <<<");
                                            logger.info(
                                                "Navigating to my_phone_number");
                                            Get.toNamed('/my_phone_number');
                                            logger.info(
                                                "Phone navigation completed");
                                          } else if (controller
                                                      .notificationsList[index]
                                                  ['notification_type'] ==
                                              "welcome") {
                                            logger.info(
                                                ">>> ENTERING WELCOME CONDITION <<<");
                                            logger.info(
                                                "Navigating to WelcomeScreen");
                                            Get.to(
                                              WelcomeScreen(
                                                firstName: controller
                                                            .notificationsList[
                                                        index]['from']
                                                    ['first_name'],
                                              ),
                                            );
                                            logger.info(
                                                "Welcome navigation completed");
                                          } else if (controller
                                                              .notificationsList[
                                                          index]
                                                      ['notification_type'] !=
                                                  null &&
                                              controller.notificationsList[index]
                                                      ['ride_id'] !=
                                                  null) {
                                            logger.info(
                                                ">>> ENTERING RIDE/TRIP CONDITION <<<");
                                            logger.info(
                                                "Notification type is not null: ${controller.notificationsList[index]['notification_type']}");
                                            logger.info(
                                                "Ride ID is not null: ${controller.notificationsList[index]['ride_id']}");

                                            // Handle ride/trip notifications
                                            var type =
                                                controller.notificationsList[
                                                            index]['type'] ==
                                                        "1"
                                                    ? "ride"
                                                    : "trip";
                                            logger.info(
                                                "Determined type: $type (based on type field: ${controller.notificationsList[index]['type']})");

                                            var route =
                                                '/trip_detail/${controller.notificationsList[index]['ride_id']}/$type/${controller.notificationsList[index]['notification_type']}/${controller.notificationsList[index]['ride_detail_id']}';
                                            logger.info(
                                                "Trip detail navigation route: $route");
                                            Get.toNamed(route);
                                            logger.info(
                                                "Trip detail navigation completed");
                                          } else {
                                            // Fallback: do NOT navigate anywhere for
                                            // unhandled notification types or when
                                            // there is no concrete screen to open.
                                            logger.info(
                                                ">>> ENTERING FALLBACK CONDITION (NO NAVIGATION) <<<");
                                            logger.info(
                                                "Notification type (may be welcome/birthday/christmas/password/etc.): ${controller.notificationsList[index]['notification_type']}");
                                            logger.info(
                                                "No navigation will be performed for this notification.");
                                          }

                                          logger.info(
                                              "=== NOTIFICATION TAP DEBUG END ===");
                                        },
                                      );
                                    },
                                    separatorBuilder: (context, index) {
                                      return const SizedBox();
                                    }),
                              ),
                            ],
                          ),
                        ),
                        if (controller.isOverlayLoading.value == true) ...[
                          overlayWidget(context),
                        ],
                        if (controller.notificationsList.isEmpty &&
                            controller.filter.value == true &&
                            controller.isOverlayLoading.value == false) ...[
                          Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Image.asset(noNotifications),
                                txt20Size(
                                    title:
                                        "${controller.labelTextDetail['notification_page_no_messages_label'] ?? "You have no notifications"}",
                                    context: context),
                              ],
                            ),
                          )
                        ]
                      ],
                    );
            }
          }),
        ));
  }
}

class WelcomeScreen extends StatelessWidget {
  final dynamic firstName;

  const WelcomeScreen({super.key, required this.firstName});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.close, color: Colors.grey[700]),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: EdgeInsets.symmetric(horizontal: 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                SizedBox(height: 16),

                // Logo with refined styling
                Container(
                  width: 70,
                  height: 70,
                  padding: EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: primaryColor.withOpacity(0.1),
                        blurRadius: 20,
                        offset: Offset(0, 8),
                      ),
                    ],
                  ),
                  child: Image.asset('assets/icons/logo.png'),
                ),

                SizedBox(height: 20),

                // Welcome badge with modern gradient
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 14, vertical: 7),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [
                        primaryColor.withOpacity(0.12),
                        primaryColor.withOpacity(0.06),
                      ],
                    ),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: primaryColor.withOpacity(0.15),
                      width: 1,
                    ),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        Icons.celebration_outlined,
                        size: 16,
                        color: primaryColor,
                      ),
                      SizedBox(width: 6),
                      Text(
                        'Welcome to ProximaRide',
                        style: TextStyle(
                          fontSize: 14,
                          color: primaryColor,
                          fontWeight: FontWeight.w600,
                          letterSpacing: 0.3,
                        ),
                      ),
                    ],
                  ),
                ),

                SizedBox(height: 24),

                // Main message card with elegant design
                Container(
                  width: double.infinity,
                  padding: EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: Colors.grey.shade200,
                      width: 1,
                    ),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.04),
                        blurRadius: 12,
                        offset: Offset(0, 4),
                      ),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Refined greeting with better typography
                      RichText(
                        text: TextSpan(
                          style: TextStyle(
                            fontSize: 15,
                            height: 1.7,
                            color: Colors.grey[700],
                            letterSpacing: 0.2,
                          ),
                          children: [
                            TextSpan(
                              text: 'Hi $firstName,\n\n',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w700,
                                color: Colors.grey[900],
                                letterSpacing: 0,
                              ),
                            ),
                            TextSpan(
                              text:
                                  'Thanks for signing up to ProximaRide, and welcome!\n\n',
                            ),
                            TextSpan(
                              text:
                                  'I\'m Erman, a dad and the founder, and glad that you decided to join us. I started ProximaRide because I wanted to make ridesharing safer, more affordable and more reliable for people like my daughter, who travels to her school in Ottawa (from Montreal) every week. Everyone should arrive at their destination safely, just like her.\n\n',
                            ),
                            TextSpan(
                              text:
                                  'Don\'t worry, we don\'t send a lot of messages; just the essentials. So, just relax and enjoy the ride.\n\n',
                            ),
                            TextSpan(
                              text:
                                  'We are always here to answer any queries that you may have so feel free to contact us. And remember - sharing is caring!\n\n',
                            ),
                          ],
                        ),
                      ),

                      // Elegant closing message
                      Text(
                        'Again, thank you for joining, and welcome!',
                        style: TextStyle(
                          fontSize: 15,
                          color: Colors.grey[600],
                          fontStyle: FontStyle.italic,
                          letterSpacing: 0.2,
                        ),
                      ),
                    ],
                  ),
                ),

                SizedBox(height: 20),

                // Refined signature section
                Container(
                  width: double.infinity,
                  padding: EdgeInsets.all(18),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: Colors.grey.shade200,
                      width: 1,
                    ),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.04),
                        blurRadius: 12,
                        offset: Offset(0, 4),
                      ),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Container(
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              boxShadow: [
                                BoxShadow(
                                  color: primaryColor.withOpacity(0.2),
                                  blurRadius: 8,
                                  offset: Offset(0, 2),
                                ),
                              ],
                            ),
                            child: CircleAvatar(
                              radius: 22,
                              backgroundImage: AssetImage(founderImage),
                              backgroundColor: primaryColor,
                            ),
                          ),
                          14.widthBox,
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Erman',
                                style: TextStyle(
                                  fontSize: 15,
                                  fontWeight: FontWeight.w700,
                                  color: Colors.grey[900],
                                  letterSpacing: 0.2,
                                ),
                              ),
                              Text(
                                'Founder, ProximaRide',
                                style: TextStyle(
                                  fontSize: 13,
                                  color: Colors.grey[600],
                                  letterSpacing: 0.1,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                      14.heightBox,
                      Text(
                        'And the entire ProximaRide Team',
                        style: TextStyle(
                          fontSize: 13,
                          color: Colors.grey[600],
                          fontStyle: FontStyle.italic,
                          letterSpacing: 0.1,
                        ),
                      ),
                      14.heightBox,
                      GridView.builder(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        gridDelegate:
                            const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 5,
                          childAspectRatio: 1,
                          mainAxisSpacing: 8,
                          crossAxisSpacing: 8,
                        ),
                        itemCount: teamMemberImages.length,
                        itemBuilder: (context, index) {
                          return Container(
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.06),
                                  blurRadius: 4,
                                  offset: Offset(0, 2),
                                ),
                              ],
                            ),
                            child: CircleAvatar(
                              radius: 20,
                              backgroundImage:
                                  AssetImage(teamMemberImages[index]),
                              backgroundColor: primaryColor.withOpacity(0.1),
                            ),
                          );
                        },
                      ),
                    ],
                  ),
                ),

                SizedBox(height: 28),

                // Professional footer buttons with modern styling
                _buildModernButton(
                  context: context,
                  icon: Icons.help_outline_rounded,
                  label: 'Help & Contact',
                  onPressed: () => Get.toNamed('/contact_us'),
                ),
                SizedBox(height: 10),
                _buildModernButton(
                  context: context,
                  icon: Icons.description_outlined,
                  label: 'Terms & Conditions',
                  onPressed: () => Get.toNamed('/term_of_use'),
                ),
                SizedBox(height: 10),
                _buildModernButton(
                  context: context,
                  icon: Icons.coffee_outlined,
                  label: 'Coffee on the Wall',
                  onPressed: () => Get.toNamed('/coffee_on_wall'),
                ),

                SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // Modern professional button widget
  Widget _buildModernButton({
    required BuildContext context,
    required IconData icon,
    required String label,
    required VoidCallback onPressed,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onPressed,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          width: double.infinity,
          padding: EdgeInsets.symmetric(horizontal: 18, vertical: 14),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: primaryColor.withOpacity(0.2),
              width: 1.5,
            ),
            boxShadow: [
              BoxShadow(
                color: primaryColor.withOpacity(0.05),
                blurRadius: 8,
                offset: Offset(0, 2),
              ),
            ],
          ),
          child: Row(
            children: [
              Container(
                padding: EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: primaryColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(
                  icon,
                  size: 20,
                  color: primaryColor,
                ),
              ),
              SizedBox(width: 14),
              Expanded(
                child: Text(
                  label,
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                    color: Colors.grey[800],
                    letterSpacing: 0.2,
                  ),
                ),
              ),
              Icon(
                Icons.arrow_forward_ios_rounded,
                size: 16,
                color: Colors.grey[400],
              ),
            ],
          ),
        ),
      ),
    );
  }
}



