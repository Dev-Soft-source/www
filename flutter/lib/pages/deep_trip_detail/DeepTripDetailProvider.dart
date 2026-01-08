
import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class DeepTripDetailProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getTripDetail (rideId, rideDetailId, token, langId) async {
    try {
      final response = await getConnect.get(
        "$baseUrl/$rideDetail?id=$rideId&lang_id=$langId&ride_detail_id=$rideDetailId",
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


  Future updateBookingStatus (bookingId, status, token) async {
    try {
      var url = "";
      if(status == "accept"){
        url = "$baseUrl/$acceptBookingRequest?booking_id=$bookingId";
      }else{
        url = "$baseUrl/$rejectBookingRequest?booking_id=$bookingId";
      }

      print(url);

      final response = await getConnect.get(
          url,
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

  Future enterCode (bookingId, amount, token) async {
    try {
      var data ={
        '_method': 'PUT',
        'code': amount
      };
      final response = await getConnect.post(
          "$baseUrl/$securedCashCode?booking_id=$bookingId",
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