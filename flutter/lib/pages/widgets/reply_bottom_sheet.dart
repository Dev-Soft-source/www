import 'package:flutter/material.dart' hide ModalBottomSheetRoute;
import 'package:flutter/foundation.dart';
import 'package:modal_bottom_sheet/modal_bottom_sheet.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';
import 'package:proximaride_app/pages/widgets/text_area_widget.dart';

replyBottomSheet({controller, context, height, onSubmit, String replyHeadingLabel = "Reply",
  String replyPlaceholder = "Enter your reply",
  String replySubmitButtonLabel = "Submit"}) {
  return defaultTargetPlatform == TargetPlatform.android
      ? showMaterialModalBottomSheet(
          context: context,
          builder: (context) => getBottomSheet(controller:controller, context:context, height:height, onSubmit:onSubmit, replyPlaceholder:replyPlaceholder, replyHeadingLabel:replyHeadingLabel,replySubmitButtonLabel:replySubmitButtonLabel),
          shape: const RoundedRectangleBorder(
              borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(10.0),
                  topRight: Radius.circular(10.0)),
              side: BorderSide(
                  color: Colors.white, style: BorderStyle.solid, width: 2.0)),
        )
      : showCupertinoModalBottomSheet(
          context: context,
          builder: (context) => getBottomSheet(controller:controller, context:context, height:height, onSubmit:onSubmit, replyPlaceholder:replyPlaceholder, replyHeadingLabel:replyHeadingLabel,replySubmitButtonLabel:replySubmitButtonLabel),
          shape: const RoundedRectangleBorder(
              borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(10.0),
                  topRight: Radius.circular(10.0)),
              side: BorderSide(
                  color: Colors.white, style: BorderStyle.solid, width: 2.0)));
}

getBottomSheet({controller, context, height, onSubmit, String replyHeadingLabel = "Reply", String replyPlaceholder = "Enter your reply",
String replySubmitButtonLabel = "Submit"}) {
  return Container(
      height: height,
      padding: const EdgeInsets.all(10.0),
      child: Stack(
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              txt20Size(
                  title: replyHeadingLabel,
                  textColor: textColor,
                  context: context,
                  fontFamily: regular),
              10.heightBox,
              textAreaWidget(
                textController: controller.replyTextController,
                readonly: false,
                fontSize: 16.0,
                fontFamily: regular,
                placeHolder: replyPlaceholder,
                maxLines: 4,
                characterLimit: 100,
              )
            ],
          ),
          Align(
            alignment: Alignment.bottomCenter,
            child: Row(
              children: [
                Expanded(
                    child: SizedBox(
                  height: 50,
                  child: elevatedButtonWidget(
                      textWidget: txt22Size(
                          title: replySubmitButtonLabel,
                          context: context,
                          textColor: Colors.white,
                          fontFamily: regular),
                      onPressed: onSubmit
                      ),
                ))
              ],
            ),
          )
        ],
      ));
}
