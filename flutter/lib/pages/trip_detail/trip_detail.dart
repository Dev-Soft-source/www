
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
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


class TripDetailPage extends GetView<TripDetailController> {
  const TripDetailPage({super.key});

  @override

  Widget build(BuildContext context) {
    Get.put(TripDetailController());
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryColor,
        title: Obx(() => secondAppBarWidget(title: controller.type == 'ride' ? "${controller.labelTextDetail['trip_main_heading'] ?? 'Trip details'}" : controller.type == 'trip' ? "${controller.labelTextDetail['ride_main_heading'] ?? 'Trip details'}" : "${controller.labelTextDetail['main_heading'] ?? "Ride detail"}", context: context)),
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

            var cancelHours = 0;
            if(controller.type == "ride"){
              cancelHours = int.parse(controller.cancelSetting['driver_cancel_hours'].toString());
            }
            else if(controller.type == "trip"){
              cancelHours = int.parse(controller.cancelSetting['passenger_cancel_hours'].toString());
            }

            var dateTimeString = "${controller.ride['date']} ${controller.ride['time']}";
            DateFormat dateFormat = DateFormat("yyyy-MM-dd HH:mm:ss");
            DateTime dateTime = dateFormat.parse(dateTimeString);
            DateTime cancelDateTime = dateTime.add(Duration(hours: cancelHours));
            DateTime currentDateTime = DateTime.now();

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

                         if(controller.ride['booking_requests'].isNotEmpty && controller.type == "ride")...[
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
                            type: controller.type,
                            moreSpots: controller.ride['more_ride_detail']
                          ),
                          if(controller.ride['seats_left'] != null && controller.ride['seats_left'] <= 0)...[
                            10.heightBox,
                            Center(child: txt22Size(title: "${controller.labelTextDetail['all_seats_booked_label'] ?? "This ride is fully booked"}", fontFamily: regular, context: context,textColor: Colors.red)),
                          ],
                          10.heightBox,
                          pickupDropoffInfoWidget(context: context,screenWidth: context.screenWidth,pickup: controller.ride['pickup'],
                              dropoff: controller.ride['dropoff'],description: controller.ride['details'],
                              pickUpHeading: "${controller.labelTextDetail['multi_info_heading'] ?? "Pick up & drop off info"}",
                              pickupLabel: "${controller.labelTextDetail['pickup_label'] ?? "Pick up"}",
                              dropOffLabel: "${controller.labelTextDetail['dropoff_label'] ?? "Drop off"}",
                              descriptionLabel: "${controller.labelTextDetail['description_label'] ?? "Description"}"),
                          10.heightBox,
                          if(controller.ride['payment_method_slug'] == "secured" && controller.type == "ride")...[
                            securedCashWidget(context: context, bookingList: controller.ride['bookings'], date: controller.ride['date'],  screenWidth: context.screenWidth, controller: controller, height: (context.height / 2) + 50, errors: controller.errors.toList(), time: controller.ride['time']),
                            10.heightBox,
                          ],
                          if(controller.type == "ride")...[
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
                          ],
                          if(controller.type == "findRide")...[
                            coPassengerWidget(context: context, coPassengerList: controller.ride['bookings'], tripId: controller.ride['id'].toString(), screenWidth: context.screenWidth, heading: "${controller.labelTextDetail['co_passenger_label'] ?? "Co-passenger"}"),
                            10.heightBox,
                            // seatAvailableWidget(context: context, controller: controller, screenWidth: context.screenWidth),
                            // 10.heightBox,
                          ],
                          if(controller.type == "trip" || controller.type == "ride")...[
                            myCoPassengerWidget(context: context, coPassengerList: controller.ride['bookings'], tripId: controller.ride['id'].toString(), screenWidth: context.screenWidth, type: controller.type,
                            tripCoPassengerHeading: "${controller.labelTextDetail['trip_co_passenger_heading'] ?? "My co-passenger(s)"}",
                            rideCoPassengerHeading: "${controller.labelTextDetail['ride_co_passenger_heading'] ?? "My passengers"}",
                            age: "${controller.labelTextDetail['driver_age_label'] ?? "Age"}",
                            review: "${controller.labelTextDetail['review_label'] ?? "Review"}"),
                            10.heightBox,
                          ],

                          if(controller.type == "trip" || controller.type == "findRide")...[
                            driverInfoWidget(
                                context: context,
                                driverName: "${controller.ride['driver'] != null ? controller.ride['driver']['first_name'] : ""} ${controller.ride['driver'] != null ? controller.ride['driver']['last_name'] : ""}",
                                driverRating: "${(controller.ride['driver'] != null && controller.ride['driver']['average_rating'] != null) ? controller.ride['driver']['average_rating'].toStringAsFixed(1) : ""}",
                                rideId: "${controller.ride['id']}",
                                driverImage: "${(controller.ride['driver'] != null && controller.ride['driver']['profile_image'] != null) ? controller.ride['driver']['profile_image'] : ""}",
                                screenWidth: context.screenWidth,
                                pageType: controller.type == "trip" ? '1' : '0',
                                heading: "${controller.labelTextDetail['driver_info_label'] ?? "Driver info"}",
                                hidePhoto: (controller.type == "findRide" && controller.hideDriverInfo.value == false) ? false : true
                            ),
                            10.heightBox,
                          ],

                          if(controller.type == "findRide" || controller.type == "ride" || controller.type == "trip")...[
                            vehicleInfoWidget(
                                context: context,
                                vehicleDetail: "${controller.ride['vehicle'] != null ? controller.ride['vehicle']['year'] : ""} ${controller.ride['vehicle'] != null ? controller.ride['vehicle']['make'] : ""} ${controller.ride['vehicle'] != null ? controller.ride['vehicle']['model'] : ""}",
                                licenseNumber: "${controller.ride['vehicle'] != null ? controller.ride['vehicle']['liscense_no'] : ""}",
                                carType: "${controller.ride['vehicle'] != null ? controller.ride['vehicle']['car_type'] : ""}",
                                rideId: "${controller.ride['id']}",
                                vehicleImage: "${controller.ride['vehicle'] != null ? controller.ride['vehicle']['image'] : ""}",
                                vehicleId: "${controller.ride['vehicle_id']}",
                                screenWidth: context.screenWidth,
                                type: controller.type,
                                heading: "${controller.labelTextDetail['vehicle_info_label'] ?? "Vehicle info"}",
                                hidePhoto: (controller.type == "findRide" && controller.hideDriverInfo.value == false) ? false : true
                            ),
                            10.heightBox,
                          ],

                          paymentOptionWidget(context: context, payment: controller.ride['payment_method'], toolTipMessage: controller.ride['payment_method_tooltip'].toString(), screenWidth: context.screenWidth, heading: "${controller.labelTextDetail['payment_method_label'] ?? "Payment method"}"),
                          10.heightBox,
                          bookingTypeWidget(context: context, bookingType: controller.ride['booking_method'], screenWidth: context.screenWidth, toolTipMessage: controller.ride['booking_method_tooltip'].toString(), heading: "${controller.labelTextDetail['booking_type_label'] ?? "Booking type"}", imagePath: controller.ride['booking_method_image'].toString()),
                          10.heightBox,
                          cancellationPolicyWidget(context: context,screenWidth: context.screenWidth,policyType: controller.ride['booking_type'] ?? "", policyRate: controller.firmCancellationPrice.value,
                              heading: "${controller.labelTextDetail['cancellation_policy_label'] ?? "Cancellation policy"}",
                          discountLabel: "${controller.labelTextDetail['discount_label'] ?? "discount"}",
                          bookingTypeSlug: controller.ride['booking_type_slug'].toString(),
                          bookingTypeToolTip: controller.ride['booking_type_tooltip'].toString(),
                          cancellationPolicyUrl: controller.labelTextDetail['cancellation_policy_tooltip_url'].toString(),
                          ),

                          10.heightBox,
                          rideFeatureWidget(context: context, featureList: controller.ride['features'], rideDetail: controller.ride, screenWidth: context.screenWidth, heading: "${controller.labelTextDetail['ride_features_label'] ?? "Ride features"}"),
                          10.heightBox,
                          if(controller.type == "findRide")...[
                            chatDriverWidget(context: context, screenWidth: context.screenWidth, rideId: "${controller.ride['id']}", driverId: "${controller.ride['driver']['id']}", heading: "${controller.labelTextDetail['driver_chat_heading'] ?? "Chat with the driver"}"),
                            10.heightBox,
                          ],
                          if(controller.type == "ride" && controller.status == "upcoming")...[
                            Container(
                              padding: const EdgeInsets.only(left: 5.0, right: 5.0),
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                mainAxisSize: MainAxisSize.max,
                                children: [
                                  InkWell(
                                    onTap: (){
                                      Get.toNamed("/post_ride/${controller.ride['id']}/update");
                                    },
                                    child: textWithUnderLine(
                                        title: "${controller.labelTextDetail['edit_ride_btn_label'] ?? "Edit ride"}",
                                        textColor: primaryColor,
                                        textSize: 22.0,
                                        context: context
                                    ),
                                  ),
                                  if(cancelDateTime.isAfter(currentDateTime))...[
                                    InkWell(
                                      onTap: () async{
                                        if(controller.ride['bookings'] != null && controller.ride['bookings'].length > 0){
                                          Get.toNamed('/cancel_booking/ride');
                                        }else{
                                          await controller.cancelRideByDriver();
                                        }

                                      },
                                      child: textWithUnderLine(
                                          title: "${controller.labelTextDetail['cancel_ride_btn_label'] ?? "Cancel ride"}",
                                          textColor: Colors.red,
                                          textSize: 22.0,
                                          context: context,
                                          decorationColor: Colors.red
                                      ),
                                    )
                                  ],
                                ],
                              ),
                            ),

                          ]else...[
                            if(controller.type == "ride")...[
                              10.heightBox,
                            ]else if(controller.type == "findRide")...[
                              80.heightBox,
                            ]else if(controller.status.toString() != "upcoming" && controller.type == "trip")...[
                              10.heightBox,
                            ]else...[
                              160.heightBox,
                            ]
                          ],

                        ],
                      ),
                    )
                ),
                if(controller.type != "ride")...[
                  Positioned(
                    bottom: 0,
                    child: Container(
                      padding: const EdgeInsets.only(left: 15.0, right: 15.0, top: 10, bottom: 10),
                      color: Colors.grey.shade100,
                      width: context.screenWidth,
                      child: controller.type == "findRide" ?
                      elevatedButtonWidget(
                      textWidget: controller.ride['booking_method_id'] == "31" ? txt28Size(title: "${controller.labelTextDetail['instant_btn_label'] ?? "Instant booking"}", context: context, textColor: Colors.white) :
                      txt28Size(title: "${controller.labelTextDetail['book_seat_btn_label'] ?? "Book your seats"}", context: context, textColor: Colors.white),
                          context: context,
                          onPressed: () async{
                            if(controller.ride['seats_left'] == 0){
                              // controller.serviceController.showDialogue('No seats available');
                              controller.serviceController.showDialogue("${controller.labelTextDetail['no_seat_available_label'] ?? 'No seats available'}");
                            }else{
                              await controller.checkRide('add');
                            }
                          }
                      ) :
                      controller.status.toString() == "upcoming" ? tripDetailButtonWidget(
                        context: context,
                        tripStatus: "${controller.ride['status']}",
                        rideId: "${controller.ride['id']}",
                        status: controller.status.toString(),
                        driverId: "${controller.ride['driver']['id']}",
                        bookedSeat: "${controller.ride['booked_seats']}",
                        cancelBookingBtn: "${controller.labelTextDetail['cancel_booking_btn_label'] ?? "Cancel booking"}",
                        chatWithDriverBtn: "${controller.labelTextDetail['driver_chat_button_label'] ?? "Chat with driver"}",
                        updateBookingBtn: "${controller.labelTextDetail['edit_button_actions_label'] ?? "Update booking"}",
                        showBtn: cancelDateTime.isAfter(currentDateTime) ? true : false,
                        noShowDriverLabel: "${controller.labelTextDetail['no_show_driver_label'] ?? "No show driver"}",
                        rideDetailId: controller.ride['ride_detail'][0]['id'].toString(),
                        onPressed: () async{
                          await controller.noShowDriverData();
                        },
                      ) : const SizedBox(),
                    ),
                  ),
                ],
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

