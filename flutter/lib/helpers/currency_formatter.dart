String formatCurrencyValue(dynamic value, {int decimals = 2}) {
  if (value == null || value.toString().trim().isEmpty) {
    return 0.toStringAsFixed(decimals);
  }

  final parsed = num.tryParse(value.toString());
  if (parsed == null) {
    return value.toString();
  }

  return parsed.toStringAsFixed(decimals);
}

String formatCurrency(dynamic value, {int decimals = 2, String symbol = r'$'}) {
  return '$symbol${formatCurrencyValue(value, decimals: decimals)}';
}
