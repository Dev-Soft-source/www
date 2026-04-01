import 'package:flutter/material.dart';
import '../../consts/constFileLink.dart';
import 'textWidget.dart';

Widget _normalizedButtonChild({
  required dynamic textWidget,
  required BuildContext? context,
  required Color textColor,
}) {
  if (textWidget is String) {
    return buttonLabelText(
      title: textWidget,
      textColor: textColor,
      context: context,
      textAlign: TextAlign.center,
    );
  }

  if (textWidget is Text) {
    final resolvedTitle = textWidget.data ?? textWidget.textSpan?.toPlainText() ?? "";
    return Text(
      resolvedTitle,
      textAlign: TextAlign.center,
      maxLines: 2,
      overflow: TextOverflow.ellipsis,
      style: appButtonTextStyle(
        textColor: textColor,
        context: context,
      ),
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
    bool enabled = true}) {
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
      ),
    ),
  );
}
