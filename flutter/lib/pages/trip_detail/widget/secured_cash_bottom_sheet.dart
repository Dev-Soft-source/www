import 'package:flutter/material.dart' hide ModalBottomSheetRoute;
import 'package:flutter/foundation.dart';
import 'package:modal_bottom_sheet/modal_bottom_sheet.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/button_Widget.dart';
import 'package:proximaride_app/pages/widgets/fields_widget.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../widgets/tool_tip.dart';

securedCashBottomSheet({controller, context, height, onSubmit,errors}) {
  return defaultTargetPlatform == TargetPlatform.android
      ? showMaterialModalBottomSheet(
          context: context,
          builder: (context) => getBottomSheet(controller, context, height, onSubmit,errors),
          shape: const RoundedRectangleBorder(
              borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(10.0),
                  topRight: Radius.circular(10.0)),
              side: BorderSide(
                  color: Colors.white, style: BorderStyle.solid, width: 2.0)),
        )
      : showCupertinoModalBottomSheet(
          context: context,
          builder: (context) => getBottomSheet(controller, context, height, onSubmit,errors),
          shape: const RoundedRectangleBorder(
              borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(10.0),
                  topRight: Radius.circular(10.0)),
              side: BorderSide(
                  color: Colors.white, style: BorderStyle.solid, width: 2.0)));
}

getBottomSheet(controller, context, height, onSubmit,errors) {
  return Obx(() =>
      Container(
          height: height,
          padding: const EdgeInsets.all(10.0),
          child: Stack(
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  txt20Size(
                      title: "Enter code",
                      textColor: textColor,
                      context: context,
                      fontFamily: regular),
                  10.heightBox,
                  fieldsWidget(
                    textController: controller.securedCashTextEditingController,
                    readonly: false,
                    fontSize: 18.0,
                    fontFamily: regular,
                    placeHolder: "Enter secured cash payment code",
                    onChanged: (value){
                      controller.errors.clear();
                    }
                  ),
                  if(controller.errors.length == 0) ...[
                  ] else ...[
                    toolTip(tip: 'Please enter code', type: 'string'),
                  ],

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
                                  title: "Submit",
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
          ))

  );
}
