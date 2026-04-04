import 'package:intl/intl.dart';

Map<dynamic, dynamic> _asRideMap(Object? ride) {
  if (ride is Map) {
    return Map<dynamic, dynamic>.from(ride);
  }
  return {};
}

/// Prefer API `departure_date_display` (localized on server); else format raw `date`.
String rideDepartureDateDisplay(Object? ride) {
  final m = _asRideMap(ride);
  final v = m['departure_date_display'];
  if (v != null && v.toString().trim().isNotEmpty) {
    return v.toString();
  }
  final raw = m['date'];
  if (raw == null || raw.toString().isEmpty) return '';
  final parsedDate = DateTime.parse(raw.toString());
  return DateFormat('MMMM d, yyyy').format(parsedDate);
}

/// Prefer API `departure_time_display`; else format using [labels] noon/midnight (ride detail page).
String rideDepartureTimeDisplay(
  Object? ride,
  Map<dynamic, dynamic> labels,
) {
  final m = _asRideMap(ride);
  final v = m['departure_time_display'];
  if (v != null && v.toString().trim().isNotEmpty) {
    return v.toString();
  }
  if (m['time'] == null) return '';
  final parsedTime = DateFormat('HH:mm:ss').parse(m['time'].toString());
  final noon = '${labels['noon_label'] ?? 'noon'}';
  final midnight = '${labels['midnight_label'] ?? 'midnight'}';
  if (parsedTime.hour == 12 && parsedTime.minute == 0) {
    return '${DateFormat('h:mm').format(parsedTime)} $noon';
  }
  if (parsedTime.hour == 0 && parsedTime.minute == 0) {
    return '${DateFormat('h:mm').format(parsedTime)} $midnight';
  }
  return DateFormat('h:mm a').format(parsedTime);
}

