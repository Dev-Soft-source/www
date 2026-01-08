



import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class CloseAccountProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future closeAccount (token,reasons,recommend,improveMsg,closeAccReason,closeAcc) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      data.fields.add(MapEntry("reasons[]",  reasons.toString()));
      data.fields.add(MapEntry("recommen", recommend.toString()));
      data.fields.add(MapEntry("improve_message", improveMsg.toString()));
      data.fields.add(MapEntry("close_account_reason", closeAccReason.toString()));
      data.fields.add(MapEntry("close_account",closeAcc.toString() ));

      final response = await getConnect.post(
          "$baseUrl/$closeMyAccount",
          data,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': 'Bearer $token',
          }
      );
      if(response.status.isOk)
        {
          return response.body;
        }
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