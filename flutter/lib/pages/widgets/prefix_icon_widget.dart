import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:proximaride_app/pages/post_ride/widget/seat_image_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/seat_number_widget.dart';
import 'package:proximaride_app/pages/widgets/check_box_widget.dart';
import 'package:proximaride_app/pages/widgets/date_field_widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';
import 'package:proximaride_app/pages/post_ride/widget/post_ride_widget.dart';
Widget preFixIconWidget({context, String imagePath = ""}){
  return Container( padding: EdgeInsets.all(getValueForScreenType<double>(
    context: context,
    mobile: 15.0,
    tablet: 15.0,
  )), height: getValueForScreenType<double>(
    context: context,
    mobile: 10.0,
    tablet: 10.0,
  ), width: getValueForScreenType<double>(
    context: context,
    mobile: 10.0,
    tablet: 10.0,
  ), child: Image.asset(imagePath));
}