

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class ForgetPasswordProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future forgetPassword (email) async {
    try {
      final data = {
        "email": email.toString(),
      };
      final response = await getConnect.post(
        "$baseUrl/$forgotPassword",
        data,
        headers: {
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
}