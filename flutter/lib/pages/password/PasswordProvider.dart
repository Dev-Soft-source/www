

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class PasswordProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future updatePassword (password, newPassword, confirmPassword, token, userId) async {
    try {
      final data = {
        "_method": "PUT",
        "current_password": password.toString(),
        "new_password": newPassword.toString(),
        "confirm_password": confirmPassword.toString(),
      };
      final response = await getConnect.post(
        "$baseUrl/$updateUserPassword?id=$userId",
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