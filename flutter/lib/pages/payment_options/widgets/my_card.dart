import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget myCard(
    {cardDetail,
    context,
    controller,
    Color textColor = textColor,
    Color cardBgColor = Colors.white,
    onSetPrimary,
    onDelete}) {
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
          cardDetail['primary_card'] == "1" ? Row(
            children: [
              Spacer(),
              Container(
                  padding: EdgeInsets.all(5.0),
                  decoration: const BoxDecoration(
                    color: Colors.green,
                    // borderRadius: BorderRadius.all(Radius.circular(10))
                  ),
                  child: txt18Size(context: context, title: "${controller.labelTextDetail['mobile_default_card_tab'] ?? "Default card"}",textColor: Colors.white)),
            ],
          ) : SizedBox(),
          Row(
            children: [
              Expanded(
                flex: 10,
                child: txt20Size(
                        title: "${controller.labelTextDetail['mobile_card_name_label'] ?? 'Card name'}",
                        textColor: textColor,
                        fontFamily: regular,
                        context: context,
                      ),
              ),
              const Spacer(flex: 1),
              Expanded(
                flex: 10,
                child: txt16SizeCapitalize(
                        title: cardDetail['card_type'],
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
                        title: "${controller.labelTextDetail['mobile_card_number_label'] ?? 'Card number'}",
                        textColor: textColor,
                        fontFamily: regular,
                        context: context,
                      ),
              ),
              const Spacer(flex: 1),
              Expanded(
                flex: 10,
                child: txt16Size(
                        title: "XXXX XXXX XXXX ${cardDetail['card_number']}",
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
                        title: "${controller.labelTextDetail['mobile_expiry_date_label'] ?? 'Expiry date'}",
                        textColor: textColor,
                        fontFamily: regular,
                        context: context,
                      ),
              ),
              const Spacer(flex: 1),
              Expanded(
                flex: 10,
                child: txt16Size(
                        title: cardDetail['exp_month'].toString().length == 1 ? "0${cardDetail['exp_month']}-${cardDetail['exp_year']}" : "${cardDetail['exp_month']}-${cardDetail['exp_year']}",
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
                child: elevatedButtonWidget(
                  context: context,
                  btnColor: Colors.red,
                  btnRadius: 2.0,
                  onPressed: onDelete,
                  textWidget: txt16Size(
                    context: context,
                    fontFamily: regular,
                    title: "${controller.labelTextDetail['delete_card_button_text'] ?? 'Delete card'}",
                    textColor: Colors.white,
                  ),
                ),
              ),
              5.widthBox,
              if(cardDetail['primary_card'] != "1")...[
              Expanded(
                flex: 10,
                child: elevatedButtonWidget(
                  context: context,
                  btnRadius: 2.0,
                  onPressed: onSetPrimary,
                  textWidget: txt16Size(
                    context: context,
                    fontFamily: regular,
                    title: "${controller.labelTextDetail['set_primary_card_label'] ?? 'Set primary'}",
                    textColor: Colors.white,
                  ),
                ),
              ),
              ]
            ],
          )
        ],
      ),
    ),
  );
}
