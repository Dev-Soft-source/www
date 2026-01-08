
import 'package:flutter/material.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/average_rating_stars_widget.dart';
import 'package:proximaride_app/pages/widgets/reply_bottom_sheet.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import 'network_cache_image_widget.dart';

Widget reviewCard({
  Color reviewCardColor = primaryColor,
  context,
  controller,
  bottomSheetHeight,
  name = "",
  rating = 0.0,
  review = "",
  image = "",
  addedOn = "",
  replied = true,
  showReply = true,
  profileType = "user",
  onSubmit,
  type = 0,
  screenHeight,
  screenWidth,
  index,
  String responseLabel = "response",
  String repliedLabel = "replied",
  String replyLabel = "Reply",
  String replyHeadingLabel = "Reply",
  String replyPlaceholder = "Enter your reply",
  String replySubmitButtonLabel = "Submit",
}) {
  var replyUserData = type == 2 ? "${controller.reviewsLeft[index]['to']['first_name']}'s $responseLabel: " : "$repliedLabel: ";
  replyUserData = replyUserData.replaceFirst(replyUserData[0], replyUserData[0].toUpperCase());
  return Card(
    elevation: 2,
    color: reviewCardColor,
    surfaceTintColor: reviewCardColor,
    child: Container(
      padding: EdgeInsets.all(getValueForScreenType<double>(
        context: context,
        mobile: 10.0,
        tablet: 10.0,
      )),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.start,
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.max,
        children: [
          Container(
            width: 66.0,
            height: 66.0,
            decoration: BoxDecoration(
              color: Colors.blueAccent.shade100.withOpacity(0.3),
              borderRadius: BorderRadius.circular(50.0),
            ),
            child: Align(
              alignment: Alignment.center,
              child: ClipRRect(
                borderRadius: BorderRadius.circular(50.0),
                child: networkCacheImageWidget(
                  image,
                  BoxFit.contain,
                  56.0,
                  56.0,
                ),
              ),
            ),
          ),
          5.widthBox,
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Expanded(
                        child: txt22SizeCapitalized(
                            title: name,
                            fontFamily: regular,
                            textColor: textColor,
                            context: context)),
                    if (showReply == true && profileType == "user")
                      if (type == 0)
                        Align(
                          alignment: Alignment.centerRight,
                          child: InkWell(
                            onTap: () async {
                              await replyBottomSheet(
                                controller: controller,
                                context: context,
                                height: bottomSheetHeight,
                                onSubmit: onSubmit,
                                replyHeadingLabel: replyHeadingLabel,
                                replyPlaceholder: replyPlaceholder,
                                replySubmitButtonLabel: replySubmitButtonLabel
                              );
                            },
                            child: Ink(
                              child: textWithUnderLine(
                                  title: replyLabel,
                                  textColor: primaryColor,
                                  fontFamily: regular,
                                  context: context,
                                  textSize: 18),
                            ),
                          ),
                        ),
                  ],
                ),
                5.heightBox,
                averageStarWidget(
                  fixValue: rating < 11 ? rating.toInt() : 0,
                  decimalValue: rating < 11 ? (rating - rating.toInt()) : 0.0,
                  screenHeight: screenHeight,
                  screenWidth: screenWidth,
                  emptyStars: rating < 11 ? ((5 - rating).toInt()) : 0,
                  starSize: 0.025,
                ),
                5.heightBox,
                txt16Size(
                  title: review,
                  textColor: textColor,
                  fontFamily: bold,
                  context: context
                ),
                5.heightBox,
                Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (!showReply) ...[
                      txt16Size(
                          title: replyUserData,
                          textColor: primaryColor,
                          fontFamily: regular,
                          context: context),
                      0.3.widthBox,
                      Expanded(
                        child: txt16Size(
                            title: replied,
                            textColor: textColor,
                            fontFamily: bold,
                            context: context),
                      ),
                    ],
                    3.widthBox,
                    Image.asset(clockImage, height: 15, width: 15,),
                    txt16Size(
                        title: addedOn.toString().substring(10, 16),
                        textColor: textColor,
                        fontFamily: bold,
                        context: context),
                  ],
                ),

              ],
            ),
          ),
        ],
      ),
    ),
  );
}
