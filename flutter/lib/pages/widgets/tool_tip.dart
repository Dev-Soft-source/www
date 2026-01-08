import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

class TriangleClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    final path = Path()
      ..moveTo(size.width / 2, 0) // Top
      ..lineTo(size.width, size.height) // Bottom right
      ..lineTo(0, size.height) // Bottom left
      ..close();
    return path;
  }

  @override
  bool shouldReclip(CustomClipper<Path> oldClipper) {
    return false;
  }
}

class ClippedTriangleWidget extends StatelessWidget {
  const ClippedTriangleWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return ClipPath(
      clipper: TriangleClipper(),
      child: Container(
        color: Colors.red,
        width: 15,
        height: 10,
      ),
    );
  }
}

Widget toolTip({fontSize = 16.0, tip, type = 'normal',int position = 0,double width = 0}) {
  return Column(
    crossAxisAlignment: position == 1 ? CrossAxisAlignment.start : CrossAxisAlignment.center,
    children: [

      position == 1 ? Padding(
        padding: EdgeInsets.only(left: width*0.1953),
        child: const ClippedTriangleWidget(),
      ) : const ClippedTriangleWidget(),
      Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(5.0),
          color: Colors.red,
        ),
        child: Tooltip(
          triggerMode: TooltipTriggerMode.manual,
          showDuration: const Duration(seconds: 1),
          message: 'I am a Tooltip',
          child: Padding(
              padding: const EdgeInsets.all(5.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if(type == 'normal')...[
                  for (var list in tip['eList']) ...[
                    Text(
                      list,
                      style: TextStyle(color: Colors.white, fontSize: fontSize),
                    ),
                  ]
                ] else ...[
                  Text(capitalizeFirstLetter(tip),style: const TextStyle(color: Colors.white,fontSize: 16),)
                  ],// this conditioning is done because of a quick fix to show toolTip in a bottom sheet (specifically for )
                ]
              )),
        ),
      ),
    ],
  );
}

Widget toolTipPassword(context, checkList,type) {
  return Column(
    crossAxisAlignment: CrossAxisAlignment.center,
    children: [
      const ClippedTriangleWidget(),
      Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(5.0),
          color: Colors.red,
        ),
        child: Tooltip(
          triggerMode: TooltipTriggerMode.manual,
          showDuration: const Duration(seconds: 1),
          message: 'I am a Tooltip',
          child: Padding(
              padding: const EdgeInsets.all(5.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      checkIcon(checkList.any((element) => element == "small")),
                      5.widthBox,
                      txt16Size(
                          context: context,
                          title: 'Include small alphabet',
                          fontFamily: regular,
                          textColor: Colors.white)
                    ],
                  ),
                  Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      checkIcon(checkList.any((element) => element == "capital")),
                      5.widthBox,
                      txt16Size(
                          context: context,
                          title: 'Include capital alphabet',
                          fontFamily: regular,
                          textColor: Colors.white)
                    ],
                  ),
                  Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      checkIcon(checkList.any((element) => element == "number")),
                      5.widthBox,
                      txt16Size(
                          context: context,
                          title: 'Include number',
                          fontFamily: regular,
                          textColor: Colors.white)
                    ],
                  ),
                  Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      checkIcon(checkList.any((element) => element == "special")),
                      5.widthBox,
                      txt16Size(
                          context: context,
                          title: 'Include special character',
                          fontFamily: regular,
                          textColor: Colors.white)
                    ],
                  ),
                  Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      checkIcon(checkList.any((element) => element == "length")),
                      5.widthBox,
                      txt16Size(
                          context: context,
                          title: 'Password length should be 8 or more',
                          fontFamily: regular,
                          textColor: Colors.white)
                    ],
                  ),
                  if(type == 'confirm_password')...[
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        checkIcon(checkList.any((element) => element == "match")),
                        5.widthBox,
                        txt16Size(
                            context: context,
                            title: 'Passwords do not match',
                            fontFamily: regular,
                            textColor: Colors.white)
                      ],
                    ),
                  ]
                ],
              )),
        ),
      ),
    ],
  );
}

Widget checkIcon(type) {
  return Container(
    padding: const EdgeInsets.all(5.0),
    decoration: BoxDecoration(
      borderRadius: const BorderRadius.all(Radius.circular(50.0)),
      color: type == true ? Colors.red[400] : Colors.green
    ),

    child: type == false
        ? const Icon(
            Icons.check,
            color: Colors.white,
      size: 12,
          )
        : const Icon(
            Icons.close,
            color: Colors.white,
      size: 12,

    ),
  );
}

String capitalizeFirstLetter(String text) {
  if (text.isEmpty) return '';
  text = text.replaceAll('_', ' ');
  return text[0].toUpperCase() + text.substring(1);
}