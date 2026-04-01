import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget requestBookingCardWidget({
  context,
  booking,
  onPressedAccept,
  onPressedReject,
  String ageLabel = "Age",
  String reviewLabel = "Review",
  String seatRequestLabel = "seat requested",
  String acceptBtnLabel = "Accept",
  String rejectBtnLabel = "Reject",
}) {
  return _RequestBookingCard(
    context: context,
    booking: booking,
    onPressedAccept: onPressedAccept,
    onPressedReject: onPressedReject,
    ageLabel: ageLabel,
    reviewLabel: reviewLabel,
    seatRequestLabel: seatRequestLabel,
    acceptBtnLabel: acceptBtnLabel,
    rejectBtnLabel: rejectBtnLabel,
  );
}

class _RequestBookingCard extends StatefulWidget {
  final dynamic context;
  final dynamic booking;
  final VoidCallback? onPressedAccept;
  final VoidCallback? onPressedReject;
  final String ageLabel;
  final String reviewLabel;
  final String seatRequestLabel;
  final String acceptBtnLabel;
  final String rejectBtnLabel;

  const _RequestBookingCard({
    required this.context,
    required this.booking,
    required this.onPressedAccept,
    required this.onPressedReject,
    required this.ageLabel,
    required this.reviewLabel,
    required this.seatRequestLabel,
    required this.acceptBtnLabel,
    required this.rejectBtnLabel,
  });

  @override
  State<_RequestBookingCard> createState() => _RequestBookingCardState();
}

class _RequestBookingCardState extends State<_RequestBookingCard>
    with SingleTickerProviderStateMixin {
  late final AnimationController _glowController;

  @override
  void initState() {
    super.initState();
    _glowController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1800),
      lowerBound: 0.0,
      upperBound: 1.0,
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _glowController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final passenger = widget.booking['passenger'];
    final reviewText = (passenger['average_rating'] == null ||
            "${passenger['average_rating']}".isEmpty)
        ? "No review yet"
        : "${widget.reviewLabel}: ${passenger['average_rating'].ceil()}";

    return AnimatedBuilder(
      animation: _glowController,
      builder: (context, child) {
        final glowStrength = 0.18 + (_glowController.value * 0.12);
        final flickerPhase = _glowController.value < 0.18 ||
                (_glowController.value > 0.34 && _glowController.value < 0.44) ||
                (_glowController.value > 0.72 && _glowController.value < 0.82)
            ? 1.0
            : 0.0;
        final borderOpacity =
            0.48 + (_glowController.value * 0.18) + (flickerPhase * 0.24);

        return Container(
          margin: const EdgeInsets.symmetric(vertical: 6.0),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(14.0),
            border: Border.all(
              color: primaryColor.withOpacity(borderOpacity.clamp(0.0, 1.0)),
              width: 1.6,
            ),
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                color: primaryColor
                    .withOpacity((glowStrength + (flickerPhase * 0.10)).clamp(0.0, 1.0)),
                blurRadius: 18 + (_glowController.value * 6) + (flickerPhase * 8),
                spreadRadius: 1.5 + (flickerPhase * 1.2),
                offset: const Offset(0, 6),
              ),
            ],
          ),
          child: child,
        );
      },
      child: Container(
        padding: EdgeInsets.fromLTRB(
          getValueForScreenType<double>(
            context: widget.context,
            mobile: 15.0,
            tablet: 15.0,
          ),
          getValueForScreenType<double>(
            context: widget.context,
            mobile: 12.0,
            tablet: 12.0,
          ),
          getValueForScreenType<double>(
            context: widget.context,
            mobile: 15.0,
            tablet: 15.0,
          ),
          getValueForScreenType<double>(
            context: widget.context,
            mobile: 12.0,
            tablet: 12.0,
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      circleImageWidget(
                        width: 60.0,
                        height: 60.0,
                        imageType: "network",
                        imagePath: passenger != null ? passenger['profile_image'] : "",
                        context: widget.context,
                      ),
                      8.widthBox,
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            txt20SizeCapitalize(
                              title: "${passenger['first_name']}",
                              context: widget.context,
                            ),
                            5.heightBox,
                            txt20Size(
                              context: widget.context,
                              title:
                                  "${widget.ageLabel}: ${passenger['age']} | ${passenger['gender_label']} | $reviewText",
                              fontFamily: bold,
                            ),
                            7.heightBox,
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 4,
                              ),
                              decoration: BoxDecoration(
                                color: primaryColor.withOpacity(0.10),
                                borderRadius: BorderRadius.circular(999),
                              ),
                              child: txt20Size(
                                context: widget.context,
                                title:
                                    "${widget.booking['seats']} ${widget.seatRequestLabel}",
                                textColor: primaryColor,
                              ),
                            ),
                          ],
                        ),
                      )
                    ],
                  ),
                ),
              ],
            ),
            12.heightBox,
            Row(
              mainAxisAlignment: MainAxisAlignment.start,
              children: [
                Expanded(
                  child: elevatedButtonWidget(
                    textWidget: txt22Size(
                      context: widget.context,
                      title: widget.rejectBtnLabel,
                      textColor: Colors.white,
                    ),
                    context: widget.context,
                    onPressed: widget.onPressedReject,
                    btnColor: primaryColor,
                  ),
                ),
                8.widthBox,
                Expanded(
                  child: elevatedButtonWidget(
                    textWidget: txt22Size(
                      context: widget.context,
                      title: widget.acceptBtnLabel,
                      textColor: Colors.white,
                    ),
                    context: widget.context,
                    onPressed: widget.onPressedAccept,
                  ),
                )
              ],
            )
          ],
        ),
      ),
    );
  }
}
