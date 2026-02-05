import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

Widget balanceCard({context, balance, width, String balanceLabel= "Your balance"}) {
  return Container(
    height: 130,
    width: width,
    decoration: const BoxDecoration(
      borderRadius: BorderRadius.all(
        Radius.circular(10.0),
      ),
      image: DecorationImage(
        image: AssetImage(walletBalanceBackground),
        fit: BoxFit.fill,
      ),
    ),
    child: Container(
      padding: const EdgeInsets.all(10),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          txt25Size(
              title: balanceLabel,
              context: context,
              fontFamily: regular,
              textColor: Colors.white),
          const Spacer(),
            txt30Size(
                title: '\$ $balance CAD',
                context: context,
                fontFamily: bold,
                textColor: Colors.white),
        ],
      ),
    ),
  );
}
