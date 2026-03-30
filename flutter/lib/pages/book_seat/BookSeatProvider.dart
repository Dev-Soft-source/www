import 'dart:async';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:get/get_connect/http/src/exceptions/exceptions.dart';
import 'package:proximaride_app/consts/const_api.dart';
import 'package:proximaride_app/services/logger_service.dart';

class BookSeatProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getBookSeatDetail(rideId, fromStopId, toStopId, token, langId) async {
    try {
      final queryParams = <String, String>{
        'id': rideId.toString(),
        'lang_id': langId.toString(),
        'from_stop_id': fromStopId.toString(),
        'to_stop_id': toStopId.toString(),
      };
      final response = await getConnect.get(
          "$baseUrl/$bookSeats?${Uri(queryParameters: queryParams).query}",
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          });

      logger.info("Get Book Seat Detail Response: ${response.body.toString()}");

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
      logger.error("HTTP error in getBookSeatDetail: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in getBookSeatDetail: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future bookingRide(
      token,
      cardId,
      bookingCredit,
      seats,
      seatsAmount,
      onlinePayment,
      cashPayment,
      total,
      rideId,
      bookingType,
      bookingId,
      paymentMethod,
      captureId,
      type,
      bookedByWallet,
      coffeeFromWall,
      bookedSeatIds,
      taxPercentage,
      deductType,
      taxType,
      taxAmount,
      messageToDriver,
      fromStopId,
      toStopId,
      gPay) async {
    try {
      final url = "$baseUrl/$bookingStore?id=$rideId";
      final data = FormData({});

      data.fields.add(MapEntry("payment_method", paymentMethod.toString()));
      data.fields.add(MapEntry("booking_method", bookingType.toString()));
      data.fields.add(MapEntry("booking_id", bookingId.toString()));
      if (paymentMethod == "paypal") {
        data.fields.add(MapEntry("paypal_id", captureId.toString()));
      } else {
        data.fields.add(MapEntry("card_id", cardId.toString()));
      }
      data.fields.add(MapEntry("g_pay", gPay.toString()));
      data.fields.add(MapEntry("booking_credit", bookingCredit.toString()));
      data.fields.add(MapEntry("seats", seats.toString()));
      data.fields.add(MapEntry("seats_amount", seatsAmount.toString()));
      data.fields.add(MapEntry("online_payment", onlinePayment.toString()));
      data.fields.add(MapEntry("cash_payment", cashPayment.toString()));
      data.fields.add(MapEntry("total", total.toString()));
      data.fields.add(MapEntry('type', type.toString()));
      data.fields.add(MapEntry('booked_by_wallet', bookedByWallet.toString()));
      data.fields.add(MapEntry('booked_seat_ids', bookedSeatIds.toString()));
      data.fields.add(MapEntry('coffee_from_wall', coffeeFromWall.toString()));
      data.fields.add(MapEntry("tax_percentage", taxPercentage.toString()));
      data.fields.add(MapEntry("deduct_tax", deductType.toString()));
      data.fields.add(MapEntry("tax_type", taxType.toString()));
      data.fields.add(MapEntry("tax_amount", taxAmount.toString()));
      data.fields.add(MapEntry("driver_message", messageToDriver.toString()));
      data.fields.add(MapEntry("from_stop_id", fromStopId.toString()));
      data.fields.add(MapEntry("to_stop_id", toStopId.toString()));

      logger.info("Booking Ride URL: $url");
      logger.info("Booking Ride Data: ${data.fields.toString()}");

      final response = await getConnect.post(url, data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
      });

      logger.info("Booking Ride Response: ${response.body.toString()}");

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
      logger.error("HTTP error in bookingRide: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in bookingRide: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future seatOnHold(token, seatId, type) async {
    try {
      var url = "$baseUrl/$seatOnHoldRequest";
      final data = FormData({});
      data.fields.add(MapEntry("seat_id", seatId.toString()));
      data.fields.add(MapEntry("type", type.toString()));

      logger.info("Seat On Hold URL: $url");
      logger.info("Seat On Hold Data: ${data.fields.toString()}");

      final response = await getConnect.post(url, data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
      });

      logger.info("Seat On Hold Response: ${response.body.toString()}");

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
      logger.error("HTTP error in seatOnHold: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in seatOnHold: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }

  Future createPaymentIntent(token, amount, paymentToken) async {
    try {
      final data = {
        'amount': amount * 100,
        'currency': 'usd',
        'stripeToken': paymentToken
      };

      var url = "$baseUrl/$createNewPaymentIntent";

      logger.info("Create Payment Intent URL: $url");
      logger.info("Create Payment Intent Data: ${data.toString()}");

      final response = await getConnect.post(url, data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
      });

      logger
          .info("Create Payment Intent Response: ${response.body.toString()}");

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
      logger.error("HTTP error in createPaymentIntent: $e");
      return Future.error({
        "type": "server",
        "message": "Server error occurred. Please try again later."
      });
    } catch (exception) {
      logger.error("Unknown error in createPaymentIntent: $exception");
      return Future.error({"type": "unknown", "message": exception.toString()});
    }
  }
}
