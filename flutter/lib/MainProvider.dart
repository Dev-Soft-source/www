
import 'package:get/get_connect/connect.dart';
import 'consts/const_api.dart';

class MainProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future updateStatus (token, online) async {
    try {
      final data = FormData({});
      data.fields.add(MapEntry("online", online));

      final response = await getConnect.post(
          "$baseUrl/$onlineStatus",
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