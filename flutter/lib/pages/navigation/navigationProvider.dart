

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class NavigationProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future updateUserFcmToken (token, fcmToken) async {
    try {

      final data = FormData({});
      data.fields.add(MapEntry("token", fcmToken));

      final response = await getConnect.post(
        "$baseUrl/$addToken",
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

  Future removeFcmToken (token) async {
    try {
      final data = FormData({});

      final response = await getConnect.post(
          "$baseUrl/$removeToken",
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