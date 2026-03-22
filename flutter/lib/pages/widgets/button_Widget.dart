import 'package:flutter/material.dart';
import '../../consts/constFileLink.dart';
import 'textWidget.dart';

Widget elevatedButtonWidget(
    {textWidget,
    onPressed,
    Color btnColor = btnPrimaryColor,
    context,
    double btnRadius = 5.0,
    bool enabled = true}) {
  return ElevatedButton(
    onPressed: enabled ? onPressed : null,
    style: ElevatedButton.styleFrom(
        backgroundColor: enabled ? btnColor : Colors.grey.shade600,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(5))),
    child: DefaultTextStyle.merge(
      style: appButtonTextStyle(context: context),
      textAlign: TextAlign.center,
      child: textWidget,
    ),
  );
}
