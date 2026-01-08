import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/circle_icon_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/trip_card_date_time_widget.dart';
import 'package:proximaride_app/pages/widgets/trip_card_from_to_widget.dart';

Widget tripCardWidget({controller, context, tripDetail,
  String bookSeat = "", String tripStatus = "", rating, onTapTripCard,
  Color cardBgColor = Colors.white,leaveReviewDays = 0, String bookingDeparture = "", String bookingDestination = "", String bookingPrice = ""}){
  bool showReviewButton = true;
  String tripDate = "";
  if(tripDetail['date'] != null){
    DateTime parsedDate = DateTime.parse(tripDetail['date']);
    DateFormat outputFormat = DateFormat('MMMM d, yyyy');
    tripDate = outputFormat.format(parsedDate);


    DateTime futureDate = parsedDate.add(Duration(days: leaveReviewDays));
    DateTime currentDate = DateTime.now();
    if (currentDate.isAfter(futureDate)) {
      showReviewButton = false;
    }
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

  return InkWell(
    onTap: onTapTripCard,
    child: Card(
      surfaceTintColor: cardBgColor,
      elevation: 2,
      color: cardBgColor,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          tripCardDateTimeWidget(date: tripDate, time: tripTime, tripStatus: tripStatus, context: context,
              atLabel: "${controller.labelTextDetail['card_section_at_label'] ?? 'at'}",
              seatLeftLabel: "${controller.labelTextDetail['card_section_seats_left'] ?? 'seats left'}",
              perSeatLabel: "${controller.labelTextDetail['card_section_per_seat'] ?? 'per seat'}",
              notLiveLabel: "${controller.labelTextDetail['card_section_not_live'] ?? 'Not live'}",
              bookingRequestLabel: "${controller.labelTextDetail['card_section_booking_request'] ?? 'booking request'}",
              completedStatusLabel: "${controller.labelTextDetail['card_section_completed'] ?? 'Completed'}",
              cancelStatusLabel: "${controller.labelTextDetail['card_section_cancelled'] ?? 'Cancelled'}"
          ),
          tripCardFromToWidget(
              from: bookingDeparture,
              to: bookingDestination,
              price: bookingPrice,
              pickup: "${tripDetail['pickup']}",
              dropOff: "${tripDetail['dropoff']}",
              context:  context,
              tripStatus: tripStatus,
              fromLabel: "${controller.labelTextDetail['card_section_from_label'] ?? 'From'}",
              toLabel: "${controller.labelTextDetail['card_section_to_label'] ?? 'to'}",
              seatLeftLabel: "${controller.labelTextDetail['card_section_seats_left'] ?? 'seats left'}",
              perSeatLabel: "${controller.labelTextDetail['card_section_per_seat'] ?? 'per seat'}",
              reviewedLabel: "${controller.labelTextDetail['trips_card_section_reviewed'] ?? 'Reviewed'}",
              reviewDriverLabel: "${controller.labelTextDetail['trips_card_section_review_driver'] ?? 'Review your driver'}",
              isRating: rating != null ? true : false,
              onTapReview: tripStatus == "completed" ?
                  () async{
                    if(rating != null){
                      Get.toNamed('/review_detail/${rating['id']}/from/driver');
                    }else{
                      if(showReviewButton)
                        {
                          await controller.addDriverReview(tripDetail['id'], "driver");
                        }
                    }
                  } :
              null,
            showReviewButton: showReviewButton
          ),
          10.heightBox,
          const Divider(indent: 0, height: 0,),
          Row(
            mainAxisAlignment: MainAxisAlignment.start,
            children: [
              Expanded(
                  child: Center(child: txt20Size(title: "$bookSeat ${controller.labelTextDetail['trips_card_section_seat_booked'] ?? "seats booked"}", context: context))
              ),
              5.widthBox,
              SizedBox(height: 40, width: 1, child: Container(color: Colors.grey.shade400)),
              5.widthBox,
              Expanded(
                  child: Center(child: txt20Size(title: "${tripDetail['seats_left']} ${controller.labelTextDetail['trips_card_section_seat_available'] ?? "seats available"}", context: context))
              ),
            ],
          ),
          const Divider(indent: 0, height: 0,),
          10.heightBox,
          Container(
            padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
              context: context,
              mobile: 15.0,
              tablet: 15.0,
            ),getValueForScreenType<double>(
              context: context,
              mobile: 0.0,
              tablet: 0.0,
            ),getValueForScreenType<double>(
              context: context,
              mobile: 15.0,
              tablet: 15.0,
            ),getValueForScreenType<double>(
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
                  circleImageWidget(width: 30.0, height: 30.0, imagePath: "${tripDetail['vehicle'] != null ? tripDetail['vehicle']['image'] : tripDetail['car_image']}", imageType: "network", context: context),
                  if(tripDetail['features'].isNotEmpty)...[
                    for(var i = 0; i < tripDetail['features'].length; i++)...[
                      2.widthBox,
                      circleIconWidget(width: 30.0, height: 30.0, imagePath: tripDetail['features'][i]['image'], context: context),
                      2.widthBox,
                    ]
                  ]else...[
                    2.widthBox,
                  ],
                  if(tripDetail['payment_method_image'] != null)...[
                    circleIconWidget(width: 30.0, height: 30.0, imagePath: tripDetail['payment_method_image'], context: context),
                    2.widthBox,
                  ],
                  if(tripDetail['booking_method_image'] != null)...[
                    circleIconWidget(width: 30.0, height: 30.0, imagePath: tripDetail['booking_method_image'], context: context),
                    2.widthBox,
                  ],
                  if(tripDetail['animal_friendly_image'] != null)...[
                    circleIconWidget(width: 30.0, height: 30.0, imagePath: tripDetail['animal_friendly_image'], context: context),
                    2.widthBox,
                  ],
                  if(tripDetail['smoke_image'] != null)...[
                    circleIconWidget(width: 30.0, height: 30.0, imagePath: tripDetail['smoke_image'], context: context),
                    2.widthBox,
                  ],
                  if(tripDetail['luggage_image'] != null)...[
                    circleIconWidget(width: 30.0, height: 30.0, imagePath: tripDetail['luggage_image'], context: context),
                    2.widthBox,
                  ],

                ],
              ),
            ),
          ),
          const Divider(),
          Container(
            padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
              context: context,
              mobile: 15.0,
              tablet: 15.0,
            ),getValueForScreenType<double>(
              context: context,
              mobile: 0.0,
              tablet: 0.0,
            ),getValueForScreenType<double>(
              context: context,
              mobile: 15.0,
              tablet: 15.0,
            ),getValueForScreenType<double>(
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
                  circleImageWidget(width: 40.0, height: 40.0, imagePath: tripDetail['driver'] != null ? (tripDetail['driver']['profile_image'] ?? ""
                      "") : "", imageType: "network", context: context),
                  5.widthBox,
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      txt20SizeCapitalize(title: "${tripDetail['driver'] != null ? tripDetail['driver']['first_name'] : ""} ${tripDetail['driver'] != null ? tripDetail['driver']['last_name'] : ""}", context: context),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: [
                          txt16Size(title: "${controller.labelTextDetail['card_section_age'] ?? "Age"}:",context: context),
                          txt16Size(title: "${tripDetail['driver'] != null ? tripDetail['driver']['age'] : ""}",context: context),
                          5.widthBox,
                          SizedBox(width: 1, height: 15, child: Container(color: Colors.grey.shade400)),
                          5.widthBox,
                          txt16Size(title: "${tripDetail['driver'] != null ? tripDetail['driver']['gender_label'] : ""}",context: context),
                          5.widthBox,
                          SizedBox(width: 1, height: 15, child: Container(color: Colors.grey.shade400)),
                          5.widthBox,
                          txt16Size(title: "${controller.labelTextDetail['card_section_driven'] ?? "Driven"}:",context: context),
                          txt16Size(title: "${tripDetail['driver'] != null ? tripDetail['driver']['driven_rides'] : ""}",context: context),
                          5.widthBox,
                          SizedBox(width: 1, height: 15, child: Container(color: Colors.grey.shade400)),
                          5.widthBox,
                          txt16Size(title: "${controller.labelTextDetail['card_section_review'] ?? "Review"}:",context: context),
                          txt16Size(title: "${tripDetail['driver'] != null && tripDetail['driver']['average_rating'] != null ? tripDetail['driver']['average_rating'].toStringAsFixed(1)  : ""}",context: context),
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
    ),
  );
}