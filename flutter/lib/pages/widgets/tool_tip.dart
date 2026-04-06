import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/widgets/textWidget.dart';

/// Symmetric insets for tooltip bubbles (horizontal == vertical).
const EdgeInsets _kTooltipBubblePadding = EdgeInsets.fromLTRB(10.0, 16.0, 10.0, 12.0);

const TextHeightBehavior _kTooltipTextHeightBehavior = TextHeightBehavior(
  applyHeightToFirstAscent: false,
  applyHeightToLastDescent: false,
);

TextStyle _tooltipLineTextStyle(double fontSize) => TextStyle(
      color: Colors.white,
      fontSize: fontSize,
      fontFamily: carlito,
      height: 1.0,
    );

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
        color: tooltipBackgroundColor,
        width: 15,
        height: 10,
      ),
    );
  }
}

bool _isGenericFieldRequiredMessage(String s) {
  final t = s.trim().toLowerCase();
  if (t.isEmpty) return false;
  final noDot = t.endsWith('.') ? t.substring(0, t.length - 1) : t;
  return noDot == 'this field is required';
}

/// Laravel often returns both [validation.required] and a custom attribute message;
/// show only the specific lines. Also drops case-insensitive duplicates.
List<String> normalizeTooltipEList(List<dynamic> raw) {
  final items = raw
      .map((e) => e.toString().trim())
      .where((s) => s.isNotEmpty)
      .toList();
  if (items.isEmpty) return items;

  final seenLower = <String>{};
  final unique = <String>[];
  for (final s in items) {
    final key = s.toLowerCase();
    if (seenLower.contains(key)) continue;
    seenLower.add(key);
    unique.add(s);
  }

  if (unique.length == 1) return unique;

  final withoutGeneric = unique.where((s) => !_isGenericFieldRequiredMessage(s)).toList();
  if (withoutGeneric.isNotEmpty) {
    return withoutGeneric;
  }
  return unique;
}

// Widget toolTip(
//     {fontSize = 16.0,
//     tip,
//     type = 'normal',
//     int position = 0,
//     double width = 0}) {
//   return Column(
//     crossAxisAlignment:
//         position == 1 ? CrossAxisAlignment.start : CrossAxisAlignment.center,
//     children: [
//       position == 1
//           ? Padding(
//               padding: EdgeInsets.only(left: width * 0.1953),
//               child: const ClippedTriangleWidget(),
//             )
//           : const ClippedTriangleWidget(),
//       Container(
//         decoration: BoxDecoration(
//           borderRadius: BorderRadius.circular(5.0),
//           color: primaryColor,
//         ),
//         child: Tooltip(
//           triggerMode: TooltipTriggerMode.manual,
//           showDuration: const Duration(seconds: 1),
//           message: 'I am a Tooltip',
//           child: Padding(
//               padding: const EdgeInsets.all(5.0),
//               child: Column(
//                   crossAxisAlignment: CrossAxisAlignment.start,
//                   children: [
//                     if (type == 'normal') ...[
//                       for (var list in tip['eList']) ...[
//                         Text(
//                           list,
//                           style: TextStyle(
//                               color: Colors.white, fontSize: fontSize),
//                         ),
//                       ]
//                     ] else ...[
//                       Text(
//                         capitalizeFirstLetter(tip),
//                         style:
//                             const TextStyle(color: Colors.white, fontSize: 16),
//                       )
//                     ], // this conditioning is done because of a quick fix to show toolTip in a bottom sheet (specifically for )
//                   ])),
//         ),
//       ),
//     ],
//   );
// }

Widget toolTip({
  double fontSize = 20.0,
  dynamic tip,
  String type = 'normal',
  int position = 0,
  double width = 0,
}) {
  return Column(
    mainAxisSize: MainAxisSize.min,
    crossAxisAlignment:
        position == 1 ? CrossAxisAlignment.start : CrossAxisAlignment.center,
    children: [
      position == 1
          ? Padding(
              padding: EdgeInsets.only(left: width * 0.1953),
              child: const ClippedTriangleWidget(),
            )
          : const ClippedTriangleWidget(),
      Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(5.0),
          color: tooltipBackgroundColor,
        ),
        child: Padding(
          padding: _kTooltipBubblePadding,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (tip is Map && tip['eList'] is List && type == 'normal') ...[
                for (final line
                    in normalizeTooltipEList(tip['eList'] as List<dynamic>)) ...[
                  Text(
                    line,
                    style: _tooltipLineTextStyle(fontSize),
                    strutStyle: StrutStyle(
                      fontSize: fontSize,
                      fontFamily: carlito,
                      height: 1.0,
                      leading: 0,
                      forceStrutHeight: true,
                    ),
                    textHeightBehavior: _kTooltipTextHeightBehavior,
                    softWrap: true,
                  ),
                ]
              ] else if (tip is String) ...[
                Text(
                  capitalizeFirstLetter(tip),
                  style: _tooltipLineTextStyle(fontSize),
                  strutStyle: StrutStyle(
                    fontSize: fontSize,
                    fontFamily: carlito,
                    height: 1.0,
                    leading: 0,
                    forceStrutHeight: true,
                  ),
                  textHeightBehavior: _kTooltipTextHeightBehavior,
                  softWrap: true,
                )
              ],
            ],
          ),
        ),
      ),
    ],
  );
}

Widget toolTipPassword(context, checkList, type) {
  return Column(
    mainAxisSize: MainAxisSize.min,
    crossAxisAlignment: CrossAxisAlignment.center,
    children: [
      const ClippedTriangleWidget(),
      Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(5.0),
          color: tooltipBackgroundColor,
        ),
        child: Padding(
          padding: _kTooltipBubblePadding,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  checkIcon(checkList.any((element) => element == "small")),
                  5.widthBox,
                  txt20Size(
                      context: context,
                      title: 'Include small alphabet',
                      fontFamily: carlito,
                      textColor: Colors.white)
                ],
              ),
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  checkIcon(
                      checkList.any((element) => element == "capital")),
                  5.widthBox,
                  txt20Size(
                      context: context,
                      title: 'Include capital alphabet',
                      fontFamily: carlito,
                      textColor: Colors.white)
                ],
              ),
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  checkIcon(
                      checkList.any((element) => element == "number")),
                  5.widthBox,
                  txt20Size(
                      context: context,
                      title: 'Include number',
                      fontFamily: carlito,
                      textColor: Colors.white)
                ],
              ),
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  checkIcon(
                      checkList.any((element) => element == "special")),
                  5.widthBox,
                  txt20Size(
                      context: context,
                      title: 'Include special character',
                      fontFamily: carlito,
                      textColor: Colors.white)
                ],
              ),
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  checkIcon(
                      checkList.any((element) => element == "length")),
                  5.widthBox,
                  txt20Size(
                      context: context,
                      title: 'Password length should be 8 or more',
                      fontFamily: carlito,
                      textColor: Colors.white)
                ],
              ),
              if (type == 'confirm_password') ...[
                Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    checkIcon(
                        checkList.any((element) => element == "match")),
                    5.widthBox,
                    txt20Size(
                        context: context,
                        title: 'Password does not match',
                        fontFamily: carlito,
                        textColor: Colors.white)
                  ],
                ),
              ]
            ],
          ),
        ),
      ),
    ],
  );
}

// Widget toolTipEmptyPassword(BuildContext context) {
//   return Column(
//     crossAxisAlignment: CrossAxisAlignment.center,
//     children: [
//       const ClippedTriangleWidget(),
//       Container(
//         decoration: BoxDecoration(
//           borderRadius: BorderRadius.circular(5.0),
//           color: primaryColor,
//         ),
//         child: Padding(
//           padding: const EdgeInsets.all(5.0),
//           child: Row(
//             mainAxisSize: MainAxisSize.min,
//             children: [
//               // const Icon(Icons.error_outline, color: Colors.white, size: 18),
//               // 6.widthBox,
//               txt16Size(
//                 context: context,
//                 title: 'Password is required',
//                 fontFamily: regular,
//                 textColor: Colors.white,
//               ),
//             ],
//           ),
//         ),
//       ),
//     ],
//   );
// }

Widget toolTipEmptyPassword(BuildContext context) {
  return Align(
    alignment: Alignment.centerLeft, // stick to start of field
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.center, // align contents to start
      children: [
        const ClippedTriangleWidget(),
        Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(5.0),
            color: tooltipBackgroundColor,
          ),
          padding: _kTooltipBubblePadding,
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              txt20Size(
                context: context,
                title: 'Password is required',
                fontFamily: carlito,
                textColor: Colors.white,
              ),
            ],
          ),
        ),
      ],
    ),
  );
}

// Widget checkIcon(type) {
//   return Container(
//     padding: const EdgeInsets.all(5.0),
//     decoration: BoxDecoration(
//       borderRadius: const BorderRadius.all(Radius.circular(50.0)),
//       color: type == true ? Colors.red[400] : Colors.green
//     ),

//     child: type == false
//         ? const Icon(
//             Icons.check,
//             color: Colors.white,
//       size: 12,
//           )
//         : const Icon(
//             Icons.close,
//             color: Colors.white,
//       size: 12,

//     ),
//   );
// }

Widget checkIcon(bool isValid) {
  return Container(
    padding: const EdgeInsets.all(5.0),
    decoration: BoxDecoration(
      borderRadius: const BorderRadius.all(Radius.circular(50.0)),
      color: isValid ? Colors.green : Colors.red[400],
    ),
    child: Icon(
      isValid ? Icons.check : Icons.close,
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
