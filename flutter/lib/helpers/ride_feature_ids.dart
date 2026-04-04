/// Parses `ride['feature_ids']` the same way as search result cards and booking:
/// a non-empty string split on `=` (e.g. `1=2` → pink + extra-care).
List<String> parseRideFeatureIdsFromRide(Map<String, dynamic> ride) {
  final features = <String>[];
  final dataFeature = ride['feature_ids']?.toString().trim();
  if (dataFeature != null && dataFeature.isNotEmpty) {
    features.addAll(dataFeature.split('='));
  }
  return features;
}

bool _isRideScalarTruthy(dynamic v) {
  if (v == null) return false;
  if (v is bool) return v;
  if (v is num) return v != 0;
  final s = v.toString().trim().toLowerCase();
  return s == '1' || s == 'true' || s == 'yes';
}

/// Pink ride: feature id `1` on [feature_ids], or legacy `women_only` flag.
bool rideHasPinkFeature(Map<String, dynamic> ride) {
  if (parseRideFeatureIdsFromRide(ride).contains('1')) return true;
  return _isRideScalarTruthy(ride['women_only']);
}

/// Extra-care ride: feature id `2` on [feature_ids], or legacy `extra_care` flag.
bool rideHasExtraCareFeature(Map<String, dynamic> ride) {
  if (parseRideFeatureIdsFromRide(ride).contains('2')) return true;
  return _isRideScalarTruthy(ride['extra_care']);
}
