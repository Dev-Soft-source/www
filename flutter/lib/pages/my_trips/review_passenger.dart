import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/my_trips/MyTripController.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class ReviewPassengerPage extends StatelessWidget {
  const ReviewPassengerPage({super.key});

  String _formatPassengerRating(dynamic booking) {
    if (booking is! Map) {
      return "0.0";
    }

    double? ratingValue;
    final passengerAverageRating = booking['passenger_average_rating'];
    if (passengerAverageRating != null) {
      ratingValue = double.tryParse(passengerAverageRating.toString());
    }

    if (ratingValue == null) {
      final rating = booking['rating'];
      if (rating is Map && rating['average_rating'] != null) {
        ratingValue = double.tryParse(rating['average_rating'].toString());
      }
    }

    return (ratingValue ?? 0.0).toStringAsFixed(1);
  }

  @override
  Widget build(BuildContext context) {
    final MyTripController controller = Get.isRegistered<MyTripController>()
        ? Get.find<MyTripController>()
        : Get.put(MyTripController());

    return Scaffold(
      appBar: AppBar(
        leading: safeBackButton(context),
        title: Obx(() => secondAppBarWidget(
            context: context,
            title:
                "${controller.labelTextTripDetail['review_passengers_heading'] ?? "Review passengers"}")),
        backgroundColor: primaryColor,
      ),
      body: Obx(() {
        final rawBookings = controller.cancelRideInfo['bookings'];
        final List<dynamic> bookings =
            rawBookings is List ? rawBookings : <dynamic>[];

        return Stack(
          children: [
            Container(
              padding: const EdgeInsets.all(15.0),
              child: ListView.separated(
                itemCount: bookings.length,
                itemBuilder: (context, index) {
                  final booking = bookings[index];
                  final passenger =
                      booking is Map ? booking['passenger'] : null;

                  final passengerAverageRating =
                      booking['passenger_average_rating'];

                  return Container(
                    padding: const EdgeInsets.all(15.0),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(5.0),
                      color: Colors.grey.shade200,
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      mainAxisSize: MainAxisSize.max,
                      children: [
                        Expanded(
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              circleImageWidget(
                                width: 60.0,
                                height: 60.0,
                                imagePath: passenger != null
                                    ? (passenger['profile_image'] ?? "")
                                        .toString()
                                    : "",
                                imageType: "network",
                                context: context,
                                borderRadius: 60.0,
                              ),
                              10.widthBox,
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    txt22SizeCapitalized(
                                      context: context,
                                      title:
                                          "${passenger != null ? passenger['first_name'] ?? '' : ''} ${passenger != null ? passenger['last_name'] ?? '' : ''}",
                                    ),
                                    10.heightBox,
                                    Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.start,
                                      children: [
                                        if (passengerAverageRating == null ||
                                            passengerAverageRating == 0) ...[
                                          txt18Size(
                                            textColor: Colors.grey.shade800,
                                            context: context,
                                            title: controller.labelTextDetail[
                                                    'no_reviews_label'] ??
                                                "No review yet",
                                          ),
                                        ] else ...[
                                          Image.asset(reviewsImage, width: 20),
                                          5.widthBox,
                                          txt18Size(
                                            context: context,
                                            title:
                                                _formatPassengerRating(booking),
                                          ),
                                        ]
                                      ],
                                    )
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                        if (booking is Map && booking['rating'] != null) ...[
                          5.widthBox,
                          InkWell(
                            onTap: () {
                              final rid = booking['rating'] is Map
                                  ? booking['rating']['id']
                                  : null;
                              if (rid != null) {
                                Get.toNamed('/review_detail/$rid/to/passenger');
                              }
                            },
                            child: textWithUnderLine(
                              title:
                                  "${controller.labelTextTripDetail['review_passengers_i_review_label'] ?? "I reviewed"}",
                              context: context,
                              fontFamily: bold,
                              textColor: primaryColor,
                              decorationColor: primaryColor,
                              textSize: 16.0,
                            ),
                          ),
                        ] else ...[
                          15.widthBox,
                          SizedBox(
                            width: 120,
                            child: elevatedButtonWidget(
                              textWidget:
                                  "${controller.labelTextTripDetail['review_passengers_review_label'] ?? "Review"}",
                              buttonFontSize: 20.0,
                              context: context,
                              onPressed: () async {
                                await controller.addPassengerReview(
                                  controller.cancelRideInfo['id'],
                                  "passenger",
                                  passenger != null
                                      ? (passenger['profile_image'] ?? "")
                                          .toString()
                                      : "",
                                  "${passenger != null ? passenger['first_name'] ?? '' : ''} ${passenger != null ? passenger['last_name'] ?? '' : ''}",
                                  booking is Map
                                      ? booking['id'].toString()
                                      : "",
                                );
                              },
                            ),
                          )
                        ],
                      ],
                    ),
                  );
                },
                separatorBuilder: (context, index) {
                  return 10.heightBox;
                },
              ),
            ),
            if (controller.isOverlayLoading.value == true) ...[
              overlayWidget(context),
            ]
          ],
        );
      }),
    );
  }
}
