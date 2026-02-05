import 'dart:async';
import 'dart:io';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/services/logger_service.dart';

class ConnectivityService extends GetxService {
  final Connectivity _connectivity = Connectivity();
  final _isConnected = true.obs;
  StreamSubscription<List<ConnectivityResult>>? _connectivitySubscription;

  bool get isConnected => _isConnected.value;
  RxBool get isConnectedRx => _isConnected;

  @override
  void onInit() {
    super.onInit();
    _initConnectivity();
    _connectivitySubscription =
        _connectivity.onConnectivityChanged.listen(_updateConnectionStatus);
  }

  @override
  void onClose() {
    _connectivitySubscription?.cancel();
    super.onClose();
  }

  Future<void> _initConnectivity() async {
    try {
      final result = await _connectivity.checkConnectivity();
      _updateConnectionStatus(result);
    } catch (e) {
      logger.error('Failed to check connectivity: $e');
      _isConnected.value = true; // Assume connected on error
    }
  }

  void _updateConnectionStatus(List<ConnectivityResult> results) async {
    // Check if any result indicates connectivity
    final hasConnection =
        results.any((result) => result != ConnectivityResult.none);

    if (hasConnection) {
      // Additional check: try to lookup a host to verify real internet
      final hasInternet = await _checkInternetAccess();
      _isConnected.value = hasInternet;
    } else {
      _isConnected.value = false;
    }

    logger.info('Connectivity status changed: ${_isConnected.value}');
  }

  Future<bool> _checkInternetAccess() async {
    try {
      final result = await InternetAddress.lookup('google.com');
      return result.isNotEmpty && result[0].rawAddress.isNotEmpty;
    } on SocketException catch (_) {
      return false;
    } catch (_) {
      return true; // Assume connected on other errors
    }
  }

  /// Manually check connectivity status
  Future<bool> checkConnectivity() async {
    try {
      final result = await _connectivity.checkConnectivity();
      final hasConnection = result.any((r) => r != ConnectivityResult.none);

      if (hasConnection) {
        final hasInternet = await _checkInternetAccess();
        _isConnected.value = hasInternet;
        return hasInternet;
      } else {
        _isConnected.value = false;
        return false;
      }
    } catch (e) {
      logger.error('Error checking connectivity: $e');
      return _isConnected.value; // Return current state on error
    }
  }
}
