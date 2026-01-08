

import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/consts/const_api.dart';

class SearchRideProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));


  Future getLabelTextDetail(langId) async {
    try {
      var url = "$baseUrl/$findRidePage";
      if(langId != 0){
        url = "$url?lang_id=$langId";
      }
      final response = await getConnect.get(url,
          headers: {
            'Accept': 'application/json',
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

  Future checkBooking(rideId,token) async{

    try{

      var url = "$baseUrl/$checkIsAlreadyBooked?id=$rideId";

      final response = await getConnect.get(
          url,
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

    }catch (exception) {
      return Future.error(exception.toString());
    }

  }

    Future getSearchRide (
      toTextEditingController,
      fromTextEditingController,
      keywordTextEditingController,
      dateTextEditingController,
      driverNameEditingController,
      driverAge,
      driverRating,
      driverPhone,
      passengerRating,
      paymentMethod,
      vehicleType,
      features,
      luggage,
      smoking,
      pet,
      pinkRideValue,
      extraCareValue,
      pageLimit,
      page,
      token
    ) async {
    try {

      pinkRideValue = pinkRideValue == true ? '1' : '0';
      extraCareValue = extraCareValue == true ? '1' : '0';

      var url = "$baseUrl/$searchRideDetail";

      url = "$url?from=$fromTextEditingController&to=$toTextEditingController&date=$dateTextEditingController&keyword=$keywordTextEditingController&driver_age=$driverAge&driver_rating=$driverRating"
          "&driver_phone=$driverPhone&driver_name=$driverNameEditingController&passenger_rating=$passengerRating&payment_method=$paymentMethod&vehicle_type=$vehicleType"
          "&features=$features&luggage=$luggage&smoking=$smoking&pets=$pet&pink_ride=${pinkRideValue.toString()}&extra_care=${extraCareValue.toString()}&paginate_limit=$pageLimit&page=$page";

      final response = await getConnect.get(
        url,
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


  Future getFindRidePreferenceOptions (token, lang) async {
    try {
      var url = "";
      if(lang == 0){
        url = "$baseUrl/$findRideFeatureOptions?lang=";
      }else{
        url = "$baseUrl/$findRideFeatureOptions?lang=$lang";
      }
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

  Future addNewVehicle (make, model, licenseNumber, color, year,  vehicleType, fuel, imagePath, imageName, vehicleId, token) async {
    try {

      var url = "";
      if(vehicleId == 0){
        url = "$baseUrl/$vehicleStore";
      }else{
        url = "$baseUrl/$vehicleUpdate?id=$vehicleId";
      }

      final data = FormData({});
      if(vehicleId != 0){
        data.fields.add(const MapEntry("_method", "PUT"));
      }
      data.fields.add(MapEntry("make", make));
      data.fields.add(MapEntry("model", model));
      data.fields.add(MapEntry("type", vehicleType));
      data.fields.add(MapEntry("liscense_no", licenseNumber));
      data.fields.add(MapEntry("color", color.toString()));
      data.fields.add(MapEntry("year", year));
      data.fields.add(MapEntry("car_type", fuel.toString()));
      if(imageName != ""){
        data.files.add(MapEntry("image", MultipartFile(File(imagePath),filename: "$imageName")));
      }
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

  Future getVehicleInfo (vehicleId, token) async {
    try {
      final response = await getConnect.get(
          "$baseUrl/$editVehicle?id=$vehicleId",
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

  Future removeVehicle (vehicleId, token) async {
    try {
      final response = await getConnect.delete(
          "$baseUrl/$deleteVehicle?id=$vehicleId",
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