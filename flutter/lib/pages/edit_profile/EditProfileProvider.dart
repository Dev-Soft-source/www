import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class EditProfileProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  Future getUserDetail(token, langId) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$getUserProfileDetail?lang_id=$langId", headers: {
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

  Future getCountries(token) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$getCountriesList", headers: {
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

  Future getStates(countryId, token) async {
    try {
      final response = await getConnect
          .get("$baseUrl/$getStatesList?country_id=$countryId", headers: {
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

  Future getCities(stateId, search, token) async {
    try {
      final response = await getConnect
          .get("$baseUrl/$getCitiesList?state_id=$stateId&search=$search", headers: {
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

  Future editUserProfile(
      firstName,
      lastName,
      dob,
      address,
      postalCode,
      miniBio,
      gender,
      countryId,
      stateId,
      cityId,
      imagePath,
      imageName,
      imagePathOriginal,
      imageNameOriginal,
      type,
      token,
      userId,
      langId,
      oldImagePath) async {
    try {
      final data = FormData({});
      data.fields.add(const MapEntry("_method", "PUT"));
      data.fields.add(MapEntry("first_name", firstName));
      data.fields.add(MapEntry("last_name", lastName));
      data.fields.add(MapEntry("gender", gender));
      data.fields.add(MapEntry("dob", dob));
      data.fields.add(MapEntry("country", countryId.toString()));
      data.fields.add(MapEntry("address", address));
      data.fields.add(MapEntry("state", stateId.toString()));
      data.fields.add(MapEntry("city", cityId.toString()));
      data.fields.add(MapEntry("zipcode", postalCode.toString()));
      data.fields.add(MapEntry("bio", miniBio));
      data.fields.add(MapEntry("state", stateId.toString()));
      data.fields.add(MapEntry("type", type.toString()));
      data.fields.add(MapEntry("lang_id", langId.toString()));
      data.fields.add(MapEntry("old_image_path", oldImagePath.toString()));
      if (imageName != "") {
        data.files.add(MapEntry("government_issued_id",
            MultipartFile(File(imagePath), filename: "$imageName")));
        data.files.add(MapEntry("government_issued_original_id",
            MultipartFile(File(imagePathOriginal), filename: "$imageNameOriginal")));
      }
      final response = await getConnect
          .post("$baseUrl/$updateProfile?id=$userId", data, headers: {
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
}
