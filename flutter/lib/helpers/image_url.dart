import 'package:proximaride_app/consts/const_api.dart';

String normalizeImageUrl(String? imagePath) {
  final raw = (imagePath ?? '').trim();
  if (raw.isEmpty) return '';

  String mapLegacyPath(String path) {
    if (path.contains('/api/app/v1/')) return path;

    const pathMap = <String, String>{
      '/users_images/': '/api/app/v1/users-images/',
      '/student_cards/': '/api/app/v1/student-cards/',
      '/driver_liscenses/': '/api/app/v1/driver-liscenses/',
      '/car_images/': '/api/app/v1/car-images/',
      '/home_page_icons/': '/api/app/v1/home-page-icons/',
      '/flag_icons/': '/api/app/v1/flag-icons/',
    };

    for (final entry in pathMap.entries) {
      if (path.startsWith(entry.key)) {
        return path.replaceFirst(entry.key, entry.value);
      }
    }

    return path;
  }

  final uri = Uri.tryParse(raw);
  if (uri != null && uri.hasScheme) {
    final normalizedPath = mapLegacyPath(uri.path);
    return uri.replace(path: normalizedPath).toString();
  }

  final normalizedRelative = mapLegacyPath(raw.startsWith('/') ? raw : '/$raw');
  return '$url$normalizedRelative';
}
