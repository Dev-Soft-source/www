import 'dart:async';
import 'dart:io';
import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class CloseAccountProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future closeAccount(
      token, reasons, recommend, improveMsg, closeAccReason, closeAcc) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      data.fields.add(MapEntry("reasons[]", reasons.toString()));
      data.fields.add(MapEntry("recommen", recommend.toString()));
      data.fields.add(MapEntry("improve_message", improveMsg.toString()));
      data.fields
          .add(MapEntry("close_account_reason", closeAccReason.toString()));
      data.fields.add(MapEntry("close_account", closeAcc.toString()));

      final response =
          await getConnect.post("$baseUrl/$closeMyAccount", data, headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': 'Bearer $token',
      });
      if (response.status.isOk) {
        return response.body;
      }
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
        return Future.error(response.statusText as Object);
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
    } on GetHttpException catch (e) {
      logger.error("HTTP error in closeAccount: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in closeAccount: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
