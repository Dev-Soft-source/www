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
    final content = Container(
      alignment: Alignment.center,
      constraints: const BoxConstraints(minHeight: 44),
      padding: const EdgeInsets.all(8.0),
      child: txt20SizeAlignCenter(context: context, title: title),
    );

    return TableCell(
      verticalAlignment: TableCellVerticalAlignment.middle,
      child: onTap != null && !isHeader
          ? InkWell(
              onTap: onTap,
              child: content,
            )
          : content,
    );
  }

  return TableRow(
    decoration: isHeader ? BoxDecoration(color: Colors.grey.shade300) : null,
    children: [
      buildCell(cell1),
      buildCell(cell2),
    ],
  );
}
