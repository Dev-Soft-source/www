import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class MessagingProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getMessages(token, id, rideId, type) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$messages?user_id=$id&ride_id=$rideId&type=$type",  // Note: rideId is not dynamic
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          });

      if (response.status.hasError) {
        if (response.status.code == 500) {
          return response.body;
        } else if (response.status.code == 422) {
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

  Future sendNewMessage (token, rideId, receiverId, msg) async {
    try{
      var url = "";
      url = "$baseUrl/$sendMessage";
      final data = FormData({});

      data.fields.add(MapEntry("ride_id", rideId));
      data.fields.add(MapEntry("receiver_id", receiverId));
      data.fields.add(MapEntry("message", msg));

      final response = await getConnect.post(
          url,
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
    }
    catch(exception){
      return Future.error(exception.toString());
    }
  }

}
