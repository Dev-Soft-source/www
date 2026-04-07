/// Strips trailing zeros after the decimal point (e.g. `12.30` → `12.3`, `12.00` → `12`).
String _stripTrailingFractionZeros(String s) {
  final dot = s.indexOf('.');
  if (dot == -1) {
    return s;
  }
  var frac = s.substring(dot + 1);
  frac = frac.replaceAll(RegExp(r'0+$'), '');
  if (frac.isEmpty) {
    return s.substring(0, dot);
  }
  return '${s.substring(0, dot)}.$frac';
}

String _formatNumNatural(num n) {
  if (n % 1 == 0) {
    return n.toInt().toString();
  }
  return _stripTrailingFractionZeros(n.toString());
}

String formatCurrencyValue(dynamic value) {
  if (value == null || value.toString().trim().isEmpty) {
    return '0';
  }

  final parsed = num.tryParse(value.toString());
  if (parsed == null) {
    return value.toString();
  }

  return _formatNumNatural(parsed);
}

String formatCurrency(dynamic value, {String symbol = r'$'}) {
  return '$symbol${formatCurrencyValue(value)}';
}
