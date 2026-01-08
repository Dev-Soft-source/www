
import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class MyPassengerProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));


  Future getMyPassengers (rideId, token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$myPassengerList?id=$rideId&lang_id=$langId",
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

  Future noShowDriverData (rideId, type, bookingId, userId, token) async {
    try {
      var data ={
        'ride_id': rideId,
        'type': type,
        'booking_id': bookingId,
        'user_id': userId,
      };
      final response = await getConnect.post(
          "$baseUrl/$noShow",
          data,
          headers: {
            'Accept': 'application/json',
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

}