import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

/// 16-digit style mask (4×4 groups). Only [last4] is stored/shown (PCI); the rest is placeholder.
String maskedCardNumberLine(String last4) {
  final d = last4.trim();
  if (d.isEmpty) {
    return '•••• •••• •••• ••••';
  }
  return '•••• •••• •••• $d';
}

String formatCardExpiryDisplay(dynamic monthRaw, dynamic yearRaw) {
  final mStr = monthRaw?.toString().trim() ?? '';
  final yStr = yearRaw?.toString().trim() ?? '';
  if (mStr.isEmpty || yStr.isEmpty) return '';
  final m = int.tryParse(mStr);
  final monthPart = m == null ? mStr.padLeft(2, '0') : m.toString().padLeft(2, '0');
  return '$monthPart / $yStr';
}

Widget myCard(
    {cardDetail,
    context,
    controller,
    Color textColor = textColor,
    Color cardBgColor = Colors.white,
    onSetPrimary,
    onDelete}) {
  final String cardType = cardDetail['name_on_card']?.toString() ?? "";
  final String cardLast4 = cardDetail['card_number']?.toString() ?? "";
  final String expMonth = cardDetail['exp_month']?.toString() ?? "";
  final String expYear = cardDetail['exp_year']?.toString() ?? "";

  return Container(
    decoration: BoxDecoration(
      borderRadius: const BorderRadius.all(Radius.circular(5.0)),
      shape: BoxShape.rectangle,
      border: Border.all(color: Colors.grey, width: 1.0),
      color: cardBgColor,
    ),
    child: Padding(
      padding: const EdgeInsets.all(15.0),
      child: Column(
        children: [
          cardDetail['primary_card'] == "1"
              ? Row(
                  children: [
                    Spacer(),
                    Container(
                        padding: EdgeInsets.all(5.0),
                        decoration: const BoxDecoration(
                          color: Colors.green,
                          // borderRadius: BorderRadius.all(Radius.circular(5)),
                        ),
                        child: txt18Size(
                            context: context,
                            title:
                                "${controller.labelTextDetail['mobile_default_card_tab'] ?? "Default card"}",
                            textColor: Colors.white)),
                  ],
                )
              : SizedBox(),
          Row(
            children: [
              Expanded(
                flex: 10,
                child: txt20Size(
                  title:
                      "${controller.labelTextDetail['mobile_card_name_label'] ?? 'Card name'}",
                  textColor: textColor,
                  fontFamily: regular,
                  context: context,
                ),
              ),
              const Spacer(flex: 1),
              Expanded(
                flex: 10,
                child: txt20SizeCapitalize(
                  title: cardType,
                  textColor: textColor,
                  fontFamily: bold,
                  context: context,
                ),
              ),
            ],
          ),
          const Divider(),
          Row(
            children: [
              Expanded(
                flex: 10,
                child: txt20Size(
                  title:
                      "${controller.labelTextDetail['mobile_card_number_label'] ?? 'Card number'}",
                  textColor: textColor,
                  fontFamily: regular,
                  context: context,
                ),
              ),
              const Spacer(flex: 1),
              Expanded(
                flex: 10,
                child: txt20Size(
                  title: maskedCardNumberLine(cardLast4),
                  textColor: textColor,
                  fontFamily: bold,
                  context: context,
                ),
              ),
            ],
          ),
          const Divider(),
          Row(
            children: [
              Expanded(
                flex: 10,
                child: txt20Size(
                  title:
                      "${controller.labelTextDetail['mobile_expiry_date_label'] ?? 'Expiry date'}",
                  textColor: textColor,
                  fontFamily: regular,
                  context: context,
                ),
              ),
              const Spacer(flex: 1),
              Expanded(
                flex: 10,
                child: txt20Size(
                  title: formatCardExpiryDisplay(expMonth, expYear),
                  textColor: textColor,
                  fontFamily: bold,
                  context: context,
                ),
              ),
            ],
          ),
          const Divider(),
          Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              elevatedButtonWidget(
                context: context,
                btnColor: Colors.red,
                btnRadius: 2.0,
                onPressed: onDelete,
                textWidget: txt22Size(
                  context: context,
                  fontFamily: regular,
                  title:
                      "${controller.labelTextDetail['delete_card_button_text'] ?? 'Delete card'}",
                  textColor: Colors.white,
                ),
              ),
              if (cardDetail['primary_card'] != "1") ...[
                8.heightBox,
                elevatedButtonWidget(
                  context: context,
                  btnRadius: 2.0,
                  onPressed: onSetPrimary,
                  textWidget: txt22Size(
                    context: context,
                    fontFamily: regular,
                    title:
                        "${controller.labelTextDetail['set_primary_card_label'] ?? 'Set primary'}",
                    textColor: Colors.white,
                  ),
                ),
              ],
            ],
          ),
        ],
      ),
    ),
  );
}
