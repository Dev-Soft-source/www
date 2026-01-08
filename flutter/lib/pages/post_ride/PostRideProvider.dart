import 'dart:convert';
import 'dart:io';

import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class PostRideProvider extends GetConnect {
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

  /// Consolidated API call that fetches all Post Ride initialization data in one request.
  /// This replaces 11 separate API calls for better performance.
  Future getPostRideInitData(token, langId, {int? rideId, String? rideType}) async {
    try {
      var url = "$baseUrl/$postRideInit?lang_id=$langId";
      if (rideId != null && rideId != 0) {
        url += "&ride_id=$rideId";
        if (rideType != null) {
          url += "&ride_type=$rideType";
        }
      }

      final response = await getConnect.get(url, headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      });

      print("getPostRideInitData response: ${response.body}");

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

  Future getLabelTextDetail(langId) async {
    try {
      var url = "$baseUrl/$postRidePage";
      if(langId != 0){
        url = "$url?lang_id=$langId";
      }
      final response = await getConnect.get(url,
          headers: {
            'Accept': 'application/json',
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

  Future getPreferenceOptions(token, lang) async {
    try {
      final response = await getConnect
          .get("$baseUrl/$preferenceOptions?lang=$lang", headers: {
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

  Future getRidePreferenceOptions(token, lang) async {
    try {
      final response = await getConnect
          .get("$baseUrl/$rideFeatureOptions?lang=$lang", headers: {
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

  Future getPinkRideInfo(token, lang) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$pinkRideInfoIcon", headers: {
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

  Future getExtraCareRideInfo(token, lang) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$extraCareInfoIcon", headers: {
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

  Future getBookingOption(token, lang) async {
    try {

      var url = "";
      if(lang == 0){
        url = "$baseUrl/$bookingOptions?lang=";
      }else{
        url = "$baseUrl/$bookingOptions?lang=$lang";
      }

      final response =
          await getConnect.get(url, headers: {
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

  Future getCancellationOption(token, lang) async {
    try {

      var url = "";
      if(lang == 0){
        url = "$baseUrl/$cancellationOptions?lang=";
      }else{
        url = "$baseUrl/$cancellationOptions?lang=$lang";
      }

      final response =
      await getConnect.get(url, headers: {
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

  Future getLuggage(token, lang) async {
    try {
      var url = "";
      if(lang == 0){
        url = "$baseUrl/$luggageOptions?lang=";
      }else{
        url = "$baseUrl/$luggageOptions?lang=$lang";
      }

      final response =
          await getConnect.get(url, headers: {
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

  Future getPostRideAgainData(id, token, lang) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$editRide?ride_id=$id", headers: {
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

  Future getPaymentOptions(token, lang) async {
    try {

      var url = "";
      if(lang == 0){
        url = "$baseUrl/$paymentOptions?lang=";
      }else{
        url = "$baseUrl/$paymentOptions?lang=$lang";
      }
      final response =
          await getConnect.get(url, headers: {
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

  Future getPostRide(token) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$getPostRideData", headers: {
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

  Future postRide(
      fromTextEditingController,
      toTextEditingController,
      pickUpLocationTextEditingController,
      dropOffLocationTextEditingController,
      dateTextEditingController,
      timeTextEditingController,
      recurring,
      recurringType,
      recurringTripsTextEditingController,
      dropOffDescriptionTextEditingController,
      seatAvailable,
      seatMiddle,
      seatBack,
      skipNow,
      addNewVehicle,
      alreadyAdded,
      makeTextEditingController,
      modelTextEditingController,
      licenseNumberTextEditingController,
      colorTextEditingController,
      yearTextEditingController,
      vehicleType,
      fuel,
      carImagePath,
      carImageName,
      carImagePathOriginal,
      carImageNameOriginal,
      smoking,
      pet,
      featureList,
      bookingOption,
      luggage,
      paymentOption,
      disclaimer,
      acceptMoreLuggage,
      openCustomized,
      priceTextEditingController,
      anythingTextEditingController,
      vehicleId,
      token,
      rideType,
      rideId,
      bookingType,
      fromSpots,
      toSpots,
      priceSpots,
      rideDetailIdsArray
      ) async {
    try {

      final data = FormData({});
      if (rideType == "update") {
        data.fields.add(const MapEntry("_method", "PUT"));
      }
      data.fields.add(MapEntry('booking_type', bookingType.toString()));
      data.fields.add(MapEntry("from", fromTextEditingController));
      data.fields.add(MapEntry("to", toTextEditingController));
      data.fields.add(MapEntry("pickup", pickUpLocationTextEditingController));
      data.fields
          .add(MapEntry("dropoff", dropOffLocationTextEditingController));
      data.fields.add(MapEntry("date", dateTextEditingController.toString()));
      data.fields.add(MapEntry("time", timeTextEditingController));
      data.fields.add(MapEntry(
          "details", dropOffDescriptionTextEditingController.toString()));
      data.fields.add(MapEntry("seats", seatAvailable.toString()));
      data.fields.add(MapEntry("smoke", smoking.toString()));
      data.fields.add(MapEntry("animal_friendly", pet.toString()));
      data.fields.add(MapEntry("features", featureList.toString()));
      data.fields.add(MapEntry("booking_method", bookingOption.toString()));
      data.fields.add(MapEntry("luggage", luggage.toString()));
      data.fields
          .add(MapEntry("accept_more_luggage", acceptMoreLuggage.toString()));
      data.fields.add(MapEntry("open_customized", openCustomized.toString()));
      data.fields.add(MapEntry("price", priceTextEditingController.toString()));
      data.fields.add(MapEntry("payment_method", paymentOption.toString()));
      data.fields
          .add(MapEntry("notes", anythingTextEditingController.toString()));
      data.fields.add(MapEntry("middle_seats", seatMiddle.toString()));
      data.fields.add(MapEntry("back_seats", seatBack.toString()));

      if(fromSpots.length > 0){
        data.fields.add(MapEntry("from_spot", jsonEncode(fromSpots)));
        data.fields.add(MapEntry("to_spot", jsonEncode(toSpots)));
        data.fields.add(MapEntry("price_spot", jsonEncode(priceSpots)));
        data.fields.add(MapEntry("ride_detail_ids", rideDetailIdsArray.toString()));
      }


      if(disclaimer == true) {
        data.fields.add(const MapEntry("agree_terms", "1"));
      }

      if (addNewVehicle == true) {
        skipNow = "0";
        addNewVehicle = "1";
        alreadyAdded = "0";

        data.fields.add(MapEntry("make", makeTextEditingController.toString()));
        data.fields
            .add(MapEntry("model", modelTextEditingController.toString()));
        data.fields.add(MapEntry("vehicle_type", vehicleType.toString()));
        data.fields.add(MapEntry("year", yearTextEditingController.toString()));
        data.fields
            .add(MapEntry("color", colorTextEditingController.toString()));
        data.fields.add(MapEntry(
            "license_no", licenseNumberTextEditingController.toString()));
        data.fields.add(MapEntry("car_type", fuel.toString()));

      } else if (skipNow == true) {
        addNewVehicle = "0";
        skipNow = 1;
        alreadyAdded = "0";
      } else if (alreadyAdded == true) {
        addNewVehicle = "0";
        skipNow = "0";
        alreadyAdded = "1";
        data.fields.add(MapEntry("vehicle_id", vehicleId.toString()));
      }
      data.fields.add(MapEntry("skip_vehicle", skipNow.toString()));
      data.fields.add(MapEntry("add_vehicle", addNewVehicle.toString()));
      data.fields.add(MapEntry("added_vehicle", alreadyAdded.toString()));

      if (recurring == true) {
        data.fields.add(MapEntry("recurring", recurring.toString()));
        data.fields.add(MapEntry("recurring_type", recurringType.toString()));
        data.fields.add(MapEntry(
            "recurring_trips", recurringTripsTextEditingController.toString()));
      }
      data.fields.add(MapEntry("vehicle_id", vehicleId.toString()));
      if (carImageName != "") {
        data.files.add(MapEntry("image",
            MultipartFile(File(carImagePath), filename: "$carImageName")));
        data.files.add(MapEntry("car_image_original",
            MultipartFile(File(carImagePathOriginal), filename: "$carImageNameOriginal")));
      }
      var url = "";
      if (rideType == "update") {
        url = "$baseUrl/$updatePostRide?ride_id=$rideId";
      } else {
        url = "$baseUrl/$addPostRide";
      }
      final response = await getConnect.post(url, data, headers: {
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

  Future editPostRideData(id, token, lang) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$editRide?ride_id=$id", headers: {
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

  Future getPostRideSetting(token, langId) async {
    try {
      final response =
          await getConnect.get("$baseUrl/$postRideSettingRequest?lang_id=$langId", headers: {
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

  Future getCitiesDistance(token, langId, from, to) async {
    try {
      var data = {
        'search': from,
        'searchData': to
      };
      final response =
      await getConnect.post("$baseUrl/$distanceCities", data, headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
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
