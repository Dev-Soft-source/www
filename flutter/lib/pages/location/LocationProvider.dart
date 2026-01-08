
import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class LocationProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

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

  Future getCities(stateId, token) async {
    try {
      final response = await getConnect
          .get("$baseUrl/$getCitiesList?state_id=$stateId", headers: {
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