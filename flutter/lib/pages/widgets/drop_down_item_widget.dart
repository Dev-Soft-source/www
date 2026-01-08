import 'package:flutter/material.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../consts/constFileLink.dart';

Widget dropDownItemWidget({context, onTap, String name ="", bool isSelected = false}){
  return InkWell(
    onTap: onTap,
    child: Ink(
      padding: const EdgeInsets.fromLTRB(5.0, 0.0, 5.0, 0.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            child: txt18Size(title: name, textColor: textColor, fontFamily: regular, context: context),
          ),
          isSelected == true ?
          const Icon(Icons.check, size: 14, color: textColor) :
          const SizedBox(),
        ],
      ),
    ),
  );
}