import 'package:dotted_line/dotted_line.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
Widget postRideAgainCardWidget({context, screenWidth, String fromText = "", String toText = "", onTap, Color cardBgColor = Colors.white, String fromLabel = "From", String toLabel = "To"}){
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
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 10,
                  height: 10,
                  decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(50),
                      color: primaryColor
                  ),
                ),
                const SizedBox(
                  height: 20,
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
                txt18Size(title: "$fromLabel:", context: context),
                5.heightBox,
                txt18Size(title: "$toLabel:", context: context),

              ],
            ),
            10.widthBox,
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  txt16Size(title: fromText, context: context, fontFamily: bold),
                  10.heightBox,
                  txt16Size(title: toText, context: context, fontFamily: bold)
                ],
              ),
            )
          ],
        ),
      ),
    ),
  );
}