import 'package:flutter/material.dart';
import '../../consts/constFileLink.dart';
import 'textWidget.dart';

Widget _normalizedButtonChild({
  required dynamic textWidget,
  required BuildContext? context,
  required Color textColor,
  double? buttonFontSize,
}) {
  if (textWidget is String) {
    return buttonLabelText(
      title: textWidget,
      textColor: textColor,
      context: context,
      textAlign: TextAlign.center,
      textSize: buttonFontSize,
    );
  }

  if (textWidget is Text) {
    final resolvedTitle = textWidget.data ?? textWidget.textSpan?.toPlainText() ?? "";
    TextStyle resolvedStyle = appButtonTextStyle(
      textColor: textColor,
      context: context,
    ).merge(textWidget.style);
    if (buttonFontSize != null) {
      resolvedStyle = resolvedStyle.copyWith(fontSize: buttonFontSize);
    }
    return Text(
      resolvedTitle,
      textAlign: TextAlign.center,
      maxLines: 2,
      overflow: TextOverflow.ellipsis,
      style: resolvedStyle,
    );
  }

  return DefaultTextStyle.merge(
    style: appButtonTextStyle(
      textColor: textColor,
      context: context,
    ),
    textAlign: TextAlign.center,
    child: textWidget ?? const SizedBox.shrink(),
  );
}

Widget elevatedButtonWidget(
    {textWidget,
    onPressed,
    Color btnColor = btnPrimaryColor,
    context,
    double btnRadius = 5.0,
    bool enabled = true,
    double? buttonFontSize}) {
  final buttonTextColor = enabled ? Colors.white : Colors.white;
  return ElevatedButton(
    onPressed: enabled ? onPressed : null,
    style: ElevatedButton.styleFrom(
        backgroundColor: enabled ? btnColor : Colors.grey.shade600,
        minimumSize: const Size.fromHeight(buttonHeight),
        padding: const EdgeInsets.symmetric(vertical: 8.0, horizontal: 8.0),
        alignment: Alignment.center,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(5))),
    child: Center(
      child: _normalizedButtonChild(
        textWidget: textWidget,
        context: context,
        textColor: buttonTextColor,
        buttonFontSize: buttonFontSize,
      ),
    ),
  );
}
