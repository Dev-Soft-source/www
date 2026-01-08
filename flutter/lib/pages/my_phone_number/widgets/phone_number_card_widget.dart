import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../widgets/button_Widget.dart';

Widget phoneNumberCardWidget(
    { context,
      verification,
      number,
      def,
      onDelete,
      onVerify,
      onSetDefault,
      Color cardBgColor = Colors.white,
      controller
    }) {

  return Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      txt20Size(fontFamily: regular,context: context,title: verification == "0" ?
      "${controller.labelTextDetail['unverified_number_label'] ?? "Unverified number"}" :
      def == "0" ? "${controller.labelTextDetail['verified_number_label'] ?? "Verified number"}" :
      "${controller.labelTextDetail['default_verified_number_label'] ?? "Default verified number"}"),
      Container(
        decoration: BoxDecoration(
          border: Border.all(width: 1, color: Colors.grey.shade400),
          borderRadius: BorderRadius.circular(5.0),
          color: cardBgColor,
        ),
        padding: const EdgeInsets.all(10.0),
        child: Column(
          children: [
            Row(
              children: [
                // txt20Size(fontFamily: bold, title: "Phone number:", context: context, textColor: Colors.black),
                5.widthBox,
                txt20Size(fontFamily: bold, title: number, context: context, textColor: Colors.black),
              ],
            ),
            const Divider(),
            Row(children: [
              if(def == "0" && verification == "1")...[
                Expanded(
                  child: elevatedButtonWidget(
                    textWidget: txt16Size(title: "${controller.labelTextDetail['set_as_default_label'] ?? "Set as default"}", context: context, textColor: Colors.white),
                    onPressed: onSetDefault,
                  ),
                ),
                10.widthBox,
                Expanded(
                  child: elevatedButtonWidget(
                    btnColor: Colors.red,
                    textWidget: txt16Size(title: "${controller.labelTextDetail['delete_button_text'] ?? "Delete"}", context: context, textColor: Colors.white),
                    onPressed: onDelete,
                  ),
                ),
              ],//end of if
              if(def == "0" && verification == "0")...[
                Expanded(
                  child: elevatedButtonWidget(
                      textWidget: txt16Size(title: "${controller.labelTextDetail['mobile_verify_button_text'] ?? "Verify"}", context: context, textColor: Colors.white),
                      onPressed: onVerify
                  ),
                ),
                10.widthBox,
                Expanded(
                  child: elevatedButtonWidget(
                    btnColor: Colors.red,
                      textWidget: txt16Size(title: "${controller.labelTextDetail['delete_button_text'] ?? "Delete"}", context: context, textColor: Colors.white),
                      onPressed: onDelete
                  ),
                ),
              ]//end of if
            ],)
          ],
        ),
      ),
    ],
  );
}
