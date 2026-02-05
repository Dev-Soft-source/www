import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/data_row_widget.dart';

Widget myBalanceWidget({context, required data, controller, bool changeColor = false}) {

  String tripDate = "";
  if(data['added_date'] != null){
    DateTime parsedDate = DateTime.parse(data['added_date']);
    DateFormat outputFormat = DateFormat('MMMM d, yyyy');
    tripDate = outputFormat.format(parsedDate);
  }


  var idText = "";
  if(data['dr_amount'] != null && data['dr_amount'] != 0.0 && data['booking_id'] == null){
    idText = "Top-up transaction";
  }else if(data['dr_amount'] != null && data['dr_amount'] != 0.0 && data['booking_id'] != null){
    idText = "Refund transaction";
  }else if(data['cr_amount'] != null && data['cr_amount'] != 0.0){
    idText = "Payment transaction";
  }

  return Container(
    decoration: BoxDecoration(
      color: changeColor == true ? Colors.white : Colors.grey.shade200,
      borderRadius: const BorderRadius.all(
          Radius.circular(7.0)),
      border: Border.all(
        color: Colors.grey,
        width: 1.0,
      ),
    ),
    padding: const EdgeInsets.only(
        left: 0, right: 0, top: 10),
    child: Column(
      children: [
        dataRowWidget(
            context: context,
            title: "$idText ${controller.labelTextDetail['balance_id_label'] ?? 'ID'}",
            data: '${data['random_id']}',
        ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['balance_amount_label'] ?? 'Amount'}",
            data: '${data['cr_amount'] != null && data['cr_amount'] != "" ? "-" : "+"} \$${ data['cr_amount'] ?? data['dr_amount'] }',
            priceColor:  data['cr_amount'] != null ? Colors.red : Colors.green
        ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['balance_date_label'] ?? 'Date'}",
            data: tripDate,
        ),
      ],
    ),
  );
}
