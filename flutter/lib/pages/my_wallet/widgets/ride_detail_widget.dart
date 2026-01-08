import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/data_row_widget.dart';

Widget rideDetailWidget({context, required data, controller}) {

  double bookingFee = 0;
  double bookingAmt = 0;
  double cancelBookingFee = 0;
  double cancelBookingAmt = 0;

  if(data['booking_transaction_sum'].length > 0 && data['booking_transaction_sum'][0] != null){
    bookingAmt = double.parse(data['booking_transaction_sum'][0]['booking_transaction_sum'].toString());
  }

  if(data['booking_cancel_transaction_sum'].length > 0 && data['booking_cancel_transaction_sum'][0] != null){
    cancelBookingAmt = double.parse(data['booking_cancel_transaction_sum'][0]['booking_cancel_transaction_sum'].toString());
  }

  if(data['booking_credit_sum'].length > 0  && data['booking_credit_sum'][0] != null){
    bookingFee = double.parse(data['booking_credit_sum'][0]['booking_credit_sum'].toString());
  }

  if(data['booking_credit_cancel_sum'].length > 0  && data['booking_credit_cancel_sum'][0] != null){
    cancelBookingFee = double.parse(data['booking_credit_cancel_sum'][0]['booking_credit_cancel_sum'].toString());
  }

  return Container(
    decoration: BoxDecoration(
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
          title:
          "${controller.labelTextDetail['passenger_label'] ?? 'Passenger'}",
          data:
          '${data['passenger']['first_name']}',
        ),
        const Divider(),
        dataRowWidget(
          context: context,
          title:
          "${controller.labelTextDetail['fare_label'] ?? 'Fare'}",
          data: '\$${(((bookingAmt - cancelBookingAmt) - (bookingFee - cancelBookingFee))- cancelBookingFee).toStringAsFixed(1)}',
        ),
        const Divider(),
        dataRowWidget(
          context: context,
          title: "${controller.labelTextDetail['booking_fee_label'] ?? 'Booking fee'}",
          data: '\$${(bookingFee - cancelBookingFee).toStringAsFixed(1)}',
        ),
        const Divider(),
        dataRowWidget(
          context: context,
          title: "${controller.labelTextDetail['total_label'] ?? 'Total'}",
          data: '\$${( ((bookingAmt - cancelBookingAmt) - cancelBookingFee)).toStringAsFixed(1)}',
        ),

      ],
    ),
  );

}
