

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class CoPassengerProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getTripDetail (rideId, token) async {
    try {
      final response = await getConnect.get(
        "$baseUrl/$rideDetail?id=$rideId",
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

  Future getCoPassengers (rideId, token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$coPassengers?ride_id=$rideId&lang_id=$langId",
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

}