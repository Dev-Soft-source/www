import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget messageContainer({
  context,
  message = "N/A",
  time = "12:00:00",
  msgType = 0,
  from = "",
  to = "",
  date = "",
  rideTime = "",
  onTap
}) {
  String tripDate = "";
  String tripTime = "";

  if(date != ""){

    DateTime parsedDate = DateTime.parse(date);
    DateFormat outputFormat =
    DateFormat('MMMM d, yyyy');
    tripDate = outputFormat.format(parsedDate);

    DateTime parsedTime = DateFormat("HH:mm:ss")
        .parse(rideTime);
    DateFormat outputTimeFormat =
    DateFormat("h:mm a");
    tripTime =
    outputTimeFormat.format(parsedTime);
  }

  return InkWell(
    onTap: onTap,
    child: Container(
      decoration: BoxDecoration(
        color: msgType == 1 ? primaryColor : Colors.grey[200],
        borderRadius: BorderRadius.only(
            bottomRight: msgType == 1 ? const Radius.circular(0.0) : const Radius.circular(10.0),
            bottomLeft: msgType == 1 ? const Radius.circular(10.0) : const Radius.circular(0.0),
            topLeft: const Radius.circular(10.0),
            topRight: const Radius.circular(10.0)),
      ),
      padding: EdgeInsets.all(getValueForScreenType<double>(
        context: context,
        mobile: 10.0,
        tablet: 10.0,
      )),
      constraints: const BoxConstraints(
        maxWidth: 300,
        minWidth: 100
      ),
      child: Column(
        crossAxisAlignment: msgType == 1 ? CrossAxisAlignment.end : CrossAxisAlignment.start,
        children: [
          if(from == "")...[
            txt18Size(
                title: message.toString(),
                fontFamily: bold,
                context: context,
                textColor: msgType == 1 ? Colors.white : Colors.black
            ),
            2.heightBox,
            txt14Size(
                title: time,
                fontFamily: regular,
                context: context,
                textColor: msgType == 1 ? Colors.white : Colors.black
            ),
          ]else...[
            Align(
              alignment: Alignment.topCenter,
              child: txt22Size(
                  title: "Ride Detail",
                  fontFamily: bold,
                  context: context,
                  textColor: msgType == 1 ? Colors.white : Colors.black
              ),
            ),
            2.heightBox,
            Align(
              alignment: Alignment.topLeft,
              child: txt18Size(
                  title: "From: ${from.toString()}",
                  fontFamily: bold,
                  context: context,
                  textColor: msgType == 1 ? Colors.white : Colors.black
              ),
            ),
            2.heightBox,
            Align(
              alignment: Alignment.topLeft,
              child: txt18Size(
                  title: "To: ${to.toString()}",
                  fontFamily: bold,
                  context: context,
                  textColor: msgType == 1 ? Colors.white : Colors.black
              ),
            ),
            2.heightBox,
            Align(
              alignment: Alignment.topLeft,
              child:  Row(
                mainAxisAlignment: MainAxisAlignment.start,
                children: [
                  txt16Size(title: tripDate, context: context, textColor: Colors.white),
                  3.widthBox,
                  txt16Size(title: "at", context: context, textColor: Colors.white),
                  3.widthBox,
                  txt16Size(title: tripTime, context: context, textColor: Colors.white),
                ],
              ),
            ),
            2.heightBox,
            txt14Size(
                title: time,
                fontFamily: regular,
                context: context,
                textColor: msgType == 1 ? Colors.white : Colors.black
            ),
          ]

        ],
      ),
    ),
  );

}
