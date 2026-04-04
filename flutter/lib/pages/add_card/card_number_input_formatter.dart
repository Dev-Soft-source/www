import 'package:flutter/services.dart';
import 'package:flutter/widgets.dart';

/// Max PAN digits for the app's card-type dropdown values (default 16).
int maxPanDigitsForCardType(String cardType) {
  switch (cardType) {
    case 'AmEx':
      return 15;
    case 'CUP':
    case 'JC':
      return 19;
    case 'DiC':
    case 'Visa':
    case 'MasterCard':
    case 'Dis':
      return 16;
    default:
      return 16;
  }
}

/// Trims [controller] when card type changes to a shorter max length.
void clampCardNumberField(TextEditingController controller, String cardType) {
  final max = maxPanDigitsForCardType(cardType);
  final digits = controller.text.replaceAll(RegExp(r'\D'), '');
  if (digits.length > max) {
    final t = digits.substring(0, max);
    controller.value = TextEditingValue(
      text: t,
      selection: TextSelection.collapsed(offset: t.length),
    );
  }
}

/// Keeps only digits, max [maxDigits] (default 16). Use for PAN entry; strip not needed for API.
class CardDigitsOnlyFormatter extends TextInputFormatter {
  CardDigitsOnlyFormatter({this.maxDigits = 16});

  final int maxDigits;

  @override
  TextEditingValue formatEditUpdate(
    TextEditingValue oldValue,
    TextEditingValue newValue,
  ) {
    final digits = newValue.text.replaceAll(RegExp(r'\D'), '');
    final trimmed =
        digits.length > maxDigits ? digits.substring(0, maxDigits) : digits;
    return TextEditingValue(
      text: trimmed,
      selection: TextSelection.collapsed(offset: trimmed.length),
    );
  }
}
