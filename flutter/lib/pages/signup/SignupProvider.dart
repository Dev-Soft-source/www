

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class SignupProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getLabelTextDetail(langId) async {
    try {
      var url = "$baseUrl/$signUpPage";
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

    Future registerUser (
      firstName,
      lastName,
      email,
      password,
      confirmPassword,
        langId
    ) async {
    try {
      final data = {
        "first_name": firstName.toString(),
        "last_name": lastName.toString(),
        "email": email.toString(),
        "password": password.toString(),
        "password_confirmation": confirmPassword.toString(), 
        "remember-me": "1".toString(),
        "lang_id": langId.toString()
      };
      final response = await getConnect.post(
        "$baseUrl/$signup",
        data,
        headers:{
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      );
      if(response.status.hasError){
        if(response.status.code == 500){
          return response.body;
        }else if(response.status.code == 422){
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