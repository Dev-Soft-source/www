import 'dart:developer' as developer;
import 'package:dotted_line/dotted_line.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/helpers/currency_formatter.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';



Widget tripCardFromToWidget({String from = "", String to = "", String pickup = "", String dropOff = "", String price = "",  context,
  String tripStatus = "", onTapReview, bool isRating = false,bool showReviewButton = true, String seatsLeft = "",
  Map? labelTextDetail,
  String fromLabel = "From", String toLabel = "To", String perSeatLabel = "per seat", String seatLeftLabel = "seats left",
  String reviewedLabel = "Reviewed", String reviewDriverLabel = "Review your driver"}){
  final resolvedFromLabel =
      labelTextDetail?['from_label']?.toString() ?? fromLabel;
  final resolvedToLabel =
      labelTextDetail?['to_label']?.toString() ?? toLabel;
  final resolvedPerSeatLabel =
      labelTextDetail?['per_seat_label']?.toString() ?? perSeatLabel;
  final resolvedSeatLeftLabel =
      labelTextDetail?['seats_left_label']?.toString() ?? seatLeftLabel;
  final resolvedReviewedLabel =
      labelTextDetail?['trips_card_section_reviewed']?.toString() ??
          reviewedLabel;
  final resolvedReviewDriverLabel =
      labelTextDetail?['trips_card_section_review_driver']?.toString() ??
          reviewDriverLabel;
  developer.log(
    'labelTextDetail: $labelTextDetail',
    name: 'tripCardFromToWidget',
  );
  Widget routeMarker() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 20,
          height: 20,
          decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(50), color: primaryColor),
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
              color: Colors.grey.shade400),
        ),
      ],
    );
  }

  Widget routeText() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            txt18Size(title: "$resolvedFromLabel: ", context: context),
            Expanded(
              child: txt16Size(
                  title: from, context: context, fontFamily: bold),
            ),
          ],
        ),
        2.heightBox,
        txt16Size(title: pickup, context: context, fontFamily: bold),
        20.heightBox,
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            txt18Size(title: "$resolvedToLabel: ", context: context),
            Expanded(
              child:
                  txt16Size(title: to, context: context, fontFamily: bold),
            ),
          ],
        ),
        2.heightBox,
        txt16Size(title: dropOff, context: context, fontFamily: bold),
      ],
    );
  }

  Widget trailingSummary({required bool isNarrow}) {
    return Align(
      alignment: isNarrow ? Alignment.topRight : Alignment.topRight,
      child: Column(
      crossAxisAlignment:
          CrossAxisAlignment.end,
      children: [
        if (tripStatus != 'search') ...[
          Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              txt24Size(
                  title: formatCurrency(price),
                  context: context,
                  textColor: primaryColor),
              txt16Size(
                title: " $resolvedPerSeatLabel",
                context: context,
                fontFamily: bold,
                textColor: primaryColor,
              ),
            ],
          ),
        ],
        if (tripStatus == "search") ...[
          Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              txt24Size(
                title: seatsLeft,
                context: context,
                textColor: int.parse(seatsLeft.toString()) <= 0
                    ? Colors.red
                    : textColor,
              ),
              txt16Size(
                title: " $resolvedSeatLeftLabel",
                context: context,
                fontFamily: bold,
                textColor: int.parse(seatsLeft.toString()) <= 0
                    ? Colors.red
                    : textColor,
              ),
            ],
          ),
        ],
      ],
    ),
    );
  }

  Widget reviewAction() {
    return elevatedButtonWidget(
        textWidget: txt16Size(
            title:
                isRating ? resolvedReviewedLabel : resolvedReviewDriverLabel,
            context: context,
            textColor: Colors.white),
        onPressed: onTapReview,
        context: context,
        btnColor: btnPrimaryColor);
  }

  return Container(
    padding: EdgeInsets.fromLTRB(
        getValueForScreenType<double>(
          context: context,
          mobile: 15.0,
          tablet: 15.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 15.0,
          tablet: 15.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 15.0,
          tablet: 15.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 0.0,
          tablet: 0.0,
        )),
    child: LayoutBuilder(
      builder: (context, constraints) {
        final isNarrow = constraints.maxWidth < 340;

        if (isNarrow) {
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  routeMarker(),
                  10.widthBox,
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Expanded(
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  txt18Size(
                                      title: "$resolvedFromLabel: ",
                                      context: context),
                                  Expanded(
                                    child: txt16Size(
                                        title: from,
                                        context: context,
                                        fontFamily: bold),
                                  ),
                                ],
                              ),
                            ),
                            8.widthBox,
                            Flexible(child: trailingSummary(isNarrow: false)),
                          ],
                        ),
                        2.heightBox,
                        txt16Size(
                            title: pickup, context: context, fontFamily: bold),
                        20.heightBox,
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            txt18Size(
                                title: "$resolvedToLabel: ",
                                context: context),
                            Expanded(
                              child: txt16Size(
                                  title: to,
                                  context: context,
                                  fontFamily: bold),
                            ),
                          ],
                        ),
                        2.heightBox,
                        txt16Size(
                            title: dropOff,
                            context: context,
                            fontFamily: bold),
                      ],
                    ),
                  ),
                ],
              ),
              if (tripStatus == "completed" && (isRating || showReviewButton)) ...[
                12.heightBox,
                Padding(
                  padding: const EdgeInsets.only(left: 30),
                  child: reviewAction(),
                ),
              ]
            ],
          );
        }

        return Row(
          mainAxisAlignment: MainAxisAlignment.start,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            routeMarker(),
            10.widthBox,
            Expanded(child: routeText()),
            10.widthBox,
            trailingSummary(isNarrow: false),
          ],
        );
      },
    ),
  );
}
