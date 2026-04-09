import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/helpers/ride_departure_display.dart';
import 'package:proximaride_app/helpers/currency_formatter.dart';
import 'package:proximaride_app/pages/my_trips/widget/ride_price_info_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/trip_card_date_time_widget.dart';
import 'package:proximaride_app/pages/widgets/trip_card_from_to_widget.dart';

Widget rideCardWidget(
    {controller,
    context,
    onTap,
    tripDetail,
    onTapRideCard,
    String tripStatus = "",
    onTapReviewPassenger,
    Color cardBgColor = Colors.white}) {
  Map<String, dynamic> getRideDetailMap(dynamic rideDetail) {
    if (rideDetail is Map) {
      final nestedRideDetail = rideDetail['ride_detail'];
      if (nestedRideDetail is Map) {
        return Map<String, dynamic>.from(nestedRideDetail);
      }
      if (nestedRideDetail is List && nestedRideDetail.isNotEmpty) {
        final firstRideDetail = nestedRideDetail.first;
        if (firstRideDetail is Map) {
          return Map<String, dynamic>.from(firstRideDetail);
        }
      }
    }

    return <String, dynamic>{};
  }

  final rideDetailMap = getRideDetailMap(tripDetail);
  String tripDate = "";
  if (tripDetail['date'] != null) {
    tripDate = rideDepartureDateDisplay(tripDetail);
  }

  final String tripTime =
      rideDepartureTimeDisplay(tripDetail, controller.labelTextDetail);

  var requestCount = tripDetail['booking_requests'] != null
      ? tripDetail['booking_requests'].length
      : 0;
  final bookedSeats = int.tryParse("${tripDetail['booked_seats'] ?? 0}") ?? 0;

  return _inkWellOrGestureWeb(
    onTap: onTapRideCard,
    child: Card(
      surfaceTintColor: cardBgColor,
      elevation: 2,
      color: cardBgColor,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          tripCardDateTimeWidget(
              date: tripDate,
              time: tripTime,
              context: context,
              tripStatus: tripStatus,
              request: requestCount.toString(),
              isLive: tripStatus == "upcoming"
                  ? (tripDetail['vehicle_id'] == null)
                      ? false
                      : true
                  : true,
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
              totalSeat: "${tripDetail['seats']}",
              cancelStatusLabel:
                  "${controller.labelTextDetail['card_section_cancelled'] ?? 'Cancelled'}"),
          tripCardFromToWidget(
              from: "${rideDetailMap['departure'] ?? ''}",
              to: "${rideDetailMap['destination'] ?? ''}",
              price: "${tripDetail['price']}",
              pickup: "${tripDetail['pickup']}",
              dropOff: "${tripDetail['dropoff']}",
              labelTextDetail: controller.labelTextDetail,
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
              context: context),
          const Divider(),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              ridePriceInfoWidget(
                  title:
                      "${controller.labelTextDetail['card_section_booked'] ?? "Booked"}",
                  value:
                      "${tripDetail['booked_seats']} ${controller.labelTextDetail['card_section_seats'] ?? "seats"}",
                  context: context),
              if (bookedSeats > 0) ...[
                const Divider(),
                ridePriceInfoWidget(
                    title:
                        "${controller.labelTextDetail['card_section_seats_fee'] ?? "Fare"}",
                    value: formatCurrency(tripDetail['fare']),
                    context: context),
                const Divider(),
                ridePriceInfoWidget(
                    title:
                        "${controller.labelTextDetail['card_section_booking_fee'] ?? "Booking fee"}",
                    value: formatCurrency(tripDetail['booking_fee']),
                    context: context),
                const Divider(),
                ridePriceInfoWidget(
                    title:
                        "${controller.labelTextDetail['card_section_amount'] ?? "Total amount"}",
                    value: formatCurrency(tripDetail['total_amount']),
                    context: context),
              ],
            ],
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
                mainAxisAlignment: MainAxisAlignment.start,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  circleImageWidget(
                      width: 30.0,
                      height: 30.0,
                      imagePath:
                          "${tripDetail['vehicle'] != null ? tripDetail['vehicle']['image'] : tripDetail['car_image']}",
                      imageType: "network",
                      context: context),
                  2.widthBox,
                  if (tripDetail['payment_method_image'] != null) ...[
                    circleIconWidget(
                        width: 30.0,
                        height: 30.0,
                        imagePath: tripDetail['payment_method_image'],
                        context: context),
                    2.widthBox,
                  ],
                  if (tripDetail['booking_method_image'] != null) ...[
                    circleIconWidget(
                        width: 30.0,
                        height: 30.0,
                        imagePath: tripDetail['booking_method_image'],
                        context: context),
                    2.widthBox,
                  ],
                  if (tripDetail['animal_friendly_image'] != null) ...[
                    circleIconWidget(
                        width: 30.0,
                        height: 30.0,
                        imagePath: tripDetail['animal_friendly_image'],
                        context: context),
                    2.widthBox,
                  ],
                  if (tripDetail['smoke_image'] != null) ...[
                    circleIconWidget(
                        width: 30.0,
                        height: 30.0,
                        imagePath: tripDetail['smoke_image'],
                        context: context),
                    2.widthBox,
                  ],
                  if (tripDetail['luggage_image'] != null) ...[
                    circleIconWidget(
                        width: 30.0,
                        height: 30.0,
                        imagePath: tripDetail['luggage_image'],
                        context: context),
                    2.widthBox,
                  ],
                  // features
                  if (tripDetail['features'].isNotEmpty) ...[
                    for (var i = 0; i < tripDetail['features'].length; i++) ...[
                      2.widthBox,
                      circleIconWidget(
                          width: 30.0,
                          height: 30.0,
                          imagePath: tripDetail['features'][i]['image'],
                          context: context),
                      2.widthBox,
                    ]
                  ] else ...[
                    2.widthBox,
                  ],
                ],
              ),
            ),
          ),
          if (tripDetail['bookings'].isNotEmpty) ...[
            const Divider(),
            _inkWellOrGestureWeb(
              onTap: onTapReviewPassenger,
              child: Container(
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
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (tripStatus == "completed") ...[
                          Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              txt20Size(
                                  context: context,
                                  title: "Review passengers",
                                  textColor: primaryColor,
                                  fontFamily: bold),
                              5.widthBox,
                              Image.asset(
                                arrowBtnImage,
                                width: 16,
                              )
                            ],
                          ),
                          5.heightBox,
                        ],
                        Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            for (var i = 0;
                                i < tripDetail['bookings'].length;
                                i++) ...[
                              circleImageWidget(
                                  width: 30.0,
                                  height: 30.0,
                                  imagePath: tripDetail['bookings'][i] !=
                                              null &&
                                          tripDetail['bookings'][i]
                                                  ['passenger'] !=
                                              null
                                      ? tripDetail['bookings'][i]['passenger']
                                              ['profile_image'] ??
                                          ""
                                      : "",
                                  imageType: "network",
                                  context: context),
                              5.widthBox,
                            ],
                          ],
                        ),
                      ],
                    )),
              ),
            ),
          ],
          10.heightBox,
        ],
      ),
    ),
  );
}

/// [InkWell] drives extra mouse-region work on Flutter web and can contribute to
/// `mouse_tracker.dart` assertions when combined with list rebuilds. Use a plain
/// [GestureDetector] on web; keep material ink elsewhere.
Widget _inkWellOrGestureWeb({VoidCallback? onTap, required Widget child}) {
  if (kIsWeb) {
    return GestureDetector(
      behavior: HitTestBehavior.opaque,
      onTap: onTap,
      child: child,
    );
  }
  return InkWell(onTap: onTap, child: child);
}
