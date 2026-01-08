import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class StageProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 1200));


  Future getLabelTextDetail(langId, stageUrl, token) async {
    try {
      var url = "$baseUrl/$stageUrl";
      if(langId != 0){
        url = "$url?lang_id=$langId";
      }
      final response = await getConnect.get(url,
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
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

  Future setStageOne(
      token,
      firstName,
      lastName,
      gender,
      dob,
      country,
      state,
      city,
      postalCode,
      mini,

      ) async {
    try {

      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      data.fields.add(MapEntry("first_name", firstName));
      data.fields.add(MapEntry("last_name", lastName));
      data.fields.add(MapEntry("gender", gender));
      data.fields.add(MapEntry("dob", dob));
      data.fields.add(MapEntry("country", country));
      data.fields.add(MapEntry("state", state));
      data.fields.add(MapEntry("city", city));
      data.fields.add(MapEntry("zipcode", postalCode));
      data.fields.add(MapEntry('about', mini));

      final response = await getConnect
          .post("$baseUrl/$step1", data, headers: {
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

  Future setStageTwo (imageName, imagePath, imageNameOriginal, imagePathOriginal, token, skip) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      if(skip == "0"){
        data.files.add(MapEntry("image", MultipartFile(File(imagePath),filename: "$imageName")));
        data.files.add(MapEntry("profile_original_image", MultipartFile(File(imagePath),filename: "$imageName")));
      }
      data.fields.add(MapEntry("skip", skip));

      final response = await getConnect.post(
          "$baseUrl/$step2",
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest'
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

  Future setStageThree (make, model, licenseNumber, color, year,  vehicleType, fuel, carImagePath, carImageName, carImagePathOriginal, carImageNameOriginal, licenseImagePath,licenseImageName, licenseImagePathOriginal,licenseImageNameOriginal,skipVehicle, skipLicense, token, skip) async {
    try {
      var url = "";
        url = "$baseUrl/$step3";

      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));

      if(skip == "0" && skipVehicle == "0") {
        data.fields.add(MapEntry("make", make));
        data.fields.add(MapEntry("model", model));
        data.fields.add(MapEntry("type", vehicleType));
        data.fields.add(MapEntry("license_no", licenseNumber));
        data.fields.add(MapEntry("color", color.toString()));
        data.fields.add(MapEntry("year", year));
        data.fields.add(MapEntry("car_type", fuel.toString()));
        if(carImageName != ""){
          data.files.add(MapEntry("image", MultipartFile(File(carImagePath),filename: "$carImageName")));
          data.files.add(MapEntry("original_image", MultipartFile(File(carImagePathOriginal),filename: "$carImageNameOriginal")));
        }
      }

      if(skip == "0" && skipLicense == "0") {
        if(licenseImageName != ""){
          data.files.add(MapEntry("driver_liscense", MultipartFile(File(licenseImagePath),filename: "$licenseImageName")));
          data.files.add(MapEntry("driver_license_original_upload", MultipartFile(File(licenseImagePathOriginal),filename: "$licenseImageNameOriginal")));
        }
      }

      data.fields.add(MapEntry("skip", skip));
      data.fields.add(MapEntry("skip_vehicle", skipVehicle));
      data.fields.add(MapEntry("skip_license", skipLicense));


      final response = await getConnect.post(
          url,
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

  Future setStageFour (number, token, skip) async {
    try{
      var url="$baseUrl/$savePhoneNumber";
      final data = FormData({});
      if(skip == "0")
        {
          data.fields.add(MapEntry("phone", number));
        }
      else if(skip == "1"){
        data.fields.add(const MapEntry("phone", ""));
      }
      data.fields.add(const MapEntry("step", '5'));
      data.fields.add(MapEntry("skip", skip));


      final response = await getConnect.post(
          url,
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
          }
      );

      if(response.status.hasError){
        if(response.status.code == 422)
        {
          return response.body;
        }
        return Future.error(response.statusText as Object);
      }
      else{
        return response.body;
      }
    }catch(exception){
      return Future.error(exception.toString());
    }
  }

  Future updateLanguageId(
      token,
      langId
      ) async {
    try {

      final data = FormData({});
      data.fields.add(MapEntry("lang_id", langId.toString()));

      final response = await getConnect
          .post("$baseUrl/$updateUserLanguage", data, headers: {
        'Authorization': 'Bearer $token',
        'X-Requested-With': 'XMLHttpRequest',
      });
      print(response.body);
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


  Future logoutAdminDeActiveAccount(token) async {
    try {
      var url = "$baseUrl/$logoutUserAccount";
      final response = await getConnect.get(url,
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
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
