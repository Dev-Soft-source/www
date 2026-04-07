import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/ride_feature_ids.dart';
import 'package:proximaride_app/pages/search_ride/SearchRideController.dart';
import 'package:proximaride_app/pages/search_ride/widget/filter_side_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/circle_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/trip_card_date_time_widget.dart';
import 'package:proximaride_app/pages/widgets/trip_card_from_to_widget.dart';
import 'package:side_sheet/side_sheet.dart';

class SearchRideResultPage extends StatelessWidget {
  const SearchRideResultPage({super.key});

  Map<String, dynamic> _normalizeRideDetail(dynamic ride) {
    if (ride is Map) {
      final directRideDetail = ride['ride_detail'];
      if (directRideDetail is Map) {
        return Map<String, dynamic>.from(directRideDetail);
      }

      if (directRideDetail is List && directRideDetail.isNotEmpty) {
        final firstDetail = directRideDetail.first;
        if (firstDetail is Map) {
          return Map<String, dynamic>.from(firstDetail);
        }
      }
    }

    if (ride is List && ride.isNotEmpty) {
      final firstRide = ride.first;
      if (firstRide is Map) {
        return Map<String, dynamic>.from(firstRide);
      }
    }

    return <String, dynamic>{};
  }

  String _formatMinorPriceForDisplay(dynamic value) {
    if (value == null || value.toString().trim().isEmpty) {
      return "0.00";
    }

    final parsed = num.tryParse(value.toString());
    if (parsed == null) {
      return value.toString();
    }

    final major = parsed / 100;
    return major.toStringAsFixed(2);
  }

  @override
  Widget build(BuildContext context) {
    var controller = Get.find<SearchRideController>();
    return PopScope(
        canPop: true,
        onPopInvoked: (didPop) {
          controller.clearFilter();
        },
        child: Scaffold(
          appBar: AppBar(
            backgroundColor: primaryColor,
            title: Obx(() => secondAppBarWidget(
                context: context,
                title:
                    "${controller.labelTextDetail['main_heading'] ?? "Search result"}")),
            leading: IconButton(
              onPressed: controller.handleBackNavigation,
              icon: const Icon(Icons.arrow_back, color: Colors.white),
            ),
          ),
          body: Obx(() {
            if (controller.isLoading.value == true) {
              return Center(child: progressCircularWidget(context));
            } else {
              return Stack(
                children: [
                  SingleChildScrollView(
                    child: Container(
                      padding: EdgeInsets.all(getValueForScreenType<double>(
                        context: context,
                        mobile: 15.0,
                        tablet: 15.0,
                      )),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Align(
                            alignment: Alignment.topRight,
                            child: SizedBox(
                              height: getValueForScreenType<double>(
                                context: context,
                                mobile: 36.0,
                                tablet: 26.0,
                              ),
                              child: ElevatedButton.icon(
                                  onPressed: () {
                                    SideSheet.right(
                                        body: filterSideWidget(
                                          context: context,
                                          controller: controller,
                                          screenWidth: context.screenWidth,
                                          screenHeight: context.screenHeight,
                                        ),
                                        context: context,
                                        width: context.screenWidth - 50);
                                  },
                                  style: ElevatedButton.styleFrom(
                                      backgroundColor: btnPrimaryColor,
                                      shape: RoundedRectangleBorder(
                                          borderRadius:
                                              BorderRadius.circular(5))),
                                  icon: Image.asset(filterImage,
                                      height: getValueForScreenType<double>(
                                        context: context,
                                        mobile: 15.0,
                                        tablet: 15.0,
                                      ),
                                      width: getValueForScreenType<double>(
                                        context: context,
                                        mobile: 15.0,
                                        tablet: 15.0,
                                      )),
                                  label: txt22Size(
                                      title:
                                          "${controller.labelTextDetail['filter_section_heading'] ?? "Search filters"}",
                                      context: context,
                                      textColor: Colors.white)),
                            ),
                          ),
                          10.heightBox,
                          ListView.separated(
                            itemCount: controller.rides.length,
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            itemBuilder: (context, index) {
                              final ride = controller.rides[index];
                              final rideDetail = _normalizeRideDetail(ride);
                              final ridePrice = _formatMinorPriceForDisplay(
                                  ride['price_minor'] ??
                                      rideDetail['price_minor'] ??
                                      rideDetail['price']);
                              final departure =
                                  ride['departure']?.toString() ?? "";
                              final destination =
                                  ride['destination']?.toString() ?? "";
                              final fromStopId = int.tryParse(
                                      ride['from_stop_id']?.toString() ?? '') ??
                                  0;
                              final toStopId = int.tryParse(
                                      ride['to_stop_id']?.toString() ?? '') ??
                                  0;
                              final pickup = ride['pickup']?.toString() ?? "";
                              final dropOff = ride['dropoff']?.toString() ?? "";
                              final seatsLeft =
                                  ride['seats_left']?.toString() ?? "0";
                              final totalSeats =
                                  ride['seats']?.toString() ?? "0";

                              DateTime parsedDate =
                                  DateTime.parse(ride['date']);
                              DateFormat outputFormat =
                                  DateFormat('MMMM d, yyyy');
                              String tripDate = outputFormat.format(parsedDate);

                              DateTime parsedTime =
                                  DateFormat("HH:mm:ss").parse(ride['time']);
                              DateFormat outputTimeFormat = DateFormat("HH:mm");
                              String tripTime =
                                  outputTimeFormat.format(parsedTime);

                              var firmPrice = 0.0;
                              final bookingTypeId =
                                  ride['booking_type_id']?.toString() ?? "";
                              final bookingTypeLabel = ride['booking_type']
                                      ?.toString()
                                      .toLowerCase() ??
                                  "";
                              final isFirmCancellationRide =
                                  bookingTypeId == "37" ||
                                      bookingTypeLabel.contains("firm");
                              if (isFirmCancellationRide) {
                                final parsedRidePrice =
                                    double.tryParse(ridePrice) ?? 0.0;
                                final parsedFirmDiscount = double.tryParse(
                                        controller.firmDiscount.value
                                            .toString()) ??
                                    0.0;
                                firmPrice = parsedRidePrice -
                                    ((parsedRidePrice * parsedFirmDiscount) /
                                        100);
                              }

                              final bookings = ride['bookings'];
                              final hideDriverInfo =
                                  bookings is List && bookings.isNotEmpty;

                              final statusBorderColors = <Color>[];
                              final features = parseRideFeatureIdsFromRide(
                                  Map<String, dynamic>.from(ride));
                              if (features.contains('1')) {
                                // pink ride
                                statusBorderColors.add(Color(0XFFE91E63));
                              }
                              if (features.contains('2')) {
                                // extra care ride
                                statusBorderColors.add(Color(0XFF48bb78));
                              }

                              if ((double.tryParse(ridePrice) ?? 0.0) < 15) {
                                statusBorderColors.add(Colors.blue);
                              }

                              Widget wrapWithStatusBorders(Widget child) {
                                Widget wrapped = child;

                                for (var i = statusBorderColors.length - 1;
                                    i >= 0;
                                    i--) {
                                  wrapped = Container(
                                    padding: const EdgeInsets.all(3),
                                    decoration: BoxDecoration(
                                      color: statusBorderColors[i],
                                      borderRadius: BorderRadius.circular(
                                        10 +
                                            ((statusBorderColors.length - i) *
                                                3),
                                      ),
                                    ),
                                    child: wrapped,
                                  );
                                }

                                return Padding(
                                  padding: const EdgeInsets.all(4),
                                  child: wrapped,
                                );
                              }

                              return InkWell(
                                onTap: () async {
                                  final tripDetailId = rideDetail != null &&
                                          rideDetail.isNotEmpty
                                      ? (rideDetail['id'] ?? 0)
                                      : 0;
                                  await controller.checkBooking(
                                      ride['id'], fromStopId, toStopId);
                                },
                                child: wrapWithStatusBorders(Card(
                                  margin: EdgeInsets.zero,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  surfaceTintColor: index % 2 != 0
                                      ? Colors.grey.shade100
                                      : Colors.white,
                                  elevation: 2,
                                  color: index % 2 != 0
                                      ? Colors.grey.shade100
                                      : Colors.white,
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      tripCardDateTimeWidget(
                                        date: tripDate,
                                        time: tripTime,
                                        context: context,
                                        price: ridePrice,
                                        tripStatus: "search",
                                        atLabel:
                                            "${controller.labelTextDetail['card_section_at_label'] ?? 'at'}",
                                        seatLeftLabel:
                                            "${controller.labelTextDetail['card_section_seats_left'] ?? 'seats left'}",
                                        perSeatLabel:
                                            "${controller.labelTextDetail['card_section_per_seat'] ?? 'per seat'}",
                                        notLiveLabel:
                                            "${controller.labelTextDetail['card_section_not_live'] ?? 'Not live'}",
                                        bookingRequestLabel:
                                            "${controller.labelTextDetail['card_section_booking_request'] ?? 'booking request'}",
                                        completedStatusLabel:
                                            "${controller.labelTextDetail['card_section_completed'] ?? 'Completed'}",
                                        cancelStatusLabel:
                                            "${controller.labelTextDetail['card_section_cancelled'] ?? 'Cancelled'}",
                                        totalSeat: totalSeats,
                                        firmPrice: firmPrice,
                                      ),
                                      tripCardFromToWidget(
                                        from: departure,
                                        to: destination,
                                        price: ridePrice,
                                        context: context,
                                        tripStatus: 'search',
                                        seatsLeft: seatsLeft,
                                        pickup: pickup,
                                        dropOff: dropOff,
                                        fromLabel:
                                            "${controller.labelTextDetail['card_section_from_label'] ?? 'From'}",
                                        toLabel:
                                            "${controller.labelTextDetail['card_section_to_label'] ?? 'to'}",
                                        seatLeftLabel:
                                            "${controller.labelTextDetail['card_section_seats_left'] ?? 'seats left'}",
                                        perSeatLabel:
                                            "${controller.labelTextDetail['card_section_per_seat'] ?? 'per seat'}",
                                        reviewedLabel:
                                            "${controller.labelTextDetail['trips_card_section_reviewed'] ?? 'Reviewed'}",
                                        reviewDriverLabel:
                                            "${controller.labelTextDetail['trips_card_section_review_driver'] ?? 'Review your driver'}",
                                      ),
                                      const Divider(),
                                      Container(
                                        padding: EdgeInsets.fromLTRB(
                                            getValueForScreenType<double>(
                                              context: context,
                                              mobile: 15.0,
                                              tablet: 15.0,
                                            ),
                                            getValueForScreenType<double>(
                                              context: context,
                                              mobile: 0.0,
                                              tablet: 0.0,
                                            ),
                                            getValueForScreenType<double>(
                                              context: context,
                                              mobile: 15.0,
                                              tablet: 15.0,
                                            ),
                                            getValueForScreenType<double>(
                                              context: context,
                                              mobile: 0.0,
                                              tablet: 0.0,
                                            )),
                                        child: SingleChildScrollView(
                                          scrollDirection: Axis.horizontal,
                                          child: Row(
                                            mainAxisAlignment:
                                                MainAxisAlignment.start,
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              if (hideDriverInfo == true &&
                                                  controller.rides[index][
                                                          'remove_car_image'] ==
                                                      0) ...[
                                                InkWell(
                                                  onTap: () {
                                                    var make = controller.rides[
                                                                    index]
                                                                ['vehicle'] !=
                                                            null
                                                        ? controller
                                                                .rides[index]
                                                            ['vehicle']['make']
                                                        : controller
                                                                .rides[index]
                                                            ['make'];
                                                    var model = controller
                                                                        .rides[
                                                                    index]
                                                                ['vehicle'] !=
                                                            null
                                                        ? controller
                                                                .rides[index]
                                                            ['vehicle']['model']
                                                        : controller
                                                                .rides[index]
                                                            ['model'];
                                                    var year = controller.rides[
                                                                    index]
                                                                ['vehicle'] !=
                                                            null
                                                        ? controller
                                                                .rides[index]
                                                            ['vehicle']['year']
                                                        : controller
                                                                .rides[index]
                                                            ['year'];
                                                    var licenseNo = controller
                                                                        .rides[
                                                                    index]
                                                                ['vehicle'] !=
                                                            null
                                                        ? controller.rides[
                                                                    index]
                                                                ['vehicle']
                                                            ['license_no']
                                                        : controller
                                                                .rides[index]
                                                            ['license_no'];
                                                    var carType = controller
                                                                        .rides[
                                                                    index]
                                                                ['vehicle'] !=
                                                            null
                                                        ? controller.rides[
                                                                    index]
                                                                ['vehicle']
                                                            ['car_type']
                                                        : controller
                                                                .rides[index]
                                                            ['car_type'];
                                                    controller.serviceController
                                                        .showDialogue(
                                                            "$year,$make,$model\n$licenseNo\n$carType",
                                                            title:
                                                                "Vehicle Info");
                                                  },
                                                  child: circleImageWidget(
                                                      width: 30.0,
                                                      height: 30.0,
                                                      imagePath:
                                                          "${controller.rides[index]['vehicle'] != null ? controller.rides[index]['vehicle']['image'] : controller.rides[index]['car_image']}",
                                                      imageType: "network",
                                                      context: context),
                                                ),
                                              ],
                                              
                                              if (controller.rides[index][
                                                      'payment_method_image'] !=
                                                  null) ...[
                                                InkWell(
                                                  onTap: () {
                                                    controller.serviceController
                                                        .showDialogue(
                                                            controller
                                                                .rides[index][
                                                                    'payment_method_tooltip']
                                                                .toString(),
                                                            title:
                                                                "${controller.rides[index]['payment_method']}");
                                                  },
                                                  child: circleIconWidget(
                                                      width: 30.0,
                                                      height: 30.0,
                                                      imagePath: controller
                                                              .rides[index][
                                                          'payment_method_image'],
                                                      context: context),
                                                ),
                                                2.widthBox,
                                              ],
                                              if (controller.rides[index][
                                                      'booking_method_image'] !=
                                                  null) ...[
                                                InkWell(
                                                  onTap: () {
                                                    controller.serviceController
                                                        .showDialogue(
                                                            controller
                                                                .rides[index][
                                                                    'booking_method_tooltip']
                                                                .toString(),
                                                            title: controller
                                                                .rides[index][
                                                                    'booking_method']
                                                                .toString());
                                                  },
                                                  child: circleIconWidget(
                                                      width: 30.0,
                                                      height: 30.0,
                                                      imagePath: controller
                                                              .rides[index][
                                                          'booking_method_image'],
                                                      context: context),
                                                ),
                                                2.widthBox,
                                              ],
                                              if (controller.rides[index][
                                                      'animal_friendly_image'] !=
                                                  null) ...[
                                                InkWell(
                                                  onTap: () {
                                                    controller.serviceController
                                                        .showDialogue(
                                                            controller
                                                                .rides[index][
                                                                    'animal_friendly_tooltip']
                                                                .toString(),
                                                            title: controller
                                                                .labelTextDetail[
                                                                    'pets_allowed_label']
                                                                .toString());
                                                  },
                                                  child: circleIconWidget(
                                                      width: 30.0,
                                                      height: 30.0,
                                                      imagePath: controller
                                                              .rides[index][
                                                          'animal_friendly_image'],
                                                      context: context),
                                                ),
                                                2.widthBox,
                                              ],
                                              if (controller.rides[index]
                                                      ['smoke_image'] !=
                                                  null) ...[
                                                InkWell(
                                                  onTap: () {
                                                    controller.serviceController
                                                        .showDialogue(
                                                            controller
                                                                .rides[index][
                                                                    'smoke_tooltip']
                                                                .toString(),
                                                            title: controller
                                                                .labelTextDetail[
                                                                    'smoking_label']
                                                                .toString());
                                                  },
                                                  child: circleIconWidget(
                                                      width: 30.0,
                                                      height: 30.0,
                                                      imagePath: controller
                                                              .rides[index]
                                                          ['smoke_image'],
                                                      context: context),
                                                ),
                                                2.widthBox,
                                              ],
                                              if (controller.rides[index]
                                                      ['luggage_image'] !=
                                                  null) ...[
                                                InkWell(
                                                  onTap: () {
                                                    controller.serviceController
                                                        .showDialogue(
                                                            controller
                                                                .rides[index][
                                                                    'luggage_tooltip']
                                                                .toString(),
                                                            title: controller
                                                                .rides[index]
                                                                    ['luggage']
                                                                .toString());
                                                  },
                                                  child: circleIconWidget(
                                                      width: 30.0,
                                                      height: 30.0,
                                                      imagePath: controller
                                                              .rides[index]
                                                          ['luggage_image'],
                                                      context: context),
                                                ),
                                                2.widthBox,
                                              ],
                                              if (controller
                                                  .rides[index]['features']
                                                  .isNotEmpty) ...[
                                                for (var i = 0;
                                                    i <
                                                        controller
                                                            .rides[index]
                                                                ['features']
                                                            .length;
                                                    i++) ...[
                                                  2.widthBox,
                                                  InkWell(
                                                    onTap: () {
                                                      controller
                                                          .serviceController
                                                          .showDialogue(
                                                              "${controller.rides[index]['features'][i]['tooltip']}",
                                                              title:
                                                                  "${controller.rides[index]['features'][i]['title']}");
                                                    },
                                                    child: circleIconWidget(
                                                        width: 30.0,
                                                        height: 30.0,
                                                        imagePath:
                                                            controller.rides[
                                                                        index]
                                                                    ['features']
                                                                [i]['image'],
                                                        context: context),
                                                  ),
                                                  2.widthBox,
                                                ]
                                              ] else ...[
                                                2.widthBox,
                                              ],
                                            ],
                                            
                                          ),
                                        ),
                                      ),
                                      const Divider(),
                                      Container(
                                        padding: EdgeInsets.fromLTRB(
                                            getValueForScreenType<double>(
                                              context: context,
                                              mobile: 15.0,
                                              tablet: 15.0,
                                            ),
                                            getValueForScreenType<double>(
                                              context: context,
                                              mobile: 0.0,
                                              tablet: 0.0,
                                            ),
                                            getValueForScreenType<double>(
                                              context: context,
                                              mobile: 15.0,
                                              tablet: 15.0,
                                            ),
                                            getValueForScreenType<double>(
                                              context: context,
                                              mobile: 0.0,
                                              tablet: 0.0,
                                            )),
                                        child: SingleChildScrollView(
                                          scrollDirection: Axis.horizontal,
                                          child: Row(
                                            mainAxisAlignment:
                                                MainAxisAlignment.start,
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              hideDriverInfo == true
                                                  ? circleImageWidget(
                                                      width: 40.0,
                                                      height: 40.0,
                                                      imagePath: controller
                                                                          .rides[
                                                                      index]
                                                                  ['driver'][
                                                              'profile_image'] ??
                                                          "",
                                                      imageType: "network",
                                                      context: context)
                                                  : SizedBox(),
                                              5.widthBox,
                                              Column(
                                                crossAxisAlignment:
                                                    CrossAxisAlignment.start,
                                                children: [
                                                  Row(
                                                    children: [
                                                      txt18SizeCapitalize(
                                                          title:
                                                              "${controller.rides[index]['driver']['first_name']}",
                                                          context: context),
                                                      5.widthBox,
                                                      SizedBox(
                                                          width: 1,
                                                          height: 15,
                                                          child: Container(
                                                              color: Colors.grey
                                                                  .shade400)),
                                                      5.widthBox,
                                                      txt16Size(
                                                          title:
                                                              "${controller.labelTextDetail['card_section_age'] ?? "Age"}: ",
                                                          context: context),
                                                      txt16Size(
                                                          title:
                                                              "${controller.rides[index]['driver_age']}",
                                                          context: context),
                                                      5.widthBox,
                                                      SizedBox(
                                                          width: 1,
                                                          height: 15,
                                                          child: Container(
                                                              color: Colors.grey
                                                                  .shade400)),
                                                      5.widthBox,
                                                      if ((controller.rides[
                                                                      index][
                                                                  'gender_image'] ??
                                                              "")
                                                          .toString()
                                                          .isNotEmpty)
                                                        circleIconWidget(
                                                            width: 20.0,
                                                            height: 20.0,
                                                            imagePath: controller
                                                                        .rides[
                                                                    index][
                                                                'gender_image'],
                                                            context: context)
                                                      else
                                                        txt16Size(
                                                            title:
                                                                "${controller.rides[index]['driver']['gender_label']}",
                                                            context: context),
                                                    ],
                                                  ),
                                                  Row(
                                                    mainAxisAlignment:
                                                        MainAxisAlignment.start,
                                                    children: [
                                                      txt16Size(
                                                          title:
                                                              "${controller.labelTextDetail['card_section_driven'] ?? "Driven"}: ",
                                                          context: context),
                                                      txt16Size(
                                                          title: "${controller.rides[index]['driven_count']}",
                                                              // "${controller.rides[index]['driven_count']} ${controller.labelTextDetail['card_section_passengers'] ?? "passengers"}",
                                                          context: context),
                                                      5.widthBox,
                                                      SizedBox(
                                                          width: 1,
                                                          height: 15,
                                                          child: Container(
                                                              color: Colors.grey
                                                                  .shade400)),
                                                      5.widthBox,
                                                      txt16Size(
                                                          title:
                                                              "${controller.labelTextDetail['card_section_review'] ?? "Review"}: ",
                                                          context: context),
                                                      txt16Size(
                                                          //
                                                          title: controller.rides[
                                                                          index]
                                                                      [
                                                                      'driver_average_rating'] !=
                                                                  null
                                                              ? "${(controller.rides[index]['driver_average_rating']).toStringAsFixed(1)}"
                                                              : "${controller.labelTextDetail['card_section_no_review'] ?? 'No reviews'}",
                                                          context: context),
                                                      5.widthBox,
                                                    ],
                                                  ),
                                                  10.heightBox,
                                                ],
                                              )
                                            ],
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                )),
                              );
                            },
                            separatorBuilder: (context, index) {
                              return const SizedBox();
                            },
                          ),
                          10.heightBox,
                          if (controller.noRideFound.value) ...[
                            Center(
                              child: txt20Size(
                                  context: context,
                                  title:
                                      "${controller.labelTextDetail['search_result_no_found_message'] ?? "No ride for this location exist"}"),
                            ),
                            20.heightBox,
                          ] else ...[
                            if (controller.noMoreData.value) ...[
                              Center(
                                child: txt20Size(
                                    context: context,
                                    title:
                                        "${controller.labelTextDetail['search_result_no_more_data_message'] ?? "No more data to show"}"),
                              ),
                              20.heightBox,
                            ] else ...[
                              if (controller.isScrollLoading.value) ...[
                                Center(
                                  child: progressCircularWidget(context,
                                      width: 50.0, height: 50.0),
                                ),
                                20.heightBox,
                              ] else ...[
                                controller.searchTotal.value > 3
                                    ? Center(
                                        child: elevatedButtonWidget(
                                            textWidget: txt18Size(
                                                title:
                                                    "${controller.labelTextDetail['search_result_load_more_btn'] ?? "Load more"}",
                                                context: context,
                                                fontFamily: regular,
                                                textColor: Colors.white),
                                            context: context,
                                            onPressed: () async {
                                              controller.page++;
                                              await controller.getMoreRides();
                                            }),
                                      )
                                    : SizedBox(),
                              ]
                            ]
                          ],
                        ],
                      ),
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
