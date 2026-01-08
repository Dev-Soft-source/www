

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class PayoutAccountProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));


  Future getBanks(token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$bankDetail?lang_id=$langId",
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
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

  Future updateBankDetail (bankId, bankTitle, accountNumber, iban, branch, address, setDefault, token, userId) async {
    try {
      final data = {
        "bank_id": bankId.toString(),
        "bank_title": bankTitle.toString(),
        "acc_no": accountNumber.toString(),
        "iban": iban.toString(),
        "branch": branch.toString(),
        "address": address.toString(),
        "user_id": userId.toString(),
        "set_default": setDefault,
        'type': 'bank',
      };
      final response = await getConnect.post(
        "$baseUrl/$storeUpdateBankDetail",
        data,
        headers: {
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

  Future updatePaypalDetail (paypalEmail, setDefault, token, userId) async {
    try {
      final data = {
        "paypal_email": paypalEmail.toString(),
        "set_default": setDefault,
        'type': 'paypal',
      };
      final response = await getConnect.post(
          "$baseUrl/$storeUpdateBankDetail",
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

  Future verifyBank (userVerifyAmount, token, userId) async {
    try {
      final data = {
        "user_verify_amount": userVerifyAmount.toString(),
      };
      final response = await getConnect.post(
          "$baseUrl/$verifyBankDetail",
          data,
          headers: {
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
}