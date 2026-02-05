
import 'package:flutter/material.dart';


Widget cardShadowWidget({widgetChild, context, Color bgColor = Colors.white}){
  return Container(
    decoration: BoxDecoration(
      boxShadow: [
        BoxShadow(
          color: Colors.grey.shade200,
          blurRadius: 10.0,
        ),
      ],
    ),
    child:Card(
      elevation: 0,
      surfaceTintColor: bgColor,
      color: bgColor,
      child: widgetChild,
    )
  );
}
