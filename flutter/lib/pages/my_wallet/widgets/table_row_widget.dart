

import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';


TableRow tableRowWidget({context, String cell1 = "", String cell2 = ""}) {
  return TableRow( children: [
    Column(
        children:[
          txt16Size(context: context, title: cell1),
      ]
    ),
    Column(
        children:[
          txt16Size(context: context, title: cell2),
        ]
    ),
  ]);
}
