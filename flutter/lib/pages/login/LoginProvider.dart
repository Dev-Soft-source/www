import 'dart:async';
import 'dart:io';
import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/percentage_to_pipe.dart';
import 'package:proximaride_app/services/logger_service.dart';

class LoginProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getLanguages() async {
    try {
      final response = await getConnect.get("$baseUrl/$languageList", headers: {
        'Accept': 'application/json',
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
      logger.error("HTTP error in getLanguages: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getLanguages: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future getLabelTextDetail(langId, path) async {
    try {
      var url = "$baseUrl/$path";
      if (langId != 0) {
        url = "$url?lang_id=$langId";
      }
      final response = await getConnect.get(url, headers: {
        'Accept': 'application/json',
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

  Future loginUser(var email, var password, langId) async {
    try {
      final data = {
        "email": email.toString(),
        "password": password.toString(),
        "lang_id": langId.toString()
      };
      final response = await getConnect.post("$baseUrl/$login", data,
          headers: {'X-Requested-With': 'XMLHttpRequest'});

      logger.info("Login Response ${response.body}");
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
      logger.error("HTTP error in loginUser: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in loginUser: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future linkedInUserInfo(token) async {
    try {
      final response = await getConnect
          .get("https://api.linkedin.com/v2/userinfo", headers: {
        'Accept': 'application/json',
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
      logger.error("HTTP error in linkedInUserInfo: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in linkedInUserInfo: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future tikTokUserAccessToken(token) async {
    try {
      final data = {
        'client_key': 'sbawj9a1vuvtt3arxd',
        'client_secret': '1nXAcrG3ltMGQAYWA5aTNX5MC8osBtmZ',
        'code': token,
        'grant_type': 'authorization_code',
        'redirect_uri': '$url/'
      };
      final response = await getConnect
          .post('https://open.tiktokapis.com/v2/oauth/token', data, headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
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
      logger.error("HTTP error in tikTokUserAccessToken: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in tikTokUserAccessToken: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future tikTokUserInfo(token) async {
    try {
      final response = await getConnect.get(
          'https://open.tiktokapis.com/v2/user/info/?fields=open_id,union_id,avatar_url,display_name',
          headers: {
            'Accept': 'application/json',
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
      logger.error("HTTP error in tikTokUserInfo: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in tikTokUserInfo: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future socialLogin(type, email, name, photoUrl, typeId, langId) async {
    try {
      final data = {
        "type": type.toString(),
        "type_id": typeId.toString(),
        "user_name": name.toString(),
        "email": email.toString(),
        "photourl": photoUrl.toString(),
        "lang_id": langId.toString()
      };
      final response = await getConnect.post("$baseUrl/$socialLoginPost", data,
          headers: {'X-Requested-With': 'XMLHttpRequest'});
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
      logger.error("HTTP error in socialLogin: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in socialLogin: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future loginWithToken(String token, int langId) async {
    try {
      logger.info("Token: ${token}");

      token = convertPercentToPipe(token);

      logger.info("Token after conversion: ${token}");
      final response = await getConnect.get(
        "$baseUrl/$myProfileInfo?lang_id=$langId",
        headers: {
          // 'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      logger.info("Lang Id: ${langId}");

      logger.info("Login with token response: ${response.body}");

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
      logger.error("HTTP error in loginWithToken: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in loginWithToken: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
