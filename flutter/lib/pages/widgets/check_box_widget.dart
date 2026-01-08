import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/color.dart';

Widget checkBoxWidget({bool value = false, Color activeColor = primaryColor, onChanged, bool isError = false,size}){
  return Checkbox(
      side: BorderSide(color: isError ? primaryColor : Colors.grey.shade500),
      activeColor: primaryColor,
      value: value,
      onChanged: onChanged
  );
}