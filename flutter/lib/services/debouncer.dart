import 'dart:async';

typedef DebounceCallback = void Function();

class Debouncer {
  final int milliseconds;
  Timer? _timer;
  
  Debouncer({required this.milliseconds});
  
  void run(DebounceCallback callback) {
    _timer?.cancel();
    _timer = Timer(Duration(milliseconds: milliseconds), () {
      callback();
    });
  }
}