import 'package:dotted_line/dotted_line.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/card_shadow_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
Widget fromToWidget({context, String from = "", String to = "", String date = "", String time = "", String leftSeat = "", String perSeat = "",
  String fromLabel = "From", String toLabel = "To", String atLabel = "at" , String perSeatLabel = "per seat" , String seatLeftLabel = "seat left",
  String type = "",  moreSpots}){

  return cardShadowWidget(
      context: context,
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
            child: Row(
              mainAxisAlignment: MainAxisAlignment.start,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      width: 20,
                      height: 20,
                      decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(50),
                          color: primaryColor
                      ),
                    ),
                    const SizedBox(
                      height: 50,
                      child: Padding(
                        padding: EdgeInsets.only(left: 9),
                        child: DottedLine(
                          direction: Axis.vertical,
                          alignment: WrapAlignment.center,
                          lineLength: double.infinity,
                          lineThickness: 1.0,
                          dashLength: 2.0,
                          dashColor: Colors.black,
                          dashRadius: 0.0,
                          dashGapLength: 1.0,
                          dashGapColor: Colors.transparent,
                          dashGapRadius: 0.0,
                        ),
                      ),
                    ),
                    Container(
                      width: 20,
                      height: 20,
                      decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(50),
                          color: Colors.grey.shade400
                      ),
                    ),
                  ],
                ),
                10.widthBox,
                Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        txt18Size(title: fromLabel, context: context),
                        txt16Size(title: from, context: context, fontFamily: bold),
                        5.heightBox,
                        txt18Size(title: toLabel, context: context),
                        txt16Size(title: to, context: context, fontFamily: bold),
                      ],
                    )
                ),
                10.widthBox,
                RichText(
                  text: TextSpan(
                    children: [
                      textSpan(title: date, context: context, fontFamily: bold, textColor: textColor, textSize: 16.0),
                      textSpan(title: " $atLabel ", context: context, fontFamily: bold, textColor: textColor, textSize: 16.0),
                      textSpan(title: time, context: context, fontFamily: bold, textColor: textColor, textSize: 16.0),
                    ],
                  ),
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
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            width: 20,
                            height: 20,
                            decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(50),
                                color: primaryColor
                            ),
                          ),
                          const SizedBox(
                            height: 50,
                            child: Padding(
                              padding: EdgeInsets.only(left: 9),
                              child: DottedLine(
                                direction: Axis.vertical,
                                alignment: WrapAlignment.center,
                                lineLength: double.infinity,
                                lineThickness: 1.0,
                                dashLength: 2.0,
                                dashColor: Colors.black,
                                dashRadius: 0.0,
                                dashGapLength: 1.0,
                                dashGapColor: Colors.transparent,
                                dashGapRadius: 0.0,
                              ),
                            ),
                          ),
                          Container(
                            width: 20,
                            height: 20,
                            decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(50),
                                color: Colors.grey.shade400
                            ),
                          ),
                        ],
                      ),
                      10.widthBox,
                      Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              txt18Size(title: fromLabel, context: context),
                              txt16Size(title: moreSpots[i]['departure'].toString(), context: context, fontFamily: bold),
                              5.heightBox,
                              txt18Size(title: toLabel, context: context),
                              txt16Size(title: moreSpots[i]['destination'].toString(), context: context, fontFamily: bold),
                            ],
                          )
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
                    child: RichText(
                      text: TextSpan(
                        children: [
                          textSpan(title: "\$$perSeat", context: context, fontFamily: bold, textColor: primaryColor, textSize: 20.0),
                          textSpan(title: " $perSeatLabel", context: context, fontFamily: bold, textColor: primaryColor, textSize: 16.0,),
                        ],
                      ),
                    ),
                  )
              ),
            ],
          )
        ],
      )
  );
}