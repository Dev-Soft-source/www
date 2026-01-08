
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';



Widget tripCardDateTimeWidget({String date = "", String time = "", String seatLeft = "", String tripStatus = "",  context,
  String request = "",bool isLive = true, String atLabel = "at", String seatLeftLabel = "seats left", String price = "",
  String perSeatLabel = "per seat", String notLiveLabel = "Not live", String bookingRequestLabel = "booking request",
  String completedStatusLabel = "Completed", String cancelStatusLabel = "Cancelled", String totalSeat = "", double firmPrice = 0.0}){

  var seatLabel = "seat";
  totalSeat = totalSeat.toString() == "" ? "0" : totalSeat.toString();
  if(totalSeat.toString() != "null" && int.parse(totalSeat.toString()) > 1){
    seatLabel = "seats";
  }
  return Container(
    padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
      context: context,
      mobile: 12.0,
      tablet: 12.0,
    ),getValueForScreenType<double>(
      context: context,
      mobile: 15.0,
      tablet: 15.0,
    ),getValueForScreenType<double>(
      context: context,
      mobile: 12.0,
      tablet: 12.0,
    ),getValueForScreenType<double>(
      context: context,
      mobile: 0.0,
      tablet: 0.0,
    )),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            txt16Size(title: date, context: context),
            3.widthBox,
            txt16Size(title: atLabel, context: context),
            3.widthBox,
            txt16Size(title: time, context: context),
          ],
        ),
        if(seatLeft != "")...[
          txt16Size(title: "$seatLeft $seatLeftLabel", context: context)
        ],
        if(totalSeat != "" && totalSeat != "0")...[
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              txt16Size(title: "Total $totalSeat $seatLabel", context: context),
              if(firmPrice != 0.0)...[
                Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    txt16SizeLineThrough(title: "\$$price", context: context),
                    2.widthBox,
                    RichText(
                      text: TextSpan(
                        children: [
                          textSpan(title: "\$$firmPrice", context: context, fontFamily: bold, textColor: textColor, textSize: 24.0),
                          textSpan(title: perSeatLabel, context: context, fontFamily: bold, textColor: textColor, textSize: 16.0),
                        ],
                      ),
                    ),
                  ],
                )
              ]else if(tripStatus == "search")...[
                RichText(
                  text: TextSpan(
                    children: [
                      textSpan(title: "\$$price", context: context, fontFamily: bold, textColor: textColor, textSize: 24.0),
                      textSpan(title: perSeatLabel, context: context, fontFamily: bold, textColor: textColor, textSize: 16.0),
                    ],
                  ),
                ),
              ],

            ],
          )
        ],
        if(tripStatus == "upcoming")...[
          if(isLive == false)...[
            txt16Size(title: notLiveLabel, context: context, textColor: Colors.red),
          ]
        ],
        if(tripStatus == "completed")...[
          txt16Size(title: completedStatusLabel, context: context, textColor: Colors.green)
        ],
        if(tripStatus == "cancelled")...[
          txt16Size(title: cancelStatusLabel, context: context, textColor: Colors.red)
        ],
        if(request != "0" &&  request != "")...[
          txt16Size(title: "$request $bookingRequestLabel", context: context, textColor: Colors.red)
        ],
      ],
    )
  );
}
