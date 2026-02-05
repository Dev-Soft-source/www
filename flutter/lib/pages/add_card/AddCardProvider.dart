import 'dart:async';
import 'dart:io';
import 'package:get/get.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/services/logger_service.dart';

import '../../consts/const_api.dart';

class AddCardProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future addCard(token, cardName, cardNumber, cardType, month, year, cvvCode,
      address, primaryCard, tokenId) async {
    try {
      final data = FormData({});
      data.fields.add(MapEntry("name_on_card", cardName));
      data.fields.add(MapEntry("stripeToken", tokenId));
      data.fields.add(MapEntry("card_number", cardNumber));
      data.fields.add(MapEntry("card_type", cardType));
      data.fields.add(MapEntry("exp_month", month));
      data.fields.add(MapEntry("exp_year", year));
      data.fields.add(MapEntry("cvv_code", cvvCode));
      data.fields.add(MapEntry("address", address));
      data.fields.add(MapEntry("primary_card", primaryCard));
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
