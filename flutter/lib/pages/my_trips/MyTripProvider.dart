
import 'package:get/get_connect/connect.dart';
import 'package:proximaride_app/consts/const_api.dart';

class MyTripProvider extends GetConnect{
  final getConnect = GetConnect(timeout: const Duration(seconds: 180));

    Future getAllTrips (page, token, type, pageLimit, langId) async {
    try {
      var url = baseUrl;
      if(type == "upcoming"){
        url = "$url/$upComingTrips";
      }else if(type == "completed"){
        url = "$url/$completedTrips";
      }else{
        url = "$url/$cancelledTrips";
      }
      url = "$url?page=$page&lang_id=$langId&paginate_limit=$pageLimit";
      final response = await getConnect.get(
        url,
        headers:{
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Authorization': 'Bearer $token',
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

  Future getAllRides (page, token, type, pageLimit, langId) async {
    try {
      var url = baseUrl;
      if(type == "upcoming"){
        url = "$url/$upComingRides";
      }else if(type == "completed"){
        url = "$url/$completedRides";
      }else{
        url = "$url/$cancelledRides";
      }
      url = "$url?page=$page&lang_id=$langId&paginate_limit=$pageLimit";
      final response = await getConnect.get(
          url,
          headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': 'Bearer $token',
          }
      );

      print(url);
      print('resssssssssssssssssssss get rides');
      print(response.body);
      print(response.body['data']['rides']['total']);

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

  Future addReview (
      rideId,
      reviewTextEditingController,
      vehicleCondition,
      conscious,
      comfort,
      communication,
      attitude,
      hygiene,
      respect,
      safety,
      timeliness,
      token
      ) async {
    try {

      final data = {
        'review': reviewTextEditingController,
        'vehicle_condition': vehicleCondition,
        'conscious': conscious,
        'comfort': comfort,
        'communication': communication,
        'attitude': attitude,
        'hygiene': hygiene,
        'respect': respect,
        'safety': safety,
        'timeliness': timeliness,
      };
      final response = await getConnect.post(
          "$baseUrl/$addDriverReview?ride_id=$rideId",
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
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

  Future cancelMyBooking (
      bookingId,
      reviewTextEditingController,
      tripCancelTextEditingController,
      token,
      pageType
      ) async {
    try {
      var url ="";
      var data = {};

      if(pageType == "trip"){
        url = "$baseUrl/$cancelMyBookingPost?booking_id=$bookingId";
        data = {
          '_method': "PUT",
          'message': reviewTextEditingController,
          'cancel_seats': tripCancelTextEditingController,
        };
      }

      if(pageType == "ride"){
        url = "$baseUrl/$cancelRide?id=$bookingId";
        data = {
          '_method': "PUT",
          'message': reviewTextEditingController,
          'reason': tripCancelTextEditingController
        };
      }

      final response = await getConnect.post(
          url,
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
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

  Future storePassengerReview (
      bookingId,
      reviewTextEditingController,
      conscious,
      comfort,
      communication,
      attitude,
      hygiene,
      respect,
      safety,
      timeliness,
      token
      ) async {
    try {



      final data = {
        'review': reviewTextEditingController,
        'conscious': conscious,
        'comfort': comfort,
        'communication': communication,
        'attitude': attitude,
        'hygiene': hygiene,
        'respect': respect,
        'safety': safety,
        'timeliness': timeliness,
      };

      final response = await getConnect.post(
          "$baseUrl/$storePassengerReviewed?booking_id=$bookingId",
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
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

  Future removePassengerFromRide (
      bookingId,
      removePassengerType,
      reviewTextEditingController,
      tripCancelTextEditingController,
      blockDaysTextEditingController,
      removeType,
      token
      ) async {
    try {
      var data = {
        '_method': "PUT",
        'removed_permanently': removePassengerType,
        'admin_message': reviewTextEditingController,
        'block_day' : blockDaysTextEditingController,
        'remove_type': removeType,
        'passenger_message': tripCancelTextEditingController,
      };

      final response = await getConnect.post(
          "$baseUrl/$removePassenger?booking_id=$bookingId",
          data,
          headers: {
            'Authorization': 'Bearer $token',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
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