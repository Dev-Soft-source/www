import 'dart:async';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class PasswordProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future updatePassword(
      password, newPassword, confirmPassword, token, userId) async {
    try {
      final data = {
        "_method": "PUT",
        "current_password": password.toString(),
        "new_password": newPassword.toString(),
        "confirm_password": confirmPassword.toString(),
      };
      final response = await getConnect
          .post("$baseUrl/$updateUserPassword?id=$userId", data, headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': 'Bearer $token',
      });
      if (response.status.hasError) {
        if (response.status.connectionError) {
          return Future.error({
            "type": "network",
            "message":
                "No internet connection. Please check your network and try again."
          });
        }
        if (response.status.code == 422) {
          return response.body;
        }
        return Future.error({
          'type': 'server',
          'message': response.statusText ?? 'Server error occurred'
        });
      } else {
        return response.body;
      }
    } on SocketException {
      return Future.error({
        "type": "network",
        "message":
            "No internet connection. Please check your network and try again."
      });
    } on TimeoutException {
      return Future.error({
        "type": "network",
        "message": "Request timed out. Please try again."
      });
    } catch (exception) {
      if (exception is SocketException ||
          exception.toString().contains("SocketException")) {
        return Future.error({
          'type': 'network',
          'message':
              'No internet connection. Please check your network and try again.'
        });
      }
      return Future.error({'type': 'unknown', 'message': exception.toString()});
    }
  }
}
