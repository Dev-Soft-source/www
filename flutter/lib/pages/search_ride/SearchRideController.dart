import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:proximaride_app/consts/constFileLink.dart';
import 'package:proximaride_app/pages/post_ride/PostRideProvider.dart';
import 'package:proximaride_app/pages/search_ride/SearchRideProvider.dart';
import 'package:proximaride_app/services/service.dart';

class SearchRideController extends GetxController{


  final serviceController = Get.find<Service>();
  var isLoading = false.obs;
  var isOverlayLoading = false.obs;
  var driverAge = "".obs;
  var driverRating = "".obs;
  var driverPhone = "".obs;
  var passengerRating = "".obs;
  var paymentMethod = "".obs;
  var vehicleType = "".obs;
  var featureList = [].obs;
  var luggage = "".obs;
  var smoking = "".obs;
  var pet = "".obs;
  var errors = [].obs;
  var pinkRideReadOnly = false.obs;
  var pinkRideCheck = false.obs;
  var extraCareCheck = false.obs;

  var vehicleTypeList = [].obs;
  var vehicleTypeLabelList = [].obs;

  ScrollController scrollController = ScrollController();
  var rides = List<dynamic>.empty(growable: true).obs;
  var recentSearchList = List<dynamic>.empty(growable: true).obs;
  var smokingList = [].obs;
  var petList = [].obs;
  var smokingLabelList = [].obs;
  var petLabelList = [].obs;
  var rideFeatureList = [].obs;
  var rideFeatureLabelList = [].obs;
  var paymentOptionList = [].obs;
  var paymentOptionToolTipList = [].obs;
  var paymentOptionLabelList = [].obs;
  var bookingOptionList = [].obs;
  var bookingOptionToolTipList = [].obs;
  var bookingOptionLabelList = [].obs;
  var luggageList = [].obs;
  var luggageListToolTip = [].obs;
  var luggageListLabel = [].obs;
  var passengerRatingList = [].obs;
  var passengerRatingLabelList = [].obs;
  var isScrollLoading = false.obs;
  var noRideFound = false.obs;
  var noMoreData = false.obs;
  var filter = false.obs;
  var actionType = "".obs;
 var pageLimit = 10;
 var page  = 1;
  var searchTotal = 0.obs;

 var isAlreadyBooked = false.obs;
 var labelTextDetail = {}.obs;
 var popupTextDetail = {}.obs;
  var validationMessageDetail = {}.obs;
  var firmDiscount = "".obs;

 late TextEditingController fromTextEditingController, toTextEditingController, keywordTextEditingController, dateTextEditingController, driverNameEditingController;

@override
  void onInit() async{
    // TODO: implement onInit
    super.onInit();
    fromTextEditingController = TextEditingController();
    toTextEditingController= TextEditingController();
    keywordTextEditingController = TextEditingController();
    dateTextEditingController = TextEditingController();
    driverNameEditingController = TextEditingController();
    await getLabelTextDetail();
    await getSearchRide(0);

    // paginateRideList();
    Future.delayed(const Duration(seconds: 1), () async{
      isOverlayLoading(true);
      await getPreferenceOptions();
      await getFindRidePreferenceOptions();
      await getBookingOption();
      await getLuggage();
      await getPaymentOptions();
      isOverlayLoading(false);
    });
  }

  @override
  void onClose() {
    // TODO: implement onClose
    super.onClose();
  }


  // Future<void> checkBooking(int rideId) async {
  //   try {
  //     final ride = rides.firstWhere((ride) => ride.id == rideId);
  //     // Do something with the found ride
  //     print('Ride found: ${ride.departure}');
  //   } catch (e) {
  //     // Handle the case where no ride is found
  //     print('Ride not found');
  //   }
  // }


  getLabelTextDetail() async{
    try{
      isLoading(true);
      await SearchRideProvider().getLabelTextDetail(
          serviceController.langId
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['findRidePage'] != null){
            labelTextDetail.addAll(resp['data']['findRidePage']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_convertible_text'] ?? "Convertable");
            vehicleTypeList.add(labelTextDetail['vehicle_type_convertible_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_coupe_text'] ?? "Coupe");
            vehicleTypeList.add(labelTextDetail['vehicle_type_coupe_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_hatchback_text'] ?? "Hatchback");
            vehicleTypeList.add(labelTextDetail['vehicle_type_hatchback_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_minivan_text'] ?? "Minivan");
            vehicleTypeList.add(labelTextDetail['vehicle_type_minivan_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_sedan_text'] ?? "Sedan");
            vehicleTypeList.add(labelTextDetail['vehicle_type_sedan_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_station_wagon_text'] ?? "Station wagon");
            vehicleTypeList.add(labelTextDetail['vehicle_type_station_wagon_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_suv_text'] ?? "SUV");
            vehicleTypeList.add(labelTextDetail['vehicle_type_suv_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_truck_text'] ?? "Truck");
            vehicleTypeList.add(labelTextDetail['vehicle_type_truck_value']);
            vehicleTypeLabelList.add(labelTextDetail['vehicle_type_van_text'] ?? "Van");
            vehicleTypeList.add(labelTextDetail['vehicle_type_van_value']);
          }

          if(resp['data'] != null && resp['data']['messages'] != null){
            popupTextDetail.addAll(resp['data']['messages']);
          }

          if(resp['data'] != null && resp['data']['validationMessages'] != null){
            validationMessageDetail.addAll(resp['data']['validationMessages']);
          }
        }
        isLoading(false);
      },onError: (err){
        isLoading(false);
        serviceController.showDialogue(err.toString());
      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  Future<void> checkBooking(rideId, tripDetailId) async{
  try {
    SearchRideProvider()
      .checkBooking(rideId, serviceController.token)
      .then((resp) async {
        if(resp['status'] != null && resp['status'] == "Error"){
          serviceController.showDialogue(resp['message'] .toString());
        } else if(resp['status'] != null && resp['status'] == "Success") {
          if(resp['data'] != null ) {

          final ride = rides.firstWhere((ride) => ride['id'] == rideId);

          serviceController.loginUserDetail['passenger_average_rating'] = (serviceController.loginUserDetail['passenger_average_rating'] == "" ? '0.0' : serviceController.loginUserDetail['passenger_average_rating'])!;

          if (ride['features'].any((feature) => feature['slug'] == 'pink_rides') && (serviceController.loginUserDetail['gender'] != 'female' && serviceController.loginUserDetail['gender'] != 'Female')) {
            serviceController.showDialogue("${popupTextDetail['female_user_message'] ?? 'Only female passengers can select this ride'}");
            return;
          }
          if (ride['features'].any((feature) => feature['slug'] == 'with_review_passenger') && int.parse(serviceController.loginUserDetail['passenger_total_ratings'] != null ? serviceController.loginUserDetail['passenger_total_ratings'].toString() : "0") == 0 )  {
            serviceController.showDialogue("${popupTextDetail['passenger_with_review_message'] ?? 'Driver only want passengers with reviews'}");
            return;
          }
          if (ride['features'].any((feature) => feature['slug'] == 'star3_passenger') && double.parse(serviceController.loginUserDetail['passenger_average_rating'].toString()) < 3) {
            serviceController.showDialogue("${popupTextDetail['star3_passenger_message'] ?? 'Driver only want passengers with-3 star reviews and above'}");
            return;
          }
          if (ride['features'].any((feature) => feature['slug'] == 'star4_passenger') && double.parse(serviceController.loginUserDetail['passenger_average_rating'].toString()) < 4) {
            serviceController.showDialogue("${popupTextDetail['star4_passenger_message'] ?? 'Driver want only passengers with-4 star reviews and above'}");
            return;
          }

          if(ride['features'].any((feature) => feature['slug'] == 'star5-passenger') && double.parse(serviceController.loginUserDetail['passenger_average_rating'] != null ? serviceController.loginUserDetail['passenger_average_rating'].toString() : "0.0") < 5) {
            serviceController.showDialogue("${popupTextDetail['star5_passenger_message'] ?? 'Driver want only passengers with-5 star reviews'}");
            return;
          }

          if(resp['data']['hasBooking'] != null) {
              if(resp['data']['hasBooking']) {
                Get.toNamed("/book_seat/$rideId/${resp['data']['seats']}/$tripDetailId");
                isAlreadyBooked.value = true;
              }
              else{
                Get.toNamed(
                    '/trip_detail/$rideId/findRide/findRide/$tripDetailId');
              }
            }
          }
        }
    });
  }
  catch(exception){
    serviceController.showDialogue(exception.toString());
  }
  }

  getSearchRide(type) async{
    try{
      if(type == 1 && (fromTextEditingController.text == "" || toTextEditingController.text == "")){
        if(fromTextEditingController.text == ""){
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['from_error'] ?? 'Origin');
          var err = {
            'title': "from",
            'eList' : [message ?? 'Origin field is required']
          };
          errors.add(err);
        }

        if(toTextEditingController.text == ""){
          var message = validationMessageDetail['required'];
          message = message.replaceAll(":Attribute", labelTextDetail['to_error'] ?? 'Destination');
          var err = {
            'title': "to",
            'eList' : [ message ?? 'Destination field is required']
          };
          errors.add(err);
        }
        return;
      }
      
      if(filter.value == true && actionType.value == "clear"){
        bool isConfirmed = await serviceController.showConfirmationDialog("${popupTextDetail['search_result_clear_message'] ?? "Are you sure you want to clear your search result?"}");
        if(isConfirmed == false){
          actionType.value = "";
          filter.value = false;
          return;
        }else{
          filter.value = false;
        }
      }
      rides.clear();
      page = 1;
      noRideFound.value = false;
      noMoreData.value = false;
      isScrollLoading(false);

      String features = "";
      if(featureList.isNotEmpty){
        for(int i=0;i<featureList.length;i++)
        {
          features+= featureList[i];
          if(i< featureList.length-1) {
            features+= "=";
          }
        }
      }
      type == 0 ? isLoading(true) : isOverlayLoading(true);
      SearchRideProvider().getSearchRide(
        toTextEditingController.text,
        fromTextEditingController.text,
        keywordTextEditingController.text,
        dateTextEditingController.text,
        driverNameEditingController.text,
        driverAge.value,
        driverRating.value,
        driverPhone.value,
        passengerRating.value,
        paymentMethod.value,
        vehicleType.value,
        features,
        luggage.value,
        smoking.value,
        pet.value,
        pinkRideCheck.value,
        extraCareCheck.value,
        pageLimit,
        page,
        serviceController.token
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['rides'] != null && resp['data']['rides'].isNotEmpty &&  resp['data']['rides']['data'] != null){
            rides.clear();
            rides.addAll(resp['data']['rides']['data']);
            searchTotal.value = resp['data']['rides']['total'] ?? 0;
            rides.refresh();
            firmDiscount.value = resp['data']['firm_cancellation_discount'].toString();
          }

          if(resp['data'] != null && resp['data']['rides'] != null && resp['data']['rides'].isNotEmpty &&  resp['data']['rides']['data'] != null && resp['data']['rides']['data'].isEmpty){
            noRideFound.value = true;
          }
          if(resp['data'] != null && resp['data']['recentSearches'] != null){
            recentSearchList.clear();
            recentSearchList.addAll(resp['data']['recentSearches']);
            recentSearchList.refresh();
          }
        }
        type == 0 ? isLoading(false) : isOverlayLoading(false);
        if(type != 0){
          Get.toNamed('/search_ride_result');
        }
      },onError: (error){
        type == 0 ? isLoading(false) : isOverlayLoading(false);
        serviceController.showDialogue(error.toString());

      });

    }catch (exception) {
      type == 0 ? isLoading(false) : isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  getPreferenceOptions() async{
    try{
      PostRideProvider().getPreferenceOptions(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['preferencesOptions'] != null){
            smokingList.add(resp['data']['preferencesOptions']['smoking_option1']);
            smokingList.add(resp['data']['preferencesOptions']['smoking_option2']);

            smokingLabelList.add(resp['data']['preferencesOptions']['smoking_option1_label']);
            smokingLabelList.add(resp['data']['preferencesOptions']['smoking_option2_label']);

            petList.add(resp['data']['preferencesOptions']['animals_option1']);
            petList.add(resp['data']['preferencesOptions']['animals_option2']);
            petList.add(resp['data']['preferencesOptions']['animals_option3']);

            petLabelList.add(resp['data']['preferencesOptions']['animals_option1_label']);
            petLabelList.add(resp['data']['preferencesOptions']['animals_option2_label']);
            petLabelList.add(resp['data']['preferencesOptions']['animals_option3_label']);

          }
        }
      },onError: (error){
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());

      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  getFindRidePreferenceOptions() async{
    try{
      SearchRideProvider().getFindRidePreferenceOptions(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['featuresOptions'] != null){
            rideFeatureList.addAll(resp['data']['featuresOptions']);
          }
          if(resp['data'] != null && resp['data']['passengerRatingOptions'] != null){
            passengerRatingList.addAll(resp['data']['passengerRatingOptions']);
          }

          if(resp['data'] != null && resp['data']['featuresLabels'] != null){
            rideFeatureLabelList.addAll(resp['data']['featuresLabels']);
          }

          if(resp['data'] != null && resp['data']['passengerRatingLabels'] != null){
            passengerRatingLabelList.addAll(resp['data']['passengerRatingLabels']);
          }

        }
      },onError: (error){
        isOverlayLoading(false);
        serviceController.showDialogue(error.toString());

      });

    }catch (exception) {
      isOverlayLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  getBookingOption() async{
    try{
      PostRideProvider().getBookingOption(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['bookingOptions'] != null){
            bookingOptionList.addAll(resp['data']['bookingOptions']);
          }
          if(resp['data'] != null && resp['data']['bookingTooltips'] != null){
            bookingOptionToolTipList.addAll(resp['data']['bookingTooltips']);
          }

          if(resp['data'] != null && resp['data']['bookingLabels'] != null){
            bookingOptionLabelList.addAll(resp['data']['bookingLabels']);
          }
        }
      },onError: (error){
        isLoading(false);
        serviceController.showDialogue(error.toString());
      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());    }
  }

  getLuggage() async{
    try{
      PostRideProvider().getLuggage(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['luggageOptions'] != null){
            luggageList.addAll(resp['data']['luggageOptions']);
          }
          if(resp['data'] != null && resp['data']['luggageTooltips'] != null){
            luggageListToolTip.addAll(resp['data']['luggageTooltips']);
          }


          if(resp['data'] != null && resp['data']['luggageLabels'] != null){
            luggageListLabel.addAll(resp['data']['luggageLabels']);
          }
        }
      },onError: (error){
        isLoading(false);
        serviceController.showDialogue(error.toString());      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());    }
  }

  getPaymentOptions() async{
    try{
      PostRideProvider().getPaymentOptions(
          serviceController.token,
          serviceController.langId.value
      ).then((resp) async {
        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data'] != null && resp['data']['paymentOptions'] != null){
            paymentOptionList.addAll(resp['data']['paymentOptions']);
          }

          if(resp['data'] != null && resp['data']['paymentTooltips'] != null){
            paymentOptionToolTipList.addAll(resp['data']['paymentTooltips']);
          }
          if(resp['data'] != null && resp['data']['paymentLabels'] != null){
            paymentOptionLabelList.addAll(resp['data']['paymentLabels']);
          }
        }
      },onError: (error){
        isLoading(false);
        serviceController.showDialogue(error.toString());      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());    }
  }


  Future<void> getMoreRides() async {
    try {
      if(noMoreData.value == true){
        return;
      }
      isScrollLoading(true);

      String features = "";
      if(featureList.isNotEmpty){
        for(int i=0;i<featureList.length;i++)
        {
          features+= featureList[i];
          if(i< featureList.length-1) {
            features+= "=";
          }
        }
      }

      await SearchRideProvider().getSearchRide(
          toTextEditingController.text,
          fromTextEditingController.text,
          keywordTextEditingController.text,
          dateTextEditingController.text,
          driverNameEditingController.text,
          driverAge.value,
          driverRating.value,
          driverPhone.value,
          passengerRating.value,
          paymentMethod.value,
          vehicleType.value,
          features,
          luggage.value,
          smoking.value,
          pet.value,
          pinkRideCheck.value,
          extraCareCheck.value,
          pageLimit,
          page,
          serviceController.token
      ).then((resp) async {

        if(resp['data'] != null && resp['data']['rides'] != null && resp['data']['rides'].isNotEmpty &&  resp['data']['rides']['data'] != null && resp['data']['rides']['data'].isEmpty){
          noMoreData(true);
          return;
        }

        if(resp['data'] != null && resp['data']['rides'] != null && resp['data']['rides'].isNotEmpty &&  resp['data']['rides']['data'] != null){
          rides.addAll(resp['data']['rides']['data']);
        }
        isScrollLoading(false);

      }, onError: (error) {
        isScrollLoading(false);
        serviceController.showDialogue(error.toString());      });
    } catch (exception) {
      isScrollLoading(false);
      serviceController.showDialogue(exception.toString());
    }
  }

  clearFilter(){
    driverAge.value = "";
    driverRating.value = "";
    driverPhone.value = "";
    driverNameEditingController.text = "";
    passengerRating.value = "";
    paymentMethod.value = "";
    vehicleType.value = "";
    featureList.clear();
    luggage.value = "";
    smoking.value = "";
    pet.value = "";
  }

  getPostRideSetting() async{
    try{
      PostRideProvider().getPostRideSetting(
        serviceController.token,
        serviceController.langId.value
      ).then((resp) async {

        bool pinkRide = true;

        if(resp['status'] != null && resp['status'] == "Success"){
          if(resp['data']['user']['pink_ride'] == "1"){
            pinkRide = true;
          }else if(resp['data']['user']['pink_ride'] == "0"){
            pinkRide = false;
          }else if(resp['data'] != null && resp['data']['pinkRideSetting'] != null){
            if(resp['data']['pinkRideSetting']['female'] == "1" && resp['data']['user']['gender'] != "female"){
              pinkRide = false;
            }if(resp['data']['pinkRideSetting']['verify_phone'] == "1" && resp['data']['user']['email_verified'] != "1"){
              pinkRide = false;
            }if(resp['data']['pinkRideSetting']['verify_email'] == "1" && resp['data']['user']['email_verified'] != "1"){
              pinkRide = false;
            }if(resp['data']['pinkRideSetting']['driver_license'] == "1" && resp['data']['user']['driver'] != "2"){
              pinkRide = false;
            }
          }

          if(pinkRide == false){
            pinkRideReadOnly.value = true;
          }

        }
      },onError: (error){
        isLoading(false);
        serviceController.showDialogue(error.toString());      });

    }catch (exception) {
      isLoading(false);
      serviceController.showDialogue(exception.toString());    }
  }

}