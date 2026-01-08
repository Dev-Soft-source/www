import 'dart:io';

import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../consts/constFileLink.dart';


Widget imageUploadWidget({context, onTap, String title = "Upload government-issued ID.", imageFile, double screenWidth = 0.0, bool isError = false, String title1 = "Choose file", String title2 = "(Only JPG, PNG, JPEG and GIF are allowed. Max. 10MB)"}){
  return InkWell(
    onTap: onTap,
    child: DottedBorder(
      color: isError ? Colors.red : primaryColor,
      borderType: BorderType.RRect,
      dashPattern: const [4,6],
      radius: const Radius.circular(12),
      // padding: const EdgeInsets.only(left: 50),
      child: SizedBox(
        // padding: const EdgeInsets.fromLTRB(0.0, 25.0, 0.0, 25.0),
        height: screenWidth - 40,
        width: screenWidth,
        child: imageFile == null ? Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // const Padding(padding: EdgeInsets.only(top: 25)),
            Image.asset(uploadIconImage, height: 46, width: 46),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                txt20Size(title: title, textColor: textColor, fontFamily: regular, context: context),
                5.widthBox,
                txt20Size(title: title1, textColor: primaryColor, fontFamily: regular, context: context)
              ],
            ),
            txt16SizeAlignCenter(title: title2, textColor: textColor, context: context, fontFamily: regular)
          ],
        ) : ClipRRect(
          borderRadius: BorderRadius.circular(5.0),
          child: Image.file(File(imageFile)),
        ),
      ),
    ),
  );
}