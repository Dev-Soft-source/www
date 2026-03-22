import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:http/http.dart' as http;
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/helpers/percentage_to_pipe.dart';
import 'package:proximaride_app/services/logger_service.dart';

class LoginProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Map<String, String> _networkError([Object? error]) {
    final message = error == null
        ? "Could not reach the API server at $baseUrl. Check that the backend is running and that this device can access it."
        : "Could not reach the API server at $baseUrl. Check that the backend is running and that this device can access it. Details: $error";

    return {
      "type": "network",
      "message": message,
    };
  }

  Future getLanguages() async {
    try {
      final response = await getConnect.get("$baseUrl/$languageList", headers: {
        'Accept': 'application/json',
      });
      if (response.status.hasError) {
        if (response.status.connectionError) {
          logger.error("Connection error in getLanguages for $baseUrl");
          return Future.error(_networkError(response.statusText));
        }
        if (response.status.code == 422) {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      } else {
        return response.body;
      }
    } on SocketException catch (e) {
      logger.error("Socket error in getLanguages for $baseUrl: $e");
      return Future.error(_networkError(e));
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
          logger.error("Connection error in getLabelTextDetail for $url");
          return Future.error(_networkError(response.statusText));
        }
        if (response.status.code == 422) {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      } else {
        return response.body;
      }
    } on SocketException catch (e) {
      logger.error("Socket error in getLabelTextDetail for $url: $e");
      return Future.error(_networkError(e));
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
      final response = await http.post(
        Uri.parse("$baseUrl/$login"),
        headers: {
          'Accept': 'application/json',
        },
        body: data,
      );

      final responseBody =
          response.body.isNotEmpty ? jsonDecode(response.body) : null;

      logger.info("Login Response $responseBody");
      if (response.statusCode >= 400) {
        if (response.statusCode == 0) {
          return Future.error({
            "type": "network",
            "message":
                "No internet connection. Please check your network and try again."
          });
        }
        if (response.statusCode == 422) {
          return responseBody;
        }
        return Future.error(response.reasonPhrase ?? 'Request failed');
      } else {
        return responseBody;
      }
    } on SocketException catch (e) {
      logger.error("Socket error in loginUser for $baseUrl/$login: $e");
      return Future.error(_networkError(e));
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
          logger.error("Connection error in loginWithToken for $baseUrl/$myProfileInfo");
          return Future.error(_networkError(response.statusText));
        }
        if (response.status.code == 422) {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      } else {
        return response.body;
      }
    } on SocketException catch (e) {
      logger.error("Socket error in loginWithToken for $baseUrl/$myProfileInfo: $e");
      return Future.error(_networkError(e));
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
      final response = await http.post(
        Uri.parse("$baseUrl/$socialLoginPost"),
        headers: {
          'Accept': 'application/json',
        },
        body: data,
      );

      final responseBody =
          response.body.isNotEmpty ? jsonDecode(response.body) : null;

      if (response.statusCode >= 400) {
        if (response.statusCode == 0) {
          return Future.error({
            "type": "network",
            "message":
                "No internet connection. Please check your network and try again."
          });
        }
        if (response.statusCode == 422) {
          return responseBody;
        }
        return Future.error(response.reasonPhrase ?? 'Request failed');
      } else {
        return responseBody;
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
      logger.info("Token: $token");

      token = convertPercentToPipe(token);

      logger.info("Token after conversion: $token");
      final response = await getConnect.get(
        "$baseUrl/$myProfileInfo?lang_id=$langId",
        headers: {
          // 'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      logger.info("Lang Id: $langId");

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
