
import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class PostRideAgainProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getPostRideList (token, type, page, limit, langId) async {
    try {
      var url = baseUrl;
      if(type == "upcoming"){
        url = "$url/$getPostRideUpcomingList";
      }else if(type == "completed"){
        url = "$url/$getPostRideCompletedList";
      }else if(type == "cancelled"){
        url = "$url/$getPostRideCancelledList";
      }
      url = "$url?paginate_limit=$limit&page=$page&lang_id=$langId";
      final response = await getConnect.get(
        url,
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