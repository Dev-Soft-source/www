
import 'package:get/get.dart';
import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class MyWalletProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getPassengerMyRides (token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$passengerMyRides?lang_id=$langId",
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
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

  Future getPaidOutData (token) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$driverPaidOut",
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
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

  Future getStudentRewardPoint (token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$studentRewardPoints?lang_id=$langId",
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
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

  Future getDriverAvailableData (token) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$driverAvailableBalance",
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
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

  Future getDriverPendingData (token) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$driverPendingBalance",
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
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

  Future getDriverRewardPoint (token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$driverRewardPoints?lang_id=$langId",
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
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

  Future sendPayoutRequest (token) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$sendPayOutRequest",
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
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

  Future getToUpBalance (token) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$topUpBalanceData",
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
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

  Future buyTopUpBalance (token, cardId, amount, captureId, gPay) async {
    try {
      var url = "";
      url = "$baseUrl/$storeTopUpBalance";
      final data = FormData({});

      data.fields.add(MapEntry("payment_method", (captureId != "" ? "paypal" : "credit_card").toString()));
      if(captureId != ""){
        data.fields.add(MapEntry("paypal_id", captureId.toString()));
      }
      if(cardId != ""){
        data.fields.add(MapEntry("card_id", cardId.toString()));
      }
      data.fields.add(MapEntry("dr_amount", amount.toString()));
      data.fields.add(MapEntry("gPay", gPay.toString()));

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

  Future claimMyReward (token, type) async {
    try {
      final data = {"type": type.toString()};
      final response = await getConnect.post(
          "$baseUrl/$claimMyRewardPoints",
          data,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': 'Bearer $token',
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