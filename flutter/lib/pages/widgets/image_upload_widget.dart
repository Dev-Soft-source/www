import 'dart:io';

import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../consts/constFileLink.dart';

Widget imageUploadWidget(
    {context,
    onTap,
    String title = "Upload government-issued ID.",
    imageFile,
    double screenWidth = 0.0,
    bool isError = false,
    String title1 = "",
    String title2 = "(JPG, PNG, JPEG, and GIF. 10MB max.)"}) {
  return InkWell(
    onTap: onTap,
    child: DottedBorder(
      color: isError ? Colors.red : primaryColor,
      borderType: BorderType.RRect,
      dashPattern: const [4, 6],
      radius: const Radius.circular(12),
      // padding: const EdgeInsets.only(left: 50),
      child: SizedBox(
        // padding: const EdgeInsets.fromLTRB(0.0, 25.0, 0.0, 25.0),
        height: screenWidth - 40,
        width: screenWidth,
        child: imageFile == null
            ? Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  // const Padding(padding: EdgeInsets.only(top: 25)),
                  Image.asset(uploadIconImage, height: 46, width: 46),
                  Wrap(
                    alignment: WrapAlignment.center,
                    crossAxisAlignment: WrapCrossAlignment.center,
                    spacing: 5,
                    runSpacing: 4,
                    children: [
                      txt20Size(
                          title: title,
                          textColor: textColor,
                          fontFamily: regular,
                          context: context)
                      ,
                      if (title1.trim().isNotEmpty)
                        txt20Size(
                            title: title1,
                            textColor: primaryColor,
                            fontFamily: regular,
                            context: context)
                    ],
                  ),
                  txt20SizeAlignCenter(
                      title: title2,
                      textColor: textColor,
                      context: context,
                      fontFamily: regular)
                ],
              )
            : ClipRRect(
                borderRadius: BorderRadius.circular(5.0),
                child: Image.file(File(imageFile)),
              ),
      ),
    ),
  );
}
