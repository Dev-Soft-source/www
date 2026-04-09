import 'package:flutter/material.dart';
import 'package:proximaride_app/helpers/currency_formatter.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/trip_route_from_to_rail.dart';
import '../../consts/constFileLink.dart';



Widget tripCardFromToWidget({String from = "", String to = "", String pickup = "", String dropOff = "", String price = "",  context,
  String tripStatus = "", onTapReview, bool isRating = false,bool showReviewButton = true, String seatsLeft = "",
  Map? labelTextDetail,
  String fromLabel = "From", String toLabel = "To", String perSeatLabel = "per seat", String seatLeftLabel = "seats left",
  String reviewedLabel = "Reviewed", String reviewDriverLabel = "Review your driver"}){
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
  final seatsLeftParsed = int.tryParse(seatsLeft.toString()) ?? 0;

  Widget trailingSummary({required bool isNarrow}) {
    return Align(
      alignment: isNarrow ? Alignment.topRight : Alignment.topRight,
      child: Column(
      crossAxisAlignment:
          CrossAxisAlignment.end,
      children: [
        // if (tripStatus != 'search') ...[
          Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              txt24Size(
                  title: formatCurrency(price),
                  context: context,
                  textColor: primaryColor),
              Transform.translate(
                offset: const Offset(0, -4),
                child: txt14Size(
                  title: resolvedPerSeatLabel,
                  context: context,
                  fontFamily: bold,
                  textColor: placeHolderColor,
                ),
              ),
            ],
          ),
        // ],
        if (tripStatus == "search") ...[
          Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              txt24Size(
                title: seatsLeft,
                context: context,
                textColor: seatsLeftParsed <= 0
                    ? Colors.red
                    : textColor,
              ),
              Transform.translate(
                offset: const Offset(0, -4),
                child: txt14Size(
                  title: resolvedSeatLeftLabel,
                  context: context,
                  fontFamily: bold,
                  textColor: seatsLeftParsed <= 0
                      ? Colors.red
                      : placeHolderColor,
                ),
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
          mobile: 10.0,
          tablet: 10.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 10.0,
          tablet: 10.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 10.0,
          tablet: 10.0,
        ),
        getValueForScreenType<double>(
          context: context,
          mobile: 0.0,
          tablet: 0.0,
        )),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.start,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Expanded(
          child: TripRouteFromToRail(
            from: from,
            pickup: pickup,
            to: to,
            dropOff: dropOff,
          ),
        ),
        5.widthBox,
        trailingSummary(isNarrow: false),
      ],
    ),
  );
}
