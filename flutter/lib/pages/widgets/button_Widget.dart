
import 'package:flutter/material.dart';
import '../../consts/constFileLink.dart';



Widget elevatedButtonWidget({ textWidget, onPressed, Color btnColor = btnPrimaryColor, context, double btnRadius = 5.0}){
  return ElevatedButton(
    onPressed: onPressed,
    style: ElevatedButton.styleFrom(
        backgroundColor: btnColor,
        shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(5)
        )
    ),
    child: textWidget,
  );
}
