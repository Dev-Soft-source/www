

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class MyPhoneNumberProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));


  Future getPhoneNumbers(token, langId) async{
    try {

      var url = "$baseUrl/$phoneNumbers?lang_id=$langId";
      final response = await getConnect.get(
          url,
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

  Future addNewPhoneNumber(token , number) async{
    try{
      var url="$baseUrl/$savePhoneNumber";
      final data = FormData({});
      data.fields.add(MapEntry("phone", number));

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
        if(response.status.code == 422)
        {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }
      else{
        return response.body;
      }
    }catch(exception){
      return Future.error(exception.toString());
    }

  }

  Future sendVerificationCode(token, phoneNumber, phoneNumberId) async {
    try{
      var url="$baseUrl/$sendCode?id=$phoneNumberId";
      final data = FormData({});
      // Always send phone number - backend can use it or look up by ID
      data.fields.add(MapEntry("phone", phoneNumber));

      final response = await getConnect.post(
          url,
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
          }
      );
      print("sendVerificationCode response: ${response.body}");
      if(response.status.hasError){
        if(response.status.code == 422)
        {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }
      else{
        return response.body;
      }
    }catch(exception){
      return Future.error(exception.toString());
    }
  }

  Future verifyPhone (token, code) async {
    try{
      var url="$baseUrl/$verify";
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      data.fields.add(MapEntry("code", code));

      final response = await getConnect.post(
          url,
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
          }
      );

      if(response.status.hasError){
        if(response.status.code == 422)
        {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }
      else{
        return response.body;
      }
    }catch(exception){
      return Future.error(exception.toString());
    }
  }

  Future deletePhoneNumber (token, phoneNumId) async {
    try{
      var url="$baseUrl/$deletePhone?id=$phoneNumId";

      final response = await getConnect.delete(
          url,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
          }
      );

      if(response.status.hasError){
        if(response.status.code == 422)
        {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }
      else{
        return response.body;
      }
    }catch(exception){
      return Future.error(exception.toString());
    }
  }

  Future setAsDefaultNumber(token, phoneNumberId) async {
    try{
      var url="$baseUrl/$defaultNum?id=$phoneNumberId";

      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));


      final response = await getConnect.post(
          url,
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
          }
      );

      if(response.status.hasError){
        if(response.status.code == 422)
        {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }
      else{
        return response.body;
      }
    }catch(exception){
      return Future.error(exception.toString());
    }
  }


}