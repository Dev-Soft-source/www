import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class MyReviewsProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getAllReviews(token, paginateLimit, page, reviewType, langId) async {
    try {
      // paginateLimit = 20;
      var url = baseUrl;
      if(reviewType == "received"){
        url = "$url/$reviewsReceived";
      }else{
        url = "$url/$reviewsLeft";
      }
      url = "$url?paginate_limit=$paginateLimit&page=$page&lang_id=$langId";

      final response = await getConnect.get(
          url,
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
        print(response.body);
        return response.body;
      }
    } catch (exception) {
      return Future.error(exception.toString());
    }
  }

  Future addReply(ratingId, reply, token) async {
    try {
      final data = FormData({});
      data.fields.add(MapEntry("rating_id", ratingId.toString()));
      data.fields.add(MapEntry("reply", reply.toString()));
      final response =
          await getConnect.post("$baseUrl/$reviewReply", data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
      });
      if (response.status.hasError) {
        if (response.status.code == 422) {
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
