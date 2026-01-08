
import 'package:flutter/material.dart';

import '../../consts/constFileLink.dart';


Widget txt44Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 44.0,
    tablet: 44.0,
  )).color(textColor).fontFamily(bold).make();
}

Widget txt30Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 30.0,
    tablet: 30.0,
  )).color(textColor).fontFamily(bold).make();
}

Widget txt25Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 25.0,
    tablet: 25.0,
  )).color(textColor).fontFamily(bold).make();
}


Widget txt24Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 24.0,
    tablet: 24.0,
  )).color(textColor).fontFamily(bold).make();
}

Widget txt22Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 22.0,
    tablet: 22.0,
  )).color(textColor).fontFamily(fontFamily).make();
}

Widget txt22SizeCapitalized({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 22.0,
    tablet: 22.0,
  )).color(textColor).fontFamily(fontFamily).capitalize.make();
}

Widget txt20Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 20.0,
    tablet: 20.0,
  )).color(textColor).fontFamily(fontFamily).make();
}

// Widget txt20Required({context,String title="",fontFamily = regular,textColor = textColor}){
//   return RichText(
//     text: TextSpan(
//       style: TextStyle(fontFamily: bold,fontSize: 20),
//       children: [
//         TextSpan(
//         text: title,
//         style: TextStyle(
//             color: textColor,
//
//         ),
//     ),
//         TextSpan(
//           text: '*',
//           style: TextStyle(
//               color: Colors.red
//           ),
//         )
//       ],
//     ),
//   );
// }

Widget txt18Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 18.0,
    tablet: 18.0,
  )).color(textColor).lineHeight(1.2).fontFamily(fontFamily).make();
}

Widget txt18SizeCapitalized({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 18.0,
    tablet: 18.0,
  )).color(textColor).fontFamily(fontFamily).capitalize.make();
}

Widget txt16Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 16.0,
    tablet: 16.0,
  )).color(textColor).fontFamily(fontFamily).make();
}

Widget txt16SizeLineThrough({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 16.0,
    tablet: 16.0,
  )).color(textColor).lineThrough.fontFamily(fontFamily).make();
}

Widget txt16SizeUpperCase({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.toUpperCase().text.size(getValueForScreenType<double>(
    context: context,
    mobile: 16.0,
    tablet: 16.0,
  )).color(textColor).fontFamily(fontFamily).make();
}

Widget txt14Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 14.0,
    tablet: 14.0,
  )).color(textColor).fontFamily(fontFamily).make();
}

Widget txt12Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 12.0,
    tablet: 12.0,
  )).color(textColor).fontFamily(fontFamily).make();
}

Widget txt12SizeAlignCenter({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 12.0,
    tablet: 12.0,
  )).color(textColor).align(TextAlign.center).fontFamily(fontFamily).make();
}

Widget txt12SizeCapitalized({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 12.0,
    tablet: 12.0,
  )).color(textColor).fontFamily(fontFamily).capitalize.make();
}

Widget txt14SizeCapitalized({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 14.0,
    tablet: 14.0,
  )).color(textColor).fontFamily(fontFamily).capitalize.make();
}

Widget txt10Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 10.0,
    tablet: 10.0,
  )).color(textColor).fontFamily(fontFamily).make();
}

Widget txt14SizeWithOutContext({String title ="", String fontFamily= regular, Color textColor = textColor}){
  return Text(title,style: TextStyle(
    fontFamily: fontFamily,
    fontSize: 14,
    color: textColor,
  ),);
}

Widget txt16SizeWithOutContext({String title ="", String fontFamily= regular, Color textColor = textColor}){
  return Text(title,style: TextStyle(
    fontFamily: fontFamily,
    fontSize: 16,
    color: textColor,
  ),);
}

Widget textWithUnderLine({String title ="", String fontFamily= regular, Color textColor = textColor, context, double textSize = 0.0, Color decorationColor = primaryColor}){
  return Text(
    title,
    style: TextStyle(
      decoration: TextDecoration.underline,
      decorationColor: decorationColor,
      fontFamily: fontFamily,
      color: textColor,
      fontSize: getValueForScreenType<double>(
        context: context,
        mobile: textSize,
        tablet: textSize,
      )
    ),
  );
}

TextSpan textSpan({String title ="", String fontFamily= regular, Color textColor = textColor, context, textSize, recognizer}){
  return TextSpan(
    text: title,
    style: TextStyle(
      color: textColor,
      fontSize: getValueForScreenType<double>(
        context: context,
        mobile: textSize,
        tablet: textSize,
      ),
      fontFamily: fontFamily
    ),
    recognizer:recognizer
  );
}

Widget txt25SizeCenter({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 25.0,
    tablet: 25.0,
  )).color(textColor).fontFamily(bold).align(TextAlign.center).make();
}


Widget txt28Size({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 28.0,
    tablet: 28.0,
  )).color(textColor).fontFamily(bold).make();
}

Widget txt16SizeAlignCenter({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 16.0,
    tablet: 16.0,
  )).color(textColor).align(TextAlign.center).fontFamily(fontFamily).make();
}


Widget txt22SizeAlignCenter({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 22.0,
    tablet: 22.0,
  )).color(textColor).align(TextAlign.center).fontFamily(fontFamily).make();
}

Widget txt28SizeAlignCenter({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 28.0,
    tablet: 28.0,
  )).color(textColor).align(TextAlign.center).fontFamily(fontFamily).make();
}

Widget txt20SizeCapitalize({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 20.0,
    tablet: 20.0,
  )).color(textColor).fontFamily(fontFamily).capitalize.make();
}

Widget txt25SizeCapitalize({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 25.0,
    tablet: 25.0,
  )).color(textColor).fontFamily(bold).capitalize.make();
}

Widget txt18SizeCapitalize({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 18.0,
    tablet: 18.0,
  )).color(textColor).lineHeight(1.2).capitalize.fontFamily(fontFamily).make();
}

Widget txt16SizeCapitalize({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 16.0,
    tablet: 16.0,
  )).color(textColor).fontFamily(fontFamily).capitalize.make();
}

Widget txt24SizeCapitalize({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 24.0,
    tablet: 24.0,
  )).color(textColor).fontFamily(bold).capitalize.make();
}

Widget txt18SizeCenter({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 18.0,
    tablet: 18.0,
  )).color(textColor).lineHeight(1.2).fontFamily(fontFamily).align(TextAlign.center).make();
}


Widget txt22SizeEllipsis({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 22.0,
    tablet: 22.0,
  )).color(textColor).fontFamily(bold).ellipsis.make();
}


Widget txt25SizeEllipsis({String title ="", String fontFamily= regular, Color textColor = textColor, context}){
  return title.text.size(getValueForScreenType<double>(
    context: context,
    mobile: 25.0,
    tablet: 25.0,
  )).color(textColor).fontFamily(bold).ellipsis.make();
}



