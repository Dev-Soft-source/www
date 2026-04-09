
import 'package:flutter/material.dart';


Widget cardShadowWidget({
  widgetChild,
  context,
  Color bgColor = Colors.white,
  EdgeInsetsGeometry margin = EdgeInsets.zero,
  EdgeInsetsGeometry padding = EdgeInsets.zero,
}) {
  return Container(
    margin: margin,
    decoration: BoxDecoration(
      boxShadow: [
        BoxShadow(
          color: Colors.grey.shade200,
          blurRadius: 10.0,
        ),
      ],
    ),
    child: Card(
      elevation: 0,
      shadowColor: Colors.grey.shade200,
      surfaceTintColor: bgColor,
      color: bgColor,
      child: Padding(
        padding: padding,
        child: widgetChild,
      ),
    ),
  );
}
