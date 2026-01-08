

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class EmailAddressProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future updateEmailAddress (email, newEmail, confirmEmail, token, userId) async {
    try {
      final data = {
        "_method": "PUT",
        "old_email": email.toString(),
        "email": newEmail.toString(),
        "email_confirmation": confirmEmail.toString(),
      };
      final response = await getConnect.post(
        "$baseUrl/$updateUserEmail?id=$userId",
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