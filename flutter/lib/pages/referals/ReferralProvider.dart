
import 'package:get/get.dart';
import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class ReferralProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getMyReferralData (token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$myReferralList?lang_id=$langId",
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
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