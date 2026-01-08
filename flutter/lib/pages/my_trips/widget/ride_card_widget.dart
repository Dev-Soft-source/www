import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
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
  String tripDate = "";
  if (tripDetail['date'] != null) {
    DateTime parsedDate = DateTime.parse(tripDetail['date']);
    DateFormat outputFormat = DateFormat('MMMM d, yyyy');
    tripDate = outputFormat.format(parsedDate);
  }


  String tripTime = "";
  if(tripDetail['time'] != null) {
    DateTime parsedTime = DateFormat("HH:mm:ss").parse(tripDetail['time']);
    if(parsedTime.hour == 12 && parsedTime.minute == 0){
      DateFormat outputTimeFormat = DateFormat("h:mm");
      tripTime = "${outputTimeFormat.format(parsedTime)} noon";
    }else if(parsedTime.hour == 0 && parsedTime.minute == 0){
      DateFormat outputTimeFormat = DateFormat("h:mm");
      tripTime = "${outputTimeFormat.format(parsedTime)} midnight";
    }else{
      DateFormat outputTimeFormat = DateFormat("h:mm a");
      tripTime = outputTimeFormat.format(parsedTime);
    }
  }

  var requestCount = tripDetail['booking_requests'] != null
      ? tripDetail['booking_requests'].length
      : 0;

  return InkWell(
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
            isLive: tripStatus == "upcoming" ? (tripDetail['vehicle_id'] == null) ? false : true : true,
            atLabel: "${controller.labelTextDetail['card_section_at_label'] ?? 'at'}",
            seatLeftLabel: "${controller.labelTextDetail['card_section_seats_left'] ?? 'seats left'}",
            perSeatLabel: "${controller.labelTextDetail['card_section_per_seat'] ?? 'per seat'}",
            notLiveLabel: "${controller.labelTextDetail['card_section_not_live'] ?? 'Not live'}",
            bookingRequestLabel: "${controller.labelTextDetail['card_section_booking_request'] ?? 'booking request'}",
            completedStatusLabel: "${controller.labelTextDetail['card_section_completed'] ?? 'Completed'}",
            totalSeat: "${tripDetail['seats']}",
            cancelStatusLabel: "${controller.labelTextDetail['card_section_cancelled'] ?? 'Cancelled'}"
          ),
          tripCardFromToWidget(
              from: "${tripDetail['ride_detail'][0]['departure']}",
              to: "${tripDetail['ride_detail'][0]['destination']}",
              price: "${tripDetail['ride_detail'][0]['price']}",
              pickup: "${tripDetail['pickup']}",
              dropOff: "${tripDetail['dropoff']}",
              fromLabel: "${controller.labelTextDetail['card_section_from_label'] ?? 'From'}",
              toLabel: "${controller.labelTextDetail['card_section_to_label'] ?? 'to'}",
              seatLeftLabel: "${controller.labelTextDetail['card_section_seats_left'] ?? 'seats left'}",
              perSeatLabel: "${controller.labelTextDetail['card_section_per_seat'] ?? 'per seat'}",
              reviewedLabel: "${controller.labelTextDetail['trips_card_section_reviewed'] ?? 'Reviewed'}",
              reviewDriverLabel: "${controller.labelTextDetail['trips_card_section_review_driver'] ?? 'Review your driver'}",
              context: context),
          const Divider(),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              ridePriceInfoWidget(
                  title: "${controller.labelTextDetail['card_section_booked'] ?? "Booked"}",
                  value: "${tripDetail['booked_seats']} ${controller.labelTextDetail['card_section_seats'] ?? "seats"}",
                  context: context),
              const Divider(),
              ridePriceInfoWidget(
                  title: "${controller.labelTextDetail['card_section_seats_fee'] ?? "Fare"}",
                  value: "\$${tripDetail['ride_detail'][0]['price']}",
                  context: context),
              const Divider(),
              ridePriceInfoWidget(
                  title: "${controller.labelTextDetail['card_section_booking_fee'] ?? "Booking fee"}",
                  value: "\$${tripDetail['booking_fee']}",
                  context: context),
              const Divider(),
              ridePriceInfoWidget(
                  title: "${controller.labelTextDetail['card_section_amount'] ?? "Total amount"}",
                  value: "\$${tripDetail['total_amount']}",
                  context: context),
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
                      imagePath: "${tripDetail['vehicle'] != null ? tripDetail['vehicle']['image'] : tripDetail['car_image']}",
                      imageType: "network",
                      context: context),
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
                ],
              ),
            ),
          ),
          if (tripDetail['bookings'].isNotEmpty) ...[
            const Divider(),
            InkWell(
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
                              txt16Size(
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
                                  width: 34.0,
                                  height: 34.0,
                                  imagePath:
                                      tripDetail['bookings'][i] != null &&
                                              tripDetail['bookings'][i]
                                                      ['passenger'] !=
                                                  null
                                          ? tripDetail['bookings'][i]['passenger']['profile_image'] ?? ""
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
