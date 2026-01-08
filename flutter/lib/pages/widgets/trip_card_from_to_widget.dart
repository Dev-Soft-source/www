
import 'package:dotted_line/dotted_line.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';



Widget tripCardFromToWidget({String from = "", String to = "", String pickup = "", String dropOff = "", String price = "",  context,
  String tripStatus = "", onTapReview, bool isRating = false,bool showReviewButton = true, String seatsLeft = "",
  String fromLabel = "From", String toLabel = "To", String perSeatLabel = "per seat", String seatLeftLabel = "seats left",
  String reviewedLabel = "Reviewed", String reviewDriverLabel = "Review your driver"}){
  return Container(
    padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
      context: context,
      mobile: 15.0,
      tablet: 15.0,
    ),getValueForScreenType<double>(
      context: context,
      mobile: 15.0,
      tablet: 15.0,
    ),getValueForScreenType<double>(
      context: context,
      mobile: 15.0,
      tablet: 15.0,
    ),getValueForScreenType<double>(
      context: context,
      mobile: 0.0,
      tablet: 0.0,
    )),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.start,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 20,
              height: 20,
              decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(50),
                  color: primaryColor
              ),
            ),
            const SizedBox(
              height: 60,
              child: Padding(
                padding: EdgeInsets.only(left: 9),
                child: DottedLine(
                  direction: Axis.vertical,
                  alignment: WrapAlignment.center,
                  lineLength: double.infinity,
                  lineThickness: 1.0,
                  dashLength: 2.0,
                  dashColor: Colors.black,
                  dashRadius: 0.0,
                  dashGapLength: 1.0,
                  dashGapColor: Colors.transparent,
                  dashGapRadius: 0.0,
                ),
              ),
            ),
            Container(
              width: 20,
              height: 20,
              decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(50),
                  color: Colors.grey.shade400
              ),
            ),
          ],
        ),
        10.widthBox,
        Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    txt18Size(title: "$fromLabel: ", context: context),
                    Expanded(child: txt16Size(title: from, context: context, fontFamily: bold)),
                  ],
                ),
                2.heightBox,
                txt16Size(title: pickup, context: context, fontFamily: bold),
                20.heightBox,
                Row(
                  children: [
                    txt18Size(title: "$toLabel: ", context: context),
                    Expanded(child: txt16Size(title: to, context: context, fontFamily: bold)),
                  ],
                ),
                2.heightBox,
                txt16Size(title: dropOff, context: context, fontFamily: bold),
              ],
            )
        ),
        10.widthBox,
        Column(
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            if(tripStatus != 'search')...[
            RichText(
              text: TextSpan(
                children: [
                  textSpan(title: "\$$price", context: context, fontFamily: bold, textColor: textColor, textSize: 24.0),
                  textSpan(title: " $perSeatLabel", context: context, fontFamily: bold, textColor: textColor, textSize: 16.0),
                ],
              ),
            ),
            ],

            15.heightBox,
            if(tripStatus == "search")...[
              RichText(
                text: TextSpan(
                  children: [
                    textSpan(title: seatsLeft, context: context, fontFamily: bold, textColor: int.parse(seatsLeft.toString()) <= 0 ? Colors.red : textColor, textSize: 24.0),
                    textSpan(title: " $seatLeftLabel", context: context, fontFamily: bold, textColor: int.parse(seatsLeft.toString()) <= 0 ? Colors.red : textColor, textSize: 16.0),
                  ],
                ),
              ),

            ],
            if(tripStatus == "completed")...[
              if(isRating || showReviewButton)...[
              elevatedButtonWidget(
                textWidget: txt16Size(title: isRating ? reviewedLabel :  reviewDriverLabel, context: context, textColor: Colors.white),
                onPressed: onTapReview,
                context: context,
               btnColor: btnPrimaryColor
              )
              ]
            ]
          ],
        )
      ],
    ),
  );
}
