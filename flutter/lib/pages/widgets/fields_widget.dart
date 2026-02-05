import 'package:flutter/material.dart';
import '../../../consts/constFileLink.dart';

Widget fieldsWidget(
    {textController,
    fieldType,
    readonly,
    fontSize,
    fontFamily,
    onChanged,
    String placeHolder = "",
    suffix,
    prefixIcon,
    hintTextColor = textColor,
    bool isError = false,
    maxLength = 500,
    int maxLines = 1,
    int? hintMaxLines,
    focusNode,
    onTap,
    bool autoFocus = false,
    onFieldSubmitted,
    inputFormatters}) {
  return TextFormField(
    onTap: onTap,
    controller: textController,
    readOnly: readonly,
    autofocus: autoFocus,
    onEditingComplete: () {},
    maxLength: maxLength,
    maxLines: maxLines,
    inputFormatters: inputFormatters,
    keyboardType: fieldType == "text"
        ? TextInputType.text
        : fieldType == "email"
            ? TextInputType.emailAddress
            : fieldType == "phone"
                ? TextInputType.phone
                : TextInputType.number,
    decoration: InputDecoration(
        errorStyle: const TextStyle(color: primaryColor, fontSize: 16),
        counterText: "",
        enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(5.0),
            borderSide: BorderSide(
                color: isError ? Colors.red : Colors.grey.shade400,
                style: BorderStyle.solid,
                width: 1)),
        focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(5.0),
            borderSide: const BorderSide(color: primaryColor)),
        filled: true,
        suffixIcon: suffix,
        prefixIcon: prefixIcon,
        hintText: placeHolder,
        hintMaxLines: hintMaxLines,
        hintStyle: TextStyle(
            fontSize: 18, fontFamily: fontFamily, color: hintTextColor),
        fillColor: inputColor,
        contentPadding:
            const EdgeInsets.symmetric(vertical: 8.0, horizontal: 8.0)),
    style:
        TextStyle(fontSize: fontSize, fontFamily: fontFamily, color: textColor),
    onChanged: onChanged,
    focusNode: focusNode,
    onFieldSubmitted: onFieldSubmitted,
  );
}
