

import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class ProfilePhotoProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future profilePhotoUpdate (imageName, imagePath, imageNameOriginal, imagePathOriginal, token, userId) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      data.files.add(MapEntry("image", MultipartFile(File(imagePath),filename: "$imageName")));
      data.files.add(MapEntry("profile_original_image", MultipartFile(File(imagePathOriginal),filename: "$imageNameOriginal")));

      final response = await getConnect.post(
        "$baseUrl/$updateProfilePhoto?id=$userId",
        data,
        headers: {
          'Authorization': 'Bearer $token',
          'X-Requested-With': 'XMLHttpRequest'
        }
      );
      print(response.body);
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