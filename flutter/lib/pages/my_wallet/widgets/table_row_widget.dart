import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

TableRow tableRowWidget({
  required BuildContext context,
  String cell1 = "",
  String cell2 = "",
  VoidCallback? onTap,
  bool isHeader = false,
}) {
  Widget buildCell(String title) {
    Widget content = Padding(
      padding: const EdgeInsets.all(8.0),
      child: txt20Size(context: context, title: title),
    );

    if (onTap != null && !isHeader) {
      return InkWell(
        onTap: onTap,
        child: content,
      );
    }
    return content;
  }

  return TableRow(
    decoration: isHeader ? BoxDecoration(color: Colors.grey.shade300) : null,
    children: [
      Column(children: [buildCell(cell1)]),
      Column(children: [buildCell(cell2)]),
    ],
  );
}
