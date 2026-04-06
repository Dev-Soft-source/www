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
import 'package:proximaride_app/pages/widgets/app_html_text.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/services/logger_service.dart';
import '../widgets/tool_tip.dart';

const String _kAgreeTermsFallbackHtml =
    'I will abide by ProximaRide rules and I have read and agree to ProximaRide <a href="/term_condition">Terms of services</a>, <a href="/term_of_use">Term of use</a>, <a href="/privacy_policy">Privacy policy</a> and all associated rules and policies.';

/// Inserts the required-field star inside the last block (before `</p>` / `</div>`)
/// so [flutter_html] does not render it as a separate block on the next line.
String _agreeTermsHtmlWithRequiredStar(String? raw) {
  final base =
      (raw == null || raw.trim().isEmpty) ? _kAgreeTermsFallbackHtml : raw.trim();
  final star = '<span class="pr-required-star">*</span>';

  final lower = base.toLowerCase();
  for (final tag in ['</p>', '</div>']) {
    final idx = lower.lastIndexOf(tag);
    if (idx != -1) {
      return '${base.substring(0, idx)}\u00A0$star${base.substring(idx)}';
    }
  }
  return '$base\u00A0$star';
}

class PostRidePage extends StatefulWidget {
  const PostRidePage({super.key});

  @override
  State<PostRidePage> createState() => _PostRidePageState();
}

class _PostRidePageState extends State<PostRidePage> {
  bool _controllerReady = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted) return;
      if (Get.isRegistered<PostRideController>()) {
        Get.delete<PostRideController>(force: true);
      }
      Get.put(PostRideController());
      if (mounted) {
        setState(() => _controllerReady = true);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    if (!_controllerReady) {
      return Scaffold(
        backgroundColor: Colors.white,
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: secondAppBarWidget(
            title: 'Post a ride',
            context: context,
          ),
          leading: safeBackButton(context),
        ),
        body: SafeArea(
          child: Center(child: progressCircularWidget(context)),
        ),
      );
    }
    return const _PostRideScaffold();
  }
}

class _PostRideScaffold extends StatelessWidget {
  const _PostRideScaffold();

  Future<void> _handleAgreementLinkTap(String link) async {
    final normalizedLink = link.toLowerCase();
    final uri = Uri.tryParse(link);
    final path = (uri?.path ?? normalizedLink).toLowerCase();

    if (normalizedLink.contains('term_condition') ||
        path.contains('term-condition') ||
        path.contains('terms-and-conditions')) {
      Get.toNamed('/term_condition');
      return;
    }

    if (normalizedLink.contains('term_of_use') ||
        path.contains('term-of-use') ||
        path.contains('terms-of-use')) {
      Get.toNamed('/term_of_use');
      return;
    }

    if (normalizedLink.contains('privacy_policy') ||
        path.contains('privacy-policy')) {
      Get.toNamed('/privacy_policy');
    }
  }

  @override
  Widget build(BuildContext context) {
    final PostRideController controller = Get.find<PostRideController>();
    return Scaffold(
        appBar: AppBar(
          backgroundColor: primaryColor,
          title: Obx(() => secondAppBarWidget(
              title: controller.rideType.value == "update"
                  ? "${controller.labelTextDetail['main_heading_update'] ?? "Edit ride"}"
                  : "${controller.labelTextDetail['main_heading'] ?? "Post a ride"}",
              context: context)),
          leading: safeBackButton(context),
        ),
        body: SafeArea(
          child: Obx(() {
            if (controller.isLoading.value == true) {
              return Center(child: progressCircularWidget(context));
            } else {
              logger.info('bookings.value ${controller.bookings.value}');
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
                            if (controller.rideType.value != 'update') ...[
                              Align(
                                alignment: Alignment.topRight,
                                child: SizedBox(
                                  width: context.screenWidth,
                                  // height: getValueForScreenType<double>(
                                  //   context: context,
                                  //   mobile: 40.0,
                                  //   tablet: 40.0,
                                  // ),
                                  child: elevatedButtonWidget(
                                    textWidget: txt22Size(
                                        title:
                                            "${controller.labelTextDetail['post_arrived_again_label'] ?? "Copy ride details"}",
                                        textColor: Colors.white,
                                        context: context,
                                        fontFamily: regular),
                                    context: context,
                                    onPressed: () {
                                      Get.toNamed("/post_ride_again");
                                    },
                                  ),
                                ),
                              ),
                            ],
                            10.heightBox,
                            txt18Size(
                                context: context,
                                textColor: Colors.red,
                                fontFamily: carlito,
                                title:
                                    '* ${controller.labelTextDetail['indicates_required_field_text'] ?? "Indicates required fields"}'),
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
                            pricePaymentOptionWidget(
                                context: context,
                                controller: controller,
                                screenWidth: context.screenWidth,
                                bookingCheck: controller.bookings.value,
                                error: controller.errors.toList()),
                            10.heightBox,
                            Obx(
                              () => vehicleWidget(
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
                              onTap: () {
                                if (controller.errors.firstWhereOrNull(
                                        (element) =>
                                            element['title'] ==
                                            "agree_terms") !=
                                    null) {
                                  controller.errors.remove(controller.errors
                                      .firstWhereOrNull((element) =>
                                          element['title'] == "agree_terms"));
                                }
                                controller.disclaimer.value =
                                    controller.disclaimer.value == true
                                        ? false
                                        : true;
                              },
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Checkbox(
                                    value: controller.disclaimer.value,
                                    activeColor: primaryColor,
                                    side: BorderSide(
                                      color: controller.errors.any((element) =>
                                              element['title'] ==
                                              "agree_terms")
                                          ? Colors.red
                                          : Colors.grey.shade500,
                                    ),
                                    onChanged: (_) {
                                      if (controller.errors.firstWhereOrNull(
                                              (element) =>
                                                  element['title'] ==
                                                  "agree_terms") !=
                                          null) {
                                        controller.errors.remove(controller
                                            .errors
                                            .firstWhereOrNull((element) =>
                                                element['title'] ==
                                                "agree_terms"));
                                      }
                                      controller.disclaimer.value =
                                          !controller.disclaimer.value;
                                    },
                                  ),
                                  Expanded(
                                    child: Padding(
                                      padding: const EdgeInsets.only(top: 5.0),
                                      child: AppHtmlText(
                                        data: _agreeTermsHtmlWithRequiredStar(
                                          controller.labelTextDetail[
                                                  'agree_terms_label']
                                              ?.toString(),
                                        ),
                                        fontSize: 20,
                                        fontFamily: bold,
                                        fontWeight: FontWeight.w400,
                                        textColor: textColor,
                                        linkColor: primaryColor,
                                        lineHeight: 1.4,
                                        openLinksExternally: false,
                                        onLinkTapCallback:
                                            _handleAgreementLinkTap,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            10.heightBox,
                            if (controller.errors.firstWhereOrNull((element) =>
                                    element['title'] == "agree_terms") !=
                                null) ...[
                              toolTip(
                                  tip: controller.errors.firstWhereOrNull(
                                      (element) =>
                                          element['title'] == "agree_terms"))
                            ],
                            10.heightBox,
                            SizedBox(
                              // padding: const EdgeInsets.all(15.0),
                              // color: Colors.grey.shade100,
                              width: context.screenWidth,
                              child: elevatedButtonWidget(
                                  enabled: controller.disclaimer.value,
                                  textWidget: txt22Size(
                                      title: controller.rideType.value ==
                                              'update'
                                          ? '${controller.labelTextDetail['update_button_label'] ?? "Update ride"}'
                                          : "${controller.labelTextDetail['submit_button_label'] ?? "Post ride"}",
                                      context: context,
                                      textColor: Colors.white),
                                  context: context,
                                  onPressed: () async {
                                    await controller.postRide(
                                        context, context.screenHeight);
                                  }),
                            ),
                            30.heightBox,
                          ],
                        ),
                      ),
                  ),
                  // Align(
                  //   alignment: Alignment.bottomCenter,
                  //   child: Container(
                  //     padding: const EdgeInsets.all(15.0),
                  //     color: Colors.grey.shade100,
                  //     width: context.screenWidth,
                  //     child: elevatedButtonWidget(
                  //         textWidget: txt28Size(
                  //             title: controller.rideType.value == 'update'
                  //                 ? '${controller.labelTextDetail['update_button_label'] ?? "Update ride"}'
                  //                 : "${controller.labelTextDetail['submit_button_label'] ?? "Post ride"}",
                  //             context: context,
                  //             textColor: Colors.white),
                  //         context: context,
                  //         onPressed: () async {
                  //           await controller.postRide(
                  //               context, context.screenHeight);
                  //         }),
                  //   ),
                  // ),
                  if (controller.isOverlayLoading.value == true) ...[
                    overlayWidget(context),
                    //overlayWidget(context)
                  ]
                ],
              );
            }
          }),
        ));
  }
}

