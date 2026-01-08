import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/my_wallet/widgets/table_row_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../../consts/constFileLink.dart';

Widget driverRewardWidget({context}) {
  return Container(
    child: Table(
      border: TableBorder.all(
          color: Colors.grey.shade400,
          style: BorderStyle.solid,
          width: 1
      ),

      children: [
        TableRow( children: [
          Column(children:[txt20Size(context: context, title: "Points")]),
          Column(children:[txt20Size(context: context, title: "Rewards")]),
        ]),
        tableRowWidget(context: context, cell1: "1 to 10", cell2: "T-shirt"),
      ],
    )
  );
}
