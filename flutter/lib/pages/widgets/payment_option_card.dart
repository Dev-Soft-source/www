import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget paymentOptionCard({
  context,
  img,
  title = '',
  onTap,
  iconWidth,
  iconHeight,
}) {
  return InkWell(
    onTap: onTap,
    child: Container(
        padding: const EdgeInsets.all(15.0),
        decoration: BoxDecoration(
          color: paymentCardColor,
          borderRadius: BorderRadius.all(
            Radius.circular(8.0),
          ),
          border: Border.all(color: Colors.grey, width: 1)
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            Image.asset(
              img,
              height: iconHeight,
              width: iconWidth,
            ),
            10.widthBox,
            Expanded(
              child: txt18Size(title: title, context: context),
            ),
            const Icon(
              Icons.arrow_forward_ios,
              size: 20,
            ),
          ],
        )),
  );
}
