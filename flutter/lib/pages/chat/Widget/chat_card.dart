import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

import '../../widgets/card_shadow_widget.dart';
import '../../widgets/network_cache_image_widget.dart';


Widget chatCard({
  context,
  controller,
  image = "",
  name = "N/A",
  message = "",
  time = "12:00:00",
  numberOfMessages = 0,
  onTap,
  chatObj
}) {
  return InkWell(
    onTap: onTap,
    child: cardShadowWidget(
      context: context,
      widgetChild: Container(
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
                              textColor: textColor,
                              context: context)),
                      Column(
                        mainAxisAlignment: MainAxisAlignment.start,
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          InkWell(
                            onTap: () async{
                              await controller.deleteChat(chatObj);
                            },
                            child: Icon(Icons.close_sharp, color: primaryColor, size: 20),
                          ),
                          txt14Size(title: time, context: context, fontFamily: bold, textColor: primaryColor),
                        ],
                      )
                    ],
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Expanded(
                          child: txt16Size(
                              title: message.toString(),
                              fontFamily: bold,
                              context: context)),
                      if(numberOfMessages != 0) ...[
                        Badge(
                          label: Text(numberOfMessages.toString()),
                          backgroundColor: primaryColor,
                          offset: const Offset(1.0, 5.0),
                          child: const Icon(Icons.add,color: Colors.transparent,),
                        )
                      ]
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    ),
  );
}
