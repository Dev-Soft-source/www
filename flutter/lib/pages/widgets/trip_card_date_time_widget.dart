
import 'package:flutter/material.dart';
import 'package:proximaride_app/helpers/currency_formatter.dart';
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
                    txt16SizeLineThrough(
                        title: formatCurrency(price), context: context),
                    6.widthBox,
                    txt16Size(
                      title: "->",
                      context: context,
                      fontFamily: bold,
                    ),
                    6.widthBox,
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        txt24Size(
                            title: formatCurrency(firmPrice),
                            context: context,
                            textColor: textColor),
                        txt16Size(
                          title: perSeatLabel,
                          context: context,
                          fontFamily: bold,
                          textColor: textColor,
                        ),
                      ],
                    ),
                  ],
                )
              ]else if(tripStatus == "search")...[
                Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    txt24Size(
                        title: formatCurrency(price),
                        context: context,
                        textColor: textColor),
                    txt16Size(
                      title: perSeatLabel,
                      context: context,
                      fontFamily: bold,
                      textColor: textColor,
                    ),
                  ],
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
