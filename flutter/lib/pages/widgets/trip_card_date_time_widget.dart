import 'package:flutter/material.dart';
import 'package:proximaride_app/helpers/currency_formatter.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import '../../consts/constFileLink.dart';

Widget tripCardDateTimeWidget(
    {String date = "",
    String time = "",
    String seatLeft = "",
    String tripStatus = "",
    context,
    String request = "",
    bool isLive = true,
    String atLabel = "at",
    String seatLeftLabel = "seats left",
    String price = "",
    String perSeatLabel = "per seat",
    String notLiveLabel = "Not live",
    String bookingRequestLabel = "booking request",
    String completedStatusLabel = "Completed",
    String cancelStatusLabel = "Cancelled",
    String totalSeat = "",
    String bookingMethodLabel = "", int bookingMethodId = 0, 
    String bookingMethodIcon = "",
    double firmPrice = 0.0}) {
  var seatLabel = "seat";
  totalSeat = totalSeat.toString() == "" ? "0" : totalSeat.toString();
  if (totalSeat.toString() != "null" && int.parse(totalSeat.toString()) > 1) {
    seatLabel = "seats";
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
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.start,
            children: [
              txt16Size(title: date, context: context, textColor: Colors.grey.shade800),
              3.widthBox,
              txt16Size(title: atLabel, context: context, textColor: Colors.grey.shade800),
              3.widthBox,
              txt16Size(title: time, context: context, textColor: Colors.grey.shade800),
            ],
          ),
          
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Row(children: [
                if (tripStatus == "upcoming") ...[
                  if (isLive == false) ...[
                    txt16Size(
                        title: notLiveLabel,
                        context: context,
                        textColor: Colors.red),
                  ]
                ],
                if (tripStatus == "completed") ...[
                  txt16Size(
                      title: completedStatusLabel,
                      context: context,
                      textColor: Colors.green)
                ],
                if (tripStatus == "cancelled") ...[
                  txt16Size(
                      title: cancelStatusLabel,
                      context: context,
                      textColor: Colors.red)
                ],
                if (request != "0" && request != "") ...[
                  8.widthBox,
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                    decoration: BoxDecoration(
                      color: Colors.red.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(999),
                      border: Border.all(color: Colors.red.withOpacity(0.35)),
                    ),
                    child: txt14Size(
                      title: "$request $bookingRequestLabel",
                      context: context,
                      textColor: Colors.red,
                      fontFamily: bold,
                    ),
                  )
                ],
              ]),
              
              if (bookingMethodLabel != "" && bookingMethodLabel.isNotEmpty) ...[
            6.heightBox,
            (() {
              final isInstant = bookingMethodId == 31;
              final badgeColor = isInstant ? Colors.green : primaryColor;
              return Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 10, vertical: 2),
                decoration: BoxDecoration(
                  color: badgeColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(5),
                  border: Border.all(color: badgeColor.withOpacity(0.35)),
                ),
                child: txt14Size(
                  title: bookingMethodLabel,
                  context: context,
                  fontFamily: bold,
                  textColor: badgeColor,
                ),
              );
            })(),
          ],
              if (seatLeft != "") ...[
                Row(children: [
                  txt16Size(title: "$seatLeft $seatLeftLabel", context: context)
                ])
              ],
              if (totalSeat != "" && totalSeat != "0") ...[
                Row(
                  children: [
                    // txt16Size(
                    //     title: "Total $totalSeat $seatLabel", context: context),
                    if (firmPrice != 0.0) ...[
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(mainAxisSize: MainAxisSize.max, children: [
                            txt16SizeLineThrough(
                                title: formatCurrency(price), context: context),
                            txt16Size(
                              title: "→", // "-->"
                              context: context,
                              fontFamily: bold,
                            ),
                          ]),
                          // 5.heightBox,
                          Column(
                            mainAxisSize: MainAxisSize.min,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              txt24Size(
                                  title: formatCurrency(firmPrice),
                                  context: context,
                                  textColor: primaryColor),
                              2.heightBox,
                              txt16Size(
                                title: perSeatLabel,
                                context: context,
                                fontFamily: bold,
                                textColor: placeHolderColor,
                              ),
                            ],
                          ),
                        ],
                      )
                    ]
                    //  else if (tripStatus == "search") ...[
                    //   Column(
                    //     mainAxisSize: MainAxisSize.min,
                    //     crossAxisAlignment: CrossAxisAlignment.start,
                    //     children: [
                    //       txt24Size(
                    //           title: formatCurrency(price),
                    //           context: context,
                    //           textColor: primaryColor),
                    //       2.heightBox,
                    //       txt16Size(
                    //         title: perSeatLabel,
                    //         context: context,
                    //         fontFamily: bold,
                    //         textColor: placeHolderColor,
                    //       ),
                    //     ],
                    //   ),
                    // ],
                  ],
                ),
              ],
            ],
          )
        ],
      ));
}
