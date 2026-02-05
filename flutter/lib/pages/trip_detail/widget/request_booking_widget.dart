import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/trip_detail/widget/request_booking_card_widget.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget requestBookingWidget({context, bookingRequestList, double screenWidth = 0.0, controller}){
  return cardShadowWidget(
      context: context,
      widgetChild: Column(
        children: [
          postRideWidget(title: "${controller.labelTextDetail['booking_request_heading'] ?? "You have the following booking requests"}", screenWidth: screenWidth, context: context),
          if(bookingRequestList.isNotEmpty)...[
            for(var booking in bookingRequestList)...[
              requestBookingCardWidget(context: context, booking: booking, onPressedAccept: () async{
                await controller.updateBookingStatus('accept', booking['id']);
              }, onPressedReject: () async{
                await controller.updateBookingStatus('reject', booking['id']);
              },
              ageLabel: "${controller.labelTextDetail['driver_age_label'] ?? "Age"}",
              reviewLabel: "${controller.labelTextDetail['review_label'] ?? "Review"}",
              seatRequestLabel: "${controller.labelTextDetail['seat_requested_label'] ?? "seat requested"}",
              acceptBtnLabel: "${controller.labelTextDetail['request_accept_label'] ?? "Accept"}",
              rejectBtnLabel: "${controller.labelTextDetail['request_reject_label'] ?? "Reject"}"
              ),
              if(bookingRequestList.length == 2)...[
              Divider(),]
            ],
          ]
        ],
      )
  );
}