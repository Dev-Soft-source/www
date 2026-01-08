import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/data_row_widget.dart';

Widget myRideWidget({context, required myRide, controller, bool changeColor = false}) {

  String tripDate = "";
  if(myRide['ride']['completed_date'] != null){
    DateTime parsedDate = DateTime.parse(myRide['ride']['completed_date']);
    DateFormat outputFormat = DateFormat('MMMM d, yyyy');
    tripDate = outputFormat.format(parsedDate);
  }

  double bookingFee = 0;
  double bookingAmt = 0;
  double cancelBookingFee = 0;
  double cancelBookingAmt = 0;

  if(myRide['booking_transaction_sum'].length > 0 && myRide['booking_transaction_sum'][0] != null){
    bookingAmt = double.parse(myRide['booking_transaction_sum'][0]['booking_transaction_sum'].toString());
  }

  if(myRide['booking_cancel_transaction_sum'].length > 0 && myRide['booking_cancel_transaction_sum'][0] != null){
    cancelBookingAmt = double.parse(myRide['booking_cancel_transaction_sum'][0]['booking_cancel_transaction_sum'].toString());
  }

  if(myRide['booking_credit_sum'].length > 0  && myRide['booking_credit_sum'][0] != null){
    bookingFee = double.parse(myRide['booking_credit_sum'][0]['booking_credit_sum'].toString());
  }

  if(myRide['booking_credit_cancel_sum'].length > 0  && myRide['booking_credit_cancel_sum'][0] != null){
    cancelBookingFee = double.parse(myRide['booking_credit_cancel_sum'][0]['booking_credit_cancel_sum'].toString());
  }

  return Container(
    decoration: BoxDecoration(
      color: changeColor == true ? Colors.white : Colors.grey.shade200,
      borderRadius:
      const BorderRadius.all(
          Radius.circular(
              7.0)),
      border: Border.all(
        color: Colors.grey,
        width: 1.0,
      ),
    ),
    padding:
    const EdgeInsets.only(
        left: 0,
        right: 0,
        top: 10),
    child: Column(
      children: [
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['passenger_ride_id_label'] ?? 'Ride ID'}",
            data: '${myRide['ride']['random_id']}',
        ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['passenger_my_ride_from_label'] ?? 'From'}",
            data: '${myRide['departure']}',
            ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['passenger_my_ride_to_label'] ?? 'To'}",
            data: '${myRide['destination']}',
          ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['passenger_my_ride_date_label'] ?? 'Date'}",
            data: tripDate,
          ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['passenger_my_ride_booking_fee_label'] ?? 'Booking fee'}",
            data: '\$${(bookingFee - cancelBookingFee).toStringAsFixed(1)}',
            ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['passenger_my_ride_fare_label'] ?? 'Fare'}",
            data: '\$${((bookingAmt - cancelBookingAmt) - (bookingFee - cancelBookingFee)).toStringAsFixed(1)}',
        ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['passenger_my_ride_total_amount_label'] ?? 'Total amount'}",
            data: '\$${(bookingAmt - cancelBookingAmt).toStringAsFixed(1)}',
        ),
        10.heightBox,
      ],
    ),
  );
}
