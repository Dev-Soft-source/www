

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class LoginProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));



  Future getLanguages () async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$languageList",
          headers: {
            'Accept': 'application/json',
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

  Future getLabelTextDetail(langId, path) async {
    try {
      var url = "$baseUrl/$path";
      if(langId != 0){
        url = "$url?lang_id=$langId";
      }
      final response = await getConnect.get(url,
          headers: {
            'Accept': 'application/json',
          });

      if (response.status.hasError) {
        if (response.status.code == 422) {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      } else {
        return response.body;
      }
    } catch (exception) {
      return Future.error(exception.toString());
    }
  }

  Future loginUser (var email, var password, langId) async {
    try {
      final data = {"email": email.toString(), "password": password.toString(), "lang_id": langId.toString()};
      final response = await getConnect.post(
        "$baseUrl/$login",
        data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
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

  Future linkedInUserInfo (token) async {
    try {
      final response = await getConnect.get(
          "https://api.linkedin.com/v2/userinfo",
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



  Future tikTokUserAccessToken (token) async {
    try {
      final data = {
        'client_key': 'sbawj9a1vuvtt3arxd',
        'client_secret': '1nXAcrG3ltMGQAYWA5aTNX5MC8osBtmZ',
        'code': token,
        'grant_type': 'authorization_code',
        'redirect_uri': '$url/'
      };
      final response = await getConnect.post(
          'https://open.tiktokapis.com/v2/oauth/token',
          data,
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
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

  Future tikTokUserInfo (token) async {
    try {
      final response = await getConnect.get(
          'https://open.tiktokapis.com/v2/user/info/?fields=open_id,union_id,avatar_url,display_name',
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

  Future socialLogin (type, email, name, photoUrl, typeId, langId) async {
    try {
      final data = {"type": type.toString(), "type_id": typeId.toString(), "user_name": name.toString(), "email": email.toString(), "photourl": photoUrl.toString(), "lang_id": langId.toString()};
      final response = await getConnect.post(
          "$baseUrl/$socialLoginPost",
          data,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
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