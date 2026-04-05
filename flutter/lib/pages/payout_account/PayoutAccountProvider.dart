import 'dart:async';
import 'dart:io';
import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class PayoutAccountProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getBanks(token, langId) async {
    try {
      final response = await getConnect
          .get("$baseUrl/$bankDetail?lang_id=$langId", headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': 'Bearer $token',
      });
      logger.info("Get Banks Response: ${response.body.toString()}");
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
      logger.error("HTTP error in getBanks: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getBanks: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  /// Same bank fields as web payout (direct deposit): holder name, transit (5),
  /// institution (3), account number (7–12). Authenticated user is resolved server-side.
  Future updateBankDetail(
    String bankTitle,
    String accountNumber,
    String branchNumber,
    String institutionNumber,
    String setDefault,
    String token,
  ) async {
    try {
      final data = {
        'type': 'bank',
        'account_holder_name': bankTitle,
        'account_holder_number': accountNumber,
        'branch_number': branchNumber,
        'institution_number': institutionNumber,
        'set_default': setDefault,
      };
      final response = await getConnect
          .post("$baseUrl/$storeUpdateBankDetail", data, headers: {
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
      logger.error("HTTP error in updateBankDetail: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in updateBankDetail: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future updateInteracDetail(
    String interacEmail,
    String interacEmailConfirm,
    bool interacAutodeposit,
    String setDefault,
    String token,
  ) async {
    try {
      final data = {
        'type': 'interac',
        'interac_email': interacEmail,
        'interac_email_confirm': interacEmailConfirm,
        'interac_autodeposit': interacAutodeposit ? '1' : '0',
        'set_default': setDefault,
      };
      final response = await getConnect
          .post("$baseUrl/$storeUpdateBankDetail", data, headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': 'Bearer $token',
      });
      logger.info("Update Interac Response: ${response.body.toString()}");
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
      logger.error("HTTP error in updateInteracDetail: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in updateInteracDetail: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future updatePaypalDetail(paypalEmail, setDefault, token, userId) async {
    try {
      final data = {
        "paypal_email": paypalEmail.toString(),
        "set_default": setDefault,
        'type': 'paypal',
      };
      final response = await getConnect
          .post("$baseUrl/$storeUpdateBankDetail", data, headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': 'Bearer $token',
      });
      logger.info("Update Paypal Detail Response: ${response.body.toString()}");
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
      logger.error("HTTP error in updatePaypalDetail: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in updatePaypalDetail: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future verifyBank(userVerifyAmount, token, userId) async {
    try {
      final data = {
        "user_verify_amount": userVerifyAmount.toString(),
      };
      final response =
          await getConnect.post("$baseUrl/$verifyBankDetail", data, headers: {
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
      logger.error("HTTP error in verifyBank: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in verifyBank: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
