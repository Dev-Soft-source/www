

import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class DriverLicenseProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 120));


  Future getDriverLicense (token, langId) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$getVerifyDriverLicense?lang_id=$langId",
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

  Future updateDriverLicense (imageName, imagePath, imageNameOriginal, imagePathOriginal, token, userId) async {
    try {

      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      if(imageName != ""){
        data.files.add(MapEntry("driver_liscense", MultipartFile(File(imagePath),filename: "$imageName")));
        data.files.add(MapEntry("driver_license_original_upload", MultipartFile(File(imagePathOriginal),filename: "$imageNameOriginal")));
      }
      final response = await getConnect.post(
        "$baseUrl/$driverLicenseUpdate?id=$userId",
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

  Future removeDriverLicense(token) async{

    try {
      final response = await getConnect.get(
          "$baseUrl/$removeLicense",
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