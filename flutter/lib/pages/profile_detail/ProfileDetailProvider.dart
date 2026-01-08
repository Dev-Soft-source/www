

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class ProfileDetailProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getMyProfileDetail (reviewLimit, token, langId) async {
    try {
      final response = await getConnect.get(
        "$baseUrl/$myProfileInfo?reviews_limit=3&lang_id=$langId",
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
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

  Future addReply (ratingId,reply,token) async {
    try {
      final data = FormData({});
      data.fields.add(MapEntry("rating_id", ratingId.toString()));
      data.fields.add(MapEntry("reply", reply.toString()));
      final response = await getConnect.post(
          "$baseUrl/$reviewReply",
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

  Future getDriverProfileDetail (id, pageLimit, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$driverInfo?ride_id=$id&reviews_limit=$pageLimit&lang_id=$langId",
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

  Future getPassengerProfileDetail (id, pageLimit, token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$passengerProfile?user_id=$id&reviews_limit=$pageLimit&lang_id=$langId",
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