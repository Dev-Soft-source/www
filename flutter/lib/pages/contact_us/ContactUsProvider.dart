

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class ContactUsProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future storeContactUs (name, email, phone, message, token) async {
    try {
      final data = {
        "name": name.toString(),
        "email": email.toString(),
        "phone": phone.toString(),
        "message": message.toString(),
      };
      final response = await getConnect.post(
        "$baseUrl/$sendContactUs",
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