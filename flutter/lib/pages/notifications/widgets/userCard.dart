import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../widgets/card_shadow_widget.dart';
import '../../widgets/network_cache_image_widget.dart';


Widget userCard({
  Color reviewCardColor = primaryColor,
  context,
  controller,
  image = "",
  name = "Usman",
  notification = "",
  date = "12-5-24",
  time = "12:00:00",
  userType = "N/A",
  onTap,
  onLongPress,
  Color bgColor = Colors.white

}) {

  String tripDate = "";
  if(date != null){
    DateTime parsedDate = DateTime.parse(date);
    DateFormat outputFormat = DateFormat('MMMMd,yyyy');
    tripDate = outputFormat.format(parsedDate);
  }

  String tripTime = "";
  if(time != null) {

    DateTime parsedTime = DateTime.parse(date);

    if(parsedTime.hour == 12 && parsedTime.minute == 0){
      DateFormat outputTimeFormat = DateFormat("h:mm");
      tripTime = "${outputTimeFormat.format(parsedTime)} noon";
    }else if(parsedTime.hour == 0 && parsedTime.minute == 0){
      DateFormat outputTimeFormat = DateFormat("h:mm");
      tripTime = "${outputTimeFormat.format(parsedTime)} midnight";
    }else{
      DateFormat outputTimeFormat = DateFormat("h:mma");
      tripTime = outputTimeFormat.format(parsedTime);
    }
  }

  return InkWell(
    onTap: onTap,
    onLongPress: onLongPress,
    child: cardShadowWidget(
      context: context,
      bgColor: bgColor,
      widgetChild: Container(
        padding: EdgeInsets.all(getValueForScreenType<double>(
          context: context,
          mobile: 10.0,
          tablet: 10.0,
        )),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.start,
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.max,
          children: [
            Container(
              width: 70.0,
              height: 70.0,
              decoration: BoxDecoration(
                color: Colors.blueAccent.shade100.withOpacity(0.3),
                borderRadius: BorderRadius.circular(50.0),
              ),
              child: Align(
                alignment: Alignment.center,
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(50.0),
                  child: networkCacheImageWidget(image, BoxFit.contain, 56.0, 56.0),
                ),
              ),
            ),
            5.widthBox,
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Expanded(
                          child: txt20SizeCapitalize(
                              title: name,
                              textColor: textColor,
                              context: context)),
                      txt16Size(title: userType,context: context)
                    ],
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Expanded(
                          child: txt16Size(
                              title: notification,
                              fontFamily: bold,
                              textColor: Colors.blue,
                              context: context)),
                    ],
                  ),
                  Row(
                    children: [
                      const Icon(Icons.calendar_month,size: 15,),
                      txt16Size(title: tripDate,context: context, fontFamily: bold),
                      5.widthBox,
                      const Icon(Icons.access_time_outlined,size: 15,),
                      txt16Size(title: tripTime,context: context, fontFamily: bold),
                    ],
                  )
                ],
              ),
            ),
          ],
        ),
      ),
    ),
  );
}
