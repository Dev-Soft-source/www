
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/deep_trip_detail/DeepTripDetailController.dart';
import 'package:proximaride_app/pages/trip_detail/TripDetailController.dart';
import 'package:proximaride_app/pages/trip_detail/widget/booking_type_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/cancelation_policy_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/chat_driver_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/co_passenger_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/driver_info_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/from_to_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/my_co_passenger_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/payment_option_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/pickup_dropoff_info_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/request_booking_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/ride_feature_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/seat_booked_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/secured_cash_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/trip_detail_button_widget.dart';
import 'package:proximaride_app/pages/trip_detail/widget/vehicle_info_widget.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/overlay_widget.dart';
import 'package:proximaride_app/pages/widgets/progress_circular_widget.dart';
import 'package:proximaride_app/pages/widgets/second_appbar_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';


class DeepTripDetailPage extends GetView<DeepTripDetailController> {
  const DeepTripDetailPage({super.key});

  @override

  Widget build(BuildContext context) {
    Get.put(DeepTripDetailController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(title: "${controller.labelTextDetail['trip_main_heading'] ?? 'Trip details'}", context: context)),
        leading: const BackButton(
            color: Colors.white
        ),
      ),

      body: Obx(() {
        if(controller.isLoading.value == true){
          return Center(child: progressCircularWidget(context));
        }else{
          if(controller.ride.isNotEmpty){
            DateTime parsedDate = DateTime.parse(controller.ride['date']);
            DateFormat outputFormat = DateFormat('MMMM d, yyyy');
            String tripDate = outputFormat.format(parsedDate);


            String tripTime = "";
            if(controller.ride['time'] != null) {
              DateTime parsedTime = DateFormat("HH:mm:ss").parse(controller.ride['time']);
              if(parsedTime.hour == 12 && parsedTime.minute == 0){
                DateFormat outputTimeFormat = DateFormat("h:mm");
                tripTime = "${outputTimeFormat.format(parsedTime)} ${controller.labelTextDetail['noon_label'] ?? "noon"}";
              }else if(parsedTime.hour == 0 && parsedTime.minute == 0){
                DateFormat outputTimeFormat = DateFormat("h:mm");
                tripTime = "${outputTimeFormat.format(parsedTime)} ${controller.labelTextDetail['midnight_label'] ?? "midnight"}";
              }else{
                DateFormat outputTimeFormat = DateFormat("h:mm a");
                tripTime = outputTimeFormat.format(parsedTime);
              }
            }

            return Stack(
              children: [
                Container(
                    padding: EdgeInsets.all(getValueForScreenType<double>(
                      context: context,
                      mobile: 15.0,
                      tablet: 15.0,
                    )
                    ),
                    child: SingleChildScrollView(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [

                         if(controller.ride['booking_requests'].isNotEmpty)...[
                           requestBookingWidget(
                             context: context,
                             bookingRequestList: controller.ride['booking_requests'],
                             screenWidth: context.screenWidth,
                             controller: controller
                           ),
                           10.heightBox,
                         ],
                          fromToWidget(context: context, from: controller.ride['ride_detail'][0]['departure'], to: controller.ride['ride_detail'][0]['destination'], date: tripDate, time: tripTime, perSeat: controller.ride['ride_detail'][0]['price'], leftSeat: controller.ride['seats_left'].toString(),
                              fromLabel: "${controller.labelTextDetail['from_label'] ?? "From"}",
                              toLabel: "${controller.labelTextDetail['to_label'] ?? "To"}",
                              atLabel: "${controller.labelTextDetail['at_label'] ?? "at"}",
                            seatLeftLabel: "${controller.labelTextDetail['seats_left_label'] ?? "seat left"}",
                            perSeatLabel: "${controller.labelTextDetail['per_seat_label'] ?? "per seat"}",
                            type: "",
                            moreSpots: null
                          ),
                          if(controller.ride['seats_left'] != null && controller.ride['seats_left'] <= 0)...[
                            10.heightBox,
                            Center(child: txt22Size(title: "${controller.labelTextDetail['all_seats_booked_label'] ?? "This ride is fully booked"}", fontFamily: regular, context: context,textColor: Colors.red)),
                          ],
                          10.heightBox,
                          pickupDropoffInfoWidget(context: context,screenWidth: context.screenWidth,pickup: controller.ride['pickup'],
                              dropoff: controller.ride['dropoff'],description: controller.ride['details'],
                              pickUpHeading: "${controller.labelTextDetail['pickup_dropoff_info_heading'] ?? "Pick up & drop off info"}",
                              pickupLabel: "${controller.labelTextDetail['pickup_label'] ?? "Pick up"}",
                              dropOffLabel: "${controller.labelTextDetail['dropoff_label'] ?? "Drop off"}",
                              descriptionLabel: "${controller.labelTextDetail['description_label'] ?? "Description"}"),
                          10.heightBox,
                          if(controller.ride['payment_method_slug'] == "secured")...[
                            securedCashWidget(context: context, bookingList: controller.ride['bookings'], date: controller.ride['date'],  screenWidth: context.screenWidth, controller: controller, height: (context.height / 2) + 50, errors: controller.errors.toList(), time: controller.ride['time']),
                            10.heightBox,
                          ],
                          seatBookedWidget(
                            context: context,
                            booked: "${controller.ride['booked_seats']} ${controller.labelTextDetail['ride_seat_label'] ?? "seats"}",
                            fare: "\$${controller.ride['fare']}",
                            fee: "\$${controller.ride['booking_fee']}",
                            totalAmount: "\$${controller.ride['total_amount']}",
                            screenWidth: context.screenWidth,
                            controller: controller
                          ),
                          10.heightBox,
                          myCoPassengerWidget(context: context, coPassengerList: controller.ride['bookings'], tripId: controller.ride['id'].toString(), screenWidth: context.screenWidth, type: "ride",
                          tripCoPassengerHeading: "${controller.labelTextDetail['trip_co_passenger_heading'] ?? "My co-passenger(s)"}",
                          rideCoPassengerHeading: "${controller.labelTextDetail['ride_co_passenger_heading'] ?? "My passengers"}",
                          age: "${controller.labelTextDetail['driver_age_label'] ?? "Age"}",
                          review: "${controller.labelTextDetail['review_label'] ?? "Review"}"),
                          10.heightBox,

                          vehicleInfoWidget(
                              context: context,
                              vehicleDetail: "${controller.ride['vehicle'] != null ? controller.ride['vehicle']['year'] : ""} ${controller.ride['vehicle'] != null ? controller.ride['vehicle']['make'] : ""} ${controller.ride['vehicle'] != null ? controller.ride['vehicle']['model'] : ""}",
                              licenseNumber: "${controller.ride['vehicle'] != null ? controller.ride['vehicle']['liscense_no'] : ""}",
                              carType: "${controller.ride['vehicle'] != null ? controller.ride['vehicle']['car_type'] : ""}",
                              rideId: "${controller.ride['id']}",
                              vehicleImage: "${controller.ride['vehicle'] != null ? controller.ride['vehicle']['image'] : ""}",
                              vehicleId: "${controller.ride['vehicle_id']}",
                              screenWidth: context.screenWidth,
                              type: "ride",
                              heading: "${controller.labelTextDetail['vehicle_info_label'] ?? "Vehicle info"}"
                          ),
                          10.heightBox,

                          paymentOptionWidget(context: context, payment: controller.ride['payment_method'], toolTipMessage: controller.ride['payment_method_tooltip'].toString(), screenWidth: context.screenWidth, heading: "${controller.labelTextDetail['payment_method_label'] ?? "Payment method"}"),
                          10.heightBox,
                          bookingTypeWidget(context: context, bookingType: controller.ride['booking_method'], screenWidth: context.screenWidth, toolTipMessage: controller.ride['booking_method_tooltip'].toString(), heading: "${controller.labelTextDetail['booking_type_label'] ?? "Booking type"}", imagePath: controller.ride['booking_method_image'].toString()),
                          10.heightBox,
                          cancellationPolicyWidget(context: context,screenWidth: context.screenWidth,policyType: controller.ride['booking_type'] ?? "", policyRate: controller.firmCancellationPrice.value,
                              heading: "${controller.labelTextDetail['cancellation_policy_label'] ?? "Cancellation policy"}",
                          discountLabel: "${controller.labelTextDetail['discount_label'] ?? "discount"}",
                          bookingTypeSlug: controller.ride['booking_type_slug'].toString(),
                          bookingTypeToolTip: controller.ride['booking_type_tooltip'].toString()),
                          10.heightBox,
                          rideFeatureWidget(context: context, featureList: controller.ride['features'], rideDetail: controller.ride, screenWidth: context.screenWidth, heading: "${controller.labelTextDetail['ride_features_label'] ?? "Ride features"}"),
                          10.heightBox,
                        ],
                      ),
                    )
                ),
                if(controller.isOverlayLoading.value == true)...[
                  overlayWidget(context)
                ],
              ],
            );
          }else{
            return Center(child: txt20Size(title: "${controller.labelTextDetail['no_ride_found_message'] ?? "No ride data found"}", context: context));
          }
        }
      })
    );
  }
}

