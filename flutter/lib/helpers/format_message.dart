String formatMessage(String message, {bool separateOnCommaOnly = false}) {
  // Replace common phrases with line breaks
  if (separateOnCommaOnly) {
    return message
        .replaceAll(RegExp(r',\s*'), ',\n') // After commas only
        .trim(); // Remove leading/trailing spaces
  }

  return message
      // .replaceAll(RegExp(r',\s*'), ',\n') // After commas
      .replaceAll(RegExp(r'\.\s*'), '.\n') // After periods
      .replaceAll(RegExp(r'—\s*'), '—\n') // After em dash
      .trim(); // Remove leading/trailing spaces
}
