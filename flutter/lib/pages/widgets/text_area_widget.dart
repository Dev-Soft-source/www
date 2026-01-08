import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../../../consts/constFileLink.dart';

Widget textAreaWidget({textController, readonly, fontSize, fontFamily, placeHolder, maxLines, bool isError = false,onChanged,focusNode,characterLimit = 500}){
  return TextFormField(
    controller: textController,
    maxLines: maxLines,
    inputFormatters: [
      LengthLimitingTextInputFormatter(characterLimit), // Limit the text length to 100 characters
    ],
    onChanged: onChanged,
    readOnly: readonly,
    keyboardType: TextInputType.multiline,
    decoration: InputDecoration(
      enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(5.0),
          borderSide:BorderSide(color: isError ? Colors.red : Colors.grey.shade400,
              style: BorderStyle.solid, width: 1)
      ),
      focusedBorder:OutlineInputBorder(
          borderRadius: BorderRadius.circular(5.0),
          borderSide:const BorderSide(color: primaryColor)
      ),
      hintText: placeHolder,
      hintStyle: TextStyle(
        fontSize: fontSize,
        fontFamily: fontFamily,
        color: textColor
      ),
      filled: true,
      fillColor: inputColor,
    ),
    style: TextStyle(
      fontSize: fontSize,
      fontFamily: fontFamily,
      color: textColor
    ),
    focusNode: focusNode,
  );
}