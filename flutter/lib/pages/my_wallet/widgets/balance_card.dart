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
    child: Padding(
      padding: const EdgeInsets.all(10),
      child: LayoutBuilder(
        builder: (context, constraints) {
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                balanceLabel,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  color: Colors.white,
                  fontFamily: regular,
                  fontSize: constraints.maxWidth < 120 ? 18 : 25,
                  fontWeight: FontWeight.w700,
                ),
              ),
              FittedBox(
                fit: BoxFit.scaleDown,
                alignment: Alignment.centerLeft,
                child: Text(
                  '\$ $balance CAD',
                  style: TextStyle(
                    color: Colors.white,
                    fontFamily: bold,
                    fontSize: constraints.maxWidth < 120 ? 24 : 30,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          );
        },
      ),
    ),
  );
}
