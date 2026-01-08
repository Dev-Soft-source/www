import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class NotificationProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));

  Future getNotifications(token, bookingType, paymentMethod, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$notifications?booking_type=$bookingType&payment_method=$paymentMethod&lang_id=$langId",
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

  Future readNotification(token,notificationId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$rNotification?id=$notificationId",
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

  Future deleteNotification(token,notificationId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$dNotification?notificationId=$notificationId",
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          });
      print(response.body);
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
