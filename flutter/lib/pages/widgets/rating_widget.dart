import 'package:flutter/material.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/consts/constFileLink.dart';

Widget ratingWidget({
  context,
  String title = "",
  onRatingUpdate,
  double value = 0.0,
  bool isSelectable = true,
}) {
  final roundedValue = (value).roundToDouble();
  return Row(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      RatingBar(
        minRating: 0,
        maxRating: 5,
        initialRating: value,
        onRatingUpdate: isSelectable ? onRatingUpdate : (value) {},
        itemSize: 20.0,
        allowHalfRating: false,
        ratingWidget: RatingWidget(
          full: const Icon(
              Icons.star,
              color: Colors.amber),
          half: const Icon(
            Icons.add,
            color: Colors.black,
          ), empty: const Icon(
          Icons.star,
          color: Colors.grey,
        ),

        ),
        glow: false,
        ignoreGestures: isSelectable ? false : true,
      ),

      // VxRating(
      //   onRatingUpdate: (roundedValue) {
      //     if(double.parse(roundedValue) < 1.0)
      //       {
      //         print('I raaaaaaaaaaaan');
      //         onRatingUpdate("0.0");
      //       }
      //     else
      //       {
      //
      //         onRatingUpdate;
      //       }
      //   },
      //   isSelectable: isSelectable,
      //   count: 5,
      //   value: roundedValue * 2,
      //   selectionColor: const Color(0XFFFFDB26),
      //   size: 18,
      //   stepInt: true,
      // ),
      10.widthBox,
      Expanded(
        child: txt16Size(
          title: title,
          context: context,
        ),
      ),
    ],
  );
}
