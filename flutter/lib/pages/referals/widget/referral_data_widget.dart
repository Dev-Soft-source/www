import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget referralDataWidget({String data = "", String title = "", context}){
  return Padding(
    padding: EdgeInsets.fromLTRB(10.0, 0.0, 10.0, 0.0),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        txt20Size(title: title, fontFamily: bold, context: context),
        txt20Size(title: data, fontFamily: bold, context: context),
      ],
    ),
  );
}