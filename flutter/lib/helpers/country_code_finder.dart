class CountryCodeFinder {
  // Map of country names to phone country codes (calling codes)
  static const Map<String, String> _countryCodes = {
    'afghanistan': '+93',
    'albania': '+355',
    'algeria': '+213',
    'argentina': '+54',
    'australia': '+61',
    'austria': '+43',
    'bangladesh': '+880',
    'belgium': '+32',
    'brazil': '+55',
    'canada': '+1',
    'china': '+86',
    'colombia': '+57',
    'denmark': '+45',
    'egypt': '+20',
    'finland': '+358',
    'france': '+33',
    'germany': '+49',
    'greece': '+30',
    'india': '+91',
    'indonesia': '+62',
    'iran': '+98',
    'iraq': '+964',
    'ireland': '+353',
    'israel': '+972',
    'italy': '+39',
    'japan': '+81',
    'kenya': '+254',
    'malaysia': '+60',
    'mexico': '+52',
    'netherlands': '+31',
    'new zealand': '+64',
    'nigeria': '+234',
    'norway': '+47',
    'pakistan': '+92',
    'philippines': '+63',
    'poland': '+48',
    'portugal': '+351',
    'russia': '+7',
    'saudi arabia': '+966',
    'singapore': '+65',
    'south africa': '+27',
    'south korea': '+82',
    'spain': '+34',
    'sweden': '+46',
    'switzerland': '+41',
    'thailand': '+66',
    'turkey': '+90',
    'ukraine': '+380',
    'united arab emirates': '+971',
    'united kingdom': '+44',
    'united states': '+1',
    'vietnam': '+84',
  };

  /// Finds the phone country code for a given country name
  /// Returns null if country is not found
  static String? findCode(String countryName) {
    return _countryCodes[countryName.toLowerCase().trim()];
  }

  /// Gets all available countries
  static List<String> getAllCountries() {
    return _countryCodes.keys.toList();
  }

  /// Gets all phone country codes
  static List<String> getAllCodes() {
    return _countryCodes.values.toList();
  }
}
