
import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';



Widget paymentCardBodyWidget({ int selectedCardId = 0, int cardId = 0, onChanged, String cardNumber = "", String cardType ="", context}){
  String maskedCardNumber = "";
  maskedCardNumber = "XXXX XXXX XXXX $cardNumber";



  return Container(
    padding: const EdgeInsets.all(5.0),
    decoration: BoxDecoration(
      color: paymentCardColor,
      borderRadius: BorderRadius.circular(10.0),
    ),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            SizedBox(
              height: 25,
              width: 25,
              child: Radio(
                value: cardId,
                groupValue: selectedCardId,
                activeColor: primaryColor,
                onChanged: onChanged,
              ),
            ),
            5.widthBox,
            Image.asset(
               cardType == "Visa" ?
               visaCardPng :
               cardType == "mastercard" ?
               masterCardPng :
               cardType == "amex" ?
               amExCardPng :
               cardType == "discover" ?
               discoverCardPng :
               cardType == "unionpay" ?
               jcb :
               cardType == "jcb" ?
               unionpay :
               cardType == "diners" ?
               dinerClubCardPng :
               visaCardPng,
              width: 40.0
            )
          ],
        ),
        Column(
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            txt14Size(title: maskedCardNumber, context: context),
            txt14SizeCapitalized(title: cardType, context: context)
          ],
        )
      ],
    ),
  );
}
