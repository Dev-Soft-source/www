import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/data_row_widget.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/table_row_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../../consts/constFileLink.dart';

Widget driverAvailableWidget({context, required data, controller, bool changeColor = false}) {

  String tripDate = "";
  if(data['ride']['completed_date'] != null){
    DateTime parsedDate = DateTime.parse(data['ride']['completed_date']);
    DateFormat outputFormat = DateFormat('MMMM d, yyyy');
    tripDate = outputFormat.format(parsedDate);
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
        if(data['status'] == "request")...[
          Align(
            alignment: Alignment.centerRight,
            child: Padding(
              padding: EdgeInsets.only(right: 5.0),
              child: Container(
                padding: EdgeInsets.all(5.0),
                decoration: BoxDecoration(
                  color: Colors.amber,
                  border: Border.all(width: 1, style: BorderStyle.solid, color: Colors.amber),
                  borderRadius: BorderRadius.circular(5.0),
                ),
                child: txt18Size(title: "In progress", context: context, textColor: Colors.white),
              ),
            ),
          ),
          5.heightBox,
        ],
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['driver_available_ride_id_label'] ?? 'Ride ID'}",
            data: '${data['ride']['random_id']}',
        ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['driver_available_from_label'] ?? 'From'}",
            data: '${data['ride']['default_ride_detail'][0]['departure']}',
        ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['driver_available_to_label'] ?? 'To'}",
            data: '${data['ride']['default_ride_detail'][0]['destination']}',
        ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['driver_available_date_label'] ?? 'Date'}",
            data: tripDate,
        ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: "${controller.labelTextDetail['driver_available_total_amount_label'] ?? 'Total amount'}",
            data: '\$${data['total_payout_cost']}',
            onTap: (() {
              Get.toNamed('/ride_fair_detail/${data['ride_id']}/available');
            })),
        10.heightBox,
      ],
    ),
  );
}
