import 'dart:async';
import 'dart:io';
import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class SignupProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getLabelTextDetail(langId) async {
    try {
      var url = "$baseUrl/$signUpPage";
      if (langId != 0) {
        url = "$url?lang_id=$langId";
      }
      logger.info("URL: $url");
      final response = await getConnect.get(url, headers: {
        'Accept': 'application/json',
      });

      logger.info("Response: ${response.body}");

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
      logger.error("HTTP error in getLabelTextDetail: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getLabelTextDetail: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future registerUser(firstName, lastName, email, password, confirmPassword,
      rideshareDisclaimer, referralCode, langId) async {
    try {
      final data = {
        "first_name": firstName.toString(),
        "last_name": lastName.toString(),
        "email": email.toString(),
        "password": password.toString(),
        "password_confirmation": confirmPassword.toString(),
        "rideshare_disclaimer": rideshareDisclaimer.toString(),
        "remember-me": "1".toString(),
        "lang_id": langId.toString()
      };

      // Add referral code if provided
      if (referralCode != null && referralCode.toString().isNotEmpty) {
        data["referral_code"] = referralCode.toString();
      }
      final response =
          await getConnect.post("$baseUrl/$signup", data, headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      });
      if (response.status.hasError) {
        if (response.status.connectionError) {
          return Future.error({
            "type": "network",
            "message":
                "No internet connection. Please check your network and try again."
          });
        }
        if (response.status.code == 500) {
          return response.body;
        } else if (response.status.code == 422) {
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
      logger.error("HTTP error in registerUser: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in registerUser: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
