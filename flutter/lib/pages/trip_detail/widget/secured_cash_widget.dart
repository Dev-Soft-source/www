import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/trip_detail/widget/secured_cash_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/circle_image_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget securedCashWidget({context, bookingList, double screenWidth = 0.0, controller, double height = 0.0, errors, String date = "", String time = ""}){


  String dateTimeString = "$date $time";
  DateFormat dateFormat = DateFormat("yyyy-MM-dd HH:mm");
  DateTime parsedDate = dateFormat.parse(dateTimeString);
  DateTime currentDate = DateTime.now();
  bool showBtn = false;
  int difference = parsedDate.difference(currentDate).inMinutes;

  if(difference <= 30){
    showBtn = true;
  }

  return cardShadowWidget(
      context: context,
      widgetChild: Column(
        children: [
          postRideWidget(title: "${controller.labelTextDetail['secured_cash_heading'] ?? "Secured cash payment"}", screenWidth: screenWidth, context: context),
          if(bookingList.isNotEmpty)...[
            for(var booking in bookingList)...[
              if(booking['secured_cash'] == '1')...[
                Container(
                    padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
                      context: context,
                      mobile: 15.0,
                      tablet: 15.0,
                    ),getValueForScreenType<double>(
                      context: context,
                      mobile: 10.0,
                      tablet: 10.0,
                    ),getValueForScreenType<double>(
                      context: context,
                      mobile: 15.0,
                      tablet: 15.0,
                    ),getValueForScreenType<double>(
                      context: context,
                      mobile: 10.0,
                      tablet: 10.0,
                    )),
                    child: SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              circleImageWidget(width: 36, height: 36, imageType: "network", imagePath: "${booking['passenger']['profile_image']}", context: context),
                              5.widthBox,
                              txt16Size(context: context, title: "${booking['passenger']['first_name']} ${controller.labelTextDetail['driver_age_label'] ?? "Age"}: ${booking['passenger']['age']}  ${booking['passenger']['gender_label']} ${controller.labelTextDetail['review_label'] ?? "Review"}: ${booking['passenger']['average_rating'] != null ? booking['passenger']['average_rating'].toStringAsFixed(1) : ""} ")
                            ],
                          ),
                          showBtn == true ? elevatedButtonWidget(
                              textWidget: txt16Size(context: context, title: "${controller.labelTextDetail['enter_code_label'] ?? "Enter code"}", textColor: Colors.white),
                              context: context,
                              onPressed: booking['secured_cash_attempt_count'] == "10" ? null : () async{
                                controller.securedCashTextEditingController.text = "";
                                await securedCashBottomSheet(controller: controller, context: context, height: height, onSubmit: () async{
                                  await controller.enterCode(booking['id']);
                                },
                                errors: errors
                                );
                              }
                          ) : SizedBox(),
                        ],
                      ),
                    )
                ),
              ]
            ]
          ],

        ],
      )
  );
}