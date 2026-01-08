import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';

Widget averageStarWidget({
  context,
  fixValue,
  decimalValue,
  screenHeight = 0.1,
  screenWidth = 0.1,
  starSize = 0.045,
  emptyStars,
}) {

  var count = 0;
  return Container(
    child: Row(children: [
      for(int i=0;i<fixValue;i++)...[
        Image.asset(
          fullStar,
          height: starSize * screenWidth,
          width: starSize * screenWidth,
        ),
        5.widthBox,
      ],
      if(fixValue<5)...[
      if(decimalValue > 0.0)...[
        if(decimalValue <=0.25 )...[
          Image.asset(
            quarter1Star,
            height: starSize * screenWidth,
            width: starSize * screenWidth,
          ),
          5.widthBox
        ],
        if(decimalValue > 0.25 && decimalValue <0.75)...[
          Image.asset(
            quarter2Star,
            height: starSize * screenWidth,
            width: starSize * screenWidth,
          ),
          5.widthBox
        ],
        if(decimalValue >= 0.75)...[
          Image.asset(
            quarter3Star,
            height: starSize * screenWidth,
            width: starSize * screenWidth,
          ),
          5.widthBox
        ],
        ],
      ],
      for(int i=0;i<emptyStars;i++)...[
        Image.asset(
          emptyStar,
          height: starSize * screenWidth,
          width: starSize * screenWidth,
        ),
        5.widthBox
      ],



    ],
    ),
  );
}
