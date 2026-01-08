
import 'package:flutter/material.dart';
import '../../consts/constFileLink.dart';



Widget outlineButtonWidget({ textWidget, onPressed, Color btnColor = btnPrimaryColor, context, double btnRadius = 5.0}){
  return OutlinedButton(
    onPressed: onPressed,
    style: OutlinedButton.styleFrom(
        shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(5)
        ),
      side: BorderSide(width: 2.0, color: primaryColor),
    ),
    child: textWidget,
  );
}
