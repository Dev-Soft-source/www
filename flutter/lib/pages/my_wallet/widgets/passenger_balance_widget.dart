import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/data_row_widget.dart';

Widget passengerBalanceWidget({context}) {
  return Container(
    decoration: BoxDecoration(
      borderRadius:
      const BorderRadius
          .all(
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
            title: 'ID',
            data: '3945854',
        ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: 'User',
            data: 'Jackson',
        ),
        const Divider(),
        dataRowWidget(
            context: context,
            title: 'Date',
            data:
            'May 16, 2024',
          ),
        const Divider(),
        // dataRowWidget(
        //     context: context,
        //     title:
        //     'Referral type',
        //     data:
        //     'As a passenger',
        //   ),
        // const Divider(),
        dataRowWidget(
            context: context,
            title:
            'Booking credit',
            data: '\$2',
        ),
        10.heightBox,
      ],
    ),
  );
}
