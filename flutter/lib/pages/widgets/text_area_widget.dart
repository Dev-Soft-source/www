import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../../../consts/constFileLink.dart';
import 'textWidget.dart';

Widget textAreaWidget(
    {textController,
    readonly,
    fontSize,
    fontFamily,
    placeHolder,
    maxLines,
    bool isError = false,
    onChanged,
    focusNode,
    characterLimit = 500,
    int? hintMaxLines,
    int? maxLength,
    bool showCounter = true}) {
  return TextFormField(
    controller: textController,
    maxLines: maxLines,
    inputFormatters: [
      LengthLimitingTextInputFormatter(
          characterLimit), // Limit the text length to 100 characters
    ],
    onChanged: onChanged,
    readOnly: readonly,
    maxLength: maxLength,
    keyboardType: TextInputType.multiline,
    decoration: InputDecoration(
      enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(5.0),
          borderSide: BorderSide(
              color: isError ? Colors.red : Colors.grey.shade400,
              style: BorderStyle.solid,
              width: 1)),
      focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(5.0),
          borderSide: const BorderSide(color: primaryColor)),
      hintText: placeHolder,
      hintStyle: appPlaceholderTextStyle(
        context: null,
      ).copyWith(
        fontSize: fontSize ?? 19.0,
        fontFamily: fontFamily,
      ),
      hintMaxLines: hintMaxLines,
      filled: true,
      fillColor: inputColor,
      contentPadding:
          const EdgeInsets.symmetric(vertical: 16.0, horizontal: 12.0),
      counterText: showCounter ? null : "",
    ),
    style: TextStyle(
      fontSize: fontSize ?? 19.0,
      fontFamily: fontFamily,
      color: textColor,
      height: 1.2,
    ),
    focusNode: focusNode,
  );
}
