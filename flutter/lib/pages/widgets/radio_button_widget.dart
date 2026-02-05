import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/color.dart';

Widget radioButtonWidget({String value = "0", Color activeColor = primaryColor, onChanged, String groupValue = '0'}){
  return Radio(
      activeColor: primaryColor,
      value: value,
      onChanged: onChanged,
      groupValue: groupValue,
  );
}