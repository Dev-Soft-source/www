

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class ReviewProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getAllReviews (profileId,token,paginateLimit,page,profileType, langId) async {
    try {
      final response = await getConnect.get(
        "$baseUrl/$allReviews?user_id=$profileId&paginate_limit=$paginateLimit&page=$page&type=$profileType&lang_id=$langId",
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

}