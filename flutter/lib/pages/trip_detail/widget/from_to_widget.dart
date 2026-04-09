import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/trip_route_from_to_rail.dart';

Widget fromToWidget({context, String from = "", String to = "", String date = "", String time = "", String leftSeat = "", String perSeat = "",
  String pickup = "", String dropOff = "",
  String fromLabel = "From", String toLabel = "To", String atLabel = "at" , String perSeatLabel = "per seat" , String seatLeftLabel = "seat left",
  String type = "",  moreSpots}){

  return cardShadowWidget(
      context: context,
      margin: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      widgetChild: Column(
        children: [
          Container(
            padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
              context: context,
              mobile: 15.0,
              tablet: 15.0,
            ),getValueForScreenType<double>(
              context: context,
              mobile: 15.0,
              tablet: 15.0,
            ),getValueForScreenType<double>(
              context: context,
              mobile: 15.0,
              tablet: 15.0,
            ),getValueForScreenType<double>(
              context: context,
              mobile: 0.0,
              tablet: 0.0,
            )),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Align(
                  alignment: Alignment.centerRight,
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    mainAxisAlignment: MainAxisAlignment.end,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      txt18Size(
                          title: date,
                          context: context,
                          fontFamily: bold,
                          textColor: Colors.grey.shade800),
                      txt18Size(
                          title: " $atLabel ",
                          context: context,
                          fontFamily: bold,
                          textColor: Colors.grey.shade800),
                      txt18Size(
                          title: time,
                          context: context,
                          fontFamily: bold,
                          textColor: Colors.grey.shade800),
                    ],
                  ),
                ),
                8.heightBox,
                TripRouteFromToRail(
                  showTopSpacer: false,
                  from: from,
                  // pickup: pickup,
                  to: to,
                  // dropOff: dropOff,
                  gapBeforeToBlock : 28.0,
                  connectorGutterHeight : 26.0,
                ),
              ],
            ),
          ),
          10.heightBox,
          if(type == "ride" && moreSpots != null)...[
            for(var i =0; i < moreSpots.length; i++)...[
              if(moreSpots[i]['departure'] != null)...[
                Container(
                  padding: EdgeInsets.fromLTRB(getValueForScreenType<double>(
                    context: context,
                    mobile: 15.0,
                    tablet: 15.0,
                  ),getValueForScreenType<double>(
                    context: context,
                    mobile: 15.0,
                    tablet: 15.0,
                  ),getValueForScreenType<double>(
                    context: context,
                    mobile: 15.0,
                    tablet: 15.0,
                  ),getValueForScreenType<double>(
                    context: context,
                    mobile: 0.0,
                    tablet: 0.0,
                  )),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      TripRouteFromToRail(
                        from: moreSpots[i]['departure'].toString(),
                        to: moreSpots[i]['destination'].toString(),
                        showTopSpacer: false,
                      ),
                    ],
                  ),
                ),
                10.heightBox,
              ]
            ]
          ],

          Divider(indent: 0, height: 0,),
          Row(
            mainAxisAlignment: MainAxisAlignment.start,
            children: [
              Expanded(
                  child: Center(child: txt20Size(title: "$leftSeat $seatLeftLabel", context: context))
              ),
              5.widthBox,
              SizedBox(height: 40, width: 1, child: Container(color: Colors.grey.shade400)),
              5.widthBox,
              Expanded(
                  child: Center(
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      mainAxisAlignment: MainAxisAlignment.center,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        txt20Size(
                          title: "\$$perSeat",
                          context: context,
                          fontFamily: bold,
                          textColor: primaryColor,
                        ),
                        txt18Size(
                          title: " $perSeatLabel",
                          context: context,
                          fontFamily: bold,
                          textColor: primaryColor,
                        ),
                      ],
                    ),
                  )
              ),
            ],
          )
        ],
      )
  );
}
