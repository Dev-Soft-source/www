import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/trip_detail/widget/seat_booked_row_widget.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget seatBookedWidget({context, String booked = "", String fare = "", String fee = "", String totalAmount = "",  double screenWidth = 0.0, controller}){
  return cardShadowWidget(
      context: context,
      widgetChild: Column(
        children: [
          postRideWidget(title: "${controller.labelTextDetail['mobile_seat_booked_heading'] ?? "Seats booked"}", screenWidth: screenWidth, context: context),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              10.heightBox,
              seatBookedRowWidget(context: context, title: "${controller.labelTextDetail['mobile_seat_booked_label'] ?? "Booked"}", value: booked),
              const Divider(),
              seatBookedRowWidget(context: context, title: "${controller.labelTextDetail['mobile_seat_fare_label'] ?? "Fare"}", value: fare),
              const Divider(),
              seatBookedRowWidget(context: context, title: "${controller.labelTextDetail['mobile_seat_booking_fee_label'] ?? "Booking fee"}", value: fee),
              const Divider(),
              seatBookedRowWidget(context: context, title: "${controller.labelTextDetail['mobile_seat_total_amount_label'] ?? "Total Amount"}", value: totalAmount),
              10.heightBox
            ],
          )

        ],
      )
  );
}