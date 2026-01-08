import 'package:flutter/material.dart';
import '../../../consts/constFileLink.dart';

Widget dateFieldWidget({textController, fontSize, fontFamily, onTap, prefixIcon, bool isError = false}){
  return TextFormField(
    controller: textController,
    readOnly: true,
    decoration: InputDecoration(
      enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(5.0),
          borderSide:BorderSide(color: isError ? Colors.red : Colors.grey.shade400,
              style: BorderStyle.solid, width: 1)
      ),
      focusedBorder:OutlineInputBorder(
          borderRadius: BorderRadius.circular(5.0),
          borderSide: const BorderSide(color: primaryColor)
      ),
      filled: true,
      fillColor: inputColor,
      prefixIcon: prefixIcon,
      contentPadding: const EdgeInsets.symmetric(vertical: 0.0, horizontal: 8.0)
    ),
    style: TextStyle(
      fontSize: fontSize,
      fontFamily: fontFamily,
      color: textColor
    ),
    onTap: onTap,
  );
}