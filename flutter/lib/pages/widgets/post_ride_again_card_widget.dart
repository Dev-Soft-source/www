import 'package:dotted_line/dotted_line.dart';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
Widget postRideAgainCardWidget({context, screenWidth, String fromText = "", String toText = "", String depatureAt = "", onTap, Color cardBgColor = Colors.white, String fromLabel = "From", String toLabel = "To"}){
  final trimmedDepartureAt = depatureAt.trim();
  String formattedDepartureAt = "";

  if (trimmedDepartureAt.isNotEmpty) {
    DateTime? parsedDate = DateTime.tryParse(trimmedDepartureAt);

    if (parsedDate == null) {
      for (final format in ['MMMM d, yyyy', 'MMMM dd, yyyy', 'yyyy-MM-dd']) {
        try {
          parsedDate = DateFormat(format).parseStrict(trimmedDepartureAt);
          break;
        } catch (_) {}
      }
    }

    formattedDepartureAt = parsedDate != null
        ? DateFormat('MMMM d, yyyy').format(parsedDate)
        : trimmedDepartureAt;
  }
  
  return InkWell(
    onTap: onTap,
    child: Card(
      elevation: 2,
      color: cardBgColor,
      surfaceTintColor: cardBgColor,
      child: Container(
        padding: EdgeInsets.all(10.0),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          mainAxisAlignment: MainAxisAlignment.start,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 10),
                Container(
                  width: 10,
                  height: 10,
                  decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(50),
                      color: primaryColor
                  ),
                ),
                const SizedBox(
                  height: 30,
                  width: 20,
                  child: Padding(
                    padding: EdgeInsets.only(left: 4.4),
                    child: DottedLine(
                      direction: Axis.vertical,
                      alignment: WrapAlignment.center,
                      lineLength: double.infinity,
                      lineThickness: 1.0,
                      dashLength: 2.0,
                      dashColor: Colors.black,
                      dashRadius: 0.0,
                      dashGapLength: 1.5,
                      dashGapColor: Colors.transparent,
                      dashGapRadius: 0.0,
                    ),
                  ),
                ),
                Container(
                  width: 10,
                  height: 10,
                  decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(50),
                      color: Colors.grey.shade400
                  ),
                ),
              ],
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                txt20Size(title: "$fromLabel:", context: context),
                10.heightBox,
                txt20Size(title: "$toLabel:", context: context),
                if (formattedDepartureAt.isNotEmpty) ...[
                5.heightBox,
                txt20Size(title: "", context: context),
                ]
              ],
            ),
            10.widthBox,
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  txt20Size(title: fromText, context: context, fontFamily: bold),
                  10.heightBox,
                  txt20Size(title: toText, context: context, fontFamily: bold),
                  if (formattedDepartureAt.isNotEmpty) ...[
                    10.heightBox,
                    txt16Size(title: formattedDepartureAt, context: context)
                  ]
                ],
              ),
            )
          ],
        ),
      ),
    ),
  );
}
