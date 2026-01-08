

import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class StudentCardProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));


  Future getStudentCard (token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$getVerifyStudentCard?lang_id=$langId",
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

  Future updateStudentCard (imageName, imagePath, imageNameOriginal, imagePathOriginal, expiryDate, token, userId) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      data.fields.add(MapEntry("student_card_exp_date", expiryDate));
      if(imageName != ""){
        data.files.add(MapEntry("student_card", MultipartFile(File(imagePath),filename: "$imageName")));
        data.files.add(MapEntry("student_card_original_upload", MultipartFile(File(imagePathOriginal),filename: "$imageNameOriginal")));
      }
      final response = await getConnect.post(
        "$baseUrl/$studentCardUpdate?id=$userId",
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