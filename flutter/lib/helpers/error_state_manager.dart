import 'package:get/get.dart';

enum ErrorType {
  network,
  server,
  unknown,
}

class ErrorStateManager {
  final isLoading = false.obs;
  final hasError = false.obs;
  final errorMessage = "".obs;
  final errorType = ErrorType.unknown.obs;
  final Rxn<Function> onRetry = Rxn<Function>();

  void setLoading() {
    isLoading.value = true;
    hasError.value = false;
    errorMessage.value = "";
  }

  void setError(String message, ErrorType type, Function retryCallback) {
    isLoading.value = false;
    hasError.value = true;
    errorMessage.value = message;
    errorType.value = type;
    onRetry.value = retryCallback;
  }

  void setSuccess() {
    isLoading.value = false;
    hasError.value = false;
    errorMessage.value = "";
    errorType.value = ErrorType.unknown;
    onRetry.value = null;
  }

  void reset() {
    isLoading.value = false;
    hasError.value = false;
    errorMessage.value = "";
    errorType.value = ErrorType.unknown;
    onRetry.value = null;
  }
}
