import 'dart:async';
import 'dart:io';
import 'package:get/get.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/services/logger_service.dart';

import '../../consts/const_api.dart';

class AddCardProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future<dynamic> createSetupIntent(dynamic token) async {
    try {
      final response = await getConnect.post(
        "$baseUrl/$paymentOptionSetupIntent",
        FormData({}),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      );
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
        if (response.status.code == 404) {
          return Future.error({
            "type": "server",
            "message":
                "Card setup endpoint was not found (404). Confirm API_URL in assets/.env ends with /api/app/v1 and your server has POST payment-option/setup-intent.",
          });
        }
        return Future.error(response.statusText as Object);
      }
      return response.body;
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
      logger.error("HTTP error in createSetupIntent: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in createSetupIntent: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future<dynamic> addCardWithSetupIntent(
    dynamic token,
    String cardName,
    String setupIntentId,
    String primaryCard,
  ) async {
    try {
      final data = FormData({});
      data.fields.add(MapEntry("name_on_card", cardName));
      data.fields.add(MapEntry("setup_intent_id", setupIntentId));
      data.fields.add(MapEntry("primary_card", primaryCard));
      logger.info("Add card (setup intent): $setupIntentId");
      final response = await getConnect
          .post("$baseUrl/$paymentOptionsAddCard", data, headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
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
      logger.error("HTTP error in addCardWithSetupIntent: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in addCardWithSetupIntent: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future addCard(
    token,
    cardName,
    cardNumber,
    cardType,
    month,
    year,
    cvvCode,
    address,
    primaryCard,
    String paymentMethodId, {
    String? billingLine1,
    String? billingLine2,
    String? billingCity,
    String? billingState,
    String? billingPostalCode,
    String? billingCountry,
  }) async {
    try {
      final data = FormData({});
      data.fields.add(MapEntry("name_on_card", cardName));
      data.fields.add(MapEntry("payment_method_id", paymentMethodId));
      data.fields.add(MapEntry("card_number", cardNumber));
      data.fields.add(MapEntry("card_type", cardType));
      data.fields.add(MapEntry("exp_month", month));
      data.fields.add(MapEntry("exp_year", year));
      data.fields.add(MapEntry("cvv_code", cvvCode));
      data.fields.add(MapEntry("address", address));
      data.fields.add(MapEntry("primary_card", primaryCard));
      if (billingLine1 != null && billingLine1.isNotEmpty) {
        data.fields.add(MapEntry("billing_line1", billingLine1));
      }
      if (billingLine2 != null && billingLine2.isNotEmpty) {
        data.fields.add(MapEntry("billing_line2", billingLine2));
      }
      if (billingCity != null && billingCity.isNotEmpty) {
        data.fields.add(MapEntry("billing_city", billingCity));
      }
      if (billingState != null && billingState.isNotEmpty) {
        data.fields.add(MapEntry("billing_state", billingState));
      }
      if (billingPostalCode != null && billingPostalCode.isNotEmpty) {
        data.fields.add(MapEntry("billing_postal_code", billingPostalCode));
      }
      if (billingCountry != null && billingCountry.isNotEmpty) {
        data.fields.add(MapEntry("billing_country", billingCountry));
      }
      logger.info("Card Data: $data");
      final response = await getConnect
          .post("$baseUrl/$paymentOptionsAddCard", data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
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
      logger.error("HTTP error in addCard: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in addCard: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
