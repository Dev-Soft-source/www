import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
Widget tripDetailButtonWidget({context, String tripStatus = "", String rideId = "", String status = "", String driverId = "", String bookedSeat = "",
String cancelBookingBtn = "Cancel booking", String chatWithDriverBtn = "Chat with driver", String updateBookingBtn = "Update Booking", bool showBtn = true,
  onPressed, String noShowDriverLabel = "No show driver", String rideDetailId = "0"}){
  return Column(
    mainAxisAlignment: MainAxisAlignment.spaceBetween,
    children: [
      if(status == "upcoming" && showBtn == true)...[
        SizedBox(
          width: 500,
          child: elevatedButtonWidget(
              textWidget: txt28SizeAlignCenter(title: cancelBookingBtn, context: context, textColor: Colors.white),
              context: context,
              onPressed: () async{
                Get.toNamed('/cancel_booking/trip');
              },
            btnColor: Colors.red
          ),
        ),
        5.heightBox,
      ],
      if(status == "upcoming" && showBtn == false)...[
        SizedBox(
          width: 500,
          child: elevatedButtonWidget(
              textWidget: txt28SizeAlignCenter(title: noShowDriverLabel, context: context, textColor: Colors.white),
              context: context,
              onPressed: onPressed,
              btnColor: Colors.red
          ),
        ),
        5.heightBox,
      ],
      SizedBox(
        width: 500,
        child: elevatedButtonWidget(
            textWidget: txt28SizeAlignCenter(title: chatWithDriverBtn, context: context, textColor: Colors.white),
            context: context,
            onPressed: () async{
              Get.toNamed('/messaging_page/$driverId/$rideId/new');
            },
          btnColor: Colors.grey,
        ),
      ),
      if(showBtn == true)...[
        5.heightBox,
        SizedBox(
          width: 500,
          child: elevatedButtonWidget(
              textWidget: txt28SizeAlignCenter(title: updateBookingBtn, context: context, textColor: Colors.white),
              context: context,
              onPressed: () async{
                Get.toNamed("/book_seat/$rideId/$bookedSeat/$rideDetailId");
              }
          ),
        )
      ]

    ],
  );
}