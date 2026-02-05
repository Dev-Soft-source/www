import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../widgets/button_Widget.dart';

Widget phoneNumberCardWidget(
    {context,
    verification,
    number,
    def,
    onDelete,
    onVerify,
    onSetDefault,
    Color cardBgColor = Colors.white,
    controller}) {
  final verificationStatus = (verification ?? "0").toString();
  final defaultStatus = (def ?? "0").toString();

  return Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      txt20Size(
          fontFamily: regular,
          context: context,
          title: verificationStatus == "0"
              ? "${controller.labelTextDetail['unverified_number_label'] ?? "Unverified number"}"
              : defaultStatus == "0"
                  ? "${controller.labelTextDetail['verified_number_label'] ?? "Verified number"}"
                  : "${controller.labelTextDetail['default_verified_number_label'] ?? "Default verified number"}"),
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
                // Spacer
                5.widthBox,
                // Phone number as 20px body text
                txt20Size(
                    fontFamily: bold,
                    title: number,
                    context: context,
                    textColor: Colors.black),
              ],
            ),
            const Divider(),
            Row(
              children: [
                if (defaultStatus == "0" && verificationStatus == "1") ...[
                  Expanded(
                    child: elevatedButtonWidget(
                      textWidget: txt22Size(
                          title:
                              "${controller.labelTextDetail['set_as_default_label'] ?? "Set as default"}",
                          context: context,
                          textColor: Colors.white),
                      onPressed: onSetDefault,
                    ),
                  ),
                  10.widthBox,
                  Expanded(
                    child: elevatedButtonWidget(
                      btnColor: Colors.red,
                      textWidget: txt22Size(
                          title:
                              "${controller.labelTextDetail['delete_button_text'] ?? "Delete"}",
                          context: context,
                          textColor: Colors.white),
                      onPressed: onDelete,
                    ),
                  ),
                ], //end of if
                if (defaultStatus == "0" && verificationStatus == "0") ...[
                  Expanded(
                    child: elevatedButtonWidget(
                        textWidget: txt22Size(
                            title:
                                "${controller.labelTextDetail['mobile_verify_button_text'] ?? "Verify"}",
                            context: context,
                            textColor: Colors.white),
                        onPressed: onVerify),
                  ),
                  10.widthBox,
                  Expanded(
                    child: elevatedButtonWidget(
                        btnColor: Colors.red,
                        textWidget: txt22Size(
                            title:
                                "${controller.labelTextDetail['delete_button_text'] ?? "Delete"}",
                            context: context,
                            textColor: Colors.white),
                        onPressed: onDelete),
                  ),
                ] //end of if
              ],
            )
          ],
        ),
      ),
    ],
  );
}
