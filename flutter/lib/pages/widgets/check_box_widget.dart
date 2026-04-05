import 'package:flutter/material.dart';
import 'package:proximaride_app/consts/color.dart';

Widget checkBoxWidget({
  bool value = false,
  Color activeColor = primaryColor,
  onChanged,
  bool isError = false,
  size,
  /// Tighter vertical layout so the box lines up with adjacent label text (e.g. payout rows).
  bool compact = false,
}) {
  return Checkbox(
      materialTapTargetSize:
          compact ? MaterialTapTargetSize.shrinkWrap : null,
      visualDensity: compact ? VisualDensity.compact : null,
      side: BorderSide(color: isError ? primaryColor : Colors.grey.shade500),
      activeColor: activeColor,
      fillColor: WidgetStateProperty.resolveWith((states) {
        if (states.contains(WidgetState.selected)) {
          return activeColor;
        }
        return Colors.transparent;
      }),
      value: value,
      onChanged: onChanged
  );
}
