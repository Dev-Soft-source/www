
import 'dart:convert';

import 'package:flutter/cupertino.dart';
import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class BookSeatProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getBookSeatDetail (rideId, rideDetailId, token, langId) async {
    try {
      final response = await getConnect.get(
        "$baseUrl/$bookSeats?id=$rideId&lang_id=$langId&ride_detail_id=$rideDetailId",
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        }
      );
      if(response.status.hasError){
        if(response.status.code == 422){
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }else{
        return response.body;
      }
    } catch (exception) {
      return Future.error(exception.toString());
    }
  }

  Future bookingRide (token, cardId, bookingCredit, seats, seatsAmount, onlinePayment, cashPayment, total, rideId, bookingType, bookingId, paymentMethod, captureId,type, bookedByWallet, coffeeFromWall, bookedSeatIds,taxPercentage,
      deductType,
      taxType,
      taxAmount, messageToDriver, gPay) async {
    try {
      var url = "";

      if(bookingId != 0){
        if(bookingType == "instant"){
          url = "$baseUrl/$updateInstantBooking?booking_id=$bookingId";
        }else{
          url = "$baseUrl/$updateBookingRequest?booking_id=$bookingId";
        }
      }else{
        if(bookingType == "instant"){
          url = "$baseUrl/$instantBooking?id=$rideId";
        }else{
          url = "$baseUrl/$requestBooking?id=$rideId";
        }
      }
      final data = FormData({});
      if(bookingId != 0){
        data.fields.add(const MapEntry("_method", "PUT"));
      }

      data.fields.add(MapEntry("payment_method", paymentMethod.toString()));
      if(paymentMethod == "paypal"){
        data.fields.add(MapEntry("paypal_id", captureId.toString()));
      }else{
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


      final response = await getConnect.post(
          url,
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
          }
      );

      print(response.body);

      if(response.status.hasError){
        if(response.status.code == 422){
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }else{
        return response.body;
      }
    } catch (exception) {
      return Future.error(exception.toString());
    }
  }

  Future seatOnHold (token, seatId, type) async {
    try {
      var url = "";

      url = "$baseUrl/$seatOnHoldRequest";
      final data = FormData({});
      data.fields.add(MapEntry("seat_id", seatId.toString()));
      data.fields.add(MapEntry("type", type.toString()));

      final response = await getConnect.post(
          url,
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
          }
      );

      if(response.status.hasError){
        if(response.status.code == 422){
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }else{
        return response.body;
      }
    } catch (exception) {
      return Future.error(exception.toString());
    }
  }


  Future createPaymentIntent (token, amount, paymentToken) async {
    try {
      var url = "";

      final data = {'amount': amount * 100, 'currency': 'usd', 'stripeToken': paymentToken};

      url = "$baseUrl/$createNewPaymentIntent";

      final response = await getConnect.post(
          url,
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
          }
      );

      print(response.body);

      if(response.status.hasError){
        if(response.status.code == 422){
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }else{
        return response.body;
      }
    } catch (exception) {
      return Future.error(exception.toString());
    }
  }
}