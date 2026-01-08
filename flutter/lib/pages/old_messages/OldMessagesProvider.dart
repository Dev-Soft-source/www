import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class OldMessagesProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getOldChats(token) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$oldChats",
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

}
